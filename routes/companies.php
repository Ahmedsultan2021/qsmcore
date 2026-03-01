<?php

use App\Http\Controllers\Companies\CompanyEmployeeController;
use App\Http\Controllers\Companies\CompanyDepartmentController;
use App\Http\Controllers\Companies\CompanyReportController;
use App\Http\Controllers\Companies\CompanyFormController;
use App\Http\Controllers\Companies\CompanyFormFieldController;
use App\Http\Controllers\Companies\CompanyFormSubmissionController;
use App\Http\Controllers\Companies\CompanyRiskController;
use App\Http\Controllers\Companies\CompanyAuditController;
use App\Http\Controllers\Auth\EmployeeAuthController;
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
    
    Route::get('/dashboard', function () {
        return Inertia::render('Companies/Dashboard');
    })->name('dashboard');
    
    // Employees routes (for companies portal - with roles & permissions)
    Route::resource('employees', CompanyEmployeeController::class);
    
    // Departments routes (for companies portal)
    Route::resource('departments', CompanyDepartmentController::class);
    
    // Risks routes (for risk register)
    Route::resource('risks', CompanyRiskController::class);
    Route::get('departments/{department}/risks', [CompanyRiskController::class, 'departmentRisks'])->name('departments.risks.index');
    
    // Reports routes (nested under departments)
    Route::resource('departments.reports', CompanyReportController::class);
    Route::get('reports', [CompanyReportController::class, 'allReports'])->name('reports.index');
    
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
