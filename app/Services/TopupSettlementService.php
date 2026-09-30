<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

/**
 * Peng Settlement topup: satu-satunya tempat yang memutuskan Nasib order
 * sekaligus sisi saldonya.
 *
 * Dipakai oleh webhook Xendit, webhook Digiflazz, polling dari browser, dan
 * command rekonsiliasi — semuanya lewat method yang sama supaya tidak ada
 * jalur yang bisa lupa refunded saldonya.
 */
class TopupSettlementService
{
    public function __construct(
        protected DigiflazzService $digiflazz,
        protected BalanceService $balance,
    ) {}

    /**
     * Langkah 1: Xendit mengonfirmasi pembayaran.
     * Order masuk "processing", saldo user naik dan terkunci, lalu topUp dikirim.
     */
    public function markPaid(Order $order): void
    {
        if ($order->status !== 'pending') {
            return;
        }

        $order->update([
            'status' => 'processing',
            'digiflazz_started_at' => $order->digiflazz_started_at ?: now(),
        ]);
        $order->transaction?->update(['status' => 'processing']);

        $this->balance->markPaid($order);

        $result = $this->digiflazz->topUp(
            $order->buyer_sku_code,
            $order->customer_number,
            $order->order_id,
            $order->effective_zone_id
        );

        Log::info('Digiflazz topUp response', ['order_id' => $order->order_id, 'response' => $result]);

        $this->applyResult($order->fresh(), $result);
    }

    /**
     * Terapkan hasil respons Digiflazz (sukses / pending / gagal).
     */
    public function applyResult(Order $order, array $result): void
    {
        if ($order->status === 'success') {
            return;
        }

        $data = $result['data'] ?? [];
        $status = strtolower(trim((string) ($data['status'] ?? '')));

        if ($status === 'sukses') {
            $this->settleSuccess($order, $data, $result);
        } elseif ($status === 'pending') {
            $order->update([
                'status' => 'processing',
                'note' => null,
                'last_status_check_at' => now(),
            ]);
            $order->transaction?->update(['status' => 'processing', 'raw_response' => $result]);
        } else {
            $this->settleFailure(
                $order,
                $data['message'] ?? ($data['status'] ?: 'Gagal diproses Digiflazz'),
                $result,
            );
        }
    }

    protected function settleSuccess(Order $order, array $data, ?array $result = null): void
    {
        $order->update([
            'status' => 'success',
            'note' => $data['sn'] ?? null,
            'reconcile_status' => 'none',
        ]);
        $order->transaction?->update(['status' => 'success', 'raw_response' => $result]);

        // Kalau saldonya sempat dikembalikan (sukses terlambat), tarik ulang.
        if ($order->saldo_status === 'refunded') {
            $this->balance->markLateSuccess($order);
        } else {
            $this->balance->markDelivered($order);
        }

        $product = Product::where('buyer_sku_code', $order->buyer_sku_code)->first();
        $product?->consumeStock((int) ($order->quantity ?? 1));
    }

    /**
     * Gagal: kembalikan saldo ke user dan lepaskan diskon.
     */
    public function settleFailure(Order $order, string $reason, ?array $result = null): void
    {
        $order->update([
            'status' => 'failed',
            'note' => $reason,
        ]);
        $order->transaction?->update(['status' => 'failed', 'raw_response' => $result]);

        $this->balance->markRefunded($order, $reason);
        $order->releaseDiscounts();
    }

    /**
     * Tanya status satu order ke Digiflazz. Mengembalikan status final
     * ('sukses'/'gagal'), atau null kalau masih pending / gagal ditanya.
     */
    public function poll(Order $order): ?string
    {
        try {
            $result = $this->digiflazz->checkStatus(
                $order->buyer_sku_code,
                $order->customer_number,
                $order->order_id,
                $order->effective_zone_id
            );
        } catch (\Exception $e) {
            Log::error('Digiflazz status poll failed: '.$e->getMessage());

            return null;
        }

        $order->update([
            'last_status_check_at' => now(),
            'digiflazz_poll_count' => (int) $order->digiflazz_poll_count + 1,
        ]);

        $status = strtolower(trim((string) ($result['data']['status'] ?? '')));

        if (in_array($status, ['sukses', 'gagal'], true)) {
            $this->applyResult($order, $result);

            return $status;
        }

        return null;
    }

    /**
     * Apakah order sudah melewati batas waktu=topup.
     * Order tanpa timestamp Digiflazz tidak dinilai di sini; command
     * rekonsiliasi tetap akan mem-poll order tersebut.
     */
    public function hasExceededGracePeriod(Order $order): bool
    {
        $startedAt = $order->digiflazz_started_at;
        $graceMinutes = (int) config('digiflazz.reconcile.grace_minutes', 30);

        if (! $graceMinutes || ! $startedAt) {
            return false;
        }

        return now()->greaterThanOrEqualTo($startedAt->copy()->addMinutes($graceMinutes));
    }

    public function hasExceededPollLimit(Order $order): bool
    {
        $maxPolls = (int) config('digiflazz.reconcile.max_polls', 12);

        return $maxPolls > 0 && (int) $order->digiflazz_poll_count >= $maxPolls;
    }

    public function balance(): BalanceService
    {
        return $this->balance;
    }
}
