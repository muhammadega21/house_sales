<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisKetersediaan;
use App\Enums\StatusUnit;
use App\Models\Perumahan;
use App\Models\UnitRumah;
use App\Traits\HasFileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UnitRumahService extends BaseService
{
    use HasFileUpload;

    public function create(array $data, Model|string|null $model = null): UnitRumah
    {
        $this->ensureActivePerumahan((int) $data['id_perumahan']);
        $this->validateBusinessRules($data);

        return DB::transaction(function () use ($data): UnitRumah {
            $data = $this->storeFiles($data);
            /** @var UnitRumah $unit */
            $unit = parent::create($data, UnitRumah::class);
            Perumahan::whereKey($unit->id_perumahan)->increment('total_unit');

            return $unit;
        });
    }

    public function update(array $data, Model|string|int $model, string|int|null $id = null): UnitRumah
    {
        $id ??= $model;
        /** @var UnitRumah $unit */
        $unit = $this->findById(UnitRumah::class, $id);

        if ($unit->status_unit === StatusUnit::Dijual) {
            throw ValidationException::withMessages(['unit' => 'Unit yang sudah dijual tidak dapat diubah.']);
        }

        $this->ensureActivePerumahan((int) $data['id_perumahan']);
        $this->validateBusinessRules($data, $unit->id);

        return DB::transaction(function () use ($data, $unit): UnitRumah {
            $data = $this->storeFiles($data, $unit);
            $oldPerumahanId = $unit->id_perumahan;
            $unit->update($data);

            if ($oldPerumahanId !== $unit->id_perumahan) {
                Perumahan::whereKey($oldPerumahanId)->decrement('total_unit');
                Perumahan::whereKey($unit->id_perumahan)->increment('total_unit');
            }

            return $unit;
        });
    }

    /** @return array{deleted: bool, message: string} */
    public function delete(Model|string|int $model, string|int|null $id = null): array
    {
        $id ??= $model;
        /** @var UnitRumah $unit */
        $unit = $this->findById(UnitRumah::class, $id);

        if ($unit->status_unit !== StatusUnit::Tersedia) {
            return ['deleted' => false, 'message' => 'Hanya unit berstatus tersedia yang dapat dihapus.'];
        }

        DB::transaction(function () use ($unit): void {
            $this->deleteFile($unit->foto_unit);
            $this->deleteFile($unit->denah_unit);
            Perumahan::whereKey($unit->id_perumahan)->where('total_unit', '>', 0)->decrement('total_unit');
            $unit->delete();
        });

        return ['deleted' => true, 'message' => 'Unit rumah berhasil dihapus.'];
    }

    public function getAvailable()
    {
        return UnitRumah::query()->tersedia()->with('perumahan')->whereHas('perumahan', fn($query) => $query->aktif())->get();
    }

    public function getWithFilters(Request $request): LengthAwarePaginator
    {
        $query = UnitRumah::query()->with('perumahan');
        if ($request->filled('search')) {
            $search = '%' . trim((string) $request->input('search')) . '%';
            $query->where(fn($q) => $q->where('kode_unit', 'like', $search)->orWhere('tipe_rumah', 'like', $search));
        }
        foreach (['id_perumahan', 'kategori', 'status_unit', 'jenis_ketersediaan'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }
        return $query->latest()->paginate(10)->withQueryString();
    }

    private function ensureActivePerumahan(int $perumahanId): void
    {
        if (!Perumahan::aktif()->whereKey($perumahanId)->exists()) {
            throw ValidationException::withMessages(['id_perumahan' => 'Perumahan harus berstatus aktif.']);
        }
    }

    private function validateBusinessRules(array $data, ?int $unitId = null): void
    {
        if ((float) $data['luas_bangunan'] > (float) $data['luas_tanah']) {
            throw ValidationException::withMessages(['luas_bangunan' => 'Luas bangunan tidak boleh lebih dari luas tanah.']);
        }
        if ((float) $data['harga_jual'] <= 0) {
            throw ValidationException::withMessages(['harga_jual' => 'Harga jual harus lebih dari 0.']);
        }
        if ($data['jenis_ketersediaan'] === JenisKetersediaan::Indent->value && empty($data['tanggal_selesai_bangun'])) {
            throw ValidationException::withMessages(['tanggal_selesai_bangun' => 'Tanggal selesai bangun wajib diisi untuk unit indent.']);
        }
        $duplicate = UnitRumah::where('id_perumahan', $data['id_perumahan'])->where('kode_unit', $data['kode_unit'])->when($unitId, fn($q) => $q->whereKeyNot($unitId))->exists();
        if ($duplicate) {
            throw ValidationException::withMessages(['kode_unit' => 'Kode unit sudah digunakan pada perumahan ini.']);
        }
    }

    private function storeFiles(array $data, ?UnitRumah $unit = null): array
    {
        foreach (['foto_unit' => 'foto-unit', 'denah_unit' => 'denah-unit'] as $field => $folder) {
            // If new file uploaded, replace and delete old
            if (($data[$field] ?? null) instanceof UploadedFile) {
                $data[$field] = $this->uploadFile($data[$field], $folder, $unit?->{$field});

                // If remove flag is present, delete old file and set null
            } elseif (!empty($data['remove_' . $field])) {
                $this->deleteFile($unit?->{$field});
                $data[$field] = null;

                // Otherwise leave field untouched (unset so update doesn't overwrite)
            } else {
                unset($data[$field]);
            }
        }
        return $data;
    }
}
