<?php

namespace Database\Factories;

use App\Models\Perumahan;
use App\Models\UnitRumah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitRumah>
 */
class UnitRumahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategori = fake()->randomElement(['subsidi', 'subsidi', 'non_subsidi']);
        $luasTanah = fake()->numberBetween(60, 120);

        return [
            'id_perumahan' => Perumahan::factory(),
            'kode_unit' => fake()->unique()->regexify('[A-Z]-[0-9]{2}'),
            'tipe_rumah' => fake()->randomElement(['36/60', '45/72', '60/84', '70/98']),
            'kategori' => $kategori,
            'jenis_ketersediaan' => fake()->randomElement(['ready_stock', 'ready_stock', 'ready_stock', 'indent']),
            'luas_tanah' => $luasTanah,
            'luas_bangunan' => fake()->numberBetween(36, min(90, $luasTanah)),
            'jumlah_kamar_tidur' => fake()->numberBetween(2, 4),
            'jumlah_kamar_mandi' => fake()->numberBetween(1, 2),
            'harga_jual' => $kategori === 'subsidi'
                ? fake()->numberBetween(150, 185) * 1_000_000
                : fake()->numberBetween(300, 700) * 1_000_000,
            'dp_minimum_persen' => $kategori === 'subsidi' ? 1 : 10,
            'status_unit' => 'tersedia',
        ];
    }
}
