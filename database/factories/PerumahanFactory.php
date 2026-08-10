<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Perumahan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Perumahan>
 */
class PerumahanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_perumahan' => 'Perumahan ' . fake()->unique()->randomElement([
                'Griya Sejahtera',
                'Bukit Permata',
                'Pesona Asri',
                'Taman Cemerlang',
                'Bumi Sriwijaya',
                'Permata Hijau',
                'Grand Ogan',
                'Musi Indah',
            ]),
            'alamat' => 'Jl. ' . fake()->streetName() . ' No. ' . fake()->buildingNumber(),
            'kota' => fake()->randomElement(['Palembang', 'Ogan Ilir', 'Banyuasin']),
            'provinsi' => 'Sumatera Selatan',
            'kode_pos' => fake()->numerify('3####'),
            'total_unit' => 0,
            'deskripsi' => 'Kawasan perumahan ' . fake()->randomElement(['subsidi', 'campuran', 'komersial']) . ' dengan fasilitas umum lengkap.',
            'status' => 'aktif',
        ];
    }
}
