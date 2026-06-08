<?php require_once __DIR__ . '/../../config/app.php'; ?>
<!DOCTYPE html>
<html class="light scroll-smooth" lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Keselamatan Berlayar – VTS Palembang</title>
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
                    secondary: '#535f70', 'on-secondary': '#ffffff',
                    tertiary: '#6b5778', 'on-tertiary': '#ffffff',
                    'tertiary-container': '#f3daff', 'on-tertiary-container': '#251431',
                    'tertiary-fixed': '#f3daff', 'tertiary-fixed-dim': '#d9bbf0',
                    background: '#f7f9fe', 'on-background': '#181c20',
                    surface: '#f7f9fe', 'on-surface': '#181c20',
                    'surface-container': '#eaecf1', 'surface-container-low': '#f1f4f9',
                    'surface-container-high': '#dde0e5', 'surface-container-highest': '#d7dae0',
                    'on-surface-variant': '#42474e', outline: '#72777f',
                    'outline-variant': '#c2c7cf',
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
            <button class="bg-primary text-white px-5 py-2 rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">
                Masuk Sistem
            </button>
        </a>
    </div>
</nav>

<!-- Hero -->
<section class="pt-28 pb-20 px-8 bg-primary text-white">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-4 text-blue-200 text-sm font-semibold uppercase tracking-widest">
            <a href="<?= BASE_URL ?>index.php#layanan" class="hover:underline">Layanan</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span>Keselamatan Berlayar</span>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-4xl filled">verified_user</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">Keselamatan Berlayar</h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-3xl leading-relaxed">
            Pencegahan tabrakan kapal dan kandas melalui monitoring aktif pergerakan kapal di seluruh area pengawasan VTS Palembang secara 24/7 sepanjang tahun.
        </p>
    </div>
</section>

<!-- Statistik -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-10 text-center">Data Operasional 2024</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-primary mb-2">99,8%</div>
                <div class="text-sm text-on-surface-variant font-medium">Tingkat Keselamatan</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-primary mb-2">18.240</div>
                <div class="text-sm text-on-surface-variant font-medium">Kapal Dimonitor / Tahun</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-primary mb-2">24/7</div>
                <div class="text-sm text-on-surface-variant font-medium">Jam Pengawasan Aktif</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm text-center border border-outline-variant/20">
                <div class="text-4xl font-extrabold text-primary mb-2">< 90&quot;</div>
                <div class="text-sm text-on-surface-variant font-medium">Waktu Respons Insiden</div>
            </div>
        </div>
    </div>
</section>

<!-- Cara Kerja -->
<section class="py-16 px-8 bg-surface">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-primary mb-4 text-center">Bagaimana VTS Menjaga Keselamatan</h2>
        <p class="text-center text-on-surface-variant mb-12">Empat lapis pengawasan terintegrasi untuk nol insiden.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">radar</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">1. Pemantauan Radar & AIS</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Posisi, kecepatan, dan arah setiap kapal dipantau secara real-time melalui radar X-band dan transponder AIS kelas A/B.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">crisis_alert</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">2. Deteksi Bahaya Otomatis</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Sistem VTMIS menganalisis trajektori kapal dan memberikan peringatan dini jika terdeteksi potensi tabrakan atau pendangkalan.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">cell_tower</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">3. Komunikasi VHF Langsung</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Operator VTS menghubungi kapal melalui radio VHF Ch.12/16 untuk memberikan panduan, koreksi haluan, atau instruksi lambat jalan.</p>
                </div>
            </div>
            <div class="flex gap-4 p-6 bg-surface-container rounded-xl">
                <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">assignment_turned_in</span>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-1">4. Dokumentasi & Tindak Lanjut</h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Setiap insiden atau near-miss dicatat dalam sistem OPS-LOG dan dilaporkan ke Syahbandar untuk tindak lanjut administrasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tabel Insiden (Mockup) -->
<section class="py-16 px-8 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-primary mb-2">Log Insiden Terkini</h2>
        <p class="text-on-surface-variant text-sm mb-8">Data ilustratif – pembaruan setiap shift jaga.</p>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-outline-variant/20">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="px-5 py-3 text-left font-semibold">Tgl/Waktu</th>
                        <th class="px-5 py-3 text-left font-semibold">Nama Kapal</th>
                        <th class="px-5 py-3 text-left font-semibold">Jenis Insiden</th>
                        <th class="px-5 py-3 text-left font-semibold">Lokasi</th>
                        <th class="px-5 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant">14 Jul 2025 · 02:14</td>
                        <td class="px-5 py-3 font-medium">KM Sriwijaya Jaya</td>
                        <td class="px-5 py-3">Potensi Crossing Conflict</td>
                        <td class="px-5 py-3 text-on-surface-variant">KM 28+400</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Teratasi</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant">13 Jul 2025 · 18:47</td>
                        <td class="px-5 py-3 font-medium">MT Pertamina 32</td>
                        <td class="px-5 py-3">Kandas Ringan (Pasang Surut)</td>
                        <td class="px-5 py-3 text-on-surface-variant">Muara Banyuasin</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Teratasi</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant">12 Jul 2025 · 09:05</td>
                        <td class="px-5 py-3 font-medium">TB Musi Perkasa</td>
                        <td class="px-5 py-3">AIS Loss of Signal</td>
                        <td class="px-5 py-3 text-on-surface-variant">KM 15+700</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Dimonitor</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant">11 Jul 2025 · 22:30</td>
                        <td class="px-5 py-3 font-medium">KM Andalas Raya</td>
                        <td class="px-5 py-3">Overspeed Zona Restricted</td>
                        <td class="px-5 py-3 text-on-surface-variant">KM 5+200</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Teratasi</span></td>
                    </tr>
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-5 py-3 text-on-surface-variant">10 Jul 2025 · 07:12</td>
                        <td class="px-5 py-3 font-medium">MV Pacific Star</td>
                        <td class="px-5 py-3">Pelanggaran Alur Masuk</td>
                        <td class="px-5 py-3 text-on-surface-variant">Alur Pelayaran Barat</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Teratasi</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 px-8 bg-primary text-white text-center">
    <div class="max-w-2xl mx-auto">
        <span class="material-symbols-outlined text-5xl mb-4 block opacity-80 filled">verified_user</span>
        <h2 class="text-3xl font-bold mb-4">Butuh Informasi Lebih Lanjut?</h2>
        <p class="text-blue-100 mb-8">Operator VTS siap memberikan bantuan navigasi setiap saat. Hubungi kami melalui VHF Ch.12 atau email resmi.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>index.php#kontak">
                <button class="bg-white text-primary font-bold px-8 py-3 rounded-md hover:bg-blue-50 transition-colors">
                    Hubungi Kami
                </button>
            </a>
            <a href="<?= BASE_URL ?>index.php#layanan">
                <button class="bg-white/10 border border-white/30 text-white font-bold px-8 py-3 rounded-md hover:bg-white/20 transition-colors">
                    Layanan Lainnya
                </button>
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-surface-container-low border-t border-outline-variant/30 py-8 px-8 text-center text-sm text-on-surface-variant">
    © 2024 Distrik Navigasi Type B Palembang – VTS Palembang. Hak Cipta Dilindungi.
</footer>

</body>
</html>
