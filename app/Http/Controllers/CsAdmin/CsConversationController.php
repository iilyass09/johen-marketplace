<?php

namespace App\Http\Controllers\CsAdmin;

use App\Http\Controllers\Controller;
use App\Models\LiveChatChannel;
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
        $csChannelId = LiveChatChannel::csChannel()->id;

        LiveChatConversation::expireStaleGuestSessions($csChannelId);

        $query = LiveChatConversation::with(['channel', 'lastMessage.sender'])
            ->whereNotNull('guest_id')
            ->where('channel_id', $csChannelId)
            ->whereNull('archived_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('guest_name', 'like', '%' . $request->search . '%');
        }

        $conversations = $query
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_message_at')
            ->limit(200)
            ->get();

        $totalUnread = LiveChatConversation::whereNotNull('guest_id')
            ->where('channel_id', $csChannelId)
            ->whereNull('archived_at')
            ->where('admin_unread_count', '>', 0)
            ->count();

        $archivedCount = LiveChatConversation::whereNotNull('guest_id')
            ->where('channel_id', $csChannelId)
            ->whereNotNull('archived_at')
            ->count();

        return view('admin.csadmin.conversations.index', compact('conversations', 'totalUnread', 'archivedCount'));
    }

    public function show(LiveChatConversation $conversation)
    {
        $this->guardGuest($conversation);

        return redirect()->route('csadmin.conversations', ['open' => $conversation->id]);
    }

    public function loadMessages(LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $conversation->applySessionTimeout();

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

        $conversation->applySessionTimeout();

        $afterId = $request->input('after', 0);

        $adminId = Auth::guard('admin')->id();

        $messages = LiveChatMessage::where('conversation_id', $conversation->id)
            ->where('id', '>', $afterId)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', $adminId))
            ->with(['sender', 'replyTo', 'attachments'])
            ->ordered()
            ->get();

        $conversation->markAdminRead();

        return response()->json([
            'messages' => $messages,
            'conversation' => $conversation,
        ]);
    }

    public function reply(Request $request, LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        if ($conversation->applySessionTimeout() || $conversation->isSessionExpired()) {
            return response()->json([
                'error' => 'Sesi chat telah berakhir, tamu tidak dapat menerima balasan lagi.',
                'session_expired' => true,
            ], 423);
        }

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

    public function archived()
    {
        $csChannelId = LiveChatChannel::csChannel()->id;

        $conversations = LiveChatConversation::with(['channel', 'lastMessage.sender'])
            ->whereNotNull('guest_id')
            ->where('channel_id', $csChannelId)
            ->whereNotNull('archived_at')
            ->orderBy('last_message_at', 'desc')
            ->limit(100)
            ->get();

        $html = '';
        foreach ($conversations as $conv) {
            $html .= view('admin.csadmin.conversations._list-item', ['conv' => $conv])->render();
        }

        return response()->json([
            'html' => $html,
            'count' => $conversations->count(),
        ]);
    }

    public function archivedCount(): JsonResponse
    {
        $csChannelId = LiveChatChannel::csChannel()->id;

        $count = LiveChatConversation::whereNotNull('guest_id')
            ->where('channel_id', $csChannelId)
            ->whereNotNull('archived_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function archive(LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $conversation->update(['archived_at' => now()]);

        return response()->json(['success' => true, 'archived_at' => $conversation->archived_at]);
    }

    public function restore(LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $conversation->update(['archived_at' => null]);

        return response()->json(['success' => true]);
    }

    public function deleteConversation(LiveChatConversation $conversation): JsonResponse
    {
        $this->guardGuest($conversation);

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['success' => true]);
    }

    private function guardGuest(LiveChatConversation $conversation): void
    {
        abort_if(
            $conversation->user_id !== null
            || $conversation->guest_id === null
            || $conversation->channel_id !== LiveChatChannel::csChannel()->id,
            404
        );
    }
}