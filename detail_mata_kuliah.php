<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID tidak valid.');
}

$stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$mk = $stmt->get_result()->fetch_assoc();

if (!$mk) {
    http_response_code(404);
    exit('Mata kuliah tidak ditemukan.');
}

$stmt = $conn->prepare('SELECT id, judul FROM materi WHERE mata_kuliah_id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$materi = $stmt->get_result();

$stmt = $conn->prepare('SELECT id, judul, tenggat, status FROM tugas WHERE mata_kuliah_id=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$tugas = $stmt->get_result();

layout_atas('Detail Mata Kuliah');
?>

<p><strong>Kode:</strong> <?= e($mk['kode']) ?></p>
<p><strong>Nama:</strong> <?= e($mk['nama']) ?></p>
<p><strong>Dosen:</strong> <?= e($mk['dosen']) ?></p>
<p><strong>Semester:</strong> <?= e($mk['semester']) ?></p>

<h2>Materi terkait</h2>
<ul>
<?php while ($m = $materi->fetch_assoc()): ?>
    <li><a href="detail_materi.php?id=<?= (int)$m['id'] ?>"><?= e($m['judul']) ?></a></li>
<?php endwhile; ?>
</ul>

<h2>Tugas terkait</h2>
<ul>
<?php while ($t = $tugas->fetch_assoc()): ?>
    <li><a href="detail_tugas.php?id=<?= (int)$t['id'] ?>"><?= e($t['judul']) ?></a> — <?= e($t['tenggat']) ?> (<?= e($t['status']) ?>)</li>
<?php endwhile; ?>
</ul>

<a class="btn btn-secondary" href="mata_kuliah.php">Kembali</a>

<?php layout_bawah(); ?>