<?php

namespace App\Services;

use App\Models\LiveChatChannel;
use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;

class LiveChatRouter
{
    public const MAX_EMAIL_ATTEMPTS = 3;

    /**
     * Dipanggil setiap pesan teks dari visitor (user/guest) berhasil tersimpan.
     * Menangani deteksi game, gerbang email guest, dan perpindahan channel.
     */
    public function onVisitorMessage(LiveChatConversation $conversation, string $text): void
    {
        if ($conversation->status !== 'open' || trim($text) === '') {
            return;
        }

        if ($conversation->isGuest() && $conversation->pending_channel_id) {
            $this->handlePending($conversation, $text);

            return;
        }

        $target = $this->detectTarget($conversation, $text);

        if (! $target) {
            return;
        }

        if ($conversation->isGuest() && empty($conversation->guest_email)) {
            $this->startPending($conversation, $target);

            return;
        }

        $this->redirect($conversation, $target);
    }

    /**
     * Mencari channel game yang cocok dengan isi pesan.
     * Hanya berjalan untuk percakapan yang masih di channel resepsionis.
     */
    public function detectTarget(LiveChatConversation $conversation, string $text): ?LiveChatChannel
    {
        $current = $conversation->channel;

        if (! $current || ! $current->is_reception) {
            return null;
        }

        $lower = mb_strtolower(trim($text));
        $best = null;
        $bestLen = -1;

        $channels = LiveChatChannel::query()
            ->where('is_active', true)
            ->where('is_reception', false)
            ->ordered()
            ->get();

        foreach ($channels as $channel) {
            foreach ($channel->detectionKeywords() as $keyword) {
                if ($keyword === '' || ! $this->matchesKeyword($lower, $keyword)) {
                    continue;
                }

                if (mb_strlen($keyword) > $bestLen) {
                    $best = $channel;
                    $bestLen = mb_strlen($keyword);
                }
            }
        }

        return $best;
    }

    protected function handlePending(LiveChatConversation $conversation, string $text): void
    {
        $attempts = (int) $conversation->pending_email_attempts;
        $target = $conversation->pendingChannel;

        if (filter_var(trim($text), FILTER_VALIDATE_EMAIL)) {
            $conversation->update([
                'guest_email' => trim($text),
                'pending_channel_id' => null,
                'pending_email_attempts' => 0,
            ]);

            if ($target) {
                $this->redirect($conversation, $target);
            }

            return;
        }

        if ($attempts >= self::MAX_EMAIL_ATTEMPTS) {
            $conversation->update([
                'pending_channel_id' => null,
                'pending_email_attempts' => 0,
            ]);

            if ($target) {
                $this->redirect($conversation, $target);
            }

            return;
        }

        $conversation->increment('pending_email_attempts');

        $this->pushSystemMessage(
            $conversation,
            '⚠️ Email tidak valid. Mohon masukkan email yang benar agar diarahkan ke admin '.($target?->name ?? 'game').'.'
        );
    }

    protected function startPending(LiveChatConversation $conversation, LiveChatChannel $target): void
    {
        $conversation->update([
            'pending_channel_id' => $target->id,
            'pending_email_attempts' => 1,
        ]);

        $this->pushSystemMessage(
            $conversation,
            "🔔 Untuk diarahkan ke admin {$target->name}, mohon masukkan email Anda terlebih dahulu."
        );
    }

    protected function redirect(LiveChatConversation $conversation, LiveChatChannel $target): void
    {
        if ($conversation->channel_id === $target->id) {
            $conversation->update([
                'pending_channel_id' => null,
                'pending_email_attempts' => 0,
            ]);

            return;
        }

        $conversation->update([
            'channel_id' => $target->id,
            'pending_channel_id' => null,
            'pending_email_attempts' => 0,
            'last_message_at' => now(),
            'admin_unread_count' => $conversation->admin_unread_count + 1,
            'user_unread_count' => $conversation->user_unread_count + 1,
        ]);

        $this->pushSystemMessage($conversation, "✔ Percakapan dialihkan ke admin {$target->name}.");

        $this->notifyGameAdmins($conversation, $target);
    }

    protected function pushSystemMessage(LiveChatConversation $conversation, string $message): void
    {
        LiveChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'system',
            'message_type' => 'text',
            'message' => $message,
        ]);

        $conversation->increment('user_unread_count');
        $conversation->update(['last_message_at' => now()]);
    }

    protected function notifyGameAdmins(LiveChatConversation $conversation, LiveChatChannel $target): void
    {
        if (empty(config('services.vapid.public_key')) || empty(config('services.vapid.private_key'))) {
            return;
        }

        try {
            app(PushService::class)->sendToTargets([
                [
                    'guard' => 'lcadmin',
                    'url' => '/lcadmin/conversations?open='.$conversation->id,
                ],
                [
                    'guard' => 'admin',
                    'url' => '/admin/live-chat/conversations/'.$conversation->id,
                ],
            ], 'Percakapan dialihkan', 'Guest '.($conversation->guest_name ?? 'User').' dialihkan ke admin '.$target->name);
        } catch (\Throwable $e) {
            logger()->warning('LiveChatRouter: notifikasi redirect gagal', ['error' => $e->getMessage()]);
        }
    }

    protected function matchesKeyword(string $text, string $keyword): bool
    {
        $kw = mb_strtolower(trim($keyword));

        return (bool) preg_match('~(?<![a-z0-9])'.preg_quote((string) $kw, '~').'(?![a-z0-9])~i', $text);
    }
}