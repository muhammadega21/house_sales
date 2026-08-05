<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Enums\Role;
use App\Enums\StatusPenjualan as StatusPenjualanEnum;
use App\Http\Controllers\Controller;
use App\Models\StatusHistory;
use App\Models\StatusPenjualan;
use App\Services\StatusPenjualanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

final class StatusPenjualanController extends Controller
{
    public function __construct(private readonly StatusPenjualanService $statusPenjualanService) {}

    public function index(Request $request): View
    {
        $idMarketing = auth()->id();
        $search = $request->input('search', '');
        $filterStatus = $request->input('status', '');
        $periodeMulai = $request->input('periode_mulai', '');
        $periodeSelesai = $request->input('periode_selesai', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = StatusPenjualan::query()
            ->whereHas('booking', fn($q) => $q->where('id_marketing', $idMarketing))
            ->with(['booking.konsumen', 'unit.perumahan']);

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->whereHas('booking', function ($q) use ($term) {
                $q->where('kode_booking', 'like', $term)
                    ->orWhereHas('konsumen', function ($q) use ($term) {
                        $q->where('nama_lengkap', 'like', $term);
                    });
            });
        }

        if ($filterStatus !== '') {
            $query->where('status_saat_ini', $filterStatus);
        }

        if ($periodeMulai !== '' && $periodeSelesai !== '') {
            $query->whereBetween('tanggal_perubahan', [$periodeMulai, $periodeSelesai]);
        }

        $statusPenjualans = $query->orderByDesc('tanggal_perubahan')->paginate($perPage)->withQueryString();

        return view('marketing.status-penjualan.index', compact(
            'statusPenjualans',
            'search',
            'filterStatus',
            'periodeMulai',
            'periodeSelesai',
            'perPage'
        ));
    }

    public function show(int $id): View
    {
        $statusPenjualan = StatusPenjualan::with(['booking.konsumen', 'unit.perumahan'])->findOrFail($id);

        $this->authorize('view', $statusPenjualan);

        $availableTransitions = $statusPenjualan->status_saat_ini
            ->allowedTransitions();

        $timeline = StatusHistory::where('id_booking', $statusPenjualan->id_booking)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('marketing.status-penjualan.show', compact('statusPenjualan', 'availableTransitions', 'timeline'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $statusPenjualan = StatusPenjualan::findOrFail($id);

        $this->authorize('update', $statusPenjualan);

        $validated = $request->validate([
            'status_baru' => ['required', 'string', 'in:' . implode(',', array_map(fn(StatusPenjualanEnum $status) => $status->value, StatusPenjualanEnum::cases()))],
            'catatan' => ['required', 'string', 'max:500'],
        ]);

        if ($request->user()->role === Role::Marketing && $validated['status_baru'] !== StatusPenjualanEnum::Batal->value) {
            return back()->with('error', 'Marketing hanya dapat membatalkan status penjualan.')->withInput();
        }

        try {
            $this->statusPenjualanService->transition(
                $statusPenjualan,
                $validated['status_baru'],
                $validated['catatan'],
                auth()->id()
            );

            return back()->with('success', 'Status penjualan berhasil diperbarui.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
