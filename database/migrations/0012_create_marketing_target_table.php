<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_marketing')->constrained('users')->onDelete('cascade');
            $table->integer('periode_bulan')->comment('1-12');
            $table->integer('periode_tahun');
            $table->integer('target_unit');
            $table->integer('realisasi_unit')->default(0);
            $table->decimal('total_nilai_penjualan', 18, 2)->default(0);
            $table->decimal('total_komisi', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['id_marketing', 'periode_bulan', 'periode_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_target');
    }
};
