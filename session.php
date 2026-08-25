<?php
// Pastikan session dimulai hanya jika belum ada session yang aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fungsi untuk mengecek apakah user sudah login.
 * Jika belum, alihkan secara otomatis ke halaman Login.php
 */
function cek_login() {
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        header("Location: Login.php?error=" . urlencode("Silakan login terlebih dahulu untuk mengakses halaman ini."));
        exit;
    }
}

/**
 * Fungsi opsional untuk membatasi akses berdasarkan level / status (misal: Admin vs Karyawan)
 * 
 * @param array $level_diizinkan Contoh penggunaan: cek_akses(['admin', 'pimpinan']);
 */
function cek_akses($level_diizinkan = []) {
    // Jalankan pengecekan login dasar terlebih dahulu
    cek_login();

    $user_status = $_SESSION['status'] ?? '';

    if (!in_array(strtolower($user_status), array_map('strtolower', $level_diizinkan))) {
        header("Location: dashboard_admin.php?error=" . urlencode("Anda tidak memiliki hak akses ke halaman tersebut."));
        exit;
    }
}

// Jalankan pemeriksaan login default secara otomatis saat file ini di-include
cek_login();
?>