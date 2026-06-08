<?php require_once __DIR__ . '/../../config/app.php'; ?>
<!DOCTYPE html>
<html class="light scroll-smooth" lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Efisiensi Lalu Lintas – VTS Palembang</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: '#001e40', 'on-primary': '#ffffff',
                    'primary-container': '#d0e4ff', 'on-primary-container': '#001d35',
                    tertiary: '#6b5778', 'tertiary-container': '#f3daff', 'on-tertiary-container': '#251431',
                    background: '#f7f9fe', 'on-background': '#181c20',
                    surface: '#f7f9fe', 'on-surface': '#181c20',
                    'surface-container': '#eaecf1', 'surface-container-low': '#f1f4f9',
                    'on-surface-variant': '#42474e', outline: '#72777f', 'outline-variant': '#c2c7cf',
                },
                fontFamily: { body: ['Inter', 'sans-serif'] },
            }
        }
    };
</script>
</head>
<body class="bg-background text-on-surface font-body">

<!-- Top Nav -->
<nav class="fixed top-0 w-full z-50 bg-[#f7f9fe]/90 backdrop-blur-xl shadow-sm shadow-black/5 font-['Inter']">
    <div class="flex justify-between items-center px-8 h-16 max-w-7xl mx-auto">
        <a href="<?= BASE_URL ?>index.php" class="flex items-center gap-3 text-primary hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined">arrow_back</span>
            <span class="font-bold tracking-tight">VTS PALEMBANG</span>
        </a>
        <a href="<?= BASE_URL ?>login.php">
            <button class="bg-primary text-white px-5 py-2 rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">Masuk Sistem</button>
        </a>
    </div>
</nav>

<!-- Hero -->
<section class="pt-28 pb-20 px-8 bg-[#1a3a5c] text-white">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-4 text-blue-200 text-sm font-semibold uppercase tracking-widest">
            <a href="<?= BASE_URL ?>index.php#layanan" class="hover:underline">Layanan</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span>Efisiensi Lalu Lintas</span>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-4xl filled">speed</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">Efisiensi Lalu Lintas</h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-3xl leading-relaxed">
            Optimalisasi pergerakan kapal untuk meminimalkan waktu tunggu di perairan Sungai Musi dan meningkatkan produktivitas Pelabuhan Palembang.
        </p>
    </div>
</section>

<!-- Statistik -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-10 text-center">Kinerja Lalu Lintas 2024</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a3a5c] mb-2">50+</div>
                <div class="text-sm text-on-surface-variant font-medium">Kapal Dilayani / Hari</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a3a5c] mb-2">-42%</div>
                <div class="text-sm text-on-surface-variant font-medium">Reduksi Waktu Tunggu</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a3a5c] mb-2">8 kn</div>
                <div class="text-sm text-on-surface-variant font-medium">Kecepatan Optimal Alur</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a3a5c] mb-2">97,2%</div>
                <div class="text-sm text-on-surface-variant font-medium">Kepatuhan Jadwal</div>
            </div>
        </div>
    </div>
</section>

