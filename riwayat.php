<?php
require_once 'koneksi.php';
require_once 'auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT p.id AS peminjaman_id, p.tanggal_pinjam, p.status, b.judul, b.penulis 
          FROM peminjaman p
          JOIN buku b ON p.buku_id = b.id
          WHERE p.user_id = '$user_id'
          ORDER BY p.id DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        <a href="dashboard_siswa.php" class="text-decoration-none fw-bold mb-3 d-inline-block">← Kembali ke Dashboard</a>
        
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <h3 class="fw-bold mb-4">📜 Riwayat Peminjaman Saya</h3>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th class="text-center">Tanggal Pinjam</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['judul']); ?></td>
                                    <td><?= htmlspecialchars($row['penulis']); ?></td>
                                    <td class="text-center"><?= !empty($row['tanggal_pinjam']) && $row['tanggal_pinjam'] !== '-' ? date('d/m/Y', strtotime($row['tanggal_pinjam'])) : '-'; ?></td>
                                    <td class="text-center">
                                        <?php if ($row['status'] === 'dipinjam'): ?>
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Dikembalikan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['status'] === 'dipinjam'): ?>
                                            <a href="proses_kembalikan.php?id=<?= $row['peminjaman_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Yakin ingin mengembalikan buku ini?')">
                                                Kembalikan
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat peminjaman.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>