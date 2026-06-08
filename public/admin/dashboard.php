<?php
// public/admin/dashboard.php — ADMIN COMMAND CENTER · Sovereign Engine v5.1
declare(strict_types=1);
require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';

requireLogin();
requireRole('Admin');

$user    = $_SESSION['user'];
$pdo     = getPdo();
$flash   = $_SESSION['flash_success'] ?? null;
$flashE  = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ── Data Queries ────────────────────────────────────────────────────────────

// All users
$usersStmt = $pdo->query(
    "SELECT nip, full_name, role, team, is_active, created_at
     FROM users
     ORDER BY FIELD(role,'Admin','Manager','Supervisor','Operator'), full_name ASC"
);
$allUsers = $usersStmt ? $usersStmt->fetchAll() : [];

// Stats
$totalUsers  = count($allUsers);
$activeUsers = count(array_filter($allUsers, fn($u) => (int)$u['is_active'] === 1));
$byRole      = array_count_values(array_column($allUsers, 'role'));

// Password reset requests
$resetStmt = $pdo->query(
    "SELECT er.id, er.requested_by_nip, u.full_name, er.created_at
     FROM edit_requests er
     LEFT JOIN users u ON u.nip = er.requested_by_nip
     WHERE er.table_name = 'password_reset' AND er.status = 'pending'
     ORDER BY er.created_at DESC
     LIMIT 50"
);
$resetRequests = $resetStmt ? $resetStmt->fetchAll() : [];

// Recent audit logs
$auditStmt = $pdo->query(
    "SELECT al.id, al.actor_nip, u.full_name, al.action_type, al.table_name,
            al.description, al.logged_at
     FROM audit_logs al
     LEFT JOIN users u ON u.nip = al.actor_nip
     ORDER BY al.logged_at DESC
     LIMIT 30"
);
$auditLogs = $auditStmt ? $auditStmt->fetchAll() : [];

// DB size
$dbSizeMb = 0.0;
try {
    $sz = $pdo->query("SELECT ROUND(SUM(data_length+index_length)/1024/1024,2) AS s FROM information_schema.tables WHERE table_schema=DATABASE()");
    $dbSizeMb = $sz ? (float)($sz->fetchColumn() ?: 0) : 0.0;
} catch (Throwable) {}

// ── Chart Data ─────────────────────────────────────────────────────────────

// Traffic Trends: vessel_traffic count per day for last 7 days
$trafficLabels = [];
$trafficData   = [];
try {
    $tStmt = $pdo->query(
        "SELECT DATE(dsr.shift_date) AS day, COUNT(vt.id) AS total
         FROM daily_shift_reports dsr
         LEFT JOIN vessel_traffic vt ON vt.daily_shift_report_id = dsr.id
         WHERE dsr.shift_date >= CURDATE() - INTERVAL 6 DAY
         GROUP BY DATE(dsr.shift_date)
         ORDER BY day ASC"
    );
    $tRows = $tStmt ? $tStmt->fetchAll() : [];
    // Fill in all 7 days including zeros
    $dayMap = [];
    foreach ($tRows as $r) $dayMap[$r['day']] = (int)$r['total'];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} days"));
        $trafficLabels[] = date('d M', strtotime($d));
        $trafficData[]   = $dayMap[$d] ?? 0;
    }
} catch (Throwable) {
    for ($i = 6; $i >= 0; $i--) {
        $trafficLabels[] = date('d M', strtotime("-{$i} days"));
        $trafficData[]   = 0;
    }
}

