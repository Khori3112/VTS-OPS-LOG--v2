<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/helpers.php';
require_once __DIR__ . '/../../middleware.php';
require_once __DIR__ . '/../partials/layout.php';

requireLogin();
requireRole('Manager');

$user = $_SESSION['user'];
$pdo = getPdo();
$stmt = $pdo->prepare(
    'SELECT al.logged_at, al.action_type, al.table_name, al.record_id, al.old_data, al.new_data, al.description,
            u.full_name, u.nip
     FROM audit_logs al
     LEFT JOIN users u ON u.nip = al.actor_nip
     ORDER BY al.logged_at DESC LIMIT 300'
);
$stmt->execute();
$rows = $stmt->fetchAll();

$fmt = static function (?string $json): string {
    if ($json === null || $json === '') return '-';
    $data = json_decode($json, true);
    if (!is_array($data)) return (string) $json;
    return (string) json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
};

ob_start();
?>
<div class="card">
    <h2>Audit History</h2>
    <div class="table-wrap table-sticky">
        <table>
            <thead><tr><th>Waktu</th><th>User</th><th>Aktivitas</th><th>Objek</th><th>Detail</th><th>Old</th><th>New</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?><tr><td colspan="7">Belum ada audit.</td></tr><?php else: foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $r['logged_at']) ?></td>
                    <td><?= htmlspecialchars((string) ($r['full_name'] ?? $r['nip'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string) $r['action_type']) ?></td>
                    <td><?= htmlspecialchars((string) (($r['table_name'] ?? '-') . '#' . ($r['record_id'] ?? '-'))) ?></td>
                    <td><?= htmlspecialchars((string) ($r['description'] ?? '-')) ?></td>
                    <td><pre class="json-pre"><?= htmlspecialchars($fmt($r['old_data'])) ?></pre></td>
                    <td><pre class="json-pre"><?= htmlspecialchars($fmt($r['new_data'])) ?></pre></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
renderLayout('Audit History Manager', $user, (string) ob_get_clean());
