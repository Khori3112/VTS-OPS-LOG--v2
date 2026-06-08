<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

// ---------------------------------------------------------------------------
// Auto-detect base path — Sovereign Engine v5.1
// BASE_PATH  = project root without trailing slash  e.g. '/VTS-OPS-LOG'
// BASE_APP   = project root with trailing slash     e.g. '/VTS-OPS-LOG/'
// BASE_URL   = public folder URL                    e.g. '/VTS-OPS-LOG/public/'
// ---------------------------------------------------------------------------
if (!defined('BASE_PATH')) {
    if (PHP_SAPI === 'cli') {
        define('BASE_PATH', '');
        define('BASE_APP',  '/');
        define('BASE_URL',  '/public/');
    } else {
        // Derive base path from SCRIPT_NAME (URL) vs the physical file path.
        // Works even with XAMPP junctions across drives (e.g. C: htdocs → D: project).
        //
        // Strategy: strip the script's path-relative-to-app-root from SCRIPT_NAME URL.
        // e.g. SCRIPT_NAME = /VTS-OPS-LOG/public/login.php
        //      script rel  = public/login.php  (relative path inside app root)
        //   → base URL     = /VTS-OPS-LOG/

        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptFile = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_FILENAME'] ?? ''));
        $appRoot    = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));

        $baseDir = '';
        if ($scriptName !== '' && $scriptFile !== '' && $appRoot !== '') {
            // Normalize both to forward slashes, get the file's path within app root
            $scriptFileNorm = str_replace('\\', '/', (string) realpath($scriptFile));
            if (str_starts_with($scriptFileNorm, $appRoot . '/')) {
                $relPath = substr($scriptFileNorm, strlen($appRoot) + 1); // e.g. "public/login.php"
                // Strip relPath from the end of SCRIPT_NAME to get base
                if (str_ends_with($scriptName, '/' . $relPath)) {
                    $baseDir = substr($scriptName, 0, strlen($scriptName) - strlen('/' . $relPath));
                }
            }
        }

        // Fallback: try DOCUMENT_ROOT (works when on the same drive)
        if ($baseDir === '') {
            $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
            if ($docRoot !== '' && str_starts_with($appRoot, $docRoot)) {
                $baseDir = substr($appRoot, strlen($docRoot));
            }
        }

        $baseDir = rtrim($baseDir, '/');

        // ------------------------------------------------------------------
        // Manual override via .env: add APP_BASE_URL=/VTS-OPS-LOG
        // Use when XAMPP junction, Docker, or reverse-proxy breaks auto-detect.
        // ------------------------------------------------------------------
        $_envOverridePath = __DIR__ . '/../.env';
        if (is_readable($_envOverridePath)) {
            foreach (file($_envOverridePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $_envLine) {
                if (preg_match('/^APP_BASE_URL\s*=\s*(.*)$/', trim((string) $_envLine), $_envMatch)) {
                    $baseDir = rtrim(trim($_envMatch[1]), '/');
                    break;
                }
            }
            unset($_envLine, $_envMatch);
        }
        unset($_envOverridePath);

        // ------------------------------------------------------------------
        // Special case: PHP built-in server launched with  -t public
        // e.g.  php -S localhost:8080 -t public
        // DOCUMENT_ROOT becomes the project's public/ subfolder, so
        // BASE_URL must be '/' (no '/public/' prefix) to avoid double-prefix.
        // ------------------------------------------------------------------
        $_pubReal     = rtrim(str_replace('\\', '/', (string)(realpath($appRoot . '/public') ?: '')), '/');
        $_docRootReal = rtrim(str_replace('\\', '/', (string)(realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '')), '/');
        $_serverRootIsPublic = ($baseDir === '' && $_pubReal !== '' && $_docRootReal === $_pubReal);
        unset($_pubReal, $_docRootReal);

        if ($_serverRootIsPublic) {
            define('BASE_PATH', '');
            define('BASE_APP',  '/');
            define('BASE_URL',  '/');
        } else {
            define('BASE_PATH', $baseDir);               // e.g. '/VTS-OPS-LOG'
            define('BASE_APP',  $baseDir . '/');          // e.g. '/VTS-OPS-LOG/'
            define('BASE_URL',  $baseDir . '/public/');   // e.g. '/VTS-OPS-LOG/public/'
        }
        unset($_serverRootIsPublic);
    }
}

/**
 * Redirect helper — automatically prepends BASE_PATH so paths work in XAMPP subfolders.
 * Pass a path beginning with '/', e.g. redirect('/public/dashboard.php')
 */
function redirect(string $path, int $code = 302): never
{
    // When the PHP built-in server runs with -t public, BASE_URL='/' and
    // BASE_PATH=''. Paths hard-coded as '/public/...' inside action files must
    // have the '/public' prefix stripped so the redirect lands correctly.
    if (BASE_URL === '/' && BASE_PATH === '' && str_starts_with($path, '/public/')) {
        $path = substr($path, strlen('/public'));
    }
    http_response_code($code);
    header('Location: ' . BASE_PATH . $path);
    exit;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfOrDie(string $redirectPath): void
{
    $postedToken  = (string) ($_POST['csrf_token'] ?? '');
    $sessionToken = csrfToken();

    if ($postedToken === '' || !hash_equals($sessionToken, $postedToken)) {
        $_SESSION['flash_error'] = 'Token keamanan tidak valid. Silakan coba lagi.';
        redirect($redirectPath);
    }
}
