<!-- Design System -->
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>VTS PALEMBANG - Direktorat Jenderal Perhubungan Laut</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .vts-watermark {
            background-image: url("data:image/svg+xml,%3Csvg width='400' height='400' viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M200 0L210 190L400 200L210 210L200 400L190 210L0 200L190 190Z' fill='%23001e40' fill-opacity='0.03'/%3E%3C/svg%3E");
        }
    </style>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-low": "#f1f4f9",
                        "surface-container-high": "#e5e8ed",
                        "outline": "#737780",
                        "on-surface-variant": "#43474f",
                        "primary-fixed-dim": "#a7c8ff",
                        "on-surface": "#181c20",
                        "on-secondary": "#ffffff",
                        "surface-tint": "#3a5f94",
                        "error": "#ba1a1a",
                        "background": "#f7f9fe",
                        "on-primary-fixed": "#001b3c",
                        "on-background": "#181c20",
                        "surface-dim": "#d7dadf",
                        "primary": "#001e40",
                        "inverse-on-surface": "#eef1f6",
                        "tertiary-container": "#d0a600",
                        "on-secondary-container": "#616363",
                        "error-container": "#ffdad6",
                        "outline-variant": "#c3c6d1",
                        "on-primary": "#ffffff",
                        "on-error-container": "#93000a",
                        "secondary-fixed-dim": "#c6c6c7",
                        "on-secondary-fixed-variant": "#454747",
                        "secondary-fixed": "#e2e2e2",
                        "on-tertiary-fixed-variant": "#584400",
                        "tertiary-fixed-dim": "#f1c100",
                        "surface-container-lowest": "#ffffff",
                        "tertiary-fixed": "#ffe08b",
                        "on-tertiary": "#ffffff",
                        "surface-container": "#ebeef3",
                        "on-primary-container": "#799dd6",
                        "surface": "#f7f9fe",
                        "on-error": "#ffffff",
                        "surface-bright": "#f7f9fe",
                        "inverse-surface": "#2d3135",
                        "on-secondary-fixed": "#1a1c1c",
                        "surface-variant": "#dfe3e8",
                        "on-tertiary-container": "#4f3d00",
                        "surface-container-highest": "#dfe3e8",
                        "primary-fixed": "#d5e3ff",
                        "tertiary": "#745b00",
                        "on-primary-fixed-variant": "#1f477b",
                        "inverse-primary": "#a7c8ff",
                        "secondary-container": "#dfe0e0",
                        "secondary": "#5d5f5f",
                        "primary-container": "#003366",
                        "on-tertiary-fixed": "#241a00"
                    },
                    fontFamily: {
                        "headline": ["Inter"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
                },
            },
        }
    </script>
</head>
<body class="bg-background text-on-surface font-body selection:bg-tertiary-container selection:text-on-tertiary-container">
<!-- Top Navigation Bar -->
<nav class="fixed top-0 w-full z-50 bg-[#f7f9fe]/80 dark:bg-[#181c20]/80 backdrop-blur-xl shadow-sm shadow-[#181c20]/5 font-['Inter'] antialiased tracking-tight">
<div class="flex justify-between items-center px-8 h-20 w-full mx-auto">
<div class="flex items-center gap-4">
<div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center overflow-hidden">
<img alt="Logo Kemenhub" class="w-8 h-8 object-contain" data-alt="Official logo of the Indonesian Ministry of Transportation featuring high-contrast blue and yellow elements on a white background." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDo57homC9cRrA4YpNwLGlDX2KJd7gDJ4yDewW3fw7wtr_d-FJKOcipDshBpxQ9K2riUw5wA73eHzcGby41RruPoowKuD-ASOHGCSbANEJ0c3yLLUqYm4nFW1wyeXoWYdHUOhwAWuyYfvML4XGZKiMGeMRrS2I4AfmSBUDVGB5pQqrmGIEFb8r2xhO4ooR6vze_kcKhzEHUttNpq0zcaC6n0X-hEkAUqmjuXrjh16WaY8szn9_CyAMkcX8Bb_IlXmFIj6BtnioZVo_R"/>
</div>
<span class="text-xl font-bold tracking-tighter text-[#001e40] dark:text-[#f1f4f9]">VTS PALEMBANG</span>
</div>
<div class="hidden md:flex items-center space-x-8">
<a class="text-[#003366] dark:text-[#d0a600] border-b-2 border-[#d0a600] pb-1" href="#">Beranda</a>
<a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#001e40] dark:hover:text-white transition-colors" href="#">Layanan</a>
<a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#001e40] dark:hover:text-white transition-colors" href="#">Informasi</a>
<a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#001e40] dark:hover:text-white transition-colors" href="#">Kontak</a>
<button class="bg-primary text-on-primary px-6 py-2.5 rounded-md font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all">Masuk Sistem</button>
</div>
<button class="md:hidden p-2 text-primary">
<span class="material-symbols-outlined">menu</span>
</button>
</div>
</nav>
<!-- Hero Section -->
<header class="relative h-screen w-full flex items-center overflow-hidden">
<div class="absolute inset-0 z-0">
<div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/60 to-transparent z-10"></div>
<img alt="VTS Tower Palembang" class="w-full h-full object-cover" data-alt="Modern maritime vessel traffic service tower at sunset overlooking a busy Indonesian river port with dramatic orange and blue lighting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgHdrpoc_JNQ1ZZv4H3xPlAsyeFQCSrIEuMNKZ3e_LabhhwLwl60oVkrM5jNE5UlAfeUCwfIpF5dZAYGxGzqFGOqiL5kkL8tbt5iWBK1rATCOAVKs4t-IWDwCTxbr_Jk3IBJBkXvmv5egQ8ZiyT7khXfrd-RlKwZ-OG8Lpjo7FR6egagTzscGuhKoN2zd3IRsHIlPdn71pxbX6Lt0pSeVoyaUpB14fMYsiLlrdl1gizjzgkWEquNfO_TMAwg2hUEnVDy0EL66sm_m8"/>
</div>
<div class="relative z-20 px-8 md:px-24 max-w-5xl">
<div class="inline-block px-3 py-1 bg-tertiary-container text-on-tertiary-container text-[0.6875rem] font-bold tracking-[0.05rem] uppercase mb-6 rounded-sm">
                Distrik Navigasi Type B Palembang
            </div>
<h1 class="text-5xl md:text-7xl font-extrabold text-white leading-[1.1] tracking-tighter mb-8">
                VESSEL TRAFFIC SERVICE (VTS) PALEMBANG
            </h1>
<p class="text-lg md:text-xl text-primary-fixed max-w-2xl mb-12 leading-relaxed opacity-90">
                Pusat pengawasan dan manajemen lalu lintas kapal yang handal untuk menjamin keselamatan pelayaran dan perlindungan lingkungan maritim di wilayah perairan Palembang.
            </p>
<div class="flex flex-col sm:flex-row gap-4">
<button class="bg-tertiary-container text-on-tertiary-container px-8 py-4 rounded-md font-bold text-lg flex items-center justify-center gap-3 hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-black/20">
                    MASUK SISTEM OPS-LOG
                    <span class="material-symbols-outlined">login</span>
</button>
<button class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-4 rounded-md font-bold text-lg hover:bg-white/20 transition-all">
                    Pelajari Lebih Lanjut
                </button>
</div>
</div>
<div class="absolute bottom-12 left-8 md:left-24 z-20 flex gap-8">
<div class="flex flex-col">
<span class="text-tertiary-fixed-dim text-[0.6875rem] font-bold tracking-[0.05rem] uppercase">Latitude</span>
<span class="text-white font-mono text-lg">2.9909° S</span>
</div>
<div class="flex flex-col">
<span class="text-tertiary-fixed-dim text-[0.6875rem] font-bold tracking-[0.05rem] uppercase">Longitude</span>
<span class="text-white font-mono text-lg">104.7567° E</span>
</div>
</div>
</header>
<!-- Sejarah Singkat (About) Section -->
<section class="relative py-32 px-8 overflow-hidden bg-surface-container-low vts-watermark">
<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
<div class="lg:col-span-5 relative">
<div class="aspect-square bg-surface-container-highest rounded-xl overflow-hidden shadow-2xl shadow-primary/10">
<img alt="Sejarah VTS" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700" data-alt="Vintage black and white photo of Indonesian maritime port combined with modern navigation equipment representing historical evolution." src="https://lh3.googleusercontent.com/aida-public/AB6AXuARoCrSONbKagp9M6IwrAAGRmOMEirLjnMQmBsf9ucW5fbiMwM1gZV2AxKMTSiUoSXpcXbQmakjBa3o2nDAaF9ySunMRt-f3yKYvFtrAxN8jWeaZxLEXkpbGEfoBeaBDo23omLcZZdA5IkQk4mnMS2q_2IlOaVnbetTSM3fiAXpSQ98N_7R-MpgTaPCGYBLhGgMqkcZ_almLfosan7zCUlqWLFj99yNa7I-uFNgPvn03CHJd1UBM0-JLXvHRH86eflwYxsqYMOfZ--g"/>
</div>
<div class="absolute -bottom-6 -right-6 w-48 h-48 bg-tertiary rounded-xl p-6 flex flex-col justify-end text-white shadow-xl">
<span class="text-4xl font-bold">20+</span>
<span class="text-sm font-medium opacity-80 leading-tight">Tahun Pengabdian Navigasi Maritim</span>
</div>
</div>
<div class="lg:col-span-7">
<div class="flex items-center gap-4 mb-6">
<div class="h-[2px] w-12 bg-tertiary"></div>
<span class="text-tertiary font-bold tracking-widest text-sm uppercase">Sejarah Singkat</span>
</div>
<h2 class="text-4xl md:text-5xl font-bold text-primary tracking-tight mb-8">Menjaga Nadi Perairan Musi Sejak Dekade Silam</h2>
<div class="space-y-6 text-on-surface-variant leading-relaxed text-lg">
<p>
                        Vessel Traffic Service (VTS) Palembang didirikan sebagai bagian vital dari infrastruktur transportasi laut di wilayah Sumatera Selatan. Sebagai unit di bawah Distrik Navigasi Type B Palembang, kami memiliki mandat strategis dalam mengelola alur pelayaran yang kompleks di Sungai Musi.
                    </p>
<p>
                        Dengan perkembangan teknologi radar dan AIS yang pesat, VTS Palembang terus bertransformasi dari pusat komunikasi radio sederhana menjadi command center modern yang terintegrasi. Komitmen kami tetap teguh: memastikan setiap kapal yang melintas mendapatkan panduan navigasi yang akurat demi keamanan jiwa, harta benda, dan kelestarian ekosistem sungai yang menjadi urat nadi ekonomi daerah.
                    </p>
</div>
<div class="mt-10 grid grid-cols-2 gap-8 border-t border-outline-variant/30 pt-10">
<div>
<h4 class="font-bold text-primary mb-2">Visi Utama</h4>
<p class="text-sm text-on-surface-variant">Menjadi pusat pelayanan lalu lintas kapal berstandar internasional yang transparan dan akuntabel.</p>
</div>
<div>
<h4 class="font-bold text-primary mb-2">Misi Utama</h4>
<p class="text-sm text-on-surface-variant">Meningkatkan keselamatan pelayaran melalui pengawasan real-time dan bantuan navigasi aktif.</p>
</div>
</div>
</div>
</div>
</section>
<!-- Fungsi VTS (Services) Section -->
<section class="py-32 px-8 bg-surface">
<div class="max-w-7xl mx-auto">
<div class="text-center mb-20">
<span class="text-tertiary font-bold tracking-widest text-sm uppercase">Layanan Utama</span>
<h2 class="text-4xl md:text-5xl font-extrabold text-primary tracking-tight mt-4">Fungsi &amp; Peran Strategis VTS</h2>
<div class="w-24 h-1 bg-tertiary-container mx-auto mt-6"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
<!-- Card 1 -->
<div class="group bg-surface-container hover:bg-primary transition-all duration-500 p-8 rounded-xl shadow-sm hover:shadow-2xl hover:shadow-primary/20 flex flex-col h-full">
<div class="w-14 h-14 rounded-lg bg-tertiary-container flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-on-tertiary-container text-3xl">verified_user</span>
</div>
<h3 class="text-xl font-bold text-primary group-hover:text-white mb-4 transition-colors">Keselamatan Berlayar</h3>
<p class="text-on-surface-variant group-hover:text-primary-fixed opacity-90 leading-relaxed mb-6">
                        Pencegahan tabrakan kapal dan kandas melalui monitoring aktif pergerakan kapal di area pengawasan secara 24/7.
                    </p>
<div class="mt-auto flex items-center gap-2 text-tertiary group-hover:text-tertiary-fixed font-bold text-sm uppercase tracking-wider">
                        Detail Layanan <span class="material-symbols-outlined text-sm">arrow_forward</span>
</div>
</div>
<!-- Card 2 -->
<div class="group bg-surface-container hover:bg-primary transition-all duration-500 p-8 rounded-xl shadow-sm hover:shadow-2xl hover:shadow-primary/20 flex flex-col h-full">
<div class="w-14 h-14 rounded-lg bg-tertiary-container flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-on-tertiary-container text-3xl">speed</span>
</div>
<h3 class="text-xl font-bold text-primary group-hover:text-white mb-4 transition-colors">Efisiensi Lalu Lintas</h3>
<p class="text-on-surface-variant group-hover:text-primary-fixed opacity-90 leading-relaxed mb-6">
                        Optimalisasi pergerakan kapal untuk meminimalkan waktu tunggu dan meningkatkan produktivitas pelabuhan.
                    </p>
<div class="mt-auto flex items-center gap-2 text-tertiary group-hover:text-tertiary-fixed font-bold text-sm uppercase tracking-wider">
                        Detail Layanan <span class="material-symbols-outlined text-sm">arrow_forward</span>
</div>
</div>
<!-- Card 3 -->
<div class="group bg-surface-container hover:bg-primary transition-all duration-500 p-8 rounded-xl shadow-sm hover:shadow-2xl hover:shadow-primary/20 flex flex-col h-full">
<div class="w-14 h-14 rounded-lg bg-tertiary-container flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-on-tertiary-container text-3xl">eco</span>
</div>
<h3 class="text-xl font-bold text-primary group-hover:text-white mb-4 transition-colors">Perlindungan Maritim</h3>
<p class="text-on-surface-variant group-hover:text-primary-fixed opacity-90 leading-relaxed mb-6">
                        Deteksi dini risiko pencemaran dan perlindungan area sensitif di lingkungan perairan Sungai Musi.
                    </p>
<div class="mt-auto flex items-center gap-2 text-tertiary group-hover:text-tertiary-fixed font-bold text-sm uppercase tracking-wider">
                        Detail Layanan <span class="material-symbols-outlined text-sm">arrow_forward</span>
</div>
</div>
<!-- Card 4 -->
<div class="group bg-surface-container hover:bg-primary transition-all duration-500 p-8 rounded-xl shadow-sm hover:shadow-2xl hover:shadow-primary/20 flex flex-col h-full">
<div class="w-14 h-14 rounded-lg bg-tertiary-container flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-on-tertiary-container text-3xl">near_me</span>
</div>
<h3 class="text-xl font-bold text-primary group-hover:text-white mb-4 transition-colors">Bantuan Navigasi</h3>
<p class="text-on-surface-variant group-hover:text-primary-fixed opacity-90 leading-relaxed mb-6">
                        Pemberian informasi teknis navigasi dan cuaca kepada kapal dalam kondisi sulit atau keadaan darurat.
                    </p>
<div class="mt-auto flex items-center gap-2 text-tertiary group-hover:text-tertiary-fixed font-bold text-sm uppercase tracking-wider">
                        Detail Layanan <span class="material-symbols-outlined text-sm">arrow_forward</span>
