<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KonsumenRequest;
use App\Models\Konsumen;
use App\Models\User;
use App\Services\KonsumenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class KonsumenController extends Controller
{
    public function __construct(private readonly KonsumenService $konsumenService) {}

    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $filterMarketing = $request->input('id_marketing', '');
        $perPage = (int) $request->input('per_page', 10);

        $query = Konsumen::query()
            ->with('marketing')
            ->withCount('bookings as total_bookings');

        if ($search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama_lengkap', 'like', $term)
                    ->orWhere('nik', 'like', $term)
                    ->orWhere('no_hp', 'like', $term);
            });
        }

        if ($filterMarketing !== '') {
            $query->where('id_marketing', $filterMarketing);
        }

        $konsumens = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
        $marketingOptions = User::marketing()->aktif()->orderBy('nama_lengkap')->get()
            ->mapWithKeys(fn($m) => [$m->id => $m->nama_lengkap]);

        return view('admin.konsumen.index', array_merge(
            compact('konsumens', 'search', 'filterMarketing', 'marketingOptions'),
            ['perPage' => $perPage, 'hasFilters' => $search !== '' || $filterMarketing !== '']
        ));
    }

    public function show(int $id): View
    {
        $konsumen = $this->konsumenService->getDetail($id);

        if (!$konsumen) {
            abort(404, 'Konsumen tidak ditemukan.');
        }

        $this->authorize('view', $konsumen);

        return view('admin.konsumen.show', compact('konsumen'));
    }

    public function edit(int $id): View
    {
        $konsumen = $this->konsumenService->findById(Konsumen::class, $id);

        $this->authorize('update', $konsumen);

        return view('admin.konsumen.edit', compact('konsumen'));
    }

    public function update(KonsumenRequest $request, int $id): RedirectResponse
    {
        $konsumen = Konsumen::findOrFail($id);

        $this->authorize('update', $konsumen);

        try {
            $this->konsumenService->update($request->validated(), $id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.konsumen.index')->with('success', 'Konsumen berhasil diperbarui.');
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

        return redirect()->route('admin.konsumen.index')->with('success', 'Konsumen berhasil dihapus.');
    }
}