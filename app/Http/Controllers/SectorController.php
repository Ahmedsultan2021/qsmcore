<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Models\Industry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sector::with(['industry', 'companies']);
        
        // Filter by industry
        if ($request->has('industry_id') && $request->industry_id) {
            $query->where('industry_id', $request->industry_id);
        }
        
        // Search by name or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $sectors = $query->latest()->paginate(10)->withQueryString();
        
        $industries = Industry::all();
        
        return Inertia::render('Admin/Sectors/Index', [
            'sectors' => $sectors,
            'industries' => $industries,
            'filters' => $request->only(['industry_id', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $industries = Industry::all();
        
        return Inertia::render('Admin/Sectors/Create', [
            'industries' => $industries,
            'industry_id' => $request->industry_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'industry_id' => 'required|exists:industries,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $sector = Sector::create($validated);

        return redirect()->route('sectors.index', ['industry_id' => $sector->industry_id])
            ->with('success', 'Sector created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        $sector->load(['industry', 'companies.employees']);
        
        return Inertia::render('Admin/Sectors/Show', [
            'sector' => $sector,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sector $sector)
    {
        $industries = Industry::all();
        
        return Inertia::render('Admin/Sectors/Edit', [
            'sector' => $sector,
            'industries' => $industries,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sector $sector)
    {
        $validated = $request->validate([
            'industry_id' => 'required|exists:industries,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $sector->update($validated);

        return redirect()->route('sectors.index', ['industry_id' => $sector->industry_id])
            ->with('success', 'Sector updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $industryId = $sector->industry_id;
        $sector->delete();

        return redirect()->route('sectors.index', ['industry_id' => $industryId])
            ->with('success', 'Sector deleted successfully');
    }
}
