<?php

namespace App\Policies;

use App\Models\LiveChatMessage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LiveChatMessagePolicy
{
    use HandlesAuthorization;

    public function view(User $user, LiveChatMessage $message): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $conversation = $message->conversation;

        if ($user->isLiveChatAdmin()) {
            return $conversation->channel->operators()
                ->where('user_id', $user->id)
                ->exists();
        }

        return $conversation->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, LiveChatMessage $message): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($message->sender_id === $user->id) {
            return true;
        }

        if ($user->isLiveChatAdmin()) {
            return $message->conversation->channel->operators()
                ->where('user_id', $user->id)
                ->exists();
        }

        return false;
    }
}