<!-- Cara Kerja -->
<section class="py-16 px-8 bg-surface">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-primary mb-4 text-center">Manajemen Arus Kapal</h2>
        <p class="text-center text-on-surface-variant mb-12">Strategi pengaturan lalu lintas berbasis data real-time VTMIS.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a3a5c] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">schedule</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">1. Perencanaan Jadwal Masuk</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">VTS mengkoordinasi jadwal masuk kapal bersama Pandu dan POSAL untuk menghindari antrian dan kemacetan di alur sempit.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a3a5c] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">directions_boat</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">2. Separasi Arus Lalu Lintas</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Kapal masuk dan keluar diatur pada jalur berbeda dengan sistem one-way traffic di segmen kritis Sungai Musi.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a3a5c] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">water</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">3. Manajemen Pasang Surut</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Data pasang surut real-time Stasiun Musi dianalisis untuk menentukan window perjalanan optimal bagi kapal LOA &gt;100m.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a3a5c] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">analytics</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">4. Laporan Kinerja Harian</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Rekap jumlah kapal, total waktu tunggu, dan insiden lalu lintas dilaporkan ke Kepala Syahbandar setiap akhir shift.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Jadwal Kapal (Mockup) -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-2">Jadwal Pergerakan Kapal Hari Ini</h2>
        <p class="text-on-surface-variant text-sm mb-8">Data ilustratif – diperbarui setiap 30 menit.</p>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-outline-variant/20">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#1a3a5c] text-white">
                        <th class="px-5 py-3 text-left font-semibold">ETA/ETD</th>
                        <th class="px-5 py-3 text-left font-semibold">Nama Kapal</th>
                        <th class="px-5 py-3 text-left font-semibold">Type</th>
                        <th class="px-5 py-3 text-left font-semibold">LOA / Draft</th>
                        <th class="px-5 py-3 text-left font-semibold">Arah</th>
                        <th class="px-5 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 font-mono text-on-surface-variant">06:30</td>
                        <td class="px-5 py-3 font-medium">MV Ciremai</td>
                        <td class="px-5 py-3">General Cargo</td>
                        <td class="px-5 py-3 text-on-surface-variant">145m / 6.8m</td>
                        <td class="px-5 py-3">
                            <span class="flex items-center gap-1 text-green-700"><span class="material-symbols-outlined text-sm">south</span> Masuk</span>
                        </td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Dalam Alur</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 font-mono text-on-surface-variant">07:15</td>
                        <td class="px-5 py-3 font-medium">MT Krakatau Gas</td>
                        <td class="px-5 py-3">Tanker LPG</td>
                        <td class="px-5 py-3 text-on-surface-variant">180m / 8.2m</td>
                        <td class="px-5 py-3">
                            <span class="flex items-center gap-1 text-red-700"><span class="material-symbols-outlined text-sm">north</span> Keluar</span>
                        </td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Berlabuh Jangkar</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 font-mono text-on-surface-variant">09:00</td>
                        <td class="px-5 py-3 font-medium">KM Bukit Siguntang</td>
                        <td class="px-5 py-3">RoRo Penumpang</td>
                        <td class="px-5 py-3 text-on-surface-variant">123m / 4.9m</td>
                        <td class="px-5 py-3">
                            <span class="flex items-center gap-1 text-green-700"><span class="material-symbols-outlined text-sm">south</span> Masuk</span>
                        </td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Tiba di Dermaga</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 font-mono text-on-surface-variant">11:30</td>
                        <td class="px-5 py-3 font-medium">TB Sumber Mas + TK</td>
                        <td class="px-5 py-3">Tug & Barge</td>
                        <td class="px-5 py-3 text-on-surface-variant">82m / 3.5m</td>
                        <td class="px-5 py-3">
                            <span class="flex items-center gap-1 text-red-700"><span class="material-symbols-outlined text-sm">north</span> Keluar</span>
                        </td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">Terjadwal</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 px-8 bg-[#1a3a5c] text-white text-center">
    <div class="max-w-2xl mx-auto">
        <span class="material-symbols-outlined text-5xl mb-4 block opacity-80 filled">speed</span>
        <h2 class="text-3xl font-bold mb-4">Koordinasi Masuk/Keluar Pelabuhan</h2>
        <p class="text-blue-100 mb-8">Hubungi VTS Palembang untuk informasi window perjalanan dan kondisi alur saat ini.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>index.php#kontak">
                <button class="bg-white text-[#1a3a5c] font-bold px-8 py-3 rounded-md hover:bg-blue-50 transition-colors">Hubungi Kami</button>
            </a>
            <a href="<?= BASE_URL ?>index.php#layanan">
                <button class="bg-white/10 border border-white/30 text-white font-bold px-8 py-3 rounded-md hover:bg-white/20 transition-colors">Layanan Lainnya</button>
            </a>
        </div>
    </div>
</section>

<footer class="bg-surface-container-low border-t border-outline-variant/30 py-8 px-8 text-center text-sm text-on-surface-variant">
    © 2024 Distrik Navigasi Type B Palembang – VTS Palembang. Hak Cipta Dilindungi.
</footer>

</body>
</html>
