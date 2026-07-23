<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_booking')->constrained('booking')->onDelete('cascade');
            $table->foreignId('id_konsumen')->constrained('konsumen')->onDelete('cascade');
            $table->foreignId('id_unit')->constrained('unit_rumah')->onDelete('cascade');
            $table->enum('status_saat_ini', ['prospek', 'booking', 'pengajuan_kpr', 'akad', 'serah_terima', 'batal']);
            $table->timestamp('tanggal_perubahan')->useCurrent();
            $table->foreignId('diubah_oleh')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();

            $table->index(['id_booking', 'status_saat_ini']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_penjualan');
    }
};
