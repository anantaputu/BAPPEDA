<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataInputController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (TIDAK PERLU LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Landing');
})->name('landing');

Route::get('/welcome', function () {
    return Inertia::render('Welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (DARI BREEZE / AUTH BAWAAN)
|--------------------------------------------------------------------------
| JANGAN bikin route /login manual
| Breeze sudah sediain sendiri
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // ===== INPUT DATA =====
    Route::get('/input-data', [DataInputController::class, 'index'])
        ->name('input-data.index');

    Route::get('/input-data/{data}', [DataInputController::class, 'create'])
        ->name('input-data.create');

    Route::post('/input-data/{data}', [DataInputController::class, 'store'])
        ->name('input-data.store');

    // ===== MASTER DATA =====
    Route::get('/data/create', [DataController::class, 'create'])
        ->name('data.create');

    Route::post('/data', [DataController::class, 'store'])
        ->name('data.store');

Route::get(
    '/input-data/{upload}/mapping',
    [DataInputController::class, 'mapping']
)->name('input-data.mapping');

Route::post(
    '/input-data/{upload}/mapping',
    [DataInputController::class, 'storeMapping']
)->name('input-data.mapping.store');

Route::post(
    '/input-data/{upload}/parse',
    [DataInputController::class, 'parse']
)->name('input-data.parse');
});
