<?php

namespace App\Http\Controllers\LCAdmin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LCAdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        $totalConversations = LiveChatConversation::whereIn('channel_id', $channelIds)->count();
        $openConversations = LiveChatConversation::whereIn('channel_id', $channelIds)->where('status', 'open')->count();
        $pendingConversations = LiveChatConversation::whereIn('channel_id', $channelIds)->where('status', 'pending')->count();
        $closedConversations = LiveChatConversation::whereIn('channel_id', $channelIds)->where('status', 'closed')->count();
        $unreadMessages = LiveChatMessage::whereIn('conversation_id', function ($query) use ($channelIds) {
            $query->select('id')
                ->from('live_chat_conversations')
                ->whereIn('channel_id', $channelIds);
        })->where('sender_type', 'user')->whereNull('read_at')->count();

        $messagesToday = LiveChatMessage::whereIn('conversation_id', function ($query) use ($channelIds) {
            $query->select('id')
                ->from('live_chat_conversations')
                ->whereIn('channel_id', $channelIds);
        })->whereDate('created_at', Carbon::today())->count();

        $adminRepliesToday = LiveChatMessage::whereIn('conversation_id', function ($query) use ($channelIds) {
            $query->select('id')
                ->from('live_chat_conversations')
                ->whereIn('channel_id', $channelIds);
        })->where('sender_type', 'admin')->whereDate('created_at', Carbon::today())->count();

        $recentConversations = LiveChatConversation::whereIn('channel_id', $channelIds)
            ->with(['user', 'channel', 'lastMessage.sender'])
            ->where('status', '!=', 'closed')
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();

        $channels = $user->assignedOperators()->with('channel')->get()->pluck('channel')->unique('id');
        $channelStats = $channels->map(function ($ch) {
            $total = LiveChatConversation::where('channel_id', $ch->id)->count();
            $open = LiveChatConversation::where('channel_id', $ch->id)->where('status', 'open')->count();
            return [
                'channel' => $ch,
                'total' => $total,
                'open' => $open,
            ];
        });

        return view('admin.lcadmin.dashboard', compact(
            'totalConversations',
            'openConversations',
            'pendingConversations',
            'closedConversations',
            'unreadMessages',
            'messagesToday',
            'adminRepliesToday',
            'recentConversations',
            'channelStats'
        ));
    }
}
