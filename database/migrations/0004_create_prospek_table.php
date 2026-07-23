<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_marketing')->constrained('users')->onDelete('cascade');
            $table->string('nama_prospek', 100);
            $table->string('no_hp', 15);
            $table->string('email', 100)->nullable();
            $table->enum('sumber_prospek', ['facebook', 'instagram', 'tiktok', 'walk_in', 'referral', 'lainnya'])->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status_prospek', ['baru', 'dihubungi', 'berminat', 'tidak_berminat', 'jadi_konsumen'])->default('baru');
            $table->date('tanggal_prospek');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospek');
    }
};
