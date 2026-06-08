<?php require_once __DIR__ . '/../../config/app.php'; ?>
<!DOCTYPE html>
<html class="light scroll-smooth" lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Bantuan Navigasi – VTS Palembang</title>
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
<section class="pt-28 pb-20 px-8 bg-[#2a1a4a] text-white">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-4 text-purple-200 text-sm font-semibold uppercase tracking-widest">
            <a href="<?= BASE_URL ?>index.php#layanan" class="hover:underline">Layanan</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span>Bantuan Navigasi</span>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-4xl filled">near_me</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">Bantuan Navigasi</h1>
        <p class="text-lg md:text-xl text-purple-100 max-w-3xl leading-relaxed">
            Pemberian informasi teknis navigasi dan kondisi cuaca kepada kapal yang mengalami kesulitan, lost COM, atau dalam situasi kedaruratan di perairan wilayah kerja VTS Palembang.
        </p>
    </div>
</section>

<!-- Statistik -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-10 text-center">Kinerja Bantuan Navigasi 2024</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#2a1a4a] mb-2">312</div>
                <div class="text-sm text-on-surface-variant font-medium">Permintaan Bantuan / Tahun</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#2a1a4a] mb-2">&lt; 2'</div>
                <div class="text-sm text-on-surface-variant font-medium">Waktu Respons Rata-rata</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#2a1a4a] mb-2">24/7</div>
                <div class="text-sm text-on-surface-variant font-medium">Ketersediaan Operator</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#2a1a4a] mb-2">98,4%</div>
                <div class="text-sm text-on-surface-variant font-medium">Tingkat Penyelesaian</div>
            </div>
        </div>
    </div>
</section>

