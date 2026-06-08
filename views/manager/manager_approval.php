<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';
require_once __DIR__ . '/../partials/layout.php';
requireLogin();
requireRole('Manager');
$user = $_SESSION['user'];
$pdo = getPdo();
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ── Server Intelligence ────────────────────────────────────────────────────
$dbSizeMb = 0.00;
try {
    $dbSizeStmt = $pdo->prepare(
        "SELECT ROUND(SUM(data_length + index_length)/1024/1024, 2) AS size_mb
         FROM information_schema.tables WHERE table_schema = DATABASE()"
    );
    $dbSizeStmt->execute();
    $dbSizeMb = (float) ($dbSizeStmt->fetchColumn() ?: 0);
} catch (Throwable) { /* non-critical */ }

$logFilePath  = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log';
$logSizeKb    = is_file($logFilePath) ? round(filesize($logFilePath) / 1024, 1) : 0.0;

$lastBackupTs = isset($_SESSION['backup_last_ts'])
    ? date('d M Y  H:i', (int) $_SESSION['backup_last_ts'])
    : 'Belum ada backup sesi ini';
// ──────────────────────────────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT dsr.id, dsr.shift_date, dsr.shift_category, dsr.team, dsr.updated_at, dsr.final_copy_watermark, u.full_name AS operator_name FROM daily_shift_reports dsr LEFT JOIN users u ON u.nip = dsr.operator_nip WHERE dsr.is_locked = 1 ORDER BY dsr.shift_date DESC, dsr.updated_at DESC LIMIT 200');
$stmt->execute();
$rows = $stmt->fetchAll();

