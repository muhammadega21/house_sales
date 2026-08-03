<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenKpr extends Model
{
    protected $table = 'dokumen_kpr';

    public $timestamps = false;

    protected $fillable = [
        'id_konsumen',
        'jenis_dokumen',
        'nama_file',
        'path_file',
        'ukuran_file',
        'tipe_file',
        'status_verifikasi',
        'catatan_verifikasi',
        'diupload_oleh',
        'tanggal_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'datetime',
            'tanggal_verifikasi' => 'datetime',
        ];
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'id_konsumen');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diupload_oleh');
    }
}