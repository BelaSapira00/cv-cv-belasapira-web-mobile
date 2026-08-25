<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya admin yang dapat memproses
cek_akses('admin');

// PROSES TAMBAH PETUGAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $email        = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $cek_email = mysqli_query($koneksi, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        header("Location: data_petugas.php?msg=email_kembar");
        exit;
    }

    // Petugas yang dibuat langsung berstatus 'terverifikasi' (tanpa perlu OTP)
    $query = "INSERT INTO users (nama_lengkap, email, password, role, status_verifikasi) 
              VALUES ('$nama_lengkap', '$email', '$password', 'petugas', 'terverifikasi')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: data_petugas.php?msg=sukses_tambah");
        exit;
    } else {
        header("Location: data_petugas.php?msg=gagal");
        exit;
    }
}

// PROSES HAPUS PETUGAS
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Cegah hapus jika id yang dikirim adalah milik dirinya sendiri
    if ($id === $_SESSION['user_id']) {
        header("Location: data_petugas.php?msg=gagal");
        exit;
    }

    $query = "DELETE FROM users WHERE id = $id AND role = 'petugas'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: data_petugas.php?msg=sukses_hapus");
        exit;
    } else {
        header("Location: data_petugas.php?msg=gagal");
        exit;
    }
}

header("Location: data_petugas.php");
exit;
?>