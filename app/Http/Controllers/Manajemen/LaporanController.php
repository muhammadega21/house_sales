<?php

declare(strict_types=1);

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Perumahan;
use App\Models\User;
use App\Services\LaporanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LaporanController extends Controller
{
    public function __construct(
        private readonly LaporanService $laporanService
    ) {}

    /**
     * Halaman laporan penjualan untuk manajemen.
     *
     * Filter: periode_mulai, periode_selesai, id_perumahan, kategori
     *
     * @param  Request $request
     * @return View
     */
    public function penjualan(Request $request): View
    {
        $request->validate([
            'periode_mulai'   => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'id_perumahan'    => ['nullable', 'integer', 'exists:perumahan,id'],
            'kategori'        => ['nullable', 'in:subsidi,non_subsidi'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
            'id_perumahan',
            'kategori',
        ]);

        $laporan = $this->laporanService->getLaporanPenjualan($filters);

        $perumahanList = Perumahan::aktif()->orderBy('nama_perumahan')->get(['id', 'nama_perumahan']);

        $chartLabels   = collect($laporan['breakdown_bulan'])->pluck('label');
        $chartUnit     = collect($laporan['breakdown_bulan'])->pluck('total_unit');
        $chartNilai    = collect($laporan['breakdown_bulan'])->pluck('total_nilai');
        $chartKategori = [
            'labels' => ['Subsidi', 'Non-Subsidi'],
            'data'   => [
                $laporan['breakdown_kategori']['subsidi']['total_unit'],
                $laporan['breakdown_kategori']['non_subsidi']['total_unit'],
            ],
        ];

        return view('manajemen.laporan.penjualan', compact(
            'laporan',
            'filters',
            'perumahanList',
            'chartLabels',
            'chartUnit',
            'chartNilai',
            'chartKategori',
        ));
    }

    /**
     * Halaman laporan kinerja semua marketing untuk manajemen.
     *
     * Filter: periode_mulai, periode_selesai
     *
     * @param  Request $request
     * @return View
     */
    public function marketing(Request $request): View
    {
        $request->validate([
            'periode_mulai'   => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
        ]);

        $laporan = $this->laporanService->getLaporanPerMarketing($filters);

        $totalClosing        = array_sum(array_column($laporan, 'total_closing'));
        $totalNilaiPenjualan = array_sum(array_column($laporan, 'total_nilai_penjualan'));
        $totalKomisi         = array_sum(array_column($laporan, 'total_komisi'));
        $avgConversionRate   = count($laporan) > 0
            ? round(array_sum(array_column($laporan, 'conversion_rate')) / count($laporan), 2)
            : 0.0;

        usort($laporan, fn(array $a, array $b) => $b['total_closing'] <=> $a['total_closing']);

        $chartLabels     = array_column($laporan, 'nama');
        $chartProspek    = array_column($laporan, 'total_prospek');
        $chartBooking    = array_column($laporan, 'total_booking');
        $chartClosing    = array_column($laporan, 'total_closing');
        $chartPencapaian = array_column($laporan, 'pencapaian_target');

        return view('manajemen.laporan.marketing', compact(
            'laporan',
            'filters',
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

    /**
     * Export laporan penjualan ke PDF untuk manajemen.
     *
     * @param  Request $request
     * @return StreamedResponse
     */
    public function exportPdf(Request $request): StreamedResponse
    {
        $request->validate([
            'periode_mulai'   => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'id_perumahan'    => ['nullable', 'integer', 'exists:perumahan,id'],
            'kategori'        => ['nullable', 'in:subsidi,non_subsidi'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
            'id_perumahan',
            'kategori',
        ]);

        return $this->laporanService->exportLaporanPenjualanPdf($filters, 'manajemen');
    }

    /**
     * Export laporan penjualan ke Excel untuk manajemen.
     *
     * @param  Request $request
     * @return BinaryFileResponse
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $request->validate([
            'periode_mulai'   => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'id_perumahan'    => ['nullable', 'integer', 'exists:perumahan,id'],
            'kategori'        => ['nullable', 'in:subsidi,non_subsidi'],
        ]);

        $filters = $this->resolveFilters($request, [
            'periode_mulai',
            'periode_selesai',
            'id_perumahan',
            'kategori',
        ]);

        return $this->laporanService->exportLaporanPenjualanExcel($filters);
    }

    /**
     * Ambil subset filter dari request, hilangkan nilai null/kosong,
     * dan isi default periode jika tidak disediakan.
     *
     * @param  Request       $request
     * @param  array<string> $keys
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

        if (!isset($filters['periode_mulai'])) {
            $filters['periode_mulai'] = Carbon::now()->startOfMonth()->toDateString();
        }

        if (!isset($filters['periode_selesai'])) {
            $filters['periode_selesai'] = Carbon::now()->endOfMonth()->toDateString();
        }

        return $filters;
    }
}
