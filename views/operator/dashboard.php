<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';
require_once __DIR__ . '/../partials/layout.php';

requireLogin();
requireRole('Operator');

$user = $_SESSION['user'];
$pdo  = getPdo();

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$dailyShiftReportId = (int) ($user['daily_shift_report_id'] ?? 0);

// ── Active Shift Auto-Detect ────────────────────────────────────────────────
// Guarantee a valid shift record. Re-detects when:
//   • session daily_shift_report_id is 0 (first page load or cleared session)
//   • operator's session is stale (logged in during a previous shift)
{
    $_tz           = new DateTimeZone('Asia/Jakarta');
    $_nowASD       = new DateTimeImmutable('now', $_tz);
    $_curShiftCat  = currentShiftCategory($_nowASD);
    $_curShiftDate = operationalShiftDate($_nowASD);
    $_sesShiftCat  = (string) ($user['login_shift_category'] ?? '');
    $_sesShiftDate = (string) ($user['login_shift_date']     ?? '');
    if ($dailyShiftReportId <= 0
        || $_sesShiftCat  !== $_curShiftCat
        || $_sesShiftDate !== $_curShiftDate) {
        $dailyShiftReportId = upsertDailyShiftReport($pdo, $user, $_curShiftDate, $_curShiftCat);
        $_SESSION['user']['daily_shift_report_id'] = $dailyShiftReportId;
        $_SESSION['user']['login_shift_category']  = $_curShiftCat;
        $_SESSION['user']['login_shift_date']      = $_curShiftDate;
        $user = $_SESSION['user'];
    }
    unset($_tz, $_nowASD, $_curShiftCat, $_curShiftDate, $_sesShiftCat, $_sesShiftDate);
}

// Shift Report
$shiftReport = null;
if ($dailyShiftReportId > 0) {
    $s = $pdo->prepare('SELECT * FROM daily_shift_reports WHERE id = :id LIMIT 1');
    $s->execute([':id' => $dailyShiftReportId]);
    $shiftReport = $s->fetch() ?: null;
}
$isLocked        = (int) ($shiftReport['is_locked']            ?? 0) === 1;
$isFinalized     = (int) ($shiftReport['final_copy_watermark'] ?? 0) === 1;
$editAccessUntil = ($isLocked && !$isFinalized)
    ? getActiveEditAccessUntil($pdo, $dailyShiftReportId, (string) $user['nip'])
    : null;
$canWriteForms   = !$isFinalized && (!$isLocked || $editAccessUntil !== null);

// Vessel Traffic Stats (inbound / outbound / transit)
$trafficCounts = ['inbound' => 0, 'outbound' => 0, 'transit' => 0];
if ($dailyShiftReportId > 0) {
    $tStmt = $pdo->prepare(
        'SELECT direction, COUNT(*) AS cnt FROM vessel_traffic WHERE daily_shift_report_id = :id GROUP BY direction'
    );
    $tStmt->execute([':id' => $dailyShiftReportId]);
    foreach ($tStmt->fetchAll() as $r) {
        $d = (string) ($r['direction'] ?? '');
        if (array_key_exists($d, $trafficCounts)) {
            $trafficCounts[$d] = (int) $r['cnt'];
        }
    }
}

// Wave Height Trend - Ambang Luar last 24h
$waveTrendLabels = [];
$waveTrendData   = [];
if ($dailyShiftReportId > 0) {
    $wvStmt = $pdo->prepare(
        'SELECT DATE_FORMAT(wr.created_at, "%H:%i") AS label,
                ROUND(AVG(wo.wave_height_m), 2) AS avg_wave
         FROM weather_reports wr
         JOIN weather_observations wo ON wo.weather_report_id = wr.id
         WHERE wo.area_name = "Ambang Luar"
           AND wr.created_at >= NOW() - INTERVAL 24 HOUR
         GROUP BY DATE_FORMAT(wr.created_at, "%Y-%m-%d %H")
         ORDER BY wr.created_at ASC LIMIT 24'
    );
    $wvStmt->execute();
    foreach ($wvStmt->fetchAll() as $r) {
        $waveTrendLabels[] = (string) $r['label'];
        $waveTrendData[]   = (float)  $r['avg_wave'];
    }
}
if (empty($waveTrendLabels)) {
    $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
    for ($h = 11; $h >= 0; $h--) {
        $waveTrendLabels[] = $now->modify("-{$h} hours")->format('H:00');
        $waveTrendData[]   = 0.0;
    }
}

// 11 Maritime Areas
$areaStmt = $pdo->query('SELECT area_name FROM maritime_areas WHERE is_active = 1 ORDER BY display_order ASC');
$areas    = $areaStmt ? $areaStmt->fetchAll(PDO::FETCH_COLUMN) : [];
if (empty($areas)) {
    $areas = ['Banyuasin','Selat Gelasa','Bangka','Muara Sungai Musi','Selat Bangka Utara',
              'Selat Bangka Selatan','Perairan Sungsang','Perairan Tanjung Buyut',
              'Perairan Upang','Ambang Luar','Tanjung Api-Api'];
}

