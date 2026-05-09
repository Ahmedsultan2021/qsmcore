<?php

use App\Http\Controllers\Companies\CompanyEmployeeController;
use App\Http\Controllers\Companies\CompanyDepartmentController;
use App\Http\Controllers\Companies\CompanyReportController;
use App\Http\Controllers\Companies\CompanyFormController;
use App\Http\Controllers\Companies\CompanyFormFieldController;
use App\Http\Controllers\Companies\CompanyFormSubmissionController;
use App\Http\Controllers\Companies\CompanyReportFileController;
use App\Http\Controllers\Companies\CompanyRiskController;
use App\Http\Controllers\Companies\CompanyAuditController;
use App\Http\Controllers\Companies\CompanyCapaController;
use App\Http\Controllers\Auth\EmployeeAuthController;
use App\Models\Capa;
use App\Models\Department;
use App\Models\HelpDocument;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Companies Portal Routes
|--------------------------------------------------------------------------
| All routes related to the Companies Portal (employee authentication and management)
| These routes are loaded before web.php to avoid conflicts with resource routes
|
*/

// Companies Portal Authentication Routes
// Login route without guest middleware - we check authentication in controller
Route::get('/companies/login', [EmployeeAuthController::class, 'create'])->name('companies.login');
Route::post('/companies/login', [EmployeeAuthController::class, 'store'])->middleware('guest:employee');

