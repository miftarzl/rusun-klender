<?php 
session_start(); 
include 'koneksi.php'; 

// Proteksi Keamanan: Jika belum login, tendang kembali ke index umum
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// QUERY RUNNING TEXT / PENGUMUMAN
$query_marquee = "SELECT konten FROM informasi WHERE judul = 'Pengumuman' LIMIT 1";
$result_marquee = $conn->query($query_marquee); 
$data_marquee = $result_marquee ? $result_marquee->fetch_assoc() : null;
$teks_pengumuman = !empty($data_marquee['konten']) ? $data_marquee['konten'] : "Selamat datang di Portal Layanan Digital Rusun Klender RT 008.";

// QUERY STATISTIK UNTUK WARGA (Total Unit diganti dari tabel unit_rusun karena duplikat sudah dibersihkan)
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
    <title>ID-RUSUN | Dashboard Warga</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #0b0e14;
            color: #e5e5e5;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background-color: #0b0e14;
            border-bottom: 1px solid #1e293b;
        }

        .navbar .brand {
            color: #0284c7;
            font-weight: 700;
            font-size: 20px;
            letter-spacing: 0.5px;
        }

        .navbar .menu {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .navbar .menu a {
            color: #e5e5e5;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .navbar .menu a:hover,
        .navbar .menu a.active {
            color: #0ea5e9 !important; 
        }

        /* Styling Tombol Logout & Info Warga */
        .user-greeting {
            color: #10b981; 
            font-weight: 600; 
            font-size: 13px;
            margin-right: 5px;
        }

        .btn-logout {
            background: #0284c7 !important;
            color: white !important;
            padding: 7px 16px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            text-decoration: none !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3) !important;
            transition: background 0.2s ease !important;
            border: none !important;
        }

        .btn-logout:hover {
            background: #0369a1 !important;
        }

        /* Running Text / Announcement Bar khusus di bawah Navbar */
        .announcement-bar {
            background: #0e131f;
            border-bottom: 1px solid #1e293b;
            padding: 10px 50px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-sizing: border-box;
        }

        .announcement-badge {
            background: rgba(2, 132, 199, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(2, 132, 199, 0.3);
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .announcement-content marquee {
            color: #cbd5e1;
            font-size: 13px;
        }
        
        /* Hero Banner Full-Width */
        .hero-banner {
            position: relative;
            width: 100%;
            min-height: 450px;
            display: flex;
            align-items: center;
            padding: 40px 50px;
            box-sizing: border-box;
            background: linear-gradient(rgba(11, 14, 20, 0.75), rgba(11, 14, 20, 0.88)), url('assets/img/rusun klender.jpeg') no-repeat center center;
            background-size: cover;
        }

        /* Kotak Transparan (Glassmorphism Box) */
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

        .badge-portal {
            display: inline-block;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .transparent-box .tagline {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 6px;
            font-style: italic;
        }

        .transparent-box h1 {
            font-size: 40px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
        }

        .transparent-box .meta-info {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 18px;
            display: flex;
            gap: 20px;
        }

        .transparent-box .description {
            font-size: 14px;
            color: #cbd5e1;
            line-height: 1.65;
            margin: 0;
        }

        /* Statistik Grid */
        .stats-section {
            max-width: 1300px;
            margin: 30px auto 40px auto;
            padding: 0 50px;
            box-sizing: border-box;
        }

        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 20px; 
        }

        .stat-card { 
            background: #11141d; 
            border: 1px solid #1e293b; 
            border-radius: 10px; 
            padding: 28px; 
            text-align: center; 
        }

        .stat-card h3 { 
            color: #10b981; 
            font-size: 34px; 
            font-weight: 700; 
            margin: 0 0 6px 0; 
        }

        .stat-card p { 
            color: #94a3b8; 
            font-size: 14px; 
            font-weight: 600; 
            margin: 0; 
        }

        @media(max-width: 768px) {
            .navbar { padding: 15px 20px; flex-direction: column; gap: 15px; }
            .announcement-bar { padding: 10px 20px; }
            .hero-banner, .stats-section { padding: 20px; }
            .stats-grid { grid-template-columns: 1fr; }
        }

        /* FLOATING CHATBOT WIDGET */
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
            from { opacity: 0; transform: translateY(15px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .chat-header {
            background: rgba(18, 22, 31, 0.95);
            border-bottom: 1px solid #1e293b;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .close-chat-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 16px;
            cursor: pointer;
        }
        .close-chat-btn:hover { color: #fff; }

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

        .message {
            max-width: 85%;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message.bot {
            background: #1a2233;
            color: #e2e8f0;
            border: 1px solid #1e293b;
            align-self: flex-start;
            border-bottom-left-radius: 3px;
        }

        .message.user {
            background: #0284c7;
            color: #ffffff;
            align-self: flex-end;
            border-bottom-right-radius: 3px;
        }

        .input-group {
            background: #10141d;
            border-top: 1px solid #1e293b;
            padding: 10px 12px;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .input-group input[type="text"] {
            background: #171f2e;
            border: 1px solid #1e293b;
            border-radius: 6px;
            padding: 8px 10px;
            color: #ffffff;
            font-size: 12px;
            flex: 1;
        }

        .input-group input[type="text"]:focus {
            outline: none;
            border-color: #0284c7;
        }

        .input-group button {
            background: #0284c7;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="brand">RUSUN-KL</div>
    <div class="menu">
        <a href="index_warga.php" class="active">Home</a>
        <a href="warga_surat.php">Layanan Dokumen</a>
        <a href="warga_iuran.php">Informasi Iuran</a>
        <a href="about.php">About Us</a>
        <a href="contact_us.php">Contact Us</a>
        
        <span class="user-greeting">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?> (Warga)</span>
        
        <a href="logout.php" class="btn-logout">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Logout
        </a>
    </div>
</div>

<!-- Running Text Bar di Bawah Navbar (Aman & Tidak Menabrak) -->
<div class="announcement-bar">
    <div class="announcement-badge">INFO RT 008</div>
    <div class="announcement-content" style="width: 100%; overflow: hidden;">
        <marquee scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();">
            <span>📢 <?php echo htmlspecialchars($teks_pengumuman); ?></span>
        </marquee>
    </div>
</div>

<!-- Hero Banner Full-Width dengan Kotak Transparan -->
<div class="hero-banner">
    <div class="transparent-box">
        <div class="badge-portal">MODE: WARGA RESMI</div>
        <div class="tagline">Sistem cerdas, pelayanan administrasi tanpa batas.</div>
        <h1>Selamat Datang Kembali</h1>
        
        <div class="meta-info">
            <span>RT 008 / RW 001</span>
            <span>Jakarta Timur</span>
        </div>
        
        <p class="description">
            Halo <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>, Anda berhasil masuk ke sistem internal warga RT 008. Sekarang Anda dapat mengakses berbagai layanan dokumen, informasi iuran, serta menggunakan asisten virtual untuk kebutuhan administrasi warga secara cepat dan transparan.
        </p>
    </div>
</div>

<!-- Statistik Warga Grid (Total Unit, Warga Tinggal, Unit Kosong) -->
<div class="stats-section">
    <div class="stats-grid">
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
<button class="floating-chat-btn" onclick="toggleChat()">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
    Tanya AI
</button>

<div class="floating-chat-wrapper" id="chatWrapper">
    <div class="chat-header">
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="width: 7px; height: 7px; background-color: #10b981; border-radius: 50%; display:inline-block;"></span>
            Asisten Virtual Rusun Klender RT08
        </div>
        <button class="close-chat-btn" onclick="toggleChat()">&times;</button>
    </div>
    <div class="chat-box" id="chatBox">
        <div class="message bot">Halo <?php echo htmlspecialchars($_SESSION['username']); ?>! Sesi aman warga aktif. Silakan tanyakan hal-hal terkait administrasi rusun.</div>
    </div>
    <div class="input-group">
        <input type="text" id="userInput" placeholder="Ketik pertanyaan Anda..." onkeydown="if(event.key === 'Enter') sendMessage()">
        <button onclick="sendMessage()">Kirim</button>
    </div>
</div>

<script>
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
            <div id="${typingId}" class="message bot" style="color: #94a3b8; font-style: italic;">
                <span>Sedang mengetik...</span>
            </div>`;
            
        chatBox.scrollTop = chatBox.scrollHeight;

        const currentRole = 'warga';

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