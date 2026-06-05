<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CareerController;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
| All routes related to the Admin Portal (admin authentication and management)
| These routes handle Industries, Sectors, Companies, and Employees CRUD operations
|
*/

// Public Routes
Route::get('/', function () {
    $blogPosts = BlogPost::published()
        ->with('user')
        ->latest('published_at')
        ->take(6)
        ->get();
    
    $industries = \App\Models\Industry::orderBy('name')->get();
    $partners   = \App\Models\Company::where('is_active', true)->orderBy('name')->get();

    return Inertia::render('Welcome', [
        'blogPosts'  => $blogPosts,
        'industries' => $industries,
        'partners'   => $partners,
    ]);
})->name('welcome');

// Contact form submission
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Careers (public)
Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::get('/careers/{vacancy}', [CareerController::class, 'show'])->name('careers.show');
Route::post('/careers/{vacancy}/apply', [CareerController::class, 'apply'])->name('careers.apply');

// Blog Listing Page
Route::get('/blog', function () {
    $blogPosts = BlogPost::published()
        ->with('user')
        ->latest('published_at')
        ->paginate(9);
    
    return Inertia::render('Blog', [
        'blogPosts' => $blogPosts,
    ]);
})->name('blog.index');

// Admin Dashboard
Route::get('/dashboard', \App\Http\Controllers\Admin\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin Authenticated Routes
Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Industries Management (Admin CRUD)
    Route::resource('industries', IndustryController::class);
    
    // Sectors Management (Admin CRUD)
    Route::resource('sectors', SectorController::class);

    // Global form template library (read-only index)
    Route::get('form-templates', [\App\Http\Controllers\Admin\FormTemplateController::class, 'index'])->name('form-templates.index');
    
    // Companies Management (Admin CRUD - no roles/permissions access)
    Route::post('companies/{company}/impersonate', [CompanyController::class, 'impersonate'])->name('companies.impersonate');
    Route::post('companies/{company}/toggle-active', [CompanyController::class, 'toggleActive'])->name('companies.toggle-active');
    Route::resource('companies', CompanyController::class);
    
    // Employees Management (Admin CRUD - no roles/permissions access)
    Route::post('employees/{employee}/impersonate', [EmployeeController::class, 'impersonate'])->name('employees.impersonate');
    Route::post('employees/{employee}/toggle-active', [EmployeeController::class, 'toggleActive'])->name('employees.toggle-active');
    Route::resource('employees', EmployeeController::class);
    
    // Blog Posts Management (Admin CRUD)
    Route::resource('blog-posts', \App\Http\Controllers\Admin\BlogPostController::class);

    // Vacancies & Applications (Admin)
    Route::post('vacancies/{vacancy}/toggle-active', [\App\Http\Controllers\Admin\VacancyController::class, 'toggleActive'])->name('vacancies.toggle-active');
    Route::resource('vacancies', \App\Http\Controllers\Admin\VacancyController::class);
    Route::patch('applications/{application}/status', [\App\Http\Controllers\Admin\ApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::delete('applications/{application}', [\App\Http\Controllers\Admin\ApplicationController::class, 'destroy'])->name('applications.destroy');

    // Inquiries (from public contact form)
    Route::get('inquiries', [\App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('inquiries.index');
    Route::post('inquiries/{inquiry}/mark-read', [\App\Http\Controllers\Admin\InquiryController::class, 'markRead'])->name('inquiries.mark-read');
    Route::post('inquiries/{inquiry}/mark-unread', [\App\Http\Controllers\Admin\InquiryController::class, 'markUnread'])->name('inquiries.mark-unread');
    Route::delete('inquiries/{inquiry}', [\App\Http\Controllers\Admin\InquiryController::class, 'destroy'])->name('inquiries.destroy');

    // Help Documents (Admin CRUD - shown to companies portal users on Help page)
    Route::resource('help-documents', \App\Http\Controllers\Admin\HelpDocumentController::class)
        ->except(['show']);
});

// Admin Authentication Routes (loaded from auth.php)
require __DIR__ . '/auth.php';
