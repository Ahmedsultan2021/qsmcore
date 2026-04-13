<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('message', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_read', $request->status === 'read');
        }

        $inquiries = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Inquiries/Index', [
            'inquiries'    => $inquiries,
            'unreadCount'  => Inquiry::where('is_read', false)->count(),
            'filters'      => $request->only(['search', 'status']),
        ]);
    }

    public function markRead(Inquiry $inquiry)
    {
        $inquiry->update(['is_read' => true]);
        return back();
    }

    public function markUnread(Inquiry $inquiry)
    {
        $inquiry->update(['is_read' => false]);
        return back();
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return back()->with('success', 'Inquiry deleted.');
    }
}
