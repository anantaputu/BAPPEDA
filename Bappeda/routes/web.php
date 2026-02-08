<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// --- IMPORT CONTROLLER ---
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\Public\DatasetController;
use App\Http\Controllers\Public\DashboardController; // Traffic Cop
use App\Http\Controllers\Public\OverviewController;  // Statistik Public

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DataController;
use App\Http\Controllers\Admin\TemaController;
use App\Http\Controllers\Admin\UrusanController;
use App\Http\Controllers\Admin\BidangController;
use App\Http\Controllers\Admin\FrekuensiController;
use App\Http\Controllers\Admin\KataKunciController;
use App\Http\Controllers\Admin\SatuanController;

use App\Http\Controllers\Inputer\InputerDashboardController;
use App\Http\Controllers\Inputer\DataInputController;
use App\Http\Controllers\Inputer\DataOutputController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (Bebas Akses)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index']);
Route::get('/public-dashboard', [DashboardController::class, 'index'])
    ->name('public.dashboard');
Route::get('/cari', [SearchController::class, 'index'])->name('public.search');
Route::get('/dataset/{id}', [DatasetController::class, 'show'])->name('dataset.detail');
Route::get('/export/data/{id}', [DataOutputController::class, 'export'])->name('public.export');

/*
|--------------------------------------------------------------------------
| 2. AUTH ROUTES (Login / Logout)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);    
});

Route::middleware('auth')->post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| 3. TRAFFIC COP (Pintu Masuk Utama)
|--------------------------------------------------------------------------
| Mengarahkan user ke dashboard yang sesuai (Admin / Inputer).
*/
Route::middleware(['auth', 'role:admin|inputer'])->group(function () {
    Route::get('/dashboard', [OverviewController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| 4. SHARED RESOURCES (Master Data)
|--------------------------------------------------------------------------
| Bisa diakses oleh ADMIN dan INPUTER.
| Prefix 'admin' digunakan agar URL tetap konsisten (/admin/data).
*/
Route::middleware(['auth', 'role:admin|inputer'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('data', DataController::class);
    Route::resource('tema', TemaController::class);
    Route::resource('urusan', UrusanController::class);
    Route::resource('bidang', BidangController::class);
    Route::resource('frekuensi', FrekuensiController::class);
    Route::resource('katakunci', KataKunciController::class);
    Route::resource('satuan', SatuanController::class);
});

/*
|--------------------------------------------------------------------------
| 5. INPUTER FEATURES (Wizard & Upload)
|--------------------------------------------------------------------------
| INI YANG HILANG TADI.
| Bisa diakses oleh ADMIN dan INPUTER.
*/
Route::middleware(['auth', 'role:admin|inputer'])->prefix('inputer')->name('inputer.')->group(function () {
    
    // Dashboard Inputer
    Route::get('/dashboard', [InputerDashboardController::class, 'index'])->name('dashboard');
    
    // Tabel Riwayat Upload (Data Saya)
    Route::get('/data', [DataInputController::class, 'index'])->name('index'); 
    
    // Wizard Input Data (Langkah 1 & 2)
    Route::get('/wizard', [DataInputController::class, 'createWizard'])->name('wizard');
    Route::post('/wizard/analyze', [DataInputController::class, 'analyzeFile'])->name('wizard.analyze');
    Route::post('/wizard/store-all', [DataInputController::class, 'storeComplete'])->name('wizard.store-all');
    
    // Export & Edit
    Route::get('/export/{id}', [DataOutputController::class, 'export'])->name('export');
});

/*
|--------------------------------------------------------------------------
| 6. KHUSUS ADMIN (Sensitive Data)
|--------------------------------------------------------------------------
| Halaman ini BENAR-BENAR HANYA untuk Admin.
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class); // Kelola User
});