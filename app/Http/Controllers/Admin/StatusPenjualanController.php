<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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
        $search = $request->input('search', '');
        $filterStatus = $request->input('status', '');
        $filterPerumahan = $request->input('perumahan', '');
        $filterMarketing = $request->input('marketing', '');
        $periodeMulai = $request->input('periode_mulai', '');
        $periodeSelesai = $request->input('periode_selesai', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = StatusPenjualan::query()
            ->with(['booking.konsumen', 'booking.marketing', 'unit.perumahan']);

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

        if ($filterPerumahan !== '') {
            $query->whereHas('unit.perumahan', function ($q) use ($filterPerumahan) {
                $q->where('id', $filterPerumahan);
            });
        }

        if ($filterMarketing !== '') {
            $query->whereHas('booking', function ($q) use ($filterMarketing) {
                $q->where('id_marketing', $filterMarketing);
            });
        }

        if ($periodeMulai !== '' && $periodeSelesai !== '') {
            $query->whereBetween('tanggal_perubahan', [$periodeMulai, $periodeSelesai]);
        }

        $statusPenjualans = $query->orderByDesc('tanggal_perubahan')->paginate($perPage)->withQueryString();

        $summary = StatusPenjualan::query()
            ->selectRaw('status_saat_ini, count(*) as total')
            ->groupBy('status_saat_ini')
            ->pluck('total', 'status_saat_ini')
            ->toArray();

        return view('admin.status-penjualan.index', compact(
            'statusPenjualans',
            'search',
            'filterStatus',
            'filterPerumahan',
            'filterMarketing',
            'periodeMulai',
            'periodeSelesai',
            'perPage',
            'summary'
        ));
    }

    public function show(int $id): View
    {
        $statusPenjualan = StatusPenjualan::with(['booking.konsumen', 'booking.marketing', 'unit.perumahan'])->findOrFail($id);

        $this->authorize('view', $statusPenjualan);

        $availableTransitions = $statusPenjualan->status_saat_ini
            ->allowedTransitions();

        $timeline = StatusHistory::where('id_booking', $statusPenjualan->id_booking)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.status-penjualan.show', compact('statusPenjualan', 'availableTransitions', 'timeline'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $statusPenjualan = StatusPenjualan::findOrFail($id);

        $this->authorize('update', $statusPenjualan);

        $validated = $request->validate([
            'status_baru' => ['required', 'string', 'in:' . implode(',', array_map(fn(StatusPenjualanEnum $status) => $status->value, StatusPenjualanEnum::cases()))],
            'catatan' => ['required', 'string', 'max:500'],
        ]);

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
