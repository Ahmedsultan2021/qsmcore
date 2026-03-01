<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['company.sector.industry']);
        
        // Filter by company
        if ($request->has('company_id') && $request->company_id) {
            $query->where('company_id', $request->company_id);
        }
        
        // Filter by sector
        if ($request->has('sector_id') && $request->sector_id) {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('sector_id', $request->sector_id);
            });
        }
        
        // Filter by industry
        if ($request->has('industry_id') && $request->industry_id) {
            $query->whereHas('company.sector', function ($q) use ($request) {
                $q->where('industry_id', $request->industry_id);
            });
        }
        
        // Search by name, email, or position
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }
        
        $employees = $query->latest()->paginate(10)->withQueryString();
        
        $companies = Company::with(['sector.industry'])->get();
        $sectors = \App\Models\Sector::with('industry')->get();
        $industries = \App\Models\Industry::all();
        
        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
            'companies' => $companies,
            'sectors' => $sectors,
            'industries' => $industries,
            'filters' => $request->only(['company_id', 'sector_id', 'industry_id', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $companies = Company::with(['sector.industry'])->get();
        
        return Inertia::render('Admin/Employees/Create', [
            'companies' => $companies,
            'company_id' => $request->company_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $employee = Employee::create($validated);

        return redirect()->route('employees.index', ['company_id' => $employee->company_id])
            ->with('success', 'Employee created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load('company.sector.industry');
        
        return Inertia::render('Admin/Employees/Show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $companies = Company::with(['sector.industry'])->get();
        
        return Inertia::render('Admin/Employees/Edit', [
            'employee' => $employee,
            'companies' => $companies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id . '|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        if (isset($validated['password'])) {
            // dd('hi');
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return redirect()->route('employees.index', ['company_id' => $employee->company_id])
            ->with('success', 'Employee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $companyId = $employee->company_id;
        $employee->delete();

        return redirect()->route('employees.index', ['company_id' => $companyId])
            ->with('success', 'Employee deleted successfully');
    }
}
