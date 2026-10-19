<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

$jumlah = [];

foreach (['mata_kuliah', 'materi', 'tugas'] as $tabel) {
    $result = $conn->query("SELECT COUNT(*) AS total FROM `$tabel`");
    $jumlah[$tabel] = (int)$result->fetch_assoc()['total'];
}

layout_atas('Beranda');
?>

<section class="hero">
    <h2>Selamat Datang di LMS PAW</h2>
    <p>Kelola mata kuliah, materi pembelajaran, dan tugas dalam satu tempat.</p>
</section>

<div class="ringkas">
    <div>Jumlah Mata Kuliah<strong><?= $jumlah['mata_kuliah'] ?></strong></div>
    <div>Jumlah Materi<strong><?= $jumlah['materi'] ?></strong></div>
    <div>Jumlah Tugas<strong><?= $jumlah['tugas'] ?></strong></div>
</div>

<h2>Menu Pembelajaran</h2>
<div class="menu-grid">
    <a class="menu-card" href="mata_kuliah.php">
        <h3>Mata Kuliah</h3>
        <p>Tambah, cari, lihat, dan edit mata kuliah.</p>
    </a>
    <a class="menu-card" href="materi.php">
        <h3>Materi</h3>
        <p>Kelola materi pembelajaran.</p>
    </a>
    <a class="menu-card" href="tugas.php">
        <h3>Tugas</h3>
        <p>Kelola instruksi dan tenggat tugas.</p>
    </a>
</div>

<?php layout_bawah(); ?>