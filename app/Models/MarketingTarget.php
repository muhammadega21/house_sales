<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingTarget extends Model
{
    protected $table = 'marketing_target';
    protected $fillable = ['id_marketing', 'periode_bulan', 'periode_tahun', 'target_unit', 'realisasi_unit', 'total_nilai_penjualan', 'total_komisi'];
    protected function casts(): array { return ['target_unit' => 'integer', 'realisasi_unit' => 'integer', 'total_nilai_penjualan' => 'decimal:2', 'total_komisi' => 'decimal:2']; }
    public function marketing(): BelongsTo { return $this->belongsTo(User::class, 'id_marketing'); }
}
