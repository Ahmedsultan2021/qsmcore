<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class EmployeeAuthController extends Controller
{
    /**
     * Show the login form for employees.
     */
    public function create()
    {
        // If already authenticated as employee, redirect to companies dashboard
        if (Auth::guard('employee')->check()) {
            return redirect()->route('companies.dashboard');
        }
        
        // Check if this is companies login route
        if (request()->routeIs('companies.login')) {
            return Inertia::render('Auth/CompanyLogin');
        }
        
        return Inertia::render('Auth/EmployeeLogin');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::guard('employee')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $employee = Auth::guard('employee')->user();

        // Block deactivated employee accounts
        if (!$employee->is_active) {
            Auth::guard('employee')->logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact your administrator.',
            ]);
        }

        // Block employees of deactivated companies
        if (!$employee->company?->is_active) {
            Auth::guard('employee')->logout();
            throw ValidationException::withMessages([
                'email' => 'Your company account has been deactivated. Please contact your administrator.',
            ]);
        }

        $request->session()->regenerate();

        // All employee logins go to companies dashboard
        return redirect()->route('companies.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // All employee logouts redirect to companies login
        return redirect()->route('companies.login');
    }

    /**
     * Leave admin impersonation: log out the employee guard and return to admin panel.
     */
    public function leaveImpersonation(Request $request)
    {
        if (! $request->session()->get('impersonating')) {
            return redirect()->route('companies.dashboard');
        }

        $returnUrl = $request->session()->pull('impersonation_return_url', route('companies.index'));

        Auth::guard('employee')->logout();
        $request->session()->forget('impersonating');

        if (! Auth::guard('web')->check()) {
            return redirect()->route('login')
                ->with('error', 'Your admin session has expired. Please log in again.');
        }

        return redirect($returnUrl);
    }
}
