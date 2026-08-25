<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Hanya Siswa yang boleh mengajukan peminjaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buku_id'])) {
    $user_id = $_SESSION['user_id'];
    $buku_id = (int)$_POST['buku_id'];

    // 1. Cek apakah stok buku masih ada
    $query_buku = "SELECT stok FROM buku WHERE id = $buku_id";
    $result_buku = mysqli_query($koneksi, $query_buku);
    $buku = mysqli_fetch_assoc($result_buku);

    if (!$buku || $buku['stok'] <= 0) {
        header("Location: detail_buku.php?id=$buku_id&msg=stok_habis");
        exit;
    }

    // 2. Cek apakah siswa sedang meminjam buku yang sama dan belum dikembalikan
    $query_cek ="SELECT id FROM peminjaman 
                WHERE user_id = '$user_id' AND buku_id = '$buku_id' AND status = 'dipinjam'";
    $result_cek = mysqli_query($koneksi, $query_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        header("Location: detail_buku.php?id=$buku_id&msg=sudah_pinjam");
        exit;
    }

    // 3. Tentukan tanggal dipinjam (hari ini) & batas pengembalian (7 hari ke depan)
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali_rencana = date('Y-m-d', strtotime('+7 days'));

    // 4. Masukkan ke tabel peminjaman
    $query_pinjam = "INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, tanggal_kembali, status) 
                 VALUES ('$user_id', '$buku_id', '$tgl_pinjam', '$tgl_kembali_rencana', 'dipinjam')";

if (mysqli_query($koneksi, $query_pinjam)) {
    // Kurangi stok buku
    mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id = '$buku_id'");

    header("Location: detail_buku.php?id=$buku_id&msg=sukses");
    exit;
} else {
    header("Location: detail_buku.php?id=$buku_id&msg=gagal");
    exit;
}
}

header("Location: catalog.php");
exit;
?>