<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Konsumen;
use App\Models\Prospek;
use App\Models\StatusPenjualan;
use App\Observers\BookingObserver;
use App\Policies\BookingPolicy;
use App\Policies\KonsumenPolicy;
use App\Policies\ProspekPolicy;
use App\Policies\StatusPenjualanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Booking::observe(BookingObserver::class);

        Gate::policy(Prospek::class, ProspekPolicy::class);
        Gate::policy(Konsumen::class, KonsumenPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(StatusPenjualan::class, StatusPenjualanPolicy::class);
    }
}
