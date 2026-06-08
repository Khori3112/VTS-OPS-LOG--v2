<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../middleware.php';

requireLogin();
requireAnyRole(['Operator', 'Supervisor', 'Manager']);

$user = $_SESSION['user'];
$dailyShiftReportId = (int) ($user['daily_shift_report_id'] ?? 0);
if ($dailyShiftReportId <= 0) {
    $_SESSION['flash_error'] = 'Shift report tidak valid untuk export PDF.';
    redirect('/public/dashboard.php');
}

$pdo = getPdo();
try {
$reportStmt = $pdo->prepare(
    'SELECT dsr.*, uo.full_name AS operator_name, us.full_name AS supervisor_name, um.full_name AS manager_name
     FROM daily_shift_reports dsr
     LEFT JOIN users uo ON uo.nip = dsr.operator_nip
     LEFT JOIN users us ON us.nip = dsr.supervisor_nip
     LEFT JOIN users um ON um.nip = dsr.manager_nip
     WHERE dsr.id = :id
     LIMIT 1'
);
$reportStmt->execute([':id' => $dailyShiftReportId]);
$report = $reportStmt->fetch();

if (!$report) {
    $_SESSION['flash_error'] = 'Data shift report tidak ditemukan.';
    redirect('/public/dashboard.php');
}

// Operator hanya boleh export laporan yang menjadi miliknya sendiri
if ($user['role'] === 'Operator' && $report['operator_nip'] !== $user['nip']) {
    $_SESSION['flash_error'] = 'Akses ditolak. Anda hanya bisa export laporan shift Anda sendiri.';
    redirect('/public/dashboard.php');
}

if ((int) $report['is_locked'] !== 1) {
    $_SESSION['flash_error'] = 'Cetak PDF hanya bisa setelah laporan dikunci dan dikirim.';
    redirect('/public/dashboard.php');
}

$a1 = $pdo->prepare('SELECT log_time, vessel_name, call_sign, activity, location, notes FROM vts_logs WHERE daily_shift_report_id = :id ORDER BY log_time ASC');
$a1->execute([':id' => $dailyShiftReportId]);
$a1Rows = $a1->fetchAll();

$a2 = $pdo->prepare('SELECT vessel_name, call_sign, last_port, next_port, etd, eta, alongside_time, anchor_time, depart_time, vessel_type, agent, remarks FROM vessel_traffic WHERE daily_shift_report_id = :id ORDER BY created_at ASC');
$a2->execute([':id' => $dailyShiftReportId]);
$a2Rows = $a2->fetchAll();

$a3 = $pdo->prepare(
    'SELECT wo.area_name, wo.weather_condition, wo.wind_direction, wo.wind_speed_knots, wo.wave_height_m, wo.wave_category
     FROM weather_reports wr
     JOIN weather_observations wo ON wo.weather_report_id = wr.id
     WHERE wr.daily_shift_report_id = :id
     ORDER BY wo.area_name ASC'
);
$a3->execute([':id' => $dailyShiftReportId]);
$a3Rows = $a3->fetchAll();

$a5 = $pdo->prepare('SELECT * FROM pre_arrival_reports WHERE daily_shift_report_id = :id ORDER BY created_at ASC');
$a5->execute([':id' => $dailyShiftReportId]);
$a5Rows = $a5->fetchAll();

$a6 = $pdo->prepare('SELECT * FROM incident_reports WHERE daily_shift_report_id = :id ORDER BY created_at ASC');
$a6->execute([':id' => $dailyShiftReportId]);
$a6Rows = $a6->fetchAll();

$a7 = $pdo->prepare('SELECT * FROM special_ops_reports WHERE daily_shift_report_id = :id ORDER BY created_at ASC');
$a7->execute([':id' => $dailyShiftReportId]);
$a7Rows = $a7->fetchAll();

$a8 = $pdo->prepare('SELECT * FROM contravention_reports WHERE daily_shift_report_id = :id ORDER BY created_at ASC');
$a8->execute([':id' => $dailyShiftReportId]);
$a8Rows = $a8->fetchAll();

$sessionUser = $_SESSION['user'] ?? [];
$supervisorName = (string) ($report['supervisor_name'] ?: 'Ria Irawan, S.Pd');
$managerName = (string) ($report['manager_name'] ?: 'Merry D. Anitasari');

if (($sessionUser['role'] ?? '') === 'Supervisor' && !empty($sessionUser['full_name'])) {
    $supervisorName = (string) $sessionUser['full_name'];
}
if (($sessionUser['role'] ?? '') === 'Manager' && !empty($sessionUser['full_name'])) {
    $managerName = (string) $sessionUser['full_name'];
}

// Dynamic QR: use local PNG if supervisor is Ria Irawan, otherwise Google Charts
// All images live in public/assets/img/ — same directory as this file.
$isRiaIrawan  = str_contains(mb_strtolower($supervisorName, 'UTF-8'), 'ria irawan');
$qrLocalPath  = __DIR__ . '/assets/img/qr_ria_irawan.png';
$_qrFileUri   = 'file://' . str_replace('\\', '/', $qrLocalPath);
$qrSrc        = ($isRiaIrawan && file_exists($qrLocalPath))
    ? $_qrFileUri
    : 'https://chart.apis.google.com/chart?chs=72x72&cht=qr&chl=VTS-PLG-' . $dailyShiftReportId . '-' . rawurlencode($supervisorName);

} catch (Throwable $e) {
    logError('export_pdf: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Gagal membuat PDF — terjadi kesalahan sistem. Tim teknis telah diberitahu.';
    redirect('/public/dashboard.php');
}

// ── Resolve logo paths for Dompdf via filesystem (file://) ──────────────────
// Images are stored in public/assets/img/ — same folder as this script.
// Using __DIR__ directly (no /../) is correct since export_pdf.php lives in public/.
$_logoNavPath = realpath(__DIR__ . '/assets/img/logo_navigasi.png');
$_logoKmhPath = realpath(__DIR__ . '/assets/img/logo_kemhub.png');
$_pdfLogoNav  = $_logoNavPath ? 'file://' . str_replace('\\', '/', $_logoNavPath) : '';
$_pdfLogoKmh  = $_logoKmhPath ? 'file://' . str_replace('\\', '/', $_logoKmhPath) : '';

try {
ob_start();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        .meta { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #555; padding: 4px 5px; vertical-align: top; }
        th { background: #002B5B; color: #fff; font-weight: bold; }
        .page-break { page-break-before: always; }
        @page          { margin: 16mm 12mm; size: A4 portrait; }
        @page a4portrait  { margin: 16mm 12mm; size: A4 portrait; }
        @page a4landscape { margin: 12mm; size: A4 landscape; }
        .portrait-section  { page: a4portrait; }
        .landscape-section { page: a4landscape; }
        /* Diagonal watermark */
        .watermark {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            font-size: 52px;
            font-weight: bold;
            color: rgba(160, 0, 0, 0.09);
            letter-spacing: 0.06em;
            line-height: 1.6;
            text-align: center;
            transform: rotate(-28deg);
        }
        .signature { margin-top: 24px; width: 100%; }
        .sig-col { width: 45%; display: inline-block; vertical-align: top; text-align: center; font-size: 10px; }
        .qr-box { display: inline-block; vertical-align: top; width: 10%; text-align: center; font-size: 8px; }
        /* A-2 Heavy border table */
        .a2-table { border: 2px solid #111 !important; }
        .a2-table th { border: 2px solid #111; background: #001733; color: #fff; font-size: 8px; padding: 4px 5px; }
        .a2-table td { border: 1px solid #333; font-size: 8.5px; padding: 4px 5px; }
        /* ── Kementerian branding header ── */
        .kemhub-header {
            border-bottom: 3px double #002B5B;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kemhub-header table { border: none; margin: 0; }
        .kemhub-header td   { border: none; padding: 0 6px; vertical-align: middle; }
        .kemhub-logo { width: 56px; height: 56px; border-radius: 50%; border: 1px solid #ccc; }
        .kemhub-title { font-size: 10px; font-weight: bold; color: #002B5B;
                        text-align: center; text-transform: uppercase; letter-spacing: 0.04em; line-height: 1.5; }
        .kemhub-title .inst-main { font-size: 13px; }
        .kemhub-sub { font-size: 8px; color: #555; text-align: center; margin-top: 2px; }
        .shift-meta { font-size: 9px; background: #f0f4f8; padding: 4px 8px;
                      border-radius: 3px; margin-top: 6px; color: #333; }
        /* ── A-3 two-column weather grid ── */
        .wx-grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .wx-grid td { border: none; padding: 0 4px 6px 0; vertical-align: top; width: 50%; }
        .wx-card { border: 1px solid #ccc; border-radius: 4px; padding: 5px 7px; font-size: 9px; }
        .wx-card .wx-area { font-size: 9px; font-weight: bold; color: #002B5B;
                            border-bottom: 1px solid #dce6f5; padding-bottom: 3px; margin-bottom: 4px; }
        .wx-row  { display: table; width: 100%; }
        .wx-cell { display: table-cell; width: 50%; padding-bottom: 2px; }
        .wx-lbl  { color: #888; font-size: 7.5px; }
        .wx-val  { font-weight: 600; }
    </style>
</head>
<body>
<?php if ((int) ($report['final_copy_watermark'] ?? 0) === 1): ?>
<div class="watermark">FINAL COPY<br>VTS PALEMBANG</div>
<?php endif; ?>

<!-- Kementerian Perhubungan Institutional Header -->
<div class="kemhub-header">
    <table><tr>
        <td style="width:62px">
            <?php if ($_pdfLogoNav): ?>
            <img src="<?= htmlspecialchars($_pdfLogoNav) ?>" class="kemhub-logo" alt="Logo Navigasi">
            <?php else: ?>
            <div class="kemhub-logo" style="display:inline-block;text-align:center;font-size:9px;font-weight:bold;color:#002B5B;border-radius:50%;border:1px solid #ccc;padding:4px">VTS</div>
            <?php endif; ?>
        </td>
        <td>
            <div class="kemhub-title">
                <div>KEMENTERIAN PERHUBUNGAN REPUBLIK INDONESIA</div>
                <div class="inst-main">DIREKTORAT JENDERAL PERHUBUNGAN LAUT</div>
                <div>DISTRIK NAVIGASI TIPE B PALEMBANG</div>
                <div>VESSEL TRAFFIC SERVICE (VTS) PALEMBANG</div>
            </div>
            <div class="kemhub-sub">Jl. Merdeka No. 306, Palembang, Sumatera Selatan &mdash; Telp. (0711) 352xxx</div>
        </td>
        <td style="width:62px">
            <?php if ($_pdfLogoKmh): ?>
            <img src="<?= htmlspecialchars($_pdfLogoKmh) ?>" class="kemhub-logo" alt="Logo Kemhub">
            <?php else: ?>
            <div class="kemhub-logo" style="display:inline-block;text-align:center;font-size:9px;font-weight:bold;color:#d0a600;border-radius:50%;border:1px solid #ccc;padding:4px">KMH</div>
            <?php endif; ?>
        </td>
    </tr></table>
    <div class="shift-meta">
        <strong>LAPORAN OPERASIONAL SHIFT</strong>
        &nbsp;&mdash;&nbsp;
        Tanggal: <strong><?= htmlspecialchars((string) $report['shift_date']) ?></strong>
        &nbsp;|&nbsp; Shift: <strong><?= htmlspecialchars((string) $report['shift_category']) ?></strong>
        &nbsp;|&nbsp; Tim: <strong><?= htmlspecialchars((string) $report['team']) ?></strong>
        <?php if ((int) ($report['final_copy_watermark'] ?? 0) === 1): ?>
            &nbsp;|&nbsp; <strong style="color:#900">&#9632; FINAL COPY</strong>
        <?php endif; ?>
    </div>
</div>

<div class="portrait-section">
<h2>Form A1 - VTS Log</h2>
<table>
    <thead>
    <tr>
        <th>Waktu</th>
        <th>Kapal</th>
        <th>Call Sign</th>
        <th>Aktivitas</th>
        <th>Lokasi</th>
        <th>Catatan</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!$a1Rows): ?>
        <tr><td colspan="6">Tidak ada data A1.</td></tr>
    <?php else: foreach ($a1Rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars((string) $row['log_time']) ?></td>
            <td><?= htmlspecialchars((string) ($row['vessel_name'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['call_sign'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) $row['activity']) ?></td>
            <td><?= htmlspecialchars((string) ($row['location'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['notes'] ?? '-')) ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<h2 style="font-size:11px;color:#002B5B;border-left:3px solid #FECB00;padding-left:6px;margin-bottom:8px">Form A-3 &mdash; Watch Keeping Weather Observation</h2>
<?php if (!$a3Rows): ?>
    <p style="color:#888;font-size:9px">Tidak ada data cuaca A-3.</p>
<?php else: ?>
<?php
$a3Chunks = array_chunk($a3Rows, 2); // 2-column grid
?>
<table class="wx-grid">
<?php foreach ($a3Chunks as $pair): ?>
    <tr>
    <?php foreach ($pair as $row): ?>
        <td>
            <div class="wx-card">
                <div class="wx-area"><?= htmlspecialchars((string) $row['area_name']) ?></div>
                <div class="wx-row">
                    <div class="wx-cell"><span class="wx-lbl">Kondisi:</span><br><span class="wx-val"><?= htmlspecialchars((string) $row['weather_condition']) ?></span></div>
                    <div class="wx-cell"><span class="wx-lbl">Arah Angin:</span><br><span class="wx-val"><?= htmlspecialchars((string) $row['wind_direction']) ?></span></div>
                </div>
                <div class="wx-row" style="margin-top:3px">
                    <div class="wx-cell"><span class="wx-lbl">Kecepatan:</span><br><span class="wx-val"><?= htmlspecialchars((string) $row['wind_speed_knots']) ?> kn</span></div>
                    <div class="wx-cell"><span class="wx-lbl">Gel. (m) / Kat:</span><br><span class="wx-val"><?= htmlspecialchars((string) $row['wave_height_m']) ?> m / <?= htmlspecialchars((string) $row['wave_category']) ?></span></div>
                </div>
            </div>
        </td>
    <?php endforeach; ?>
    <?php if (count($pair) === 1): ?><td></td><?php endif; ?>
    </tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<div class="signature">
    <div class="sig-col">
        Mengetahui, Supervisor VTS,<br><br><br><br>
        <strong><?= htmlspecialchars((string) $supervisorName) ?></strong><br>
        <span style="font-size:8px">Supervisor Tim <?= htmlspecialchars((string) ($report['team'] ?? '')) ?></span>
    </div>
    <div class="sig-col">
        Manajer VTS Palembang,<br><br><br><br>
        <strong><?= htmlspecialchars((string) $managerName) ?></strong><br>
        <span style="font-size:8px">Manajer Operasional VTS</span>
    </div>
    <?php if ((int) ($report['is_locked'] ?? 0) === 1): ?>
    <div class="qr-box">
        <p style="font-size:7px; margin-bottom:2px">QR Verifikasi</p>
        <img src="<?= htmlspecialchars($qrSrc) ?>" width="72" height="72" alt="QR">
        <p style="font-size:7px; margin-top:2px">Laporan #<?= $dailyShiftReportId ?></p>
        <?php if ((int) ($report['final_copy_watermark'] ?? 0) === 1): ?>
        <p style="font-size:6px; color:#900; font-weight:bold; margin-top:2px;">FINAL COPY</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
</div>

<?php $useA2Landscape = count($a2Rows) >= 1; // A-2 always renders landscape – many columns need the extra width ?>
<?php if ($useA2Landscape): ?>
<div class="page-break landscape-section"></div>
<?php else: ?>
<div class="page-break portrait-section"></div>
<?php endif; ?>
<h2 style="font-size:11px;color:#002B5B;border-left:3px solid #FECB00;padding-left:6px;margin-bottom:8px">Form A-2 &mdash; Daily Vessel Traffic Report <span style="font-size:8px;color:#888">(Landscape &mdash; <?= count($a2Rows) ?> kapal)</span></h2>
<table class="a2-table">
    <thead>
    <tr>
        <th>Nama Kapal</th>
        <th>Call Sign</th>
        <th>Last Port</th>
        <th>Next Port</th>
        <th>ETD</th>
        <th>ETA</th>
        <th>Alongside</th>
        <th>Anchor</th>
        <th>Depart</th>
        <th>Type</th>
        <th>Agent</th>
        <th>Remarks</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!$a2Rows): ?>
        <tr><td colspan="12">Tidak ada data A2.</td></tr>
    <?php else: foreach ($a2Rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars((string) $row['vessel_name']) ?></td>
            <td><?= htmlspecialchars((string) ($row['call_sign'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['last_port'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['next_port'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['etd'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['eta'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['alongside_time'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['anchor_time'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['depart_time'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['vessel_type'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['agent'] ?? '-')) ?></td>
            <td><?= htmlspecialchars((string) ($row['remarks'] ?? '-')) ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php if ($a5Rows || $a6Rows || $a7Rows || $a8Rows): ?>
<div class="page-break portrait-section"></div>
<h2>Lampiran Situasi Khusus (A5-A8)</h2>
<?php endif; ?>

<?php if ($a5Rows): ?>
<h3>A5 Pra-Kedatangan</h3>
<table><thead><tr><th>Kapal</th><th>IMO</th><th>GT</th><th>LOA</th><th>Draft</th><th>POB</th><th>ETA</th><th>Muatan</th><th>Catatan</th></tr></thead><tbody>
<?php foreach ($a5Rows as $row): ?><tr>
<td><?= htmlspecialchars((string) $row['vessel_name']) ?></td>
<td><?= htmlspecialchars((string) ($row['imo_number'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['gross_tonnage'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['loa_m'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['draft_m'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['pob_count'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) $row['expected_arrival']) ?></td>
<td><?= htmlspecialchars((string) ($row['cargo_details'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['special_notes'] ?? '-')) ?></td>
</tr><?php endforeach; ?>
</tbody></table>
<?php endif; ?>

<?php if ($a6Rows): ?>
<h3>A6 Insiden</h3>
<table><thead><tr><th>Waktu</th><th>Judul</th><th>Kronologi</th><th>Korban Meninggal/Hilang</th><th>Lokasi Polusi</th><th>Pihak Dihubungi</th><th>Tindakan</th></tr></thead><tbody>
<?php foreach ($a6Rows as $row): ?><tr>
<td><?= htmlspecialchars((string) $row['incident_datetime']) ?></td>
<td><?= htmlspecialchars((string) $row['title']) ?></td>
<td><?= htmlspecialchars((string) $row['chronology']) ?></td>
<td><?= (int) $row['deaths_count'] ?> / <?= (int) $row['missing_count'] ?></td>
<td><?= htmlspecialchars((string) ($row['pollution_location'] ?? '-')) ?></td>
<td><?= htmlspecialchars(implode(', ', json_decode((string) ($row['authorities_notified'] ?? '[]'), true) ?: [])) ?></td>
<td><?= htmlspecialchars((string) ($row['immediate_action'] ?? '-')) ?></td>
</tr><?php endforeach; ?>
</tbody></table>
<?php endif; ?>

<?php if ($a7Rows): ?>
<h3>A7 Operasi Khusus</h3>
<table><thead><tr><th>Operasi</th><th>Mulai</th><th>Selesai</th><th>Sektor</th><th>Lokasi</th><th>Uraian Kejadian</th><th>Detail Prosedur</th><th>Hasil</th></tr></thead><tbody>
<?php foreach ($a7Rows as $row): ?><tr>
<td><?= htmlspecialchars((string) $row['operation_name']) ?></td>
<td><?= htmlspecialchars((string) $row['operation_start']) ?></td>
<td><?= htmlspecialchars((string) ($row['operation_end'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['sector'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['location'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['event_description'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['operation_details'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['outcome_notes'] ?? '-')) ?></td>
</tr><?php endforeach; ?>
</tbody></table>
<?php endif; ?>

<?php if ($a8Rows): ?>
<h3>A8 Pelanggaran</h3>
<table><thead><tr><th>Kapal</th><th>Pelanggaran</th><th>Waktu</th><th>Lokasi</th><th>Rujukan Hukum</th><th>Peringatan</th><th>Tindakan</th></tr></thead><tbody>
<?php foreach ($a8Rows as $row): ?><tr>
<td><?= htmlspecialchars((string) $row['vessel_name']) ?></td>
<td><?= htmlspecialchars((string) $row['violation_type']) ?></td>
<td><?= htmlspecialchars((string) $row['violation_datetime']) ?></td>
<td><?= htmlspecialchars((string) ($row['location'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['legal_reference'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['warning_type'] ?? '-')) ?></td>
<td><?= htmlspecialchars((string) ($row['action_taken'] ?? '-')) ?></td>
</tr><?php endforeach; ?>
</tbody></table>
<?php endif; ?>
</body>
</html>
<?php
$html = (string) ob_get_clean();

$autoloadCandidates = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];
$autoloadPath = null;
foreach ($autoloadCandidates as $candidate) {
    if (is_file($candidate)) {
        $autoloadPath = $candidate;
        break;
    }
}

if ($autoloadPath === null) {
    $_SESSION['flash_error'] = 'Dompdf belum terpasang. Jalankan: composer require dompdf/dompdf';
    redirect('/public/dashboard.php');
}

require_once $autoloadPath;

if (!class_exists(\Dompdf\Dompdf::class)) {
    $_SESSION['flash_error'] = 'Library Dompdf tidak ditemukan setelah autoload.';
    redirect('/public/dashboard.php');
}

$dompdf = new \Dompdf\Dompdf([
    'isRemoteEnabled' => true,
    'defaultPaperSize' => 'a4',
]);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'VTS-OPS-LOG-' . $report['shift_date'] . '-' . $report['shift_category'] . '-Team-' . $report['team'] . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
echo $dompdf->output();
exit;

} catch (Throwable $pdfEx) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    logError('export_pdf render: ' . $pdfEx->getMessage());
    http_response_code(500);
    $errMsg = htmlspecialchars($pdfEx->getMessage());
    echo <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gagal Buat PDF — VTS OPS-LOG</title>
    <style>
        body{font-family:Inter,system-ui,sans-serif;background:#0d1117;color:#e2e8f0;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:2rem}
        .card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:2.5rem;max-width:560px;width:100%}
        h1{color:#f87171;font-size:1.25rem;margin:0 0 .75rem;display:flex;align-items:center;gap:.5rem}
        pre{background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.08);border-radius:.5rem;padding:1rem;font-size:11px;overflow-x:auto;color:#fca5a5;white-space:pre-wrap}
        a{display:inline-block;margin-top:1.5rem;padding:.6rem 1.25rem;border-radius:.5rem;background:#FECB00;color:#001E40;font-weight:700;text-decoration:none;font-size:.85rem}
        .hint{font-size:.75rem;color:#94a3b8;margin-top:1rem;line-height:1.6}
    </style>
</head>
<body>
    <div class="card">
        <h1>&#10007; Gagal Membuat PDF</h1>
        <p style="font-size:.85rem;color:#94a3b8">Proses rendering PDF mengalami kesalahan. Kemungkinan penyebab:</p>
        <ul class="hint">
            <li>Dompdf belum terinstall &mdash; jalankan <code>composer install</code></li>
            <li>Memory limit PHP terlalu rendah &mdash; tambahkan <code>memory_limit = 256M</code> di php.ini</li>
            <li>Asset gambar tidak dapat diakses oleh Dompdf (path lokal)</li>
        </ul>
        <pre>$errMsg</pre>
        <a href="javascript:history.back()">&#8592; Kembali ke Dashboard</a>
    </div>
</body>
</html>
HTML;
    exit;
}
