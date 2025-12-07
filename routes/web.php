<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnonymousReportController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/feed', function () {
    return view('feed');
})->name('feed');

// Report Pages
Route::get('/report-form', function () {
    return view('report-form');
})->name('report-form');

// Report Routes (using controllers)
Route::get('/report-anon', [AnonymousReportController::class, 'create'])->name('report-anon');
Route::post('/report-anon', [AnonymousReportController::class, 'store'])->name('report-anon.store');

Route::middleware('auth')->group(function () {
    Route::get('/report', [ReportController::class, 'create'])->name('report.create');
    Route::post('/report', [ReportController::class, 'store'])->name('report.store');
    Route::get('/report/{id}', [ReportController::class, 'show'])->name('report.show');
    Route::put('/report/{id}', [ReportController::class, 'update'])->name('report.update');
});

// Location API Endpoints
Route::get('/api/lgus', function () {
    return \App\Models\Lgu::where('province', 'Davao del Norte')
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'code']);
});

Route::get('/api/lgus/{lguId}/barangays', function ($lguId) {
    return \App\Models\Barangay::where('lgu_id', $lguId)
        ->where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'code']);
});

// Authentication Routes (using controllers)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Routes (protected with auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/user-dashboard', function () {
        return view('auth.user-dashboard', [
            'userReports' => collect([]), // Empty collection for now
            'totalUserReports' => 0,
            'pendingCount' => 0,
        ]);
    })->name('user-dashboard');

    Route::get('/admin-dashboard', function () {
        return view('auth.admin-dashboard', [
            'reports' => collect([]), // Empty collection for now
            'totalReports' => 0,
            'pendingReports' => 0,
            'inReviewReports' => 0,
            'resolvedReports' => 0,
        ]);
    })->name('admin-dashboard');

    Route::get('/lgu-dashboard', function () {
        return view('auth.lgu-dashboard', [
            'lguReports' => collect([]), // Empty collection for now
            'totalAssigned' => 0,
            'pendingAssigned' => 0,
            'inProgressAssigned' => 0,
            'fixedAssigned' => 0,
            'verifiedAssigned' => 0,
        ]);
    })->name('lgu-dashboard');

    Route::get('/admin-settings', function () {
        return view('auth.admin-settings', [
            'users' => collect([]), // Empty collection for now
            'totalUsers' => 0,
        ]);
    })->name('admin-settings');
});
