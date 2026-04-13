<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('order')->orderBy('name')->paginate(20);

        return Inertia::render('Admin/Partners/Index', [
            'partners' => $partners,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Partners/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'website'   => 'nullable|url|max:255',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'logo'      => 'nullable|image|max:2048',
        ]);

        $partner = Partner::create(collect($validated)->except('logo')->all());

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store("partner-logos/{$partner->id}", 'public');
            $partner->update(['logo_path' => $path]);
        }

        return redirect()->route('partners.index')
            ->with('success', 'Partner added successfully.');
    }

    public function edit(Partner $partner)
    {
        return Inertia::render('Admin/Partners/Edit', [
            'partner' => $partner,
        ]);
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'website'      => 'nullable|url|max:255',
            'order'        => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
            'logo'         => 'nullable|image|max:2048',
            'remove_logo'  => 'nullable|boolean',
        ]);

        $partner->update(collect($validated)->except(['logo', 'remove_logo'])->all());

        if ($request->hasFile('logo')) {
            if ($partner->logo_path) {
                Storage::disk('public')->delete($partner->logo_path);
            }
            $path = $request->file('logo')->store("partner-logos/{$partner->id}", 'public');
            $partner->update(['logo_path' => $path]);
        } elseif ($request->boolean('remove_logo') && $partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
            $partner->update(['logo_path' => null]);
        }

        return redirect()->route('partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        if ($partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
        }
        $partner->delete();

        return back()->with('success', 'Partner deleted.');
    }

    public function toggleActive(Partner $partner)
    {
        $partner->update(['is_active' => !$partner->is_active]);

        return back();
    }
}
