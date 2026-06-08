<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../middleware.php';

requireLogin();
requireAnyRole(['Operator', 'Supervisor']);

require_once __DIR__ . '/../views/operator/historical_entry.php';
