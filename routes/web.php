<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CapaController;
use App\Http\Controllers\DefectTypeController;
use App\Http\Controllers\MachineRiskController;
use App\Http\Controllers\ManufacturingDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionBatchController;
use App\Http\Controllers\ProductionLineController;
use App\Http\Controllers\QualityInspectionController;
use App\Http\Controllers\SixSigmaAnalyticsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rute Autentikasi (Tamu / Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Rute Terproteksi (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil Pengguna (Bisa diakses oleh semua pengguna login)
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard Eksekutif Six Sigma Manufaktur
    Route::get('/', [ManufacturingDashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [ManufacturingDashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [ManufacturingDashboardController::class, 'index'])->name('admin.dashboard');

    // Analitik Six Sigma (Pareto 80/20, Fishbone 5M+1E, SPC p-Chart, Kesimpulan & Cetak Laporan)
    Route::get('/analytics', [SixSigmaAnalyticsController::class, 'index'])->name('admin.analytics.index');

    // =========================================================================
    // 1. OTORISASI KHUSUS QUALITY CONTROL (Admin & Staff QC)
    // =========================================================================
    Route::middleware('role:admin,staff')->group(function () {
        Route::post('/api/inspections/calculate-preview', [QualityInspectionController::class, 'calculatePreview'])->name('admin.inspections.calculate-preview');
        Route::get('/inspections/create', [QualityInspectionController::class, 'create'])->name('admin.inspections.create');
        Route::post('/inspections', [QualityInspectionController::class, 'store'])->name('admin.inspections.store');
    });

    // =========================================================================
    // 2. OTORISASI KHUSUS OPERASIONAL PRODUKSI & K3 (Admin & Supervisor)
    // =========================================================================
    Route::middleware('role:admin,employee')->group(function () {
        Route::resource('batches', ProductionBatchController::class)->only(['create', 'store', 'edit', 'update'])->names('admin.batches');
        Route::resource('risks', MachineRiskController::class)->only(['create', 'store', 'edit', 'update'])->names('admin.risks');
    });

    // Read-Only Katalog & Daftar Operasional untuk Seluruh Karyawan Terautentikasi
    Route::get('/inspections', [QualityInspectionController::class, 'index'])->name('admin.inspections.index');
    Route::get('/inspections/{inspection}', [QualityInspectionController::class, 'show'])->name('admin.inspections.show');

    Route::get('/batches', [ProductionBatchController::class, 'index'])->name('admin.batches.index');
    Route::get('/batches/{batch}', [ProductionBatchController::class, 'show'])->name('admin.batches.show');

    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/lines', [ProductionLineController::class, 'index'])->name('admin.lines.index');
    Route::get('/defects', [DefectTypeController::class, 'index'])->name('admin.defects.index');
    Route::get('/risks', [MachineRiskController::class, 'index'])->name('admin.risks.index');

    // Tindakan Perbaikan Cacat (CAPA) - Kolaboratif Tim Mutu & Produksi
    Route::resource('capa', CapaController::class)->except(['destroy'])->names('admin.capa');

    // =========================================================================
    // 3. OTORISASI EKSKLUSIF SUPER ADMIN (Plant Manager / Administrator)
    // =========================================================================
    Route::middleware('role:admin')->group(function () {
        // Fitur Baru: Manajemen Pengguna (User Management)
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::resource('users', UserController::class)->names('admin.users');

        // Mutasi Master Data (Hanya Admin yang berwenang menambah/mengubah spesifikasi)
        Route::resource('products', ProductController::class)->except(['index', 'show'])->names('admin.products');
        Route::resource('lines', ProductionLineController::class)->except(['index', 'show'])->names('admin.lines');
        Route::resource('defects', DefectTypeController::class)->except(['index', 'show'])->names('admin.defects');

        // Aksi Destruktif (Hanya Admin yang berwenang menghapus data permanen)
        Route::delete('batches/{batch}', [ProductionBatchController::class, 'destroy'])->name('admin.batches.destroy');
        Route::delete('capa/{capa}', [CapaController::class, 'destroy'])->name('admin.capa.destroy');
        Route::delete('risks/{risk}', [MachineRiskController::class, 'destroy'])->name('admin.risks.destroy');
    });
});
