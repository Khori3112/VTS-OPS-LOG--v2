<!DOCTYPE html>
<html lang="id" class="scroll-smooth"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>VTS OPS-LOG — Distrik Navigasi Kelas I Palembang</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
  *{box-sizing:border-box;}
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
  .ms-fill{font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;}
  body{background:#f8fafc;color:#1e293b;}
  /* Navy stripe header */
  .ministry-bar{background:linear-gradient(135deg,#001E40 0%,#002d5e 100%);}
  /* Gold accent line */
  .gold-bar{background:#FECB00;height:4px;}
  .gold-line{background:linear-gradient(90deg,transparent 0%,#FECB00 50%,transparent 100%);height:2px;}
  /* Nav */
  .nav-white{background:rgba(255,255,255,0.97);backdrop-filter:blur(10px);border-bottom:1px solid #e2e8f0;}
  .nav-link{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#475569;transition:color .15s;}
  .nav-link:hover,.nav-link.active{color:#001E40;}
  .nav-link.active{border-bottom:2px solid #FECB00;padding-bottom:2px;}
  /* Card styles */
  .service-card{background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;transition:all .25s ease;box-shadow:0 1px 3px rgba(0,0,0,.06);}
  .service-card:hover{border-color:#FECB00;box-shadow:0 8px 24px rgba(0,30,64,.1);transform:translateY(-3px);}
  .stat-card-w{background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.05);}
  .info-card{background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;padding:20px 24px;}
  .badge-navy{display:inline-flex;align-items:center;gap:6px;background:rgba(0,30,64,.08);color:#001E40;border:1px solid rgba(0,30,64,.15);border-radius:20px;padding:4px 14px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;}
  /* Hero */
  .hero-bg{background:linear-gradient(160deg,#001E40 0%,#003070 55%,#001a38 100%);}
  /* Animations */
  @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
  .fu{animation:fadeUp .5s cubic-bezier(.16,1,.3,1) both;}
  .fu-1{animation-delay:.05s;}.fu-2{animation-delay:.15s;}.fu-3{animation-delay:.25s;}.fu-4{animation-delay:.35s;}
  @keyframes pulse-slow{0%,100%{opacity:1;}50%{opacity:.4;}}
  .pulse-slow{animation:pulse-slow 2.5s ease-in-out infinite;}
  /* Logo fallback */
  .logo-fallback{display:none;background:#001E40;border:2px solid rgba(254,203,0,.5);color:#FECB00;font-weight:900;font-size:11px;align-items:center;justify-content:center;text-align:center;border-radius:6px;}
  /* Footer */
  .footer-navy{background:#001E40;}
  address a{color:inherit;}
  address a:hover{color:#FECB00;}
</style>
</head>
<body class="bg-[#f8fafc] text-slate-700 font-['Inter'] antialiased">

<!-- ═══ FIXED HEADER — Government Institutional (White) ══════════════════ -->
<header class="fixed top-0 w-full z-50 shadow-sm">
  <!-- Gold accent stripe -->
  <div class="gold-bar"></div>
  <!-- Tier 1: Ministry Branding Bar (Navy) -->
  <div class="ministry-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between py-3 gap-4">
      <!-- Logo Kemenhub -->
      <div class="flex-shrink-0">
        <img src="<?= BASE_URL ?>assets/img/logo_kemenhub.png" alt="Logo Kementerian Perhubungan"
             class="h-14 w-14 object-contain"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="logo-fallback h-14 w-14">KH</div>
      </div>
      <!-- Ministry Text — Center -->
      <div class="flex-1 text-center select-none">
        <p class="text-white font-black tracking-[.08em] uppercase text-sm leading-snug"
           style="font-weight:900;">KEMENTERIAN PERHUBUNGAN</p>
        <p class="text-[#FECB00] font-bold tracking-[.05em] uppercase text-[11px] leading-snug mt-0.5">
          DIREKTORAT JENDERAL PERHUBUNGAN LAUT</p>
        <p class="text-blue-200/80 font-medium tracking-[.04em] uppercase text-[10px] leading-snug mt-0.5">
          DISTRIK NAVIGASI TIPE A KELAS I PALEMBANG</p>
      </div>
      <!-- Logo Navigasi -->
      <div class="flex-shrink-0">
        <img src="<?= BASE_URL ?>assets/img/logo_navigasi.png" alt="Logo Distrik Navigasi"
             class="h-14 w-14 object-contain"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="logo-fallback h-14 w-14">VTS</div>
      </div>
    </div>
  </div>
  <!-- Tier 2: White Navigation Bar -->
  <nav class="nav-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex items-center justify-between h-12">
      <div class="hidden md:flex items-center gap-7">
        <a href="#beranda" class="nav-link active">Beranda</a>
        <a href="#layanan" class="nav-link">Layanan</a>
        <a href="#informasi" class="nav-link">Informasi</a>
        <a href="#kontak" class="nav-link">Kontak</a>
      </div>
      <div class="hidden md:flex items-center gap-2 text-[10px] text-slate-400 font-mono">
        <span class="w-1.5 h-1.5 rounded-full bg-green-500 pulse-slow inline-block"></span>
        SYS ONLINE · RADAR &amp; AIS AKTIF
      </div>
      <a href="<?= BASE_URL ?>login.php">
        <button class="text-[10px] font-black tracking-widest uppercase bg-[#001E40] text-white px-5 py-2 rounded hover:bg-[#002d5e] active:scale-95 transition-all">
          MASUK SISTEM
        </button>
      </a>
    </div>
  </nav>
</header>

<!-- ═══ HERO SECTION — Clean Institutional ════════════════════════════════ -->
<section id="beranda" class="hero-bg relative overflow-hidden pt-40 pb-24 px-6">
  <!-- Subtle geometric accent -->
  <div class="absolute inset-0 opacity-5" style="background-image:repeating-linear-gradient(0deg,transparent,transparent 40px,rgba(255,255,255,.5) 40px,rgba(255,255,255,.5) 41px),repeating-linear-gradient(90deg,transparent,transparent 40px,rgba(255,255,255,.5) 40px,rgba(255,255,255,.5) 41px);"></div>
  <!-- Gold vertical bar accents -->
  <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#FECB00]"></div>

  <div class="relative z-10 max-w-5xl mx-auto text-center">
    <!-- Tab badge -->
    <div class="fu fu-1 flex justify-center mb-6">
      <span class="badge-navy" style="background:rgba(254,203,0,.15);color:#FECB00;border-color:rgba(254,203,0,.35);">
        <span class="w-1.5 h-1.5 rounded-full bg-[#FECB00] pulse-slow inline-block"></span>
        VESSEL TRAFFIC SERVICE · SISTEM OPERASIONAL
      </span>
    </div>
    <!-- Main title -->
    <h1 class="fu fu-2 font-black text-white leading-none tracking-tight mb-3 select-none"
        style="font-size:clamp(2.6rem,8vw,5.5rem);font-weight:900;">
      VTS OPS-LOG
    </h1>
    <h2 class="fu fu-2 font-black tracking-widest uppercase text-[#FECB00] mb-6"
        style="font-size:clamp(1rem,3vw,1.75rem);font-weight:900;letter-spacing:.18em;">
      PALEMBANG
    </h2>
    <!-- Subtitle -->
    <p class="fu fu-3 text-blue-100/80 text-base md:text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
      Pusat pengawasan dan manajemen lalu lintas kapal terintegrasi — memastikan keselamatan pelayaran
      dan perlindungan lingkungan maritim di wilayah perairan Palembang secara
      <span class="text-white font-semibold">24 jam, 7 hari</span>.
    </p>
    <!-- CTA Buttons -->
    <div class="fu fu-4 flex flex-col sm:flex-row gap-4 justify-center mb-14">
      <a href="<?= BASE_URL ?>login.php">
        <button class="flex items-center justify-center gap-3 font-black text-sm tracking-[.1em] uppercase
                       bg-[#FECB00] text-[#001E40] px-10 py-4 rounded hover:brightness-110
                       active:scale-95 transition-all shadow-lg shadow-[#FECB00]/25"
                style="font-weight:900;">
          <span class="material-symbols-outlined ms-fill" style="font-size:18px;">login</span>
          MASUK SISTEM
        </button>
      </a>
      <a href="<?= BASE_URL ?>register.php">
        <button class="flex items-center justify-center gap-3 font-bold text-sm tracking-[.08em] uppercase
                       text-white border border-white/30 px-10 py-4 rounded
                       hover:border-white/60 hover:bg-white/10 transition-all">
          <span class="material-symbols-outlined" style="font-size:16px;">person_add</span>
          DAFTAR AKUN
        </button>
      </a>
    </div>
    <!-- Stats bar -->
    <div class="grid grid-cols-3 gap-3 max-w-lg mx-auto">
      <div class="rounded-lg px-4 py-3 text-center" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);">
        <div class="font-black text-white text-2xl" style="font-weight:900;">24/7</div>
        <div class="text-[10px] text-blue-200/60 tracking-widest uppercase mt-0.5">Pengawasan</div>
      </div>
      <div class="rounded-lg px-4 py-3 text-center" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);">
        <div class="font-black text-[#FECB00] text-2xl" style="font-weight:900;">18.240</div>
        <div class="text-[10px] text-blue-200/60 tracking-widest uppercase mt-0.5">Kapal / Tahun</div>
      </div>
      <div class="rounded-lg px-4 py-3 text-center" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);">
        <div class="font-black text-white text-2xl" style="font-weight:900;">99.8%</div>
        <div class="text-[10px] text-blue-200/60 tracking-widest uppercase mt-0.5">Keselamatan</div>
      </div>
    </div>
  </div>
  <!-- Wave divider -->
  <div class="absolute bottom-0 left-0 right-0 overflow-hidden" style="height:56px;">
    <svg viewBox="0 0 1200 56" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:100%;">
      <path d="M0,56 L0,28 C200,50 400,10 600,28 C800,46 1000,14 1200,28 L1200,56 Z" fill="#f8fafc"/>
    </svg>
  </div>
</section>

<!-- Gold accent line -->
<div class="gold-line max-w-5xl mx-auto px-8"></div>


<!-- ═══ LAYANAN SECTION — Clean White ════════════════════════════════════ -->
<section id="layanan" class="py-24 px-6 bg-white">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-14">
      <span class="badge-navy mb-4 inline-flex">Fungsi Strategis</span>
      <h2 class="font-black text-[#001E40] text-3xl md:text-4xl tracking-tight mb-4"
          style="font-weight:900;">Layanan Utama VTS Palembang</h2>
      <p class="text-slate-500 text-sm max-w-xl mx-auto leading-relaxed">
        Empat fungsi inti yang menjamin keselamatan, efisiensi, dan perlindungan di perairan Sungai Musi.
      </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
      <!-- Card 1 -->
      <div class="service-card rounded-xl p-7 flex flex-col h-full">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6 flex-shrink-0"
             style="background:rgba(0,30,64,.08);border:1px solid rgba(0,30,64,.15);">
          <span class="material-symbols-outlined ms-fill text-[#001E40]" style="font-size:26px;">verified_user</span>
        </div>
        <h3 class="font-black text-[#001E40] text-base mb-3 tracking-tight" style="font-weight:900;">Keselamatan Berlayar</h3>
        <p class="text-slate-500 text-sm leading-relaxed mb-6 flex-1">
          Pencegahan tabrakan kapal dan kandas melalui monitoring aktif pergerakan kapal di area pengawasan secara 24/7.
        </p>
        <a href="<?= BASE_URL ?>layanan/keselamatan-berlayar.php"
           class="flex items-center gap-2 text-[#001E40] hover:text-[#FECB00] text-[11px] font-bold tracking-[.12em] uppercase transition-colors mt-auto">
          Detail Layanan <span class="material-symbols-outlined" style="font-size:14px;">arrow_forward</span>
        </a>
      </div>
      <!-- Card 2 -->
      <div class="service-card rounded-xl p-7 flex flex-col h-full">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6 flex-shrink-0"
             style="background:rgba(0,30,64,.08);border:1px solid rgba(0,30,64,.15);">
          <span class="material-symbols-outlined ms-fill text-[#001E40]" style="font-size:26px;">speed</span>
        </div>
        <h3 class="font-black text-[#001E40] text-base mb-3 tracking-tight" style="font-weight:900;">Efisiensi Lalu Lintas</h3>
        <p class="text-slate-500 text-sm leading-relaxed mb-6 flex-1">
          Optimalisasi pergerakan kapal untuk meminimalkan waktu tunggu dan meningkatkan produktivitas pelabuhan.
        </p>
        <a href="<?= BASE_URL ?>layanan/efisiensi-lalu-lintas.php"
           class="flex items-center gap-2 text-[#001E40] hover:text-[#FECB00] text-[11px] font-bold tracking-[.12em] uppercase transition-colors mt-auto">
          Detail Layanan <span class="material-symbols-outlined" style="font-size:14px;">arrow_forward</span>
        </a>
      </div>
      <!-- Card 3 -->
      <div class="service-card rounded-xl p-7 flex flex-col h-full">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6 flex-shrink-0"
             style="background:rgba(0,30,64,.08);border:1px solid rgba(0,30,64,.15);">
          <span class="material-symbols-outlined ms-fill text-[#001E40]" style="font-size:26px;">eco</span>
        </div>
        <h3 class="font-black text-[#001E40] text-base mb-3 tracking-tight" style="font-weight:900;">Perlindungan Maritim</h3>
        <p class="text-slate-500 text-sm leading-relaxed mb-6 flex-1">
          Deteksi dini risiko pencemaran dan perlindungan area sensitif di lingkungan perairan Sungai Musi.
        </p>
        <a href="<?= BASE_URL ?>layanan/perlindungan-maritim.php"
           class="flex items-center gap-2 text-[#001E40] hover:text-[#FECB00] text-[11px] font-bold tracking-[.12em] uppercase transition-colors mt-auto">
          Detail Layanan <span class="material-symbols-outlined" style="font-size:14px;">arrow_forward</span>
        </a>
      </div>
      <!-- Card 4 -->
      <div class="service-card rounded-xl p-7 flex flex-col h-full">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6 flex-shrink-0"
             style="background:rgba(0,30,64,.08);border:1px solid rgba(0,30,64,.15);">
          <span class="material-symbols-outlined ms-fill text-[#001E40]" style="font-size:26px;">near_me</span>
        </div>
        <h3 class="font-black text-[#001E40] text-base mb-3 tracking-tight" style="font-weight:900;">Bantuan Navigasi</h3>
        <p class="text-slate-500 text-sm leading-relaxed mb-6 flex-1">
          Pemberian informasi teknis navigasi dan cuaca kepada kapal dalam kondisi sulit atau keadaan darurat.
        </p>
        <a href="<?= BASE_URL ?>layanan/bantuan-navigasi.php"
           class="flex items-center gap-2 text-[#001E40] hover:text-[#FECB00] text-[11px] font-bold tracking-[.12em] uppercase transition-colors mt-auto">
          Detail Layanan <span class="material-symbols-outlined" style="font-size:14px;">arrow_forward</span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ═══ INFORMASI SECTION — Clean White/Light Gray ════════════════════════ -->
<section id="informasi" class="py-24 px-6" style="background:#f1f5f9;">
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
      <div>
        <span class="badge-navy mb-5 inline-flex">Tentang Kami</span>
        <h2 class="font-black text-[#001E40] text-3xl md:text-4xl tracking-tight mb-6 leading-tight"
            style="font-weight:900;">
          Menjaga Nadi Perairan Musi<br/><span class="text-[#FECB00]" style="-webkit-text-stroke:1px #b8920a;">Sejak Dekade Silam</span>
        </h2>
        <div class="space-y-4 text-slate-600 text-sm leading-relaxed mb-8">
          <p>VTS Palembang adalah unit strategis di bawah <span class="text-[#001E40] font-semibold">Distrik Navigasi Tipe A Kelas I Palembang</span>, bertanggung jawab atas pengawasan dan manajemen lalu lintas kapal di perairan Sungai Musi dan sekitarnya.</p>
          <p>Dengan dukungan teknologi radar X-band, transponder AIS, dan sistem VTMIS terintegrasi, VTS Palembang memandu ribuan pergerakan kapal setiap tahunnya — dari kapal kargo internasional, tanker minyak, hingga kapal penumpang domestik.</p>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-8">
          <div class="info-card border-l-4 border-[#001E40]">
            <h4 class="font-black text-[#001E40] text-sm mb-1" style="font-weight:900;">Visi</h4>
            <p class="text-slate-500 text-xs leading-relaxed">Pusat pelayanan lalu lintas kapal berstandar internasional yang transparan dan akuntabel.</p>
          </div>
          <div class="info-card border-l-4 border-[#FECB00]">
            <h4 class="font-black text-[#001E40] text-sm mb-1" style="font-weight:900;">Misi</h4>
            <p class="text-slate-500 text-xs leading-relaxed">Meningkatkan keselamatan pelayaran melalui pengawasan real-time dan bantuan navigasi aktif.</p>
          </div>
        </div>
        <a href="<?= BASE_URL ?>login.php">
          <button class="flex items-center gap-2 text-[11px] font-black tracking-[.12em] uppercase text-white bg-[#001E40] px-8 py-3 rounded hover:bg-[#002d5e] active:scale-95 transition-all"
                  style="font-weight:900;">
            <span class="material-symbols-outlined ms-fill" style="font-size:15px;">login</span>
            AKSES SISTEM OPS-LOG
          </button>
        </a>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div class="stat-card-w p-6 text-center">
          <div class="font-black text-3xl text-[#001E40] mb-1" style="font-weight:900;">20+</div>
          <div class="text-slate-500 text-xs tracking-wide uppercase">Tahun Beroperasi</div>
        </div>
        <div class="stat-card-w p-6 text-center">
          <div class="font-black text-3xl text-[#001E40] mb-1" style="font-weight:900;">50+</div>
          <div class="text-slate-500 text-xs tracking-wide uppercase">Kapal / Hari</div>
        </div>
        <div class="stat-card-w p-6 text-center">
          <div class="font-black text-3xl text-[#001E40] mb-1" style="font-weight:900;">3</div>
          <div class="text-slate-500 text-xs tracking-wide uppercase">Shift Jaga / Hari</div>
        </div>
        <div class="stat-card-w p-6 text-center">
          <div class="font-black text-3xl text-[#001E40] mb-1" style="font-weight:900;">11</div>
          <div class="text-slate-500 text-xs tracking-wide uppercase">Area Maritim</div>
        </div>
        <div class="stat-card-w p-6 col-span-2 flex items-center gap-4">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
               style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);">
            <span class="w-3 h-3 rounded-full bg-green-500 pulse-slow inline-block"></span>
          </div>
          <div>
            <div class="font-black text-[#001E40] text-sm" style="font-weight:900;">Sistem Aktif</div>
            <div class="text-slate-500 text-xs">Radar &amp; AIS Network terhubung · Seluruh sensor nominal</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ KONTAK / FOOTER ════════════════════════════════════════════════════ -->
<footer id="kontak" class="footer-navy border-t-4 border-[#FECB00] py-16 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
      <!-- Brand -->
      <div>
        <div class="flex items-center gap-3 mb-5">
          <img src="<?= BASE_URL ?>assets/img/logo_navigasi.png" alt="VTS Logo" class="h-10 w-10 object-contain"
               onerror="this.style.display:'none';this.nextElementSibling.style.display='inline-flex'">
          <div class="logo-fallback h-8 w-8" style="font-size:9px;">VTS</div>
          <div>
            <div class="font-black text-white text-sm tracking-tight" style="font-weight:900;">VTS PALEMBANG</div>
            <div class="text-[10px] text-blue-300/50 tracking-wide uppercase">OPS-LOG System</div>
          </div>
        </div>
        <p class="text-blue-200/40 text-xs leading-relaxed pr-4">
          Instalasi Vessel Traffic Service (VTS) pada Kantor Distrik Navigasi Tipe A Kelas I Palembang. Direktorat Jenderal Perhubungan Laut.
        </p>
      </div>
      <!-- Kantor -->
      <div>
        <h4 class="font-black text-[#FECB00] text-[10px] tracking-[.2em] uppercase mb-5" style="font-weight:900;">Kantor Kami</h4>
        <address class="not-italic text-blue-200/50 text-xs space-y-3">
          <div class="flex gap-2.5">
            <span class="material-symbols-outlined text-[#FECB00]/50 flex-shrink-0" style="font-size:14px;">location_on</span>
            <span>Jln Belinyu No 9 Boom Baru Palembang Sumsel</span>
          </div>
          <div class="flex gap-2.5">
            <span class="material-symbols-outlined text-[#FECB00]/50 flex-shrink-0" style="font-size:14px;">chat</span>
            <span><a href="https://wa.me/628112985835" target="_blank" rel="noopener" class="hover:text-[#FECB00] transition-colors">08112985835 (WhatsApp)</a></span>
          </div>
          <div class="flex gap-2.5">
            <span class="material-symbols-outlined text-[#FECB00]/50 flex-shrink-0" style="font-size:14px;">mail</span>
            <span>vts.palembang@dephub.go.id</span>
          </div>
          <div class="flex gap-2.5">
            <span class="material-symbols-outlined text-[#FECB00]/50 flex-shrink-0" style="font-size:14px;">radio</span>
            <span>VHF Ch.12 (Kerja) · Ch.16 (Distress)</span>
          </div>
        </address>
      </div>
      <!-- Tautan Cepat -->
      <div>
        <h4 class="font-black text-[#FECB00] text-[10px] tracking-[.2em] uppercase mb-5" style="font-weight:900;">Tautan Cepat</h4>
        <ul class="space-y-2.5 text-xs">
          <li><a href="#beranda" class="text-blue-200/50 hover:text-white transition-colors">Beranda</a></li>
          <li><a href="#layanan" class="text-blue-200/50 hover:text-white transition-colors">Layanan</a></li>
          <li><a href="#informasi" class="text-blue-200/50 hover:text-white transition-colors">Tentang VTS</a></li>
          <li><a href="<?= BASE_URL ?>register.php" class="text-blue-200/50 hover:text-white transition-colors">Daftar Akun</a></li>
          <li><a href="<?= BASE_URL ?>login.php" class="text-[#FECB00] hover:brightness-110 transition-colors font-semibold">Masuk Sistem OPS-LOG →</a></li>
        </ul>
      </div>
      <!-- Status Operasional -->
      <div>
        <h4 class="font-black text-[#FECB00] text-[10px] tracking-[.2em] uppercase mb-5" style="font-weight:900;">Status Operasional</h4>
        <div class="space-y-2.5">
          <div class="flex items-center justify-between text-[10px]">
            <span class="text-blue-200/50">Radar X-Band</span>
            <span class="flex items-center gap-1.5 text-green-400 font-semibold">
              <span class="w-1.5 h-1.5 rounded-full bg-green-400 pulse-slow inline-block"></span> ONLINE
            </span>
          </div>
          <div class="flex items-center justify-between text-[10px]">
            <span class="text-blue-200/50">AIS Transponder</span>
            <span class="flex items-center gap-1.5 text-green-400 font-semibold">
              <span class="w-1.5 h-1.5 rounded-full bg-green-400 pulse-slow inline-block"></span> ONLINE
            </span>
          </div>
          <div class="flex items-center justify-between text-[10px]">
            <span class="text-blue-200/50">VTMIS System</span>
            <span class="flex items-center gap-1.5 text-green-400 font-semibold">
              <span class="w-1.5 h-1.5 rounded-full bg-green-400 pulse-slow inline-block"></span> ONLINE
            </span>
          </div>
          <div class="flex items-center justify-between text-[10px]">
            <span class="text-blue-200/50">OPS-LOG Database</span>
            <span class="flex items-center gap-1.5 text-green-400 font-semibold">
              <span class="w-1.5 h-1.5 rounded-full bg-green-400 pulse-slow inline-block"></span> ONLINE
            </span>
          </div>
          <div class="mt-4 pt-4 border-t border-white/10 text-[10px] text-blue-200/30 font-mono">
            LAST SYNC: <?= date('d M Y · H:i') ?> WIB
          </div>
        </div>
      </div>
    </div>
    <!-- Footer bottom -->
    <div class="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-[11px] text-blue-200/30">
      <span>© <?= date('Y') ?> Direktorat Jenderal Perhubungan Laut — VTS Palembang. Hak Cipta Dilindungi.</span>
      <span class="font-mono tracking-widest text-[10px]">SOVEREIGN ENGINE V5.1 · BUILD <?= date('Ymd') ?></span>
    </div>
  </div>
</footer>
</body></html>

