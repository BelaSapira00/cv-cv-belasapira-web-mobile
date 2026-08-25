<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya admin atau petugas yang memiliki akses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: data_buku.php?msg=akses_ditolak");
    exit;
}

// Ambil daftar kategori dari database
$query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Baru - Perpustakaan Digital</title>
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --navy-dark: #0A192F;
            --navy-primary: #1E3A8A;
            --gray-bg: #F8FAFC;
            --text-dark: #1E293B;
        }

        body {
            background-color: var(--gray-bg);
            color: var(--text-dark);
            padding-top: 30px;
            padding-bottom: 50px;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .card-header-custom {
            background-color: var(--navy-dark);
            color: #FFFFFF;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            padding: 20px;
        }

        .btn-navy {
            background-color: var(--navy-primary);
            color: #FFFFFF;
            border: none;
        }

        .btn-navy:hover {
            background-color: var(--navy-dark);
            color: #FFFFFF;
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 800px;">
    <div class="card card-custom">
        <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
            <h4 class="m-0 fs-5"><i class="bi bi-journal-plus me-2"></i>Tambah Buku Baru</h4>
            <a href="data_buku.php" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
        <div class="card-body p-4">
            
            <!-- Target proses diset ke proses_buku.php dengan enctype multipart -->
            <form action="proses_buku.php?action=tambah" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label for="judul" class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul buku" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="penulis" class="form-label fw-semibold">Penulis / Pengarang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="penulis" name="penulis" placeholder="Nama penulis" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="penerbit" class="form-label fw-semibold">Penerbit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="penerbit" name="penerbit" placeholder="Nama penerbit" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tahun_terbit" class="form-label fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" min="1900" max="<?= date('Y'); ?>" value="<?= date('Y'); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kategori_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <?php while ($kat = mysqli_fetch_assoc($query_kategori)): ?>
                                <option value="<?= $kat['id']; ?>"><?= htmlspecialchars($kat['nama_kategori']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="stok" class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok" name="stok" min="1" value="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi / Sinopsis</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Ringkasan singkat isi buku..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cover" class="form-label fw-semibold">Gambar Cover Buku</label>
                        <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG (Maks 2MB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="file_ebook" class="form-label fw-semibold">File E-Book (PDF)</label>
                        <input type="file" class="form-control" id="file_ebook" name="file_ebook" accept=".pdf">
                        <small class="text-muted">Format: PDF (Maks 10MB)</small>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="data_buku.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" name="simpan" class="btn btn-navy"><i class="bi bi-save me-1"></i> Simpan Buku</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>