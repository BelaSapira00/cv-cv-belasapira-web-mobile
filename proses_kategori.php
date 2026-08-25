<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya admin yang dapat memproses
cek_akses('admin');

// PROSES TAMBAH KATEGORI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama_kategori = mysqli_real_escape_string($koneksi, trim($_POST['nama_kategori']));

    if (!empty($nama_kategori)) {
        $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
        if (mysqli_query($koneksi, $query)) {
            header("Location: kategori.php?msg=sukses_tambah");
            exit;
        }
    }
    header("Location: kategori.php?msg=gagal");
    exit;
}

// PROSES HAPUS KATEGORI
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $query = "DELETE FROM kategori WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        header("Location: kategori.php?msg=sukses_hapus");
        exit;
    } else {
        header("Location: kategori.php?msg=gagal");
        exit;
    }
}

header("Location: kategori.php");
exit;
?>