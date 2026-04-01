<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Capa;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyCapaController extends Controller
{
    public function index(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();

        $query = Capa::query()
            ->where('company_id', $authEmployee->company_id)
            ->with(['department', 'assignee', 'creator'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->integer('assigned_to'));
        }

        if ($request->boolean('overdue')) {
            $query->whereNot('status', 'closed')
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString());
        }

        $capas = $query->paginate(10)->withQueryString();

        $departments = Department::query()
            ->where('company_id', $authEmployee->company_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $employees = Employee::query()
            ->where('company_id', $authEmployee->company_id)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get(['id', 'fname', 'lname', 'email']);

        return Inertia::render('Companies/Capa/Index', [
            'capas' => $capas,
            'departments' => $departments,
            'employees' => $employees,
            'filters' => $request->only(['status', 'department_id', 'assigned_to', 'overdue']),
        ]);
    }

    public function create()
    {
        $authEmployee = Auth::guard('employee')->user();

        $departments = Department::query()
            ->where('company_id', $authEmployee->company_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $employees = Employee::query()
            ->where('company_id', $authEmployee->company_id)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get(['id', 'fname', 'lname', 'email']);

        return Inertia::render('Companies/Capa/Create', [
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:corrective,preventive',
            'status' => 'required|in:open,in_progress,closed',
            'priority' => 'required|in:low,medium,high',
            'department_id' => 'nullable|integer|exists:departments,id',
            'assigned_to' => 'nullable|integer|exists:employees,id',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'preventive_action' => 'nullable|string',
        ]);

        if (!empty($validated['department_id'])) {
            $department = Department::findOrFail($validated['department_id']);
            if ($department->company_id !== $authEmployee->company_id) {
                abort(403);
            }
        }

        if (!empty($validated['assigned_to'])) {
            $assignee = Employee::findOrFail($validated['assigned_to']);
            if ($assignee->company_id !== $authEmployee->company_id) {
                abort(403);
            }
        }

        $validated['company_id'] = $authEmployee->company_id;
        $validated['created_by'] = $authEmployee->id;
        $validated['closed_at'] = ($validated['status'] ?? null) === 'closed' ? now() : null;

        $capa = Capa::create($validated);

        return redirect()->route('companies.capa.show', $capa->id)
            ->with('success', 'CAPA created successfully');
    }

    public function show(Capa $capa)
    {
        $authEmployee = Auth::guard('employee')->user();
        if ($capa->company_id !== $authEmployee->company_id) {
            abort(403);
        }

        $capa->load(['department', 'assignee', 'creator']);

        return Inertia::render('Companies/Capa/Show', [
            'capa' => $capa,
        ]);
    }

    public function edit(Capa $capa)
    {
        $authEmployee = Auth::guard('employee')->user();
        if ($capa->company_id !== $authEmployee->company_id) {
            abort(403);
        }

        $capa->load(['department', 'assignee', 'creator']);

        $departments = Department::query()
            ->where('company_id', $authEmployee->company_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $employees = Employee::query()
            ->where('company_id', $authEmployee->company_id)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get(['id', 'fname', 'lname', 'email']);

        return Inertia::render('Companies/Capa/Edit', [
            'capa' => $capa,
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Capa $capa)
    {
        $authEmployee = Auth::guard('employee')->user();
        if ($capa->company_id !== $authEmployee->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:corrective,preventive',
            'status' => 'required|in:open,in_progress,closed',
            'priority' => 'required|in:low,medium,high',
            'department_id' => 'nullable|integer|exists:departments,id',
            'assigned_to' => 'nullable|integer|exists:employees,id',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'preventive_action' => 'nullable|string',
        ]);

        if (!empty($validated['department_id'])) {
            $department = Department::findOrFail($validated['department_id']);
            if ($department->company_id !== $authEmployee->company_id) {
                abort(403);
            }
        }

        if (!empty($validated['assigned_to'])) {
            $assignee = Employee::findOrFail($validated['assigned_to']);
            if ($assignee->company_id !== $authEmployee->company_id) {
                abort(403);
            }
        }

        if (($validated['status'] ?? null) === 'closed') {
            $validated['closed_at'] = $capa->closed_at ?? now();
        } else {
            $validated['closed_at'] = null;
        }

        $capa->update($validated);

        return redirect()->route('companies.capa.show', $capa->id)
            ->with('success', 'CAPA updated successfully');
    }

    public function destroy(Capa $capa)
    {
        $authEmployee = Auth::guard('employee')->user();
        if ($capa->company_id !== $authEmployee->company_id) {
            abort(403);
        }

        $capa->delete();

        return redirect()->route('companies.capa.index')
            ->with('success', 'CAPA deleted successfully');
    }
}

