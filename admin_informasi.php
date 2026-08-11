<?php
session_start();
include 'koneksi.php'; 

// Proteksi Keamanan: Jika bukan admin, blokir akses demi keamanan database
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

// =========================================================================
// PROSES SIMPAN DATA (PENGGANTI PROSES_PENGUMUMAN.PHP)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $isi_informasi = isset($_POST['isi_informasi']) ? trim($_POST['isi_informasi']) : '';

    // Menggunakan Prepared Statement untuk keamanan database
    $stmt = $conn->prepare("UPDATE informasi SET konten = ? WHERE judul = 'Pengumuman'");
    $stmt->bind_param("s", $isi_informasi);
    
    if ($stmt->execute()) {
        // PENGUBAHAN DI SINI: Langsung dialihkan ke halaman home milik admin
        header("Location: index_admin.php");
        exit;
    } else {
        $error_msg = "Gagal memperbarui pengumuman: " . $conn->error;
    }
    $stmt->close();
}

// =========================================================================
// MENGAMBIL DATA LAMA UNTUK DITAMPILKAN DI TEXTAREA
// =========================================================================
$query = "SELECT konten FROM informasi WHERE judul = 'Pengumuman' LIMIT 1";
$result = $conn->query($query);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin | Kelola Teks Berjalan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0c0f17; color: #ffffff; padding: 20px; }
        .admin-box { max-width: 600px; margin: 50px auto; background: #11141d; padding: 30px; border-radius: 8px; border: 1px solid #222736; }
        h2 { color: #e50914; margin-bottom: 20px; font-size: 22px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: #a3a3a3; }
        textarea { width: 100%; height: 120px; background: #1c2030; border: 1px solid #32394e; border-radius: 6px; color: #fff; padding: 12px; font-size: 14px; resize: none; outline: none; }
        textarea:focus { border-color: #e50914; }
        .btn-save { background: #e50914; color: white; border: none; padding: 12px 20px; font-weight: 700; border-radius: 6px; cursor: pointer; transition: 0.2s; }
        .btn-save:hover { background: #b80710; }
        .alert { padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 13px; }
        .alert-danger { background: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c; }
        .btn-batal { background: #222736; color: #a3a3a3; border: 1px solid #32394e; padding: 12px 20px; font-weight: 600; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px; margin-left: 10px; transition: 0.2s; }
        .btn-batal:hover { background: #1c2030; color: #fff; }
    </style>
</head>
<body>

<div class="admin-box">
    <h2>Kelola Pemberitahuan Berjalan</h2>

    <!-- Notifikasi Gagal (Jika ada error sistem) -->
    <?php if(isset($error_msg)): ?>
        <div class="alert alert-danger">❌ <?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Action dikosongkan agar memproses di file yang sama -->
    <form action="" method="POST">
        <div class="form-group">
            <label for="isi_informasi">Teks Pengumuman Aktif:</label>
            <textarea name="isi_informasi" id="isi_informasi" required placeholder="Tulis pengumuman baru di sini..."><?php echo isset($data['konten']) ? htmlspecialchars($data['konten']) : ''; ?></textarea>
        </div>
        <button type="submit" name="submit" class="btn-save">Simpan Perubahan</button>
        <a href="index_admin.php" class="btn-batal">Batal</a>
    </form>
</div>

</body>
</html>