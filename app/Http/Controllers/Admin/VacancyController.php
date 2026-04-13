<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VacancyController extends Controller
{
    public function index()
    {
        $vacancies = Vacancy::withCount('applications')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Vacancies/Index', [
            'vacancies' => $vacancies,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Vacancies/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'department'   => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'type'         => 'required|in:full-time,part-time,contract,remote',
            'description'  => 'required|string',
            'requirements' => 'nullable|string',
            'benefits'     => 'nullable|string',
            'salary_range' => 'nullable|string|max:100',
            'closing_date' => 'nullable|date|after:today',
            'is_active'    => 'boolean',
        ]);

        Vacancy::create($validated);

        return redirect()->route('vacancies.index')
            ->with('success', 'Vacancy created successfully.');
    }

    public function show(Vacancy $vacancy)
    {
        $vacancy->load(['applications' => fn ($q) => $q->latest()]);

        return Inertia::render('Admin/Vacancies/Show', [
            'vacancy' => $vacancy,
        ]);
    }

    public function edit(Vacancy $vacancy)
    {
        return Inertia::render('Admin/Vacancies/Edit', [
            'vacancy' => $vacancy,
        ]);
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'department'   => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'type'         => 'required|in:full-time,part-time,contract,remote',
            'description'  => 'required|string',
            'requirements' => 'nullable|string',
            'benefits'     => 'nullable|string',
            'salary_range' => 'nullable|string|max:100',
            'closing_date' => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        $vacancy->update($validated);

        return redirect()->route('vacancies.index')
            ->with('success', 'Vacancy updated successfully.');
    }

    public function destroy(Vacancy $vacancy)
    {
        $vacancy->delete();

        return redirect()->route('vacancies.index')
            ->with('success', 'Vacancy deleted successfully.');
    }

    public function toggleActive(Vacancy $vacancy)
    {
        $vacancy->update(['is_active' => !$vacancy->is_active]);

        return back()->with('success', 'Vacancy ' . ($vacancy->is_active ? 'activated' : 'deactivated') . '.');
    }
}
