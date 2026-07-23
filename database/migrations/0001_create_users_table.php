<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 100)->comment('Nama lengkap user');
            $table->string('username', 50)->unique()->comment('Username untuk login');
            $table->string('password')->comment('Bcrypt hash password');
            $table->string('email', 100)->unique()->nullable()->comment('Email user');
            $table->string('no_hp', 15)->nullable()->comment('Nomor handphone');
            $table->enum('role', ['admin', 'marketing', 'manajemen'])->comment('Peran user dalam sistem');
            $table->string('foto_profil', 255)->nullable()->comment('Path foto profil');
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif')->comment('Status keaktifan user');
            $table->rememberToken()->comment('Token untuk remember me');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
