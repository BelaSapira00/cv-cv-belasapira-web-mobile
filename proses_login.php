<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $query  = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {

            // Cek jika siswa belum verifikasi OTP
            if ($user['role'] === 'siswa' && $user['status_verifikasi'] === 'pending') {
                $_SESSION['pending_email'] = $user['email'];
                header("Location: verifikasi.php?msg=butuh_otp");
                exit;
            }

            // Set Session Login
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email']        = $user['email'];
            $_SESSION['role']         = $user['role'];

            // Redirect otomatis sesuai Role
            if ($user['role'] === 'admin') {
                header("Location: dashboard_admin.php");
            } elseif ($user['role'] === 'petugas') {
                header("Location: dashboard_petugas.php");
            } else {
                header("Location: dashboard_siswa.php");
            }
            exit;
        }
    }

    // Jika gagal
    header("Location: login.php?msg=gagal");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>