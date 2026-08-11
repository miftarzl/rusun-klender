<?php 
session_start(); 
include 'koneksi.php'; 

// Proteksi Keamanan: Jika belum login atau bukan Warga
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Warga') {
    header("Location: index.php");
    exit;
}

$id_warga = $_SESSION['id_warga'];

$query_marquee = "SELECT konten FROM informasi WHERE judul = 'Pengumuman' LIMIT 1";
$result_marquee = $conn->query($query_marquee); 
$data_marquee = $result_marquee ? $result_marquee->fetch_assoc() : null;
$teks_pengumuman = !empty($data_marquee['konten']) ? $data_marquee['konten'] : "Selamat datang di Portal Layanan Digital Rusun Klender RT 008.";

// Ambil riwayat pembayaran iuran warga ini dari database
$query_iuran = "SELECT * FROM iuran WHERE id_warga = ? ORDER BY bulan_tahun DESC";
$stmt_iuran = $conn->prepare($query_iuran);
$stmt_iuran->bind_param("i", $id_warga);
$stmt_iuran->execute();
$result_iuran = $stmt_iuran->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Informasi Tagihan & Iuran</title>
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

    .table-container { 
        background: #141416; 
        padding: 25px; 
        border-radius: 10px; 
        border: 1px solid #232326; 
        margin-top: 25px; 
    }
    
    table { width: 100%; border-collapse: collapse; color: #e4e4e7; }
    table, th, td { border: 1px solid #232326; }
    th, td { padding: 14px; text-align: left; }
    th { background-color: #1a1a1e; color: #fff; }
    tr:hover { background-color: #1c1c1f; }
    
    .lunas { color: #10b981; font-weight: bold; }
    .belum { color: #ef4444; font-weight: bold; }
    .info-box { 
        background: #1a1a1e; 
        padding: 15px; 
        border-left: 4px solid #0284c7; 
        border-radius: 4px; 
        margin-bottom: 20px; 
        font-size: 14px; 
        line-height: 1.5; 
    }

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

    @media(max-width: 768px) {
        .container { margin-left: 20px !important; margin-right: 20px !important; margin-top: 110px !important; }
    }
</style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row">
        <div class="brand">Rusun-AI</div>
        <div class="menu">
            <a href="index_warga.php">Home</a>
            <a href="warga_surat.php">Layanan Dokumen</a>
            <a href="warga_iuran.php" class="active">Informasi Iuran</a>
            <a href="about.php">About Us</a>
            <a href="contact_us.php">Contact Us</a>
            
            <!-- Teks Halo Warga warna hijau (#10b981) -->
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
    <div class="main-content" style="margin-top: 25px;">
        <div class="badge-pill">Keuangan Warga</div>
        <h1 style="color: #fff; margin: 0;">Buku Rekap Iuran Perbulan</h1>
        <p style="color: #a1a1aa; margin-top: 5px;">Pantau riwayat pembayaran iuran perbulan warga Rusun RT 008.</p>

        <div class="table-container">
            <div class="info-box">
                <strong>Catatan Konfirmasi:</strong> Pembayaran dilakukan langsung secara tunai melalui Bendahara RT 008 (Rp 10.000 / bulan). Status halaman ini akan otomatis berubah menjadi <span class="lunas">Lunas</span> begitu Admin melakukan validasi pembukuan berkas setoran Anda.
            </div>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Periode Bulan</th>
                            <th>Kategori Alokasi</th>
                            <th>Nominal Tagihan</th>
                            <th>Status Verifikasi</th>
                            <th>Tanggal Penyetoran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_iuran->num_rows == 0): ?>
                            <tr><td colspan="5" style="text-align:center; color: #8a8a93;">Belum ada riwayat transaksi pembayaran iuran yang tercatat oleh admin.</td></tr>
                        <?php else: ?>
                            <?php while($row = $result_iuran->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo date('F Y', strtotime($row['bulan_tahun'] . '-01')); ?></strong></td>
                                    <td style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($row['jenis_iuran']); ?></td>
                                    <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="<?php echo ($row['status_bayar'] == 'Lunas') ? 'lunas' : 'belum'; ?>">
                                            <?php echo $row['status_bayar']; ?>
                                        </span>
                                    </td>
                                    <td style="color: #8a8a93;">
                                        <?php echo $row['tanggal_bayar'] ? date('d-m-Y H:i', strtotime($row['tanggal_bayar'])) . ' WIB' : '-'; ?>
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

</body>
</html>