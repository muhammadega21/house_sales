<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        return $user->id === $booking->id_marketing;
    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        return $user->id === $booking->id_marketing;
    }

    public function delete(User $user, Booking $booking): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        return $user->id === $booking->id_marketing;
    }
}