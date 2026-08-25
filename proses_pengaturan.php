<?php
session_start();
require_once 'koneksi.php';

if (isset($_POST['update_profil'])) {
    $username      = $_SESSION['username'];
    $nama          = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email         = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $telp          = mysqli_real_escape_string($koneksi, trim($_POST['telp']));
    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);

    // 1. Update data dasar (Nama, Email, Telp)
    $query_update = "UPDATE karyawan SET 
                        nama_karyawan = '$nama', 
                        email = '$email', 
                        telp = '$telp' 
                     WHERE username = '$username'";

    if (!mysqli_query($koneksi, $query_update)) {
        header("Location: pengaturan.php?error=" . urlencode("Gagal memperbarui profil."));
        exit;
    }

    // 2. Jika user ingin mengganti password
    if (!empty($password_baru)) {
        if (empty($password_lama)) {
            header("Location: pengaturan.php?error=" . urlencode("Masukkan password lama untuk mengonfirmasi pergantian password."));
            exit;
        }

        // Cek password lama di database
        $cek_user = mysqli_query($koneksi, "SELECT password FROM karyawan WHERE username = '$username'");
        $data_user = mysqli_fetch_assoc($cek_user);

        // Verifikasi password lama (Mendukung Password Hash / MD5 / Plaintext)
        $password_valid = false;
        if (password_verify($password_lama, $data_user['password'])) {
            $password_valid = true;
        } elseif (md5($password_lama) === $data_user['password']) {
            $password_valid = true;
        } elseif ($password_lama === $data_user['password']) {
            $password_valid = true;
        }

        if ($password_valid) {
            $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE karyawan SET password = '$password_hashed' WHERE username = '$username'");
        } else {
            header("Location: pengaturan.php?error=" . urlencode("Password lama tidak sesuai!"));
            exit;
        }
    }

    header("Location: pengaturan.php?sukses=" . urlencode("Profil berhasil diperbarui!"));
    exit;
} else {
    header("Location: pengaturan.php");
    exit;
}
?>