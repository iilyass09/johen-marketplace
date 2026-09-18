<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Models\LiveChatMessageHiddenUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveChatChatController extends Controller
{
    public function index(Request $request)
    {
        $query = LiveChatConversation::with(['user', 'channel', 'lastMessage.sender']);

        $user = Auth::user();

        if ($user->isLiveChatAdmin()) {
            $channelIds = $user->assignedOperators()->pluck('channel_id');
            $query->whereIn('channel_id', $channelIds);
        }

        if ($request->filled('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conversations = $query->orderBy('last_message_at', 'desc')->paginate(20);

        return view('admin.live-chat.conversations.index', compact('conversations'));
    }

    public function show(LiveChatConversation $conversation)
    {
        $user = Auth::user();

        if ($user->isLiveChatAdmin()) {
            $hasAccess = $conversation->channel->operators()
                ->where('user_id', $user->id)
                ->exists();

            if (!$hasAccess) {
                abort(403);
            }
        }

        $conversation->load(['user', 'channel', 'lastMessage.sender']);

        $conversation->markAdminRead();

        $adminId = Auth::id();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', $adminId))
            ->with(['sender', 'replyTo'])
            ->ordered()
            ->get();

        $activeOperator = $conversation->channel->getActiveOperator();

        return view('admin.live-chat.conversations.show', compact('conversation', 'messages', 'activeOperator'));
    }

    public function poll(LiveChatConversation $conversation, Request $request)
    {
        $afterId = $request->input('after', 0);

        $adminId = Auth::id();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', $adminId))
            ->with(['sender', 'replyTo'])
            ->ordered()
            ->get();

        $conversation->markAdminRead();

        return response()->json($messages);
    }

    public function reply(Request $request, LiveChatConversation $conversation)
    {
        $request->validate([
            'message_type' => 'required|in:text,image,video,audio',
            'message' => 'nullable|string|max:5000',
            'reply_to_message_id' => 'nullable|exists:live_chat_messages,id',
            'media' => 'required_if:message_type,image,video,audio|file|max:1048576|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,webm,flv,3gp,wav,oga,ogg,opus,mp3,m4a,aac,weba',
            'media_duration' => 'nullable|integer|min:0|max:1800',
            'thumbnail' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,webp',
        ]);

        $userId = Auth::id();

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
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
            $data['media_duration'] = $request->message_type === 'audio' ? (int) $request->media_duration : null;
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

    public function deleteMessage(LiveChatMessage $message)
    {
        if ($message->sender_type !== 'admin' || $message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    public function hideMessage(LiveChatMessage $message)
    {
        LiveChatMessageHiddenUser::firstOrCreate([
            'live_chat_message_id' => $message->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }
}
