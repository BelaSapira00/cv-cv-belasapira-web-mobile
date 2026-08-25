<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya SISWA yang bisa akses
cek_akses('siswa');

$user_id = $_SESSION['user_id'];

// Query mengambil buku yang SEDANG DIPINJAM oleh siswa menggunakan JOIN ke tabel kategori
$query = "SELECT p.id AS peminjaman_id, p.tanggal_pinjam, p.tanggal_kembali, 
                 b.id AS buku_id, b.judul, b.penulis, k.nama_kategori 
          FROM peminjaman p
          JOIN buku b ON p.buku_id = b.id
          LEFT JOIN kategori k ON b.kategori_id = k.id
          WHERE p.user_id = ? AND p.status = 'dipinjam'
          ORDER BY p.id DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacaan Saya - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --navy-dark: #0A192F;
            --blue-light: #38BDF8;
            --gray-bg: #F8FAFC;
        }

        body { 
            background-color: var(--gray-bg); 
            color: #1E293B; 
        }

        /* --- NAVBAR STYLES --- */
        .navbar-custom { 
            background-color: var(--navy-dark); 
            padding: 0.8rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .navbar-brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .navbar-brand-logo img {
            height: 40px;
            width: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .brand-blue {
            color: #38BDF8;
        }

        .brand-white {
            color: #FFFFFF;
        }

        .nav-link-custom {
            color: #E2E8F0 !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            transition: color 0.3s;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--blue-light) !important;
        }

        .btn-logout {
            background-color: #EF4444;
            color: #FFFFFF !important;
            padding: 6px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.3s;
            border: none;
        }

        .btn-logout:hover {
            background-color: #DC2626;
        }

        .card-custom { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
    </style>
</head>
<body>

    <!-- NAVBAR DESKTOP & DESAIN SERAGAM -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <!-- LOGO DENGAN GAMBAR BUNDAR & TEKS BIRU-PUTIH -->
            <a class="navbar-brand-logo" href="dashboard_siswa.php">
                <img src="logo.png" alt="Logo Perpustakaan">
                <div>
                    <span class="brand-blue">Perpustakaan</span>
                    <span class="brand-white">Digital</span>
                </div>
            </a>

            <!-- BUTTON TOGGLER FOR MOBILE -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- NAV LINKS -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center gap-2 my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="dashboard_siswa.php">📚 Katalog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom active" href="baca_buku.php">📖 Bacaan Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="favorit.php">⭐ Favorit</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="riwayat.php">📜 Riwayat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="profil.php">👤 Profil</a>
                    </li>
                </ul>

                <!-- TOMBOL KELUAR -->
                <div class="d-flex align-items-center">
                    <a href="proses_logout.php" class="btn-logout">Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTAINER BUKU -->
    <div class="container py-5">
        <h3 class="fw-bold mb-4">📖 Buku yang Sedang Dibaca</h3>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4">
                        <div class="card card-custom h-100 p-3">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <span class="badge bg-primary mb-2"><?= htmlspecialchars($row['nama_kategori'] ?? 'Umum'); ?></span>
                                    <h5 class="fw-bold"><?= htmlspecialchars($row['judul']); ?></h5>
                                    <p class="text-muted small">Penulis: <?= htmlspecialchars($row['penulis']); ?></p>
                                    <p class="small text-secondary">
                                        <i class="bi bi-calendar-check me-1"></i> Batas Kembali: <br>
                                        <strong><?= date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></strong>
                                    </p>
                                </div>
                                <div class="mt-3 d-grid gap-2">
                                    <a href="baca.php?id=<?= (int) $row['buku_id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-book-half me-1"></i> Baca Sekarang
                                    </a>
                                    <a href="proses_kembalikan.php?id=<?= (int) $row['peminjaman_id']; ?>" 
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Apakah Anda yakin ingin mengembalikan buku ini?')">
                                        <i class="bi bi-arrow-return-left me-1"></i> Kembalikan Buku
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">Anda belum meminjam atau membaca buku apapun saat ini.</p>
                    <a href="dashboard_siswa.php" class="btn btn-primary">Cari & Pinjam Buku</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>