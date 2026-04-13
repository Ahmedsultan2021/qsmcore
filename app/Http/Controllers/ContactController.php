<?php

namespace App\Http\Controllers;

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

        return back()->with('contact_success', true);
    }
}
