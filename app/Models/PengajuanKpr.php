<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanKpr extends Model
{
    protected $table = 'pengajuan_kpr';

    protected $fillable = [
        'id_konsumen',
        'id_booking',
        'id_unit',
        'nama_bank',
        'plafon_kpr',
        'tenor_tahun',
        'suku_bunga',
        'tanggal_pengajuan',
        'status_pengajuan',
        'tanggal_keputusan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_keputusan' => 'date',
            'plafon_kpr' => 'decimal:2',
            'suku_bunga' => 'decimal:2',
        ];
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'id_konsumen');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitRumah::class, 'id_unit');
    }

    public function pengajuanKprHistory(): HasMany
    {
        return $this->hasMany(PengajuanKprHistory::class, 'id_pengajuan');
    }
}
