<?php

namespace App\Services;

use App\Models\LiveChatConversation;
use App\Models\LiveChatMessage;
use App\Models\PushSubscription;
use Minishlink\WebPush\ContentEncoding;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushService
{
    protected function webPush(): WebPush
    {
        return new WebPush([
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ], [
            'TTL' => 300,
            'urgency' => 'high',
        ]);
    }

    /**
     * Mengirim notifikasi web push ke admin yang terdaftar pada guard tertentu.
     *
     * @param  array<int, array{guard: string, url: string}>  $targets
     * @param  int|null  $userId  batasi ke satu user (misal pemilik percakapan di guard "web")
     */
    public function sendToTargets(array $targets, string $title, string $body, array $extra = [], ?int $userId = null): int
    {
        if (empty($targets)) {
            return 0;
        }

        if (empty(config('services.vapid.public_key')) || empty(config('services.vapid.private_key'))) {
            logger()->warning('PushService: VAPID keys belum dikonfigurasi. Notifikasi dilewati.');

            return 0;
        }

        $guards = array_values(array_unique(array_map(fn ($t) => $t['guard'], $targets)));
        /** @var \Illuminate\Support\Collection<int, PushSubscription> $subs */
        $subs = PushSubscription::query()
            ->whereIn('guard', $guards)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->get()
            ->unique('endpoint');

        if ($subs->isEmpty()) {
            logger()->info('PushService: tidak ada subscription terdaftar', [
                'guards' => $guards,
                'user_id' => $userId,
            ]);

            return 0;
        }

        try {
            $webPush = $this->webPush();
        } catch (\Throwable $e) {
            logger()->warning('PushService: gagal inisialisasi WebPush', ['error' => $e->getMessage()]);

            return 0;
        }

        foreach ($subs as $sub) {
            $target = collect($targets)->first(fn ($t) => $t['guard'] === $sub->guard);
            if (! $target) {
                continue;
            }

            $itemPayload = array_merge([
                'title' => trim($title),
                'body' => trim($body),
                'app_name' => config('services.vapid.notif_brand', 'Johen Gaming'),
            ], $extra);
            if (! empty($target['url'])) {
                $itemPayload['url'] = $target['url'];
            }
            $itemPayload['target_guard'] = $target['guard'];

            try {
                $subscription = new Subscription(
                    $sub->endpoint,
                    $sub->public_key,
                    $sub->auth_token,
                    ContentEncoding::aes128gcm,
                );

                $webPush->queueNotification(
                    $subscription,
                    json_encode($itemPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    [],
                    [],
                );
            } catch (\Throwable $e) {
                logger()->warning('PushService: subscription diabaikan', [
                    'guard' => $sub->guard,
                    'endpoint' => substr($sub->endpoint, 0, 60),
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        $delivered = 0;

        try {
            foreach ($webPush->flush() as $report) {
                if ($report->isSuccess()) {
                    $delivered++;
                } else {
                    logger()->info('PushService: laporan kirim', [
                        'success' => false,
                        'expired' => $report->isSubscriptionExpired(),
                        'status' => $report->getResponse()?->getStatusCode(),
                        'reason' => $report->getReason(),
                        'endpoint' => substr($report->getEndpoint(), 0, 60),
                    ]);

                    if ($report->isSubscriptionExpired()) {
                        PushSubscription::query()->where('endpoint', $report->getEndpoint())->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->error('PushService: flush gagal', ['error' => $e->getMessage(), 'trace' => substr($e->getTraceAsString(), 0, 500)]);
        }

        logger()->info('PushService: selesai', ['guards' => $guards, 'user_id' => $userId, 'subs' => $subs->count(), 'delivered' => $delivered]);

        return $delivered;
    }

    /**
    * Kirim notifikasi desktop ke user (guard "web") saat admin/operator membalas live chat.
     */
    public function sendUserReplyNotification(LiveChatConversation $conversation, LiveChatMessage $message, string $senderName): void
    {
        try {
            $channel = $conversation->channel;
            $admin = $channel?->admins()->where('is_active', true)->first();

            $preview = match ($message->message_type) {
                'image' => '📷 Foto',
                'video' => '🎬 Video',
                'audio' => '🎤 Pesan suara',
                'text' => $message->message && mb_strlen((string) $message->message) > 150
                    ? mb_substr((string) $message->message, 0, 150).'…'
                    : (string) $message->message,
                default => '',
            };

            $this->sendToTargets([
                [
                    'guard' => 'web',
                    'url' => '/?chat=1&channel='.urlencode((string) $channel?->slug),
                ],
            ], $senderName, $preview, [
                'icon' => $admin && $admin->photo_path ? asset('storage/'.$admin->photo_path) : asset('logo-96.png'),
                'msg_id' => $message->id,
                'tag' => 'conv-'.$conversation->id.'-msg-'.$message->id,
                'conversation_id' => $conversation->id,
                'channel_slug' => $channel?->slug,
            ], $conversation->user_id);
        } catch (\Throwable $e) {
            logger()->warning('PushService: notif balasan ke user gagal', ['error' => $e->getMessage()]);
        }
    }
}