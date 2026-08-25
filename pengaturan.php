<?php
session_start();
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Ambil data user dari tabel USERS (sesuai database phpMyAdmin)
$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$pesan_error = $_GET['error'] ?? '';
$pesan_sukses = $_GET['sukses'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Perpustakaan Digital</title>
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

        .text-blue { color: var(--blue-light); }
        .text-white { color: #FFFFFF; }

        /* --- HEADER SELULER (HP) --- */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
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
            background: none; border: none; color: #FFFFFF;
            font-size: 1.6rem; cursor: pointer; display: flex; align-items: center;
        }

        .mobile-brand {
            display: flex; align-items: center; gap: 10px;
            font-size: 1rem; font-weight: 700; line-height: 1.2;
        }

        .mobile-brand img { height: 35px; width: 35px; border-radius: 50%; object-fit: cover; }

        /* --- OVERLAY & SIDEBAR HP --- */
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050;
        }
        .sidebar-overlay.active { display: block; }

        .mobile-sidebar {
            display: none; position: fixed; top: 0; left: 0;
            width: 260px; height: 100vh; background-color: var(--navy-dark);
            color: #FFFFFF; z-index: 1100; flex-direction: column;
            transform: translateX(-100%); transition: transform 0.3s ease;
        }
        .mobile-sidebar.show { transform: translateX(0); }

        .sidebar-brand {
            padding: 20px; display: flex; align-items: center; gap: 12px;
            border-bottom: 1px solid #1E293B;
        }
        .sidebar-brand img { height: 38px; width: 38px; border-radius: 50%; object-fit: cover; }

        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; }
        .sidebar-menu li a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 25px; color: #94A3B8; text-decoration: none; font-weight: 500; transition: all 0.3s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            color: #FFFFFF; background-color: rgba(56, 189, 248, 0.1); border-left: 4px solid var(--blue-light);
        }

        .sidebar-logout { padding: 20px 25px; border-top: 1px solid #1E293B; }
        .sidebar-logout a { color: #EF4444; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 10px; }

        /* --- NAVBAR DESKTOP --- */
        .navbar { 
            background-color: var(--navy-dark); padding: 1rem 5%;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
        }
        .navbar .logo { 
            color: #FFFFFF; font-size: 1.1rem; font-weight: 700;
            text-decoration: none; display: flex; align-items: center; gap: 12px; line-height: 1.2;
        }
        .navbar .logo img { height: 38px; width: 38px; border-radius: 50%; object-fit: cover; }

        .btn-back { background-color: rgba(255, 255, 255, 0.1); color: #FFFFFF; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; transition: background 0.3s; }
        .btn-back:hover { background-color: rgba(255, 255, 255, 0.2); }

        /* --- FORM PENGATURAN --- */
        .container-setting {
            max-width: 600px;
            margin: 40px auto;
            background: var(--gray-card);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .container-setting h2 { color: var(--navy-dark); margin-bottom: 10px; font-size: 1.5rem; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; color: var(--text-dark); }
        .form-group input {
            width: 100%; padding: 10px 14px;
            border: 1px solid var(--gray-border); border-radius: 6px;
            font-size: 0.95rem; background-color: #F8FAFC;
        }
        .form-group input:focus { outline: none; border-color: var(--blue-light); background-color: #FFF; }
        .form-group input:disabled { background-color: #E2E8F0; color: var(--text-muted); cursor: not-allowed; }
        
        .btn-simpan {
            background-color: var(--navy-primary); color: white;
            padding: 12px 24px; border: none; border-radius: 6px;
            cursor: pointer; font-weight: 600; width: 100%; transition: background 0.3s;
        }
        .btn-simpan:hover { background-color: var(--navy-dark); }

        .alert-error { background-color: #FEE2E2; color: #DC2626; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; }
        .alert-success { background-color: #DEF7EC; color: #03543F; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; }

        @media (max-width: 768px) {
            .navbar { display: none; }
            .mobile-header, .mobile-sidebar { display: flex; }
            .container-setting { margin: 80px 15px 30px 15px; padding: 20px; }
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
                <span class="text-blue">Perpustakaan</span><br>
                <span class="text-white">Digital</span>
            </div>
        </div>
        <div style="width: 24px;"></div>
    </header>

    <!-- OVERLAY SIDEBAR HP -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR MOBILE -->
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
            <li><a href="dashboard_admin.php">📊 Dashboard</a></li>
            <li><a href="data_buku.php">📚 Data Buku</a></li>
            <li><a href="kategori.php">🏷️ Kategori Buku</a></li>
            <li><a href="petugas.php">👨‍💼 Data Petugas</a></li>
            <li><a href="siswa.php">🎓 Data Siswa</a></li>
            <li><a href="laporan.php">📜 Laporan</a></li>
            <li class="active"><a href="pengaturan.php">⚙️ Pengaturan</a></li>
        </ul>

        <div class="sidebar-logout">
            <a href="proses_logout.php">🚪 Keluar</a>
        </div>
    </aside>

    <!-- NAVBAR DESKTOP -->
    <nav class="navbar">
        <a href="dashboard_admin.php" class="logo">
            <img src="logo.png" alt="Logo">
            <div>
                <span class="text-blue">Perpustakaan</span>
                <span class="text-white">Digital</span>
            </div>
        </a>
        <div>
            <a href="dashboard_admin.php" class="btn-back">← Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container-setting">
        <h2>⚙️ PENGATURAN AKUN</h2>
        <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid var(--gray-border);">

        <?php if (!empty($pesan_error)): ?>
            <div class="alert-error"><?= htmlspecialchars($pesan_error); ?></div>
        <?php endif; ?>

        <?php if (!empty($pesan_sukses)): ?>
            <div class="alert-success"><?= htmlspecialchars($pesan_sukses); ?></div>
        <?php endif; ?>

        <form action="proses_pengaturan.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?? ''; ?>">

            <div class="form-group">
                <label>Username (Tidak dapat diubah)</label>
                <input type="text" value="<?= htmlspecialchars($user['username'] ?? ''); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama_lengkap'] ?? $user['nama'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--gray-border);">
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">
                <em>Isi kolom di bawah ini hanya jika ingin mengganti password:</em>
            </p>

            <div class="form-group">
                <label for="password_lama">Password Lama</label>
                <input type="password" id="password_lama" name="password_lama" placeholder="Masukkan password lama">
            </div>

            <div class="form-group">
                <label for="password_baru">Password Baru</label>
                <input type="password" id="password_baru" name="password_baru" placeholder="Masukkan password baru">
            </div>

            <button type="submit" name="update_profil" class="btn-simpan">💾 Simpan Perubahan</button>
        </form>
    </div>

    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            mobileSidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('active');
        }

        if(hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
        if(sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>