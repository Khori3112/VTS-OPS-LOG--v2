<?php
// actions/admin/user_crud.php — ADMIN COMMAND CENTER · Sovereign Engine v5.1
// Handles: add_user | edit_user | delete_user | reset_password | dismiss_reset
declare(strict_types=1);
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/public/admin/dashboard.php');
}

verifyCsrfOrDie('/public/admin/dashboard.php');

$action  = trim($_POST['action'] ?? '');
$adminNip = (string) ($_SESSION['user']['nip'] ?? 'SYSTEM');

// Default password for password resets
const DEFAULT_PASSWORD = 'VTS@12345';

try {
    $pdo = getPdo();

    switch ($action) {

        //── ADD USER ────────────────────────────────────────────────────
        case 'add_user': {
            $fullName = trim($_POST['full_name'] ?? '');
            $nip      = trim($_POST['nip']       ?? '');
            $role     = trim($_POST['role']       ?? '');
            $team     = trim($_POST['team']       ?? '');
            $password = (string) ($_POST['password'] ?? '');

            if ($fullName === '' || $nip === '' || $role === '' || $password === '') {
                $_SESSION['flash_error'] = 'Semua field wajib diisi.';
                redirect('/public/admin/dashboard.php');
            }
            if (!in_array($role, ['Operator','Supervisor','Manager','Admin'], true)) {
                $_SESSION['flash_error'] = 'Role tidak valid.';
                redirect('/public/admin/dashboard.php');
            }
            $teamValue = null;
            if ($role === 'Operator') {
                if (!in_array($team, ['A','B','C','D','E'], true)) {
                    $_SESSION['flash_error'] = 'Tim wajib dipilih untuk Operator.';
                    redirect('/public/admin/dashboard.php');
                }
                $teamValue = $team;
            }
            if (strlen($password) < 8) {
                $_SESSION['flash_error'] = 'Password minimal 8 karakter.';
                redirect('/public/admin/dashboard.php');
            }

            // Uniqueness check
            $dup = $pdo->prepare('SELECT COUNT(*) FROM users WHERE nip = :nip');
            $dup->execute([':nip' => $nip]);
            if ((int) $dup->fetchColumn() > 0) {
                $_SESSION['flash_error'] = 'NIP ' . htmlspecialchars($nip) . ' sudah terdaftar.';
                redirect('/public/admin/dashboard.php');
            }

            $hash = password_hash($password, PASSWORD_ARGON2ID);

            $pdo->beginTransaction();
            $pdo->prepare(
                'INSERT INTO users (nip, full_name, password_hash, role, team, is_active)
                 VALUES (:nip, :full_name, :hash, :role, :team, 1)'
            )->execute([':nip' => $nip, ':full_name' => $fullName, ':hash' => $hash, ':role' => $role, ':team' => $teamValue]);

            $pdo->prepare(
                'INSERT INTO audit_logs (actor_nip,action_type,table_name,record_id,new_data,description,logged_at)
                 VALUES (:a,"CREATE","users",NULL,:d,"Admin created new user",NOW())'
            )->execute([':a' => $adminNip, ':d' => json_encode(['nip' => $nip, 'role' => $role], JSON_UNESCAPED_UNICODE)]);

            $pdo->commit();
            $_SESSION['flash_success'] = "User {$fullName} (NIP: {$nip}) berhasil ditambahkan.";
            redirect('/public/admin/dashboard.php');
        }

        //── EDIT USER ───────────────────────────────────────────────────
        case 'edit_user': {
            $nip      = trim($_POST['nip']       ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $role     = trim($_POST['role']       ?? '');
            $team     = trim($_POST['team']       ?? '');
            $isActive = (int) ($_POST['is_active'] ?? 0);

            if ($nip === '' || $fullName === '' || $role === '') {
                $_SESSION['flash_error'] = 'Data tidak lengkap.';
                redirect('/public/admin/dashboard.php');
            }
            if (!in_array($role, ['Operator','Supervisor','Manager','Admin'], true)) {
                $_SESSION['flash_error'] = 'Role tidak valid.';
                redirect('/public/admin/dashboard.php');
            }
            $teamValue = ($role === 'Operator' && in_array($team, ['A','B','C','D','E'], true)) ? $team : null;

            // Fetch old data for audit
            $old = $pdo->prepare('SELECT role, team, is_active FROM users WHERE nip = :nip LIMIT 1');
            $old->execute([':nip' => $nip]);
            $oldRow = $old->fetch() ?: [];

            $pdo->beginTransaction();
            $pdo->prepare(
                'UPDATE users SET full_name=:fn, role=:role, team=:team, is_active=:active WHERE nip=:nip'
            )->execute([':fn' => $fullName, ':role' => $role, ':team' => $teamValue, ':active' => $isActive, ':nip' => $nip]);

            $pdo->prepare(
                'INSERT INTO audit_logs (actor_nip,action_type,table_name,record_id,old_data,new_data,description,logged_at)
                 VALUES (:a,"UPDATE","users",NULL,:old,:new,"Admin updated user",NOW())'
            )->execute([
                ':a'   => $adminNip,
                ':old' => json_encode($oldRow, JSON_UNESCAPED_UNICODE),
                ':new' => json_encode(['role' => $role, 'team' => $teamValue, 'is_active' => $isActive], JSON_UNESCAPED_UNICODE),
            ]);

            $pdo->commit();
            $_SESSION['flash_success'] = "User NIP {$nip} berhasil diperbarui.";
            redirect('/public/admin/dashboard.php');
        }

        //── DELETE USER ─────────────────────────────────────────────────
        case 'delete_user': {
            $nip = trim($_POST['nip'] ?? '');
            if ($nip === '') {
                $_SESSION['flash_error'] = 'NIP tidak ditemukan.';
                redirect('/public/admin/dashboard.php');
            }
            // Prevent self-deletion
            if ($nip === $adminNip) {
                $_SESSION['flash_error'] = 'Tidak dapat menghapus akun sendiri.';
                redirect('/public/admin/dashboard.php');
            }

            $pdo->beginTransaction();

            // Remove FK-referenced records to allow deletion
            // Nullify FKs in daily_shift_reports (supervisor/manager columns)
            $pdo->prepare("UPDATE daily_shift_reports SET supervisor_nip=NULL WHERE supervisor_nip=:nip")->execute([':nip' => $nip]);
            $pdo->prepare("UPDATE daily_shift_reports SET manager_nip=NULL  WHERE manager_nip=:nip")   ->execute([':nip' => $nip]);

            // Delete edit_requests where this user is involved
            $pdo->prepare("DELETE FROM edit_requests WHERE requested_by_nip=:nip OR requested_to_nip=:nip")->execute([':nip' => $nip]);

            // Log before deleting
            $pdo->prepare(
                'INSERT INTO audit_logs (actor_nip,action_type,table_name,record_id,new_data,description,logged_at)
                 VALUES (:a,"DELETE","users",NULL,:d,"Admin deleted user",NOW())'
            )->execute([':a' => $adminNip, ':d' => json_encode(['deleted_nip' => $nip])]);

            $pdo->prepare("DELETE FROM users WHERE nip=:nip")->execute([':nip' => $nip]);

            $pdo->commit();
            $_SESSION['flash_success'] = "User NIP {$nip} berhasil dihapus.";
            redirect('/public/admin/dashboard.php');
        }

        //── RESET PASSWORD ───────────────────────────────────────────────
        case 'reset_password': {
            $nip = trim($_POST['nip'] ?? '');
            if ($nip === '') {
                $_SESSION['flash_error'] = 'NIP tidak ditemukan.';
                redirect('/public/admin/dashboard.php');
            }

            $hash = password_hash(DEFAULT_PASSWORD, PASSWORD_ARGON2ID);

            $pdo->beginTransaction();

            $pdo->prepare("UPDATE users SET password_hash=:hash WHERE nip=:nip")
                ->execute([':hash' => $hash, ':nip' => $nip]);

            // Mark any pending password_reset requests for this NIP as resolved
            $pdo->prepare(
                "UPDATE edit_requests SET status='approved', resolved_at=NOW()
                 WHERE table_name='password_reset' AND requested_by_nip=:nip AND status='pending'"
            )->execute([':nip' => $nip]);

            $pdo->prepare(
                'INSERT INTO audit_logs (actor_nip,action_type,table_name,record_id,new_data,description,logged_at)
                 VALUES (:a,"PASSWORD_RESET","users",NULL,:d,"Admin reset user password to default",NOW())'
            )->execute([':a' => $adminNip, ':d' => json_encode(['target_nip' => $nip])]);

            $pdo->commit();
            $_SESSION['flash_success'] = "Password NIP {$nip} direset ke default: " . DEFAULT_PASSWORD;
            redirect('/public/admin/dashboard.php');
        }

        //── DISMISS RESET REQUEST ───────────────────────────────────────
        case 'dismiss_reset': {
            $requestId = (int) ($_POST['request_id'] ?? 0);
            if ($requestId <= 0) {
                redirect('/public/admin/dashboard.php');
            }
            $pdo->prepare(
                "UPDATE edit_requests SET status='rejected', resolved_at=NOW() WHERE id=:id AND table_name='password_reset'"
            )->execute([':id' => $requestId]);

            $_SESSION['flash_success'] = 'Permintaan reset password diabaikan.';
            redirect('/public/admin/dashboard.php');
        }

        default:
            redirect('/public/admin/dashboard.php');
    }

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    logError('user_crud [' . $action . ']: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem: ' . htmlspecialchars($e->getMessage());
    redirect('/public/admin/dashboard.php');
}
