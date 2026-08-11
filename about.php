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
    <title>ID-RUSUN | About Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            background-color: #0b0e14 !important; 
            color: #e2e8f0 !important; 
            font-family: 'Inter', sans-serif !important;
        }
        
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

        /* Container Layout - Jarak atas diperbesar agar lebih turun */
        .container {
            margin-top: 85px !important; 
            padding-top: 0 !important;
            display: block !important;
            max-width: 1240px;
            margin-left: auto !important;
            margin-right: auto !important;
            text-align: left;
        }

        /* Style untuk Foto Rusun */
        .rusun-hero-box {
            margin-top: 25px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #1e293b;
            background: #11141d;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6);
        }
        .rusun-img {
            width: 100%;
            height: 380px;
            object-fit: cover;
            display: block;
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }
        .rusun-img:hover {
            opacity: 1;
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
                <a href="about.php" class="active">About Us</a>
                <a href="contact_us.php">Contact Us</a>
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
                <a href="about.php" class="active">About Us</a>
                <a href="contact_us.php">Contact Us</a>
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
    <div class="main-content" style="max-width: 900px; margin: 40px auto 50px auto; flex: none; width: 100%;">
        <div class="badge">Tentang Platform</div>
        <h1>Mengenal Rusun-KL</h1>
        <p class="tagline">Transformasi Digital Administrasi Hunian Vertikal</p>
        
        <!-- Foto Gedung Rusun -->
        <div class="rusun-hero-box">
            <img src="assets/img/rusun klender.jpeg" alt="Gedung Rusun Klender RT 008" class="rusun-img" onerror="this.src='https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?q=80&w=1000&auto=format&fit=cover';">
        </div>
        
        <div style="margin-top: 30px; line-height: 1.8; color: #cbd5e1; display: flex; flex-direction: column; gap: 20px; font-size: 15px;">
            <p>
                <strong style="color: #ffffff;">Rusun-KL</strong> adalah platform pusat kendali informasi kependudukan dan layanan mandiri cerdas yang dirancang khusus untuk lingkungan <strong style="color: #ffffff;">Rumah Susun Klender RT 008 / RW 001, Jakarta Timur</strong>. Sistem ini mengintegrasikan basis data fisik kependudukan ke dalam arsitektur web modern guna menggantikan pencatatan konvensional yang rentan terhadap risiko kehilangan atau kerusakan data.
            </p>
            <p>
                Dikembangkan sebagai solusi inovatif, platform ini membawa fitur utama berupa <strong style="color: #ffffff;">Asisten Virtual berbasis Natural Language Processing (NLP)</strong>. Melalui chatbot pintar ini, warga dapat melakukan interaksi dua arah menggunakan bahasa alami untuk mencari informasi alokasi unit, ketersediaan blok yang kosong, nomor lantai, hingga mencarikan pengumuman RT terbaru secara <em>real-time</em> tanpa harus menunggu respon manual dari pengurus.
            </p>
            <p>
                Dengan adanya integrasi antara manajemen basis data MySQL yang terstruktur dan kecerdasan komputasi bahasa (NLP), Rusun-AI hadir demi mewujudkan tata kelola administrasi tingkat rukun tetangga yang transparan, responsif, dan tanpa batas waktu.
            </p>
        </div>

        <!-- GALERI FOTO RUSUN / FASILITAS -->
        <div style="margin-top: 45px; margin-bottom: 50px;">
            <h3 style="color: #ffffff; font-size: 18px; margin-bottom: 20px; border-left: 3px solid #0284c7; padding-left: 10px;">📸 Galeri Rusun & Fasilitas</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                
                <div style="background: #11141d; border: 1px solid #1e293b; border-radius: 8px; overflow: hidden; transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#0284c7';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#1e293b';">
                    <div style="width: 100%; height: 160px; background: #171f2e; overflow: hidden;">
                        <img src="assets/img/lapangan.jpeg" alt="Fasilitas Rusun 1" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.src='https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600&auto=format&fit=cover';">
                    </div>
                    <div style="padding: 16px;">
                        <h4 style="color: #ffffff; font-size: 15px; margin-bottom: 6px;">Area Lingkungan Blok</h4>
                        <p style="color: #94a3b8; font-size: 13px; line-height: 1.5;">Kawasan blok hunian yang tertata bersih dan nyaman untuk warga RT 008.</p>
                    </div>
                </div>

                <div style="background: #11141d; border: 1px solid #1e293b; border-radius: 8px; overflow: hidden; transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#0284c7';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#1e293b';">
                    <div style="width: 100%; height: 160px; background: #171f2e; overflow: hidden;">
                        <img src="assets/img/rw01.jpeg" alt="Fasilitas Rusun 2" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.src='https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=600&auto=format&fit=cover';">
                    </div>
                    <div style="padding: 16px;">
                        <h4 style="color: #ffffff; font-size: 15px; margin-bottom: 6px;">Sekretariat & Pos RT</h4>
                        <p style="color: #94a3b8; font-size: 13px; line-height: 1.6;">Pusat koordinasi, pelayanan administrasi, dan koordinasi warga sehari-hari.</p>
                    </div>
                </div>

                <div style="background: #11141d; border: 1px solid #1e293b; border-radius: 8px; overflow: hidden; transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='#0284c7';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#1e293b';">
                    <div style="width: 100%; height: 160px; background: #171f2e; overflow: hidden;">
                        <img src="assets/img/gudang motor.jpeg" alt="Fasilitas Rusun 3" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.src='https://images.unsplash.com/photo-1574359411503-822b39f03314?q=80&w=600&auto=format&fit=cover';">
                    </div>
                    <div style="padding: 16px;">
                        <h4 style="color: #ffffff; font-size: 15px; margin-bottom: 6px;">Fasilitas Bersama</h4>
                        <p style="color: #94a3b8; font-size: 13px; line-height: 1.6;">Ruang terbuka dan fasilitas pendukung aktivitas warga di lingkungan rusun.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>