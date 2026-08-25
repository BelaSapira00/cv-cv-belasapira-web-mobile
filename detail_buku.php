<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID buku dari URL
$buku_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query_buku = "SELECT b.*, k.nama_kategori 
               FROM buku b 
               LEFT JOIN kategori k ON b.kategori_id = k.id 
               WHERE b.id = $buku_id";
$result_buku = mysqli_query($koneksi, $query_buku);
$buku = mysqli_fetch_assoc($result_buku);

// Jika buku tidak ditemukan
if (!$buku) {
    header("Location: catalog.php");
    exit;
}

$pesan = '';
$tipe_pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'sukses') {
        $pesan = 'Pengajuan peminjaman berhasil! Silakan ambil buku di perpustakaan.';
        $tipe_pesan = 'success';
    } elseif ($_GET['msg'] === 'stok_habis') {
        $pesan = 'Maaf, stok buku ini sedang habis.';
        $tipe_pesan = 'error';
    } elseif ($_GET['msg'] === 'sudah_pinjam') {
        $pesan = 'Kamu masih meminjam buku ini dan belum mengembalikannya.';
        $tipe_pesan = 'error';
    } elseif ($_GET['msg'] === 'gagal') {
        $pesan = 'Terjadi kesalahan sistem, silakan coba lagi.';
        $tipe_pesan = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($buku['judul']); ?> - <?= NAMA_APLIKASI; ?></title>
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
            color: #FFFFFF;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--blue-light);
            text-decoration: none;
        }

        .navbar-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar-nav a {
            color: #94A3B8;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar-nav a:hover {
            color: #FFFFFF;
        }

        /* --- CONTAINER --- */
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-back:hover {
            color: var(--navy-primary);
        }

        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #DEF7EC;
            color: #03543F;
            border: 1px solid #BCF0DA;
        }

        .alert-error {
            background-color: #FEF2F2;
            color: #DC2626;
            border: 1px solid #FECACA;
        }

        .card-detail {
            background: var(--gray-card);
            border-radius: 12px;
            border: 1px solid var(--gray-border);
            padding: 30px;
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 30px;
        }

        .cover-wrapper img {
            width: 100%;
            height: 330px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .book-info h1 {
            font-size: 1.8rem;
            color: var(--navy-dark);
            margin-bottom: 8px;
        }

        .category-badge {
            display: inline-block;
            background-color: var(--blue-bg);
            color: var(--navy-primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px 0;
            border-top: 1px solid var(--gray-border);
            border-bottom: 1px solid var(--gray-border);
        }

        .meta-item span {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .meta-item strong {
            font-size: 0.95rem;
            color: var(--text-dark);
        }

        .synopsis {
            font-size: 0.92rem;
            line-height: 1.6;
            color: var(--text-dark);
            margin-bottom: 25px;
        }

        .btn-pinjam {
            background-color: var(--navy-primary);
            color: #FFFFFF;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-pinjam:hover {
            background-color: var(--navy-dark);
        }

        .btn-disabled {
            background-color: #94A3B8;
            cursor: not-allowed;
        }

        @media (max-width: 640px) {
            .card-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="#" class="navbar-brand">PerpusDigital</a>
        <div class="navbar-nav">
            <a href="catalog.php">📚 Katalog Buku</a>
            <a href="riwayat.php">📜 Riwayat Pinjam</a>
            <a href="proses_logout.php" style="color: #EF4444;">🚪 Keluar</a>
        </div>
    </nav>

    <div class="container">
        <a href="catalog.php" class="btn-back">← Kembali ke Katalog</a>

        <?php if ($pesan): ?>
            <div class="alert alert-<?= $tipe_pesan; ?>"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <div class="card-detail">
            <div class="cover-wrapper">
                <img src="uploads/covers/<?= !empty($buku['cover']) ? $buku['cover'] : 'default_cover.jpg'; ?>" alt="<?= htmlspecialchars($buku['judul']); ?>">
            </div>

            <div class="book-info">
                <h1><?= htmlspecialchars($buku['judul']); ?></h1>
                <span class="category-badge"><?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum'); ?></span>

                <div class="meta-grid">
                    <div class="meta-item">
                        <span>Pengarang</span>
                        <strong><?= htmlspecialchars($buku['penulis']); ?></strong>
                    </div>
                    <div class="meta-item">
                        <span>Penerbit</span>
                        <strong><?= htmlspecialchars($buku['penerbit']); ?> (<?= $buku['tahun_terbit']; ?>)</strong>
                    </div>
                    <div class="meta-item">
                        <span>Kode ISBN</span>
                        <strong><?= htmlspecialchars($buku['isbn'] ?? '-'); ?></strong>
                    </div>
                    <div class="meta-item">
                        <span>Sisa Stok</span>
                        <strong><?= $buku['stok']; ?> Eksemplar</strong>
                    </div>
                </div>

                <div class="synopsis">
                    <strong>Deskripsi Buku:</strong>
                    <p><?= nl2br(htmlspecialchars($buku['deskripsi'] ?? 'Tidak ada deskripsi.')); ?></p>
                </div>

                <?php if ($_SESSION['role'] === 'siswa'): ?>
                    <?php if ($buku['stok'] > 0): ?>
                        <form action="proses_pinjam.php" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin meminjam buku ini?')">
                            <input type="hidden" name="buku_id" value="<?= $buku['id']; ?>">
                            <button type="submit" class="btn-pinjam">📖 Ajukan Peminjaman</button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn-pinjam btn-disabled" disabled>Stok Habis</button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>