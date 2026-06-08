<?php

declare(strict_types=1);

require_once __DIR__ . '/config/app.php';

function requireRole(string $requiredRole): void
{
    if (empty($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
        http_response_code(403);
        die('Access Denied');
    }
}

function requireAnyRole(array $roles): void
{
    if (empty($_SESSION['role']) || !in_array((string) $_SESSION['role'], $roles, true)) {
        http_response_code(403);
        die('Access Denied');
    }
}
