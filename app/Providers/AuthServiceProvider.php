<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\Prospek;
use App\Models\StatusPenjualan;
use App\Policies\BookingPolicy;
use App\Policies\KonsumenPolicy;
use App\Policies\ProspekPolicy;
use App\Policies\StatusPenjualanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Prospek::class => ProspekPolicy::class,
        Konsumen::class => KonsumenPolicy::class,
        Booking::class => BookingPolicy::class,
        StatusPenjualan::class => StatusPenjualanPolicy::class,
    ];
}
