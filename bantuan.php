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
    <title>Pusat Bantuan - Perpustakaan Digital</title>
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

        .hero-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .hero-section h1 {
            color: var(--navy-dark);
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .hero-section p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* --- FAQ ACCORDION --- */
        .faq-section {
            background: var(--gray-card);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .faq-item {
            border-bottom: 1px solid var(--gray-border);
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            padding: 18px 10px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--navy-dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: color 0.3s;
        }

        .faq-question:hover {
            color: var(--navy-primary);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, padding 0.3s ease;
            color: var(--text-muted);
            font-size: 0.93rem;
            line-height: 1.6;
            padding: 0 10px;
        }

        .faq-item.active .faq-answer {
            max-height: 200px;
            padding-bottom: 18px;
        }

        .faq-item.active .icon {
            transform: rotate(180deg);
        }

        .icon {
            transition: transform 0.3s;
            font-size: 0.8rem;
        }

        /* --- CONTACT CARD --- */
        .contact-card {
            background: linear-gradient(135deg, var(--navy-primary) 0%, var(--navy-dark) 100%);
            color: #FFFFFF;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
        }

        .contact-card h3 {
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #FFFFFF;
        }

        .contact-card p {
            color: #CBD5E1;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .contact-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
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
        <div class="hero-section">
            <h1>Pusat Bantuan & Layanan</h1>
            <p>Punya pertanyaan seputar peminjaman buku digital? Temukan jawabannya di bawah ini.</p>
        </div>

        <!-- FAQ SECTION -->
        <div class="faq-section">
            <div class="faq-item">
                <button class="faq-question">
                    <span>📚 Bagaimana cara meminjam buku online?</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    Kamu bisa memilih buku melalui halaman Katalog, klik tombol <strong>"Detail & Baca"</strong> pada buku yang diinginkan, kemudian klik tombol <strong>"Ajukan Peminjaman"</strong>.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>📖 Bagaimana cara membaca e-book yang sudah dipinjam?</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    Setelah berhasil meminjam, buka menu <strong>"Bacaan Saya"</strong> atau masuk ke halaman Detail Buku, lalu klik tombol <strong>"Baca Sekarang"</strong> untuk membukanya langsung di browser.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>⌛ Berapa lama durasi peminjaman buku?</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    Batas waktu standar peminjaman buku adalah <strong>7 hari</strong>. Pastikan untuk mengembalikan buku sebelum atau tepat pada batas tanggal pengembalian.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>🔑 Bagaimana jika saya lupa kata sandi akun?</span>
                    <span class="icon">▼</span>
                </button>
                <div class="faq-answer">
                    Kamu dapat menghubungi petugas perpustakaan di sekolah secara langsung untuk membantu melakukan reset kata sandi akun kamu.
                </div>
            </div>
        </div>

        <!-- CONTACT CARD -->
        <div class="contact-card">
            <h3>Masih Membutuhkan Bantuan?</h3>
            <p>Petugas perpustakaan kami siap membantu kamu pada jam kerja sekolah.</p>
            <div class="contact-info">
                <div class="contact-item">📧 perpustakaan@sekolah.sch.id</div>
                <div class="contact-item">📞 (022) 123-4567</div>
                <div class="contact-item">📍 Ruang Perpustakaan Lt. 2</div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT UNTUK ACCORDION -->
    <script>
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const faqItem = button.parentElement;
                
                // Toggle kelas active pada item yang diklik
                faqItem.classList.toggle('active');

                // Tutup item lain
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    if (otherItem !== faqItem) {
                        otherItem.classList.remove('active');
                    }
                });
            });
        });
    </script>
</body>
</html>