<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    protected string $username;
    protected string $key;
    protected string $baseUrl;
    protected bool $production;

    public function __construct()
    {
        $this->username = config('digiflazz.username');
        $this->key = config('digiflazz.key');
        $this->baseUrl = config('digiflazz.base_url');
        $this->production = config('digiflazz.production');
    }

    public function checkBalance(): array
    {
        $endpoint = $this->production ? '/cek-saldo' : '/cek-saldo';
        $sign = md5($this->username . $this->key . 'depo');

        $response = Http::post($this->baseUrl . $endpoint, [
            'cmd' => 'deposit',
            'username' => $this->username,
            'sign' => $sign,
        ]);

        return $response->json();
    }

    public function getPriceList(): array
    {
        $sign = md5($this->username . $this->key . 'pricelist');

        $response = Http::post($this->baseUrl . '/price-list', [
            'cmd' => 'prepaid',
            'username' => $this->username,
            'sign' => $sign,
        ]);

        $data = $response->json();

        if (isset($data['data'])) {
            return $data['data'];
        }

        return [];
    }

    public function topUp(string $buyerSkuCode, string $customerNumber, string $refId): array
    {
        $sign = md5($this->username . $this->key . $refId);

        $response = Http::post($this->baseUrl . '/transaction', [
            'cmd' => 'topup',
            'username' => $this->username,
            'buyer_sku_code' => $buyerSkuCode,
            'customer_no' => $customerNumber,
            'ref_id' => $refId,
            'sign' => $sign,
        ]);

        return $response->json();
    }

    public function checkStatus(string $buyerSkuCode, string $customerNumber, string $refId): array
    {
        $sign = md5($this->username . $this->key . $refId);

        $response = Http::post($this->baseUrl . '/transaction', [
            'cmd' => 'status',
            'username' => $this->username,
            'buyer_sku_code' => $buyerSkuCode,
            'customer_no' => $customerNumber,
            'ref_id' => $refId,
            'sign' => $sign,
        ]);

        return $response->json();
    }
}
