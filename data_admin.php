<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Proteksi Ketat: Hanya Admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil data seluruh Admin dari database
$query_admin = "SELECT * FROM users WHERE role = 'admin' ORDER BY id DESC";
$result_admin = mysqli_query($koneksi, $query_admin);

$pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'sukses_tambah') $pesan = 'Akun Administrator baru berhasil ditambahkan!';
    elseif ($_GET['msg'] === 'sukses_hapus') $pesan = 'Akun Administrator berhasil dihapus.';
    elseif ($_GET['msg'] === 'gagal') $pesan = 'Terjadi kesalahan sistem, silakan coba lagi.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Admin - <?= NAMA_APLIKASI; ?></title>
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
            z-index: 100;
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
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 30px;
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

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
        }

        .card {
            background: var(--gray-card);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            padding: 25px;
        }

        .card h3 {
            font-size: 1.1rem;
            color: var(--navy-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--gray-border);
        }

        /* --- FORM --- */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--gray-border);
            border-radius: 6px;
            outline: none;
            font-size: 0.9rem;
        }

        .btn-submit {
            width: 100%;
            background-color: var(--navy-primary);
            color: #FFFFFF;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background-color: var(--navy-dark);
        }

        /* --- TABLE --- */
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-border);
            font-size: 0.88rem;
        }

        th {
            background-color: var(--gray-bg);
            color: var(--navy-dark);
        }

        .btn-delete {
            background-color: #EF4444;
            color: #FFFFFF;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-delete:hover {
            background-color: #DC2626;
        }

        .badge-self {
            background-color: var(--blue-bg);
            color: var(--navy-primary);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR NAVIGASI -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="logo.png" alt="Logo">
            <div>
                <span>Perpus</span>Digital
                <div style="font-size: 0.7rem; color: #94A3B8;">SMK Bhakti Putra</div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="dashboard_admin.php">📊 Dashboard</a></li>
            <li><a href="data_buku.php">📚 Data Buku</a></li>
            <li><a href="kategori.php">🏷️ Kategori Buku</a></li>
            <li class="active"><a href="data_admin.php">👑 Data Admin</a></li>
            <li><a href="data_petugas.php">👨‍💼 Data Petugas</a></li>
            <li><a href="data_siswa.php">🎓 Data Siswa</a></li>
            <li><a href="laporan.php">📑 Laporan</a></li>
        </ul>

        <div class="btn-logout">
            <a href="proses_logout.php">🚪 Keluar</a>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">
        <div class="top-header">
            <h2>Kelola Akun Administrator</h2>
        </div>

        <?php if ($pesan): ?>
            <div class="alert"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <div class="grid-container">
            <!-- FORM TAMBAH ADMIN -->
            <div class="card">
                <h3>Tambah Admin Baru</h3>
                <form action="proses_admin.php" method="POST">
                    <input type="hidden" name="aksi" value="tambah">

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" name="email" placeholder="contoh@sekolah.sch.id" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                    </div>

                    <button type="submit" class="btn-submit">➕ Simpan Administrator</button>
                </form>
            </div>

            <!-- TABEL DAFTAR ADMIN -->
            <div class="card">
                <h3>Daftar Administrator</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_admin) > 0): $no = 1; ?>
                            <?php while ($admin = mysqli_fetch_assoc($result_admin)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= htmlspecialchars($admin['nama_lengkap']); ?></strong></td>
                                    <td><?= htmlspecialchars($admin['email']); ?></td>
                                    <td>
                                        <?php if ($admin['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge-self">Saya</span>
                                        <?php else: ?>
                                            <a href="proses_admin.php?aksi=hapus&id=<?= $admin['id']; ?>" class="btn-delete" onclick="return confirm('Apakah kamu yakin ingin menghapus administrator ini?')">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">Belum ada data admin.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>