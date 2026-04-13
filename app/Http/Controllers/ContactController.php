<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'message' => 'required|string|max:3000',
        ]);

        // Store in database
        Inquiry::create($validated);

        // Send email notification
        try {
            Mail::raw(
                implode("\n", [
                    "New inquiry from QSMCore website",
                    "-----------------------------------",
                    "Name:    {$validated['name']}",
                    "Email:   {$validated['email']}",
                    "Phone:   " . ($validated['phone'] ?? 'Not provided'),
                    "",
                    "Message:",
                    $validated['message'],
                ]),
                function ($mail) use ($validated) {
                    $mail->to('support@qsm.com')
                         ->replyTo($validated['email'], $validated['name'])
                         ->subject("New Inquiry from {$validated['name']}");
                }
            );
        } catch (\Exception $e) {
            // Log but don't fail — inquiry is already saved to DB
            \Log::warning('Contact email failed: ' . $e->getMessage());
        }

        return back()->with('contact_success', true);
    }
}
