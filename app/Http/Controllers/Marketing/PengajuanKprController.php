<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanKprRequest;
use App\Models\Booking;
use App\Models\PengajuanKpr;
use App\Services\PengajuanKprService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class PengajuanKprController extends Controller
{
    public function __construct(private readonly PengajuanKprService $pengajuanKprService) {}

    public function index(Request $request): View
    {
        $idMarketing = Auth::id();

        $query = PengajuanKpr::query()
            ->whereHas('booking', function ($q) use ($idMarketing) {
                $q->where('id_marketing', $idMarketing);
            })
            ->with(['konsumen', 'booking', 'unit']);

        $search = $request->input('search', '');

        if ($search !== '') {
            $term = '%'.$search.'%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('konsumen', function ($q) use ($term) {
                    $q->where('nama_lengkap', 'like', $term)
                        ->orWhere('nik', 'like', $term);
                })->orWhere('nama_bank', 'like', $term);
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $pengajuans = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        return view('marketing.pengajuan-kpr.index', compact('pengajuans', 'search', 'perPage'));
    }

    public function store(PengajuanKprRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tanggal_pengajuan'] = $validated['tanggal_pengajuan'] ?? now()->toDateString();
        $validated['status_pengajuan'] = 'draft';

        try {
            $pengajuan = $this->pengajuanKprService->createForBooking($validated, $validated['id_booking']);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('marketing.pengajuan-kpr.index')->with('success', 'Pengajuan KPR berhasil disimpan sebagai draft.');
    }
}