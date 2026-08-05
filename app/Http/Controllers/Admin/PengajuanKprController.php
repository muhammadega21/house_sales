<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanKprRequest;
use App\Models\PengajuanKpr;
use App\Models\StatusHistory;
use App\Services\DokumenService;
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
            $term = '%' . $search . '%';
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
        $pengajuan = PengajuanKpr::with([
            'konsumen',
            'booking.unit.perumahan',
            'booking.marketing',
            'booking.pembayaran' => fn($q) => $q->where('status_verifikasi', 'diverifikasi'),
        ])->findOrFail($id);

        $dokumenChecklist = app(DokumenService::class)->getChecklist($pengajuan->id_konsumen);
        $statusHistory = StatusHistory::where('id_booking', $pengajuan->id_booking)
            ->orderBy('created_at', 'asc')
            ->get();
        $allowedStatus = $this->pengajuanKprService->getAllowedStatusTransitions($pengajuan->status_pengajuan);

        $otherPengajuans = PengajuanKpr::where('id_konsumen', $pengajuan->id_konsumen)
            ->where('id', '!=', $pengajuan->id)
            ->orderByDesc('created_at')
            ->get();

        $rejectionCount = PengajuanKpr::where('id_konsumen', $pengajuan->id_konsumen)
            ->where('status_pengajuan', 'ditolak')
            ->count();

        return view('admin.pengajuan-kpr.show', compact('pengajuan', 'dokumenChecklist', 'statusHistory', 'otherPengajuans', 'rejectionCount', 'allowedStatus'));
    }

    public function updateStatus(int $id): View
    {
        $pengajuan = PengajuanKpr::with(['konsumen', 'booking', 'unit', 'pengajuanKprHistory'])->findOrFail($id);
        $allowedStatus = $this->pengajuanKprService->getAllowedStatusTransitions($pengajuan->status_pengajuan);
        if (empty($allowedStatus)) {
            return redirect()
                ->route('admin.pengajuan-kpr.show', $pengajuan->id)
                ->with('warning', 'Status pengajuan ini sudah final dan tidak dapat diubah lagi.');
        }

        $statusLabels = [
            'draft' => 'Draft',
            'diajukan' => 'Diajukan',
            'verifikasi_bank' => 'Verifikasi Bank',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'akad' => 'Akad',
            'batal' => 'Batal',
        ];

        return view('admin.pengajuan-kpr.update-status', compact('pengajuan', 'allowedStatus', 'statusLabels'));
    }

    public function prosesUpdateStatus(PengajuanKprRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status_pengajuan' => 'required|string|in:draft,diajukan,verifikasi_bank,disetujui,ditolak,akad,batal',
            'catatan' => 'required|string|max:1000',
        ]);

        try {
            $this->pengajuanKprService->updateStatus(
                $id,
                $validated['status_pengajuan'],
                $validated['catatan'],
                auth()->id() ?? 0,
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('admin.pengajuan-kpr.index')->with('success', 'Status pengajuan KPR berhasil diperbarui.');
    }

    public function stats(Request $request): View
    {
        $statusCounts = PengajuanKpr::query()
            ->selectRaw('status_pengajuan, count(*) as total')
            ->groupBy('status_pengajuan')
            ->pluck('total', 'status_pengajuan');

        $total = PengajuanKpr::query()->count();
        $approved = $statusCounts->get('disetujui', 0);
        $rejected = $statusCounts->get('ditolak', 0);
        $approvalRate = $total > 0 ? round(($approved / $total) * 100, 2) : 0;

        $perBank = PengajuanKpr::query()
            ->selectRaw('nama_bank, count(*) as total')
            ->groupBy('nama_bank')
            ->orderByDesc('total')
            ->get();

        return view('admin.pengajuan-kpr.stats', compact('statusCounts', 'total', 'approved', 'rejected', 'approvalRate', 'perBank'));
    }
}
