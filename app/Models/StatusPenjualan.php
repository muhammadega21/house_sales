<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPenjualan as StatusPenjualanEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusPenjualan extends Model
{
    protected $table = 'status_penjualan';

    public $timestamps = false;

    protected $fillable = [
        'id_booking',
        'id_konsumen',
        'id_unit',
        'status_saat_ini',
        'tanggal_perubahan',
        'diubah_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perubahan' => 'datetime',
            'status_saat_ini' => StatusPenjualanEnum::class,
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'id_konsumen');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitRumah::class, 'id_unit');
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}