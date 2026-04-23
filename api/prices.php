<?php
/**
 * JSON endpoint لأسعار الذهب - يستخدمه الـ JS في الصفحة لتحديث الأسعار تلقائيًا.
 */

require_once __DIR__ . '/../includes/prices.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
header('Access-Control-Allow-Origin: *');

$prices = getGoldPrices();

echo json_encode([
    'karat24'    => $prices['karat24'],
    'karat21'    => $prices['karat21'],
    'karat18'    => $prices['karat18'],
    'ounce'      => $prices['ounce']      ?? null,
    'pound'      => $prices['pound']      ?? null,
    'updated_at' => (int) ($prices['updated_at'] ?? 0),
    'source'     => $prices['source']     ?? 'unknown',
    'stale'      => (bool) ($prices['stale'] ?? false),
    'currency'   => 'EGP',
], JSON_UNESCAPED_UNICODE);
