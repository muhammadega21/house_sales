<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Konsumen;
use App\Models\User;

class KonsumenPolicy
{
    public function view(User $user, Konsumen $konsumen): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        if ($user->role->value === 'marketing') {
            return $user->id === $konsumen->id_marketing;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role->value, ['admin', 'marketing'], true);
    }

    public function update(User $user, Konsumen $konsumen): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        if ($user->role->value === 'marketing') {
            return $user->id === $konsumen->id_marketing;
        }

        return false;
    }

    public function delete(User $user, Konsumen $konsumen): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        return false;
    }
}