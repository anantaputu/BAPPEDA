<?php
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
});
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/welcome', function () {
    return Inertia::render('Welcome');
});
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
});