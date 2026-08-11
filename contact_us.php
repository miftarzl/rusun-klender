<?php 
session_start(); 
include 'koneksi.php'; 

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$is_admin = $is_logged_in && $_SESSION['role'] === 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            background-color: #0b0e14 !important; 
            color: #e2e8f0 !important; 
            font-family: 'Inter', sans-serif !important;
        }
        
        /* NAVBAR & HOVER EFFECT */
        .navbar .menu a {
            color: #e5e5e5;
            text-decoration: none;
            transition: color 0.2s ease;
            cursor: pointer;
        }
        .navbar .menu a.active {
            color: #0ea5e9 !important;
        }
        .navbar .menu a:hover {
            color: #0ea5e9 !important;
        }
        .user-info-badge {
            color: #10b981;
            font-weight: 600;
            margin-left: 15px;
            font-size: 13px;
        }

        /* Styling Tombol Logout Navbar */
        .btn-login-nav {
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

        .btn-login-nav:hover {
            background: linear-gradient(135deg, #0369a1, #075985) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.55) !important;
            color: white !important;
        }

        .btn-login-nav:active {
            transform: translateY(0) !important;
        }

        /* Container Layout - Jarak atas disamakan menjadi 85px agar tidak terlalu ke atas */
        .container {
            margin-top: 85px !important;
            padding-top: 0 !important;
            display: block !important;
            max-width: 1240px;
            margin-left: auto !important;
            margin-right: auto !important;
            text-align: left;
        }

        /* Badge Tema */
        .badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        h1 {
            color: #ffffff;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .tagline {
            color: #94a3b8;
            font-size: 15px;
            font-weight: 500;
        }

        /* Style untuk Grid dan Kartu Kontak */
        .contact-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 20px; 
            margin-top: 30px; 
        }
        .contact-card { 
            background: #11141d; 
            border: 1px solid #1e293b; 
            padding: 25px; 
            border-radius: 12px; 
            transition: transform 0.2s, border-color 0.2s; 
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        .contact-card:hover { 
            transform: translateY(-3px); 
            border-color: #0284c7; 
        }
        .contact-card h3 { 
            color: #0284c7; 
            margin-bottom: 12px; 
            font-size: 18px; 
        }
        .contact-card p { 
            color: #cbd5e1; 
            font-size: 14px; 
            line-height: 1.6; 
        }

        /* Container Map */
        .map-section {
            margin-top: 35px;
            background: #11141d;
            border: 1px solid #1e293b;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        .map-iframe-wrapper {
            width: 100%;
            height: 380px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #000000;
        }
        /* Filter CSS dihapus agar peta Google Maps kembali ke warna standar bawaan (putih/terang) */
        .map-iframe-wrapper iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row">
        <div class="brand" style="color: #0284c7;">Rusun-KL</div>
        <div class="menu">
            <?php if ($is_logged_in && $_SESSION['role'] === 'Warga'): ?>
                <!-- STATUS: LOGIN SEBAGAI WARGA -->
                <a href="index_warga.php">Home</a>
                <a href="warga_surat.php">Layanan Dokumen</a>
                <a href="warga_iuran.php">Informasi Iuran</a>
                <a href="about.php">About Us</a>
                <a href="contact_us.php" class="active">Contact Us</a>
                <span class="user-info-badge">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?> (Warga)</span>
                <a href="logout.php" class="btn-login-nav">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </a>
            <?php else: ?>
                <!-- STATUS: BELUM LOGIN (PUBLIK) OR LOGIN SEBAGAI ADMIN -->
                <a href="index.php">Home</a>
                <?php if ($is_logged_in): ?>
                    <a href="warga.php">Data Warga</a>
                <?php endif; ?>
                <a href="about.php">About Us</a>
                <a href="contact_us.php" class="active">Contact Us</a>
                <?php if ($is_admin): ?>
                    <a href="tambah_warga.php">Tambah Warga</a>
                    <a href="admin_informasi.php">Perbarui Pengumuman</a>
                <?php endif; ?>
                
                <?php if ($is_logged_in): ?>
                    <span class="user-info-badge">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)</span>
                    <a href="logout.php" class="btn-login-nav">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Logout
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="main-content" style="width: 100%; max-width: 1100px; margin: 20px auto 40px auto; flex: none;">
        <div class="badge">Hubungi Kami</div>
        <h1>Kontak Pengurus</h1>
        <p class="tagline">Layanan Informasi Fisik dan Sekretariat RT 008</p>
        
        <div class="contact-grid">
            <div class="contact-card">
                <h3>📍 Alamat Sekretariat</h3>
                <p>Rumah Susun Klender, Blok Tempat Pengurus<br>RT 008 / RW 001, Kelurahan Malaka Jaya<br>Kecamatan Duren Sawit, Jakarta Timur<br>DKI Jakarta, 13460</p>
            </div>
            
            <div class="contact-card">
                <h3>🕒 Jam Operasional RT</h3>
                <p><strong style="color: #ffffff;">Senin - Jumat:</strong> 19.30 - 21.30 WIB<br><strong style="color: #ffffff;">Sabtu - Minggu:</strong> Dengan Perjanjian Lebih Awal<br><br><em style="color: #94a3b8;">*Untuk keperluan tanda tangan fisik surat pengantar kependudukan.</em></p>
            </div>
            
            <div class="contact-card">
                <h3>📞 Layanan Darurat</h3>
                <p>Jika ada kendala mendesak mengenai data kependudukan fisik atau kendala teknis pada sistem AI Chatbot, silakan laporankan hal tersebut kepada kami lewat kotak saran.</p>
            </div>
        </div>

        <!-- Integrasi Peta Lokasi Google Maps -->
        <div class="map-section">
            <h3 style="color: #fff; margin-top: 0; margin-bottom: 15px; font-size: 18px;">🗺️ Peta Lokasi Hunian (RT 008)</h3>
            <div class="map-iframe-wrapper">
                <iframe src="https://maps.google.com/maps?q=Rusun%20Klender%20Jakarta%20Timur&t=&z=17&ie=UTF8&iwloc=&output=embed" loading="lazy" allowfullscreen></iframe>
            </div>
        </div>

    </div>
</div>

</body>
</html>