<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameAccountService
{
    /**
     * Deteksi keberadaan akun game.
     *
     * @return array{valid: bool, nickname: ?string, checked: bool}
     *         checked=false berarti brand tidak punya validator / input belum layak cek
     *         (frontend memperlakukan sebagai netral).
     */
    public function check(string $brand, string $userId, ?string $zoneId = null): array
    {
        $userId = trim($userId);
        $zoneId = trim((string) $zoneId);

        if ($this->formatValid($userId, $brand, $zoneId) === false) {
            return $this->result(false, null, false);
        }

        // Mode simulasi pembayaran: selalu terdeteksi agar UI bisa dites lokal.
        if (config('services.payment.simulation')) {
            $suffix = str_pad((string) (abs(crc32($userId.$zoneId)) % 10000), 4, '0', STR_PAD_LEFT);

            return $this->result(true, 'PlayerSim'.$suffix);
        }

        $resolver = $this->matchBrand($brand);

        if ($resolver === null) {
            return $this->result(false, null, false);
        }

        $cacheKey = 'gameaccount_'.md5($brand.'|'.$userId.'|'.$zoneId);
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        $checked = $this->resolve($resolver, $userId, $zoneId);

        // Hasil gagal-jaringan (null) tidak di-cache agar user bisa langsung coba lagi.
        if ($checked === null) {
            return $this->result(false, null, false);
        }

        Cache::put($cacheKey, $checked, now()->addMinutes((int) config('gameaccount.cache_ttl', 5)));

        return $checked;
    }

    protected function formatValid(string $userId, string $brand, string $zoneId): bool
    {
        if ($userId === '' || strlen($userId) < 5 || strlen($userId) > 32 || preg_match('/\s/', $userId)) {
            return false;
        }

        if ($this->requiresZone($brand) && $zoneId === '') {
            return false;
        }

        return true;
    }

    protected function requiresZone(string $brand): bool
    {
        return (bool) Brand::where('name', $brand)->value('requires_zone_id');
    }

    protected function matchBrand(string $brand): ?array
    {
        $brand = mb_strtolower(trim($brand));

        foreach ((array) config('gameaccount.brands', []) as $pattern => $resolver) {
            if (fnmatch(mb_strtolower($pattern), $brand)) {
                return $resolver;
            }
        }

        return null;
    }

    /**
     * @return array|null null berarti gagal jaringan/error → netral
     */
    protected function resolve(array $resolver, string $userId, ?string $zoneId): ?array
    {
        try {
            return match ($resolver['type']) {
                'enka' => $this->checkEnka($resolver, $userId),
                'isan' => $this->checkIsan($resolver, $userId, $zoneId),
                'gopay' => $this->checkGopay($resolver, $userId, $zoneId),
                default => $this->result(false, null),
            };
        } catch (\Throwable $e) {
            Log::warning('GameAccount resolve failed: '.$e->getMessage(), ['type' => $resolver['type'] ?? null]);

            return null;
        }
    }

    protected function checkEnka(array $resolver, string $userId): array
    {
        $path = str_replace('{user_id}', urlencode($userId), $resolver['path']);

        $response = Http::timeout((int) config('gameaccount.timeout', 5))
            ->acceptJson()
            ->withHeaders(['User-Agent' => 'JohenMarketplace/1.0'])
            ->get('https://enka.network/'.$path);

        if (! $response->successful()) {
            // 404 = akun tidak ditemukan; status lain tetap diperlakukan tidak valid.
            return $this->result(false, null);
        }

        $nickname = data_get($response->json(), $resolver['nickname_path']);

        if (! is_string($nickname) || trim($nickname) === '') {
            // Akun ada tapi profil privat / tanpa nama tampilan.
            return $this->result(false, null);
        }

        return $this->result(true, trim($nickname));
    }

    /**
     * Validator komunitas isan.eu.org (agregasi Codashop).
     * 200 + success=true → valid; selain itu → tidak ditemukan.
     */
    protected function checkIsan(array $resolver, string $userId, ?string $zoneId): array
    {
        $query = [];

        foreach ((array) ($resolver['params'] ?? []) as $key => $template) {
            $value = str_replace(['{user_id}', '{zone_id}'], [$userId, (string) $zoneId], $template);

            if (! str_contains($value, '{')) {
                $query[$key] = $value;
            }
        }

        $url = rtrim((string) config('gameaccount.isan_url'), '/').'/'.$resolver['game'];

        $response = Http::timeout((int) config('gameaccount.timeout', 5))
            ->acceptJson()
            ->get($url, $query);

        if (! $response->successful() || ($response->json('success')) !== true) {
            return $this->result(false, null);
        }

        $nickname = trim((string) $response->json('name'));

        if ($nickname === '') {
            return $this->result(false, null);
        }

        return $this->result(true, $nickname);
    }

    /**
     * Validator GoPay Games (gopay.co.id).
     * 2xx + success/message=success → valid, nickname di data.username dll;
     * selain itu (mis. 404 "Invalid user account") → tidak ditemukan.
     */
    protected function checkGopay(array $resolver, string $userId, ?string $zoneId): array
    {
        $response = Http::timeout((int) config('gameaccount.timeout', 5))
            ->acceptJson()
            ->post((string) config('gameaccount.gopay_url'), [
                'code' => $resolver['code'],
                'data' => [
                    'userId' => $userId,
                    'zoneId' => (string) ($zoneId ?? ''),
                ],
            ]);

        if (! $response->successful()) {
            return $this->result(false, null);
        }

        $body = $response->json() ?? [];

        $ok = ($body['success'] ?? null) === true
            || strcasecmp((string) ($body['message'] ?? ''), 'success') === 0;

        if (! $ok) {
            return $this->result(false, null);
        }

        foreach (['data.username', 'data.userAccount', 'data.nickname', 'data.name', 'username', 'userAccount'] as $path) {
            $nickname = trim((string) data_get($body, $path));

            if ($nickname !== '') {
                return $this->result(true, $nickname);
            }
        }

        return $this->result(false, null);
    }

    protected function result(bool $valid, ?string $nickname, bool $checked = true): array
    {
        return ['valid' => $valid, 'nickname' => $nickname, 'checked' => $checked];
    }
}