<!-- Cara Kerja -->
<section class="py-16 px-8 bg-surface">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-primary mb-4 text-center">Prosedur Bantuan Navigasi VTS</h2>
        <p class="text-center text-on-surface-variant mb-12">Dari permohonan kapal hingga resolusi – terstandar IALA VTS.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#2a1a4a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">radio</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">1. Penerimaan Panggilan MAYDAY/PAN</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Operator VTS memantau VHF Ch.16 (distress) dan Ch.12 (kerja) secara terus-menerus. Setiap panggilan darurat direspons dalam 2 menit dengan konfirmasi posisi dan sifat darurat.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#2a1a4a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">explore</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">2. Verifikasi Posisi & Situasi</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Posisi kapal diverifikasi via AIS/radar. Situasi dinilai untuk menentukan level respons: informasi navigasi, arahan darurat, atau aktivasi BASARNAS.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#2a1a4a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">storm</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">3. Diseminasi Info Cuaca & Navigasi</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">VTS menyiarkan NAVTEX dan Marine Weather Broadcast setiap 4 jam: kecepatan arus, visibilitas, kecepatan angin, ketinggian gelombang, dan peringatan bahaya lokal.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#2a1a4a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">support_agent</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">4. Koordinasi SAR & Eskalasi</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Untuk darurat jiwa, VTS langsung mengaktifkan jaringan SAR lokal, berkoordinasi dengan KN SAR Palembang, TNI-AL, dan Basarnas Sumsel dalam satu rantai komando.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Info Cuaca & Channel (Mockup) -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-2">Kondisi Navigasi Terkini</h2>
        <p class="text-on-surface-variant text-sm mb-8">Data ilustratif – diperbarui setiap 4 jam.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Current Weather -->
            <div class="bg-white rounded-xl shadow-sm border border-outline-variant/20 p-6">
                <h3 class="font-bold text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-xl">cloud</span> Cuaca Saat Ini</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-on-surface-variant">Angin</span><span class="font-semibold">S 12 kt</span></div>
                    <div class="flex justify-between"><span class="text-on-surface-variant">Visibilitas</span><span class="font-semibold">8 NM</span></div>
                    <div class="flex justify-between"><span class="text-on-surface-variant">Arus</span><span class="font-semibold">2.1 kt (Pasang)</span></div>
                    <div class="flex justify-between"><span class="text-on-surface-variant">Ombak</span><span class="font-semibold">0.3 m</span></div>
                    <div class="flex justify-between"><span class="text-on-surface-variant">Pasang Puncak</span><span class="font-semibold">14:52 LT (+2.8m)</span></div>
                </div>
            </div>
            <!-- VHF Channels -->
            <div class="bg-white rounded-xl shadow-sm border border-outline-variant/20 p-6">
                <h3 class="font-bold text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-xl">cell_tower</span> Channel VHF VTS</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">VHF Ch.16</span>
                        <span class="font-semibold flex items-center gap-1 text-red-600"><span class="w-2 h-2 bg-red-500 rounded-full animate-pulse inline-block"></span> Distress</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">VHF Ch.12</span>
                        <span class="font-semibold flex items-center gap-1 text-green-700"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse inline-block"></span> Kerja Utama</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">VHF Ch.67</span>
                        <span class="font-semibold text-blue-700">Koordinasi SAR</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">VHF Ch.70</span>
                        <span class="font-semibold text-blue-700">DSC</span>
                    </div>
                </div>
            </div>
            <!-- NAVTEX Aktif -->
            <div class="bg-white rounded-xl shadow-sm border border-outline-variant/20 p-6">
                <h3 class="font-bold text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-xl">warning</span> NAVTEX Aktif</h3>
                <div class="space-y-3 text-sm">
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="font-semibold text-yellow-800 text-xs uppercase tracking-wide mb-1">IN FORCE</p>
                        <p class="text-yellow-700">Alur Sempit KM 8-12: Larangan Berlabuh Jangkar. Kedalaman Min 6m berlaku s.d. 20 Jul.</p>
                    </div>
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="font-semibold text-blue-800 text-xs uppercase tracking-wide mb-1">INFO</p>
                        <p class="text-blue-700">Pelampung No.7 alur Barat sedang dalam perbaikan. Low visibility expected 03:00-06:00 LT.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Bantuan -->
        <h2 class="text-2xl font-bold text-primary mb-2">Log Bantuan Navigasi Terkini</h2>
        <p class="text-on-surface-variant text-sm mb-6">Data ilustratif – 5 kejadian terakhir.</p>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-outline-variant/20">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#2a1a4a] text-white">
                        <th class="px-5 py-3 text-left font-semibold">Waktu</th>
                        <th class="px-5 py-3 text-left font-semibold">Kapal</th>
                        <th class="px-5 py-3 text-left font-semibold">Jenis Bantuan</th>
                        <th class="px-5 py-3 text-left font-semibold">Ch. VHF</th>
                        <th class="px-5 py-3 text-left font-semibold">Durasi</th>
                        <th class="px-5 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant font-mono">14 Jul · 04:22</td>
                        <td class="px-5 py-3 font-medium">MV Palembang Star</td>
                        <td class="px-5 py-3">Panduan Alur Kabut Tebal</td>
                        <td class="px-5 py-3 text-on-surface-variant">Ch.12</td>
                        <td class="px-5 py-3 text-on-surface-variant">18 menit</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Selesai</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant font-mono">13 Jul · 21:07</td>
                        <td class="px-5 py-3 font-medium">KM Kota Baru 07</td>
                        <td class="px-5 py-3">Kehilangan Sinyal GPS</td>
                        <td class="px-5 py-3 text-on-surface-variant">Ch.12</td>
                        <td class="px-5 py-3 text-on-surface-variant">7 menit</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Selesai</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant font-mono">12 Jul · 14:55</td>
                        <td class="px-5 py-3 font-medium">TB Musi Jaya III</td>
                        <td class="px-5 py-3">Informasi Pasang Surut Darurat</td>
                        <td class="px-5 py-3 text-on-surface-variant">Ch.16</td>
                        <td class="px-5 py-3 text-on-surface-variant">4 menit</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Selesai</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant font-mono">11 Jul · 08:30</td>
                        <td class="px-5 py-3 font-medium">MT Pertamina Sei Lais</td>
                        <td class="px-5 py-3">Panduan Berlabuh Darurat</td>
                        <td class="px-5 py-3 text-on-surface-variant">Ch.12</td>
                        <td class="px-5 py-3 text-on-surface-variant">32 menit</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Selesai</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant font-mono">10 Jul · 23:14</td>
                        <td class="px-5 py-3 font-medium">KM Sriwijaya Express</td>
                        <td class="px-5 py-3">MAYDAY – Mesin Mati</td>
                        <td class="px-5 py-3 text-on-surface-variant">Ch.16</td>
                        <td class="px-5 py-3 text-on-surface-variant">SAR Aktif</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Diselesaikan SAR</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 px-8 bg-[#2a1a4a] text-white text-center">
    <div class="max-w-2xl mx-auto">
        <span class="material-symbols-outlined text-5xl mb-4 block opacity-80 filled">near_me</span>
        <h2 class="text-3xl font-bold mb-4">Butuh Panduan Navigasi?</h2>
        <p class="text-purple-100 mb-4 text-lg">Hubungi VTS Palembang segera melalui radio VHF:</p>
        <div class="flex justify-center gap-6 mb-8 flex-wrap">
            <div class="bg-white/10 rounded-xl px-6 py-4">
                <div class="text-2xl font-extrabold">Ch.12</div>
                <div class="text-purple-200 text-sm">Kerja Utama</div>
            </div>
            <div class="bg-red-500/30 border border-red-400/40 rounded-xl px-6 py-4">
                <div class="text-2xl font-extrabold">Ch.16</div>
                <div class="text-purple-200 text-sm">Darurat / Distress</div>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>index.php#kontak">
                <button class="bg-white text-[#2a1a4a] font-bold px-8 py-3 rounded-md hover:bg-purple-50 transition-colors">Info Kontak Lengkap</button>
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
