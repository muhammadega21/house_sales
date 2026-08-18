<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perumahan;
use App\Models\User;
use App\Services\LaporanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function __construct(
        private readonly LaporanService $laporanService
    ) {}

    // -------------------------------------------------------------------------
    // 1. LAPORAN PENJUALAN
    // -------------------------------------------------------------------------

    /**
     * Halaman laporan penjualan (tabel, ringkasan, chart).
     *
     * Filter: periode_mulai, periode_selesai, id_perumahan, kategori, id_marketing
     */
    public function penjualan(Request $request): View
    {
        $request->validate([
            'periode_mulai' => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'id_perumahan' => ['nullable', 'integer', 'exists:perumahan,id'],
            'kategori' => ['nullable', 'in:subsidi,non_subsidi'],
            'id_marketing' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:akad,serah_terima'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
            'id_perumahan',
            'kategori',
            'id_marketing',
            'status',
        ]);

        $laporan = $this->laporanService->getLaporanPenjualan($filters);

        $perPage = (int) $request->query('per_page', 15);
        $currentPage = (int) $request->query('page', 1);
        $total = count($laporan['data']);
        $items = array_slice($laporan['data'], ($currentPage - 1) * $perPage, $perPage);

        $laporan['data'] = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $perumahanList = Perumahan::aktif()->orderBy('nama_perumahan')->get(['id', 'nama_perumahan']);
        $marketingList = User::marketing()->aktif()->orderBy('nama_lengkap')->get(['id', 'nama_lengkap']);

        $chartLabels = collect($laporan['per_bulan'])->pluck('label');
        $chartUnit = collect($laporan['per_bulan'])->pluck('total_unit');
        $chartNilai = collect($laporan['per_bulan'])->pluck('total_nilai');

        $chartKategori = [
            'labels' => ['Subsidi', 'Non-Subsidi'],
            'data' => [
                $laporan['per_kategori']['subsidi']['total_unit'],
                $laporan['per_kategori']['non_subsidi']['total_unit'],
            ],
        ];

        return view('admin.laporan.penjualan', compact(
            'laporan',
            'filters',
            'perumahanList',
            'marketingList',
            'chartLabels',
            'chartUnit',
            'chartNilai',
            'chartKategori',
        ));
    }

    // -------------------------------------------------------------------------
    // 2. LAPORAN PER MARKETING
    // -------------------------------------------------------------------------

    /**
     * Halaman laporan kinerja marketing (target vs realisasi).
     *
     * Filter: periode_mulai, periode_selesai, id_marketing
     */
    public function marketing(Request $request): View
    {
        $request->validate([
            'periode_mulai' => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'id_marketing' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
            'id_marketing',
        ]);

        $laporan = $this->laporanService->getLaporanPerMarketing($filters);
        $marketingList = User::marketing()->aktif()->orderBy('nama_lengkap')->get(['id', 'nama_lengkap']);

        // Agregat ringkasan
        $totalClosing = array_sum(array_column($laporan, 'total_closing'));
        $totalNilaiPenjualan = array_sum(array_column($laporan, 'total_nilai_penjualan'));
        $totalKomisi = array_sum(array_column($laporan, 'total_komisi'));
        $avgConversionRate = count($laporan) > 0
            ? round(array_sum(array_column($laporan, 'conversion_rate')) / count($laporan), 2)
            : 0.0;

        // Data chart: bar chart kinerja per marketing
        $chartLabels = array_column($laporan, 'nama');
        $chartProspek = array_column($laporan, 'total_prospek');
        $chartBooking = array_column($laporan, 'total_booking');
        $chartClosing = array_column($laporan, 'total_closing');
        $chartPencapaian = array_column($laporan, 'pencapaian_target');

        return view('admin.laporan.marketing', compact(
            'laporan',
            'filters',
            'marketingList',
            'totalClosing',
            'totalNilaiPenjualan',
            'totalKomisi',
            'avgConversionRate',
            'chartLabels',
            'chartProspek',
            'chartBooking',
            'chartClosing',
            'chartPencapaian',
        ));
    }

    // -------------------------------------------------------------------------
    // 3. LAPORAN UNIT
    // -------------------------------------------------------------------------

    /**
     * Halaman laporan ketersediaan unit per perumahan.
     *
     * Filter: id_perumahan, kategori, status_unit
     */
    public function unit(Request $request): View
    {
        $request->validate([
            'id_perumahan' => ['nullable', 'integer', 'exists:perumahan,id'],
            'kategori' => ['nullable', 'in:subsidi,non_subsidi'],
            'status_unit' => ['nullable', 'in:tersedia,dibooking,dijual,dibatalkan'],
        ]);

        $filters = $this->resolveFilters($request, [
            'id_perumahan',
            'kategori',
            'status_unit',
        ]);

        $laporan = $this->laporanService->getLaporanUnit($filters);

        // Agregat total keseluruhan
        $totalUnit = array_sum(array_column($laporan, 'total_unit'));
        $totalTersedia = array_sum(array_column($laporan, 'tersedia'));
        $totalDibooking = array_sum(array_column($laporan, 'dibooking'));
        $totalDijual = array_sum(array_column($laporan, 'dijual'));
        $totalDibatalkan = array_sum(array_column($laporan, 'dibatalkan'));

        $perPage = (int) $request->query('per_page', 15);
        $currentPage = (int) $request->query('page', 1);
        $total = count($laporan);
        $items = array_slice($laporan, ($currentPage - 1) * $perPage, $perPage);

        $laporan = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $perumahanList = Perumahan::aktif()->orderBy('nama_perumahan')->get(['id', 'nama_perumahan']);

        // Data chart: stacked bar per perumahan
        $chartLabels = array_column($laporan, 'nama_perumahan');
        $chartTersedia = array_column($laporan, 'tersedia');
        $chartDibooking = array_column($laporan, 'dibooking');
        $chartDijual = array_column($laporan, 'dijual');
        $chartDibatalkan = array_column($laporan, 'dibatalkan');

        // Pie chart status global
        $chartStatusPie = [
            'labels' => ['Tersedia', 'Dibooking', 'Dijual', 'Dibatalkan'],
            'data' => [$totalTersedia, $totalDibooking, $totalDijual, $totalDibatalkan],
        ];

        return view('admin.laporan.unit', compact(
            'laporan',
            'filters',
            'perumahanList',
            'totalUnit',
            'totalTersedia',
            'totalDibooking',
            'totalDijual',
            'totalDibatalkan',
            'chartLabels',
            'chartTersedia',
            'chartDibooking',
            'chartDijual',
            'chartDibatalkan',
            'chartStatusPie',
        ));
    }

    // -------------------------------------------------------------------------
    // 4. EXPORT PDF
    // -------------------------------------------------------------------------

    /**
     * Export laporan penjualan ke PDF dan langsung download.
     *
     * Parameter sama dengan penjualan().
     */
    public function exportPdf(Request $request): Response
    {
        $request->validate([
            'periode_mulai' => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'id_perumahan' => ['nullable', 'integer', 'exists:perumahan,id'],
            'kategori' => ['nullable', 'in:subsidi,non_subsidi'],
            'id_marketing' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:akad,serah_terima'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
            'id_perumahan',
            'kategori',
            'id_marketing',
            'status',
        ]);

        return $this->laporanService->exportLaporanPenjualanPdf($filters, 'admin');
    }

    // -------------------------------------------------------------------------
    // 5. EXPORT EXCEL
    // -------------------------------------------------------------------------

    /**
     * Export laporan penjualan ke Excel dan langsung download.
     *
     * Parameter sama dengan penjualan().
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'periode_mulai' => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'id_perumahan' => ['nullable', 'integer', 'exists:perumahan,id'],
            'kategori' => ['nullable', 'in:subsidi,non_subsidi'],
            'id_marketing' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:akad,serah_terima'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
            'id_perumahan',
            'kategori',
            'id_marketing',
            'status',
        ]);

        return $this->laporanService->exportLaporanPenjualanExcel($filters);
    }

    // -------------------------------------------------------------------------
    // HELPER PRIVATE
    // -------------------------------------------------------------------------

    /**
     * Ambil subset filter dari request, hilangkan nilai null/kosong,
     * dan isi default periode jika tidak disediakan.
     *
     * @param  array<string>  $keys
     * @return array<string, mixed>
     */
    private function resolveFilters(Request $request, array $keys): array
    {
        $filters = [];

        foreach ($keys as $key) {
            $value = $request->input($key);
            if ($value !== null && $value !== '') {
                $filters[$key] = $value;
            }
        }

        // Default periode: bulan berjalan
        if (! isset($filters['periode_mulai'])) {
            $filters['periode_mulai'] = Carbon::now()->startOfMonth()->toDateString();
        }
        if (! isset($filters['periode_selesai'])) {
            $filters['periode_selesai'] = Carbon::now()->endOfMonth()->toDateString();
        }

        return $filters;
    }
}
