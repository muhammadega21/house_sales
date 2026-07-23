<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulasi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_konsumen')->nullable()->constrained('konsumen')->onDelete('set null');
            $table->foreignId('id_unit')->constrained('unit_rumah')->onDelete('cascade');
            $table->foreignId('id_marketing')->constrained('users')->onDelete('cascade');
            $table->enum('metode_pembayaran', ['kpr', 'cash_bertahap', 'cash_keras']);
            $table->decimal('harga_rumah', 18, 2);
            $table->decimal('dp_persen', 5, 2)->nullable();
            $table->decimal('dp_nominal', 18, 2)->nullable();
            $table->integer('tenor_tahun')->nullable();
            $table->decimal('suku_bunga', 5, 2)->nullable();
            $table->decimal('cicilan_bulanan', 15, 2)->nullable();
            $table->decimal('total_pembayaran', 18, 2)->nullable();
            $table->decimal('total_bunga', 18, 2)->nullable();
            $table->timestamp('tanggal_simulasi')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulasi_pembayaran');
    }
};
