<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_kpr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_konsumen')->constrained('konsumen')->onDelete('cascade');
            $table->enum('jenis_dokumen', [
                'ktp', 'kk', 'npwp', 'slip_gaji', 'rekening_koran',
                'surat_kerja', 'surat_nikah', 'surat_keterangan_penghasilan',
                'formulir_kpr', 'lainnya'
            ]);
            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->integer('ukuran_file')->nullable()->comment('bytes');
            $table->string('tipe_file', 20)->nullable()->comment('pdf/jpg/png');
            $table->enum('status_verifikasi', ['belum_diverifikasi', 'valid', 'tidak_valid', 'perlu_revisi'])->default('belum_diverifikasi');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('diupload_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamp('tanggal_upload')->useCurrent();
            $table->timestamp('tanggal_verifikasi')->nullable();

            $table->index(['id_konsumen', 'status_verifikasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_kpr');
    }
};
