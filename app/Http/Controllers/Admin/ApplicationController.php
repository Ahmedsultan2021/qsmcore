<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,shortlisted,rejected',
        ]);

        $application->update(['status' => $request->status]);

        return back()->with('success', 'Application status updated.');
    }

    public function destroy(Application $application)
    {
        if ($application->cv_path) {
            Storage::disk('public')->delete($application->cv_path);
        }
        $application->delete();

        return back()->with('success', 'Application deleted.');
    }
}
