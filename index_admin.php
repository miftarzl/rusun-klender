<?php 
session_start(); 
include 'koneksi.php'; 

// Proteksi Keamanan
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

$success_message = "";
$error_message = "";

// Proses Update Teks Berjalan (Pengumuman) dari Modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_informasi') {
    $isi_informasi = isset($_POST['isi_informasi']) ? trim($_POST['isi_informasi']) : '';

    $stmt = $conn->prepare("UPDATE informasi SET konten = ? WHERE judul = 'Pengumuman'");
    $stmt->bind_param("s", $isi_informasi);
    
    if ($stmt->execute()) {
        $success_message = "Teks berjalan berhasil diperbarui!";
    } else {
        $error_message = "Gagal memperbarui pengumuman: " . $conn->error;
    }
    $stmt->close();
}

// 1. Mengambil data teks berjalan (Pengumuman)
$query_marquee = "SELECT konten FROM informasi WHERE judul = 'Pengumuman' LIMIT 1";
$result_marquee = $conn->query($query_marquee); 
$data_marquee = $result_marquee ? $result_marquee->fetch_assoc() : null;
$teks_pengumuman = !empty($data_marquee['konten']) ? $data_marquee['konten'] : "Selamat datang di Portal Layanan Digital Rusun Klender RT 008.";

// 2. Query Data Statistik untuk Dashboard
$total_warga = $conn->query("SELECT COUNT(*) as total FROM warga")->fetch_assoc()['total'] ?? 0;
$warga_tetap = $conn->query("SELECT COUNT(*) as total FROM warga WHERE status_tinggal LIKE '%tetap%'")->fetch_assoc()['total'] ?? 0;
$warga_kontrak = $conn->query("SELECT COUNT(*) as total FROM warga WHERE status_tinggal LIKE '%kontrak%'")->fetch_assoc()['total'] ?? 0;
$surat_pending = $conn->query("SELECT COUNT(*) as total FROM surat_pengantar WHERE status_pengajuan = 'Pending'")->fetch_assoc()['total'] ?? 0;

