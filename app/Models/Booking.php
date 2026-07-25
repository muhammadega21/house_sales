<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'kode_booking',
        'id_konsumen',
        'id_unit',
        'id_marketing',
        'tanggal_booking',
        'booking_fee',
        'status_pembayaran_fee',
        'tanggal_bayar_fee',
        'metode_bayar_fee',
        'bukti_bayar_fee',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_booking' => 'date',
            'tanggal_bayar_fee' => 'date',
            'booking_fee' => 'decimal:2',
        ];
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'id_konsumen');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitRumah::class, 'id_unit');
    }

    public function marketing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_marketing');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_booking');
    }

    public function pengajuanKpr(): HasOne
    {
        return $this->hasOne(PengajuanKpr::class, 'id_booking');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(StatusHistory::class, 'id_booking');
    }
}