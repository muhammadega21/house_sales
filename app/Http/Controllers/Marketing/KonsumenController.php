<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\KonsumenRequest;
use App\Models\Konsumen;
use App\Services\KonsumenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class KonsumenController extends Controller
{
    public function __construct(private readonly KonsumenService $konsumenService) {}

    public function index(Request $request): View
    {
        $idMarketing = auth()->id();
        $search = $request->input('search', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = Konsumen::query()
            ->where('id_marketing', $idMarketing)
            ->withCount('bookings as total_bookings');

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama_lengkap', 'like', $term)
                    ->orWhere('nik', 'like', $term)
                    ->orWhere('no_hp', 'like', $term);
            });
        }

        $konsumens = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Konsumen::where('id_marketing', $idMarketing)->count(),
            'with_booking' => Konsumen::where('id_marketing', $idMarketing)
                ->whereHas('bookings', fn($q) => $q->where('status_pembayaran_fee', '!=', 'refund'))
                ->count(),
            'with_kpr' => Konsumen::where('id_marketing', $idMarketing)
                ->whereHas('pengajuanKpr', fn($q) => $q->whereIn('status_pengajuan', ['diajukan', 'verifikasi_bank', 'disetujui']))
                ->count(),
        ];

        return view('marketing.konsumen.index', array_merge(
            compact('konsumens', 'search', 'stats'),
            ['perPage' => $perPage, 'hasFilters' => $search !== '']
        ));
    }

    public function create(): View
    {
        return view('marketing.konsumen.create');
    }

    public function store(KonsumenRequest $request): RedirectResponse
    {
        try {
            $this->konsumenService->create($request->validated());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        if ($request->has('_save_and_new')) {
            return redirect()->route('marketing.konsumen.create')->with('success', 'Konsumen berhasil ditambahkan.');
        }

        return redirect()->route('marketing.konsumen.index')->with('success', 'Konsumen berhasil ditambahkan.');
    }

    public function show(int $id): View
    {
        $konsumen = $this->konsumenService->getDetail($id);

        if (!$konsumen) {
            abort(404, 'Konsumen tidak ditemukan.');
        }

        $this->authorize('view', $konsumen);

        return view('marketing.konsumen.show', compact('konsumen'));
    }

    public function edit(int $id): View
    {
        $konsumen = $this->konsumenService->findById(Konsumen::class, $id);

        $this->authorize('update', $konsumen);

        return view('marketing.konsumen.edit', compact('konsumen'));
    }

    public function update(KonsumenRequest $request, int $id): RedirectResponse
    {
        $konsumen = Konsumen::findOrFail($id);

        $this->authorize('update', $konsumen);

        try {
            $this->konsumenService->update($request->validated(), Konsumen::class, $id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('marketing.konsumen.index')->with('success', 'Konsumen berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $konsumen = Konsumen::findOrFail($id);

        $this->authorize('delete', $konsumen);

        try {
            $this->konsumenService->delete($id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('marketing.konsumen.index')->with('success', 'Konsumen berhasil dihapus.');
    }
}