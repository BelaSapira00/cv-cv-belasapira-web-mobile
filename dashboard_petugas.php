<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya PETUGAS yang bisa akses
cek_akses('petugas');

// Ambil data statistik operasional
$total_dipinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'"))['total'] ?? 0;
$total_kembali  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE status='dikembalikan'"))['total'] ?? 0;
$total_siswa    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role='siswa' AND status_verifikasi='terverifikasi'"))['total'] ?? 0;

// Ambil 5 transaksi peminjaman terbaru beserta nama siswa & judul buku
$query_transaksi = "SELECT p.*, u.nama_lengkap, b.judul 
                   FROM peminjaman p 
                   JOIN users u ON p.user_id = u.id 
                   JOIN buku b ON p.buku_id = b.id 
                   ORDER BY p.id DESC LIMIT 5";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - <?= NAMA_APLIKASI; ?></title>
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

        .sidebar-brand span {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--blue-light);
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

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: var(--gray-card);
            padding: 20px 25px;
            border-radius: 12px;
            border: 1px solid var(--gray-border);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-role {
            background-color: var(--blue-bg);
            color: var(--navy-primary);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* --- STATS CARDS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--gray-card);
            padding: 22px;
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card h3 {
            font-size: 1.8rem;
            color: var(--navy-dark);
            margin-bottom: 4px;
        }

        .stat-card p {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .stat-icon {
            font-size: 2rem;
            background: var(--blue-bg);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        /* --- TABLE --- */
        .table-card {
            background: var(--gray-card);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            padding: 25px;
        }

        .table-card h3 {
            margin-bottom: 20px;
            color: var(--navy-dark);
            font-size: 1.1rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-border);
            font-size: 0.9rem;
            white-space: nowrap;
        }

        th {
            background-color: var(--gray-bg);
            color: var(--navy-dark);
            font-weight: 600;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-dipinjam {
            background-color: #FEF08A;
            color: #713F12;
        }

        .status-dikembalikan {
            background-color: #DEF7EC;
            color: #03543F;
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

            .top-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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

    <!-- SIDEBAR NAVIGASI PETUGAS -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="logo.png" alt="Logo">
            <div>
                <span>Perpustakaan </span>Digital
                <div style="font-size: 0.7rem; color: #94A3B8;">SMK Bhakti Putra</div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard_petugas.php">📊 Dashboard</a></li>
            <li><a href="data_buku.php">📚 Data Buku</a></li>
            <li><a href="data_siswa.php">🎓 Data Siswa</a></li>
            <li><a href="pengembalian.php">🧾 Pengembalian & Denda</a></li>
            <li><a href="laporan.php">📋 Laporan</a></li>
            <li><a href="pengaturan.php">⚙️ Pengaturan</a></li>
        </ul>

        <div class="btn-logout">
            <a href="proses_logout.php">🚪 Keluar</a>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <!-- HEADER TOP BAR -->
        <div class="top-header">
            <div>
                <h2>Dashboard Petugas</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Selamat bertugas, <strong><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>!</p>
            </div>
            <div class="user-profile">
                <span class="badge-role">Petugas</span>
            </div>
        </div>

        <!-- STATISTIK PETUGAS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <h3><?= $total_dipinjam; ?></h3>
                    <p>Buku Sedang Dipinjam</p>
                </div>
                <div class="stat-icon">📖</div>
            </div>

            <div class="stat-card">
                <div>
                    <h3><?= $total_kembali; ?></h3>
                    <p>Total Pengembalian</p>
                </div>
                <div class="stat-icon">✅</div>
            </div>

            <div class="stat-card">
                <div>
                    <h3><?= $total_siswa; ?></h3>
                    <p>Siswa Terverifikasi</p>
                </div>
                <div class="stat-icon">🎓</div>
            </div>
        </div>

        <!-- TABEL TRANSAKSI TERBARU -->
        <div class="table-card">
            <h3>Transaksi Peminjaman Terbaru</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Peminjam (Siswa)</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
                            <?php while ($trx = mysqli_fetch_assoc($result_transaksi)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($trx['nama_lengkap']); ?></td>
                                    <td><?= htmlspecialchars($trx['judul']); ?></td>
                                    <td><?= date('d/m/Y', strtotime($trx['tanggal_pinjam'])); ?></td>
                                    <td><?= date('d/m/Y', strtotime($trx['tanggal_kembali'])); ?></td>
                                    <td>
                                        <?php if ($trx['status'] === 'dipinjam'): ?>
                                            <span class="status-badge status-dipinjam">Dipinjam</span>
                                        <?php else: ?>
                                            <span class="status-badge status-dikembalikan">Dikembalikan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Belum ada aktivitas peminjaman.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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