<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Cek akses khusus siswa
cek_akses('siswa');

// Ambil ID buku dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: baca_buku.php");
    exit;
}

$buku_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// 1. Ambil data peminjaman dan data buku
$query_cek = "SELECT p.*, b.judul, b.penulis, b.file_ebook 
              FROM peminjaman p
              JOIN buku b ON p.buku_id = b.id
              WHERE p.buku_id = ? AND p.user_id = ? AND p.status = 'dipinjam'";

$stmt = mysqli_prepare($koneksi, $query_cek);
mysqli_stmt_bind_param($stmt, "ii", $buku_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);

// Jika buku tidak ditemukan dalam daftar pinjaman user
if (!$buku) {
    echo "<script>alert('Anda tidak memiliki akses membaca buku ini atau buku belum dipinjam!'); window.location='baca_buku.php';</script>";
    exit;
}

// 2. Ambil dari kolom file_ebook
$file_pdf = $buku['file_ebook'] ?? '';
$filepath = "uploads/pdf/" . $file_pdf;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membaca: <?= htmlspecialchars($buku['judul']); ?> - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #0A192F; color: #FFFFFF; height: 100vh; display: flex; flex-direction: column; }
        .reader-header { background-color: #1E293B; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; }
        .pdf-container { flex: 1; width: 100%; height: 100%; border: none; }
    </style>
</head>
<body>

    <div class="reader-header">
        <div>
            <h5 class="m-0 fw-bold text-info"><?= htmlspecialchars($buku['judul']); ?></h5>
            <small class="text-secondary">Penulis: <?= htmlspecialchars($buku['penulis']); ?></small>
        </div>
        <div>
            <a href="baca_buku.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-x-lg me-1"></i> Tutup Buku
            </a>
        </div>
    </div>

    <?php if (!empty($file_pdf) && file_exists($filepath)): ?>
        <iframe src="<?= htmlspecialchars($filepath); ?>#toolbar=0" class="pdf-container"></iframe>
    <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4 text-center">
            <i class="bi bi-file-earmark-x text-warning display-1 mb-3"></i>
            <h4>Berkas Buku Tidak Ditemukan</h4>
            <p class="text-secondary">
                File <code><?= htmlspecialchars($file_pdf); ?></code> tidak ditemukan di folder <code>uploads/pdf/</code>.
            </p>
            <a href="baca_buku.php" class="btn btn-primary mt-2">Kembali ke Bacaan Saya</a>
        </div>
    <?php endif; ?>

</body>
</html>