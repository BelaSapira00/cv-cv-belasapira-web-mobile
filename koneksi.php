<?php
require_once 'config.php';

$host = 'localhost';
$user = 'root';
$pass = ''; // Sesuaikan dengan password MySQL kamu
$db   = 'perpusdigital';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>