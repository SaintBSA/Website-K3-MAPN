<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MasterOptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/profile/settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

    // Rute untuk Laporan (yang juga membutuhkan otorisasi role)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    });

    Route::middleware(['role:spv'])->group(function () {
        Route::get('/reports/{id}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{id}', [ReportController::class, 'update'])->name('reports.update');
    });

    // Rute yang dapat diakses oleh Admin dan SPV
    Route::middleware(['role:admin,spv'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    });
});Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Rute untuk Laporan (yang juga membutuhkan otorisasi role)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    });

    Route::middleware(['role:spv'])->group(function () {
        Route::get('/user/management', [UserController::class, 'index'])->name('user.index');
        Route::put('/user/{user}', [UserController::class, 'updateRole'])->name('user.update.role');
        Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // Rute yang dapat diakses oleh Admin dan SPV
    Route::middleware(['role:admin,spv'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    });

    Route::middleware(['role:spv'])->group(function () {
    Route::resource('master-options', MasterOptionController::class)->names([
        'index' => 'master.settings',
        'store' => 'master.store',
        'update' => 'master.update',
        'destroy' => 'master.destroy'
    ]);
});
});