</div>
</div>
</div>
</div>
</section>
<!-- Footer -->
<footer class="bg-[#f1f4f9] dark:bg-[#001e40] w-full py-12 px-8 border-t border-[#c3c6d1]/20 text-sm font-['Inter'] leading-relaxed">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto text-[#181c20] dark:text-[#f7f9fe]">
<div class="space-y-6">
<div class="font-bold text-[#003366] dark:text-[#d0a600] text-xl tracking-tighter uppercase">VTS PALEMBANG</div>
<p class="text-[#737780] dark:text-[#c3c6d1] pr-4">
                    Instalasi Vessel Traffic Service (VTS) pada Kantor Distrik Navigasi Type B Palembang. Direktorat Jenderal Perhubungan Laut.
                </p>
<div class="flex gap-4">
<a class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center hover:bg-tertiary-container transition-colors" href="#">
<span class="material-symbols-outlined text-sm">public</span>
</a>
<a class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center hover:bg-tertiary-container transition-colors" href="#">
<span class="material-symbols-outlined text-sm">share</span>
</a>
</div>
</div>
<div>
<h4 class="font-bold mb-6 text-primary dark:text-[#d0a600] uppercase tracking-wider">Kantor Kami</h4>
<address class="not-italic text-[#737780] dark:text-[#c3c6d1] space-y-4">
<div class="flex gap-3">
<span class="material-symbols-outlined text-primary dark:text-tertiary">location_on</span>
<span>Jl. Belabak No. 1, Kel. 3 Ilir, Kec. Ilir Timur II, Kota Palembang, Sumatera Selatan 30116</span>
</div>
<div class="flex gap-3">
<span class="material-symbols-outlined text-primary dark:text-tertiary">call</span>
<span>(0711) 710000</span>
</div>
<div class="flex gap-3">
<span class="material-symbols-outlined text-primary dark:text-tertiary">mail</span>
<span>vts.palembang@dephub.go.id</span>
</div>
</address>
</div>
<div>
<h4 class="font-bold mb-6 text-primary dark:text-[#d0a600] uppercase tracking-wider">Tautan Cepat</h4>
<ul class="space-y-3">
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] dark:hover:text-[#d0a600] transition-colors" href="#">Kebijakan Privasi</a></li>
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] dark:hover:text-[#d0a600] transition-colors" href="#">Syarat &amp; Ketentuan</a></li>
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] dark:hover:text-[#d0a600] transition-colors" href="#">Peta Situs</a></li>
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] dark:hover:text-[#d0a600] transition-colors" href="#">Kontak Kami</a></li>
</ul>
</div>
<div>
<h4 class="font-bold mb-6 text-primary dark:text-[#d0a600] uppercase tracking-wider">Status Sistem</h4>
<div class="bg-surface-container-high/30 p-4 rounded-lg">
<div class="flex items-center gap-3 mb-2">
<span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
<span class="text-xs font-bold uppercase tracking-widest">Operational</span>
</div>
<p class="text-xs text-[#737780] dark:text-[#c3c6d1]">Radar &amp; AIS Network terhubung dengan baik. Seluruh sensor aktif.</p>
</div>
</div>
</div>
<div class="mt-12 pt-8 border-t border-[#c3c6d1]/10 text-center text-xs text-[#737780] dark:text-[#c3c6d1]">
            © 2024 Direktorat Jenderal Perhubungan Laut - VTS Palembang. Hak Cipta Dilindungi Undang-Undang.
        </div>
</footer>
</body></html>

<!-- Beranda VTS Palembang -->
<!DOCTYPE html>

<html lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Registrasi Staf | VTS PALEMBANG</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      .industrial-gradient {
        background: linear-gradient(135deg, #001e40 0%, #003366 100%);
      }
      .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
      }
    </style>
</head>
<body class="bg-background font-body text-on-surface min-h-screen flex items-center justify-center p-4 md:p-8">
<main class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 overflow-hidden rounded-xl shadow-xl bg-surface-container-lowest">
<!-- Left Column: Branding & Information -->
<section class="md:col-span-5 industrial-gradient p-10 flex flex-col justify-between text-white">
<div class="space-y-8">
<!-- Branding -->
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-white p-1 rounded-lg">
<img alt="Kemenhub Logo" class="w-full h-full object-contain" data-alt="Official logo of the Ministry of Transportation Indonesia featuring a stylized golden eagle emblem on a white field" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUnDvVotl9_xCdHAWy__rbOwXTN9fVtf-9LIv3uvGVXGhKBfYFPvKSEF_hJje3Ang87uFvDHC4RAf2FxK-LHCJ1ZpxwCprHJiefbHC5oUVtnhazoYVjS1QixTXiApzB6URl--Fe8qizoObC6ecCZ7V7iil5pfF4Rb7CuKn5x7mSCCNpmxOepf6HFai9U6tmQv7NhbQ1b64Lbr4kdsjzDl_BaHbDLeVHiFS8nau5lD0vN8OSkwalsS84tbJJwu07Y_wqwAl1eU-cDkg"/>
</div>
<div>
<h1 class="text-xl font-bold tracking-tighter uppercase leading-none">VTS PALEMBANG</h1>
<p class="text-[10px] tracking-[0.2em] opacity-70 uppercase font-semibold">Vessel Traffic Service</p>
</div>
</div>
<!-- Messaging -->
<div class="space-y-4">
<h2 class="text-3xl font-extrabold tracking-tight leading-tight">Registrasi Personnel Operasional.</h2>
<p class="text-on-primary-container text-sm leading-relaxed max-w-sm">
                        Silakan lengkapi formulir pendaftaran untuk mendapatkan akses ke Sistem Logistik dan Pemantauan Kapal VTS Palembang.
                    </p>
</div>
<!-- Industrial Status Widget (Visual Only) -->
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
<p class="text-[10px] text-on-primary-container/60 uppercase tracking-[0.15em] font-medium">
                    © 2024 Kemenhub RI - Distrik Navigasi Palembang
                </p>
</div>
</section>
<!-- Right Column: Multi-column Registration Form -->
<section class="md:col-span-7 bg-white p-10">
<div class="mb-10 flex justify-between items-end">
<div>
<h3 class="text-2xl font-bold text-primary">Buat Akun Baru</h3>
<p class="text-sm text-on-surface-variant">Pastikan data yang diinput sesuai dengan NIP resmi.</p>
</div>
<span class="material-symbols-outlined text-primary text-4xl opacity-10">person_add</span>
</div>
<form action="register.php" class="space-y-6" method="POST">
<!-- Row 1: NIP & Full Name -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="nip">NIP Personnel</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">badge</span>
<input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all placeholder:text-outline-variant text-sm font-medium" id="nip" name="nip" placeholder="882910" type="text"/>
</div>
</div>
<div class="space-y-2">
<label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="nama">Nama Lengkap</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">person</span>
<input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all placeholder:text-outline-variant text-sm font-medium" id="nama" name="nama" placeholder="Masukkan Nama Sesuai SK" type="text"/>
</div>
</div>
</div>
<!-- Row 2: Team & Role -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="team">Tim Operasional (Team)</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">groups</span>
<select class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all text-sm font-medium appearance-none" id="team" name="team">
<option disabled="" selected="" value="">Pilih Tim</option>
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
<select class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all text-sm font-medium appearance-none" id="role" name="role">
<option disabled="" selected="" value="">Pilih Peran</option>
<option value="Operator">Operator</option>
<option value="Supervisor">Supervisor</option>
<option value="Manager">Manager</option>
</select>
</div>
</div>
</div>
<!-- Row 3: Password -->
<div class="space-y-2">
<label class="block text-[11px] font-bold uppercase tracking-wider text-outline" for="password">Password Keamanan</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">lock</span>
<input class="w-full pl-10 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-0 focus:border-b-2 focus:border-primary transition-all placeholder:text-outline-variant text-sm font-medium" id="password" name="password" placeholder="••••••••" type="password"/>
</div>
<div class="flex items-center gap-2 mt-2 px-1">
<span class="material-symbols-outlined text-[16px] text-secondary font-bold">shield_lock</span>
<p class="text-[10px] text-on-surface-variant font-medium">Security Note: Password akan diamankan menggunakan algoritma hashing <span class="text-primary font-bold">Argon2id</span> (Industrial Standard).</p>
</div>
</div>
<!-- Actions -->
<div class="pt-4 flex flex-col gap-4">
<button class="industrial-gradient w-full py-4 rounded-lg text-white font-bold text-sm tracking-widest uppercase hover:opacity-90 active:scale-[0.98] transition-all shadow-lg flex items-center justify-center gap-3" type="submit">
                        Daftarkan Personnel
                        <span class="material-symbols-outlined text-xl">how_to_reg</span>
</button>
<div class="flex items-center justify-center gap-2 pt-2">
<span class="text-xs text-on-surface-variant">Sudah memiliki akun?</span>
<a class="text-xs font-bold text-primary hover:underline flex items-center gap-1" href="login.php">
                            Masuk ke Sistem
                            <span class="material-symbols-outlined text-sm">login</span>
</a>
</div>
</div>
</form>
</section>
</main>
<!-- Global Footer Fragment -->
<footer class="fixed bottom-0 left-0 w-full p-4 flex justify-between items-center pointer-events-none">
<div class="text-[9px] uppercase tracking-[0.3em] text-outline font-bold opacity-40">
            VTS PALEMBANG // SYSTEM_REGISTRATION_V2.4
        </div>
<div class="text-[9px] uppercase tracking-[0.3em] text-outline font-bold opacity-40">
            SECURE ACCESS ONLY
        </div>
</footer>
</body></html>

<!-- Design System -->
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
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
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .active-nav-icon {
            font-variation-settings: 'FILL' 1;
        }
    </style>
</head>
<body class="bg-background font-body text-on-surface antialiased overflow-hidden">
<!-- Top Navigation Shell -->
<header class="bg-[#001e40] dark:bg-slate-950 text-[#fecb00] dark:text-[#fecb00] font-['Inter'] antialiased tracking-tight shadow-xl bg-opacity-90 backdrop-blur-md flex justify-between items-center w-full px-8 py-4 docked full-width top-0 z-50 fixed">
<div class="flex items-center gap-6">
<span class="text-xl font-bold tracking-tighter text-white">VTS PALEMBANG</span>
<div class="h-6 w-px bg-white/20"></div>
<div class="flex items-center gap-2 px-3 py-1 bg-white/10 rounded-lg">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
<span class="text-[10px] font-bold uppercase tracking-widest text-white">SYSTEM ACTIVE</span>
</div>
<div class="flex items-center gap-2 px-3 py-1 border border-white/20 rounded-lg">
<span class="text-[10px] font-bold uppercase tracking-widest text-white">PT TERRA DIGITAL SYSTEM</span>
</div>
</div>
<nav class="hidden md:flex gap-8">
<a class="text-[#fecb00] border-b-2 border-[#fecb00] pb-1" href="#">Beranda</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Layanan</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Informasi</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Kontak</a>
</nav>
<div class="flex items-center gap-4">
<div class="flex flex-col items-end mr-4">
<span class="text-[10px] font-bold text-white/60 uppercase">Shift Lock Timer</span>
<span class="text-error font-mono font-bold tracking-widest">04:52</span>
</div>
<div class="p-2 text-slate-300 hover:bg-white/10 transition-all cursor-pointer">
<span class="material-symbols-outlined">notifications</span>
</div>
<button class="bg-secondary-container text-primary font-bold px-4 py-2 rounded-lg text-sm active:opacity-80 active:scale-95 transition-transform">
                Masuk Sistem
            </button>
</div>
</header>
<!-- Sidebar Shell -->
<aside class="h-screen w-64 fixed left-0 top-0 border-r-0 bg-[#f7f9fe] dark:bg-slate-900 text-[#001e40] dark:text-slate-100 font-['Inter'] text-sm font-medium flex flex-col h-full py-6 z-40 pt-24">
<div class="px-6 mb-8 flex items-center gap-4">
<div class="h-10 w-10 rounded-lg bg-primary-container flex items-center justify-center text-white font-bold">V</div>
<div>
<p class="text-sm font-black uppercase tracking-tight">Valdo</p>
<p class="text-[10px] text-on-surface-variant font-semibold">NIP: 882910 | Shift B</p>
</div>
</div>
<div class="flex-1 space-y-1">
<a class="flex items-center gap-3 bg-[#001e40] text-white rounded-lg p-3 mx-2 translate-x-1 transition-transform" href="#">
<span class="material-symbols-outlined active-nav-icon">dashboard</span>
<span>Dashboard</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined">edit_note</span>
<span>Input Log (A1-A3)</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined">warning</span>
<span>Laporan Khusus (A4-A8)</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined">group</span>
<span>Manajemen User</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined">picture_as_pdf</span>
<span>Arsip &amp; Export PDF</span>
</a>
</div>
<div class="mt-auto pt-6 border-t border-outline-variant/20 mx-4">
<div class="flex items-center gap-3 text-slate-600 p-3 hover:bg-slate-200 transition-colors rounded-lg cursor-pointer">
<span class="material-symbols-outlined">settings</span>
<span>Settings</span>
</div>
<div class="flex items-center gap-3 text-error p-3 hover:bg-error-container transition-colors rounded-lg cursor-pointer">
<span class="material-symbols-outlined">logout</span>
<span>Logout</span>
</div>
</div>
</aside>
<!-- Main Canvas -->
<main class="pl-64 pt-20 h-screen overflow-y-auto bg-surface">
<div class="p-8 space-y-8">
<!-- Hero Command Grid -->
<div class="grid grid-cols-12 gap-6">
<!-- Tactical View (Bento Large) -->
<div class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-xl overflow-hidden relative border border-outline-variant/10 shadow-sm">
<div class="absolute top-0 left-0 w-full p-4 z-10 flex justify-between items-center glass-panel border-b border-outline-variant/10">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary">radar</span>
<h2 class="font-semibold text-primary">Tactical Radar Feed: Muara Musi Zone</h2>
</div>
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-error animate-pulse"></div>
<span class="text-[10px] font-bold uppercase text-on-surface-variant">Live Feed A-01</span>
</div>
</div>
<div class="h-[450px] w-full bg-[#001b3c] relative flex items-center justify-center">
<img class="w-full h-full object-cover opacity-60" data-alt="abstract top-down dark green radar interface with glowing circular grid lines and vessel blips representing maritime traffic surveillance" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC7VepYE_5YVAdtnCSgbTSBlvJ2_iSRxdkfNOO2-dx-ounsRf6cMM9zW37phlgx3X_a0DkBK-jW890dHT-c5nZcy_Syo2PGvfbHArNRZ32HJzLtEHgu5P5m779w0ffno_qcXL_mRM2USDdq_BEUnrDEiwMm0l7SdPKOra8VTVCUGKeAhePjWJehghQQkZoJNXywuuoFN14chW0LTl0vIDflOYjbmNtnPUgpy9e9-gfEgDkyLfRzmzXu0S33CtO99lLEYMnW_5K3yBUN"/>
<!-- Tactical Overlay -->
<div class="absolute inset-0 flex items-center justify-center pointer-events-none">
<div class="w-[300px] h-[300px] border border-primary-container/30 rounded-full animate-[spin_10s_linear_infinite]"></div>
<div class="absolute w-px h-full bg-primary-container/20"></div>
<div class="absolute w-full h-px bg-primary-container/20"></div>
</div>
<!-- Radar Info Chips -->
<div class="absolute bottom-4 left-4 flex gap-2">
<div class="glass-panel px-3 py-2 rounded-lg text-[10px] font-bold text-primary">
                                LAT: 2.9901 S | LON: 104.7567 E
                            </div>
<div class="glass-panel px-3 py-2 rounded-lg text-[10px] font-bold text-primary">
                                SCALE: 1:5000 NM
                            </div>
</div>
</div>
</div>
<!-- Secondary Command Column -->
<div class="col-span-12 lg:col-span-4 space-y-6">
<!-- Traffic Monitoring -->
<div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/10 shadow-sm">
<div class="flex justify-between items-start mb-6">
<h3 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Live Traffic</h3>
<span class="material-symbols-outlined text-primary">directions_boat</span>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="bg-surface-container-low rounded-lg p-4 text-center">
<p class="text-3xl font-black text-primary">16</p>
<p class="text-[10px] font-bold uppercase text-on-surface-variant">Inbound</p>
</div>
<div class="bg-surface-container-low rounded-lg p-4 text-center">
<p class="text-3xl font-black text-on-primary-container">09</p>
<p class="text-[10px] font-bold uppercase text-on-surface-variant">Outbound</p>
</div>
</div>
</div>
<!-- Weather Module -->
<div class="bg-primary-container rounded-xl p-6 text-white relative overflow-hidden">
<img class="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-overlay" data-alt="dramatic dark stormy clouds over a river with golden sunset light breaking through the horizon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB1kCP8S3vT2yVcHXTWkDCWdaKMfrWM6i1SY1s6hF56q_MFg9OxGhu45VXPquLiRGWJXCsh8KZBuNzXljQ5DedVl68wvpDZtNPbJNA4po0-F-lJEen2fDtEXp7OvqTv21qyzc0cYbM2IgQOXJ0HK213LfGlKyigr0oYkR6pHWKwM9m3R86WW88AnTDYID2z9kqAqYnFt1gDOawyssf_loLPCTuZoB-vtmlliblfUh7cINQww8lZpKUPUfoo288Z8ssKbk_QrIbFCPjw"/>
<div class="relative z-10">
<div class="flex justify-between items-center mb-4">
<div>
<p class="text-[10px] font-bold uppercase opacity-60">Weather Station</p>
<p class="text-sm font-bold">Muara Sg. Musi</p>
</div>
<span class="material-symbols-outlined text-3xl text-secondary-container">cloudy</span>
</div>
<div class="flex items-end gap-2">
<span class="text-4xl font-black">28°C</span>
<span class="text-xs font-medium opacity-80 pb-1">Wind: 12kn NE</span>
</div>
<div class="mt-4 pt-4 border-t border-white/10 flex justify-between text-[10px] font-bold">
<span>HUMIDITY: 82%</span>
<span>VISIBILITY: 8.5 KM</span>
</div>
</div>
</div>
<!-- Equipment Health -->
<div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/10 shadow-sm">
<h3 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-4">Equipment Status</h3>
<div class="space-y-3">
<div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-sm">satellite_alt</span>
<span class="text-xs font-bold">AIS Transponder</span>
</div>
<div class="w-2 h-2 rounded-full bg-emerald-500"></div>
</div>
<div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-sm">settings_input_antenna</span>
<span class="text-xs font-bold">Primary Radar</span>
</div>
<div class="w-2 h-2 rounded-full bg-emerald-500"></div>
</div>
<div class="flex items-center justify-between p-3 bg-error-container/20 border border-error/10 rounded-lg">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-error text-sm">error</span>
<span class="text-xs font-bold text-error">ODU Unit - Sector B</span>
</div>
<div class="flex items-center gap-2">
<span class="text-[8px] font-black text-error uppercase">Fault</span>
<div class="w-2 h-2 rounded-full bg-error"></div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Operational Logs Table -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/10 shadow-sm overflow-hidden">
<div class="p-6 border-b border-outline-variant/10 flex justify-between items-center">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary">history_edu</span>
<h2 class="font-bold text-primary">Recent Shift Logs (A-1 Summary)</h2>
</div>
<div class="flex items-center gap-2 px-3 py-1 bg-surface-container-high rounded text-[10px] font-bold text-on-surface-variant">
<span class="material-symbols-outlined text-[12px]">lock</span>
                        LOCKED BY SHIFT SUPERVISOR
                    </div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left text-xs">
<thead class="bg-surface-container-low text-on-surface-variant uppercase font-bold tracking-widest">
<tr>
<th class="px-6 py-4">Timestamp</th>
<th class="px-6 py-4">Vessel Name</th>
<th class="px-6 py-4">Activity Type</th>
<th class="px-6 py-4">Status</th>
<th class="px-6 py-4">Officer</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/10">
<tr class="hover:bg-surface-container-lowest transition-colors">
<td class="px-6 py-4 font-mono">08:45:22</td>
<td class="px-6 py-4 font-bold">MV OCEANIC STAR</td>
<td class="px-6 py-4">Entry Clearance Issued</td>
<td class="px-6 py-4">
<span class="px-2 py-1 rounded bg-secondary-fixed text-on-secondary-fixed font-bold text-[10px]">VERIFIED</span>
</td>
<td class="px-6 py-4">Valdo (Shift B)</td>
</tr>
<tr class="hover:bg-surface-container-lowest transition-colors">
<td class="px-6 py-4 font-mono">08:12:05</td>
<td class="px-6 py-4 font-bold">TB MUSI JAYA V</td>
<td class="px-6 py-4">Mooring Confirmation</td>
<td class="px-6 py-4">
<span class="px-2 py-1 rounded bg-secondary-fixed text-on-secondary-fixed font-bold text-[10px]">VERIFIED</span>
</td>
<td class="px-6 py-4">Valdo (Shift B)</td>
</tr>
<tr class="hover:bg-surface-container-lowest transition-colors">
<td class="px-6 py-4 font-mono">07:55:18</td>
<td class="px-6 py-4 font-bold">KM SERAYU</td>
<td class="px-6 py-4">Departure Reporting</td>
<td class="px-6 py-4">
<span class="px-2 py-1 rounded bg-secondary-fixed text-on-secondary-fixed font-bold text-[10px]">VERIFIED</span>
</td>
<td class="px-6 py-4">Valdo (Shift B)</td>
</tr>
</tbody>
</table>
</div>
<div class="p-4 bg-surface-container-low/50 text-center">
<button class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">View Full Log Repository</button>
</div>
</div>
</div>
</main>
<!-- Floating Action Button -->
<button class="fixed bottom-8 right-8 bg-secondary-container text-primary font-black px-6 py-4 rounded-full shadow-2xl flex items-center gap-3 hover:scale-105 active:scale-95 transition-all z-50">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add_alert</span>
<span>Lapor Kejadian Khusus</span>
</button>
<!-- Footer -->
<footer class="pl-64 w-full border-t border-white/10 bg-[#001e40] dark:bg-black text-white font-['Inter'] text-xs uppercase tracking-widest w-full px-8 py-12 flex flex-col items-center gap-4 text-center">
<div class="flex gap-8 opacity-60 mb-4">
<span>Disnav Palembang Address</span>
<span>Phone: +62 711 XXXXXXX</span>
<span>Email: vts.palembang@dephub.go.id</span>
<span>VHF Channel: 16 / 68</span>
</div>
<p>© 2024 Kemenhub RI - VTS Palembang. All Rights Reserved.</p>
</footer>
</body></html>

<!-- Registrasi Staff (Modernism) -->
<!DOCTYPE html>

<html lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "surface-container-low": "#f1f4f9",
              "surface-container-high": "#e5e8ed",
              "outline": "#737780",
              "on-surface-variant": "#43474f",
              "primary-fixed-dim": "#a7c8ff",
              "on-surface": "#181c20",
              "on-secondary": "#ffffff",
              "surface-tint": "#3a5f94",
              "error": "#ba1a1a",
              "background": "#f7f9fe",
              "on-primary-fixed": "#001b3c",
              "on-background": "#181c20",
              "surface-dim": "#d7dadf",
              "primary": "#001e40",
              "inverse-on-surface": "#eef1f6",
              "tertiary-container": "#d0a600",
              "on-secondary-container": "#616363",
              "error-container": "#ffdad6",
              "outline-variant": "#c3c6d1",
              "on-primary": "#ffffff",
              "on-error-container": "#93000a",
              "secondary-fixed-dim": "#c6c6c7",
              "on-secondary-fixed-variant": "#454747",
              "secondary-fixed": "#e2e2e2",
              "on-tertiary-fixed-variant": "#584400",
              "tertiary-fixed-dim": "#f1c100",
              "surface-container-lowest": "#ffffff",
              "tertiary-fixed": "#ffe08b",
              "on-tertiary": "#ffffff",
              "surface-container": "#ebeef3",
              "on-primary-container": "#799dd6",
              "surface": "#f7f9fe",
              "on-error": "#ffffff",
              "surface-bright": "#f7f9fe",
              "inverse-surface": "#2d3135",
              "on-secondary-fixed": "#1a1c1c",
              "surface-variant": "#dfe3e8",
              "on-tertiary-container": "#4f3d00",
              "surface-container-highest": "#dfe3e8",
              "primary-fixed": "#d5e3ff",
              "tertiary": "#745b00",
              "on-primary-fixed-variant": "#1f477b",
              "inverse-primary": "#a7c8ff",
              "secondary-container": "#dfe0e0",
              "secondary": "#5d5f5f",
              "primary-container": "#003366",
              "on-tertiary-fixed": "#241a00"
            },
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
      }
      .industrial-pattern {
        background-image: radial-gradient(#c3c6d1 0.5px, transparent 0.5px);
        background-size: 24px 24px;
      }
    </style>
</head>
<body class="bg-background font-body text-on-surface antialiased min-h-screen flex flex-col industrial-pattern">
<!-- TopNavBar Suppression: Page is Transactional (Login) -->
<main class="flex-grow flex items-center justify-center p-6 relative overflow-hidden">
<!-- Background Decorative Element (Maritime Compass Blur) -->
<div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
<div class="absolute -bottom-24 -left-24 w-96 h-96 bg-tertiary/5 rounded-full blur-3xl pointer-events-none"></div>
<div class="w-full max-w-[480px] z-10">
<!-- Government Branding Header (Independent of Card for Editorial Feel) -->
<div class="text-center mb-10">
<img alt="Logo Kemenhub" class="h-16 mx-auto mb-4 drop-shadow-sm" data-alt="Official logo of the Ministry of Transportation of the Republic of Indonesia with golden eagle and anchor symbols" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDvtY1-JoJ9PK6ILCjFHzH3TMAo5IxegF7Fdv_okq_CHkqgF6pwC8OYH4vcdWbvxXhqphCpEm3EV4N3zH5UgnDkQ686qDACX4np3zDFajINBEoag9Ntuup-u1L0RRjMVkAlqEX0H_3wNUcs8k7wdafMedPbJxm9ivpW4w8Sar9b45hPVvvkdsqr1juZ4sz0RRbbN84mRzm9-cSruCk3wtxbi6gE9hspx_aT7E1wmXMFLszDSvT9ebXgHEQWXj5BWFOMnXimlOrUwKLN"/>
<h2 class="font-headline text-display-sm font-extrabold tracking-tighter text-primary uppercase">
                    VTS PALEMBANG
                </h2>
<p class="text-on-surface-variant font-label tracking-widest text-xs mt-1 uppercase">
                    Vessel Traffic Services Operational Logistics
                </p>
</div>
<!-- Login Card -->
<div class="surface-container-lowest rounded-xl shadow-[0_8px_24px_rgba(24,28,32,0.06)] overflow-hidden">
<div class="bg-primary p-8 text-center">
<h1 class="text-on-primary font-headline text-xl font-bold tracking-tight">Sistem Autentikasi</h1>
<p class="text-on-primary-container text-sm mt-1">Silakan masuk untuk mengelola logistik operasional.</p>
</div>
<form action="#" class="p-8 space-y-6" method="POST">
<!-- NIP Field -->
<div class="space-y-2">
<label class="block font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider" for="nip">
                            Nomor Induk Pegawai (NIP)
                        </label>
<div class="relative flex items-center">
<span class="absolute left-4 text-outline pointer-events-none">
<span class="material-symbols-outlined text-[20px]">badge</span>
</span>
<input class="w-full bg-surface-container-highest border-0 border-b border-outline-variant py-3.5 pl-12 pr-4 text-on-surface focus:ring-0 focus:bg-primary-fixed-dim/10 focus:border-tertiary transition-all outline-none text-sm placeholder:text-outline/50" id="nip" name="nip" placeholder="Contoh: 19XXXXXXXXXXXXXX" required="" type="text"/>
</div>
</div>
<!-- Password Field -->
<div class="space-y-2">
<div class="flex justify-between items-end">
<label class="block font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider" for="password">
                                Kata Sandi
                            </label>
<a class="text-xs font-semibold text-tertiary hover:underline transition-all" href="#">Lupa Password?</a>
</div>
<div class="relative flex items-center">
<span class="absolute left-4 text-outline pointer-events-none">
<span class="material-symbols-outlined text-[20px]">lock</span>
</span>
<input class="w-full bg-surface-container-highest border-0 border-b border-outline-variant py-3.5 pl-12 pr-4 text-on-surface focus:ring-0 focus:bg-primary-fixed-dim/10 focus:border-tertiary transition-all outline-none text-sm placeholder:text-outline/50" id="password" name="password" placeholder="••••••••" required="" type="password"/>
</div>
</div>
<!-- Security Notice -->
<div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg">
<span class="material-symbols-outlined text-tertiary text-[18px]">security</span>
<p class="text-[11px] text-on-surface-variant leading-tight">
                            Akses terbatas pada personel berwenang. Semua aktivitas di dalam sistem ini dipantau sesuai regulasi Kemenhub.
                        </p>
</div>
<!-- Action Button -->
<button class="w-full bg-tertiary-container text-on-tertiary-container py-4 rounded-md font-bold text-sm uppercase tracking-widest hover:brightness-105 active:scale-[0.98] transition-all shadow-sm" type="submit">
                        Masuk Sistem
                    </button>
</form>
<!-- Footer Action -->
<div class="bg-surface-container-low p-6 text-center border-t border-outline-variant/10">
<p class="text-sm text-on-surface-variant">
                        Belum memiliki akses? 
                        <a class="text-primary font-bold hover:text-tertiary transition-colors ml-1" href="#">Daftar Akun Baru</a>
</p>
</div>
</div>
<!-- Accessibility/Help -->
<div class="mt-8 flex justify-center gap-6">
<a class="flex items-center gap-2 text-xs text-outline hover:text-on-surface transition-colors" href="#">
<span class="material-symbols-outlined text-[16px]">help</span>
                    Bantuan Login
                </a>
<span class="text-outline-variant/30">|</span>
<a class="flex items-center gap-2 text-xs text-outline hover:text-on-surface transition-colors" href="#">
<span class="material-symbols-outlined text-[16px]">verified_user</span>
                    Kebijakan Privasi
                </a>
</div>
</div>
</main>
<!-- Shared Component: Footer -->
<footer class="w-full py-12 px-8 bg-[#f1f4f9] border-t border-[#c3c6d1]/20">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
<div class="space-y-4">
<h3 class="font-bold text-[#003366] font-headline">VTS PALEMBANG</h3>
<p class="text-[#737780] text-sm leading-relaxed max-w-xs">
                    Pusat layanan informasi dan pengaturan lalu lintas kapal di perairan Palembang dan sekitarnya.
                </p>
</div>
<div class="space-y-4">
<h4 class="font-bold text-on-surface text-sm uppercase tracking-widest">Tautan Cepat</h4>
<nav class="flex flex-col space-y-2">
<a class="text-[#737780] text-sm hover:text-[#003366] transition-all" href="#">Kebijakan Privasi</a>
<a class="text-[#737780] text-sm hover:text-[#003366] transition-all" href="#">Syarat &amp; Ketentuan</a>
<a class="text-[#737780] text-sm hover:text-[#003366] transition-all" href="#">Peta Situs</a>
<a class="text-[#737780] text-sm hover:text-[#003366] transition-all" href="#">Kontak Kami</a>
</nav>
</div>
<div class="space-y-4">
<h4 class="font-bold text-on-surface text-sm uppercase tracking-widest">Kontak Operasional</h4>
<div class="text-[#737780] text-sm space-y-1">
<p>Phone: +62 711 XXXXXXX</p>
<p>Email: vts.palembang@dephub.go.id</p>
<p>VHF Channel: 16 / 68</p>
</div>
</div>
<div class="space-y-4">
<h4 class="font-bold text-on-surface text-sm uppercase tracking-widest">Alamat</h4>
<p class="text-[#737780] text-sm leading-relaxed">
                    Jl. Malahayati No. 1, Komplek Pelabuhan Boom Baru, Palembang, Sumatera Selatan.
                </p>
</div>
</div>
<div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-[#c3c6d1]/10 text-center">
<p class="text-xs text-[#737780]">
                © 2024 Direktorat Jenderal Perhubungan Laut - VTS Palembang. Hak Cipta Dilindungi Undang-Undang.
            </p>
</div>
</footer>
</body></html>

<!-- Dashboard (Modernism) -->
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>VTS PALEMBANG - Input Log A3</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
          fontFamily: {
            "headline": ["Inter"],
            "body": ["Inter"],
            "label": ["Inter"]
          },
          borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
        },
      },
    }
  </script>
