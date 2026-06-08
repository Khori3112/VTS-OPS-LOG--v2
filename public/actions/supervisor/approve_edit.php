<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Supervisor');

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

$requestId = (int) ($data['request_id'] ?? 0);
$csrfToken = (string) ($data['csrf_token'] ?? '');
$action = (string) ($data['action'] ?? '');
$minutes = isset($data['minutes']) ? (int) $data['minutes'] : 15;

if ($minutes < 1 || $minutes > 120) {
    $minutes = 15;
}

if (!hash_equals(csrfToken(), $csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF token tidak valid.']);
    exit;
}

if ($requestId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parameter tidak valid.']);
    exit;
}

$supervisorNip = (string) ($_SESSION['user']['nip'] ?? '');

try {
    $pdo = getPdo();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'SELECT id, table_name, record_id, requested_by_nip, status
         FROM edit_requests
         WHERE id = :id AND status = "pending"
         LIMIT 1
         FOR UPDATE'
    );
    $stmt->execute([':id' => $requestId]);
    $req = $stmt->fetch();

    if (!$req) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Permintaan tidak ditemukan atau sudah diproses.']);
        exit;
    }

    $recordId = (int) $req['record_id'];

    if ($action === 'approve') {
        $reportStmt = $pdo->prepare(
            'SELECT id, final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1'
        );
        $reportStmt->execute([':id' => $recordId]);
        $report = $reportStmt->fetch();

        if (!$report || (int) ($report['final_copy_watermark'] ?? 0) === 1) {
            $pdo->prepare(
                'UPDATE edit_requests SET status = "rejected", resolved_at = NOW(), updated_at = NOW() WHERE id = :id'
            )->execute([':id' => $requestId]);
            $pdo->commit();
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Laporan sudah final, permintaan ditolak otomatis.']);
            exit;
        }

        $unlockUntil = (new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta')))
            ->modify("+{$minutes} minutes")
            ->format('Y-m-d H:i:s');

        $pdo->prepare(
            'UPDATE edit_requests
             SET status = "approved", resolved_at = NOW(), unlocked_until = :unlocked_until, updated_at = NOW()
             WHERE id = :id'
        )->execute([':unlocked_until' => $unlockUntil, ':id' => $requestId]);

        logAudit(
            $pdo,
            $supervisorNip,
            'UNLOCK',
            'daily_shift_reports',
            $recordId,
            ['minutes' => $minutes, 'unlocked_until' => $unlockUntil, 'requested_by' => $req['requested_by_nip']],
            'Supervisor memberikan akses edit sementara pada laporan'
        );

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Akses edit diberikan selama ' . $minutes . ' menit.']);
    } else {
        $pdo->prepare(
            'UPDATE edit_requests SET status = "rejected", resolved_at = NOW(), updated_at = NOW() WHERE id = :id'
        )->execute([':id' => $requestId]);

        logAudit(
            $pdo,
            $supervisorNip,
            'UPDATE',
            'edit_requests',
            $requestId,
            ['status' => 'rejected', 'record_id' => $recordId],
            'Supervisor menolak permintaan edit laporan'
        );

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Permintaan ditolak.']);
    }
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
}
