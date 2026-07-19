<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Profile (Breeze Livewire-managed Blade view)
    Route::get('/profile', fn() => view('profile'))->name('profile');

    // Projects
    Route::get('/projects/assigned', [\App\Http\Controllers\ProjectController::class, 'assigned'])->name('projects.assigned');
    Route::get('/projects/{project}/progress', [\App\Http\Controllers\ProjectController::class, 'progress'])->name('projects.progress');
    
    // Core project routes (viewable by owner or members, managed by policy)
    Route::resource('projects', \App\Http\Controllers\ProjectController::class)->only(['index', 'create', 'store', 'show']);

    // Owner-only routes
    Route::middleware(['project.owner'])->group(function () {
        Route::get('/projects/{project}/rows', [\App\Http\Controllers\ProjectController::class, 'rows'])->name('projects.rows');
        Route::get('/projects/{project}/annotators', [\App\Http\Controllers\ProjectController::class, 'annotators'])->name('projects.annotators');
        Route::post('/projects/{project}/annotators', [\App\Http\Controllers\ProjectController::class, 'addAnnotator'])->name('projects.annotators.add');
        Route::delete('/projects/{project}/annotators/{user}', [\App\Http\Controllers\ProjectController::class, 'removeAnnotator'])->name('projects.annotators.remove');
        
        Route::resource('projects', \App\Http\Controllers\ProjectController::class)->only(['edit', 'update', 'destroy']);
        
        // Export
        Route::get('/projects/{project}/export/{format}', [App\Http\Controllers\ExportController::class, 'download'])
            ->name('projects.export')
            ->where('format', 'csv|xlsx');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
