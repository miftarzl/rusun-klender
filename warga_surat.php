<?php 
session_start(); 
include 'koneksi.php'; 

// Proteksi Keamanan Warga
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Warga') {
    header("Location: index.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];
$pesan = "";

$query_marquee = "SELECT konten FROM informasi WHERE judul = 'Pengumuman' LIMIT 1";
$result_marquee = $conn->query($query_marquee); 
$data_marquee = $result_marquee ? $result_marquee->fetch_assoc() : null;
$teks_pengumuman = !empty($data_marquee['konten']) ? $data_marquee['konten'] : "Selamat datang di Portal Layanan Digital Rusun Klender RT 008.";

// Proses Pengajuan Surat Baru
if (isset($_POST['ajukan_surat'])) {
    $jenis_surat = $_POST['jenis_surat'];
    $keperluan = $_POST['keperluan'];

    $stmt = $conn->prepare("INSERT INTO surat_pengantar (id_warga, jenis_surat, keperluan, status_pengajuan) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iss", $id_warga, $jenis_surat, $keperluan);
    
    if ($stmt->execute()) {
        $pesan = "<div class='alert sukses'>Surat berhasil diajukan! Menunggu persetujuan RT.</div>";
    } else {
        $pesan = "<div class='alert gagal'>Gagal mengajukan surat. Coba lagi.</div>";
    }
}

// Ambil riwayat pengajuan surat milik warga ini
$query_riwayat = "SELECT * FROM surat_pengantar WHERE id_warga = ? ORDER BY tanggal_ajuan DESC";
$stmt_riwayat = $conn->prepare($query_riwayat);
$stmt_riwayat->bind_param("i", $id_warga);
$stmt_riwayat->execute();
$result_riwayat = $stmt_riwayat->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Pengajuan Dokumen</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
    body { 
        background-color: #0d0d0f !important; 
        color: #e4e4e7 !important; 
        font-family: 'Inter', sans-serif;
    }

    /* Perbaikan jarak atas container supaya tidak kepotong navbar */
    .container {
        margin-top: 100px !important; /* Dinaikkan agar aman dari navbar */
        padding-top: 20px !important;
        max-width: 1240px;
        margin-left: auto !important;
        margin-right: auto !important;
        text-align: left;
    }

    .navbar .brand {
        color: #0284c7 !important;
    }

    .navbar .menu a {
        transition: color 0.2s ease !important;
    }
    .navbar .menu a:hover {
        color: #0284c7 !important; 
    }
    .navbar .menu a.active {
        color: #0284c7 !important;
        font-weight: 600;
    }

    /* Styling Tombol Logout SaaS Clean (Blue Theme) */
    .btn-logout {
        background: linear-gradient(135deg, #0284c7, #0369a1) !important;
        color: white !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        text-decoration: none !important;
        box-shadow: 0 4px 15px rgba(2, 132, 199, 0.35) !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        margin-left: 15px !important;
        border: none !important;
    }

    .btn-logout:hover {
        background: linear-gradient(135deg, #0369a1, #075985) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(2, 132, 199, 0.55) !important;
        color: white !important;
    }

    .btn-logout:active {
        transform: translateY(0) !important;
    }
    
    .layout-surat { display: grid; grid-template-columns: 1fr 2fr; gap: 25px; margin-top: 25px; }
    .box-form, .box-tabel { background: #141416; padding: 25px; border-radius: 10px; border: 1px solid #232326; }
    
    .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
    .form-group label { margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #fff; }
    .input-custom, .select-custom, .textarea-custom { background: #1a1a1e; color: #fff; padding: 10px 12px; border-radius: 8px; border: 1px solid #2e2e35; font-family: inherit; transition: border-color 0.2s; }
    .select-custom:focus, .textarea-custom:focus { outline: none; border-color: #0284c7; box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15); }
    .textarea-custom { resize: vertical; height: 100px; }
    
    .btn-submit { 
        background: linear-gradient(135deg, #0284c7, #0369a1); 
        color: white; 
        border: none; 
        padding: 12px; 
        border-radius: 8px; 
        font-weight: 600; 
        cursor: pointer; 
        transition: all 0.2s; 
        width: 100%; 
        box-shadow: 0 4px 15px rgba(2, 132, 199, 0.35);
    }
    .btn-submit:hover { 
        background: linear-gradient(135deg, #0369a1, #075985); 
        transform: translateY(-1px);
    }
    
    table { width: 100%; border-collapse: collapse; color: #e4e4e7; }
    table, th, td { border: 1px solid #232326; }
    th, td { padding: 12px; text-align: left; }
    th { background-color: #1a1a1e; color: #fff; }
    tr:hover { background-color: #1c1c1f; }
    
    .badge { padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px; display: inline-block; }
    .pending { background: #453008; color: #ffb84d; }
    .disetujui { background: #0f3d23; color: #52d689; }
    .ditolak { background: #4c1d23; color: #ff7686; }
    
    .btn-cetak { background: #0284c7; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: background 0.2s; display: inline-block; }
    .btn-cetak:hover { background: #0369a1; }
    
    .alert { padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; font-weight: 600; }
    .alert.sukses { background: #0f3d23; color: #52d689; border: 1px solid #14532d; }
    .alert.gagal { background: #4c1d23; color: #ff7686; border: 1px solid #7f1d1d; }

    /* Styling Badge Pill Tag */
    .badge-pill {
        display: inline-block;
        padding: 6px 14px;
        border: 1px solid #10b981; /* Warna hijau neon ala screenshot */
        color: #10b981;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        background: rgba(16, 185, 129, 0.05);
    }
    
    @media (max-width: 992px) { 
        .layout-surat { grid-template-columns: 1fr; } 
        .container { margin-left: 20px !important; margin-right: 20px !important; margin-top: 110px !important; }
    }
</style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row">
        <div class="brand">Rusun-KL</div>
        <div class="menu">
            <a href="index_warga.php">Home</a>
            <a href="warga_surat.php" class="active">Layanan Dokumen</a>
            <a href="warga_iuran.php">Informasi Iuran</a>
            <a href="about.php">About Us</a>
            <a href="contact_us.php">Contact Us</a>
            <!-- Teks Halo Warga kembali warna hijau (#10b981) -->
            <span style="color: #10b981; font-weight: 600; margin-left: 15px;">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?> (Warga)</span>
            
            <!-- Tombol Logout Blue Clean -->
            <a href="logout.php" class="btn-logout">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </a>
        </div>
    </div>
    
    <div class="running-text-container">
        <div class="running-text-label" style="background: #0284c7;">Info RT 008</div>
        <marquee class="running-text-content" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();">
            <span>📢 <?php echo htmlspecialchars($teks_pengumuman); ?></span>
        </marquee>
    </div>
</div>

<div class="container">
    <div class="main-content" style="margin: 20px auto 0 auto;">
        <div class="badge-pill">Layanan Surat</div>
        <h1 style="color: #fff; margin: 0;">Pusat Layanan Dokumen Mandiri</h1>
        <p style="color: #a1a1aa; margin-top: 5px;">Ajukan surat keterangan atau laporkan status kependudukan Anda secara online ke Pengurus RT.</p>
        
        <?php echo $pesan; ?>

        <div class="layout-surat">
            <!-- KIRI: Form Pengajuan -->
            <div class="box-form">
                <h3 style="margin-top:0; color:#fff;">Formulir Pengajuan</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Jenis Dokumen / Surat</label>
                        <select name="jenis_surat" class="select-custom" required>
                            <option value="Surat Pengantar">Surat Pengantar</option>
                            <option value="Domisili">Domisili</option>
                            <option value="Surat Kematian">Surat Kematian</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keperluan / Keterangan Alasan</label>
                        <textarea name="keperluan" class="textarea-custom" placeholder="Contoh: Mengurus pencatatan BPJS, klaim asuransi, atau pindah domisili..." required></textarea>
                    </div>
                    <button type="submit" name="ajukan_surat" class="btn-submit">Kirim Dokumen</button>
                </form>
            </div>

            <!-- KANAN: Tabel Riwayat -->
            <div class="box-tabel">
                <h3 style="margin-top:0; color:#fff;">Riwayat Permohonan Anda</h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Ajuan</th>
                                <th>Jenis Surat</th>
                                <th>Keperluan</th>
                                <th>Status RT</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_riwayat->num_rows == 0): ?>
                                <tr><td colspan="5" style="text-align:center; color:#8a8a93;">Anda belum pernah mengajukan dokumen apapun.</td></tr>
                            <?php else: ?>
                                <?php while($row = $result_riwayat->fetch_assoc()): ?>
                                    <tr>
                                        <td style="color:#8a8a93; font-size:13px;"><?php echo date('d-m-Y H:i', strtotime($row['tanggal_ajuan'])); ?></td>
                                        <td style="color:#fff; font-weight:600;"><?php echo htmlspecialchars($row['jenis_surat']); ?></td>
                                        <td><?php echo htmlspecialchars($row['keperluan']); ?></td>
                                        <td>
                                            <span class="badge <?php echo ($row['status_pengajuan'] == 'Pending') ? 'pending' : (($row['status_pengajuan'] == 'Disetujui') ? 'disetujui' : 'ditolak'); ?>">
                                                <?php echo $row['status_pengajuan']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row['status_pengajuan'] == 'Disetujui'): ?>
                                                <a href="cetak_surat.php?id=<?php echo $row['id_surat']; ?>" target="_blank" class="btn-cetak">Cetak</a>
                                            <?php else: ?>
                                                <span style="color:#555;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>