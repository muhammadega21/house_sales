<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingNotification extends Model
{
    protected $table = 'marketing_notifications';

    protected $fillable = [
        'id_marketing',
        'title',
        'message',
        'type',
        'data',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function marketing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_marketing');
    }
}
