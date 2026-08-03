<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\PengajuanKpr;
use App\Models\UnitRumah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanKprService extends BaseService
{
    public function createForBooking(array $data, int $bookingId): PengajuanKpr
    {
        $booking = Booking::with(['konsumen', 'unit'])->findOrFail($bookingId);

        if (Auth::check() && Auth::user()->role?->value === 'marketing' && Auth::id() !== $booking->id_marketing) {
            abort(403, 'Anda tidak memiliki akses untuk mengajukan KPR pada booking ini.');
        }

        $existing = PengajuanKpr::where('id_booking', $bookingId)->first();

        if ($existing) {
            throw new \InvalidArgumentException('Pengajuan KPR sudah ada untuk booking ini.');
        }

        $data['id_konsumen'] = $booking->id_konsumen;
        $data['id_unit'] = $booking->id_unit;

        return DB::transaction(function () use ($data): PengajuanKpr {
            $pengajuan = parent::create($data, PengajuanKpr::class);

            $booking->statusPenjualan->update([
                'status_saat_ini' => 'pengajuan_kpr',
                'tanggal_perubahan' => now(),
                'diubah_oleh' => Auth::id(),
            ]);

            return $pengajuan->fresh();
        });
    }

    public function updateStatus(int $id, string $newStatus, ?string $catatan = null): PengajuanKpr
    {
        $pengajuan = $this->findById(PengajuanKpr::class, $id, ['booking', 'booking.konsumen', 'booking.unit']);

        $allowedTransitions = [
            'draft' => ['diajukan'],
            'diajukan' => ['verifikasi_bank', 'batal'],
            'verifikasi_bank' => ['disetujui', 'ditolak'],
            'disetujui' => ['akad', 'batal'],
            'ditolak' => ['diajukan', 'batal'],
            'akad' => ['serah_terima'],
            'batal' => [],
            'serah_terima' => [],
        ];

        $currentStatus = $pengajuan->status_pengajuan;

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [], true)) {
            throw new \InvalidArgumentException(
                "Transisi dari '{$currentStatus}' ke '{$newStatus}' tidak diizinkan."
            );
        }

        return DB::transaction(function () use ($pengajuan, $newStatus, $catatan): PengajuanKpr {
            $pengajuan->update([
                'status_pengajuan' => $newStatus,
                'tanggal_keputusan' => $newStatus !== 'draft' ? now() : null,
                'catatan' => $catatan ?? $pengajuan->catatan,
            ]);

            if ($newStatus === 'batal') {
                $pengajuan->booking->update(['status_pembayaran_fee' => 'refund']);
                $pengajuan->unit->update(['status_unit' => 'tersedia']);
            }

            return $pengajuan->fresh();
        });
    }

    public function getForMarketing(int $idMarketing): Collection
    {
        return PengajuanKpr::query()
            ->whereHas('booking', function (Builder $q) use ($idMarketing) {
                $q->where('id_marketing', $idMarketing);
            })
            ->with(['konsumen', 'booking', 'unit'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getForBooking(int $bookingId): ?PengajuanKpr
    {
        return PengajuanKpr::where('id_booking', $bookingId)->first();
    }
}