<?php 
session_start(); 
include 'koneksi.php'; 

// ROUTING: Jika sudah login, lempar ke halaman sesuai role masing-masing
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['role'] === 'Admin') {
        header("Location: index_admin.php");
        exit;
    } else {
        header("Location: index_warga.php");
        exit;
    }
}

$error = '';

// PROSES LOGIN DI DALAM HALAMAN YANG SAMA
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username_or_nik']));
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
    } else {
        $query = "SELECT * FROM akun WHERE username = '$username'";
        $result = $conn->query($query);

        if ($result && $result->num_rows === 1) {
            $akun = $result->fetch_assoc();

            if ($password === $akun['password'] || password_verify($password, $akun['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['id_user']   = $akun['id'];
                $_SESSION['username']  = $akun['username'];
                $_SESSION['role']      = $akun['role']; 
                $_SESSION['nama_asli'] = $akun['username']; 

                if ($akun['role'] === 'Warga') {
                    $user_clean = $akun['username'];
                    $check_warga = $conn->query("SELECT id, nama FROM warga WHERE nik = '$user_clean' LIMIT 1");
                    
                    if ($check_warga && $check_warga->num_rows > 0) {
                        $warga_data = $check_warga->fetch_assoc();
                        $_SESSION['id_warga'] = $warga_data['id']; 
                        $_SESSION['nama_asli'] = $warga_data['nama']; 
                    } else {
                        $_SESSION['id_warga'] = $akun['id'];
                    }
                }

                $redirect_target = ($akun['role'] === 'Admin') ? 'index_admin.php' : 'index_warga.php';
                echo "<script>
                        alert('Login Berhasil! Selamat Datang, " . $_SESSION['username'] . "'); 
                        window.location.href = '$redirect_target';
                      </script>";
                exit;

            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak terdaftar di sistem!";
        }
    }
}

// QUERY STATISTIK UNTUK UMUM (Total Unit diubah dari tabel unit_rusun)
$query_unit_all = "SELECT COUNT(*) as total_unit FROM unit_rusun";
$res_unit_all = $conn->query($query_unit_all);
$total_unit = ($res_unit_all) ? $res_unit_all->fetch_assoc()['total_unit'] : 0;

$query_warga = "SELECT COUNT(*) as total_warga FROM warga"; 
$res_warga = $conn->query($query_warga);
$total_warga = ($res_warga) ? $res_warga->fetch_assoc()['total_warga'] : 0;

$query_unit_kosong = "SELECT COUNT(*) as unit_kosong FROM unit_rusun WHERE status = 'Kosong' OR status = 'Tersedia'";
$res_unit = $conn->query($query_unit_kosong);
$unit_kosong = ($res_unit) ? $res_unit->fetch_assoc()['unit_kosong'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Portal Publik RT 008</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS untuk Peta -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            margin-top: 25px !important;
            padding-top: 0 !important;
            display: block !important;
            max-width: 1240px;
            margin-left: auto !important;
            margin-right: auto !important;
            text-align: left;
        }
        
        .menu a {
            color: #e5e5e5;
            text-decoration: none;
            transition: color 0.2s ease;
            cursor: pointer;
        }
        .menu a:hover {
            color: #0ea5e9 !important;
        }

        /* Styling Tombol Login Navbar Baru (Tema Biru & Putih) */
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
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 15px; 
            margin-top: 25px; 
            margin-bottom: 25px; 
            text-align: left;
        }
        .stat-card { background: #11141d; border: 1px solid #1e293b; border-radius: 8px; padding: 16px; text-align: center; }
        .stat-card h3 { color: #10b981; font-size: 24px; font-weight: 700; margin-bottom: 5px; }
        .stat-card p { color: #a3a3a3; font-size: 13px; font-weight: 600; }

        /* Hero Banner Full-Width & Tanpa Garis Biru */
        .hero-banner {
            position: relative;
            width: 100%;
            max-width: 100%;
            border-radius: 0;
            overflow: hidden;
            padding: 60px 50px;
            margin-top: 20px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            background: linear-gradient(rgba(11, 14, 20, 0.75), rgba(11, 14, 20, 0.85)), url('assets/img/rusun klender.jpeg') no-repeat center center;
            background-size: cover;
            border: none;
            box-shadow: none;
            box-sizing: border-box;
        }

        /* Container Rusun Klender Dibuat Transparan, Dipanjangkan ke Kanan & Tempel ke Ujung Kiri */
         .transparent-box {
            background: rgba(17, 20, 29, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px;
            padding: 40px 45px;
            max-width: 780px;
            width: 100%;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            box-sizing: border-box;
            text-align: left;
        }

        /* ========================================================
            FLOATING CHATBOT WIDGET STYLING (BIRU & PUTIH)
            ======================================================== */
        .floating-chat-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #0284c7;
            color: white;
            border: none;
            border-radius: 40px;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.4);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .floating-chat-btn:hover {
            background: #0369a1;
            transform: scale(1.03);
        }

        .floating-chat-btn::before {
            content: "💬";
            font-size: 16px;
        }

        .floating-chat-wrapper {
            position: fixed;
            bottom: 80px;
            right: 25px;
            width: 340px;
            height: 420px;
            max-width: calc(100vw - 30px);
            background: linear-gradient(145deg, #131722, #0b0e14);
            border: 1px solid rgba(2, 132, 199, 0.3);
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.85);
            display: none;
            flex-direction: column;
            z-index: 9998;
            overflow: hidden;
            animation: slideUp 0.25s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chat-header {
            background: rgba(18, 22, 31, 0.95) !important;
            border-bottom: 1px solid #1e293b !important;
            padding: 14px 18px !important;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .chat-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chat-header-left::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
        }

        .close-chat-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-chat-btn:hover {
            color: #fff;
        }

        .chat-box {
            padding: 12px;
            height: 290px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
            flex-grow: 1;
        }
        .chat-box::-webkit-scrollbar {
            width: 6px;
        }
        .chat-box::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }
        .chat-box::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 3px;
        }
        .chat-box::-webkit-scrollbar-thumb:hover {
            background: #0284c7;
        }
        .message {
            max-width: 85%;
            padding: 11px 14px !important;
            border-radius: 12px !important;
            font-size: 13px !important;
            line-height: 1.5;
            animation: fadeIn 0.3s ease;
            word-wrap: break-word;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.bot {
            background: #1a2233 !important;
            color: #e2e8f0 !important;
            border: 1px solid #1e293b !important;
            align-self: flex-start;
            border-bottom-left-radius: 4px !important;
        }

        .message.user {
            background: #0284c7 !important;
            color: #ffffff !important;
            align-self: flex-end;
            border-bottom-right-radius: 4px !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .input-group {
            background: #10141d !important;
            border-top: 1px solid #1e293b !important;
            padding: 12px 16px !important;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-shrink: 0;
        }

        .input-group input[type="text"] {
            background: #171f2e !important;
            border: 1px solid #1e293b !important;
            border-radius: 8px !important;
            padding: 10px 12px !important;
            color: #ffffff !important;
            font-size: 13px !important;
            flex: 1;
            transition: all 0.2s ease;
        }

        .input-group input[type="text"]:focus {
            outline: none;
            border-color: #0284c7 !important;
            background: #1c2638 !important;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .input-group button {
            background: #0284c7 !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 10px 16px !important;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
        }

        .input-group button:hover {
            background: #0369a1 !important;
        }

        .input-group button:active {
            transform: scale(0.96);
        }

        /* Styling Kontak Kami / Footer Section (Full-Width / Dipetakan ke Ujung Kiri-Kanan Tanpa Bolong) */
        .contact-section {
            background: #11141d;
            border-top: 1px solid #1e293b;
            border-bottom: 1px solid #1e293b;
            border-left: none;
            border-right: none;
            border-radius: 0;
            padding: 50px 60px;
            margin: 40px 0 !important;
            width: 100%;
            max-width: 100%;
            color: #e5e5e5;
            text-align: left;
            box-sizing: border-box;
        }
        .contact-title {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 20px;
        }
        .map-container {
            width: 100%;
            height: 250px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid #1e293b;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        @media(max-width: 768px) {
            .contact-grid { grid-template-columns: 1fr; }
            .contact-section { padding: 30px 20px !important; }
        }
        .contact-info-item {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .contact-icon {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }
        .contact-details h4 {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 5px;
        }
        .contact-details p {
            font-size: 13px;
            color: #a3a3a3;
            line-height: 1.5;
        }
        .contact-form .form-group {
            margin-bottom: 15px;
        }
        .contact-form label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #a3a3a3;
            margin-bottom: 6px;
        }
        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 10px 12px;
            background: #1a1f2c;
            border: 1px solid #1e293b;
            border-radius: 6px;
            color: white;
            font-size: 13px;
            box-sizing: border-box;
        }
        .contact-form input:focus, .contact-form textarea:focus {
            outline: none;
            border-color: #0284c7;
        }
        .btn-submit-msg {
            background: #0284c7;
            color: white;
            border: none;
            padding: 12px 20px;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
        }
        .btn-submit-msg:hover {
            background: #0369a1;
        }

        /* Styling Modal Login */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .login-box {
            background: #111111;
            border: 1px solid #1e293b;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.5);
            position: relative;
            text-align: left;
        }
        .brand-modal { font-weight: 800; font-size: 28px; color: #0284c7; text-transform: uppercase; text-align: center; margin-bottom: 5px; }
        .title-modal { text-align: center; font-size: 14px; color: #777777; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #0284c7; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 12px; background: #222222; border: 1px solid #333333; border-radius: 6px; color: white; font-size: 14px; transition: border 0.2s; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: #0284c7; background: #2a2a2a; }
        .btn-login { width: 100%; background: #0284c7; color: white; border: none; padding: 14px; font-size: 15px; font-weight: 700; border-radius: 6px; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn-login:hover { background: #0369a1; }
        .alert { padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 20px; font-weight: 600; background: rgba(2, 132, 199, 0.1); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.3); line-height: 1.4; }
        .footer-link { text-align: center; margin-top: 25px; font-size: 13px; color: #aaa; }
        .footer-link a { color: #10b981; text-decoration: none; font-weight: 600; }
        .footer-link a:hover { text-decoration: underline; }
        .close-btn { position: absolute; top: 15px; right: 20px; background: none; border: none; color: #aaa; font-size: 20px; cursor: pointer; }
        .close-btn:hover { color: #fff; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row">
        <div class="brand" style="color: #0284c7;">Rusun-KL</div>
        <div class="menu">
            <a href="index.php" class="active">Home</a>
            <a href="about.php">About Us</a>
            <a href="contact_us.php">Contact Us</a>
            
            <a onclick="openLoginModal()" class="btn-login-nav">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                Login
            </a>
        </div>
    </div>
</div>

<div class="hero-banner">
    <div class="transparent-box">
        <div class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">Portal Umum / Publik</div>
        <div class="tagline">Sistem cerdas, pelayanan administrasi tanpa batas.</div>
        <h1 style="color: #ffffff;">Selamat Datang Di Portal Rusun Klender</h1>
        
        <div class="meta-info" style="justify-content: flex-start;">
            <span>RT 008 / RW 001</span>
            <span>Jakarta Timur</span>
        </div>
        
        <p class="description">
            Selamat datang di platform pusat kendali informasi Rumah Susun Klender. Di bawah ini adalah statistik hunian waktu nyata (<em>real-time</em>) di lingkungan RT 008. Untuk melihat data kependudukan lengkap atau mengubah informasi, silakan masuk menggunakan akun Anda melalui tombol Login di atas.
        </p>
    </div>
</div>

<div style="max-width: 1240px; margin: 25px auto; padding: 0 15px;">
    <div class="stats-grid" style="margin-top: 0; margin-bottom: 0;">
        <div class="stat-card">
            <h3><?php echo $unit_kosong; ?></h3>
            <p>Unit Kosong</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_warga; ?></h3>
            <p>Warga Tinggal</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_unit; ?></h3>
            <p>Total Unit</p>
        </div>
    </div>
</div>

<!-- FLOATING CHATBOT WIDGET -->
<button class="floating-chat-btn" onclick="toggleChat()">Tanya AI</button>

<div class="floating-chat-wrapper" id="chatWrapper">
    <div class="chat-header">
        <div class="chat-header-left">Asisten Virtual Rusun Klender</div>
        <button class="close-chat-btn" onclick="toggleChat()">&times;</button>
    </div>
    <div class="chat-box" id="chatBox">
        <div class="message bot">Halo! Saya adalah AI Chatbot Layanan Rusun Klender RT08. Ada yang bisa saya bantu? Anda bisa menanyakan informasi umum seputar ketersediaan unit rumah kosong saat ini.</div>
    </div>
    <div class="input-group">
        <input type="text" id="userInput" placeholder="Ketik pertanyaan Anda..." onkeydown="if(event.key === 'Enter') sendMessage()">
        <button onclick="sendMessage()">Kirim</button>
    </div>
</div>

<!-- SECTION: KONTAK KAMI & PETA (FULL WIDTH TANPA GAP/BOLONG KIRI-KANAN) -->
<div class="contact-section">
    <div class="contact-title">Kontak Kami</div>
    
    <div id="map" class="map-container"></div>

    <div class="contact-grid">
        <div>
            <div class="contact-info-item">
                <div class="contact-icon">📍</div>
                <div class="contact-details">
                    <h4>Alamat Utama</h4>
                    <p>Rumah Susun Klender RT 008 / RW 001, Kelurahan Klender, Kecamatan Duren Sawit, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13470</p>
                </div>
            </div>

            <div class="contact-info-item">
                <div class="contact-icon">⏰</div>
                <div class="contact-details">
                    <h4>Jam Pelayanan</h4>
                    <p>Senin - Jumat<br>08:00 - 15:00 WIB</p>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <h4 style="color: #fff; margin-bottom: 5px; font-size: 15px;">Punya pertanyaan?</h4>
            <p style="color: #a3a3a3; font-size: 13px; margin-bottom: 15px;">Jangan ragu untuk mengirim kami pesan. Kami akan dengan senang hati membantu Anda.</p>
            
            <form action="simpan_saran.php" method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" placeholder="Masukkan nama Anda" required>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="Masukkan email aktif Anda" required>
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea name="pesan" rows="4" placeholder="Tuliskan pesan atau pertanyaan Anda di sini..." required></textarea>
                </div>
                <button type="submit" class="btn-submit-msg">Kirim Pesan</button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL POPUP LOGIN -->
<div class="modal-overlay" id="loginModal">
    <div class="login-box">
        <button class="close-btn" onclick="closeLoginModal()">&times;</button>
        <div class="brand-modal">Rusun-KL</div>
        <div class="title-modal">Masuk ke Sistem Kependudukan</div>

        <?php if (!empty($error)): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username_or_nik" placeholder="Masukkan Username Anda" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password Anda" required>
            </div>
            
            <button type="submit" name="login" class="btn-login">MASUK</button>
        </form>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-6.2205, 106.9234], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([-6.2205, 106.9234]).addTo(map)
        .bindPopup('<b>Rusun Klender RT 008</b><br>Jakarta Timur')
        .openPopup();

    function openLoginModal() {
        document.getElementById('loginModal').style.display = 'flex';
    }

    function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
    }

    <?php if (!empty($error)): ?>
        window.onload = function() {
            openLoginModal();
        };
    <?php endif; ?>

    function toggleChat() {
        const chatWrapper = document.getElementById('chatWrapper');
        if (chatWrapper.style.display === 'flex') {
            chatWrapper.style.display = 'none';
        } else {
            chatWrapper.style.display = 'flex';
            document.getElementById('userInput').focus();
        }
    }

    async function sendMessage() {
        const inputField = document.getElementById('userInput');
        const chatBox = document.getElementById('chatBox');
        const messageText = inputField.value.trim();

        if (messageText === '') return;

        chatBox.innerHTML += `<div class="message user">${escapeHtml(messageText)}</div>`;
        inputField.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        const typingId = 'typing-' + Date.now();
        chatBox.innerHTML += `
            <div id="${typingId}" class="message bot" style="color: #94a3b8; font-style: italic; display: flex; align-items: center; gap: 5px;">
                <span>Chatbot sedang mengetik...</span>
            </div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

        const currentRole = window.location.pathname.includes('warga') ? 'warga' : 'publik';

        try {
            const response = await fetch('chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    message: messageText, 
                    role: currentRole 
                })
            });
            const data = await response.json();

            const typingElement = document.getElementById(typingId);
            if (typingElement) typingElement.remove();

            if (data.status === 'success') {
                chatBox.innerHTML += `<div class="message bot">${data.reply}</div>`;
            } else {
                chatBox.innerHTML += `<div class="message bot">Terjadi kesalahan pada sistem server.</div>`;
            }

        } catch (error) {
            const typingElement = document.getElementById(typingId);
            if (typingElement) typingElement.remove();

            chatBox.innerHTML += `<div class="message bot">Maaf, sistem AI sedang offline atau database terputus.</div>`;
        }

        chatBox.scrollTop = chatBox.scrollHeight;
    }

    /* Fungsi Escape HTML untuk Keamanan Chatbot */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>
</body>
</html>