// 3. Query Mengambil Data Kotak Saran dari Warga
$query_saran = "SELECT * FROM saran ORDER BY tanggal DESC";
$result_saran = $conn->query($query_saran);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Dashboard Admin Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Menyisipkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #0d0d0f !important;
            color: #e4e4e7 !important;
            font-family: 'Inter', sans-serif;
        }

        /* Styling Menu Navbar & Efek Hover Biru */
        .navbar .brand {
            color: #0284c7 !important;
        }

        .menu a {
            color: #e5e5e5;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .menu a:hover {
            color: #0284c7 !important;
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

        .main-content {
            width: 100% !important;
            max-width: 1200px !important;
            display: block !important;
            margin: 0 auto !important;
            padding: 20px !important;
            box-sizing: border-box !important;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px;
            margin-bottom: 15px;
            color: #fff;
        }

        .main-content h1 {
            font-size: 32px !important;
            font-weight: 700 !important;
            color: #fff !important;
            margin: 0 0 10px 0 !important;
            text-align: left !important;
        }

        .description {
            color: #a1a1aa !important;
            font-size: 15px !important;
            line-height: 1.6 !important;
            margin-bottom: 20px !important;
            text-align: left !important;
            max-width: 800px;
        }

        /* QUICK EDIT BANNER (Pengganti Tombol Bawah) */
        .quick-edit-banner {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.1), rgba(20, 20, 22, 0.9));
            border: 1px solid rgba(2, 132, 199, 0.35);
            padding: 14px 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .quick-edit-banner:hover {
            border-color: #0284c7;
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.18), rgba(20, 20, 22, 0.95));
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(2, 132, 199, 0.2);
        }

        .quick-edit-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quick-edit-icon {
            background: rgba(2, 132, 199, 0.2);
            color: #0284c7;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .quick-edit-text h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .quick-edit-text p {
            margin: 3px 0 0 0;
            font-size: 12px;
            color: #a1a1aa;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 550px;
        }

        .quick-edit-action {
            font-size: 13px;
            font-weight: 600;
            color: #0284c7;
            background: transparent;
            border: 1px solid #0284c7;
            padding: 6px 14px;
            border-radius: 6px;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .quick-edit-banner:hover .quick-edit-action {
            background: #0284c7;
            color: #fff;
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 25px;
            margin-top: 25px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-content: start;
        }

        .card-stat {
            background: #141416;
            border: 1px solid #232326;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: transform 0.2s;
        }

        .card-stat:hover {
            transform: translateY(-3px);
            border-color: #3f3f46;
        }

        .card-stat h3 {
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #8a8a93;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-stat p {
            margin: 10px 0 0 0;
            font-size: 32px;
            font-weight: 700;
            color: #fff;
        }

        .card-stat.total { border-left: 4px solid #0284c7; }
        .card-stat.tetap { border-left: 4px solid #10b981; }
        .card-stat.pending { border-left: 4px solid #f59e0b; }

        .chart-wrapper {
            background: #141416;
            border: 1px solid #232326;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .chart-wrapper h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #fff;
            align-self: flex-start;
            font-weight: 600;
        }

        .chart-container {
            position: relative;
            width: 100%;
            max-width: 180px;
        }

        .saran-wrapper {
            margin-top: 30px;
            background: #141416;
            border: 1px solid #232326;
            padding: 25px;
            border-radius: 10px;
        }

        .saran-wrapper h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
            border-bottom: 1px solid #232326;
            padding-bottom: 10px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .admin-table th, .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #232326;
            color: #d4d4d8;
        }

        .admin-table th {
            background: #1a1a1e;
            color: #fff;
            font-weight: 600;
        }

        .admin-table tr:hover {
            background: #18181b;
        }

        .btn-custom {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-secondary-dark {
            background: #222;
            color: #e4e4e7;
            border: 1px solid #333;
        }

        .btn-secondary-dark:hover {
            background: #333;
            color: #fff;
        }

        /* ALERT STYLES */
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; font-weight: 600; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; font-weight: 600; }

        /* MODAL POPUP STYLES */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(4px); overflow-y: auto; padding: 20px; }
        .modal-card { background: #141416; border: 1px solid #0284c7; border-radius: 10px; width: 100%; max-width: 520px; padding: 25px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); position: relative; animation: fadeIn 0.3s ease-in-out; margin: auto; }
        @keyframes fadeIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-header { font-size: 18px; font-weight: 700; color: #ffffff; margin-bottom: 15px; border-bottom: 1px solid #232326; padding-bottom: 10px; text-transform: uppercase; }
        .modal-close-btn { position: absolute; top: 15px; right: 20px; background: transparent; border: none; color: #a1a1aa; font-size: 20px; cursor: pointer; transition: 0.2s; }
        .modal-close-btn:hover { color: #ffffff; }
        .modal-footer { margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: #a1a1aa; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; padding: 10px 14px; background: #1a1a1e; border: 1px solid #232326; border-radius: 6px; color: #fff; font-size: 14px; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: #0284c7; }

        @media (max-width: 992px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row">
        <div class="brand">Rusun-AI [ADMIN]</div>
        <div class="menu">
            <a href="index_admin.php" class="active">Home</a>
            <a href="warga.php">Data Warga</a>
            <a href="admin_surat.php">Urus Dokumen</a>
            <a href="admin_iuran.php">Kelola Iuran</a>
            
            <!-- Teks Nama Admin berwarna hijau (#10b981) -->
            <span style="color: #10b981; font-weight: 600; margin-left: 15px;">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            
            <!-- Tombol Logout -->
            <a href="logout.php" class="btn-logout">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </a>
        </div>
    </div>
    
    <div class="running-text-container">
        <div class="running-text-label" style="background-color: #0284c7;">Panel Kontrol</div>
        <marquee class="running-text-content" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();">
            <span>📢 <?php echo htmlspecialchars($teks_pengumuman); ?></span>
        </marquee>
    </div>
</div>

<div class="container">
    <div class="main-content">
        <div class="badge" style="background-color: #0284c7;">Hak Akses Level: Tertinggi</div>
        <h1>Pusat Kendali Pengurus RT 008</h1>
        <p class="description">
            Selamat datang di Konsol Administrasi Utama. Pantau demografi warga secara mendalam, kelola administrasi surat pengantar secara digital, verifikasi rekap iuran, serta tinjau kotak saran secara real-time.
        </p>

        <!-- QUICK EDIT BANNER (Di bagian atas menggantikan tombol bawah yang kaku) -->
        <div class="quick-edit-banner" onclick="openInformasiModal()">
            <div class="quick-edit-info">
                <div class="quick-edit-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
                <div class="quick-edit-text">
                    <h4>Status Pengumuman Aktif</h4>
                    <p><?php echo htmlspecialchars($teks_pengumuman); ?></p>
                </div>
            </div>
            <button type="button" class="quick-edit-action">
                Ubah Teks
            </button>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="dashboard-layout">
            <div class="stats-row">
                <div class="card-stat total">
                    <h3>Total Warga Terdaftar</h3>
                    <p><?php echo $total_warga; ?></p>
                </div>
                
                <div class="card-stat tetap">
                    <h3>Warga Status Tetap</h3>
                    <p><?php echo $warga_tetap; ?></p>
                </div>
                
                <div class="card-stat pending">
                    <h3>Pengajuan Surat Pending</h3>
                    <p><?php echo $surat_pending; ?></p>
                </div>
            </div>

            <div class="chart-wrapper">
                <h3>Proporsi Hunian</h3>
                <div class="chart-container">
                    <canvas id="demografiChart"></canvas>
                </div>
            </div>
        </div>

        <div class="saran-wrapper">
            <h3>Daftar Kotak Saran & Masukan Warga</h3>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Waktu</th>
                            <th style="width: 25%;">Nama</th>
                            <th style="width: 55%;">Pesan / Masukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_saran && $result_saran->num_rows > 0): ?>
                            <?php while($row = $result_saran->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['tanggal']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                                <td><?php echo nl2br(htmlspecialchars($row['pesan'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #71717a; padding: 20px;">Belum ada saran atau masukan dari warga.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: KELOLA TEKS BERJALAN                -->
<!-- ========================================== -->
<div class="modal-overlay" id="informasiModal">
    <div class="modal-card">
        <button class="modal-close-btn" onclick="closeInformasiModal()">×</button>
        <div class="modal-header">Kelola Pemberitahuan Berjalan</div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update_informasi">
            
            <div class="form-group">
                <label for="isi_informasi">Teks Pengumuman Aktif:</label>
                <textarea class="form-control" name="isi_informasi" id="isi_informasi" required placeholder="Tulis pengumuman baru di sini..." style="height: 120px; resize: none;"><?php echo htmlspecialchars($teks_pengumuman); ?></textarea>
            </div>

            <div class="modal-footer" style="margin-top: 25px;">
                <button type="button" class="btn-custom btn-secondary-dark" onclick="closeInformasiModal()">Batal</button>
                <button type="submit" class="btn-custom" style="background-color: #0284c7; color: white;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Script Chart.js
    const ctx = document.getElementById('demografiChart').getContext('2d');
    const demografiChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Tetap', 'Kontrak'],
            datasets: [{
                data: [<?php echo $warga_tetap; ?>, <?php echo $warga_kontrak; ?>],
                backgroundColor: ['#10b981', '#f59e0b'],
                borderColor: '#141416',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        color: '#8a8a93',
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        },
                        padding: 10
                    }
                }
            },
            cutout: '75%'
        }
    });

    // Script Modal Kelola Teks Berjalan
    function openInformasiModal() {
        document.getElementById('informasiModal').style.display = 'flex';
    }

    function closeInformasiModal() {
        document.getElementById('informasiModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const informasiModal = document.getElementById('informasiModal');
        if (event.target === informasiModal) {
            closeInformasiModal();
        }
    }
</script>
</body>
</html>