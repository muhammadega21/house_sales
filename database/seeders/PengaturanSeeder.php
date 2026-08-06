<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'kunci' => 'nama_perusahaan',
                'nilai' => 'PT House Sales Indonesia',
                'keterangan' => 'Nama resmi perusahaan pengembang perumahan',
            ],
            [
                'kunci' => 'booking_fee_minimum',
                'nilai' => '5000000',
                'keterangan' => 'Minimal pembayaran booking fee (Rupiah)',
            ],
            [
                'kunci' => 'dp_minimum_persen',
                'nilai' => '10',
                'keterangan' => 'Persentase minimal Down Payment (%)',
            ],
            [
                'kunci' => 'email_kontak',
                'nilai' => 'info@housesales.com',
                'keterangan' => 'Email resmi kontak perusahaan',
            ],
            [
                'kunci' => 'telepon_kontak',
                'nilai' => '021-5551234',
                'keterangan' => 'Nomor telepon kantor pusat',
            ],
            [
                'kunci' => 'default_kpr_bunga',
                'nilai' => '8',
                'keterangan' => 'Bunga KPR default (%)',
            ],
            [
                'kunci' => 'default_cash_keras_diskon',
                'nilai' => '0',
                'keterangan' => 'Diskon default untuk cash keras (%)',
            ],
            [
                'kunci' => 'dp_subsidi_min_persen',
                'nilai' => '1',
                'keterangan' => 'Persentase DP subsidi minimum (%)',
            ],
            [
                'kunci' => 'dp_subsidi_max_persen',
                'nilai' => '5',
                'keterangan' => 'Persentase DP subsidi maksimum (%)',
            ],
            [
                'kunci' => 'dp_non_subsidi_min_persen',
                'nilai' => '10',
                'keterangan' => 'Persentase DP non-subsidi minimum (%)',
            ],
            [
                'kunci' => 'dp_non_subsidi_max_persen',
                'nilai' => '30',
                'keterangan' => 'Persentase DP non-subsidi maksimum (%)',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('pengaturan_sistem')->updateOrInsert(
                ['kunci' => $setting['kunci']],
                [
                    'nilai' => $setting['nilai'],
                    'keterangan' => $setting['keterangan'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
