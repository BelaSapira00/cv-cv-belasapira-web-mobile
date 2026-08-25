<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Hanya Admin yang bisa mengelola data petugas
cek_akses('admin');

// Ambil semua pengguna dengan role petugas
$query_petugas = "SELECT * FROM users WHERE role = 'petugas' ORDER BY dibuat_pada DESC";
$result_petugas = mysqli_query($koneksi, $query_petugas);

$pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'sukses_tambah') $pesan = 'Akun petugas berhasil ditambahkan!';
    elseif ($_GET['msg'] === 'sukses_hapus') $pesan = 'Akun petugas berhasil dihapus!';
    elseif ($_GET['msg'] === 'email_kembar') $pesan = 'Email sudah terdaftar! Gunakan email lain.';
    elseif ($_GET['msg'] === 'gagal') $pesan = 'Terjadi kesalahan, silakan coba lagi.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Petugas - <?= NAMA_APLIKASI; ?></title>
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

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .alert {
            background-color: #DEF7EC;
            color: #03543F;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            border: 1px solid #BCF0DA;
        }

        .alert-error {
            background-color: #FEF2F2;
            color: #DC2626;
            border-color: #FECACA;
        }

        .card {
            background: var(--gray-card);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            padding: 25px;
            margin-bottom: 30px;
        }

        .card h3 {
            margin-bottom: 15px;
            color: var(--navy-dark);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--navy-dark);
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-border);
            border-radius: 6px;
            outline: none;
        }

        .btn-add {
            background-color: var(--navy-primary);
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-add:hover {
            background-color: var(--navy-dark);
        }

        /* --- TABLE --- */
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
            font-size: 0.88rem;
            white-space: nowrap;
        }

        th {
            background-color: var(--gray-bg);
            color: var(--navy-dark);
        }

        .btn-danger {
            color: #DC2626;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-danger:hover {
            text-decoration: underline;
        }

        .status-badge {
            background-color: #DEF7EC;
            color: #03543F;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
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

            .btn-add {
                width: 100%;
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

    <!-- SIDEBAR NAVIGASI ADMIN -->
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
            <li><a href="data_buku.php">📚 Data Buku</a></li>
            <li><a href="kategori.php">🏷️ Kategori Buku</a></li>
            <li class="active"><a href="data_petugas.php">👨‍💼 Data Petugas</a></li>
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
        <div class="top-header">
            <h2>Kelola Data Petugas Perpustakaan</h2>
        </div>

        <?php if ($pesan): ?>
            <div class="alert <?= strpos($_GET['msg'], 'sukses') === false ? 'alert-error' : ''; ?>">
                <?= htmlspecialchars($pesan); ?>
            </div>
        <?php endif; ?>

        <!-- FORM TAMBAH PETUGAS -->
        <div class="card">
            <h3>Tambah Petugas Baru</h3>
            <form action="proses_petugas.php" method="POST">
                <input type="hidden" name="action" value="tambah">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama petugas" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" placeholder="petugas@sekolah.sch.id" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" minlength="6" required>
                    </div>
                </div>

                <button type="submit" class="btn-add">Tambah Petugas</button>
            </form>
        </div>

        <!-- TABEL DATA PETUGAS -->
        <div class="card">
            <h3>Daftar Petugas</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Terdaftar</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_petugas) > 0): $no = 1; ?>
                            <?php while ($petugas = mysqli_fetch_assoc($result_petugas)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= htmlspecialchars($petugas['nama_lengkap']); ?></strong></td>
                                    <td><?= htmlspecialchars($petugas['email']); ?></td>
                                    <td><span class="status-badge">Terverifikasi</span></td>
                                    <td><?= date('d/m/Y', strtotime($petugas['dibuat_pada'])); ?></td>
                                    <td>
                                        <a href="proses_petugas.php?action=hapus&id=<?= $petugas['id']; ?>" class="btn-danger" onclick="return confirm('Yakin ingin menghapus akun petugas ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada akun petugas terdaftar.</td>
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