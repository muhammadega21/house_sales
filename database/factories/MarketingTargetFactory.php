<?php

namespace Database\Factories;

use App\Models\MarketingTarget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketingTarget>
 */
class MarketingTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'periode_bulan' => now()->month,
            'periode_tahun' => now()->year,
            'target_unit' => fake()->numberBetween(2, 5),
            'realisasi_unit' => 0,
            'total_nilai_penjualan' => 0,
            'total_komisi' => 0,
        ];
    }
}
