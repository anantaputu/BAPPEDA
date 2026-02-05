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
use App\Http\Controllers\Public\DatasetController;
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
Route::get('/dataset/{id}', [DatasetController::class, 'show'])->name('dataset.detail');

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
    
    Route::get('/dashboard', [DataInputController::class, 'dashboard'])->name('dashboard');
    Route::get('/data', [DataInputController::class, 'index'])->name('index'); 

    // --- WIZARD EXCEL-FIRST ---
    Route::get('/wizard', [DataInputController::class, 'createWizard'])->name('wizard');
    
    // API Step 1: Analisa Excel (Baca Header)
    Route::post('/wizard/analyze', [DataInputController::class, 'analyzeFile'])->name('wizard.analyze');
    
    // API Step 2: Simpan Semuanya (Metadata + Field + Data)
    Route::post('/wizard/store-all', [DataInputController::class, 'storeComplete'])->name('wizard.store-all');

    // Export (Tetap ada)
    Route::get('/export/{upload}', [DataOutputController::class, 'export'])->name('export');
});