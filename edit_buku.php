<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Proteksi: Hanya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: data_buku.php?msg=akses_ditolak");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id = $id");
$buku = mysqli_fetch_assoc($query);

if (!$buku) {
    header("Location: data_buku.php");
    exit;
}

$query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku - Perpus Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark font-weight-bold">
            <h5 class="mb-0">Edit Data Buku</h5>
        </div>
        <div class="card-body">
            <form action="proses_buku.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $buku['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($buku['judul']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($buku['penulis']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($buku['penerbit']); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" class="form-control" value="<?= $buku['tahun_terbit']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $buku['stok']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select" required>
                        <?php while ($kat = mysqli_fetch_assoc($query_kategori)): ?>
                            <option value="<?= $kat['id']; ?>" <?= ($kat['id'] == $buku['kategori_id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ganti File E-Book (PDF Opsional)</label>
                    <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                    <?php if(!empty($buku['file_ebook'])): ?>
                        <small class="text-muted">File saat ini: <code><?= $buku['file_ebook']; ?></code></small>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="data_buku.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>