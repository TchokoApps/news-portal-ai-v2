<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Stub routes for sidebar testing
    Route::get('/articles', function () {
        return view('admin.articles.index');
    })->name('admin.articles.index');

    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('admin.users.index');

    Route::get('/roles', function () {
        return view('admin.roles.index');
    })->name('admin.roles.index');

    Route::get('/settings', function () {
        return view('admin.settings.index');
    })->name('admin.settings.index');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('admin.logout');
});
