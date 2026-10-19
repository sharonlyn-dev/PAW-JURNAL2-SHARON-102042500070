<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$edit = $id && $id > 0;
$data = ['mata_kuliah_id'=>'', 'judul'=>'', 'isi'=>'', 'tautan'=>''];

if ($edit) {
    $stmt = $conn->prepare('SELECT * FROM materi WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) exit('Materi tidak ditemukan.');
    $data = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validasi_token();
    $idPost = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $editPost = $idPost && $idPost > 0;
    $mk = filter_input(INPUT_POST, 'mata_kuliah_id', FILTER_VALIDATE_INT);
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tautan = trim($_POST['tautan'] ?? '');

    if (!$mk || $judul === '' || mb_strlen($judul) > 150 ||
        $isi === '' || strlen($tautan) > 255 ||
        ($tautan !== '' && !filter_var($tautan, FILTER_VALIDATE_URL))) {
        flash('Periksa isian. Tautan harus berupa URL yang valid.', 'error');
    } else {
        try {
            if ($editPost) {
                $stmt = $conn->prepare('UPDATE materi SET mata_kuliah_id=?,judul=?,isi=?,tautan=? WHERE id=?');
                $stmt->bind_param('isssi', $mk, $judul, $isi, $tautan, $idPost);
                $stmt->execute();
                flash($stmt->affected_rows ? 'Materi diperbarui.' : 'Tidak ada perubahan.');
            } else {
                $stmt = $conn->prepare('INSERT INTO materi (mata_kuliah_id,judul,isi,tautan) VALUES (?,?,?,?)');
                $stmt->bind_param('isss', $mk, $judul, $isi, $tautan);
                $stmt->execute();
                flash('Materi berhasil ditambahkan.');
            }
            header('Location: materi.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            flash('Gagal menyimpan materi. Periksa mata kuliah.', 'error');
        }
    }
    $data = compact('mk', 'judul', 'isi', 'tautan');
    $data['mata_kuliah_id'] = $mk;
}

$mata = $conn->query('SELECT id,nama FROM mata_kuliah ORDER BY nama');
layout_atas($edit ? 'Edit Materi' : 'Tambah Materi');
?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(token()) ?>">
    <input type="hidden" name="id" value="<?= $edit ? (int)$id : '' ?>">

    <label>Mata Kuliah</label>
    <select name="mata_kuliah_id" required>
        <option value="">Pilih mata kuliah</option>
        <?php while ($m = $mata->fetch_assoc()): ?>
        <option value="<?= (int)$m['id'] ?>" <?= $data['mata_kuliah_id'] == $m['id'] ? 'selected' : '' ?>>
            <?= e($m['nama']) ?>
        </option>
        <?php endwhile; ?>
    </select>

    <label>Judul Materi</label>
    <input name="judul" maxlength="150" required value="<?= e($data['judul']) ?>">

    <label>Isi Materi</label>
    <textarea name="isi" rows="5" required><?= e($data['isi']) ?></textarea>

    <label>Tautan (opsional)</label>
    <input type="url" name="tautan" maxlength="255" value="<?= e($data['tautan']) ?>">

    <button>Simpan</button>
    <a class="btn btn-secondary" href="materi.php">Batal</a>
</form>

<?php layout_bawah(); ?>