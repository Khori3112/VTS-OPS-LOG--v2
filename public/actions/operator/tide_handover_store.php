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

$user               = $_SESSION['user'];
$dailyShiftReportId = (int) ($_POST['daily_shift_report_id'] ?? 0);

if ($dailyShiftReportId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Shift report tidak valid.']);
    exit;
}

try {
    $pdo          = getPdo();
    $errorMessage = null;

    if (!canWriteShiftRecord($pdo, $user, $dailyShiftReportId, $errorMessage)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => $errorMessage ?? 'Aksi tidak diizinkan.']);
        exit;
    }

    // ── TIDE REPORT (UPSERT) ─────────────────────────────────────────────────
    $highestTide = $_POST['highest_tide_value_m'] !== '' ? (float) $_POST['highest_tide_value_m'] : null;
    $highestTime = trim($_POST['highest_tide_time'] ?? '');
    $lowestTide  = $_POST['lowest_tide_value_m']  !== '' ? (float) $_POST['lowest_tide_value_m']  : null;
    $lowestTime  = trim($_POST['lowest_tide_time']  ?? '');
    $curLevel    = $_POST['current_water_level_m'] !== '' ? (float) $_POST['current_water_level_m'] : null;
    $warnings    = trim($_POST['tide_warnings']    ?? '');

    if ($highestTide !== null && $highestTime !== '' && $lowestTide !== null && $lowestTime !== '' && $curLevel !== null) {
        // Validate HH:MM
        if (!preg_match('/^\d{2}:\d{2}$/', $highestTime) || !preg_match('/^\d{2}:\d{2}$/', $lowestTime)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Format waktu pasang/surut tidak valid (HH:MM).']);
            exit;
        }

        $tideStmt = $pdo->prepare(
            'INSERT INTO tide_reports
                (daily_shift_report_id, highest_tide_value_m, highest_tide_time, lowest_tide_value_m,
                 lowest_tide_time, current_water_level_m, warnings, is_locked, edit_requested, created_by_nip)
             VALUES
                (:dsrId, :htv, :htt, :ltv, :ltt, :cwl, :warn, 0, "none", :nip)
             ON DUPLICATE KEY UPDATE
                highest_tide_value_m  = VALUES(highest_tide_value_m),
                highest_tide_time     = VALUES(highest_tide_time),
                lowest_tide_value_m   = VALUES(lowest_tide_value_m),
                lowest_tide_time      = VALUES(lowest_tide_time),
                current_water_level_m = VALUES(current_water_level_m),
                warnings              = VALUES(warnings)'
        );
        $tideStmt->execute([
            ':dsrId' => $dailyShiftReportId,
            ':htv'   => $highestTide,
            ':htt'   => $highestTime . ':00',
            ':ltv'   => $lowestTide,
            ':ltt'   => $lowestTime . ':00',
            ':cwl'   => $curLevel,
            ':warn'  => $warnings !== '' ? $warnings : null,
            ':nip'   => $user['nip'],
        ]);

        logAudit($pdo, $user['nip'], 'CREATE', 'tide_reports', (int) $pdo->lastInsertId(), null, 'A4 tide report upserted');
    }

    // ── HANDOVER REPORT (UPSERT) ─────────────────────────────────────────────
    $shipsIn      = (int) ($_POST['ships_in_count']      ?? 0);
    $shipsOut     = (int) ($_POST['ships_out_count']     ?? 0);
    $shipsTransit = (int) ($_POST['ships_transit_count'] ?? 0);
    $shipsAnchor  = (int) ($_POST['ships_anchor_count']  ?? 0);
    $eqStatus     = trim($_POST['equipment_status']      ?? '');
    $ntmNotes     = trim($_POST['ntm_notes']             ?? '');
    $summaryNotes = trim($_POST['summary_notes']         ?? '');

    $handoverStmt = $pdo->prepare(
        'INSERT INTO handover_reports
            (daily_shift_report_id, ships_in_count, ships_out_count, ships_transit_count, ships_anchor_count,
             equipment_status, ntm_notes, summary_notes, is_locked, edit_requested, created_by_nip)
         VALUES
            (:dsrId, :si, :so, :st, :sa, :eq, :ntm, :sum, 0, "none", :nip)
         ON DUPLICATE KEY UPDATE
            ships_in_count      = VALUES(ships_in_count),
            ships_out_count     = VALUES(ships_out_count),
            ships_transit_count = VALUES(ships_transit_count),
            ships_anchor_count  = VALUES(ships_anchor_count),
            equipment_status    = VALUES(equipment_status),
            ntm_notes           = VALUES(ntm_notes),
            summary_notes       = VALUES(summary_notes)'
    );
    $handoverStmt->execute([
        ':dsrId' => $dailyShiftReportId,
        ':si'    => $shipsIn,
        ':so'    => $shipsOut,
        ':st'    => $shipsTransit,
        ':sa'    => $shipsAnchor,
        ':eq'    => $eqStatus    !== '' ? $eqStatus    : null,
        ':ntm'   => $ntmNotes    !== '' ? $ntmNotes    : null,
        ':sum'   => $summaryNotes !== '' ? $summaryNotes : null,
        ':nip'   => $user['nip'],
    ]);

    logAudit($pdo, $user['nip'], 'CREATE', 'handover_reports', (int) $pdo->lastInsertId(), null, 'A4 handover report upserted');

    echo json_encode(['success' => true, 'message' => 'A4 Pasang Surut & Serah Terima berhasil disimpan.']);
    exit;

} catch (Throwable $e) {
    logError('tide_handover_store: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data A4.']);
    exit;
}
