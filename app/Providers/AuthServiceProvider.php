<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Konsumen;
use App\Policies\KonsumenPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Konsumen::class => KonsumenPolicy::class,
    ];

    public function boot(): void
    {
        parent::boot();
    }
}