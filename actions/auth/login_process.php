<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/public/login.php');
}

verifyCsrfOrDie('/public/login.php');

$nip = trim($_POST['nip'] ?? '');
$password = $_POST['password'] ?? '';

if ($nip === '' || $password === '') {
    $_SESSION['flash_error'] = 'NIP dan password wajib diisi.';
    redirect('/public/login.php');
}

try {
    $pdo = getPdo();
    $stmt = $pdo->prepare('SELECT nip, full_name, password_hash, role, team, is_active FROM users WHERE nip = :nip LIMIT 1');
    $stmt->execute([':nip' => $nip]);
    $user = $stmt->fetch();

    if (!$user || (int) $user['is_active'] !== 1 || !password_verify($password, (string) $user['password_hash'])) {
        $_SESSION['flash_error'] = 'Login gagal. Cek NIP/password.';
        redirect('/public/login.php');
    }

    $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
    $shiftCategory = currentShiftCategory($now);
    $shiftDate = operationalShiftDate($now);

    // Admin role: skip attendance + shift report logic (no team assignment)
    if ($user['role'] === 'Admin') {
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['role'] = 'Admin';
        $_SESSION['user'] = [
            'nip'       => $user['nip'],
            'full_name' => $user['full_name'],
            'role'      => 'Admin',
            'team'      => null,
            'login_shift_date'     => $shiftDate,
            'login_shift_category' => $shiftCategory,
            'daily_shift_report_id' => null,
        ];
        $_SESSION['login_at'] = time();
        try {
            $pdo->prepare(
                'INSERT INTO audit_logs (actor_nip,action_type,table_name,record_id,new_data,description,logged_at)
                 VALUES (:nip,"LOGIN",NULL,NULL,:data,"Admin login",NOW())'
            )->execute([':nip' => $user['nip'], ':data' => json_encode(['ip' => $_SERVER['REMOTE_ADDR'] ?? null])]);
        } catch (Throwable) { /* non-critical */ }
        header('Location: ' . BASE_URL . 'admin/dashboard.php');
        exit;
    }

    $pdo->beginTransaction();

    $attendanceStmt = $pdo->prepare(
        'INSERT INTO attendance_logs (user_nip, login_at, login_date, shift_category, team, source_ip, user_agent)
         VALUES (:user_nip, :login_at, :login_date, :shift_category, :team, :source_ip, :user_agent)'
    );

    $attendanceStmt->execute([
        ':user_nip' => $user['nip'],
        ':login_at' => $now->format('Y-m-d H:i:s'),
        ':login_date' => $shiftDate,
        ':shift_category' => $shiftCategory,
        ':team' => $user['team'],
        ':source_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ]);

    $dailyShiftReportId = upsertDailyShiftReport($pdo, $user, $shiftDate, $shiftCategory);

    logAudit(
        $pdo,
        (string) $user['nip'],
        'LOGIN',
        'attendance_logs',
        (int) $pdo->lastInsertId(),
        [
            'shift_date' => $shiftDate,
            'shift_category' => $shiftCategory,
            'team' => $user['team'],
            'daily_shift_report_id' => $dailyShiftReportId,
        ],
        'User login and attendance recorded automatically'
    );

    $pdo->commit();

    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $_SESSION['role'] = $user['role'];
    $_SESSION['user'] = [
        'nip' => $user['nip'],
        'full_name' => $user['full_name'],
        'role' => $user['role'],
        'team' => $user['team'],
        'login_shift_date' => $shiftDate,
        'login_shift_category' => $shiftCategory,
        'daily_shift_report_id' => $dailyShiftReportId,
    ];
    $_SESSION['login_at'] = time();

    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem saat login.';
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
