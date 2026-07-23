<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'email',
        'no_hp',
        'role',
        'foto_profil',
        'status',
        'persentase_komisi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => Role::class,
            'persentase_komisi' => 'decimal:2',
        ];
    }

    public function prospek(): HasMany
    {
        return $this->hasMany(Prospek::class, 'id_marketing');
    }

    public function konsumen(): HasMany
    {
        return $this->hasMany(Konsumen::class, 'id_marketing');
    }

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_marketing');
    }

    public function marketingTarget(): HasMany
    {
        return $this->hasMany(MarketingTarget::class, 'id_marketing');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeMarketing($query)
    {
        return $query->where('role', Role::Marketing);
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', Role::Admin);
    }

    public function scopeManajemen($query)
    {
        return $query->where('role', Role::Manajemen);
    }
}

