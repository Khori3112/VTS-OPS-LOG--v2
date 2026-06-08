<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireAnyRole(['Operator', 'Supervisor']);

$user = $_SESSION['user'];
$pdo  = getPdo();

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ── Maritime areas ──────────────────────────────────────────────────────────
$areaStmt = $pdo->query('SELECT area_name FROM maritime_areas WHERE is_active = 1 ORDER BY display_order ASC');
$areas    = $areaStmt ? $areaStmt->fetchAll(PDO::FETCH_COLUMN) : [];
if (empty($areas)) {
    $areas = ['Banyuasin','Selat Gelasa','Bangka','Muara Sungai Musi','Selat Bangka Utara',
              'Selat Bangka Selatan','Perairan Sungsang','Perairan Tanjung Buyut',
              'Perairan Upang','Ambang Luar','Tanjung Api-Api'];
}

// ── Personnel list for Officer / Supervisor dropdowns ──────────────────────
$personnelStmt = $pdo->query(
    'SELECT nip, full_name, role, team FROM users WHERE is_active = 1 ORDER BY role ASC, team ASC, full_name ASC'
);
$personnel = $personnelStmt ? $personnelStmt->fetchAll() : [];

$supervisors = array_filter($personnel, fn ($p) => $p['role'] === 'Supervisor');
$operators   = array_filter($personnel, fn ($p) => $p['role'] === 'Operator');

// ── "Import from previous shift" — last weather observations ───────────────
$prevWeather = [];
$prevShiftDate = date('Y-m-d', strtotime('-1 day'));
$prevStmt = $pdo->prepare(
    'SELECT wo.area_name, wo.weather_condition, wo.wind_direction, wo.wind_speed_knots,
            wo.wave_height_m, wo.wave_category
       FROM weather_reports wr
       JOIN weather_observations wo ON wo.weather_report_id = wr.id
       JOIN daily_shift_reports dsr ON dsr.id = wr.daily_shift_report_id
      WHERE dsr.shift_date = :d
      ORDER BY wo.area_name ASC'
);
$prevStmt->execute([':d' => $prevShiftDate]);
foreach ($prevStmt->fetchAll() as $r) {
    $prevWeather[(string) $r['area_name']] = $r;
}

$weatherOptions = ['Cerah','Berawan Sebagian','Berawan','Hujan Ringan','Gerimis',
                   'Hujan Sedang','Hujan Lebat','Hujan Sangat Lebat','Badai'];
$waveCategories = ['Tenang','Rendah','Sedang','Tinggi','Sangat Tinggi'];

