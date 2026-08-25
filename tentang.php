<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? 'siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Aplikasi - Perpustakaan Digital</title>
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

        /* --- NAVBAR --- */
        .navbar {
            background-color: var(--navy-dark);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .navbar .logo span {
            color: var(--blue-light);
        }

        .btn-back {
            background-color: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* --- CONTAINER --- */
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .about-card {
            background: var(--gray-card);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            padding: 35px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .about-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--gray-border);
            margin-bottom: 25px;
        }

        .about-header h1 {
            color: var(--navy-dark);
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .version-badge {
            background-color: var(--blue-bg);
            color: var(--navy-primary);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
        }

        .about-content {
            line-height: 1.7;
            font-size: 0.95rem;
            color: var(--text-dark);
        }

        .about-content h3 {
            color: var(--navy-dark);
            margin: 25px 0 15px 0;
            font-size: 1.1rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .feature-card {
            background: var(--gray-bg);
            padding: 18px;
            border-radius: 8px;
            border-left: 4px solid var(--navy-primary);
        }

        .feature-card h4 {
            margin-bottom: 6px;
            color: var(--navy-dark);
            font-size: 0.95rem;
        }

        .feature-card p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .tech-list {
            list-style: none;
            padding-left: 0;
        }

        .tech-list li {
            padding: 6px 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .tech-list strong {
            color: var(--navy-dark);
        }

        .footer-info {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-border);
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="#" class="logo">
            <span>Perpus</span>Digital
        </a>
        <div>
            <?php 
                $dashboard_link = ($role === 'admin') ? 'dashboard_admin.php' : (($role === 'petugas') ? 'dashboard_petugas.php' : 'dashboard_siswa.php');
            ?>
            <a href="<?= $dashboard_link; ?>" class="btn-back">← Kembali ke Dashboard</a>
        </div>
    </nav>

    <!-- CONTAINER UTAMA -->
    <div class="container">
        <div class="about-card">
            <div class="about-header">
                <h1>Perpustakaan Digital Sekolah</h1>
                <span class="version-badge">Versi 1.0.0</span>
            </div>

            <div class="about-content">
                <p>
                    <strong>Perpustakaan Digital</strong> adalah platform membaca berbasis web yang dirancang untuk mempermudah siswa dan pengajar dalam mengakses, meminjam, dan membaca koleksi buku digital kapan saja dan di mana saja secara <em>online</em>.
                </p>

                <h3>Fitur Utama Aplikasi:</h3>
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>📚 Katalog Buku</h4>
                        <p>Eksplorasi koleksi buku digital berdasarkan kategori, penulis, dan judul.</p>
                    </div>
                    <div class="feature-card">
                        <h4>📖 E-Reader Integritas</h4>
                        <p>Fasilitas membaca buku digital langsung dari browser tanpa perlu mengunduh file.</p>
                    </div>
                    <div class="feature-card">
                        <h4>⌛ Peminjaman Otomatis</h4>
                        <p>Pengajuan dan manajemen waktu peminjaman buku dengan riwayat terorganisir.</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔒 Multi-Hak Akses</h4>
                        <p>Akses khusus terpisah untuk Admin, Petugas Perpustakaan, dan Siswa.</p>
                    </div>
                </div>

                <h3>Teknologi yang Digunakan:</h3>
                <ul class="tech-list">
                    <li><strong>Language:</strong> PHP 8.x</li>
                    <li><strong>Database:</strong> MySQL / MariaDB</li>
                    <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript</li>
                </ul>
            </div>

            <div class="footer-info">
                &copy; <?= date('Y'); ?> Perpustakaan Digital. All rights reserved.
            </div>
        </div>
    </div>

</body>
</html>