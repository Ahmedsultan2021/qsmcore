<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Department;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyReportController extends Controller
{
    /**
     * Display all reports for the company (across all departments).
     */
    public function allReports(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        $query = Report::whereHas('department', function ($query) use ($authEmployee) {
                $query->where('company_id', $authEmployee->company_id);
            })
            ->with(['department', 'creator']);
        
        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        $reports = $query->latest()->paginate(10)->withQueryString();
        
        $departments = Department::where('company_id', $authEmployee->company_id)
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Companies/Reports/AllReports', [
            'reports' => $reports,
            'departments' => $departments,
            'filters' => $request->only(['department_id']),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $reports = Report::where('department_id', $department->id)
            ->with(['department', 'creator'])
            ->latest()
            ->paginate(10);
        
        $formsCount = Form::where('department_id', $department->id)->count();
        
        return Inertia::render('Companies/Reports/Index', [
            'department' => $department,
            'reports' => $reports,
            'formsCount' => $formsCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $forms = Form::where('department_id', $department->id)
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Companies/Reports/Create', [
            'department' => $department,
            'forms' => $forms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,submitted,reviewed,approved,rejected',
            'report_date' => 'required|date',
            'forms' => 'nullable|array',
            'forms.*' => 'exists:forms,id',
        ]);

        $validated['department_id'] = $department->id;
        $validated['created_by'] = $authEmployee->id;

        $forms = $validated['forms'] ?? [];
        unset($validated['forms']);

        $report = Report::create($validated);
        
        if (!empty($forms)) {
            $report->forms()->attach($forms);
        }

        return redirect()->route('companies.departments.reports.index', $department->id)
            ->with('success', 'Report created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Report is already scoped to department via route model binding
        $report->load(['department.company.sector.industry', 'creator', 'forms', 'formResponses']);
        
        // Get form submission status for current user
        $authEmployee = Auth::guard('employee')->user();
        $submittedForms = FormResponse::where('report_id', $report->id)
            ->where('submitted_by', $authEmployee->id)
            ->pluck('form_id')
            ->toArray();
        
        return Inertia::render('Companies/Reports/Show', [
            'department' => $department,
            'report' => $report,
            'submittedForms' => $submittedForms,
            'generalReportStatus' => $report->general_report_status,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Report is already scoped to department via route model binding
        $forms = Form::where('department_id', $department->id)
            ->orderBy('name')
            ->get();
        $report->load('forms');
        
        return Inertia::render('Companies/Reports/Edit', [
            'department' => $department,
            'report' => $report,
            'forms' => $forms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Report is already scoped to department via route model binding
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,submitted,reviewed,approved,rejected',
            'report_date' => 'required|date',
            'forms' => 'nullable|array',
            'forms.*' => 'exists:forms,id',
        ]);

        $forms = $validated['forms'] ?? [];
        unset($validated['forms']);

        $report->update($validated);
        
        $report->forms()->sync($forms);

        return redirect()->route('companies.departments.reports.index', $department->id)
            ->with('success', 'Report updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department, Report $report)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        // Report is already scoped to department via route model binding
        $report->delete();

        return redirect()->route('companies.departments.reports.index', $department->id)
            ->with('success', 'Report deleted successfully');
    }
}

