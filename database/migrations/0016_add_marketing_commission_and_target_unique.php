<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->decimal('persentase_komisi', 5, 2)->default(0)->after('status');
        });
        Schema::table('marketing_target', function (Blueprint $table): void {
            $table->unique(['id_marketing', 'periode_bulan', 'periode_tahun']);
        });
    }
    public function down(): void
    {
        Schema::table('marketing_target', function (Blueprint $table): void { $table->dropUnique(['id_marketing', 'periode_bulan', 'periode_tahun']); });
        Schema::table('users', function (Blueprint $table): void { $table->dropColumn('persentase_komisi'); });
    }
};
