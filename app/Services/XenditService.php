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

    /**
     * Buat Fixed Virtual Account (VA) sekali pakai untuk ditampilkan di halaman sendiri.
     */
    public function createVirtualAccount(array $params): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Xendit belum dikonfigurasi.'];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->post($this->baseUrl.'/callback_virtual_accounts', $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'id' => $data['id'] ?? null,
                    'external_id' => $data['external_id'] ?? null,
                    'account_number' => $data['account_number'] ?? null,
                    'bank_code' => $data['bank_code'] ?? null,
                    'expected_amount' => $data['expected_amount'] ?? null,
                    'status' => $data['status'] ?? null,
                ];
            }

            Log::error('Xendit createVirtualAccount failed: '.$response->body());

            return [
                'success' => false,
                'message' => $response->json('message') ?? $response->json('error_code') ?? 'Gagal membuat Virtual Account Xendit.',
            ];
        } catch (\Exception $e) {
            Log::error('Xendit createVirtualAccount error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getVirtualAccount(string $id): ?array
    {
        if (! $this->isConfigured() || $id === '') {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->get($this->baseUrl.'/callback_virtual_accounts/'.$id);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit getVirtualAccount failed: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit getVirtualAccount error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Simulasikan pembayaran VA (TEST mode).
     */
    public function simulateVirtualAccountPayment(string $externalId, int $amount): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->post($this->baseUrl.'/callback_virtual_accounts/external_id='.$externalId.'/simulate_payment', [
                    'amount' => $amount,
                ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error('Xendit simulateVA failed: '.$response->body());

            return ['success' => false, 'message' => $response->json('message') ?? 'Gagal simulasi VA.', 'data' => $response->json()];
        } catch (\Exception $e) {
            Log::error('Xendit simulateVA error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Buat kode pembayaran Retail Outlet (Alfamart/Indomaret) untuk ditampilkan sendiri.
     */
    public function createRetailOutlet(array $params): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Xendit belum dikonfigurasi.'];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->post($this->baseUrl.'/fixed_payment_code', $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'id' => $data['id'] ?? null,
                    'external_id' => $data['external_id'] ?? null,
                    'payment_code' => $data['payment_code'] ?? null,
                    'retail_outlet_name' => $data['retail_outlet_name'] ?? null,
                    'expected_amount' => $data['expected_amount'] ?? null,
                    'status' => $data['status'] ?? null,
                ];
            }

            Log::error('Xendit createRetailOutlet failed: '.$response->body());

            return [
                'success' => false,
                'message' => $response->json('message') ?? $response->json('error_code') ?? 'Gagal membuat kode pembayaran minimarket Xendit.',
            ];
        } catch (\Exception $e) {
            Log::error('Xendit createRetailOutlet error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getRetailOutlet(string $id): ?array
    {
        if (! $this->isConfigured() || $id === '') {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->get($this->baseUrl.'/fixed_payment_code/'.$id);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit getRetailOutlet failed: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit getRetailOutlet error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Buat e-wallet charge (GoPay/DANA/OVO/ShopeePay).
     * Catatan: e-wallet charge umumnya WAJIB redirect (is_redirect_required=true);
     * arahkan pelanggan ke checkout_url dari response.
     */
    public function createEwalletCharge(array $params): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Xendit belum dikonfigurasi.'];
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->post($this->baseUrl.'/ewallets/charges', $params);

            if ($response->successful()) {
                $data = $response->json();
                $actions = $data['actions'] ?? [];
                $checkoutUrl = $actions['mobile_web_checkout_url']
                    ?? $actions['desktop_web_checkout_url']
                    ?? $actions['mobile_deeplink_checkout_url']
                    ?? null;

                return [
                    'success' => true,
                    'id' => $data['id'] ?? null,
                    'reference_id' => $data['reference_id'] ?? null,
                    'status' => $data['status'] ?? null,
                    'channel_code' => $data['channel_code'] ?? null,
                    'checkout_url' => $checkoutUrl,
                    'is_redirect_required' => (bool) ($data['is_redirect_required'] ?? true),
                    'qr_string' => $actions['qr_checkout_string'] ?? null,
                    'actions' => $actions,
                ];
            }

            Log::error('Xendit createEwalletCharge failed: '.$response->body());

            return [
                'success' => false,
                'message' => $response->json('message') ?? $response->json('error_code') ?? 'Gagal membuat charge e-wallet Xendit.',
            ];
        } catch (\Exception $e) {
            Log::error('Xendit createEwalletCharge error: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getEwalletCharge(string $id): ?array
    {
        if (! $this->isConfigured() || $id === '') {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->get($this->baseUrl.'/ewallets/charges/'.$id);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit getEwalletCharge failed: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Xendit getEwalletCharge error: '.$e->getMessage());

            return null;
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

    /**
     * Ambil channel pembayaran yang benar-benar tersedia & diaktifkan pada
     * akun Xendit (GET /payment_channels). Hasil di-cache sebentar.
     *
     * Return array berisi channel code yang enabled, contoh: ['QRIS','DANA','OVO',...].
     * Bila gagal / tidak dikonfigurasi, return null (pemanggil boleh pakai fallback).
     */
    public function availableChannels(): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return \Illuminate\Support\Facades\Cache::remember('xendit.available_channels', 600, function () {
            try {
                $response = Http::withBasicAuth($this->secretKey, '')
                    ->acceptJson()
                    ->get($this->baseUrl.'/payment_channels');

                if (! $response->successful()) {
                    Log::warning('Xendit get payment channels failed: '.$response->status());

                    return null;
                }

                return collect($response->json())
                    ->filter(fn ($ch) => ! empty($ch['is_enabled']))
                    ->pluck('channel_code')
                    ->map(fn ($c) => strtoupper((string) $c))
                    ->values()
                    ->all();
            } catch (\Exception $e) {
                Log::warning('Xendit get payment channels error: '.$e->getMessage());

                return null;
            }
        });
    }

    /**
     * Map code metode pembayaran internal ke channel code Xendit.
     */
    public function channelCodeFor(string $method): string
    {
        return match (strtolower($method)) {
            'qris' => 'QRIS',
            'gopay' => 'GOPAY',
            'dana' => 'DANA',
            'ovo' => 'OVO',
            'shopeepay' => 'SHOPEEPAY',
            'linkaja', 'link_aja' => 'LINKAJA',
            'bca_va', 'bca' => 'BCA',
            'bri_va', 'bri' => 'BRI',
            'bni_va', 'bni' => 'BNI',
            'mandiri_va', 'mandiri' => 'MANDIRI',
            'permata_va', 'permata' => 'PERMATA',
            'alfamart' => 'ALFAMART',
            'indomaret' => 'INDOMARET',
            default => strtoupper((string) $method),
        };
    }
}
