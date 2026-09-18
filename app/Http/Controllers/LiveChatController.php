<?php

namespace App\Http\Controllers;

use App\Models\LiveChatChannel;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Models\LiveChatMessageReaction;
use App\Models\LiveChatMessageStar;
use App\Models\LiveChatMessageHiddenUser;
use App\Models\User;
use App\Services\LiveChatMedia;
use App\Services\MediaStore;
use App\Services\PushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class LiveChatController extends Controller
{
    public function __construct(
        protected MediaStore $mediaStore
    ) {}

    public function channels(): JsonResponse
    {
        $channels = LiveChatChannel::active()->ordered()->get();

        $userId = Auth::id();

        $channels->each(function ($channel) use ($userId) {
            $conversation = LiveChatConversation::where('channel_id', $channel->id)
                ->where('user_id', $userId)
                ->first();

            $hasMessages = $conversation && $conversation->messages()->exists();

            $activeOperator = $channel->getActiveOperator();
            $admin = $channel->admins()->where('is_active', true)->first();

            $channel->unread_count = $hasMessages ? $conversation->user_unread_count : 0;
            $channel->last_message = $hasMessages ? $conversation->lastMessage : null;
            $channel->last_message_at = $hasMessages ? $conversation->last_message_at : null;
            $channel->has_conversation = $hasMessages;
            $channel->conversation_id = $conversation ? $conversation->id : null;
            $channel->is_online = $activeOperator !== null;
            $channel->operator_name = $activeOperator ? $activeOperator->display_name : null;
            $channel->admin_photo = $admin && $admin->photo_path ? asset('storage/' . $admin->photo_path) : null;
        });

        return response()->json($channels);
    }

    public function guestChannels(Request $request): JsonResponse
    {
        $data = $request->validate([
            'guest_id' => ['required', 'string', 'max:64'],
        ]);

        $channels = LiveChatChannel::active()->ordered()->get();

        $channels->each(function ($channel) use ($data) {
            $conversation = LiveChatConversation::where('channel_id', $channel->id)
                ->where('guest_id', $data['guest_id'])
                ->first();

            $hasMessages = $conversation && $conversation->messages()->exists();

            $activeOperator = $channel->getActiveOperator();
            $admin = $channel->admins()->where('is_active', true)->first();

            $channel->unread_count = $hasMessages ? $conversation->user_unread_count : 0;
            $channel->last_message = $hasMessages ? $conversation->lastMessage : null;
            $channel->last_message_at = $hasMessages ? $conversation->last_message_at : null;
            $channel->has_conversation = $hasMessages;
            $channel->conversation_id = $conversation ? $conversation->id : null;
            $channel->is_online = $activeOperator !== null;
            $channel->operator_name = $activeOperator ? $activeOperator->display_name : null;
            $channel->admin_photo = $admin && $admin->photo_path ? asset('storage/' . $admin->photo_path) : null;
        });

        return response()->json($channels);
    }

    public function getConversation(Request $request, string $channelSlug): JsonResponse
    {
        $channel = LiveChatChannel::where('slug', $channelSlug)->firstOrFail();
        $userId = Auth::id();

        $conversation = LiveChatConversation::firstOrCreate(
            [
                'channel_id' => $channel->id,
                'user_id' => $userId,
            ],
            [
                'status' => 'open',
            ]
        );

        $conversation->load(['channel', 'lastMessage.sender']);

        $activeOperator = $channel->getActiveOperator();
        $admin = $channel->admins()->where('is_active', true)->first();

        return response()->json([
            'conversation' => $conversation,
            'active_operator' => $activeOperator ? [
                'name' => $activeOperator->display_name,
                'photo' => $admin && $admin->photo_path ? asset('storage/' . $admin->photo_path) : null,
                'schedule' => $activeOperator->getCurrentSchedule()?->schedule_label,
                'is_on_duty' => true,
            ] : null,
        ]);
    }

    public function guestConversation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'channel_slug' => ['required', 'string'],
            'guest_id' => ['required', 'string', 'max:64'],
        ]);

        $channel = LiveChatChannel::where('slug', $data['channel_slug'])->firstOrFail();

        $conversation = LiveChatConversation::firstOrCreate(
            [
                'channel_id' => $channel->id,
                'guest_id' => $data['guest_id'],
            ],
            [
                'user_id' => null,
                'guest_name' => 'Guest',
                'status' => 'open',
            ]
        );

        $conversation->load(['channel', 'lastMessage.sender']);

        $activeOperator = $channel->getActiveOperator();
        $admin = $channel->admins()->where('is_active', true)->first();

        return response()->json([
            'conversation' => $conversation,
            'active_operator' => $activeOperator ? [
                'name' => $activeOperator->display_name,
                'photo' => $admin && $admin->photo_path ? asset('storage/' . $admin->photo_path) : null,
                'schedule' => $activeOperator->getCurrentSchedule()?->schedule_label,
                'is_on_duty' => true,
            ] : null,
        ]);
    }

    public function guestMessages(Request $request, LiveChatConversation $conversation): JsonResponse
    {
        $this->ensureGuestConversation($conversation, $request->input('guest_id'));

        $afterId = $request->input('after');

        $query = LiveChatMessage::where('conversation_id', $conversation->id)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', null))
            ->with(['sender', 'replyTo', 'reactions', 'stars', 'attachments']);

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->ordered()->get();

        return response()->json($messages);
    }

    public function messages(Request $request, LiveChatConversation $conversation): JsonResponse
    {
        $userId = Auth::id();

        if ($conversation->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $afterId = $request->input('after');
        $query = LiveChatMessage::where('conversation_id', $conversation->id)
            ->whereDoesntHave('hiddenUsers', fn ($hidden) => $hidden->where('user_id', $userId))
            ->with(['sender', 'replyTo', 'reactions', 'stars', 'attachments']);

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->ordered()->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:live_chat_conversations,id',
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

        $userId = Auth::id();
        $conversation = LiveChatConversation::findOrFail($request->conversation_id);

        if ($conversation->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $files = $request->file('media', []);
        if ($request->message_type !== 'text' && empty($files)) {
            return response()->json(['errors' => ['media' => ['Media wajib diisi.']]], 422);
        }

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'sender_type' => 'user',
            'message_type' => $request->message_type,
            'message' => $request->message,
            'reply_to_message_id' => $request->reply_to_message_id,
        ];

        $message = LiveChatMessage::create($data);

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

        $lastInteraction = $conversation->last_message_at;

        $conversation->update([
            'last_message_at' => now(),
            'admin_unread_count' => $conversation->admin_unread_count + 1,
        ]);

        $channel = $conversation->channel;
        $operator = $channel->getActiveOperator();
        $shouldAutoReply = !$lastInteraction || $lastInteraction->diffInMinutes(now()) >= 60;

        if ($operator && $shouldAutoReply) {
            $userName = Auth::user()->name ?? 'Pangeran';
            $operatorName = $operator->display_name ?? 'Admin';
            $channelName = str_replace('Johen ', '', $channel->name);

            LiveChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => null,
                'sender_type' => 'admin',
                'message_type' => 'text',
                'message' => "Halo pangeran {$userName}! Kamu sekarang terhubung dengan admin {$operatorName} johen {$channelName}. Ada yang bisa kami bantu pangeran?",
            ]);

            $conversation->update([
                'last_message_at' => now(),
                'user_unread_count' => $conversation->user_unread_count + 1,
            ]);
        }

        $message->load('sender', 'replyTo', 'reactions', 'stars', 'attachments');

        $this->notifyAdmins($message, $conversation, $request->message_type);

        return response()->json($message);
    }

    protected function notifyAdmins(LiveChatMessage $message, LiveChatConversation $conversation, string $messageType): void
    {
        if (Schema::hasColumn('live_chat_conversations', 'archived_at') && $conversation->archived_at) {
            return;
        }

        try {
            $user = Auth::user();
            $userName = $user?->name ?: 'User';
            $attachmentCount = (int) $message->attachments()->count();

            $body = match ($messageType) {
                'image' => $attachmentCount > 0 ? "📷 {$attachmentCount} Foto" : '📷 Foto',
                'video' => $attachmentCount > 0 ? "🎬 {$attachmentCount} Video" : '🎬 Video',
                'audio' => '🎤 Pesan suara',
                default => mb_strlen((string) $message->message) > 150
                    ? mb_substr((string) $message->message, 0, 150).'…'
                    : (string) $message->message,
            };

            app(PushService::class)->sendToTargets([
                ['guard' => 'admin', 'url' => '/admin/live-chat/conversations/'.$conversation->id],
                ['guard' => 'lcadmin', 'url' => '/lcadmin/conversations?open='.$conversation->id],
            ], $userName, $body, [
                'tag' => 'conv-'.$conversation->id.'-msg-'.$message->id,
                'msg_id' => $message->id,
                'icon' => $user?->avatar ? media_url($user->avatar) : asset('logo-96.png'),
                'conversation_id' => $conversation->id,
                'channel_slug' => $conversation->channel?->slug,
            ]);
        } catch (\Throwable) {
            // Push tidak boleh menggagalkan kirim pesan.
        }
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'media' => 'required|file|max:1048576|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,webm,flv,3gp',
        ]);

        $file = $request->file('media');
        $path = $file->store('live-chat/media', 'public');
        MediaStore::import($path);

        return response()->json([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function markRead(LiveChatConversation $conversation): JsonResponse
    {
        $userId = Auth::id();

        if ($conversation->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->markUserRead();

        LiveChatMessage::where('conversation_id', $conversation->id)
            ->where('sender_type', '!=', 'user')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function guestSendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:live_chat_conversations,id',
            'guest_id' => 'required|string|max:64',
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

        $conversation = LiveChatConversation::findOrFail($request->conversation_id);
        $this->ensureGuestConversation($conversation, $request->guest_id);

        $files = $request->file('media', []);
        if ($request->message_type !== 'text' && empty($files)) {
            return response()->json(['errors' => ['media' => ['Media wajib diisi.']]], 422);
        }

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'user',
            'message_type' => $request->message_type,
            'message' => $request->message,
            'reply_to_message_id' => $request->reply_to_message_id,
        ];

        $message = LiveChatMessage::create($data);

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

        $lastInteraction = $conversation->last_message_at;

        $conversation->update([
            'last_message_at' => now(),
            'admin_unread_count' => $conversation->admin_unread_count + 1,
        ]);

        $channel = $conversation->channel;
        $operator = $channel->getActiveOperator();
        $shouldAutoReply = !$lastInteraction || $lastInteraction->diffInMinutes(now()) >= 60;

        if ($operator && $shouldAutoReply) {
            $operatorName = $operator->display_name ?? 'Admin';
            $channelName = str_replace('Johen ', '', $channel->name);

            LiveChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => null,
                'sender_type' => 'admin',
                'message_type' => 'text',
                'message' => "Halo! Kamu sekarang terhubung dengan admin {$operatorName} johen {$channelName}. Ada yang bisa kami bantu?",
            ]);

            $conversation->update([
                'last_message_at' => now(),
                'user_unread_count' => $conversation->user_unread_count + 1,
            ]);
        }

        $message->load('sender', 'replyTo', 'reactions', 'stars', 'attachments');

        $this->notifyAdmins($message, $conversation, $request->message_type);

        return response()->json($message);
    }

    public function guestMarkRead(Request $request, LiveChatConversation $conversation): JsonResponse
    {
        $this->ensureGuestConversation($conversation, $request->input('guest_id'));

        $conversation->markUserRead();

        LiveChatMessage::where('conversation_id', $conversation->id)
            ->where('sender_type', '!=', 'user')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function guestUnreadCount(Request $request): JsonResponse
    {
        $data = $request->validate([
            'guest_id' => ['required', 'string', 'max:64'],
        ]);

        $total = LiveChatConversation::where('guest_id', $data['guest_id'])
            ->sum('user_unread_count');

        return response()->json(['unread_count' => $total]);
    }

    protected function ensureGuestConversation(LiveChatConversation $conversation, ?string $guestId): void
    {
        abort_unless($conversation->user_id === null && $guestId !== null && $conversation->guest_id === $guestId, 403);
    }

    public function unreadCount(): JsonResponse
    {
        $userId = Auth::id();

        $total = LiveChatConversation::where('user_id', $userId)
            ->sum('user_unread_count');

        return response()->json(['unread_count' => $total]);
    }

    public function deleteMessage(LiveChatMessage $message): JsonResponse
    {
        $userId = Auth::id();

        if ($message->sender_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    public function hideMessage(LiveChatMessage $message): JsonResponse
    {
        $this->ensureConversationOwner($message);

        LiveChatMessageHiddenUser::firstOrCreate([
            'live_chat_message_id' => $message->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function toggleReaction(Request $request, LiveChatMessage $message): JsonResponse
    {
        $this->ensureConversationOwner($message);

        $data = $request->validate(['emoji' => 'required|in:👍,❤️,😂,😮']);
        $reaction = LiveChatMessageReaction::where([
            'live_chat_message_id' => $message->id,
            'user_id' => Auth::id(),
            'emoji' => $data['emoji'],
        ])->first();

        $active = ! $reaction;
        $reaction ? $reaction->delete() : LiveChatMessageReaction::create([
            'live_chat_message_id' => $message->id,
            'user_id' => Auth::id(),
            'emoji' => $data['emoji'],
        ]);

        $message->load('reactions', 'stars');

        return response()->json(['active' => $active, 'reaction_summary' => $message->reaction_summary]);
    }

    public function toggleStar(LiveChatMessage $message): JsonResponse
    {
        $this->ensureConversationOwner($message);

        $star = LiveChatMessageStar::where([
            'live_chat_message_id' => $message->id,
            'user_id' => Auth::id(),
        ])->first();
        $star ? $star->delete() : LiveChatMessageStar::create([
            'live_chat_message_id' => $message->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['is_starred' => ! $star]);
    }

    private function ensureConversationOwner(LiveChatMessage $message): void
    {
        abort_unless($message->conversation->user_id === Auth::id(), 403);
    }
}
