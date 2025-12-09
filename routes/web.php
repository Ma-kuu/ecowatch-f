<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnonymousReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PublicFeedController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/feed', [PublicFeedController::class, 'index'])->name('feed');
Route::post('/feed/reports/{report}/upvote', [PublicFeedController::class, 'toggleUpvote'])->name('feed.upvote');
Route::post('/feed/reports/{report}/flag', [PublicFeedController::class, 'flagReport'])->name('feed.flag')->middleware('auth');

// Report Pages
Route::get('/report-form', function () {
    return view('report-form');
})->name('report-form');

// Report Routes (using controllers)
Route::get('/report-anon', [AnonymousReportController::class, 'create'])->name('report-anon');
Route::post('/report-anon', [AnonymousReportController::class, 'store'])->name('report-anon.store');

Route::middleware('auth')->group(function () {
    Route::get('/report', [ReportController::class, 'create'])->name('report.create');
    Route::get('/report-authenticated', [ReportController::class, 'create'])->name('report-authenticated');
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
    Route::get('/user-dashboard', [DashboardController::class, 'userDashboard'])
        ->name('user-dashboard');

    Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboard'])
        ->name('admin-dashboard');

    Route::get('/lgu-dashboard', [DashboardController::class, 'lguDashboard'])
        ->name('lgu-dashboard');

    // LGU Report Mark Fixed Route
    Route::post('/lgu/reports/{id}/mark-fixed', [DashboardController::class, 'markReportFixed'])
        ->name('lgu.reports.mark-fixed');

    // Admin Settings Routes
    Route::get('/admin-settings', [AdminController::class, 'settings'])
        ->name('admin-settings');

    Route::post('/admin/users', [AdminController::class, 'createUser'])
        ->name('admin.users.create');

    Route::post('/admin/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])
        ->name('admin.users.toggle-status');

    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])
        ->name('admin.users.delete');

    // Admin Report Validation Route
    Route::post('/admin/reports/{id}/validate', [AdminController::class, 'validateReport'])
        ->name('admin.reports.validate');

    // Admin Report Update Route
    Route::put('/admin/reports/{id}', [AdminController::class, 'updateReport'])
        ->name('admin.reports.update');

    // Admin Report Delete Route
    Route::delete('/admin/reports/{id}', [AdminController::class, 'deleteReport'])
        ->name('admin.reports.delete');
});

// ============================================================================
// USER ROUTES (Authenticated Users)
// ============================================================================
Route::middleware(['auth'])->group(function () {
    // User Confirmation Routes
    Route::post('/user/reports/{id}/confirm', [UserController::class, 'confirmReportResolved'])
        ->name('user.reports.confirm');

    Route::post('/user/reports/{id}/reject', [UserController::class, 'rejectReportResolution'])
        ->name('user.reports.reject');
});
