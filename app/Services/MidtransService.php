<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction as MidtransTransaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function isConfigured(): bool
    {
        return !empty(config('midtrans.server_key')) && !empty(config('midtrans.client_key'));
    }

    public function createSnapTransaction(array $params): array
    {
        try {
            $snapToken = Snap::getSnapToken($params);
            return [
                'success' => true,
                'token' => $snapToken,
                'redirect_url' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getNotification(): Notification
    {
        return new Notification();
    }

    public function checkStatus(string $orderId)
    {
        return MidtransTransaction::status($orderId);
    }

    public function cancelTransaction(string $orderId)
    {
        return MidtransTransaction::cancel($orderId);
    }
}
