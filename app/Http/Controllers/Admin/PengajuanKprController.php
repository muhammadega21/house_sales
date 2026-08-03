<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanKprRequest;
use App\Models\PengajuanKpr;
use App\Services\PengajuanKprService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PengajuanKprController extends Controller
{
    public function __construct(private readonly PengajuanKprService $pengajuanKprService) {}

    public function index(Request $request): View
    {
        $query = PengajuanKpr::query()->with(['konsumen', 'booking', 'unit']);

        $search = $request->input('search', '');
        $filterStatus = $request->input('status_pengajuan', '');
        $perPage = (int) $request->input('per_page', 10);

        if ($search !== '') {
            $term = '%'.$search.'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('konsumen', function ($q) use ($term) {
                    $q->where('nama_lengkap', 'like', $term)
                        ->orWhere('nik', 'like', $term);
                })->orWhere('nama_bank', 'like', $term);
            });
        }

        if ($filterStatus !== '') {
            $query->where('status_pengajuan', $filterStatus);
        }

        $pengajuans = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        return view('admin.pengajuan-kpr.index', compact('pengajuans', 'search', 'filterStatus', 'perPage'));
    }

    public function show(int $id): View
    {
        $pengajuan = PengajuanKpr::with(['konsumen', 'booking', 'unit', 'booking.pembayaran'])->findOrFail($id);

        return view('admin.pengajuan-kpr.show', compact('pengajuan'));
    }

    public function updateStatus(PengajuanKprRequest $request, int $id): RedirectResponse
    {
        try {
            $this->pengajuanKprService->updateStatus($id, $request->input('status_pengajuan'), $request->input('catatan'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('admin.pengajuan-kpr.index')->with('success', 'Status pengajuan KPR berhasil diperbarui.');
    }
}