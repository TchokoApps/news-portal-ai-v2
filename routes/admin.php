<?php

use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest.admin')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'store'])->name('admin.login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('admin.register');
    Route::post('/register', [RegisterController::class, 'store'])->name('admin.register.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('admin.forgot-password');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('admin.forgot-password.store');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('admin.reset-password.create');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('admin.reset-password.store');
});

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin Profile Routes
    Route::resource('admin-profile', ProfileController::class)->only(['index', 'update'])->names('admin.profile');

    // Category Resource Routes
    Route::patch('/category/toggle-status-field', [CategoryController::class, 'toggleStatusField'])->name('category.toggle-status-field');
    Route::resource('category', CategoryController::class);

    // Language Resource Routes
    Route::patch('/language/toggle-status-field', [LanguageController::class, 'toggleStatusField'])->name('language.toggle-status-field');
    Route::resource('language', LanguageController::class);

    // News Resource Routes - fetch-category must come BEFORE resource route
    Route::get('/news/fetch-category', [NewsController::class, 'fetchCategory'])->name('news.fetch-category');
    Route::patch('/news/toggle-status-field', [NewsController::class, 'toggleStatusField'])->name('news.toggle-status-field');
    Route::post('/news/{news}/toggle-breaking-news', [NewsController::class, 'toggleBreakingNews'])->name('news.toggle-breaking-news');
    Route::post('/news/{news}/toggle-slider', [NewsController::class, 'toggleSlider'])->name('news.toggle-slider');
    Route::post('/news/{news}/toggle-popular', [NewsController::class, 'togglePopular'])->name('news.toggle-popular');
    Route::post('/news/{news}/toggle-status', [NewsController::class, 'toggleStatus'])->name('news.toggle-status');
    Route::post('/news/{news}/clone', [NewsController::class, 'cloneNews'])->name('news.clone');
    Route::resource('news', NewsController::class);

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
