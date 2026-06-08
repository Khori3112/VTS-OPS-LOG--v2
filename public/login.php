<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/app.php';
if (!empty($_SESSION['user'])) {
    redirect('/public/dashboard.php');
}
$error   = $_SESSION['flash_error']   ?? null;
$success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Masuk Sistem — VTS Palembang</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',sans-serif;background:#0d1117;color:#e2e8f0;min-height:100vh;display:flex;flex-direction:column;
       background-image:radial-gradient(rgba(255,255,255,0.045) 1px,transparent 1px);background-size:28px 28px;}
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}
  .glass-card{background:rgba(15,21,35,0.82);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
              border:1px solid rgba(255,255,255,0.09);box-shadow:0 24px 64px rgba(0,0,0,0.55)}
  .input-dark{display:block;width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);
              color:#e2e8f0;border-radius:8px;padding:11px 12px 11px 42px;font-size:14px;outline:none;
              transition:border-color .15s,background .15s,box-shadow .15s;font-family:'Inter',sans-serif}
  .input-dark::placeholder{color:rgba(255,255,255,.25)}
  .input-dark:focus{background:rgba(254,203,0,.08);border-color:rgba(254,203,0,.55);box-shadow:0 0 0 4px rgba(254,203,0,.08)}
  .input-icon{position:absolute;left:0;top:50%;transform:translate(0,-50%);color:#64748b;font-size:18px!important;pointer-events:none;}
  .input-icon-button{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:34px;height:34px;border-radius:999px;border:none;background:transparent;color:#64748b;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .2s,color .2s,transform .2s;}
  .input-icon-button:hover{background:rgba(254,203,0,.12);color:#FECB00;transform:scale(1.05);}
  .clickable-icon{cursor:pointer;transition:transform .2s,color .2s,opacity .2s;}
  .clickable-icon:hover{transform:scale(1.06);color:#FECB00;opacity:.95;}
  .icon-action:focus-visible{outline:2px solid #FECB00;outline-offset:4px;}
  .btn-login{width:100%;background:linear-gradient(135deg,#FECB00 0%,#d4a800 100%);color:#001E40;
             font-weight:800;font-size:13px;letter-spacing:.12em;text-transform:uppercase;
             border:none;border-radius:8px;padding:14px;cursor:pointer;transition:filter .15s,transform .1s,box-shadow .2s;
             font-family:'Inter',sans-serif;box-shadow:0 12px 30px rgba(254,203,0,.18)}
  .btn-login:hover{filter:brightness(1.08);transform:translateY(-1px);box-shadow:0 16px 34px rgba(254,203,0,.24)}
  .btn-login:active{transform:translateY(0);}
  @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  @keyframes floatOrb{0%{transform:translate(0,0) rotate(0deg)}50%{transform:translate(8px,8px) rotate(3deg)}100%{transform:translate(0,0) rotate(0deg)}}
  .card-animate{animation:fadeUp .4s ease both;will-change:transform,opacity;}
  .glass-card{transition:transform .25s ease,box-shadow .25s ease;}
  .glass-card:hover{transform:translateY(-4px);}
  .orb1,.orb2{animation:floatOrb 18s ease-in-out infinite;}
  .info-icon{color:#FECB00;}
  .orb1{position:fixed;top:-120px;right:-120px;width:480px;height:480px;
        background:radial-gradient(circle,rgba(0,30,64,.55) 0%,transparent 70%);pointer-events:none;z-index:0}
  .orb2{position:fixed;bottom:-120px;left:-120px;width:420px;height:420px;
        background:radial-gradient(circle,rgba(254,203,0,.08) 0%,transparent 70%);pointer-events:none;z-index:0}
</style>
</head>
<body>
<div class="orb1"></div>
<div class="orb2"></div>

<main class="flex-grow flex items-center justify-center p-6 relative z-10" style="min-height:100vh">
  <div class="w-full card-animate" style="max-width:440px">

    <!-- Brand -->
    <div class="text-center mb-8">
      <!-- Dual Logo Row -->
      <div class="flex items-center justify-center gap-4 mb-4">
        <!-- Logo Kemenhub -->
        <img src="<?= BASE_URL ?>assets/img/logo_kemenhub.png" alt="Logo Kemenhub"
             class="h-14 w-14 object-contain flex-shrink-0"
             onerror="this.style.display='none'">
        <!-- Ministry Text -->
        <div class="flex-1 text-center leading-tight px-1">
          <p class="font-black text-white text-[11px] tracking-wide uppercase" style="font-family:'Inter',sans-serif;font-weight:900;">Kementerian Perhubungan</p>
          <p class="text-slate-400 text-[9px] tracking-widest uppercase">Direktorat Jenderal Perhubungan Laut</p>
          <p class="text-[#FECB00]/70 text-[9px] tracking-widest uppercase">Distrik Navigasi Kelas I · Palembang</p>
        </div>
        <!-- Logo Navigasi -->
        <img src="<?= BASE_URL ?>assets/img/logo_navigasi.png" alt="Logo VTS"
             class="h-14 w-14 object-contain flex-shrink-0"
             onerror="this.style.display='none'">
      </div>
      <h2 class="text-xl font-black tracking-tight text-white uppercase" style="font-weight:900;">VTS PALEMBANG</h2>
      <p class="text-xs text-slate-500 tracking-widest uppercase mt-1">Vessel Traffic Services — Ops-Log System</p>
    </div>

    <!-- Card -->
    <div class="glass-card rounded-2xl overflow-hidden">

      <!-- Card Header -->
      <div class="px-8 py-5 border-b border-white/5" style="background:rgba(0,30,64,0.6)">
        <div class="flex items-center gap-3">
          <button type="button" class="material-symbols-outlined text-[#FECB00] info-icon clickable-icon icon-action" data-action="header-info" aria-label="Info keamanan" style="font-size:20px;font-variation-settings:'FILL' 1">verified_user</button>
          <div>
            <p class="text-sm font-black text-white tracking-tight">Sistem Autentikasi</p>
            <p class="text-[10px] text-slate-400 tracking-wide">Akses terbatas personel berwenang</p>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      <?php if ($error): ?>
      <div class="mx-6 mt-5 px-4 py-3 bg-red-950/60 border border-red-500/30 rounded-xl flex items-center gap-2">
        <span class="material-symbols-outlined text-red-400" style="font-size:16px">error_outline</span>
        <p class="text-red-300 text-xs"><?= htmlspecialchars((string) $error) ?></p>
      </div>
      <?php endif; ?>
      <?php if ($success): ?>
      <div class="mx-6 mt-5 px-4 py-3 bg-emerald-950/60 border border-emerald-500/30 rounded-xl flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-400" style="font-size:16px">check_circle</span>
        <p class="text-emerald-300 text-xs"><?= htmlspecialchars((string) $success) ?></p>
      </div>
      <?php endif; ?>

      <!-- Form -->
      <form action="<?= BASE_APP ?>actions/auth/login_process.php" method="POST" class="px-8 py-7 space-y-5" autocomplete="off">
        <input name="csrf_token" type="hidden" value="<?= csrfToken() ?>"/>

        <div>
          <!-- Authority Warning -->
          <div class="inline-flex items-center gap-1.5 mb-3 px-2.5 py-1 rounded-sm" style="background:rgba(220,38,38,0.1);border:1px solid rgba(220,38,38,0.25);">
            <button type="button" class="material-symbols-outlined text-red-500 info-icon clickable-icon icon-action" data-action="warning-info" aria-label="Aturan akses" style="font-size:12px;font-variation-settings:'FILL' 1">warning</button>
            <span class="text-[9px] font-black text-red-400 tracking-[.2em] uppercase" style="font-weight:900;">Authority Access Only</span>
          </div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" for="nip">
            Nomor Induk Pegawai (NIP)
          </label>
          <div class="relative">
            <button type="button" class="input-icon-button clickable-icon icon-action" data-action="focus" data-target="nip" aria-label="Fokus NIP">
              <span class="material-symbols-outlined">badge</span>
            </button>
            <input class="input-dark" id="nip" name="nip" type="text" maxlength="30"
                   placeholder="Contoh: 19801010001" required autocomplete="username"/>
          </div>
        </div>

        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest" for="password">Kata Sandi</label>
          </div>
          <div class="relative">
            <button type="button" class="input-icon-button clickable-icon icon-action" data-action="focus" data-target="password" aria-label="Fokus Kata Sandi">
              <span class="material-symbols-outlined">lock</span>
            </button>
            <input class="input-dark" id="password" name="password" type="password"
                   placeholder="••••••••" required autocomplete="current-password"
                   style="padding-right:44px"/>
            <button type="button" id="togglePw"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors"
                    tabindex="-1" aria-label="Tampilkan atau sembunyikan kata sandi">
              <span class="material-symbols-outlined" id="eyeIcon" style="font-size:18px">visibility_off</span>
            </button>
          </div>
        </div>

        <div class="flex items-start gap-3 px-3 py-3 rounded-xl" style="background:rgba(254,203,0,0.05);border:1px solid rgba(254,203,0,0.12)">
          <span class="material-symbols-outlined text-yellow-400 flex-shrink-0" style="font-size:16px;font-variation-settings:'FILL' 1">info</span>
          <p class="text-[10px] text-slate-400 leading-snug">Semua aktivitas login dipantau dan dicatat oleh sistem sesuai ketentuan Kemenhub.</p>
        </div>

        <button type="submit" class="btn-login">
          <span class="material-symbols-outlined align-middle mr-1" style="font-size:16px;font-variation-settings:'FILL' 1">login</span>
          Masuk Sistem
        </button>

        <!-- Forgot Password -->
        <div class="text-center">
          <button type="button" id="btnForgot"
                  class="text-[11px] text-slate-500 hover:text-slate-300 transition-colors tracking-wide">
            <span class="material-symbols-outlined align-middle" style="font-size:13px;">help_outline</span>
            Lupa Password?
          </button>
        </div>
      </form>

      <!-- Footer -->
      <div class="px-8 pb-6 text-center border-t border-white/5 pt-4" style="background:rgba(0,0,0,0.2)">
        <p class="text-xs text-slate-500">
          Distrik Navigasi Kelas I Palembang &mdash;
          <span class="text-slate-600">Direktorat Jenderal Perhubungan Laut</span>
        </p>
        <p class="text-[10px] text-slate-700 mt-1">
          Belum punya akun?
          <a href="<?= BASE_URL ?>register.php" class="text-[#FECB00]/60 hover:text-[#FECB00] transition-colors">Daftar di sini</a>
        </p>
      </div>
    </div>

    <p class="text-center text-[10px] text-slate-700 mt-6 tracking-widest uppercase">
      VTS Palembang &copy; <?= date('Y') ?> &mdash; Versi 1.0
    </p>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
// Toggle password
document.getElementById('togglePw')?.addEventListener('click', function() {
  const pw  = document.getElementById('password');
  const ico = document.getElementById('eyeIcon');
  pw.type          = pw.type === 'password' ? 'text' : 'password';
  ico.textContent  = pw.type === 'text' ? 'visibility' : 'visibility_off';
});

function iconActionHandler(event) {
  const action = event.currentTarget.dataset.action;
  if (!action) {
    return;
  }

  if (action === 'focus') {
    const targetId = event.currentTarget.dataset.target;
    document.getElementById(targetId)?.focus();
    return;
  }

  if (action === 'header-info') {
    Swal.fire({
      title: 'Keamanan Login',
      html: '<p>Halaman ini hanya untuk user berwenang. Semua akses dicatat dan dipantau.</p>',
      icon: 'info',
      background: '#0f1523',
      color: '#e2e8f0',
      iconColor: '#FECB00',
      confirmButtonColor: '#FECB00',
      confirmButtonText: 'Tutup',
    });
    return;
  }

  if (action === 'warning-info') {
    Swal.fire({
      title: 'Akses Terbatas',
      html: '<p>Hanya personel yang memiliki NIP dan password valid yang boleh menggunakan sistem ini.</p>',
      icon: 'warning',
      background: '#0f1523',
      color: '#e2e8f0',
      iconColor: '#FECB00',
      confirmButtonColor: '#FECB00',
      confirmButtonText: 'Mengerti',
    });
    return;
  }
}

document.querySelectorAll('.icon-action').forEach((button) => {
  button.addEventListener('click', iconActionHandler);
});

// Forgot Password flow
document.getElementById('btnForgot')?.addEventListener('click', async function() {
  const nipVal = document.getElementById('nip')?.value?.trim() || '';
  if (!nipVal) {
    Swal.fire({
      title: 'NIP Belum Diisi',
      text: 'Isi NIP Anda terlebih dahulu, lalu klik Lupa Password.',
      icon: 'warning',
      background: '#0f1523',
      color: '#e2e8f0',
      confirmButtonColor: '#FECB00',
      confirmButtonText: '<span style="color:#001e40;font-weight:900;">OK</span>',
    });
    return;
  }

  // Animate button
  const btn = this;
  btn.disabled = true;
  btn.innerHTML = '<span class="material-symbols-outlined align-middle" style="font-size:13px;">hourglass_top</span> Mengirim...';

  // Show SweetAlert2 loading popup for 2 seconds
  Swal.fire({
    title: 'Protokol Keamanan',
    html: '<span style="font-size:13px;color:#94a3b8;">Mengirim Permintaan Protokol Keamanan...</span>',
    icon: 'info',
    background: '#0f1523',
    color: '#e2e8f0',
    iconColor: '#FECB00',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: () => { Swal.showLoading(); },
    willClose: () => {}
  });

  try {
    await fetch('<?= BASE_APP ?>actions/auth/forgot_password.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'nip=' + encodeURIComponent(nipVal) + '&csrf_token=' + encodeURIComponent(document.querySelector('[name=csrf_token]')?.value || ''),
    });
  } catch (_) { /* network errors are non-critical — show popup anyway */ }

  // Wait 2 seconds then show SweetAlert
  await new Promise(r => setTimeout(r, 2000));

  btn.disabled = false;
  btn.innerHTML = '<span class="material-symbols-outlined align-middle" style="font-size:13px;">help_outline</span> Lupa Password?';

  Swal.fire({
    title: 'Permintaan Terkirim!',
    html: 'Hubungi <strong style="color:#FECB00">Admin (Khori)</strong> di <strong style="color:#FECB00">08112985835</strong> untuk verifikasi identitas &amp; reset password akun NIP <strong style="color:#FECB00">' + nipVal + '</strong>.',
    icon: 'success',
    background: '#0f1523',
    color: '#e2e8f0',
    iconColor: '#FECB00',
    confirmButtonColor: '#FECB00',
    confirmButtonText: '<span style="color:#001e40;font-weight:900;">Mengerti</span>',
  });
});
</script>
</body>
</html>