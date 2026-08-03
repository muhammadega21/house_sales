<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\DokumenKpr;
use App\Models\PengajuanKpr;
use App\Models\Prospek;
use App\Traits\HasFileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KonsumenService extends BaseService
{
    use HasFileUpload;

    public function create(array $data, Model|string|null $model = null): Konsumen
    {
        if (Konsumen::query()->where('nik', $data['nik'])->exists()) {
            throw ValidationException::withMessages([
                'nik' => 'NIK sudah terdaftar pada konsumen lain.',
            ]);
        }

        if (Auth::check() && Auth::user()->role?->value === 'marketing') {
            $data['id_marketing'] = Auth::id();
        }

        $data = $this->handleFileUploads($data);

        return DB::transaction(function () use ($data): Konsumen {
            $konsumen = parent::create($data, Konsumen::class);
            return $konsumen;
        });
    }

    public function update(array $data, Model|string $model, string|int $id): Konsumen
    {
        if (is_string($model)) {
            $konsumen = $this->findById($model, $id);
        } else {
            $konsumen = $model;
        }

        if (Auth::check() && Auth::user()->role?->value === 'marketing' && Auth::id() !== $konsumen->id_marketing) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah konsumen ini.');
        }

        $nik = $data['nik'] ?? $konsumen->nik;
        if ($konsumen->nik !== $nik && Konsumen::query()->where('nik', $nik)->where('id', '!=', $id)->exists()) {
            throw ValidationException::withMessages([
                'nik' => 'NIK sudah terdaftar pada konsumen lain.',
            ]);
        }

        $data = $this->handleFileUploads($data, $konsumen);

        $konsumen->update($data);

        return $konsumen->fresh();
    }

    public function delete(Model|string|int $model, string|int|null $id = null): bool|array
    {
        $konsumen = $this->findById($model, $id, ['bookings']);

        $this->deleteFile($konsumen->foto_ktp);
        $this->deleteFile($konsumen->foto_kk);

        return (bool) $konsumen->delete();
    }

    public function getForMarketing(int $idMarketing): Collection
    {
        return Konsumen::query()
            ->where('id_marketing', $idMarketing)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getWithBookingCount(): Collection
    {
        return Konsumen::query()
            ->withCount('bookings as total_bookings')
            ->orderByDesc('created_at')
            ->get();
    }

    public function searchByNik(string $nik): ?Konsumen
    {
        return Konsumen::query()->where('nik', $nik)->first();
    }

    public function getDetail(int $id): ?Konsumen
    {
        return Konsumen::query()
            ->with(['prospek', 'bookings', 'bookings.statusHistory', 'bookings.statusPenjualan', 'dokumenKpr', 'pengajuanKpr', 'marketing'])
            ->find($id);
    }

    public function getBookingCount(int $konsumenId): int
    {
        return Booking::query()->where('id_konsumen', $konsumenId)->count();
    }

    public function hasActiveBooking(int $konsumenId): bool
    {
        return Booking::query()
            ->where('id_konsumen', $konsumenId)
            ->where('status_pembayaran_fee', '!=', 'refund')
            ->exists();
    }

    private function handleFileUploads(array $data, ?Konsumen $konsumen = null): array
    {
        $ktpFile = $data['foto_ktp'] ?? null;
        $kkFile = $data['foto_kk'] ?? null;

        if ($ktpFile instanceof \Illuminate\Http\UploadedFile) {
            $oldKtp = $konsumen?->foto_ktp;
            $data['foto_ktp'] = $this->uploadFile($ktpFile, 'dokumen/ktp', $oldKtp);
        } elseif (isset($data['remove_foto_ktp']) && $data['remove_foto_ktp']) {
            if ($konsumen) {
                $this->deleteFile($konsumen->foto_ktp);
            }
            $data['foto_ktp'] = null;
        } else {
            unset($data['foto_ktp']);
        }

        if ($kkFile instanceof \Illuminate\Http\UploadedFile) {
            $oldKk = $konsumen?->foto_kk;
            $data['foto_kk'] = $this->uploadFile($kkFile, 'dokumen/kk', $oldKk);
        } elseif (isset($data['remove_foto_kk']) && $data['remove_foto_kk']) {
            if ($konsumen) {
                $this->deleteFile($konsumen->foto_kk);
            }
            $data['foto_kk'] = null;
        } else {
            unset($data['foto_kk']);
        }

        return $data;
    }
}