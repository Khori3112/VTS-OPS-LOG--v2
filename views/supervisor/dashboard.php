<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';
require_once __DIR__ . '/../partials/layout.php';
requireLogin();
requireRole('Supervisor');
$user = $_SESSION['user'];
$pdo = getPdo();
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
$reqStmt = $pdo->prepare('SELECT er.id, er.record_id, er.reason, er.created_at, er.requested_by_nip, u.full_name FROM edit_requests er LEFT JOIN users u ON u.nip = er.requested_by_nip WHERE er.status = "pending" ORDER BY er.created_at DESC LIMIT 100');
$reqStmt->execute();
$pending = $reqStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Supervisor Dashboard - Maritime Command</title>
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
<body class="antialiased">
    <!-- Sidebar -->
    <aside class="sidebar flex flex-col pt-20 pb-6 border-r border-blue-900/20">
        <div class="px-6 mb-8">
            <button class="w-full machined-button text-on-primary font-bold py-3 rounded-md flex items-center justify-center gap-2 shadow-lg shadow-blue-500/10 active:opacity-80 transition-all duration-200">
                <span class="material-symbols-outlined text-sm">add</span> New Incident
            </button>
        </div>
        <nav class="flex-1 space-y-1">
            <a class="bg-blue-900/40 text-blue-200 border-l-4 border-amber-400 px-4 py-3 flex items-center gap-3 transition-all duration-200 translate-x-1" href="<?= BASE_APP ?>public/dashboard.php">Dashboard</a>
            <a class="text-slate-400 px-4 py-3 flex items-center gap-3 hover:bg-blue-900/20 hover:text-slate-100 transition-all" href="<?= BASE_APP ?>views/supervisor/audit_logs.php">Audit Logs</a>
        </nav>
        <div class="mt-auto px-4 pb-4">
            <a href="<?= BASE_APP ?>public/logout.php"
               class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg
                      bg-red-900/40 border border-red-700/40 text-red-300 hover:bg-red-800/60
                      hover:text-white transition-all text-xs font-bold uppercase tracking-widest mb-4">
                <span class="material-symbols-outlined text-sm">logout</span> Logout
            </a>
        </div>
        <div class="px-4 space-y-2">
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
                    <span class="text-lg font-bold tracking-widest uppercase text-slate-100">Maritime Command</span>
                    <span class="ml-4 text-blue-400 font-semibold border-b-2 border-blue-400 py-1 text-xs tracking-wider">SUPERVISOR</span>
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
            <div class="glass-panel rounded-lg p-6 mb-8 flex gap-4 items-center">
                <a class="machined-button px-4 py-2 rounded" href="<?= BASE_URL ?>audit_logs.php">Audit History</a>
                <a class="machined-button px-4 py-2 rounded" href="<?= BASE_URL ?>dashboard.php">Buka Router Dashboard</a>
            </div>
            <!-- Permintaan Edit Pending -->
            <div class="glass-panel rounded-lg p-6 mb-8">
                <h3 class="font-bold mb-4">Permintaan Edit Pending</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs border-separate border-spacing-y-1">
                        <thead class="sticky top-0 bg-[#002147] text-[#fecb00]">
                            <tr><th>ID</th><th>Peminta</th><th>Shift Report</th><th>Alasan</th><th>Waktu</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        <?php if (!$pending): ?>
                            <tr><td colspan="6" class="text-center text-slate-400">Tidak ada permintaan edit pending.</td></tr>
                        <?php else: foreach ($pending as $row): ?>
                            <tr data-id="<?= (int) $row['id'] ?>">
                                <td><?= (int) $row['id'] ?></td>
                                <td><?= htmlspecialchars((string) ($row['full_name'] ?? $row['requested_by_nip'])) ?></td>
                                <td>#<?= (int) $row['record_id'] ?></td>
                                <td><?= htmlspecialchars((string) $row['reason']) ?></td>
                                <td><?= htmlspecialchars((string) $row['created_at']) ?></td>
                                <td>
                                    <button class="machined-button px-2 py-1 rounded approve-btn" data-id="<?= (int) $row['id'] ?>" data-tippy-content="Approve">APPROVE</button>
                                    <button class="machined-button px-2 py-1 rounded bg-red-600 text-white reject-btn" data-id="<?= (int) $row['id'] ?>" data-tippy-content="Reject">REJECT</button>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <div class="toast" id="toast"></div>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script>
    var APP_BASE = <?= json_encode(BASE_APP) ?>;
    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.textContent = msg;
        toast.style.display = 'block';
        setTimeout(()=>{ toast.style.display = 'none'; }, 2000);
    }
    document.addEventListener('DOMContentLoaded', function() {
        tippy('.approve-btn');
        tippy('.reject-btn');
        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.onclick = function() {
                const id = this.dataset.id;
                fetch(APP_BASE + 'actions/supervisor/approve_edit.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ request_id: id, csrf_token: '<?= htmlspecialchars(csrfToken()) ?>', action: 'approve' })
                }).then(r=>r.json()).then(res=>{
                    if(res.success) {
                        const row = document.querySelector('tr[data-id="'+id+'"]');
                        if(row) row.remove();
                        showToast('Permintaan edit disetujui!');
                    } else {
                        showToast('Gagal approve!');
                    }
                });
            }
        });
        document.querySelectorAll('.reject-btn').forEach(btn => {
            btn.onclick = function() {
                const id = this.dataset.id;
                fetch(APP_BASE + 'actions/supervisor/approve_edit.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ request_id: id, csrf_token: '<?= htmlspecialchars(csrfToken()) ?>', action: 'reject' })
                }).then(r=>r.json()).then(res=>{
                    if(res.success) {
                        const row = document.querySelector('tr[data-id="'+id+'"]');
                        if(row) row.remove();
                        showToast('Permintaan edit ditolak!');
                    } else {
                        showToast('Gagal reject!');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
