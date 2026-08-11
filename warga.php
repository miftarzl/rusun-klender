<?php
session_start();
include 'koneksi.php'; 

// Proteksi Keamanan: Hanya Admin yang boleh akses halaman Data Warga
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

$success_message = "";
$error_message = "";

// Proses Tambah Data Warga Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah_warga') {
    $nik            = $_POST['nik'];
    $no_kk          = $_POST['no_kk'];
    $nama           = $_POST['nama'];
    $tempat_lahir   = $_POST['tempat_lahir'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $agama          = $_POST['agama'];
    $pekerjaan      = $_POST['pekerjaan'];
    $alamat         = $_POST['alamat'];
    $status_tinggal = $_POST['status_tinggal'];

    $stmt = $conn->prepare("INSERT INTO warga (nik, no_kk, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, pekerjaan, alamat, status_tinggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $nik, $no_kk, $nama, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $agama, $pekerjaan, $alamat, $status_tinggal);
    
    if ($stmt->execute()) {
        $success_message = "Data warga baru berhasil ditambahkan!";
    } else {
        $error_message = "Gagal menambah data: " . $conn->error;
    }
    $stmt->close();
}

// Proses Update Data Warga jika Form Edit disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_warga') {
    $nik_lama       = $_POST['nik_lama'];
    $nik            = $_POST['nik'];
    $no_kk          = $_POST['no_kk'];
    $nama           = $_POST['nama'];
    $tempat_lahir   = $_POST['tempat_lahir'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];
    $jenis_kelamin  = $_POST['jenis_kelamin'];
    $agama          = $_POST['agama'];
    $pekerjaan      = $_POST['pekerjaan'];
    $alamat         = $_POST['alamat'];
    $status_tinggal = $_POST['status_tinggal'];

    $stmt = $conn->prepare("UPDATE warga SET nik=?, no_kk=?, nama=?, tempat_lahir=?, tanggal_lahir=?, jenis_kelamin=?, agama=?, pekerjaan=?, alamat=?, status_tinggal=? WHERE nik=?");
    $stmt->bind_param("sssssssssss", $nik, $no_kk, $nama, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $agama, $pekerjaan, $alamat, $status_tinggal, $nik_lama);
    
    if ($stmt->execute()) {
        $success_message = "Data warga berhasil diperbarui!";
    } else {
        $error_message = "Gagal memperbarui data: " . $conn->error;
    }
    $stmt->close();
}

// Proses Hapus Data Warga
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus_warga') {
    $nik = $_POST['nik'];

    $stmt = $conn->prepare("DELETE FROM warga WHERE nik = ?");
    $stmt->bind_param("s", $nik);
    
    if ($stmt->execute()) {
        $success_message = "Data warga berhasil dihapus dari sistem.";
    } else {
        $error_message = "Gagal menghapus data: " . $conn->error;
    }
    $stmt->close();
}

