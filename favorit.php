<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan user sudah login dan ber-role siswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Menggunakan Prepared Statement untuk keamanan SQL Injection
$stmt = mysqli_prepare($koneksi, "SELECT f.id AS favorit_id, b.*, k.nama_kategori 
                                  FROM favorit f
                                  JOIN buku b ON f.buku_id = b.id
                                  LEFT JOIN kategori k ON b.kategori_id = k.id
                                  WHERE f.user_id = ?
                                  ORDER BY f.created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result_favorit = mysqli_stmt_get_result($stmt);

$pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'sukses_hapus') $pesan = 'Buku berhasil dihapus dari daftar favorit.';
    elseif ($_GET['msg'] === 'sukses_tambah') $pesan = 'Buku berhasil ditambahkan ke favorit!';
    elseif ($_GET['msg'] === 'ada') $pesan = 'Buku ini sudah ada di daftar favorit Anda.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Favorit Saya - <?= NAMA_APLIKASI; ?></title>
    <style>
        :root {
            --navy-dark: #0A192F;
            --navy-primary: #1E3A8A;
            --blue-light: #38BDF8;
            --gray-bg: #F8FAFC;
            --gray-card: #FFFFFF;
            --gray-border: #E2E8F0;
            --text-dark: #1E293B;
            --text-muted: #64748B;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--gray-bg); color: var(--text-dark); min-height: 100vh; }

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
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .mobile-brand img {
            height: 35px;
            width: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .text-blue { color: var(--blue-light); }
        .text-white { color: #FFFFFF; }

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
        }

        .sidebar-brand img {
            height: 38px;
            width: 38px;
            border-radius: 50%;
            object-fit: cover;
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

        /* --- NAVBAR DESKTOP --- */
        .navbar { 
            background-color: var(--navy-dark); 
            padding: 0.8rem 4%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .logo { 
            color: #FFFFFF; 
            font-size: 1.1rem; 
            font-weight: 700; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            line-height: 1.2;
        }

        .navbar .logo img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
            list-style: none;
        }

        .nav-links a {
            color: #E2E8F0;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--blue-light);
        }

        .btn-logout { 
            background-color: #EF4444; 
            color: #FFFFFF !important; 
            padding: 6px 18px; 
            border-radius: 6px; 
            text-decoration: none; 
            font-size: 0.9rem; 
            font-weight: 600; 
            transition: background 0.3s; 
        }

        .btn-logout:hover { 
            background-color: #DC2626; 
        }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .header-section { margin-bottom: 25px; }
        .header-section h1 { font-size: 1.8rem; color: var(--navy-dark); margin-bottom: 5px; }
        .alert { background-color: #DEF7EC; color: #03543F; padding: 12px 18px; border-radius: 8px; font-size: 0.9rem; margin-bottom: 20px; border: 1px solid #BCF0DA; }
        .books-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
        .book-card { background: var(--gray-card); border-radius: 12px; border: 1px solid var(--gray-border); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.3s, box-shadow 0.3s; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .cover-box { width: 100%; height: 260px; background-color: #E2E8F0; overflow: hidden; position: relative; }
        .cover-box img { width: 100%; height: 100%; object-fit: cover; }
        .badge-kat { position: absolute; top: 10px; left: 10px; background-color: rgba(10, 25, 47, 0.85); color: var(--blue-light); padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; z-index: 2; }
        .book-details { padding: 18px; display: flex; flex-direction: column; flex-grow: 1; }
        .book-title { font-size: 1rem; font-weight: 700; color: var(--navy-dark); margin-bottom: 6px; line-height: 1.3; }
        .book-author { font-size: 0.83rem; color: var(--text-muted); margin-bottom: 15px; }
        .book-actions { margin-top: auto; display: flex; gap: 8px; }
        .btn-detail { flex: 1; background-color: var(--navy-primary); color: #FFFFFF; padding: 8px; border-radius: 6px; text-decoration: none; font-size: 0.82rem; font-weight: 600; text-align: center; transition: background 0.3s; }
        .btn-detail:hover { background-color: var(--navy-dark); }
        .btn-remove { background-color: #FEE2E2; color: #DC2626; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.82rem; font-weight: 600; display: flex; align-items: center; justify-content: center; transition: background 0.3s; }
        .btn-remove:hover { background-color: #FCA5A5; }
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--gray-card); border-radius: 12px; border: 1px solid var(--gray-border); color: var(--text-muted); }
        .empty-state a { display: inline-block; margin-top: 15px; background-color: var(--navy-primary); color: #FFFFFF; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; }

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
                <span class="text-blue">Perpustakaan</span>
                <span class="text-white">Digital</span>
            </div>
        </div>
        <div style="width: 24px;"></div>
    </header>

    <!-- OVERLAY LAYAR GELAP SAAT SIDEBAR BUKA DI HP -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR RESPONSIF UNTUK HP -->
    <aside class="mobile-sidebar" id="mobileSidebar">
        <div class="sidebar-brand">
            <img src="logo.png" alt="Logo">
            <div>
                <div style="font-weight: 700; line-height: 1.2;">
                    <span class="text-blue">Perpustakaan</span><br>
                    <span class="text-white">Digital</span>
                </div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="dashboard_siswa.php">📚 Katalog Buku</a></li>
            <li><a href="baca_buku.php">📖 Bacaan Saya</a></li>
            <li class="active"><a href="favorit.php">⭐ Favorit Saya</a></li>
            <li><a href="riwayat.php">📜 Riwayat</a></li>
            <li><a href="profil.php">👤 Profil Saya</a></li>
        </ul>

        <div class="sidebar-logout">
            <a href="proses_logout.php">🚪 Keluar</a>
        </div>
    </aside>

    <!-- NAVBAR DESKTOP -->
    <nav class="navbar">
        <a href="dashboard_siswa.php" class="logo">
            <img src="logo.png" alt="Logo">
            <div>
                <span class="text-blue">Perpustakaan</span>
                <span class="text-white">Digital</span>
            </div>
        </a>

        <ul class="nav-links">
            <li><a href="dashboard_siswa.php">📚 Katalog</a></li>
            <li><a href="baca_buku.php">📖 Bacaan Saya</a></li>
            <li><a href="favorit.php" class="active">⭐ Favorit</a></li>
            <li><a href="riwayat.php">📜 Riwayat</a></li>
            <li><a href="profil.php">👤 Profil</a></li>
        </ul>

        <div>
            <a href="proses_logout.php" class="btn-logout">Keluar</a>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>❤️ Buku Favorit Saya</h1>
            <p style="color: var(--text-muted);">Kumpulan buku pilihan yang kamu simpan untuk dibaca nanti.</p>
        </div>

        <?php if ($pesan): ?>
            <div class="alert"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <div class="books-grid">
            <?php if (mysqli_num_rows($result_favorit) > 0): ?>
                <?php while ($buku = mysqli_fetch_assoc($result_favorit)): ?>
                    <div class="book-card">
                        <div class="cover-box">
                            <span class="badge-kat"><?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum'); ?></span>
                            <img src="uploads/cover/<?= !empty($buku['cover']) ? htmlspecialchars($buku['cover']) : 'default_cover.jpg'; ?>" alt="<?= htmlspecialchars($buku['judul']); ?>">
                        </div>
                        <div class="book-details">
                            <h3 class="book-title"><?= htmlspecialchars($buku['judul']); ?></h3>
                            <p class="book-author">Oleh: <?= htmlspecialchars($buku['penulis']); ?></p>
                            
                            <div class="book-actions">
                                <a href="detail_buku.php?id=<?= $buku['id']; ?>" class="btn-detail">Lihat Buku</a>
                                <a href="proses_favorit.php?aksi=hapus&buku_id=<?= $buku['id']; ?>" class="btn-remove" title="Hapus dari Favorit" onclick="return confirm('Hapus buku ini dari daftar favorit?')">🗑️</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>Belum ada buku favorit yang ditambahkan.</p>
                    <a href="dashboard_siswa.php">Jelajahi Buku</a>
                </div>
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