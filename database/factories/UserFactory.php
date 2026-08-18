<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $namaLengkap = $firstName . ' ' . $lastName;

        return [
            'nama_lengkap' => $namaLengkap,
            'username' => $this->generateUniqueUsername($firstName, $lastName),
            'password' => static::$password ??= Hash::make('password123'),
            'email' => fake()->unique()->safeEmail(),
            'no_hp' => '08' . fake()->unique()->numerify('##########'),
            'role' => fake()->randomElement([Role::Marketing, Role::Admin, Role::Manajemen]),
            'foto_profil' => null,
            'status' => 'aktif',
            'persentase_komisi' => fake()->randomFloat(2, 1.5, 3.0),
            'total_komisi_earned' => 0,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Generate username unik: firstname.lastname + angka random jika duplikat
     */
    private function generateUniqueUsername(string $firstName, string $lastName): string
    {
        $base = strtolower($firstName) . '.' . strtolower($lastName);
        $base = preg_replace('/[^a-z0-9]/', '', $base);
        $base = substr($base, 0, 40);

        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    // ========== STATE METHODS ==========

    /** Marketing aktif dengan komisi 2% */
    public function marketing(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => Role::Marketing,
            'persentase_komisi' => 2.00,
        ]);
    }

    /** Admin aktif */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => Role::Admin,
            'persentase_komisi' => 0,
        ]);
    }

    /** Manajemen aktif */
    public function manajemen(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => Role::Manajemen,
            'persentase_komisi' => 0,
        ]);
    }

    /** Non-aktif */
    public function nonAktif(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'non_aktif',
        ]);
    }

    /** Dengan password kustom */
    public function withPassword(string $password): static
    {
        return $this->state(fn(array $attributes) => [
            'password' => Hash::make($password),
        ]);
    }
}