// ── Personnel roster (includes jabatan) ────────────────────────────────────
$personnelStmt = $pdo->query(
    'SELECT nip, full_name, jabatan, role, team, is_active
     FROM users
     ORDER BY
       FIELD(role,"Manager","Supervisor","Operator"),
       team ASC,
       COALESCE(jabatan,"Operator VTS") ASC,
       full_name ASC'
);
$allPersonnel = $personnelStmt ? $personnelStmt->fetchAll() : [];
$totalPersonnel = count($allPersonnel);
$activePersonnel = count(array_filter($allPersonnel, fn ($p) => (int)$p['is_active'] === 1));
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Manager Approval - Maritime Command</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="<?= BASE_APP ?>assets/style.css" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; background: #131317 url('<?= BASE_APP ?>assets/dotgrid.svg'); color: #e4e1e7; }
        .glass-panel { background: rgba(14,14,18,0.8); backdrop-filter: blur(20px); }
        .machined-button { background: linear-gradient(135deg, #aec7f6 0%, #708ab5 100%); }
        .sidebar { width: 16rem; min-height: 100vh; background: #001e40; position: fixed; left: 0; top: 0; z-index: 40; }
        .main-content { margin-left: 16rem; min-height: 100vh; background: transparent; }
        .badge-lock { background: #fecb00; color: #001e40; font-weight: bold; border-radius: 0.5rem; padding: 0.25rem 0.75rem; margin-left: 1rem; }
        .badge-final { background: #001e40; color: #fecb00; font-weight: bold; border-radius: 0.5rem; padding: 0.25rem 0.75rem; margin-left: 1rem; border: 2px solid #fecb00; }
    </style>
</head>
<body class="antialiased overflow-hidden">
    <!-- Sidebar -->
    <aside class="sidebar flex flex-col pt-20 pb-6 border-r border-blue-900/20">
        <div class="px-6 mb-8">
            <button class="w-full machined-button text-on-primary font-bold py-3 rounded-md flex items-center justify-center gap-2 shadow-lg shadow-blue-500/10 active:opacity-80 transition-all duration-200">
                <span class="material-symbols-outlined text-sm">add</span> New Incident
            </button>
        </div>
        <nav class="flex-1 space-y-1">
            <a class="bg-blue-900/40 text-blue-200 border-l-4 border-amber-400 px-4 py-3 flex items-center gap-3 transition-all duration-200 translate-x-1" href="#">Dashboard</a>
            <a class="text-slate-400 px-4 py-3 flex items-center gap-3 hover:bg-blue-900/20 hover:text-slate-100 transition-all" href="#">Routine Input</a>
            <a class="text-slate-400 px-4 py-3 flex items-center gap-3 hover:bg-blue-900/20 hover:text-slate-100 transition-all" href="#">Special Incidents</a>
            <a class="text-slate-400 px-4 py-3 flex items-center gap-3 hover:bg-blue-900/20 hover:text-slate-100 transition-all" href="#">User Management</a>
            <a class="text-slate-400 px-4 py-3 flex items-center gap-3 hover:bg-blue-900/20 hover:text-slate-100 transition-all" href="#">Archives</a>
        </nav>
        <div class="mt-auto px-4 space-y-2">
            <div class="text-[9px] text-slate-500 px-2 mb-2">Systems Health</div>
            <div class="flex items-center justify-between p-2 rounded bg-slate-900/40 border border-blue-900/10">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-400 text-sm">radar</span><span class="text-[9px]">Radar Status</span></div>
                <span class="text-[9px] text-emerald-400 font-bold">ONLINE</span>
            </div>
            <div class="flex items-center justify-between p-2 rounded bg-slate-900/40 border border-blue-900/10">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-emerald-400 text-sm">settings_input_antenna</span><span class="text-[9px]">AIS Status</span></div>
                <span class="text-[9px] text-emerald-400 font-bold">ONLINE</span>
            </div>
            <div class="flex items-center justify-between p-2 rounded bg-amber-900/20 border border-amber-900/40">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-amber-400 text-sm">router</span><span class="text-[9px] text-amber-200">ODU Status</span></div>
                <span class="text-[9px] text-amber-400 font-bold">FAULT</span>
            </div>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="main-content pt-16 h-screen overflow-y-auto bg-surface text-on-surface">
        <div class="p-8 space-y-8">
            <!-- Header -->
            <header class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-4">
                    <img src="<?= BASE_APP ?>assets/img/logo_navigasi.png" alt="Logo Navigasi" class="h-9 w-9 object-contain rounded-full border border-[#FECB00]/30" onerror="this.style.display='none'">
                    <span class="text-lg font-bold tracking-widest uppercase text-slate-100">Maritime Command</span>
                    <span class="ml-4 text-blue-400 font-semibold border-b-2 border-blue-400 py-1 text-xs tracking-wider">MANAGER</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-100 leading-none"><?= htmlspecialchars($user['full_name'] ?? '-') ?></p>
                        <p class="text-[10px] text-blue-300 opacity-70 tracking-tighter">NIP: <?= htmlspecialchars($user['nip'] ?? '-') ?></p>
                    </div>
                    <div class="w-8 h-8 rounded bg-blue-800 flex items-center justify-center overflow-hidden border border-blue-400/30">
                        <img alt="User" class="w-full h-full object-cover" src="<?= BASE_APP ?>assets/user.png" />
                    </div>
                </div>
            </header>
            <!-- Audit & Router -->
            <div class="glass-panel rounded-lg p-6 mb-8 flex gap-4 items-center flex-wrap">
                <a class="machined-button px-4 py-2 rounded" href="<?= BASE_URL ?>audit_logs.php">Audit History</a>
                <a class="machined-button px-4 py-2 rounded" href="<?= BASE_URL ?>dashboard.php">Buka Router Dashboard</a>
                <a id="backupBtn" href="<?= BASE_APP ?>actions/admin/backup_db.php"
                   class="flex items-center gap-2 px-4 py-2 rounded font-bold text-sm text-emerald-100 bg-emerald-800/60 hover:bg-emerald-700/70 border border-emerald-500/40 transition-colors"
                   title="Download backup database sekarang"
                   onclick="this.textContent='⏳ Menyiapkan...';this.style.pointerEvents='none';setTimeout(function(){var a=document.getElementById('backupBtn');if(a){a.innerHTML='&#x2B07; Backup DB';a.style.pointerEvents='auto';}},10000);">
                    &#x2B07; Backup DB
                </a>
            </div>
            <!-- Buat / Update User -->
            <div class="glass-panel rounded-lg p-6 mb-8">
                <!-- ── Server Intelligence Strip ─────────────────────────────── -->
                <div class="mb-6 grid grid-cols-3 gap-3">
                    <!-- DB Size -->
                    <div class="flex items-center gap-3 bg-slate-900/50 border border-blue-900/20 rounded-lg px-4 py-3">
                        <span class="material-symbols-outlined text-sky-400 text-2xl">storage</span>
                        <div>
                            <p class="text-[8px] font-bold uppercase tracking-widest text-slate-500">Database Size</p>
                            <p class="text-sm font-black text-sky-300"><?= number_format($dbSizeMb, 2) ?> <span class="text-[9px] font-normal text-slate-400">MB</span></p>
                        </div>
                    </div>
                    <!-- Error Log Size -->
                    <div class="flex items-center gap-3 bg-slate-900/50 border border-<?= $logSizeKb > 100 ? 'red' : 'blue' ?>-900/20 rounded-lg px-4 py-3">
                        <span class="material-symbols-outlined text-<?= $logSizeKb > 100 ? 'red' : 'emerald' ?>-400 text-2xl">bug_report</span>
                        <div>
                            <p class="text-[8px] font-bold uppercase tracking-widest text-slate-500">Error Log</p>
                            <p class="text-sm font-black text-<?= $logSizeKb > 100 ? 'red' : 'emerald' ?>-300"><?= number_format($logSizeKb, 1) ?> <span class="text-[9px] font-normal text-slate-400">KB</span></p>
                        </div>
                    </div>
                    <!-- Last Backup -->
                    <div class="flex items-center gap-3 bg-slate-900/50 border border-emerald-900/20 rounded-lg px-4 py-3">
                        <span class="material-symbols-outlined text-emerald-400 text-2xl">backup</span>
                        <div>
                            <p class="text-[8px] font-bold uppercase tracking-widest text-slate-500">Last Backup</p>
                            <p class="text-[11px] font-bold text-emerald-300 leading-tight"><?= htmlspecialchars($lastBackupTs) ?></p>
                        </div>
                    </div>
                </div>
                <!-- ──────────────────────────────────────────────────────────── -->
                <h3 class="font-bold mb-4">Buat / Update User (Argon2id)</h3>
                <form action="<?= BASE_APP ?>actions/manager/create_user.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label>NIP</label><input name="nip" type="text" required></div>
                        <div><label>Nama Lengkap</label><input name="full_name" type="text" required></div>
                        <div><label>Password</label><input name="password" type="password" required></div>
                        <div><label>Jabatan Fungsional</label>
                            <select name="jabatan">
                                <option value="">— Pilih Jabatan —</option>
                                <option value="Kepala Sub-seksi VTS">Kepala Sub-seksi VTS</option>
                                <option value="Penjaga Jaga (Watching Keeper)">Penjaga Jaga (Watching Keeper)</option>
                                <option value="Operator VTS">Operator VTS</option>
                                <option value="Markonis / Radio Operator">Markonis / Radio Operator</option>
                                <option value="Teknisi Elektronika Navigasi">Teknisi Elektronika Navigasi</option>
                                <option value="Administrasi / Tata Usaha">Administrasi / Tata Usaha</option>
                            </select>
                        </div>
                        <div><label>Role (Akses Sistem)</label>
                            <select name="role" required>
                                <option value="Operator">Operator</option>
                                <option value="Supervisor">Supervisor</option>
                                <option value="Manager">Manager</option>
                            </select>
                        </div>
                        <div><label>Tim</label>
                            <select name="team" required>
                                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option><option value="E">E</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex; align-items:end;"><button type="submit" class="machined-button px-4 py-2 rounded">Simpan User</button></div>
                </form>
            </div>

            <!-- Daftar Seluruh Personel ─────────────────────────────────── -->
            <div class="glass-panel rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h3 class="font-bold">Daftar Personel VTS Palembang</h3>
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] bg-blue-900/40 border border-blue-700/40 rounded px-3 py-1 text-blue-200">
                            Total: <strong><?= $totalPersonnel ?></strong> personel
                        </span>
                        <span class="text-[10px] bg-emerald-900/40 border border-emerald-700/40 rounded px-3 py-1 text-emerald-200">
                            Aktif: <strong><?= $activePersonnel ?></strong>
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border-separate border-spacing-y-0.5">
                        <thead class="sticky top-0 bg-[#002147] text-[#fecb00]">
                            <tr>
                                <th class="px-3 py-2 text-left">NIP</th>
                                <th class="px-3 py-2 text-left">Nama Lengkap</th>
                                <th class="px-3 py-2 text-left">Jabatan</th>
                                <th class="px-3 py-2 text-center">Role</th>
                                <th class="px-3 py-2 text-center">Tim</th>
                                <th class="px-3 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$allPersonnel): ?>
                            <tr><td colspan="6" class="text-center text-slate-400 py-4">Belum ada data personel. Jalankan: php sql/seed_personnel.php</td></tr>
                        <?php else: foreach ($allPersonnel as $p):
                            $jabBadge = match ($p['jabatan'] ?? '') {
                                'Kepala Sub-seksi VTS'            => 'text-amber-300 bg-amber-900/40 border-amber-700/40',
                                'Penjaga Jaga (Watching Keeper)'  => 'text-sky-300 bg-sky-900/30 border-sky-700/40',
                                'Markonis / Radio Operator'       => 'text-purple-300 bg-purple-900/30 border-purple-700/40',
                                'Teknisi Elektronika Navigasi'    => 'text-emerald-300 bg-emerald-900/30 border-emerald-700/40',
                                'Administrasi / Tata Usaha'       => 'text-pink-300 bg-pink-900/30 border-pink-700/40',
                                default                           => 'text-slate-300 bg-slate-900/30 border-slate-700/40',
                            };
                        ?>
                            <tr class="bg-slate-900/20 hover:bg-blue-900/10 transition-colors">
                                <td class="px-3 py-2 font-mono text-[10px] text-slate-400"><?= htmlspecialchars((string)$p['nip']) ?></td>
                                <td class="px-3 py-2 font-semibold"><?= htmlspecialchars((string)$p['full_name']) ?></td>
                                <td class="px-3 py-2">
                                    <?php if ($p['jabatan']): ?>
                                    <span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded-full border <?= $jabBadge ?>">
                                        <?= htmlspecialchars((string)$p['jabatan']) ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-slate-500 text-[10px]">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="text-[9px] font-bold uppercase tracking-wider
                                        <?= $p['role'] === 'Manager' ? 'text-amber-400' : ($p['role'] === 'Supervisor' ? 'text-sky-400' : 'text-slate-300') ?>">
                                        <?= htmlspecialchars((string)$p['role']) ?>
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="inline-block w-6 h-6 rounded text-[10px] font-black flex items-center justify-center
                                        bg-[#001E40] text-[#FECB00] border border-[#FECB00]/30">
                                        <?= htmlspecialchars((string)$p['team']) ?>
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <?php if ((int)$p['is_active'] === 1): ?>
                                        <span class="text-[9px] text-emerald-400 font-bold">&#x25CF; AKTIF</span>
                                    <?php else: ?>
                                        <span class="text-[9px] text-red-400 font-bold">&#x25CF; NONAKTIF</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="glass-panel rounded-lg p-6 mb-8">
                <h3 class="font-bold mb-4">Locked Reports (Siap Acknowledge)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border-separate border-spacing-y-1">
                        <thead class="sticky top-0 bg-[#002147] text-[#fecb00]">
                            <tr><th>ID</th><th>Tanggal</th><th>Shift</th><th>Tim</th><th>Operator</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        <?php if (!$rows): ?>
                            <tr><td colspan="7" class="text-center text-slate-400">Tidak ada laporan locked.</td></tr>
                        <?php else: foreach ($rows as $row): ?>
                            <tr>
                                <td>#<?= (int) $row['id'] ?></td>
                                <td><?= htmlspecialchars((string) $row['shift_date']) ?></td>
                                <td><?= htmlspecialchars((string) $row['shift_category']) ?></td>
                                <td><?= htmlspecialchars((string) $row['team']) ?></td>
                                <td><?= htmlspecialchars((string) ($row['operator_name'] ?? '-')) ?></td>
                                <td><?= (int) $row['final_copy_watermark'] === 1 ? 'FINAL' : 'LOCKED' ?></td>
                                <td>
                                    <?php if ((int) $row['final_copy_watermark'] === 1): ?>
                                        <button type="button" disabled style="opacity:0.6;cursor:not-allowed;">Finalized</button>
                                    <?php else: ?>
                                        <button class="machined-button px-4 py-2 rounded acknowledge-btn" data-id="<?= (int) $row['id'] ?>" data-tippy-content="Acknowledge & Finalize">ACKNOWLEDGE & FINALIZE</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <!-- APPROVED stamp overlay -->
    <div id="approvedOverlay" style="display:none;position:fixed;inset:0;z-index:200;pointer-events:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.3)">
        <div id="approvedStamp"
             style="font-family:Georgia,serif;color:#dc2626;border:6px solid #dc2626;border-radius:0.5rem;padding:2rem 3.5rem;font-size:3.5rem;font-weight:900;letter-spacing:0.15em;text-transform:uppercase;opacity:0;transform:rotate(-20deg) scale(0.4);transition:all 0.4s cubic-bezier(0.175,0.885,0.32,1.275);box-shadow:0 0 40px rgba(220,38,38,0.4);background:rgba(255,255,255,0.06);backdrop-filter:blur(4px)">APPROVED</div>
    </div>
    <div class="toast" id="toast"></div>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy.umd.min.js"></script>
    <script>
    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.style.display = 'block';
        setTimeout(function(){ toast.style.display = 'none'; }, 2500);
    }
    document.addEventListener('DOMContentLoaded', function() {
        tippy('.acknowledge-btn');
        document.querySelectorAll('.acknowledge-btn').forEach(function(btn) {
            btn.onclick = function() {
                var id    = this.dataset.id;
                var self  = this;
                var overlay = document.getElementById('approvedOverlay');
                var stamp   = document.getElementById('approvedStamp');
                // Show APPROVED stamp with spring animation
                overlay.style.display = 'flex';
                overlay.style.pointerEvents = 'all';
                requestAnimationFrame(function(){
                    requestAnimationFrame(function(){
                        stamp.style.opacity = '1';
                        stamp.style.transform = 'rotate(-20deg) scale(1)';
                    });
                });
                // After stamp animates in, call the API
                setTimeout(function(){
                    fetch(<?= json_encode(BASE_APP . 'actions/manager/finalize_report.php') ?>, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ daily_shift_report_id: id, csrf_token: '<?= htmlspecialchars(csrfToken()) ?>' })
                    }).then(function(r){ return r.json(); }).then(function(res){
                        if (res.success) {
                            var row = self.closest('tr');
                            row.classList.add('fade-out');
                            setTimeout(function(){ row.remove(); }, 400);
                            showToast('Laporan berhasil di-finalize!');
                        } else {
                            showToast(res.message || 'Gagal finalize!');
                        }
                    }).catch(function(){
                        showToast('Koneksi gagal — coba lagi.');
                    }).finally(function(){
                        // Dismiss stamp
                        setTimeout(function(){
                            stamp.style.opacity = '0';
                            stamp.style.transform = 'rotate(-20deg) scale(0.4)';
                            setTimeout(function(){
                                overlay.style.display = 'none';
                                overlay.style.pointerEvents = 'none';
                            }, 450);
                        }, 900);
                    });
                }, 650);
            };
        });
    });
    </script>
</body>
</html>
