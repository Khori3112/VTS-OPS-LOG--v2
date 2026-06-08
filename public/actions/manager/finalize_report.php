<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Manager');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payload tidak valid.']);
    exit;
}

$reportId   = (int) ($data['daily_shift_report_id'] ?? 0);
$csrfToken  = (string) ($data['csrf_token'] ?? '');

if (!hash_equals(csrfToken(), $csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF token tidak valid.']);
    exit;
}

if ($reportId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID laporan tidak valid.']);
    exit;
}

$managerNip = (string) ($_SESSION['user']['nip'] ?? '');

try {
    $pdo = getPdo();
    $pdo->beginTransaction();

    $checkStmt = $pdo->prepare(
        'SELECT id, is_locked, final_copy_watermark
         FROM daily_shift_reports
         WHERE id = :id
         LIMIT 1
         FOR UPDATE'
    );
    $checkStmt->execute([':id' => $reportId]);
    $report = $checkStmt->fetch();

    if (!$report) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Laporan tidak ditemukan.']);
        exit;
    }

    if ((int) $report['is_locked'] !== 1) {
        $pdo->rollBack();
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Laporan harus dikunci (Locked) sebelum dapat di-finalize.']);
        exit;
    }

    if ((int) $report['final_copy_watermark'] === 1) {
        $pdo->rollBack();
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Laporan sudah di-finalize sebelumnya.']);
        exit;
    }

    $pdo->prepare(
        'UPDATE daily_shift_reports
         SET final_copy_watermark = 1,
             is_locked             = 1,
             edit_requested        = "none",
             manager_acknowledged_at = NOW(),
             manager_nip           = :manager_nip
         WHERE id = :id'
    )->execute([':manager_nip' => $managerNip, ':id' => $reportId]);

    // Tolak semua edit request yang masih pending
    $pdo->prepare(
        'UPDATE edit_requests
         SET status = "rejected", resolved_at = NOW(), updated_at = NOW()
         WHERE table_name = "daily_shift_reports"
           AND record_id = :record_id
           AND status = "pending"'
    )->execute([':record_id' => $reportId]);

    logAudit(
        $pdo,
        $managerNip,
        'LOCK',
        'daily_shift_reports',
        $reportId,
        ['final_copy_watermark' => 1, 'manager_nip' => $managerNip],
        'Manager finalized report — Final Copy watermark applied'
    );

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Laporan berhasil di-finalize. Status: FINAL COPY.']);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
}
