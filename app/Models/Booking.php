<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPembayaranFee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
            'status_pembayaran_fee' => StatusPembayaranFee::class,
        ];
    }

    public function scopeStatusPembayaran($query, ?string $status)
    {
        if (!$status) return $query;

        return $query->where('status_pembayaran_fee', $status);
    }

    public function scopeStatusPenjualan($query, ?string $status)
    {
        if (!$status) return $query;

        return $query->whereHas('statusHistory', function ($q) use ($status) {
            $q->where('status_sesudah', $status);
        });
    }

    public function scopePerumahan($query, ?int $idPerumahan)
    {
        if (!$idPerumahan) return $query;

        return $query->whereHas('unit', function ($q) use ($idPerumahan) {
            $q->where('id_perumahan', $idPerumahan);
        });
    }

    public function scopeKategori($query, ?string $kategori)
    {
        if (!$kategori) return $query;

        return $query->whereHas('unit', function ($q) use ($kategori) {
            $q->where('kategori', $kategori);
        });
    }

    public function scopeMarketing($query, ?int $idMarketing)
    {
        if (!$idMarketing) return $query;

        return $query->where('id_marketing', $idMarketing);
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

    public function statusPenjualan(): HasOne
    {
        return $this->hasOne(StatusPenjualan::class, 'id_booking');
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