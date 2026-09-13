<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatChannel;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveChatConversationController extends Controller
{
    public function index(Request $request)
    {
        $query = LiveChatConversation::with(['user', 'channel', 'lastMessage.sender']);

        if ($request->filled('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $conversations = $query->orderBy('last_message_at', 'desc')->paginate(20);

        $channels = LiveChatChannel::active()->ordered()->get();

        return view('admin.live-chat.conversations.index', compact('conversations', 'channels'));
    }

    public function show(LiveChatConversation $conversation)
    {
        $conversation->load(['user', 'channel', 'lastMessage.sender']);

        $conversation->markAdminRead();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->with(['sender', 'replyTo'])
            ->ordered()
            ->get();

        $activeOperator = $conversation->channel->getActiveOperator();

        return view('admin.live-chat.conversations.show', compact('conversation', 'messages', 'activeOperator'));
    }

    public function poll(LiveChatConversation $conversation, Request $request)
    {
        $afterId = $request->input('after', 0);

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->with(['sender', 'replyTo'])
            ->ordered()
            ->get();

        $conversation->markAdminRead();

        return response()->json($messages);
    }

    public function reply(Request $request, LiveChatConversation $conversation)
    {
        $request->validate([
            'message_type' => 'required|in:text,image,video',
            'message' => 'nullable|string|max:5000',
            'reply_to_message_id' => 'nullable|exists:live_chat_messages,id',
            'media' => 'required_if:message_type,image,video|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi',
        ]);

        $userId = Auth::id();
        $senderType = Auth::user()->isAdmin() ? 'admin' : 'admin';

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'sender_type' => $senderType,
            'message_type' => $request->message_type,
            'message' => $request->message,
            'reply_to_message_id' => $request->reply_to_message_id,
        ];

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $path = $file->store('live-chat/media', 'public');
            $data['media_path'] = $path;
            $data['media_name'] = $file->getClientOriginalName();
            $data['media_mime'] = $file->getMimeType();
            $data['media_size'] = $file->getSize();
        }

        $message = LiveChatMessage::create($data);

        $conversation->update([
            'last_message_at' => now(),
            'user_unread_count' => $conversation->user_unread_count + 1,
        ]);

        $message->load('sender', 'replyTo');

        return response()->json($message);
    }

    public function close(LiveChatConversation $conversation)
    {
        $conversation->update(['status' => 'closed']);

        return response()->json(['success' => true, 'message' => 'Conversation ditutup']);
    }

    public function reopen(LiveChatConversation $conversation)
    {
        $conversation->update(['status' => 'open']);

        return response()->json(['success' => true, 'message' => 'Conversation dibuka kembali']);
    }
}
