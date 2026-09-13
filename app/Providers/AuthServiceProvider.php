<?php

namespace App\Providers;

use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Models\LiveChatOperator;
use App\Models\User;
use App\Policies\LiveChatConversationPolicy;
use App\Policies\LiveChatMessagePolicy;
use App\Policies\LiveChatOperatorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        LiveChatConversation::class => LiveChatConversationPolicy::class,
        LiveChatMessage::class => LiveChatMessagePolicy::class,
        LiveChatOperator::class => LiveChatOperatorPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
