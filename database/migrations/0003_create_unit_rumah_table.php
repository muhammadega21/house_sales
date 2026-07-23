<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_rumah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perumahan')->constrained('perumahan')->onDelete('cascade');
            $table->string('kode_unit', 20)->comment('Blok/Nomor');
            $table->string('tipe_rumah', 50)->comment('36/60, 45/72, dll');
            $table->enum('kategori', ['subsidi', 'non_subsidi']);
            $table->enum('jenis_ketersediaan', ['ready_stock', 'indent']);
            $table->decimal('luas_tanah', 8, 2)->comment('m²');
            $table->decimal('luas_bangunan', 8, 2)->comment('m²');
            $table->integer('jumlah_kamar_tidur')->nullable();
            $table->integer('jumlah_kamar_mandi')->nullable();
            $table->decimal('harga_jual', 18, 2)->comment('Rupiah');
            $table->decimal('dp_minimum_persen', 5, 2)->nullable()->comment('%');
            $table->enum('status_unit', ['tersedia', 'dibooking', 'dijual', 'dibatalkan'])->default('tersedia');
            $table->string('foto_unit', 255)->nullable();
            $table->string('denah_unit', 255)->nullable();
            $table->date('tanggal_selesai_bangun')->nullable()->comment('Untuk indent');
            $table->timestamps();

            $table->unique(['id_perumahan', 'kode_unit']);
            $table->index(['id_perumahan', 'status_unit', 'kategori']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_rumah');
    }
};

