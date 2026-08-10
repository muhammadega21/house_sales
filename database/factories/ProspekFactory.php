<?php

namespace Database\Factories;

use App\Models\Prospek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prospek>
 */
class ProspekFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_prospek' => fake()->name(),
            'no_hp' => '08' . fake()->unique()->numerify('##########'),
            'email' => fake()->optional()->safeEmail(),
            'sumber_prospek' => fake()->randomElement(['facebook', 'instagram', 'tiktok', 'walk_in', 'referral', 'lainnya']),
            'catatan' => fake()->optional()->sentence(8),
            'status_prospek' => 'baru',
            'tanggal_prospek' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }
}
