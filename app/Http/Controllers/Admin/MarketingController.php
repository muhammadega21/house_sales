<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\MarketingRequest;
use App\Http\Requests\MarketingTargetRequest;
use App\Models\MarketingTarget;
use App\Models\User;
use App\Services\MarketingService;
use App\Traits\HasDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    use HasDataTable;

    protected array $searchable     = ['nama_lengkap', 'username', 'email', 'no_hp'];
    protected array $filterable     = ['status'];
    protected array $sortable       = ['nama_lengkap', 'status', 'created_at'];
    protected string $defaultSortBy = 'nama_lengkap';
    protected string $defaultSortDir = 'asc';

    public function __construct(private readonly MarketingService $marketingService) {}

    public function index(Request $request): View
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $marketings = $this->buildDataTableQuery(User::marketing(), $request);

        $marketings->getCollection()->transform(function (User $marketing) use ($bulan, $tahun) {
            $marketing->kinerja = $this->marketingService->getKinerja($marketing->id, $bulan, $tahun);

            return $marketing;
        });

        $active            = User::marketing()->aktif()->count();
        $closing           = $marketings->getCollection()->sum(fn (User $m) => $m->kinerja['closing']);
        $averageConversion = $marketings->getCollection()->avg(fn (User $m) => $m->kinerja['conversion_rate']) ?? 0;
        $totalAll          = User::marketing()->count();

        return view('admin.marketing.index', array_merge(
            compact('marketings', 'bulan', 'tahun', 'active', 'closing', 'averageConversion', 'totalAll'),
            $this->dataTableMeta($request, [
                $bulan !== (int) now()->month,
                $tahun !== (int) now()->year,
            ]),
        ));
    }

    public function create(): View
    {
        return view('admin.marketing.create');
    }

    public function store(MarketingRequest $request): RedirectResponse
    {
        $this->marketingService->createMarketing($request->validated());

        return redirect()->route('admin.marketing.index')->with('success', 'Marketing berhasil ditambahkan.');
    }

    public function show(Request $request, User $marketing): View
    {
        abort_unless($marketing->role === Role::Marketing, 404);

        $bulan   = (int) $request->input('bulan', now()->month);
        $tahun   = (int) $request->input('tahun', now()->year);
        $kinerja = $this->marketingService->getKinerja($marketing->id, $bulan, $tahun);

        $chart = collect(range(1, 12))->map(function (int $month) use ($marketing, $tahun) {
            $value = $this->marketingService->getKinerja($marketing->id, $month, $tahun);

            return [
                'prospek'    => $value['prospek'],
                'closing'    => $value['closing'],
                'pencapaian' => $value['pencapaian_target'],
            ];
        });

        $target = MarketingTarget::where([
            'id_marketing'  => $marketing->id,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
        ])->first();

        return view('admin.marketing.show', compact('marketing', 'bulan', 'tahun', 'kinerja', 'chart', 'target'));
    }

    public function edit(User $marketing): View
    {
        abort_unless($marketing->role === Role::Marketing, 404);

        return view('admin.marketing.edit', compact('marketing'));
    }

    public function update(MarketingRequest $request, User $marketing): RedirectResponse
    {
        abort_unless($marketing->role === Role::Marketing, 404);
        $this->marketingService->updateMarketing($marketing, $request->validated());

        return redirect()->route('admin.marketing.index')->with('success', 'Data marketing diperbarui.');
    }

    public function destroy(User $marketing): RedirectResponse
    {
        abort_unless($marketing->role === Role::Marketing, 404);
        $marketing->update(['status' => 'non_aktif']);

        return redirect()->route('admin.marketing.index')->with('success', 'Marketing dinonaktifkan. Data historis tetap tersimpan.');
    }

    public function setTarget(User $marketing): View
    {
        abort_unless($marketing->role === Role::Marketing, 404);

        $bulan  = (int) request('bulan', now()->month);
        $tahun  = (int) request('tahun', now()->year);
        $target = MarketingTarget::where([
            'id_marketing'  => $marketing->id,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
        ])->first();

        return view('admin.marketing.set-target', compact('marketing', 'bulan', 'tahun', 'target'));
    }

    public function storeTarget(MarketingTargetRequest $request, User $marketing): RedirectResponse
    {
        abort_unless($marketing->role === Role::Marketing, 404);

        $data                 = $request->validated();
        $data['id_marketing'] = $marketing->id;
        $this->marketingService->setTarget($data);

        return redirect()->route('admin.marketing.show', $marketing)->with('success', 'Target marketing berhasil disimpan.');
    }
}
