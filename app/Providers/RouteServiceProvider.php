<?php

namespace App\Providers;

use App\Models\Form;
use App\Models\Report;
use App\Models\ReportFile;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Scope form binding when in report context (departments.reports.forms routes)
        Route::bind('form', function ($value, $route) {
            $report = $route->parameter('report');
            if (! $report instanceof Report) {
                $report = Report::query()->findOrFail($report);
            }
            $formId = is_numeric($value) ? (int) $value : $value;

            return $report->forms()->where('forms.id', $formId)->firstOrFail();
        });

        Route::bind('reportFile', function ($value, $route) {
            $report = $route->parameter('report');
            if ($report instanceof Report) {
                return $report->reportFiles()->findOrFail($value);
            }
            return ReportFile::findOrFail($value);
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Load companies.php first to avoid route conflicts with companies resource route
            Route::middleware('web')
                ->group(base_path('routes/companies.php'));

            // Load web.php after companies.php (Admin Portal routes)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
