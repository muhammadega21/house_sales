<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProspekRequest;
use App\Models\Prospek;
use App\Enums\StatusProspek;
use App\Services\ProspekService;
use App\Traits\HasDataTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProspekController extends Controller
{
    use HasDataTable;

    protected array $searchable = ['nama_prospek', 'no_hp', 'marketing.nama_lengkap'];

    protected array $filterable = ['status_prospek', 'sumber_prospek', 'id_marketing'];

    protected array $sortable = ['nama_prospek', 'no_hp', 'status_prospek', 'tanggal_prospek', 'created_at'];

    protected string $defaultSortBy = 'created_at';

    protected string $defaultSortDir = 'desc';

    protected string $defaultPerPage = '10';

    public function __construct(private readonly ProspekService $prospekService) {}

    public function index(Request $request): View
    {
        $query = Prospek::query()->with('marketing');

        $query->when(!$request->has('show_converted'), function ($q) {
            $q->where('status_prospek', '!=', 'jadi_konsumen');
        });

        $prospeks = $this->buildDataTableQuery($query, $request);
        $stats = $this->prospekService->getAllStats();
        $meta = $this->dataTableMeta($request);

        return view('admin.prospek.index', array_merge(
            compact('prospeks', 'stats'),
            $meta
        ));
    }

    public function show(int $id): View
    {
        $prospek = $this->prospekService->findById(Prospek::class, $id, ['marketing']);

        return view('admin.prospek.show', compact('prospek'));
    }

    public function create(): View
    {
        return view('admin.prospek.create');
    }

    public function store(ProspekRequest $request): RedirectResponse
    {
        $this->prospekService->create($request->validated());

        return redirect()->route('admin.prospek.index')->with('success', 'Prospek berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $prospek = $this->prospekService->findById(Prospek::class, $id);

        if ($prospek->status_prospek === StatusProspek::JadiKonsumen->value) {
            abort(403, 'Prospek ini sudah dikonversi menjadi konsumen dan tidak dapat diedit.');
        }

        $this->authorize('update', $prospek);

        return view('admin.prospek.edit', compact('prospek'));
    }

    public function update(ProspekRequest $request, int $id): RedirectResponse
    {
        $prospek = Prospek::findOrFail($id);

        $this->authorize('update', $prospek);

        $this->prospekService->update($request->validated(), Prospek::class, $id);

        return redirect()->route('admin.prospek.index')->with('success', 'Prospek berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $prospek = Prospek::findOrFail($id);

        $this->authorize('delete', $prospek);

        $this->prospekService->delete(Prospek::class, $id);

        return redirect()->route('admin.prospek.index')->with('success', 'Prospek berhasil dihapus.');
    }

    public function convert(int $id): View
    {
        $prospek = $this->prospekService->findById(Prospek::class, $id, ['marketing']);

        $this->authorize('convert', $prospek);

        return view('marketing.prospek.convert', [
            'prospek' => $prospek,
            'routeName' => 'admin.prospek.store-convert',
        ]);
    }

    public function storeConvert(Request $request, int $id): RedirectResponse
    {
        $prospek = $this->prospekService->findById(Prospek::class, $id);

        $this->authorize('convert', $prospek);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'string', 'digits:16'],
            'alamat_lengkap' => ['required', 'string', 'max:500'],
            'no_kk' => ['nullable', 'string', 'max:16'],
            'no_hp' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:50'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'status_pernikahan' => ['nullable', 'in:belum_menikah,menikah,cerai_hidup,cerai_mati'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'nama_perusahaan' => ['nullable', 'string', 'max:100'],
            'penghasilan_bulanan' => ['nullable', 'numeric', 'min:0'],
            'npwp' => ['nullable', 'string', 'max:15'],
            'foto_ktp' => ['nullable', 'image', 'max:5120'],
            'foto_kk' => ['nullable', 'image', 'max:5120'],
        ]);

        $dataKonsumen = $validated;

        $konsumen = $this->prospekService->convertToKonsumen($id, $dataKonsumen);

        return redirect()->route('admin.konsumen.show', $konsumen->id)->with('success', 'Prospek berhasil dikonversi menjadi konsumen.');
    }

    public function stats(Request $request): JsonResponse
    {
        $stats = $this->prospekService->getAllStats();
        $pipeline = $this->prospekService->getPipeline();
        $perMarketing = $this->prospekService->getStatsPerMarketing();

        return response()->json([
            'stats' => $stats,
            'pipeline' => $pipeline,
            'marketing' => $perMarketing,
        ]);
    }
}
