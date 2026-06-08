<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

header('Content-Type: application/json');

requireLogin();
requireAnyRole(['Operator', 'Supervisor']);

// ── Request parsing (supports both JSON and FormData) ───────────────────────
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (str_contains($contentType, 'application/json')) {
    $raw    = file_get_contents('php://input');
    $data   = json_decode($raw, true) ?? [];
    $action = (string)($data['action'] ?? '');
    // CSRF from JSON body
    $submittedCsrf = (string)($data['csrf_token'] ?? '');
} else {
    $data          = $_POST;
    $action        = (string)($data['action'] ?? '');
    $submittedCsrf = (string)($data['csrf_token'] ?? '');
}

// ── CSRF check ───────────────────────────────────────────────────────────────
$sessionCsrf = csrfToken();
if ($submittedCsrf === '' || !hash_equals($sessionCsrf, $submittedCsrf)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF token tidak valid.']);
    exit;
}

$pdo  = getPdo();
$user = $_SESSION['user'];

// ── Helper: sanitize string input ────────────────────────────────────────────
function hs(mixed $v): string {
    return trim((string)($v ?? ''));
}

// ── Map extended weather labels to the 5 DB-valid ENUM values ────────────────
function mapWeatherCondition(string $raw): string {
    $map = [
        'Cerah'              => 'Cerah',
        'Berawan Sebagian'   => 'Berawan',
        'Berawan'            => 'Berawan',
        'Gerimis'            => 'Hujan Ringan',
        'Hujan Ringan'       => 'Hujan Ringan',
        'Hujan Sedang'       => 'Hujan Lebat',
        'Hujan Lebat'        => 'Hujan Lebat',
        'Hujan Sangat Lebat' => 'Hujan Lebat',
        'Badai'              => 'Badai',
    ];
    return $map[$raw] ?? 'Cerah';
}

// ── Helper: log audit for historical entry ───────────────────────────────────
function logHistoricalAudit(PDO $pdo, int $reportId, string $formType, string $nip, string $date, string $shift): void {
    $desc = sprintf(
        'Manual Backdate Entry by %s — %s untuk %s %s',
        $nip ?: 'N/A',
        strtoupper($formType),
        $date,
        $shift
    );
    try {
        // Use 'CREATE' as action_type (ENUM-safe until migration adds BACKDATE_ENTRY)
        logAudit($pdo, $nip ?: 'system', 'CREATE', 'daily_shift_reports', $reportId, null, $desc);
    } catch (Throwable) {
        // Non-fatal — audit failure must not block data save
    }
}

// ── ACTIONS ───────────────────────────────────────────────────────────────────

