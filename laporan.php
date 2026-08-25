<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Cek hak akses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: login.php");
    exit;
}

// Tentukan dashboard sesuai role user
$dashboard_url = ($_SESSION['role'] === 'admin') ? 'dashboard_admin.php' : 'dashboard_petugas.php';

$filter = $_GET['filter'] ?? 'semua';
$where_clause = "";

if ($filter === 'dipinjam') {
    $where_clause = "WHERE p.status = 'dipinjam'";
} elseif ($filter === 'dikembalikan') {
    $where_clause = "WHERE p.status = 'dikembalikan'";
}

// Query mengambil data transaksi beserta denda
$query = "SELECT p.*, u.nama_lengkap AS nama_siswa, b.judul 
          FROM peminjaman p
          JOIN users u ON p.user_id = u.id
          JOIN buku b ON p.buku_id = b.id
          $where_clause
          ORDER BY p.id DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Transaksi Peminjaman</title>
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

    <!-- Header & Tombol Kembali -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0"><i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Laporan & Transaksi Peminjaman</h3>
        <a href="<?= $dashboard_url; ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0">Riwayat Transaksi</h5>
            <div>
                <a href="laporan.php?filter=semua" class="btn btn-sm <?= $filter === 'semua' ? 'btn-primary' : 'btn-outline-primary'; ?>">Semua</a>
                <a href="laporan.php?filter=dipinjam" class="btn btn-sm <?= $filter === 'dipinjam' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sedang Dipinjam</a>
                <a href="laporan.php?filter=dikembalikan" class="btn btn-sm <?= $filter === 'dikembalikan' ? 'btn-primary' : 'btn-outline-primary'; ?>">Sudah Dikembalikan</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-custom m-0">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Siswa</th>
                        <th>Judul Buku</th>
                        <th class="text-center">Tgl Pinjam</th>
                        <th class="text-center">Batas Kembali</th>
                        <th class="text-center">Tgl Dikembalikan</th>
                        <th class="text-center">Denda</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                <td><?= htmlspecialchars($row['judul']); ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td class="text-center">
                                    <?= !empty($row['tanggal_pengembalian']) ? date('d/m/Y', strtotime($row['tanggal_pengembalian'])) : '-'; ?>
                                </td>
                                <td class="text-center fw-bold <?= ($row['denda'] > 0) ? 'text-danger' : 'text-muted'; ?>">
                                    <?= ($row['denda'] > 0) ? 'Rp ' . number_format($row['denda'], 0, ',', '.') : 'Rp 0'; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] === 'dipinjam'): ?>
                                        <span class="badge bg-warning text-dark">Sedang Dipinjam</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <a href="cetak_laporan.php?filter=<?= $filter; ?>" target="_blank" class="btn btn-success">
                <i class="bi bi-printer me-1"></i> Cetak Laporan (PDF/Print)
            </a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>