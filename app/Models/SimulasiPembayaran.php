<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulasiPembayaran extends Model
{
    protected $table = 'simulasi_pembayaran';

    public $timestamps = false;

    protected $fillable = [
        'id_konsumen',
        'id_unit',
        'id_marketing',
        'metode_pembayaran',
        'harga_rumah',
        'dp_persen',
        'dp_nominal',
        'tenor_tahun',
        'suku_bunga',
        'cicilan_bulanan',
        'total_pembayaran',
        'total_bunga',
    ];

    protected function casts(): array
    {
        return [
            'harga_rumah' => 'decimal:2',
            'dp_nominal' => 'decimal:2',
            'cicilan_bulanan' => 'decimal:2',
            'total_pembayaran' => 'decimal:2',
            'total_bunga' => 'decimal:2',
            'dp_persen' => 'decimal:2',
            'suku_bunga' => 'decimal:2',
            'tanggal_simulasi' => 'datetime',
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
}