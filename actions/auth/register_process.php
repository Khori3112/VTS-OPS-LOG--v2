<?php
// actions/auth/register_process.php — Sovereign Engine v5.1
declare(strict_types=1);
require_once __DIR__ . '/../../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/public/register.php');
}

verifyCsrfOrDie('/public/register.php');

$fullName        = trim($_POST['full_name']        ?? '');
$nip             = trim($_POST['nip']               ?? '');
$role            = trim($_POST['role']              ?? '');
$team            = trim($_POST['team']              ?? '');
$password        = (string) ($_POST['password']         ?? '');
$passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

// ── Input validation ──────────────────────────────────────────────────────
if ($fullName === '' || $nip === '' || $role === '' || $password === '') {
    $_SESSION['flash_error'] = 'Semua field wajib diisi.';
    redirect('/public/register.php');
}

if (strlen($nip) > 30) {
    $_SESSION['flash_error'] = 'NIP maksimal 30 karakter.';
    redirect('/public/register.php');
}

if (!in_array($role, ['Operator', 'Supervisor', 'Manager'], true)) {
    $_SESSION['flash_error'] = 'Role tidak valid.';
    redirect('/public/register.php');
}

// Team required only for Operator
$teamValue = null;
if ($role === 'Operator') {
    if (!in_array($team, ['A','B','C','D','E'], true)) {
        $_SESSION['flash_error'] = 'Tim wajib dipilih untuk role Operator.';
        redirect('/public/register.php');
    }
    $teamValue = $team;
}

if (strlen($password) < 8) {
    $_SESSION['flash_error'] = 'Password minimal 8 karakter.';
    redirect('/public/register.php');
}

if ($password !== $passwordConfirm) {
    $_SESSION['flash_error'] = 'Konfirmasi password tidak cocok.';
    redirect('/public/register.php');
}

try {
    $pdo = getPdo();

    // Check NIP uniqueness
    $dup = $pdo->prepare('SELECT COUNT(*) FROM users WHERE nip = :nip');
    $dup->execute([':nip' => $nip]);
    if ((int) $dup->fetchColumn() > 0) {
        $_SESSION['flash_error'] = 'NIP ' . htmlspecialchars($nip) . ' sudah terdaftar.';
        redirect('/public/register.php');
    }

    // Hash with Argon2id — is_active = 1 (instant active, demo mode)
    $hash = password_hash($password, PASSWORD_ARGON2ID);

    $pdo->beginTransaction();

    $pdo->prepare(
        'INSERT INTO users (nip, full_name, password_hash, role, team, is_active)
         VALUES (:nip, :full_name, :hash, :role, :team, 1)'
    )->execute([
        ':nip'       => $nip,
        ':full_name' => $fullName,
        ':hash'      => $hash,
        ':role'      => $role,
        ':team'      => $teamValue,
    ]);

    // Audit log — use 'REGISTER' action type
    $pdo->prepare(
        'INSERT INTO audit_logs (actor_nip, action_type, table_name, record_id, new_data, description, logged_at)
         VALUES (:nip, "REGISTER", "users", NULL, :data, "New user registered — instant active (demo mode)", NOW())'
    )->execute([
        ':nip'  => $nip,
        ':data' => json_encode([
            'full_name' => $fullName,
            'role'      => $role,
            'status'    => 'active',
            'team'      => $teamValue,
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
        ], JSON_UNESCAPED_UNICODE),
    ]);

    $pdo->commit();

    $_SESSION['flash_success'] = 'Registrasi berhasil! Akun Anda sudah aktif — silakan login sekarang.';
    redirect('/public/login.php');

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    logError('register_process: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    redirect('/public/register.php');
}