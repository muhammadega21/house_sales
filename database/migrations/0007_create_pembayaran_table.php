<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_booking')->constrained('booking')->onDelete('cascade');
            $table->foreignId('id_konsumen')->constrained('konsumen')->onDelete('cascade');
            $table->enum('jenis_pembayaran', ['booking_fee', 'dp', 'cicilan', 'pelunasan']);
            $table->decimal('nominal', 18, 2);
            $table->date('tanggal_bayar');
            $table->enum('metode_bayar', ['transfer', 'tunai', 'debit', 'kpr'])->nullable();
            $table->string('no_referensi', 50)->nullable();
            $table->string('bukti_bayar', 255)->nullable();
            $table->enum('status_verifikasi', ['pending', 'diverifikasi', 'ditolak'])->default('pending');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_verifikasi')->nullable();
            $table->timestamps();

            $table->index(['id_booking', 'status_verifikasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
