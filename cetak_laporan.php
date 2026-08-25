<?php
session_start();
require_once 'koneksi.php';

// Cek session login
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Tangkap filter dari URL (default: semua)
$filter = isset($_GET['filter']) ? $_GET['filter'] : (isset($_GET['status']) ? $_GET['status'] : 'semua');
$where_clause = "";

if ($filter === 'dipinjam') {
    $where_clause = "WHERE p.status_pinjam = 'dipinjam' OR p.status = 'dipinjam'";
} elseif ($filter === 'dikembalikan' || $filter === 'kembali') {
    $where_clause = "WHERE p.status_pinjam = 'dikembalikan' OR p.status = 'dikembalikan'";
}

// Query TANPA tabel 'siswa' (menggunakan tabel 'users' & 'buku')
$query_str = "SELECT p.*, 
                     u.nama_lengkap AS nama_peminjam, 
                     b.judul AS judul_buku
              FROM peminjaman p 
              LEFT JOIN users u ON p.user_id = u.id 
              LEFT JOIN buku b ON p.buku_id = b.id 
              $where_clause 
              ORDER BY p.tanggal_pinjam DESC";

$result = mysqli_query($koneksi, $query_str);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Transaksi Peminjaman</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            margin: 20px;
            position: relative;
        }

        /* LOGO WATERMARK SAMAR DI TENGAH */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.10;
            width: 320px;
            z-index: -1;
            pointer-events: none;
        }

        /* KOP LAPORAN */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header h3 { margin: 5px 0; font-size: 14px; color: #333; }
        .header p { margin: 0; font-size: 11px; color: #555; }

        /* TABEL LAPORAN */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
            background: transparent;
        }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: rgba(230, 230, 230, 0.6); }

        .ttd {
            margin-top: 40px;
            float: right;
            text-align: center;
            width: 200px;
        }

        /* SEMBUNYIKAN TOMBOL SAAT PRINT */
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- LOGO WATERMARK -->
    <img src="logo.png" class="watermark" alt="Logo Watermark" onerror="this.style.display='none'">

    <!-- TOMBOL AKSI DI LAYAR -->
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 15px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold;">🖨️ Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 8px 15px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; font-weight: bold;">❌ Tutup</button>
    </div>

    <!-- KOP HEADER -->
    <div class="header">
        <h2>PERPUS DIGITAL - SMK BHAKTI PUTRA</h2>
        <h3>LAPORAN TRANSAKSI PEMINJAMAN BUKU</h3>
        <p>Dicetak Tanggal: <?= date('d/m/Y H:i'); ?></p>
    </div>

    <!-- ISI TABEL -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th>Nama Siswa</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Batas Kembali</th>
                <th>Tgl Dikembalikan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if ($result && mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)): 
                    // Fallback nama kolom tanggal dan status
                    $tgl_pinjam   = $row['tanggal_pinjam'] ?? $row['tgl_pinjam'] ?? null;
                    $tgl_rencana  = $row['tanggal_kembali_rencana'] ?? $row['tanggal_kembali'] ?? $row['batas_kembali'] ?? null;
                    $tgl_real     = $row['tanggal_kembali_real'] ?? $row['tanggal_dikembalikan'] ?? $row['tgl_kembali'] ?? null;
                    $status_val   = $row['status_pinjam'] ?? $row['status'] ?? 'dipinjam';
            ?>
                <tr>
                    <td style="text-align: center;"><?= $no++; ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_peminjam'] ?? '-'); ?></strong></td>
                    <td><?= htmlspecialchars($row['judul_buku'] ?? '-'); ?></td>
                    <td><?= !empty($tgl_pinjam) ? date('d/m/Y', strtotime($tgl_pinjam)) : '-'; ?></td>
                    <td><?= !empty($tgl_rencana) ? date('d/m/Y', strtotime($tgl_rencana)) : '-'; ?></td>
                    <td><?= !empty($tgl_real) ? date('d/m/Y', strtotime($tgl_real)) : '-'; ?></td>
                    <td style="text-transform: capitalize;"><?= htmlspecialchars($status_val); ?></td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 15px;">Belum ada data transaksi peminjaman.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN PETUGAS -->
    <div class="ttd">
        <p>Petugas Perpustakaan,</p>
        <br><br><br>
        <p><strong>( <u><?= htmlspecialchars($_SESSION['username'] ?? $_SESSION['nama_lengkap'] ?? 'Petugas'); ?></u> )</strong></p>
    </div>

</body>
</html>