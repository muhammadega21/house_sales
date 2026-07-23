<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusUnit;
use App\Http\Controllers\Controller;
use App\Http\Requests\PerumahanRequest;
use App\Models\Perumahan;
use App\Models\UnitRumah;
use App\Services\PerumahanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerumahanController extends Controller
{
    public function __construct(
        private readonly PerumahanService $perumahanService,
    ) {}

    public function index(Request $request): View
    {
        $perumahan = $this->perumahanService->getWithUnitCount($request);
        $provinsi = Perumahan::query()->distinct()->orderBy('provinsi')->pluck('provinsi');
        $summary = [
            'aktif' => Perumahan::aktif()->count(),
            'tersedia' => UnitRumah::where('status_unit', StatusUnit::Tersedia->value)->count(),
            'dijual' => UnitRumah::where('status_unit', StatusUnit::Dijual->value)->count(),
        ];

        return view('admin.perumahan.index', compact('perumahan', 'provinsi', 'summary'));
    }

    public function create(): View
    {
        return view('admin.perumahan.create');
    }

    public function store(PerumahanRequest $request): RedirectResponse
    {
        $this->perumahanService->create($request->validated());

        return redirect()
            ->route('admin.perumahan.index')
            ->with('success', 'Perumahan berhasil ditambahkan.');
    }

    public function show(Perumahan $perumahan): View
    {
        $perumahan->loadCount('unitRumah');
        $unitSummary = [
            'tersedia' => $perumahan->unitRumah()->where('status_unit', StatusUnit::Tersedia->value)->count(),
            'dibooking' => $perumahan->unitRumah()->where('status_unit', StatusUnit::Dibooking->value)->count(),
            'dijual' => $perumahan->unitRumah()->where('status_unit', StatusUnit::Dijual->value)->count(),
            'dibatalkan' => $perumahan->unitRumah()->where('status_unit', StatusUnit::Dibatalkan->value)->count(),
        ];
        $units = $perumahan->unitRumah()->latest()->paginate(10);

        return view('admin.perumahan.show', compact('perumahan', 'unitSummary', 'units'));
    }

    public function edit(Perumahan $perumahan): View
    {
        return view('admin.perumahan.edit', compact('perumahan'));
    }

    public function update(PerumahanRequest $request, Perumahan $perumahan): RedirectResponse
    {
        $this->perumahanService->update($request->validated(), $perumahan->id);

        return redirect()
            ->route('admin.perumahan.index')
            ->with('success', 'Perumahan berhasil diperbarui.');
    }

    public function destroy(Perumahan $perumahan): RedirectResponse
    {
        $result = $this->perumahanService->delete($perumahan->id);

        return redirect()
            ->route('admin.perumahan.index')
            ->with($result['deleted'] ? 'success' : 'warning', $result['message']);
    }
}