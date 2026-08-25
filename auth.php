<?php
require_once 'config.php';

function cek_akses($role_diizinkan) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?msg=harus_login");
        exit;
    }

    if ($_SESSION['role'] !== $role_diizinkan) {
        // Jika bukan role yang sesuai, balikkan ke dashboard masing-masing
        if ($_SESSION['role'] === 'admin') header("Location: dashboard_admin.php");
        elseif ($_SESSION['role'] === 'petugas') header("Location: dashboard_petugas.php");
        else header("Location: dashboard_siswa.php");
        exit;
    }
}
?>