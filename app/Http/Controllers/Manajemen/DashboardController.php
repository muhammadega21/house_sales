<?php

declare(strict_types=1);

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Services\DokumenService;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly LaporanService $laporanService, private readonly DokumenService $dokumenService) {}

    public function index(Request $request): View
    {
        $dashboard = $this->laporanService->getDashboardManajemen();

        $totalPerumahan = $dashboard['total_perumahan'];
        $totalUnit = $dashboard['total_unit'];
        $totalTerjual = $dashboard['total_terjual'];
        $totalTersedia = $dashboard['total_tersedia'];
        $totalOmsetBulanIni = $dashboard['total_omset_bulan_ini'];
        $totalBookingBulanIni = $dashboard['total_booking_bulan_ini'];
        $top5Marketing = $dashboard['top_5_marketing'];
        $penjualanPerBulan = $dashboard['penjualan_per_bulan'];
        $kategoriBreakdown = $dashboard['kategori_breakdown'];
        $rataRataWaktuClosing = $dashboard['rata_rata_waktu_closing'];

        $totalBooking = DB::table('booking')->count();
        $conversionRatePerusahaan = $totalBooking > 0
            ? round(($totalTerjual / $totalBooking) * 100, 1)
            : 0.0;

        $latestBookings = DB::table('booking as b')
            ->leftJoin('konsumen as k', 'k.id', '=', 'b.id_konsumen')
            ->leftJoin('status_penjualan as sp', function ($join) {
                $join->on('sp.id_booking', '=', 'b.id')
                    ->whereRaw('sp.tanggal_perubahan = (SELECT MAX(tanggal_perubahan) FROM status_penjualan WHERE id_booking = b.id)');
            })
            ->select([
                'b.kode_booking',
                'b.tanggal_booking',
                'k.nama_lengkap as nama_konsumen',
                'b.created_at',
                'sp.status_saat_ini as status_penjualan',
            ])
            ->orderByDesc('b.created_at')
            ->limit(5)
            ->get();

        $unitsAvailableByPerumahan = DB::table('perumahan as p')
            ->leftJoin('unit_rumah as u', 'u.id_perumahan', '=', 'p.id')
            ->select([
                'p.nama_perumahan',
                DB::raw("SUM(CASE WHEN u.status_unit = 'tersedia' THEN 1 ELSE 0 END) as tersedia"),
                DB::raw('COUNT(u.id) as total_unit'),
            ])
            ->groupBy('p.id', 'p.nama_perumahan')
            ->orderBy('p.nama_perumahan')
            ->get();

        $dokumenPending = $this->dokumenService->getStatsForAdmin()['belum_diverifikasi'] ?? 0;

        return view('manajemen.dashboard', array_merge(
            compact(
                'totalPerumahan',
                'totalUnit',
                'totalTerjual',
                'totalTersedia',
                'totalOmsetBulanIni',
                'totalBookingBulanIni',
                'conversionRatePerusahaan',
                'rataRataWaktuClosing',
                'top5Marketing',
                'penjualanPerBulan',
                'kategoriBreakdown',
                'latestBookings',
                'unitsAvailableByPerumahan',
                'dokumenPending'
            ),
            ['activeTab' => 'dashboard']
        ));
    }
}
