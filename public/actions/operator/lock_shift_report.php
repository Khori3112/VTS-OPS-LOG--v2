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

try {
	verifyCsrfOrDie('/views/operator/dashboard.php');
	$user = $_SESSION['user'];
	$dailyShiftReportId = (int) ($_POST['daily_shift_report_id'] ?? 0);
	if ($dailyShiftReportId <= 0) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => 'ID shift tidak valid.']);
		exit;
	}

	$pdo = getPdo();
	if (!userCanWriteNow($user)) {
		http_response_code(403);
		echo json_encode(['success' => false, 'message' => 'Lock laporan ditolak karena di luar shift aktif.']);
		exit;
	}

	$checkStmt = $pdo->prepare('SELECT id, final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
	$checkStmt->execute([':id' => $dailyShiftReportId]);
	$row = $checkStmt->fetch();
	if (!$row) {
		http_response_code(404);
		echo json_encode(['success' => false, 'message' => 'Laporan shift tidak ditemukan.']);
		exit;
	}
	if ((int) ($row['final_copy_watermark'] ?? 0) === 1) {
		http_response_code(409);
		echo json_encode(['success' => false, 'message' => 'Laporan sudah final oleh Manager. Lock ulang tidak diperlukan.']);
		exit;
	}

	$stmt = $pdo->prepare('UPDATE daily_shift_reports SET is_locked = 1, edit_requested = "none" WHERE id = :id');
	$stmt->execute([':id' => $dailyShiftReportId]);

	// Regenerate session ID after privilege change
	session_regenerate_id(true);

	logAudit(
		$pdo,
		$user['nip'],
		'LOCK',
		'daily_shift_reports',
		$dailyShiftReportId,
		['is_locked' => 1],
		'Shift report locked by operator'
	);

	echo json_encode(['success' => true, 'message' => 'Laporan shift berhasil dikunci. Input menjadi read-only.']);
	exit;
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Gagal mengunci laporan shift.']);
	exit;
}