$weatherByArea = [];
if ($dailyShiftReportId > 0) {
    $wStmt = $pdo->prepare(
        'SELECT wo.* FROM weather_observations wo
         JOIN weather_reports wr ON wr.id = wo.weather_report_id
         WHERE wr.daily_shift_report_id = :id'
    );
    $wStmt->execute([':id' => $dailyShiftReportId]);
    foreach ($wStmt->fetchAll() as $r) {
        $weatherByArea[(string) $r['area_name']] = $r;
    }
}
// Weather options must match weather_observations.weather_condition ENUM in schema.sql
$weatherOptions = ['Cerah', 'Berawan', 'Hujan Ringan', 'Hujan Lebat', 'Badai'];
// Wave categories must match weather_observations.wave_category ENUM in schema.sql
$waveCategories = ['Tenang', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'];

// A-1 VTS Log
$logRows = [];
if ($dailyShiftReportId > 0) {
    $logStmt = $pdo->prepare(
        'SELECT * FROM vts_logs WHERE daily_shift_report_id = :id ORDER BY created_at DESC LIMIT 30'
    );
    $logStmt->execute([':id' => $dailyShiftReportId]);
    $logRows = $logStmt->fetchAll();
}

// A-2 Vessel Traffic
$vesselRows = [];
if ($dailyShiftReportId > 0) {
    $a2Stmt = $pdo->prepare(
        'SELECT vessel_name, call_sign, last_port, next_port, vessel_type, agent, direction, remarks, created_at
         FROM vessel_traffic WHERE daily_shift_report_id = :id ORDER BY created_at DESC LIMIT 50'
    );
    $a2Stmt->execute([':id' => $dailyShiftReportId]);
    $vesselRows = $a2Stmt->fetchAll();
}

// A-4 Tide Report
$tideReport = null;
if ($dailyShiftReportId > 0) {
    $t4 = $pdo->prepare('SELECT * FROM tide_reports WHERE daily_shift_report_id = :id LIMIT 1');
    $t4->execute([':id' => $dailyShiftReportId]);
    $tideReport = $t4->fetch() ?: null;
}

// A-4 Handover Report
$handoverReport = null;
if ($dailyShiftReportId > 0) {
    $h4 = $pdo->prepare('SELECT * FROM handover_reports WHERE daily_shift_report_id = :id LIMIT 1');
    $h4->execute([':id' => $dailyShiftReportId]);
    $handoverReport = $h4->fetch() ?: null;
}

// A-5 Pre-Arrival Reports
$preArrivalRows = [];
if ($dailyShiftReportId > 0) {
    $p5 = $pdo->prepare('SELECT * FROM pre_arrival_reports WHERE daily_shift_report_id = :id ORDER BY created_at DESC LIMIT 50');
    $p5->execute([':id' => $dailyShiftReportId]);
    $preArrivalRows = $p5->fetchAll();
}

// A-8 Contravention Reports
$contraventionRows = [];
if ($dailyShiftReportId > 0) {
    $c8 = $pdo->prepare('SELECT * FROM contravention_reports WHERE daily_shift_report_id = :id ORDER BY created_at DESC LIMIT 30');
    $c8->execute([':id' => $dailyShiftReportId]);
    $contraventionRows = $c8->fetchAll();
}

// A-6 Saved incident rows (for display in A6 tab)
$incidentRows = [];
if ($dailyShiftReportId > 0) {
    $iStmt = $pdo->prepare(
        'SELECT id, incident_datetime, title, deaths_count, missing_count, pollution_location
         FROM incident_reports
         WHERE daily_shift_report_id = :id ORDER BY incident_datetime ASC LIMIT 30'
    );
    $iStmt->execute([':id' => $dailyShiftReportId]);
    $incidentRows = $iStmt->fetchAll();
}

// A-7 Saved special ops rows (for display in A7 tab)
$specialOpsRows = [];
if ($dailyShiftReportId > 0) {
    $soStmt = $pdo->prepare(
        'SELECT id, operation_name, operation_start, operation_end, location, outcome_notes
         FROM special_ops_reports
         WHERE daily_shift_report_id = :id ORDER BY operation_start ASC LIMIT 20'
    );
    $soStmt->execute([':id' => $dailyShiftReportId]);
    $specialOpsRows = $soStmt->fetchAll();
}

// Shift End Time for countdown
$shiftCat = currentShiftCategory();
$nowJKT   = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
$shiftEnd = match ($shiftCat) { 'Pagi' => 14, 'Siang' => 20, default => 8 };
$endDT    = $nowJKT->setTime($shiftEnd, 0, 0);
if ($endDT <= $nowJKT) {
    $endDT = $endDT->modify('+1 day');
}
$shiftEndISO = $endDT->format(DateTimeInterface::ATOM);

// JS-injected values (safe JSON)
$jsTrafficData = json_encode(array_values($trafficCounts), JSON_UNESCAPED_UNICODE);
$jsWaveLabels  = json_encode($waveTrendLabels,             JSON_UNESCAPED_UNICODE);
$jsWaveData    = json_encode($waveTrendData,               JSON_UNESCAPED_UNICODE);
$jsShiftEnd    = json_encode($shiftEndISO);
$jsReportId    = $dailyShiftReportId;
$jsCsrf        = json_encode(csrfToken());
$jsBaseUrl     = json_encode(BASE_APP);  // BASE_APP = project root, for action endpoints

// Active tab — validated against allowed set; driven by ?tab=X from sidenav links
$_allowedTabs = ['a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8'];
$activeTab    = (isset($_GET['tab']) && in_array($_GET['tab'], $_allowedTabs, true))
                ? $_GET['tab']
                : 'a1';
// If the requested tab requires canWriteForms but it's locked, fall back to a1
if (in_array($activeTab, ['a6', 'a7'], true) && !$canWriteForms) {
    $activeTab = 'a1';
}

// A-6 unacknowledged badge count (incidents in this shift not yet finalized)
$a6BadgeCount = 0;
if ($dailyShiftReportId > 0 && !$isFinalized) {
    $a6q = $pdo->prepare(
        'SELECT COUNT(*) FROM incident_reports WHERE daily_shift_report_id = :id'
    );
    $a6q->execute([':id' => $dailyShiftReportId]);
    $a6BadgeCount = (int) $a6q->fetchColumn();
}

// Weather icon config helper
function weatherIconConfig(string $cond): array
{
    $lc = mb_strtolower($cond, 'UTF-8');
    if (str_contains($lc, 'badai') || str_contains($lc, 'sangat')) {
        return ['thunderstorm', 'text-red-500', true];
    }
    if (str_contains($lc, 'lebat')) {
        return ['thunderstorm', 'text-red-400', true];
    }
    if (str_contains($lc, 'hujan') || str_contains($lc, 'gerimis')) {
        return ['rainy', 'text-yellow-400', false];
    }
    if (str_contains($lc, 'berawan')) {
        return ['cloud', 'text-blue-300', false];
    }
    return ['wb_sunny', 'text-blue-400', false];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Operator — VTS Palembang</title>
    <!-- PWA -->
    <link rel="manifest" href="<?= BASE_APP ?>manifest.php">
    <meta name="theme-color" content="#002147">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VTS-OPS">
    <link rel="apple-touch-icon" href="<?= BASE_APP ?>assets/img/logo_navigasi.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@200..700,0..1&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#dce6f4 0%,#eef2f9 60%,#dce6f4 100%);background-attachment:fixed;color:#1a2840;min-height:100vh}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}
        .glass-panel{background:rgba(255,255,255,.82);backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.95);box-shadow:0 4px 24px rgba(0,30,64,.09)}
        .tab-btn{padding:.5rem 1.25rem;border-radius:.5rem;font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#5a6b80;background:transparent;border:1px solid transparent;transition:all .15s;cursor:pointer}
        .tab-btn:hover{background:rgba(0,30,64,.06)}
        .tab-btn.active{background:#001E40;color:#FECB00;border-color:rgba(0,30,64,.25)}
        .tab-content{animation:fadeInTab .22s ease both}
        @keyframes fadeInTab{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .tab-content.hidden{display:none!important;animation:none}
        .input-dark{background:rgba(255,255,255,.85);border:1px solid rgba(0,30,64,.18);color:#1a2840;border-radius:.375rem;padding:.375rem .5rem;font-size:.75rem;width:100%}
        .input-dark:focus{outline:none;border-color:#001E40;box-shadow:0 0 0 2px rgba(0,30,64,.1)}
        .input-dark option{background:#fff;color:#1a2840}
        .machined-button{background:linear-gradient(135deg,#001E40 0%,#003580 100%);color:#FECB00;font-weight:700;border-radius:.375rem;padding:.5rem 1rem;font-size:.75rem;cursor:pointer;border:none;transition:filter .15s}
        .machined-button:hover{filter:brightness(1.15)}
        .machined-button:disabled{opacity:.5;cursor:not-allowed}
        #toast-host{position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:.5rem;pointer-events:none}
        .toast-item{padding:.75rem 1.5rem;border-radius:.75rem;font-size:.75rem;font-weight:600;color:#fff;opacity:0;transform:translateY(16px);transition:all .3s;pointer-events:none;max-width:380px;text-align:center}
        .toast-item.show{opacity:1;transform:translateY(0);pointer-events:auto}
        .toast-item.success{background:rgba(5,150,105,.95);backdrop-filter:blur(8px)}
        .toast-item.error{background:rgba(220,38,38,.95);backdrop-filter:blur(8px)}
        .toast-item.warning{background:rgba(161,98,7,.95);backdrop-filter:blur(8px)}
        .toast-item.info{background:rgba(37,99,235,.95);backdrop-filter:blur(8px)}
        .weather-card{background:rgba(255,255,255,.6);border:1px solid rgba(0,30,64,.1);border-radius:1rem;padding:1rem;transition:border-color .2s}
        .weather-card:hover{border-color:rgba(0,30,64,.25)}
        .wd-icon{font-size:2rem!important;line-height:1}
        .countdown-danger{color:#dc2626!important;animation:blink .8s infinite}
        .countdown-warning{color:#b45309!important}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:.5}}
        table.dtbl{width:100%;border-collapse:collapse}
        table.dtbl th{background:#001E40;color:#FECB00;font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;padding:.5rem .75rem;text-align:left}
        table.dtbl td{font-size:.7rem;padding:.5rem .75rem;border-bottom:1px solid rgba(0,30,64,.07);color:#1a2840}
        table.dtbl tr:hover td{background:rgba(0,30,64,.03)}
        .skeleton{background:linear-gradient(90deg,rgba(0,30,64,.04) 25%,rgba(0,30,64,.1) 50%,rgba(0,30,64,.04) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:.5rem}
        @keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
        .badge-pulse{display:inline-flex;align-items:center;justify-content:center;min-width:1.1rem;height:1.1rem;border-radius:9999px;background:#dc2626;color:#fff;font-size:.55rem;font-weight:900;animation:badgePulse 1s ease-in-out infinite;margin-left:.25rem;padding:0 .25rem}
        @keyframes badgePulse{0%,100%{box-shadow:0 0 0 0 rgba(220,38,38,.7)}50%{box-shadow:0 0 0 5px rgba(220,38,38,0)}}
        #incidentModal{display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.6);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem}
        #incidentModal.open{display:flex}
        #incidentModalBox{background:#0f1523;border:1px solid rgba(255,255,255,.1);border-radius:1.25rem;max-width:560px;width:100%;max-height:90vh;overflow-y:auto;padding:2rem;position:relative}
        .modal-label{display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#475569;margin-bottom:.25rem}
        textarea.input-dark{resize:vertical;min-height:80px}
        /* White-theme text overrides (scoped to main content) */
        main .text-white{color:#1a2840!important}
        main h1,main h2,main h3,main h4{color:#1a2840}
        main .text-slate-400{color:#64748b!important}
        main .text-slate-500{color:#94a3b8!important}
        main .text-emerald-400{color:#059669!important}
        main .text-blue-400{color:#2563eb!important}
        main .text-amber-400{color:#d97706!important}
        main .text-red-400{color:#dc2626!important}
        main .text-red-300{color:#ef4444!important}
        main .text-emerald-300{color:#059669!important}
        main .text-amber-300{color:#b45309!important}
        /* Analytics stat numbers */
        main p.text-xl{color:#1a2840}
        /* Dark badge/overlay keep white text */
        .badge-pulse,span[class*="bg-"][class*="text-white"]{color:#fff!important}
        /* Keep locekd/status badges legible */
        .bg-\[\#001E40\]{color:#FECB00}
    </style>
</head>
<body class="antialiased">
<?php include __DIR__ . '/../partials/topnav.php'; ?>
<div class="flex">
<?php include __DIR__ . '/../partials/sidenav.php'; ?>
<main class="flex-1 ml-64 pt-16 h-screen overflow-y-auto">
<div class="p-6 space-y-6 max-w-screen-2xl mx-auto">

<!-- PAGE HEADER -->
<div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-4">
        <div class="relative flex-shrink-0 h-10 w-10">
            <img src="<?= BASE_APP ?>assets/img/logo_navigasi.png" alt="Logo"
                 class="h-10 w-10 object-contain rounded-full border border-[#FECB00]/30 absolute inset-0"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div style="display:none"
                 class="absolute inset-0 h-10 w-10 rounded-full bg-[#001E40] border border-[#FECB00]/40
                        items-center justify-center text-[#FECB00] font-black text-[11px] tracking-wider select-none">VTS</div>
        </div>
        <div>
            <p class="text-[8.5px] font-semibold uppercase tracking-[.12em] text-[#001E40]/55 leading-tight -mb-0.5">DISTRIK NAVIGASI TIPE A KELAS I PALEMBANG</p>
            <h1 class="text-lg font-black uppercase tracking-widest text-[#001E40]">Dashboard Operasional</h1>
            <p class="text-[10px] text-slate-400 tracking-widest">
                Shift <?= htmlspecialchars($shiftCat) ?> &nbsp;&bull;&nbsp;
                Tim <?= htmlspecialchars((string)($user['team'] ?? '-')) ?> &nbsp;&bull;&nbsp;
                <?= date('d M Y') ?> &nbsp;&bull;&nbsp;
                Sisa: <span id="shiftCountdown" class="font-bold text-emerald-400">--:--:--</span>
            </p>
        </div>
    </div>
    <div class="flex gap-2 flex-wrap items-center">
        <?php if ($isFinalized): ?>
        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest bg-[#001E40] text-[#FECB00] border border-[#FECB00] rounded-full">FINAL COPY</span>
        <?php elseif ($isLocked): ?>
        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest bg-amber-900/60 text-[#FECB00] rounded-full">LOCKED</span>
        <?php endif; ?>
        <?php if ($editAccessUntil): ?>
        <span class="px-3 py-1 text-[10px] font-bold uppercase bg-emerald-900/50 text-emerald-300 border border-emerald-500/30 rounded-full">EDIT s/d <?= date('H:i', strtotime($editAccessUntil)) ?></span>
        <?php endif; ?>
        <?php if (!$isLocked && !$isFinalized && $dailyShiftReportId > 0): ?>
        <form method="post" action="<?= BASE_APP ?>actions/operator/lock_shift_report.php" style="display:inline">
            <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <button type="submit" class="machined-button text-[9px] px-3 py-1.5 flex items-center gap-1"
                    id="btnKunciLaporan"
                    data-tippy-content="Sekali dikunci, data tidak bisa diubah tanpa izin Supervisor. Pastikan semua entri sudah benar sebelum mengunci."
                    onclick="return confirm('Yakin ingin mengunci laporan ini? Data tidak dapat diubah setelah dikunci tanpa izin Supervisor.')">
                <span class="material-symbols-outlined" style="font-size:12px">lock</span>
                Kunci Laporan
            </button>
        </form>
        <?php endif; ?>
        <?php if ($isLocked && !$isFinalized && !$editAccessUntil): ?>
        <button type="button" id="btnMintaEdit" onclick="openEditReasonModal()"
           data-tippy-content="Hanya gunakan jika terdapat kesalahan input data yang krusial. Izin berlaku selama 15 menit (demo: langsung disetujui)."
           class="text-[9px] font-semibold px-3 py-1.5 rounded border border-amber-500/40 text-amber-800 hover:bg-amber-100/70 transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined" style="font-size:12px">edit_note</span>
            Minta Izin Edit
        </button>
        <?php endif; ?>
        <?php if ($isLocked): ?>
        <a href="<?= BASE_URL ?>export_pdf.php" target="_blank"
           data-tippy-content="Buka laporan shift ini sebagai PDF. Pastikan laporan sudah dikunci."
           class="text-[9px] font-black px-4 py-1.5 rounded flex items-center gap-1.5 shadow shadow-amber-500/30
                  bg-[#FECB00] text-[#001E40] hover:brightness-110 active:scale-95 transition-all">
            <span class="material-symbols-outlined" style="font-size:13px;font-variation-settings:'FILL' 1">picture_as_pdf</span>
            Cetak PDF
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- ANALYTICS CHARTS -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Bar: Vessel Traffic -->
    <div class="glass-panel rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Traffic Analytics</p>
                <h2 class="text-sm font-bold text-white">Lalu Lintas Kapal — Shift Ini</h2>
            </div>
            <span class="material-symbols-outlined text-[#AEC7F6] text-2xl">directions_boat</span>
        </div>
        <div class="relative h-40">
            <div id="skelTraffic" class="skeleton absolute inset-0 z-10"></div>
            <canvas id="chartTraffic"></canvas>
        </div>
        <div class="flex gap-6 mt-3">
            <div class="text-center"><p class="text-xl font-black text-emerald-400"><?= $trafficCounts['inbound'] ?></p><p class="text-[9px] text-slate-500 uppercase tracking-widest">Masuk</p></div>
            <div class="text-center"><p class="text-xl font-black text-blue-400"><?= $trafficCounts['outbound'] ?></p><p class="text-[9px] text-slate-500 uppercase tracking-widest">Keluar</p></div>
            <div class="text-center"><p class="text-xl font-black text-amber-400"><?= $trafficCounts['transit'] ?></p><p class="text-[9px] text-slate-500 uppercase tracking-widest">Transit</p></div>
        </div>
    </div>
    <!-- Line: Wave Trend Ambang Luar -->
    <div class="glass-panel rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Weather Trend</p>
                <h2 class="text-sm font-bold text-white">Gelombang Ambang Luar — 24 Jam</h2>
            </div>
            <span class="material-symbols-outlined text-[#AEC7F6] text-2xl">waves</span>
        </div>
        <div class="relative h-40">
            <div id="skelWave" class="skeleton absolute inset-0 z-10"></div>
            <canvas id="chartWave"></canvas>
        </div>
    </div>
</div>

<!-- TABS NAV -->
<div class="flex gap-2 flex-wrap">
    <button class="tab-btn <?= $activeTab === 'a1' ? 'active' : '' ?>" data-tab="a1">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">article</span>A-1 VTS Log
    </button>
    <button class="tab-btn <?= $activeTab === 'a2' ? 'active' : '' ?>" data-tab="a2">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">directions_boat</span>A-2 Traffic
    </button>
    <button class="tab-btn <?= $activeTab === 'a3' ? 'active' : '' ?>" data-tab="a3">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">cloud</span>A-3 Cuaca
    </button>
    <button class="tab-btn <?= $activeTab === 'a4' ? 'active' : '' ?>" data-tab="a4">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">water</span>A-4 Pasang
    </button>
    <button class="tab-btn <?= $activeTab === 'a5' ? 'active' : '' ?>" data-tab="a5">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">anchor</span>A-5 Pra-Tiba
    </button>
    <?php if ($canWriteForms): ?>
    <button class="tab-btn <?= $activeTab === 'a6' ? 'active' : '' ?>" data-tab="a6" id="tabBtnA6">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">emergency</span>A-6 Insiden
        <?php if ($a6BadgeCount > 0): ?>
        <span class="badge-pulse" id="a6Badge"><?= $a6BadgeCount ?></span>
        <?php endif; ?>
    </button>
    <button class="tab-btn <?= $activeTab === 'a7' ? 'active' : '' ?>" data-tab="a7">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">shield</span>A-7 Ops Khusus
    </button>
    <?php endif; ?>
    <button class="tab-btn <?= $activeTab === 'a8' ? 'active' : '' ?>" data-tab="a8">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">gavel</span>A-8 Pelanggaran
    </button>
</div>

<!-- TAB A-1: VTS LOG -->
<div id="tab-a1" class="tab-content <?= $activeTab !== 'a1' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-sm">Form A-1 — VTS Log</h2>
        <?php if ($canWriteForms): ?>
        <div class="flex items-center gap-2">
            <span id="a1-autosave" class="text-[9px] text-slate-400 italic"></span>
            <button type="button" id="addA1Row" class="machined-button">+ Tambah Baris</button>
        </div>
        <?php endif; ?>
    </div>
    <?php if ($canWriteForms): ?>
    <form id="formA1" autocomplete="off">
        <input type="hidden" name="form_type" value="a1">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="overflow-x-auto">
            <table class="dtbl">
                <thead><tr><th>Waktu Log</th><th>Nama Kapal</th><th>Call Sign</th><th>Aktivitas *</th><th>Lokasi</th><th>Catatan</th><th></th></tr></thead>
                <tbody id="a1Rows">
                <?php for ($__r = 0; $__r < 5; $__r++): ?>
                <tr class="a1-row">
                    <td><input class="input-dark" name="log_time[]" type="datetime-local"></td>
                    <td><input class="input-dark" name="vessel_name[]" placeholder="Nama kapal"></td>
                    <td><input class="input-dark" name="call_sign[]" placeholder="e.g. YBZT2"></td>
                    <td><input class="input-dark" name="activity[]" placeholder="Aktivitas (wajib)"></td>
                    <td><input class="input-dark" name="location[]" placeholder="Lokasi/Area"></td>
                    <td><input class="input-dark" name="notes[]" placeholder="Catatan"></td>
                    <td><button type="button" class="remove-a1-row text-red-400 hover:text-red-600 text-xs px-1">✕</button></td>
                </tr>
                <?php endfor; unset($__r); ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <button type="submit" id="btnSaveA1" class="machined-button">Simpan A-1</button>
        </div>
    </form>
    <?php endif; ?>
    <?php if ($logRows): ?>
    <h4 class="mt-6 mb-2 font-bold text-xs text-slate-400 uppercase tracking-widest">Log Tersimpan</h4>
    <div class="overflow-x-auto">
        <table class="dtbl">
            <thead><tr><th>Waktu</th><th>Nama Kapal</th><th>Call Sign</th><th>Aktivitas</th><th>Lokasi</th><th>Catatan</th></tr></thead>
            <tbody>
            <?php foreach ($logRows as $log): ?><tr>
                <td class="font-mono"><?= htmlspecialchars((string)($log['log_time'] ?? date('H:i', strtotime((string)$log['created_at'])))) ?></td>
                <td class="font-semibold"><?= htmlspecialchars((string)($log['vessel_name'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($log['call_sign'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)$log['activity']) ?></td>
                <td><?= htmlspecialchars((string)($log['location'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($log['notes'] ?? '-')) ?></td>
            </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif (!$canWriteForms): ?>
    <p class="text-slate-400 text-xs text-center py-8">Belum ada data VTS Log.</p>
    <?php endif; ?>
</div>
</div>

<!-- TAB A-2: VESSEL TRAFFIC -->
<div id="tab-a2" class="tab-content <?= $activeTab !== 'a2' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-sm">Form A-2  Vessel Traffic</h2>
        <div class="flex gap-2 items-center">
            <span id="autosave-indicator" class="text-[9px] text-slate-500 italic"></span>
            <?php if ($canWriteForms): ?>
            <button type="button" id="addVesselRow" class="machined-button">+ Baris</button>
            <?php endif; ?>
        </div>
    </div>
    <form id="formA2" action="<?= BASE_APP ?>actions/operator/save_all_forms.php" method="post" autocomplete="off">
        <input type="hidden" name="form_type" value="a2">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="overflow-x-auto">
            <table class="dtbl">
                <thead><tr>
                    <th>Nama Kapal</th><th>Call Sign</th><th>Asal</th><th>Tujuan</th>
                    <th>Tipe</th><th>Agen</th><th>Arah</th><th>Keterangan</th>
                    <?php if ($canWriteForms): ?><th></th><?php endif; ?>
                </tr></thead>
                <tbody id="vesselRows">
                <?php if ($canWriteForms): ?>
                <?php for ($__r = 0; $__r < 5; $__r++): ?>
                <tr class="vessel-row">
                    <td><input class="input-dark" name="vessel_name[]" placeholder="Nama kapal"></td>
                    <td><input class="input-dark" name="call_sign[]" placeholder="e.g. YBZT2"></td>
                    <td><input class="input-dark" name="last_port[]" placeholder="Last port"></td>
                    <td><input class="input-dark" name="next_port[]" placeholder="Next port"></td>
                    <td><input class="input-dark" name="vessel_type[]" placeholder="Tipe"></td>
                    <td><input class="input-dark" name="agent[]" placeholder="Agen"></td>
                    <td><select class="input-dark" name="direction[]">
                        <option value="inbound">Masuk</option>
                        <option value="outbound">Keluar</option>
                        <option value="transit">Transit</option>
                    </select></td>
                    <td><input class="input-dark" name="remarks[]" placeholder="Catatan"></td>
                    <td><button type="button" class="remove-row text-red-400 hover:text-red-600 text-xs px-1">✕</button></td>
                </tr>
                <?php endfor; unset($__r); ?>
                <?php else: ?>
                <tr><td colspan="9" class="text-center text-slate-400 py-4 text-xs">Laporan dikunci — input tidak tersedia.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($vesselRows): ?>
        <h4 class="mt-6 mb-2 font-bold text-xs text-slate-400 uppercase tracking-widest">Data A-2 Tersimpan</h4>
        <div class="overflow-x-auto">
            <table class="dtbl">
                <thead><tr><th>Nama</th><th>Call Sign</th><th>Asal</th><th>Tujuan</th><th>Tipe</th><th>Agen</th><th>Arah</th><th>Waktu</th></tr></thead>
                <tbody>
                <?php foreach ($vesselRows as $vr): ?>
                <?php
                    $d   = (string)($vr['direction'] ?? '');
                    $dc  = ['inbound' => 'text-emerald-400', 'outbound' => 'text-blue-400', 'transit' => 'text-amber-400'];
                    $dlb = ['inbound' => 'Masuk', 'outbound' => 'Keluar', 'transit' => 'Transit'];
                ?>
                <tr>
                    <td class="font-semibold"><?= htmlspecialchars((string)$vr['vessel_name']) ?></td>
                    <td><?= htmlspecialchars((string)($vr['call_sign'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string)($vr['last_port'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string)($vr['next_port'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string)($vr['vessel_type'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string)($vr['agent'] ?? '-')) ?></td>
                    <td><span class="font-bold <?= $dc[$d] ?? '' ?>"><?= htmlspecialchars($dlb[$d] ?? $d) ?></span></td>
                    <td class="text-slate-400"><?= htmlspecialchars(date('d/m H:i', strtotime($vr['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php if ($canWriteForms): ?>
        <div class="mt-4"><button type="submit" class="machined-button">Simpan A-2</button></div>
        <?php endif; ?>
    </form>
</div>
</div>

<!-- TAB A-3: WEATHER GRID (11 AREAS) -->
<div id="tab-a3" class="tab-content <?= $activeTab !== 'a3' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex justify-between items-center mb-5">
        <h2 class="font-bold text-sm">Form A-3  Grid Cuaca Maritim (11 Wilayah)</h2>
    </div>
    <form id="formA3" method="post" action="<?= BASE_APP ?>actions/operator/save_all_forms.php">
        <input type="hidden" name="form_type" value="a3">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        <?php foreach ($areas as $i => $areaName): ?>
        <?php
            $w   = $weatherByArea[$areaName] ?? [];
            $cd  = (string)($w['weather_condition'] ?? 'Cerah');
            [$icn, $icnClr, $icnPls] = weatherIconConfig($cd);
            $plsClass = $icnPls ? 'animate-pulse' : '';
        ?>
        <div class="weather-card">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined wd-icon <?= $icnClr ?> <?= $plsClass ?>" id="icon-<?= $i ?>"><?= $icn ?></span>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-wider text-[#FECB00] truncate"><?= htmlspecialchars($areaName) ?></p>
                    <p class="text-[9px] text-slate-400"><?= htmlspecialchars($cd) ?></p>
                </div>
            </div>
            <input type="hidden" name="area_name[<?= $i ?>]" value="<?= htmlspecialchars($areaName) ?>">
            <div class="space-y-1.5">
                <select name="weather_condition[<?= $i ?>]" class="input-dark text-[11px]" id="wcsel-<?= $i ?>" <?= $canWriteForms ? '' : 'disabled' ?>>
                    <?php foreach ($weatherOptions as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>" <?= $cd === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="grid grid-cols-2 gap-1">
                    <input name="wind_direction[<?= $i ?>]" class="input-dark text-[11px]" placeholder="Arah angin" value="<?= htmlspecialchars((string)($w['wind_direction'] ?? '')) ?>" <?= $canWriteForms ? '' : 'disabled' ?>>
                    <input name="wind_speed_knots[<?= $i ?>]" type="number" step="0.1" min="0" class="input-dark text-[11px]" placeholder="Kts" value="<?= htmlspecialchars((string)($w['wind_speed_knots'] ?? '0')) ?>" <?= $canWriteForms ? '' : 'disabled' ?>>
                </div>
                <input name="wave_height_m[<?= $i ?>]" type="number" step="0.01" min="0" class="input-dark text-[11px]" placeholder="Tinggi gelombang (m)" value="<?= htmlspecialchars((string)($w['wave_height_m'] ?? '0.00')) ?>" <?= $canWriteForms ? '' : 'disabled' ?>>
                <select name="wave_category[<?= $i ?>]" class="input-dark text-[11px]" <?= $canWriteForms ? '' : 'disabled' ?>>
                    <?php $wc = (string)($w['wave_category'] ?? 'Tenang');
                    foreach ($waveCategories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $wc === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <?php if ($canWriteForms): ?>
        <div class="mt-5"><button type="submit" class="machined-button">Simpan A-3</button></div>
        <?php endif; ?>
    </form>
</div>
</div>

<!-- TAB A-4: PASANG SURUT & SERAH TERIMA -->
<div id="tab-a4" class="tab-content <?= $activeTab !== 'a4' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-bold text-sm text-white">Form A-4 — Pasang Surut &amp; Serah Terima Shift</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Data tidal harian dan kondisi peralatan saat serah terima</p>
        </div>
        <?php if ($tideReport || $handoverReport): ?>
        <span class="px-2 py-0.5 text-[9px] font-black rounded-full bg-emerald-900/50 text-emerald-300 border border-emerald-500/30">DATA TERSIMPAN</span>
        <?php endif; ?>
    </div>
    <form id="formA4" method="post" action="<?= BASE_APP ?>actions/operator/save_all_forms.php" autocomplete="off">
        <input type="hidden" name="form_type" value="a4">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <!-- TIDE DATA -->
        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#FECB00] mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">water</span> Data Pasang Surut
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="modal-label">Air Tertinggi (m) <span class="text-red-400">*</span></label>
                <input name="highest_tide_value_m" type="number" step="0.01" min="0" max="20"
                       class="input-dark" placeholder="e.g. 3.40"
                       value="<?= htmlspecialchars((string)($tideReport['highest_tide_value_m'] ?? '')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">Waktu Air Tertinggi <span class="text-red-400">*</span></label>
                <input name="highest_tide_time" type="time" class="input-dark"
                       value="<?= htmlspecialchars(substr((string)($tideReport['highest_tide_time'] ?? ''), 0, 5)) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">Air Terendah (m) <span class="text-red-400">*</span></label>
                <input name="lowest_tide_value_m" type="number" step="0.01" min="0" max="20"
                       class="input-dark" placeholder="e.g. 0.20"
                       value="<?= htmlspecialchars((string)($tideReport['lowest_tide_value_m'] ?? '')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">Waktu Air Terendah <span class="text-red-400">*</span></label>
                <input name="lowest_tide_time" type="time" class="input-dark"
                       value="<?= htmlspecialchars(substr((string)($tideReport['lowest_tide_time'] ?? ''), 0, 5)) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">TMA Saat Ini (m) <span class="text-red-400">*</span></label>
                <input name="current_water_level_m" type="number" step="0.01" min="0" max="20"
                       class="input-dark" placeholder="Tinggi Muka Air"
                       value="<?= htmlspecialchars((string)($tideReport['current_water_level_m'] ?? '')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">Peringatan Pasang Surut</label>
                <input name="tide_warnings" class="input-dark" placeholder="e.g. Waspada pasang ekstrem pukul 14:00"
                       value="<?= htmlspecialchars((string)($tideReport['warnings'] ?? '')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
        </div>

        <!-- HANDOVER DATA -->
        <h3 class="text-[10px] font-black uppercase tracking-widest text-[#FECB00] mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">swap_horiz</span> Serah Terima Shift
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="modal-label">Kapal Masuk</label>
                <input name="ships_in_count" type="number" min="0" class="input-dark" placeholder="0"
                       value="<?= htmlspecialchars((string)($handoverReport['ships_in_count'] ?? '0')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">Kapal Keluar</label>
                <input name="ships_out_count" type="number" min="0" class="input-dark" placeholder="0"
                       value="<?= htmlspecialchars((string)($handoverReport['ships_out_count'] ?? '0')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">Kapal Transit</label>
                <input name="ships_transit_count" type="number" min="0" class="input-dark" placeholder="0"
                       value="<?= htmlspecialchars((string)($handoverReport['ships_transit_count'] ?? '0')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
            <div>
                <label class="modal-label">Kapal Berlabuh</label>
                <input name="ships_anchor_count" type="number" min="0" class="input-dark" placeholder="0"
                       value="<?= htmlspecialchars((string)($handoverReport['ships_anchor_count'] ?? '0')) ?>"
                       <?= $canWriteForms ? '' : 'readonly' ?>>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="modal-label">Status Peralatan VTS</label>
                <textarea name="equipment_status" class="input-dark" rows="3"
                          placeholder="Radar, AIS, VHF — normal/rusak/maintenance..."
                          <?= $canWriteForms ? '' : 'readonly' ?>><?= htmlspecialchars((string)($handoverReport['equipment_status'] ?? '')) ?></textarea>
            </div>
            <div>
                <label class="modal-label">NTM / Pengumuman Pelaut</label>
                <textarea name="ntm_notes" class="input-dark" rows="3"
                          placeholder="Notice to Mariners aktif di wilayah ini..."
                          <?= $canWriteForms ? '' : 'readonly' ?>><?= htmlspecialchars((string)($handoverReport['ntm_notes'] ?? '')) ?></textarea>
            </div>
            <div>
                <label class="modal-label">Ringkasan Serah Terima</label>
                <textarea name="summary_notes" class="input-dark" rows="3"
                          placeholder="Hal penting untuk shift berikutnya..."
                          <?= $canWriteForms ? '' : 'readonly' ?>><?= htmlspecialchars((string)($handoverReport['summary_notes'] ?? '')) ?></textarea>
            </div>
        </div>
        <?php if ($canWriteForms): ?>
        <div class="mt-2">
            <button type="submit" class="machined-button">
                <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">save</span>
                Simpan A-4
            </button>
        </div>
        <?php else: ?>
        <p class="text-xs text-slate-400 italic mt-2">Laporan dikunci — data tidak dapat diubah.</p>
        <?php endif; ?>
    </form>
</div>
</div>

<!-- TAB A-5: PRE-ARRIVAL -->
<div id="tab-a5" class="tab-content <?= $activeTab !== 'a5' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-bold text-sm text-white">Form A-5 — Laporan Pra-Kedatangan</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Kapal yang diperkirakan tiba dalam 24 jam ke depan</p>
        </div>
        <span class="text-[9px] text-slate-400 font-semibold bg-white/5 px-2 py-0.5 rounded-full"><?= count($preArrivalRows) ?> kapal terdaftar</span>
    </div>

    <?php if ($preArrivalRows): ?>
    <div class="overflow-x-auto mb-5">
        <table class="dtbl">
            <thead><tr><th>Kapal</th><th>IMO</th><th>GT</th><th>LOA (m)</th><th>Draft (m)</th><th>POB</th><th>ETA</th><th>Muatan</th><th>Catatan</th></tr></thead>
            <tbody>
            <?php foreach ($preArrivalRows as $par): ?>
            <tr>
                <td class="font-semibold"><?= htmlspecialchars((string)$par['vessel_name']) ?></td>
                <td><?= htmlspecialchars((string)($par['imo_number'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($par['gross_tonnage'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($par['loa_m'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($par['draft_m'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($par['pob_count'] ?? '-')) ?></td>
                <td class="font-mono text-amber-300"><?= htmlspecialchars((string)$par['expected_arrival']) ?></td>
                <td><?= htmlspecialchars((string)($par['cargo_details'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($par['special_notes'] ?? '-')) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif (!$canWriteForms): ?>
    <p class="text-slate-400 text-xs text-center py-8">Belum ada data pra-kedatangan untuk shift ini.</p>
    <?php endif; ?>

    <?php if ($canWriteForms): ?>
    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#FECB00] mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add_circle</span> Daftarkan Kapal Baru
    </h3>
    <form id="formA5" method="post" action="<?= BASE_APP ?>actions/operator/save_all_forms.php" autocomplete="off">
        <input type="hidden" name="form_type" value="a5">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="modal-label">Nama Kapal <span class="text-red-400">*</span></label>
                <input name="vessel_name" class="input-dark" placeholder="Nama lengkap kapal" required>
            </div>
            <div>
                <label class="modal-label">Nomor IMO</label>
                <input name="imo_number" class="input-dark" placeholder="e.g. IMO9876543">
            </div>
            <div>
                <label class="modal-label">Gross Tonnage (GT)</label>
                <input name="gross_tonnage" type="number" step="0.01" min="0" class="input-dark" placeholder="e.g. 18500.00">
            </div>
            <div>
                <label class="modal-label">LOA (meter)</label>
                <input name="loa_m" type="number" step="0.01" min="0" class="input-dark" placeholder="e.g. 185.00">
            </div>
            <div>
                <label class="modal-label">Draft (meter)</label>
                <input name="draft_m" type="number" step="0.01" min="0" class="input-dark" placeholder="e.g. 9.50">
            </div>
            <div>
                <label class="modal-label">POB (Persons on Board)</label>
                <input name="pob_count" type="number" min="0" class="input-dark" placeholder="e.g. 22">
            </div>
            <div>
                <label class="modal-label">ETA <span class="text-red-400">*</span></label>
                <input name="expected_arrival" type="datetime-local" class="input-dark" required>
            </div>
            <div class="sm:col-span-2">
                <label class="modal-label">Detail Muatan / Kargo</label>
                <input name="cargo_details" class="input-dark" placeholder="e.g. Batubara 15.000 MT, CPO 8.000 MT">
            </div>
            <div class="lg:col-span-3">
                <label class="modal-label">Catatan Khusus</label>
                <textarea name="special_notes" class="input-dark" rows="2"
                          placeholder="Catatan yang perlu diketahui petugas shift..."></textarea>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="machined-button">
                <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">add</span>
                Tambah A-5
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>
</div>

<!-- TAB A-6: LAPORAN INSIDEN -->
<div id="tab-a6" class="tab-content <?= $activeTab !== 'a6' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-bold text-sm text-white">Form A-6 — Laporan Insiden</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Kecelakaan, SAR, Pencemaran Laut, &amp; Insiden lainnya</p>
        </div>
        <?php if ($a6BadgeCount > 0): ?>
        <div class="flex items-center gap-1 px-3 py-1 bg-red-900/50 border border-red-500/40 rounded-full">
            <span class="material-symbols-outlined text-red-400 text-sm animate-pulse">warning</span>
            <span class="text-[10px] font-black text-red-300"><?= $a6BadgeCount ?> INSIDEN BELUM DIAKUI MANAGER</span>
        </div>
        <?php endif; ?>
    </div>
    <?php if ($incidentRows): ?>
    <div class="overflow-x-auto mb-5">
        <h4 class="mb-2 font-bold text-xs text-slate-400 uppercase tracking-widest">Insiden Tersimpan (<?= count($incidentRows) ?>)</h4>
        <table class="dtbl">
            <thead><tr><th>Waktu</th><th>Judul Insiden</th><th>Meninggal</th><th>Hilang</th><th>Lokasi/Koordinat</th></tr></thead>
            <tbody>
            <?php foreach ($incidentRows as $inc): ?><tr>
                <td class="font-mono text-amber-600"><?= htmlspecialchars(date('d/m H:i', strtotime((string)$inc['incident_datetime']))) ?></td>
                <td class="font-semibold"><?= htmlspecialchars((string)$inc['title']) ?></td>
                <td><?= (int)$inc['deaths_count'] ?></td>
                <td><?= (int)$inc['missing_count'] ?></td>
                <td><?= htmlspecialchars((string)($inc['pollution_location'] ?? '-')) ?></td>
            </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <?php if ($canWriteForms): ?>
    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#FECB00] mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add_circle</span> Catat Insiden Baru
    </h3>
    <?php endif; ?>
    <form id="formA6" method="post" action="<?= BASE_APP ?>actions/operator/save_all_forms.php" novalidate>
        <input type="hidden" name="form_type" value="a6">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="modal-label">Judul Insiden <span class="text-red-400">*</span></label>
                <input name="title" class="input-dark" placeholder="Contoh: Kapal Tenggelam di Selat Bangka Selatan" required>
            </div>
            <div>
                <label class="modal-label">Waktu Kejadian <span class="text-red-400">*</span></label>
                <input name="incident_datetime" type="datetime-local" class="input-dark" required>
            </div>
            <div>
                <label class="modal-label">Lokasi Pencemaran / Koordinat <span class="text-red-400 a6-required-mark"> *</span>
                    <span class="material-symbols-outlined align-middle text-slate-400 cursor-help ml-0.5"
                          style="font-size:12px"
                          data-tippy-content="Gunakan format koordinat derajat-menit (DD°MM&apos;S BBBB°MM&apos;E) atau nama area perairan yang spesifik.">help_outline</span>
                </label>
                <input name="pollution_location" id="a6CoordField" class="input-dark" placeholder="Contoh: 02°15'S 105°10'E">
            </div>
            <div>
                <label class="modal-label">Jumlah Korban Meninggal <span class="text-red-400 a6-required-mark"> *</span></label>
                <input name="deaths_count" id="a6DeathsField" type="number" min="0" value="0" class="input-dark">
            </div>
            <div>
                <label class="modal-label">Jumlah Korban Hilang</label>
                <input name="missing_count" type="number" min="0" value="0" class="input-dark">
            </div>
            <div class="md:col-span-2">
                <label class="modal-label">Kronologi Kejadian <span class="text-red-400">*</span></label>
                <textarea name="chronology" class="input-dark" rows="4" placeholder="Uraikan kronologi secara sistematis..." required></textarea>
            </div>
            <div>
                <label class="modal-label">Instansi yang Dihubungi</label>
                <div class="space-y-1">
                    <?php foreach (['BASARNAS','Syahbandar','TNI AL','Polisi Air','Pemadam Kebakaran','Rumah Sakit'] as $auth): ?>
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="authorities_notified[]" value="<?= htmlspecialchars($auth) ?>" class="rounded accent-amber-400">
                        <?= htmlspecialchars($auth) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <label class="modal-label">Tindakan Segera</label>
                <textarea name="immediate_action" class="input-dark" rows="5" placeholder="Tindakan yang sudah diambil..."></textarea>
            </div>
        </div>
        <div class="mt-5 flex gap-3">
            <button type="submit" class="machined-button">
                <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">send</span>
                Kirim A-6 Insiden
            </button>
        </div>
    </form>
</div>
</div>

<!-- TAB A-7: OPERASI KHUSUS -->
<div id="tab-a7" class="tab-content <?= $activeTab !== 'a7' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-bold text-sm text-white">Form A-7 — Operasi Khusus</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">VVIP, Latihan Militer, Survei Hidrografi, dan Ops Khusus lainnya</p>
        </div>
    </div>
    <?php if ($specialOpsRows): ?>
    <div class="overflow-x-auto mb-5">
        <h4 class="mb-2 font-bold text-xs text-slate-400 uppercase tracking-widest">Operasi Tersimpan (<?= count($specialOpsRows) ?>)</h4>
        <table class="dtbl">
            <thead><tr><th>Nama Operasi</th><th>Waktu Mulai</th><th>Waktu Selesai</th><th>Lokasi</th><th>Catatan</th></tr></thead>
            <tbody>
            <?php foreach ($specialOpsRows as $so): ?><tr>
                <td class="font-semibold"><?= htmlspecialchars((string)$so['operation_name']) ?></td>
                <td class="font-mono text-blue-600"><?= htmlspecialchars(date('d/m H:i', strtotime((string)$so['operation_start']))) ?></td>
                <td class="font-mono"><?= $so['operation_end'] ? htmlspecialchars(date('d/m H:i', strtotime((string)$so['operation_end']))) : '-' ?></td>
                <td><?= htmlspecialchars((string)($so['location'] ?? '-')) ?></td>
                <td><?= htmlspecialchars(mb_strimwidth((string)($so['outcome_notes'] ?? '-'), 0, 60, '…')) ?></td>
            </tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <?php if ($canWriteForms): ?>
    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#FECB00] mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add_circle</span> Tambah Operasi Baru
    </h3>
    <?php endif; ?>
    <form id="formA7" method="post" action="<?= BASE_APP ?>actions/operator/save_all_forms.php">
        <input type="hidden" name="form_type" value="a7">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="modal-label">Nama Operasi <span class="text-red-400">*</span></label>
                <input name="operation_name" class="input-dark" placeholder="Contoh: Latmil TNI AL Zona Bangka Selatan" required>
            </div>
            <div>
                <label class="modal-label">Waktu Mulai <span class="text-red-400">*</span></label>
                <input name="operation_start" type="datetime-local" class="input-dark" required>
            </div>
            <div>
                <label class="modal-label">Waktu Selesai (opsional)</label>
                <input name="operation_end" type="datetime-local" class="input-dark">
            </div>
            <div>
                <label class="modal-label">Lokasi Operasi</label>
                <input name="location" class="input-dark" placeholder="Contoh: Selat Bangka Selatan">
            </div>
            <div>
                <label class="modal-label">Catatan Hasil</label>
                <textarea name="outcome_notes" class="input-dark" rows="3" placeholder="Ringkasan hasil operasi..."></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="modal-label">Uraian Kejadian &amp; Detail Operasi <span class="text-amber-400">*</span></label>
                <textarea name="operation_details" class="input-dark" rows="5"
                          placeholder="Uraikan secara lengkap situasi, kejadian, dan detail operasi yang berlangsung..." required></textarea>
            </div>
        </div>
        <div class="mt-5">
            <button type="submit" class="machined-button">
                <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">send</span>
                Kirim A-7 Operasi Khusus
            </button>
        </div>
    </form>
</div>
</div>

<!-- TAB A-8: PELANGGARAN BERLAYAR -->
<div id="tab-a8" class="tab-content <?= $activeTab !== 'a8' ? 'hidden' : '' ?>">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-bold text-sm text-white">Form A-8 — Pelanggaran Berlayar</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Kapal yang melanggar aturan navigasi, SOLAS, atau COLREG</p>
        </div>
        <span class="text-[9px] text-slate-400 font-semibold bg-white/5 px-2 py-0.5 rounded-full"><?= count($contraventionRows) ?> pelanggaran tercatat</span>
    </div>

    <?php if ($contraventionRows): ?>
    <div class="overflow-x-auto mb-5">
        <table class="dtbl">
            <thead><tr><th>Kapal</th><th>Pelanggaran</th><th>Waktu</th><th>Lokasi</th><th>Peringatan</th><th>Rujukan Hukum</th><th>Tindakan</th></tr></thead>
            <tbody>
            <?php foreach ($contraventionRows as $cr): ?>
            <tr>
                <td class="font-semibold"><?= htmlspecialchars((string)$cr['vessel_name']) ?></td>
                <td><?= htmlspecialchars((string)$cr['violation_type']) ?></td>
                <td class="font-mono text-red-300"><?= htmlspecialchars(date('d/m H:i', strtotime((string)$cr['violation_datetime']))) ?></td>
                <td><?= htmlspecialchars((string)($cr['location'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($cr['warning_type'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string)($cr['legal_reference'] ?? '-')) ?></td>
                <td><?= htmlspecialchars(mb_strimwidth((string)($cr['action_taken'] ?? '-'), 0, 60, '…')) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif (!$canWriteForms): ?>
    <p class="text-slate-400 text-xs text-center py-8">Belum ada data pelanggaran untuk shift ini.</p>
    <?php endif; ?>

    <?php if ($canWriteForms): ?>
    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#FECB00] mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add_circle</span> Catat Pelanggaran Baru
    </h3>
    <form id="formA8" method="post" action="<?= BASE_APP ?>actions/operator/save_all_forms.php" autocomplete="off">
        <input type="hidden" name="form_type" value="a8">
        <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="modal-label">Nama Kapal <span class="text-red-400">*</span></label>
                <input name="vessel_name" class="input-dark" placeholder="Nama kapal pelanggar" required>
            </div>
            <div>
                <label class="modal-label">Jenis Pelanggaran <span class="text-red-400">*</span></label>
                <select name="violation_type" class="input-dark" required>
                    <option value="">— Pilih Jenis —</option>
                    <option value="Tidak Merespons Panggilan Radio VHF">Tidak Merespons Panggilan Radio VHF</option>
                    <option value="Pelanggaran Jalur Pelayaran (TSS)">Pelanggaran Jalur Pelayaran (TSS)</option>
                    <option value="Berlayar Tanpa Izin di Area Terlarang">Berlayar Tanpa Izin di Area Terlarang</option>
                    <option value="Tidak Menyalakan Lampu Navigasi">Tidak Menyalakan Lampu Navigasi</option>
                    <option value="AIS Dimatikan atau Tidak Berfungsi">AIS Dimatikan atau Tidak Berfungsi</option>
                    <option value="Pelanggaran COLREG / Aturan Balas-Berbalas">Pelanggaran COLREG / Aturan Balas-Berbalas</option>
                    <option value="Pencemaran Minyak / BBM">Pencemaran Minyak / BBM</option>
                    <option value="Pembuangan Sampah di Laut">Pembuangan Sampah di Laut</option>
                    <option value="Kecepatan Berlebih di Kawasan Pelabuhan">Kecepatan Berlebih di Kawasan Pelabuhan</option>
                    <option value="Pelanggaran Lainnya">Pelanggaran Lainnya</option>
                </select>
            </div>
            <div>
                <label class="modal-label">Waktu Pelanggaran <span class="text-red-400">*</span></label>
                <input name="violation_datetime" type="datetime-local" class="input-dark" required>
            </div>
            <div>
                <label class="modal-label">Lokasi Kejadian</label>
                <input name="location" class="input-dark" placeholder="Nama area atau koordinat">
            </div>
            <div>
                <label class="modal-label">Jenis Peringatan</label>
                <select name="warning_type" class="input-dark">
                    <option value="">— Pilih —</option>
                    <option value="Teguran Lisan (VHF)">Teguran Lisan (VHF)</option>
                    <option value="Surat Peringatan">Surat Peringatan</option>
                    <option value="Laporan ke Syahbandar">Laporan ke Syahbandar</option>
                    <option value="Laporan ke TNI AL / Polisi Air">Laporan ke TNI AL / Polisi Air</option>
                    <option value="Tindakan Administratif">Tindakan Administratif</option>
                </select>
            </div>
            <div>
                <label class="modal-label">Rujukan Hukum</label>
                <input name="legal_reference" class="input-dark" placeholder="e.g. PM 129/2016 Pasal 5 Ayat 2">
            </div>
            <div class="lg:col-span-3">
                <label class="modal-label">Tindakan yang Diambil</label>
                <textarea name="action_taken" class="input-dark" rows="3"
                          placeholder="Uraikan tindakan yang sudah diambil oleh operator VTS..."></textarea>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="machined-button">
                <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">gavel</span>
                Catat A-8 Pelanggaran
            </button>
        </div>
    </form>
    <?php elseif (!$contraventionRows): ?>
    <p class="text-slate-400 text-xs text-center py-8">Belum ada data pelanggaran untuk shift ini.</p>
    <?php endif; ?>
</div>
</div>

</div><!-- /container -->
</main>
</div><!-- /flex -->

<!-- ═══════════════════════════════════════════════════════════
     INCIDENT CHOOSER OVERLAY — opened by sidenav & New Incident btn
     ═══════════════════════════════════════════════════════════ -->
<div id="incidentOverlay"
     class="fixed inset-0 z-[600] hidden items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
     role="dialog" aria-modal="true" aria-label="Pilih Jenis Laporan Insiden">
    <div class="bg-[#0f1523] border border-white/10 rounded-2xl p-7 w-full max-w-md shadow-2xl" id="incidentOverlayBox">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-black text-white tracking-widest uppercase flex items-center gap-2">
                <span class="material-symbols-outlined text-[#FECB00]">add_circle</span>
                Buat Laporan Baru
            </h3>
            <button onclick="closeIncidentChooser()"
                    class="text-slate-400 hover:text-white transition-colors p-1 rounded hover:bg-white/5" aria-label="Tutup">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        <p class="text-[11px] text-slate-400 mb-5 tracking-wide">Pilih jenis laporan yang ingin dibuat — formulir akan terbuka otomatis:</p>
        <div class="grid grid-cols-3 gap-3">
            <button onclick="chooseIncident('a6')"
                    class="flex flex-col items-center gap-3 p-5 rounded-xl border border-red-500/30 bg-red-900/20
                           hover:bg-red-900/40 hover:border-red-400/50 transition-all text-center group">
                <span class="material-symbols-outlined text-3xl text-red-400 group-hover:text-red-300
                             transition-colors" style="font-variation-settings:'FILL' 1">emergency</span>
                <div>
                    <p class="text-sm font-black text-white">A-6 Insiden</p>
                    <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">Kecelakaan · SAR<br>Pencemaran Laut</p>
                </div>
            </button>
            <button onclick="chooseIncident('a7')"
                    class="flex flex-col items-center gap-3 p-5 rounded-xl border border-amber-500/30 bg-amber-900/20
                           hover:bg-amber-900/40 hover:border-amber-400/50 transition-all text-center group">
                <span class="material-symbols-outlined text-3xl text-amber-400 group-hover:text-amber-300
                             transition-colors" style="font-variation-settings:'FILL' 1">shield</span>
                <div>
                    <p class="text-sm font-black text-white">A-7 Ops Khusus</p>
                    <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">VVIP · Latihan Militer<br>Survei Hidrografi</p>
                </div>
            </button>
            <button onclick="chooseIncident('a8')"
                    class="flex flex-col items-center gap-3 p-5 rounded-xl border border-purple-500/30 bg-purple-900/20
                           hover:bg-purple-900/40 hover:border-purple-400/50 transition-all text-center group">
                <span class="material-symbols-outlined text-3xl text-purple-400 group-hover:text-purple-300
                             transition-colors" style="font-variation-settings:'FILL' 1">gavel</span>
                <div>
                    <p class="text-sm font-black text-white">A-8 Pelanggaran</p>
                    <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">COLREG · TSS<br>Pencemaran</p>
                </div>
            </button>
        </div>
    </div>
</div>

<div id="toast-host"></div>

<script>
var baseUrl = <?= $jsBaseUrl ?>;
// TOAST
function showToast(msg, type, dur) {
    var host = document.getElementById('toast-host');
    var el = document.createElement('div');
    el.className = 'toast-item ' + (type || 'info');
    el.textContent = msg;
    host.appendChild(el);
    requestAnimationFrame(function(){ requestAnimationFrame(function(){ el.classList.add('show'); }); });
    setTimeout(function(){
        el.classList.remove('show');
        setTimeout(function(){ el.remove(); }, 350);
    }, dur || 3500);
}
// Flash
(function(){
    var s = <?= json_encode($flashSuccess) ?>, e = <?= json_encode($flashError) ?>;
    if (s) showToast(s, 'success');
    if (e) showToast(e, 'error');
})();

// SHIFT COUNTDOWN
(function(){
    var el  = document.getElementById('shiftCountdown');
    var end = new Date(<?= $jsShiftEnd ?>);
    if (!el || isNaN(end.getTime())) return;
    var rid   = <?= $jsReportId ?>;
    var wKey  = 'shft_warn_' + rid;
    var warned = sessionStorage.getItem(wKey) === '1';
    function tick(){
        var left = Math.max(0, Math.floor((end - Date.now()) / 1000));
        var h = Math.floor(left/3600), m = Math.floor((left%3600)/60), s = left%60;
        el.textContent = [h,m,s].map(function(n){ return String(n).padStart(2,'0'); }).join(':');
        el.className = '';
        var leftMin = Math.floor(left/60);
        if (leftMin < 5)       el.className = 'font-bold countdown-danger';
        else if (leftMin < 30) el.className = 'font-bold countdown-warning';
        else                   el.className = 'font-bold text-emerald-400';
        if (leftMin < 10 && leftMin > 0 && !warned) {
            warned = true;
            sessionStorage.setItem(wKey, '1');
            showToast('Waktu Shift Segera Berakhir! Harap Kunci Laporan!', 'warning', 7000);
        }
    }
    tick();
    setInterval(tick, 1000);
})();

// TABS — with smooth fade-in on switch
document.querySelectorAll('.tab-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
        document.querySelectorAll('.tab-content').forEach(function(c){ c.classList.add('hidden'); });
        this.classList.add('active');
        var t = document.getElementById('tab-' + this.dataset.tab);
        if (t) {
            t.classList.remove('hidden');
            // re-trigger fade-in animation
            t.style.animation = 'none';
            void t.offsetHeight;
            t.style.animation = '';
        }
        // Auto-fill datetime-local fields with current time when switching to a form tab
        var nowLocal = (function(){
            var d = new Date(); d.setSeconds(0, 0);
            return d.toISOString().slice(0, 16);
        })();
        var dtFields = {
            'a5': ['expected_arrival'],
            'a6': ['incident_datetime'],
            'a7': ['operation_start'],
            'a8': ['violation_datetime']
        };
        var tab = this.dataset.tab;
        if (dtFields[tab]) {
            dtFields[tab].forEach(function(fname) {
                var panel = document.getElementById('tab-' + tab);
                if (!panel) return;
                var el = panel.querySelector('[name="' + fname + '"]');
                if (el && !el.value) { el.value = nowLocal; }
            });
        }
    });
});

// INCIDENT CHOOSER
function openIncidentChooser() {
    var el = document.getElementById('incidentOverlay');
    if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
}
function closeIncidentChooser() {
    var el = document.getElementById('incidentOverlay');
    if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
}
function chooseIncident(tab) {
    closeIncidentChooser();
    document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
    document.querySelectorAll('.tab-content').forEach(function(c){ c.classList.add('hidden'); });
    var btn   = document.querySelector('.tab-btn[data-tab="' + tab + '"]');
    var panel = document.getElementById('tab-' + tab);
    if (btn)   btn.classList.add('active');
    if (panel) {
        panel.classList.remove('hidden');
        panel.style.animation = 'none'; void panel.offsetHeight; panel.style.animation = '';
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
(function(){
    var overlay = document.getElementById('incidentOverlay');
    if (!overlay) return;
    // Close on backdrop click
    overlay.addEventListener('click', function(e){ if (e.target === overlay) closeIncidentChooser(); });
    // Close on Escape
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeIncidentChooser(); });
})();

// A-2 ROW ADD / REMOVE
(function(){
    var addBtn = document.getElementById('addVesselRow');
    var tbody  = document.getElementById('vesselRows');
    if (!addBtn || !tbody) return;
    addBtn.addEventListener('click', function(){
        var tpl = tbody.querySelector('tr.vessel-row');
        if (!tpl) return;
        var cl = tpl.cloneNode(true);
        cl.querySelectorAll('input').forEach(function(i){ i.value = ''; });
        tbody.appendChild(cl);
    });
    tbody.addEventListener('click', function(e){
        if (!e.target.classList.contains('remove-row')) return;
        var rows = tbody.querySelectorAll('tr.vessel-row');
        if (rows.length <= 1) return;
        e.target.closest('tr').remove();
    });
})();

// AUTO-SAVE DRAFT EVERY 5 MIN
(function(){
    var rid  = <?= $jsReportId ?>;
    var csrf = <?= $jsCsrf ?>;
    if (rid <= 0) return;
    function collectVessels(){
        var data = [];
        document.querySelectorAll('#vesselRows tr.vessel-row').forEach(function(tr){
            var obj = {};
            tr.querySelectorAll('input,select').forEach(function(inp){
                var nm = (inp.getAttribute('name') || '').replace(/\[\]$/, '');
                if (nm) obj[nm] = inp.value;
            });
            if (Object.values(obj).some(function(v){ return v && v.trim && v.trim() !== ''; })) data.push(obj);
        });
        return data;
    }
    function autoSave(){
        var vessels = collectVessels();
        if (!vessels.length) return;
        var ind = document.getElementById('autosave-indicator');
        if (ind) ind.textContent = 'Menyimpan draft...';
        fetch(baseUrl + 'actions/operator/draft_save.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({daily_shift_report_id: rid, csrf_token: csrf, vessels: vessels})
        }).then(function(r){ return r.json(); }).then(function(res){
            if (ind) ind.textContent = res.success ? 'Draft disimpan ' + new Date().toLocaleTimeString('id-ID') : 'Gagal simpan';
        }).catch(function(){ if (ind) ind.textContent = 'Autosave gagal'; });
    }
    setInterval(autoSave, 5 * 60 * 1000);
})();

// CHARTS.JS
(function(){
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = 'rgba(0,30,64,0.08)';
    var ctxT = document.getElementById('chartTraffic');
    if (ctxT) {
        new Chart(ctxT, {
            type: 'bar',
            data: {
                labels: ['Masuk','Keluar','Transit'],
                datasets: [{
                    label: 'Kapal',
                    data: <?= $jsTrafficData ?>,
                    backgroundColor: ['rgba(52,211,153,.7)','rgba(96,165,250,.7)','rgba(251,191,36,.7)'],
                    borderColor: ['#34d399','#60a5fa','#fbbf24'],
                    borderWidth: 1.5, borderRadius: 4
                }]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                plugins:{ legend:{display:false}, tooltip:{callbacks:{label:function(c){ return c.parsed.y+' kapal'; }}} },
                scales:{ y:{beginAtZero:true,ticks:{stepSize:1,precision:0},grid:{color:'rgba(0,30,64,.05)'}}, x:{grid:{display:false}} },
                animation:{ onComplete: function(){ var s=document.getElementById('skelTraffic'); if(s) s.remove(); } }
            }
        });
        // Remove skeleton immediately if data is zero (no animation fires)
        requestAnimationFrame(function(){ setTimeout(function(){ var s=document.getElementById('skelTraffic');if(s)s.remove(); }, 600); });
    }
    var ctxW = document.getElementById('chartWave');
    if (ctxW) {
        new Chart(ctxW, {
            type: 'line',
            data: {
                labels: <?= $jsWaveLabels ?>,
                datasets: [{
                    label: 'Tinggi Gelombang (m)',
                    data: <?= $jsWaveData ?>,
                    borderColor:'#60a5fa', backgroundColor:'rgba(96,165,250,.1)',
                    borderWidth:2, tension:.4, fill:true,
                    pointRadius:3, pointHoverRadius:5, pointBackgroundColor:'#60a5fa'
                }]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                plugins:{ legend:{display:false}, tooltip:{callbacks:{label:function(c){ return c.parsed.y+' m'; }}} },
                scales:{
                    y:{beginAtZero:true, min:0, grid:{color:'rgba(0,30,64,.05)'}, title:{display:true,text:'Meter (m)',font:{size:9}}},
                    x:{grid:{display:false}, ticks:{maxTicksLimit:8}}
                },
                animation:{ onComplete: function(){ var s=document.getElementById('skelWave'); if(s) s.remove(); } }
            }
        });
        requestAnimationFrame(function(){ setTimeout(function(){ var s=document.getElementById('skelWave');if(s)s.remove(); }, 600); });
    }
})();

// WEATHER CARD ICON LIVE UPDATE
(function(){
    function updateIcon(idx, val){
        var ico = 'wb_sunny', clr = 'text-blue-400', pls = false;
        if (/(badai|sangat)/i.test(val))           { ico='thunderstorm'; clr='text-red-500'; pls=true; }
        else if (/lebat/i.test(val))               { ico='thunderstorm'; clr='text-red-400'; pls=true; }
        else if (/(hujan|gerimis)/i.test(val))     { ico='rainy';        clr='text-yellow-400'; }
        else if (/berawan/i.test(val))             { ico='cloud';        clr='text-blue-300'; }
        var el = document.getElementById('icon-' + idx);
        if (!el) return;
        el.textContent = ico;
        el.className = 'material-symbols-outlined wd-icon ' + clr + (pls ? ' animate-pulse' : '');
    }
    document.querySelectorAll('[id^="wcsel-"]').forEach(function(sel){
        sel.addEventListener('change', function(){
            var idx = this.id.replace('wcsel-','');
            updateIcon(idx, this.value);
        });
    });
})();

// A-6 DYNAMIC VALIDATION: require deaths_count + pollution_location
(function(){
    var form = document.getElementById('formA6');
    if (!form) return;
    var deathsField  = document.getElementById('a6DeathsField');
    var coordField   = document.getElementById('a6CoordField');
    form.addEventListener('submit', function(e){
        var deaths = coordField ? coordField.value.trim() : '';
        var coord  = deathsField ? deathsField.value.trim() : '';
        if (coord === '' || coord === '' ) {
            // require both to be present / non-empty for A6
        }
        if (!coordField || coordField.value.trim() === '') {
            e.preventDefault();
            coordField.focus();
            coordField.style.borderColor = '#ef4444';
            showToast('Koordinat/Lokasi wajib diisi untuk laporan A-6!', 'error', 4000);
            setTimeout(function(){ coordField.style.borderColor = ''; }, 3000);
            return;
        }
        // deaths_count is numeric; 0 is acceptable (unknown), so just ensure it's filled
        if (!deathsField || deathsField.value === '') {
            e.preventDefault();
            deathsField.focus();
            deathsField.style.borderColor = '#ef4444';
            showToast('Jumlah korban wajib diisi (masukkan 0 jika tidak ada).', 'error', 4000);
            setTimeout(function(){ deathsField.style.borderColor = ''; }, 3000);
        }
    });
})();

// TIPPY.JS — "Online" status tooltip in topnav
(function(){
    if (typeof tippy === 'undefined') return;
    var onlineEl = document.querySelector('[data-tippy-login]');
    if (onlineEl) {
        tippy(onlineEl, {
            content: onlineEl.dataset.tippyLogin,
            allowHTML: true,
            theme: 'translucent',
            placement: 'bottom-end',
            delay: [100, 0],
            arrow: true,
        });
    }
    // Action button tooltips (Kunci Laporan, Minta Izin Edit, A-6 coordinate help)
    tippy('[data-tippy-content]', {
        theme: 'translucent',
        placement: 'top',
        delay: [80, 0],
        arrow: true,
    });
})();

// SERVICE WORKER REGISTRATION
(function(){
    if (!('serviceWorker' in navigator)) return;
    var swUrl  = <?= json_encode(BASE_APP . 'sw.js') ?>;
    var scope  = <?= json_encode(BASE_APP) ?>;
    navigator.serviceWorker.register(swUrl, { scope: scope })
        .then(function() { /* SW registered */ })
        .catch(function() { /* SW registration failed — no-op in production */ });
})();

// ============================================================
// AJAX FORM HELPERS  — A1 modal + fetch intercepts A2/A3/A6/A7
// ============================================================
function submitForm(formEl, url, btnLabel, tabAnchor) {
    formEl.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = formEl.querySelector('[type=submit]');
        if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan…'; }
        fetch(baseUrl + url, { method: 'POST', body: new FormData(formEl) })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                showToast(res.message || (res.success ? 'Berhasil disimpan.' : 'Terjadi kesalahan.'), res.success ? 'success' : 'error');
                if (res.success) {
                    setTimeout(function() {
                        location.href = location.pathname + (tabAnchor ? '?tab=' + tabAnchor : '');
                    }, 1200);
                } else {
                    if (btn) { btn.disabled = false; btn.textContent = btnLabel; }
                }
            })
            .catch(function() {
                showToast('Koneksi gagal. Coba lagi.', 'error');
                if (btn) { btn.disabled = false; btn.textContent = btnLabel; }
            });
    });
}

// A1 INLINE ROWS + CUSTOM FETCH
(function(){
    var addA1  = document.getElementById('addA1Row');
    var a1Tbdy = document.getElementById('a1Rows');
    var fA1    = document.getElementById('formA1');
    if (addA1 && a1Tbdy) {
        addA1.addEventListener('click', function(){
            var tpl = a1Tbdy.querySelector('tr.a1-row');
            if (!tpl) return;
            var cl = tpl.cloneNode(true);
            cl.querySelectorAll('input').forEach(function(i){ i.value = ''; });
            var nowLocal = (function(){ var d = new Date(); d.setSeconds(0,0); return d.toISOString().slice(0,16); })();
            var ltInput = cl.querySelector('[name="log_time[]"]');
            if (ltInput) ltInput.value = nowLocal;
            a1Tbdy.appendChild(cl);
        });
        a1Tbdy.addEventListener('click', function(e){
            if (!e.target.classList.contains('remove-a1-row')) return;
            var rows = a1Tbdy.querySelectorAll('tr.a1-row');
            if (rows.length <= 1) return;
            e.target.closest('tr').remove();
        });
    }
    if (fA1) {
        fA1.addEventListener('submit', function(e){
            e.preventDefault();
            var btn = document.getElementById('btnSaveA1');
            if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan\u2026'; }
            fetch(baseUrl + 'actions/operator/save_all_forms.php', { method: 'POST', body: new FormData(fA1) })
                .then(function(r){ return r.json(); })
                .then(function(res){
                    showToast(res.message || (res.success ? 'A-1 berhasil disimpan.' : 'Gagal menyimpan.'), res.success ? 'success' : 'error');
                    if (res.success) {
                        setTimeout(function(){ location.href = location.pathname + '?tab=a1'; }, 1200);
                    } else {
                        if (btn) { btn.disabled = false; btn.textContent = 'Simpan A-1'; }
                    }
                })
                .catch(function(){
                    showToast('Koneksi gagal. Coba lagi.', 'error');
                    if (btn) { btn.disabled = false; btn.textContent = 'Simpan A-1'; }
                });
        });
    }

    // A2
    var fA2 = document.getElementById('formA2');
    if (fA2) submitForm(fA2, 'actions/operator/save_all_forms.php', 'Simpan A-2', 'a2');

    // A3
    var fA3 = document.getElementById('formA3');
    if (fA3) submitForm(fA3, 'actions/operator/save_all_forms.php', 'Simpan A-3', 'a3');

    // A4
    var fA4 = document.getElementById('formA4');
    if (fA4) submitForm(fA4, 'actions/operator/save_all_forms.php', 'Simpan A-4', 'a4');

    // A5
    var fA5 = document.getElementById('formA5');
    if (fA5) submitForm(fA5, 'actions/operator/save_all_forms.php', 'Tambah A-5', 'a5');

    // A6
    var fA6 = document.getElementById('formA6');
    if (fA6) submitForm(fA6, 'actions/operator/save_all_forms.php', 'Kirim A-6 Insiden', 'a6');

    // A7
    var fA7 = document.getElementById('formA7');
    if (fA7) submitForm(fA7, 'actions/operator/save_all_forms.php', 'Kirim A-7 Operasi Khusus', 'a7');

    // A8
    var fA8 = document.getElementById('formA8');
    if (fA8) submitForm(fA8, 'actions/operator/save_all_forms.php', 'Catat A-8 Pelanggaran', 'a8');
})();

// EDIT REASON MODAL
function openEditReasonModal() {
    var el = document.getElementById('editReasonModal');
    if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
}
function closeEditReasonModal() {
    var el = document.getElementById('editReasonModal');
    if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
}
(function(){
    var f = document.getElementById('formEditReason');
    if (!f) return;
    f.addEventListener('submit', function(e){
        e.preventDefault();
        var btn = document.getElementById('btnSubmitEditReason');
        if (btn) { btn.disabled = true; btn.textContent = 'Mengajukan\u2026'; }
        fetch(baseUrl + 'actions/operator/request_edit.php', { method: 'POST', body: new FormData(f) })
            .then(function(r){ return r.json(); })
            .then(function(res){
                showToast(res.message || (res.success ? 'Izin edit diberikan!' : 'Gagal.'), res.success ? 'success' : 'error');
                if (res.success) { closeEditReasonModal(); setTimeout(function(){ location.reload(); }, 1800); }
                else if (btn) { btn.disabled = false; btn.textContent = 'Ajukan Permintaan'; }
            })
            .catch(function(){ showToast('Koneksi gagal.', 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Ajukan Permintaan'; } });
    });
    var modal = document.getElementById('editReasonModal');
    if (modal) modal.addEventListener('click', function(e){ if (e.target === this) closeEditReasonModal(); });
})();
</script>

<!-- EDIT REASON MODAL -->
<div id="editReasonModal" class="fixed inset-0 z-[800] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
  <div class="bg-[#0f1523] border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-black text-white tracking-widest uppercase text-xs flex items-center gap-2">
        <span class="material-symbols-outlined text-amber-400 text-sm">edit_note</span>
        Minta Izin Edit
      </h3>
      <button onclick="closeEditReasonModal()" class="text-slate-400 hover:text-white transition-colors text-xl leading-none">&times;</button>
    </div>
    <p class="text-[11px] text-slate-400 mb-4">Izin edit berlaku <strong class="text-amber-300">15 menit</strong>. Untuk demo, permintaan langsung disetujui otomatis.</p>
    <form id="formEditReason" autocomplete="off">
      <input type="hidden" name="daily_shift_report_id" value="<?= $jsReportId ?>">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
      <div class="mb-4">
        <label class="modal-label" style="color:#94a3b8">Alasan Edit <span class="text-red-400">*</span></label>
        <textarea name="reason" id="editReasonText" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:#e2e8f0;border-radius:.375rem;padding:.375rem .5rem;font-size:.75rem;width:100%;resize:vertical;min-height:80px" placeholder="Jelaskan mengapa butuh edit..." required></textarea>
      </div>
      <div class="flex gap-3 items-center">
        <button type="submit" id="btnSubmitEditReason" class="machined-button">Ajukan Permintaan</button>
        <button type="button" onclick="closeEditReasonModal()" class="text-slate-400 text-xs hover:text-white transition-colors">Batal</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>