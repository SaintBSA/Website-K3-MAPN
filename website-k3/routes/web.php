<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MasterOptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/profile/settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware(['role:spv'])->group(function () {
        Route::resource('master-options', MasterOptionController::class)->names([
            'index' => 'master.settings',
            'store' => 'master.store',
            'update' => 'master.update',
            'destroy' => 'master.destroy'
        ]);
        
        Route::get('/user/management', [UserController::class, 'index'])->name('user.index');
        Route::put('/user/{user}', [UserController::class, 'updateRole'])->name('user.update.role');
        Route::put('/user/{user}/status', [UserController::class, 'toggleStatus'])->name('user.toggle.status');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    });

    Route::middleware(['role:admin,spv'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show'); 
    });

    Route::middleware(['role:spv'])->group(function () {
        Route::get('/reports/{id}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{id}', [ReportController::class, 'update'])->name('reports.update');
    });
});