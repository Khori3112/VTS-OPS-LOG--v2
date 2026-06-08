<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../middleware.php';

requireLogin();

$role = (string) ($_SESSION['user']['role'] ?? '');

if ($role === 'Supervisor') {
    require __DIR__ . '/../views/supervisor/audit_logs.php';
    exit;
}

if ($role === 'Manager') {
    require __DIR__ . '/../views/manager/audit_logs.php';
    exit;
}

// Operator has no audit log access — redirect to dashboard
redirect('/public/dashboard.php');
