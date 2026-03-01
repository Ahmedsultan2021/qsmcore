<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Audit;
use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        $departments = Department::where('company_id', $employee->company_id)
            ->with('company')
            ->latest()
            ->paginate(10);
        
        return Inertia::render('Companies/Departments/Index', [
            'departments' => $departments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employee = Auth::guard('employee')->user();
        
        return Inertia::render('Companies/Departments/Create', [
            'company_id' => $employee->company_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['company_id'] = $employee->company_id;

        Department::create($validated);

        return redirect()->route('companies.departments.index')
            ->with('success', 'Department created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $department->load('company.sector.industry');
        
        // Get reports count for statistics
        $reportsCount = $department->reports()->count();
        $reportsByStatus = $department->reports()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Real form statistics for the department
        $departmentForms = $department->forms;
        $totalForms = $departmentForms->count();
        
        // Get all form IDs that have at least one response (completed)
        $completedFormIds = FormResponse::whereHas('form', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->distinct()
            ->pluck('form_id')
            ->toArray();
        
        // Get all reports for this department with their forms
        $departmentReports = $department->reports()->with('forms')->get();
        
        // Track forms attached to reports (but not completed)
        $formsAttachedToReports = [];
        
        foreach ($departmentReports as $report) {
            foreach ($report->forms as $form) {
                $formId = $form->id;
                
                // Only track if form is not already completed
                if (!in_array($formId, $completedFormIds) && !in_array($formId, $formsAttachedToReports)) {
                    $formsAttachedToReports[] = $formId;
                }
            }
        }
        
        // Calculate statistics
        $completed = count(array_unique($completedFormIds));
        $inProgress = count($formsAttachedToReports); // Forms attached to reports but not completed
        $pending = $totalForms - $completed - $inProgress; // Forms not attached to any report
        
        $formsStats = [
            'total' => $totalForms,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => max(0, $pending), // Ensure non-negative
        ];
        
        // Real risk statistics
        $risks = $department->risks;
        $riskStats = [
            'total' => $risks->count(),
            'low' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) < 5)->count(),
            'medium' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) >= 5 && ($r->likelihood * $r->impact) < 10)->count(),
            'high' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) >= 10 && ($r->likelihood * $r->impact) < 15)->count(),
            'critical' => $risks->filter(fn($r) => ($r->likelihood * $r->impact) >= 15)->count(),
        ];
        
        // Real audit statistics - get audits related to this department's reports
        $departmentReportIds = $department->reports()->pluck('id')->toArray();
        
        // Get audits that have at least one report from this department
        $audits = Audit::where('company_id', $authEmployee->company_id)
            ->whereHas('reports', function ($query) use ($departmentReportIds) {
                $query->whereIn('reports.id', $departmentReportIds);
            })
            ->with(['reports.forms', 'reports.formResponses'])
            ->get();
        
        // Calculate audit statistics based on completion status and date
        $completed = 0;
        $inProgress = 0;
        $pending = 0;
        $today = now()->startOfDay();
        
        foreach ($audits as $audit) {
            $auditDate = $audit->audit_date->startOfDay();
            $status = $audit->completion_status;
            
            // If audit date is in the future, it's pending
            if ($auditDate->isFuture()) {
                $pending++;
            } elseif ($status === 'complete') {
                $completed++;
            } elseif ($status === 'pending') {
                // Internal audit with incomplete reports = in progress
                if ($audit->reports->isNotEmpty()) {
                    $inProgress++;
                } else {
                    // This shouldn't happen (external audits are always complete), but handle it
                    $pending++;
                }
            }
        }
        
        $auditStats = [
            'total' => $audits->count(),
            'completed' => $completed,
            'in_progress' => $inProgress,
            'pending' => $pending,
        ];
        
        // Fake statistics for functions
        $functionsStats = [
            'audits' => $auditStats,
            'kpis' => [
                'total' => 28,
                'on_target' => 22,
                'below_target' => 4,
                'above_target' => 2,
            ],
            'risk_register' => $riskStats,
        ];
        
        return Inertia::render('Companies/Departments/Show', [
            'department' => $department,
            'reportsCount' => $reportsCount,
            'reportsByStatus' => $reportsByStatus,
            'formsStats' => $formsStats,
            'functionsStats' => $functionsStats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        return Inertia::render('Companies/Departments/Edit', [
            'department' => $department,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $department->update($validated);

        return redirect()->route('companies.departments.index')
            ->with('success', 'Department updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure department belongs to same company
        if ($department->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $department->delete();

        return redirect()->route('companies.departments.index')
            ->with('success', 'Department deleted successfully');
    }
}

