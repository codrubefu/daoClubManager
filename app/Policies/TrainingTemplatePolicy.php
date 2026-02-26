<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TrainingTemplate;
use App\Models\User;

class TrainingTemplatePolicy
{
    public function viewAny(User $authUser): bool
    {
        return in_array($authUser->role, ['admin', 'coach'], true);
    }

    public function view(User $authUser, TrainingTemplate $template): bool
    {
        return in_array($authUser->role, ['admin', 'coach'], true);
    }

    public function create(User $authUser): bool
    {
        return $authUser->role === 'admin';
    }

    public function update(User $authUser, TrainingTemplate $template): bool
    {
        return $authUser->role === 'admin';
    }

    public function delete(User $authUser, TrainingTemplate $template): bool
    {
        return $authUser->role === 'admin';
    }
}
