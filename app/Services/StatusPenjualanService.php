<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPenjualan as StatusPenjualanEnum;
use App\Enums\StatusUnit;
use App\Models\Booking;
use App\Models\StatusHistory;
use App\Models\StatusPenjualan as StatusPenjualanModel;
use App\Models\UnitRumah;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StatusPenjualanService
{
    public function __construct(
        private readonly KomisiService $komisiService
    ) {}

    public function transition(
        StatusPenjualanModel $status,
        string $newStatus,
        string $catatan,
        int $userId
    ): void {
        $currentStatus = $status->status_saat_ini instanceof StatusPenjualanEnum
            ? $status->status_saat_ini
            : StatusPenjualanEnum::tryFrom((string) ($status->status_saat_ini ?? ''))
            ?? StatusPenjualanEnum::Prospek;

        $targetStatus = StatusPenjualanEnum::from($newStatus);

        if ($currentStatus === $targetStatus) {
            throw new InvalidArgumentException(
                "Status sudah berada di '{$currentStatus->label()}'."
            );
        }

        if ($currentStatus->isFinal()) {
            throw new InvalidArgumentException(
                "Status '{$currentStatus->label()}' bersifat final dan tidak dapat diubah."
            );
        }

        if (! in_array($targetStatus, $currentStatus->allowedTransitions(), true)) {
            $allowedLabels = array_map(fn ($s) => $s->label(), $currentStatus->allowedTransitions());
            $allowedList = empty($allowedLabels)
                ? 'tidak ada transisi yang tersedia'
                : 'bisa hanya ke: ' . implode(', ', $allowedLabels);

            throw new InvalidArgumentException(
                "Transisi dari '{$currentStatus->label()}' ke '{$targetStatus->label()}' tidak diizinkan. " .
                "Status masih '{$currentStatus->label()}', yang $allowedList."
            );
        }

        if (empty(trim($catatan))) {
            throw new InvalidArgumentException('Catatan perubahan status wajib diisi.');
        }

        DB::transaction(function () use ($status, $currentStatus, $targetStatus, $catatan, $userId): void {
            StatusHistory::create([
                'id_booking' => $status->id_booking,
                'status_sebelum' => $currentStatus->value,
                'status_sesudah' => $targetStatus->value,
                'catatan' => $catatan,
                'diubah_oleh' => $userId,
            ]);

            $status->update([
                'status_saat_ini' => $targetStatus->value,
                'tanggal_perubahan' => now(),
                'diubah_oleh' => $userId,
                'catatan' => $catatan,
            ]);

            $this->handleSideEffects($status, $targetStatus, $currentStatus);

            activity_log([
                'id_user' => $userId,
                'aksi' => 'update_status',
                'entitas' => 'status_penjualan',
                'entitas_id' => $status->id_booking,
                'deskripsi' => sprintf(
                    'Status penjualan diubah dari %s menjadi %s.',
                    $currentStatus->label(),
                    $targetStatus->label()
                ),
                'data_lama' => ['status_saat_ini' => $currentStatus->value],
                'data_baru' => ['status_saat_ini' => $targetStatus->value, 'catatan' => $catatan],
            ]);
        });
    }

    private function handleSideEffects(
        StatusPenjualanModel $status,
        StatusPenjualanEnum $newStatus,
        StatusPenjualanEnum $currentStatus
    ): void {
        $unit = UnitRumah::find($status->id_unit);
        $booking = Booking::find($status->id_booking);

        match ($newStatus) {
            StatusPenjualanEnum::Booking => $unit?->update(['status_unit' => StatusUnit::Dibooking->value]),
            StatusPenjualanEnum::Akad => $this->handleAkad($status, $unit),
            StatusPenjualanEnum::Batal => $this->handleBatal($status, $unit, $currentStatus, $booking),
            default => null,
        };
    }

    private function handleAkad(StatusPenjualanModel $status, UnitRumah|null $unit): void
    {
        if (! $unit) {
            return;
        }

        $unit->update(['status_unit' => StatusUnit::Dijual->value]);
        $this->komisiService->hitungKomisi($status->id_booking);
    }

    private function handleBatal(StatusPenjualanModel $status, UnitRumah|null $unit, StatusPenjualanEnum $currentStatus, Booking|null $booking): void
    {
        if ($unit) {
            $unit->update(['status_unit' => StatusUnit::Tersedia->value]);
        }

        $historyAkad = StatusHistory::where('id_booking', $status->id_booking)
            ->where('status_sesudah', StatusPenjualanEnum::Akad->value)
            ->exists();

        if ($historyAkad || $currentStatus === StatusPenjualanEnum::Akad) {
            $this->komisiService->rollbackKomisi($status->id_booking);
        }

        if ($booking && $booking->status_pembayaran_fee === 'sudah_bayar') {
            $booking->update(['status_pembayaran_fee' => 'refund']);
        }
    }

    public function getAvailableTransitions(StatusPenjualanModel $status): array
    {
        $currentStatus = $status->status_saat_ini instanceof StatusPenjualanEnum
            ? $status->status_saat_ini
            : StatusPenjualanEnum::from((string) $status->status_saat_ini);

        return array_map(
            static fn(StatusPenjualanEnum $transition) => $transition->value,
            $currentStatus->allowedTransitions()
        );
    }

    public function getTimeline(int $idBooking): \Illuminate\Support\Collection
    {
        return StatusHistory::where('id_booking', $idBooking)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