<style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .glass-panel {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    input:focus, select:focus {
      outline: none;
      border-bottom: 2px solid #001e40 !important;
    }
  </style>
</head>
<body class="bg-background font-body text-on-surface antialiased flex">
<!-- Side Navigation Rail -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-[#f7f9fe] dark:bg-slate-900 flex flex-col h-full py-6 font-['Inter'] text-sm font-medium">
<div class="px-6 mb-8">
<h1 class="text-[#001e40] font-black text-xl tracking-tighter">VTS PALEMBANG</h1>
</div>
<nav class="flex-1 space-y-1">
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span>Dashboard</span>
</a>
<a class="flex items-center gap-3 bg-[#001e40] text-white rounded-lg p-3 mx-2 translate-x-1 transition-transform" href="#">
<span class="material-symbols-outlined" data-icon="edit_note" style="font-variation-settings: 'FILL' 1;">edit_note</span>
<span>Input Log (A1-A3)</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="warning">warning</span>
<span>Laporan Khusus (A4-A8)</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="group">group</span>
<span>Manajemen User</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="picture_as_pdf">picture_as_pdf</span>
<span>Arsip &amp; Export PDF</span>
</a>
</nav>
<div class="px-4 mt-auto">
<div class="flex items-center gap-3 p-3 bg-surface-container-low rounded-xl mb-4">
<div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-white font-bold">V</div>
<div>
<p class="text-on-surface font-bold leading-none">Valdo</p>
<p class="text-xs text-on-surface-variant">Shift B | 882910</p>
</div>
</div>
<div class="space-y-1">
<a class="flex items-center gap-3 text-slate-600 p-3 hover:bg-slate-200 transition-colors rounded-lg" href="#">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
<span>Settings</span>
</a>
<a class="flex items-center gap-3 text-error p-3 hover:bg-error-container transition-colors rounded-lg" href="#">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
<span>Logout</span>
</a>
</div>
</div>
</aside>
<!-- Main Content Canvas -->
<main class="flex-1 ml-64 min-h-screen relative pb-20">
<!-- Top Header -->
<header class="sticky top-0 z-40 glass-panel border-b border-outline-variant/10 px-8 py-4 flex justify-between items-center">
<div>
<h2 class="headline-md text-primary font-bold tracking-tight">Form A3: Handover &amp; Maritime Weather</h2>
<p class="text-on-surface-variant text-sm">Update logistik harian dan kondisi cuaca perairan Palembang</p>
</div>
<div class="flex items-center gap-4">
<div class="text-right">
<p class="text-xs font-bold text-primary uppercase tracking-widest">Operational Status</p>
<p class="text-sm text-secondary font-semibold">Active Monitoring</p>
</div>
<span class="material-symbols-outlined text-primary p-2 bg-primary-fixed rounded-full" data-icon="notifications">notifications</span>
</div>
</header>
<div class="p-8 space-y-8">
<!-- Section: System Identity -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
<div class="md:col-span-2 glass-panel p-6 rounded-xl shadow-sm border border-white/20">
<h3 class="label-sm text-primary mb-4">Informasi Petugas Jaga</h3>
<div class="grid grid-cols-2 gap-6">
<div class="space-y-1">
<label class="text-[10px] font-bold uppercase text-on-surface-variant">Waktu Handover</label>
<input class="w-full bg-surface-container-high border-none rounded-none border-b-2 border-outline-variant py-2 text-sm" type="datetime-local" value="2024-05-24T08:00"/>
</div>
<div class="space-y-1">
<label class="text-[10px] font-bold uppercase text-on-surface-variant">Regu Jaga</label>
<select class="w-full bg-surface-container-high border-none rounded-none border-b-2 border-outline-variant py-2 text-sm">
<option>Regu Bravo (Shift Pagi)</option>
<option>Regu Alpha (Shift Sore)</option>
<option>Regu Charlie (Shift Malam)</option>
</select>
</div>
</div>
</div>
<div class="glass-panel p-6 rounded-xl border border-white/20 flex flex-col justify-center bg-gradient-to-br from-primary to-primary-container text-white">
<div class="flex items-center gap-3 mb-2">
<span class="material-symbols-outlined text-secondary-container" data-icon="anchor">anchor</span>
<span class="font-bold tracking-tight text-lg">Vessel Traffic Services</span>
</div>
<p class="text-xs text-on-primary-container leading-relaxed">Pastikan seluruh data cuaca sesuai dengan update terbaru dari BMKG Maritim untuk keselamatan pelayaran di alur Musi.</p>
</div>
</section>
<!-- Section: 11-Area Maritime Weather Grid -->
<section>
<div class="flex items-center justify-between mb-6">
<h3 class="headline-md text-primary font-bold">Maritime Weather Conditions</h3>
<div class="flex items-center gap-2 px-3 py-1 bg-surface-container-highest rounded-full text-xs font-bold text-on-surface-variant">
<span class="w-2 h-2 rounded-full bg-secondary-container animate-pulse"></span>
            Live Updates Required
          </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
<!-- Area Cards (Total 11 requested areas) -->
<!-- Card Template -->
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Banyuasin</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Berawan</option>
<option>Hujan Ringan</option>
<option>Hujan Lebat</option>
<option>Cerah</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" placeholder="5-10" type="number"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" placeholder="0.5" step="0.1" type="number"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Selat Gelasa</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Hujan Ringan</option>
<option>Berawan</option>
<option>Hujan Lebat</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="12"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="1.2"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">OKI (Ogan Komering Ilir)</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Cerah Berawan</option>
<option>Berawan</option>
<option>Hujan</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="8"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.4"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Boombaru</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Berawan</option>
<option>Cerah</option>
<option>Hujan Petir</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="4"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.2"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Selat Bangka</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Hujan Sedang</option>
<option>Berawan</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="15"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="1.5"/>
</div>
</div>
</div>
</div>
<!-- Repeat for other 6 areas to complete the 11 areas -->
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Tanjung Carat</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Berawan</option>
<option>Cerah</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="6"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.3"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Pulau Payung</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Cerah</option>
<option>Berawan</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="4"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.2"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Tanjung Kampeh</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Berawan</option>
<option>Hujan</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="9"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.6"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Sungsang</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Cerah Berawan</option>
<option>Hujan</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="7"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.5"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Upang</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Berawan</option>
<option>Hujan Ringan</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="5"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.2"/>
</div>
</div>
</div>
</div>
<div class="glass-panel p-4 rounded-xl border border-white/40 shadow-sm">
<div class="flex justify-between items-start mb-4">
<h4 class="font-bold text-primary">Lais</h4>
<span class="material-symbols-outlined text-primary/40 text-lg" data-icon="location_on">location_on</span>
</div>
<div class="space-y-3">
<select class="w-full text-xs bg-surface-container-low border-none rounded py-1.5 font-medium">
<option>Cerah</option>
<option>Berawan</option>
</select>
<div class="grid grid-cols-2 gap-2">
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Angin (kn)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="3"/>
</div>
<div class="bg-white/50 p-2 rounded">
<p class="text-[9px] uppercase font-bold text-slate-500">Gelombang (m)</p>
<input class="w-full bg-transparent border-none p-0 text-sm font-bold text-primary focus:ring-0" type="number" value="0.1"/>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Section: Pasang Surut & Final Action -->
<section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
<div class="glass-panel p-8 rounded-xl border border-white/20">
<div class="flex items-center gap-3 mb-6">
<span class="material-symbols-outlined text-primary" data-icon="water">water</span>
<h3 class="title-md font-bold text-primary">Tidal Information (Pasang Surut)</h3>
</div>
<div class="grid grid-cols-2 gap-8">
<div class="space-y-4">
<div>
<label class="label-sm text-on-surface-variant block mb-2">High Tide (Pasang Max)</label>
<div class="flex items-end gap-2">
<input class="w-24 bg-surface-container-high border-none border-b-2 border-primary py-2 text-xl font-bold text-primary" placeholder="3.2" step="0.1" type="number"/>
<span class="text-xs font-bold text-on-surface-variant pb-2">METERS</span>
</div>
</div>
<div>
<label class="label-sm text-on-surface-variant block mb-2">Time of Peak</label>
<input class="w-full bg-surface-container-high border-none border-b-2 border-outline-variant py-2" type="time"/>
</div>
</div>
<div class="space-y-4">
<div>
<label class="label-sm text-on-surface-variant block mb-2">Low Tide (Surut Min)</label>
<div class="flex items-end gap-2">
<input class="w-24 bg-surface-container-high border-none border-b-2 border-primary py-2 text-xl font-bold text-primary" placeholder="0.8" step="0.1" type="number"/>
<span class="text-xs font-bold text-on-surface-variant pb-2">METERS</span>
</div>
</div>
<div>
<label class="label-sm text-on-surface-variant block mb-2">Time of Low</label>
<input class="w-full bg-surface-container-high border-none border-b-2 border-outline-variant py-2" type="time"/>
</div>
</div>
</div>
</div>
<div class="flex flex-col justify-end gap-4 p-8 glass-panel rounded-xl border border-white/20 bg-surface-container-low/50">
<div class="mb-4">
<h3 class="title-md font-bold text-primary">Finalize Report</h3>
<p class="text-sm text-on-surface-variant">Pastikan semua data telah diverifikasi sebelum melakukan penguncian laporan A3.</p>
</div>
<div class="flex gap-4">
<button class="flex-1 py-4 px-6 bg-surface-container-highest text-primary font-bold rounded-lg hover:bg-outline-variant/20 transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined" data-icon="drafts">drafts</span>
              Save Draft
            </button>
<button class="flex-[2] py-4 px-6 bg-gradient-to-r from-primary to-primary-container text-white font-bold rounded-lg shadow-lg hover:shadow-primary/20 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
<span class="material-symbols-outlined" data-icon="lock" style="font-variation-settings: 'FILL' 1;">lock</span>
              Simpan Laporan &amp; Lock
            </button>
</div>
</div>
</section>
</div>
<!-- Footer -->
<footer class="w-full px-8 py-12 flex flex-col items-center gap-4 text-center border-t border-white/10 bg-[#001e40] text-white">
<div class="text-white font-bold tracking-tight">VTS PALEMBANG</div>
<div class="flex flex-wrap justify-center gap-x-8 gap-y-2 font-['Inter'] text-xs uppercase tracking-widest text-slate-400">
<span>Disnav Palembang Address</span>
<span>Phone: +62 711 XXXXXXX</span>
<span>Email: vts.palembang@dephub.go.id</span>
<span>VHF Channel: 16 / 68</span>
</div>
<div class="text-slate-500 text-[10px] mt-4">
        © 2024 Kemenhub RI - VTS Palembang. All Rights Reserved.
      </div>
</footer>
</main>
<!-- FAB: Yellow Incident Button -->
<button class="fixed bottom-8 right-8 w-16 h-16 bg-[#fecb00] text-[#001e40] rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-50 group">
<span class="material-symbols-outlined text-3xl" data-icon="report_problem" style="font-variation-settings: 'FILL' 1;">report_problem</span>
<span class="absolute right-full mr-4 px-3 py-1 bg-[#001e40] text-white text-xs font-bold whitespace-nowrap rounded opacity-0 group-hover:opacity-100 transition-opacity">Lapor Kejadian Khusus</span>
</button>
</body></html>

<!-- Login Sistem OPS-LOG -->
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Managerial Finalization &amp; Audit Trail | VTS PALEMBANG</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
      }
      .watermark-overlay {
        background-image: repeating-linear-gradient(-45deg, rgba(0, 30, 64, 0.03) 0, rgba(0, 30, 64, 0.03) 100px, transparent 100px, transparent 200px);
      }
      .industrial-gradient {
        background: linear-gradient(135deg, #001e40 0%, #003366 100%);
      }
    </style>
</head>
<body class="bg-background text-on-surface font-body antialiased selection:bg-secondary-container">
<!-- Sidebar Navigation -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-[#f7f9fe] dark:bg-slate-900 flex flex-col py-6 z-40 border-r border-outline-variant/15">
<div class="px-6 mb-8">
<h1 class="text-[#001e40] font-black tracking-tighter text-2xl">VTS PALEMBANG</h1>
<p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-widest mt-1">Sovereign Engine v2.4</p>
</div>
<nav class="flex-1 space-y-1">
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors rounded-lg font-medium text-sm" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                Dashboard
            </a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors rounded-lg font-medium text-sm" href="#">
<span class="material-symbols-outlined" data-icon="edit_note">edit_note</span>
                Input Log (A1-A3)
            </a>
<a class="flex items-center gap-3 bg-[#001e40] text-white rounded-lg p-3 mx-2 shadow-lg shadow-primary/20 font-medium text-sm" href="#">
<span class="material-symbols-outlined" data-icon="warning" style="font-variation-settings: 'FILL' 1;">warning</span>
                Laporan Khusus (A4-A8)
            </a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors rounded-lg font-medium text-sm" href="#">
<span class="material-symbols-outlined" data-icon="group">group</span>
                Manajemen User
            </a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors rounded-lg font-medium text-sm" href="#">
<span class="material-symbols-outlined" data-icon="picture_as_pdf">picture_as_pdf</span>
                Arsip &amp; Export PDF
            </a>
</nav>
<div class="mt-auto px-4 space-y-4">
<div class="p-4 bg-surface-container-high rounded-xl flex items-center gap-3">
<img class="w-10 h-10 rounded-full object-cover" data-alt="professional portrait of an Indonesian female manager in formal attire with a confident smile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAfQ9AG5DadD7aNsc9f_ayvWdLWdaB6HoF5VEn_hg-NY6sISm8-kJJGo6WlNkNZI2NIdg1z2N3ndTSErSK4bJD6JJrl2s9lQGPMWA1207absMkDC9Z4BVDcCDE4ghLHu0Xa5qkWcqa7akFu4v1en4BZy4VUIdW5tKhgjOI2q30kMc4zrvbZuO9X8TMh7OdK7_JS6TIt4Z3cEWpoR4UlUTYqrbw-BhUcEtVa-w6fYtle9_Sgxd0T0smTvee5cKDb3Khkfci9_7xTIDo1"/>
<div>
<p class="text-xs font-bold text-primary">Merry D. Anitasari</p>
<p class="text-[10px] text-on-surface-variant font-medium">Manager | Shift A</p>
</div>
</div>
<div class="space-y-1">
<a class="flex items-center gap-3 text-slate-600 p-3 hover:bg-slate-200 transition-colors rounded-lg text-sm" href="#">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
                    Settings
                </a>
<a class="flex items-center gap-3 text-error p-3 hover:bg-error-container/20 transition-colors rounded-lg text-sm" href="#">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
                    Logout
                </a>
</div>
</div>
</aside>
<!-- Main Content Canvas -->
<main class="ml-64 min-h-screen flex flex-col">
<!-- Header / Top Bar -->
<header class="bg-primary px-8 py-6 sticky top-0 z-30 flex justify-between items-center shadow-2xl shadow-primary/10">
<div class="flex items-center gap-4">
<div class="bg-secondary-container text-on-secondary-container px-4 py-2 rounded-lg font-black tracking-tighter text-sm flex items-center gap-2">
<span class="material-symbols-outlined text-base" data-icon="pending_actions">pending_actions</span>
                    AWAITING ACKNOWLEDGE
                </div>
<div class="h-8 w-px bg-white/20"></div>
<div>
<h2 class="text-white font-bold headline-md leading-none">Managerial Finalization</h2>
<p class="text-on-primary-container text-xs mt-1 uppercase tracking-widest font-semibold">Incident Report ID: VTS-2024-0892-ALPHA</p>
</div>
</div>
<div class="flex gap-3">
<button class="bg-white/10 text-white px-5 py-2.5 rounded hover:bg-white/20 transition-all text-sm font-bold flex items-center gap-2">
<span class="material-symbols-outlined text-sm" data-icon="print">print</span>
                    Preview Draft
                </button>
<button class="industrial-gradient text-white px-6 py-2.5 rounded hover:opacity-90 transition-all text-sm font-bold flex items-center gap-2 shadow-xl border border-white/10">
<span class="material-symbols-outlined text-sm" data-icon="lock_open">lock_open</span>
                    Acknowledge Report
                </button>
</div>
</header>
<div class="p-8 space-y-8 max-w-7xl mx-auto w-full">
<!-- Asymmetric Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
<!-- Left Column: Audit Trail & Edit Requests (8 cols) -->
<div class="lg:col-span-8 space-y-8">
<!-- Audit Log Table -->
<section class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/10">
<div class="flex justify-between items-end mb-6">
<div>
<h3 class="title-md font-bold text-primary flex items-center gap-2">
<span class="material-symbols-outlined text-xl" data-icon="history">history</span>
                                    Comprehensive Audit Trail
                                </h3>
<p class="text-on-surface-variant text-sm mt-1 font-medium">System-wide immutable record of all modifications.</p>
</div>
<span class="text-[10px] font-black text-on-surface-variant/40 bg-surface-container px-2 py-1 rounded">ISO 27001 COMPLIANT</span>
</div>
<div class="overflow-hidden rounded-lg border border-outline-variant/15">
<table class="w-full text-left text-sm border-collapse">
<thead class="bg-surface-container-high">
<tr>
<th class="px-4 py-3 font-bold text-on-surface text-xs uppercase tracking-tight">Field</th>
<th class="px-4 py-3 font-bold text-on-surface text-xs uppercase tracking-tight">Old Value</th>
<th class="px-4 py-3 font-bold text-on-surface text-xs uppercase tracking-tight">New Value</th>
<th class="px-4 py-3 font-bold text-on-surface text-xs uppercase tracking-tight">User</th>
<th class="px-4 py-3 font-bold text-on-surface text-xs uppercase tracking-tight">Timestamp</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/10">
<tr class="hover:bg-surface-container-low transition-colors">
<td class="px-4 py-4 font-bold text-primary">Vessel Name</td>
<td class="px-4 py-4 text-on-surface-variant italic">MV OCEANIC STAR</td>
<td class="px-4 py-4 font-medium text-secondary">MV OCEANIC PRIDE</td>
<td class="px-4 py-4">
<div class="flex items-center gap-2">
<div class="w-6 h-6 rounded-full bg-primary-container text-[10px] flex items-center justify-center text-white font-bold">V</div>
<span class="font-medium text-xs">Valdo (Op)</span>
</div>
</td>
<td class="px-4 py-4 text-xs font-mono text-on-surface-variant">2024-10-24 08:14:22</td>
</tr>
<tr class="hover:bg-surface-container-low transition-colors">
<td class="px-4 py-4 font-bold text-primary">ETA Palembang</td>
<td class="px-4 py-4 text-on-surface-variant italic">14:00 WIB</td>
<td class="px-4 py-4 font-medium text-secondary">15:30 WIB</td>
<td class="px-4 py-4">
<div class="flex items-center gap-2">
<div class="w-6 h-6 rounded-full bg-primary-container text-[10px] flex items-center justify-center text-white font-bold">V</div>
<span class="font-medium text-xs">Valdo (Op)</span>
</div>
</td>
<td class="px-4 py-4 text-xs font-mono text-on-surface-variant">2024-10-24 08:22:10</td>
</tr>
<tr class="hover:bg-surface-container-low transition-colors">
<td class="px-4 py-4 font-bold text-primary">Incident Status</td>
<td class="px-4 py-4 text-on-surface-variant italic">DRAFT</td>
<td class="px-4 py-4 font-medium text-secondary">REVIEW</td>
<td class="px-4 py-4">
<div class="flex items-center gap-2">
<div class="w-6 h-6 rounded-full bg-surface-variant text-[10px] flex items-center justify-center text-primary font-bold">S</div>
<span class="font-medium text-xs">Supervisor B</span>
</div>
</td>
<td class="px-4 py-4 text-xs font-mono text-on-surface-variant">2024-10-24 09:45:00</td>
</tr>
</tbody>
</table>
</div>
</section>
<!-- Edit Request Panel (Supervisor Role Focus) -->
<section class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/10">
<div class="flex items-center justify-between mb-6">
<h3 class="title-md font-bold text-primary flex items-center gap-2">
<span class="material-symbols-outlined text-xl" data-icon="edit_attributes">edit_attributes</span>
                                Pending Edit Requests
                            </h3>
<span class="bg-error/10 text-error px-3 py-1 rounded-full text-[10px] font-black">2 REQUIRES ATTENTION</span>
</div>
<div class="space-y-4">
<!-- Request Card 1 -->
<div class="flex items-center gap-6 p-4 rounded-lg bg-surface-container-low border-l-4 border-secondary shadow-sm">
<div class="flex-1">
<div class="flex items-center gap-2 mb-1">
<span class="text-xs font-black text-on-surface-variant uppercase tracking-widest">Request #REQ-882</span>
<span class="h-1 w-1 bg-outline-variant rounded-full"></span>
<span class="text-xs font-bold text-primary">Valdo (Operator)</span>
</div>
<p class="body-md font-medium text-on-surface">Change "Coordinate Error" to "GNSS Interruption" for technical accuracy.</p>
</div>
<div class="flex gap-2">
<button class="bg-white text-error border border-error/20 px-4 py-2 rounded text-xs font-bold hover:bg-error/5 transition-colors">REJECT</button>
<button class="bg-secondary-container text-on-secondary-container px-4 py-2 rounded text-xs font-bold hover:opacity-90 transition-colors shadow-md">APPROVE</button>
</div>
</div>
<!-- Request Card 2 -->
<div class="flex items-center gap-6 p-4 rounded-lg bg-surface-container-low border-l-4 border-secondary shadow-sm">
<div class="flex-1">
<div class="flex items-center gap-2 mb-1">
<span class="text-xs font-black text-on-surface-variant uppercase tracking-widest">Request #REQ-883</span>
<span class="h-1 w-1 bg-outline-variant rounded-full"></span>
<span class="text-xs font-bold text-primary">Samsul (Bridge Officer)</span>
</div>
<p class="body-md font-medium text-on-surface">Update vessel draft from 8.2m to 8.5m after manual sounding verification.</p>
</div>
<div class="flex gap-2">
<button class="bg-white text-error border border-error/20 px-4 py-2 rounded text-xs font-bold hover:bg-error/5 transition-colors">REJECT</button>
<button class="bg-secondary-container text-on-secondary-container px-4 py-2 rounded text-xs font-bold hover:opacity-90 transition-colors shadow-md">APPROVE</button>
</div>
</div>
</div>
</section>
</div>
<!-- Right Column: Verification & System Status (4 cols) -->
<div class="lg:col-span-4 space-y-8">
<!-- Verification / Final Copy Preview -->
<section class="glass-panel rounded-xl border border-white/40 shadow-xl overflow-hidden relative">
<div class="watermark-overlay absolute inset-0 z-0"></div>
<div class="relative z-10 p-6">
<div class="flex items-center justify-between mb-8">
<div class="w-12 h-12">
<img class="w-full h-full object-contain brightness-0 opacity-80" data-alt="high-contrast navy and gold vector logo of a government agency with nautical symbols" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCjoOlvyWlA9ymbS-VuSm7d1QtIY8ep0cVWTTv6AzGOix2nkFKPfW2BJjff89LllKCt8coA4cFyxRoFeNPLh8-X3Ty4okpilTtLiyYM8ikyB1DCInttVIaRGqZf4r6ixpXNbzgpOGinciwEFYn6bvWSpvZ5ZXj9ypbp7WBYdk0RyuHVpwYCLf02EjhFNBKatCzmQj_9pg2-GFufL8PGoR7rcrc_frg6e_PsnvoMnuSq8sqAIT4jzFam62P_9vfPGPE-X6OdYb_C5eRS"/>
</div>
<div class="text-right">
<p class="text-[10px] font-black text-primary uppercase">Official Copy</p>
<p class="text-[10px] font-medium text-on-surface-variant">Ref: PLB/LOG/2024/OCT/01</p>
</div>
</div>
<div class="space-y-4 mb-8">
<div class="p-3 bg-white/50 rounded-lg border border-white/60">
<h4 class="label-sm text-on-surface-variant mb-1">Incident Type</h4>
<p class="text-sm font-bold text-primary">A4 - Navigational Hazard Detection</p>
</div>
<div class="p-3 bg-white/50 rounded-lg border border-white/60">
<h4 class="label-sm text-on-surface-variant mb-1">Position</h4>
<p class="text-sm font-mono font-bold text-primary">02°59'S 104°45'E</p>
</div>
<div class="p-3 bg-white/50 rounded-lg border border-white/60">
<h4 class="label-sm text-on-surface-variant mb-1">Vessel Status</h4>
<p class="text-sm font-bold text-primary">Moored - Secured</p>
</div>
</div>
<div class="flex flex-col items-center py-6 border-y border-primary/10 gap-2 mb-6">
<div class="w-32 h-16 border-b border-primary/20 flex items-center justify-center opacity-40">
<span class="text-xs text-on-surface-variant italic">[ E-SIGNATURE PENDING ]</span>
</div>
<p class="label-sm font-bold text-primary">Merry D. Anitasari</p>
<p class="text-[9px] text-on-surface-variant font-medium">Head of VTS Operations</p>
</div>
<div class="absolute -right-8 top-1/2 -translate-y-1/2 rotate-90 flex items-center gap-2 pointer-events-none">
<span class="text-[10px] font-black text-secondary tracking-widest opacity-30">VOID WITHOUT AUTHORIZATION</span>
</div>
</div>
</section>
<!-- Quick Insights Panel -->
<section class="bg-primary text-white rounded-xl p-6 shadow-2xl relative overflow-hidden">
<div class="absolute top-0 right-0 p-4 opacity-10">
<span class="material-symbols-outlined text-6xl" data-icon="verified_user">verified_user</span>
</div>
<h3 class="title-md font-bold mb-4">Integrity Summary</h3>
<div class="space-y-4">
<div class="flex justify-between items-center text-sm">
<span class="text-on-primary-container">Validation Checks</span>
<span class="font-bold text-secondary-container">12/12 PASSED</span>
</div>
<div class="w-full bg-white/10 h-1.5 rounded-full">
<div class="bg-secondary-container h-full w-full rounded-full"></div>
</div>
<div class="flex justify-between items-center text-sm">
<span class="text-on-primary-container">Audit Logs Hash</span>
<span class="font-mono text-[10px] opacity-70">f82x...k8l1</span>
</div>
<div class="pt-4 border-t border-white/10 mt-4">
<p class="text-[11px] leading-relaxed opacity-80 italic">Acknowledging this report will permanently lock the log entries from further manual edits. Any subsequent changes must be logged as "Corrections to Final Records" with supervisor escalation.</p>
</div>
</div>
</section>
</div>
</div>
</div>
<!-- Footer -->
<footer class="mt-auto w-full border-t border-outline-variant/15 bg-primary px-8 py-10 flex flex-col items-center gap-4 text-center">
<div class="flex gap-8 mb-4">
<img class="h-8 object-contain filter grayscale invert opacity-50" data-alt="a clean high-tech corporate office building with modern glass architecture under a clear blue sky" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAJ85Xtc83Mj9jywwsCRvulmR4GmF67pjj54Ctl-DEYzTPgnDAlxi1Lxr7CuOYcnzeiNE6t_jdcI1GXdXkFWiAfaB6GOhaXCw7O_pSE8YRQQ5rSYrPgMJwntBLcB3Djjd6ESLluOf-TjFi_aw8cxJ9-dI4cJitHMQg40N5uaOjw_03L1WE0RXHsFJ_Hsu-km2h_0qMcHvK7wE1SI8fcoZA9hPhG_WBngveA36uSN3tcKkDDC3CQ0UecnStkRrfkrBHpLgia1GQsm2Cx"/>
</div>
<p class="font-label text-white text-[10px] uppercase tracking-[0.2em] font-bold">© 2024 KEMENHUB RI - VTS PALEMBANG. ALL RIGHTS RESERVED.</p>
<div class="flex gap-6 text-[10px] text-on-primary-container font-medium">
<a class="hover:text-white transition-colors" href="#">Disnav Palembang Address</a>
<a class="hover:text-white transition-colors" href="#">Phone: +62 711 XXXXXXX</a>
<a class="hover:text-white transition-colors" href="#">Email: vts.palembang@dephub.go.id</a>
<a class="hover:text-white transition-colors" href="#">VHF Channel: 16 / 68</a>
</div>
</footer>
</main>
</body></html>

<!-- Log A3 Handover (Modernism) -->
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>VTS PALEMBANG - Input Log A2 Traffic Entry</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
      }
      .industrial-gradient {
        background: linear-gradient(135deg, #001e40 0%, #003366 100%);
      }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">
<!-- TopNavBar -->
<nav class="fixed top-0 z-50 w-full bg-[#001e40] bg-opacity-90 backdrop-blur-md shadow-xl flex justify-between items-center px-8 py-4 font-['Inter'] antialiased tracking-tight">
<div class="flex items-center gap-4">
<span class="text-xl font-bold tracking-tighter text-white uppercase">VTS PALEMBANG</span>
</div>
<div class="hidden md:flex items-center gap-8">
<a class="text-slate-300 hover:text-white transition-colors" href="#">Beranda</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Layanan</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Informasi</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Kontak</a>
</div>
<div class="flex items-center gap-6">
<button class="text-slate-300 hover:bg-white/10 transition-all p-2 rounded-full flex items-center">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
</button>
<div class="h-8 w-8 rounded-full bg-primary-container flex items-center justify-center overflow-hidden border border-white/20">
<img alt="Logo Kemenhub" class="w-full h-full object-cover" data-alt="professional golden ministry of transportation seal on deep navy circular background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAmCE4U0bLCtczcuwh2ChwM0169tGGAm5swwFz3ou_cCzbRZhpN6ZF_GDC25cKVakLIZwe8Qij29pa3PdgZwwrbgfdt93mQtUtBwf8xDsBLankOanP2aLWf9SugO5q3WbQv8U-Bg5Wrgd6J5B6JF1hXq6SordZHf9ogu5Zg6Bs4chQB6X7TfP-AryDnZusiZz_rxkw59d0AGr2a8VSI8f5zuvdlk8QfRjZl-7-EEeZEnVOx40db3EC7tPyLI1tprt_UhDXDYe6knDk7"/>
</div>
<button class="bg-secondary-container text-on-secondary-container px-6 py-2 rounded font-bold hover:opacity-90 transition-all Active:scale-95">Masuk Sistem</button>
</div>
</nav>
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-[#f7f9fe] dark:bg-slate-900 flex flex-col pt-24 pb-6 font-['Inter'] text-sm font-medium border-r border-outline-variant/15">
<div class="px-6 mb-8 flex items-center gap-3">
<div class="w-10 h-10 rounded bg-primary industrial-gradient flex items-center justify-center">
<span class="material-symbols-outlined text-white" data-icon="account_circle">account_circle</span>
</div>
<div>
<p class="text-[#001e40] font-black leading-tight">Valdo</p>
<p class="text-on-surface-variant text-xs font-normal">NIP: 882910 | Shift B</p>
</div>
</div>
<nav class="flex-1 flex flex-col gap-1">
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                Dashboard
            </a>
<a class="flex items-center gap-3 bg-[#001e40] text-white rounded-lg p-3 mx-2 Active:translate-x-1 transition-transform" href="#">
<span class="material-symbols-outlined" data-icon="edit_note" style="font-variation-settings: 'FILL' 1;">edit_note</span>
                Input Log (A1-A3)
            </a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="warning">warning</span>
                Laporan Khusus (A4-A8)
            </a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="group">group</span>
                Manajemen User
            </a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="picture_as_pdf">picture_as_pdf</span>
                Arsip &amp; Export PDF
            </a>
</nav>
<div class="mt-auto px-4">
<button class="w-full industrial-gradient text-white py-3 rounded-lg flex items-center justify-center gap-2 shadow-lg hover:opacity-90 transition-all mb-4">
<span class="material-symbols-outlined text-sm" data-icon="campaign">campaign</span>
                Lapor Kejadian Khusus
            </button>
<div class="flex flex-col gap-1">
<a class="flex items-center gap-3 text-slate-400 p-2 hover:text-[#001e40] transition-colors" href="#">
<span class="material-symbols-outlined text-lg" data-icon="settings">settings</span>
                    Settings
                </a>
<a class="flex items-center gap-3 text-slate-400 p-2 hover:text-error transition-colors" href="#">
<span class="material-symbols-outlined text-lg" data-icon="logout">logout</span>
                    Logout
                </a>
</div>
</div>
</aside>
<!-- Main Content Canvas -->
<main class="ml-64 pt-24 px-8 pb-12">
<!-- Dashboard Stats Summary -->
<header class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
<div class="glass-panel p-5 rounded-xl border border-white/40 shadow-sm flex flex-col">
<span class="text-label-sm font-bold text-on-surface-variant uppercase tracking-widest mb-2">Total Traffic Today</span>
<div class="flex items-end gap-2">
<span class="text-3xl font-bold text-primary">42</span>
<span class="text-secondary text-sm font-semibold mb-1 flex items-center gap-1">
<span class="material-symbols-outlined text-xs" data-icon="trending_up">trending_up</span> +12%
                    </span>
</div>
</div>
<div class="glass-panel p-5 rounded-xl border border-white/40 shadow-sm flex flex-col">
<span class="text-label-sm font-bold text-on-surface-variant uppercase tracking-widest mb-2">Vessels Inbound</span>
<div class="flex items-end gap-2">
<span class="text-3xl font-bold text-primary">18</span>
<span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full mb-1">Active</span>
</div>
</div>
<div class="glass-panel p-5 rounded-xl border border-white/40 shadow-sm flex flex-col">
<span class="text-label-sm font-bold text-on-surface-variant uppercase tracking-widest mb-2">Vessels Outbound</span>
<div class="flex items-end gap-2">
<span class="text-3xl font-bold text-primary">24</span>
<span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full mb-1">Cleared</span>
</div>
</div>
<div class="glass-panel p-5 rounded-xl border border-white/40 shadow-sm flex flex-col border-l-4 border-l-secondary-container">
<span class="text-label-sm font-bold text-on-surface-variant uppercase tracking-widest mb-2">Incident Alerts</span>
<div class="flex items-end gap-2">
<span class="text-3xl font-bold text-secondary">03</span>
<span class="text-error text-sm font-semibold mb-1">Unresolved</span>
</div>
</div>
</header>
<!-- Command Panel: A2 Traffic Entry -->
<section class="glass-panel rounded-xl overflow-hidden shadow-xl border border-white/20">
<!-- Form Tabs -->
<div class="bg-surface-container-low px-6 pt-4 flex gap-8 border-b border-outline-variant/15">
<button class="pb-3 px-2 text-on-surface-variant font-semibold text-sm hover:text-primary transition-colors">A1 - Arrival Log</button>
<button class="pb-3 px-2 text-primary font-bold text-sm border-b-2 border-primary relative">
                    A2 - Traffic Entry
                    <span class="absolute -top-1 -right-2 bg-secondary-container text-on-secondary-fixed text-[10px] px-1 rounded-sm">ACTIVE</span>
</button>
<button class="pb-3 px-2 text-on-surface-variant font-semibold text-sm hover:text-primary transition-colors">A3 - Departure Log</button>
</div>
<!-- Panel Header -->
<div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h1 class="text-headline-md font-bold text-primary flex items-center gap-3">
                        Vessel Traffic Entry System (A2)
                    </h1>
<p class="text-on-surface-variant text-sm mt-1">Real-time maritime traffic monitoring for Musi River fairway.</p>
</div>
<div class="flex items-center gap-3">
<div class="relative">
<span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
<span class="material-symbols-outlined text-sm" data-icon="search">search</span>
</span>
<input class="pl-10 pr-4 py-2 bg-surface-container-high rounded-lg text-sm border-none focus:ring-2 focus:ring-primary w-64" placeholder="Cari Nama/Call Sign..." type="text"/>
</div>
<button class="industrial-gradient text-white px-5 py-2.5 rounded font-bold text-sm flex items-center gap-2 hover:shadow-lg transition-all Active:scale-95">
<span class="material-symbols-outlined" data-icon="add">add</span>
                        [+ Tambah Baris Kapal]
                    </button>
</div>
</div>
<!-- Dynamic Data Table -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-highest text-label-sm font-bold text-on-surface-variant uppercase tracking-widest">
<th class="px-6 py-4">Status</th>
<th class="px-6 py-4">Nama Kapal</th>
<th class="px-6 py-4">Call Sign</th>
<th class="px-6 py-4">Asal</th>
<th class="px-6 py-4">Tujuan</th>
<th class="px-6 py-4">ETA/ETD</th>
<th class="px-6 py-4">Posisi</th>
<th class="px-6 py-4 text-center">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/10 text-body-md">
<!-- Record 1: Locked -->
<tr class="hover:bg-primary/5 transition-colors group bg-surface-container-lowest/50">
<td class="px-6 py-4">
<span class="flex items-center gap-2 text-primary font-bold">
<span class="w-2 h-2 rounded-full bg-secondary-container"></span>
                                    INBOUND
                                </span>
</td>
<td class="px-6 py-4 font-bold text-primary">MV ORIENTAL SAMUDRA</td>
<td class="px-6 py-4 font-mono text-slate-500">9V8821</td>
<td class="px-6 py-4">Singapore</td>
<td class="px-6 py-4">Boom Baru</td>
<td class="px-6 py-4">
<div class="flex flex-col">
<span class="font-semibold">08:45 WIB</span>
<span class="text-[10px] text-slate-400">22 JUN 2024</span>
</div>
</td>
<td class="px-6 py-4">
<span class="bg-surface-container-high px-2 py-1 rounded text-xs font-mono">02° 59' S / 104° 45' E</span>
</td>
<td class="px-6 py-4">
<div class="flex items-center justify-center gap-4">
<span class="material-symbols-outlined text-outline cursor-not-allowed opacity-50" data-icon="lock" style="font-variation-settings: 'FILL' 1;">lock</span>
<button class="text-primary hover:text-secondary-container transition-colors" title="Request Edit">
<span class="material-symbols-outlined" data-icon="edit_document">edit_document</span>
</button>
</div>
</td>
</tr>
<!-- Record 2: Active/Draft -->
<tr class="hover:bg-primary/5 transition-colors group">
<td class="px-6 py-4">
<span class="flex items-center gap-2 text-slate-600 font-bold">
<span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                    OUTBOUND
                                </span>
</td>
<td class="px-6 py-4 font-bold text-primary">MT GAS KALIMANTAN</td>
<td class="px-6 py-4 font-mono text-slate-500">PK3290</td>
<td class="px-6 py-4">Palembang</td>
<td class="px-6 py-4">Surabaya</td>
<td class="px-6 py-4">
<div class="flex flex-col">
<span class="font-semibold text-secondary">14:20 WIB</span>
<span class="text-[10px] text-slate-400">22 JUN 2024</span>
</div>
</td>
<td class="px-6 py-4">
<span class="bg-surface-container-high px-2 py-1 rounded text-xs font-mono">03° 02' S / 104° 48' E</span>
</td>
<td class="px-6 py-4">
<div class="flex items-center justify-center gap-4">
<button class="text-primary hover:scale-110 transition-transform">
<span class="material-symbols-outlined" data-icon="edit" style="font-variation-settings: 'FILL' 0;">edit</span>
</button>
<button class="text-error hover:scale-110 transition-transform">
<span class="material-symbols-outlined" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</button>
</div>
</td>
</tr>
<!-- Record 3: Locked -->
<tr class="hover:bg-primary/5 transition-colors group bg-surface-container-lowest/50">
<td class="px-6 py-4">
<span class="flex items-center gap-2 text-primary font-bold">
<span class="w-2 h-2 rounded-full bg-secondary-container"></span>
                                    INBOUND
                                </span>
</td>
<td class="px-6 py-4 font-bold text-primary">TB BRAVO 09 / BG DELTA</td>
<td class="px-6 py-4 font-mono text-slate-500">YB4412</td>
<td class="px-6 py-4">Jakarta</td>
<td class="px-6 py-4">Sungai Lais</td>
<td class="px-6 py-4">
<div class="flex flex-col">
<span class="font-semibold">05:15 WIB</span>
<span class="text-[10px] text-slate-400">22 JUN 2024</span>
</div>
</td>
<td class="px-6 py-4">
<span class="bg-surface-container-high px-2 py-1 rounded text-xs font-mono">03° 00' S / 104° 46' E</span>
</td>
<td class="px-6 py-4">
<div class="flex items-center justify-center gap-4">
<span class="material-symbols-outlined text-outline cursor-not-allowed opacity-50" data-icon="lock" style="font-variation-settings: 'FILL' 1;">lock</span>
<button class="text-primary hover:text-secondary-container transition-colors" title="Request Edit">
<span class="material-symbols-outlined" data-icon="edit_document">edit_document</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Footer Pagination/Summary -->
<div class="p-6 bg-surface-container-low flex items-center justify-between border-t border-outline-variant/15">
<p class="text-xs text-on-surface-variant font-medium">Menampilkan 1-10 dari 42 data kapal hari ini.</p>
<div class="flex gap-2">
<button class="p-2 bg-white rounded border border-outline-variant/20 hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-sm" data-icon="chevron_left">chevron_left</span>
</button>
<button class="p-2 bg-primary text-white rounded shadow-md px-4 text-xs font-bold">1</button>
<button class="p-2 bg-white rounded border border-outline-variant/20 hover:bg-surface-container-high transition-colors px-4 text-xs font-bold">2</button>
<button class="p-2 bg-white rounded border border-outline-variant/20 hover:bg-surface-container-high transition-colors px-4 text-xs font-bold">3</button>
<button class="p-2 bg-white rounded border border-outline-variant/20 hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
</button>
</div>
</div>
</section>
<!-- Dynamic Monitoring Map Area (Simulated) -->
<section class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
<div class="md:col-span-2 glass-panel rounded-xl overflow-hidden shadow-lg h-80 relative border border-white/20">
<div class="absolute top-4 left-4 z-10 bg-primary/90 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 backdrop-blur-md">
<span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    LIVE AIS RADAR VIEW
                </div>
<img alt="radar maritime map of musi river palembang" class="w-full h-full object-cover" data-alt="a professional digital maritime radar map showing vessel icons moving along a glowing blue river path against a dark navy background" data-location="Palembang" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCAUdr13LSqo6UOn21x0_VF6FdWRTrs5bvtixvPxGMrVZfiiVoJBePqfzxKAMRB6IDou3BuOhOun_bVJXzkLy9fbzLaI6oiYfOOzI2Zbxf8DOM7p7Kg933gL4cC1eRWdLIXbSL-GJhyBfpPV5kAFcQq0-iZrRK6QwfBBF4QA1_hH8ujJ0UVIGdWHn3tHnigdJdPti6srXQAeM9Ui70LIPGf6f-4GWFc7nUw5hFHWJpjVU64SnMgyixNYQWNG-E5-78b5wHpxK7qVcg6"/>
<div class="absolute bottom-4 right-4 z-10 flex gap-2">
<button class="bg-white/90 p-2 rounded shadow hover:bg-white"><span class="material-symbols-outlined text-primary" data-icon="zoom_in">zoom_in</span></button>
<button class="bg-white/90 p-2 rounded shadow hover:bg-white"><span class="material-symbols-outlined text-primary" data-icon="zoom_out">zoom_out</span></button>
</div>
</div>
<div class="glass-panel p-6 rounded-xl shadow-lg border border-white/20 flex flex-col">
<h3 class="text-title-md font-bold text-primary mb-4 flex items-center gap-2">
<span class="material-symbols-outlined" data-icon="history">history</span>
                    Recent Activity
                </h3>
<div class="space-y-4 flex-1">
<div class="flex gap-3 border-l-2 border-primary/20 pl-4 py-1">
<div class="text-[10px] font-bold text-on-surface-variant uppercase w-12 pt-1">09:12</div>
<div>
<p class="text-sm font-bold text-primary leading-tight">Vessel Entry Authorized</p>
<p class="text-xs text-slate-500">MV SAMUDRA 01 approved by SPV</p>
</div>
</div>
<div class="flex gap-3 border-l-2 border-secondary-container pl-4 py-1">
<div class="text-[10px] font-bold text-on-surface-variant uppercase w-12 pt-1">08:55</div>
<div>
<p class="text-sm font-bold text-primary leading-tight">Log A2 Updated</p>
<p class="text-xs text-slate-500">Valdo added MT GAS KALIMANTAN</p>
</div>
</div>
<div class="flex gap-3 border-l-2 border-error/20 pl-4 py-1">
<div class="text-[10px] font-bold text-on-surface-variant uppercase w-12 pt-1">08:30</div>
<div>
<p class="text-sm font-bold text-error leading-tight">Edit Request Denied</p>
<p class="text-xs text-slate-500">Log #8812 cannot be modified</p>
</div>
</div>
</div>
<button class="mt-4 text-xs font-bold text-primary hover:underline flex items-center justify-center gap-2">
                    View Full System Logs <span class="material-symbols-outlined text-xs" data-icon="arrow_forward">arrow_forward</span>
</button>
</div>
</section>
</main>
<!-- Floating Action Button: Incident -->
<button class="fixed bottom-8 right-8 bg-secondary-container text-on-secondary-fixed-variant p-4 rounded-full shadow-2xl hover:scale-110 active:scale-95 transition-all z-[60] flex items-center justify-center group">
<span class="material-symbols-outlined text-3xl" data-icon="report" style="font-variation-settings: 'FILL' 1;">report</span>
<span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 group-hover:ml-3 font-bold whitespace-nowrap text-sm">URGENT REPORT</span>
</button>
<!-- Footer -->
<footer class="ml-64 bg-[#001e40] text-white border-t border-white/10 w-auto px-8 py-12 flex flex-col items-center gap-4 text-center font-['Inter'] text-xs uppercase tracking-widest">
<div class="flex flex-col md:flex-row gap-8 mb-4 opacity-70">
<span class="flex items-center gap-2">
<span class="material-symbols-outlined text-sm" data-icon="location_on">location_on</span>
                Disnav Palembang Address
            </span>
<span class="flex items-center gap-2">
<span class="material-symbols-outlined text-sm" data-icon="call">call</span>
                Phone: +62 711 XXXXXXX
            </span>
<span class="flex items-center gap-2">
<span class="material-symbols-outlined text-sm" data-icon="mail">mail</span>
                Email: vts.palembang@dephub.go.id
            </span>
<span class="flex items-center gap-2 text-[#fecb00]">
<span class="material-symbols-outlined text-sm" data-icon="radio">radio</span>
                VHF Channel: 16 / 68
            </span>
</div>
<p class="opacity-50">© 2024 Kemenhub RI - VTS Palembang. All Rights Reserved.</p>
</footer>
</body></html>

<!-- Audit & Finalisasi (Modernism) -->
<!DOCTYPE html>

<html lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>PDF Export Preview - VTS PALEMBANG</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      .sop-paper {
        aspect-ratio: 1 / 1.414;
        width: 100%;
        background: white;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
        padding: 40px;
        position: relative;
        overflow: hidden;
      }
      .sop-paper-landscape {
        aspect-ratio: 1.414 / 1;
        width: 100%;
        background: white;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1);
        padding: 40px;
        position: relative;
        overflow: hidden;
      }
      .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 8rem;
        font-weight: 900;
        color: rgba(0, 30, 64, 0.05);
        pointer-events: none;
        white-space: nowrap;
        z-index: 0;
      }
      .sop-table th, .sop-table td {
        border: 1px solid #c3c6d1;
        padding: 8px;
        font-size: 10px;
        text-align: left;
      }
      .sop-table th {
        background-color: #f1f4f9;
        font-weight: 700;
        text-transform: uppercase;
      }
      .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
      }
    </style>
</head>
<body class="bg-surface font-body text-on-surface flex min-h-screen overflow-hidden">
<!-- Sidebar Navigation -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-[#f7f9fe] dark:bg-slate-900 flex flex-col py-6 z-40 border-r border-outline-variant/15">
<div class="px-6 mb-8">
<h1 class="text-[#001e40] font-black text-xl tracking-tighter">VTS PALEMBANG</h1>
<div class="mt-4 flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center">
<span class="material-symbols-outlined text-white">person</span>
</div>
<div>
<p class="text-sm font-bold text-primary">Valdo</p>
<p class="text-[10px] text-on-surface-variant uppercase tracking-wider">NIP: 882910 | Shift B</p>
</div>
</div>
</div>
<nav class="flex-1 space-y-1">
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span class="font-medium text-sm">Dashboard</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="edit_note">edit_note</span>
<span class="font-medium text-sm">Input Log (A1-A3)</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="warning">warning</span>
<span class="font-medium text-sm">Laporan Khusus (A4-A8)</span>
</a>
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="group">group</span>
<span class="font-medium text-sm">Manajemen User</span>
</a>
<a class="flex items-center gap-3 bg-[#001e40] text-white rounded-lg p-3 mx-2 translate-x-1 transition-transform shadow-lg shadow-primary/20" href="#">
<span class="material-symbols-outlined" data-icon="picture_as_pdf" style="font-variation-settings: 'FILL' 1;">picture_as_pdf</span>
<span class="font-medium text-sm">Arsip &amp; Export PDF</span>
</a>
</nav>
<div class="mt-auto pt-4 border-t border-outline-variant/15">
<a class="flex items-center gap-3 text-slate-600 dark:text-slate-400 p-3 mx-2 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
<span class="font-medium text-sm">Settings</span>
</a>
<a class="flex items-center gap-3 text-error p-3 mx-2 hover:bg-error-container/20 transition-colors" href="#">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
<span class="font-medium text-sm">Logout</span>
</a>
</div>
</aside>
<!-- Main Content Canvas -->
<main class="ml-64 flex-1 flex flex-col h-screen bg-surface-container-low">
<!-- Top Control Bar -->
<header class="bg-[#001e40] text-white px-8 py-4 flex justify-between items-center z-30 shadow-xl">
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-secondary-container">picture_as_pdf</span>
<div>
<h2 class="font-bold tracking-tight text-lg">Pratinjau Ekspor PDF</h2>
<p class="text-[10px] text-on-primary-container uppercase tracking-[0.2em]">Form SOP Log Harian VTS Palembang</p>
</div>
</div>
<div class="flex gap-3">
<button class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded flex items-center gap-2 text-sm transition-all">
<span class="material-symbols-outlined text-sm">close</span>
                    Batal
                </button>
<!-- Validation Tooltip State -->
<div class="group relative">
<button class="bg-slate-500 cursor-not-allowed opacity-50 text-white px-6 py-2 rounded flex items-center gap-2 text-sm font-bold shadow-lg" disabled="">
<span class="material-symbols-outlined text-sm">print</span>
                        Cetak Laporan
                    </button>
<div class="absolute bottom-full right-0 mb-2 w-48 p-2 bg-inverse-surface text-inverse-on-surface text-[10px] rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        Lengkapi tanda tangan dan verifikasi data sebelum mencetak.
                    </div>
</div>
</div>
</header>
<div class="flex flex-1 overflow-hidden">
<!-- Left: Live PDF Preview Area -->
<section class="flex-1 overflow-y-auto p-12 space-y-12 bg-surface-container-highest/30">
<!-- Page 1: A1 Summary (Portrait) -->
<div class="sop-paper mx-auto">
<div class="watermark">FINAL COPY</div>
<div class="relative z-10">
<div class="flex items-center gap-4 border-b-2 border-primary pb-4 mb-8">
<img alt="Kemenhub Logo" class="h-16 w-auto" data-alt="Official logo of the Ministry of Transportation of Indonesia with blue and gold eagle emblem" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDlubWEpfFzBMjaJ4QjqKDCGGYXnbT2ERhHGueVBbFRqUX6gCRBy2nA5MFed2E_zokIT_qoLqpYJ8e5YCDBb4lVN-NTAqa5ernZVcX8QOaOkS2thjQ1KvXby6gi5lQKtD6fXrp1MylL9pY7b23isXC1SPBhHzVQVVYxUdSclw6S9Y3pbuTUJwcYuN08WjdTMT1pFwicDPxLKHEc-_AlSrpIc_qVeG7aMQk3TbLxtKhx2nxTFv7qPO78FrPQIKDJfsX-_qmCTGylAaOL"/>
<div class="flex-1">
<h3 class="text-sm font-extrabold uppercase text-primary">KEMENTERIAN PERHUBUNGAN</h3>
<p class="text-[10px] font-bold">DIREKTORAT JENDERAL PERHUBUNGAN LAUT</p>
<p class="text-[10px]">DISTRIK NAVIGASI KELAS I PALEMBANG - VTS CENTER</p>
</div>
<div class="text-right">
<p class="text-[10px] font-bold border border-primary px-2 py-1">FORM-VTS-A1</p>
</div>
</div>
<div class="text-center mb-8">
<h4 class="font-bold text-lg underline">LAPORAN HARIAN OPERASIONAL (A1)</h4>
<p class="text-xs">Tanggal: 24 Mei 2024 | Shift: B (08:00 - 16:00)</p>
</div>
<div class="space-y-6">
<div class="grid grid-cols-2 gap-4">
<div class="bg-surface-container p-4 rounded">
<p class="text-[10px] font-bold text-primary-container uppercase mb-2">Ringkasan Lalu Lintas</p>
<div class="flex justify-between text-xs py-1 border-b border-outline-variant/30">
<span>Kapal Masuk:</span>
<span class="font-bold text-primary">12 Unit</span>
</div>
<div class="flex justify-between text-xs py-1 border-b border-outline-variant/30">
<span>Kapal Keluar:</span>
<span class="font-bold text-primary">08 Unit</span>
</div>
<div class="flex justify-between text-xs py-1">
<span>Kapal Melintas:</span>
<span class="font-bold text-primary">05 Unit</span>
</div>
</div>
<div class="bg-surface-container p-4 rounded">
<p class="text-[10px] font-bold text-primary-container uppercase mb-2">Status Peralatan</p>
<div class="flex items-center gap-2 text-[10px] mb-1">
<div class="w-2 h-2 rounded-full bg-green-500"></div>
<span>Radar X-Band: Operational</span>
</div>
<div class="flex items-center gap-2 text-[10px] mb-1">
<div class="w-2 h-2 rounded-full bg-green-500"></div>
<span>AIS Base Station: Operational</span>
</div>
<div class="flex items-center gap-2 text-[10px]">
<div class="w-2 h-2 rounded-full bg-green-500"></div>
<span>VHF Radio: Operational</span>
</div>
</div>
</div>
<div>
<p class="text-[10px] font-bold uppercase mb-2">Catatan Kejadian Penting:</p>
<p class="text-[10px] italic leading-relaxed text-slate-700">
                                    "Arus sungai terpantau kuat di koordinat 2°59'S 104°45'E. Dilaporkan kapal tongkang BG. SAMUDRA-01 sempat mengalami keterlambatan olah gerak namun berhasil dikendalikan. Seluruh peralatan komunikasi dalam kondisi prima."
                                </p>
</div>
</div>
<div class="mt-24 grid grid-cols-2 text-center text-[10px]">
<div>
<p class="mb-12">Mengetahui,<br/>Kepala VTS Center</p>
<p class="font-bold underline">MUHAMMAD HASSAN, S.T.</p>
<p>NIP. 19780512 200501 1 002</p>
</div>
<div>
<p class="mb-12">Dibuat Oleh,<br/>VTS Operator On Duty</p>
<p class="font-bold underline italic text-slate-400">[BELUM DITANDA TANGANI]</p>
<p>VALDO - Shift B</p>
</div>
</div>
</div>
</div>
<!-- Page 2: A2 Detailed Traffic (Landscape) -->
<div class="sop-paper-landscape mx-auto">
<div class="watermark">FINAL COPY</div>
<div class="relative z-10">
<div class="flex justify-between items-start mb-6">
<div>
<h3 class="text-xs font-extrabold uppercase text-primary">LAPORAN PERGERAKAN KAPAL (A2)</h3>
<p class="text-[9px]">VTS PALEMBANG - CHANNEL 16/68</p>
</div>
<div class="text-right">
<p class="text-[9px] font-bold">HALAMAN 02/03</p>
</div>
</div>
<table class="w-full sop-table">
<thead>
<tr>
<th>NO</th>
<th>WAKTU (WIB)</th>
<th>NAMA KAPAL / CALLSIGN</th>
<th>POSISI / KOORDINAT</th>
<th>TUJUAN</th>
<th>DRAFT (M)</th>
<th>CARGO</th>
<th>KETERANGAN</th>
</tr>
</thead>
<tbody>
<tr>
<td>01</td>
<td>08:15</td>
<td>MV. OCEAN BLUE / YB992</td>
<td>S 02°15'01" E 104°55'12"</td>
<td>Boom Baru</td>
<td>8.5</td>
<td>General Cargo</td>
<td>Pilot on board at 08:30</td>
</tr>
<tr>
<td>02</td>
<td>09:45</td>
<td>TB. PERKASA-09 / YD445</td>
<td>S 02°20'45" E 104°50'10"</td>
<td>Tg. Api-api</td>
<td>3.2</td>
<td>Towing BG. SELATAN</td>
<td>Safe passage confirmed</td>
</tr>
<tr>
<td>03</td>
<td>11:00</td>
<td>MV. SUMATERA EXPRESS</td>
<td>S 02°25'30" E 104°48'05"</td>
<td>Musi 2</td>
<td>6.0</td>
<td>Fertilizer</td>
<td>Normal</td>
</tr>
<tr>
<td>04</td>
<td>13:20</td>
<td>BG. GOLDEN 02</td>
<td>S 02°30'12" E 104°42'55"</td>
<td>Sungai Lais</td>
<td>2.0</td>
<td>Coal</td>
<td>Anchored at inner port</td>
</tr>
<tr>
<td>05</td>
<td>15:05</td>
<td>KPLP KN. JEMPOL</td>
<td>S 02°10'00" E 105°00'00"</td>
<td>Patroli</td>
<td>2.5</td>
<td>Gov Vessel</td>
<td>Routine Patrol</td>
</tr>
<tr class="bg-surface-container-low italic text-on-surface-variant">
<td class="text-center italic py-4" colspan="8">Data Pergerakan Kapal Lainnya (06-25) Terlampir di Halaman Tambahan</td>
</tr>
</tbody>
</table>
</div>
</div>
</section>
<!-- Right: Configuration Sidebar -->
<aside class="w-80 glass-panel border-l border-outline-variant/15 p-6 flex flex-col gap-8 overflow-y-auto z-20">
<div>
<h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-sm">settings_suggest</span>
                        Konfigurasi PDF
                    </h3>
<div class="space-y-4">
<label class="flex items-start gap-3 p-3 rounded-lg hover:bg-surface-container-high transition-colors cursor-pointer border border-transparent hover:border-outline-variant/30">
<input checked="" class="mt-1 rounded text-primary focus:ring-primary border-outline" type="checkbox"/>
<div>
<p class="text-sm font-bold">Sertakan Laporan Insiden</p>
<p class="text-[10px] text-on-surface-variant">Melampirkan form A4 jika ada kejadian khusus terdata.</p>
</div>
</label>
<label class="flex items-start gap-3 p-3 rounded-lg hover:bg-surface-container-high transition-colors cursor-pointer border border-transparent hover:border-outline-variant/30">
<input checked="" class="mt-1 rounded text-primary focus:ring-primary border-outline" type="checkbox"/>
<div>
<p class="text-sm font-bold">Metadata Watermark</p>
<p class="text-[10px] text-on-surface-variant">Tampilkan label "FINAL COPY" dan Timestamp.</p>
</div>
</label>
<label class="flex items-start gap-3 p-3 rounded-lg hover:bg-surface-container-high transition-colors cursor-pointer border border-transparent hover:border-outline-variant/30">
<input class="mt-1 rounded text-primary focus:ring-primary border-outline" type="checkbox"/>
<div>
<p class="text-sm font-bold">Lampiran Gambar AIS</p>
<p class="text-[10px] text-on-surface-variant">Screenshot radar dan AIS pada jam puncak.</p>
</div>
</label>
</div>
</div>
<div class="border-t border-outline-variant/30 pt-6">
<h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-sm">signature</span>
                        Penandatangan (Verify)
                    </h3>
<div class="space-y-3">
<div class="relative">
<label class="text-[10px] font-bold text-on-surface-variant uppercase mb-1 block">Verifikator / Supervisor</label>
<select class="w-full bg-surface-container-high border-none rounded-lg text-sm font-medium py-3 focus:ring-2 focus:ring-primary">
<option disabled="" selected="" value="">Pilih Pejabat Berwenang</option>
<option value="ria">Ria Irawan, S.Kom (Supervisor)</option>
<option value="merry">Merry Anitasari, M.T (Manager)</option>
<option value="hassan">Muhammad Hassan, S.T (Kepala)</option>
</select>
</div>
<div class="p-4 bg-error-container/10 border-l-4 border-error rounded">
<div class="flex items-center gap-2 text-error mb-1">
<span class="material-symbols-outlined text-sm">warning</span>
<p class="text-[10px] font-bold uppercase">Status Tanda Tangan</p>
</div>
<p class="text-[11px] leading-tight">User <strong>Valdo</strong> belum membubuhkan tanda tangan digital pada sesi ini.</p>
</div>
<button class="w-full py-3 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-container transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-sm">draw</span>
                            Tanda Tangani Sekarang
                        </button>
</div>
</div>
<div class="mt-auto">
<div class="bg-[#001e40] p-4 rounded-xl text-white">
<div class="flex items-center gap-3 mb-3">
<span class="material-symbols-outlined text-secondary-container">verified</span>
<span class="text-xs font-bold uppercase">Integritas Data</span>
</div>
<p class="text-[10px] opacity-70 mb-4">Laporan ini memuat 45 baris data pergerakan kapal yang telah divalidasi oleh sistem VTS.</p>
<div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
<div class="bg-secondary-container h-full w-[85%]"></div>
</div>
<div class="flex justify-between mt-2 text-[8px] uppercase tracking-widest font-bold">
<span>Validasi Selesai</span>
<span>85%</span>
</div>
</div>
</div>
</aside>
</div>
<!-- Footer Info -->
<footer class="w-full px-8 py-3 bg-white border-t border-outline-variant/15 flex justify-between items-center text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">
<div>
                © 2024 KEMENHUB RI - VTS PALEMBANG. ALL RIGHTS RESERVED.
            </div>
<div class="flex gap-6">
<span>VHF CHANNEL: 16 / 68</span>
<span class="text-primary">E-ARCHIVE REFERENCE: #PDF-2024-05-24-SH-B</span>
</div>
</footer>
</main>
</body></html>

<!-- Log A2 Traffic (Modernism) -->
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Registrasi Operator - VTS PALEMBANG</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "surface-container-low": "#f1f4f9",
              "surface-container-high": "#e5e8ed",
              "outline": "#737780",
              "on-surface-variant": "#43474f",
              "primary-fixed-dim": "#a7c8ff",
              "on-surface": "#181c20",
              "on-secondary": "#ffffff",
              "surface-tint": "#3a5f94",
              "error": "#ba1a1a",
              "background": "#f7f9fe",
              "on-primary-fixed": "#001b3c",
              "on-background": "#181c20",
              "surface-dim": "#d7dadf",
              "primary": "#001e40",
              "inverse-on-surface": "#eef1f6",
              "tertiary-container": "#d0a600",
              "on-secondary-container": "#616363",
              "error-container": "#ffdad6",
              "outline-variant": "#c3c6d1",
              "on-primary": "#ffffff",
              "on-error-container": "#93000a",
              "secondary-fixed-dim": "#c6c6c7",
              "on-secondary-fixed-variant": "#454747",
              "secondary-fixed": "#e2e2e2",
              "on-tertiary-fixed-variant": "#584400",
              "tertiary-fixed-dim": "#f1c100",
              "surface-container-lowest": "#ffffff",
              "tertiary-fixed": "#ffe08b",
              "on-tertiary": "#ffffff",
              "surface-container": "#ebeef3",
              "on-primary-container": "#799dd6",
              "surface": "#f7f9fe",
              "on-error": "#ffffff",
              "surface-bright": "#f7f9fe",
              "inverse-surface": "#2d3135",
              "on-secondary-fixed": "#1a1c1c",
              "surface-variant": "#dfe3e8",
              "on-tertiary-container": "#4f3d00",
              "surface-container-highest": "#dfe3e8",
              "primary-fixed": "#d5e3ff",
              "tertiary": "#745b00",
              "on-primary-fixed-variant": "#1f477b",
              "inverse-primary": "#a7c8ff",
              "secondary-container": "#dfe0e0",
              "secondary": "#5d5f5f",
              "primary-container": "#003366",
              "on-tertiary-fixed": "#241a00"
            },
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .vts-glass {
            background: rgba(247, 251, 254, 0.85);
            backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="bg-background font-body text-on-background selection:bg-tertiary-fixed-dim selection:text-on-tertiary-fixed">
<nav class="fixed top-0 w-full z-50 bg-[#f7f9fe]/80 dark:bg-[#181c20]/80 backdrop-blur-xl flex justify-between items-center px-8 h-20 w-full mx-auto shadow-sm shadow-[#181c20]/5">
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-primary-container rounded flex items-center justify-center">
<span class="material-symbols-outlined text-tertiary-fixed" style="font-variation-settings: 'FILL' 1;">sailing</span>
</div>
<span class="text-xl font-bold tracking-tighter text-[#001e40] dark:text-[#f1f4f9]">VTS PALEMBANG</span>
</div>
<div class="hidden md:flex gap-8 items-center">
<a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#001e40] dark:hover:text-white transition-colors" href="#">Beranda</a>
<a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#001e40] dark:hover:text-white transition-colors" href="#">Layanan</a>
<a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#001e40] dark:hover:text-white transition-colors" href="#">Informasi</a>
<a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#001e40] dark:hover:text-white transition-colors" href="#">Kontak</a>
<a class="bg-primary px-6 py-2 rounded-lg text-white font-semibold hover:bg-primary-container transition-all active:scale-95" href="login.php">Masuk Sistem</a>
</div>
</nav>
<main class="min-h-screen pt-20 flex items-center justify-center px-4 relative overflow-hidden">
<div class="absolute inset-0 z-0 opacity-10 pointer-events-none">
<div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary rounded-full blur-[120px]"></div>
<div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-tertiary rounded-full blur-[120px]"></div>
</div>
<div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 bg-surface-container-lowest rounded-xl shadow-2xl overflow-hidden relative z-10">
<div class="lg:col-span-5 bg-primary relative p-12 flex flex-col justify-between text-white overflow-hidden">
<div class="absolute inset-0 z-0 opacity-20">
<img class="w-full h-full object-cover grayscale brightness-50" data-alt="abstract architectural lines of a modern maritime control tower with deep blue steel and glass textures under twilight lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBSiQ1sPR3h8IgMio1dBXd5EGi1y8a6FvsYnB6u1B6oAMNmPvB-SHMU9gXLRbvOIguj9KlWP45uAOR-vRg2rZ4KmMGVRQ-M2CiqwyIi97gAtWff-vtbT3pc7jyTgRoxpuUhSAHhjg40h4d0yLNTqjCboOtMB-wWKYGQ3fdNB6cLDYvVYSYbhTtmUXnDCbb97JIDqaEsaW7K2RjspCYpPPHzeVES2pI9gIaeIigQi4hGjt8-DVXeFDycVu2jMqLDFp2fqYv4z4uK-pzI"/>
</div>
<div class="relative z-10">
<h2 class="text-3xl font-extrabold tracking-tight mb-4">Sovereign Navigation Authority</h2>
<p class="text-primary-fixed leading-relaxed opacity-80">Selamat datang di portal pendaftaran personil operasional VTS Palembang. Pastikan data yang dimasukkan sesuai dengan dokumen kepegawaian resmi.</p>
</div>
<div class="relative z-10 mt-12">
<div class="flex items-center gap-4 mb-6">
<div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-md">
<span class="material-symbols-outlined text-tertiary-fixed">verified_user</span>
</div>
<div>
<p class="text-sm font-bold uppercase tracking-wider text-tertiary-fixed">Akses Terenkripsi</p>
<p class="text-xs text-white/60">Protokol Keamanan Maritim v2.4</p>
</div>
</div>
</div>
</div>
<div class="lg:col-span-7 p-8 md:p-12 bg-surface-container-lowest">
<div class="mb-10">
<h1 class="text-2xl font-bold text-primary mb-2">Registrasi Akun Baru</h1>
<p class="text-on-surface-variant">Lengkapi formulir di bawah untuk bergabung dalam sistem.</p>
</div>
<form action="#" class="space-y-6" method="POST">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-1.5">
<label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant flex items-center gap-2" for="nip">
<span class="material-symbols-outlined text-sm">badge</span> NIP / ID Pegawai
                            </label>
<input class="w-full bg-surface-container-highest border-0 border-b-2 border-outline-variant focus:ring-0 focus:border-tertiary transition-all px-4 py-3 placeholder:text-outline/50" id="nip" name="nip" placeholder="19XXXXXXXXXXXXXX" type="text"/>
</div>
<div class="space-y-1.5">
<label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant flex items-center gap-2" for="nama">
<span class="material-symbols-outlined text-sm">person</span> Nama Lengkap
                            </label>
<input class="w-full bg-surface-container-highest border-0 border-b-2 border-outline-variant focus:ring-0 focus:border-tertiary transition-all px-4 py-3 placeholder:text-outline/50" id="nama" name="nama" placeholder="Masukkan nama sesuai SK" type="text"/>
</div>
</div>
<div class="space-y-1.5">
<label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant flex items-center gap-2" for="password">
<span class="material-symbols-outlined text-sm">lock</span> Password
                        </label>
<div class="relative">
<input class="w-full bg-surface-container-highest border-0 border-b-2 border-outline-variant focus:ring-0 focus:border-tertiary transition-all px-4 py-3 placeholder:text-outline/50" id="password" name="password" placeholder="••••••••••••" type="password"/>
<button class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary" type="button">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-1.5">
<label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant flex items-center gap-2" for="tim">
<span class="material-symbols-outlined text-sm">groups</span> Penempatan Tim
                            </label>
<select class="w-full bg-surface-container-highest border-0 border-b-2 border-outline-variant focus:ring-0 focus:border-tertiary transition-all px-4 py-3" id="tim" name="tim">
<option disabled="" selected="" value="">Pilih Tim Operasional</option>
<option value="A">Tim A (Alpha)</option>
<option value="B">Tim B (Bravo)</option>
<option value="C">Tim C (Charlie)</option>
<option value="D">Tim D (Delta)</option>
<option value="E">Tim E (Echo)</option>
</select>
</div>
<div class="space-y-1.5">
<label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant flex items-center gap-2" for="peran">
<span class="material-symbols-outlined text-sm">manage_accounts</span> Peran Sistem
                            </label>
<select class="w-full bg-surface-container-highest border-0 border-b-2 border-outline-variant focus:ring-0 focus:border-tertiary transition-all px-4 py-3" id="peran" name="peran">
<option disabled="" selected="" value="">Pilih Jabatan</option>
<option value="operator">VTS Operator</option>
<option value="supervisor">VTS Supervisor</option>
<option value="manager">Operational Manager</option>
</select>
</div>
</div>
<div class="pt-4 flex flex-col gap-4">
<button class="w-full bg-tertiary-container text-on-tertiary-container py-4 rounded font-bold uppercase tracking-widest hover:brightness-105 active:scale-[0.98] transition-all shadow-lg shadow-tertiary/10" type="submit">
                            Ajukan Pendaftaran
                        </button>
<p class="text-center text-sm text-on-surface-variant">
                            Sudah memiliki akun? 
                            <a class="text-primary font-bold hover:underline decoration-tertiary decoration-2 underline-offset-4" href="login.php">Masuk ke Sistem</a>
</p>
</div>
</form>
</div>
</div>
</main>
<footer class="bg-[#f1f4f9] dark:bg-[#001e40] w-full py-12 px-8 border-t border-[#c3c6d1]/20">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
<div class="space-y-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-tertiary" style="font-variation-settings: 'FILL' 1;">shield</span>
<span class="font-bold text-[#003366] dark:text-[#d0a600]">VTS PALEMBANG</span>
</div>
<p class="text-sm font-['Inter'] leading-relaxed text-[#737780] dark:text-[#c3c6d1]">Sistem Monitoring Lalu Lintas Kapal Terpadu wilayah perairan Palembang dan sekitarnya.</p>
</div>
<div class="space-y-4">
<h4 class="text-primary font-bold">Navigasi</h4>
<ul class="space-y-2">
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] transition-colors" href="#">Beranda</a></li>
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] transition-colors" href="#">Peta Navigasi</a></li>
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] transition-colors" href="#">Laporan Harian</a></li>
</ul>
</div>
<div class="space-y-4">
<h4 class="text-primary font-bold">Legalitas</h4>
<ul class="space-y-2">
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] transition-colors" href="#">Kebijakan Privasi</a></li>
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] transition-colors" href="#">Syarat &amp; Ketentuan</a></li>
<li><a class="text-[#737780] dark:text-[#c3c6d1] hover:text-[#003366] transition-colors" href="#">Peta Situs</a></li>
</ul>
</div>
<div class="space-y-4">
<h4 class="text-primary font-bold">Hubungi Kami</h4>
<p class="text-sm text-[#737780] dark:text-[#c3c6d1]">Jl. Boom Baru, Palembang<br/>Sumatera Selatan, Indonesia</p>
<div class="flex gap-4">
<span class="material-symbols-outlined text-primary-container">mail</span>
<span class="material-symbols-outlined text-primary-container">call</span>
</div>
</div>
</div>
<div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-outline-variant/30 text-center">
<p class="text-sm font-['Inter'] leading-relaxed text-[#737780] dark:text-[#c3c6d1]">© 2024 Direktorat Jenderal Perhubungan Laut - VTS Palembang. Hak Cipta Dilindungi Undang-Undang.</p>
</div>
</footer>
</body></html>

