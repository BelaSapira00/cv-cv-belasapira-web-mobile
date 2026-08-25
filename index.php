<?php
require_once 'koneksi.php';

// Ambil beberapa sampel buku untuk pengunjung
$query_buku = "SELECT b.*, k.nama_kategori FROM buku b LEFT JOIN kategori k ON b.kategori_id = k.id LIMIT 6";
$result_buku = mysqli_query($koneksi, $query_buku);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= NAMA_APLIKASI; ?> - SMK Bhakti Putra</title>
    <style>
        /* --- PALET WARNA & BASE RESET --- */
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
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* --- NAVBAR --- */
        .navbar {
            background-color: rgba(10, 25, 47, 0.95);
            backdrop-filter: blur(8px);
            padding: 0.8rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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

        .navbar .logo img, 
        .logo-img {
            height: 42px !important;  
            width: 42px !important;   
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

        .nav-links a:hover {
            color: var(--blue-light);
        }

        .btn-login {
            background-color: var(--blue-light);
            color: var(--navy-dark) !important;
            padding: 8px 22px;
            border-radius: 6px;
            font-weight: 700 !important;
            transition: all 0.3s !important;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.4);
        }

        .btn-login:hover {
            background-color: #7DD3FC;
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.6);
        }

        /* --- HAMBURGER BUTTON (MOBILE) --- */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 28px;
            height: 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1001;
        }

        .hamburger span {
            width: 100%;
            height: 3px;
            background-color: #FFFFFF;
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }

        /* Animasi Hamburger saat Aktif */
        .hamburger.active span:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        .hamburger.active span:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }

        /* --- OVERLAY BACKDROP --- */
        .backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 25, 47, 0.7);
            backdrop-filter: blur(4px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 998;
        }

        .backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* --- HERO BANNER BACKGROUND --- */
        .hero-banner {
            width: 100%;
            min-height: 85vh; 
            background: url('bg_pengunjung.png') no-repeat center center/cover;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 40px;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background: linear-gradient(to top, var(--gray-bg), transparent);
        }

        .hero-cta {
            position: relative;
            z-index: 2;
            background: rgba(10, 25, 47, 0.85);
            padding: 15px 30px;
            border-radius: 50px;
            border: 1px solid var(--blue-light);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            text-align: center;
        }

        .hero-cta p {
            color: #FFFFFF;
            font-size: 1rem;
            margin: 0;
        }

        .hero-cta a {
            color: var(--blue-light);
            font-weight: 700;
            text-decoration: underline;
        }

        /* --- KATALOG SECTION --- */
        .container {
            max-width: 1200px;
            margin: 20px auto 60px;
            padding: 0 20px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-header h2 {
            font-size: 2rem;
            color: var(--navy-dark);
            margin-bottom: 8px;
        }

        .section-header p {
            color: var(--text-muted);
        }

        .grid-buku {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
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
            height: 220px;
            background-color: var(--blue-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy-primary);
            font-weight: 600;
            font-size: 1.1rem;
            padding: 15px;
            text-align: center;
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
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            width: fit-content;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .card-title {
            font-size: 1.15rem;
            color: var(--navy-dark);
            margin-bottom: 6px;
        }

        .card-author {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 18px;
        }

        .btn-baca {
            margin-top: auto;
            display: block;
            text-align: center;
            background-color: var(--navy-primary);
            color: #FFFFFF;
            text-decoration: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-baca:hover {
            background-color: var(--navy-dark);
        }

        /* --- FOOTER --- */
        footer {
            background-color: var(--navy-dark);
            color: #94A3B8;
            text-align: center;
            padding: 25px;
            font-size: 0.9rem;
            border-top: 1px solid #1E293B;
        }

        /* --- MEDIA QUERIES FOR MOBILE & TABLET (SIDEBAR RESPONSIF) --- */
        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .nav-links {
                position: fixed;
                top: 0;
                right: -280px;
                width: 280px;
                height: 100vh;
                background-color: var(--navy-dark);
                flex-direction: column;
                justify-content: flex-start;
                align-items: stretch;
                padding: 80px 25px 30px;
                gap: 20px;
                transition: right 0.3s ease-in-out;
                z-index: 999;
                box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
            }

            .nav-links.active {
                right: 0;
            }

            .nav-links a {
                font-size: 1.05rem;
                padding: 10px 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .btn-login {
                text-align: center;
                margin-top: 10px;
            }

            .hero-cta {
                width: 90%;
                padding: 12px 20px;
            }

            .hero-cta p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <!-- OVERLAY BACKDROP -->
    <div class="backdrop" id="backdrop"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="logo">
            <img src="logo.png" alt="Logo SMK Bhakti Putra" class="logo-img">
            <span>Perpustakaan</span>Digital
        </a>

        <!-- TOMBOL HAMBURGER MOBILE -->
        <button class="hamburger" id="hamburger" aria-label="Toggle Navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- SIDEBAR MENU / NAV-LINKS -->
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php">Katalog</a></li>
            <li><a href="login.php" class="btn-login">Masuk / Daftar</a></li>
        </ul>
    </nav>

    <!-- HERO BACKGROUND GAMBAR -->
    <section class="hero-banner">
        <div class="hero-cta">
            <p>Ingin membaca koleksi buku digital? <a href="login.php">Silakan Login Terlebih Dahulu</a></p>
        </div>
    </section>

    <!-- KATALOG UTAMA -->
    <div class="container">
        <div class="section-header">
            <h2>Katalog Buku Terbaru</h2>
            <p>Temukan berbagai macam materi pembelajaran, pemrograman, dan pustaka digital sekolah</p>
        </div>

        <div class="grid-buku">
            <?php if (mysqli_num_rows($result_buku) > 0): ?>
                <?php while ($buku = mysqli_fetch_assoc($result_buku)): ?>
                    <div class="card-buku">
                        <div class="card-img">
                            📖 <?= htmlspecialchars($buku['judul']); ?>
                        </div>
                        <div class="card-body">
                            <span class="badge-kategori"><?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum'); ?></span>
                            <h3 class="card-title"><?= htmlspecialchars($buku['judul']); ?></h3>
                            <p class="card-author">Penulis: <?= htmlspecialchars($buku['penulis']); ?></p>
                            <a href="login.php?msg=harus_login" class="btn-baca">Baca Buku</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; grid-column: 1/-1;">Belum ada buku yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <p>&copy; <?= date('Y'); ?> Perpustakaan Digital. All rights reserved.</p>
    </footer>

    <!-- JAVASCRIPT UNTUK TOGGLE SIDEBAR MOBILE -->
    <script>
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');
        const backdrop = document.getElementById('backdrop');

        function toggleMenu() {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
            backdrop.classList.toggle('active');
            
            // Cegah scroll pada body saat menu terbuka
            document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : 'auto';
        }

        hamburger.addEventListener('click', toggleMenu);
        backdrop.addEventListener('click', toggleMenu);

        // Menutup menu jika salah satu link di klik
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                if (navLinks.classList.contains('active')) {
                    toggleMenu();
                }
            });
        });
    </script>
</body>
</html>