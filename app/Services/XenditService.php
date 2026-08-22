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

    public function verifyCallbackToken(?string $token): bool
    {
        if ($this->callbackToken === '') {
            return false;
        }

        return hash_equals($this->callbackToken, (string) $token);
    }
}
