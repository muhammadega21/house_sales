<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPenjualan as StatusPenjualanEnum;
use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\PengajuanKpr;
use App\Models\UnitRumah;
use App\Services\DokumenService;
use App\Services\StatusPenjualanService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengajuanKprService extends BaseService
{
    public function createForBooking(array $data, int $bookingId): PengajuanKpr
    {
        $booking = Booking::with(['konsumen', 'unit', 'statusPenjualan', 'pengajuanKpr'])->findOrFail($bookingId);

        if (Auth::check() && Auth::user()->role?->value === 'marketing' && Auth::id() !== $booking->id_marketing) {
            abort(403, 'Anda tidak memiliki akses untuk mengajukan KPR pada booking ini.');
        }

        if ($booking->pengajuanKpr()->exists()) {
            throw new \InvalidArgumentException('Pengajuan KPR sudah ada untuk booking ini.');
        }

        $statusSaatIni = $booking->statusPenjualan?->status_saat_ini;
        $currentStatus = $statusSaatIni instanceof StatusPenjualanEnum ? $statusSaatIni->value : (string) ($statusSaatIni ?? '');
        if ($currentStatus !== StatusPenjualanEnum::Booking->value) {
            throw new \InvalidArgumentException('Booking harus berstatus booking sebelum membuat pengajuan KPR.');
        }

        if (! app(DokumenService::class)->isComplete($booking->id_konsumen)) {
            throw new \InvalidArgumentException('Dokumen KPR konsumen belum lengkap atau belum valid.');
        }

        $data['id_konsumen'] = $booking->id_konsumen;
        $data['id_unit'] = $booking->id_unit;

        return DB::transaction(function () use ($data, $booking): PengajuanKpr {
            $pengajuan = parent::create($data, PengajuanKpr::class);

            $statusPenjualan = $booking->statusPenjualan;
            if (! $statusPenjualan) {
                throw new \InvalidArgumentException('Status penjualan booking tidak ditemukan.');
            }

            app(StatusPenjualanService::class)->transition(
                $statusPenjualan,
                StatusPenjualanEnum::PengajuanKpr->value,
                'Pengajuan KPR dimulai oleh marketing.',
                Auth::id() ?? 0,
            );

            return $pengajuan->fresh();
        });
    }

    public function updateForBooking(array $data, int $id): PengajuanKpr
    {
        $pengajuan = PengajuanKpr::with('booking')->findOrFail($id);
        $booking = $pengajuan->booking;

        if (! $booking) {
            throw new \InvalidArgumentException('Booking untuk pengajuan tidak ditemukan.');
        }

        if (Auth::check() && Auth::user()->role?->value === 'marketing' && Auth::id() !== $booking->id_marketing) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pengajuan KPR ini.');
        }

        if (! in_array($pengajuan->status_pengajuan, ['draft', 'ditolak'], true)) {
            throw new \InvalidArgumentException('Pengajuan KPR hanya dapat diubah ketika masih draft atau ditolak.');
        }

        $allowedFields = ['nama_bank', 'plafon_kpr', 'tenor_tahun', 'suku_bunga', 'tanggal_pengajuan', 'catatan'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        $pengajuan->update($updateData);

        return $pengajuan->fresh();
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