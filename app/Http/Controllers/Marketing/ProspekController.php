<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProspekRequest;
use App\Models\Prospek;
use App\Services\ProspekService;
use App\Traits\HasDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProspekController extends Controller
{
    use HasDataTable;

    protected array $searchable = ['nama_prospek', 'no_hp'];

    protected array $filterable = ['status_prospek', 'sumber_prospek'];

    protected array $sortable = ['nama_prospek', 'no_hp', 'status_prospek', 'tanggal_prospek', 'created_at'];

    protected string $defaultSortBy = 'created_at';

    protected string $defaultSortDir = 'desc';

    protected string $defaultPerPage = '10';

    public function __construct(private readonly ProspekService $prospekService) {}

    public function index(Request $request): View
    {
        $idMarketing = auth()->id();

        $query = Prospek::query()->where('id_marketing', $idMarketing);
        $prospeks = $this->buildDataTableQuery($query, $request);
        $stats = $this->prospekService->getStats($idMarketing);
        $meta = $this->dataTableMeta($request);

        return view('marketing.prospek.index', array_merge(
            compact('prospeks', 'stats'),
            $meta
        ));
    }

    public function create(): View
    {
        return view('marketing.prospek.create');
    }

    public function store(ProspekRequest $request): RedirectResponse
    {
        $this->prospekService->create($request->validated());

        if ($request->has('_save_and_new')) {
            return redirect()->route('marketing.prospek.create')->with('success', 'Prospek berhasil ditambahkan.');
        }

        return redirect()->route('marketing.prospek.index')->with('success', 'Prospek berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $prospek = $this->prospekService->findById(Prospek::class, $id);

        if ($prospek->id_marketing !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah prospek ini.');
        }

        return view('marketing.prospek.edit', compact('prospek'));
    }

    public function update(ProspekRequest $request, int $id): RedirectResponse
    {
        $this->prospekService->update($request->validated(), Prospek::class, $id);

        return redirect()->route('marketing.prospek.index')->with('success', 'Prospek berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->prospekService->delete(Prospek::class, $id);

        return redirect()->route('marketing.prospek.index')->with('success', 'Prospek berhasil dihapus.');
    }

    public function convert(int $id): View
    {
        $prospek = $this->prospekService->findById(Prospek::class, $id);

        if ($prospek->id_marketing !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        if ($prospek->status_prospek->value !== 'berminat') {
            abort(403, 'Prospek harus berstatus berminat untuk dikonversi menjadi konsumen.');
        }

        return view('marketing.prospek.convert', [
            'prospek' => $prospek,
            'routeName' => 'marketing.prospek.store-convert',
        ]);
    }

    public function storeConvert(Request $request, int $id): RedirectResponse
    {
        $prospek = $this->prospekService->findById(Prospek::class, $id);

        if ($prospek->id_marketing !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        if ($prospek->status_prospek->value !== 'berminat') {
            return redirect()->back()->withErrors(['status_prospek' => 'Prospek harus berstatus berminat untuk dikonversi.']);
        }

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

        return redirect()->route('marketing.konsumen.show', $konsumen->id)->with('success', 'Prospek berhasil dikonversi menjadi konsumen.');
    }
}
