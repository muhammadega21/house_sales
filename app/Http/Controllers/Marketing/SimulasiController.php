<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Enums\MetodePembayaran;
use App\Http\Controllers\Controller;
use App\Http\Requests\SimulasiRequest;
use App\Models\Konsumen;
use App\Models\PengaturanSistem;
use App\Models\UnitRumah;
use App\Services\PdfService;
use App\Services\SimulasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

        $konsumenOptions = Konsumen::query()
            ->where('id_marketing', auth()->id())
            ->orderBy('nama_lengkap')
            ->get()
            ->mapWithKeys(fn($k) => [$k->id => $k->nama_lengkap . ' - ' . $k->nik])
            ->toArray();

        $settings = PengaturanSistem::getValues([
            'default_kpr_bunga',
            'default_cash_keras_diskon',
            'dp_subsidi_min_persen',
            'dp_subsidi_max_persen',
            'dp_non_subsidi_min_persen',
            'dp_non_subsidi_max_persen',
        ]);

        $dpLimits = [
            'subsidi' => [
                'min' => (float) ($settings['dp_subsidi_min_persen'] ?? 1),
                'max' => (float) ($settings['dp_subsidi_max_persen'] ?? 5),
            ],
            'non_subsidi' => [
                'min' => (float) ($settings['dp_non_subsidi_min_persen'] ?? 10),
                'max' => (float) ($settings['dp_non_subsidi_max_persen'] ?? 30),
            ],
        ];

        return view('marketing.simulasi.index', compact('units', 'konsumenOptions', 'settings', 'dpLimits'));
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

    public function simpan(SimulasiRequest $request): RedirectResponse
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

        $this->simulasiService->simpanSimulasi(
            array_merge($hasil, [
                'id_unit' => $validated['id_unit'],
                'metode' => $validated['metode_pembayaran'],
            ]),
            auth()->id(),
            $validated['id_konsumen'] ?? null,
        );

        if ($request->wantsJson() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hasil simulasi berhasil disimpan.',
            ]);
        }

        return back()->with('success', 'Hasil simulasi berhasil disimpan.');
    }

    public function perbandingan(Request $request): View
    {
        $validated = $request->validate([
            'id_unit' => ['required', 'integer', 'exists:unit_rumah,id'],
            'dp_persen' => ['required', 'numeric', 'min:0', 'max:100'],
            'tenor_tahun' => ['required', 'integer', 'min:1', 'max:30'],
            'suku_bunga' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'diskon_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'id_konsumen' => ['nullable', 'integer', 'exists:konsumen,id'],
        ]);

        $unit = UnitRumah::with('perumahan')->findOrFail($validated['id_unit']);
        $konsumen = isset($validated['id_konsumen']) ? Konsumen::find($validated['id_konsumen']) : null;

        $hasilKpr = $this->simulasiService->hitungKpr(
            $unit->harga_jual,
            (float) $validated['dp_persen'],
            (int) $validated['tenor_tahun'],
            (float) ($validated['suku_bunga'] ?? 0)
        );

        $hasilCashBertahap = $this->simulasiService->hitungCashBertahap(
            $unit->harga_jual,
            (float) $validated['dp_persen'],
            (int) $validated['tenor_tahun']
        );

        $hasilCashKeras = $this->simulasiService->hitungCashKeras(
            $unit->harga_jual,
            (float) ($validated['diskon_persen'] ?? 0)
        );

        return view('marketing.simulasi.perbandingan', compact('unit', 'konsumen', 'hasilKpr', 'hasilCashBertahap', 'hasilCashKeras'));
    }

    public function exportPdf(Request $request, PdfService $pdfService)
    {
        $validated = $request->validate([
            'id_unit' => ['required', 'integer', 'exists:unit_rumah,id'],
            'dp_persen' => ['required', 'numeric', 'min:0', 'max:100'],
            'tenor_tahun' => ['required', 'integer', 'min:1', 'max:30'],
            'suku_bunga' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'diskon_persen' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'id_konsumen' => ['nullable', 'integer', 'exists:konsumen,id'],
        ]);

        $unit = UnitRumah::with('perumahan')->findOrFail($validated['id_unit']);
        $konsumen = isset($validated['id_konsumen']) ? Konsumen::find($validated['id_konsumen']) : null;

        $hasilKpr = $this->simulasiService->hitungKpr(
            $unit->harga_jual,
            (float) $validated['dp_persen'],
            (int) $validated['tenor_tahun'],
            (float) ($validated['suku_bunga'] ?? 0)
        );

        $hasilCashBertahap = $this->simulasiService->hitungCashBertahap(
            $unit->harga_jual,
            (float) $validated['dp_persen'],
            (int) $validated['tenor_tahun']
        );

        $hasilCashKeras = $this->simulasiService->hitungCashKeras(
            $unit->harga_jual,
            (float) ($validated['diskon_persen'] ?? 0)
        );

        $filename = sprintf('perbandingan-simulasi-%s.pdf', str_replace([' ', '/'], '-', strtolower($unit->kode_unit)));

        return $pdfService->downloadView('marketing.simulasi.export', [
            'unit' => $unit,
            'konsumen' => $konsumen,
            'hasilKpr' => $hasilKpr,
            'hasilCashBertahap' => $hasilCashBertahap,
            'hasilCashKeras' => $hasilCashKeras,
        ], $filename);
    }
}