// Companies Portal Authenticated Routes
Route::middleware('auth:employee')->prefix('companies')->name('companies.')->group(function () {
    Route::post('/logout', [EmployeeAuthController::class, 'destroy'])->name('logout');
    Route::post('/leave-impersonation', [EmployeeAuthController::class, 'leaveImpersonation'])->name('leave-impersonation');
    Route::get('/ping', fn () => response()->json(['ok' => true]))->name('ping');
    
    Route::get('/dashboard', function () {
        $authEmployee = Auth::guard('employee')->user();
        $companyId    = $authEmployee->company_id;

        $departments = Department::query()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $deptIds = $departments->pluck('id');

        // KPI counts — reports tied to departments that have at least one template with Safety / Quality theme
        $openSafety = Report::whereIn('department_id', $deptIds)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->whereHas('department.formTemplates.themes', fn ($q) => $q->where('form_themes.slug', 'safety'))
            ->count();

        $openQuality = Report::whereIn('department_id', $deptIds)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->whereHas('department.formTemplates.themes', fn ($q) => $q->where('form_themes.slug', 'quality'))
            ->count();

        $closedReports = Report::whereIn('department_id', $deptIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->count();

        $overdueCAPAs = Capa::where('company_id', $companyId)
            ->where('status', '!=', 'closed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->count();

        // Report status breakdown (draft / submitted / reviewed / approved / rejected)
        $statusCounts = Report::whereIn('department_id', $deptIds)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        // Recent 5 reports
        $recentReports = Report::whereIn('department_id', $deptIds)
            ->with('department')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'id'     => $r->id,
                'title'  => $r->title,
                'dept'   => $r->department?->name,
                'status' => $r->status,
                'date'   => $r->report_date?->format('d-M-Y'),
                'url'    => route('companies.departments.reports.show', [$r->department_id, $r->id]),
            ]);

        // Open safety reports (matches openSafety KPI logic) — used by the "Open Safety Reports" sidebar
        $openSafetyAlerts = Report::whereIn('department_id', $deptIds)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->whereHas('department.formTemplates.themes', fn ($q) => $q->where('form_themes.slug', 'safety'))
            ->with('department')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($r) => [
                'id'     => $r->id,
                'title'  => $r->title,
                'status' => $r->status,
                'url'    => route('companies.departments.reports.show', [$r->department_id, $r->id]),
            ]);

        // Top 5 departments by report volume (with percentage)
        $rawVolume = Report::whereIn('department_id', $deptIds)
            ->selectRaw('department_id, COUNT(*) as cnt')
            ->groupBy('department_id')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        $totalVol = $rawVolume->sum('cnt') ?: 1;
        $volumeByDept = $rawVolume->map(fn ($r) => [
            'name'    => $departments->firstWhere('id', $r->department_id)?->name ?? 'Unknown',
            'count'   => $r->cnt,
            'percent' => (int) round($r->cnt / $totalVol * 100),
        ]);

        return Inertia::render('Companies/Dashboard', [
            'departments'   => $departments,
            'stats'         => [
                'openSafety'    => $openSafety,
                'openQuality'   => $openQuality,
                'closedReports' => $closedReports,
                'overdueCAPAs'  => $overdueCAPAs,
            ],
            'statusCounts'     => $statusCounts,
            'recentReports'    => $recentReports,
            'openSafetyAlerts' => $openSafetyAlerts,
            'volumeByDept'     => $volumeByDept,
        ]);
    })->name('dashboard');

    // CAPA Management
    Route::resource('capa', CompanyCapaController::class);

    Route::get('/settings', function () {
        return Inertia::render('Companies/Settings');
    })->name('settings');

    // Help — list active help documents for the company portal user
    Route::get('/help', function () {
        $documents = HelpDocument::active()
            ->orderBy('sort_order')
            ->latest('id')
            ->get();

        return Inertia::render('Companies/Help', [
            'documents' => $documents,
        ]);
    })->name('help');
    
    // Employees routes (for companies portal - with roles & permissions)
    Route::resource('employees', CompanyEmployeeController::class);
    
    // Departments routes (for companies portal)
    Route::post('departments/seed-defaults', [CompanyDepartmentController::class, 'seedDefaults'])->name('departments.seed-defaults');
    Route::resource('departments', CompanyDepartmentController::class);
    
    // Risks routes (for risk register)
    Route::resource('risks', CompanyRiskController::class);
    Route::get('departments/{department}/risks', [CompanyRiskController::class, 'departmentRisks'])->name('departments.risks.index');
    
    // Reports routes (nested under departments) - scoped so report must belong to department
    Route::resource('departments.reports', CompanyReportController::class)->scoped();
    Route::get('reports', [CompanyReportController::class, 'allReports'])->name('reports.index');
    Route::get('reports/export/excel', [CompanyReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('reports/export/pdf',   [CompanyReportController::class, 'exportPdf'])->name('reports.export.pdf');
    
    // Audits routes (audit tracker)
    Route::resource('audits', CompanyAuditController::class);
    
    // Forms routes (for companies portal)
    Route::get('forms/templates/bank', [CompanyFormController::class, 'templateBank'])->name('forms.templates.bank');
    Route::post('forms/from-template', [CompanyFormController::class, 'addFromTemplate'])->name('forms.from-template');
    Route::resource('forms', CompanyFormController::class);
    Route::get('forms/template/download', [CompanyFormController::class, 'downloadTemplate'])->name('forms.template.download');
    Route::post('forms/{form}/import', [CompanyFormController::class, 'importFields'])->name('forms.import');
    
    // Form Fields routes (nested under forms)
    Route::post('forms/{form}/fields', [CompanyFormFieldController::class, 'store'])->name('forms.fields.store');
    Route::put('forms/{form}/fields/{formField}', [CompanyFormFieldController::class, 'update'])->name('forms.fields.update');
    Route::delete('forms/{form}/fields/{formField}', [CompanyFormFieldController::class, 'destroy'])->name('forms.fields.destroy');
    Route::post('forms/{form}/fields/update-order', [CompanyFormFieldController::class, 'updateOrder'])->name('forms.fields.update-order');
    
    // Report file uploads (treat report as a folder for attachments)
    Route::post('departments/{department}/reports/{report}/files', [CompanyReportFileController::class, 'store'])->name('departments.reports.files.store')->scopeBindings();
    Route::get('departments/{department}/reports/{report}/files/{reportFile}/download', [CompanyReportFileController::class, 'download'])->name('departments.reports.files.download')->scopeBindings();
    Route::delete('departments/{department}/reports/{report}/files/{reportFile}', [CompanyReportFileController::class, 'destroy'])->name('departments.reports.files.destroy')->scopeBindings();

    // Form Submissions routes (nested under departments.reports.forms)
    Route::get('departments/{department}/reports/{report}/forms/{form}/create', [CompanyFormSubmissionController::class, 'create'])->name('departments.reports.forms.create');
    Route::post('departments/{department}/reports/{report}/forms/{form}', [CompanyFormSubmissionController::class, 'store'])->name('departments.reports.forms.store');
    Route::get('departments/{department}/reports/{report}/forms/{form}', [CompanyFormSubmissionController::class, 'show'])->name('departments.reports.forms.show');
});

// Legacy Employee Routes (for backward compatibility - redirects to companies portal)
Route::middleware('guest:employee')->group(function () {
    Route::get('/employee/login', function () {
        return redirect()->route('companies.login');
    })->name('employee.login');
    Route::post('/employee/login', [EmployeeAuthController::class, 'store']);
});

// Legacy employee dashboard route - redirects to companies dashboard
Route::get('/employee/dashboard', function () {
    // If authenticated as employee, redirect to companies dashboard
    if (auth()->guard('employee')->check()) {
        return redirect()->route('companies.dashboard');
    }
    // If not authenticated, redirect to companies login
    return redirect()->route('companies.login');
})->name('employee.dashboard');

Route::middleware('auth:employee')->group(function () {
    Route::post('/employee/logout', function () {
        return redirect()->route('companies.logout');
    })->name('employee.logout');
});
