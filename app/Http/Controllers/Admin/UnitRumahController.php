<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusUnit;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnitRumahRequest;
use App\Models\Perumahan;
use App\Models\UnitRumah;
use App\Services\UnitRumahService;
use App\Traits\HasDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class UnitRumahController extends Controller
{
    use HasDataTable;

    protected array $searchable     = ['kode_unit', 'tipe_rumah', 'perumahan.nama_perumahan'];
    protected array $filterable     = ['id_perumahan', 'kategori', 'status_unit', 'jenis_ketersediaan'];
    protected array $sortable       = ['kode_unit', 'tipe_rumah', 'kategori', 'harga_jual', 'luas_tanah', 'luas_bangunan', 'status_unit', 'created_at'];
    protected string $defaultSortBy = 'created_at';

    public function __construct(private readonly UnitRumahService $unitRumahService) {}

    public function index(Request $request): View
    {
        $query = UnitRumah::query()->with('perumahan');
        $units = $this->buildDataTableQuery($query, $request);

        $perumahan = Perumahan::query()->orderBy('nama_perumahan')->pluck('nama_perumahan', 'id');
        $summary   = [
            'tersedia'   => UnitRumah::where('status_unit', StatusUnit::Tersedia->value)->count(),
            'dibooking'  => UnitRumah::where('status_unit', StatusUnit::Dibooking->value)->count(),
            'dijual'     => UnitRumah::where('status_unit', StatusUnit::Dijual->value)->count(),
            'dibatalkan' => UnitRumah::where('status_unit', StatusUnit::Dibatalkan->value)->count(),
        ];
        $totalAll = UnitRumah::count();

        return view('admin.unit-rumah.index', array_merge(
            compact('units', 'perumahan', 'summary', 'totalAll'),
            $this->dataTableMeta($request),
        ));
    }

    public function create(): View
    {
        return view('admin.unit-rumah.create', [
            'perumahan' => Perumahan::aktif()->orderBy('nama_perumahan')->pluck('nama_perumahan', 'id'),
        ]);
    }

    public function store(UnitRumahRequest $request): RedirectResponse
    {
        $this->unitRumahService->create($request->validated());

        return redirect()->route('admin.unit-rumah.index')->with('success', 'Unit rumah berhasil ditambahkan.');
    }

    public function show(UnitRumah $unitRumah): View
    {
        $unitRumah->load('perumahan');
        $bookings = class_exists(\App\Models\Booking::class)
            ? $unitRumah->booking()->with('konsumen')->latest()->paginate(10)
            : new LengthAwarePaginator([], 0, 10);

        return view('admin.unit-rumah.show', compact('unitRumah', 'bookings'));
    }

    public function edit(UnitRumah $unitRumah): View
    {
        return view('admin.unit-rumah.edit', [
            'unitRumah'  => $unitRumah,
            'perumahan'  => Perumahan::aktif()->orderBy('nama_perumahan')->pluck('nama_perumahan', 'id'),
        ]);
    }

    public function update(UnitRumahRequest $request, UnitRumah $unitRumah): RedirectResponse
    {
        $this->unitRumahService->update($request->validated(), $unitRumah->id);

        return redirect()->route('admin.unit-rumah.index')->with('success', 'Unit rumah berhasil diperbarui.');
    }

    public function destroy(UnitRumah $unitRumah): RedirectResponse
    {
        $result = $this->unitRumahService->delete($unitRumah->id);

        return redirect()->route('admin.unit-rumah.index')->with($result['deleted'] ? 'success' : 'warning', $result['message']);
    }
}
