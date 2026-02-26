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

    public function view(User $authUser, User $targetUser): bool
    {
        if (in_array($authUser->role, ['admin', 'coach'], true)) {
            return true;
        }

        if ($authUser->id === $targetUser->id) {
            return true;
        }

        if ($authUser->role !== 'parent' || $targetUser->role !== 'student') {
            return false;
        }

        return $authUser->children()->whereKey($targetUser->id)->exists();
    }

    public function create(User $authUser): bool
    {
        return $authUser->role === 'admin';
    }

    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->role === 'admin' && $authUser->id !== $targetUser->id;
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->role === 'admin' && $authUser->id !== $targetUser->id;
    }

    public function viewChildren(User $authUser, User $parent): bool
    {
        if (in_array($authUser->role, ['admin', 'coach'], true)) {
            return true;
        }

        return $authUser->role === 'parent' && $authUser->id === $parent->id;
    }

    public function linkChild(User $authUser, User $parent, User $student): bool
    {
        return $authUser->role === 'admin'
            && $parent->role === 'parent'
            && $student->role === 'student';
    }

    public function unlinkChild(User $authUser, User $parent, User $student): bool
    {
        return $this->linkChild($authUser, $parent, $student);
    }
}
