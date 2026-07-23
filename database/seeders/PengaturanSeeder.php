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
