<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';

function currentShiftCategory(?DateTimeImmutable $now = null): string
{
    $now = $now ?? new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
    $time = $now->format('H:i:s');

    if ($time >= '08:00:00' && $time < '14:00:00') {
        return 'Pagi';
    }

    if ($time >= '14:00:00' && $time < '20:00:00') {
        return 'Siang';
    }

    return 'Malam';
}

function operationalShiftDate(?DateTimeImmutable $now = null): string
{
    $now = $now ?? new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
    $time = $now->format('H:i:s');

    if ($time < '08:00:00') {
        return $now->sub(new DateInterval('P1D'))->format('Y-m-d');
    }

    return $now->format('Y-m-d');
}

function requireLogin(): void
{
    if (empty($_SESSION['user'])) {
        redirect('/public/index.php');
    }
}

function userCanWriteNow(array $user): bool
{
    if (($user['role'] ?? '') !== 'Operator') {
        return true;
    }

    $activeShift = currentShiftCategory();
    $loginShift = $user['login_shift_category'] ?? '';

    return $activeShift === $loginShift;
}

function hasActiveEditAccess(PDO $pdo, int $dailyShiftReportId, string $nip): bool
{
    $stmt = $pdo->prepare(
        'SELECT id
         FROM edit_requests
         WHERE table_name = :table_name
           AND record_id = :record_id
           AND requested_by_nip = :requested_by_nip
           AND status = "approved"
           AND unlocked_until IS NOT NULL
           AND unlocked_until >= NOW()
         ORDER BY id DESC
         LIMIT 1'
    );
    $stmt->execute([
        ':table_name' => 'daily_shift_reports',
        ':record_id' => $dailyShiftReportId,
        ':requested_by_nip' => $nip,
    ]);

    return (bool) $stmt->fetch();
}

function getActiveEditAccessUntil(PDO $pdo, int $dailyShiftReportId, string $nip): ?string
{
    $stmt = $pdo->prepare(
        'SELECT unlocked_until
         FROM edit_requests
         WHERE table_name = :table_name
           AND record_id = :record_id
           AND requested_by_nip = :requested_by_nip
           AND status = "approved"
           AND unlocked_until IS NOT NULL
           AND unlocked_until >= NOW()
         ORDER BY id DESC
         LIMIT 1'
    );
    $stmt->execute([
        ':table_name' => 'daily_shift_reports',
        ':record_id' => $dailyShiftReportId,
        ':requested_by_nip' => $nip,
    ]);

    $row = $stmt->fetch();
    if (!$row || empty($row['unlocked_until'])) {
        return null;
    }

    return (string) $row['unlocked_until'];
}

function canWriteShiftRecord(PDO $pdo, array $user, int $dailyShiftReportId, ?string &$errorMessage = null): bool
{
    if (!userCanWriteNow($user)) {
        $errorMessage = 'Penulisan data ditolak. Di luar jam shift aktif operator.';
        return false;
    }

    $stmt = $pdo->prepare('SELECT id, is_locked, final_copy_watermark FROM daily_shift_reports WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $dailyShiftReportId]);
    $row = $stmt->fetch();

    if (!$row) {
        $errorMessage = 'Shift report tidak ditemukan.';
        return false;
    }

    if ((int) ($row['final_copy_watermark'] ?? 0) === 1) {
        $errorMessage = 'Laporan sudah di-acknowledge Manager dan terkunci permanen.';
        return false;
    }

    if ((int) $row['is_locked'] === 1 && !hasActiveEditAccess($pdo, $dailyShiftReportId, (string) $user['nip'])) {
        $errorMessage = 'Shift report sudah terkunci. Gunakan Minta Izin Edit ke supervisor.';
        return false;
    }

    return true;
}

function upsertDailyShiftReport(PDO $pdo, array $user, string $shiftDate, string $shiftCategory): int
{
    $stmt = $pdo->prepare(
        'SELECT id FROM daily_shift_reports WHERE shift_date = :shift_date AND shift_category = :shift_category AND team = :team LIMIT 1'
    );
    $stmt->execute([
        ':shift_date' => $shiftDate,
        ':shift_category' => $shiftCategory,
        ':team' => $user['team'],
    ]);

    $found = $stmt->fetch();
    if ($found) {
        return (int) $found['id'];
    }

    $insert = $pdo->prepare(
        'INSERT INTO daily_shift_reports (shift_date, shift_category, team, operator_nip, supervisor_nip, manager_nip)
         VALUES (:shift_date, :shift_category, :team, :operator_nip,
           (SELECT nip FROM users WHERE role = "Supervisor" ORDER BY nip ASC LIMIT 1),
           (SELECT nip FROM users WHERE role = "Manager" ORDER BY nip ASC LIMIT 1)
         )'
    );

    $insert->execute([
        ':shift_date' => $shiftDate,
        ':shift_category' => $shiftCategory,
        ':team' => $user['team'],
        ':operator_nip' => $user['nip'],
    ]);

    return (int) $pdo->lastInsertId();
}

function logAudit(PDO $pdo, string $actorNip, string $actionType, ?string $tableName, ?int $recordId, ?array $newData, string $description): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO audit_logs (actor_nip, action_type, table_name, record_id, old_data, new_data, description, logged_at)
         VALUES (:actor_nip, :action_type, :table_name, :record_id, :old_data, :new_data, :description, NOW())'
    );

    $stmt->execute([
        ':actor_nip' => $actorNip,
        ':action_type' => $actionType,
        ':table_name' => $tableName,
        ':record_id' => $recordId,
        ':old_data' => null,
        ':new_data' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
        ':description' => $description,
    ]);
}