// Team Performance: total vessel_traffic per team (all time)
$teamLabels = ['Tim A', 'Tim B', 'Tim C', 'Tim D', 'Tim E'];
$teamKeys   = ['A', 'B', 'C', 'D', 'E'];
$teamData   = array_fill(0, 5, 0);
try {
    $tmStmt = $pdo->query(
        "SELECT dsr.team, COUNT(vt.id) AS total
         FROM daily_shift_reports dsr
         LEFT JOIN vessel_traffic vt ON vt.daily_shift_report_id = dsr.id
         GROUP BY dsr.team"
    );
    $tmRows = $tmStmt ? $tmStmt->fetchAll() : [];
    $tmMap  = [];
    foreach ($tmRows as $r) $tmMap[$r['team']] = (int)$r['total'];
    foreach ($teamKeys as $k => $team) $teamData[$k] = $tmMap[$team] ?? 0;
} catch (Throwable) {}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Admin Command Center — VTS Palembang</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<style>
  *{box-sizing:border-box}
  body{font-family:'Inter',sans-serif;background:#0d1117;color:#e2e8f0;min-height:100vh;
       background-image:radial-gradient(rgba(255,255,255,0.03) 1px,transparent 1px);background-size:28px 28px;}
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}
  .ms-fill{font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24}

  /* Sidebar */
  .sidebar{width:240px;flex-shrink:0;background:#060b14;border-right:1px solid rgba(254,203,0,0.08);
           min-height:100vh;display:flex;flex-direction:column}
  .sidebar-item{display:flex;align-items:center;gap:10px;padding:10px 16px;border-radius:8px;
                font-size:13px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s;
                text-decoration:none;margin:2px 8px}
  .sidebar-item:hover{background:rgba(254,203,0,0.06);color:#94a3b8}
  .sidebar-item.active{background:rgba(254,203,0,0.1);color:#FECB00;border-left:2px solid #FECB00}

  /* Cards */
  .stat-card{background:rgba(15,21,35,0.7);border:1px solid rgba(255,255,255,0.07);
             border-radius:12px;padding:20px 24px}
  .glass-panel{background:rgba(15,21,35,0.6);border:1px solid rgba(255,255,255,0.07);border-radius:12px}

  /* Table */
  .data-table{width:100%;border-collapse:collapse;font-size:13px}
  .data-table th{padding:10px 14px;text-align:left;font-size:10px;font-weight:900;
                 letter-spacing:.12em;text-transform:uppercase;color:#475569;
                 border-bottom:1px solid rgba(255,255,255,0.06)}
  .data-table td{padding:11px 14px;border-bottom:1px solid rgba(255,255,255,0.04);vertical-align:middle}
  .data-table tr:hover td{background:rgba(255,255,255,0.02)}

  /* Badges */
  .badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;
         border-radius:4px;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase}
  .badge-active{background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.2)}
  .badge-inactive{background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.2)}
  .badge-admin{background:rgba(254,203,0,.12);color:#FECB00;border:1px solid rgba(254,203,0,.2)}
  .badge-manager{background:rgba(59,130,246,.12);color:#60a5fa;border:1px solid rgba(59,130,246,.2)}
  .badge-supervisor{background:rgba(168,85,247,.12);color:#c084fc;border:1px solid rgba(168,85,247,.2)}
  .badge-operator{background:rgba(20,184,166,.12);color:#2dd4bf;border:1px solid rgba(20,184,166,.2)}

  /* Input */
  .inp{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);color:#e2e8f0;
       border-radius:8px;padding:8px 12px;font-size:13px;outline:none;font-family:'Inter',sans-serif;
       transition:border-color .15s}
  .inp:focus{border-color:rgba(254,203,0,.5)}
  .inp::placeholder{color:rgba(255,255,255,.25)}
  select.inp option{background:#0f1523}

  /* Buttons */
  .btn-gold{background:#FECB00;color:#001E40;font-weight:900;font-size:11px;letter-spacing:.1em;
            text-transform:uppercase;border:none;border-radius:6px;padding:7px 14px;cursor:pointer;
            transition:filter .15s,transform .1s}
  .btn-gold:hover{filter:brightness(1.08)}
  .btn-sm{font-size:11px;font-weight:700;border:none;border-radius:5px;padding:5px 10px;
          cursor:pointer;transition:all .15s}
  .btn-edit{background:rgba(59,130,246,.15);color:#60a5fa;border:1px solid rgba(59,130,246,.25)}
  .btn-edit:hover{background:rgba(59,130,246,.25)}
  .btn-danger{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.25)}
  .btn-danger:hover{background:rgba(239,68,68,.25)}
  .btn-reset{background:rgba(168,85,247,.15);color:#c084fc;border:1px solid rgba(168,85,247,.25)}
  .btn-reset:hover{background:rgba(168,85,247,.25)}
  .btn-activate{background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.25)}
  .btn-activate:hover{background:rgba(34,197,94,.25)}

  /* Modal */
  .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);
                  z-index:50;display:flex;align-items:center;justify-content:center;padding:16px}
  .modal-box{background:#0c1220;border:1px solid rgba(255,255,255,.1);border-radius:16px;
             padding:32px;width:100%;max-width:480px;box-shadow:0 24px 64px rgba(0,0,0,.6)}
  .pulse-dot{width:7px;height:7px;border-radius:50%;background:#4ade80;animation:pulse 2s infinite}
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}

  /* Section tabs */
  .tab-btn{padding:8px 20px;border-radius:8px;font-size:12px;font-weight:700;letter-spacing:.08em;
           text-transform:uppercase;border:1px solid transparent;cursor:pointer;transition:all .15s}
  .tab-btn.active{background:rgba(254,203,0,.12);color:#FECB00;border-color:rgba(254,203,0,.25)}
  .tab-btn:not(.active){background:transparent;color:#475569;border-color:rgba(255,255,255,.06)}
  .tab-btn:not(.active):hover{color:#94a3b8;border-color:rgba(255,255,255,.1)}
  section.tab-section{display:none}
  section.tab-section.active{display:block}
</style>
</head>
<body class="flex">

<!-- ═══ SIDEBAR ════════════════════════════════════════════════════════════ -->
<nav class="sidebar py-5">
  <div class="px-5 mb-8">
    <div class="flex items-center gap-3 mb-1">
      <img src="<?= BASE_URL ?>assets/img/logo_navigasi.png" alt=""
           class="h-9 w-9 object-contain" onerror="this.style.display='none'">
      <div>
        <p class="text-[11px] font-black text-white tracking-tight" style="font-weight:900;">VTS PALEMBANG</p>
        <p class="text-[9px] text-[#FECB00]/60 tracking-widest uppercase">Admin Console</p>
      </div>
    </div>
  </div>

  <div class="flex-1 space-y-1">
    <a href="#" onclick="switchTab('users')" class="sidebar-item active" id="nav-users">
      <span class="material-symbols-outlined ms-fill" style="font-size:18px;">manage_accounts</span> User Governance
    </a>
    <a href="#" onclick="switchTab('resets')" class="sidebar-item" id="nav-resets">
      <span class="material-symbols-outlined ms-fill" style="font-size:18px;">lock_reset</span>
      Reset Requests
      <?php if (count($resetRequests)): ?>
      <span class="ml-auto bg-red-500 text-white text-[9px] font-black rounded-full w-4 h-4 flex items-center justify-center"><?= count($resetRequests) ?></span>
      <?php endif; ?>
    </a>
    <a href="#" onclick="switchTab('analytics')" class="sidebar-item" id="nav-analytics">
      <span class="material-symbols-outlined ms-fill" style="font-size:18px;">bar_chart</span> Analytics
    </a>
    <a href="#" onclick="switchTab('audit')" class="sidebar-item" id="nav-audit">
      <span class="material-symbols-outlined ms-fill" style="font-size:18px;">history</span> Audit Log
    </a>
  </div>

  <div class="mt-auto px-5 pb-2 border-t border-white/5 pt-5 space-y-3">
    <div class="flex items-center gap-2">
      <div class="pulse-dot flex-shrink-0"></div>
      <span class="text-[10px] text-slate-600 font-mono tracking-wide">
        <?= htmlspecialchars($user['full_name']) ?>
      </span>
    </div>
    <a href="<?= BASE_URL ?>logout.php"
       class="flex items-center gap-2 text-slate-600 hover:text-red-400 transition-colors text-[12px] font-semibold">
      <span class="material-symbols-outlined" style="font-size:16px;">logout</span> Logout
    </a>
  </div>
</nav>

<!-- ═══ MAIN ════════════════════════════════════════════════════════════════ -->
<div class="flex-1 overflow-y-auto">

  <!-- Top Bar -->
  <header class="sticky top-0 z-40 flex items-center justify-between px-8 py-4 border-b border-white/5"
          style="background:rgba(6,11,20,0.95);backdrop-filter:blur(12px);">
    <div>
      <h1 class="font-black text-white text-lg tracking-tight" style="font-weight:900;">
        ADMIN COMMAND CENTER
      </h1>
      <p class="text-[10px] text-[#FECB00]/50 tracking-[.2em] uppercase font-mono">VTS PALEMBANG · SOVEREIGN ENGINE V5.1</p>
    </div>
    <div class="flex items-center gap-3 text-[11px] text-slate-500">
      <span class="material-symbols-outlined ms-fill text-green-400" style="font-size:14px;">circle</span>
      DB: <?= number_format($dbSizeMb, 2) ?> MB
      &nbsp;·&nbsp;
      <?= date('d M Y · H:i') ?> WIB
    </div>
  </header>

  <div class="p-8">

    <!-- Flash Messages -->
    <?php if ($flash): ?>
    <div class="mb-6 flex items-center gap-3 px-5 py-3 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-sm">
      <span class="material-symbols-outlined ms-fill" style="font-size:18px;">check_circle</span>
      <?= htmlspecialchars($flash) ?>
    </div>
    <?php endif; ?>
    <?php if ($flashE): ?>
    <div class="mb-6 flex items-center gap-3 px-5 py-3 rounded-xl bg-red-950/60 border border-red-500/30 text-red-300 text-sm">
      <span class="material-symbols-outlined ms-fill" style="font-size:18px;">error</span>
      <?= htmlspecialchars($flashE) ?>
    </div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="stat-card">
        <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-2 font-black">Total User</p>
        <p class="text-3xl font-black text-white" style="font-weight:900;"><?= $totalUsers ?></p>
      </div>
      <div class="stat-card">
        <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-2 font-black">Aktif</p>
        <p class="text-3xl font-black text-green-400" style="font-weight:900;"><?= $activeUsers ?></p>
      </div>
      <div class="stat-card">
        <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-2 font-black">Pending Reset</p>
        <p class="text-3xl font-black text-purple-400" style="font-weight:900;"><?= count($resetRequests) ?></p>
      </div>
      <div class="stat-card">
        <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-2 font-black">Operator</p>
        <p class="text-3xl font-black text-[#FECB00]" style="font-weight:900;"><?= $byRole['Operator'] ?? 0 ?></p>
      </div>
    </div>

    <!-- ════ TAB: USER GOVERNANCE ════ -->
    <section id="tab-users" class="tab-section active">
      <div class="glass-panel p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
          <div>
            <h2 class="font-black text-white text-base tracking-tight" style="font-weight:900;">User Governance</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola semua akun personel VTS</p>
          </div>
          <div class="flex gap-2 items-center flex-wrap">
            <!-- Search -->
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none" style="font-size:16px;">search</span>
              <input type="text" id="searchInput" placeholder="Cari nama / NIP..."
                     class="inp pl-9 w-52" oninput="filterTable()"/>
            </div>
            <!-- Role filter -->
            <select id="roleFilter" class="inp" onchange="filterTable()">
              <option value="">Semua Role</option>
              <option>Admin</option>
              <option>Manager</option>
              <option>Supervisor</option>
              <option>Operator</option>
            </select>
            <!-- Status filter -->
            <select id="statusFilter" class="inp" onchange="filterTable()">
              <option value="">Semua Status</option>
              <option value="1">Aktif</option>
              <option value="0">Inactive</option>
            </select>
            <button class="btn-gold flex items-center gap-1.5" onclick="openAddModal()">
              <span class="material-symbols-outlined ms-fill" style="font-size:15px;">person_add</span>
              Tambah User
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="data-table" id="userTable">
            <thead>
              <tr>
                <th>NIP</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Tim</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($allUsers as $u): ?>
              <tr data-role="<?= htmlspecialchars($u['role']) ?>"
                  data-status="<?= (int)$u['is_active'] ?>"
                  data-search="<?= strtolower(htmlspecialchars($u['nip'] . ' ' . $u['full_name'])) ?>">
                <td class="font-mono text-[12px] text-slate-300"><?= htmlspecialchars($u['nip']) ?></td>
                <td class="font-semibold text-white"><?= htmlspecialchars($u['full_name']) ?></td>
                <td>
                  <?php
                    $rBadge = match($u['role']) {
                      'Admin'      => 'badge-admin',
                      'Manager'    => 'badge-manager',
                      'Supervisor' => 'badge-supervisor',
                      default      => 'badge-operator',
                    };
                  ?>
                  <span class="badge <?= $rBadge ?>"><?= htmlspecialchars($u['role']) ?></span>
                </td>
                <td class="text-slate-400 font-mono"><?= $u['team'] ? htmlspecialchars($u['team']) : '—' ?></td>
                <td>
                  <span class="badge <?= (int)$u['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                    <?= (int)$u['is_active'] ? 'Aktif' : 'Inactive' ?>
                  </span>
                </td>
                <td class="text-slate-500 text-[11px]"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td>
                  <div class="flex gap-1.5 flex-wrap">
                    <?php if ($u['nip'] !== $user['nip']): // can't edit self via this table ?>
                    <button class="btn-sm btn-edit" onclick='openEditModal(<?= json_encode([
                      "nip"       => $u["nip"],
                      "full_name" => $u["full_name"],
                      "role"      => $u["role"],
                      "team"      => $u["team"],
                      "is_active" => (int)$u["is_active"],
                    ]) ?>)'>
                      <span class="material-symbols-outlined" style="font-size:13px;">edit</span> Edit
                    </button>
                    <button class="btn-sm btn-reset" onclick="resetPassword('<?= htmlspecialchars(addslashes($u['nip'])) ?>','<?= htmlspecialchars(addslashes($u['full_name'])) ?>')">
                      <span class="material-symbols-outlined" style="font-size:13px;">lock_reset</span> Reset PW
                    </button>
                    <button class="btn-sm btn-danger" onclick="deleteUser('<?= htmlspecialchars(addslashes($u['nip'])) ?>','<?= htmlspecialchars(addslashes($u['full_name'])) ?>')">
                      <span class="material-symbols-outlined" style="font-size:13px;">delete</span>
                    </button>
                    <?php else: ?>
                    <span class="text-[10px] text-slate-600 italic">Akun Anda</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php if (empty($allUsers)): ?>
          <p class="text-center text-slate-600 py-8 text-sm">Belum ada user terdaftar.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ════ TAB: RESET REQUESTS ════ -->
    <section id="tab-resets" class="tab-section">
      <div class="glass-panel p-6">
        <h2 class="font-black text-white text-base tracking-tight mb-6" style="font-weight:900;">
          Password Reset Requests
        </h2>
        <?php if (empty($resetRequests)): ?>
        <div class="text-center py-12">
          <span class="material-symbols-outlined ms-fill text-slate-600" style="font-size:48px;">lock_open</span>
          <p class="text-slate-500 mt-3 text-sm">Tidak ada permintaan reset yang pending.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
          <table class="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Waktu Request</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($resetRequests as $rr): ?>
              <tr>
                <td class="text-slate-500 text-[11px]"><?= (int)$rr['id'] ?></td>
                <td class="font-mono text-[12px] text-slate-300"><?= htmlspecialchars($rr['requested_by_nip']) ?></td>
                <td class="text-white font-semibold"><?= htmlspecialchars($rr['full_name'] ?? '—') ?></td>
                <td class="text-slate-400 text-[12px]"><?= htmlspecialchars($rr['created_at']) ?></td>
                <td>
                  <div class="flex gap-1.5">
                    <button class="btn-sm btn-reset"
                            onclick="resetPassword('<?= htmlspecialchars(addslashes($rr['requested_by_nip'])) ?>','<?= htmlspecialchars(addslashes($rr['full_name'] ?? $rr['requested_by_nip'])) ?>')">
                      <span class="material-symbols-outlined" style="font-size:13px;">lock_reset</span> Reset PW
                    </button>
                    <form method="POST" action="<?= BASE_APP ?>actions/admin/user_crud.php" style="display:inline">
                      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                      <input type="hidden" name="action" value="dismiss_reset">
                      <input type="hidden" name="request_id" value="<?= (int)$rr['id'] ?>">
                      <button type="submit" class="btn-sm btn-danger">Dismiss</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- ════ TAB: ANALYTICS ════ -->
    <section id="tab-analytics" class="tab-section">
      <div class="mb-6">
        <h2 class="font-black text-white text-base tracking-tight" style="font-weight:900;">Data Intelligence</h2>
        <p class="text-xs text-slate-500 mt-0.5">Visualisasi aktivitas &amp; performa tim operasional</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Line Chart: Traffic Trends -->
        <div class="glass-panel p-6">
          <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined ms-fill text-[#FECB00]" style="font-size:18px;">trending_up</span>
            <div>
              <h3 class="font-black text-white text-sm" style="font-weight:900;">Traffic Trends</h3>
              <p class="text-[10px] text-slate-500">Jumlah laporan lalu lintas kapal — 7 hari terakhir</p>
            </div>
          </div>
          <div class="relative" style="height:240px;">
            <canvas id="chartTraffic"></canvas>
          </div>
        </div>

        <!-- Bar Chart: Team Performance -->
        <div class="glass-panel p-6">
          <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined ms-fill text-[#FECB00]" style="font-size:18px;">groups</span>
            <div>
              <h3 class="font-black text-white text-sm" style="font-weight:900;">Team Performance</h3>
              <p class="text-[10px] text-slate-500">Total laporan kapal per tim (A&ndash;E)</p>
            </div>
          </div>
          <div class="relative" style="height:240px;">
            <canvas id="chartTeam"></canvas>
          </div>
        </div>

      </div>

      <!-- Summary row -->
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
        <?php
          $topTeam  = 'N/A';
          $topScore = 0;
          foreach ($teamKeys as $k => $team) {
            if ($teamData[$k] > $topScore) { $topScore = $teamData[$k]; $topTeam = 'Tim ' . $team; }
          }
          $totalVessel7d = array_sum($trafficData);
          $avgPerDay     = $totalVessel7d > 0 ? round($totalVessel7d / 7, 1) : 0;
        ?>
        <div class="stat-card">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-2 font-black">Total Kapal (7 Hari)</p>
          <p class="text-3xl font-black text-[#FECB00]" style="font-weight:900;"><?= $totalVessel7d ?></p>
        </div>
        <div class="stat-card">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-2 font-black">Rata-rata / Hari</p>
          <p class="text-3xl font-black text-blue-400" style="font-weight:900;"><?= $avgPerDay ?></p>
        </div>
        <div class="stat-card">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest mb-2 font-black">Tim Teraktif</p>
          <p class="text-3xl font-black text-green-400" style="font-weight:900;"><?= htmlspecialchars($topTeam) ?></p>
        </div>
      </div>
    </section>

    <!-- ════ TAB: AUDIT LOG ════ -->
    <section id="tab-audit" class="tab-section">
      <div class="glass-panel p-6">
        <h2 class="font-black text-white text-base tracking-tight mb-6" style="font-weight:900;">System Audit Log</h2>
        <div class="overflow-x-auto">
          <table class="data-table">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Aksi</th>
                <th>Tabel</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($auditLogs as $al): ?>
              <?php
                $aColor = match($al['action_type']) {
                  'LOGIN'          => 'text-green-400',
                  'DELETE'         => 'text-red-400',
                  'PASSWORD_RESET' => 'text-purple-400',
                  'REGISTER'       => 'text-blue-400',
                  'CREATE'         => 'text-teal-400',
                  'LOCK', 'UNLOCK' => 'text-yellow-400',
                  default          => 'text-slate-400',
                };
              ?>
              <tr>
                <td class="text-slate-500 text-[11px] font-mono whitespace-nowrap"><?= htmlspecialchars($al['logged_at']) ?></td>
                <td class="font-mono text-[12px] text-slate-300"><?= htmlspecialchars($al['actor_nip']) ?></td>
                <td class="text-white text-[12px]"><?= htmlspecialchars($al['full_name'] ?? '—') ?></td>
                <td><span class="font-black text-[10px] tracking-wide <?= $aColor ?>"><?= htmlspecialchars($al['action_type']) ?></span></td>
                <td class="text-slate-500 text-[11px] font-mono"><?= htmlspecialchars($al['table_name'] ?? '—') ?></td>
                <td class="text-slate-400 text-[12px] max-w-xs truncate"><?= htmlspecialchars($al['description'] ?? '') ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($auditLogs)): ?>
              <tr><td colspan="6" class="text-center text-slate-600 py-8">Belum ada log.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </div><!-- /p-8 -->
</div><!-- /main -->

<!-- ═══ MODAL: ADD USER ════════════════════════════════════════════════════ -->
<div id="modalAdd" class="modal-backdrop" style="display:none">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-black text-white text-base" style="font-weight:900;">
        <span class="material-symbols-outlined ms-fill text-[#FECB00] align-middle" style="font-size:20px;">person_add</span>
        Tambah User Baru
      </h3>
      <button onclick="closeModal('modalAdd')" class="text-slate-500 hover:text-white transition-colors">
        <span class="material-symbols-outlined" style="font-size:20px;">close</span>
      </button>
    </div>
    <form method="POST" action="<?= BASE_APP ?>actions/admin/user_crud.php" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="add_user">
      <div>
        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
        <input type="text" name="full_name" class="inp w-full" required maxlength="120" placeholder="Nama lengkap"/>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">NIP</label>
        <input type="text" name="nip" class="inp w-full" required maxlength="30" placeholder="NIP unik"/>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Role</label>
          <select name="role" id="addRole" class="inp w-full" required onchange="updateTeamField('add')">
            <option value="" disabled selected>Pilih</option>
            <option>Operator</option>
            <option>Supervisor</option>
            <option>Manager</option>
            <option>Admin</option>
          </select>
        </div>
        <div id="addTeamWrap">
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Tim</label>
          <select name="team" id="addTeam" class="inp w-full">
            <option value="">—</option>
            <?php foreach (['A','B','C','D','E'] as $t): ?>
            <option value="<?= $t ?>">Tim <?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Password</label>
        <input type="password" name="password" class="inp w-full" required minlength="8" placeholder="Min. 8 karakter"/>
      </div>
      <div class="pt-2 flex gap-2 justify-end">
        <button type="button" onclick="closeModal('modalAdd')"
                class="btn-sm text-slate-400 border border-white/10 bg-transparent hover:bg-white/5 px-4 py-2">Batal</button>
        <button type="submit" class="btn-gold">Simpan User</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══ MODAL: EDIT USER ═══════════════════════════════════════════════════ -->
<div id="modalEdit" class="modal-backdrop" style="display:none">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-black text-white text-base" style="font-weight:900;">
        <span class="material-symbols-outlined ms-fill text-blue-400 align-middle" style="font-size:20px;">edit</span>
        Edit User
      </h3>
      <button onclick="closeModal('modalEdit')" class="text-slate-500 hover:text-white transition-colors">
        <span class="material-symbols-outlined" style="font-size:20px;">close</span>
      </button>
    </div>
    <form method="POST" action="<?= BASE_APP ?>actions/admin/user_crud.php" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="edit_user">
      <input type="hidden" name="nip" id="editNip">
      <div>
        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
        <input type="text" name="full_name" id="editFullName" class="inp w-full" required maxlength="120"/>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Role</label>
          <select name="role" id="editRole" class="inp w-full" required onchange="updateTeamField('edit')">
            <option>Operator</option>
            <option>Supervisor</option>
            <option>Manager</option>
            <option>Admin</option>
          </select>
        </div>
        <div id="editTeamWrap">
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Tim</label>
          <select name="team" id="editTeam" class="inp w-full">
            <option value="">—</option>
            <?php foreach (['A','B','C','D','E'] as $t): ?>
            <option value="<?= $t ?>">Tim <?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status</label>
        <select name="is_active" id="editActive" class="inp w-full">
          <option value="1">Aktif</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <div class="pt-2 flex gap-2 justify-end">
        <button type="button" onclick="closeModal('modalEdit')"
                class="btn-sm text-slate-400 border border-white/10 bg-transparent hover:bg-white/5 px-4 py-2">Batal</button>
        <button type="submit" class="btn-gold">Update User</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══ FORM: DELETE / RESET (hidden) ═════════════════════════════════════ -->
<form id="formAction" method="POST" action="<?= BASE_APP ?>actions/admin/user_crud.php" style="display:none">
  <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
  <input type="hidden" name="action" id="faAction">
  <input type="hidden" name="nip"    id="faNip">
</form>

<script>
// ── Tab switching ──────────────────────────────────────────────────────────
function switchTab(name) {
  document.querySelectorAll('.tab-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.sidebar-item').forEach(s => s.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  document.getElementById('nav-' + name).classList.add('active');
  return false;
}

// ── Search / Filter ────────────────────────────────────────────────────────
function filterTable() {
  const q      = document.getElementById('searchInput').value.toLowerCase();
  const role   = document.getElementById('roleFilter').value;
  const status = document.getElementById('statusFilter').value;
  document.querySelectorAll('#userTable tbody tr').forEach(tr => {
    const matchSearch = !q || tr.dataset.search.includes(q);
    const matchRole   = !role   || tr.dataset.role   === role;
    const matchStatus = status === '' || tr.dataset.status === status;
    tr.style.display  = matchSearch && matchRole && matchStatus ? '' : 'none';
  });
}

// ── Modal helpers ──────────────────────────────────────────────────────────
function openAddModal() {
  document.getElementById('modalAdd').style.display = 'flex';
}
function openEditModal(data) {
  document.getElementById('editNip').value       = data.nip;
  document.getElementById('editFullName').value  = data.full_name;
  document.getElementById('editRole').value      = data.role;
  document.getElementById('editTeam').value      = data.team || '';
  document.getElementById('editActive').value    = String(data.is_active);
  updateTeamField('edit');
  document.getElementById('modalEdit').style.display = 'flex';
}
function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}
// Close on backdrop click
['modalAdd','modalEdit'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) closeModal(id);
  });
});