$jsCsrf    = json_encode(csrfToken());
$jsBaseUrl = json_encode(BASE_APP);  // BASE_APP = project root, for action endpoints
$jsAreas   = json_encode(array_values($areas), JSON_UNESCAPED_UNICODE);
$jsPrevW   = json_encode($prevWeather, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entri Data Historis — VTS Palembang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@200..700,0..1&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif;background:#0d1117;color:#e2e8f0}
        .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}
        .glass-panel{background:rgba(14,14,22,.82);backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.07)}
        .input-dark{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.11);color:#e2e8f0;border-radius:.375rem;padding:.375rem .5rem;font-size:.75rem;width:100%}
        .input-dark:focus{outline:none;border-color:#FECB00;box-shadow:0 0 0 2px rgba(254,203,0,.15)}
        .input-dark option{background:#1e293b;color:#e2e8f0}
        .machined-button{background:linear-gradient(135deg,#AEC7F6 0%,#708AB5 100%);color:#001E40;font-weight:700;border-radius:.375rem;padding:.5rem 1rem;font-size:.75rem;cursor:pointer;border:none;transition:filter .15s}
        .machined-button:hover{filter:brightness(1.1)}
        .machined-button:disabled{opacity:.5;cursor:not-allowed}
        .tab-btn{padding:.5rem 1.25rem;border-radius:.5rem;font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;background:transparent;border:1px solid transparent;transition:all .15s;cursor:pointer}
        .tab-btn:hover{background:rgba(255,255,255,.05)}
        .tab-btn.active{background:#001E40;color:#FECB00;border-color:rgba(254,203,0,.3)}
        .tab-content{animation:fadeInTab .22s ease both}
        @keyframes fadeInTab{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .tab-content.hidden{display:none!important;animation:none}
        #toast-host{position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:100;display:flex;flex-direction:column;gap:.5rem;pointer-events:none}
        .toast-item{padding:.75rem 1.5rem;border-radius:.75rem;font-size:.75rem;font-weight:600;color:#fff;opacity:0;transform:translateY(16px);transition:all .3s;pointer-events:none;max-width:380px;text-align:center}
        .toast-item.show{opacity:1;transform:translateY(0);pointer-events:auto}
        .toast-item.success{background:rgba(16,185,129,.92);backdrop-filter:blur(8px)}
        .toast-item.error{background:rgba(239,68,68,.92);backdrop-filter:blur(8px)}
        .toast-item.warning{background:rgba(245,158,11,.92);backdrop-filter:blur(8px);color:#1a1a1a}
        .modal-label{display:block;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:.25rem}
        .weather-card{background:rgba(0,30,64,.5);border:1px solid rgba(174,199,246,.15);border-radius:1rem;padding:1rem;transition:border-color .2s}
        .weather-card:hover{border-color:rgba(254,203,0,.3)}
        textarea.input-dark{resize:vertical;min-height:80px}
        table.dtbl{width:100%;border-collapse:collapse}
        table.dtbl th{background:#002147;color:#FECB00;font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;padding:.5rem .75rem;text-align:left}
        table.dtbl td{font-size:.7rem;padding:.4rem .6rem;border-bottom:1px solid rgba(255,255,255,.05)}
        .badge-backdate{display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .75rem;border-radius:9999px;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.4);color:#fbbf24;font-size:.6rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    </style>
</head>
<body class="antialiased">
<?php include __DIR__ . '/../partials/topnav.php'; ?>
<div class="flex">
<?php include __DIR__ . '/../partials/sidenav.php'; ?>
<main class="flex-1 ml-64 pt-16 h-screen overflow-y-auto">
<div class="p-6 space-y-6 max-w-screen-2xl mx-auto">

<!-- PAGE HEADER -->
<div class="flex items-start justify-between flex-wrap gap-3">
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
            <h1 class="text-lg font-black uppercase tracking-widest text-white flex items-center gap-3">
                Entri Data Historis
                <span class="badge-backdate"><span class="material-symbols-outlined text-[12px]">history</span> BACKDATE MODE</span>
            </h1>
            <p class="text-[10px] text-amber-400/80 tracking-wide mt-0.5">
                Input data lama secara manual — setiap entri akan dicatat di Audit Log sebagai <em>Manual Backdate Entry</em>.
            </p>
        </div>
    </div>
    <a href="<?= BASE_URL ?>dashboard.php"
       class="flex items-center gap-1.5 text-xs text-blue-300 hover:text-white border border-blue-700/40 px-3 py-1.5 rounded-lg hover:bg-blue-900/30 transition-colors">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Dashboard
    </a>
</div>

<!-- ══════════════════════════════════════════════════════════════════
     SHIFT SELECTOR CONTROL — manual override
     ══════════════════════════════════════════════════════════════════ -->
<div class="glass-panel rounded-2xl p-5">
    <h2 class="text-xs font-black uppercase tracking-widest text-[#FECB00] mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">tune</span> Tentukan Shift yang Ingin Diinput
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <label class="modal-label">Tanggal Laporan <span class="text-red-400">*</span></label>
            <input type="date" id="shiftDate" class="input-dark"
                   value="<?= htmlspecialchars(date('Y-m-d')) ?>"
                   max="<?= date('Y-m-d') ?>" required>
        </div>
        <div>
            <label class="modal-label">Shift <span class="text-red-400">*</span></label>
            <select id="shiftCategory" class="input-dark" required>
                <option value="Pagi">Pagi (08:00–14:00)</option>
                <option value="Siang">Siang (14:00–20:00)</option>
                <option value="Malam">Malam (20:00–08:00)</option>
            </select>
        </div>
        <div>
            <label class="modal-label">Tim <span class="text-red-400">*</span></label>
            <select id="shiftTeam" class="input-dark" required>
                <?php foreach (['A','B','C','D','E'] as $t): ?>
                <option value="<?= $t ?>">Tim <?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end">
            <button id="btnLoadShift" class="machined-button w-full flex items-center justify-center gap-2"
                    data-tippy-content="Sistem akan mencari atau membuat Shift Report untuk parameter yang dipilih.">
                <span class="material-symbols-outlined text-sm">search</span>
                Muat / Buat Shift
            </button>
        </div>
    </div>
    <!-- Shift info banner -->
    <div id="shiftBanner" class="hidden mt-4 flex items-center gap-3 p-3 rounded-xl bg-emerald-900/20 border border-emerald-500/30">
        <span class="material-symbols-outlined text-emerald-400">check_circle</span>
        <span id="shiftBannerText" class="text-xs text-emerald-300 font-semibold"></span>
    </div>
    <div id="shiftError" class="hidden mt-4 flex items-center gap-3 p-3 rounded-xl bg-red-900/20 border border-red-500/30">
        <span class="material-symbols-outlined text-red-400">error</span>
        <span id="shiftErrorText" class="text-xs text-red-300"></span>
    </div>
    <!-- Hidden shift report ID (populated by AJAX) -->
    <input type="hidden" id="resolvedShiftReportId" value="0">
</div>

<!-- STAFF SELECTOR -->
<div class="glass-panel rounded-2xl p-5">
    <h2 class="text-xs font-black uppercase tracking-widest text-[#FECB00] mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">badge</span> Personel yang Bertugas
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="modal-label">Operator / Officer on Duty</label>
            <select id="officerNip" class="input-dark">
                <option value="">— Pilih Operator —</option>
                <?php foreach ($operators as $p): ?>
                <option value="<?= htmlspecialchars($p['nip']) ?>">
                    <?= htmlspecialchars($p['full_name']) ?> (Tim <?= htmlspecialchars($p['team']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="modal-label">Supervisor on Watch</label>
            <select id="supervisorNip" class="input-dark">
                <option value="">— Pilih Supervisor —</option>
                <?php foreach ($supervisors as $p): ?>
                <option value="<?= htmlspecialchars($p['nip']) ?>"
                    <?= str_contains(mb_strtolower($p['full_name']), 'ria irawan') ? 'data-ria="1"' : '' ?>>
                    <?= htmlspecialchars($p['full_name']) ?> (Tim <?= htmlspecialchars($p['team']) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- TAB NAVIGATION -->
<div class="flex gap-2 flex-wrap">
    <button class="tab-btn active" data-tab="he-a2">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">directions_boat</span>A-2 Traffic
    </button>
    <button class="tab-btn" data-tab="he-a3">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">cloud</span>A-3 Cuaca
    </button>
    <button class="tab-btn" data-tab="he-a6">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">emergency</span>A-6 Insiden
    </button>
    <button class="tab-btn" data-tab="he-a7">
        <span class="material-symbols-outlined align-middle mr-1" style="font-size:14px">shield</span>A-7 Ops Khusus
    </button>
</div>

<!-- ═══════════════════════════════════
     TAB A-2: VESSEL TRAFFIC (Historical)
     ═══════════════════════════════════ -->
<div id="tab-he-a2" class="tab-content">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="font-bold text-sm text-white">Form A-2 — Daily Vessel Traffic Report</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Identik dengan dokumen fisik: Jenis/Nama Kapal, Tanda Panggil, Pelabuhan, ETD/ETA, QSO/Posisi</p>
        </div>
        <button id="btnAddA2Row" class="machined-button text-[11px] px-3 py-1.5 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Baris
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="dtbl" id="a2HistTable">
            <thead>
                <tr>
                    <th style="width:22px">#</th>
                    <th>Nama Kapal</th>
                    <th>Tanda Panggil<br><span class="font-normal normal-case tracking-normal text-blue-300/70" style="font-size:.6rem">Call Sign</span></th>
                    <th>Asal<br><span class="font-normal normal-case tracking-normal text-blue-300/70" style="font-size:.6rem">Last Port</span></th>
                    <th>Tujuan<br><span class="font-normal normal-case tracking-normal text-blue-300/70" style="font-size:.6rem">Next Port</span></th>
                    <th>ETD</th>
                    <th>ETA</th>
                    <th>QSO / Posisi</th>
                    <th>Tipe</th>
                    <th>Agen</th>
                    <th>Arah</th>
                    <th style="width:32px"></th>
                </tr>
            </thead>
            <tbody id="a2HistBody">
                <!-- JS will populate rows -->
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex gap-3 items-center">
        <button id="btnSaveA2" class="machined-button flex items-center gap-2" disabled>
            <span class="material-symbols-outlined text-sm">save</span> Simpan A-2 Historis
        </button>
        <span id="a2SaveStatus" class="text-[10px] text-slate-400 italic"></span>
    </div>
</div>
</div>

<!-- ═══════════════════════════════════
     TAB A-3: WEATHER GRID (Historical)
     ═══════════════════════════════════ -->
<div id="tab-he-a3" class="tab-content hidden">
<div class="glass-panel rounded-2xl p-5">
    <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="font-bold text-sm text-white">Form A-3 — Watch Keeping Weather Observation</h2>
            <p class="text-[10px] text-slate-400 mt-0.5">Grid 11 wilayah perairan — format identik dengan dokumen Watch Keeping Hand Over</p>
        </div>
        <button id="btnImportPrev" class="flex items-center gap-1.5 text-xs text-amber-300 border border-amber-500/40 px-3 py-1.5 rounded-lg hover:bg-amber-900/30 transition-colors"
                data-tippy-content="Salin data cuaca dari shift terakhir yang tersimpan di database sebagai titik awal pengisian.">
            <span class="material-symbols-outlined text-sm">download</span>
            Import dari Shift Sebelumnya
        </button>
    </div>

    <form id="formA3Hist" novalidate>
        <input type="hidden" name="csrf_token" id="csrfA3" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="a3Grid">
        <?php foreach ($areas as $i => $areaName): ?>
        <?php $w = $prevWeather[$areaName] ?? []; ?>
        <div class="weather-card" data-area-idx="<?= $i ?>">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-blue-300 text-2xl">cloud</span>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-[#FECB00]">
                        <?= sprintf('%d.%d', (int)($i/2)+1, ($i%2)+1) ?> — <?= htmlspecialchars($areaName) ?>
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="col-span-2">
                    <label class="modal-label">Kondisi Cuaca</label>
                    <select name="weather_condition[<?= $i ?>]" class="input-dark text-[11px] wc-select" data-idx="<?= $i ?>">
                        <?php foreach ($weatherOptions as $opt): ?>
                        <option value="<?= htmlspecialchars($opt) ?>"
                            <?= ($w['weather_condition'] ?? '') === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="modal-label">Arah Angin</label>
                    <input type="text" name="wind_direction[<?= $i ?>]" class="input-dark text-[11px]"
                           placeholder="e.g. SE" value="<?= htmlspecialchars((string)($w['wind_direction'] ?? '')) ?>">
                </div>
                <div>
                    <label class="modal-label">Kecepatan (knot)</label>
                    <input type="number" name="wind_speed_knots[<?= $i ?>]" class="input-dark text-[11px]"
                           min="0" step="0.1" placeholder="0.0"
                           value="<?= htmlspecialchars((string)($w['wind_speed_knots'] ?? '0')) ?>">
                </div>
                <div>
                    <label class="modal-label">Tinggi Gelombang (m)</label>
                    <input type="number" name="wave_height_m[<?= $i ?>]" class="input-dark text-[11px]"
                           min="0" step="0.01" placeholder="0.00"
                           value="<?= htmlspecialchars((string)($w['wave_height_m'] ?? '0.00')) ?>">
                </div>
                <div>
                    <label class="modal-label">Kategori Gelombang</label>
                    <select name="wave_category[<?= $i ?>]" class="input-dark text-[11px]">
                        <?php foreach ($waveCategories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"
                            <?= ($w['wave_category'] ?? 'Tenang') === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- hidden area_name -->
                <input type="hidden" name="area_name[<?= $i ?>]" value="<?= htmlspecialchars($areaName) ?>">
            </div>
        </div>
        <?php endforeach; ?>
        </div><!-- /grid -->
        <div class="mt-5 flex gap-3 items-center">
            <button type="button" id="btnSaveA3" class="machined-button flex items-center gap-2" disabled>
                <span class="material-symbols-outlined text-sm">save</span> Simpan A-3 Historis
            </button>
            <span id="a3SaveStatus" class="text-[10px] text-slate-400 italic"></span>
        </div>
    </form>
</div>
</div>

<!-- ═══════════════════════════════════
     TAB A-6: INCIDENT REPORT (Historical)
     ═══════════════════════════════════ -->
<div id="tab-he-a6" class="tab-content hidden">
<div class="glass-panel rounded-2xl p-5">
    <div class="mb-5">
        <h2 class="font-bold text-sm text-white">Form A-6 — Laporan Insiden / Kejadian</h2>
        <p class="text-[10px] text-slate-400 mt-0.5">Kecelakaan, SAR, Pencemaran, Insiden Navigasi</p>
    </div>
    <form id="formA6Hist" novalidate>
        <input type="hidden" name="csrf_token" id="csrfA6" value="<?= htmlspecialchars(csrfToken()) ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="modal-label">Judul Insiden <span class="text-red-400">*</span></label>
                <input name="title" class="input-dark" placeholder="Contoh: Kapal Kandas di Perairan Sungsang" required>
            </div>
            <div>
                <label class="modal-label">Waktu Kejadian <span class="text-red-400">*</span></label>
                <input name="incident_datetime" type="datetime-local" class="input-dark" required>
            </div>
            <div>
                <label class="modal-label">Lokasi / Koordinat</label>
                <input name="pollution_location" class="input-dark" placeholder="02°15'S 105°10'E atau nama area">
            </div>
            <div>
                <label class="modal-label">Jumlah Korban Meninggal</label>
                <input name="deaths_count" type="number" min="0" value="0" class="input-dark">
            </div>
            <div>
                <label class="modal-label">Jumlah Korban Hilang</label>
                <input name="missing_count" type="number" min="0" value="0" class="input-dark">
            </div>
            <div class="md:col-span-2">
                <label class="modal-label">Kronologi Kejadian <span class="text-red-400">*</span></label>
                <textarea name="chronology" class="input-dark" rows="5"
                          placeholder="Uraikan kronologi secara sistematis dan kronologis..." required></textarea>
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
                <textarea name="immediate_action" class="input-dark" rows="5"
                          placeholder="Tindakan yang sudah diambil..."></textarea>
            </div>
        </div>
        <div class="mt-5 flex gap-3 items-center">
            <button type="button" id="btnSaveA6" class="machined-button flex items-center gap-2" disabled>
                <span class="material-symbols-outlined text-sm">send</span> Simpan A-6 Historis
            </button>
            <span id="a6SaveStatus" class="text-[10px] text-slate-400 italic"></span>
        </div>
    </form>
</div>
</div>

<!-- ═══════════════════════════════════
     TAB A-7: SPECIAL OPS (Historical)
     ═══════════════════════════════════ -->
<div id="tab-he-a7" class="tab-content hidden">
<div class="glass-panel rounded-2xl p-5">
    <div class="mb-5">
        <h2 class="font-bold text-sm text-white">Form A-7 — Operasi Khusus</h2>
        <p class="text-[10px] text-slate-400 mt-0.5">VVIP, Latihan Militer, Survei Hidrografi, Operasi SAR</p>
    </div>
    <form id="formA7Hist" novalidate>
        <input type="hidden" name="csrf_token" id="csrfA7" value="<?= htmlspecialchars(csrfToken()) ?>">
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
                <label class="modal-label">Waktu Selesai</label>
                <input name="operation_end" type="datetime-local" class="input-dark">
            </div>
            <div>
                <label class="modal-label">Lokasi Operasi</label>
                <input name="location" class="input-dark" placeholder="Contoh: Selat Bangka Selatan">
            </div>
            <div>
                <label class="modal-label">Sektor <span class="text-amber-400">*</span></label>
                <select name="sector" class="input-dark" required>
                    <option value="">— Pilih Sektor —</option>
                    <option value="Sektor Selat Bangka Utara">Sektor Selat Bangka Utara</option>
                    <option value="Sektor Selat Bangka Selatan">Sektor Selat Bangka Selatan</option>
                    <option value="Sektor Muara Sungai Musi">Sektor Muara Sungai Musi</option>
                    <option value="Sektor Perairan Sungsang">Sektor Perairan Sungsang</option>
                    <option value="Sektor Perairan Tanjung Buyut">Sektor Perairan Tanjung Buyut</option>
                    <option value="Sektor Tanjung Api-Api">Sektor Tanjung Api-Api</option>
                    <option value="Sektor Ambang Luar">Sektor Ambang Luar</option>
                    <option value="Sektor Banyuasin">Sektor Banyuasin</option>
                    <option value="Lintas Sektor">Lintas Sektor</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="modal-label">Uraian Kejadian / Situasi <span class="text-amber-400">*</span></label>
                <textarea name="event_description" class="input-dark" rows="4"
                          placeholder="Uraikan secara lengkap situasi dan kejadian selama operasi..." required></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="modal-label">Detail Prosedur Operasi</label>
                <textarea name="operation_details" class="input-dark" rows="3"
                          placeholder="Langkah-langkah prosedur yang dilaksanakan..."></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="modal-label">Hasil Operasi</label>
                <textarea name="outcome_notes" class="input-dark" rows="3"
                          placeholder="Ringkasan hasil dan kesimpulan operasi..."></textarea>
            </div>
        </div>
        <div class="mt-5 flex gap-3 items-center">
            <button type="button" id="btnSaveA7" class="machined-button flex items-center gap-2" disabled>
                <span class="material-symbols-outlined text-sm">send</span> Simpan A-7 Historis
            </button>
            <span id="a7SaveStatus" class="text-[10px] text-slate-400 italic"></span>
        </div>
    </form>
</div>
</div>

</div><!-- /container -->
</main>
</div><!-- /flex -->

<div id="toast-host"></div>

<script>
// ── Injected PHP data ────────────────────────────────────────────────────────
var CSRF_TOKEN = <?= $jsCsrf ?>;
var BASE_URL   = <?= $jsBaseUrl ?>;
var AREAS      = <?= $jsAreas ?>;
var PREV_WEATHER = <?= $jsPrevW ?>;

// ── Toast ─────────────────────────────────────────────────────────────────────
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
// Flash from PHP session
(function(){
    var s = <?= json_encode($flashSuccess) ?>, e = <?= json_encode($flashError) ?>;
    if (s) showToast(s, 'success');
    if (e) showToast(e, 'error');
})();

// ── Tabs ──────────────────────────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
        document.querySelectorAll('.tab-content').forEach(function(c){ c.classList.add('hidden'); });
        this.classList.add('active');
        var t = document.getElementById('tab-' + this.dataset.tab);
        if (t) {
            t.classList.remove('hidden');
            t.style.animation = 'none'; void t.offsetHeight; t.style.animation = '';
        }
    });
});

// ── Shift Resolver ───────────────────────────────────────────────────────────
var resolvedId = 0;

document.getElementById('btnLoadShift').addEventListener('click', function() {
    var date     = document.getElementById('shiftDate').value;
    var category = document.getElementById('shiftCategory').value;
    var team     = document.getElementById('shiftTeam').value;

    if (!date || !category || !team) {
        showToast('Lengkapi Tanggal, Shift, dan Tim terlebih dahulu.', 'warning');
        return;
    }

    this.textContent = 'Memuat...';
    this.disabled = true;

    var fd = new FormData();
    fd.append('action',         'resolve_shift');
    fd.append('shift_date',     date);
    fd.append('shift_category', category);
    fd.append('team',           team);
    fd.append('officer_nip',    document.getElementById('officerNip').value);
    fd.append('supervisor_nip', document.getElementById('supervisorNip').value);
    fd.append('csrf_token',     CSRF_TOKEN);

    var self = this;
    fetch(BASE_URL + 'actions/operator/historical_entry_store.php', {
        method: 'POST',
        body: fd
    }).then(function(r){ return r.json(); }).then(function(res){
        self.innerHTML = '<span class="material-symbols-outlined text-sm">search</span> Muat / Buat Shift';
        self.disabled = false;

        var banner = document.getElementById('shiftBanner');
        var errDiv = document.getElementById('shiftError');
        if (res.success) {
            resolvedId = parseInt(res.shift_report_id, 10);
            document.getElementById('resolvedShiftReportId').value = resolvedId;
            document.getElementById('shiftBannerText').textContent =
                'Shift Report #' + resolvedId + ' siap — ' + date + ' ' + category + ' Tim ' + team;
            banner.classList.remove('hidden');
            errDiv.classList.add('hidden');
            // unlock all save buttons
            ['btnSaveA2','btnSaveA3','btnSaveA6','btnSaveA7'].forEach(function(id){
                var b = document.getElementById(id);
                if (b) b.disabled = false;
            });
            showToast('Shift Report dimuat. Silakan isi formulir.', 'success');
        } else {
            banner.classList.add('hidden');
            errDiv.classList.remove('hidden');
            document.getElementById('shiftErrorText').textContent = res.message || 'Gagal memuat shift.';
            showToast(res.message || 'Gagal', 'error');
        }
    }).catch(function(){
        self.innerHTML = '<span class="material-symbols-outlined text-sm">search</span> Muat / Buat Shift';
        self.disabled = false;
        showToast('Koneksi gagal. Periksa jaringan.', 'error');
    });
});

// ── A-2 Dynamic Table ─────────────────────────────────────────────────────────
function makeA2Row(n) {
    var tr = document.createElement('tr');
    tr.innerHTML =
        '<td class="text-slate-500 text-center">' + n + '</td>' +
        '<td><input class="input-dark" name="vessel_name[]" placeholder="Nama kapal" autocomplete="off"></td>' +
        '<td><input class="input-dark font-mono" name="call_sign[]" placeholder="YBZT2" autocomplete="off"></td>' +
        '<td><input class="input-dark" name="last_port[]" placeholder="Asal" autocomplete="off"></td>' +
        '<td><input class="input-dark" name="next_port[]" placeholder="Tujuan" autocomplete="off"></td>' +
        '<td><input class="input-dark" name="etd[]" type="datetime-local"></td>' +
        '<td><input class="input-dark" name="eta[]" type="datetime-local"></td>' +
        '<td><input class="input-dark" name="qso_position[]" placeholder="QSO / Koordinat" autocomplete="off"></td>' +
        '<td><input class="input-dark" name="vessel_type[]" placeholder="Tipe" autocomplete="off"></td>' +
        '<td><input class="input-dark" name="agent[]" placeholder="Agen" autocomplete="off"></td>' +
        '<td><select class="input-dark" name="direction[]">' +
              '<option value="inbound">Masuk</option>' +
              '<option value="outbound">Keluar</option>' +
              '<option value="transit">Transit</option>' +
            '</select></td>' +
        '<td class="text-center"><button type="button" class="rm-a2-row text-red-400 hover:text-red-300 text-xs px-1"' +
            ' title="Hapus baris"><span class="material-symbols-outlined" style="font-size:14px">delete</span></button></td>';
    return tr;
}
(function(){
    var tbody = document.getElementById('a2HistBody');
    // Start with 3 rows
    for (var i = 1; i <= 3; i++) tbody.appendChild(makeA2Row(i));

    document.getElementById('btnAddA2Row').addEventListener('click', function(){
        var rows = tbody.querySelectorAll('tr');
        tbody.appendChild(makeA2Row(rows.length + 1));
    });

    tbody.addEventListener('click', function(e){
        var rmBtn = e.target.closest('.rm-a2-row');
        if (!rmBtn) return;
        var rows = tbody.querySelectorAll('tr');
        if (rows.length <= 1) { showToast('Minimal satu baris diperlukan.', 'warning'); return; }
        rmBtn.closest('tr').remove();
        // renumber
        tbody.querySelectorAll('tr').forEach(function(tr, idx){
            var numCell = tr.querySelector('td:first-child');
            if (numCell) numCell.textContent = idx + 1;
        });
    });
})();

// ── A-2 Save ──────────────────────────────────────────────────────────────────
document.getElementById('btnSaveA2').addEventListener('click', function(){
    var id = parseInt(document.getElementById('resolvedShiftReportId').value, 10);
    if (id <= 0) { showToast('Muat Shift terlebih dahulu.', 'warning'); return; }

    var tbody = document.getElementById('a2HistBody');
    var rows = tbody.querySelectorAll('tr');
    var vessels = [];
    rows.forEach(function(tr){
        var name = tr.querySelector('[name="vessel_name[]"]').value.trim();
        if (!name) return;
        vessels.push({
            vessel_name:   name,
            call_sign:     tr.querySelector('[name="call_sign[]"]').value.trim(),
            last_port:     tr.querySelector('[name="last_port[]"]').value.trim(),
            next_port:     tr.querySelector('[name="next_port[]"]').value.trim(),
            etd:           tr.querySelector('[name="etd[]"]').value,
            eta:           tr.querySelector('[name="eta[]"]').value,
            qso_position:  tr.querySelector('[name="qso_position[]"]').value.trim(),
            vessel_type:   tr.querySelector('[name="vessel_type[]"]').value.trim(),
            agent:         tr.querySelector('[name="agent[]"]').value.trim(),
            direction:     tr.querySelector('[name="direction[]"]').value,
        });
    });
    if (!vessels.length) { showToast('Isi minimal satu nama kapal.', 'warning'); return; }

    var status = document.getElementById('a2SaveStatus');
    status.textContent = 'Menyimpan...';
    this.disabled = true;
    var self = this;

    fetch(BASE_URL + 'actions/operator/historical_entry_store.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            action: 'save_a2',
            shift_report_id: id,
            csrf_token: CSRF_TOKEN,
            officer_nip: document.getElementById('officerNip').value,
            supervisor_nip: document.getElementById('supervisorNip').value,
            vessels: vessels
        })
    }).then(function(r){ return r.json(); }).then(function(res){
        self.disabled = false;
        if (res.success) {
            status.textContent = 'Tersimpan ' + new Date().toLocaleTimeString('id-ID');
            showToast('A-2: ' + res.inserted + ' kapal berhasil disimpan.', 'success');
        } else {
            status.textContent = 'Gagal';
            showToast(res.message || 'Gagal simpan A-2', 'error');
        }
    }).catch(function(){
        self.disabled = false;
        status.textContent = 'Error koneksi';
        showToast('Koneksi gagal.', 'error');
    });
});

// ── A-3 Import Previous ───────────────────────────────────────────────────────
document.getElementById('btnImportPrev').addEventListener('click', function(){
    var keys = Object.keys(PREV_WEATHER);
    if (!keys.length) {
        showToast('Tidak ada data cuaca shift sebelumnya di database.', 'warning');
        return;
    }
    var cards = document.querySelectorAll('#a3Grid .weather-card');
    cards.forEach(function(card){
        var idx      = card.getAttribute('data-area-idx');
        var areaName = AREAS[idx];
        var prev     = PREV_WEATHER[areaName];
        if (!prev) return;
        card.querySelector('[name="weather_condition[' + idx + ']"]').value = prev.weather_condition || 'Cerah';
        card.querySelector('[name="wind_direction[' + idx + ']"]').value   = prev.wind_direction   || '';
        card.querySelector('[name="wind_speed_knots[' + idx + ']"]').value = prev.wind_speed_knots || '0';
        card.querySelector('[name="wave_height_m[' + idx + ']"]').value    = prev.wave_height_m    || '0.00';
        card.querySelector('[name="wave_category[' + idx + ']"]').value    = prev.wave_category    || 'Tenang';
    });
    showToast('Data cuaca shift sebelumnya berhasil diimpor.', 'success', 4000);
});

// ── A-3 Save ──────────────────────────────────────────────────────────────────
document.getElementById('btnSaveA3').addEventListener('click', function(){
    var id = parseInt(document.getElementById('resolvedShiftReportId').value, 10);
    if (id <= 0) { showToast('Muat Shift terlebih dahulu.', 'warning'); return; }

    var form   = document.getElementById('formA3Hist');
    var cards  = form.querySelectorAll('.weather-card');
    var obsArr = [];
    cards.forEach(function(card, i){
        obsArr.push({
            area_name:         form.querySelector('[name="area_name[' + i + ']"]').value,
            weather_condition: form.querySelector('[name="weather_condition[' + i + ']"]').value,
            wind_direction:    form.querySelector('[name="wind_direction[' + i + ']"]').value.trim() || '-',
            wind_speed_knots:  parseFloat(form.querySelector('[name="wind_speed_knots[' + i + ']"]').value) || 0,
            wave_height_m:     parseFloat(form.querySelector('[name="wave_height_m[' + i + ']"]').value) || 0,
            wave_category:     form.querySelector('[name="wave_category[' + i + ']"]').value,
        });
    });

    var status = document.getElementById('a3SaveStatus');
    status.textContent = 'Menyimpan...';
    this.disabled = true;
    var self = this;

    fetch(BASE_URL + 'actions/operator/historical_entry_store.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            action: 'save_a3',
            shift_report_id: id,
            csrf_token: CSRF_TOKEN,
            officer_nip: document.getElementById('officerNip').value,
            observations: obsArr
        })
    }).then(function(r){ return r.json(); }).then(function(res){
        self.disabled = false;
        if (res.success) {
            status.textContent = 'Tersimpan ' + new Date().toLocaleTimeString('id-ID');
            showToast('A-3: Data cuaca ' + obsArr.length + ' wilayah berhasil disimpan.', 'success');
        } else {
            status.textContent = 'Gagal';
            showToast(res.message || 'Gagal simpan A-3', 'error');
        }
    }).catch(function(){
        self.disabled = false; status.textContent = 'Error';
        showToast('Koneksi gagal.', 'error');
    });
});

// ── A-6 Save ──────────────────────────────────────────────────────────────────
document.getElementById('btnSaveA6').addEventListener('click', function(){
    var id = parseInt(document.getElementById('resolvedShiftReportId').value, 10);
    if (id <= 0) { showToast('Muat Shift terlebih dahulu.', 'warning'); return; }

    var form = document.getElementById('formA6Hist');
    var title = form.querySelector('[name="title"]').value.trim();
    var incTime = form.querySelector('[name="incident_datetime"]').value;
    var chronology = form.querySelector('[name="chronology"]').value.trim();
    if (!title || !incTime || !chronology) {
        showToast('Lengkapi Judul, Waktu Kejadian, dan Kronologi.', 'warning'); return;
    }

    var auths = [];
    form.querySelectorAll('[name="authorities_notified[]"]:checked').forEach(function(cb){ auths.push(cb.value); });

    var status = document.getElementById('a6SaveStatus');
    status.textContent = 'Menyimpan...';
    this.disabled = true;
    var self = this;

    fetch(BASE_URL + 'actions/operator/historical_entry_store.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            action: 'save_a6',
            shift_report_id: id,
            csrf_token: CSRF_TOKEN,
            officer_nip: document.getElementById('officerNip').value,
            title: title,
            incident_datetime: incTime,
            chronology: chronology,
            deaths_count: parseInt(form.querySelector('[name="deaths_count"]').value, 10) || 0,
            missing_count: parseInt(form.querySelector('[name="missing_count"]').value, 10) || 0,
            pollution_location: form.querySelector('[name="pollution_location"]').value.trim(),
            authorities_notified: auths,
            immediate_action: form.querySelector('[name="immediate_action"]').value.trim(),
        })
    }).then(function(r){ return r.json(); }).then(function(res){
        self.disabled = false;
        if (res.success) {
            status.textContent = 'Tersimpan ' + new Date().toLocaleTimeString('id-ID');
            showToast('A-6 Insiden berhasil disimpan.', 'success');
            form.reset();
        } else {
            status.textContent = 'Gagal';
            showToast(res.message || 'Gagal simpan A-6', 'error');
        }
    }).catch(function(){
        self.disabled = false; status.textContent = 'Error';
        showToast('Koneksi gagal.', 'error');
    });
});

