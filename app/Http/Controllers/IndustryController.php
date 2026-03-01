<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $industries = Industry::withCount(['sectors', 'companies'])->latest()->paginate(10);
        
        return Inertia::render('Admin/Industries/Index', [
            'industries' => $industries,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Industries/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Industry::create($validated);

        return redirect()->route('industries.index')
            ->with('success', 'Industry created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Industry $industry)
    {
        $industry->load(['sectors.companies']);
        
        return Inertia::render('Admin/Industries/Show', [
            'industry' => $industry,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Industry $industry)
    {
        return Inertia::render('Admin/Industries/Edit', [
            'industry' => $industry,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Industry $industry)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $industry->update($validated);

        return redirect()->route('industries.index')
            ->with('success', 'Industry updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Industry $industry)
    {
        $industry->delete();

        return redirect()->route('industries.index')
            ->with('success', 'Industry deleted successfully');
    }
}
