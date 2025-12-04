<?php

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

Route::get('/report-authenticated', function () {
    $violationTypes = collect([
        (object)['slug' => 'illegal-dumping', 'name' => 'Illegal Dumping'],
        (object)['slug' => 'water-pollution', 'name' => 'Water Pollution'],
        (object)['slug' => 'air-pollution', 'name' => 'Air Pollution'],
        (object)['slug' => 'deforestation', 'name' => 'Deforestation'],
        (object)['slug' => 'noise-pollution', 'name' => 'Noise Pollution'],
        (object)['slug' => 'soil-contamination', 'name' => 'Soil Contamination'],
        (object)['slug' => 'wildlife-violations', 'name' => 'Wildlife Violations'],
        (object)['slug' => 'industrial-violations', 'name' => 'Industrial Violations'],
    ]);
    return view('report-show', ['violationTypes' => $violationTypes]);
})->name('report-authenticated');

Route::get('/report-anon', function () {
    $violationTypes = collect([
        (object)['slug' => 'illegal-dumping', 'name' => 'Illegal Dumping'],
        (object)['slug' => 'water-pollution', 'name' => 'Water Pollution'],
        (object)['slug' => 'air-pollution', 'name' => 'Air Pollution'],
        (object)['slug' => 'deforestation', 'name' => 'Deforestation'],
        (object)['slug' => 'noise-pollution', 'name' => 'Noise Pollution'],
        (object)['slug' => 'soil-contamination', 'name' => 'Soil Contamination'],
        (object)['slug' => 'wildlife-violations', 'name' => 'Wildlife Violations'],
        (object)['slug' => 'industrial-violations', 'name' => 'Industrial Violations'],
    ]);
    return view('report-anon', ['violationTypes' => $violationTypes]);
})->name('report-anon');

// Authentication Pages (static views only)
Route::get('/login', function () {
    return view('auth.admin.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.admin.register');
})->name('register');

// Dashboard Pages (static views only)
Route::get('/user-dashboard', function () {
    return view('auth.admin.user-dashboard', [
        'userReports' => collect([]), // Empty collection for now
        'totalUserReports' => 0,
        'pendingCount' => 0,
    ]);
})->name('user-dashboard');

Route::get('/admin-dashboard', function () {
    return view('auth.admin.admin-dashboard', [
        'reports' => collect([]), // Empty collection for now
        'totalReports' => 0,
        'pendingReports' => 0,
        'inReviewReports' => 0,
        'resolvedReports' => 0,
    ]);
})->name('admin-dashboard');

Route::get('/lgu-dashboard', function () {
    return view('auth.lgu.lgu-dashboard', [
        'lguReports' => collect([]), // Empty collection for now
        'totalAssigned' => 0,
        'pendingAssigned' => 0,
        'inProgressAssigned' => 0,
        'fixedAssigned' => 0,
        'verifiedAssigned' => 0,
    ]);
})->name('lgu-dashboard');

Route::get('/admin-settings', function () {
    return view('auth.admin.admin-settings', [
        'users' => collect([]), // Empty collection for now
        'totalUsers' => 0,
    ]);
})->name('admin-settings');

// Logout Route
Route::post('/logout', function () {
    // Add logout logic here when implementing authentication
    return redirect()->route('login');
})->name('logout');
