<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Industry;
use App\Models\Report;
use App\Models\Risk;
use App\Models\Sector;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $now = now();
        $lastMonth = $now->copy()->subMonth();

        // Entity counts
        $industriesTotal = Industry::count();
        $sectorsTotal = Sector::count();
        $companiesTotal = Company::count();
        $employeesTotal = Employee::count();

        $lastMonthEnd = $lastMonth->copy()->endOfMonth();
        $industriesLastMonth = Industry::where('created_at', '<=', $lastMonthEnd)->count();
        $sectorsLastMonth = Sector::where('created_at', '<=', $lastMonthEnd)->count();
        $companiesLastMonth = Company::where('created_at', '<=', $lastMonthEnd)->count();
        $employeesLastMonth = Employee::where('created_at', '<=', $lastMonthEnd)->count();

        $growth = function ($current, $previous) {
            if ($previous <= 0) {
                return $current > 0 ? 100.0 : 0.0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $stats = [
            'industries' => [
                'total' => $industriesTotal,
                'active' => $industriesTotal,
                'growth' => $growth($industriesTotal, $industriesLastMonth),
            ],
            'sectors' => [
                'total' => $sectorsTotal,
                'active' => $sectorsTotal,
                'growth' => $growth($sectorsTotal, $sectorsLastMonth),
            ],
            'companies' => [
                'total' => $companiesTotal,
                'active' => $companiesTotal,
                'growth' => $growth($companiesTotal, $companiesLastMonth),
            ],
            'employees' => [
                'total' => $employeesTotal,
                'active' => $employeesTotal,
                'growth' => $growth($employeesTotal, $employeesLastMonth),
            ],
        ];

        // Charts: last 12 months (per month)
        $monthLabels = [];
        $companiesPerMonth = [];
        $reportsPerMonth = [];
        $risksPerMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = $now->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthLabels[] = $monthStart->format('M Y');
            $companiesPerMonth[] = Company::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $reportsPerMonth[] = Report::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $risksPerMonth[] = Risk::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        }
        if (array_sum($companiesPerMonth) === 0) {
            $companiesPerMonth[11] = 1;
        }
        if (array_sum($reportsPerMonth) === 0) {
            $reportsPerMonth[11] = 1;
        }
        if (array_sum($risksPerMonth) === 0) {
            $risksPerMonth[11] = 1;
        }

        $chartData = [
            'monthLabels' => $monthLabels,
            'companiesPerMonth' => $companiesPerMonth,
            'reportsPerMonth' => $reportsPerMonth,
            'risksPerMonth' => $risksPerMonth,
        ];

        // Reports: total, this month, pending, completed (by general_report_status or status)
        $reportsTotal = Report::count();
        $reportsThisMonth = Report::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $reportsCompleted = Report::where('status', 'approved')->count();
        $reportsPending = $reportsTotal - $reportsCompleted;

        $reportsData = [
            'total' => $reportsTotal,
            'thisMonth' => $reportsThisMonth,
            'pending' => $reportsPending,
            'completed' => $reportsCompleted,
        ];

        // Risks (incidents): total, this month, resolved (closed/mitigated), pending
        $risksTotal = Risk::count();
        $risksThisMonth = Risk::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count();
        $risksResolved = Risk::whereIn('status', ['closed', 'mitigated'])->count();
        $risksPending = $risksTotal - $risksResolved;

        $incidentsData = [
            'total' => $risksTotal,
            'thisMonth' => $risksThisMonth,
            'resolved' => $risksResolved,
            'pending' => $risksPending,
        ];

        // Recent activities: latest companies, employees, sectors, industries merged and sorted by date
        $recentActivities = collect();
        Company::latest('created_at')->take(5)->get()->each(function ($c) use ($recentActivities) {
            $recentActivities->push([
                'type' => 'company',
                'action' => 'New company registered',
                'name' => $c->name,
                'time' => $c->created_at->diffForHumans(),
                'sort_at' => $c->created_at->timestamp,
            ]);
        });
        Employee::with('company')->latest('created_at')->take(5)->get()->each(function ($e) use ($recentActivities) {
            $recentActivities->push([
                'type' => 'employee',
                'action' => 'New employee added',
                'name' => $e->fname . ' ' . $e->lname,
                'time' => $e->created_at->diffForHumans(),
                'sort_at' => $e->created_at->timestamp,
            ]);
        });
        Sector::with('industry')->latest('created_at')->take(5)->get()->each(function ($s) use ($recentActivities) {
            $recentActivities->push([
                'type' => 'sector',
                'action' => 'Sector updated',
                'name' => $s->name,
                'time' => $s->updated_at->diffForHumans(),
                'sort_at' => $s->updated_at->timestamp,
            ]);
        });
        Industry::latest('created_at')->take(5)->get()->each(function ($i) use ($recentActivities) {
            $recentActivities->push([
                'type' => 'industry',
                'action' => 'Industry created',
                'name' => $i->name,
                'time' => $i->created_at->diffForHumans(),
                'sort_at' => $i->created_at->timestamp,
            ]);
        });

        $recentActivities = $recentActivities->sortByDesc('sort_at')->values()->take(8)->map(function ($a) {
            unset($a['sort_at']);
            return $a;
        })->toArray();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'reportsData' => $reportsData,
            'incidentsData' => $incidentsData,
            'recentActivities' => $recentActivities,
        ]);
    }
}
