<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenisKelamin;
use App\Enums\StatusPernikahan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Konsumen extends Model
{
    protected $table = 'konsumen';

    protected $fillable = [
        'id_prospek',
        'id_marketing',
        'nama_lengkap',
        'nik',
        'no_kk',
        'no_hp',
        'email',
        'alamat_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_pernikahan',
        'pekerjaan',
        'nama_perusahaan',
        'penghasilan_bulanan',
        'npwp',
        'foto_ktp',
        'foto_kk',
    ];

    protected function casts(): array
    {
        return [
            'jenis_kelamin' => JenisKelamin::class,
            'status_pernikahan' => StatusPernikahan::class,
            'tanggal_lahir' => 'date',
            'penghasilan_bulanan' => 'decimal:2',
        ];
    }

    public function prospek(): BelongsTo
    {
        return $this->belongsTo(Prospek::class, 'id_prospek');
    }

    public function marketing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_marketing');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_konsumen');
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class, 'id_konsumen')->where('status_pembayaran_fee', '!=', 'refund');
    }

    public function dokumenKpr(): HasMany
    {
        return $this->hasMany(DokumenKpr::class, 'id_konsumen');
    }

    public function pengajuanKpr(): HasMany
    {
        return $this->hasMany(PengajuanKpr::class, 'id_konsumen');
    }

    public function simulasiPembayaran(): HasMany
    {
        return $this->hasMany(SimulasiPembayaran::class, 'id_konsumen');
    }
}
