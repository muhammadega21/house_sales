<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Command;

class CekBookingHangus extends Command
{
    protected $signature = 'booking:cek-hangus';
    protected $description = 'Batalkan booking yang tidak bayar DP dalam 14 hari';

    public function handle(BookingService $bookingService): void
    {
        $batasTanggal = now()->subDays(14);

        $bookingHangus = Booking::query()
            ->where('tanggal_booking', '<', $batasTanggal)
            ->whereHas('statusPenjualan', function ($q) {
                $q->where('status_saat_ini', \App\Enums\StatusPenjualan::Booking->value);
            })
            ->whereDoesntHave('pembayaran', function ($q) {
                $q->where('jenis_pembayaran', \App\Enums\JenisPembayaran::Dp->value)
                    ->where('status_verifikasi', \App\Enums\StatusVerifikasi::Diverifikasi->value);
            })
            ->get();

        foreach ($bookingHangus as $booking) {
            $bookingService->cancel(
                $booking->id,
                'Booking hangus: tidak ada pembayaran DP dalam 14 hari',
                1
            );
        }

        $this->info("{$bookingHangus->count()} booking hangus diproses.");
    }
}
