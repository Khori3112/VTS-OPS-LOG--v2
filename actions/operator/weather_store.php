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

$weatherOptions = ['Cerah', 'Berawan', 'Hujan Ringan', 'Hujan Lebat', 'Badai'];
$waveCategories = ['Tenang', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'];


	$pdo = getPdo();
	$areas = [];
	try {
		$areasStmt = $pdo->query('SELECT area_name FROM maritime_areas WHERE is_active = 1 ORDER BY display_order ASC, area_name ASC');
		$areas = $areasStmt ? $areasStmt->fetchAll(PDO::FETCH_COLUMN) : [];
	} catch (Throwable $e) {
		$areas = [];
	}
	if (!$areas) {
		$areas = [
			'Banyuasin',
			'Selat Gelasa',
			'Bangka',
			'Muara Sungai Musi',
			'Selat Bangka Utara',
			'Selat Bangka Selatan',
			'Perairan Sungsang',
			'Perairan Tanjung Buyut',
			'Perairan Upang',
			'Ambang Luar',
			'Tanjung Api-Api',
		];
	}

	$errorMessage = null;
	if (!canWriteShiftRecord($pdo, $user, $dailyShiftReportId, $errorMessage)) {
		http_response_code(403);
		echo json_encode(['success' => false, 'message' => $errorMessage ?? 'Aksi tidak diizinkan.']);
		exit;
	}

	$pdo->beginTransaction();

	$weatherReportStmt = $pdo->prepare('SELECT id FROM weather_reports WHERE daily_shift_report_id = :id LIMIT 1');
	$weatherReportStmt->execute([':id' => $dailyShiftReportId]);
	$weatherReport = $weatherReportStmt->fetch();

	if ($weatherReport) {
		$weatherReportId = (int) $weatherReport['id'];
	} else {
		$createWeatherReport = $pdo->prepare(
			'INSERT INTO weather_reports (daily_shift_report_id, created_by_nip, is_locked, edit_requested)
			 VALUES (:daily_shift_report_id, :created_by_nip, 0, "none")'
		);
		$createWeatherReport->execute([
			':daily_shift_report_id' => $dailyShiftReportId,
			':created_by_nip' => $user['nip'],
		]);
		$weatherReportId = (int) $pdo->lastInsertId();
	}

	$upsert = $pdo->prepare(
		'INSERT INTO weather_observations (
			weather_report_id, area_name, weather_condition, wind_direction, wind_speed_knots, wave_height_m, wave_category,
			is_locked, edit_requested
		 ) VALUES (
			:weather_report_id, :area_name, :weather_condition, :wind_direction, :wind_speed_knots, :wave_height_m, :wave_category,
			0, "none"
		 )
		 ON DUPLICATE KEY UPDATE
			weather_condition = VALUES(weather_condition),
			wind_direction = VALUES(wind_direction),
			wind_speed_knots = VALUES(wind_speed_knots),
			wave_height_m = VALUES(wave_height_m),
			wave_category = VALUES(wave_category),
			updated_at = CURRENT_TIMESTAMP'
	);

	foreach ($areas as $index => $areaName) {
		$weatherCondition = trim($_POST['weather_condition'][$index] ?? '');
		$windDirection = trim($_POST['wind_direction'][$index] ?? '');
		$windSpeed = (float) ($_POST['wind_speed_knots'][$index] ?? 0);
		$waveHeight = (float) ($_POST['wave_height_m'][$index] ?? 0);
		$waveCategory = trim($_POST['wave_category'][$index] ?? '');

		if (!in_array($weatherCondition, $weatherOptions, true)) {
			$weatherCondition = 'Cerah';
		}
		if ($windDirection === '') {
			$windDirection = '-';
		}
		if (!in_array($waveCategory, $waveCategories, true)) {
			$waveCategory = 'Tenang';
		}

		$upsert->execute([
			':weather_report_id' => $weatherReportId,
			':area_name' => $areaName,
			':weather_condition' => $weatherCondition,
			':wind_direction' => $windDirection,
			':wind_speed_knots' => $windSpeed,
			':wave_height_m' => $waveHeight,
			':wave_category' => $waveCategory,
		]);
	}

	logAudit(
		$pdo,
		$user['nip'],
		'UPDATE',
		'weather_reports',
		$weatherReportId,
		['daily_shift_report_id' => $dailyShiftReportId],
		'A3 weather grid updated for all maritime areas'
	);

	$pdo->commit();
	// Regenerate session ID after privilege change
	session_regenerate_id(true);
	echo json_encode(['success' => true, 'message' => 'Data cuaca A3 berhasil diperbarui.']);
	exit;
} catch (Throwable $e) {
	if (isset($pdo) && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data cuaca A3.']);
	exit;
}
