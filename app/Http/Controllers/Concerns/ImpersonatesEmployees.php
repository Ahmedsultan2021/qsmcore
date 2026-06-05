<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

trait ImpersonatesEmployees
{
    protected function loginAsEmployee(Employee $employee, string $returnUrl): RedirectResponse
    {
        $employee->loadMissing('company');

        if (! $employee->is_active) {
            return back()->with('error', 'Cannot login as a deactivated employee account.');
        }

        if (! $employee->company?->is_active) {
            return back()->with('error', 'Cannot login as an employee of a deactivated company.');
        }

        Auth::guard('employee')->login($employee);

        session([
            'impersonating' => true,
            'impersonation_return_url' => $returnUrl,
        ]);

        return redirect()->route('companies.dashboard');
    }
}
