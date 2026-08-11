<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

$bulan_aktif = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');
// Ambil pilihan jenis iuran dari parameter GET, default ke 'Iuran Perbulan'
$jenis_aktif = isset($_GET['jenis']) ? $_GET['jenis'] : 'Iuran Perbulan';

// Tentukan tarif dasar berdasarkan jenis iuran
if ($jenis_aktif == 'Uang Duka') {
    $tarif_default = 20000; 
} else {
    $tarif_default = 10000;
}

// Proses Tambah / Update Jenis Iuran Baru (Custom oleh Admin)
if (isset($_POST['tambah_jenis_iuran'])) {
    $nama_iuran_baru = trim($_POST['nama_iuran_baru']);
    $nominal_baru = floatval($_POST['nominal_baru']);
    
    if (!empty($nama_iuran_baru) && $nominal_baru > 0) {
        // Alihkan halaman ke jenis iuran baru yang ditambahkan agar langsung aktif
        header("Location: admin_iuran.php?bulan=$bulan_aktif&jenis=" . urlencode($nama_iuran_baru) . "&nominal=$nominal_baru&status=sukses_tambah");
        exit;
    }
}

// Proses Hapus Jenis Iuran (Menghapus seluruh record iuran dengan jenis dan bulan tersebut)
if (isset($_POST['hapus_jenis_iuran'])) {
    $jenis_dihapus = $_POST['jenis_iuran_dihapus'];
    
    // Pastikan tidak menghapus kategori default jika diinginkan, atau izinkan semua
    $stmt_hapus = $conn->prepare("DELETE FROM iuran WHERE jenis_iuran = ? AND bulan_tahun = ?");
    $stmt_hapus->bind_param("ss", $jenis_dihapus, $bulan_aktif);
    
    if ($stmt_hapus->execute()) {
        header("Location: admin_iuran.php?bulan=$bulan_aktif&jenis=Iuran Perbulan&status=sukses_hapus");
        exit;
    }
}

// Cek apakah jenis iuran aktif saat ini memiliki tarif khusus atau pakai default
if (isset($_GET['jenis']) && $_GET['jenis'] !== 'Iuran Perbulan' && $_GET['jenis'] !== 'Uang Duka') {
    $jenis_aktif = $_GET['jenis'];
    $tarif = isset($_GET['nominal']) ? floatval($_GET['nominal']) : $tarif_default;
} else {
    if ($jenis_aktif == 'Uang Duka') {
        $tarif = 20000;
    } else {
        $tarif = 10000;
    }
}

