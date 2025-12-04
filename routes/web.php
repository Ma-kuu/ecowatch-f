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
    return view('report-show');
})->name('report-authenticated');

Route::get('/report-anon', function () {
    return view('report-anon');
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
    return view('auth.admin.user-dashboard');
})->name('user-dashboard');

Route::get('/admin-dashboard', function () {
    return view('auth.admin.admin-dashboard');
})->name('admin-dashboard');

Route::get('/lgu-dashboard', function () {
    return view('auth.lgu.lgu-dashboard');
})->name('lgu-dashboard');

Route::get('/admin-settings', function () {
    return view('auth.admin.admin-settings');
})->name('admin-settings');
