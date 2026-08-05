<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $table = 'pengaturan_sistem';

    public $timestamps = false;

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'string',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = self::where('kunci', $key)->first();

        return $setting?->nilai ?? $default;
    }

    public static function getValues(array $keys): array
    {
        return self::whereIn('kunci', $keys)
            ->pluck('nilai', 'kunci')
            ->toArray();
    }
}
