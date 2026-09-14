<?php

namespace App\Http\Controllers\LCAdmin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LCAdminConversationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        $query = LiveChatConversation::with(['user', 'channel', 'lastMessage.sender'])
            ->whereIn('channel_id', $channelIds);

        if ($request->filled('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conversations = $query->orderBy('last_message_at', 'desc')->paginate(20);

        $channels = $user->assignedOperators()->with('channel')->get()->pluck('channel')->unique('id');

        return view('admin.lcadmin.conversations.index', compact('conversations', 'channels'));
    }

    public function show(LiveChatConversation $conversation)
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            abort(403);
        }

        $conversation->load(['user', 'channel', 'lastMessage.sender']);
        $conversation->markAdminRead();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->with(['sender', 'replyTo'])
            ->ordered()
            ->get();

        $activeOperator = $conversation->channel->getActiveOperator();

        return view('admin.lcadmin.conversations.show', compact('conversation', 'messages', 'activeOperator'));
    }

    public function loadMessages(LiveChatConversation $conversation): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->load(['user', 'channel']);
        $conversation->markAdminRead();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->with(['sender', 'replyTo'])
            ->ordered()
            ->get();

        $activeOperator = $conversation->channel->getActiveOperator();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
            'activeOperator' => $activeOperator,
        ]);
    }

    public function poll(LiveChatConversation $conversation, Request $request): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $afterId = $request->input('after', 0);

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->with(['sender', 'replyTo'])
            ->ordered()
            ->get();

        $conversation->markAdminRead();

        return response()->json($messages);
    }

    public function reply(Request $request, LiveChatConversation $conversation): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message_type' => 'required|in:text,image,video',
            'message' => 'nullable|string|max:5000',
            'reply_to_message_id' => 'nullable|exists:live_chat_messages,id',
            'media' => 'required_if:message_type,image,video|file|max:1048576|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,webm,flv,3gp',
            'thumbnail' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,webp',
        ]);

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => 'admin',
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

        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('live-chat/media', 'public');
            $data['poster_path'] = $thumbPath;
        }

        $message = LiveChatMessage::create($data);

        $conversation->update([
            'last_message_at' => now(),
            'user_unread_count' => $conversation->user_unread_count + 1,
        ]);

        $message->load('sender', 'replyTo');

        return response()->json($message);
    }

    public function close(LiveChatConversation $conversation): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->update(['status' => 'closed']);

        return response()->json(['success' => true, 'message' => 'Conversation ditutup']);
    }

    public function reopen(LiveChatConversation $conversation): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->update(['status' => 'open']);

        return response()->json(['success' => true, 'message' => 'Conversation dibuka kembali']);
    }

    public function toggleFavorite(LiveChatConversation $conversation): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->update(['is_favorited' => !$conversation->is_favorited]);

        return response()->json(['is_favorited' => $conversation->is_favorited]);
    }

    public function deleteMessage(LiveChatMessage $message): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        $conversation = $message->conversation;
        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    public function deleteConversation(LiveChatConversation $conversation): JsonResponse
    {
        $user = Auth::guard('admin')->user();
        $channelIds = $user->assignedOperators()->pluck('channel_id');

        if (!$channelIds->contains($conversation->channel_id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['success' => true]);
    }
}
