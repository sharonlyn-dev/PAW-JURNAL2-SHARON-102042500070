<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validasi_token(): void {
    if (
        $_SERVER['REQUEST_METHOD'] !== 'POST' ||
        !isset($_POST['csrf']) ||
        !hash_equals(token(), (string)$_POST['csrf'])
    ) {
        http_response_code(403);
        exit('Permintaan tidak valid. Silakan kembali dan coba lagi.');
    }
}

function flash($pesan, $jenis = 'ok'): void {
    $_SESSION['flash'] = [$pesan, $jenis];
}

function layout_atas($judul): void {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> | LMS PAW</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body>
<div class="site-wrap">
    <header class="site-header">
        <a class="brand" href="index.php">
            <span class="brand-mark">L</span>
            <span><strong>LMS PAW</strong><small>Learning Management System</small></span>
        </a>
        <nav class="main-nav">
            <a href="index.php">Beranda</a>
            <a href="mata_kuliah.php">Mata Kuliah</a>
            <a href="materi.php">Materi</a>
            <a href="tugas.php">Tugas</a>
        </nav>
    </header>

    <main class="main-content">
        <h1><?= e($judul) ?></h1>
        <?php if (!empty($_SESSION['flash'])):
            [$pesan, $jenis] = $_SESSION['flash'];
            unset($_SESSION['flash']);
        ?>
            <div class="alert <?= e($jenis) ?>"><?= e($pesan) ?></div>
        <?php endif; ?>
<?php
}

function layout_bawah(): void {
?>
    </main>
    <footer class="site-footer">
        LMS PAW — Praktikum Pemrograman Aplikasi Web
    </footer>
</div>
</body>
</html>
<?php
}