// ── A-7 Save ──────────────────────────────────────────────────────────────────
document.getElementById('btnSaveA7').addEventListener('click', function(){
    var id = parseInt(document.getElementById('resolvedShiftReportId').value, 10);
    if (id <= 0) { showToast('Muat Shift terlebih dahulu.', 'warning'); return; }

    var form = document.getElementById('formA7Hist');
    var opName = form.querySelector('[name="operation_name"]').value.trim();
    var opStart = form.querySelector('[name="operation_start"]').value;
    if (!opName || !opStart) {
        showToast('Nama Operasi dan Waktu Mulai wajib diisi.', 'warning'); return;
    }

    var status = document.getElementById('a7SaveStatus');
    status.textContent = 'Menyimpan...';
    this.disabled = true;
    var self = this;

    fetch(BASE_URL + 'actions/operator/historical_entry_store.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            action: 'save_a7',
            shift_report_id: id,
            csrf_token: CSRF_TOKEN,
            officer_nip: document.getElementById('officerNip').value,
            operation_name: opName,
            operation_start: opStart,
            operation_end: form.querySelector('[name="operation_end"]').value,
            location: form.querySelector('[name="location"]').value.trim(),
            sector: form.querySelector('[name="sector"]').value,
            event_description: form.querySelector('[name="event_description"]').value.trim(),
            operation_details: form.querySelector('[name="operation_details"]').value.trim(),
            outcome_notes: form.querySelector('[name="outcome_notes"]').value.trim(),
        })
    }).then(function(r){ return r.json(); }).then(function(res){
        self.disabled = false;
        if (res.success) {
            status.textContent = 'Tersimpan ' + new Date().toLocaleTimeString('id-ID');
            showToast('A-7 Operasi Khusus berhasil disimpan.', 'success');
            form.reset();
        } else {
            status.textContent = 'Gagal';
            showToast(res.message || 'Gagal simpan A-7', 'error');
        }
    }).catch(function(){
        self.disabled = false; status.textContent = 'Error';
        showToast('Koneksi gagal.', 'error');
    });
});

// ── Tooltips ──────────────────────────────────────────────────────────────────
if (typeof tippy !== 'undefined') {
    tippy('[data-tippy-content]', { theme: 'translucent', placement: 'top', delay: [80, 0], arrow: true });
}
</script>
</body>
</html>
