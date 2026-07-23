<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perumahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perumahan', 150)->comment('Nama proyek perumahan');
            $table->text('alamat')->comment('Alamat lengkap perumahan');
            $table->string('kota', 50)->comment('Kota/Kabupaten');
            $table->string('provinsi', 50)->comment('Provinsi');
            $table->string('kode_pos', 10)->nullable()->comment('Kode pos');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Koordinat latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Koordinat longitude');
            $table->integer('total_unit')->default(0)->comment('Total unit rumah di perumahan ini');
            $table->text('deskripsi')->nullable()->comment('Deskripsi perumahan');
            $table->string('foto_kawasan', 255)->nullable()->comment('Path foto kawasan/masterplan');
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif')->comment('Status operasional perumahan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perumahan');
    }
};
