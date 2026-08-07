<?php

declare(strict_types=1);

namespace App\Services;

use App\Exports\LaporanPenjualanExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanService
{
    // -------------------------------------------------------------------------
    // 1. LAPORAN PENJUALAN
    // -------------------------------------------------------------------------

    /**
     * Ambil data laporan penjualan dengan berbagai filter.
     *
     * Filter yang didukung:
     *   - periode_mulai   : string (Y-m-d), default awal bulan ini
     *   - periode_selesai : string (Y-m-d), default akhir bulan ini
     *   - id_perumahan    : int
     *   - kategori        : string ('subsidi'|'non_subsidi')
     *   - id_marketing    : int
     *   - status          : string ('akad'|'serah_terima')
     *
     * @param  array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getLaporanPenjualan(array $filters): array
    {
        $start = isset($filters['periode_mulai'])
            ? Carbon::parse($filters['periode_mulai'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = isset($filters['periode_selesai'])
            ? Carbon::parse($filters['periode_selesai'])->endOfDay()
            : Carbon::now()->endOfMonth();

        // Hanya hitung yang status = 'akad' atau 'serah_terima'
        $allowedStatus = ['akad', 'serah_terima'];
        if (!empty($filters['status']) && in_array($filters['status'], $allowedStatus, true)) {
            $allowedStatus = [$filters['status']];
        }

        $query = DB::table('status_penjualan as sp')
            ->join('booking as b', 'b.id', '=', 'sp.id_booking')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->join('konsumen as k', 'k.id', '=', 'sp.id_konsumen')
            ->join('perumahan as p', 'p.id', '=', 'u.id_perumahan')
            ->join('users as m', 'm.id', '=', 'b.id_marketing')
            ->whereIn('sp.status_saat_ini', $allowedStatus)
            ->whereBetween('sp.tanggal_perubahan', [$start, $end])
            ->select([
                'sp.id as id_status',
                'sp.status_saat_ini',
                'sp.tanggal_perubahan',
                'b.id as id_booking',
                'b.kode_booking',
                'b.tanggal_booking',
                'u.id as id_unit',
                'u.kode_unit',
                'u.tipe_rumah',
                'u.kategori',
                'u.harga_jual',
                'k.id as id_konsumen',
                'k.nama_lengkap as nama_konsumen',
                'k.nik',
                'p.id as id_perumahan',
                'p.nama_perumahan',
                'm.id as id_marketing',
                'm.nama_lengkap as nama_marketing',
            ]);

        // Filter perumahan
        if (!empty($filters['id_perumahan'])) {
            $query->where('u.id_perumahan', (int) $filters['id_perumahan']);
        }

        // Filter kategori
        if (!empty($filters['kategori'])) {
            $query->where('u.kategori', $filters['kategori']);
        }

        // Filter marketing
        if (!empty($filters['id_marketing'])) {
            $query->where('b.id_marketing', (int) $filters['id_marketing']);
        }

        $rows = $query->orderBy('sp.tanggal_perubahan', 'desc')->get();

        // --- Agregat utama ---
        $totalUnitTerjual    = $rows->count();
        $totalNilaiPenjualan = (float) $rows->sum('harga_jual');
        $totalBooking        = DB::table('booking as b')
            ->join('unit_rumah as u', 'u.id', '=', 'b.id_unit')
            ->whereBetween('b.tanggal_booking', [$start->toDateString(), $end->toDateString()])
            ->when(!empty($filters['id_perumahan']), fn($q) => $q->where('u.id_perumahan', (int) $filters['id_perumahan']))
            ->when(!empty($filters['kategori']), fn($q) => $q->where('u.kategori', $filters['kategori']))
            ->when(!empty($filters['id_marketing']), fn($q) => $q->where('b.id_marketing', (int) $filters['id_marketing']))
            ->count();
        $rataRataHarga = $totalUnitTerjual > 0
            ? round($totalNilaiPenjualan / $totalUnitTerjual, 2)
            : 0;

        // --- Breakdown per kategori ---
        $breakdownKategori = [
            'subsidi' => [
                'total_unit'  => $rows->where('kategori', 'subsidi')->count(),
                'total_nilai' => (float) $rows->where('kategori', 'subsidi')->sum('harga_jual'),
            ],
            'non_subsidi' => [
                'total_unit'  => $rows->where('kategori', 'non_subsidi')->count(),
                'total_nilai' => (float) $rows->where('kategori', 'non_subsidi')->sum('harga_jual'),
            ],
        ];

        // --- Breakdown per bulan ---
        $breakdownBulan = $rows
            ->groupBy(fn($row) => Carbon::parse($row->tanggal_perubahan)->format('Y-m'))
            ->map(fn($group, $bulan) => [
                'bulan'       => $bulan,
                'label'       => Carbon::parse($bulan . '-01')->translatedFormat('F Y'),
                'total_unit'  => $group->count(),
                'total_nilai' => (float) $group->sum('harga_jual'),
            ])
            ->sortKeys()
            ->values()
            ->toArray();

        return [
            'total_unit_terjual'    => $totalUnitTerjual,
            'total_nilai_penjualan' => $totalNilaiPenjualan,
            'total_booking'         => $totalBooking,
            'rata_rata_harga'       => $rataRataHarga,
            'breakdown_kategori'    => $breakdownKategori,
            'breakdown_bulan'       => $breakdownBulan,
            'data'                  => $rows->toArray(),
            'filters'               => [
                'periode_mulai'   => $start->toDateString(),
                'periode_selesai' => $end->toDateString(),
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // 2. LAPORAN PER MARKETING
    // -------------------------------------------------------------------------

    /**
     * Laporan kinerja masing-masing marketing.
     *
     * @param  array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getLaporanPerMarketing(array $filters): array
    {
        $start = isset($filters['periode_mulai'])
            ? Carbon::parse($filters['periode_mulai'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = isset($filters['periode_selesai'])
            ? Carbon::parse($filters['periode_selesai'])->endOfDay()
            : Carbon::now()->endOfMonth();

        $bulan = $start->month;
        $tahun = $start->year;

        $marketingList = DB::table('users')
            ->where('role', 'marketing')
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'persentase_komisi']);

        $result = [];

        foreach ($marketingList as $marketing) {
            $idMarketing = (int) $marketing->id;

            // Total prospek
            $totalProspek = DB::table('prospek')
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_prospek', [$start->toDateString(), $end->toDateString()])
                ->count();

            // Total booking
            $totalBooking = DB::table('booking')
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_booking', [$start->toDateString(), $end->toDateString()])
                ->count();

            // Total closing (status akad)
            $closingRows = DB::table('status_penjualan as sp')
                ->join('booking as b', 'b.id', '=', 'sp.id_booking')
                ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                ->where('b.id_marketing', $idMarketing)
                ->where('sp.status_saat_ini', 'akad')
                ->whereBetween('sp.tanggal_perubahan', [$start, $end])
                ->select('u.harga_jual')
                ->get();

            $totalClosing        = $closingRows->count();
            $totalNilaiPenjualan = (float) $closingRows->sum('harga_jual');
            $totalKomisi         = $totalNilaiPenjualan * ((float) ($marketing->persentase_komisi ?? 0) / 100);
            $conversionRate      = $totalProspek > 0
                ? round($totalClosing / $totalProspek * 100, 2)
                : 0.0;

            // Target bulan ini
            $target = DB::table('marketing_target')
                ->where('id_marketing', $idMarketing)
                ->where('periode_bulan', $bulan)
                ->where('periode_tahun', $tahun)
                ->first();

            $targetUnit        = (int) ($target->target_unit ?? 0);
            $pencapaianTarget  = $targetUnit > 0
                ? round($totalClosing / $targetUnit * 100, 2)
                : 0.0;

            $result[] = [
                'id_marketing'         => $idMarketing,
                'nama'                 => $marketing->nama_lengkap,
                'total_prospek'        => $totalProspek,
                'total_booking'        => $totalBooking,
                'total_closing'        => $totalClosing,
                'conversion_rate'      => $conversionRate,
                'total_nilai_penjualan'=> $totalNilaiPenjualan,
                'total_komisi'         => $totalKomisi,
                'target_unit'          => $targetUnit,
                'pencapaian_target'    => $pencapaianTarget,
            ];
        }

        // Sort by total_closing desc
        usort($result, fn($a, $b) => $b['total_closing'] <=> $a['total_closing']);

        return $result;
    }

    // -------------------------------------------------------------------------
    // 3. LAPORAN UNIT (per perumahan)
    // -------------------------------------------------------------------------

    /**
     * Laporan ketersediaan unit per perumahan.
     *
     * @param  array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function getLaporanUnit(array $filters): array
    {
        $query = DB::table('perumahan as p')
            ->leftJoin('unit_rumah as u', 'u.id_perumahan', '=', 'p.id')
            ->where('p.status', 'aktif')
            ->select([
                'p.id as id_perumahan',
                'p.nama_perumahan',
                'p.kota',
                DB::raw('COUNT(u.id) as total_unit'),
                DB::raw("SUM(CASE WHEN u.status_unit = 'tersedia' THEN 1 ELSE 0 END) as tersedia"),
                DB::raw("SUM(CASE WHEN u.status_unit = 'dibooking' THEN 1 ELSE 0 END) as dibooking"),
                DB::raw("SUM(CASE WHEN u.status_unit = 'dijual' THEN 1 ELSE 0 END) as dijual"),
                DB::raw("SUM(CASE WHEN u.status_unit = 'dibatalkan' THEN 1 ELSE 0 END) as dibatalkan"),
                DB::raw("SUM(CASE WHEN u.kategori = 'subsidi' AND u.status_unit = 'dijual' THEN 1 ELSE 0 END) as terjual_subsidi"),
                DB::raw("SUM(CASE WHEN u.kategori = 'non_subsidi' AND u.status_unit = 'dijual' THEN 1 ELSE 0 END) as terjual_non_subsidi"),
            ])
            ->groupBy('p.id', 'p.nama_perumahan', 'p.kota');

        if (!empty($filters['id_perumahan'])) {
            $query->where('p.id', (int) $filters['id_perumahan']);
        }

        return $query->orderBy('p.nama_perumahan')->get()->map(fn($row) => (array) $row)->toArray();
    }

    // -------------------------------------------------------------------------
    // 4. DASHBOARD ADMIN
    // -------------------------------------------------------------------------

    /**
     * Data untuk Dashboard Admin.
     *
     * @return array<string, mixed>
     */
    public function getDashboardAdmin(): array
    {
        $now       = Carbon::now();
        $startBulan = $now->copy()->startOfMonth();
        $endBulan   = $now->copy()->endOfMonth();

        // ---- Statistik Umum ----
        $totalUsers     = DB::table('users')->where('status', 'aktif')->count();
        $totalPerumahan = DB::table('perumahan')->where('status', 'aktif')->count();
        $totalUnit      = DB::table('unit_rumah')->count();
        $totalTersedia  = DB::table('unit_rumah')->where('status_unit', 'tersedia')->count();
        $totalTerjual   = DB::table('unit_rumah')->where('status_unit', 'dijual')->count();

        // Booking bulan ini
        $totalBookingBulanIni = DB::table('booking')
            ->whereBetween('tanggal_booking', [$startBulan->toDateString(), $endBulan->toDateString()])
            ->count();

        // Omset bulan ini (dari status akad/serah_terima bulan ini)
        $totalOmsetBulanIni = (float) DB::table('status_penjualan as sp')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
            ->whereBetween('sp.tanggal_perubahan', [$startBulan, $endBulan])
            ->sum('u.harga_jual');

        // ---- Prospek per Marketing (chart) ----
        $prospekPerMarketing = DB::table('users as m')
            ->leftJoin('prospek as pr', function ($join) use ($startBulan, $endBulan) {
                $join->on('pr.id_marketing', '=', 'm.id')
                    ->whereBetween('pr.tanggal_prospek', [$startBulan->toDateString(), $endBulan->toDateString()]);
            })
            ->where('m.role', 'marketing')
            ->where('m.status', 'aktif')
            ->select([
                'm.id',
                'm.nama_lengkap',
                DB::raw('COUNT(pr.id) as total_prospek'),
            ])
            ->groupBy('m.id', 'm.nama_lengkap')
            ->orderBy('total_prospek', 'desc')
            ->get()
            ->toArray();

        // ---- Top 5 Marketing by Closing ----
        $top5Marketing = DB::table('users as m')
            ->join('booking as b', 'b.id_marketing', '=', 'm.id')
            ->join('status_penjualan as sp', 'sp.id_booking', '=', 'b.id')
            ->where('m.role', 'marketing')
            ->where('sp.status_saat_ini', 'akad')
            ->whereBetween('sp.tanggal_perubahan', [$startBulan, $endBulan])
            ->select([
                'm.id',
                'm.nama_lengkap',
                DB::raw('COUNT(sp.id) as total_closing'),
            ])
            ->groupBy('m.id', 'm.nama_lengkap')
            ->orderBy('total_closing', 'desc')
            ->limit(5)
            ->get()
            ->toArray();

        // ---- Penjualan per Bulan (6 bulan terakhir) ----
        $penjualanPerBulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulanRef  = $now->copy()->subMonths($i);
            $awal      = $bulanRef->copy()->startOfMonth();
            $akhir     = $bulanRef->copy()->endOfMonth();
            $total     = (float) DB::table('status_penjualan as sp')
                ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                ->whereBetween('sp.tanggal_perubahan', [$awal, $akhir])
                ->sum('u.harga_jual');

            $penjualanPerBulan[] = [
                'bulan'  => $bulanRef->format('Y-m'),
                'label'  => $bulanRef->translatedFormat('M Y'),
                'total'  => $total,
                'count'  => DB::table('status_penjualan')
                    ->whereIn('status_saat_ini', ['akad', 'serah_terima'])
                    ->whereBetween('tanggal_perubahan', [$awal, $akhir])
                    ->count(),
            ];
        }

        // ---- Kategori Breakdown (pie chart) ----
        $kategoriBreakdown = [
            'subsidi' => [
                'total_unit'  => DB::table('unit_rumah')->where('kategori', 'subsidi')->where('status_unit', 'dijual')->count(),
                'total_nilai' => (float) DB::table('status_penjualan as sp')
                    ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                    ->where('u.kategori', 'subsidi')
                    ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                    ->sum('u.harga_jual'),
            ],
            'non_subsidi' => [
                'total_unit'  => DB::table('unit_rumah')->where('kategori', 'non_subsidi')->where('status_unit', 'dijual')->count(),
                'total_nilai' => (float) DB::table('status_penjualan as sp')
                    ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                    ->where('u.kategori', 'non_subsidi')
                    ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                    ->sum('u.harga_jual'),
            ],
        ];

        return [
            'total_users'              => $totalUsers,
            'total_perumahan'          => $totalPerumahan,
            'total_unit'               => $totalUnit,
            'total_tersedia'           => $totalTersedia,
            'total_terjual'            => $totalTerjual,
            'total_booking_bulan_ini'  => $totalBookingBulanIni,
            'total_omset_bulan_ini'    => $totalOmsetBulanIni,
            'prospek_per_marketing'    => $prospekPerMarketing,
            'top_5_marketing'          => $top5Marketing,
            'penjualan_per_bulan'      => $penjualanPerBulan,
            'kategori_breakdown'       => $kategoriBreakdown,
        ];
    }

    // -------------------------------------------------------------------------
    // 5. DASHBOARD MARKETING
    // -------------------------------------------------------------------------

    /**
     * Data untuk Dashboard Marketing (per user marketing).
     *
     * @param  int $idMarketing
     * @return array<string, mixed>
     */
    public function getDashboardMarketing(int $idMarketing): array
    {
        $now        = Carbon::now();
        $startBulan = $now->copy()->startOfMonth();
        $endBulan   = $now->copy()->endOfMonth();
        $bulan      = $now->month;
        $tahun      = $now->year;

        $marketing = DB::table('users')->find($idMarketing);

        // ---- Prospek bulan ini ----
        $prospekBulanIni = DB::table('prospek')
            ->where('id_marketing', $idMarketing)
            ->whereBetween('tanggal_prospek', [$startBulan->toDateString(), $endBulan->toDateString()])
            ->get(['status_prospek']);

        $totalProspekBulanIni = $prospekBulanIni->count();
        $prospekBaru          = $prospekBulanIni->where('status_prospek', 'baru')->count();
        $prospekBerminat      = $prospekBulanIni->where('status_prospek', 'berminat')->count();

        // Konversi bulan ini (jadi_konsumen)
        $konversiBulanIni = $prospekBulanIni->where('status_prospek', 'jadi_konsumen')->count();

        $conversionRate = $totalProspekBulanIni > 0
            ? round($konversiBulanIni / $totalProspekBulanIni * 100, 2)
            : 0.0;

        // ---- Booking bulan ini ----
        $totalBookingBulanIni = DB::table('booking')
            ->where('id_marketing', $idMarketing)
            ->whereBetween('tanggal_booking', [$startBulan->toDateString(), $endBulan->toDateString()])
            ->count();

        // ---- Closing bulan ini (status akad) ----
        $closingRows = DB::table('status_penjualan as sp')
            ->join('booking as b', 'b.id', '=', 'sp.id_booking')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->where('b.id_marketing', $idMarketing)
            ->where('sp.status_saat_ini', 'akad')
            ->whereBetween('sp.tanggal_perubahan', [$startBulan, $endBulan])
            ->select('u.harga_jual')
            ->get();

        $totalClosingBulanIni = $closingRows->count();
        $totalNilaiClosing    = (float) $closingRows->sum('harga_jual');
        $persentaseKomisi     = (float) ($marketing->persentase_komisi ?? 0);
        $totalKomisiBulanIni  = $totalNilaiClosing * ($persentaseKomisi / 100);

        // ---- Target bulan ini ----
        $target = DB::table('marketing_target')
            ->where('id_marketing', $idMarketing)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->first();

        $targetBulanIni   = (int) ($target->target_unit ?? 0);
        $pencapaianTarget = $targetBulanIni > 0
            ? round($totalClosingBulanIni / $targetBulanIni * 100, 2)
            : 0.0;

        // ---- Prospek per Sumber (pie chart) ----
        $prospekPerSumber = DB::table('prospek')
            ->where('id_marketing', $idMarketing)
            ->whereBetween('tanggal_prospek', [$startBulan->toDateString(), $endBulan->toDateString()])
            ->select('sumber_prospek', DB::raw('COUNT(*) as total'))
            ->groupBy('sumber_prospek')
            ->get()
            ->toArray();

        // ---- Tren Bulanan (6 bulan terakhir) ----
        $trenBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulanRef  = $now->copy()->subMonths($i);
            $awal      = $bulanRef->copy()->startOfMonth();
            $akhir     = $bulanRef->copy()->endOfMonth();

            $prospekCount = DB::table('prospek')
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_prospek', [$awal->toDateString(), $akhir->toDateString()])
                ->count();

            $closingCount = DB::table('status_penjualan as sp')
                ->join('booking as b', 'b.id', '=', 'sp.id_booking')
                ->where('b.id_marketing', $idMarketing)
                ->where('sp.status_saat_ini', 'akad')
                ->whereBetween('sp.tanggal_perubahan', [$awal, $akhir])
                ->count();

            $trenBulanan[] = [
                'bulan'         => $bulanRef->format('Y-m'),
                'label'         => $bulanRef->translatedFormat('M Y'),
                'total_prospek' => $prospekCount,
                'total_closing' => $closingCount,
            ];
        }

        return [
            'total_prospek_bulan_ini'  => $totalProspekBulanIni,
            'prospek_baru'             => $prospekBaru,
            'prospek_berminat'         => $prospekBerminat,
            'konversi_bulan_ini'       => $konversiBulanIni,
            'conversion_rate'          => $conversionRate,
            'total_booking_bulan_ini'  => $totalBookingBulanIni,
            'total_closing_bulan_ini'  => $totalClosingBulanIni,
            'target_bulan_ini'         => $targetBulanIni,
            'pencapaian_target'        => $pencapaianTarget,
            'total_komisi_bulan_ini'   => $totalKomisiBulanIni,
            'prospek_per_sumber'       => $prospekPerSumber,
            'tren_bulanan'             => $trenBulanan,
        ];
    }

    // -------------------------------------------------------------------------
    // 6. DASHBOARD MANAJEMEN
    // -------------------------------------------------------------------------

    /**
     * Data untuk Dashboard Manajemen (overview perusahaan).
     *
     * @return array<string, mixed>
     */
    public function getDashboardManajemen(): array
    {
        $now        = Carbon::now();
        $startBulan = $now->copy()->startOfMonth();
        $endBulan   = $now->copy()->endOfMonth();
        $startTahun = $now->copy()->startOfYear();
        $endTahun   = $now->copy()->endOfYear();

        // ---- Statistik Unit ----
        $totalPerumahan = DB::table('perumahan')->where('status', 'aktif')->count();
        $totalUnit      = DB::table('unit_rumah')->count();
        $totalTerjual   = DB::table('unit_rumah')->where('status_unit', 'dijual')->count();
        $totalTersedia  = DB::table('unit_rumah')->where('status_unit', 'tersedia')->count();

        // ---- Omset ----
        $totalOmsetBulanIni = (float) DB::table('status_penjualan as sp')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
            ->whereBetween('sp.tanggal_perubahan', [$startBulan, $endBulan])
            ->sum('u.harga_jual');

        $totalOmsetTahunIni = (float) DB::table('status_penjualan as sp')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
            ->whereBetween('sp.tanggal_perubahan', [$startTahun, $endTahun])
            ->sum('u.harga_jual');

        // ---- Booking bulan ini ----
        $totalBookingBulanIni = DB::table('booking')
            ->whereBetween('tanggal_booking', [$startBulan->toDateString(), $endBulan->toDateString()])
            ->count();

        // ---- Top 5 Marketing by Nilai Penjualan (bulan ini) ----
        $top5Marketing = DB::table('users as m')
            ->join('booking as b', 'b.id_marketing', '=', 'm.id')
            ->join('status_penjualan as sp', 'sp.id_booking', '=', 'b.id')
            ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
            ->where('m.role', 'marketing')
            ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
            ->whereBetween('sp.tanggal_perubahan', [$startBulan, $endBulan])
            ->select([
                'm.id',
                'm.nama_lengkap',
                DB::raw('COUNT(sp.id) as total_closing'),
                DB::raw('SUM(u.harga_jual) as total_nilai_penjualan'),
            ])
            ->groupBy('m.id', 'm.nama_lengkap')
            ->orderBy('total_nilai_penjualan', 'desc')
            ->limit(5)
            ->get()
            ->toArray();

        // ---- Penjualan per Bulan (12 bulan terakhir) ----
        $penjualanPerBulan = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulanRef = $now->copy()->subMonths($i);
            $awal     = $bulanRef->copy()->startOfMonth();
            $akhir    = $bulanRef->copy()->endOfMonth();

            $totalNilai = (float) DB::table('status_penjualan as sp')
                ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                ->whereBetween('sp.tanggal_perubahan', [$awal, $akhir])
                ->sum('u.harga_jual');

            $count = DB::table('status_penjualan')
                ->whereIn('status_saat_ini', ['akad', 'serah_terima'])
                ->whereBetween('tanggal_perubahan', [$awal, $akhir])
                ->count();

            $penjualanPerBulan[] = [
                'bulan'       => $bulanRef->format('Y-m'),
                'label'       => $bulanRef->translatedFormat('M Y'),
                'total_nilai' => $totalNilai,
                'total_unit'  => $count,
            ];
        }

        // ---- Kategori Breakdown (pie chart) ----
        $kategoriBreakdown = [
            'subsidi' => [
                'total_unit'  => DB::table('unit_rumah')->where('kategori', 'subsidi')->where('status_unit', 'dijual')->count(),
                'total_nilai' => (float) DB::table('status_penjualan as sp')
                    ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                    ->where('u.kategori', 'subsidi')
                    ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                    ->sum('u.harga_jual'),
            ],
            'non_subsidi' => [
                'total_unit'  => DB::table('unit_rumah')->where('kategori', 'non_subsidi')->where('status_unit', 'dijual')->count(),
                'total_nilai' => (float) DB::table('status_penjualan as sp')
                    ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                    ->where('u.kategori', 'non_subsidi')
                    ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                    ->sum('u.harga_jual'),
            ],
        ];

        // ---- Rata-rata Waktu Closing (hari dari booking → akad) ----
        $rataRataWaktuClosing = $this->hitungRataRataWaktuClosing();

        return [
            'total_perumahan'          => $totalPerumahan,
            'total_unit'               => $totalUnit,
            'total_terjual'            => $totalTerjual,
            'total_tersedia'           => $totalTersedia,
            'total_omset_bulan_ini'    => $totalOmsetBulanIni,
            'total_omset_tahun_ini'    => $totalOmsetTahunIni,
            'total_booking_bulan_ini'  => $totalBookingBulanIni,
            'top_5_marketing'          => $top5Marketing,
            'penjualan_per_bulan'      => $penjualanPerBulan,
            'kategori_breakdown'       => $kategoriBreakdown,
            'rata_rata_waktu_closing'  => $rataRataWaktuClosing,
        ];
    }

    // -------------------------------------------------------------------------
    // 7. EXPORT PDF LAPORAN PENJUALAN
    // -------------------------------------------------------------------------

    /**
     * Generate dan download PDF laporan penjualan.
     *
     * @param  array<string, mixed> $filters
     * @param  string $role ('admin'|'manajemen')
     * @return StreamedResponse
     */
    public function exportLaporanPenjualanPdf(array $filters, string $role = 'admin'): StreamedResponse
    {
        $data      = $this->getLaporanPenjualan($filters);
        $view      = $role === 'manajemen'
            ? 'manajemen.laporan.export-pdf'
            : 'admin.laporan.export-pdf';
        $filename  = 'laporan-penjualan-' . now()->format('Ymd-His') . '.pdf';

        /** @var \Barryvdh\DomPDF\Facade\Pdf $pdf */
        $pdf = app('dompdf.wrapper');
        $pdf->loadView($view, ['laporan' => $data, 'filters' => $filters, 'generated_at' => now()]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    // -------------------------------------------------------------------------
    // 8. EXPORT EXCEL LAPORAN PENJUALAN
    // -------------------------------------------------------------------------

    /**
     * Generate dan download Excel laporan penjualan.
     *
     * @param  array<string, mixed> $filters
     * @return BinaryFileResponse
     */
    public function exportLaporanPenjualanExcel(array $filters): BinaryFileResponse
    {
        $filename = 'laporan-penjualan-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new LaporanPenjualanExport($filters), $filename);
    }

    // -------------------------------------------------------------------------
    // HELPER PRIVATE
    // -------------------------------------------------------------------------

    /**
     * Hitung rata-rata waktu (hari) dari tanggal_booking hingga status akad.
     *
     * @return float
     */
    private function hitungRataRataWaktuClosing(): float
    {
        $rows = DB::table('status_penjualan as sp')
            ->join('booking as b', 'b.id', '=', 'sp.id_booking')
            ->where('sp.status_saat_ini', 'akad')
            ->select([
                'b.tanggal_booking',
                'sp.tanggal_perubahan as tanggal_akad',
            ])
            ->get();

        if ($rows->isEmpty()) {
            return 0.0;
        }

        $totalHari = $rows->reduce(function (float $carry, object $row): float {
            $booking = Carbon::parse($row->tanggal_booking);
            $akad    = Carbon::parse($row->tanggal_akad);
            return $carry + (float) $booking->diffInDays($akad);
        }, 0.0);

        return round($totalHari / $rows->count(), 1);
    }
}
