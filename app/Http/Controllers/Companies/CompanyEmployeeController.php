<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class CompanyEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        $employees = Employee::where('company_id', $employee->company_id)
            ->with(['company', 'roles'])
            ->latest()
            ->paginate(10);
        
        $roles = Role::where('guard_name', 'employee')->get();
        
        return Inertia::render('Companies/Employees/Index', [
            'employees' => $employees,
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employee = Auth::guard('employee')->user();
        $roles = Role::where('guard_name', 'employee')->get();
        
        return Inertia::render('Companies/Employees/Create', [
            'company_id' => $employee->company_id,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employee = Auth::guard('employee')->user();
        
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['company_id'] = $employee->company_id;

        $newEmployee = Employee::create($validated);
        
        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $newEmployee->assignRole($roles);
        }

        return redirect()->route('companies.employees.index')
            ->with('success', 'Employee created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure employee belongs to same company
        if ($employee->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $employee->load(['company.sector.industry', 'roles']);
        
        return Inertia::render('Companies/Employees/Show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure employee belongs to same company
        if ($employee->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $roles = Role::where('guard_name', 'employee')->get();
        $employee->load('roles');
        
        return Inertia::render('Companies/Employees/Edit', [
            'employee' => $employee,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure employee belongs to same company
        if ($employee->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id . '|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);
        
        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $employee->syncRoles($roles);
        } else {
            $employee->syncRoles([]);
        }

        return redirect()->route('companies.employees.index')
            ->with('success', 'Employee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $authEmployee = Auth::guard('employee')->user();
        
        // Ensure employee belongs to same company
        if ($employee->company_id !== $authEmployee->company_id) {
            abort(403);
        }
        
        $employee->delete();

        return redirect()->route('companies.employees.index')
            ->with('success', 'Employee deleted successfully');
    }
}
