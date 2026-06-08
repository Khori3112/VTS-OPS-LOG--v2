<?php

declare(strict_types=1);

// Hanya boleh dijalankan dari CLI.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Akses ditolak. Jalankan via: php sql/seed_personnel.php');
}

require_once __DIR__ . '/../config/helpers.php';

// -----------------------------------------------------------------------
// DATA 30 PERSONEL VTS PALEMBANG
// Password default semua: VTS@2026  (harus diganti setelah login pertama)
// NIP format PNS: YYYYMMDD YYYYMM [1=L/2=P] NNN (18 digit tanpa spasi)
// -----------------------------------------------------------------------
$defaultPassword = 'VTS@2026';

$personnel = [
    // ---- MANAGER (1 orang) ----
    [
        'nip'     => '197505021997032001',
        'full_name' => 'Merry Dhani Anitasari',
        'role'    => 'Manager',
        'team'    => 'A',
        'jabatan' => 'Kepala Sub-seksi VTS',
    ],

    // ---- SUPERVISOR (5 orang, 1 per tim) ----
    [
        'nip'     => '198003122007121001',
        'full_name' => 'Ria Irawan, S.Pd',
        'role'    => 'Supervisor',
        'team'    => 'A',
        'jabatan' => 'Penjaga Jaga (Watching Keeper)',
    ],
    [
        'nip'     => '197806152008121002',
        'full_name' => 'Akmal Lastson',
        'role'    => 'Supervisor',
        'team'    => 'B',
        'jabatan' => 'Penjaga Jaga (Watching Keeper)',
    ],
    [
        'nip'     => '198204102009121003',
        'full_name' => 'Jefri Hardani',
        'role'    => 'Supervisor',
        'team'    => 'C',
        'jabatan' => 'Penjaga Jaga (Watching Keeper)',
    ],
    [
        'nip'     => '198511082010121004',
        'full_name' => 'Tri Yuni Hasnan',
        'role'    => 'Supervisor',
        'team'    => 'D',
        'jabatan' => 'Penjaga Jaga (Watching Keeper)',
    ],
    [
        'nip'     => '198709152011121005',
        'full_name' => 'Satria Hidarsyah',
        'role'    => 'Supervisor',
        'team'    => 'E',
        'jabatan' => 'Penjaga Jaga (Watching Keeper)',
    ],

    // ---- OPERATOR VTS TIM A (4 orang) ----
    [
        'nip'     => '199203182012121001',
        'full_name' => 'Adi Sang Putra',
        'role'    => 'Operator',
        'team'    => 'A',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199405092013121002',
        'full_name' => 'Ahmad Fauzi Ramadhan',
        'role'    => 'Operator',
        'team'    => 'A',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199607212014121003',
        'full_name' => 'Budi Prasetyo',
        'role'    => 'Operator',
        'team'    => 'A',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199809042015121004',
        'full_name' => 'Candra Wijaya',
        'role'    => 'Operator',
        'team'    => 'A',
        'jabatan' => 'Operator VTS',
    ],

    // ---- OPERATOR VTS TIM B (4 orang) ----
    [
        'nip'     => '199101132016121001',
        'full_name' => 'Dede Syahputra',
        'role'    => 'Operator',
        'team'    => 'B',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199302272016121002',
        'full_name' => 'Eko Kurniawan',
        'role'    => 'Operator',
        'team'    => 'B',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199511192017121003',
        'full_name' => 'Ferry Andrian',
        'role'    => 'Operator',
        'team'    => 'B',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199607032017122004',
        'full_name' => 'Fitri Handayani',
        'role'    => 'Operator',
        'team'    => 'B',
        'jabatan' => 'Operator VTS',
    ],

    // ---- OPERATOR VTS TIM C (4 orang) ----
    [
        'nip'     => '199002162018121001',
        'full_name' => 'Guntur Saputra',
        'role'    => 'Operator',
        'team'    => 'C',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199205302018121002',
        'full_name' => 'Hendra Kusuma',
        'role'    => 'Operator',
        'team'    => 'C',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199408142019121003',
        'full_name' => 'Irwan Setiawan',
        'role'    => 'Operator',
        'team'    => 'C',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199512072019122004',
        'full_name' => 'Indah Puspitasari',
        'role'    => 'Operator',
        'team'    => 'C',
        'jabatan' => 'Operator VTS',
    ],

    // ---- OPERATOR VTS TIM D (4 orang) ----
    [
        'nip'     => '199001082020121001',
        'full_name' => 'Joko Purnomo',
        'role'    => 'Operator',
        'team'    => 'D',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199204252020121002',
        'full_name' => 'Lukman Hakim',
        'role'    => 'Operator',
        'team'    => 'D',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199507162021121003',
        'full_name' => 'Muhammad Fajri',
        'role'    => 'Operator',
        'team'    => 'D',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199703022021121004',
        'full_name' => 'Niko Andrianto',
        'role'    => 'Operator',
        'team'    => 'D',
        'jabatan' => 'Operator VTS',
    ],

    // ---- OPERATOR VTS TIM E (4 orang) ----
    [
        'nip'     => '199801112022121001',
        'full_name' => 'Omar Abdullah',
        'role'    => 'Operator',
        'team'    => 'E',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '199905202022121002',
        'full_name' => 'Putra Andika',
        'role'    => 'Operator',
        'team'    => 'E',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '200003312023121003',
        'full_name' => 'Rahmat Hidayat',
        'role'    => 'Operator',
        'team'    => 'E',
        'jabatan' => 'Operator VTS',
    ],
    [
        'nip'     => '200112182023121004',
        'full_name' => 'M. Auda Ramadhan',
        'role'    => 'Operator',
        'team'    => 'E',
        'jabatan' => 'Operator VTS',
    ],

    // ---- TEKNISI ELEKTRONIKA NAVIGASI (Tim A & B) ----
    [
        'nip'     => '199306242014121010',
        'full_name' => 'Riki Firmansyah',
        'role'    => 'Operator',
        'team'    => 'A',
        'jabatan' => 'Teknisi Elektronika Navigasi',
    ],
    [
        'nip'     => '199501152015121011',
        'full_name' => 'Muhammad Helmi',
        'role'    => 'Operator',
        'team'    => 'B',
        'jabatan' => 'Teknisi Elektronika Navigasi',
    ],

    // ---- ADMINISTRASI / TATA USAHA (Tim C & D) ----
    [
        'nip'     => '199608292016122012',
        'full_name' => 'Tita Bela Hisri',
        'role'    => 'Operator',
        'team'    => 'C',
        'jabatan' => 'Administrasi / Tata Usaha',
    ],
    [
        'nip'     => '199712172017122013',
        'full_name' => 'Silvi Erin Arfila',
        'role'    => 'Operator',
        'team'    => 'D',
        'jabatan' => 'Administrasi / Tata Usaha',
    ],

    // ---- MARKONIS / RADIO OPERATOR (1 per tim, 5 orang) ----
    [
        'nip'   => '199104082013121015',
        'full_name' => 'Hendri Saputra',
        'role'    => 'Operator',
        'team'    => 'A',
        'jabatan' => 'Markonis / Radio Operator',
    ],
    [
        'nip'     => '199206182014121016',
        'full_name' => 'Novita Sari',
        'role'    => 'Operator',
        'team'    => 'B',
        'jabatan' => 'Markonis / Radio Operator',
    ],
    [
        'nip'     => '199308292015121017',
        'full_name' => 'Agus Riyanto',
        'role'    => 'Operator',
        'team'    => 'C',
        'jabatan' => 'Markonis / Radio Operator',
    ],
    [
        'nip'     => '199410052016121018',
        'full_name' => 'Dewi Anggraeni',
        'role'    => 'Operator',
        'team'    => 'D',
        'jabatan' => 'Markonis / Radio Operator',
    ],
    [
        'nip'     => '199512162016121019',
        'full_name' => 'Bayu Nugroho',
        'role'    => 'Operator',
        'team'    => 'E',
        'jabatan' => 'Markonis / Radio Operator',
    ],
];

