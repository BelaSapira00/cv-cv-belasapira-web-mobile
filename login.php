<?php
require_once 'koneksi.php';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header("Location: dashboard_admin.php");
    elseif ($_SESSION['role'] === 'petugas') header("Location: dashboard_petugas.php");
    else header("Location: dashboard_siswa.php");
    exit;
}

$pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'harus_login') $pesan = 'Silakan login terlebih dahulu untuk membaca buku.';
    elseif ($_GET['msg'] === 'gagal') $pesan = 'Email atau password salah!';
    elseif ($_GET['msg'] === 'logout') $pesan = 'Anda berhasil keluar.';
    elseif ($_GET['msg'] === 'belum_verifikasi') $pesan = 'Akun belum diverifikasi. Silakan cek OTP!';
}

// Menangkap pesan sukses dari lupa password jika ada
$pesan_sukses = $_GET['sukses'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= NAMA_APLIKASI; ?></title>
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

        .login-card {
            background: var(--gray-card);
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(10, 25, 47, 0.1);
            border: 1px solid var(--gray-border);
            padding: 35px 30px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header img {
            height: 60px;
            width: 60px;
            margin-bottom: 10px;
        }

        .login-header h2 {
            color: var(--navy-dark);
            font-size: 1.5rem;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
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

        .alert-success {
            background-color: #F0FDF4;
            color: #166534;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            border: 1px solid #BBF7D0;
            text-align: center;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--navy-dark);
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--gray-border);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        /* Tambahan styling untuk link lupa password */
        .forgot-password {
            display: flex;
            justify-content: flex-end;
            margin-top: 6px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: var(--navy-primary);
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
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
        }

        .btn-submit:hover {
            background-color: var(--navy-dark);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--navy-primary);
            font-weight: 600;
            text-decoration: none;
        }

        .login-footer a:hover {
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

    <div class="login-card">
        <div class="login-header">
            <img src="logo.png" alt="Logo">
            <h2>Masuk Akun</h2>
            <p>Admin, Petugas, dan Siswa</p>
        </div>

        <?php if ($pesan): ?>
            <div class="alert"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <?php if ($pesan_sukses): ?>
            <div class="alert-success"><?= htmlspecialchars($pesan_sukses); ?></div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <div class="form-group">
                <label for="email">Alamat Email / Gmail</label>
                <input type="email" id="email" name="email" placeholder="contoh@gmail.com" required>
            </div>

            <div class="form-group" style="margin-bottom: 5px;">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <!-- Link Lupa Password -->
            <div class="forgot-password">
                <a href="lupa_password.php">Lupa Kata Sandi?</a>
            </div>

            <button type="submit" class="btn-submit">Masuk Sekarang</button>
        </form>

        <div class="login-footer">
            Belum punya akun siswa? <a href="register.php">Daftar Siswa</a>
        </div>
        <a href="index.php" class="back-home">← Kembali ke Utama</a>
    </div>

</body>
</html>