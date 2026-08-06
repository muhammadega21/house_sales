<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPenjualan as StatusPenjualanEnum;
use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\MarketingNotification;
use App\Models\PengajuanKpr;
use App\Models\PengajuanKprHistory;
use App\Models\StatusPenjualan;
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
        $data['status_pengajuan'] = $data['status_pengajuan'] ?? 'draft';

        return DB::transaction(function () use ($data, $booking): PengajuanKpr {
            $pengajuan = parent::create($data, PengajuanKpr::class);

            PengajuanKprHistory::create([
                'id_pengajuan' => $pengajuan->id,
                'status_sebelum' => 'draft',
                'status_sesudah' => $pengajuan->status_pengajuan,
                'catatan' => $pengajuan->catatan,
                'diubah_oleh' => Auth::id() ?? 0,
            ]);

            $statusPenjualan = $booking->statusPenjualan;
            if (! $statusPenjualan) {
                throw new \InvalidArgumentException('Status penjualan booking tidak ditemukan.');
            }

            // Hanya ubah status penjualan jika pengajuan dibuat dalam keadaan "diajukan".
            if ($pengajuan->status_pengajuan === 'diajukan') {
                app(StatusPenjualanService::class)->transition(
                    $statusPenjualan,
                    StatusPenjualanEnum::PengajuanKpr->value,
                    'Pengajuan KPR dimulai oleh marketing.',
                    Auth::id() ?? 0,
                );
            }

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

    private array $allowedTransitions = [
        'draft' => ['diajukan', 'batal'],
        'diajukan' => ['verifikasi_bank', 'ditolak', 'batal'],
        'verifikasi_bank' => ['disetujui', 'ditolak', 'batal'],
        'disetujui' => ['akad', 'batal'],
        'ditolak' => [],
        'akad' => [],
        'batal' => [],
    ];

    public function getAllowedStatusTransitions(string $currentStatus): array
    {
        return $this->allowedTransitions[$currentStatus] ?? [];
    }

    public function updateStatus(int $id, string $newStatus, string $catatan, int $userId): PengajuanKpr
    {
        $pengajuan = $this->findById(PengajuanKpr::class, $id, ['booking', 'booking.konsumen', 'booking.unit', 'booking.statusPenjualan']);
        $currentStatus = $pengajuan->status_pengajuan;

        if (! in_array($newStatus, $this->allowedTransitions[$currentStatus] ?? [], true)) {
            throw new \InvalidArgumentException(
                "Transisi dari '{$currentStatus}' ke '{$newStatus}' tidak diizinkan."
            );
        }

        return DB::transaction(function () use ($pengajuan, $newStatus, $catatan, $userId): PengajuanKpr {
            $pengajuan->update([
                'status_pengajuan' => $newStatus,
                'tanggal_keputusan' => in_array($newStatus, ['disetujui', 'ditolak', 'akad']) ? now() : null,
                'catatan' => $catatan,
            ]);

            PengajuanKprHistory::create([
                'id_pengajuan' => $pengajuan->id,
                'status_sebelum' => $pengajuan->getOriginal('status_pengajuan'),
                'status_sesudah' => $newStatus,
                'catatan' => $catatan,
                'diubah_oleh' => $userId,
            ]);

            $this->handleSideEffects($pengajuan, $newStatus, $userId);

            return $pengajuan->fresh();
        });
    }

    private function handleSideEffects(PengajuanKpr $pengajuan, string $newStatus, int $userId): void
    {
        $statusPenjualan = StatusPenjualan::where('id_booking', $pengajuan->id_booking)->first();

        if ($statusPenjualan && $statusPenjualan->status_saat_ini === StatusPenjualanEnum::Booking->value) {
            if (in_array($newStatus, ['diajukan', 'verifikasi_bank', 'disetujui', 'akad'], true)) {
                app(StatusPenjualanService::class)->transition(
                    $statusPenjualan,
                    StatusPenjualanEnum::PengajuanKpr->value,
                    'Status penjualan otomatis disinkronkan ke Pengajuan KPR karena status pengajuan berubah.',
                    $userId,
                );
                $statusPenjualan->refresh();
            }
        }

        match ($newStatus) {
            'diajukan', 'verifikasi_bank' => null,
            'disetujui' => null,
            'akad' => $this->handleAkad($pengajuan, $statusPenjualan, $userId),
            'ditolak', 'batal' => $this->handleDitolakAtauBatal($pengajuan, $newStatus, $statusPenjualan, $userId),
            default => null,
        };

        $this->sendNotificationToMarketing($pengajuan, $newStatus);
    }

    private function handleAkad(PengajuanKpr $pengajuan, ?StatusPenjualan $status, int $userId): void
    {
        if (! $status) {
            return;
        }

        app(StatusPenjualanService::class)->transition(
            $status,
            'akad',
            'KPR disetujui, akad kredit',
            $userId,
        );

        app(KomisiService::class)->hitungKomisi($pengajuan->id_booking);
    }

    private function handleDitolakAtauBatal(PengajuanKpr $pengajuan, string $newStatus, ?StatusPenjualan $status, int $userId): void
    {
        $pengajuanAktifLain = PengajuanKpr::query()
            ->where('id_booking', $pengajuan->id_booking)
            ->where('id', '!=', $pengajuan->id)
            ->whereIn('status_pengajuan', ['diajukan', 'verifikasi_bank', 'disetujui'])
            ->exists();

        if (! $pengajuanAktifLain) {
            // Tidak ada pengajuan lain aktif untuk booking yang sama.
            // Status penjualan tetap di tahap pengajuan_kpr sehingga marketing bisa membuat pengajuan baru.
            // Jika pengajuan dibatalkan setelah disetujui, proses manual lebih lanjut dapat dilakukan.
        }
    }

    private function sendNotificationToMarketing(PengajuanKpr $pengajuan, string $newStatus): void
    {
        if (! $pengajuan->booking || ! $pengajuan->booking->id_marketing) {
            return;
        }

        $konsumenName = $pengajuan->konsumen?->nama_lengkap ?? 'Konsumen';
        $bankName = $pengajuan->nama_bank ?? 'bank';
        $marketingId = $pengajuan->booking->id_marketing;

        $title = match ($newStatus) {
            'disetujui' => "Pengajuan KPR konsumen {$konsumenName} disetujui",
            'ditolak' => "Pengajuan KPR konsumen {$konsumenName} ditolak",
            'akad' => "Pengajuan KPR konsumen {$konsumenName} telah akad",
            'batal' => "Pengajuan KPR konsumen {$konsumenName} dibatalkan",
            'diajukan' => "Pengajuan KPR konsumen {$konsumenName} diajukan",
            'verifikasi_bank' => "Pengajuan KPR konsumen {$konsumenName} sedang verifikasi bank",
            default => "Status pengajuan KPR diperbarui",
        };

        $message = match ($newStatus) {
            'disetujui' => "Pengajuan KPR konsumen {$konsumenName} di bank {$bankName} disetujui.",
            'ditolak' => "Pengajuan KPR konsumen {$konsumenName} ditolak oleh bank {$bankName}. Konsumen bisa mencoba bank lain.",
            'akad' => "Pengajuan KPR konsumen {$konsumenName} sudah akad. Unit akan ditandai dijual.",
            'batal' => "Pengajuan KPR konsumen {$konsumenName} dibatalkan.",
            'diajukan' => "Pengajuan KPR konsumen {$konsumenName} telah diajukan ke bank {$bankName}.",
            'verifikasi_bank' => "Pengajuan KPR konsumen {$konsumenName} dalam proses verifikasi bank.",
            default => "Status pengajuan KPR konsumen {$konsumenName} diperbarui.",
        };

        MarketingNotification::create([
            'id_marketing' => $marketingId,
            'title' => $title,
            'message' => $message,
            'type' => 'pengajuan_kpr',
            'data' => [
                'pengajuan_id' => $pengajuan->id,
                'booking_id' => $pengajuan->id_booking,
                'status' => $newStatus,
            ],
        ]);
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
