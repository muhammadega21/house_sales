<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Perumahan;
use App\Traits\HasFileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class PerumahanService extends BaseService
{
    use HasFileUpload;

    public function create(array $data, Model|string|null $model = null): Perumahan
    {
        if (($data['foto_kawasan'] ?? null) instanceof UploadedFile) {
            $data['foto_kawasan'] = $this->uploadFile($data['foto_kawasan'], 'foto-kawasan');
        } else {
            unset($data['foto_kawasan']);
        }

        /** @var Perumahan $perumahan */
        $perumahan = parent::create($data, Perumahan::class);

        return $perumahan;
    }

    public function update(array $data, Model|string|int $model, string|int|null $id = null): Perumahan
    {
        $id ??= $model;

        /** @var Perumahan $perumahan */
        $perumahan = $this->findById(Perumahan::class, $id);

        if (($data['foto_kawasan'] ?? null) instanceof UploadedFile) {
            $data['foto_kawasan'] = $this->uploadFile(
                $data['foto_kawasan'],
                'foto-kawasan',
                $perumahan->foto_kawasan,
            );
        } elseif (!empty($data['remove_foto_kawasan'])) {
            $this->deleteFile($perumahan->foto_kawasan);
            $data['foto_kawasan'] = null;
        } else {
            unset($data['foto_kawasan']);
        }

        $perumahan->update($data);

        return $perumahan;
    }

    /**
     * @return array{deleted: bool, message: string}
     */
    public function delete(Model|string|int $model, string|int|null $id = null): array
    {
        $id ??= $model;

        /** @var Perumahan $perumahan */
        $perumahan = $this->findById(Perumahan::class, $id);

        if ($perumahan->unitRumah()->exists()) {
            $perumahan->update(['status' => 'non_aktif']);

            return [
                'deleted' => false,
                'message' => 'Perumahan masih memiliki unit rumah. Status diubah menjadi Non-Aktif.',
            ];
        }

        $this->deleteFile($perumahan->foto_kawasan);
        $perumahan->delete();

        return [
            'deleted' => true,
            'message' => 'Perumahan berhasil dihapus.',
        ];
    }

    public function getWithUnitCount(Request $request): LengthAwarePaginator
    {
        $query = Perumahan::query()->withCount([
            'unitRumah',
            'unitRumah as unit_tersedia_count' => fn($unitQuery) => $unitQuery->where('status_unit', 'tersedia'),
            'unitRumah as unit_dibooking_count' => fn($unitQuery) => $unitQuery->where('status_unit', 'dibooking'),
            'unitRumah as unit_dijual_count' => fn($unitQuery) => $unitQuery->where('status_unit', 'dijual'),
            'unitRumah as unit_dibatalkan_count' => fn($unitQuery) => $unitQuery->where('status_unit', 'dibatalkan'),
        ]);

        if ($request->filled('search')) {
            $search = '%' . trim((string) $request->input('search')) . '%';
            $query->where(fn($builder) => $builder
                ->where('nama_perumahan', 'like', $search)
                ->orWhere('kota', 'like', $search));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('provinsi')) {
            $query->where('provinsi', (string) $request->input('provinsi'));
        }

        return $query->latest()->paginate(10)->withQueryString();
    }
}
