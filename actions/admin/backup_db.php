<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Manager');

// ── Rate-limit: one backup per 60 s ──────────────────────────────────────────
$lastTs = (int) ($_SESSION['backup_last_ts'] ?? 0);
if ($lastTs > 0 && (time() - $lastTs) < 60) {
    $_SESSION['flash_error'] = 'Tunggu 60 detik sebelum melakukan backup berikutnya.';
    redirect('/public/manager_approval.php');
}
$_SESSION['backup_last_ts'] = time();

// ── Log rotation: truncate logs/error.log if > 5 MB ─────────────────────────
$logFile  = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log';
if (is_file($logFile) && filesize($logFile) > 5 * 1024 * 1024) {
    $archive = dirname($logFile) . DIRECTORY_SEPARATOR . 'error.' . date('Ymd_His') . '.log';
    @rename($logFile, $archive);
    @file_put_contents($logFile, '');
}

// ── Collect DB credentials from loaded ENV ───────────────────────────────────
$dbHost = (string) ($_ENV['DB_HOST'] ?? '127.0.0.1');
$dbPort = (string) ($_ENV['DB_PORT'] ?? '3306');
$dbName = (string) ($_ENV['DB_NAME'] ?? 'vts_ops_log');
$dbUser = (string) ($_ENV['DB_USER'] ?? '');
$dbPass = (string) ($_ENV['DB_PASS'] ?? '');

if ($dbUser === '') {
    $_SESSION['flash_error'] = 'Konfigurasi DB tidak ditemukan. Periksa file .env';
    redirect('/public/manager_approval.php');
}

// ── Locate mysqldump ──────────────────────────────────────────────────────────
$candidates = [
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    'C:\\XAMPP\\mysql\\bin\\mysqldump.exe',
    'D:\\xampp\\mysql\\bin\\mysqldump.exe',
    'D:\\XAMPP\\mysql\\bin\\mysqldump.exe',
    'mysqldump',   // fallback: hope it's in PATH
];
$mysqldump = '';
foreach ($candidates as $c) {
    if ($c === 'mysqldump' || is_executable($c)) {
        $mysqldump = $c;
        break;
    }
}
if ($mysqldump === '') {
    $_SESSION['flash_error'] = 'mysqldump tidak ditemukan. Pastikan XAMPP MySQL ada di PATH.';
    redirect('/public/manager_approval.php');
}

// ── Write temporary .my.cnf (password never appears in process list) ─────────
$tempDir = sys_get_temp_dir();
$confFile = $tempDir . DIRECTORY_SEPARATOR . 'vts_bk_' . bin2hex(random_bytes(8)) . '.cnf';
$sqlFile  = $tempDir . DIRECTORY_SEPARATOR . 'vts_backup_' . date('Ymd_His') . '.sql';
$zipFile  = $tempDir . DIRECTORY_SEPARATOR . 'vts_backup_' . date('Ymd_His') . '.zip';

$cnfContent = "[client]\nhost=" . $dbHost . "\nport=" . $dbPort
            . "\nuser=" . $dbUser . "\npassword=" . $dbPass . "\n";

if (file_put_contents($confFile, $cnfContent, LOCK_EX) === false) {
    $_SESSION['flash_error'] = 'Gagal membuat file konfigurasi sementara untuk backup.';
    redirect('/public/manager_approval.php');
}
chmod($confFile, 0600);

// ── Execute mysqldump ─────────────────────────────────────────────────────────
$cmd = sprintf(
    '%s --defaults-extra-file=%s --single-transaction --routines --triggers --set-gtid-purged=OFF %s > %s 2>&1',
    escapeshellarg($mysqldump),
    escapeshellarg($confFile),
    escapeshellarg($dbName),
    escapeshellarg($sqlFile)
);

$retCode = 0;
system($cmd, $retCode);
@unlink($confFile); // remove credentials file immediately

if ($retCode !== 0 || !is_file($sqlFile) || filesize($sqlFile) === 0) {
    @unlink($sqlFile);
    $_SESSION['flash_error'] = 'mysqldump gagal (exit code ' . $retCode . '). Periksa konfigurasi XAMPP.';
    redirect('/public/manager_approval.php');
}

// ── Zip the SQL dump ──────────────────────────────────────────────────────────
if (!class_exists('ZipArchive')) {
    // Fallback: offer plain .sql download
    $filename = 'vts_backup_' . date('Ymd_His') . '.sql';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($sqlFile));
    header('Cache-Control: no-store, no-cache');
    readfile($sqlFile);
    @unlink($sqlFile);
    exit;
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    @unlink($sqlFile);
    $_SESSION['flash_error'] = 'Gagal membuat file ZIP backup.';
    redirect('/public/manager_approval.php');
}
$zip->addFile($sqlFile, $dbName . '_' . date('Ymd_His') . '.sql');
$zip->close();
@unlink($sqlFile);

if (!is_file($zipFile) || filesize($zipFile) === 0) {
    $_SESSION['flash_error'] = 'File ZIP tidak valid — backup gagal.';
    redirect('/public/manager_approval.php');
}

// ── Stream ZIP to browser ─────────────────────────────────────────────────────
$downloadName = 'vts_backup_' . $dbName . '_' . date('Ymd_His') . '.zip';
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($zipFile));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
readfile($zipFile);
@unlink($zipFile);
exit;
