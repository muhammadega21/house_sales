<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin Default
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Administrator Utama',
                'password' => Hash::make('password123'),
                'email' => 'admin@housesales.com',
                'no_hp' => '081234567890',
                'role' => Role::Admin,
                'status' => 'aktif',
            ]
        );

        // 2. Marketing 1
        User::updateOrCreate(
            ['username' => 'marketing1'],
            [
                'nama_lengkap' => 'Budi Marketing',
                'password' => Hash::make('password123'),
                'email' => 'budi@housesales.com',
                'no_hp' => '081234567891',
                'role' => Role::Marketing,
                'status' => 'aktif',
            ]
        );

        // 3. Marketing 2
        User::updateOrCreate(
            ['username' => 'marketing2'],
            [
                'nama_lengkap' => 'Siti Marketing',
                'password' => Hash::make('password123'),
                'email' => 'siti@housesales.com',
                'no_hp' => '081234567892',
                'role' => Role::Marketing,
                'status' => 'aktif',
            ]
        );

        // 4. Manajemen Default
        User::updateOrCreate(
            ['username' => 'manajemen'],
            [
                'nama_lengkap' => 'Manager Executive',
                'password' => Hash::make('password123'),
                'email' => 'manajemen@housesales.com',
                'no_hp' => '081234567893',
                'role' => Role::Manajemen,
                'status' => 'aktif',
            ]
        );
    }
}
