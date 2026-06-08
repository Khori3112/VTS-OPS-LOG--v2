<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Operator');

	header('Content-Type: application/json');
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	    http_response_code(405);
	    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
	    exit;
	}

verifyCsrfOrDie('/views/operator/dashboard.php');

$user = $_SESSION['user'];
$dailyShiftReportId = (int) ($_POST['daily_shift_report_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');

if ($dailyShiftReportId <= 0 || $reason === '') {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'Alasan edit wajib diisi.']);
	exit;
}

try {
	$pdo = getPdo();

	$dsrStmt = $pdo->prepare('SELECT id, supervisor_nip, is_locked, final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
	$dsrStmt->execute([':id' => $dailyShiftReportId]);
	$report = $dsrStmt->fetch();

	if (!$report) {
	    http_response_code(404);
	    echo json_encode(['success' => false, 'message' => 'Shift report tidak ditemukan.']);
	    exit;
	}

	if ((int) ($report['final_copy_watermark'] ?? 0) === 1) {
	    http_response_code(409);
	    echo json_encode(['success' => false, 'message' => 'Laporan sudah final (Manager Acknowledge). Request edit tidak diizinkan.']);
	    exit;
	}

	$insert = $pdo->prepare(
		'INSERT INTO edit_requests (table_name, record_id, requested_by_nip, requested_to_nip, reason,
		                            status, unlocked_until, resolved_at)
		 VALUES (:table_name, :record_id, :requested_by_nip, :requested_to_nip, :reason,
		         "approved", DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW())'
	);

	$insert->execute([
		':table_name' => 'daily_shift_reports',
		':record_id' => $dailyShiftReportId,
		':requested_by_nip' => $user['nip'],
		':requested_to_nip' => $report['supervisor_nip'],
		':reason' => $reason,
	]);

	$updateShift = $pdo->prepare('UPDATE daily_shift_reports SET edit_requested = "approved" WHERE id = :id');
	$updateShift->execute([':id' => $dailyShiftReportId]);

    session_regenerate_id(true);

    logAudit(
        $pdo,
        $user['nip'],
        'REQUEST_EDIT',
        'daily_shift_reports',
        $dailyShiftReportId,
        ['reason' => $reason, 'requested_to_nip' => $report['supervisor_nip']],
        'Operator mengajukan permintaan edit — auto-disetujui (demo mode, 15 menit)'
    );

    echo json_encode(['success' => true, 'message' => 'Izin edit disetujui otomatis (demo mode). Anda punya 15 menit untuk mengedit.']);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
    exit;
}
