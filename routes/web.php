<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Manajemen;
use App\Http\Controllers\Marketing;
use Illuminate\Support\Facades\Route;

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
    Route::get('/pembayaran/{id}/tolak', [Admin\PembayaranController::class, 'tolak'])->name('pembayaran.tolak');
    Route::put('/pembayaran/{id}/tolak', [Admin\PembayaranController::class, 'prosesTolak'])->name('pembayaran.proses-tolak');
    Route::get('/pembayaran/{id}', [Admin\PembayaranController::class, 'show'])->name('pembayaran.show');

    // Dokumen KPR Management & Verification
    Route::get('/dokumen', [Admin\DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{id}', [Admin\DokumenController::class, 'show'])->name('dokumen.show');
    Route::get('/dokumen/{id}/verifikasi', [Admin\DokumenController::class, 'verifikasi'])->name('dokumen.verifikasi');
    Route::put('/dokumen/{id}/verifikasi', [Admin\DokumenController::class, 'prosesVerifikasi'])->name('dokumen.proses-verifikasi');

    // Pengajuan KPR Management
    Route::get('/pengajuan-kpr', [Admin\PengajuanKprController::class, 'index'])->name('pengajuan-kpr.index');
    Route::get('/pengajuan-kpr/stats', [Admin\PengajuanKprController::class, 'stats'])->name('pengajuan-kpr.stats');
    Route::get('/pengajuan-kpr/{id}', [Admin\PengajuanKprController::class, 'show'])->name('pengajuan-kpr.show');
    Route::get('/pengajuan-kpr/{id}/update-status', [Admin\PengajuanKprController::class, 'updateStatus'])->name('pengajuan-kpr.update-status');
    Route::put('/pengajuan-kpr/{id}/status', [Admin\PengajuanKprController::class, 'prosesUpdateStatus'])->name('pengajuan-kpr.proses-update-status');

    // Status Penjualan
    Route::get('/status-penjualan', [Admin\StatusPenjualanController::class, 'index'])->name('status-penjualan.index');
    Route::get('/status-penjualan/{id}', [Admin\StatusPenjualanController::class, 'show'])->name('status-penjualan.show');
    Route::put('/status-penjualan/{id}', [Admin\StatusPenjualanController::class, 'update'])->name('status-penjualan.update');

    Route::get('/activity-log', [Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

    // Marketing Management & Performance
    Route::resource('marketing', Admin\MarketingController::class);
    Route::get('/marketing/{marketing}/target', [Admin\MarketingController::class, 'setTarget'])->name('marketing.set-target');
    Route::post('/marketing/{marketing}/target', [Admin\MarketingController::class, 'storeTarget'])->name('marketing.store-target');

    // Prospek Management
    Route::get('/prospek', [Admin\ProspekController::class, 'index'])->name('prospek.index');
    Route::get('/prospek/create', [Admin\ProspekController::class, 'create'])->name('prospek.create');
    Route::post('/prospek', [Admin\ProspekController::class, 'store'])->name('prospek.store');
    Route::get('/prospek/{id}/edit', [Admin\ProspekController::class, 'edit'])->name('prospek.edit');
    Route::get('/prospek/{id}/convert', [Admin\ProspekController::class, 'convert'])->name('prospek.convert');
    Route::post('/prospek/{id}/convert', [Admin\ProspekController::class, 'storeConvert'])->name('prospek.store-convert');
    Route::put('/prospek/{id}', [Admin\ProspekController::class, 'update'])->name('prospek.update');
    Route::delete('/prospek/{id}', [Admin\ProspekController::class, 'destroy'])->name('prospek.destroy');
    Route::get('/prospek/{id}', [Admin\ProspekController::class, 'show'])->name('prospek.show');
    Route::get('/prospek/stats', [Admin\ProspekController::class, 'stats'])->name('prospek.stats');

    // Booking Management
    Route::get('/booking', [Admin\BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/create', [Admin\BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [Admin\BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{id}', [Admin\BookingController::class, 'show'])->name('booking.show');
    Route::get('/booking/{id}/tracking', [Admin\BookingController::class, 'tracking'])->name('booking.tracking');
    Route::get('/booking/{id}/edit', [Admin\BookingController::class, 'edit'])->name('booking.edit');
    Route::put('/booking/{id}', [Admin\BookingController::class, 'update'])->name('booking.update');
    Route::get('/booking/{id}/cancel', [Admin\BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/booking/{id}/cancel', [Admin\BookingController::class, 'processCancel'])->name('booking.process-cancel');
    Route::delete('/prospek/{id}', [Admin\ProspekController::class, 'destroy'])->name('prospek.destroy');
    Route::get('/prospek/{id}', [Admin\ProspekController::class, 'show'])->name('prospek.show');
    Route::get('/prospek/stats', [Admin\ProspekController::class, 'stats'])->name('prospek.stats');

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
    Route::get('/prospek/create', [Marketing\ProspekController::class, 'create'])->name('prospek.create');
    Route::post('/prospek', [Marketing\ProspekController::class, 'store'])->name('prospek.store');
    Route::get('/prospek/{id}', [Marketing\ProspekController::class, 'show'])->name('prospek.show');
    Route::get('/prospek/{id}/edit', [Marketing\ProspekController::class, 'edit'])->name('prospek.edit');
    Route::put('/prospek/{id}', [Marketing\ProspekController::class, 'update'])->name('prospek.update');
    Route::delete('/prospek/{id}', [Marketing\ProspekController::class, 'destroy'])->name('prospek.destroy');
    Route::get('/prospek/{id}/convert', [Marketing\ProspekController::class, 'convert'])->name('prospek.convert');
    Route::post('/prospek/{id}/convert', [Marketing\ProspekController::class, 'storeConvert'])->name('prospek.store-convert');

    // Konsumen
    Route::get('/konsumen', [Marketing\KonsumenController::class, 'index'])->name('konsumen.index');
    Route::get('/konsumen/create', [Marketing\KonsumenController::class, 'create'])->name('konsumen.create');
    Route::post('/konsumen', [Marketing\KonsumenController::class, 'store'])->name('konsumen.store');
    Route::get('/konsumen/{id}', [Marketing\KonsumenController::class, 'show'])->name('konsumen.show');
    Route::get('/konsumen/{id}/edit', [Marketing\KonsumenController::class, 'edit'])->name('konsumen.edit');
    Route::put('/konsumen/{id}', [Marketing\KonsumenController::class, 'update'])->name('konsumen.update');
    Route::delete('/konsumen/{id}', [Marketing\KonsumenController::class, 'destroy'])->name('konsumen.destroy');

    // Booking
    Route::get('/booking', [Marketing\BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/create', [Marketing\BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [Marketing\BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{id}', [Marketing\BookingController::class, 'show'])->name('booking.show');
    Route::get('/booking/{id}/tracking', [Marketing\BookingController::class, 'tracking'])->name('booking.tracking');
    Route::get('/booking/{id}/edit', [Marketing\BookingController::class, 'edit'])->name('booking.edit');
    Route::put('/booking/{id}', [Marketing\BookingController::class, 'update'])->name('booking.update');
    Route::get('/booking/{id}/cancel', [Marketing\BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/booking/{id}/cancel', [Marketing\BookingController::class, 'processCancel'])->name('booking.process-cancel');
    Route::get('/booking/cek-unit/{idUnit}', [Marketing\BookingController::class, 'cekUnit'])->name('booking.cek-unit');

    // Dokumen
    Route::get('/dokumen/{id_konsumen}', [Marketing\DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{id_konsumen}/create', [Marketing\DokumenController::class, 'create'])->name('dokumen.create');
    Route::post('/dokumen/upload', [Marketing\DokumenController::class, 'store'])->name('dokumen.store');
    Route::delete('/dokumen/{id}', [Marketing\DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('/dokumen/{id}/replace', [Marketing\DokumenController::class, 'replace'])->name('dokumen.replace');
    Route::put('/dokumen/{id}/replace', [Marketing\DokumenController::class, 'update'])->name('dokumen.update');
    Route::get('/dokumen/{id}/download', [Marketing\DokumenController::class, 'download'])->name('dokumen.download');
    Route::get('/dokumen/{id}/preview', [Marketing\DokumenController::class, 'preview'])->name('dokumen.preview');

    // Pembayaran
    Route::get('/pembayaran', [Marketing\PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create', [Marketing\PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::get('/pembayaran/info-booking/{idBooking}', [Marketing\PembayaranController::class, 'infoBooking'])->name('pembayaran.info-booking');
    Route::post('/pembayaran', [Marketing\PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/{id}', [Marketing\PembayaranController::class, 'show'])->name('pembayaran.show');

    // Pengajuan KPR
    Route::get('/pengajuan-kpr', [Marketing\PengajuanKprController::class, 'index'])->name('pengajuan-kpr.index');
    Route::get('/pengajuan-kpr/create', [Marketing\PengajuanKprController::class, 'create'])->name('pengajuan-kpr.create');
    Route::get('/pengajuan-kpr/info-booking/{idBooking}', [Marketing\PengajuanKprController::class, 'infoBooking'])->name('pengajuan-kpr.info-booking');
    Route::get('/pengajuan-kpr/{id}/edit', [Marketing\PengajuanKprController::class, 'edit'])->name('pengajuan-kpr.edit');
    Route::get('/pengajuan-kpr/{id}', [Marketing\PengajuanKprController::class, 'show'])->name('pengajuan-kpr.show');
    Route::put('/pengajuan-kpr/{id}', [Marketing\PengajuanKprController::class, 'update'])->name('pengajuan-kpr.update');
    Route::post('/pengajuan-kpr', [Marketing\PengajuanKprController::class, 'store'])->name('pengajuan-kpr.store');

    // Simulasi
    Route::get('/simulasi', [Marketing\SimulasiController::class, 'index'])->name('simulasi.index');
    Route::post('/simulasi/hitung', [Marketing\SimulasiController::class, 'hitung'])->name('simulasi.hitung');
    Route::post('/simulasi/simpan', [Marketing\SimulasiController::class, 'simpan'])->name('simulasi.simpan');
    Route::get('/simulasi/perbandingan', [Marketing\SimulasiController::class, 'perbandingan'])->name('simulasi.perbandingan');
    Route::get('/simulasi/export-pdf', [Marketing\SimulasiController::class, 'exportPdf'])->name('simulasi.export-pdf');

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
