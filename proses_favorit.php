<?php
require_once 'koneksi.php';
require_once 'auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$aksi = $_GET['aksi'] ?? '';
$buku_id = (int) ($_GET['buku_id'] ?? 0);

if ($buku_id <= 0) {
    header("Location: dashboard_siswa.php");
    exit;
}

if ($aksi === 'tambah') {
    // Cek apakah sudah ada di favorit
    $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM favorit WHERE user_id = ? AND buku_id = ?");
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $buku_id);
    mysqli_stmt_execute($stmt_check);
    $res = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($res) === 0) {
        $stmt_add = mysqli_prepare($koneksi, "INSERT INTO favorit (user_id, buku_id, created_at) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($stmt_add, "ii", $user_id, $buku_id);
        mysqli_stmt_execute($stmt_add);
        header("Location: favorit.php?msg=sukses_tambah");
    } else {
        header("Location: favorit.php?msg=ada");
    }
    exit;
} elseif ($aksi === 'hapus') {
    $stmt_del = mysqli_prepare($koneksi, "DELETE FROM favorit WHERE user_id = ? AND buku_id = ?");
    mysqli_stmt_bind_param($stmt_del, "ii", $user_id, $buku_id);
    mysqli_stmt_execute($stmt_del);
    header("Location: favorit.php?msg=sukses_hapus");
    exit;
}

header("Location: dashboard_siswa.php");
exit;