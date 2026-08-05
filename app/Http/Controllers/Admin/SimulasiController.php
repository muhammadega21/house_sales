<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\MetodePembayaran;
use App\Http\Controllers\Controller;
use App\Http\Requests\SimulasiRequest;
use App\Models\UnitRumah;
use App\Services\SimulasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SimulasiController extends Controller
{
    public function __construct(private readonly SimulasiService $simulasiService) {}

    public function index(Request $request): View
    {
        $units = UnitRumah::query()
            ->where('status_unit', 'tersedia')
            ->orderBy('kode_unit')
            ->get();

        return view('admin.simulasi.index', compact('units'));
    }

    public function hitung(SimulasiRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $hargaRumah = $this->simulasiService->getUnitHarga((int) $validated['id_unit']);
        $metode = MetodePembayaran::from($validated['metode_pembayaran']);
        $dpPersen = (float) ($validated['dp_persen'] ?? 0);
        $tenorTahun = isset($validated['tenor_tahun']) ? (int) $validated['tenor_tahun'] : 0;
        $sukuBunga = (float) ($validated['suku_bunga'] ?? 0);
        $diskonCashKeras = (float) ($validated['diskon_persen'] ?? 0);

        $hasil = match ($metode) {
            MetodePembayaran::Kpr => $this->simulasiService->hitungKpr($hargaRumah, $dpPersen, $tenorTahun, $sukuBunga),
            MetodePembayaran::CashBertahap => $this->simulasiService->hitungCashBertahap($hargaRumah, $dpPersen, $tenorTahun),
            MetodePembayaran::CashKeras => $this->simulasiService->hitungCashKeras($hargaRumah, $diskonCashKeras),
        };

        $perbandingan = $this->simulasiService->hitungSemuaMetode($hargaRumah, $dpPersen, $tenorTahun, $sukuBunga, $diskonCashKeras);

        return response()->json([
            'success' => true,
            'hasil' => $hasil,
            'perbandingan' => $perbandingan,
        ]);
    }
}
