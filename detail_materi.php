<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) exit('ID tidak valid.');

$stmt = $conn->prepare(
    'SELECT m.*, mk.nama AS nama_mk
     FROM materi m JOIN mata_kuliah mk ON mk.id=m.mata_kuliah_id
     WHERE m.id=?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if (!$r) exit('Materi tidak ditemukan.');

layout_atas('Detail Materi');
?>

<p><strong>Judul:</strong> <?= e($r['judul']) ?></p>
<p><strong>Mata Kuliah:</strong> <?= e($r['nama_mk']) ?></p>
<p><strong>Isi:</strong></p>
<div><?= nl2br(e($r['isi'])) ?></div>

<?php if ($r['tautan'] !== ''): ?>
    <p><a href="<?= e($r['tautan']) ?>" target="_blank" rel="noopener noreferrer">Buka tautan materi</a></p>
<?php endif; ?>

<a class="btn btn-secondary" href="materi.php">Kembali</a>

<?php layout_bawah(); ?>