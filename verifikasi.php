<?php
require_once 'koneksi.php';

// Pastikan ada email yang sedang diproses verifikasinya
if (!isset($_SESSION['pending_email'])) {
    header("Location: login.php");
    exit;
}

$email_siswa  = $_SESSION['pending_email'];
$simulasi_otp = $_SESSION['simulasi_otp'] ?? '';

$pesan = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'otp_salah') $pesan = 'Kode verifikasi salah! Silakan periksa kembali.';
    elseif ($_GET['msg'] === 'butuh_otp') $pesan = 'Akun kamu belum diverifikasi. Masukkan kode OTP!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - <?= NAMA_APLIKASI; ?></title>
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

        .otp-card {
            background: var(--gray-card);
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(10, 25, 47, 0.1);
            border: 1px solid var(--gray-border);
            padding: 35px 30px;
            text-align: center;
        }

        .otp-header img {
            height: 55px;
            width: 55px;
            margin-bottom: 10px;
        }

        .otp-header h2 {
            color: var(--navy-dark);
            font-size: 1.4rem;
            margin-bottom: 6px;
        }

        .otp-header p {
            color: var(--text-muted);
            font-size: 0.88rem;
            margin-bottom: 20px;
        }

        .otp-header strong {
            color: var(--navy-primary);
        }

        .alert {
            background-color: #FEF2F2;
            color: #DC2626;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            border: 1px solid #FECACA;
        }

        .simulasi-box {
            background-color: #E0F2FE;
            color: var(--navy-primary);
            border: 1px dashed var(--blue-light);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .otp-input {
            width: 100%;
            padding: 14px;
            font-size: 1.5rem;
            letter-spacing: 12px;
            text-align: center;
            border: 2px solid var(--gray-border);
            border-radius: 10px;
            outline: none;
            transition: border-color 0.3s;
            font-weight: 700;
            color: var(--navy-dark);
        }

        .otp-input:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.2);
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

        .otp-footer {
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .otp-footer a {
            color: var(--navy-primary);
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="otp-card">
        <div class="otp-header">
            <img src="logo.png" alt="Logo">
            <h2>Verifikasi Kode OTP</h2>
            <p>Kode telah dikirim ke email:<br><strong><?= htmlspecialchars($email_siswa); ?></strong></p>
        </div>

        <?php if ($pesan): ?>
            <div class="alert"><?= htmlspecialchars($pesan); ?></div>
        <?php endif; ?>

        <?php if ($simulasi_otp): ?>
            <div class="simulasi-box">
                📩 <strong>[Simulasi Kode OTP]:</strong> <span style="font-size: 1.1rem; font-weight:700;"><?= htmlspecialchars($simulasi_otp); ?></span>
            </div>
        <?php endif; ?>

        <form action="proses_verifikasi.php" method="POST">
            <div class="form-group">
                <input type="text" name="kode_otp" class="otp-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autofocus autocomplete="off">
            </div>

            <button type="submit" class="btn-submit">Verifikasi Akun</button>
        </form>

        <div class="otp-footer">
            Bukan email kamu? <a href="register.php">Daftar Ulang</a>
        </div>
    </div>

</body>
</html>