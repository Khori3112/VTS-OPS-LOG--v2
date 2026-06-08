<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Manager');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/public/manager_approval.php');
}

verifyCsrfOrDie('/views/manager/manager_approval.php');

$nip      = trim($_POST['nip']       ?? '');
$fullName = trim($_POST['full_name'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$role     = trim($_POST['role']      ?? '');
$team     = trim($_POST['team']      ?? '');
$jabatan  = trim($_POST['jabatan']   ?? '');

if ($nip === '' || $fullName === '' || $password === '' || !in_array($role, ['Operator', 'Supervisor', 'Manager'], true) || !in_array($team, ['A', 'B', 'C', 'D', 'E'], true)) {
    $_SESSION['flash_error'] = 'Data user baru tidak valid.';
    redirect('/public/manager_approval.php');
}

try {
    $pdo = getPdo();
    $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

    $stmt = $pdo->prepare(
        'INSERT INTO users (nip, full_name, jabatan, password_hash, role, team, is_active)
         VALUES (:nip, :full_name, :jabatan, :password_hash, :role, :team, 1)
         ON DUPLICATE KEY UPDATE
            full_name     = VALUES(full_name),
            jabatan       = VALUES(jabatan),
            password_hash = VALUES(password_hash),
            role          = VALUES(role),
            team          = VALUES(team)'
    );
    $stmt->execute([
        ':nip'           => $nip,
        ':full_name'     => $fullName,
        ':jabatan'       => $jabatan !== '' ? $jabatan : null,
        ':password_hash' => $passwordHash,
        ':role'          => $role,
        ':team'          => $team,
    ]);

    logAudit(
        $pdo,
        (string) ($_SESSION['user']['nip'] ?? $nip),
        'CREATE',
        'users',
        null,
        ['nip' => $nip, 'role' => $role, 'team' => $team, 'jabatan' => $jabatan],
        'Manager created/updated user with Argon2id password hash'
    );

    $_SESSION['flash_success'] = 'User berhasil dibuat/diperbarui dengan password Argon2id.';
    redirect('/public/manager_approval.php');
} catch (Throwable $e) {
    $_SESSION['flash_error'] = 'Gagal membuat user baru.';
    redirect('/public/manager_approval.php');
}
