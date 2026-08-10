<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\StatusProspek;
use App\Models\Booking;
use App\Models\DokumenKpr;
use App\Models\Konsumen;
use App\Models\MarketingTarget;
use App\Models\Pembayaran;
use App\Models\PengajuanKpr;
use App\Models\Perumahan;
use App\Models\Prospek;
use App\Models\StatusHistory;
use App\Models\StatusPenjualan;
use App\Models\UnitRumah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DummyDataSeeder extends Seeder
{
    private int $bookingSeq = 1;
    private array $dokumenWajib = ['ktp', 'kk', 'npwp', 'slip_gaji', 'rekening_koran', 'surat_kerja', 'formulir_kpr'];

    public function run(): void
    {
        // ---------- 1. USER ----------
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create([
            'nama_lengkap' => 'Administrator',
            'username' => 'admin',
            'role' => 'admin',
        ]);
        User::where('role', 'manajemen')->first() ?? User::factory()->create([
            'nama_lengkap' => 'Direktur Utama',
            'username' => 'manajemen',
            'role' => 'manajemen',
        ]);
        $marketings = User::where('role', 'marketing')->get();
        if ($marketings->count() < 4) {
            $marketings = User::factory()->count(4)->create(['role' => 'marketing', 'persentase_komisi' => 2]);
        }

        // ---------- 2. PERUMAHAN + UNIT ----------
        $perumahanList = Perumahan::factory()->count(3)->create();
        foreach ($perumahanList as $p) {
            $units = UnitRumah::factory()->count(14)->create(['id_perumahan' => $p->id]);
            $p->update(['total_unit' => $units->count()]);
        }

        // ---------- 3. TARGET MARKETING (6 bulan terakhir) ----------
        foreach ($marketings as $m) {
            for ($i = 0; $i < 6; $i++) {
                $date = now()->subMonths($i);
                MarketingTarget::firstOrCreate(
                    ['id_marketing' => $m->id, 'periode_bulan' => $date->month, 'periode_tahun' => $date->year],
                    ['target_unit' => fake()->numberBetween(2, 4), 'realisasi_unit' => 0, 'total_nilai_penjualan' => 0, 'total_komisi' => 0],
                );
            }
        }

        // ---------- 4. PROSPEK (funnel lengkap per marketing) ----------
        $statusFunnel = [
            StatusProspek::Baru->value => 4,
            StatusProspek::Dihubungi->value => 3,
            StatusProspek::Berminat->value => 2,
            StatusProspek::TidakBerminat->value => 1,
            StatusProspek::JadiKonsumen->value => 2,
        ];

        $tahapBooking = ['serah_terima', 'akad', 'pengajuan_kpr', 'booking', 'batal'];
        $tahapIndex = 0;

        foreach ($marketings as $m) {
            foreach ($statusFunnel as $status => $jumlah) {
                for ($i = 0; $i < $jumlah; $i++) {
                    $prospek = Prospek::factory()->create([
                        'id_marketing' => $m->id,
                        'status_prospek' => $status,
                        'tanggal_prospek' => fake()->dateTimeBetween('-5 months', 'now')->format('Y-m-d'),
                    ]);

                    if ($status === StatusProspek::JadiKonsumen->value) {
                        $konsumen = Konsumen::factory()->create([
                            'id_prospek' => $prospek->id,
                            'id_marketing' => $m->id,
                            'nama_lengkap' => $prospek->nama_prospek,
                            'no_hp' => $prospek->no_hp,
                        ]);

                        $tahap = $tahapBooking[$tahapIndex % count($tahapBooking)];
                        $tahapIndex++;
                        $this->buatTransaksi($konsumen, $m, $admin, $tahap);
                    }
                }
            }
        }

        // ---------- 5. KONSUMEN LANGSUNG (tanpa prospek, dibuat admin) ----------
        foreach ($marketings->take(2) as $m) {
            $konsumen = Konsumen::factory()->create(['id_marketing' => $m->id]);
            $this->buatTransaksi($konsumen, $m, $admin, 'booking');
        }

        // ---------- 6. SINKRONKAN REALISASI TARGET ----------
        foreach ($marketings as $m) {
            $closing = StatusPenjualan::where('status_saat_ini', 'akad')
                ->orWhere('status_saat_ini', 'serah_terima')
                ->get()
                ->filter(fn($s) => Booking::find($s->id_booking)?->id_marketing === $m->id);

            foreach ($closing->groupBy(fn($s) => $s->tanggal_perubahan->format('Y-m')) as $ym => $rows) {
                [$y, $mo] = array_map('intval', explode('-', $ym));
                $target = MarketingTarget::where('id_marketing', $m->id)
                    ->where('periode_tahun', $y)->where('periode_bulan', $mo)->first();
                if (!$target) continue;

                $nilai = 0;
                $komisi = 0;
                foreach ($rows as $s) {
                    $b = Booking::with('unit')->find($s->id_booking);
                    $nilai += (float) $b?->unit?->harga_jual;
                    $komisi += $nilai ? $b->unit->harga_jual * ((float) $m->persentase_komisi / 100) : 0;
                }
                $target->update([
                    'realisasi_unit' => $rows->count(),
                    'total_nilai_penjualan' => $nilai,
                    'total_komisi' => $komisi,
                ]);
            }
        }

        $this->command?->info('Dummy data berhasil dibuat.');
    }

    /**
     * Membuat satu transaksi lengkap: booking + pembayaran + dokumen + status + KPR.
     */
    private function buatTransaksi(Konsumen $konsumen, User $marketing, User $admin, string $tahap): void
    {
        $unit = UnitRumah::where('status_unit', 'tersedia')
            ->inRandomOrder()->first();
        if (!$unit) return;

        $tanggal = fake()->dateTimeBetween('-4 months', '-10 days');
        $kode = 'BK-' . $tanggal->format('Ymd') . '-' . str_pad((string) $this->bookingSeq++, 3, '0', STR_PAD_LEFT);
        $fee = $unit->kategori === 'subsidi' ? 1_000_000 : 5_000_000;

        $booking = Booking::create([
            'kode_booking' => $kode,
            'id_konsumen' => $konsumen->id,
            'id_unit' => $unit->id,
            'id_marketing' => $marketing->id,
            'tanggal_booking' => $tanggal->format('Y-m-d'),
            'booking_fee' => $fee,
            'status_pembayaran_fee' => 'sudah_bayar',
            'tanggal_bayar_fee' => $tanggal->format('Y-m-d'),
            'metode_bayar_fee' => 'transfer',
        ]);

        // ----- Pembayaran booking fee (terverifikasi) -----
        Pembayaran::create([
            'id_booking' => $booking->id,
            'id_konsumen' => $konsumen->id,
            'jenis_pembayaran' => 'booking_fee',
            'nominal' => $fee,
            'tanggal_bayar' => $tanggal->format('Y-m-d'),
            'metode_bayar' => 'transfer',
            'no_referensi' => 'TRF' . fake()->numerify('##########'),
            'status_verifikasi' => 'diverifikasi',
            'diverifikasi_oleh' => $admin->id,
            'tanggal_verifikasi' => $tanggal->format('Y-m-d'),
        ]);

        // ----- Dokumen KPR (placeholder file) -----
        $jumlahDokumen = in_array($tahap, ['booking']) ? 3 : count($this->dokumenWajib);
        foreach (array_slice($this->dokumenWajib, 0, $jumlahDokumen) as $jenis) {
            $path = 'dokumen-kpr/' . $konsumen->id;
            $filename = $jenis . '_' . $konsumen->id . '_' . now()->format('YmdHis') . '.pdf';
            $content = 'Dokumen dummy ' . $jenis . ' untuk ' . $konsumen->nama_lengkap . ' (keperluan demo).';
            Storage::disk('public')->put($path . '/' . $filename, $content);

            DokumenKpr::create([
                'id_konsumen' => $konsumen->id,
                'jenis_dokumen' => $jenis,
                'nama_file' => $filename,
                'path_file' => $path . '/' . $filename,
                'ukuran_file' => strlen($content),
                'tipe_file' => 'pdf',
                'status_verifikasi' => $tahap === 'booking' ? 'belum_diverifikasi' : 'valid',
                'diupload_oleh' => $marketing->id,
            ]);
        }

        // ----- Riwayat status penjualan -----
        $langkah = ['booking'];
        if (in_array($tahap, ['pengajuan_kpr', 'akad', 'serah_terima'])) $langkah[] = 'pengajuan_kpr';
        if (in_array($tahap, ['akad', 'serah_terima'])) $langkah[] = 'akad';
        if ($tahap === 'serah_terima') $langkah[] = 'serah_terima';
        if ($tahap === 'batal') $langkah[] = 'batal';

        $statusAkhir = $tahap === 'batal' ? 'batal' : last($langkah);
        StatusPenjualan::create([
            'id_booking' => $booking->id,
            'id_konsumen' => $konsumen->id,
            'id_unit' => $unit->id,
            'status_saat_ini' => $statusAkhir,
            'tanggal_perubahan' => $tanggal->format('Y-m-d H:i:s'),
            'diubah_oleh' => $marketing->id,
            'catatan' => 'Dibuat oleh seeder dummy',
        ]);

        $sebelum = null;
        $tgl = clone $tanggal;
        foreach ($langkah as $st) {
            StatusHistory::create([
                'id_booking' => $booking->id,
                'status_sebelum' => $sebelum,
                'status_sesudah' => $st,
                'catatan' => 'Perubahan status (dummy)',
                'diubah_oleh' => $st === 'akad' || $st === 'serah_terima' ? $admin->id : $marketing->id,
                'created_at' => $tgl->format('Y-m-d H:i:s'),
            ]);
            $sebelum = $st;
            $tgl->modify('+7 days');
        }

        // ----- Side effect status unit -----
        $unit->update(['status_unit' => match (true) {
            in_array($tahap, ['akad', 'serah_terima']) => 'dijual',
            $tahap === 'batal' => 'tersedia',
            default => 'dibooking',
        }]);

        // ----- Pembayaran lanjutan (DP + pelunasan) -----
        if ($tahap !== 'batal' && $tahap !== 'booking') {
            $dp = (float) $unit->harga_jual * 0.1;
            Pembayaran::create([
                'id_booking' => $booking->id,
                'id_konsumen' => $konsumen->id,
                'jenis_pembayaran' => 'dp',
                'nominal' => $dp,
                'tanggal_bayar' => $tgl->format('Y-m-d'),
                'metode_bayar' => 'transfer',
                'no_referensi' => 'TRF' . fake()->numerify('##########'),
                'status_verifikasi' => 'diverifikasi',
                'diverifikasi_oleh' => $admin->id,
                'tanggal_verifikasi' => $tgl->format('Y-m-d'),
            ]);
        }

        // ----- Pengajuan KPR -----
        if (in_array($tahap, ['pengajuan_kpr', 'akad', 'serah_terima'])) {
            PengajuanKpr::create([
                'id_konsumen' => $konsumen->id,
                'id_booking' => $booking->id,
                'id_unit' => $unit->id,
                'nama_bank' => fake()->randomElement(['Bank BTN', 'Bank BNI', 'Bank Mandiri', 'Bank BRI']),
                'plafon_kpr' => (float) $unit->harga_jual * 0.9,
                'tenor_tahun' => fake()->numberBetween(10, 20),
                'suku_bunga' => fake()->randomElement([7.5, 8.0, 8.5]),
                'tanggal_pengajuan' => $tanggal->format('Y-m-d'),
                'status_pengajuan' => match ($tahap) {
                    'pengajuan_kpr' => 'verifikasi_bank',
                    default => 'akad',
                },
                'tanggal_keputusan' => in_array($tahap, ['akad', 'serah_terima']) ? $tgl->format('Y-m-d') : null,
            ]);
        }
    }
}
