<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Sector;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Company::with(['sector.industry', 'employees']);
        
        // Filter by sector
        if ($request->has('sector_id') && $request->sector_id) {
            $query->where('sector_id', $request->sector_id);
        }
        
        // Filter by industry
        if ($request->has('industry_id') && $request->industry_id) {
            $query->whereHas('sector', function ($q) use ($request) {
                $q->where('industry_id', $request->industry_id);
            });
        }
        
        // Search by name, email, or phone
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $companies = $query->latest()->paginate(10)->withQueryString();
        
        $sectors = Sector::with('industry')->get();
        $industries = \App\Models\Industry::all();
        
        return Inertia::render('Admin/Companies/Index', [
            'companies' => $companies,
            'sectors' => $sectors,
            'industries' => $industries,
            'filters' => $request->only(['sector_id', 'industry_id', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $sectors = Sector::with('industry')->get();
        
        return Inertia::render('Admin/Companies/Create', [
            'sectors' => $sectors,
            'sector_id' => $request->sector_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $company = Company::create($validated);

        return redirect()->route('companies.index', ['sector_id' => $company->sector_id])
            ->with('success', 'Company created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $company->load(['sector.industry', 'employees']);
        
        return Inertia::render('Admin/Companies/Show', [
            'company' => $company,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $sectors = Sector::with('industry')->get();
        
        return Inertia::render('Admin/Companies/Edit', [
            'company' => $company,
            'sectors' => $sectors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $company->update($validated);

        return redirect()->route('companies.index', ['sector_id' => $company->sector_id])
            ->with('success', 'Company updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $sectorId = $company->sector_id;
        $company->delete();

        return redirect()->route('companies.index', ['sector_id' => $sectorId])
            ->with('success', 'Company deleted successfully');
    }
}
