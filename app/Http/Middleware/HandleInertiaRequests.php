<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $employee = $request->user('employee');
        if ($employee) {
            $employee->loadMissing([
                'company.sector.industry',
            ]);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'authEmployee' => [
                'employee' => $employee,
            ],
            'impersonating' => $request->session()->get('impersonating', false),
            'flash' => [
                'success'         => $request->session()->get('success'),
                'error'           => $request->session()->get('error'),
                'contact_success' => $request->session()->get('contact_success'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
