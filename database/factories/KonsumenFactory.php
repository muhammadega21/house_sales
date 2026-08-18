<?php

namespace Database\Factories;

use App\Models\Konsumen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Konsumen>
 */
class KonsumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'nik' => fake()->unique()->numerify('################'),
            'no_kk' => fake()->unique()->numerify('################'),
            'no_hp' => '08' . fake()->unique()->numerify('##########'),
            'email' => fake()->optional()->safeEmail(),
            'alamat_lengkap' => 'Jl. ' . fake()->streetName() . ' No. ' . fake()->buildingNumber() . ', RT ' . fake()->numerify('##') . ', Palembang',
            'tempat_lahir' => fake()->randomElement(['Palembang', 'Prabumulih', 'Lubuklinggau']),
            'tanggal_lahir' => fake()->dateTimeBetween('1975-01-01', '2000-12-31')->format('Y-m-d'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'status_pernikahan' => fake()->randomElement(['belum_menikah', 'menikah', 'menikah']),
            'pekerjaan' => fake()->randomElement(['Karyawan Swasta', 'PNS', 'Wiraswasta', 'Guru', 'Perawat']),
            'nama_perusahaan' => fake()->optional()->company(),
            'penghasilan_bulanan' => fake()->numberBetween(3, 15) * 1_000_000,
            'npwp' => fake()->optional()->numerify('###############'),
        ];
    }
}
