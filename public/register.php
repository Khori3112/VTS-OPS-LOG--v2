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
<title>Registrasi Akun — VTS Palembang</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',sans-serif;background:#0d1117;color:#e2e8f0;min-height:100vh;display:flex;flex-direction:column;
       background-image:radial-gradient(rgba(255,255,255,0.045) 1px,transparent 1px);background-size:28px 28px;}
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24}
  .ms-fill{font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24}
  .glass-card{background:rgba(15,21,35,0.82);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
              border:1px solid rgba(255,255,255,0.09);box-shadow:0 24px 64px rgba(0,0,0,0.55)}
  .input-dark{display:block;width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);
              color:#e2e8f0;border-radius:8px;padding:11px 12px 11px 42px;font-size:14px;outline:none;
              transition:border-color .15s,background .15s;font-family:'Inter',sans-serif}
  .input-dark::placeholder{color:rgba(255,255,255,.25)}
  .input-dark:focus{background:rgba(254,203,0,.05);border-color:rgba(254,203,0,.5)}
  .select-dark{display:block;width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);
               color:#e2e8f0;border-radius:8px;padding:11px 12px;font-size:14px;outline:none;
               transition:border-color .15s;font-family:'Inter',sans-serif;appearance:none;cursor:pointer}
  .select-dark:focus{background:rgba(254,203,0,.05);border-color:rgba(254,203,0,.5)}
  .select-dark option{background:#0f1523;color:#e2e8f0}
  .input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#64748b;pointer-events:none;font-size:18px!important}
  .btn-primary{width:100%;background:linear-gradient(135deg,#FECB00 0%,#d4a800 100%);color:#001E40;
               font-weight:900;font-size:13px;letter-spacing:.12em;text-transform:uppercase;
               border:none;border-radius:8px;padding:14px;cursor:pointer;transition:filter .15s,transform .1s;
               font-family:'Inter',sans-serif}
  .btn-primary:hover{filter:brightness(1.08)}
  .btn-primary:active{transform:scale(.98)}
  @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  .card-animate{animation:fadeUp .4s ease both}
  .orb1{position:fixed;top:-120px;right:-120px;width:480px;height:480px;
        background:radial-gradient(circle,rgba(0,30,64,.55) 0%,transparent 70%);pointer-events:none;z-index:0}
  .orb2{position:fixed;bottom:-120px;left:-120px;width:420px;height:420px;
        background:radial-gradient(circle,rgba(254,203,0,.08) 0%,transparent 70%);pointer-events:none;z-index:0}
  #teamGroup{transition:opacity .25s,max-height .3s ease;overflow:hidden;max-height:200px}
  #teamGroup.hidden-field{opacity:0;max-height:0;pointer-events:none}
</style>
</head>
<body>
<div class="orb1"></div>
<div class="orb2"></div>

<main class="flex-grow flex items-center justify-center p-6 relative z-10" style="min-height:100vh">
  <div class="w-full card-animate" style="max-width:480px">

    <!-- Brand -->
    <div class="text-center mb-8">
      <div class="flex items-center justify-center gap-4 mb-4">
        <img src="<?= BASE_URL ?>assets/img/logo_kemenhub.png" alt="Logo Kemenhub"
             class="h-12 w-12 object-contain flex-shrink-0" onerror="this.style.display='none'">
        <div class="flex-1 text-center leading-tight px-1">
          <p class="font-black text-white text-[11px] tracking-wide uppercase" style="font-weight:900;">Kementerian Perhubungan</p>
          <p class="text-slate-400 text-[9px] tracking-widest uppercase">Direktorat Jenderal Perhubungan Laut</p>
          <p class="text-[#FECB00]/70 text-[9px] tracking-widest uppercase">Distrik Navigasi Kelas I · Palembang</p>
        </div>
        <img src="<?= BASE_URL ?>assets/img/logo_navigasi.png" alt="Logo VTS"
             class="h-12 w-12 object-contain flex-shrink-0" onerror="this.style.display='none'">
      </div>
      <h2 class="text-xl font-black tracking-tight text-white uppercase" style="font-weight:900;">Registrasi Akun</h2>
      <p class="text-xs text-slate-500 tracking-widest uppercase mt-1">VTS OPS-LOG · Sistem Pendaftaran Personel</p>
    </div>

    <!-- Card -->
    <div class="glass-card rounded-2xl overflow-hidden">

      <!-- Card Header -->
      <div class="px-8 py-5 border-b border-white/5" style="background:rgba(0,30,64,0.6)">
        <div class="flex items-center gap-3">
          <span class="material-symbols-outlined ms-fill text-[#FECB00]" style="font-size:20px;">person_add</span>
          <div>
            <p class="text-sm font-black text-white tracking-tight" style="font-weight:900;">Pendaftaran Personel Baru</p>
            <p class="text-[10px] text-slate-400 tracking-wide">Akun langsung aktif &amp; siap login</p>
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
      <form action="<?= BASE_APP ?>actions/auth/register_process.php" method="POST"
            class="px-8 py-7 space-y-5" autocomplete="off">
        <input name="csrf_token" type="hidden" value="<?= csrfToken() ?>"/>

        <!-- Nama -->
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" for="full_name">
            Nama Lengkap
          </label>
          <div class="relative">
            <span class="material-symbols-outlined input-icon">person</span>
            <input class="input-dark" id="full_name" name="full_name" type="text" maxlength="120"
                   placeholder="Nama sesuai SK/identitas" required
                   value="<?= htmlspecialchars((string) ($_POST['full_name'] ?? '')) ?>"/>
          </div>
        </div>

        <!-- NIP -->
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" for="nip">
            Nomor Induk Pegawai (NIP)
          </label>
          <div class="relative">
            <span class="material-symbols-outlined input-icon">badge</span>
            <input class="input-dark" id="nip" name="nip" type="text" maxlength="30"
                   placeholder="Contoh: 19801010001" required autocomplete="off"
                   value="<?= htmlspecialchars((string) ($_POST['nip'] ?? '')) ?>"/>
          </div>
        </div>

        <!-- Role -->
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" for="role">
            Jabatan / Role
          </label>
          <div class="relative">
            <select class="select-dark" id="role" name="role" required>
              <option value="" disabled <?= empty($_POST['role']) ? 'selected' : '' ?>>— Pilih Role —</option>
              <option value="Operator"   <?= ($_POST['role'] ?? '') === 'Operator'   ? 'selected' : '' ?>>Operator</option>
              <option value="Supervisor" <?= ($_POST['role'] ?? '') === 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
              <option value="Manager"    <?= ($_POST['role'] ?? '') === 'Manager'    ? 'selected' : '' ?>>Manager</option>
            </select>
            <span class="material-symbols-outlined pointer-events-none"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#64748b;font-size:18px;">
              expand_more
            </span>
          </div>
        </div>

        <!-- Tim (conditional: Operator only) -->
        <div id="teamGroup" class="hidden-field">
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" for="team">
            Tim Shift
          </label>
          <div class="relative">
            <select class="select-dark" id="team" name="team">
              <option value="" disabled selected>— Pilih Tim (A–E) —</option>
              <?php foreach (['A','B','C','D','E'] as $t): ?>
              <option value="<?= $t ?>" <?= ($_POST['team'] ?? '') === $t ? 'selected' : '' ?>>Tim <?= $t ?></option>
              <?php endforeach; ?>
            </select>
            <span class="material-symbols-outlined pointer-events-none"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#64748b;font-size:18px;">
              expand_more
            </span>
          </div>
        </div>

        <!-- Password -->
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" for="password">
            Kata Sandi
          </label>
          <div class="relative">
            <span class="material-symbols-outlined input-icon">lock</span>
            <input class="input-dark" id="password" name="password" type="password"
                   placeholder="Min. 8 karakter" required autocomplete="new-password"
                   style="padding-right:44px" minlength="8"/>
            <button type="button" id="togglePw"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors"
                    tabindex="-1">
              <span class="material-symbols-outlined" id="eyeIcon" style="font-size:18px">visibility_off</span>
            </button>
          </div>
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" for="password_confirm">
            Konfirmasi Kata Sandi
          </label>
          <div class="relative">
            <span class="material-symbols-outlined input-icon">lock_reset</span>
            <input class="input-dark" id="password_confirm" name="password_confirm" type="password"
                   placeholder="Ulangi kata sandi" required autocomplete="new-password"
                   style="padding-right:44px" minlength="8"/>
            <button type="button" id="togglePwConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors"
                    tabindex="-1">
              <span class="material-symbols-outlined" id="eyeIconConfirm" style="font-size:18px">visibility_off</span>
            </button>
          </div>
        </div>

        <!-- Info box -->
        <div class="flex items-start gap-3 px-3 py-3 rounded-xl"
             style="background:rgba(254,203,0,0.05);border:1px solid rgba(254,203,0,0.12)">
          <span class="material-symbols-outlined ms-fill text-yellow-400 flex-shrink-0" style="font-size:16px;">info</span>
          <p class="text-[10px] text-slate-400 leading-snug">
            Akun baru akan <span class="text-green-400 font-semibold">langsung aktif</span> dan dapat digunakan untuk login segera setelah registrasi.
            Password disimpan dengan enkripsi <span class="text-slate-300 font-semibold">Argon2id</span>.
          </p>
        </div>

        <button type="submit" class="btn-primary">
          <span class="material-symbols-outlined ms-fill align-middle mr-1" style="font-size:16px;">how_to_reg</span>
          Daftar Sekarang
        </button>
      </form>

      <!-- Footer -->
      <div class="px-8 pb-6 text-center border-t border-white/5 pt-4" style="background:rgba(0,0,0,0.2)">
        <p class="text-xs text-slate-500">
          Sudah punya akun?
          <a href="<?= BASE_URL ?>login.php" class="text-[#FECB00]/80 hover:text-[#FECB00] font-semibold transition-colors">
            Masuk di sini
          </a>
        </p>
      </div>
    </div>

    <p class="text-center text-[10px] text-slate-700 mt-6 tracking-widest uppercase">
      VTS Palembang &copy; <?= date('Y') ?> &mdash; Sovereign Engine v5.1
    </p>
  </div>
</main>

<script>
// Show/hide password toggle
function makeToggle(btnId, inputId, iconId) {
  const btn   = document.getElementById(btnId);
  const input = document.getElementById(inputId);
  const icon  = document.getElementById(iconId);
  if (!btn || !input || !icon) return;
  btn.addEventListener('click', () => {
    const isHidden = input.type === 'password';
    input.type       = isHidden ? 'text' : 'password';
    icon.textContent = isHidden ? 'visibility' : 'visibility_off';
  });
}
makeToggle('togglePw',        'password',         'eyeIcon');
makeToggle('togglePwConfirm', 'password_confirm', 'eyeIconConfirm');

// Conditional team field
const roleEl  = document.getElementById('role');
const teamGrp = document.getElementById('teamGroup');
const teamEl  = document.getElementById('team');

function updateTeamVisibility() {
  const isOperator = roleEl.value === 'Operator';
  teamGrp.classList.toggle('hidden-field', !isOperator);
  teamEl.required = isOperator;
  if (!isOperator) teamEl.value = '';
}
roleEl.addEventListener('change', updateTeamVisibility);
<?php if (($_POST['role'] ?? '') === 'Operator'): ?>
// Restore team visibility if POST-back with Operator role
document.addEventListener('DOMContentLoaded', updateTeamVisibility);
<?php endif; ?>
</script>
</body>
</html>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "inverse-on-surface": "#eff1f6",
        "on-surface-variant": "#43474f",
        "on-primary-fixed-variant": "#1f477b",
        "tertiary-fixed": "#ffdbca",
        "on-secondary": "#ffffff",
        "on-primary": "#ffffff",
        "tertiary-container": "#592300",
        "surface-container-lowest": "#ffffff",
        "surface": "#f7f9fe",
        "primary-fixed": "#d5e3ff",
        "surface-container": "#eceef3",
        "on-background": "#181c20",
        "inverse-primary": "#a7c8ff",
        "on-tertiary": "#ffffff",
        "outline-variant": "#c3c6d1",
        "surface-dim": "#d8dadf",
        "inverse-surface": "#2d3135",
        "surface-tint": "#3a5f94",
        "on-secondary-container": "#6e5700",
        "surface-container-high": "#e6e8ed",
        "error-container": "#ffdad6",
        "on-secondary-fixed-variant": "#584400",
        "on-secondary-fixed": "#241a00",
        "surface-bright": "#f7f9fe",
        "secondary": "#745b00",
        "on-tertiary-container": "#d8885c",
        "secondary-container": "#fecb00",
        "on-surface": "#181c20",
        "background": "#f7f9fe",
        "secondary-fixed-dim": "#f1c100",
        "on-tertiary-fixed": "#341100",
        "on-primary-container": "#799dd6",
        "error": "#ba1a1a",
        "primary": "#001e40",
        "secondary-fixed": "#ffe08b",
        "surface-container-low": "#f1f4f9",
        "primary-fixed-dim": "#a7c8ff",
        "on-tertiary-fixed-variant": "#723610",
        "primary-container": "#003366",
        "surface-container-highest": "#e0e2e7",
        "outline": "#737780",
        "tertiary-fixed-dim": "#ffb690",
        "tertiary": "#381300",
        "on-error": "#ffffff",
        "on-primary-fixed": "#001b3c",
        "surface-variant": "#e0e2e7",
        "on-error-container": "#93000a"
      },
      fontFamily: { "headline": ["Inter"], "body": ["Inter"], "label": ["Inter"] },
      borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
    },
  },
}
</script>
<style>
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  .industrial-gradient { background: linear-gradient(135deg, #001e40 0%, #003366 100%); }
  .glass-panel { background: rgba(255,255,255,0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
</style>
</head>
<body class="bg-background font-body text-on-surface min-h-screen flex items-center justify-center p-4 md:p-8">
<main class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 overflow-hidden rounded-xl shadow-xl bg-surface-container-lowest">
  <!-- Left Column -->
  <section class="md:col-span-5 industrial-gradient p-10 flex flex-col justify-between text-white">
    <div class="space-y-8">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-white p-1 rounded-lg flex items-center justify-center">
          <img alt="Logo Kemenhub" class="w-full h-full object-contain" src="<?= BASE_URL ?>assets/img/logo_navigasi.png" onerror="this.style.display='none'"/>
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tighter uppercase leading-none">VTS PALEMBANG</h1>
          <p class="text-[10px] tracking-[0.2em] opacity-70 uppercase font-semibold">Vessel Traffic Service</p>
        </div>
      </div>
      <div class="space-y-4">
        <h2 class="text-3xl font-extrabold tracking-tight leading-tight">Registrasi Personnel Operasional.</h2>
        <p class="text-on-primary-container text-sm leading-relaxed max-w-sm">Silakan lengkapi formulir pendaftaran untuk mendapatkan akses ke Sistem Logistik dan Pemantauan Kapal VTS Palembang.</p>
      </div>
      <div class="glass-panel p-4 rounded-lg border border-white/10 space-y-3">
        <div class="flex justify-between items-center">
          <span class="text-[10px] font-bold uppercase tracking-widest text-secondary-container">System Status</span>
          <span class="flex h-2 w-2 rounded-full bg-secondary-container animate-pulse"></span>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-[10px] opacity-60 uppercase">Encryption</p>
            <p class="text-xs font-mono font-bold text-white">Argon2id</p>
          </div>
          <div>
            <p class="text-[10px] opacity-60 uppercase">Protocol</p>
            <p class="text-xs font-mono font-bold text-white">HTTPS/TLS 1.3</p>
          </div>
        </div>
      </div>
    </div>
    <div class="pt-8">
      <p class="text-[10px] text-on-primary-container/60 uppercase tracking-[0.15em] font-medium">&copy; <?= date('Y') ?> Kemenhub RI - Distrik Navigasi Palembang</p>
    </div>
  </section>
  <!-- Right Column -->
  <section class="md:col-span-7 bg-white p-10">
    <div class="mb-10 flex justify-between items-end">
      <div>
        <h3 class="text-2xl font-bold text-primary">Buat Akun Baru</h3>
        <p class="text-sm text-on-surface-variant">Pastikan data yang diinput sesuai dengan NIP resmi.</p>
      </div>
      <span class="material-symbols-outlined text-primary text-4xl opacity-10">person_add</span>
    </div>
    <?php if ($error): ?>
    <div class="mb-6 px-4 py-3 bg-error-container border border-error/20 rounded-lg flex items-center gap-2">
      <span class="material-symbols-outlined text-error text-[16px]">error_outline</span>
      <p class="text-on-error-container text-xs"><?= htmlspecialchars((string) $error) ?></p>
    </div>
    <?php endif; ?>
    <form action="<?= BASE_APP ?>actions/auth/register_process.php" class="space-y-6" method="POST">
      <input name="csrf_token" type="hidden" value="<?= csrfToken() ?>"/>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="nip">NIP Personnel</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">badge</span>
            <input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all placeholder:text-outline-variant text-sm font-medium" id="nip" maxlength="30" name="nip" placeholder="19XXXXXXXXXXXXXX" required type="text"/>
          </div>
        </div>
        <div class="space-y-2">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="nama">Nama Lengkap</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">person</span>
            <input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all placeholder:text-outline-variant text-sm font-medium" id="nama" name="nama" placeholder="Masukkan Nama Sesuai SK" required type="text"/>
          </div>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="team">Tim Operasional (Team)</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">groups</span>
            <select class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all text-sm font-medium appearance-none" id="team" name="team" required>
              <option disabled selected value="">Pilih Tim</option>
              <option value="A">Team A</option>
              <option value="B">Team B</option>
              <option value="C">Team C</option>
              <option value="D">Team D</option>
              <option value="E">Team E</option>
            </select>
          </div>
        </div>
        <div class="space-y-2">
          <label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="role">Peran (Role)</label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">admin_panel_settings</span>
            <select class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all text-sm font-medium appearance-none" id="role" name="role" required>
              <option disabled selected value="">Pilih Peran</option>
              <option value="Operator">Operator</option>
              <option value="Supervisor">Supervisor</option>
              <option value="Manager">Manager</option>
            </select>
          </div>
        </div>
      </div>
      <div class="space-y-2">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="password">Password Keamanan</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">lock</span>
          <input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all placeholder:text-outline-variant text-sm font-medium" id="password" name="password" placeholder="••••••••" required type="password"/>
        </div>
        <div class="flex items-center gap-2 mt-2 px-1">
          <span class="material-symbols-outlined text-[16px] text-secondary font-bold">shield_lock</span>
          <p class="text-[10px] text-on-surface-variant font-medium">Security Note: Password akan diamankan menggunakan algoritma hashing <span class="text-primary font-bold">Argon2id</span> (Industrial Standard).</p>
        </div>
      </div>
      <div class="pt-4 flex flex-col gap-4">
        <button class="industrial-gradient w-full py-4 rounded-lg text-white font-bold text-sm tracking-widest uppercase hover:opacity-90 active:scale-[0.98] transition-all shadow-lg flex items-center justify-center gap-3" type="submit">
          Daftarkan Personnel
          <span class="material-symbols-outlined text-xl">how_to_reg</span>
        </button>
        <div class="flex items-center justify-center gap-2 pt-2">
          <span class="text-xs text-on-surface-variant">Sudah memiliki akun?</span>
          <a class="text-xs font-bold text-primary hover:underline flex items-center gap-1" href="<?= BASE_URL ?>login.php">
            Masuk ke Sistem
            <span class="material-symbols-outlined text-sm">login</span>
          </a>
        </div>
      </div>
    </form>
  </section>
</main>
<footer class="fixed bottom-0 left-0 w-full p-4 flex justify-between items-center pointer-events-none">
  <div class="text-[9px] uppercase tracking-[0.3em] text-outline font-bold opacity-40">VTS PALEMBANG // SYSTEM_REGISTRATION_V2.4</div>
  <div class="text-[9px] uppercase tracking-[0.3em] text-outline font-bold opacity-40">SECURE ACCESS ONLY</div>
</footer>
</body>
</html>