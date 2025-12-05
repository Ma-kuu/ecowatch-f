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

// Report submission routes
Route::post('/report-anon', function () {
    // Add report submission logic here when implementing backend
    return response()->json(['success' => true, 'message' => 'Report submitted successfully']);
})->name('report-anon.store');

Route::post('/report', function () {
    // Add authenticated report submission logic here when implementing backend
    return response()->json(['success' => true, 'message' => 'Report submitted successfully']);
})->name('report.store');

Route::get('/report/{id}', function ($id) {
    // Add logic to fetch report by ID when implementing backend
    return view('report-show', [
        'report' => (object)[
            'id' => $id,
            'title' => 'Sample Report',
            'status' => 'pending',
        ]
    ]);
})->name('report.show');

// Authentication Pages (static views only)
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');

// Dashboard Pages (static views only)
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

// Logout Route
Route::post('/logout', function () {
    // Add logout logic here when implementing authentication
    return redirect()->route('login');
})->name('logout');
