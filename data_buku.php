<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan yang masuk adalah admin atau petugas
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: login.php");
    exit;
}

$role_user = $_SESSION['role']; // 'admin' atau 'petugas'

// Ambil daftar seluruh buku
$query_buku = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori 
                                       FROM buku b 
                                       LEFT JOIN kategori k ON b.kategori_id = k.id 
                                       ORDER BY b.id DESC");

// Pesan Notifikasi
$pesan = '';
$tipe = 'success';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'sukses_tambah') $pesan = 'Buku berhasil ditambahkan!';
    elseif ($_GET['msg'] === 'sukses_edit') $pesan = 'Data buku berhasil diperbarui!';
    elseif ($_GET['msg'] === 'sukses_hapus') $pesan = 'Buku berhasil dihapus!';
    elseif ($_GET['msg'] === 'akses_ditolak') { $pesan = 'Anda tidak memiliki hak akses!'; $tipe = 'danger'; }
    elseif ($_GET['msg'] === 'gagal') { $pesan = 'Terjadi kesalahan pada database!'; $tipe = 'danger'; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - <?= NAMA_APLIKASI; ?></title>
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --navy-dark: #0A192F;
            --navy-primary: #1E3A8A;
            --blue-light: #38BDF8;
            --blue-bg: #E0F2FE;
            --gray-bg: #F8FAFC;
            --gray-card: #FFFFFF;
            --gray-border: #E2E8F0;
            --text-dark: #1E293B;
            --text-muted: #64748B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--gray-bg);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        /* --- HEADER SELULER (RESPONSIF HP) --- */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: var(--navy-dark);
            color: #FFFFFF;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 99;
            border-bottom: 1px solid #1E293B;
        }

        .hamburger-btn {
            background: none;
            border: none;
            color: #FFFFFF;
            font-size: 1.6rem;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .mobile-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .mobile-brand img {
            height: 32px;
            width: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .mobile-brand .text-blue {
            color: var(--blue-light);
        }

        .mobile-brand .text-white {
            color: #FFFFFF;
        }

        /* --- OVERLAY BACKDROP --- */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 150;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            background-color: var(--navy-dark);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 200;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #1E293B;
        }

        .sidebar-brand img {
            height: 38px;
            width: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .sidebar-brand .brand-title {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .sidebar-brand .brand-title .text-blue {
            color: var(--blue-light);
        }

        .sidebar-brand .brand-title .text-white {
            color: #FFFFFF;
        }

        .sidebar-brand .brand-subtitle {
            font-size: 0.75rem;
            color: #94A3B8;
            font-weight: 400;
            margin-top: 2px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            flex-grow: 1;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            color: #94A3B8;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .sidebar-menu li a:hover, 
        .sidebar-menu li.active a {
            color: #FFFFFF;
            background-color: rgba(56, 189, 248, 0.1);
            border-left: 4px solid var(--blue-light);
        }

        .btn-logout {
            padding: 20px 25px;
            border-top: 1px solid #1E293B;
        }

        .btn-logout a {
            color: #EF4444;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        /* --- RESPONSIVE / HP VIEW (MAX-WIDTH: 768px) --- */
        @media (max-width: 768px) {
            .mobile-header {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px 15px;
                margin-top: 60px;
            }
        }
    </style>
</head>
<body>

    <!-- HEADER LAYAR HP -->
    <header class="mobile-header">
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Buka Menu">☰</button>
        <div class="mobile-brand">
            <img src="logo.png" alt="Logo">
            <div>
                <span class="text-blue">Perpustakaan</span> <span class="text-white">Digital</span>
            </div>
        </div>
        <div style="width: 24px;"></div>
    </header>

    <!-- OVERLAY LAYAR GELAP SAAT SIDEBAR BUKA DI HP -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR NAVIGASI -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="logo.png" alt="Logo">
            <div>
                <div class="brand-title">
                    <span class="text-blue">Perpustakaan</span> <span class="text-white">Digital</span>
                </div>
                <div class="brand-subtitle">SMK Bhakti Putra</div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="dashboard_admin.php">📊 Dashboard</a></li>
            <li class="active"><a href="data_buku.php">📚 Data Buku</a></li>
            <li><a href="kategori.php">🏷️ Kategori Buku</a></li>
            <li><a href="data_petugas.php">👨‍💼 Data Petugas</a></li>
            <li><a href="data_siswa.php">🎓 Data Siswa</a></li>
            <li><a href="pengembalian.php">🧾 Pengembalian & Denda</a></li>
            <li><a href="laporan.php">📑 Laporan</a></li>
            <li><a href="pengaturan.php">⚙️ Pengaturan</a></li>
        </ul>

        <div class="btn-logout">
            <a href="proses_logout.php">🚪 Keluar</a>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h3 class="m-0">📚 Daftar Buku Terdaftar</h3>
            <div class="d-flex gap-2">
                <!-- Tombol Tambah hanya tampil untuk Admin -->
                <?php if ($role_user === 'admin'): ?>
                    <a href="input_buku.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Buku Baru</a>
                <?php endif; ?>
                <a href="dashboard_admin.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

        <?php if ($pesan): ?>
            <div class="alert alert-<?= $tipe; ?> alert-dismissible fade show" role="alert">
                <?= $pesan; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Penerbit</th>
                                <th>Tahun</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>File E-Book</th>
                                <?php if ($role_user === 'admin'): ?>
                                    <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query_buku)): 
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><strong><?= htmlspecialchars($row['judul']); ?></strong></td>
                                <td><?= htmlspecialchars($row['penulis']); ?></td>
                                <td><?= htmlspecialchars($row['penerbit']); ?></td>
                                <td><?= $row['tahun_terbit']; ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['nama_kategori'] ?? 'Umum'); ?></span></td>
                                <td><span class="badge bg-secondary"><?= $row['stok']; ?></span></td>
                                <td>
                                    <?php if (!empty($row['file_ebook'])): ?>
                                        <a href="uploads/pdf/<?= $row['file_ebook']; ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-file-pdf"></i> PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted fs-7">Tidak Ada</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Aksi Hapus & Edit Hanya Untuk Admin -->
                                <?php if ($role_user === 'admin'): ?>
                                <td class="text-center">
                                    <a href="edit_buku.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="proses_buku.php?action=hapus&id=<?= $row['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT HAMBURGER & SIDEBAR -->
    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('active');
        }

        hamburgerBtn.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>