<?php

namespace App\Policies;

use App\Models\LiveChatOperator;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LiveChatOperatorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, LiveChatOperator $operator): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, LiveChatOperator $operator): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LiveChatOperator $operator): bool
    {
        return $user->isAdmin();
    }
}
