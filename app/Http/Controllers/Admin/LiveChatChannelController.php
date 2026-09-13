<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatChannel;
use Illuminate\Http\Request;

class LiveChatChannelController extends Controller
{
    public function index()
    {
        $channels = LiveChatChannel::with(['operators.user', 'conversations' => function ($q) {
            $q->where('status', '!=', 'closed');
        }])->ordered()->get();

        return view('admin.live-chat.channels.index', compact('channels'));
    }

    public function update(Request $request, LiveChatChannel $channel)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $channel->update($request->only(['name', 'description', 'is_active']));

        return response()->json(['success' => true, 'message' => 'Channel berhasil diupdate']);
    }

    public function toggle(LiveChatChannel $channel)
    {
        $channel->update(['is_active' => !$channel->is_active]);

        return response()->json(['success' => true, 'is_active' => $channel->is_active]);
    }
}
