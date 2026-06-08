<?php
// views/partials/topnav.php
$_navUser      = $_SESSION['user'] ?? [];
$_navName      = htmlspecialchars((string) ($_navUser['full_name'] ?? 'Pengguna'));
$_navNip       = htmlspecialchars((string) ($_navUser['nip'] ?? '-'));
$_navTeam      = (string) ($_navUser['team'] ?? '');
$_navRole      = (string) ($_navUser['role'] ?? '');
$_navShift     = currentShiftCategory();
$_navShiftTime = match ($_navShift) {
    'Pagi'  => '08:00 – 14:00 WIB',
    'Siang' => '14:00 – 20:00 WIB',
    default => '20:00 – 08:00 WIB',
};
$_navInitials = implode('', array_map(
    fn ($w) => strtoupper(substr($w, 0, 1)),
    array_slice(explode(' ', $_navName), 0, 2)
));
$_loginTs  = $_SESSION['login_at'] ?? null;
$_loginStr = $_loginTs ? date('d M Y H:i', (int)$_loginTs) : '-';
$_ipAddr   = htmlspecialchars((string)($_SERVER['REMOTE_ADDR'] ?? '-'));
$_tippyContent = '<div style=\"text-align:left;font-size:11px;line-height:1.6\"><strong style=\"color:#FECB00\">Session Aktif</strong><br>Login: ' . $_loginStr . '<br>IP&nbsp;&nbsp;&nbsp;: <code>' . $_ipAddr . '</code><br>Role: ' . htmlspecialchars($_navRole) . '</div>';
?>
<header class="fixed top-0 w-full z-50 flex justify-between items-center px-6 h-16 bg-[#001E40]/95 backdrop-blur-md border-b border-[#FECB00]/10 shadow-lg shadow-blue-950/30 font-Inter antialiased tracking-tight">
    <div class="flex items-center gap-4">
        <div class="flex flex-col leading-none">
            <span class="text-[11px] font-black tracking-[0.2em] uppercase text-[#FECB00]">DISNAV TYPE B PALEMBANG</span>
            <span class="text-[8px] font-medium tracking-[0.3em] uppercase text-blue-300/50 mt-0.5">Kala Jivam Asti &mdash; VTS Palembang</span>
        </div>
        <div class="h-5 w-px bg-blue-800/40 mx-1"></div>
        <span class="hidden md:inline-block text-amber-400 text-[10px] font-semibold tracking-widest uppercase border-b border-amber-400/40 pb-0.5">
            Shift <?= $_navShift ?> &nbsp;|&nbsp; <?= $_navShiftTime ?>
        </span>
    </div>

    <div class="flex items-center gap-5">
        <div class="hidden lg:flex items-center gap-3">
            <div class="text-right leading-none">
                <p class="text-xs font-bold text-slate-100"><?= $_navName ?></p>
                <p class="text-[9px] text-blue-300/60 tracking-wide mt-0.5">
                    <?= htmlspecialchars($_navRole) ?><?= $_navTeam ? ' &mdash; Tim ' . htmlspecialchars($_navTeam) : '' ?>
                    &bull; NIP <?= $_navNip ?>
                </p>
            </div>
            <div class="w-9 h-9 rounded-full bg-[#FECB00] flex items-center justify-center text-[#001E40] font-black text-[11px] select-none border-2 border-yellow-300/30 shadow-inner">
                <?= htmlspecialchars($_navInitials) ?>
            </div>
        </div>

        <div class="flex items-center gap-1 text-blue-200">
            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-blue-900/40 rounded-full border border-emerald-400/20 cursor-default"
                 data-tippy-login="<?= htmlspecialchars($_tippyContent) ?>">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                <span class="text-[8px] uppercase font-bold tracking-widest text-emerald-300">Online</span>
            </div>
            <a href="<?= BASE_URL ?>logout.php"
               id="logoutLink"
               class="ml-1 p-2 rounded hover:bg-red-900/30 text-red-400 hover:text-red-300 transition-colors"
               title="Keluar dari Sistem"
               onclick="return confirmLogout(event)">
                <span class="material-symbols-outlined text-lg">logout</span>
            </a>
        </div>
    </div>
</header>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
function confirmLogout(e) {
    if (typeof Swal === 'undefined') return true; // fallback: allow redirect
    e.preventDefault();
    var href = document.getElementById('logoutLink').href;
    Swal.fire({
        title: 'Keluar dari Sistem?',
        text: 'Sesi Anda akan diakhiri dan laporan yang belum dikunci akan tetap tersimpan sebagai draft.',
        icon: 'warning',
        background: '#0f1523',
        color: '#e2e8f0',
        iconColor: '#FECB00',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#334155',
        confirmButtonText: '<span style="font-size:13px;font-weight:700">Ya, Logout</span>',
        cancelButtonText: '<span style="font-size:13px">Batal</span>',
        reverseButtons: true,
        customClass: { popup: 'rounded-2xl border border-white/10' }
    }).then(function(result) {
        if (result.isConfirmed) window.location.href = href;
    });
    return false;
}
</script>

