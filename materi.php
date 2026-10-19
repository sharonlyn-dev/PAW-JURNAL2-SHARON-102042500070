<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validasi_token();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        flash('ID materi tidak valid.', 'error');
    } else {
        $stmt = $conn->prepare('DELETE FROM materi WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        flash($stmt->affected_rows ? 'Materi dihapus.' : 'Materi tidak ditemukan.',
            $stmt->affected_rows ? 'ok' : 'error');
    }

    header('Location: materi.php');
    exit;
}

$q = trim($_GET['q'] ?? '');
$mkId = filter_input(INPUT_GET, 'mata_kuliah_id', FILTER_VALIDATE_INT);
$mkId = $mkId && $mkId > 0 ? $mkId : 0;

$mata = $conn->query('SELECT id, nama FROM mata_kuliah ORDER BY nama');

$sql = 'SELECT m.id, m.judul, m.isi, m.tautan, mk.nama AS nama_mk
        FROM materi m JOIN mata_kuliah mk ON mk.id=m.mata_kuliah_id
        WHERE m.judul LIKE ?';
if ($mkId) $sql .= ' AND m.mata_kuliah_id=?';
$sql .= ' ORDER BY m.judul';

$stmt = $conn->prepare($sql);
$cari = "%$q%";
if ($mkId) $stmt->bind_param('si', $cari, $mkId);
else $stmt->bind_param('s', $cari);
$stmt->execute();
$data = $stmt->get_result();

layout_atas('Materi');
?>

<a class="btn" href="form_materi.php">+ Tambah Materi</a>

<form method="get">
    <label>Cari judul</label>
    <input name="q" value="<?= e($q) ?>" placeholder="Cari materi">

    <label>Filter mata kuliah</label>
    <select name="mata_kuliah_id">
        <option value="0">Semua mata kuliah</option>
        <?php while ($m = $mata->fetch_assoc()): ?>
            <option value="<?= (int)$m['id'] ?>" <?= $mkId == $m['id'] ? 'selected' : '' ?>>
                <?= e($m['nama']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button>Cari</button>
    <a class="btn btn-secondary" href="materi.php">Reset</a>
</form>

<?php if (!$data->num_rows): ?>
    <p>Materi tidak ditemukan.</p>
<?php else: ?>
<table>
<tr><th>Judul</th><th>Mata Kuliah</th><th>Aksi</th></tr>
<?php while ($r = $data->fetch_assoc()): ?>
<tr>
    <td><?= e($r['judul']) ?></td>
    <td><?= e($r['nama_mk']) ?></td>
    <td>
        <div class="actions">
            <a class="btn" href="detail_materi.php?id=<?= (int)$r['id'] ?>">Detail</a>
            <a class="btn btn-secondary" href="form_materi.php?id=<?= (int)$r['id'] ?>">Edit</a>
            <form method="post" onsubmit="return confirm('Hapus materi ini?')">
                <input type="hidden" name="csrf" value="<?= e(token()) ?>">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn-danger">Hapus</button>
            </form>
        </div>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>

<?php layout_bawah(); ?>