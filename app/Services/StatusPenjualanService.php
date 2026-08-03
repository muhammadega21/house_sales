<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPenjualan as StatusPenjualanEnum;
use App\Enums\StatusUnit;
use App\Models\StatusHistory;
use App\Models\StatusPenjualan as StatusPenjualanModel;
use App\Models\UnitRumah;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StatusPenjualanService
{
    private array $allowedTransitions = [
        'prospek' => ['booking', 'batal'],
        'booking' => ['pengajuan_kpr', 'batal'],
        'pengajuan_kpr' => ['akad', 'batal'],
        'akad' => ['serah_terima', 'batal'],
        'serah_terima' => [],
        'batal' => [],
    ];

    public function transition(
        StatusPenjualanModel $status,
        string $newStatus,
        string $catatan,
        int $userId
    ): void {
        $currentStatus = $status->status_saat_ini instanceof StatusPenjualanEnum
            ? $status->status_saat_ini->value
            : (string) $status->status_saat_ini;

        if (! in_array($newStatus, $this->allowedTransitions[$currentStatus] ?? [], true)) {
            throw new InvalidArgumentException(
                "Transisi dari '{$currentStatus}' ke '{$newStatus}' tidak diizinkan."
            );
        }

        DB::transaction(function () use ($status, $newStatus, $catatan, $userId, $currentStatus): void {
            StatusHistory::create([
                'id_booking' => $status->id_booking,
                'status_sebelum' => $currentStatus,
                'status_sesudah' => $newStatus,
                'catatan' => $catatan,
                'diubah_oleh' => $userId,
            ]);

            $status->update([
                'status_saat_ini' => $newStatus,
                'tanggal_perubahan' => now(),
                'diubah_oleh' => $userId,
                'catatan' => $catatan,
            ]);

            $this->handleSideEffects($status, $newStatus, $currentStatus);
        });
    }

    private function handleSideEffects(StatusPenjualanModel $status, string $newStatus, string $currentStatus): void
    {
        $unit = UnitRumah::find($status->id_unit);

        match ($newStatus) {
            'booking' => $unit?->update(['status_unit' => StatusUnit::Dibooking->value]),
            'akad' => $unit?->update(['status_unit' => StatusUnit::Dijual->value]),
            'batal' => $unit?->update(['status_unit' => StatusUnit::Tersedia->value]),
            default => null,
        };

        if ($newStatus === 'akad') {
            app(KomisiService::class)->hitungKomisi($status->id_booking);
        }

        if ($newStatus === 'batal' && $currentStatus === 'akad') {
            app(KomisiService::class)->rollbackKomisi($status->id_booking);
        }
    }

    public function getAllowedTransitions(string $currentStatus): array
    {
        return $this->allowedTransitions[$currentStatus] ?? [];
    }
}
