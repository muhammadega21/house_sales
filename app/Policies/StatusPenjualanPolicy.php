<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\StatusPenjualan;
use App\Models\User;

class StatusPenjualanPolicy
{
    public function view(User $user, StatusPenjualan $statusPenjualan): bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        $booking = $statusPenjualan->booking;

        return $booking && $booking->id_marketing === $user->id;
    }

    public function update(User $user, StatusPenjualan $statusPenjualan): bool
    {
        if ($user->role === Role::Admin) {
            return true;
        }

        $booking = $statusPenjualan->booking;

        return $booking && $booking->id_marketing === $user->id;
    }
}
