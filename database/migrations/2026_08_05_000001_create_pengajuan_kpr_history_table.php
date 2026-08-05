<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_kpr_history', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_pengajuan');
            $table->enum('status_sebelum', ['draft', 'diajukan', 'verifikasi_bank', 'disetujui', 'ditolak', 'akad', 'batal']);
            $table->enum('status_sesudah', ['draft', 'diajukan', 'verifikasi_bank', 'disetujui', 'ditolak', 'akad', 'batal']);
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('diubah_oleh');
            $table->timestamps();

            $table->foreign('id_pengajuan')->references('id')->on('pengajuan_kpr')->onDelete('cascade');
            $table->foreign('diubah_oleh')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kpr_history');
    }
};
