<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_booking')->constrained('booking')->onDelete('cascade');
            $table->enum('status_sebelum', ['prospek', 'booking', 'pengajuan_kpr', 'akad', 'serah_terima', 'batal'])->nullable();
            $table->enum('status_sesudah', ['prospek', 'booking', 'pengajuan_kpr', 'akad', 'serah_terima', 'batal']);
            $table->text('catatan')->nullable();
            $table->foreignId('diubah_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_history');
    }
};
