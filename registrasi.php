<?php
session_start();
include 'koneksi.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    // 1. Ambil data dari form dan bersihkan input (Anti SQL Injection)
    $nik = mysqli_real_escape_string($conn, trim($_POST['nik']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // 2. Validasi Input Kosong
    if (empty($nik) || empty($username) || empty($password) || empty($konfirmasi_password)) {
        $error = "Semua kolom wajib diisi!";
    } 
    // 3. Validasi Kesamaan Password
    elseif ($password !== $konfirmasi_password) {
        $error = "Konfirmasi password tidak cocok!";
    } 
    else {
        // --- PROSES PERKETAT SISTEM (PRE-VERIFICATION) ---
        
        // Pengecekan A: Apakah NIK ini terdaftar di data master warga Rusun?
        $cek_warga = $conn->query("SELECT * FROM warga WHERE nik = '$nik'");
        
        if ($cek_warga->num_rows === 0) {
            // JIKA ORANG RANDOM ASAL TEBAK NIK, AKAN DI-BLOK DISINI
            $error = "Pendaftaran Gagal! NIK Anda belum terdaftar di data pangkalan Rusun. Silakan hubungi Admin/Pengurus RT.";
        } else {
            // Pengecekan B: Apakah NIK ini sudah pernah bikin akun sebelumnya?
            $cek_akun_nik = $conn->query("SELECT * FROM users WHERE nik = '$nik'");
            
            // Pengecekan C: Apakah Username sudah dipakai orang lain?
            $cek_username = $conn->query("SELECT * FROM users WHERE username = '$username'");

            if ($cek_akun_nik->num_rows > 0) {
                $error = "Gagal! NIK ini sudah memiliki akun login.";
            } elseif ($cek_username->num_rows > 0) {
                $error = "Gagal! Username sudah digunakan, pilih username lain.";
            } else {
                // JIKA SEMUA VALIDASI LOLOS, AMAN!
                // Enkripsi password menggunakan BCRYPT standar industri keamanan
                $password_aman = password_hash($password, PASSWORD_BCRYPT);
                
                $query_insert = "INSERT INTO users (nik, username, password, role) VALUES ('$nik', '$username', '$password_aman', 'Warga')";
                
                if ($conn->query($query_insert)) {
                    $success = "Akun Warga berhasil dibuat! Silakan menuju halaman login.";
                } else {
                    $error = "Terjadi kesalahan sistem, gagal mendaftar.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Registrasi Warga</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #000000; color: #ffffff; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: radial-gradient(circle at 50% 30%, #141b29 0%, #000000 70%); }
        .register-box { background: #111111; border: 1px solid #222222; padding: 40px; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 15px 30px rgba(0,0,0,0.5); }
        .brand { font-weight: 800; font-size: 28px; color: #e50914; text-transform: uppercase; text-align: center; margin-bottom: 5px; }
        .title { text-align: center; font-size: 14px; color: #777777; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #e50914; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 12px; background: #222222; border: 1px solid #333333; border-radius: 6px; color: white; font-size: 14px; transition: border 0.2s; }
        .form-group input:focus { outline: none; border-color: #e50914; background: #2a2a2a; }
        .btn-register { width: 100%; background: #e50914; color: white; border: none; padding: 14px; font-size: 15px; font-weight: 700; border-radius: 6px; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn-register:hover { background: #b80710; }
        .alert { padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 20px; font-weight: 600; line-height: 1.4; }
        .alert-danger { background: rgba(229, 9, 20, 0.1); color: #e50914; border: 1px solid rgba(229, 9, 20, 0.3); }
        .alert-success { background: rgba(46, 204, 113, 0.1); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3); }
        .footer-link { text-align: center; margin-top: 25px; font-size: 13px; color: #aaa; }
        .footer-link a { color: #e50914; text-decoration: none; font-weight: 600; }
        .footer-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="register-box">
    <div class="brand">Rusun-AI</div>
    <div class="title">Registrasi Akun Sistem Kependudukan</div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nomor Induk Kependudukan (NIK)</label>
            <input type="text" name="nik" placeholder="Masukkan 16 digit NIK Anda" maxlength="16" required>
        </div>
        <div class="form-group">
            <label>Username Baru</label>
            <input type="text" name="username" placeholder="Buat username untuk login" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="konfirmasi_password" placeholder="Ulangi password" required>
        </div>
        
        <button type="submit" name="register" class="btn-register">DAFTAR SEKARANG</button>
    </form>

    <div class="footer-link">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>

</body>
</html>