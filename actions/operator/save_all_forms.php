<?php
/**
 * save_all_forms.php — Unified form handler for all A1–A8 operator forms.
 * Accepts: form_type = a1|a2|a3|a4|a5|a6|a7|a8
 */

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Operator');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
    exit;
}

verifyCsrfOrDie('/public/dashboard.php');

$user               = $_SESSION['user'];
$formType           = strtolower(trim($_POST['form_type'] ?? ''));
$dailyShiftReportId = (int) ($_POST['daily_shift_report_id'] ?? 0);

if ($dailyShiftReportId <= 0 || !in_array($formType, ['a1','a2','a3','a4','a5','a6','a7','a8'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid (form_type atau report_id salah).']);
    exit;
}

try {
    $pdo          = getPdo();
    $errMsg       = null;
    $tz           = new DateTimeZone('Asia/Jakarta');

    if (!canWriteShiftRecord($pdo, $user, $dailyShiftReportId, $errMsg)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => $errMsg ?? 'Aksi tidak diizinkan.']);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-1  VTS Log — multi-row insert
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a1') {
        $activities  = $_POST['activity']    ?? [];
        $logTimes    = $_POST['log_time']    ?? [];
        $vesselNames = $_POST['vessel_name'] ?? [];
        $callSigns   = $_POST['call_sign']   ?? [];
        $locations   = $_POST['location']    ?? [];
        $notesArr    = $_POST['notes']       ?? [];

        if (!is_array($activities) || empty($activities)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Minimal satu baris aktivitas wajib diisi.']);
            exit;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO vts_logs
                (daily_shift_report_id, log_time, vessel_name, call_sign, activity, location, notes,
                 created_by_nip, is_locked, edit_requested)
             VALUES
                (:dsrId, :lt, :vn, :cs, :act, :loc, :nt, :nip, 0, "none")'
        );

        $inserted = 0;
        foreach ($activities as $i => $act) {
            $act = trim((string) $act);
            if ($act === '') continue;

            $ltRaw = trim((string) ($logTimes[$i] ?? ''));
            $lt    = $ltRaw !== ''
                ? DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $ltRaw, $tz)
                : new DateTimeImmutable('now', $tz);
            if (!$lt) $lt = new DateTimeImmutable('now', $tz);

            $stmt->execute([
                ':dsrId' => $dailyShiftReportId,
                ':lt'    => $lt->format('Y-m-d H:i:s'),
                ':vn'    => trim((string)($vesselNames[$i] ?? '')) ?: null,
                ':cs'    => trim((string)($callSigns[$i]  ?? '')) ?: null,
                ':act'   => $act,
                ':loc'   => trim((string)($locations[$i]  ?? '')) ?: null,
                ':nt'    => trim((string)($notesArr[$i]   ?? '')) ?: null,
                ':nip'   => $user['nip'],
            ]);
            ++$inserted;
        }

        if ($inserted === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Semua baris kosong — tidak ada data yang disimpan.']);
            exit;
        }

        logAudit($pdo, $user['nip'], 'CREATE', 'vts_logs', 0, null, "A1: {$inserted} log disimpan");
        echo json_encode(['success' => true, 'message' => "A-1 berhasil disimpan ({$inserted} baris log)."]);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-2  Vessel Traffic — multi-row insert
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a2') {
        $vesselNames = $_POST['vessel_name'] ?? [];
        if (!is_array($vesselNames) || empty($vesselNames)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Minimal satu nama kapal wajib diisi.']);
            exit;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO vessel_traffic
                (daily_shift_report_id, vessel_name, call_sign, last_port, next_port, vessel_type,
                 agent, direction, remarks, created_by_nip, is_locked, edit_requested)
             VALUES
                (:dsrId, :vn, :cs, :lp, :np, :vt, :ag, :dir, :rem, :nip, 0, "none")'
        );

        $callSigns  = $_POST['call_sign']   ?? [];
        $lastPorts  = $_POST['last_port']   ?? [];
        $nextPorts  = $_POST['next_port']   ?? [];
        $vesTypes   = $_POST['vessel_type'] ?? [];
        $agents     = $_POST['agent']       ?? [];
        $directions = $_POST['direction']   ?? [];
        $remarks    = $_POST['remarks']     ?? [];

        $inserted = 0;
        foreach ($vesselNames as $i => $vn) {
            $vn = trim((string) $vn);
            if ($vn === '') continue;

            $dir = trim((string)($directions[$i] ?? 'inbound'));
            if (!in_array($dir, ['inbound','outbound','transit'], true)) $dir = 'inbound';

            $stmt->execute([
                ':dsrId' => $dailyShiftReportId,
                ':vn'    => $vn,
                ':cs'    => trim((string)($callSigns[$i]  ?? '')) ?: null,
                ':lp'    => trim((string)($lastPorts[$i]  ?? '')) ?: null,
                ':np'    => trim((string)($nextPorts[$i]  ?? '')) ?: null,
                ':vt'    => trim((string)($vesTypes[$i]   ?? '')) ?: null,
                ':ag'    => trim((string)($agents[$i]     ?? '')) ?: null,
                ':dir'   => $dir,
                ':rem'   => trim((string)($remarks[$i]    ?? '')) ?: null,
                ':nip'   => $user['nip'],
            ]);
            ++$inserted;
        }

        if ($inserted === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Semua baris kosong — tidak ada kapal yang disimpan.']);
            exit;
        }

        logAudit($pdo, $user['nip'], 'CREATE', 'vessel_traffic', 0, null, "A2: {$inserted} kapal disimpan");
        echo json_encode(['success' => true, 'message' => "A-2 berhasil disimpan: {$inserted} kapal."]);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-3  Weather — upsert all 11 areas
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a3') {
        $areaNames        = $_POST['area_name']         ?? [];
        $weatherConds     = $_POST['weather_condition'] ?? [];
        $windDirs         = $_POST['wind_direction']    ?? [];
        $windSpeeds       = $_POST['wind_speed_knots']  ?? [];
        $waveHeights      = $_POST['wave_height_m']     ?? [];
        $waveCats         = $_POST['wave_category']     ?? [];

        if (!is_array($areaNames) || empty($areaNames)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data cuaca tidak valid.']);
            exit;
        }

        // Upsert weather_report header first
        $rpStmt = $pdo->prepare(
            'INSERT INTO weather_reports (daily_shift_report_id, is_locked, edit_requested, created_by_nip)
             VALUES (:id, 0, "none", :nip)
             ON DUPLICATE KEY UPDATE updated_at = NOW()'
        );
        $rpStmt->execute([':id' => $dailyShiftReportId, ':nip' => $user['nip']]);
        $reportId = (int) $pdo->lastInsertId();
        if ($reportId === 0) {
            $rpFetch = $pdo->prepare('SELECT id FROM weather_reports WHERE daily_shift_report_id = :id LIMIT 1');
            $rpFetch->execute([':id' => $dailyShiftReportId]);
            $reportId = (int) ($rpFetch->fetchColumn() ?: 0);
        }

        // Must match weather_observations.weather_condition ENUM in schema.sql
        $validConds = ['Cerah', 'Berawan', 'Hujan Ringan', 'Hujan Lebat', 'Badai'];
        // Must match weather_observations.wave_category ENUM in schema.sql
        $validWave  = ['Tenang', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'];

        $upsert = $pdo->prepare(
            'INSERT INTO weather_observations
                (weather_report_id, area_name, weather_condition, wind_direction, wind_speed_knots,
                 wave_height_m, wave_category, is_locked, edit_requested)
             VALUES (:rid, :area, :wc, :wd, :ws, :wh, :wcat, 0, "none")
             ON DUPLICATE KEY UPDATE
                weather_condition  = VALUES(weather_condition),
                wind_direction     = VALUES(wind_direction),
                wind_speed_knots   = VALUES(wind_speed_knots),
                wave_height_m      = VALUES(wave_height_m),
                wave_category      = VALUES(wave_category)'
        );

        $upserted = 0;
        foreach ($areaNames as $idx => $area) {
            $area = trim((string) $area);
            if ($area === '') continue;

            $wc   = (string)($weatherConds[$idx] ?? 'Cerah');
            if (!in_array($wc, $validConds, true)) $wc = 'Cerah';
            $wcat = (string)($waveCats[$idx] ?? 'Tenang');
            if (!in_array($wcat, $validWave, true)) $wcat = 'Tenang';

            $upsert->execute([
                ':rid'  => $reportId,
                ':area' => $area,
                ':wc'   => $wc,
                ':wd'   => trim((string)($windDirs[$idx]    ?? 'N'))   ?: 'N',
                ':ws'   => max(0.0, (float)($windSpeeds[$idx]  ?? 0)),
                ':wh'   => max(0.0, (float)($waveHeights[$idx] ?? 0)),
                ':wcat' => $wcat,
            ]);
            ++$upserted;
        }

        logAudit($pdo, $user['nip'], 'UPDATE', 'weather_observations', $reportId, null, "A3: {$upserted} area cuaca disimpan");
        echo json_encode(['success' => true, 'message' => "A-3 cuaca berhasil disimpan ({$upserted} wilayah)."]);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-4  Tide + Handover — upsert
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a4') {
        $highestV = $_POST['highest_tide_value_m']   ?? '';
        $highestT = trim($_POST['highest_tide_time'] ?? '');
        $lowestV  = $_POST['lowest_tide_value_m']    ?? '';
        $lowestT  = trim($_POST['lowest_tide_time']  ?? '');
        $curLevel = $_POST['current_water_level_m']  ?? '';
        $warnings = trim($_POST['tide_warnings']     ?? '');

        if ($highestV !== '' && $highestT !== '' && $lowestV !== '' && $lowestT !== '' && $curLevel !== '') {
            if (!preg_match('/^\d{2}:\d{2}$/', $highestT) || !preg_match('/^\d{2}:\d{2}$/', $lowestT)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Format waktu pasang/surut tidak valid (HH:MM).']);
                exit;
            }
            $pdo->prepare(
                'INSERT INTO tide_reports
                    (daily_shift_report_id, highest_tide_value_m, highest_tide_time, lowest_tide_value_m,
                     lowest_tide_time, current_water_level_m, warnings, is_locked, edit_requested, created_by_nip)
                 VALUES (:id, :htv, :htt, :ltv, :ltt, :cwl, :warn, 0, "none", :nip)
                 ON DUPLICATE KEY UPDATE
                    highest_tide_value_m  = VALUES(highest_tide_value_m),
                    highest_tide_time     = VALUES(highest_tide_time),
                    lowest_tide_value_m   = VALUES(lowest_tide_value_m),
                    lowest_tide_time      = VALUES(lowest_tide_time),
                    current_water_level_m = VALUES(current_water_level_m),
                    warnings              = VALUES(warnings)'
            )->execute([
                ':id'   => $dailyShiftReportId,
                ':htv'  => (float) $highestV,
                ':htt'  => $highestT . ':00',
                ':ltv'  => (float) $lowestV,
                ':ltt'  => $lowestT . ':00',
                ':cwl'  => (float) $curLevel,
                ':warn' => $warnings ?: null,
                ':nip'  => $user['nip'],
            ]);
        }

        $pdo->prepare(
            'INSERT INTO handover_reports
                (daily_shift_report_id, ships_in_count, ships_out_count, ships_transit_count, ships_anchor_count,
                 equipment_status, ntm_notes, summary_notes, is_locked, edit_requested, created_by_nip)
             VALUES (:id, :si, :so, :st, :sa, :eq, :ntm, :sum, 0, "none", :nip)
             ON DUPLICATE KEY UPDATE
                ships_in_count      = VALUES(ships_in_count),
                ships_out_count     = VALUES(ships_out_count),
                ships_transit_count = VALUES(ships_transit_count),
                ships_anchor_count  = VALUES(ships_anchor_count),
                equipment_status    = VALUES(equipment_status),
                ntm_notes           = VALUES(ntm_notes),
                summary_notes       = VALUES(summary_notes)'
        )->execute([
            ':id'  => $dailyShiftReportId,
            ':si'  => (int)($_POST['ships_in_count']      ?? 0),
            ':so'  => (int)($_POST['ships_out_count']     ?? 0),
            ':st'  => (int)($_POST['ships_transit_count'] ?? 0),
            ':sa'  => (int)($_POST['ships_anchor_count']  ?? 0),
            ':eq'  => trim($_POST['equipment_status']     ?? '') ?: null,
            ':ntm' => trim($_POST['ntm_notes']            ?? '') ?: null,
            ':sum' => trim($_POST['summary_notes']        ?? '') ?: null,
            ':nip' => $user['nip'],
        ]);

        logAudit($pdo, $user['nip'], 'CREATE', 'tide_reports', 0, null, 'A4 pasang surut & serah terima disimpan');
        echo json_encode(['success' => true, 'message' => 'A-4 Pasang Surut & Serah Terima berhasil disimpan.']);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-5  Pre-Arrival
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a5') {
        $vn  = trim($_POST['vessel_name']      ?? '');
        $eta = trim($_POST['expected_arrival'] ?? '');
        if ($vn === '' || $eta === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'A5 membutuhkan nama kapal dan ETA.']);
            exit;
        }
        $etaDT = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $eta, $tz);
        if (!$etaDT) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Format ETA A5 tidak valid.']);
            exit;
        }
        $pdo->prepare(
            'INSERT INTO pre_arrival_reports
                (daily_shift_report_id, vessel_name, imo_number, gross_tonnage, loa_m, draft_m, pob_count,
                 expected_arrival, cargo_details, special_notes, is_locked, edit_requested, created_by_nip)
             VALUES (:id, :vn, :imo, :gt, :loa, :dr, :pob, :eta, :cargo, :notes, 0, "none", :nip)'
        )->execute([
            ':id'    => $dailyShiftReportId,
            ':vn'    => $vn,
            ':imo'   => trim($_POST['imo_number']    ?? '') ?: null,
            ':gt'    => trim($_POST['gross_tonnage'] ?? '') !== '' ? (float)$_POST['gross_tonnage'] : null,
            ':loa'   => trim($_POST['loa_m']         ?? '') !== '' ? (float)$_POST['loa_m']         : null,
            ':dr'    => trim($_POST['draft_m']       ?? '') !== '' ? (float)$_POST['draft_m']        : null,
            ':pob'   => trim($_POST['pob_count']     ?? '') !== '' ? (int)$_POST['pob_count']        : null,
            ':eta'   => $etaDT->format('Y-m-d H:i:s'),
            ':cargo' => trim($_POST['cargo_details'] ?? '') ?: null,
            ':notes' => trim($_POST['special_notes'] ?? '') ?: null,
            ':nip'   => $user['nip'],
        ]);
        logAudit($pdo, $user['nip'], 'CREATE', 'pre_arrival_reports', (int)$pdo->lastInsertId(), null, 'A5 pre-arrival disimpan');
        echo json_encode(['success' => true, 'message' => 'A-5 Pra-Kedatangan berhasil disimpan.']);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-6  Incident Report
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a6') {
        $incDtRaw   = trim($_POST['incident_datetime'] ?? '');
        $title      = trim($_POST['title']             ?? '');
        $chronology = trim($_POST['chronology']        ?? '');
        if ($incDtRaw === '' || $title === '' || $chronology === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'A6 membutuhkan waktu kejadian, judul, dan kronologi.']);
            exit;
        }
        $incDT = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $incDtRaw, $tz);
        if (!$incDT) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Format waktu A6 tidak valid.']);
            exit;
        }
        $auths = $_POST['authorities_notified'] ?? [];
        if (!is_array($auths)) $auths = [];
        $pdo->prepare(
            'INSERT INTO incident_reports
                (daily_shift_report_id, incident_datetime, title, chronology, deaths_count, missing_count,
                 pollution_location, authorities_notified, immediate_action, is_locked, edit_requested, created_by_nip)
             VALUES (:id, :dt, :ti, :ch, :dea, :mis, :pol, :auth, :act, 0, "none", :nip)'
        )->execute([
            ':id'   => $dailyShiftReportId,
            ':dt'   => $incDT->format('Y-m-d H:i:s'),
            ':ti'   => $title,
            ':ch'   => $chronology,
            ':dea'  => (int)($_POST['deaths_count']       ?? 0),
            ':mis'  => (int)($_POST['missing_count']      ?? 0),
            ':pol'  => trim($_POST['pollution_location']  ?? '') ?: null,
            ':auth' => json_encode(array_values($auths), JSON_UNESCAPED_UNICODE),
            ':act'  => trim($_POST['immediate_action']    ?? '') ?: null,
            ':nip'  => $user['nip'],
        ]);
        logAudit($pdo, $user['nip'], 'CREATE', 'incident_reports', (int)$pdo->lastInsertId(), null, 'A6 insiden disimpan');
        echo json_encode(['success' => true, 'message' => 'A-6 Insiden berhasil disimpan.']);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-7  Special Ops
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a7') {
        $opName     = trim($_POST['operation_name']  ?? '');
        $opStartRaw = trim($_POST['operation_start'] ?? '');
        $opEndRaw   = trim($_POST['operation_end']   ?? '');
        if ($opName === '' || $opStartRaw === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'A7 membutuhkan nama operasi dan waktu mulai.']);
            exit;
        }
        $opStart = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $opStartRaw, $tz);
        $opEnd   = $opEndRaw !== '' ? DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $opEndRaw, $tz) : null;
        if (!$opStart || ($opEndRaw !== '' && !$opEnd)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Format tanggal A7 tidak valid.']);
            exit;
        }
        // sector field (form) → prepend to location; event_description (form) → operation_details (DB)
        $locRaw = trim($_POST['location'] ?? '');
        $secRaw = trim($_POST['sector']   ?? '');
        if ($secRaw !== '' && $locRaw !== '') {
            $locVal = $secRaw . ' — ' . $locRaw;
        } elseif ($secRaw !== '') {
            $locVal = $secRaw;
        } else {
            $locVal = $locRaw ?: null;
        }
        // Accept either operation_details or legacy event_description field name
        $detVal = trim($_POST['operation_details'] ?? '') !== ''
                    ? trim($_POST['operation_details'])
                    : (trim($_POST['event_description'] ?? '') ?: null);

        $pdo->prepare(
            'INSERT INTO special_ops_reports
                (daily_shift_report_id, operation_name, operation_start, operation_end, location,
                 operation_details, outcome_notes, is_locked, edit_requested, created_by_nip)
             VALUES (:id, :nm, :os, :oe, :loc, :det, :out, 0, "none", :nip)'
        )->execute([
            ':id'  => $dailyShiftReportId,
            ':nm'  => $opName,
            ':os'  => $opStart->format('Y-m-d H:i:s'),
            ':oe'  => $opEnd ? $opEnd->format('Y-m-d H:i:s') : null,
            ':loc' => $locVal,
            ':det' => $detVal,
            ':out' => trim($_POST['outcome_notes'] ?? '') ?: null,
            ':nip' => $user['nip'],
        ]);
        logAudit($pdo, $user['nip'], 'CREATE', 'special_ops_reports', (int)$pdo->lastInsertId(), null, 'A7 ops khusus disimpan');
        echo json_encode(['success' => true, 'message' => 'A-7 Operasi Khusus berhasil disimpan.']);
        exit;
    }

    // ══════════════════════════════════════════════════════════════════════
    // A-8  Contravention
    // ══════════════════════════════════════════════════════════════════════
    if ($formType === 'a8') {
        $vn      = trim($_POST['vessel_name']         ?? '');
        $vtype   = trim($_POST['violation_type']      ?? '');
        $vdtRaw  = trim($_POST['violation_datetime']  ?? '');
        if ($vn === '' || $vtype === '' || $vdtRaw === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'A8 membutuhkan nama kapal, jenis pelanggaran, dan waktu.']);
            exit;
        }
        $vDT = DateTimeImmutable::createFromFormat('Y-m-d\\TH:i', $vdtRaw, $tz);
        if (!$vDT) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Format waktu A8 tidak valid.']);
            exit;
        }
        $pdo->prepare(
            'INSERT INTO contravention_reports
                (daily_shift_report_id, vessel_name, violation_type, violation_datetime, location,
                 warning_type, legal_reference, action_taken, is_locked, edit_requested, created_by_nip)
             VALUES (:id, :vn, :vt, :vdt, :loc, :wt, :lr, :act, 0, "none", :nip)'
        )->execute([
            ':id'  => $dailyShiftReportId,
            ':vn'  => $vn,
            ':vt'  => $vtype,
            ':vdt' => $vDT->format('Y-m-d H:i:s'),
            ':loc' => trim($_POST['location']        ?? '') ?: null,
            ':wt'  => trim($_POST['warning_type']    ?? '') ?: null,
            ':lr'  => trim($_POST['legal_reference'] ?? '') ?: null,
            ':act' => trim($_POST['action_taken']    ?? '') ?: null,
            ':nip' => $user['nip'],
        ]);
        logAudit($pdo, $user['nip'], 'CREATE', 'contravention_reports', (int)$pdo->lastInsertId(), null, 'A8 pelanggaran disimpan');
        echo json_encode(['success' => true, 'message' => 'A-8 Pelanggaran berhasil disimpan.']);
        exit;
    }

    // Should never reach here due to in_array check above
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Form type tidak dikenali.']);
    exit;

} catch (Throwable $e) {
    logError('save_all_forms [' . ($formType ?? '?') . ']: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.']);
    exit;
}
