<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_kpr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_konsumen')->constrained('konsumen')->onDelete('cascade');
            $table->foreignId('id_booking')->constrained('booking')->onDelete('cascade');
            $table->foreignId('id_unit')->constrained('unit_rumah')->onDelete('cascade');
            $table->string('nama_bank', 100)->nullable();
            $table->decimal('plafon_kpr', 18, 2)->nullable();
            $table->integer('tenor_tahun')->nullable();
            $table->decimal('suku_bunga', 5, 2)->nullable();
            $table->date('tanggal_pengajuan');
            $table->enum('status_pengajuan', ['draft', 'diajukan', 'verifikasi_bank', 'disetujui', 'ditolak', 'akad', 'batal'])->default('draft');
            $table->date('tanggal_keputusan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kpr');
    }
};
