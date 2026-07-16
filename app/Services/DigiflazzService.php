<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    protected string $username = '';
    protected string $key = '';
    protected string $baseUrl = 'https://api.digiflazz.com/v1';
    protected bool $production = false;

    public function __construct()
    {
        $this->username = (string) config('digiflazz.username', '');
        $this->key = (string) config('digiflazz.key', '');
        $this->baseUrl = (string) config('digiflazz.base_url', 'https://api.digiflazz.com/v1');
        $this->production = (bool) config('digiflazz.production', false);
    }

    public function isConfigured(): bool
    {
        return $this->username !== '' && $this->key !== '';
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Digiflazz belum dikonfigurasi.'];
        }

        try {
            $data = $this->getPriceList();
            if (!empty($data)) {
                return ['success' => true, 'message' => 'Koneksi berhasil. ' . count($data) . ' produk tersedia.', 'count' => count($data)];
            }
            return ['success' => false, 'message' => 'Gagal mengambil data. Periksa username & key.'];
        } catch (\Exception $e) {
            Log::error('Digiflazz connection test failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Koneksi gagal: ' . $e->getMessage()];
        }
    }

    public function checkBalance(): array
    {
        $sign = md5($this->username . $this->key . 'depo');

        try {
            $response = Http::post($this->baseUrl . '/cek-saldo', [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz checkBalance failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getPriceList(): array
    {
        $sign = md5($this->username . $this->key . 'pricelist');

        try {
            $response = Http::post($this->baseUrl . '/price-list', [
                'cmd' => 'prepaid',
                'username' => $this->username,
                'sign' => $sign,
            ]);

            $data = $response->json();

            if (isset($data['data'])) {
                return $data['data'];
            }

            if (isset($data['rc']) && $data['rc'] !== '00') {
                Log::error('Digiflazz price list error: ' . ($data['message'] ?? 'Unknown error'));
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Digiflazz getPriceList failed: ' . $e->getMessage());
            return [];
        }
    }

    public function syncProducts(): array
    {
        $data = $this->getPriceList();

        if (empty($data)) {
            if (!$this->isConfigured()) {
                return ['success' => false, 'message' => 'Digiflazz belum dikonfigurasi.'];
            }
            return ['success' => false, 'message' => 'Gagal mengambil data dari Digiflazz. Periksa username & key.'];
        }

        $count = 0;
        foreach ($data as $item) {
            Product::updateOrCreate(
                ['buyer_sku_code' => $item['buyer_sku_code']],
                [
                    'brand' => $item['brand'],
                    'category' => $item['category'],
                    'product_name' => $item['product_name'],
                    'price' => (int) $item['price'],
                    'selling_price' => (int) $item['price'] + (int) round($item['price'] * 0.05),
                    'type' => $item['type'],
                    'is_active' => ($item['buyer_product_status'] === true),
                    'stock' => (int) ($item['stock'] ?? ($item['unlimited_stock'] ? 9999 : 0)),
                ]
            );
            $count++;
        }

        \App\Models\SiteSetting::set('digiflazz_last_sync', now()->toDateTimeString());
        \App\Models\SiteSetting::set('digiflazz_product_count', (string) $count);

        return ['success' => true, 'message' => "{$count} produk berhasil disinkronisasi.", 'count' => $count];
    }

    public function topUp(string $buyerSkuCode, string $customerNumber, string $refId): array
    {
        $sign = md5($this->username . $this->key . $refId);

        try {
            $response = Http::post($this->baseUrl . '/transaction', [
                'cmd' => 'topup',
                'username' => $this->username,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNumber,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz topUp failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkStatus(string $buyerSkuCode, string $customerNumber, string $refId): array
    {
        $sign = md5($this->username . $this->key . $refId);

        try {
            $response = Http::post($this->baseUrl . '/transaction', [
                'cmd' => 'status',
                'username' => $this->username,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNumber,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz checkStatus failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
