<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CapaController;
use App\Http\Controllers\DefectTypeController;
use App\Http\Controllers\ManufacturingDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionBatchController;
use App\Http\Controllers\ProductionLineController;
use App\Http\Controllers\QualityInspectionController;
use App\Http\Controllers\SixSigmaAnalyticsController;
use Illuminate\Support\Facades\Route;

// Rute Autentikasi (Tamu / Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Rute Terproteksi (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil Pengguna
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard Eksekutif Six Sigma Manufaktur
    Route::get('/', [ManufacturingDashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [ManufacturingDashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [ManufacturingDashboardController::class, 'index'])->name('admin.dashboard');

    // Analitik Six Sigma (Pareto 80/20, Fishbone 5M+1E, SPC p-Chart)
    Route::get('/analytics', [SixSigmaAnalyticsController::class, 'index'])->name('admin.analytics.index');

    // Pemeriksaan Kualitas (Quality Inspections) & Kalkulator Real-time Six Sigma
    Route::post('/api/inspections/calculate-preview', [QualityInspectionController::class, 'calculatePreview'])->name('admin.inspections.calculate-preview');
    Route::resource('inspections', QualityInspectionController::class)->only(['index', 'create', 'store', 'show'])->names('admin.inspections');

    // Manajemen Batch / Lot Produksi
    Route::resource('batches', ProductionBatchController::class)->names('admin.batches');

    // Master Data: Produk & Standar CTQ
    Route::resource('products', ProductController::class)->except(['show'])->names('admin.products');

    // Master Data: Lini Produksi & Mesin
    Route::resource('lines', ProductionLineController::class)->except(['show'])->names('admin.lines');

    // Master Data: Taksonomi Cacat (Defect Types & Categories)
    Route::resource('defects', DefectTypeController::class)->except(['show'])->names('admin.defects');

    // Tindakan Perbaikan Cacat (CAPA - Corrective & Preventive Action)
    Route::resource('capa', CapaController::class)->names('admin.capa');
});
