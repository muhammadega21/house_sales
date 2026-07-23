<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perumahan extends Model
{
    use HasFactory;

    protected $table = 'perumahan';

    protected $fillable = [
        'nama_perumahan',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'latitude',
        'longitude',
        'total_unit',
        'deskripsi',
        'foto_kawasan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'total_unit' => 'integer',
        ];
    }

    public function unitRumah(): HasMany
    {
        return $this->hasMany(UnitRumah::class, 'id_perumahan');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
