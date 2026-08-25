<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya SISWA yang bisa akses
cek_akses('siswa');

$user_id = $_SESSION['user_id'];

// Ambil statistik siswa menggunakan Prepared Statement
$stmt_pinjam = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = ? AND status = 'dipinjam'");
mysqli_stmt_bind_param($stmt_pinjam, "i", $user_id);
mysqli_stmt_execute($stmt_pinjam);
$total_pinjam = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pinjam))['total'] ?? 0;

$stmt_riwayat = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE user_id = ?");
mysqli_stmt_bind_param($stmt_riwayat, "i", $user_id);
mysqli_stmt_execute($stmt_riwayat);
$total_riwayat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_riwayat))['total'] ?? 0;

// Logika pencarian buku aman dari SQL Injection
$search = trim($_GET['search'] ?? '');
$query_buku = "SELECT b.*, k.nama_kategori 
               FROM buku b 
               LEFT JOIN kategori k ON b.kategori_id = k.id";

if (!empty($search)) {
    $query_buku .= " WHERE b.judul LIKE ? OR b.penulis LIKE ? OR k.nama_kategori LIKE ?";
    $query_buku .= " ORDER BY b.id DESC";
    
    $stmt_buku = mysqli_prepare($koneksi, $query_buku);
    $search_param = "%{$search}%";
    mysqli_stmt_bind_param($stmt_buku, "sss", $search_param, $search_param, $search_param);
    mysqli_stmt_execute($stmt_buku);
    $result_buku = mysqli_stmt_get_result($stmt_buku);
} else {
    $query_buku .= " ORDER BY b.id DESC";
    $result_buku = mysqli_query($koneksi, $query_buku);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - <?= htmlspecialchars(NAMA_APLIKASI ?? 'Perpustakaan Digital'); ?></title>
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
            min-height: 100vh;
        }

        /* --- HEADER SELULER (HP) --- */
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
            z-index: 1001;
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

        .mobile-brand span {
            color: var(--blue-light);
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
            z-index: 1050;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* --- SIDEBAR RESPONSIF (HP) --- */
        .mobile-sidebar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background-color: var(--navy-dark);
            color: #FFFFFF;
            z-index: 1100;
            flex-direction: column;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .mobile-sidebar.show {
            transform: translateX(0);
        }

        .sidebar-brand {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #1E293B;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar-brand img {
            height: 38px;
            width: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .sidebar-brand span {
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

        .sidebar-logout {
            padding: 20px 25px;
            border-top: 1px solid #1E293B;
        }

        .sidebar-logout a {
            color: #EF4444;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* --- NAVBAR SISWA (DESKTOP) --- */
        .navbar {
            background-color: var(--navy-dark);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .navbar .logo {
            color: #FFFFFF;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar .logo img {
            height: 40px;
            width: 40px;
            object-fit: contain;
            border-radius: 50%;
        }

        .navbar .logo span {
            color: var(--blue-light);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: #E2E8F0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--blue-light);
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-logout {
            background-color: #EF4444;
            color: #FFFFFF !important;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-logout:hover {
            background-color: #DC2626;
        }

        /* --- CONTAINER UTAMA --- */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* --- BANNER WELCOME --- */
        .welcome-banner {
            background: linear-gradient(135deg, var(--navy-primary) 0%, var(--navy-dark) 100%);
            color: #FFFFFF;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 20px rgba(10, 25, 47, 0.15);
        }

        .welcome-text h2 {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .welcome-text h2 span {
            color: var(--blue-light);
        }

        .welcome-text p {
            color: #CBD5E1;
            font-size: 0.95rem;
        }

        .stats-brief {
            display: flex;
            gap: 15px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 20px;
            border-radius: 12px;
            text-align: center;
        }

        .stat-box strong {
            display: block;
            font-size: 1.5rem;
            color: var(--blue-light);
        }

        .stat-box span {
            font-size: 0.8rem;
            color: #E2E8F0;
        }

        /* --- SEARCH BAR --- */
        .search-section {
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex-grow: 1;
            padding: 14px 20px;
            border: 1px solid var(--gray-border);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: border-color 0.3s;
        }

        .search-input:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .btn-search {
            background-color: var(--navy-primary);
            color: #FFFFFF;
            border: none;
            padding: 0 25px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-search:hover {
            background-color: var(--navy-dark);
        }

        /* --- KATALOG BUKU --- */
        .section-title {
            font-size: 1.3rem;
            color: var(--navy-dark);
            margin-bottom: 20px;
        }

        .grid-buku {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .card-buku {
            background: var(--gray-card);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--gray-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .card-buku:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -3px rgba(10, 25, 47, 0.15);
        }

        .card-img {
            height: 200px;
            background-color: var(--blue-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy-primary);
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            padding: 15px;
            overflow: hidden;
        }

        .card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .badge-kategori {
            background-color: var(--blue-bg);
            color: var(--navy-primary);
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 700;
            width: fit-content;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .card-title {
            font-size: 1.1rem;
            color: var(--navy-dark);
            margin-bottom: 6px;
        }

        .card-author {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 15px;
        }

        .card-actions {
            margin-top: auto;
            display: flex;
            gap: 8px;
        }

        .btn-baca {
            flex: 1;
            text-align: center;
            background-color: var(--navy-primary);
            color: #FFFFFF;
            text-decoration: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .btn-baca:hover {
            background-color: var(--navy-dark);
        }

        .btn-favorit {
            background-color: var(--blue-bg);
            color: var(--navy-primary);
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-favorit:hover {
            background-color: #BAE6FD;
        }

        /* --- RESPONSIVE / HP VIEW (MAX-WIDTH: 768px) --- */
        @media (max-width: 768px) {
            .navbar {
                display: none;
            }

            .mobile-header,
            .mobile-sidebar {
                display: flex;
            }

            .container {
                margin-top: 80px;
                padding: 0 15px;
            }

            .welcome-banner {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .stats-brief {
                width: 100%;
                justify-content: space-between;
            }

            .stat-box {
                flex: 1;
            }

            .search-form {
                flex-direction: column;
            }

            .btn-search {
                padding: 12px;
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
            <div><span>Perpustakaan</span> Digital</div>
        </div>
        <div style="width: 24px;"></div>
    </header>

    <!-- OVERLAY LAYAR GELAP SAAT SIDEBAR BUKA DI HP -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR RESPONSIF UNTUK HP -->
    <aside class="mobile-sidebar" id="mobileSidebar">
        <div class="sidebar-brand">
            <img src="logo.png" alt="Logo">
            <div><span>Perpustakaan</span> Digital</div>
        </div>

        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard_siswa.php">📚 Katalog Buku</a></li>
            <li><a href="baca_buku.php">📖 Bacaan Saya</a></li>
            <li><a href="favorit.php">⭐ Favorit</a></li>
            <li><a href="riwayat.php">📜 Riwayat</a></li>
            <li><a href="profil.php">👤 Profil Saya</a></li>
        </ul>

        <div class="sidebar-logout">
            <a href="proses_logout.php">🚪 Keluar</a>
        </div>
    </aside>

    <!-- NAVBAR SISWA (DESKTOP) -->
    <nav class="navbar">
        <a href="dashboard_siswa.php" class="logo">
            <img src="logo.png" alt="Logo">
            <span>Perpustakaan</span>Digital
        </a>
        <ul class="nav-links">
            <li><a href="dashboard_siswa.php" class="active">📚 Katalog</a></li>
            <li><a href="baca_buku.php">📖 Bacaan Saya</a></li>
            <li><a href="favorit.php">⭐ Favorit</a></li>
            <li><a href="riwayat.php">📜 Riwayat</a></li>
            <li><a href="profil.php">👤 Profil</a></li>
        </ul>
        <div class="user-nav">
            <a href="proses_logout.php" class="btn-logout">Keluar</a>
        </div>
    </nav>

    <!-- CONTAINER UTAMA -->
    <div class="container">
        <!-- WELCOME BANNER -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Selamat Datang, <span><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Siswa'); ?></span>!</h2>
                <p>Mau membaca buku apa hari ini? Cari koleksi pustaka digital sekolah di bawah ini.</p>
            </div>
            <div class="stats-brief">
                <div class="stat-box">
                    <strong><?= (int) $total_pinjam; ?></strong>
                    <span>Dipinjam</span>
                </div>
                <div class="stat-box">
                    <strong><?= (int) $total_riwayat; ?></strong>
                    <span>Total Selesai</span>
                </div>
            </div>
        </div>

        <!-- FORM PENCARIAN -->
        <div class="search-section">
            <form action="dashboard_siswa.php" method="GET" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="Cari judul buku, penulis, atau kategori..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit" class="btn-search">Cari Buku</button>
            </form>
        </div>

        <!-- DAFTAR KATALOG BUKU -->
        <h3 class="section-title">
            <?= !empty($search) ? 'Hasil Pencarian: "' . htmlspecialchars($search) . '"' : 'Koleksi Buku Terbaru'; ?>
        </h3>

        <div class="grid-buku">
            <?php if (mysqli_num_rows($result_buku) > 0): ?>
                <?php while ($buku = mysqli_fetch_assoc($result_buku)): ?>
                    <div class="card-buku">
                        <div class="card-img">
                            <?php if (!empty($buku['cover'])): ?>
                                <img src="uploads/cover/<?= htmlspecialchars($buku['cover']); ?>" alt="<?= htmlspecialchars($buku['judul']); ?>">
                            <?php else: ?>
                                📖 <?= htmlspecialchars($buku['judul']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <span class="badge-kategori"><?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum'); ?></span>
                            <h4 class="card-title"><?= htmlspecialchars($buku['judul']); ?></h4>
                            <p class="card-author">Penulis: <?= htmlspecialchars($buku['penulis']); ?></p>
                            
                            <div class="card-actions">
                                <a href="baca_buku.php?id=<?= (int) $buku['id']; ?>" class="btn-baca">Detail & Baca</a>
                                <a href="proses_favorit.php?aksi=tambah&buku_id=<?= (int) $buku['id']; ?>" class="btn-favorit" title="Tambah ke Favorit">⭐</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 40px 0;">
                    Buku yang kamu cari tidak ditemukan. Coba kata kunci lain!
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- SCRIPT HAMBURGER & SIDEBAR MOBILE -->
    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            mobileSidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('active');
        }

        hamburgerBtn.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>