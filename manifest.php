<?php
declare(strict_types=1);

require_once __DIR__ . '/config/app.php';

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=604800');

$base = BASE_URL;
$icon = $base . 'assets/img/logo_navigasi.png';

echo json_encode([
    'name'             => 'VTS-OPS-LOG — Palembang',
    'short_name'       => 'VTS-OPS',
    'description'      => 'Vessel Traffic Service Operational Log — DISNAV Type B Palembang',
    'start_url'        => $base . 'public/dashboard.php',
    'scope'            => $base,
    'display'          => 'standalone',
    'orientation'      => 'portrait-primary',
    'background_color' => '#0d1117',
    'theme_color'      => '#002147',
    'lang'             => 'id',
    'icons'            => [
        ['src' => $icon, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ['src' => $icon, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
    ],
    'categories'       => ['productivity', 'navigation'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
