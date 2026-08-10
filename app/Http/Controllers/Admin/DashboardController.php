<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Konsumen;
use App\Models\Prospek;
use App\Services\LaporanService;
use App\Services\ProspekService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly LaporanService $laporanService, private readonly ProspekService $prospekService) {}

    public function index(Request $request): View
    {
        $bulanIni = now()->startOfMonth();
        $akhirBulanIni = $bulanIni->copy()->endOfMonth();

        $dashboard = $this->laporanService->getDashboardAdmin();

        $penjualanPerBulan = $dashboard['penjualan_per_bulan'];
        $kategoriBreakdown = $dashboard['kategori_breakdown'];
        $totalUsers = $dashboard['total_users'];
        $totalUnitsAvailable = $dashboard['total_tersedia'];
        $totalUnits = $dashboard['total_unit'];
        $totalBooking = $dashboard['total_booking_bulan_ini'];
        $totalOmsetBulanIni = $dashboard['total_omset_bulan_ini'];

        $topMarketing = DB::table('users as m')
            ->join('booking as b', 'b.id_marketing', '=', 'm.id')
            ->join('status_penjualan as sp', 'sp.id_booking', '=', 'b.id')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->where('m.role', 'marketing')
            ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
            ->whereBetween('sp.tanggal_perubahan', [$bulanIni->toDateString(), $akhirBulanIni->toDateString()])
            ->select([
                'm.nama_lengkap',
                DB::raw('COUNT(sp.id) as total_closing'),
                DB::raw('SUM(u.harga_jual) as total_nilai'),
                DB::raw('SUM(u.harga_jual * (m.persentase_komisi / 100)) as total_komisi'),
            ])
            ->groupBy('m.id', 'm.nama_lengkap')
            ->orderByDesc('total_closing')
            ->limit(5)
            ->get();

        $conversionRatePerusahaan = $totalBooking > 0
            ? round(($dashboard['total_terjual'] / $totalBooking) * 100, 1)
            : 0.0;

        $averageClosingTime = DB::table('status_penjualan as sp')
            ->join('booking as b', 'b.id', '=', 'sp.id_booking')
            ->where('sp.status_saat_ini', 'akad')
            ->whereBetween('sp.tanggal_perubahan', [$bulanIni->toDateString(), $akhirBulanIni->toDateString()])
            ->whereNotNull('b.tanggal_booking')
            ->selectRaw('AVG(DATEDIFF(sp.tanggal_perubahan, b.tanggal_booking)) as avg_days')
            ->value('avg_days');

        $averageClosingTime = $averageClosingTime !== null ? round((float) $averageClosingTime, 1) : 0.0;

        $trenBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulanRef = now()->copy()->subMonths($i);
            $awal = $bulanRef->copy()->startOfMonth();
            $akhir = $bulanRef->copy()->endOfMonth();

            $totalProspek = DB::table('prospek')
                ->whereBetween('tanggal_prospek', [$awal->toDateString(), $akhir->toDateString()])
                ->count();

            $totalClosing = DB::table('status_penjualan as sp')
                ->join('booking as b', 'b.id', '=', 'sp.id_booking')
                ->where('sp.status_saat_ini', 'akad')
                ->whereBetween('sp.tanggal_perubahan', [$awal->toDateString(), $akhir->toDateString()])
                ->count();

            $trenBulanan[] = [
                'label' => $bulanRef->translatedFormat('M Y'),
                'prospek' => $totalProspek,
                'closing' => $totalClosing,
            ];
        }

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

        $prospekStats = $this->prospekService->getAllStats();
        $totalProspekBulanIni = DB::table('prospek')
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->count();
        $konversiBulanIni = DB::table('prospek')
            ->where('status_prospek', 'jadi_konsumen')
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->count();
        $conversionRate = $totalProspekBulanIni > 0
            ? round(($konversiBulanIni / $totalProspekBulanIni) * 100, 1)
            : 0;

        $prospekTerbaru = Prospek::query()
            ->orderByDesc('tanggal_prospek')
            ->limit(5)
            ->get();

        $konsumenTerbaru = Konsumen::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', array_merge(
            compact(
                'totalUsers',
                'totalUnitsAvailable',
                'totalUnits',
                'totalBooking',
                'totalOmsetBulanIni',
                'conversionRatePerusahaan',
                'averageClosingTime',
                'penjualanPerBulan',
                'kategoriBreakdown',
                'trenBulanan',
                'topMarketing',
                'latestBookings',
                'unitsAvailableByPerumahan',
                'prospekStats',
                'totalProspekBulanIni',
                'konversiBulanIni',
                'conversionRate',
                'prospekTerbaru',
                'konsumenTerbaru'
            ),
            ['activeTab' => 'dashboard']
        ));
    }
}
