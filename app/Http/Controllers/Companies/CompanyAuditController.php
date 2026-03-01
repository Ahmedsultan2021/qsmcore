<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Report;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyAuditController extends Controller
{
    /**
     * Display a listing of the resource (calendar view).
     */
    public function index(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        $audits = Audit::where('company_id', $authEmployee->company_id)
            ->with(['reports.department', 'reports.forms', 'reports.formResponses'])
            ->orderBy('audit_date')
            ->get();
        
        // Get all reports for the company (for attaching to audits)
        $reports = Report::whereHas('department', function ($query) use ($authEmployee) {
                $query->where('company_id', $authEmployee->company_id);
            })
            ->with(['department'])
            ->orderBy('title')
            ->get();
        
        return Inertia::render('Companies/Audits/Index', [
            'audits' => $audits,
            'reports' => $reports,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Get all reports for the company
        $reports = Report::whereHas('department', function ($query) use ($authEmployee) {
                $query->where('company_id', $authEmployee->company_id);
            })
            ->with(['department'])
            ->orderBy('title')
            ->get();
        
        return Inertia::render('Companies/Audits/Create', [
            'reports' => $reports,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'attached_file' => 'nullable|file|max:10240',
            'audit_date' => 'required|date',
            'reports' => 'nullable|array',
            'reports.*' => 'exists:reports,id',
        ]);
        
        // Verify reports belong to the company
        if (!empty($validated['reports'])) {
            $reportIds = $validated['reports'];
            $companyReportIds = Report::whereHas('department', function ($query) use ($authEmployee) {
                    $query->where('company_id', $authEmployee->company_id);
                })
                ->pluck('id')
                ->toArray();
            
            foreach ($reportIds as $reportId) {
                if (!in_array($reportId, $companyReportIds)) {
                    return back()->withErrors(['reports' => 'One or more selected reports do not belong to your company.']);
                }
            }
        }
        
        $validated['company_id'] = $authEmployee->company_id;
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('audits/images', 'public');
        }
        
        // Handle attached file upload
        if ($request->hasFile('attached_file')) {
            $validated['attached_file'] = $request->file('attached_file')->store('audits/files', 'public');
        }
        
        $reports = $validated['reports'] ?? [];
        unset($validated['reports']);
        
        $audit = Audit::create($validated);
        
        if (!empty($reports)) {
            $audit->reports()->attach($reports);
        }
        
        return redirect()->route('companies.audits.index')
            ->with('success', 'Audit created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Audit $audit)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure audit belongs to company
        if ($audit->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $audit->load(['reports.department', 'reports.forms', 'reports.formResponses', 'company']);
        
        return Inertia::render('Companies/Audits/Show', [
            'audit' => $audit,
            'completionStatus' => $audit->completion_status,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Audit $audit)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure audit belongs to company
        if ($audit->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Get all reports for the company
        $reports = Report::whereHas('department', function ($query) use ($authEmployee) {
                $query->where('company_id', $authEmployee->company_id);
            })
            ->with(['department'])
            ->orderBy('title')
            ->get();
        
        $audit->load('reports');
        
        return Inertia::render('Companies/Audits/Edit', [
            'audit' => $audit,
            'reports' => $reports,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Audit $audit)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure audit belongs to company
        if ($audit->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'attached_file' => 'nullable|file|max:10240',
            'audit_date' => 'required|date',
            'reports' => 'nullable|array',
            'reports.*' => 'exists:reports,id',
        ]);
        
        // Verify reports belong to the company
        if (!empty($validated['reports'])) {
            $reportIds = $validated['reports'];
            $companyReportIds = Report::whereHas('department', function ($query) use ($authEmployee) {
                    $query->where('company_id', $authEmployee->company_id);
                })
                ->pluck('id')
                ->toArray();
            
            foreach ($reportIds as $reportId) {
                if (!in_array($reportId, $companyReportIds)) {
                    return back()->withErrors(['reports' => 'One or more selected reports do not belong to your company.']);
                }
            }
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($audit->image) {
                Storage::disk('public')->delete($audit->image);
            }
            $validated['image'] = $request->file('image')->store('audits/images', 'public');
        }
        
        // Handle attached file upload
        if ($request->hasFile('attached_file')) {
            // Delete old file if exists
            if ($audit->attached_file) {
                Storage::disk('public')->delete($audit->attached_file);
            }
            $validated['attached_file'] = $request->file('attached_file')->store('audits/files', 'public');
        }
        
        $reports = $validated['reports'] ?? [];
        unset($validated['reports']);
        
        $audit->update($validated);
        
        $audit->reports()->sync($reports);
        
        return redirect()->route('companies.audits.index')
            ->with('success', 'Audit updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Audit $audit)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure audit belongs to company
        if ($audit->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Delete associated files
        if ($audit->image) {
            Storage::disk('public')->delete($audit->image);
        }
        if ($audit->attached_file) {
            Storage::disk('public')->delete($audit->attached_file);
        }
        
        $audit->delete();
        
        return redirect()->route('companies.audits.index')
            ->with('success', 'Audit deleted successfully');
    }
}
