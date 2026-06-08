<?php
// views/partials/sidenav.php
$_sideRole    = (string) ($_SESSION['user']['role'] ?? '');
$_sideCurrent = basename($_SERVER['PHP_SELF'] ?? '');
$_sideQS      = (string) ($_SERVER['QUERY_STRING'] ?? '');
$_sideTeam    = (string) ($_SESSION['user']['team'] ?? '');
$_sideName    = htmlspecialchars((string) ($_SESSION['user']['full_name'] ?? 'Pengguna'));
$_sideNip     = htmlspecialchars((string) ($_SESSION['user']['nip'] ?? '-'));
$_sideShift   = currentShiftCategory();
$_sideInitial = strtoupper(substr(strip_tags($_sideName), 0, 1));
$_sideIsLocked = isset($isLocked) && $isLocked;

if (!function_exists('_sideLink')) {
    function _sideLink(string $href, string $icon, string $label, bool $active = false, string $badge = ''): string
    {
        $base = $active
            ? 'flex items-center gap-3 bg-[#001E40] border-l-2 border-[#FECB00] text-[#FECB00] rounded-r-lg px-3 py-2.5 mx-0 pl-3.5 font-bold'
            : 'flex items-center gap-3 text-slate-400 px-3 py-2.5 mx-2 hover:bg-white/5 hover:text-white rounded-lg transition-colors';
        $fill = $active ? ' style="font-variation-settings: \'FILL\' 1; color:#FECB00"' : '';
        $badgeHtml = $badge !== '' ? '<span class="ml-auto text-[9px] font-black bg-red-500 text-white px-1.5 py-0.5 rounded-full">' . htmlspecialchars($badge) . '</span>' : '';
        return '<a class="' . $base . '" href="' . htmlspecialchars($href, ENT_QUOTES) . '">'
             . '<span class="material-symbols-outlined text-[20px]"' . $fill . '>' . htmlspecialchars($icon) . '</span>'
             . '<span class="flex-1 truncate">' . htmlspecialchars($label) . '</span>' . $badgeHtml . '</a>';
    }
}
?>
<aside class="h-screen w-64 fixed left-0 top-0 flex flex-col py-0 font-['Inter'] text-sm font-medium z-40"
       style="background:rgba(10,15,26,0.97);border-right:1px solid rgba(255,255,255,0.06);backdrop-filter:blur(20px)">

    <!-- Brand + Logo -->
    <div class="flex items-center gap-3 px-5 h-16 border-b border-white/5 flex-shrink-0">
        <div class="relative w-8 h-8 flex-shrink-0">
            <img src="<?= BASE_APP ?>assets/img/logo_navigasi.png" alt="Logo"
                 class="w-8 h-8 object-contain rounded-full border border-[#FECB00]/30 absolute inset-0"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div style="display:none"
                 class="absolute inset-0 w-8 h-8 rounded-full bg-[#001E40] border border-[#FECB00]/40
                        items-center justify-center text-[#FECB00] font-black text-[9px]">VTS</div>
        </div>
        <div>
            <p class="text-white font-black text-sm tracking-tight leading-none">VTS PALEMBANG</p>
            <p class="text-[9px] text-slate-500 tracking-widest uppercase mt-0.5">Ops-Log System</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-3 space-y-0.5">
        <p class="px-5 pb-1 pt-2 text-[9px] font-black tracking-widest uppercase text-slate-600">Navigasi</p>
        <?php
        $dashHref = BASE_URL . 'dashboard.php';
        $isDash   = in_array($_sideCurrent, ['dashboard.php', 'manager_approval.php'], true)
                    && !str_contains($_sideQS, 'tab=');
        echo _sideLink($dashHref, 'dashboard', 'Dashboard', $isDash);

        if (in_array($_sideRole, ['Operator', 'Supervisor'], true)) {
            $isA1 = str_contains($_sideQS, 'tab=a1') || str_contains($_sideQS, 'tab=a2') || str_contains($_sideQS, 'tab=a3');
            echo _sideLink(BASE_URL . 'dashboard.php?tab=a1', 'assignment', 'Input Rutin A1–A3', $isA1);

            $isA6 = str_contains($_sideQS, 'tab=a6') || str_contains($_sideQS, 'tab=a7');
            echo _sideLink(BASE_URL . 'dashboard.php?tab=a6', 'warning', 'Situasi Khusus A6–A7', $isA6);
        }

        if (in_array($_sideRole, ['Supervisor', 'Manager'], true)) {
            $isAudit = str_contains($_sideCurrent, 'audit_logs');
            echo _sideLink(BASE_URL . 'audit_logs.php', 'manage_history', 'Audit Log Shift', $isAudit);
        }

        if ($_sideRole === 'Manager') {
            $isApproval = $_sideCurrent === 'manager_approval.php' && !str_contains($_sideQS, 'tab=users');
            echo _sideLink(BASE_URL . 'manager_approval.php', 'fact_check', 'Approval Laporan', $isApproval);

            $isUsers = str_contains($_sideQS, 'tab=users');
            echo _sideLink(BASE_URL . 'manager_approval.php?tab=users', 'person_add', 'Kelola Pengguna', $isUsers);

            $isAuditMgr = str_contains($_sideCurrent, 'audit_logs');
            echo _sideLink(BASE_URL . 'audit_logs.php', 'manage_history', 'Audit Log Sistem', $isAuditMgr);
        }
        ?>

        <?php if ($_sideIsLocked): ?>
        <p class="px-5 pb-1 pt-3 text-[9px] font-black tracking-widest uppercase text-slate-600">Dokumen</p>
        <?php echo _sideLink(BASE_URL . 'export_pdf.php', 'picture_as_pdf', 'Cetak PDF Laporan'); ?>
        <?php elseif (in_array($_sideRole, ['Operator', 'Supervisor'], true)): ?>
        <p class="px-5 pb-1 pt-3 text-[9px] font-black tracking-widest uppercase text-slate-600">Dokumen</p>
        <div class="flex items-center gap-3 text-slate-600 px-3 py-2.5 mx-2 rounded-lg cursor-not-allowed select-none"
             title="PDF tersedia setelah laporan dikunci">
            <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
            <span class="flex-1 truncate">Cetak PDF</span>
            <span class="text-[9px] bg-slate-700 text-slate-400 px-1.5 py-0.5 rounded">Belum Kunci</span>
        </div>
        <?php else: ?>
        <p class="px-5 pb-1 pt-3 text-[9px] font-black tracking-widest uppercase text-slate-600">Dokumen</p>
        <?php echo _sideLink(BASE_URL . 'export_pdf.php', 'picture_as_pdf', 'Export PDF'); ?>
        <?php endif; ?>
    </nav>

    <!-- User Profile Card -->
    <div class="px-4 pt-3 pb-4 border-t border-white/5 flex-shrink-0">
        <div class="flex items-center gap-3 p-3 rounded-xl mb-2" style="background:rgba(255,255,255,0.05)">
            <div class="w-9 h-9 rounded-full bg-[#001E40] flex items-center justify-center text-[#FECB00] font-black flex-shrink-0 text-sm border border-[#FECB00]/30">
                <?= $_sideInitial ?>
            </div>
            <div class="min-w-0">
                <p class="text-white font-bold leading-none truncate text-sm"><?= $_sideName ?></p>
                <p class="text-[9px] text-slate-400 mt-0.5">
                    <?= htmlspecialchars($_sideShift) ?> &bull;
                    <?= $_sideTeam ? 'Tim ' . htmlspecialchars($_sideTeam) : htmlspecialchars($_sideRole) ?>
                </p>
            </div>
        </div>
        <a class="flex items-center gap-3 text-red-400 px-3 py-2 hover:bg-red-900/20 rounded-lg transition-colors"
           href="<?= BASE_URL ?>logout.php" id="sideLogoutLink" onclick="return confirmLogout(event)">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="text-sm">Logout</span>
        </a>
    </div>
</aside>