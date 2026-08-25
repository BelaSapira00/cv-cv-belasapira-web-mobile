<?php
require_once 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'email_kembar') $pesan = 'Email sudah terdaftar! Gunakan email lain.';
    elseif ($_GET['msg'] === 'gagal') $pesan = 'Terjadi kesalahan, silakan coba lagi.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa - <?= NAMA_APLIKASI; ?></title>
    <style>
        :root {
            --navy-dark: #0A192F;
            --navy-primary: #1E3A8A;
            --blue-light: #38BDF8;
            --gray-bg: #F8FAFC;
            --gray-card: #FFFFFF;
            --gray-border: #E2E8F0;
            --text-dark: #1E293B;
            --text-muted: #64748B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--gray-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: var(--gray-card);
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(10, 25, 47, 0.1);
            border: 1px solid var(--gray-border);
            padding: 35px 30px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .register-header img {
            height: 55px;
            width: 55px;
            margin-bottom: 10px;
        }

        .register-header h2 {
            color: var(--navy-dark);
            font-size: 1.5rem;
        }

        .register-header p {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .alert {
            background-color: #FEF2F2;
            color: #DC2626;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            border: 1px solid #FECACA;
            text-align: center;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--navy-dark);
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--gray-border);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .btn-submit {
            width: 100%;
            background-color: var(--navy-primary);
            color: #FFFFFF;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: var(--navy-dark);
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .register-footer a {
            color: var(--navy-primary);
            font-weight: 600;
            text-decoration: none;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="register-header">
            <img src="logo.png" alt="Logo">
            <h2>Pendaftaran Siswa</h2>
            <p>Buat akun untuk mengakses perpustakaan digital</p>
        </div>

        <?php if ($pesan): ?>
            <div class="alert"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <form action="proses_register.php" method="POST">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label for="email">Alamat Email / Gmail</label>
                <input type="email" id="email" name="email" placeholder="contoh@gmail.com" required>
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" minlength="6" required>
            </div>

            <button type="submit" class="btn-submit">Daftar & Kirim OTP</button>
        </form>

        <div class="register-footer">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
        <a href="index.php" class="back-home">← Kembali ke Utama</a>
    </div>

</body>
</html>