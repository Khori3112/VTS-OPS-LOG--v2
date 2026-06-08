<?php

declare(strict_types=1);

// Hanya boleh dijalankan dari Command Line Interface, bukan via browser.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Akses ditolak. File ini hanya bisa dijalankan via CLI.');
}

require_once __DIR__ . '/config/helpers.php';

$areas = [
    'Banyuasin',
    'Selat Gelasa',
    'Bangka',
    'Muara Sungai Musi',
    'Selat Bangka Utara',
    'Selat Bangka Selatan',
    'Perairan Sungsang',
    'Perairan Tanjung Buyut',
    'Perairan Upang',
    'Ambang Luar',
    'Tanjung Api-Api',
];

try {
    $pdo = getPdo();

    // DDL (CREATE TABLE) in MySQL causes an implicit commit — run outside a transaction.
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS maritime_areas (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            area_name VARCHAR(120) NOT NULL,
            display_order INT NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uk_maritime_area_name (area_name),
            UNIQUE KEY uk_maritime_display_order (display_order)
        ) ENGINE=InnoDB'
    );

    // Now wrap DML in a transaction.
    $pdo->beginTransaction();

    $areaStmt = $pdo->prepare(
        'INSERT INTO maritime_areas (area_name, display_order, is_active)
         VALUES (:area_name, :display_order, 1)
         ON DUPLICATE KEY UPDATE
           display_order = VALUES(display_order),
           is_active = 1'
    );

    foreach ($areas as $index => $areaName) {
        $areaStmt->execute([
            ':area_name' => $areaName,
            ':display_order' => $index + 1,
        ]);
    }

    $managerNip = '20021039';
    $managerName = 'Merry D. Anitasari';
    $managerHash = password_hash('ChangeMe123!', PASSWORD_ARGON2ID);

    $userStmt = $pdo->prepare(
        'INSERT INTO users (nip, full_name, password_hash, role, team, is_active)
         VALUES (:nip, :full_name, :password_hash, "Manager", "A", 1)
         ON DUPLICATE KEY UPDATE
           full_name = VALUES(full_name),
           password_hash = VALUES(password_hash),
           role = "Manager",
           is_active = 1'
    );
    $userStmt->execute([
        ':nip' => $managerNip,
        ':full_name' => $managerName,
        ':password_hash' => $managerHash,
    ]);

    // ── Bootstrap Admin Account ───────────────────────────────────────────
    $adminNip  = 'Khori';
    $adminName = 'Khori';
    $adminHash = password_hash('khori123', PASSWORD_ARGON2ID);

    $adminStmt = $pdo->prepare(
        'INSERT INTO users (nip, full_name, password_hash, role, team, is_active)
         VALUES (:nip, :full_name, :password_hash, "Admin", NULL, 1)
         ON DUPLICATE KEY UPDATE
           full_name     = VALUES(full_name),
           password_hash = VALUES(password_hash),
           role          = "Admin",
           team          = NULL,
           is_active     = 1'
    );
    $adminStmt->execute([
        ':nip'           => $adminNip,
        ':full_name'     => $adminName,
        ':password_hash' => $adminHash,
    ]);

    // ── Bootstrap Demo Accounts (one per role) ────────────────────────────
    $demoAccounts = [
        // [nip, full_name, password, role, team]
        ['SUP001',  'Budi Santoso',    'demo1234', 'Supervisor', 'A'],
        ['OPR001',  'Andi Firmansyah', 'demo1234', 'Operator',   'A'],
        ['OPR002',  'Sari Dewi',       'demo1234', 'Operator',   'B'],
        ['OPR003',  'Reza Pratama',    'demo1234', 'Operator',   'C'],
    ];

    $demoStmt = $pdo->prepare(
        'INSERT INTO users (nip, full_name, password_hash, role, team, is_active)
         VALUES (:nip, :full_name, :password_hash, :role, :team, 1)
         ON DUPLICATE KEY UPDATE
           full_name     = VALUES(full_name),
           password_hash = VALUES(password_hash),
           role          = VALUES(role),
           team          = VALUES(team),
           is_active     = 1'
    );
    foreach ($demoAccounts as [$dNip, $dName, $dPass, $dRole, $dTeam]) {
        $demoStmt->execute([
            ':nip'           => $dNip,
            ':full_name'     => $dName,
            ':password_hash' => password_hash($dPass, PASSWORD_ARGON2ID),
            ':role'          => $dRole,
            ':team'          => $dTeam,
        ]);
    }

    $pdo->commit();

    echo "Bootstrap setup berhasil.\n";
    echo "Manager NIP: {$managerNip}\n";
    echo "  Nama: {$managerName}\n";
    echo "  Password sementara: ChangeMe123!\n";
    echo "\n";
    echo "Admin NIP: {$adminNip}\n";
    echo "  Nama: {$adminName}\n";
    echo "  Password: khori123\n";
    echo "\n";
    echo "Demo Accounts (password: demo1234):\n";
    echo "  [Supervisor] NIP: SUP001 — Budi Santoso, Tim A\n";
    echo "  [Operator]   NIP: OPR001 — Andi Firmansyah, Tim A\n";
    echo "  [Operator]   NIP: OPR002 — Sari Dewi, Tim B\n";
    echo "  [Operator]   NIP: OPR003 — Reza Pratama, Tim C\n";
    echo "\nJumlah master area: " . count($areas) . "\n";
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo 'Bootstrap setup gagal: ' . $e->getMessage() . "\n";
}
