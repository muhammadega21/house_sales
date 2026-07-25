<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusProspek;
use App\Enums\SumberProspek;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prospek extends Model
{
    protected $table = 'prospek';

    protected $fillable = [
        'id_marketing',
        'nama_prospek',
        'no_hp',
        'email',
        'sumber_prospek',
        'catatan',
        'status_prospek',
        'tanggal_prospek',
    ];

    protected function casts(): array
    {
        return [
            'sumber_prospek' => SumberProspek::class,
            'status_prospek' => StatusProspek::class,
            'tanggal_prospek' => 'date',
        ];
    }

    public function marketing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_marketing');
    }

    public function konsumen(): HasOne
    {
        return $this->hasOne(Konsumen::class, 'id_prospek');
    }
}
