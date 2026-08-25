<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya admin/petugas yang bisa memproses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: login.php");
    exit;
}

// PROSES VERIFIKASI SISWA MANUAL
if (isset($_GET['action']) && $_GET['action'] === 'verifikasi' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $query = "UPDATE users SET status_verifikasi = 'terverifikasi', kode_otp = NULL WHERE id = $id AND role = 'siswa'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: data_siswa.php?msg=sukses_verifikasi");
        exit;
    } else {
        header("Location: data_siswa.php?msg=gagal");
        exit;
    }
}

// PROSES HAPUS SISWA
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $query = "DELETE FROM users WHERE id = $id AND role = 'siswa'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: data_siswa.php?msg=sukses_hapus");
        exit;
    } else {
        header("Location: data_siswa.php?msg=gagal");
        exit;
    }
}

header("Location: data_siswa.php");
exit;
?>