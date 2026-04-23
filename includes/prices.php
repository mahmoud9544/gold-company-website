<?php
/**
 * أسعار الذهب المباشرة - يتم جلبها من مصدر مصري (goldpriceegypt.com)
 * مع كاش لمدة 30 دقيقة للحد من عدد الطلبات، ورجوع آمن للقيم القديمة عند فشل الاتصال.
 *
 * ترجع الدالة getGoldPrices() مصفوفة بالشكل التالي:
 *   [
 *     'karat24'     => float سعر جرام عيار 24 بالجنيه المصري,
 *     'karat21'     => float سعر جرام عيار 21,
 *     'karat18'     => float سعر جرام عيار 18,
 *     'ounce'       => float|null سعر الأونصة,
 *     'pound'       => float|null سعر الجنيه الذهب,
 *     'updated_at'  => int Unix timestamp لآخر تحديث ناجح,
 *     'source'      => string اسم المصدر,
 *     'stale'       => bool هل القيم مرجوعة من كاش قديم بسبب فشل الاتصال,
 *   ]
 */

if (!defined('GOLD_PRICES_CACHE_TTL')) {
    define('GOLD_PRICES_CACHE_TTL', 30 * 60); // 30 minutes
}

if (!function_exists('gold_prices_cache_path')) {
    function gold_prices_cache_path(): string
    {
        return dirname(__DIR__) . '/cache/gold_prices.json';
    }
}

if (!function_exists('gold_prices_default_fallback')) {
    function gold_prices_default_fallback(): array
    {
        // قيم افتراضية تُستخدم فقط إذا فشل الاتصال وما فيش كاش.
        return [
            'karat24'    => 8000.0,
            'karat21'    => 7000.0,
            'karat18'    => 6000.0,
            'ounce'      => null,
            'pound'      => null,
            'updated_at' => 0,
            'source'     => 'fallback',
            'stale'      => true,
        ];
    }
}

if (!function_exists('gold_prices_fetch_url')) {
    function gold_prices_fetch_url(string $url, int $timeout = 8): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => $timeout,
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; RoyalGoldBot/1.0; +https://royal-gold.example)',
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $body = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (is_string($body) && $code >= 200 && $code < 400 && $body !== '') {
                return $body;
            }
            return null;
        }

        $ctx = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'timeout'       => $timeout,
                'header'        => "User-Agent: Mozilla/5.0 (compatible; RoyalGoldBot/1.0)\r\n",
                'follow_location' => 1,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);
        $body = @file_get_contents($url, false, $ctx);
        return is_string($body) && $body !== '' ? $body : null;
    }
}

if (!function_exists('gold_prices_parse_goldpriceegypt')) {
    function gold_prices_parse_goldpriceegypt(string $html): ?array
    {
        // الـ meta description يحتوي على السطر المختصر بالشكل:
        // عيار 18: 6022 | 21: 7025 | 22: 7360 | 24: 8029 | الجنيه: 56200 | أونصة: 249730 | DD-MM-YYYY
        if (!preg_match(
            '/عيار\s*18\s*:\s*([0-9]+)\s*\|\s*21\s*:\s*([0-9]+)\s*\|\s*22\s*:\s*([0-9]+)\s*\|\s*24\s*:\s*([0-9]+)/u',
            $html,
            $m
        )) {
            return null;
        }

        $karat18 = (float) $m[1];
        $karat21 = (float) $m[2];
        $karat24 = (float) $m[4];

        if ($karat18 <= 0 || $karat21 <= 0 || $karat24 <= 0) {
            return null;
        }

        $ounce = null;
        $pound = null;
        if (preg_match('/الجنيه\s*:\s*([0-9]+)/u', $html, $mp)) {
            $pound = (float) $mp[1];
        }
        if (preg_match('/أونصة\s*:\s*([0-9]+)/u', $html, $mo)) {
            $ounce = (float) $mo[1];
        }

        return [
            'karat24'    => $karat24,
            'karat21'    => $karat21,
            'karat18'    => $karat18,
            'ounce'      => $ounce,
            'pound'      => $pound,
            'updated_at' => time(),
            'source'     => 'goldpriceegypt.com',
            'stale'      => false,
        ];
    }
}

if (!function_exists('gold_prices_read_cache')) {
    function gold_prices_read_cache(): ?array
    {
        $path = gold_prices_cache_path();
        if (!is_file($path)) {
            return null;
        }
        $raw = @file_get_contents($path);
        if (!is_string($raw) || $raw === '') {
            return null;
        }
        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['karat24'], $data['karat21'], $data['karat18'], $data['updated_at'])) {
            return null;
        }
        return $data;
    }
}

if (!function_exists('gold_prices_write_cache')) {
    function gold_prices_write_cache(array $data): void
    {
        $path = gold_prices_cache_path();
        $dir  = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }
}

if (!function_exists('getGoldPrices')) {
    function getGoldPrices(bool $forceRefresh = false): array
    {
        $cached = gold_prices_read_cache();
        $now    = time();

        if (!$forceRefresh
            && $cached !== null
            && isset($cached['updated_at'])
            && ($now - (int) $cached['updated_at']) < GOLD_PRICES_CACHE_TTL
        ) {
            $cached['stale'] = false;
            return $cached;
        }

        $html = gold_prices_fetch_url('https://www.goldpriceegypt.com/');
        $parsed = $html !== null ? gold_prices_parse_goldpriceegypt($html) : null;

        if ($parsed !== null) {
            gold_prices_write_cache($parsed);
            return $parsed;
        }

        // فشل الاتصال أو التحليل - نرجع القيم القديمة مع تعليمها كـ stale
        if ($cached !== null) {
            $cached['stale']  = true;
            $cached['source'] = ($cached['source'] ?? 'cache') . ' (cached)';
            return $cached;
        }

        return gold_prices_default_fallback();
    }
}

if (!function_exists('formatGoldPrice')) {
    function formatGoldPrice(float $value): string
    {
        return number_format($value, 0, '.', ',');
    }
}
