<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Enums\StatusProspek;
use App\Http\Controllers\Controller;
use App\Models\Konsumen;
use App\Models\Prospek;
use App\Services\ProspekService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly ProspekService $prospekService) {}

    public function index(Request $request): View
    {
        $idMarketing = auth()->id();
        $bulanIni = now()->startOfMonth();

        $stats = $this->prospekService->getStats($idMarketing);

        $totalProspekBulanIni = Prospek::query()
            ->where('id_marketing', $idMarketing)
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->count();

        $baruBulanIni = Prospek::query()
            ->where('id_marketing', $idMarketing)
            ->where('status_prospek', StatusProspek::Baru->value)
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->count();

        $berminatBulanIni = Prospek::query()
            ->where('id_marketing', $idMarketing)
            ->where('status_prospek', StatusProspek::Berminat->value)
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->count();

        $konversiBulanIni = Prospek::query()
            ->where('id_marketing', $idMarketing)
            ->where('status_prospek', StatusProspek::JadiKonsumen->value)
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->count();

        $conversionRate = $totalProspekBulanIni > 0
            ? round(($konversiBulanIni / $totalProspekBulanIni) * 100, 1)
            : 0;

        $prospekPerSumber = Prospek::query()
            ->where('id_marketing', $idMarketing)
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->select('sumber_prospek', DB::raw('count(*) as total'))
            ->groupBy('sumber_prospek')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->sumber_prospek->value => $item->total]);

        $dataBulan = collect();
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $start = $bulan->copy()->startOfMonth();
            $end = $bulan->copy()->endOfMonth();

            $prospekCount = Prospek::query()
                ->where('id_marketing', $idMarketing)
                ->whereBetween('tanggal_prospek', [$start, $end])
                ->count();

            $konversiCount = Prospek::query()
                ->where('id_marketing', $idMarketing)
                ->where('status_prospek', StatusProspek::JadiKonsumen->value)
                ->whereBetween('tanggal_prospek', [$start, $end])
                ->count();

            $dataBulan->push([
                'bulan' => $bulan->translatedFormat('M Y'),
                'prospek' => $prospekCount,
                'konversi' => $konversiCount,
            ]);
        }

        $prospekTerbaru = Prospek::query()
            ->where('id_marketing', $idMarketing)
            ->with('marketing')
            ->latest('tanggal_prospek')
            ->limit(5)
            ->get();

        $konsumenTerbaru = Konsumen::query()
            ->where('id_marketing', $idMarketing)
            ->with('prospek')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('marketing.dashboard', array_merge(
            compact(
                'stats',
                'totalProspekBulanIni',
                'baruBulanIni',
                'berminatBulanIni',
                'konversiBulanIni',
                'conversionRate',
                'prospekPerSumber',
                'dataBulan',
                'prospekTerbaru',
                'konsumenTerbaru'
            ),
            ['activeTab' => 'dashboard']
        ));
    }
}