// ── Team conditional ───────────────────────────────────────────────────────
function updateTeamField(prefix) {
  const role = document.getElementById(prefix + 'Role').value;
  const wrap = document.getElementById(prefix + 'TeamWrap');
  const sel  = document.getElementById(prefix + 'Team');
  const show = role === 'Operator';
  wrap.style.opacity       = show ? '1' : '0.35';
  wrap.style.pointerEvents = show ? '' : 'none';
  if (!show) sel.value = '';
  sel.required = show;
}

// ── Delete ─────────────────────────────────────────────────────────────────────────────
function deleteUser(nip, name) {
  Swal.fire({
    title: 'Hapus User?',
    html: `Akun <strong style="color:#f87171">${name}</strong> (${nip}) akan dihapus permanen.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#374151',
    confirmButtonText: '<span style="font-weight:900;">Hapus</span>',
    cancelButtonText: 'Batal',
    background: '#0f1523',
    color: '#e2e8f0',
  }).then(r => {
    if (r.isConfirmed) {
      document.getElementById('faAction').value = 'delete_user';
      document.getElementById('faNip').value    = nip;
      document.getElementById('formAction').submit();
    }
  });
}

// ── Reset Password ─────────────────────────────────────────────────────────
function resetPassword(nip, name) {
  Swal.fire({
    title: 'Reset Password?',
    html: `Password <strong style="color:#c084fc">${name}</strong> akan direset ke default: <code style="background:#1e1e2e;padding:2px 6px;border-radius:4px;color:#FECB00">VTS@12345</code>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#a855f7',
    cancelButtonColor: '#374151',
    confirmButtonText: '<span style="font-weight:900;">Reset</span>',
    cancelButtonText: 'Batal',
    background: '#0f1523',
    color: '#e2e8f0',
  }).then(r => {
    if (r.isConfirmed) {
      document.getElementById('faAction').value = 'reset_password';
      document.getElementById('faNip').value    = nip;
      document.getElementById('formAction').submit();
    }
  });
}
</script>

