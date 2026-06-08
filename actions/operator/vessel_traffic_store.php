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
		echo json_encode(['success' => false, 'message' => 'Data wajib belum lengkap.']);
		exit;
	}

	$pdo = getPdo();
	$errorMessage = null;
	if (!canWriteShiftRecord($pdo, $user, $dailyShiftReportId, $errorMessage)) {
		http_response_code(403);
		echo json_encode(['success' => false, 'message' => $errorMessage ?? 'Aksi tidak diizinkan.']);
		exit;
	}

	$vesselNames = $_POST['vessel_name'] ?? [];
	$callSigns = $_POST['call_sign'] ?? [];
	$lastPorts = $_POST['last_port'] ?? [];
	$nextPorts = $_POST['next_port'] ?? [];
	$vesselTypes = $_POST['vessel_type'] ?? [];
	$agents = $_POST['agent'] ?? [];
	$remarksArr = $_POST['remarks'] ?? [];

	if (!is_array($vesselNames) || count($vesselNames) === 0) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => 'Baris data kapal belum tersedia.']);
		exit;
	}

	$insert = $pdo->prepare(
		'INSERT INTO vessel_traffic (
			daily_shift_report_id, vessel_name, call_sign, last_port, next_port, vessel_type, agent, remarks,
			created_by_nip, is_locked, edit_requested
		) VALUES (
			:daily_shift_report_id, :vessel_name, :call_sign, :last_port, :next_port, :vessel_type, :agent, :remarks,
			:created_by_nip, 0, "none"
		)'
	);

	$inserted = 0;
	foreach ($vesselNames as $i => $nameRaw) {
		$vesselName = trim((string) $nameRaw);
		if ($vesselName === '') {
			continue;
		}

		$callSign = trim((string) ($callSigns[$i] ?? ''));
		$lastPort = trim((string) ($lastPorts[$i] ?? ''));
		$nextPort = trim((string) ($nextPorts[$i] ?? ''));
		$vesselType = trim((string) ($vesselTypes[$i] ?? ''));
		$agent = trim((string) ($agents[$i] ?? ''));
		$remarks = trim((string) ($remarksArr[$i] ?? ''));

		$insert->execute([
			':daily_shift_report_id' => $dailyShiftReportId,
			':vessel_name' => $vesselName,
			':call_sign' => $callSign !== '' ? $callSign : null,
			':last_port' => $lastPort !== '' ? $lastPort : null,
			':next_port' => $nextPort !== '' ? $nextPort : null,
			':vessel_type' => $vesselType !== '' ? $vesselType : null,
			':agent' => $agent !== '' ? $agent : null,
			':remarks' => $remarks !== '' ? $remarks : null,
			':created_by_nip' => $user['nip'],
		]);
		$inserted++;
	}

	if ($inserted === 0) {
		http_response_code(400);
		echo json_encode(['success' => false, 'message' => 'Isi minimal satu nama kapal untuk disimpan.']);
		exit;
	}

	logAudit(
		$pdo,
		$user['nip'],
		'CREATE',
		'vessel_traffic',
		$dailyShiftReportId,
		[
			'daily_shift_report_id' => $dailyShiftReportId,
			'inserted_rows' => $inserted,
		],
		'Manual vessel traffic entries created'
	);

	// Regenerate session ID after privilege change
	session_regenerate_id(true);

	echo json_encode(['success' => true, 'message' => 'Data kapal berhasil disimpan: ' . $inserted . ' baris.']);
	exit;
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data kapal.']);
	exit;
}
