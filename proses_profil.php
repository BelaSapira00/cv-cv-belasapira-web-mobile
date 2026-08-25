<?php
session_start();
require_once 'koneksi.php';

if (isset($_POST['simpan_profil'])) {
    $username      = $_SESSION['username'];
    $nama_karyawan = mysqli_real_escape_string($koneksi, trim($_POST['nama_karyawan']));
    $email         = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $telp          = mysqli_real_escape_string($koneksi, trim($_POST['telp']));
    $jk            = mysqli_real_escape_string($koneksi, trim($_POST['jk']));
    $alamat        = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));

    // Ambil data foto lama
    $query_lama = mysqli_query($koneksi, "SELECT foto FROM karyawan WHERE username = '$username'");
    $data_lama = mysqli_fetch_assoc($query_lama);
    $nama_foto = $data_lama['foto'] ?? '';

    // Proses upload foto jika ada file yang diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $ekstensi_diperbolehkan)) {
            // Nama file baru agar tidak bentrok
            $nama_foto = 'foto_' . time() . '.' . $ext;
            $target_dir = 'uploads/' . $nama_foto;

            // Buat folder uploads jika belum ada
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            move_uploaded_file($file_tmp, $target_dir);
        } else {
            header("Location: profil.php?error=" . urlencode("Format foto harus JPG, JPEG, atau PNG!"));
            exit;
        }
    }

    // Update database
    $query_update = "UPDATE karyawan SET 
                        nama_karyawan = '$nama_karyawan', 
                        email = '$email', 
                        telp = '$telp', 
                        jk = '$jk', 
                        alamat = '$alamat',
                        foto = '$nama_foto'
                     WHERE username = '$username'";

    if (mysqli_query($koneksi, $query_update)) {
        header("Location: profil.php?sukses=" . urlencode("Data profil berhasil diperbarui!"));
        exit;
    } else {
        header("Location: profil.php?error=" . urlencode("Gagal memperbarui profil: " . mysqli_error($koneksi)));
        exit;
    }
} else {
    header("Location: profil.php");
    exit;
}
?>