<script>
// ── Chart.js initialization ─────────────────────────────────────────────
(function() {
  const GOLD   = '#FECB00';
  const NAVY   = '#001E40';
  const gridColor = 'rgba(255,255,255,0.05)';
  const labelColor = '#64748b';

  // Common chart defaults
  Chart.defaults.color = labelColor;
  Chart.defaults.borderColor = gridColor;
  Chart.defaults.font.family = "'Inter', sans-serif";

  // ── Line Chart: Traffic Trends ──
  new Chart(document.getElementById('chartTraffic'), {
    type: 'line',
    data: {
      labels: <?= json_encode($trafficLabels) ?>,
      datasets: [{
        label: 'Laporan Kapal',
        data: <?= json_encode($trafficData) ?>,
        borderColor: GOLD,
        backgroundColor: 'rgba(254,203,0,0.08)',
        borderWidth: 2.5,
        pointBackgroundColor: GOLD,
        pointBorderColor: '#0d1117',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7,
        fill: true,
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0c1220',
          borderColor: 'rgba(254,203,0,0.25)',
          borderWidth: 1,
          titleColor: '#e2e8f0',
          bodyColor: GOLD,
          displayColors: false,
        }
      },
      scales: {
        x: { grid: { color: gridColor }, ticks: { font: { size: 11 } } },
        y: { grid: { color: gridColor }, ticks: { font: { size: 11 }, precision: 0 }, beginAtZero: true },
      }
    }
  });

  // ── Bar Chart: Team Performance ──
  new Chart(document.getElementById('chartTeam'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($teamLabels) ?>,
      datasets: [{
        label: 'Total Laporan',
        data: <?= json_encode($teamData) ?>,
        backgroundColor: [
          'rgba(254,203,0,0.7)',
          'rgba(96,165,250,0.7)',
          'rgba(45,212,191,0.7)',
          'rgba(192,132,252,0.7)',
          'rgba(74,222,128,0.7)',
        ],
        borderColor: [
          GOLD,
          '#60a5fa',
          '#2dd4bf',
          '#c084fc',
          '#4ade80',
        ],
        borderWidth: 1.5,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0c1220',
          borderColor: 'rgba(254,203,0,0.25)',
          borderWidth: 1,
          titleColor: '#e2e8f0',
          bodyColor: '#e2e8f0',
          displayColors: true,
        }
      },
      scales: {
        x: { grid: { color: gridColor }, ticks: { font: { size: 11 } } },
        y: { grid: { color: gridColor }, ticks: { font: { size: 11 }, precision: 0 }, beginAtZero: true },
      }
    }
  });
})();
</script>
</body>
</html>
