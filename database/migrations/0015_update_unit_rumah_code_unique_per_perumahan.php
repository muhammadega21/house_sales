<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $legacyIndex = collect(Schema::getIndexes('unit_rumah'))
            ->first(fn (array $index): bool => $index['name'] === 'unit_rumah_kode_unit_unique');

        if ($legacyIndex) {
            Schema::table('unit_rumah', function (Blueprint $table): void {
                $table->dropUnique('unit_rumah_kode_unit_unique');
                $table->unique(['id_perumahan', 'kode_unit']);
            });
        }
    }

    public function down(): void
    {
        $compoundIndex = collect(Schema::getIndexes('unit_rumah'))
            ->first(fn (array $index): bool => $index['name'] === 'unit_rumah_id_perumahan_kode_unit_unique');

        if ($compoundIndex) {
            Schema::table('unit_rumah', function (Blueprint $table): void {
                $table->dropUnique('unit_rumah_id_perumahan_kode_unit_unique');
                $table->unique('kode_unit');
            });
        }
    }
};
