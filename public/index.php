<?php declare(strict_types=1);
require_once __DIR__ . '/../config/app.php';
if (!empty($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}
require __DIR__ . '/home.php';
exit;
