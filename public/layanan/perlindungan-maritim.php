<?php require_once __DIR__ . '/../../config/app.php'; ?>
<!DOCTYPE html>
<html class="light scroll-smooth" lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Perlindungan Maritim – VTS Palembang</title>
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
<section class="pt-28 pb-20 px-8 bg-[#1a4a2a] text-white">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-4 text-green-200 text-sm font-semibold uppercase tracking-widest">
            <a href="<?= BASE_URL ?>index.php#layanan" class="hover:underline">Layanan</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span>Perlindungan Maritim</span>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-4xl filled">eco</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">Perlindungan Maritim</h1>
        <p class="text-lg md:text-xl text-green-100 max-w-3xl leading-relaxed">
            Deteksi dini risiko pencemaran, pemantauan zona sensitif ekologis, dan perlindungan lingkungan perairan Sungai Musi dari ancaman tumpahan bahan berbahaya.
        </p>
    </div>
</section>

<!-- Statistik -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-10 text-center">Rekam Jejak Perlindungan 2024</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a4a2a] mb-2">0</div>
                <div class="text-sm text-on-surface-variant font-medium">Tumpahan Minyak Besar</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a4a2a] mb-2">14</div>
                <div class="text-sm text-on-surface-variant font-medium">Zona Lindung Dipantau</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a4a2a] mb-2">100%</div>
                <div class="text-sm text-on-surface-variant font-medium">Cakupan Area Sensitif</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-[#1a4a2a] mb-2">28</div>
                <div class="text-sm text-on-surface-variant font-medium">Insiden Tercegah / Tahun</div>
            </div>
        </div>
    </div>
</section>

<!-- Cara Kerja -->
<section class="py-16 px-8 bg-surface">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-primary mb-4 text-center">Sistem Perlindungan Lingkungan VTS</h2>
        <p class="text-center text-on-surface-variant mb-12">Empat layer deteksi untuk menjaga ekosistem Sungai Musi.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a4a2a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">sensors</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">1. Sensor Anomali Kapal</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">AIS dan radar memantau kapal tanker dan B3 yang bergerak tidak normal (kecepatan turun mendadak, berhenti di zona bebas labuh) sebagai indikator potensi kebocoran.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a4a2a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">local_fire_department</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">2. Koordinasi Respons IOPP</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Dalam 2 menit setelah deteksi insiden pencemaran, VTS mengaktifkan IOPP Plan dan berkoordinasi dengan POSAL, KKP, dan tim oil spill response.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a4a2a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">map</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">3. Pemetaan Zona Sensitif</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">GIS layer ekologis terintegrasi dalam VTMIS menandai wilayah mangrove, kawasan budidaya ikan, dan intake PDAM sebagai zona prioritas perlindungan.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-[#1a4a2a] flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">fact_check</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">4. Audit Dokumen B3</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">VTS mewajibkan pelaporan manifest muatan B3 sebelum kapal masuk alur, memastikan kesiapan prosedur kedaruratan sesuai MARPOL 73/78.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tabel Monitoring Lingkungan (Mockup) -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-2">Pemantauan Lingkungan Real-Time</h2>
        <p class="text-on-surface-variant text-sm mb-8">Data ilustratif – diperbarui setiap jam.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-outline-variant/20 overflow-hidden">
                <div class="bg-[#1a4a2a] text-white px-5 py-3 font-semibold text-sm">Status Zona Lindung</div>
                <table class="w-full text-sm">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-5 py-2 text-left text-on-surface-variant font-medium">Zona</th>
                            <th class="px-5 py-2 text-left text-on-surface-variant font-medium">Km</th>
                            <th class="px-5 py-2 text-left text-on-surface-variant font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        <tr><td class="px-5 py-2.5">Kawasan Mangrove Upang</td><td class="px-5 py-2.5 text-on-surface-variant">KM 52-58</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aman</span></td></tr>
                        <tr><td class="px-5 py-2.5">Intake PDAM Karang Anyar</td><td class="px-5 py-2.5 text-on-surface-variant">KM 18</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aman</span></td></tr>
                        <tr><td class="px-5 py-2.5">Budidaya Ikan Jakabaring</td><td class="px-5 py-2.5 text-on-surface-variant">KM 8-12</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aman</span></td></tr>
                        <tr><td class="px-5 py-2.5">Muara Ogan Restriced Zone</td><td class="px-5 py-2.5 text-on-surface-variant">KM 3-5</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Waspada</span></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-outline-variant/20 overflow-hidden">
                <div class="bg-[#1a4a2a] text-white px-5 py-3 font-semibold text-sm">Log Kapal Bahan Berbahaya Hari Ini</div>
                <table class="w-full text-sm">
                    <thead class="bg-surface-container-low">
                        <tr>
                            <th class="px-5 py-2 text-left text-on-surface-variant font-medium">Kapal</th>
                            <th class="px-5 py-2 text-left text-on-surface-variant font-medium">Muatan</th>
                            <th class="px-5 py-2 text-left text-on-surface-variant font-medium">Dok. MARPOL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        <tr><td class="px-5 py-2.5 font-medium">MT Pusri 08</td><td class="px-5 py-2.5 text-on-surface-variant">Amonia Cair</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Lengkap</span></td></tr>
                        <tr><td class="px-5 py-2.5 font-medium">MT Sei Gerong</td><td class="px-5 py-2.5 text-on-surface-variant">BBM Marine</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Lengkap</span></td></tr>
                        <tr><td class="px-5 py-2.5 font-medium">MT Asahan</td><td class="px-5 py-2.5 text-on-surface-variant">CPO</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Perlu Verifikasi</span></td></tr>
                        <tr><td class="px-5 py-2.5 font-medium">KM Pelita Nusantara</td><td class="px-5 py-2.5 text-on-surface-variant">Batu Bara</td><td class="px-5 py-2.5"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Lengkap</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 px-8 bg-[#1a4a2a] text-white text-center">
    <div class="max-w-2xl mx-auto">
        <span class="material-symbols-outlined text-5xl mb-4 block opacity-80 filled">eco</span>
        <h2 class="text-3xl font-bold mb-4">Laporkan Potensi Pencemaran</h2>
        <p class="text-green-100 mb-8">Segera hubungi VTS Palembang jika Anda melihat indikasi tumpahan minyak atau bahan berbahaya di perairan Sungai Musi.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>index.php#kontak">
                <button class="bg-white text-[#1a4a2a] font-bold px-8 py-3 rounded-md hover:bg-green-50 transition-colors">Hubungi Kami</button>
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
