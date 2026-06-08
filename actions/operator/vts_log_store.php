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
	$errorMessage = null;
	if (!canWriteShiftRecord($pdo, $user, $dailyShiftReportId, $errorMessage)) {
		http_response_code(403);
		echo json_encode(['success' => false, 'message' => $errorMessage ?? 'Aksi tidak diizinkan.']);
		exit;
	}

	$logTime = trim($_POST['log_time'] ?? '');
	$vesselName = trim($_POST['vessel_name'] ?? '');
	$callSign = trim($_POST['call_sign'] ?? '');
	$activity = trim($_POST['activity'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$notes = trim($_POST['notes'] ?? '');

	if ($activity === '') {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => 'Aktivitas log wajib diisi.']);
		exit;
	}

	$normalizedLogTime = $logTime !== ''
		? DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $logTime, new DateTimeZone('Asia/Jakarta'))
		: new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));

	if (!$normalizedLogTime) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => 'Format waktu log tidak valid.']);
		exit;
	}

	$insert = $pdo->prepare(
		'INSERT INTO vts_logs (
			daily_shift_report_id, log_time, vessel_name, call_sign, activity, location, notes,
			created_by_nip, is_locked, edit_requested
		 ) VALUES (
			:daily_shift_report_id, :log_time, :vessel_name, :call_sign, :activity, :location, :notes,
			:created_by_nip, 0, "none"
		 )'
	);
	$insert->execute([
		':daily_shift_report_id' => $dailyShiftReportId,
		':log_time' => $normalizedLogTime->format('Y-m-d H:i:s'),
		':vessel_name' => $vesselName !== '' ? $vesselName : null,
		':call_sign' => $callSign !== '' ? $callSign : null,
		':activity' => $activity,
		':location' => $location !== '' ? $location : null,
		':notes' => $notes !== '' ? $notes : null,
		':created_by_nip' => $user['nip'],
	]);

	logAudit(
		$pdo,
		$user['nip'],
		'CREATE',
		'vts_logs',
		(int) $pdo->lastInsertId(),
		[
			'daily_shift_report_id' => $dailyShiftReportId,
			'activity' => $activity,
			'vessel_name' => $vesselName,
		],
		'Manual A1 VTS log entry created'
	);

	// Regenerate session ID after privilege change
	session_regenerate_id(true);

	echo json_encode(['success' => true, 'message' => 'Log A1 berhasil disimpan.']);
	exit;
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Gagal menyimpan log A1.']);
	exit;
}
