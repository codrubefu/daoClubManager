<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authUser): bool
    {
        return in_array($authUser->role, ['admin', 'coach'], true);
    }

    public function create(User $authUser): bool
    {
        return $authUser->role === 'admin';
    }
}
