<?php
require_once 'koneksi.php';
require_once 'auth.php';

cek_akses('siswa');
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = '$user_id'";
$user = mysqli_fetch_assoc(mysqli_query($koneksi, $query));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - <?= NAMA_APLIKASI; ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #F8FAFC; padding: 40px 20px; color: #1E293B; }
        .card { max-width: 450px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-kembali { display: inline-block; margin-bottom: 20px; color: #1E3A8A; text-decoration: none; font-weight: 600; }
        h2 { color: #0A192F; margin-bottom: 20px; text-align: center; }
        .info-group { margin-bottom: 15px; }
        .info-group label { display: block; font-size: 0.85rem; color: #64748B; font-weight: 600; }
        .info-group p { font-size: 1rem; color: #1E293B; font-weight: 500; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <a href="dashboard_siswa.php" class="btn-kembali">← Kembali ke Dashboard</a>
        <h2>👤 Profil Saya</h2>
        <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #E2E8F0;">
        <div class="info-group">
            <label>Nama Lengkap</label>
            <p><?= htmlspecialchars($user['nama_lengkap'] ?? $_SESSION['nama_lengkap']); ?></p>
        </div>
        <div class="info-group">
            <label>Username</label>
            <p><?= htmlspecialchars($user['username'] ?? '-'); ?></p>
        </div>
        <div class="info-group">
            <label>Role</label>
            <p style="text-transform: capitalize;"><?= htmlspecialchars($user['role'] ?? 'Siswa'); ?></p>
        </div>
    </div>
</body>
</html>