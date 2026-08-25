<?php
require_once 'koneksi.php';
require_once 'auth.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $peminjaman_id = $_GET['id'];
    $tgl_sekarang = date('Y-m-d');

    // Update status transaksi peminjaman menjadi dikembalikan
    $query = "UPDATE peminjaman 
              SET status = 'dikembalikan', tanggal_pengembalian = '$tgl_sekarang' 
              WHERE id = '$peminjaman_id'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: riwayat.php?status=success");
    } else {
        echo "<script>alert('Gagal mengembalikan buku'); window.location.href='riwayat.php';</script>";
    }
} else {
    header("Location: dashboard_siswa.php");
}