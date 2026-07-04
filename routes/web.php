<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


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

    // Datasets
    Route::resource('datasets', \App\Http\Controllers\DatasetController::class)
        ->except(['index'])
        ->names('datasets');
    Route::get('/datasets', [\App\Http\Controllers\DatasetController::class, 'index'])->name('datasets.index');

    // Projects
    Route::get('/projects/assigned', [\App\Http\Controllers\ProjectController::class, 'assigned'])->name('projects.assigned');
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    // Export
    Route::get('/projects/{project}/export/{format}', [App\Http\Controllers\ExportController::class, 'download'])
        ->name('projects.export')
        ->where('format', 'csv|xlsx');
});

require __DIR__ . '/auth.php';
