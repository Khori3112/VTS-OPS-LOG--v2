<?php
/**
 * Emergency Credential Reset Script
 * -----------------------------------------------------------------
 * PERINGATAN KEAMANAN:
 *   - Script ini HANYA untuk digunakan dalam keadaan darurat.
 *   - HAPUS atau RENAME file ini setelah digunakan.
 *   - Jangan tinggalkan file ini di server produksi.
 * -----------------------------------------------------------------
 * Akses: http://localhost:8000/public/reset_user.php?token=VTS_RESET_2026
 */

declare(strict_types=1);

// ─── Secret token guard ───────────────────────────────────────────────────
// Ganti nilai ini sebelum digunakan, lalu hapus file ini setelahnya.
const RESET_TOKEN = 'VTS_RESET_2026';

if (($_GET['token'] ?? '') !== RESET_TOKEN) {
    http_response_code(403);
    die('403 Forbidden — Token tidak valid atau tidak ada.');
}

// ─── Konfirmasi eksekusi ──────────────────────────────────────────────────
$confirmed = ($_GET['confirm'] ?? '') === 'yes';

require_once __DIR__ . '/../config/database.php';

$result   = null;
$errors   = [];

if ($confirmed) {
    try {
        $pdo = getPdo();

        // Nonaktifkan foreign key checks sementara agar TRUNCATE bisa berjalan
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        // Kosongkan semua tabel yang bergantung pada users terlebih dahulu
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
        foreach ($tables as $table) {
            $pdo->exec("TRUNCATE TABLE `{$table}`");
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        // Hash password Argon2id
        $passwordHash = password_hash('admin123', PASSWORD_ARGON2ID);

        $stmt = $pdo->prepare(
            'INSERT INTO users (nip, full_name, jabatan, password_hash, role, team, is_active)
             VALUES (:nip, :full_name, :jabatan, :password_hash, :role, :team, 1)'
        );

        // Manager — Merry Dhani Anitasari
        $stmt->execute([
            ':nip'           => '197505021997032001',
            ':full_name'     => 'Merry Dhani Anitasari',
            ':jabatan'       => 'Kepala Sub-seksi VTS',
            ':password_hash' => $passwordHash,
            ':role'          => 'Manager',
            ':team'          => 'A',
        ]);

        // Supervisor — Ria Irawan
        $stmt->execute([
            ':nip'           => '198003122007121001',
            ':full_name'     => 'Ria Irawan, S.Pd',
            ':jabatan'       => 'Penjaga Jaga (Watching Keeper)',
            ':password_hash' => $passwordHash,
            ':role'          => 'Supervisor',
            ':team'          => 'A',
        ]);

        $result = 'success';

    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
        $result   = 'error';
    }
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
        .card { background: #fff; border-radius: 12px; padding: 40px; max-width: 520px; width: 100%;
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
    <p>Script ini akan menghapus seluruh data dan membuat ulang 2 akun default.</p>

    <?php if ($result === 'success'): ?>
    <div class="alert alert-success">
        ✅ Reset berhasil! Database telah dikosongkan dan 2 akun baru telah dibuat.
    </div>
    <div class="user-list">
        <table>
            <tr><th>Nama</th><th>NIP</th><th>Role</th><th>Password</th></tr>
            <tr>
                <td>Merry Dhani Anitasari</td>
                <td><code>197505021997032001</code></td>
                <td><span class="role-tag role-manager">Manager</span></td>
                <td><code>admin123</code></td>
            </tr>
            <tr>
                <td>Ria Irawan, S.Pd</td>
                <td><code>198003122007121001</code></td>
                <td><span class="role-tag role-supervisor">Supervisor</span></td>
                <td><code>admin123</code></td>
            </tr>
        </table>
    </div>
    <p style="color:#b91c1c;font-weight:600;">🔴 SEGERA hapus file <code>public/reset_user.php</code> dari server setelah ini!</p>
    <a class="btn btn-back" href="login.php">← Kembali ke Login</a>

    <?php elseif ($result === 'error'): ?>
    <div class="alert alert-error">
        ❌ Reset gagal: <?= htmlspecialchars(implode('; ', $errors)) ?>
    </div>
    <a class="btn btn-back" href="login.php">← Kembali ke Login</a>

    <?php else: ?>
    <div class="warn-box">
        <strong style="color:#9a3412;">Tindakan ini TIDAK DAPAT dibatalkan:</strong>
        <ul>
            <li>Seluruh data laporan, vessel traffic, dan log akan dihapus permanen.</li>
            <li>Seluruh akun pengguna akan dihapus dan diganti 2 akun baru.</li>
            <li>Hanya lanjutkan jika Anda benar-benar tidak bisa login dengan cara apapun.</li>
        </ul>
    </div>

    <div class="user-list">
        <p style="margin:0 0 10px;font-weight:600;color:#0f172a;">Akun yang akan dibuat:</p>
        <table>
            <tr><th>Nama</th><th>NIP</th><th>Role</th><th>Password</th></tr>
            <tr>
                <td>Merry Dhani Anitasari</td>
                <td><code>197505021997032001</code></td>
                <td><span class="role-tag role-manager">Manager</span></td>
                <td><code>admin123</code></td>
            </tr>
            <tr>
                <td>Ria Irawan, S.Pd</td>
                <td><code>198003122007121001</code></td>
                <td><span class="role-tag role-supervisor">Supervisor</span></td>
                <td><code>admin123</code></td>
            </tr>
        </table>
    </div>

    <a class="btn btn-danger"
       href="reset_user.php?token=<?= htmlspecialchars(RESET_TOKEN) ?>&confirm=yes">
        🗑 Ya, Hapus Semua Data &amp; Reset Akun
    </a>
    <a class="btn btn-back" href="login.php">← Batal, Kembali ke Login</a>
    <?php endif; ?>
</div>
</body>
</html>
