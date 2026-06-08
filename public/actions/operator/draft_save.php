<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Operator');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = is_string($raw) ? (json_decode($raw, true) ?? []) : [];

$rid  = (int) ($data['daily_shift_report_id'] ?? 0);
$csrf = (string) ($data['csrf_token'] ?? '');

if (!hash_equals(csrfToken(), $csrf)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token tidak valid']);
    exit;
}

if ($rid <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Report ID tidak valid']);
    exit;
}

$vessels = $data['vessels'] ?? [];
if (!is_array($vessels)) {
    $vessels = [];
}

// Keep max 50 rows, strip extra keys for safety
$clean = [];
$allowedKeys = ['vessel_name', 'call_sign', 'last_port', 'next_port', 'vessel_type', 'agent', 'direction', 'remarks'];
foreach (array_slice($vessels, 0, 50) as $row) {
    if (!is_array($row)) continue;
    $cleanRow = [];
    foreach ($allowedKeys as $k) {
        $cleanRow[$k] = htmlspecialchars((string) ($row[$k] ?? ''), ENT_QUOTES, 'UTF-8');
    }
    $clean[] = $cleanRow;
}

if (!isset($_SESSION['draft_vessels'])) {
    $_SESSION['draft_vessels'] = [];
}
$_SESSION['draft_vessels'][$rid] = $clean;

echo json_encode([
    'success'  => true,
    'saved_at' => date('H:i:s'),
    'count'    => count($clean),
]);
