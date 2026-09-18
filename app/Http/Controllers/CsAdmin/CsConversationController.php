<?php

namespace App\Http\Controllers\CsAdmin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Models\LiveChatMessageHiddenUser;
use App\Services\LiveChatMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CsConversationController extends Controller
{
    public function index(Request $request)
    {
        $query = LiveChatConversation::with(['user', 'channel', 'lastMessage.sender'])
            ->whereNotNull('guest_id');

        if ($request->filled('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('guest_name', 'like', '%' . $request->search . '%');
        }

        $conversations = $query
            ->orderByDesc('is_pinned')
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        $channels = \App\Models\LiveChatChannel::active()->ordered()->get();

        return view('admin.csadmin.conversations.index', compact('conversations', 'channels'));
    }

    public function show(LiveChatConversation $conversation)
    {
        $this->guardGuest($conversation);

        $conversation->load(['user', 'channel', 'lastMessage.sender']);
        $conversation->markAdminRead();

        $adminId = Auth::guard('admin')->id();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', $adminId))
            ->with(['sender', 'replyTo', 'attachments'])
            ->ordered()
            ->get();

        $activeOperator = $conversation->channel->getActiveOperator();

        return view('admin.csadmin.conversations.show', compact('conversation', 'messages', 'activeOperator'));
    }

    public function loadMessages(LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $conversation->load(['user', 'channel']);
        $conversation->markAdminRead();

        $adminId = Auth::guard('admin')->id();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', $adminId))
            ->with(['sender', 'replyTo', 'attachments'])
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
        $this->guardGuest($conversation);

        $afterId = $request->input('after', 0);

        $adminId = Auth::guard('admin')->id();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', $adminId))
            ->with(['sender', 'replyTo', 'attachments'])
            ->ordered()
            ->get();

        $conversation->markAdminRead();

        return response()->json($messages);
    }

    public function reply(Request $request, LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $request->validate([
            'message_type' => 'required|in:text,image,video,audio',
            'message' => 'nullable|string|max:5000',
            'reply_to_message_id' => 'nullable|exists:live_chat_messages,id',
            'media' => 'nullable|array',
            'media.*' => 'file|max:1048576|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,webm,flv,3gp,wav,oga,ogg,opus,mp3,m4a,aac,weba',
            'media_duration' => 'nullable|integer|min:0|max:1800',
            'thumbnail' => 'nullable|array',
            'thumbnail.*' => 'file|max:5120|mimes:jpg,jpeg,png,webp',
            'thumbnail_indexes' => 'nullable|array',
            'thumbnail_indexes.*' => 'integer|min:0',
        ]);

        $user = Auth::guard('admin')->user();

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => 'admin',
            'message_type' => $request->message_type,
            'message' => $request->message,
            'reply_to_message_id' => $request->reply_to_message_id,
        ];

        $message = LiveChatMessage::create($data);

        $files = $request->file('media', []);
        if (!empty($files)) {
            $posterFiles = [];
            foreach ((array) $request->input('thumbnail_indexes', []) as $key => $index) {
                $thumb = $request->file('thumbnail')[$key] ?? null;
                if ($thumb) {
                    $posterFiles[(int) $index] = $thumb;
                }
            }
            $durations = $request->message_type === 'audio' ? [0 => (int) $request->media_duration] : [];
            $rows = LiveChatMedia::storeBatch($files, $posterFiles, $durations, $request->message_type !== 'audio');
            $message->attachments()->createMany($rows);
            LiveChatMedia::applyCompatColumns($message, $rows);
        }

        $conversation->update([
            'last_message_at' => now(),
            'user_unread_count' => $conversation->user_unread_count + 1,
        ]);

        $message->load('sender', 'replyTo', 'attachments');

        return response()->json($message);
    }

    public function close(LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $conversation->update(['status' => 'closed']);

        return response()->json(['success' => true, 'message' => 'Conversation ditutup']);
    }

    public function reopen(LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $conversation->update(['status' => 'open']);

        return response()->json(['success' => true, 'message' => 'Conversation dibuka kembali']);
    }

    public function deleteMessage(LiveChatMessage $message): JsonResponse
    {
        $this->guardGuest($message->conversation);

        $adminId = Auth::guard('admin')->id();

        if ($message->sender_type !== 'admin' || $message->sender_id !== $adminId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    public function hideMessage(LiveChatMessage $message): JsonResponse
    {
        $this->guardGuest($message->conversation);

        LiveChatMessageHiddenUser::firstOrCreate([
            'live_chat_message_id' => $message->id,
            'user_id' => Auth::guard('admin')->id(),
        ]);

        return response()->json(['success' => true]);
    }

    private function guardGuest(LiveChatConversation $conversation): void
    {
        abort_if($conversation->user_id !== null || $conversation->guest_id === null, 404);
    }
}