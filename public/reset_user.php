<?php
/**
 * Emergency Credential Reset Script
 * -----------------------------------------------------------------
 * Hanya boleh diakses oleh akun yang sudah diautentikasi dengan role Admin.
 * Operasi ini hanya menggunakan POST dan CSRF token untuk menghindari
 * eksekusi tidak sengaja.
 * -----------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

if (empty($_SESSION['user'])) {
    http_response_code(403);
    die('403 Forbidden — Anda harus login sebagai Admin untuk mengakses halaman ini.');
}

$userRole = (string) ($_SESSION['user']['role'] ?? '');
if ($userRole !== 'Admin') {
    http_response_code(403);
    die('403 Forbidden — Akses terbatas hanya untuk role Admin.');
}

$result = null;
$errors = [];
$generatedPasswords = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrDie('/public/reset_user.php');

    if (($_POST['confirm'] ?? '') === 'yes') {
        try {
            $pdo = getPdo();
            $tables = [
                'audit_logs',
                'edit_requests',
                'contravention_reports',
                'special_ops_reports',
                'incident_reports',
                'pre_arrival_reports',
                'handover_reports',
                'tide_reports',
                'weather_observations',
                'weather_reports',
                'vts_logs',
                'vessel_traffic',
                'attendance_logs',
                'daily_shift_reports',
                'users',
            ];

            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($tables as $table) {
                $pdo->exec("TRUNCATE TABLE `{$table}`");
            }
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

            $accounts = [
                [
                    'nip'           => '197505021997032001',
                    'full_name'     => 'Merry Dhani Anitasari',
                    'jabatan'       => 'Kepala Sub-seksi VTS',
                    'role'          => 'Manager',
                    'team'          => 'A',
                ],
                [
                    'nip'           => '198003122007121001',
                    'full_name'     => 'Ria Irawan, S.Pd',
                    'jabatan'       => 'Penjaga Jaga (Watching Keeper)',
                    'role'          => 'Supervisor',
                    'team'          => 'A',
                ],
            ];

            $stmt = $pdo->prepare(
                'INSERT INTO users (nip, full_name, jabatan, password_hash, role, team, is_active)
                 VALUES (:nip, :full_name, :jabatan, :password_hash, :role, :team, 1)'
            );

            foreach ($accounts as $account) {
                $password = generateRandomPassword(16);
                $stmt->execute([
                    ':nip'           => $account['nip'],
                    ':full_name'     => $account['full_name'],
                    ':jabatan'       => $account['jabatan'],
                    ':password_hash' => password_hash($password, PASSWORD_ARGON2ID),
                    ':role'          => $account['role'],
                    ':team'          => $account['team'],
                ]);

                $generatedPasswords[] = [
                    'full_name' => $account['full_name'],
                    'nip'       => $account['nip'],
                    'role'      => $account['role'],
                    'password'  => $password,
                ];
            }

            $result = 'success';
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
            $result = 'error';
            try {
                if (isset($pdo)) {
                    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
                }
            } catch (Throwable) {
                // ignore cleanup failure
            }
        }
    }
}

function generateRandomPassword(int $length = 16): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*()-_=+';
    $max = strlen($chars) - 1;
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }
    return $password;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Reset — VTS OPS-LOG</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; min-height: 100vh;
               display: flex; align-items: center; justify-content: center; margin: 0; padding: 16px; }
        .card { background: #fff; border-radius: 12px; padding: 40px; max-width: 640px; width: 100%;
                box-shadow: 0 4px 24px rgba(0,0,0,.1); }
        h1 { margin: 0 0 4px; font-size: 1.4rem; color: #0f172a; }
        .badge-danger { display: inline-block; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;
                        font-size: .7rem; font-weight: 700; padding: 2px 8px; border-radius: 99px;
                        text-transform: uppercase; letter-spacing: .05em; margin-bottom: 20px; }
        p { color: #64748b; font-size: .9rem; line-height: 1.6; margin: 0 0 16px; }
        .warn-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px;
                    padding: 14px 16px; margin-bottom: 24px; }
        .warn-box ul { margin: 8px 0 0; padding-left: 18px; color: #c2410c; font-size: .85rem; }
        .warn-box ul li { margin-bottom: 4px; }
        .user-list { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
                     padding: 16px; margin-bottom: 24px; }
        .user-list table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        .user-list th { text-align: left; color: #94a3b8; font-weight: 600; padding-bottom: 8px;
                        border-bottom: 1px solid #e2e8f0; }
        .user-list td { padding: 6px 0; color: #1e293b; vertical-align: top; }
        .role-tag { display: inline-block; padding: 1px 8px; border-radius: 99px; font-size: .75rem;
                    font-weight: 600; }
        .role-manager { background: #fef9c3; color: #854d0e; }
        .role-supervisor { background: #e0f2fe; color: #0369a1; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; font-size: .95rem;
               font-weight: 600; text-decoration: none; cursor: pointer; border: none; width: 100%;
               text-align: center; transition: opacity .15s; }
        .btn:hover { opacity: .88; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-back  { background: #e2e8f0; color: #334155; margin-top: 10px; }
        .alert { border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; font-size: .9rem; font-weight: 500; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: .85rem; }
    </style>
</head>
<body>
<div class="card">
    <div class="badge-danger">⚠ Emergency Only</div>
    <h1>Reset Kredensial Darurat</h1>
    <p>Operasi ini hanya dapat dilakukan oleh Admin dan tidak dapat dibatalkan.</p>

    <?php if ($result === 'success'): ?>
        <div class="alert alert-success">
            ✅ Reset berhasil! Database telah dikosongkan dan akun baru telah dibuat.
        </div>

        <div class="user-list">
            <table>
                <tr><th>Nama</th><th>NIP</th><th>Role</th><th>Password</th></tr>
                <?php foreach ($generatedPasswords as $account): ?>
                    <tr>
                        <td><?= htmlspecialchars($account['full_name']) ?></td>
                        <td><code><?= htmlspecialchars($account['nip']) ?></code></td>
                        <td><span class="role-tag <?= $account['role'] === 'Manager' ? 'role-manager' : 'role-supervisor' ?>"><?= htmlspecialchars($account['role']) ?></span></td>
                        <td><code><?= htmlspecialchars($account['password']) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <p style="color:#b91c1c;font-weight:600;">🔴 Setelah reset, segera ubah password di akun Manager dan Supervisor.</p>
        <a class="btn btn-back" href="<?= BASE_URL ?>login.php">← Kembali ke Login</a>
    <?php elseif ($result === 'error'): ?>
        <div class="alert alert-error">
            ❌ Reset gagal: <?= htmlspecialchars(implode('; ', $errors)) ?>
        </div>
        <a class="btn btn-back" href="<?= BASE_URL ?>login.php">← Kembali ke Login</a>
    <?php else: ?>
        <div class="warn-box">
            <strong style="color:#9a3412;">Perhatian:</strong>
            <ul>
                <li>Semua data laporan, aktivitas, dan log akan dihapus secara permanen.</li>
                <li>Hanya lanjutkan jika tidak ada opsi lain untuk memulihkan akses.</li>
                <li>Token CSRF digunakan untuk mencegah eksekusi tidak sengaja.</li>
            </ul>
        </div>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>" />
            <input type="hidden" name="confirm" value="yes" />
            <button type="submit" class="btn btn-danger">🗑 Ya, Hapus Semua Data & Reset Akun</button>
        </form>
        <a class="btn btn-back" href="<?= BASE_URL ?>login.php">← Batal, Kembali ke Login</a>
    <?php endif; ?>
</div>
</body>
</html>
