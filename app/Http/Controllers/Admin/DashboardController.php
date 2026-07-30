<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusProspek;
use App\Http\Controllers\Controller;
use App\Models\Prospek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $bulanIni = now()->startOfMonth();

        $totalProspekBulanIni = Prospek::query()
            ->where('tanggal_prospek', '>=', $bulanIni)
            ->count();

        $prospekPerMarketing = User::marketing()
            ->aktif()
            ->withCount(['prospek as total_prospek' => function ($query) use ($bulanIni) {
                $query->where('tanggal_prospek', '>=', $bulanIni);
            }])
            ->withCount(['prospek as konversi' => function ($query) use ($bulanIni) {
                $query->where('status_prospek', StatusProspek::JadiKonsumen->value)
                    ->where('tanggal_prospek', '>=', $bulanIni);
            }])
            ->orderByDesc('total_prospek')
            ->get()
            ->map(function ($m) {
                $conversionRate = $m->total_prospek > 0
                    ? round(($m->konversi / $m->total_prospek) * 100, 1)
                    : 0;

                return [
                    'id' => $m->id,
                    'nama_lengkap' => $m->nama_lengkap,
                    'total_prospek' => $m->total_prospek,
                    'konversi' => $m->konversi,
                    'conversion_rate' => $conversionRate,
                ];
            });

        $conversionRatePerusahaan = $totalProspekBulanIni > 0
            ? round(($prospekPerMarketing->sum('konversi') / $totalProspekBulanIni) * 100, 1)
            : 0;

        $topMarketingByProspek = $prospekPerMarketing->sortByDesc('total_prospek')->take(5);
        $topMarketingByConversion = $prospekPerMarketing->where('conversion_rate', '>', 0)->sortByDesc('conversion_rate')->take(5);

        return view('admin.dashboard', array_merge(
            compact(
                'totalProspekBulanIni',
                'prospekPerMarketing',
                'conversionRatePerusahaan',
                'topMarketingByProspek',
                'topMarketingByConversion'
            ),
            ['activeTab' => 'dashboard']
        ));
    }
}
