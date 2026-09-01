<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected string $secretKey = '';

    protected string $callbackToken = '';

    protected string $baseUrl = 'https://api.xendit.co';

    public function __construct()
    {
        $this->secretKey = (string) config('xendit.secret_key', '');
        $this->callbackToken = (string) config('xendit.callback_token', '');
    }

    public function isConfigured(): bool
    {
        return $this->secretKey !== '';
    }

    public function createInvoice(array $params): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Xendit belum dikonfigurasi.'];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->post($this->baseUrl.'/v2/invoices', $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'id' => $data['id'] ?? null,
                    'invoice_url' => $data['invoice_url'] ?? null,
                    'status' => $data['status'] ?? null,
                ];
            }

            Log::error('Xendit createInvoice failed: '.$response->body());

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Gagal membuat invoice Xendit.',
            ];
        } catch (\Exception $e) {
            Log::error('Xendit createInvoice error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getInvoice(string $invoiceId): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->get($this->baseUrl.'/v2/invoices/'.$invoiceId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit getInvoice failed: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit getInvoice error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Buat QR Code (QRIS) langsung untuk ditampilkan di halaman sendiri.
     */
    public function createQr(array $params): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Xendit belum dikonfigurasi.'];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->withHeaders(['api-version' => '2022-07-31'])
                ->post($this->baseUrl.'/qr_codes', $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'id' => $data['id'] ?? null,
                    'qr_id' => $data['id'] ?? null,
                    'qr_string' => $data['qr_string'] ?? null,
                    'status' => $data['status'] ?? null,
                    'reference_id' => $data['reference_id'] ?? null,
                    'channel_code' => $data['channel_code'] ?? null,
                ];
            }

            Log::error('Xendit createQr failed: '.$response->body());

            return [
                'success' => false,
                'message' => $response->json('message') ?? $response->json('error_code') ?? 'Gagal membuat QR Code Xendit.',
            ];
        } catch (\Exception $e) {
            Log::error('Xendit createQr error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Ambil status QR Code dari Xendit (best-effort saat polling).
     */
    public function getQr(string $qrId): ?array
    {
        if (! $this->isConfigured() || $qrId === '') {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->withHeaders(['api-version' => '2022-07-31'])
                ->get($this->baseUrl.'/qr_codes/'.$qrId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit getQr failed: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit getQr error: '.$e->getMessage());

            return null;
        }
    }

    public function verifyCallbackToken(?string $token): bool
    {
        if ($this->callbackToken === '') {
            return false;
        }

        return hash_equals($this->callbackToken, (string) $token);
    }
}
