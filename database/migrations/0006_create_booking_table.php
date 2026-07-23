<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking', 20)->unique()->comment('BK-YYYYMMDD-XXX');
            $table->foreignId('id_konsumen')->constrained('konsumen')->onDelete('cascade');
            $table->foreignId('id_unit')->constrained('unit_rumah')->onDelete('cascade');
            $table->foreignId('id_marketing')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_booking');
            $table->decimal('booking_fee', 15, 2);
            $table->enum('status_pembayaran_fee', ['belum_bayar', 'sudah_bayar', 'refund'])->default('belum_bayar');
            $table->date('tanggal_bayar_fee')->nullable();
            $table->enum('metode_bayar_fee', ['transfer', 'tunai', 'debit'])->nullable();
            $table->string('bukti_bayar_fee', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['id_konsumen', 'id_unit', 'tanggal_booking']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
