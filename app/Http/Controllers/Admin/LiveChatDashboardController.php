<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatChannel;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Models\LiveChatOperator;
use Illuminate\Http\Request;

class LiveChatDashboardController extends Controller
{
    public function index()
    {
        $totalConversations = LiveChatConversation::count();
        $openConversations = LiveChatConversation::where('status', 'open')->count();
        $pendingConversations = LiveChatConversation::where('status', 'pending')->count();
        $totalUnread = LiveChatConversation::sum('admin_unread_count');
        $onlineOperators = LiveChatOperator::active()->get()->filter->isOnDuty()->count();
        $offlineOperators = LiveChatOperator::active()->count() - $onlineOperators;

        $conversationsPerChannel = LiveChatChannel::withCount(['conversations' => function ($q) {
            $q->where('status', '!=', 'closed');
        }])->ordered()->get();

        $recentConversations = LiveChatConversation::with(['user', 'channel', 'lastMessage'])
            ->where('status', '!=', 'closed')
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.live-chat.dashboard', compact(
            'totalConversations',
            'openConversations',
            'pendingConversations',
            'totalUnread',
            'onlineOperators',
            'offlineOperators',
            'conversationsPerChannel',
            'recentConversations'
        ));
    }
}
