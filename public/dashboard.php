<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';

requireLogin();

$role = (string) ($_SESSION['user']['role'] ?? ($_SESSION['role'] ?? ''));

if ($role === 'Operator') {
    require __DIR__ . '/../views/operator/dashboard.php';
    exit;
}

if ($role === 'Supervisor') {
    require __DIR__ . '/../views/supervisor/dashboard.php';
    exit;
}

if ($role === 'Manager') {
    require __DIR__ . '/../views/manager/manager_approval.php';
    exit;
}

session_destroy();
redirect('/public/index.php');
