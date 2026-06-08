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

verifyCsrfOrDie('/public/dashboard.php');

$user = $_SESSION['user'];
$reportType = strtolower(trim($_POST['report_type'] ?? ''));
$dailyShiftReportId = (int) ($_POST['daily_shift_report_id'] ?? 0);

if ($dailyShiftReportId <= 0 || !in_array($reportType, ['a5', 'a6', 'a7', 'a8'], true)) {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'Permintaan laporan khusus tidak valid.']);
	exit;
}

try {
	$pdo = getPdo();
	$errorMessage = null;
	if (!canWriteShiftRecord($pdo, $user, $dailyShiftReportId, $errorMessage)) {
		http_response_code(403);
		echo json_encode(['success' => false, 'message' => $errorMessage ?? 'Aksi tidak diizinkan.']);
		exit;
	}

	if ($reportType === 'a5') {
		$vesselName = trim($_POST['vessel_name'] ?? '');
		$imoNumber = trim($_POST['imo_number'] ?? '');
		$grossTonnage = trim($_POST['gross_tonnage'] ?? '');
		$loaM = trim($_POST['loa_m'] ?? '');
		$draftM = trim($_POST['draft_m'] ?? '');
		$pobCount = trim($_POST['pob_count'] ?? '');
		$expectedArrivalRaw = trim($_POST['expected_arrival'] ?? '');
		$cargoDetails = trim($_POST['cargo_details'] ?? '');
		$specialNotes = trim($_POST['special_notes'] ?? '');

		if ($vesselName === '' || $expectedArrivalRaw === '') {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'A5 membutuhkan nama kapal dan ETA.']);
			exit;
		}

		$expectedArrival = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $expectedArrivalRaw, new DateTimeZone('Asia/Jakarta'));
		if (!$expectedArrival) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Format ETA A5 tidak valid.']);
			exit;
		}

		$stmt = $pdo->prepare(
			'INSERT INTO pre_arrival_reports (
				daily_shift_report_id, vessel_name, imo_number, gross_tonnage, loa_m, draft_m, pob_count,
				expected_arrival, cargo_details, special_notes, is_locked, edit_requested, created_by_nip
			 ) VALUES (
				:daily_shift_report_id, :vessel_name, :imo_number, :gross_tonnage, :loa_m, :draft_m, :pob_count,
				:expected_arrival, :cargo_details, :special_notes, 0, "none", :created_by_nip
			 )'
		);
		$stmt->execute([
			':daily_shift_report_id' => $dailyShiftReportId,
			':vessel_name' => $vesselName,
			':imo_number' => $imoNumber !== '' ? $imoNumber : null,
			':gross_tonnage' => $grossTonnage !== '' ? (float) $grossTonnage : null,
			':loa_m' => $loaM !== '' ? (float) $loaM : null,
			':draft_m' => $draftM !== '' ? (float) $draftM : null,
			':pob_count' => $pobCount !== '' ? (int) $pobCount : null,
			':expected_arrival' => $expectedArrival->format('Y-m-d H:i:s'),
			':cargo_details' => $cargoDetails !== '' ? $cargoDetails : null,
			':special_notes' => $specialNotes !== '' ? $specialNotes : null,
			':created_by_nip' => $user['nip'],
		]);

		logAudit($pdo, $user['nip'], 'CREATE', 'pre_arrival_reports', (int) $pdo->lastInsertId(), null, 'A5 pre-arrival report created');
		echo json_encode(['success' => true, 'message' => 'A5 Pra-Kedatangan berhasil disimpan.']);
		exit;
	}

	if ($reportType === 'a6') {
		$incidentDatetimeRaw = trim($_POST['incident_datetime'] ?? '');
		$title = trim($_POST['title'] ?? '');
		$chronology = trim($_POST['chronology'] ?? '');
		$deaths = (int) ($_POST['deaths_count'] ?? 0);
		$missing = (int) ($_POST['missing_count'] ?? 0);
		$pollutionLocation = trim($_POST['pollution_location'] ?? '');
		$authorities = $_POST['authorities_notified'] ?? [];
		$immediateAction = trim($_POST['immediate_action'] ?? '');

		if ($incidentDatetimeRaw === '' || $title === '' || $chronology === '') {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'A6 membutuhkan waktu kejadian, judul, dan kronologi.']);
			exit;
		}

		$incidentDatetime = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $incidentDatetimeRaw, new DateTimeZone('Asia/Jakarta'));
		if (!$incidentDatetime) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Format waktu kejadian A6 tidak valid.']);
			exit;
		}

		if (!is_array($authorities)) {
			$authorities = [];
		}

		$stmt = $pdo->prepare(
			'INSERT INTO incident_reports (
				daily_shift_report_id, incident_datetime, title, chronology, deaths_count, missing_count,
				pollution_location, authorities_notified, immediate_action, is_locked, edit_requested, created_by_nip
			 ) VALUES (
				:daily_shift_report_id, :incident_datetime, :title, :chronology, :deaths_count, :missing_count,
				:pollution_location, :authorities_notified, :immediate_action, 0, "none", :created_by_nip
			 )'
		);
		$stmt->execute([
			':daily_shift_report_id' => $dailyShiftReportId,
			':incident_datetime' => $incidentDatetime->format('Y-m-d H:i:s'),
			':title' => $title,
			':chronology' => $chronology,
			':deaths_count' => $deaths,
			':missing_count' => $missing,
			':pollution_location' => $pollutionLocation !== '' ? $pollutionLocation : null,
			':authorities_notified' => json_encode(array_values($authorities), JSON_UNESCAPED_UNICODE),
			':immediate_action' => $immediateAction !== '' ? $immediateAction : null,
			':created_by_nip' => $user['nip'],
		]);

		logAudit($pdo, $user['nip'], 'CREATE', 'incident_reports', (int) $pdo->lastInsertId(), null, 'A6 incident report created');
		echo json_encode(['success' => true, 'message' => 'A6 Insiden berhasil disimpan.']);
		exit;
	}

	if ($reportType === 'a7') {
		$operationName    = trim($_POST['operation_name']    ?? '');
		$operationStartRaw = trim($_POST['operation_start']  ?? '');
		$operationEndRaw   = trim($_POST['operation_end']    ?? '');
		$location          = trim($_POST['location']         ?? '');
		$sector            = trim($_POST['sector']           ?? '');
		$operationDetails  = trim($_POST['operation_details'] ?? '');
		$outcomeNotes      = trim($_POST['outcome_notes']    ?? '');
		$eventDescription  = trim($_POST['event_description'] ?? '');

		if ($operationName === '' || $operationStartRaw === '') {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'A7 membutuhkan nama operasi dan waktu mulai.']);
			exit;
		}

		$operationStart = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $operationStartRaw, new DateTimeZone('Asia/Jakarta'));
		$operationEnd = null;
		if ($operationEndRaw !== '') {
			$operationEnd = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $operationEndRaw, new DateTimeZone('Asia/Jakarta'));
		}
		if (!$operationStart || ($operationEndRaw !== '' && !$operationEnd)) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Format tanggal operasi A7 tidak valid.']);
			exit;
		}

		$stmt = $pdo->prepare(
			'INSERT INTO special_ops_reports (
				daily_shift_report_id, operation_name, operation_start, operation_end, location,
				sector, operation_details, outcome_notes, event_description,
				is_locked, edit_requested, created_by_nip
			 ) VALUES (
				:daily_shift_report_id, :operation_name, :operation_start, :operation_end, :location,
				:sector, :operation_details, :outcome_notes, :event_description,
				0, "none", :created_by_nip
			 )'
		);
		$stmt->execute([
			':daily_shift_report_id' => $dailyShiftReportId,
			':operation_name'        => $operationName,
			':operation_start'       => $operationStart->format('Y-m-d H:i:s'),
			':operation_end'         => $operationEnd ? $operationEnd->format('Y-m-d H:i:s') : null,
			':location'              => $location !== '' ? $location : null,
			':sector'                => $sector   !== '' ? $sector   : null,
			':operation_details'     => $operationDetails  !== '' ? $operationDetails  : null,
			':outcome_notes'         => $outcomeNotes      !== '' ? $outcomeNotes      : null,
			':event_description'     => $eventDescription  !== '' ? $eventDescription  : null,
			':created_by_nip'        => $user['nip'],
		]);

		logAudit($pdo, $user['nip'], 'CREATE', 'special_ops_reports', (int) $pdo->lastInsertId(), null, 'A7 special ops report created');
		echo json_encode(['success' => true, 'message' => 'A7 Operasi Khusus berhasil disimpan.']);
		exit;
	}

	if ($reportType === 'a8') {
		$vesselName = trim($_POST['vessel_name'] ?? '');
		$violationType = trim($_POST['violation_type'] ?? '');
		$violationDatetimeRaw = trim($_POST['violation_datetime'] ?? '');
		$location = trim($_POST['location'] ?? '');
		$legalReference = trim($_POST['legal_reference'] ?? '');
		$warningType = trim($_POST['warning_type'] ?? '');
		$actionTaken = trim($_POST['action_taken'] ?? '');

		if ($vesselName === '' || $violationType === '' || $violationDatetimeRaw === '') {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'A8 membutuhkan nama kapal, jenis pelanggaran, dan waktu kejadian.']);
			exit;
		}

		$violationDatetime = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $violationDatetimeRaw, new DateTimeZone('Asia/Jakarta'));
		if (!$violationDatetime) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Format waktu pelanggaran A8 tidak valid.']);
			exit;
		}

		$stmt = $pdo->prepare(
			'INSERT INTO contravention_reports (
				daily_shift_report_id, vessel_name, violation_type, violation_datetime, location,
				warning_type, legal_reference, action_taken, is_locked, edit_requested, created_by_nip
			 ) VALUES (
				:daily_shift_report_id, :vessel_name, :violation_type, :violation_datetime, :location,
				:warning_type, :legal_reference, :action_taken, 0, "none", :created_by_nip
			 )'
		);
		$stmt->execute([
			':daily_shift_report_id' => $dailyShiftReportId,
			':vessel_name' => $vesselName,
			':violation_type' => $violationType,
			':violation_datetime' => $violationDatetime->format('Y-m-d H:i:s'),
			':location' => $location !== '' ? $location : null,
			':warning_type' => $warningType !== '' ? $warningType : null,
			':legal_reference' => $legalReference !== '' ? $legalReference : null,
			':action_taken' => $actionTaken !== '' ? $actionTaken : null,
			':created_by_nip' => $user['nip'],
		]);

		logAudit($pdo, $user['nip'], 'CREATE', 'contravention_reports', (int) $pdo->lastInsertId(), null, 'A8 contravention report created');
		echo json_encode(['success' => true, 'message' => 'A8 Pelanggaran berhasil disimpan.']);
		exit;
	}

	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'Jenis laporan tidak dikenali.']);
	exit;
} catch (Throwable $e) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Gagal menyimpan laporan khusus.']);
	exit;
}
