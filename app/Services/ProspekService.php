<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusProspek;
use App\Models\Konsumen;
use App\Models\Prospek;
use App\Services\KonsumenService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProspekService extends BaseService
{
    private KonsumenService $konsumenService;

    public function __construct(KonsumenService $konsumenService)
    {
        $this->konsumenService = $konsumenService;
    }

    public function create(array $data, Model|string|null $model = null): Prospek
    {
        $user = auth()->user();

        if ($user?->role?->value === 'marketing') {
            $data['id_marketing'] = $user->id;
        } elseif (empty($data['id_marketing'])) {
            $data['id_marketing'] = $user?->id ?? 0;
        }

        $data['status_prospek'] = $data['status_prospek'] ?? StatusProspek::Baru->value;

        /** @var Prospek $prospek */
        $prospek = parent::create($data, Prospek::class);

        return $prospek;
    }

    public function update(array $data, Model|string|int $model, string|int|null $id = null): Prospek
    {
        $id ??= $model;

        /** @var Prospek $prospek */
        $prospek = $this->findById(Prospek::class, $id);

        if (auth()->user()?->role?->value === 'marketing' && auth()->id() !== $prospek->id_marketing) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah prospek ini.');
        }

        $prospek->update($data);

        return $prospek;
    }

    public function delete(Model|string|int $model, string|int|null $id = null): bool
    {
        $id ??= $model;

        /** @var Prospek $prospek */
        $prospek = $this->findById(Prospek::class, $id);

        if (auth()->user()?->role?->value === 'marketing' && auth()->id() !== $prospek->id_marketing) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus prospek ini.');
        }

        if ($prospek->status_prospek === StatusProspek::JadiKonsumen->value) {
            throw ValidationException::withMessages([
                'status_prospek' => 'Prospek yang sudah menjadi konsumen tidak dapat dihapus.',
            ]);
        }

        return (bool) $prospek->delete();
    }

    public function getForMarketing(int $idMarketing): Collection
    {
        return Prospek::query()->where('id_marketing', $idMarketing)->orderByDesc('created_at')->get();
    }

    public function getStats(int $idMarketing): array
    {
        $statuses = array_column(StatusProspek::cases(), 'value');
        $stats = [];

        foreach ($statuses as $status) {
            $stats[$status] = Prospek::query()
                ->where('id_marketing', $idMarketing)
                ->where('status_prospek', $status)
                ->count();
        }

        $stats['total'] = Prospek::query()->where('id_marketing', $idMarketing)->count();

        return $stats;
    }

    public function getPipeline(): array
    {
        $pipeline = [];

        foreach (StatusProspek::cases() as $status) {
            $pipeline[$status->value] = Prospek::query()->where('status_prospek', $status->value)->count();
        }

        return $pipeline;
    }

    public function getAllForAdmin(): Collection
    {
        return Prospek::query()->with('marketing')->orderByDesc('created_at')->get();
    }

    public function getAllStats(): array
    {
        $statuses = array_column(StatusProspek::cases(), 'value');
        $stats = [];

        foreach ($statuses as $status) {
            $stats[$status] = Prospek::query()->where('status_prospek', $status)->count();
        }

        $stats['total'] = Prospek::query()->count();

        return $stats;
    }

    public function getStatsPerMarketing(): Collection
    {
        $marketings = \App\Models\User::marketing()->aktif()->withCount(['prospek as total' => function ($query) {
            $query->select(DB::raw('count(*)'));
        }])->get()->map(function ($m) {
            $stats = $this->getStats($m->id);
            return [
                'id' => $m->id,
                'nama_lengkap' => $m->nama_lengkap,
                'username' => $m->username,
                'stats' => $stats,
            ];
        });

        return $marketings;
    }

    public function convertToKonsumen(int $idProspek, array $dataKonsumen): Konsumen
    {
        return DB::transaction(function () use ($idProspek, $dataKonsumen): Konsumen {
            /** @var Prospek $prospek */
            $prospek = $this->findById(Prospek::class, $idProspek);

            if ($prospek->status_prospek === StatusProspek::JadiKonsumen->value) {
                throw ValidationException::withMessages([
                    'status_prospek' => 'Prospek ini sudah pernah dikonversi menjadi konsumen.',
                ]);
            }

            $konsumen = $this->konsumenService->create($dataKonsumen);

            $prospek->update(['status_prospek' => StatusProspek::JadiKonsumen->value]);

            return $konsumen->fresh();
        });
    }
}
