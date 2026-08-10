<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisPembayaran;
use App\Enums\StatusPembayaranFee;
use App\Enums\StatusVerifikasi;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\StatusPenjualan;
use App\Services\StatusPenjualanService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembayaranService extends BaseService
{
    public function create(array $data, Model|string|null $model = null): Pembayaran
    {
        return DB::transaction(function () use ($data): Pembayaran {
            $booking = Booking::query()
                ->with(['unit', 'konsumen'])
                ->lockForUpdate()
                ->findOrFail($data['id_booking']);

            if (Auth::check() && Auth::user()->role?->value === 'marketing' && Auth::id() !== $booking->id_marketing) {
                abort(403, 'Anda tidak memiliki akses untuk menginput pembayaran pada booking ini.');
            }

            $sisaTagihan = $this->getSisaTagihan($booking->id);

            if ($sisaTagihan <= 0) {
                abort(422, 'Tagihan untuk booking ini sudah sepenuhnya lunas. Tidak dapat menambahkan pembayaran.');
            }

            if (! isset($data['bukti_bayar']) || ! $data['bukti_bayar']) {
                throw ValidationException::withMessages([
                    'bukti_bayar' => 'Bukti pembayaran wajib diupload (BR-18).',
                ]);
            }

            $data['bukti_bayar'] = $this->uploadFile($data['bukti_bayar'], 'bukti-bayar');

            $data['status_verifikasi'] = StatusVerifikasi::Pending->value;
            $data['id_konsumen'] = $booking->id_konsumen;

            /** @var Pembayaran $pembayaran */
            $pembayaran = parent::create($data, Pembayaran::class);

            if ($data['jenis_pembayaran'] === JenisPembayaran::BookingFee->value) {
                $booking->update([
                    'status_pembayaran_fee' => StatusPembayaranFee::SudahBayar->value,
                ]);
            }

            return $pembayaran->fresh();
        });
    }

    public function getForBooking(int $idBooking): Collection
    {
        return Pembayaran::query()
            ->where('id_booking', $idBooking)
            ->with(['booking.konsumen', 'booking.unit'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getForMarketing(int $idMarketing): Collection
    {
        return Pembayaran::query()
            ->whereHas('booking', function ($query) use ($idMarketing) {
                $query->where('id_marketing', $idMarketing);
            })
            ->with(['booking.konsumen', 'booking.unit'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getTotalTerverifikasi(int $idBooking): float
    {
        return (float) Pembayaran::query()
            ->where('id_booking', $idBooking)
            ->where('status_verifikasi', StatusVerifikasi::Diverifikasi->value)
            ->sum('nominal');
    }

    public function getSisaTagihan(int $idBooking): float
    {
        $booking = Booking::query()->with('unit')->find($idBooking);

        if (! $booking) {
            return 0.0;
        }

        $hargaUnit = (float) ($booking->unit?->harga_jual ?? 0);

        return $hargaUnit - $this->getTotalTerverifikasi($idBooking);
    }

    public function getRiwayatPembayaran(int $idKonsumen): Collection
    {
        return Pembayaran::query()
            ->where('id_konsumen', $idKonsumen)
            ->with(['booking.unit'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getStats(int $idMarketing): array
    {
        $bulanIni = now()->startOfMonth();

        $baseQuery = Pembayaran::query()
            ->whereHas('booking', function ($query) use ($idMarketing) {
                $query->where('id_marketing', $idMarketing);
            });

        return [
            'pending' => (clone $baseQuery)
                ->where('status_verifikasi', StatusVerifikasi::Pending->value)
                ->count(),
            'total_terverifikasi' => (clone $baseQuery)
                ->where('status_verifikasi', StatusVerifikasi::Diverifikasi->value)
                ->where('created_at', '>=', $bulanIni)
                ->sum('nominal'),
            'ditolak' => (clone $baseQuery)
                ->where('status_verifikasi', StatusVerifikasi::Ditolak->value)
                ->count(),
        ];
    }

    public function getWithRelations(int $id): ?Pembayaran
    {
        return Pembayaran::query()
            ->with(['booking.konsumen', 'booking.unit', 'booking.marketing', 'diverifikasiOleh'])
            ->find($id);
    }

    public function getActiveBookings(int $idMarketing): Collection
    {
        return Booking::query()
            ->where('id_marketing', $idMarketing)
            ->where('status_pembayaran_fee', '!=', StatusPembayaranFee::Refund->value)
            ->with(['konsumen', 'unit'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getInfoBooking(int $idBooking): ?array
    {
        $booking = Booking::query()
            ->with(['konsumen', 'unit'])
            ->find($idBooking);

        if (! $booking) {
            return null;
        }

        $hargaUnit = (float) ($booking->unit?->harga_jual ?? 0);
        $totalTerverifikasi = $this->getTotalTerverifikasi($booking->id);
        $sisaTagihan = $hargaUnit - $totalTerverifikasi;
        $dpMinimumPersen = $booking->unit ? (float) $booking->unit->dp_minimum_persen : 0;
        $dpMinimumNominal = $hargaUnit * ($dpMinimumPersen / 100);

        return [
            'kode_booking' => $booking->kode_booking,
            'konsumen' => $booking->konsumen?->nama_lengkap ?? '-',
            'unit' => ($booking->unit?->kode_unit ?? '-') . ' (' . ($booking->unit?->tipe_rumah ?? '') . ')',
            'harga_unit' => $hargaUnit,
            'harga_unit_format' => 'Rp ' . number_format($hargaUnit, 0, ',', '.'),
            'total_terverifikasi' => $totalTerverifikasi,
            'total_terverifikasi_format' => 'Rp ' . number_format($totalTerverifikasi, 0, ',', '.'),
            'sisa_tagihan' => $sisaTagihan,
            'sisa_tagihan_format' => 'Rp ' . number_format($sisaTagihan, 0, ',', '.'),
            'booking_fee' => (float) ($booking->booking_fee ?? 0),
            'dp_minimum_persen' => $dpMinimumPersen,
            'dp_minimum_nominal' => $dpMinimumNominal,
        ];
    }

    public function verifikasi(int $idPembayaran, string $status, ?string $catatan, int $adminId): Pembayaran
    {
        return DB::transaction(function () use ($idPembayaran, $status, $catatan, $adminId): Pembayaran {
            $pembayaran = Pembayaran::query()
                ->lockForUpdate()
                ->findOrFail($idPembayaran);

            if ($pembayaran->status_verifikasi !== StatusVerifikasi::Pending) {
                throw ValidationException::withMessages([
                    'status_verifikasi' => 'Pembayaran sudah diproses sebelumnya. Status saat ini: ' . $pembayaran->status_verifikasi->label(),
                ]);
            }

            $pembayaran->update([
                'status_verifikasi' => $status,
                'diverifikasi_oleh' => $adminId,
                'tanggal_verifikasi' => now(),
                'catatan_verifikasi' => $catatan,
            ]);

            if ($status === StatusVerifikasi::Diverifikasi->value) {
                $this->handleVerifiedPayment($pembayaran->fresh());
            }

            return $pembayaran->fresh();
        });
    }

    private function handleVerifiedPayment(Pembayaran $pembayaran): void
    {
        $booking = Booking::query()->with('unit')->find($pembayaran->id_booking);

        if (! $booking) {
            return;
        }

        if ($pembayaran->jenis_pembayaran === JenisPembayaran::BookingFee) {
            $booking->update([
                'status_pembayaran_fee' => StatusPembayaranFee::SudahBayar->value,
                'tanggal_bayar_fee' => $pembayaran->tanggal_bayar,
            ]);
        }

        // BR-17: Total DP + cicilan = harga rumah (cash bertahap)
        // Cek apakah total pembayaran terverifikasi sudah mencapai harga unit
        $totalTerverifikasi = $this->getTotalTerverifikasi($booking->id);

        if ($totalTerverifikasi >= ($booking->unit?->harga_jual ?? 0)) {
            // Total pembayaran sudah mencapai harga unit -> tandai lunas
            $booking->update([
                'status_pembayaran_fee' => StatusPembayaranFee::SudahBayar->value,
            ]);

            // Jika ada record status penjualan, pindahkan ke Serah Terima (final)
            $statusPenjualan = StatusPenjualan::where('id_booking', $booking->id)->first();
            if ($statusPenjualan) {
                try {
                    app(StatusPenjualanService::class)->transition(
                        $statusPenjualan,
                        \App\Enums\StatusPenjualan::SerahTerima->value,
                        'Pembayaran terverifikasi mencapai harga unit. Menandai lunas dan serah terima.',
                        Auth::id() ?? 0
                    );
                } catch (\Throwable $e) {
                    // Jangan memblokir alur jika transisi gagal — catat dan lanjutkan
                    activity_log([
                        'id_user' => Auth::id() ?? 0,
                        'aksi' => 'error_status_transition',
                        'entitas' => 'status_penjualan',
                        'entitas_id' => $booking->id,
                        'deskripsi' => 'Gagal transisi ke serah_terima: ' . $e->getMessage(),
                    ]);
                }
            }
        }

        // Notifikasi untuk marketing (opsional tapi disarankan)
        $this->sendNotifikasiToMarketing($booking->id_marketing, $pembayaran);
    }

    public function getStatsForAdmin(): array
    {
        $bulanIni = now()->startOfMonth();

        return [
            'pending' => Pembayaran::query()
                ->where('status_verifikasi', StatusVerifikasi::Pending->value)
                ->count(),
            'total_terverifikasi' => Pembayaran::query()
                ->where('status_verifikasi', StatusVerifikasi::Diverifikasi->value)
                ->where('created_at', '>=', $bulanIni)
                ->sum('nominal'),
            'ditolak' => Pembayaran::query()
                ->where('status_verifikasi', StatusVerifikasi::Ditolak->value)
                ->count(),
            'diverifikasi_hari_ini' => Pembayaran::query()
                ->where('status_verifikasi', StatusVerifikasi::Diverifikasi->value)
                ->whereDate('tanggal_verifikasi', today())
                ->count(),
            'ditolak_hari_ini' => Pembayaran::query()
                ->where('status_verifikasi', StatusVerifikasi::Ditolak->value)
                ->whereDate('tanggal_verifikasi', today())
                ->count(),
        ];
    }

    private function sendNotifikasiToMarketing(int $idMarketing, Pembayaran $pembayaran): void
    {
        // Notifikasi sederhana: catat ke tabel notifikasi
        // Implementasi penuh akan ditambahkan di fase berikutnya
    }
}
