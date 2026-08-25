<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Hanya Admin dan Petugas yang bisa mengakses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: login.php");
    exit;
}

// Ambil semua data pengguna dengan role siswa
$query_siswa = "SELECT * FROM users WHERE role = 'siswa' ORDER BY dibuat_pada DESC";
$result_siswa = mysqli_query($koneksi, $query_siswa);

$pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'sukses_verifikasi') $pesan = 'Status siswa berhasil diverifikasi secara manual!';
    elseif ($_GET['msg'] === 'sukses_hapus') $pesan = 'Data siswa berhasil dihapus!';
    elseif ($_GET['msg'] === 'sukses_edit') $pesan = 'Data siswa berhasil diperbarui!';
    elseif ($_GET['msg'] === 'gagal') $pesan = 'Terjadi kesalahan, silakan coba lagi.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - <?= NAMA_APLIKASI; ?></title>
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

        /* --- OVERLAY BACKDROP SIDEBAR --- */
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

        /* --- TABLE & BUTTONS --- */
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

        .btn-warning {
            color: #D97706;
            text-decoration: none;
            font-weight: 600;
            margin-right: 10px;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 0.88rem;
        }

        .btn-warning:hover {
            text-decoration: underline;
        }

        .btn-success {
            color: #059669;
            text-decoration: none;
            font-weight: 600;
            margin-right: 10px;
        }

        .btn-success:hover {
            text-decoration: underline;
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
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-verifikasi {
            background-color: #DEF7EC;
            color: #03543F;
        }

        .status-pending {
            background-color: #FEF08A;
            color: #713F12;
        }

        /* --- MODAL EDIT --- */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 25, 47, 0.6);
            backdrop-filter: blur(4px);
            z-index: 300;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #FFFFFF;
            border-radius: 12px;
            width: 100%;
            max-width: 480px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--gray-border);
            padding-bottom: 10px;
        }

        .modal-header h3 {
            color: var(--navy-dark);
            font-size: 1.2rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--gray-border);
            border-radius: 6px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--blue-light);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-submit {
            background-color: var(--navy-primary);
            color: #FFFFFF;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: var(--navy-dark);
        }

        .btn-cancel {
            background-color: #E2E8F0;
            color: var(--text-dark);
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
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
            <li><a href="<?= $_SESSION['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard_petugas.php'; ?>">📊 Dashboard</a></li>
            <li><a href="data_buku.php">📚 Data Buku</a></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="kategori.php">🏷️ Kategori Buku</a></li>
                <li><a href="data_petugas.php">👨‍💼 Data Petugas</a></li>
            <?php endif; ?>
            <li class="active"><a href="data_siswa.php">🎓 Data Siswa</a></li>
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
            <h2>Data Siswa Terdaftar</h2>
        </div>

        <?php if ($pesan): ?>
            <div class="alert"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <!-- TABEL DATA SISWA -->
        <div class="card">
            <h3>Daftar Siswa</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status Verifikasi</th>
                            <th>Tanggal Daftar</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_siswa) > 0): $no = 1; ?>
                            <?php while ($siswa = mysqli_fetch_assoc($result_siswa)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= htmlspecialchars($siswa['nama_lengkap']); ?></strong></td>
                                    <td><?= htmlspecialchars($siswa['email']); ?></td>
                                    <td>
                                        <?php if ($siswa['status_verifikasi'] === 'terverifikasi'): ?>
                                            <span class="status-badge status-verifikasi">Terverifikasi</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pending">Pending OTP</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($siswa['dibuat_pada'])); ?></td>
                                    <td>
                                        <button type="button" class="btn-warning" onclick="openEditModal(<?= htmlspecialchars(json_encode($siswa)); ?>)">Edit</button>
                                        
                                        <?php if ($siswa['status_verifikasi'] === 'pending'): ?>
                                            <a href="proses_siswa.php?action=verifikasi&id=<?= $siswa['id']; ?>" class="btn-success" onclick="return confirm('Verifikasi siswa ini secara manual?')">Verifikasi</a>
                                        <?php endif; ?>
                                        
                                        <a href="proses_siswa.php?action=hapus&id=<?= $siswa['id']; ?>" class="btn-danger" onclick="return confirm('Yakin ingin menghapus data siswa ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada siswa terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL EDIT SISWA -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Data Siswa</h3>
                <button type="button" class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="proses_siswa.php?action=edit" method="POST">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_nama">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="edit_status">Status Verifikasi</label>
                    <select name="status_verifikasi" id="edit_status" class="form-control" required>
                        <option value="terverifikasi">Terverifikasi</option>
                        <option value="pending">Pending OTP</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT HAMBURGER & MODAL -->
    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const editModal = document.getElementById('editModal');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('active');
        }

        hamburgerBtn.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Fungsi Modal Edit
        function openEditModal(siswa) {
            document.getElementById('edit_id').value = siswa.id;
            document.getElementById('edit_nama').value = siswa.nama_lengkap;
            document.getElementById('edit_email').value = siswa.email;
            document.getElementById('edit_status').value = siswa.status_verifikasi;
            editModal.classList.add('active');
        }

        function closeEditModal() {
            editModal.classList.remove('active');
        }

        // Tutup modal jika klik di luar box modal
        window.addEventListener('click', (e) => {
            if (e.target === editModal) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>