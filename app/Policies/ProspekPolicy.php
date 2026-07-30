<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Prospek;
use App\Models\User;

class ProspekPolicy
{
    public function view(User $user, Prospek $prospek): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        return $user->id === $prospek->id_marketing;
    }

    public function update(User $user, Prospek $prospek): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        return $user->id === $prospek->id_marketing;
    }

    public function delete(User $user, Prospek $prospek): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        if ($prospek->status_prospek->value === 'jadi_konsumen') {
            return false;
        }

        return $user->id === $prospek->id_marketing;
    }

    public function convert(User $user, Prospek $prospek): bool
    {
        if ($user->role->value === 'admin') {
            return true;
        }

        if ($prospek->status_prospek->value !== 'berminat') {
            return false;
        }

        return $user->id === $prospek->id_marketing;
    }
}