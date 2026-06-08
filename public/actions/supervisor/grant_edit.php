<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Supervisor');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	redirect('/public/dashboard.php');
}

verifyCsrfOrDie('/views/supervisor/dashboard.php');

$user = $_SESSION['user'];

$requestId = (int) ($_POST['request_id'] ?? 0);
$minutes = (int) ($_POST['minutes'] ?? 15);
if ($minutes < 1 || $minutes > 120) {
	$minutes = 15;
}

if ($requestId <= 0) {
	$_SESSION['flash_error'] = 'Request ID tidak valid.';
	redirect('/public/dashboard.php');
}

try {
	$pdo = getPdo();
	$pdo->beginTransaction();

	$findStmt = $pdo->prepare(
		'SELECT id, table_name, record_id, requested_by_nip, status
		 FROM edit_requests
		 WHERE id = :id
		 LIMIT 1
		 FOR UPDATE'
	);
	$findStmt->execute([':id' => $requestId]);
	$request = $findStmt->fetch();

	if (!$request || $request['status'] !== 'pending') {
		$pdo->rollBack();
		$_SESSION['flash_error'] = 'Permintaan edit tidak ditemukan atau sudah diproses.';
		redirect('/public/dashboard.php');
	}

	$reportStmt = $pdo->prepare('SELECT id, final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
	$reportStmt->execute([':id' => $request['record_id']]);
	$report = $reportStmt->fetch();
	if (!$report || (int) ($report['final_copy_watermark'] ?? 0) === 1) {
		$rejectStmt = $pdo->prepare(
			'UPDATE edit_requests
			 SET status = "rejected", resolved_at = NOW(), updated_at = NOW()
			 WHERE id = :id'
		);
		$rejectStmt->execute([':id' => $requestId]);

		$pdo->commit();
		$_SESSION['flash_error'] = 'Laporan sudah final oleh Manager. Akses edit tidak dapat diberikan.';
		redirect('/public/dashboard.php');
	}

	$approveStmt = $pdo->prepare(
		'UPDATE edit_requests
		 SET status = "approved", unlocked_until = DATE_ADD(NOW(), INTERVAL :minutes MINUTE), resolved_at = NOW()
		 WHERE id = :id'
	);
	$approveStmt->bindValue(':minutes', $minutes, PDO::PARAM_INT);
	$approveStmt->bindValue(':id', $requestId, PDO::PARAM_INT);
	$approveStmt->execute();

	$updateShift = $pdo->prepare(
		'UPDATE daily_shift_reports
			SET edit_requested = "none"
		 WHERE id = :id'
	);
	$updateShift->execute([':id' => $request['record_id']]);

	logAudit(
		$pdo,
		$user['nip'],
		'UNLOCK',
		(string) $request['table_name'],
		(int) $request['record_id'],
		['minutes' => $minutes, 'request_id' => $requestId],
		'Supervisor granted temporary edit access'
	);

	$pdo->commit();
	$_SESSION['flash_success'] = 'Akses edit diberikan selama ' . $minutes . ' menit.';
	redirect('/public/dashboard.php');
} catch (Throwable $e) {
	if (isset($pdo) && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	$_SESSION['flash_error'] = 'Gagal memberikan akses edit.';
	redirect('/public/dashboard.php');
}