<!-- Ekspor PDF (Modernism) -->
<!DOCTYPE html>

<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
            fontFamily: {
              "headline": ["Inter"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .vts-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .hero-gradient {
            background: linear-gradient(135deg, rgba(0, 30, 64, 0.95) 0%, rgba(0, 51, 102, 0.8) 100%);
        }
    </style>
</head>
<body class="bg-background font-body text-on-surface antialiased">
<!-- TopNavBar -->
<nav class="fixed top-0 z-50 w-full bg-[#001e40] bg-opacity-90 backdrop-blur-md shadow-xl flex justify-between items-center px-8 py-4 font-['Inter'] antialiased tracking-tight">
<div class="flex items-center gap-4">
<img alt="Logo Kemenhub" class="h-10 w-10 object-contain" data-alt="official gold crest of the Indonesian Ministry of Transportation centered on a professional navy blue background for government portal" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYl0mvla27fe6X5L0fQYF1dUTYMEoQI9i2fcykkKyFmzISjwbxTJaPZL9oEd-3c2Hq4HtiIpqIvpaY6Zx6yaOgTAP5Uo1VytXZB_woovVoJ93Q0va5gqcyADlXPPyTC-jlfbGjreEj6pPe5i5wsHqK4p4PHFyF4PXXHmSK0CsY7jwfXMjT_Qv3Sf2ddTWuFdurOvVM1jeP__du0_5lOCbFdWJwNgBMAdDh2oIvVDKu91wSSOMDf_0miPQywxtoaF7GorBHpojQY0Hl"/>
<span class="text-xl font-bold tracking-tighter text-white">VTS PALEMBANG</span>
</div>
<div class="hidden md:flex items-center gap-8">
<a class="text-[#fecb00] border-b-2 border-[#fecb00] pb-1" href="#">Beranda</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Layanan</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Informasi</a>
<a class="text-slate-300 hover:text-white transition-colors" href="#">Kontak</a>
</div>
<div class="flex items-center gap-4">
<button class="material-symbols-outlined text-slate-300 hover:bg-white/10 p-2 rounded transition-all">notifications</button>
<button class="bg-[#fecb00] text-primary px-6 py-2 rounded font-bold hover:bg-white transition-all transform active:scale-95">Masuk Sistem</button>
</div>
</nav>
<!-- Hero Section -->
<section class="relative min-h-screen flex items-center pt-20">
<div class="absolute inset-0 z-0">
<div class="absolute inset-0 hero-gradient z-10"></div>
<img alt="VTS Background" class="w-full h-full object-cover" data-alt="cinematic aerial view of a vessel traffic service tower overlooking a busy industrial river port with cargo ships at sunset blue hour lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB2lIz7ZRJ0O8OExBCJ4RCEXLT9X8IcjW3rWE_dJjcz_97aoEaZHM5Aids5k2s4_Jhv8YbUMCk93LojAUXbyOP7_B__w8dttFd1yP3EKDLRW21AonRm4a2SZK-mudbG_eK8MO6BhVVzo3UuKZhtav1zz0EgZ15JOBdQe_CVB4mZNOgYAB3Yapip8sw7dSVKRXLYxxFBujwaAi3lx2x9v14Jd7DzFrhhXmoqbNEsLi-VSTxscf4rdrC2qK4p6e1bZVWQQOPZtb9NT4EX"/>
</div>
<div class="container mx-auto px-8 relative z-20">
<div class="max-w-4xl">
<p class="text-[#fecb00] font-bold tracking-[0.2em] mb-4 text-sm uppercase">Kementerian Perhubungan Republik Indonesia</p>
<h1 class="text-white text-5xl md:text-7xl font-extrabold tracking-tight leading-tight mb-6">
                    VESSEL TRAFFIC SERVICE (VTS) <span class="text-[#fecb00]">PALEMBANG</span>
</h1>
<p class="text-slate-300 text-xl font-medium mb-10 max-w-2xl border-l-4 border-[#fecb00] pl-6 italic">
                    DISTRIK NAVIGASI TYPE B PALEMBANG - Menjamin keselamatan pelayaran dan efisiensi logistik di perairan Sumatera Selatan.
                </p>
<div class="flex flex-col md:flex-row gap-4">
<button class="bg-[#fecb00] text-primary px-8 py-4 rounded font-black text-lg hover:bg-white transition-all shadow-2xl flex items-center gap-3">
                        MASUK SISTEM OPS-LOG
                        <span class="material-symbols-outlined">login</span>
</button>
<button class="border border-white/30 text-white backdrop-blur-md px-8 py-4 rounded font-bold text-lg hover:bg-white/10 transition-all">
                        E-Navigation Portal
                    </button>
</div>
</div>
</div>
<!-- Live Traffic Stats -->
<div class="absolute bottom-0 right-0 z-30 p-8 hidden lg:block">
<div class="bg-primary-container/80 backdrop-blur-xl border-l-4 border-[#fecb00] p-6 text-white min-w-[300px]">
<div class="flex items-center gap-2 mb-4 opacity-70">
<span class="material-symbols-outlined text-xs">sensors</span>
<span class="text-xs font-bold tracking-widest uppercase">Live Traffic Stats</span>
</div>
<div class="flex justify-between items-end gap-12">
<div>
<p class="text-4xl font-black text-[#fecb00]">16</p>
<p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Vessels Inbound</p>
</div>
<div class="h-10 w-px bg-white/20"></div>
<div>
<p class="text-4xl font-black text-white">09</p>
<p class="text-[10px] font-bold tracking-widest uppercase text-slate-400">Vessels Outbound</p>
</div>
</div>
</div>
</div>
</section>
<!-- Sejarah & Visi -->
<section class="py-32 bg-surface">
<div class="container mx-auto px-8">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
<div class="relative">
<div class="absolute -top-10 -left-10 w-40 h-40 bg-secondary-container/20 rounded-full blur-3xl"></div>
<div class="relative z-10 space-y-8">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-primary-container text-[#fecb00] text-[10px] font-bold uppercase tracking-widest">
<span class="material-symbols-outlined text-sm">history_edu</span>
                            Sejarah &amp; Visi
                        </div>
<h2 class="text-4xl font-bold text-primary leading-tight">Evolusi Navigasi di Sungai Musi: Dari Tradisional ke Digital.</h2>
<p class="text-on-surface-variant text-lg leading-relaxed">
                            VTS Palembang merupakan garda terdepan dalam pengawasan lalu lintas kapal di perairan strategis Sungai Musi. Didirikan untuk mendukung pertumbuhan ekonomi Sumatera Selatan, kami terus bertransformasi mengadopsi teknologi radar dan AIS terkini guna menciptakan ekosistem maritim yang aman dan terintegrasi secara global.
                        </p>
<div class="grid grid-cols-2 gap-8 pt-4">
<div>
<h4 class="text-primary font-bold mb-2">Visi Kami</h4>
<p class="text-sm text-on-surface-variant italic">"Menjadi pusat pelayanan keselamatan pelayaran berstandar internasional yang handal dan transparan."</p>
</div>
<div>
<h4 class="text-primary font-bold mb-2">Misi Utama</h4>
<p class="text-sm text-on-surface-variant">Modernisasi infrastruktur e-Navigasi dan peningkatan kualitas SDM operator VTS.</p>
</div>
</div>
</div>
</div>
<div class="relative group">
<div class="absolute inset-0 border-2 border-primary/10 translate-x-4 translate-y-4 rounded-xl"></div>
<img alt="Command Center" class="relative z-10 w-full h-[500px] object-cover rounded-xl shadow-2xl transition-transform group-hover:scale-[1.02]" data-alt="modern minimalist maritime command center interior large screens displaying radar and maps high tech atmosphere professional vts environment" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD7gTwmpteeAYzL7p7ejREADBhVU3QVuaHET6H3WBBAI3Ccu1HKoD5rMc0hs8fCr4c3UUA7ZqCjdKezXqcnnNqddyBfP8QSabatjLMZV-vUMmDbgYcRkWr0zTnfhriorpn6eiPnAWx31D-J3D3HXjyluT-eNvWUaYxhz0NsiQslgI6YxRn_ndp2HStOrpx97vSEsxRQbB6W7_GvgVr5Y-xdY1LGgNP8mvLFaCnihcwjYCso6QgHVre9ytr240b3g96I4TSACgVbS1BD"/>
</div>
</div>
</div>
</section>
<!-- Fungsi VTS (Bento Grid) -->
<section class="py-32 bg-surface-container-low relative overflow-hidden">
<!-- Subtle Maritime Watermark -->
<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-[0.03] pointer-events-none">
<span class="material-symbols-outlined text-[600px]">explore</span>
</div>
<div class="container mx-auto px-8 relative z-10">
<div class="text-center mb-20">
<h2 class="text-primary text-4xl font-black tracking-tight mb-4 uppercase">Fungsi Strategis VTS</h2>
<div class="w-20 h-1.5 bg-[#fecb00] mx-auto"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 md:grid-rows-2 gap-4 h-auto md:h-[600px]">
<!-- Card 1: Large Feature -->
<div class="md:col-span-2 md:row-span-2 bg-[#001e40] text-white p-10 rounded-xl flex flex-col justify-between group relative overflow-hidden">
<div class="absolute top-0 right-0 p-12 opacity-10 group-hover:opacity-20 transition-opacity">
<span class="material-symbols-outlined text-[200px]">shield</span>
</div>
<div>
<span class="material-symbols-outlined text-[#fecb00] text-4xl mb-6">anchor</span>
<h3 class="text-3xl font-bold mb-4">1. Keselamatan Berlayar</h3>
<p class="text-slate-300 text-lg max-w-sm">Pengawasan real-time 24/7 untuk mencegah kecelakaan kapal, tabrakan, dan kandas di alur pelayaran sempit.</p>
</div>
<button class="self-start mt-8 flex items-center gap-2 text-[#fecb00] font-bold group-hover:gap-4 transition-all">
                        Pelajari Protokol <span class="material-symbols-outlined">arrow_forward</span>
</button>
</div>
<!-- Card 2: Interactive -->
<div class="md:col-span-2 bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 flex items-center gap-6 group hover:shadow-xl transition-all">
<div class="bg-primary/5 p-4 rounded-lg group-hover:bg-[#fecb00] transition-colors">
<span class="material-symbols-outlined text-primary text-4xl">speed</span>
</div>
<div>
<h3 class="text-xl font-bold text-primary mb-1">2. Efisiensi Lalu Lintas</h3>
<p class="text-sm text-on-surface-variant">Optimalisasi antrean kapal masuk dan keluar pelabuhan untuk menekan biaya logistik.</p>
</div>
</div>
<!-- Card 3: Interactive -->
<div class="md:col-span-1 bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 flex flex-col justify-between group hover:shadow-xl transition-all">
<div class="bg-primary/5 w-fit p-4 rounded-lg group-hover:bg-[#fecb00] transition-colors">
<span class="material-symbols-outlined text-primary text-3xl">eco</span>
</div>
<div>
<h3 class="text-lg font-bold text-primary mb-1 leading-tight">3. Perlindungan Lingkungan</h3>
<p class="text-xs text-on-surface-variant">Deteksi dini tumpahan minyak dan perlindungan ekosistem perairan Musi.</p>
</div>
</div>
<!-- Card 4: Interactive -->
<div class="md:col-span-1 bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/30 flex flex-col justify-between group hover:shadow-xl transition-all">
<div class="bg-primary/5 w-fit p-4 rounded-lg group-hover:bg-[#fecb00] transition-colors">
<span class="material-symbols-outlined text-primary text-3xl">settings_input_antenna</span>
</div>
<div>
<h3 class="text-lg font-bold text-primary mb-1 leading-tight">4. Bantuan Navigasi</h3>
<p class="text-xs text-on-surface-variant">Pemberian informasi cuaca, pasang surut, dan kondisi alur kepada nakhoda.</p>
</div>
</div>
</div>
</div>
</section>
<!-- Call to Action -->
<section class="py-20 bg-primary overflow-hidden relative">
<div class="container mx-auto px-8 flex flex-col md:flex-row items-center justify-between gap-12 relative z-10">
<div>
<h2 class="text-white text-3xl font-bold mb-4">Siap Melaporkan Kedatangan Kapal?</h2>
<p class="text-slate-400">Gunakan portal OPS-LOG untuk kemudahan administrasi pelayaran Anda.</p>
</div>
<button class="bg-[#fecb00] text-primary px-10 py-4 rounded font-black tracking-tighter hover:scale-105 transition-transform">
                AKSES DASHBOARD OPERASIONAL
            </button>
</div>
<!-- Abstract Industrial Background Shape -->
<div class="absolute -right-20 -bottom-20 w-80 h-80 border-[40px] border-white/5 rounded-full"></div>
</section>
<!-- Footer -->
<footer class="bg-[#001e40] text-white w-full border-t border-white/10 font-['Inter'] text-xs uppercase tracking-widest">
<div class="w-full px-8 py-16 flex flex-col items-center gap-8 text-center">
<div class="flex items-center gap-4 mb-4">
<img alt="Kemenhub Logo" class="h-12 w-12 object-contain grayscale brightness-200" data-alt="monochrome white and gold ministry of transportation logo for footer use" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAwsXjK27GSqHVgbv4gL-8JG_wxTBQJ_mPkqTjwH3cdaskWs-DPwiHKsAYSdxP72lMmGktG4IlWwT3adJ7XlnndA-aGvkrM57v_NpZxVRZoUKvL6KSawB9ITUjzr8oEc27_HrGe2Yjibef4Qms9IMp0yJz7KcvHBlRVjj5WEPjLRXESvqspnvL2j2qNUzmbeEksZrC5DbZR_EoEpvpy49CKiFapG5599W5ivbWYmkkNCK9CkFhZMbHLrv-PI8J6PmsUvgVTSFvWmELR"/>
<div class="text-left">
<p class="text-lg font-bold tracking-tighter text-white uppercase">VTS Palembang</p>
<p class="text-[10px] text-slate-400">Distrik Navigasi Type B Palembang</p>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-12 w-full max-w-5xl border-y border-white/5 py-12">
<div class="flex flex-col gap-3">
<p class="text-slate-400">Address</p>
<p class="text-white font-medium">Jl. Mayor Memet Sastrawirya No.1, Palembang, Sumatera Selatan</p>
</div>
<div class="flex flex-col gap-3">
<p class="text-slate-400">Contact</p>
<p class="text-white font-medium">Phone: +62 711 712234<br/>Email: vts.palembang@dephub.go.id</p>
</div>
<div class="flex flex-col gap-3">
<p class="text-slate-400">VHF Radio</p>
<p class="text-[#fecb00] font-black">Channel: 16 / 68<br/>MMSI: 005250011</p>
</div>
<div class="flex flex-col gap-3">
<p class="text-slate-400">Quick Links</p>
<div class="flex flex-col gap-2">
<a class="hover:text-[#fecb00] transition-colors" href="#">Ship Reporting</a>
<a class="hover:text-[#fecb00] transition-colors" href="#">Tide Tables</a>
<a class="hover:text-[#fecb00] transition-colors" href="#">Notices to Mariners</a>
</div>
</div>
</div>
<p class="text-slate-400 pt-8">© 2024 Kemenhub RI - VTS Palembang. All Rights Reserved.</p>
</div>
</footer>
</body></html>