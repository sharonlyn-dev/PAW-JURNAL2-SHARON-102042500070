<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validasi_token();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        flash('ID mata kuliah tidak valid.', 'error');
    } else {
        $stmt = $conn->prepare(
            'DELETE FROM mata_kuliah WHERE id = ?'
        );
        $stmt->bind_param('i', $id);

        try {
            $stmt->execute();
            flash($stmt->affected_rows ? 'Mata kuliah berhasil dihapus.' : 'Data tidak ditemukan.',
                $stmt->affected_rows ? 'ok' : 'error');
        } catch (mysqli_sql_exception $e) {
            flash('Mata kuliah masih digunakan oleh materi atau tugas.', 'error');
        }
    }

    header('Location: mata_kuliah.php');
    exit;
}

$q = trim($_GET['q'] ?? '');
$stmt = $conn->prepare(
    'SELECT id, kode, nama, dosen, semester
     FROM mata_kuliah
     WHERE kode LIKE ? OR nama LIKE ?
     ORDER BY nama'
);
$cari = "%$q%";
$stmt->bind_param('ss', $cari, $cari);
$stmt->execute();
$data = $stmt->get_result();

layout_atas('Mata Kuliah');
?>

<a class="btn" href="form_mata_kuliah.php">+ Tambah Mata Kuliah</a>

<form method="get">
    <label for="q">Cari kode atau nama mata kuliah</label>
    <input id="q" name="q" value="<?= e($q) ?>" placeholder="Masukkan kata kunci">
    <button type="submit">Cari</button>
    <a class="btn btn-secondary" href="mata_kuliah.php">Reset</a>
</form>

<?php if ($data->num_rows === 0): ?>
    <p>Data mata kuliah tidak ditemukan.</p>
<?php else: ?>
<table>
    <thead>
        <tr><th>Kode</th><th>Nama</th><th>Dosen</th><th>Semester</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php while ($row = $data->fetch_assoc()): ?>
        <tr>
            <td><?= e($row['kode']) ?></td>
            <td><?= e($row['nama']) ?></td>
            <td><?= e($row['dosen']) ?></td>
            <td><?= e($row['semester']) ?></td>
            <td>
                <div class="actions">
                    <a class="btn" href="detail_mata_kuliah.php?id=<?= (int)$row['id'] ?>">Detail</a>
                    <a class="btn btn-secondary" href="form_mata_kuliah.php?id=<?= (int)$row['id'] ?>">Edit</a>
                    <form method="post" onsubmit="return confirm('Hapus mata kuliah ini?')">
                        <input type="hidden" name="csrf" value="<?= e(token()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                        <button class="btn-danger" type="submit">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>

<?php layout_bawah(); ?>