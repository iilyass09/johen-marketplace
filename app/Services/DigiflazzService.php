<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    protected string $username = '';

    protected string $key = '';

    protected string $baseUrl = 'https://api.digiflazz.com/v1';

    protected bool $production = false;

    protected bool $simulation = false;

    protected float $marginPercent = 5.0;

    public function __construct()
    {
        $this->username = (string) (SiteSetting::get('digiflazz_username') ?? config('digiflazz.username', ''));
        $this->key = (string) (SiteSetting::get('digiflazz_key') ?? config('digiflazz.key', ''));
        $this->baseUrl = (string) config('digiflazz.base_url', 'https://api.digiflazz.com/v1');
        $this->production = (bool) (SiteSetting::get('digiflazz_production') === '1' || config('digiflazz.production', false));
        $this->simulation = (bool) config('services.payment.simulation', false);
        $this->marginPercent = self::readMarginPercent();
    }

    /**
     * Margin (%) untuk menghitung selling_price dari harga Digiflazz.
     * Disimpan di SiteSetting agar bisa diedit dari panel admin.
     */
    protected static function readMarginPercent(): float
    {
        $raw = SiteSetting::get('digiflazz_margin_percent', config('digiflazz.margin_percent', 5));

        if (! is_numeric($raw)) {
            return 5.0;
        }

        return max(0.0, min(100.0, (float) $raw));
    }

    public function getMarginPercent(): float
    {
        return $this->marginPercent;
    }

    /**
     * Harga yang dipakai sebagai basis margin, yaitu harga yang benar-benar
     * kita bayar ke Digiflazz.
     *
     * Dokumentasi resmi price list hanya mengirim satu field harga: `price`
     * ("Harga produk yang ditentukan oleh seller"). Tidak ada `original_price`,
     * jadi jangan dipakai sebagai basis.
     */
    protected function resolveCost(array $item): float
    {
        $cost = $item['price'] ?? 0;

        if (! is_numeric($cost)) {
            return 0.0;
        }

        return round((float) $cost, 2);
    }

    /**
     * Harga jual = harga beli + margin, dibulatkan ke ribuan terdekat
     * (contoh: 12 110 -> 13 000), lalu dijamin tidak lebih murah dari
     * harga Digiflazz.
     */
    protected function resolveSellingPrice(array $item): float
    {
        $cost = $this->resolveCost($item);

        $withMargin = $cost * (1 + ($this->marginPercent / 100));
        $rounded = round($withMargin / 1000) * 1000;

        // Produk sangat murah (< 500) akan bulatan ke ribuan jadi 0.
        if ($rounded <= 0) {
            $rounded = $cost;
        }

        // Margin 0% tidak boleh membuat produk lebih murah dari harga Digiflazz.
        return max($rounded, $cost);
    }

    public function isSimulation(): bool
    {
        return $this->simulation;
    }

    public function isConfigured(): bool
    {
        return $this->username !== '' && $this->key !== '';
    }

    /**
     * API key dipakai untuk verifikasi signature webhook (X-Hub-Signature).
     */
    public function getKey(): string
    {
        return $this->key;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isProduction(): bool
    {
        return $this->production;
    }

    /*
     * Deteksi konfigurasi yang berisiko (mis. key "dev-" tapi production=true).
     * Dipakai halaman status gateway di admin sebagai peringatan.
     */
    public function configProblems(): array
    {
        $problems = [];

        if ($this->username === '' || $this->key === '') {
            $problems[] = 'Digiflazz belum dikonfigurasi (username/key kosong).';
        }

        $isDevKey = str_starts_with(strtolower($this->key), 'dev-');

        if ($this->production && $isDevKey) {
            $problems[] = 'Key Digiflazz ber-prefix "dev-" tetapi DIGIFLAZZ_PRODUCTION=true. Transaksi akan dikirim sebagai produksi dengan key development. Periksa kembali.';
        }

        return $problems;
    }

    public function testConnection(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Digiflazz belum dikonfigurasi.'];
        }

        try {
            // Paksa refresh: tanpa ini tombol "Uji" bisa melapor sukses
            // dari cache 1 jam tanpa benar-benar menyentuh API.
            $data = $this->getPriceList(true);
            if (! empty($data)) {
                return ['success' => true, 'message' => 'Koneksi berhasil. '.count($data).' produk tersedia.', 'count' => count($data)];
            }

            return ['success' => false, 'message' => 'Gagal mengambil data. Periksa username & key.'];
        } catch (\Exception $e) {
            Log::error('Digiflazz connection test failed: '.$e->getMessage());

            return ['success' => false, 'message' => 'Koneksi gagal: '.$e->getMessage()];
        }
    }

    /**
     * Format respons cek-saldo menjadi label Rupiah, atau null bila gagal.
     */
    public function formatBalance(array $result): ?string
    {
        $rc = $result['data']['rc'] ?? $result['rc'] ?? null;

        if ($rc !== '00') {
            return null;
        }

        $balance = $result['data']['balance'] ?? $result['balance'] ?? null;

        if (! is_numeric($balance)) {
            return null;
        }

        return 'Rp'.number_format((float) $balance, 0, ',', '.');
    }

    /**
     * Ambil saldo lalu format ke Rupiah. Melakukan satu panggilan API.
     */
    public function balanceLabel(): ?string
    {
        return $this->formatBalance($this->checkBalance());
    }

    public function checkBalance(): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'message' => 'Digiflazz belum dikonfigurasi.'];
        }

        $sign = md5($this->username.$this->key.'depo');

        try {
            $response = Http::post($this->baseUrl.'/cek-saldo', [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $sign,
            ]);

            if ($response->failed()) {
                return ['success' => false, 'message' => 'HTTP error: '.$response->status()];
            }

            return (array) $response->json();
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

        $gameBrands = $this->gameBrandNames();

        // Dihitung SEBELUM upsert. Kalau dihitung sesudahnya, produk yang baru
        // diimpor ikut terhitung dan pengaman selalu ikut terpicu.
        $activeBefore = Product::query()->where('is_active', true)->count();

        $count = 0;
        $skipped = 0;
        $seenSkus = [];

        Log::info('Digiflazz sync: processing '.count($data).' products from API.');

        foreach ($data as $item) {
            $sku = $item['buyer_sku_code'] ?? null;

            if (! $sku) {
                $skipped++;

                continue;
            }

            // Katalog Digiflazz mencampur game dan pulsa/kuota. Hanya brand
            // yang terdaftar sebagai game di tabel brands yang diimpor.
            if ($gameBrands !== [] && ! $this->brandIsGame((string) ($item['brand'] ?? ''), $gameBrands)) {
                $skipped++;

                continue;
            }

            $seenSkus[] = $sku;

            $stock = $item['stock'] ?? null;
            $unlimited = (bool) ($item['unlimited_stock'] ?? false);

            Product::updateOrCreate(
                ['buyer_sku_code' => $sku],
                [
                    'brand' => $item['brand'],
                    'category' => $item['category'],
                    'product_name' => $item['product_name'],
                    'price' => $this->resolveCost($item),
                    'selling_price' => $this->resolveSellingPrice($item),
                    'type' => $item['type'],
                    'is_active' => ($item['buyer_product_status'] === true || $item['buyer_product_status'] === 1 || $item['buyer_product_status'] === '1'),
                    'stock' => $stock !== null ? (int) $stock : ($unlimited ? 9999 : 0),
                ]
            );
            $count++;
        }

        $orphan = $this->deactivateOrphanedProducts($seenSkus, $gameBrands, $activeBefore);
        $deactivated = $orphan['count'];

        SiteSetting::set('digiflazz_last_sync', now()->toDateTimeString());
        SiteSetting::set('digiflazz_product_count', (string) $count);

        Log::info("Digiflazz sync completed: {$count} products synced, {$skipped} skipped, {$deactivated} products deactivated.");

        $message = "{$count} produk berhasil disinkronisasi.";

        if ($skipped > 0) {
            $message .= " {$skipped} dilewati (di luar katalog game).";
        }

        if ($deactivated > 0) {
            $message .= " {$deactivated} produk nonaktif karena tidak lagi ada di Digiflazz.";
        }

        if ($orphan['skipped']) {
            $message .= " Auto-deactivate dilewati: {$orphan['reason']}.";
        }

        return ['success' => true, 'message' => $message, 'count' => $count, 'skipped' => $skipped, 'deactivated' => $deactivated, 'deactivate_skipped' => $orphan['skipped']];
    }

    /**
     * Nama brand (lowercase) yang ditandai sebagai katalog game.
     * Brand Digiflazz dan brand di tabel brands tidak selalu sama kapitalisasinya.
     *
     * @return array<int, string>
     */
    protected function gameBrandNames(): array
    {
        return Brand::query()
            ->where('catalog_group', 'game')
            ->pluck('name')
            ->map(fn ($name) => mb_strtolower(trim((string) $name)))
            ->all();
    }

    /**
     * Normalisasi nama brand: lowercase, hanya huruf & angka.
     *
     * Nama brand di price list Digiflazz tidak konsisten — publisher sering
     * ikut disertakan dan tanda baca vary:
     *   "Konami eFootball" vs brand "E-Football"
     *   "miHoYo Genshin Impact" vs brand "Genshin Impact"
     *   "MOBILE LEGENDS" vs brand "Mobile Legends"
     */
    protected function normalizeBrand(string $brand): string
    {
        return (string) preg_replace('/[^a-z0-9]+/', '', mb_strtolower(trim($brand)));
    }

    /**
     * Cocokkan nama brand dari price list Digiflazz ke tabel brands.
     */
    protected function brandIsGame(string $digiflazzBrand, array $gameBrands): bool
    {
        $needle = $this->normalizeBrand($digiflazzBrand);

        if ($needle === '') {
            return false;
        }

        foreach ($gameBrands as $brand) {
            $candidate = $this->normalizeBrand($brand);

            // Nama pendek (mis. "MLBB") rawan false positive, jadi minimal 4 karakter.
            if (strlen($candidate) < 4) {
                continue;
            }

            if (str_contains($needle, $candidate) || str_contains($candidate, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Nonaktifkan produk yang tidak ada lagi di price list Digiflazz.
     * Tanpa ini SKU lama tetap aktif dan bisa dibeli padahal sudah tidak berlaku.
     *
     * @param  array<int, string>  $seenSkus
     * @return array{count: int, skipped: bool, reason: string|null}
     */
    protected function deactivateOrphanedProducts(array $seenSkus, array $gameBrands, int $activeBefore): array
    {
        if ($seenSkus === []) {
            return ['count' => 0, 'skipped' => true, 'reason' => 'tidak ada SKU game yang cocok di price list'];
        }

        $orphanQuery = Product::query()->where('is_active', true);

        if ($gameBrands !== []) {
            $allBrands = Brand::query()->pluck('name')->map(fn ($n) => mb_strtolower(trim((string) $n)))->all();
            $orphanQuery->whereIn(
                DB::raw('LOWER(brand)'),
                $allBrands !== [] ? $allBrands : ['__none__']
            );
        }

        // Pengaman: bila data yang masuk jauh lebih sedikit dari katalog aktif
        // sebelum sinkronisasi, ini hampir pasti respons terpotong, rate-limit,
        // atau akses produk terbatas -- bukan katalog yang benar-benar menyusut.
        // Menonaktifkan semuanya akan membuat seluruh produk gagal dibeli.
        if ($activeBefore > 0 && count($seenSkus) < (int) floor($activeBefore * 0.5)) {
            Log::warning('Digiflazz sync: lewati auto-deactivate. '.count($seenSkus).' SKU masuk vs '.$activeBefore.' produk aktif sebelumnya.');

            return [
                'count' => 0,
                'skipped' => true,
                'reason' => 'hanya '.count($seenSkus).' dari '.$activeBefore.' produk aktif sebelumnya ditemukan di Digiflazz, auto-deactivate dilewati demi keamanan',
            ];
        }

        $count = $orphanQuery
            ->whereNotIn('buyer_sku_code', $seenSkus)
            ->update(['is_active' => false]);

        return ['count' => $count, 'skipped' => false, 'reason' => null];
    }

    public function topUp(string $buyerSkuCode, string $customerNumber, string $refId, ?string $zoneId = null): array
    {
        $customerNo = $this->buildCustomerNo($customerNumber, $zoneId);

        if ($this->simulation) {
            Log::info('Digiflazz SIMULASI topUp', ['ref_id' => $refId, 'sku' => $buyerSkuCode, 'customer_no' => $customerNo]);

            // Balas "Pending" agar aliran order: processing → polling → sukses tetap teruji.
            return $this->simulateResult($refId, $buyerSkuCode, $customerNo, 'Pending');
        }

        $sign = md5($this->username.$this->key.$refId);

        try {
            $response = Http::post($this->baseUrl.'/transaction', [
                'cmd' => 'topup',
                'username' => $this->username,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNo,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz topUp failed: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function checkStatus(string $buyerSkuCode, string $customerNumber, string $refId, ?string $zoneId = null): array
    {
        $customerNo = $this->buildCustomerNo($customerNumber, $zoneId);

        if ($this->simulation) {
            Log::info('Digiflazz SIMULASI checkStatus', ['ref_id' => $refId]);

            return $this->simulateResult($refId, $buyerSkuCode, $customerNo, 'Sukses');
        }

        $sign = md5($this->username.$this->key.$refId);

        try {
            $response = Http::post($this->baseUrl.'/transaction', [
                'cmd' => 'status',
                'username' => $this->username,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNo,
                'ref_id' => $refId,
                'sign' => $sign,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Digiflazz checkStatus failed: '.$e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Respons tiruan dengan format identik respons transaksi Digiflazz.
     */
    protected function simulateResult(string $refId, string $buyerSkuCode, string $customerNo, string $status): array
    {
        $sukses = $status === 'Sukses';

        return [
            'data' => [
                'rc' => $sukses ? '00' : '68',
                'message' => $sukses ? 'TRANSACTION SUCCESSFUL' : 'TRANSACTION PENDING',
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNo,
                'ref_id' => $refId,
                'status' => $status,
                'sn' => $sukses ? 'SIM'.substr(md5($refId), 0, 12) : '',
                'price' => 0,
            ],
        ];
    }

    /**
     * Format customer_no untuk Digiflazz:
     * game ber-Zone ID memerlukan format "userid.zoneid".
     */
    protected function buildCustomerNo(string $customerNumber, ?string $zoneId): string
    {
        $customerNumber = trim($customerNumber);
        $zoneId = trim((string) $zoneId);

        if ($zoneId === '' || str_contains($customerNumber, '.')) {
            return $customerNumber;
        }

        return $customerNumber.'.'.$zoneId;
    }
}
