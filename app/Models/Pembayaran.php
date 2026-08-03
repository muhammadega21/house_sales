<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenisPembayaran;
use App\Enums\StatusVerifikasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'id_booking',
        'id_konsumen',
        'jenis_pembayaran',
        'nominal',
        'tanggal_bayar',
        'metode_bayar',
        'no_referensi',
        'bukti_bayar',
        'status_verifikasi',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'catatan_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'date',
            'tanggal_verifikasi' => 'date',
            'nominal' => 'decimal:2',
            'jenis_pembayaran' => JenisPembayaran::class,
            'status_verifikasi' => StatusVerifikasi::class,
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

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}