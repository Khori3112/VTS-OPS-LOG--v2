<?php
declare(strict_types=1);
http_response_code(404);
$basePath = '';
// Walk up to find BASE_PATH — needed only for logo src, non-critical if unavailable
$appCfg = __DIR__ . '/../config/app.php';
if (is_file($appCfg)) {
    require_once $appCfg;
    $basePath = defined('BASE_URL') ? BASE_URL : '';
}
?><!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>404 — Halaman Tidak Ditemukan | Maritime Command</title>
    <script src="<?= htmlspecialchars($basePath) ?>assets/tailwind.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700;800&display=swap" rel="stylesheet"/>
    <style>
        *,::after,::before{box-sizing:border-box}
        body{margin:0;font-family:'Inter',sans-serif;background:#0d1117;color:#e4e1e7;min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden}
        .glass{background:rgba(14,14,22,.82);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.07);border-radius:1.25rem;padding:3rem 3.5rem;text-align:center;max-width:480px;width:100%;box-shadow:0 25px 60px rgba(0,0,0,.6)}
        .code{font-size:6rem;font-weight:900;background:linear-gradient(135deg,#FECB00 0%,#ae9500 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1;letter-spacing:-.02em}
        .label{font-size:.65rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:#64748b;margin:.75rem 0 .25rem}
        .title{font-size:1.15rem;font-weight:700;color:#e4e1e7;margin-bottom:.65rem}
        .desc{font-size:.82rem;color:#64748b;line-height:1.6;margin-bottom:2rem}
        .btn{display:inline-flex;align-items:center;gap:.4rem;background:linear-gradient(135deg,#AEC7F6 0%,#708AB5 100%);color:#001E40;font-weight:700;font-size:.78rem;padding:.55rem 1.4rem;border-radius:.5rem;text-decoration:none;transition:filter .15s}
        .btn:hover{filter:brightness(1.1)}
        .naval-grid{position:fixed;inset:0;background-image:radial-gradient(rgba(174,199,246,.035) 1px,transparent 1px);background-size:32px 32px;pointer-events:none;z-index:-1}
        .glow-dot{position:fixed;pointer-events:none;z-index:-1;border-radius:50%;filter:blur(80px)}
        .gd-1{width:320px;height:320px;background:rgba(254,203,0,.05);top:-80px;left:-80px}
        .gd-2{width:400px;height:400px;background:rgba(0,30,64,.3);bottom:-100px;right:-100px}
        .badge{display:inline-block;font-size:.6rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;padding:.25rem .75rem;border:1px solid rgba(254,203,0,.3);color:#FECB00;border-radius:9999px;margin-bottom:1rem}
    </style>
</head>
<body>
    <div class="naval-grid"></div>
    <div class="glow-dot gd-1"></div>
    <div class="glow-dot gd-2"></div>
    <div class="glass">
        <img src="<?= htmlspecialchars($basePath) ?>assets/img/logo_navigasi.png"
             alt="VTS Logo" style="height:48px;width:48px;object-fit:contain;margin:0 auto 1.25rem;display:block;opacity:.85"
             onerror="this.style.display='none'">
        <div class="badge">Maritime Command v2.4</div>
        <div class="code">404</div>
        <div class="label">Error</div>
        <h1 class="title">Halaman Tidak Ditemukan</h1>
        <p class="desc">Halaman yang Anda cari tidak tersedia atau telah dipindahkan.<br>
            Pastikan URL yang Anda akses sudah benar.</p>
        <a href="<?= htmlspecialchars($basePath) ?>public/dashboard.php" class="btn">
            ← Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
