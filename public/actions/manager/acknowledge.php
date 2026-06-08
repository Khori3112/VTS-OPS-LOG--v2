<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Manager');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	redirect('/public/dashboard.php');
}

verifyCsrfOrDie('/views/manager/manager_approval.php');

$user = $_SESSION['user'];

$dailyShiftReportId = (int) ($_POST['daily_shift_report_id'] ?? 0);
if ($dailyShiftReportId <= 0) {
	$_SESSION['flash_error'] = 'ID laporan tidak valid.';
	redirect('/public/dashboard.php');
}

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
	$checkStmt->execute([':id' => $dailyShiftReportId]);
	$report = $checkStmt->fetch();

	if (!$report) {
		$pdo->rollBack();
		$_SESSION['flash_error'] = 'Laporan tidak ditemukan.';
		redirect('/public/dashboard.php');
	}

	if ((int) $report['is_locked'] !== 1) {
		$pdo->rollBack();
		$_SESSION['flash_error'] = 'Laporan harus berstatus locked sebelum di-acknowledge.';
		redirect('/public/dashboard.php');
	}

	if ((int) ($report['final_copy_watermark'] ?? 0) === 1) {
		$pdo->rollBack();
		$_SESSION['flash_error'] = 'Laporan sudah final sebelumnya.';
		redirect('/public/dashboard.php');
	}

	$ackStmt = $pdo->prepare(
		'UPDATE daily_shift_reports
		 SET final_copy_watermark = 1,
			 is_locked = 1,
			 edit_requested = "none",
			 manager_acknowledged_at = NOW(),
			 manager_nip = :manager_nip
		 WHERE id = :id'
	);
	$ackStmt->execute([
		':manager_nip' => $user['nip'],
		':id' => $dailyShiftReportId,
	]);

	$rejectPendingStmt = $pdo->prepare(
		'UPDATE edit_requests
		 SET status = "rejected", resolved_at = NOW(), updated_at = NOW()
		 WHERE table_name = "daily_shift_reports"
		   AND record_id = :record_id
		   AND status = "pending"'
	);
	$rejectPendingStmt->execute([':record_id' => $dailyShiftReportId]);

	logAudit(
		$pdo,
		$user['nip'],
		'LOCK',
		'daily_shift_reports',
		$dailyShiftReportId,
		['final_copy_watermark' => 1],
		'Manager acknowledged report and applied permanent lock'
	);

	$pdo->commit();
	$_SESSION['flash_success'] = 'Acknowledge berhasil. Laporan menjadi Final Copy dan terkunci permanen.';
	redirect('/public/dashboard.php');
} catch (Throwable $e) {
	if (isset($pdo) && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	$_SESSION['flash_error'] = 'Gagal melakukan acknowledge laporan.';
	redirect('/public/dashboard.php');
}
