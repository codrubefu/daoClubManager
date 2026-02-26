<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $authUser): bool
    {
        return in_array($authUser->role, ['admin', 'coach'], true);
    }

    public function view(User $authUser, Group $group): bool
    {
        if ($authUser->role === 'admin') {
            return true;
        }

        if ($authUser->role !== 'coach') {
            return false;
        }

        return $group->coaches()->where('users.id', $authUser->id)->exists();
    }

    public function create(User $authUser): bool
    {
        return $authUser->role === 'admin';
    }

    public function update(User $authUser, Group $group): bool
    {
        return $authUser->role === 'admin';
    }

    public function delete(User $authUser, Group $group): bool
    {
        return $authUser->role === 'admin';
    }

    public function assignCoach(User $authUser, Group $group): bool
    {
        return $authUser->role === 'admin';
    }

    public function assignStudent(User $authUser, Group $group): bool
    {
        return $authUser->role === 'admin';
    }
}
