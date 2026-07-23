<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_prospek')->nullable()->constrained('prospek')->onDelete('set null');
            $table->foreignId('id_marketing')->constrained('users')->onDelete('cascade');
            $table->string('nama_lengkap', 100)->comment('Sesuai KTP');
            $table->string('nik', 16)->unique();
            $table->string('no_kk', 16)->nullable();
            $table->string('no_hp', 15);
            $table->string('email', 100)->nullable();
            $table->text('alamat_lengkap');
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->enum('status_pernikahan', ['belum_menikah', 'menikah', 'cerai_hidup', 'cerai_mati'])->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('nama_perusahaan', 100)->nullable();
            $table->decimal('penghasilan_bulanan', 15, 2)->nullable();
            $table->string('npwp', 15)->nullable();
            $table->string('foto_ktp', 255)->nullable();
            $table->string('foto_kk', 255)->nullable();
            $table->timestamps();

            $table->index('nik');
            $table->index('id_marketing');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsumen');
    }
};
