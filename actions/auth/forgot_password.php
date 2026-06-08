<?php
// actions/auth/forgot_password.php — Sovereign Engine v5.1
// Records a password-reset request into edit_requests.
// Called via AJAX from login.php — always returns JSON.
declare(strict_types=1);
require_once __DIR__ . '/../../config/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
    exit;
}

// Soft CSRF check (AJAX — token may be absent for non-JS fallback; treat leniently)
$postedToken  = (string) ($_POST['csrf_token'] ?? '');
$sessionToken = (string) ($_SESSION['csrf_token'] ?? '');
if ($postedToken !== '' && $sessionToken !== '' && !hash_equals($sessionToken, $postedToken)) {
    echo json_encode(['ok' => false, 'msg' => 'Invalid CSRF token']);
    exit;
}

$nip = trim($_POST['nip'] ?? '');
if ($nip === '' || strlen($nip) > 30) {
    echo json_encode(['ok' => false, 'msg' => 'NIP tidak valid']);
    exit;
}

try {
    $pdo = getPdo();

    // Verify the NIP exists — don't reveal whether it does in the response (same response either way)
    $exists = $pdo->prepare('SELECT COUNT(*) FROM users WHERE nip = :nip');
    $exists->execute([':nip' => $nip]);
    $found = (int) $exists->fetchColumn() > 0;

    if ($found) {
        // Insert reset request (table_name='password_reset', record_id=0 as sentinel)
        // Ignore duplicate pending requests from the same NIP within the last hour
        $dup = $pdo->prepare(
            "SELECT COUNT(*) FROM edit_requests
             WHERE table_name = 'password_reset'
               AND requested_by_nip = :nip
               AND status = 'pending'
               AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        );
        $dup->execute([':nip' => $nip]);

        if ((int) $dup->fetchColumn() === 0) {
            $pdo->prepare(
                "INSERT INTO edit_requests
                   (table_name, record_id, requested_by_nip, reason, status, created_at, updated_at)
                 VALUES ('password_reset', 0, :nip, 'Forgot password request from login page', 'pending', NOW(), NOW())"
            )->execute([':nip' => $nip]);
        }
    }

    // Always return success — do not enumerate valid NIPs
    echo json_encode(['ok' => true]);

} catch (Throwable $e) {
    logError('forgot_password: ' . $e->getMessage());
    // Still return ok=true so attacker cannot infer server state
    echo json_encode(['ok' => true]);
}
