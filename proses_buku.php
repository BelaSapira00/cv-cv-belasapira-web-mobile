<?php
require_once 'koneksi.php';
require_once 'auth.php';

// Pastikan hanya admin atau petugas yang memiliki akses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {
    header("Location: data_buku.php?msg=akses_ditolak");
    exit;
}

$action = $_GET['action'] ?? '';

// ==========================================
// 1. PROSES TAMBAH BUKU
// ==========================================
if ($action === 'tambah') {
    $judul        = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis      = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $penerbit     = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun_terbit = (int)$_POST['tahun_terbit'];
    $kategori_id  = (int)$_POST['kategori_id'];
    $stok         = (int)$_POST['stok'];
    $deskripsi    = mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?? '');

    $nama_cover = 'default_cover.jpg';
    $nama_pdf   = "NULL";

    $dir_cover = "uploads/cover/";
    $dir_pdf   = "uploads/pdf/";

    if (!file_exists($dir_cover)) mkdir($dir_cover, 0777, true);
    if (!file_exists($dir_pdf)) mkdir($dir_pdf, 0777, true);

    // Upload Cover Gambar
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $ext_cover  = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $file_cover = time() . '_' . preg_replace("/[^a-zA-Z0-9]/", "_", $judul) . '.' . strtolower($ext_cover);
        
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $dir_cover . $file_cover)) {
            $nama_cover = $file_cover;
        }
    }

    // Upload File PDF E-Book
    if (isset($_FILES['file_ebook']) && $_FILES['file_ebook']['error'] === UPLOAD_ERR_OK) {
        $ext_pdf  = pathinfo($_FILES['file_ebook']['name'], PATHINFO_EXTENSION);
        $file_pdf = time() . '_' . preg_replace("/[^a-zA-Z0-9]/", "_", $judul) . '.' . strtolower($ext_pdf);
        
        if (move_uploaded_file($_FILES['file_ebook']['tmp_name'], $dir_pdf . $file_pdf)) {
            $nama_pdf = "'" . $file_pdf . "'";
        }
    }

    $query = "INSERT INTO buku (kategori_id, judul, penulis, penerbit, tahun_terbit, deskripsi, stok, cover, file_ebook) 
              VALUES ($kategori_id, '$judul', '$penulis', '$penerbit', $tahun_terbit, '$deskripsi', $stok, '$nama_cover', $nama_pdf)";

    if (mysqli_query($koneksi, $query)) {
        header("Location: data_buku.php?msg=sukses_tambah");
        exit;
    } else {
        die("Gagal menyimpan data ke database: " . mysqli_error($koneksi));
    }
}

// ==========================================
// 2. PROSES HAPUS BUKU
// ==========================================
if ($action === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);

    if ($id > 0) {
        // Hapus relasi data di tabel terikat agar tidak kena error FK constraint
        mysqli_query($koneksi, "DELETE FROM favorit WHERE buku_id = $id");
        mysqli_query($koneksi, "DELETE FROM peminjaman WHERE buku_id = $id");

        // Ambil info nama file gambar & PDF untuk dihapus dari folder penyimpanan
        $res = mysqli_query($koneksi, "SELECT cover, file_ebook FROM buku WHERE id = $id");
        if ($data = mysqli_fetch_assoc($res)) {
            if ($data['cover'] && $data['cover'] !== 'default_cover.jpg' && file_exists("uploads/cover/" . $data['cover'])) {
                @unlink("uploads/cover/" . $data['cover']);
            }
            if ($data['file_ebook'] && file_exists("uploads/pdf/" . $data['file_ebook'])) {
                @unlink("uploads/pdf/" . $data['file_ebook']);
            }
        }

        // Hapus data utama dari tabel buku
        $query_hapus = mysqli_query($koneksi, "DELETE FROM buku WHERE id = $id");

        if ($query_hapus) {
            header("Location: data_buku.php?msg=sukses_hapus");
            exit;
        } else {
            die("Gagal menghapus buku: " . mysqli_error($koneksi));
        }
    }
}

header("Location: data_buku.php");
exit;