<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
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
        $this->username = (string) (SiteSetting::get('digiflazz_username') ?? config('digiflazz.username', ''));
        $this->key = (string) (SiteSetting::get('digiflazz_key') ?? config('digiflazz.key', ''));
        $this->baseUrl = (string) config('digiflazz.base_url', 'https://api.digiflazz.com/v1');
        $this->production = (bool) (SiteSetting::get('digiflazz_production') === '1' || config('digiflazz.production', false));
    }

    public function isConfigured(): bool
    {
        return $this->username !== '' && $this->key !== '';
    }

    public function testConnection(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Digiflazz belum dikonfigurasi.'];
        }

        try {
            $data = $this->getPriceList();
            if (! empty($data)) {
                return ['success' => true, 'message' => 'Koneksi berhasil. '.count($data).' produk tersedia.', 'count' => count($data)];
            }

            return ['success' => false, 'message' => 'Gagal mengambil data. Periksa username & key.'];
        } catch (\Exception $e) {
            Log::error('Digiflazz connection test failed: '.$e->getMessage());

            return ['success' => false, 'message' => 'Koneksi gagal: '.$e->getMessage()];
        }
    }

    public function checkBalance(): array
    {
        $sign = md5($this->username.$this->key.'depo');

        try {
            $response = Http::post($this->baseUrl.'/cek-saldo', [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz checkBalance failed: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getPriceList(bool $forceRefresh = false): array
    {
        $cacheKey = 'digiflazz_pricelist_'.md5($this->username);

        if (! $forceRefresh && Cache::has($cacheKey)) {
            return (array) Cache::get($cacheKey);
        }

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        $sign = md5($this->username.$this->key.'pricelist');

        try {
            $response = Http::post($this->baseUrl.'/price-list', [
                'cmd' => 'prepaid',
                'username' => $this->username,
                'sign' => $sign,
            ]);

            if ($response->failed()) {
                Log::error('Digiflazz price list HTTP error: status='.$response->status());

                return [];
            }

            $data = $response->json();

            if (isset($data['rc']) && $data['rc'] !== '00') {
                Log::error('Digiflazz price list error: '.($data['message'] ?? 'Unknown error'));

                return [];
            }

            if (isset($data['data']['rc']) && $data['data']['rc'] !== '00') {
                Log::error('Digiflazz price list error: '.($data['data']['message'] ?? 'Unknown error'));

                return [];
            }

            $list = $data['data'] ?? [];

            if (! empty($list)) {
                Cache::put($cacheKey, $list, now()->addHour());
            }

            return is_array($list) ? $list : [];
        } catch (\Exception $e) {
            Log::error('Digiflazz getPriceList failed: '.$e->getMessage());

            return [];
        }
    }

    public function syncProducts(bool $forceRefresh = false): array
    {
        $data = $this->getPriceList($forceRefresh);

        if (empty($data)) {
            if (! $this->isConfigured()) {
                return ['success' => false, 'message' => 'Digiflazz belum dikonfigurasi.'];
            }

            return ['success' => false, 'message' => 'Gagal mengambil data dari Digiflazz. Periksa username & key.'];
        }

        $count = 0;

        Log::info('Digiflazz sync: processing '.count($data).' products from API.');

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

        SiteSetting::set('digiflazz_last_sync', now()->toDateTimeString());
        SiteSetting::set('digiflazz_product_count', (string) $count);

        Log::info("Digiflazz sync completed: {$count} products synced.");

        return ['success' => true, 'message' => "{$count} produk berhasil disinkronisasi.", 'count' => $count];
    }

    public function topUp(string $buyerSkuCode, string $customerNumber, string $refId): array
    {
        $sign = md5($this->username.$this->key.$refId);

        try {
            $response = Http::post($this->baseUrl.'/transaction', [
                'cmd' => 'topup',
                'username' => $this->username,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNumber,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz topUp failed: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkStatus(string $buyerSkuCode, string $customerNumber, string $refId): array
    {
        $sign = md5($this->username.$this->key.$refId);

        try {
            $response = Http::post($this->baseUrl.'/transaction', [
                'cmd' => 'status',
                'username' => $this->username,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNumber,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz checkStatus failed: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
