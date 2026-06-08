# VTS-OPS-LOG — Dokumen Serah Terima Sistem

> **Versi Handover:** Phase 6 (Final Hardening)  
> **Tanggal:** Juni 2025  
> **Sistem:** Aplikasi Manajemen Operasional VTS Palembang  
> **Stack:** PHP 8.2+, MySQL 8, XAMPP, Tailwind CDN, Chart.js 4, Tippy.js 6, Dompdf

---

## Daftar Isi

1. [Gambaran Sistem](#1-gambaran-sistem)
2. [Persyaratan Instalasi](#2-persyaratan-instalasi)
3. [Langkah Deployment XAMPP](#3-langkah-deployment-xampp)
4. [Konfigurasi Database](#4-konfigurasi-database)
5. [Registri Akun Pengguna](#5-registri-akun-pengguna)
6. [Logika Bisnis Sistem](#6-logika-bisnis-sistem)
7. [Alur Kerja Shift (Sovereign Locking Protocol)](#7-alur-kerja-shift-sovereign-locking-protocol)
8. [Struktur Direktori](#8-struktur-direktori)
9. [Backup & Pemeliharaan](#9-backup--pemeliharaan)
10. [Panduan Troubleshooting](#10-panduan-troubleshooting)
11. [Referensi Keamanan](#11-referensi-keamanan)

---

## 1. Gambaran Sistem

VTS-OPS-LOG adalah sistem pelaporan operasional digital berbasis web untuk **Vessel Traffic Service (VTS) Palembang**, menggantikan pelaporan kertas manual. Sistem mencakup:

| Fitur | Deskripsi |
|---|---|
| **A-1 VTS Log** | Pencatatan log komunikasi kapal harian |
| **A-2 Vessel Traffic** | Data lalu lintas kapal masuk/keluar/transit |
| **A-3 Weather Grid** | Grid cuaca maritim 11 titik wilayah perairan |
| **A-6 Laporan Insiden** | SAR, kecelakaan, pencemaran laut |
| **A-7 Operasi Khusus** | VVIP movement, latihan militer, survei hidrografi |
| **Export PDF** | Laporan digital berformat A4, watermarked, QR-verifikasi |
| **Sovereign Locking** | Protokol penguncian laporan bertahap Operator → Supervisor → Manager |
| **PWA** | Progressive Web App — dapat diinstal di perangkat mobile |
| **Backup DB** | Download backup MySQL via browser (Manager only) |
| **Audit Trail** | Semua aksi sensitif tercatat di tabel `audit_logs` |

---

## 2. Persyaratan Instalasi

| Komponen | Versi Minimum | Catatan |
|---|---|---|
| PHP | 8.1+ | Strict types, `password_hash(PASSWORD_ARGON2ID)` |
| MySQL | 5.7+ / MariaDB 10.5+ | utf8mb4_unicode_ci |
| XAMPP | 8.1+ | Apache 2.4, PHP 8.1+, MySQL/MariaDB, `mod_rewrite` aktif |
| PHP Extensions | `pdo_mysql`, `session`, `mbstring`, `fileinfo`, `zip` | |
| Composer | 2.x | Untuk Dompdf PDF export |
| Disk | ≥ 100 MB | Termasuk vendor/ Dompdf |

---

## 3. Langkah Deployment XAMPP

> Bagian ini memakai pola developer-ready: source code di-versioning, dependency diambil via Composer, konfigurasi rahasia disimpan di `.env`, dan Apache diarahkan ke folder `public`.

### 3.1 Kloning / Salin Proyek

```text
C:\xampp\htdocs\VTS-OPS-LOG\
```

Jika repository sudah tersedia di GitHub:

```powershell
cd C:\xampp\htdocs
git clone <URL_REPOSITORY_GITHUB> VTS-OPS-LOG
cd VTS-OPS-LOG
```

Jika belum menggunakan Git, salin folder project secara manual ke path di atas.

### 3.1.1 Validasi Environment XAMPP

Pastikan service berikut aktif di XAMPP Control Panel:

| Service | Status | Catatan |
|---|---|---|
| Apache | Running | Gunakan port 80 atau port custom yang tidak bentrok |
| MySQL | Running | Default port `3306` |
| PHP | 8.1+ | Cek dengan `php -v` |

Aktifkan ekstensi PHP berikut di `C:\xampp\php\php.ini` bila belum aktif:

```ini
extension=pdo_mysql
extension=mbstring
extension=fileinfo
extension=zip
extension=sodium
```

Restart Apache setelah perubahan `php.ini`.

### 3.2 Install Composer Dependencies

```powershell
cd C:\xampp\htdocs\VTS-OPS-LOG
composer install
```

> Gunakan `composer install` untuk mengambil dependency sesuai `composer.lock`. Gunakan `composer require` hanya ketika menambah package baru.

### 3.3 Buat file konfigurasi `.env`

Salin `.env.example` menjadi `.env`, lalu sesuaikan koneksi database:

```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=vts_ops_log
DB_USER=root
DB_PASS=
APP_ENV=production
```

> **PERINGATAN:** Jangan commit file `.env` ke repositori. File ini sudah terdaftar di `.gitignore`.

### 3.4 Import Skema Database

Jalankan file SQL secara berurutan di phpMyAdmin atau MySQL CLI:

```sql
-- 1. Skema utama
source sql/schema.sql;

-- 2. Migrasi wilayah maritim
source sql/migration_master_maritime_areas.sql;

-- 3. Migrasi Phase 3 & 4
source sql/migration_phase3_phase4.sql;

-- 4. Cek foreign key cascade
source sql/fk_cascade_check.sql;
```

### 3.5 Seed Data Awal (Opsional)

Jika file seed tersedia:

```bash
php bootstrap_setup.php
```

### 3.6 Buat Direktori Logs

```powershell
New-Item -ItemType Directory -Path "C:\xampp\htdocs\VTS-OPS-LOG\logs" -Force
New-Item -ItemType File -Path "C:\xampp\htdocs\VTS-OPS-LOG\logs\error.log" -Force
```

File log runtime tidak perlu dipush ke GitHub. Simpan hanya `logs/.gitkeep` dan `logs/.htaccess`.

### 3.7 Akses Aplikasi

```text
http://localhost/VTS-OPS-LOG/public/login.php
```

Untuk development yang lebih profesional, arahkan Apache langsung ke folder `public` melalui virtual host:

```apache
<VirtualHost *:80>
    ServerName vts-ops-log.local
    DocumentRoot "C:/xampp/htdocs/VTS-OPS-LOG/public"

    <Directory "C:/xampp/htdocs/VTS-OPS-LOG/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Tambahkan `127.0.0.1 vts-ops-log.local` ke file `hosts`, restart Apache, lalu buka `http://vts-ops-log.local/`.

---

## 4. Konfigurasi Database

### Database: `vts_ops_log`

Tabel utama:

| Tabel | Fungsi |
|---|---|
| `users` | Data pengguna, role, team, Argon2id hash |
| `daily_shift_reports` | Header laporan per shift |
| `vts_logs` | Entri A-1 VTS Log |
| `vessel_traffic` | Entri A-2 lalu lintas kapal |
| `weather_reports` | Header laporan cuaca A-3 |
| `weather_observations` | Baris grid cuaca per area |
| `incident_reports` | Laporan insiden A-6 |
| `special_ops_reports` | Laporan operasi khusus A-7 |
| `contravention_reports` | Laporan pelanggaran A-8 |
| `pre_arrival_reports` | Laporan pra-kedatangan A-5 |
| `edit_access_grants` | Token izin edit pasca-kunci |
| `audit_logs` | Trail seluruh aksi sensitif |
| `maritime_areas` | 11 wilayah maritim Palembang |

### Pengaturan Karakter

Semua tabel menggunakan `utf8mb4_unicode_ci`. Pastikan koneksi MySQL juga menggunakan charset `utf8mb4` (sudah dikonfigurasi di `config/database.php`).

---

## 5. Registri Akun Pengguna

### Akun Produksi

| Nama | NIP | Role | Tim |
|---|---|---|---|
| Merry Dhani Anitasari | *(lihat data DB)* | Manager | - |
| Ria Irawan | *(lihat data DB)* | Supervisor | *(sesuai jadwal)* |
| *(Operator Tim A–E)* | *(lihat data DB)* | Operator | A / B / C / D / E |

> **Catatan:** Password disimpan sebagai hash Argon2id. Reset password dilakukan melalui form "Buat / Update User" di halaman Manager.

### Pembuatan User Baru

1. Login sebagai Manager
2. Buka **Manager Approval Dashboard**
3. Gunakan form **Buat / Update User**
4. Isi NIP, Nama Lengkap, Password, Role, dan Tim
5. Klik **Simpan User**

> Jika NIP sudah ada, sistem akan mengupdate data (termasuk hash password baru).

---

## 6. Logika Bisnis Sistem

### Jadwal Shift (Hard-coded)

Sistem menggunakan jadwal rotasi 5-tim (A–E) berdasarkan tanggal:

| Shift | Waktu |
|---|---|
| Pagi | 08:00 – 14:00 WIB |
| Siang | 14:00 – 20:00 WIB |
| Malam | 20:00 – 08:00 WIB (+1 hari) |

Shift aktif ditentukan oleh fungsi `currentShiftCategory()` di `config/helpers.php`.

### Periode Operasional Malam

Shift Malam yang dimulai pukul 20:00 dicatat pada tanggal yang sama, tetapi berakhir keesokan harinya. Fungsi `operationalShiftDate()` menangani ini — jika jam sekarang 00:00–08:00, tanggal operasional adalah hari sebelumnya.

### Hak Tulis (`canWriteShiftRecord`)

Operator hanya dapat menulis ke laporan shift yang:
1. Sedang berjalan (kategori shift cocok dengan jadwal tim)
2. Belum dikunci (`is_locked = 0`) **ATAU** memiliki token izin edit aktif

---

## 7. Alur Kerja Shift (Sovereign Locking Protocol)

```
OPERATOR                    SUPERVISOR               MANAGER
    │                           │                       │
    ├─ Input A1/A2/A3/A6/A7     │                       │
    │                           │                       │
    ├─ [Kunci Laporan] ────────►│                       │
    │   (is_locked = 1)         │                       │
    │                           ├─ [Grant Edit] ──────►│
    │◄──────────────────────────┤  (15 menit window)    │
    │   (edit_access_grants)    │                       │
    │                           │                       │
    │                           ├─ [Approve Edit] ──►  │
    │                           │  (update record)      │
    │                           │                       │
    │                           │              [ACKNOWLEDGE & FINALIZE]
    │                           │              (final_copy_watermark=1)
    │                           │                       │
    └───────────────────────────┴───────────────────────┘
                    PDF Export tersedia setelah is_locked = 1
                    Watermark "FINAL COPY" setelah finalize
```

### Jendela Revisi 15 Menit

Setelah laporan dikunci, Operator dapat meminta izin edit melalui tombol **Minta Izin Edit**. Supervisor/Manager dapat memberikan izin yang berlaku selama **15 menit** (`edit_access_grants.expires_at`). Setelah waktu habis, formulir kembali ke mode baca.

### Finalisasi oleh Manager

Setelah Supervisor menyerahkan laporan, Manager dapat menekan **ACKNOWLEDGE & FINALIZE** di dashboard Manager. Ini mengatur `final_copy_watermark = 1` dan tidak dapat dibatalkan. PDF yang dicetak setelah ini akan membawa cap **FINAL COPY** dan QR verifikasi.

---

## 8. Struktur Direktori

```
VTS-OPS-LOG/
├── .env                    ← Konfigurasi (TIDAK di-commit)
├── bootstrap_setup.php     ← Script seed awal
├── middleware.php          ← Guard autentikasi & otorisasi
├── composer.json           ← Dependencies PHP
├── sw.js                   ← Service Worker PWA
├── manifest.php            ← Web App Manifest PWA
│
├── config/
│   ├── app.php             ← BASE_PATH, BASE_URL, redirect(), csrfToken()
│   ├── database.php        ← getPdo() singleton, error handling ke 503
│   └── helpers.php         ← Helper functions: requireLogin, logAudit, logError, shift logic
│
├── public/
│   ├── index.php           ← Entry point (redirect ke login)
│   ├── login.php           ← Halaman login glassmorphism
│   ├── dashboard.php       ← Router dashboard (redirect per role)
│   ├── export_pdf.php      ← Export laporan ke PDF (Dompdf)
│   ├── home.php            ← Halaman publik
│   ├── 404.php             ← Halaman error 404
│   └── 503.php             ← Halaman error 503 (DB down)
│
├── views/
│   ├── operator/
│   │   └── dashboard.php   ← Dashboard utama operator (A1–A7, charts, PWA)
│   ├── supervisor/
│   │   ├── dashboard.php
│   │   └── audit_logs.php
│   ├── manager/
│   │   ├── manager_approval.php  ← Approval, finalize, server intelligence
│   │   └── audit_logs.php
│   └── partials/
│       ├── layout.php
│       ├── topnav.php      ← Topbar + session tooltip
│       └── sidenav.php     ← Sidebar navigasi
│
├── actions/
│   ├── auth/
│   │   ├── login_process.php
│   │   └── register_process.php
│   ├── operator/
│   │   ├── vts_log_store.php
│   │   ├── vessel_traffic_store.php
│   │   ├── weather_store.php
│   │   ├── lock_shift_report.php
│   │   ├── request_edit.php
│   │   ├── special_report_store.php
│   │   └── draft_save.php       ← Auto-save draft A-2
│   ├── supervisor/
│   │   ├── approve_edit.php
│   │   └── grant_edit.php
│   ├── manager/
│   │   ├── acknowledge.php
│   │   ├── create_user.php
│   │   └── finalize_report.php
│   └── admin/
│       └── backup_db.php        ← mysqldump → ZIP download
│
├── assets/
│   ├── style.css
│   ├── tailwind.js              ← Tailwind CDN (lokal)
│   └── img/
│       ├── logo_navigasi.png
│       └── qr_ria_irawan.png    ← QR lokal supervisor
│
├── logs/
│   ├── .htaccess               ← Deny from all (proteksi akses web)
│   └── error.log               ← Log error sistem
│
└── sql/
    ├── schema.sql
    ├── migration_master_maritime_areas.sql
    ├── migration_phase3_phase4.sql
    └── fk_cascade_check.sql
```

---

## 9. Backup & Pemeliharaan

### Backup Manual (Browser)

1. Login sebagai **Manager**
2. Buka **Manager Approval Dashboard**
3. Klik tombol **↓ Backup DB**
4. File `vts_ops_log_YYYY-MM-DD_HHmmss.zip` akan diunduh

Fungsi ini menjalankan `mysqldump` via `exec()` pada server. Pastikan `mysqldump` tersedia di PATH sistem.

### Backup Otomatis (Rekomendasi)

Tambahkan task scheduler Windows (Task Scheduler) atau cron job (Linux) yang menjalankan:

```bash
mysqldump -u root vts_ops_log > /backup/vts_$(date +%Y%m%d).sql
```

### Rotasi Log Error

File `logs/error.log` tumbuh seiring waktu. Rotate secara manual atau otomatis:

```powershell
# PowerShell — arsipkan log bulanan
$d = Get-Date -Format "yyyyMM"
Move-Item logs\error.log "logs\error_$d.log"
New-Item -ItemType File -Name "logs\error.log"
```

---

## 10. Panduan Troubleshooting

### Aplikasi menampilkan halaman 503

**Penyebab:** Koneksi database gagal (MySQL tidak berjalan atau kredensial salah).

**Solusi:**
1. Pastikan MySQL berjalan di XAMPP Control Panel
2. Periksa file `.env` — `DB_USER`, `DB_PASS`, `DB_NAME` harus benar
3. Periksa `logs/error.log` untuk detail error koneksi

### PDF export gagal / blank

**Penyebab:** Dompdf belum terinstall atau laporan belum dikunci.

**Solusi:**
1. Jalankan `composer require dompdf/dompdf` di root proyek
2. Pastikan laporan sudah berstatus `LOCKED` atau `FINAL` sebelum export

### Tombol "Kunci Laporan" tidak muncul

**Penyebab:** Laporan sudah dikunci (`is_locked = 1`) atau belum ada `daily_shift_report_id` di sesi.

**Solusi:** Pastikan operator login pada shift yang aktif dan laporan hari ini sudah dibuat.

### Error `DB_USER tidak dikonfigurasi`

**Penyebab:** File `.env` tidak ada atau `DB_USER` kosong.

**Solusi:** Buat / perbarui file `.env` sesuai template di Bagian 3.3.

### Service Worker tidak terupdate (PWA)

**Solusi:** Buka DevTools Chrome → Application → Service Workers → klik **Unregister**, lalu refresh halaman.

---

## 11. Referensi Keamanan

| Kontrol | Implementasi |
|---|---|
| **Autentikasi** | `password_hash(PASSWORD_ARGON2ID)` + session regeneration |
| **Otorisasi** | Role-based: Operator / Supervisor / Manager + `requireRole()` |
| **CSRF Protection** | Token per-sesi di semua form POST, validasi `hash_equals()` |
| **SQL Injection** | Seluruh query menggunakan PDO prepared statements |
| **XSS** | Semua output melalui `htmlspecialchars()` |
| **Session Fixation** | `session_regenerate_id(true)` pada login sukses |
| **Logs Protection** | `logs/.htaccess` → `Require all denied` |
| **Error Disclosure** | Error DB di-log ke `logs/error.log`, user diarahkan ke 503 |
| **Input Validation** | Filter & cast di boundary (action scripts), tidak bergantung JS |
| **Backup Security** | Backup hanya bisa diakses role Manager, via authenticated session |

---

*Dokumen ini dibuat sebagai bagian dari serah terima sistem VTS-OPS-LOG Phase 6 (Final Hardening). Untuk pertanyaan teknis, hubungi tim developer.*