try {
    $pdo = getPdo();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO users (nip, full_name, jabatan, password_hash, role, team, is_active)
         VALUES (:nip, :full_name, :jabatan, :password_hash, :role, :team, 1)
         ON DUPLICATE KEY UPDATE
           full_name     = VALUES(full_name),
           jabatan       = VALUES(jabatan),
           password_hash = VALUES(password_hash),
           role          = VALUES(role),
           team          = VALUES(team),
           is_active     = 1'
    );

    $inserted = 0;
    foreach ($personnel as $person) {
        $hash = password_hash($defaultPassword, PASSWORD_ARGON2ID);
        $stmt->execute([
            ':nip'           => $person['nip'],
            ':full_name'     => $person['full_name'],
            ':jabatan'       => $person['jabatan'] ?? null,
            ':password_hash' => $hash,
            ':role'          => $person['role'],
            ':team'          => $person['team'],
        ]);
        $inserted++;
        echo sprintf("  OK  [%s] %s (%s — %s — Tim %s)\n",
            $person['nip'],
            $person['full_name'],
            $person['jabatan'] ?? '-',
            $person['role'],
            $person['team']
        );
    }

    $pdo->commit();

    echo "\n========================================\n";
    echo "Seeding selesai. Total: {$inserted} personel.\n";
    echo "Password default semua: {$defaultPassword}\n";
    echo "WAJIB ganti password setelah login pertama!\n";
    echo "========================================\n";
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo 'ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}
