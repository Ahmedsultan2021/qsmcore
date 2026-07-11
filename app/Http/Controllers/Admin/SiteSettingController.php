<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteSettingController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/SiteSettings/Edit', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'contact_email'   => 'nullable|email|max:255',
            'contact_phone'   => 'nullable|string|max:255',
            'contact_address' => 'nullable|string|max:1000',
        ]);

        SiteSetting::current()->update($validated);

        return redirect()->route('site-settings.edit')
            ->with('success', 'Site contact details updated successfully.');
    }
}
