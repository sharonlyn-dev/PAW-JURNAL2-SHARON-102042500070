<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$edit = $id !== null && $id !== false && $id > 0;

$data = ['kode'=>'', 'nama'=>'', 'dosen'=>'', 'semester'=>''];

if ($edit) {
    $stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $hasil = $stmt->get_result()->fetch_assoc();

    if (!$hasil) {
        http_response_code(404);
        exit('Mata kuliah tidak ditemukan.');
    }
    $data = $hasil;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validasi_token();

    $idPost = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $kode = trim($_POST['kode'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $dosen = trim($_POST['dosen'] ?? '');
    $semester = filter_input(INPUT_POST, 'semester', FILTER_VALIDATE_INT);

    $editPost = $idPost !== null && $idPost !== false && $idPost > 0;

    if ($kode === '' || strlen($kode) > 20 ||
        $nama === '' || mb_strlen($nama) > 100 ||
        $dosen === '' || mb_strlen($dosen) > 100 ||
        !$semester || $semester < 1 || $semester > 20) {
        flash('Periksa kembali seluruh isian.', 'error');
    } else {
        try {
            if ($editPost) {
                $stmt = $conn->prepare(
                    'UPDATE mata_kuliah SET kode=?, nama=?, dosen=?, semester=? WHERE id=?'
                );
                $stmt->bind_param('sssii', $kode, $nama, $dosen, $semester, $idPost);
                $stmt->execute();
                flash($stmt->affected_rows ? 'Mata kuliah diperbarui.' : 'Tidak ada perubahan.');
            } else {
                $stmt = $conn->prepare(
                    'INSERT INTO mata_kuliah (kode,nama,dosen,semester) VALUES (?,?,?,?)'
                );
                $stmt->bind_param('sssi', $kode, $nama, $dosen, $semester);
                $stmt->execute();
                flash('Mata kuliah berhasil ditambahkan.');
            }

            header('Location: mata_kuliah.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            flash('Gagal menyimpan. Pastikan kode mata kuliah belum digunakan.', 'error');
        }
    }

    $data = compact('kode', 'nama', 'dosen', 'semester');
}

layout_atas($edit ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah');
?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(token()) ?>">
    <input type="hidden" name="id" value="<?= $edit ? (int)$id : '' ?>">

    <label>Kode Mata Kuliah</label>
    <input name="kode" maxlength="20" required value="<?= e($data['kode']) ?>">

    <label>Nama Mata Kuliah</label>
    <input name="nama" maxlength="100" required value="<?= e($data['nama']) ?>">

    <label>Dosen</label>
    <input name="dosen" maxlength="100" required value="<?= e($data['dosen']) ?>">

    <label>Semester</label>
    <input type="number" name="semester" min="1" max="20" required value="<?= e($data['semester']) ?>">

    <button type="submit">Simpan</button>
    <a class="btn btn-secondary" href="mata_kuliah.php">Batal</a>
</form>

<?php layout_bawah(); ?>