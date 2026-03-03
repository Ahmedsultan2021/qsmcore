<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
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
    
    return Inertia::render('Welcome', [
        'blogPosts' => $blogPosts,
    ]);
})->name('welcome');

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
    
    // Companies Management (Admin CRUD - no roles/permissions access)
    Route::resource('companies', CompanyController::class);
    
    // Employees Management (Admin CRUD - no roles/permissions access)
    Route::resource('employees', EmployeeController::class);
    
    // Blog Posts Management (Admin CRUD)
    Route::resource('blog-posts', \App\Http\Controllers\Admin\BlogPostController::class);
});

// Admin Authentication Routes (loaded from auth.php)
require __DIR__ . '/auth.php';
