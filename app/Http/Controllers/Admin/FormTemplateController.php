<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FormFieldsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\FormFieldsImport;
use App\Models\Department;
use App\Models\Form;
use App\Models\FormTemplate;
use App\Models\Industry;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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

    public function update(Request $request, FormTemplate $formTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = trim($validated['name']);
        if ($name === '') {
            return back()->withErrors([
                'name' => 'The form name is required.',
            ]);
        }

        $previous = $formTemplate->name;
        $formTemplate->update(['name' => $name]);

        if ($previous !== $name) {
            Form::query()
                ->where('form_template_id', $formTemplate->id)
                ->where('name', $previous)
                ->update(['name' => $name]);
        }

        return back()->with('success', 'Form name updated.');
    }

    public function create()
    {
        return Inertia::render('Admin/FormTemplates/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $name = trim($validated['name']);
        if ($name === '') {
            return back()->withErrors(['name' => 'The form name is required.']);
        }

        $template = FormTemplate::create([
            'name' => $name,
            'library_key' => $this->uniqueLibraryKey($name),
            'description' => trim((string) ($validated['description'] ?? '')) ?: null,
        ]);

        return redirect()
            ->route('form-templates.show', $template)
            ->with('success', 'Form created. Add its questions next.');
    }

    public function show(FormTemplate $formTemplate)
    {
        $formTemplate->load('formTemplateFields');

        return Inertia::render('Companies/Forms/Show', [
            'form' => [
                'id' => $formTemplate->id,
                'name' => $formTemplate->name,
                'description' => $formTemplate->description,
                'form_fields' => $formTemplate->formTemplateFields,
            ],
            'shell' => 'admin',
            'routes' => [
                'index' => 'form-templates.index',
                'update' => 'form-templates.update',
                'fieldsStore' => 'form-templates.fields.store',
                'fieldsUpdate' => 'form-templates.fields.update',
                'fieldsDestroy' => 'form-templates.fields.destroy',
                'fieldsOrder' => 'form-templates.fields.update-order',
                'destroy' => 'form-templates.destroy',
                'templateDownload' => 'form-templates.template.download',
                'import' => 'form-templates.import',
                'backLabel' => 'Form templates',
            ],
        ]);
    }

    public function destroy(FormTemplate $formTemplate)
    {
        $formTemplate->delete();

        return redirect()
            ->route('form-templates.index')
            ->with('success', 'Form deleted.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new FormFieldsTemplateExport(), 'form_fields_template.xlsx');
    }

    public function importFields(Request $request, FormTemplate $formTemplate)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new FormFieldsImport($formTemplate), $request->file('file'));

            return back()->with('success', 'Form fields imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] = "Row {$failure->row()}: ".implode(', ', $failure->errors());
            }

            return back()->withErrors(['import' => $errors])->with('error', 'Some rows failed to import. Please check the errors below.');
        } catch (\Exception $e) {
            return back()->withErrors(['import' => [$e->getMessage()]])->with('error', 'Failed to import form fields.');
        }
    }

    private function uniqueLibraryKey(string $name): string
    {
        $base = Str::slug($name) ?: 'form';
        $key = $base;
        $suffix = 2;

        while (FormTemplate::where('library_key', $key)->exists()) {
            $key = $base.'-'.$suffix;
            $suffix++;
        }

        return $key;
    }
}
