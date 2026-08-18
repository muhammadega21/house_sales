<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class KinerjaController extends Controller
{
    public function index(Request $request): View
    {
        $idMarketing = auth()->id();

        $request->validate([
            'periode_mulai' => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2099'],
        ]);

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $bulan = (int) $request->input('bulan');
            $tahun = (int) $request->input('tahun');
            $start = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
        } else {
            $start = $request->input('periode_mulai')
                ? Carbon::parse($request->input('periode_mulai'))->startOfDay()
                : Carbon::now()->startOfMonth();

            $end = $request->input('periode_selesai')
                ? Carbon::parse($request->input('periode_selesai'))->endOfDay()
                : Carbon::now()->endOfMonth();
        }

        $result = DB::transaction(function () use ($idMarketing, $start, $end) {
            $totalProspek = DB::table('prospek')
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_prospek', [$start->toDateString(), $end->toDateString()])
                ->count();

            $totalBooking = DB::table('booking')
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_booking', [$start->toDateString(), $end->toDateString()])
                ->count();

            $closingQuery = DB::table('status_penjualan as sp')
                ->join('booking as b', 'b.id', '=', 'sp.id_booking')
                ->join('unit_rumah as u', 'u.id', '=', 'sp.id_unit')
                ->join('konsumen as k', 'k.id', '=', 'b.id_konsumen')
                ->where('b.id_marketing', $idMarketing)
                ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                ->whereBetween('sp.tanggal_perubahan', [$start->toDateString(), $end->toDateString()]);

            $totalClosing = $closingQuery->count();
            $totalNilaiPenjualan = (float) $closingQuery->sum('u.harga_jual');

            $closingRows = $closingQuery
                ->select([
                    'sp.id as id_status',
                    'sp.status_saat_ini',
                    'sp.tanggal_perubahan',
                    'b.id as id_booking',
                    'k.nama_lengkap as nama_konsumen',
                    'u.kode_unit',
                    'u.tipe_rumah',
                    'u.harga_jual',
                ])
                ->orderByDesc('sp.tanggal_perubahan')
                ->paginate(10)
                ->withQueryString();

            $sumberProspek = DB::table('prospek')
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_prospek', [$start->toDateString(), $end->toDateString()])
                ->select('sumber_prospek', DB::raw('count(*) as total'))
                ->groupBy('sumber_prospek')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($item) => [
                    'label' => (string) $item->sumber_prospek,
                    'total' => $item->total,
                ]);

            $periodStart = $start->copy()->startOfMonth();
            $periodEnd = $end->copy()->endOfMonth();

            $months = [];
            $monthCursor = $periodStart->copy();
            while ($monthCursor->lessThanOrEqualTo($periodEnd)) {
                $months[] = $monthCursor->format('Y-m');
                $monthCursor->addMonth();
            }

            $prospekPerBulan = DB::table('prospek')
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_prospek', [$start->toDateString(), $end->toDateString()])
                ->selectRaw("DATE_FORMAT(tanggal_prospek, '%Y-%m') as bulan, count(*) as total")
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->toArray();

            $closingPerBulan = DB::table('status_penjualan as sp')
                ->join('booking as b', 'b.id', '=', 'sp.id_booking')
                ->where('b.id_marketing', $idMarketing)
                ->whereIn('sp.status_saat_ini', ['akad', 'serah_terima'])
                ->whereBetween('sp.tanggal_perubahan', [$start->toDateString(), $end->toDateString()])
                ->selectRaw("DATE_FORMAT(sp.tanggal_perubahan, '%Y-%m') as bulan, count(*) as total")
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->toArray();

            $chartLabels = [];
            $chartProspek = [];
            $chartClosing = [];

            foreach ($months as $monthKey) {
                $date = Carbon::createFromFormat('Y-m', $monthKey);
                $chartLabels[] = $date->translatedFormat('M Y');
                $chartProspek[] = $prospekPerBulan[$monthKey] ?? 0;
                $chartClosing[] = $closingPerBulan[$monthKey] ?? 0;
            }

            return [
                'totalProspek' => $totalProspek,
                'totalBooking' => $totalBooking,
                'totalClosing' => $totalClosing,
                'totalNilaiPenjualan' => $totalNilaiPenjualan,
                'closingRows' => $closingRows,
                'sumberProspek' => $sumberProspek,
                'chartLabels' => $chartLabels,
                'chartProspek' => $chartProspek,
                'chartClosing' => $chartClosing,
            ];
        });

        $user = $request->user();
        $persentaseKomisi = $user?->persentase_komisi ?? 0;
        $totalKomisi = $result['totalNilaiPenjualan'] * ((float) $persentaseKomisi / 100);

        $conversionRate = $result['totalProspek'] > 0
            ? round($result['totalClosing'] / $result['totalProspek'] * 100, 2)
            : 0.0;

        $targetMonth = $start->month;
        $targetYear = $start->year;

        $target = DB::table('marketing_target')
            ->where('id_marketing', $idMarketing)
            ->where('periode_bulan', $targetMonth)
            ->where('periode_tahun', $targetYear)
            ->first();

        $targetUnit = (int) ($target->target_unit ?? 0);
        $progressTarget = $targetUnit > 0
            ? round($result['totalClosing'] / $targetUnit * 100, 2)
            : 0.0;

        return view('marketing.kinerja.index', [
            'totalProspek' => $result['totalProspek'],
            'totalBooking' => $result['totalBooking'],
            'totalClosing' => $result['totalClosing'],
            'conversionRate' => $conversionRate,
            'totalKomisi' => $totalKomisi,
            'persentaseKomisi' => $persentaseKomisi,
            'targetUnit' => $targetUnit,
            'progressTarget' => $progressTarget,
            'chartLabels' => $result['chartLabels'],
            'chartProspek' => $result['chartProspek'],
            'chartClosing' => $result['chartClosing'],
            'sumberProspek' => $result['sumberProspek'],
            'closingRows' => $result['closingRows'],
            'start' => $start,
            'end' => $end,
        ]);
    }
}
