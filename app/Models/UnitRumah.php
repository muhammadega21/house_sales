<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenisKetersediaan;
use App\Enums\KategoriRumah;
use App\Enums\StatusUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitRumah extends Model
{
    use HasFactory;

    protected $table = 'unit_rumah';

    protected $fillable = [
        'id_perumahan',
        'kode_unit',
        'tipe_rumah',
        'kategori',
        'jenis_ketersediaan',
        'luas_tanah',
        'luas_bangunan',
        'jumlah_kamar_tidur',
        'jumlah_kamar_mandi',
        'harga_jual',
        'dp_minimum_persen',
        'status_unit',
        'foto_unit',
        'denah_unit',
        'tanggal_selesai_bangun',
    ];

    protected function casts(): array
    {
        return [
            'kategori' => KategoriRumah::class,
            'jenis_ketersediaan' => JenisKetersediaan::class,
            'status_unit' => StatusUnit::class,
            'luas_tanah' => 'decimal:2',
            'luas_bangunan' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'dp_minimum_persen' => 'decimal:2',
            'jumlah_kamar_tidur' => 'integer',
            'jumlah_kamar_mandi' => 'integer',
            'tanggal_selesai_bangun' => 'date',
        ];
    }

    public function perumahan(): BelongsTo
    {
        return $this->belongsTo(Perumahan::class, 'id_perumahan');
    }

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_unit');
    }

    public function scopeTersedia($query)
    {
        return $query->where('status_unit', StatusUnit::Tersedia);
    }

    public function scopeSubsidi($query)
    {
        return $query->where('kategori', KategoriRumah::Subsidi);
    }

    public function scopeNonSubsidi($query)
    {
        return $query->where('kategori', KategoriRumah::NonSubsidi);
    }

    public function scopeReadyStock($query)
    {
        return $query->where('jenis_ketersediaan', JenisKetersediaan::ReadyStock);
    }

    public function scopeIndent($query)
    {
        return $query->where('jenis_ketersediaan', JenisKetersediaan::Indent);
    }
}
