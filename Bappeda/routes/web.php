<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
// Import Konten Baru
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DataController;
use App\Http\Controllers\Admin\TemaController;
use App\Http\Controllers\Admin\UrusanController;
use App\Http\Controllers\Admin\BidangController;
use App\Http\Controllers\Admin\FrekuensiController;
use App\Http\Controllers\Admin\KataKunciController;
use App\Http\Controllers\Admin\SatuanController;
use App\Http\Controllers\Inputer\DataInputController;
use App\Http\Controllers\Inputer\DataOutputController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\SearchController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Inputer\InputerDashboardController;
use App\Http\Controllers\Public\DashboardController;
/*
|--------------------------------------------------------------------------
| PUBLIC & AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/cari', [SearchController::class, 'index'])->name('public.search');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);    
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Namespace: App\Http\Controllers\Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('data', DataController::class); // Metadata indikator
    Route::resource('tema', TemaController::class);
    Route::resource('urusan', UrusanController::class);
    Route::resource('bidang', BidangController::class);
    Route::resource('frekuensi', FrekuensiController::class);
    Route::resource('katakunci', KataKunciController::class);
    Route::resource('satuan', SatuanController::class);
});

/*
|--------------------------------------------------------------------------
| INPUTER ROUTES (Namespace: App\Http\Controllers\Inputer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:inputer'])->prefix('inputer')->name('inputer.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DataInputController::class, 'dashboard'])->name('dashboard');

    // --- BAGIAN MASTER DATA ---
    Route::get('/data', [DataInputController::class, 'index'])->name('index'); 
    
    // PERBAIKAN: Ganti nama method jadi 'createMasterData'
    Route::get('/data/create', [DataInputController::class, 'createMasterData'])->name('data.create');
    
    Route::post('/data', [DataInputController::class, 'storeNewData'])->name('data.store'); 
    
    // --- BAGIAN UPLOAD EXCEL ---
    Route::prefix('upload')->group(function () {
        // Ini tetap pakai method 'create' karena butuh parameter {data}
        Route::get('/{data}', [DataInputController::class, 'create'])->name('create');
        Route::post('/{data}', [DataInputController::class, 'store'])->name('store');
        
        Route::get('/{upload}/mapping', [DataInputController::class, 'mapping'])->name('mapping');
        Route::post('/{upload}/mapping', [DataInputController::class, 'storeMapping'])->name('mapping.store');
        Route::post('/{upload}/parse', [DataInputController::class, 'parse'])->name('parse');
    });

    Route::get('/export/{upload}', [DataOutputController::class, 'export'])->name('export');
});