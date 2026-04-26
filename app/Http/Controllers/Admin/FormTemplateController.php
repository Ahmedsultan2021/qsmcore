<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\FormTemplate;
use App\Models\Industry;
use App\Models\Sector;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormTemplateController extends Controller
{
    /**
     * Global form template library with optional filters (industry / sector / department pivots).
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'industry_id'  => 'nullable|integer|exists:industries,id',
            'sector_id'    => 'nullable|integer|exists:sectors,id',
            'department_id'=> 'nullable|integer|exists:departments,id',
            'search'       => 'nullable|string|max:100',
        ]);

        $industryId   = $validated['industry_id'] ?? null;
        $sectorId     = $validated['sector_id'] ?? null;
        $departmentId = $validated['department_id'] ?? null;
        $search       = isset($validated['search']) ? trim($validated['search']) : null;
        if ($search === '') {
            $search = null;
        }

        if ($sectorId) {
            $sector = Sector::query()->find($sectorId);
            if ($industryId && $sector && (int) $sector->industry_id !== (int) $industryId) {
                $sectorId = null;
            }
        }

        if ($departmentId) {
            $dept = Department::query()->with('company.sector')->find($departmentId);
            if (! $dept) {
                $departmentId = null;
            } else {
                if ($sectorId && (int) $dept->company->sector_id !== (int) $sectorId) {
                    $departmentId = null;
                } elseif ($industryId && (! $dept->company->sector || (int) $dept->company->sector->industry_id !== (int) $industryId)) {
                    $departmentId = null;
                }
            }
        }

        $query = FormTemplate::query()
            ->with(['themes:id,slug,name'])
            ->withCount(['formTemplateFields', 'industries', 'sectors'])
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('library_key', 'like', "%{$search}%")
            ));

        if ($industryId) {
            $query->whereHas(
                'industries',
                fn ($q) => $q->where('industries.id', $industryId)
            );
        }
        if ($sectorId) {
            $query->whereHas(
                'sectors',
                fn ($q) => $q->where('sectors.id', $sectorId)
            );
        }
        if ($departmentId) {
            $query->whereHas(
                'departments',
                fn ($q) => $q->where('departments.id', $departmentId)
            );
        }

        $formTemplates = $query
            ->orderBy('library_key')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $industries = Industry::query()->orderBy('name')->get(['id', 'name']);
        $sectors = Sector::query()->orderBy('name')->get(['id', 'name', 'industry_id']);
        $departments = Department::query()
            ->with(['company:id,name,sector_id', 'company.sector:id,industry_id'])
            ->orderBy('name')
            ->get()
            ->map(fn (Department $d) => [
                'id' => $d->id,
                'name' => $d->name,
                'label' => $d->name.' — '.$d->company->name,
                'sector_id' => $d->company->sector_id,
                'industry_id' => $d->company->sector?->industry_id,
            ])
            ->values();

        return Inertia::render('Admin/FormTemplates/Index', [
            'formTemplates' => $formTemplates,
            'filters' => [
                'industry_id'   => $industryId,
                'sector_id'     => $sectorId,
                'department_id' => $departmentId,
                'search'        => $search,
            ],
            'industries' => $industries,
            'sectors' => $sectors,
            'departments' => $departments,
        ]);
    }
}
