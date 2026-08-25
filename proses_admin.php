<?php
session_start();
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}

// ----------------------------------------------------
// 1. PROSES AKSI PADA DATA BARANG
// ----------------------------------------------------

// A. Tambah Barang
if (isset($_POST['tambah_barang'])) {
    $kode_barang = mysqli_real_escape_string($koneksi, trim($_POST['kode_barang']));
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $id_kategori = mysqli_real_escape_string($koneksi, trim($_POST['id_kategori']));
    $id_satuan   = mysqli_real_escape_string($koneksi, trim($_POST['id_satuan']));
    $stok        = (int)$_POST['stok'];
    $harga       = (double)$_POST['harga'];

    $query = "INSERT INTO barang (kode_barang, nama_barang, id_kategori, id_satuan, stok, harga) 
              VALUES ('$kode_barang', '$nama_barang', '$id_kategori', '$id_satuan', '$stok', '$harga')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: barang.php?sukses=" . urlencode("Data barang berhasil ditambahkan!"));
    } else {
        header("Location: barang.php?error=" . urlencode("Gagal menambah barang: " . mysqli_error($koneksi)));
    }
    exit;
}

// B. Edit Barang
if (isset($_POST['edit_barang'])) {
    $id_barang   = mysqli_real_escape_string($koneksi, $_POST['id_barang']);
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $id_kategori = mysqli_real_escape_string($koneksi, trim($_POST['id_kategori']));
    $id_satuan   = mysqli_real_escape_string($koneksi, trim($_POST['id_satuan']));
    $stok        = (int)$_POST['stok'];
    $harga       = (double)$_POST['harga'];

    $query = "UPDATE barang SET 
                nama_barang = '$nama_barang', 
                id_kategori = '$id_kategori', 
                id_satuan   = '$id_satuan', 
                stok        = '$stok', 
                harga       = '$harga' 
              WHERE id_barang = '$id_barang'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: barang.php?sukses=" . urlencode("Data barang berhasil diperbarui!"));
    } else {
        header("Location: barang.php?error=" . urlencode("Gagal memperbarui barang: " . mysqli_error($koneksi)));
    }
    exit;
}

// C. Hapus Barang
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_barang') {
    $id_barang = mysqli_real_escape_string($koneksi, $_GET['id']);

    $query = "DELETE FROM barang WHERE id_barang = '$id_barang'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: barang.php?sukses=" . urlencode("Data barang berhasil dihapus!"));
    } else {
        header("Location: barang.php?error=" . urlencode("Gagal menghapus barang: " . mysqli_error($koneksi)));
    }
    exit;
}

// ----------------------------------------------------
// 2. PROSES AKSI PADA DATA KARYAWAN
// ----------------------------------------------------

// A. Hapus Karyawan
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_karyawan') {
    $id_karyawan = mysqli_real_escape_string($koneksi, $_GET['id']);

    $query = "DELETE FROM karyawan WHERE id_karyawan = '$id_karyawan'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: karyawan.php?sukses=" . urlencode("Data karyawan berhasil dihapus!"));
    } else {
        header("Location: karyawan.php?error=" . urlencode("Gagal menghapus karyawan: " . mysqli_error($koneksi)));
    }
    exit;
}

// Jika diakses tanpa parameter yang sesuai, kembalikan ke Dashboard Admin
header("Location: dashboard_admin.php");
exit;
?>