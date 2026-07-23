# STATE MACHINE - STATUS PENJUALAN

## Status yang Tersedia

| Status        | Deskripsi                        | Warna Badge |
| ------------- | -------------------------------- | ----------- |
| prospek       | Calon konsumen belum booking     | Gray        |
| booking       | Sudah bayar booking fee          | Yellow      |
| pengajuan_kpr | Dokumen diajukan ke bank         | Indigo      |
| akad          | KPR disetujui, tanda tangan akad | Blue        |
| serah_terima  | Rumah diserahkan ke konsumen     | Green       |
| batal         | Transaksi dibatalkan             | Red         |

## Transisi yang Diizinkan

prospek ──────► booking
prospek ──────► batal
booking ──────► pengajuan_kpr
booking ──────► batal
pengajuan_kpr ► akad
pengajuan_kpr ► batal
akad ─────────► serah_terima
akad ─────────► batal (dengan approval manajemen)
serah_terima ─► (FINAL - tidak ada transisi keluar)
batal ────────► (FINAL - tidak ada transisi keluar)

### Tabel Transisi Detail

| Dari          | Ke            | Syarat                             | Aktor           | Side Effect                        |
| ------------- | ------------- | ---------------------------------- | --------------- | ---------------------------------- |
| prospek       | booking       | Booking fee dibayar, unit tersedia | Marketing/Admin | Unit → 'dibooking'                 |
| prospek       | batal         | Konsumen tidak lanjut              | Marketing/Admin | -                                  |
| booking       | pengajuan_kpr | Dokumen lengkap & valid            | Marketing/Admin | Buat record pengajuan              |
| booking       | batal         | Konsumen batal / timeout 14 hari   | Admin           | Unit → 'tersedia'                  |
| pengajuan_kpr | akad          | Bank approve                       | Admin           | Unit → 'dijual', hitung komisi     |
| pengajuan_kpr | batal         | Bank reject / konsumen mundur      | Admin           | Unit → 'tersedia'                  |
| akad          | serah_terima  | Rumah siap, BAST ditandatangani    | Admin           | Generate berita acara              |
| akad          | batal         | Kasus khusus + approval manajemen  | Admin           | Unit → 'tersedia', rollback komisi |

## Implementasi di Service

```php
namespace App\Services;

use App\Models\StatusPenjualan;
use App\Models\StatusHistory;
use App\Models\UnitRumah;
use App\Enums\StatusPenjualan as StatusEnum;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StatusPenjualanService
{
    private array $allowedTransitions = [
        'prospek'       => ['booking', 'batal'],
        'booking'       => ['pengajuan_kpr', 'batal'],
        'pengajuan_kpr' => ['akad', 'batal'],
        'akad'          => ['serah_terima', 'batal'],
        'serah_terima'  => [],  // FINAL
        'batal'         => [],  // FINAL
    ];

    public function transition(
        StatusPenjualan $status,
        string $newStatus,
        string $catatan,
        int $userId
    ): void {
        $currentStatus = $status->status_saat_ini;

        // Validasi transisi
        if (!in_array($newStatus, $this->allowedTransitions[$currentStatus])) {
            throw new InvalidArgumentException(
                "Transisi dari '{$currentStatus}' ke '{$newStatus}' tidak diizinkan."
            );
        }

        DB::transaction(function () use ($status, $newStatus, $catatan, $userId, $currentStatus) {
            // Catat history
            StatusHistory::create([
                'id_booking' => $status->id_booking,
                'status_sebelum' => $currentStatus,
                'status_sesudah' => $newStatus,
                'catatan' => $catatan,
                'diubah_oleh' => $userId,
            ]);

            // Update status
            $status->update([
                'status_saat_ini' => $newStatus,
                'tanggal_perubahan' => now(),
                'diubah_oleh' => $userId,
                'catatan' => $catatan,
            ]);

            // Side effects
            $this->handleSideEffects($status, $newStatus);
        });
    }

    private function handleSideEffects(StatusPenjualan $status, string $newStatus): void
    {
        $unit = UnitRumah::find($status->id_unit);

        match ($newStatus) {
            'booking' => $unit->update(['status_unit' => 'dibooking']),
            'akad' => $unit->update(['status_unit' => 'dijual']),
            'batal' => $unit->update(['status_unit' => 'tersedia']),
            default => null,
        };

        // Hitung komisi saat akad
        if ($newStatus === 'akad') {
            app(KomisiService::class)->hitungKomisi($status->id_booking);
        }

        // Rollback komisi saat batal setelah akad
        if ($newStatus === 'batal' && $status->status_saat_ini === 'akad') {
            app(KomisiService::class)->rollbackKomisi($status->id_booking);
        }
    }
}
```

Validasi di Controller

public function update(Request $request, StatusPenjualan $statusPenjualan)
{
$validated = $request->validate([
'status_baru' => 'required|in:prospek,booking,pengajuan_kpr,akad,serah_terima,batal',
'catatan' => 'required|string|max:500',
]);

    try {
        $this->statusService->transition(
            $statusPenjualan,
            $validated['status_baru'],
            $validated['catatan'],
            auth()->id()
        );

        return back()->with('success', 'Status berhasil diperbarui.');
    } catch (InvalidArgumentException $e) {
        return back()->with('error', $e->getMessage());
    }

}
