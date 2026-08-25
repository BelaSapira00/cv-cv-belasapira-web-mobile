<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Cek hak akses (admin atau petugas)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: dashboard_admin.php");
    exit;
}

// Ambil data peminjaman yang statusnya masih 'dipinjam'
$query = "SELECT p.*, u.nama_lengkap AS nama_siswa, b.judul 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.id
          JOIN buku b ON p.buku_id = b.id
          WHERE p.status = 'dipinjam'
          ORDER BY p.tanggal_kembali ASC";
$result = mysqli_query($koneksi, $query);

// Tarif denda per hari (misal Rp 1.000 / hari)
$denda_per_hari = 1000;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku & Denda - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F8FAFC; color: #1E293B; padding: 30px 15px; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-custom thead { background-color: #1E293B; color: #FFFFFF; }
    </style>
</head>
<body>

<div class="container-fluid" style="max-width: 1200px;">

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'sukses_kembali'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Pengembalian buku berhasil diproses!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0"><i class="bi bi-arrow-return-left text-primary me-2"></i>Pengembalian Buku & Denda</h3>
        <a href="dashboard_admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <div class="card card-custom p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-custom m-0">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th class="text-center">Tgl Pinjam</th>
                        <th class="text-center">Tgl Jatuh Tempo</th>
                        <th class="text-center">Keterlambatan</th>
                        <th class="text-center">Total Denda</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php 
                                // Hitung keterlambatan
                                $tgl_sekarang = new DateTime();
                                $tgl_jatuh_tempo = new DateTime($row['tanggal_kembali']);
                                
                                $terlambat = 0;
                                $total_denda = 0;

                                if ($tgl_sekarang > $tgl_jatuh_tempo) {
                                    $selisih = $tgl_sekarang->diff($tgl_jatuh_tempo);
                                    $terlambat = $selisih->days;
                                    $total_denda = $terlambat * $denda_per_hari;
                                }
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                <td><?= htmlspecialchars($row['judul']); ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td class="text-center">
                                    <?php if ($terlambat > 0): ?>
                                        <span class="badge bg-danger"><?= $terlambat; ?> Hari</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Tepat Waktu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold <?= $total_denda > 0 ? 'text-danger' : 'text-success'; ?>">
                                    Rp <?= number_format($total_denda, 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <form action="proses_kembali.php" method="POST" onsubmit="return confirm('Proses pengembalian buku ini?');">
                                        <input type="hidden" name="peminjaman_id" value="<?= $row['id']; ?>">
                                        <input type="hidden" name="buku_id" value="<?= $row['buku_id']; ?>">
                                        <input type="hidden" name="denda" value="<?= $total_denda; ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-box-arrow-in-down-left me-1"></i> Kembalikan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada buku yang sedang dipinjam saat ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>