// ══════════════════════════════════════════════════
// ACTION: resolve_shift
// Creates or retrieves a daily_shift_reports row for
// the given date / category / team without checking
// current-time shift logic (bypasses canWriteShiftRecord).
// ══════════════════════════════════════════════════
if ($action === 'resolve_shift') {
    $shiftDate    = hs($data['shift_date'] ?? '');
    $shiftCat     = hs($data['shift_category'] ?? '');
    $team         = hs($data['team'] ?? '');
    $officerNip   = hs($data['officer_nip'] ?? '');
    $supervisorNip = hs($data['supervisor_nip'] ?? '');

    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $shiftDate)) {
        echo json_encode(['success' => false, 'message' => 'Format tanggal tidak valid.']);
        exit;
    }
    // Disallow future dates
    if ($shiftDate > date('Y-m-d')) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat memasukkan data untuk tanggal yang akan datang.']);
        exit;
    }
    $allowedCats = ['Pagi', 'Siang', 'Malam'];
    if (!in_array($shiftCat, $allowedCats, true)) {
        echo json_encode(['success' => false, 'message' => 'Shift tidak valid.']);
        exit;
    }
    $allowedTeams = ['A', 'B', 'C', 'D', 'E'];
    if (!in_array($team, $allowedTeams, true)) {
        echo json_encode(['success' => false, 'message' => 'Tim tidak valid.']);
        exit;
    }

    try {
        // Check if this shift report already exists
        $sel = $pdo->prepare(
            'SELECT id, final_copy_watermark FROM daily_shift_reports
              WHERE shift_date = :d AND shift_category = :c AND team = :t LIMIT 1'
        );
        $sel->execute([':d' => $shiftDate, ':c' => $shiftCat, ':t' => $team]);
        $existing = $sel->fetch();

        if ($existing) {
            if ((int)$existing['final_copy_watermark'] === 1) {
                echo json_encode([
                    'success'  => false,
                    'message'  => 'Shift Report ini sudah FINAL (dikunci). Hubungi Supervisor atau Manager untuk membuka kunci.',
                ]);
                exit;
            }
            echo json_encode([
                'success'          => true,
                'shift_report_id'  => (int)$existing['id'],
                'is_new'           => false,
                'message'          => 'Shift Report yang ada berhasil dimuat.',
            ]);
            exit;
        }

        // Create new shift report for the historical date
        $ins = $pdo->prepare(
            'INSERT INTO daily_shift_reports
               (shift_date, shift_category, team, officer_on_duty_nip, supervisor_on_watch_nip,
                status, is_historical, created_at)
             VALUES
               (:d, :c, :t, :o, :s, "open", 1, NOW())'
        );
        $ins->execute([
            ':d' => $shiftDate,
            ':c' => $shiftCat,
            ':t' => $team,
            ':o' => $officerNip   ?: null,
            ':s' => $supervisorNip ?: null,
        ]);
        $newId = (int)$pdo->lastInsertId();

        // Log audit for creation
        logHistoricalAudit($pdo, $newId, 'shift_report', $user['nip'], $shiftDate, $shiftCat);

        echo json_encode([
            'success'         => true,
            'shift_report_id' => $newId,
            'is_new'          => true,
            'message'         => 'Shift Report baru berhasil dibuat.',
        ]);
    } catch (PDOException $e) {
        logError('historical_entry_store resolve_shift: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// ══════════════════════════════════════════════════
// ACTION: save_a2 — vessel traffic
// ══════════════════════════════════════════════════
if ($action === 'save_a2') {
    $shiftReportId = (int)($data['shift_report_id'] ?? 0);
    $vessels       = $data['vessels'] ?? [];

    if ($shiftReportId <= 0) {
        echo json_encode(['success' => false, 'message' => 'shift_report_id tidak valid.']);
        exit;
    }
    if (!is_array($vessels) || count($vessels) === 0) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada data kapal.']);
        exit;
    }

    // Verify shift report exists and is not final-locked
    $chk = $pdo->prepare('SELECT final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
    $chk->execute([':id' => $shiftReportId]);
    $sr = $chk->fetch();
    if (!$sr) {
        echo json_encode(['success' => false, 'message' => 'Shift Report tidak ditemukan.']);
        exit;
    }
    if ((int)$sr['final_copy_watermark'] === 1) {
        echo json_encode(['success' => false, 'message' => 'Shift Report sudah dikunci.']);
        exit;
    }

    $allowedDirections = ['inbound', 'outbound', 'transit'];
    $stmt = $pdo->prepare(
        'INSERT INTO vessel_traffic
           (daily_shift_report_id, vessel_name, call_sign, last_port, next_port,
            etd, eta, qso_position, vessel_type, agent, direction, created_at)
         VALUES
           (:sid, :vn, :cs, :lp, :np, :etd, :eta, :qso, :vt, :ag, :dir, NOW())'
    );

    try {
        $pdo->beginTransaction();
        $inserted = 0;
        foreach ($vessels as $v) {
            $vn  = hs($v['vessel_name'] ?? '');
            $dir = hs($v['direction'] ?? 'inbound');
            if (!in_array($dir, $allowedDirections, true)) $dir = 'inbound';
            if ($vn === '') continue;

            $etd = hs($v['etd'] ?? '');
            $eta = hs($v['eta'] ?? '');
            $stmt->execute([
                ':sid' => $shiftReportId,
                ':vn'  => $vn,
                ':cs'  => hs($v['call_sign']    ?? ''),
                ':lp'  => hs($v['last_port']    ?? ''),
                ':np'  => hs($v['next_port']    ?? ''),
                ':etd' => $etd !== '' ? $etd : null,
                ':eta' => $eta !== '' ? $eta : null,
                ':qso' => hs($v['qso_position'] ?? ''),
                ':vt'  => hs($v['vessel_type']  ?? ''),
                ':ag'  => hs($v['agent']        ?? ''),
                ':dir' => $dir,
            ]);
            $inserted++;
        }
        $pdo->commit();
        logHistoricalAudit($pdo, $shiftReportId, 'a2', $user['nip'],
            hs($data['shift_date'] ?? ''), hs($data['shift_category'] ?? ''));
        echo json_encode(['success' => true, 'inserted' => $inserted]);
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        logError('historical_entry_store save_a2: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// ══════════════════════════════════════════════════
// ACTION: save_a3 — weather observations
// ══════════════════════════════════════════════════
if ($action === 'save_a3') {
    $shiftReportId = (int)($data['shift_report_id'] ?? 0);
    $observations  = $data['observations'] ?? [];

    if ($shiftReportId <= 0 || !is_array($observations)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
        exit;
    }

    // Verify report
    $chk = $pdo->prepare('SELECT final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
    $chk->execute([':id' => $shiftReportId]);
    $sr = $chk->fetch();
    if (!$sr) { echo json_encode(['success' => false, 'message' => 'Shift Report tidak ditemukan.']); exit; }
    if ((int)$sr['final_copy_watermark'] === 1) { echo json_encode(['success' => false, 'message' => 'Shift Report sudah dikunci.']); exit; }

    try {
        $pdo->beginTransaction();

        // Upsert weather_reports parent row (one per shift report)
        $wrUpsert = $pdo->prepare(
            'INSERT INTO weather_reports (daily_shift_report_id, report_time, reported_by_nip)
             VALUES (:sid, NOW(), :nip)
             ON DUPLICATE KEY UPDATE report_time = NOW(), reported_by_nip = :nip2'
        );
        $reporterNip = hs($data['officer_nip'] ?? '');
        $wrUpsert->execute([':sid' => $shiftReportId, ':nip' => $reporterNip ?: null, ':nip2' => $reporterNip ?: null]);

        // Get the weather_report id
        $wrSel = $pdo->prepare('SELECT id FROM weather_reports WHERE daily_shift_report_id = :sid LIMIT 1');
        $wrSel->execute([':sid' => $shiftReportId]);
        $weatherReportId = (int)($wrSel->fetchColumn() ?: $pdo->lastInsertId());

        // Allowed wave categories
        $allowedWaveCats = ['Tenang', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'];

        $woUpsert = $pdo->prepare(
            'INSERT INTO weather_observations
               (weather_report_id, area_name, weather_condition, wind_direction,
                wind_speed_knots, wave_height_m, wave_category)
             VALUES
               (:wrid, :an, :wc, :wd, :ws, :wh, :wcat)
             ON DUPLICATE KEY UPDATE
               weather_condition = :wc2,
               wind_direction    = :wd2,
               wind_speed_knots  = :ws2,
               wave_height_m     = :wh2,
               wave_category     = :wcat2'
        );

        foreach ($observations as $obs) {
            $areaName = hs($obs['area_name'] ?? '');
            if ($areaName === '') continue;

            $wc   = mapWeatherCondition(hs($obs['weather_condition'] ?? 'Cerah'));
            $wcat = in_array($obs['wave_category'] ?? '', $allowedWaveCats, true)
                  ? $obs['wave_category']
                  : 'Tenang';

            $woUpsert->execute([
                ':wrid'  => $weatherReportId,
                ':an'    => $areaName,
                ':wc'    => $wc,   ':wc2'   => $wc,
                ':wd'    => hs($obs['wind_direction'] ?? '-'),   ':wd2'   => hs($obs['wind_direction'] ?? '-'),
                ':ws'    => max(0, (float)($obs['wind_speed_knots'] ?? 0)), ':ws2' => max(0, (float)($obs['wind_speed_knots'] ?? 0)),
                ':wh'    => max(0, (float)($obs['wave_height_m']    ?? 0)), ':wh2' => max(0, (float)($obs['wave_height_m']    ?? 0)),
                ':wcat'  => $wcat, ':wcat2' => $wcat,
            ]);
        }
        $pdo->commit();
        logHistoricalAudit($pdo, $shiftReportId, 'a3', $user['nip'],
            hs($data['shift_date'] ?? ''), hs($data['shift_category'] ?? ''));
        echo json_encode(['success' => true, 'saved' => count($observations)]);
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        logError('historical_entry_store save_a3: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// ══════════════════════════════════════════════════
// ACTION: save_a6 — incident report
// ══════════════════════════════════════════════════
if ($action === 'save_a6') {
    $shiftReportId = (int)($data['shift_report_id'] ?? 0);
    $title         = hs($data['title']             ?? '');
    $incidentAt    = hs($data['incident_datetime'] ?? '');
    $chronology    = hs($data['chronology']        ?? '');

    if ($shiftReportId <= 0 || $title === '' || $incidentAt === '' || $chronology === '') {
        echo json_encode(['success' => false, 'message' => 'Field wajib kosong.']);
        exit;
    }

    // Verify report
    $chk = $pdo->prepare('SELECT final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
    $chk->execute([':id' => $shiftReportId]);
    $sr = $chk->fetch();
    if (!$sr) { echo json_encode(['success' => false, 'message' => 'Shift Report tidak ditemukan.']); exit; }
    if ((int)$sr['final_copy_watermark'] === 1) { echo json_encode(['success' => false, 'message' => 'Shift Report sudah dikunci.']); exit; }

    $auths = $data['authorities_notified'] ?? [];
    if (!is_array($auths)) $auths = [];
    $authsJson = json_encode(array_map('htmlspecialchars', $auths), JSON_UNESCAPED_UNICODE);

    try {
        $ins = $pdo->prepare(
            'INSERT INTO incident_reports
               (daily_shift_report_id, title, incident_datetime, chronology,
                deaths_count, missing_count, pollution_location,
                authorities_notified, immediate_action, reported_by_nip, created_at)
             VALUES
               (:sid, :tt, :iat, :chr, :dc, :mc, :pl, :auth, :ia, :nip, NOW())'
        );
        $ins->execute([
            ':sid'  => $shiftReportId,
            ':tt'   => $title,
            ':iat'  => $incidentAt,
            ':chr'  => $chronology,
            ':dc'   => max(0, (int)($data['deaths_count']  ?? 0)),
            ':mc'   => max(0, (int)($data['missing_count'] ?? 0)),
            ':pl'   => hs($data['pollution_location'] ?? ''),
            ':auth' => $authsJson,
            ':ia'   => hs($data['immediate_action'] ?? ''),
            ':nip'  => hs($data['officer_nip'] ?? '') ?: null,
        ]);
        $newId = (int)$pdo->lastInsertId();
        logHistoricalAudit($pdo, $shiftReportId, 'a6', $user['nip'],
            hs($data['shift_date'] ?? ''), hs($data['shift_category'] ?? ''));
        echo json_encode(['success' => true, 'incident_report_id' => $newId]);
    } catch (PDOException $e) {
        logError('historical_entry_store save_a6: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// ══════════════════════════════════════════════════
// ACTION: save_a7 — special ops report
// ══════════════════════════════════════════════════
if ($action === 'save_a7') {
    $shiftReportId = (int)($data['shift_report_id'] ?? 0);
    $opName        = hs($data['operation_name']  ?? '');
    $opStart       = hs($data['operation_start'] ?? '');

    if ($shiftReportId <= 0 || $opName === '' || $opStart === '') {
        echo json_encode(['success' => false, 'message' => 'Nama Operasi dan Waktu Mulai wajib diisi.']);
        exit;
    }

    // Verify report
    $chk = $pdo->prepare('SELECT final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
    $chk->execute([':id' => $shiftReportId]);
    $sr = $chk->fetch();
    if (!$sr) { echo json_encode(['success' => false, 'message' => 'Shift Report tidak ditemukan.']); exit; }
    if ((int)$sr['final_copy_watermark'] === 1) { echo json_encode(['success' => false, 'message' => 'Shift Report sudah dikunci.']); exit; }

    try {
        $ins = $pdo->prepare(
            'INSERT INTO special_ops_reports
               (daily_shift_report_id, operation_name, operation_start, operation_end,
                location, sector, event_description, operation_details, outcome_notes,
                reported_by_nip, created_at)
             VALUES
               (:sid, :on1, :os, :oe, :loc, :sec, :evd, :opd, :otn, :nip, NOW())'
        );
        $opEnd = hs($data['operation_end'] ?? '');
        $ins->execute([
            ':sid'  => $shiftReportId,
            ':on1'  => $opName,
            ':os'   => $opStart,
            ':oe'   => $opEnd !== '' ? $opEnd : null,
            ':loc'  => hs($data['location']          ?? ''),
            ':sec'  => hs($data['sector']             ?? ''),
            ':evd'  => hs($data['event_description']  ?? ''),
            ':opd'  => hs($data['operation_details']  ?? ''),
            ':otn'  => hs($data['outcome_notes']      ?? ''),
            ':nip'  => hs($data['officer_nip']        ?? '') ?: null,
        ]);
        $newId = (int)$pdo->lastInsertId();
        logHistoricalAudit($pdo, $shiftReportId, 'a7', $user['nip'],
            hs($data['shift_date'] ?? ''), hs($data['shift_category'] ?? ''));
        echo json_encode(['success' => true, 'special_ops_report_id' => $newId]);
    } catch (PDOException $e) {
        logError('historical_entry_store save_a7: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// ── Unknown action ───────────────────────────────────────────────────────────
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal: ' . htmlspecialchars($action)]);
