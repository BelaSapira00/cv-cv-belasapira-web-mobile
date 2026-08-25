<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['pending_email'])) {
    $email    = $_SESSION['pending_email'];
    $kode_otp = mysqli_real_escape_string($koneksi, trim($_POST['kode_otp']));

    // Cek kecocokan OTP di database
    $query  = "SELECT id FROM users WHERE email = '$email' AND kode_otp = '$kode_otp' AND status_verifikasi = 'pending'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        // Update status menjadi terverifikasi & bersihkan kode_otp
        $update = "UPDATE users SET status_verifikasi = 'terverifikasi', kode_otp = NULL WHERE email = '$email'";
        mysqli_query($koneksi, $update);

        // Hapus session pending
        unset($_SESSION['pending_email']);
        unset($_SESSION['simulasi_otp']);

        // Redirect ke login dengan pesan sukses
        header("Location: login.php?msg=terverifikasi");
        exit;
    } else {
        header("Location: verifikasi.php?msg=otp_salah");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>