// ---------------------------------------------------------------------------
// Shift schedule — 5-team rotating system, reference: April 1, 2026 = cycle day 0
// Cycle: A→B→C→D→E rotating through Pagi/Siang/Malam each day
// Day 0: Pagi=A, Siang=B, Malam=C | Day 1: Pagi=B, Siang=C, Malam=D | ...
// ---------------------------------------------------------------------------

function getTeamForShift(string $date, string $shiftCategory): string
{
    $teams    = ['A', 'B', 'C', 'D', 'E'];
    $offsets  = ['Pagi' => 0, 'Siang' => 1, 'Malam' => 2];
    $shiftOff = $offsets[$shiftCategory] ?? 0;

    $ref  = new DateTimeImmutable('2026-04-01', new DateTimeZone('Asia/Jakarta'));
    $day  = new DateTimeImmutable($date, new DateTimeZone('Asia/Jakarta'));
    $diff = (int) $ref->diff($day)->days * ($day >= $ref ? 1 : -1);
    $idx  = (($diff % 5) + 5) % 5;   // ensure non-negative

    return $teams[($idx + $shiftOff) % 5];
}

function getShiftForTeam(string $date, string $team): string
{
    foreach (['Pagi', 'Siang', 'Malam'] as $category) {
        if (getTeamForShift($date, $category) === $team) {
            return $category;
        }
    }
    return 'Libur';
}

function currentShiftTeam(): string
{
    return getTeamForShift(operationalShiftDate(), currentShiftCategory());
}

/**
 * Append a structured entry to logs/error.log.
 * Safe to call from anywhere — errors are silently swallowed.
 */
function logError(string $message): void
{
    $nip     = (string) ($_SESSION['user']['nip'] ?? 'UNAUTHENTICATED');
    $logFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log';
    $entry   = '[' . date('Y-m-d H:i:s T') . '] [' . $nip . '] ' . $message . PHP_EOL;
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

// ---------------------------------------------------------------------------
// Shift info snapshot — used by dashboard, PDF header, and supervisor widgets
// ---------------------------------------------------------------------------

/**
 * Returns an associative array describing the shift currently active.
 *
 * Keys returned:
 *   shift_category  – 'Pagi' | 'Siang' | 'Malam'
 *   shift_date      – operational date string 'Y-m-d'
 *   team            – active team letter 'A'–'E'
 *   shift_start     – clock start 'HH:MM'
 *   shift_end       – clock end   'HH:MM'
 *   supervisor_name – full name from DB, or null if unavailable
 *   supervisor_nip  – NIP from DB,       or null if unavailable
 *   next_shift      – 'Pagi' | 'Siang' | 'Malam' (what follows)
 *   next_team       – team letter for the next shift
 */
function getCurrentShiftInfo(?PDO $pdo = null): array
{
    $shiftCat  = currentShiftCategory();
    $shiftDate = operationalShiftDate();
    $team      = getTeamForShift($shiftDate, $shiftCat);

    $timeBounds = [
        'Pagi'  => ['start' => '08:00', 'end' => '14:00'],
        'Siang' => ['start' => '14:00', 'end' => '20:00'],
        'Malam' => ['start' => '20:00', 'end' => '08:00'],
    ];
    $nextShiftMap = ['Pagi' => 'Siang', 'Siang' => 'Malam', 'Malam' => 'Pagi'];
    $nextShift    = $nextShiftMap[$shiftCat];

    // Next shift date: Malam → next calendar day for Pagi
    $nextShiftDate = ($shiftCat === 'Malam')
        ? (new DateTimeImmutable($shiftDate, new DateTimeZone('Asia/Jakarta')))->modify('+1 day')->format('Y-m-d')
        : $shiftDate;
    $nextTeam = getTeamForShift($nextShiftDate, $nextShift);

    $result = [
        'shift_category'  => $shiftCat,
        'shift_date'      => $shiftDate,
        'team'            => $team,
        'shift_start'     => $timeBounds[$shiftCat]['start'],
        'shift_end'       => $timeBounds[$shiftCat]['end'],
        'supervisor_name' => null,
        'supervisor_nip'  => null,
        'next_shift'      => $nextShift,
        'next_team'       => $nextTeam,
    ];

    if ($pdo !== null) {
        $stmt = $pdo->prepare(
            'SELECT nip, full_name
               FROM users
              WHERE role = "Supervisor"
                AND team = :team
                AND is_active = 1
              LIMIT 1'
        );
        $stmt->execute([':team' => $team]);
        $sup = $stmt->fetch();
        if ($sup) {
            $result['supervisor_name'] = (string) $sup['full_name'];
            $result['supervisor_nip']  = (string) $sup['nip'];
        }
    }

    return $result;
}
