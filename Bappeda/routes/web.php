<?php
use Inertia\Inertia;
use App\Http\Controllers\DataInputController;
use App\Http\Controllers\DataController;


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


    Route::get('/input-data', [DataInputController::class, 'index'])
        ->name('input-data.index');


    Route::get('/input-data/{data}', [DataInputController::class, 'create'])
        ->name('input-data.create');

    Route::post('/input-data/{data}', [DataInputController::class, 'store'])
        ->name('input-data.store');

    Route::get('/data/create', [DataController::class, 'create'])
        ->name('data.create');

    Route::post('/data', [DataController::class, 'store'])
        ->name('data.store');