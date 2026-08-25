<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $email        = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $cek_email = mysqli_query($koneksi, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        header("Location: register.php?msg=email_kembar");
        exit;
    }

    // Generasi Kode OTP 6 Digit
    $kode_otp = sprintf("%06d", mt_rand(100000, 999999));

    // Simpan data siswa ke database (role = siswa, status = pending)
    $query = "INSERT INTO users (nama_lengkap, email, password, role, status_verifikasi, kode_otp) 
              VALUES ('$nama_lengkap', '$email', '$password', 'siswa', 'pending', '$kode_otp')";

    if (mysqli_query($koneksi, $query)) {
        // Simpan email & OTP ke session untuk pengujian di halaman verifikasi
        $_SESSION['pending_email'] = $email;
        $_SESSION['simulasi_otp']  = $kode_otp;

        header("Location: verifikasi.php");
        exit;
    } else {
        header("Location: register.php?msg=gagal");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>