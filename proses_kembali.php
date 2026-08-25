<?php
require_once 'koneksi.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peminjaman_id = (int)$_POST['peminjaman_id'];
    $buku_id       = (int)$_POST['buku_id'];
    $denda         = (int)$_POST['denda'];
    $tgl_dikembalikan = date('Y-m-d');

    // 1. Update status peminjaman dan denda
    $query_update = "UPDATE peminjaman 
                     SET status = 'dikembalikan', 
                         tanggal_pengembalian = '$tgl_dikembalikan', 
                         denda = $denda 
                     WHERE id = $peminjaman_id";
    
    // 2. Kembalikan stok buku (+1)
    $query_stok = "UPDATE buku SET stok = stok + 1 WHERE id = $buku_id";

    if (mysqli_query($koneksi, $query_update) && mysqli_query($koneksi, $query_stok)) {
        header("Location: pengembalian.php?msg=sukses_kembali");
        exit;
    } else {
        die("Gagal memproses pengembalian: " . mysqli_error($koneksi));
    }
} else {
    header("Location: pengembalian.php");
    exit;
}