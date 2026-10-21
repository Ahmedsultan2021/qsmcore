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
        ->with('user:id,fname,lname')
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
        ->with('user:id,fname,lname')
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

    // Global form template library
    Route::get('form-templates', [\App\Http\Controllers\Admin\FormTemplateController::class, 'index'])->name('form-templates.index');
    Route::get('form-templates/create', [\App\Http\Controllers\Admin\FormTemplateController::class, 'create'])->name('form-templates.create');
    Route::post('form-templates', [\App\Http\Controllers\Admin\FormTemplateController::class, 'store'])->name('form-templates.store');
    Route::get('form-templates/template/download', [\App\Http\Controllers\Admin\FormTemplateController::class, 'downloadTemplate'])->name('form-templates.template.download');
    Route::get('form-templates/{formTemplate}', [\App\Http\Controllers\Admin\FormTemplateController::class, 'show'])->name('form-templates.show');
    Route::match(['put', 'patch'], 'form-templates/{formTemplate}', [\App\Http\Controllers\Admin\FormTemplateController::class, 'update'])->name('form-templates.update');
    Route::delete('form-templates/{formTemplate}', [\App\Http\Controllers\Admin\FormTemplateController::class, 'destroy'])->name('form-templates.destroy');
    Route::post('form-templates/{formTemplate}/import', [\App\Http\Controllers\Admin\FormTemplateController::class, 'importFields'])->name('form-templates.import');
    Route::post('form-templates/{formTemplate}/fields/update-order', [\App\Http\Controllers\Admin\FormTemplateFieldController::class, 'updateOrder'])->name('form-templates.fields.update-order');
    Route::post('form-templates/{formTemplate}/fields', [\App\Http\Controllers\Admin\FormTemplateFieldController::class, 'store'])->name('form-templates.fields.store');
    Route::put('form-templates/{formTemplate}/fields/{formTemplateField}', [\App\Http\Controllers\Admin\FormTemplateFieldController::class, 'update'])->name('form-templates.fields.update');
    Route::delete('form-templates/{formTemplate}/fields/{formTemplateField}', [\App\Http\Controllers\Admin\FormTemplateFieldController::class, 'destroy'])->name('form-templates.fields.destroy');
    
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

    // Public site contact details
    Route::get('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'edit'])->name('site-settings.edit');
    Route::put('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('site-settings.update');
});

// Admin Authentication Routes (loaded from auth.php)
require __DIR__ . '/auth.php';
