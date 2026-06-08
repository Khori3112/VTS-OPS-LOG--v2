<?php
/**
 * EMERGENCY USER SEEDER — VTS OPS-LOG Palembang
 * ─────────────────────────────────────────────
 * PERINGATAN: File ini MENGHAPUS seluruh tabel users dan menggantinya
 * dengan dua akun default. Gunakan HANYA saat darurat.
 *
 * Akses hanya dari localhost. Konfirmasi via query param: ?confirm=RESET_CONFIRMED
 *
 * URL: http://localhost/VTS-OPS-LOG/sql/emergency_reset.php?confirm=RESET_CONFIRMED
 */

declare(strict_types=1);

// ── Security: localhost only ───────────────────────────────────────────────
$remoteIp = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remoteIp, ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true)) {
    http_response_code(403);
    die('<h1>403 Forbidden</h1><p>File ini hanya dapat diakses dari localhost.</p>');
}

// ── Confirmation check ────────────────────────────────────────────────────
if (($_GET['confirm'] ?? '') !== 'RESET_CONFIRMED') {
    http_response_code(400);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8">
    <title>Emergency Reset — Konfirmasi</title>
    <style>body{font-family:sans-serif;background:#0f172a;color:#e2e8f0;padding:2rem}
    .warn{background:#7f1d1d;border:1px solid #dc2626;border-radius:8px;padding:1.5rem;margin:1rem 0}
    a{display:inline-block;margin-top:1rem;padding:.75rem 2rem;background:#dc2626;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold}
    code{background:#1e293b;padding:.2rem .4rem;border-radius:4px;font-size:.9rem}
    </style></head><body>
    <h2>⚠ Emergency User Reset</h2>
    <div class="warn">
        <strong>PERINGATAN KRITIS:</strong> Aksi ini akan:
        <ol>
            <li>Menghapus semua data di tabel <code>users</code></li>
            <li>Membuat ulang tabel users dari awal</li>
            <li>Memasukkan 2 akun default (Manager + Supervisor)</li>
        </ol>
    </div>
    <p>Untuk melanjutkan, klik tombol di bawah:</p>
    <a href="?confirm=RESET_CONFIRMED">✓ Saya mengerti, lanjutkan reset</a>
    </body></html>';
    exit;
}

// ── Load config ───────────────────────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getPdo();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $results = [];

    // ── Step 1: Drop existing users table ────────────────────────────────
    $pdo->exec('DROP TABLE IF EXISTS users');
    $results[] = '✓ Tabel users berhasil dihapus.';

    // ── Step 2: Recreate users table ──────────────────────────────────────
    $pdo->exec("
        CREATE TABLE users (
            nip           VARCHAR(30)                         NOT NULL,
            full_name     VARCHAR(120)                        NOT NULL,
            password_hash VARCHAR(255)                        NOT NULL,
            role          ENUM('Operator','Supervisor','Manager') NOT NULL,
            team          ENUM('A','B','C','D','E')           NOT NULL DEFAULT 'A',
            is_active     TINYINT(1)                          NOT NULL DEFAULT 1,
            jabatan       VARCHAR(80)                         NULL DEFAULT NULL,
            created_at    TIMESTAMP                           NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at    TIMESTAMP                           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (nip),
            UNIQUE KEY uk_users_nip (nip)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $results[] = '✓ Tabel users berhasil dibuat ulang.';

    // ── Step 3: Generate Argon2id hashes ──────────────────────────────────
    $password    = 'admin123';
    $hashOptions = [
        'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
        'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
        'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS,
    ];
    $hash = password_hash($password, PASSWORD_ARGON2ID, $hashOptions);

    // ── Step 4: Insert default users ──────────────────────────────────────
    $stmt = $pdo->prepare("
        INSERT INTO users (nip, full_name, password_hash, role, team, is_active, jabatan)
        VALUES (:nip, :full_name, :hash, :role, :team, 1, :jabatan)
    ");

    // Manager: Merry Dhani Anitasari
    $stmt->execute([
        ':nip'       => '197505021997032001',
        ':full_name' => 'Merry Dhani Anitasari',
        ':hash'      => $hash,
        ':role'      => 'Manager',
        ':team'      => 'A',
        ':jabatan'   => 'Kepala Sub-seksi VTS',
    ]);
    $results[] = '✓ User Manager: Merry Dhani Anitasari (NIP 197505021997032001) berhasil dibuat.';

    // Supervisor: Ria Irawan, S.Pd
    $stmt->execute([
        ':nip'       => '198003122007121001',
        ':full_name' => 'Ria Irawan, S.Pd',
        ':hash'      => $hash,
        ':role'      => 'Supervisor',
        ':team'      => 'A',
        ':jabatan'   => 'Penjaga Jaga (Watching Keeper)',
    ]);
    $results[] = '✓ User Supervisor: Ria Irawan, S.Pd (NIP 198003122007121001) berhasil dibuat.';

} catch (Throwable $e) {
    http_response_code(500);
    die('<h2 style="color:red">ERROR</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Emergency Reset — Selesai</title>
<style>
body { font-family: sans-serif; background: #0f172a; color: #e2e8f0; padding: 2rem; }
.ok  { background: #14532d; border: 1px solid #22c55e; border-radius: 8px; padding: 1.5rem; margin: 1rem 0; }
.cred{ background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 1rem; margin: .5rem 0; font-family: monospace; font-size: .95rem; }
a   { display: inline-block; margin-top: 1.5rem; padding: .75rem 2rem; background: #1d4ed8; color: #fff; border-radius: 6px; text-decoration: none; font-weight: bold; }
</style>
</head>
<body>
<h2>Emergency Reset — Selesai</h2>
<div class="ok">
<?php foreach ($results as $r): ?>
    <p><?= htmlspecialchars($r) ?></p>
<?php endforeach; ?>
</div>

<h3>Kredensial Default (password: <code>admin123</code>)</h3>
<div class="cred"><strong>Manager</strong><br>NIP: 197505021997032001<br>Nama: Merry Dhani Anitasari<br>Password: admin123</div>
<div class="cred"><strong>Supervisor</strong><br>NIP: 198003122007121001<br>Nama: Ria Irawan, S.Pd<br>Password: admin123</div>

<p style="color:#f87171;font-size:.85rem;margin-top:1rem">
    ⚠ Segera ganti password setelah login pertama.<br>
    ⚠ Hapus atau batasi akses ke file ini setelah digunakan.
</p>
<a href="<?php
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $appRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
    $base = str_starts_with($appRoot, $docRoot) ? substr($appRoot, strlen($docRoot)) : '';
    echo htmlspecialchars($base . '/public/index.php');
?>">→ Pergi ke Halaman Login</a>
</body>
</html>