// Proses Update Pembayaran
if (isset($_POST['update_iuran'])) {
    $id_warga = intval($_POST['id_warga']);
    $status = $_POST['status_bayar'];
    $nominal_bayar = isset($_POST['jumlah_bayar']) ? floatval($_POST['jumlah_bayar']) : $tarif;
    $tgl_bayar = ($status == 'Lunas') ? date('Y-m-d H:i:s') : null;

    $cek = $conn->prepare("SELECT id_iuran FROM iuran WHERE id_warga = ? AND bulan_tahun = ? AND jenis_iuran = ?");
    $cek->bind_param("iss", $id_warga, $bulan_aktif, $jenis_aktif);
    $cek->execute();
    $res_cek = $cek->get_result();

    if ($res_cek->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE iuran SET status_bayar = ?, jumlah_bayar = ?, tanggal_bayar = ? WHERE id_warga = ? AND bulan_tahun = ? AND jenis_iuran = ?");
        $stmt->bind_param("sdsiss", $status, $nominal_bayar, $tgl_bayar, $id_warga, $bulan_aktif, $jenis_aktif);
    } else {
        $stmt = $conn->prepare("INSERT INTO iuran (id_warga, bulan_tahun, jenis_iuran, jumlah_bayar, status_bayar, tanggal_bayar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $id_warga, $bulan_aktif, $jenis_aktif, $nominal_bayar, $status, $tgl_bayar);
    }

    if ($stmt->execute()) {
        header("Location: admin_iuran.php?bulan=$bulan_aktif&jenis=" . urlencode($jenis_aktif) . "&nominal=$tarif&status=sukses");
        exit;
    }
}

// Ambil data warga + status iurannya berdasarkan jenis iuran aktif
$query_warga = "SELECT w.id, w.nik, w.nama, w.alamat, i.status_bayar, i.tanggal_bayar, i.jumlah_bayar 
                FROM warga w 
                LEFT JOIN iuran i ON w.id = i.id_warga AND i.bulan_tahun = ? AND i.jenis_iuran = ?
                ORDER BY w.alamat ASC";
$stmt_warga = $conn->prepare($query_warga);
$stmt_warga->bind_param("ss", $bulan_aktif, $jenis_aktif);
$stmt_warga->execute();
$result_warga = $stmt_warga->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Pos Pembayaran Keuangan</title>
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

        /* STYLING TOMBOL LOGOUT BIRU */
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

        .filter-box {
            background: #141416;
            border: 1px solid #232326;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .filter-box label {
            font-size: 13px;
            font-weight: 600;
            color: #a1a1aa;
            text-transform: uppercase;
        }

        .select-custom {
            background: #1a1a1e;
            color: #e4e4e7;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #333338;
            font-size: 14px;
            outline: none;
            cursor: pointer;
        }
        .select-custom:focus { border-color: #0284c7; }

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

        .btn-save {
            padding: 6px 14px;
            border: none;
            background: #0284c7;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: opacity 0.2s;
        }
        .btn-save:hover { opacity: 0.85; }

        .btn-tambah-iuran {
            background: #10b981;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-tambah-iuran:hover { opacity: 0.85; }

        .btn-hapus-iuran {
            background: #ef4444;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-hapus-iuran:hover { opacity: 0.85; }

        .lunas { color: #10b981; font-weight: bold; }
        .belum { color: #ef4444; font-weight: bold; }

        .unit-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* Modal styling sederhana */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #141416;
            border: 1px solid #232326;
            padding: 25px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; border-bottom: 1px solid #232326;">
        <div class="brand" style="font-weight: 700; font-size: 20px; text-transform: uppercase; color: #0284c7;">Rusun-AI [ADMIN]</div>
        <div class="menu" style="display: flex; align-items: center;">
            <a href="index_admin.php">Home</a>
            <a href="warga.php" style="margin-left: 20px;">Data Warga</a>
            <a href="admin_surat.php" style="margin-left: 20px;">Urus Dokumen</a>
            <a href="admin_iuran.php" class="active" style="margin-left: 20px;">Kelola Iuran</a>
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
        <div class="badge">Keuangan & Kas</div>
        <h1>Manajemen Kas & Iuran Warga</h1>
        <p class="description">
            Rekapitulasi, verifikasi pembayaran, serta penambahan dan penghapusan jenis iuran warga Rusun RT 008 secara digital.
        </p>

        <div class="filter-box">
            <form method="GET" action="" style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap; flex: 1;">
                <div>
                    <label style="margin-right: 8px;">Periode Bulan:</label>
                    <input type="month" name="bulan" value="<?php echo $bulan_aktif; ?>" class="select-custom">
                </div>
                <div>
                    <label style="margin-right: 8px;">Jenis Iuran:</label>
                    <select name="jenis" class="select-custom">
                        <option value="Iuran Perbulan" <?php if($jenis_aktif == 'Iuran Perbulan') echo 'selected'; ?>>Iuran Perbulan (Rp 10.000)</option>
                        <option value="Uang Duka" <?php if($jenis_aktif == 'Uang Duka') echo 'selected'; ?>>Uang Duka (Rp 20.000)</option>
                        <?php if($jenis_aktif !== 'Iuran Perbulan' && $jenis_aktif !== 'Uang Duka'): ?>
                            <option value="<?php echo htmlspecialchars($jenis_aktif); ?>" selected><?php echo htmlspecialchars($jenis_aktif); ?> (Rp <?php echo number_format($tarif, 0, ',', '.'); ?>)</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit" class="btn-save" style="background:#222; border:1px solid #333; padding: 8px 16px;">Muat Data</button>
            </form>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="button" onclick="document.getElementById('modalTambahIuran').style.display='flex'" class="btn-tambah-iuran">+ Tambah Jenis Iuran</button>
                <?php if($jenis_aktif !== 'Iuran Perbulan' && $jenis_aktif !== 'Uang Duka'): ?>
                    <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh data untuk kategori iuran <?php echo htmlspecialchars($jenis_aktif); ?> pada bulan ini?');" style="display:inline;">
                        <input type="hidden" name="jenis_iuran_dihapus" value="<?php echo htmlspecialchars($jenis_aktif); ?>">
                        <button type="submit" name="hapus_jenis_iuran" class="btn-hapus-iuran">Hapus Kategori Ini</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-wrapper">
            <h3 style="margin-top:0; margin-bottom: 20px; color:#fff; font-size: 16px;">
                Daftar Tagihan: <span style="color:#0284c7;"><?php echo htmlspecialchars($jenis_aktif); ?></span> (<?php echo date('F Y', strtotime($bulan_aktif)); ?>)
            </h3>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Nominal Tagihan</th>
                            <th>Status</th>
                            <th>Tanggal Setor</th>
                            <th style="text-align: center;">Aksi Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_warga->fetch_assoc()): 
                            $status_sekarang = $row['status_bayar'] ?? 'Belum Bayar';
                            $nominal_tercatat = $row['jumlah_bayar'] ? $row['jumlah_bayar'] : $tarif;
                        ?>
                            <tr>
                                <td><span class="unit-badge">Unit <?php echo htmlspecialchars($row['alamat']); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                                <td style="color:#a1a1aa; font-size: 13px;"><?php echo htmlspecialchars($row['nik']); ?></td>
                                <td style="color: #fff; font-weight: 600;">Rp <?php echo number_format($nominal_tercatat, 0, ',', '.'); ?></td>
                                <td><span class="<?php echo ($status_sekarang == 'Lunas') ? 'lunas' : 'belum'; ?>"><?php echo $status_sekarang; ?></span></td>
                                <td style="color:#a1a1aa; font-size: 13px;"><?php echo $row['tanggal_bayar'] ? date('d-m-Y H:i', strtotime($row['tanggal_bayar'])) : '-'; ?></td>
                                <td style="text-align: center;">
                                    <form method="POST" action="" style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                        <input type="hidden" name="id_warga" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="jumlah_bayar" value="<?php echo $tarif; ?>">
                                        <select name="status_bayar" class="select-custom" style="padding: 6px;">
                                            <option value="Belum Bayar" <?php if($status_sekarang == 'Belum Bayar') echo 'selected'; ?>>Belum Bayar</option>
                                            <option value="Lunas" <?php if($status_sekarang == 'Lunas') echo 'selected'; ?>>Lunas</option>
                                        </select>
                                        <button type="submit" name="update_iuran" class="btn-save">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jenis Iuran Baru -->
<div id="modalTambahIuran" class="modal">
    <div class="modal-content">
        <h3 style="margin-top: 0; color: #fff; margin-bottom: 15px;">Tambah Jenis Iuran Baru</h3>
        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #a1a1aa; margin-bottom: 5px; text-transform: uppercase;">Nama Iuran / Kategori:</label>
                <input type="text" name="nama_iuran_baru" placeholder="Contoh: Iuran Kebersihan / Perayaan" required class="select-custom" style="width: 100%; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #a1a1aa; margin-bottom: 5px; text-transform: uppercase;">Nominal Tarif (Rp):</label>
                <input type="number" name="nominal_baru" placeholder="15000" required class="select-custom" style="width: 100%; box-sizing: border-box;">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('modalTambahIuran').style.display='none'" class="select-custom" style="background: #222; border: 1px solid #333;">Batal</button>
                <button type="submit" name="tambah_jenis_iuran" class="btn-save">Gunakan Jenis Ini</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>