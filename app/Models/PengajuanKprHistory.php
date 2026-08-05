<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanKprHistory extends Model
{
    protected $table = 'pengajuan_kpr_history';

    protected $fillable = [
        'id_pengajuan',
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

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanKpr::class, 'id_pengajuan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
