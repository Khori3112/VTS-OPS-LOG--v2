<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../middleware.php';

requireLogin();
requireRole('Manager');

require __DIR__ . '/../views/manager/manager_approval.php';
exit;
