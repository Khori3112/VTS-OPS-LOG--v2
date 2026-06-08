<?php

declare(strict_types=1);

function loadEnv(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $eqPos = strpos($line, '=');
        if ($eqPos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $eqPos));
        $val = trim(substr($line, $eqPos + 1));
        if (!isset($_ENV[$key]) && !isset($_SERVER[$key])) {
            $_ENV[$key] = $val;
            putenv("{$key}={$val}");
        }
    }
}

loadEnv(dirname(__DIR__) . '/.env');

function getPdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = (string) ($_ENV['DB_HOST'] ?? '127.0.0.1');
    $port = (string) ($_ENV['DB_PORT'] ?? '3306');
    $name = (string) ($_ENV['DB_NAME'] ?? 'vts_ops_log');
    $user = (string) ($_ENV['DB_USER'] ?? '');
    $pass = (string) ($_ENV['DB_PASS'] ?? '');

    if ($user === '') {
        _dbLogError('DB_USER tidak dikonfigurasi. Periksa file .env');
        _redirectTo503();
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (Throwable $e) {
        _dbLogError('PDO connection failed: ' . $e->getMessage());
        _redirectTo503();
    }

    return $pdo;
}

/** @internal — inline log writer used before helpers.php is loaded */
function _dbLogError(string $message): void
{
    $logFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log';
    $nip     = (string) ($_SESSION['user']['nip'] ?? 'UNAUTHENTICATED');
    $entry   = '[' . date('Y-m-d H:i:s T') . '] [DB] [' . $nip . '] ' . $message . PHP_EOL;
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

/** @internal — redirect to 503 page and halt execution */
function _redirectTo503(): never
{
    $base = defined('BASE_URL') ? BASE_URL : '/public/';
    header('Location: ' . $base . '503.php');
    exit;
}

