<?php
// Pengaturan Aplikasi
define('BASE_URL', 'http://localhost/perpusdigital/');
define('NAMA_APLIKASI', 'Perpustakaan Digital');

// Mulai Sesi jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>