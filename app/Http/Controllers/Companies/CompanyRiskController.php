<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Risk;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyRiskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        
        $query = Risk::with(['department', 'riskOwner'])
            ->whereHas('department', function ($q) use ($employee) {
                $q->where('company_id', $employee->company_id);
            });

        // Filters
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('risk_level') && $request->risk_level) {
            switch ($request->risk_level) {
                case 'critical':
                    $query->whereRaw('(likelihood * impact) >= 15');
                    break;
                case 'high':
                    $query->whereRaw('(likelihood * impact) >= 10 AND (likelihood * impact) < 15');
                    break;
                case 'medium':
                    $query->whereRaw('(likelihood * impact) >= 5 AND (likelihood * impact) < 10');
                    break;
                case 'low':
                    $query->whereRaw('(likelihood * impact) < 5');
                    break;
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('risk_code', 'like', '%' . $request->search . '%');
            });
        }

        $risks = $query->latest()->paginate(15);
        
        $departments = Department::where('company_id', $employee->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Companies/Risks/Index', [
            'risks' => $risks,
            'departments' => $departments,
            'filters' => $request->only(['department_id', 'status', 'category', 'risk_level', 'search']),
            'categories' => Risk::categories(),
            'statuses' => Risk::statuses(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        
        $departments = Department::where('company_id', $employee->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $employees = Employee::where('company_id', $employee->company_id)
            ->select('id', 'fname', 'lname')
            ->orderBy('fname')
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->fname . ' ' . $emp->lname,
                ];
            });

        return Inertia::render('Companies/Risks/Create', [
            'departments' => $departments,
            'employees' => $employees,
            'categories' => Risk::categories(),
            'treatmentStrategies' => Risk::treatmentStrategies(),
            'statuses' => Risk::statuses(),
            'likelihoodLevels' => Risk::likelihoodLevels(),
            'impactLevels' => Risk::impactLevels(),
            'departmentId' => $request->query('department_id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'risk_owner_id' => 'nullable|exists:employees,id',
            'risk_code' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'likelihood' => 'required|integer|min:1|max:5',
            'impact' => 'required|integer|min:1|max:5',
            'current_controls' => 'nullable|string',
            'treatment_strategy' => 'nullable|string',
            'action_plan' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'required|string',
            'date_identified' => 'nullable|date',
            'residual_likelihood' => 'nullable|integer|min:1|max:5',
            'residual_impact' => 'nullable|integer|min:1|max:5',
        ]);

        // Verify department belongs to company
        $department = Department::find($validated['department_id']);
        if ($department->company_id !== $employee->company_id) {
            return back()->withErrors(['department_id' => 'Invalid department selected.']);
        }

        // Verify risk owner belongs to company if provided
        if ($validated['risk_owner_id']) {
            $owner = Employee::find($validated['risk_owner_id']);
            if ($owner->company_id !== $employee->company_id) {
                return back()->withErrors(['risk_owner_id' => 'Invalid risk owner selected.']);
            }
        }

        Risk::create($validated);

        return redirect()->route('companies.risks.index')
            ->with('success', 'Risk created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Risk $risk)
    {
        $employee = Auth::guard('employee')->user();
        
        // Verify risk belongs to company
        if ($risk->department->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $risk->load(['department', 'riskOwner']);

        return Inertia::render('Companies/Risks/Show', [
            'risk' => $risk,
            'categories' => Risk::categories(),
            'treatmentStrategies' => Risk::treatmentStrategies(),
            'statuses' => Risk::statuses(),
            'likelihoodLevels' => Risk::likelihoodLevels(),
            'impactLevels' => Risk::impactLevels(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Risk $risk)
    {
        $employee = Auth::guard('employee')->user();
        
        // Verify risk belongs to company
        if ($risk->department->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $risk->load(['department', 'riskOwner']);

        $departments = Department::where('company_id', $employee->company_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $employees = Employee::where('company_id', $employee->company_id)
            ->select('id', 'fname', 'lname')
            ->orderBy('fname')
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->fname . ' ' . $emp->lname,
                ];
            });

        return Inertia::render('Companies/Risks/Edit', [
            'risk' => $risk,
            'departments' => $departments,
            'employees' => $employees,
            'categories' => Risk::categories(),
            'treatmentStrategies' => Risk::treatmentStrategies(),
            'statuses' => Risk::statuses(),
            'likelihoodLevels' => Risk::likelihoodLevels(),
            'impactLevels' => Risk::impactLevels(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Risk $risk)
    {
        $employee = Auth::guard('employee')->user();
        
        // Verify risk belongs to company
        if ($risk->department->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'risk_owner_id' => 'nullable|exists:employees,id',
            'risk_code' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'likelihood' => 'required|integer|min:1|max:5',
            'impact' => 'required|integer|min:1|max:5',
            'current_controls' => 'nullable|string',
            'treatment_strategy' => 'nullable|string',
            'action_plan' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'required|string',
            'date_identified' => 'nullable|date',
            'last_review_date' => 'nullable|date',
            'residual_likelihood' => 'nullable|integer|min:1|max:5',
            'residual_impact' => 'nullable|integer|min:1|max:5',
        ]);

        // Verify department belongs to company
        $department = Department::find($validated['department_id']);
        if ($department->company_id !== $employee->company_id) {
            return back()->withErrors(['department_id' => 'Invalid department selected.']);
        }

        // Verify risk owner belongs to company if provided
        if ($validated['risk_owner_id']) {
            $owner = Employee::find($validated['risk_owner_id']);
            if ($owner->company_id !== $employee->company_id) {
                return back()->withErrors(['risk_owner_id' => 'Invalid risk owner selected.']);
            }
        }

        $risk->update($validated);

        return redirect()->route('companies.risks.show', $risk)
            ->with('success', 'Risk updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Risk $risk)
    {
        $employee = Auth::guard('employee')->user();
        
        // Verify risk belongs to company
        if ($risk->department->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $risk->delete();

        return redirect()->route('companies.risks.index')
            ->with('success', 'Risk deleted successfully.');
    }

    /**
     * Display risks for a specific department.
     */
    public function departmentRisks(Department $department)
    {
        $employee = Auth::guard('employee')->user();
        
        // Verify department belongs to company
        if ($department->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // Redirect to main risks index with department filter
        return redirect()->route('companies.risks.index', ['department_id' => $department->id]);
    }
}
