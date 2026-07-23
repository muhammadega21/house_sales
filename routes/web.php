<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Marketing as Marketing;
use App\Http\Controllers\Manajemen as Manajemen;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users Management
    Route::resource('users', Admin\UserController::class)->except(['show']);

    // Perumahan Management
    Route::resource('perumahan', Admin\PerumahanController::class);

    // Unit Rumah Management
    Route::resource('unit-rumah', Admin\UnitRumahController::class);

    // Konsumen Management
    Route::resource('konsumen', Admin\KonsumenController::class);

    // Booking Management
    Route::resource('booking', Admin\BookingController::class)->except(['destroy']);

    // Pembayaran Management & Verification
    Route::get('/pembayaran', [Admin\PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{id}/verifikasi', [Admin\PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::put('/pembayaran/{id}/verifikasi', [Admin\PembayaranController::class, 'prosesVerifikasi'])->name('pembayaran.proses-verifikasi');

    // Dokumen KPR Management & Verification
    Route::get('/dokumen', [Admin\DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{id}/verifikasi', [Admin\DokumenController::class, 'verifikasi'])->name('dokumen.verifikasi');
    Route::put('/dokumen/{id}/verifikasi', [Admin\DokumenController::class, 'prosesVerifikasi'])->name('dokumen.proses-verifikasi');

    // Pengajuan KPR Management
    Route::get('/pengajuan-kpr', [Admin\PengajuanKprController::class, 'index'])->name('pengajuan-kpr.index');
    Route::get('/pengajuan-kpr/{id}', [Admin\PengajuanKprController::class, 'show'])->name('pengajuan-kpr.show');
    Route::put('/pengajuan-kpr/{id}/status', [Admin\PengajuanKprController::class, 'updateStatus'])->name('pengajuan-kpr.update-status');

    // Status Penjualan
    Route::get('/status-penjualan', [Admin\StatusPenjualanController::class, 'index'])->name('status-penjualan.index');
    Route::put('/status-penjualan/{id}', [Admin\StatusPenjualanController::class, 'update'])->name('status-penjualan.update');

    // Marketing Management & Performance
    Route::resource('marketing', Admin\MarketingController::class);
    Route::get('/marketing/{marketing}/target', [Admin\MarketingController::class, 'setTarget'])->name('marketing.set-target');
    Route::post('/marketing/{marketing}/target', [Admin\MarketingController::class, 'storeTarget'])->name('marketing.store-target');

    // Simulasi Pembayaran
    Route::get('/simulasi', [Admin\SimulasiController::class, 'index'])->name('simulasi.index');
    Route::post('/simulasi/hitung', [Admin\SimulasiController::class, 'hitung'])->name('simulasi.hitung');

    // Laporan
    Route::get('/laporan/penjualan', [Admin\LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/marketing', [Admin\LaporanController::class, 'marketing'])->name('laporan.marketing');
    Route::get('/laporan/unit', [Admin\LaporanController::class, 'unit'])->name('laporan.unit');
    Route::get('/laporan/export-pdf', [Admin\LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/export-excel', [Admin\LaporanController::class, 'exportExcel'])->name('laporan.export-excel');

    // Pengaturan Sistem
    Route::get('/pengaturan', [Admin\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan', [Admin\PengaturanController::class, 'update'])->name('pengaturan.update');
});

/*
|--------------------------------------------------------------------------
| Marketing Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:marketing'])->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/dashboard', [Marketing\DashboardController::class, 'index'])->name('dashboard');

    // Prospek
    Route::get('/prospek', [Marketing\ProspekController::class, 'index'])->name('prospek.index');
    Route::post('/prospek', [Marketing\ProspekController::class, 'store'])->name('prospek.store');
    Route::put('/prospek/{id}', [Marketing\ProspekController::class, 'update'])->name('prospek.update');

    // Konsumen
    Route::get('/konsumen', [Marketing\KonsumenController::class, 'index'])->name('konsumen.index');
    Route::post('/konsumen', [Marketing\KonsumenController::class, 'store'])->name('konsumen.store');
    Route::put('/konsumen/{id}', [Marketing\KonsumenController::class, 'update'])->name('konsumen.update');

    // Booking
    Route::get('/booking', [Marketing\BookingController::class, 'index'])->name('booking.index');
    Route::post('/booking', [Marketing\BookingController::class, 'store'])->name('booking.store');

    // Dokumen
    Route::get('/dokumen/{id_konsumen}', [Marketing\DokumenController::class, 'index'])->name('dokumen.index');
    Route::post('/dokumen/upload', [Marketing\DokumenController::class, 'upload'])->name('dokumen.upload');

    // Pembayaran
    Route::get('/pembayaran', [Marketing\PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran', [Marketing\PembayaranController::class, 'store'])->name('pembayaran.store');

    // Pengajuan KPR
    Route::get('/pengajuan-kpr', [Marketing\PengajuanKprController::class, 'index'])->name('pengajuan-kpr.index');
    Route::post('/pengajuan-kpr', [Marketing\PengajuanKprController::class, 'store'])->name('pengajuan-kpr.store');

    // Simulasi
    Route::get('/simulasi', [Marketing\SimulasiController::class, 'index'])->name('simulasi.index');
    Route::post('/simulasi/hitung', [Marketing\SimulasiController::class, 'hitung'])->name('simulasi.hitung');

    // Kinerja
    Route::get('/kinerja', [Marketing\KinerjaController::class, 'index'])->name('kinerja.index');

    // Status Penjualan
    Route::put('/status-penjualan/{id}', [Marketing\StatusPenjualanController::class, 'update'])->name('status-penjualan.update');
});

/*
|--------------------------------------------------------------------------
| Manajemen Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manajemen'])->prefix('manajemen')->name('manajemen.')->group(function () {
    Route::get('/dashboard', [Manajemen\DashboardController::class, 'index'])->name('dashboard');

    // Laporan
    Route::get('/laporan/penjualan', [Manajemen\LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/marketing', [Manajemen\LaporanController::class, 'marketing'])->name('laporan.marketing');
    Route::get('/laporan/export-pdf', [Manajemen\LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/export-excel', [Manajemen\LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
});



