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
    public function sendToTargets(array $targets, string $title, string $body, array $extra = [], ?int $userId = null): void
    {
        if (empty($targets)) {
            return;
        }

        $guards = array_values(array_unique(array_map(fn ($t) => $t['guard'], $targets)));
        /** @var \Illuminate\Support\Collection<int, PushSubscription> $subs */
        $subs = PushSubscription::query()
            ->whereIn('guard', $guards)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->get();

        if ($subs->isEmpty()) {
            return;
        }

        try {
            $webPush = $this->webPush();
        } catch (\Throwable $e) {
            logger()->warning('PushService: gagal inisialisasi WebPush', ['error' => $e->getMessage()]);

            return;
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

        try {
            foreach ($webPush->flush() as $report) {
                logger()->info('PushService: laporan kirim', [
                    'success' => $report->isSuccess(),
                    'expired' => $report->isSubscriptionExpired(),
                    'status' => $report->getResponse()?->getStatusCode(),
                    'reason' => $report->getReason(),
                ]);
                if (! $report->isSuccess() && $report->isSubscriptionExpired()) {
                    PushSubscription::query()->where('endpoint', $report->getEndpoint())->delete();
                }
            }
        } catch (\Throwable $e) {
            logger()->error('PushService: flush gagal', ['error' => $e->getMessage(), 'trace' => substr($e->getTraceAsString(), 0, 500)]);
        }
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
                'text' => $message->message && mb_strlen((string) $message->message) > 150
                    ? mb_substr((string) $message->message, 0, 150).'…'
                    : (string) $message->message,
                default => '',
            };

            $this->sendToTargets([
                [
                    'guard' => 'web',
                    'url' => route('home').'?chat=1&channel='.urlencode((string) $channel?->slug),
                ],
            ], $senderName, $preview, [
                'icon' => $admin && $admin->photo_path ? asset('storage/'.$admin->photo_path) : asset('logo.png'),
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