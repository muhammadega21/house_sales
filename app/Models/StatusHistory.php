<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusHistory extends Model
{
    protected $table = 'status_history';

    protected $fillable = [
        'id_booking',
        'status_sebelum',
        'status_sesudah',
        'catatan',
        'diubah_oleh',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function getStatusSebelumLabel(): string
    {
        return $this->status_sebelum
            ? \App\Enums\StatusPenjualan::from($this->status_sebelum)->label()
            : 'Awal';
    }

    public function getStatusSesudahLabel(): string
    {
        return \App\Enums\StatusPenjualan::from($this->status_sesudah)->label();
    }
}
