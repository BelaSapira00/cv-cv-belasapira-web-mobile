<?php
session_start();
require_once 'koneksi.php';

// Jika sudah login, redirect langsung ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$pesan_error = $_GET['error'] ?? '';
$pesan_sukses = $_GET['sukses'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SISTEM INFORMASI PERSEDIAAN BARANG</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-reset {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-reset:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            text-decoration: none;
            color: #007bff;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>RESET PASSWORD</h2>
        <p style="font-size: 13px; color: #666; margin-bottom: 20px;">
            Masukkan email terdaftar dan password baru Anda.
        </p>

        <?php if (!empty($pesan_error)): ?>
            <div class="alert-error"><?= htmlspecialchars($pesan_error); ?></div>
        <?php endif; ?>

        <?php if (!empty($pesan_sukses)): ?>
            <div class="alert-success"><?= htmlspecialchars($pesan_sukses); ?></div>
        <?php endif; ?>

        <form action="proses_lupa_password.php" method="POST">
            <div class="form-group">
                <label for="email">Alamat Email Terdaftar</label>
                <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
            </div>

            <div class="form-group">
                <label for="password_baru">Password Baru</label>
                <input type="password" id="password_baru" name="password_baru" placeholder="Masukkan password baru" required minlength="5">
            </div>

            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password Baru</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password baru" required>
            </div>

            <button type="submit" name="submit" class="btn-reset">SIMPAN PASSWORD BARU</button>
        </form>

        <a href="Login.php" class="back-link">← Kembali ke Halaman Login</a>
    </div>
</body>
</html>