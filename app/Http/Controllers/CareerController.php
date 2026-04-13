<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CareerController extends Controller
{
    public function index()
    {
        $vacancies = Vacancy::active()
            ->withCount('applications')
            ->latest()
            ->get();

        return Inertia::render('Careers/Index', [
            'vacancies' => $vacancies,
        ]);
    }

    public function show(Vacancy $vacancy)
    {
        abort_unless($vacancy->is_active, 404);

        return Inertia::render('Careers/Show', [
            'vacancy'        => $vacancy,
            'apply_success'  => session('apply_success'),
        ]);
    }

    public function apply(Request $request, Vacancy $vacancy)
    {
        abort_unless($vacancy->is_active, 404);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'cover_letter' => 'nullable|string|max:3000',
            'cv'           => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $cvPath = $request->file('cv')->store("cvs/{$vacancy->id}", 'public');

        Application::create([
            'vacancy_id'   => $vacancy->id,
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone'        => $validated['phone'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_path'      => $cvPath,
        ]);

        return redirect()->route('careers.show', $vacancy)
            ->with('apply_success', true);
    }
}
