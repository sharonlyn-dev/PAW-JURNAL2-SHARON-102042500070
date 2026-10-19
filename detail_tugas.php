<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) exit('ID tidak valid.');

$stmt = $conn->prepare(
    'SELECT t.*, mk.nama AS nama_mk
     FROM tugas t JOIN mata_kuliah mk ON mk.id=t.mata_kuliah_id
     WHERE t.id=?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if (!$r) exit('Tugas tidak ditemukan.');

layout_atas('Detail Tugas');
?>

<p><strong>Judul:</strong> <?= e($r['judul']) ?></p>
<p><strong>Mata Kuliah:</strong> <?= e($r['nama_mk']) ?></p>
<p><strong>Instruksi:</strong></p>
<div><?= nl2br(e($r['instruksi'])) ?></div>
<p><strong>Tenggat:</strong> <?= e($r['tenggat']) ?></p>
<p><strong>Status:</strong> <?= e($r['status']) ?></p>

<a class="btn btn-secondary" href="tugas.php">Kembali</a>

<?php layout_bawah(); ?>