// Logika Pencarian & Query Data Warga
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM warga WHERE nik LIKE ? OR nama LIKE ? OR alamat LIKE ? OR pekerjaan LIKE ?");
    $search_param = "%" . $search . "%";
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM warga"; 
    $result = $conn->query($query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Data Warga</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #0d0d0f !important;
            color: #e4e4e7 !important;
            font-family: 'Inter', sans-serif;
        }

        .navbar .brand {
            color: #0284c7 !important;
        }

        .menu a {
            color: #e5e5e5;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .menu a:hover, .menu a.active {
            color: #0284c7 !important;
        }

        /* STYLING TOMBOL LOGOUT BIRU (SERUPA INDEX_ADMIN.PHP) */
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
            margin-left: 20px !important;
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
            background-color: #0284c7;
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
            margin-bottom: 30px !important;
            text-align: left !important;
            max-width: 800px;
        }

        .table-wrapper {
            background: #141416;
            border: 1px solid #232326;
            padding: 25px;
            border-radius: 10px;
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
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .admin-table tr:hover {
            background: #18181b;
        }

        .btn-action {
            background-color: #0284c7;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-action:hover { background-color: #0369a1; }
        .btn-secondary { background-color: #222222; border: 1px solid #333333; color: #e4e4e7; }
        .btn-secondary:hover { background-color: #333333; color: #fff; }
        .btn-warning { background-color: #f59e0b; color: #ffffff; }
        .btn-warning:hover { background-color: #d97706; }
        .btn-danger { background-color: #ef4444; color: #ffffff; }
        .btn-danger:hover { background-color: #dc2626; }

        .btn-detail { 
            background-color: transparent; 
            color: #0284c7; 
            border: 1px solid #0284c7; 
            padding: 6px 14px; 
            font-size: 12px; 
            font-weight: 600; 
            border-radius: 4px; 
            cursor: pointer; 
            transition: 0.2s; 
        }
        .btn-detail:hover { 
            background-color: #0284c7; 
            color: #ffffff; 
        }

        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #10b981; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; font-weight: 600; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; font-weight: 600; }

        .unit-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* SEARCH BAR STYLES */
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-input {
            flex: 1;
            padding: 10px 14px;
            background: #141416;
            border: 1px solid #232326;
            border-radius: 6px;
            color: #fff;
            font-size: 14px;
        }
        .search-input:focus { outline: none; border-color: #0284c7; }

        /* MODAL POPUP STYLES */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(4px); overflow-y: auto; padding: 20px; }
        .modal-card { background: #141416; border: 1px solid #0284c7; border-radius: 10px; width: 100%; max-width: 520px; padding: 25px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); position: relative; animation: fadeIn 0.3s ease-in-out; margin: auto; }
        @keyframes fadeIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-header { font-size: 18px; font-weight: 700; color: #ffffff; margin-bottom: 15px; border-bottom: 1px solid #232326; padding-bottom: 10px; text-transform: uppercase; }
        .modal-body p { font-size: 13px; margin-bottom: 8px; color: #a1a1aa; }
        .modal-body strong { color: #ffffff; }
        .modal-close-btn { position: absolute; top: 15px; right: 20px; background: transparent; border: none; color: #a1a1aa; font-size: 20px; cursor: pointer; transition: 0.2s; }
        .modal-close-btn:hover { color: #ffffff; }
        .modal-footer { margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; }

        /* FORM STYLES DI DALAM MODAL */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: #a1a1aa; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { width: 100%; padding: 10px 14px; background: #1a1a1e; border: 1px solid #232326; border-radius: 6px; color: #fff; font-size: 14px; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: #0284c7; }
        select.form-control option { background: #141416; color: #fff; }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; border-bottom: 1px solid #232326;">
        <div class="brand" style="font-weight: 700; font-size: 20px; text-transform: uppercase;">Rusun-AI [ADMIN]</div>
        <div class="menu" style="display: flex; align-items: center;">
            <a href="index_admin.php">Home</a>
            <a href="warga.php" class="active" style="margin-left: 20px;">Data Warga</a>
            <a href="admin_surat.php" style="margin-left: 20px;">Urus Dokumen</a>
            <a href="admin_iuran.php" style="margin-left: 20px;">Kelola Iuran</a>
            <span style="color: #10b981; font-weight: 600; margin-left: 15px;">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span>

            <!-- Tombol Logout Bergradasi Biru -->
            <a href="logout.php" class="btn-logout">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </a>
        </div>
    </div>
</div>

<div class="container" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 20px;">
            <div>
                <div class="badge">Hak Akses Level: Tertinggi</div>
                <h1>Daftar Kependudukan Warga</h1>
                <p class="description" style="margin-bottom: 0 !important;">Kelola data kependudukan dan profil warga Rusun RT 008.</p>
            </div>
            <div>
                <button class="btn-action" onclick="openAddModal()">+ Tambah Warga</button>
            </div>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Form Pencarian Warga -->
        <form method="GET" action="" class="search-container">
            <input type="text" name="search" class="search-input" placeholder="Cari berdasarkan NIK, Nama, Alamat, atau Pekerjaan..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn-action" style="padding: 10px 20px;">Cari</button>
            <?php if (!empty($search)): ?>
                <a href="warga.php" class="btn-action btn-secondary" style="padding: 10px 15px; text-decoration: none;">Reset</a>
            <?php endif; ?>
        </form>

        <div class="table-wrapper">
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>Pekerjaan</th>
                            <th>Alamat / Blok</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <?php 
                                $nik_secure = htmlspecialchars($row['nik']);
                                $nama_tampil = htmlspecialchars($row['nama']);
                                $jk = htmlspecialchars($row['jenis_kelamin'] ? $row['jenis_kelamin'] : '-');
                                $pekerjaan = htmlspecialchars($row['pekerjaan'] ? $row['pekerjaan'] : '-');
                                $alamat = htmlspecialchars($row['alamat'] ? $row['alamat'] : '-');
                                
                                $no_kk = htmlspecialchars($row['no_kk']);
                                $tempat_lahir = htmlspecialchars($row['tempat_lahir']);
                                $tanggal_lahir = htmlspecialchars($row['tanggal_lahir']);
                                $agama = htmlspecialchars($row['agama']);
                                $status_tinggal = htmlspecialchars($row['status_tinggal']);
                            ?>
                                <tr>
                                    <td><strong style="color: #10b981;"><?php echo $nik_secure; ?></strong></td>
                                    <td><strong><?php echo $nama_tampil; ?></strong></td>
                                    <td><?php echo $jk; ?></td>
                                    <td><?php echo $pekerjaan; ?></td>
                                    <td><span class='unit-badge'><?php echo $alamat; ?></span></td>
                                    <td style="text-align: center;">
                                        <button class="btn-detail" onclick="openProfileModal(
                                            '<?php echo addslashes($row['nik']); ?>',
                                            '<?php echo addslashes($no_kk); ?>',
                                            '<?php echo addslashes($nama_tampil); ?>',
                                            '<?php echo addslashes($tempat_lahir); ?>',
                                            '<?php echo addslashes($tanggal_lahir); ?>',
                                            '<?php echo addslashes($jk); ?>',
                                            '<?php echo addslashes($agama); ?>',
                                            '<?php echo addslashes($pekerjaan); ?>',
                                            '<?php echo addslashes($alamat); ?>',
                                            '<?php echo addslashes($status_tinggal); ?>'
                                        )">Lihat Profil</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #71717a; padding: 30px;">Tidak ada data warga ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: LIHAT PROFIL & TOMBOL EDIT/HAPUS  -->
<!-- ========================================== -->
<div class="modal-overlay" id="profileModal">
    <div class="modal-card">
        <button class="modal-close-btn" onclick="closeProfileModal()">×</button>
        <div class="modal-header">Kartu Identitas Digital</div>
        <div class="modal-body" id="modalDataContent"></div>
        <div class="modal-footer" id="modalFooter"></div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: FORM TAMBAH WARGA BARU            -->
<!-- ========================================== -->
<div class="modal-overlay" id="addModal">
    <div class="modal-card">
        <button class="modal-close-btn" onclick="closeAddModal()">×</button>
        <div class="modal-header">Tambah Data Warga Baru</div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="tambah_warga">
            
            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control" name="nik" placeholder="Masukkan 16 digit NIK" required>
            </div>
            <div class="form-group">
                <label>No. KK</label>
                <input type="text" class="form-control" name="no_kk" placeholder="Masukkan 16 digit No. KK" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" class="form-control" name="nama" placeholder="Nama lengkap warga" required>
            </div>
            <div class="form-group" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Tempat Lahir</label>
                    <input type="text" class="form-control" name="tempat_lahir" placeholder="Kota kelahiran">
                </div>
                <div style="flex: 1;">
                    <label>Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tanggal_lahir">
                </div>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select class="form-control" name="jenis_kelamin">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Agama</label>
                <input type="text" class="form-control" name="agama" placeholder="Contoh: Islam, Kristen, dll">
            </div>
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" class="form-control" name="pekerjaan" placeholder="Pekerjaan saat ini">
            </div>
            <div class="form-group">
                <label>Alamat / Blok</label>
                <input type="text" class="form-control" name="alamat" placeholder="Contoh: Tower A-05">
            </div>
            <div class="form-group">
                <label>Status Tinggal</label>
                <input type="text" class="form-control" name="status_tinggal" placeholder="Contoh: Tetap / Kontrak">
            </div>

            <div class="modal-footer" style="margin-top: 25px;">
                <button type="button" class="btn-action btn-secondary" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn-action">Simpan Warga Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: FORM EDIT WARGA                   -->
<!-- ========================================== -->
<div class="modal-overlay" id="editModal">
    <div class="modal-card">
        <button class="modal-close-btn" onclick="closeEditModal()">×</button>
        <div class="modal-header">Edit Data Warga</div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit_warga">
            <input type="hidden" name="nik_lama" id="edit_nik_lama">
            
            <div class="form-group">
                <label>NIK</label>
                <input type="text" class="form-control" name="nik" id="edit_nik" required>
            </div>
            <div class="form-group">
                <label>No. KK</label>
                <input type="text" class="form-control" name="no_kk" id="edit_no_kk" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" class="form-control" name="nama" id="edit_nama" required>
            </div>
            <div class="form-group" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Tempat Lahir</label>
                    <input type="text" class="form-control" name="tempat_lahir" id="edit_tempat_lahir">
                </div>
                <div style="flex: 1;">
                    <label>Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tanggal_lahir" id="edit_tanggal_lahir">
                </div>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select class="form-control" name="jenis_kelamin" id="edit_jenis_kelamin">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Agama</label>
                <input type="text" class="form-control" name="agama" id="edit_agama">
            </div>
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" class="form-control" name="pekerjaan" id="edit_pekerjaan">
            </div>
            <div class="form-group">
                <label>Alamat / Blok</label>
                <input type="text" class="form-control" name="alamat" id="edit_alamat">
            </div>
            <div class="form-group">
                <label>Status Tinggal</label>
                <input type="text" class="form-control" name="status_tinggal" id="edit_status_tinggal">
            </div>

            <div class="modal-footer" style="margin-top: 25px;">
                <button type="button" class="btn-action btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-action">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- FORM HIDDEN UNTUK HAPUS DATA               -->
<!-- ========================================== -->
<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="hapus_warga">
    <input type="hidden" name="nik" id="delete_nik">
</form>

<script>
    let currentData = {};

    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    function openProfileModal(nik, no_kk, nama, tempatLahir, tglLahir, jk, agama, pekerjaan, alamat, statusTinggal) {
        currentData = { nik, no_kk, nama, tempatLahir, tglLahir, jk, agama, pekerjaan, alamat, statusTinggal };
        
        const modal = document.getElementById('profileModal');
        const container = document.getElementById('modalDataContent');
        const footer = document.getElementById('modalFooter');
        
        container.innerHTML = `
            <p>Nama Lengkap: <br><strong style="font-size: 16px; color:#0284c7;">${nama}</strong></p>
            <hr style="border:0; border-top:1px solid #232326; margin:10px 0;">
            <p>NIK: <br><strong>${nik}</strong></p>
            <p>No. KK: <br><strong>${no_kk}</strong></p>
            <p>Tempat, Tanggal Lahir: <br><strong>${tempatLahir}, ${tglLahir}</strong></p>
            <p>Jenis Kelamin: <br><strong>${jk}</strong></p>
            <p>Agama: <br><strong>${agama}</strong></p>
            <p>Pekerjaan: <br><strong>${pekerjaan}</strong></p>
            <p>Alamat / Blok: <br><strong style="color: #10b981;">${alamat}</strong></p>
            <p>Status Tinggal: <br><strong>${statusTinggal}</strong></p>
        `;
        
        let footerHtml = `<button class="btn-action btn-danger" style="padding: 8px 16px; font-size: 12px;" onclick="confirmDelete('${nik}', '${nama}')">Hapus</button>`;
        footerHtml += `<button class="btn-action" style="padding: 8px 16px; font-size: 12px;" onclick="openEditModalFromProfile()">Edit Warga</button>`;
        footerHtml += `<button class="btn-action btn-secondary" style="padding: 8px 16px; font-size: 12px;" onclick="closeProfileModal()">Tutup</button>`;
        
        footer.innerHTML = footerHtml;
        modal.style.display = 'flex';
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    function openEditModalFromProfile() {
        closeProfileModal();
        
        document.getElementById('edit_nik_lama').value = currentData.nik;
        document.getElementById('edit_nik').value = currentData.nik;
        document.getElementById('edit_no_kk').value = currentData.no_kk;
        document.getElementById('edit_nama').value = currentData.nama;
        document.getElementById('edit_tempat_lahir').value = currentData.tempatLahir;
        document.getElementById('edit_tanggal_lahir').value = currentData.tglLahir;
        document.getElementById('edit_jenis_kelamin').value = currentData.jk;
        document.getElementById('edit_agama').value = currentData.agama;
        document.getElementById('edit_pekerjaan').value = currentData.pekerjaan;
        document.getElementById('edit_alamat').value = currentData.alamat;
        document.getElementById('edit_status_tinggal').value = currentData.statusTinggal;
        
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function confirmDelete(nik, nama) {
        if (confirm(`Apakah Anda yakin ingin menghapus data warga atas nama "${nama}" (NIK: ${nik})?`)) {
            document.getElementById('delete_nik').value = nik;
            document.getElementById('deleteForm').submit();
        }
    }

    window.onclick = function(event) {
        const addModal = document.getElementById('addModal');
        const profileModal = document.getElementById('profileModal');
        const editModal = document.getElementById('editModal');
        
        if (event.target === addModal) closeAddModal();
        if (event.target === profileModal) closeProfileModal();
        if (event.target === editModal) closeEditModal();
    }
</script>
</body>
</html>