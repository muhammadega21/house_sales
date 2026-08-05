<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'aksi',
        'entitas',
        'entitas_id',
        'deskripsi',
        'data_lama',
        'data_baru',
        'ip_address',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
