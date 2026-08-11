<?php
session_start();
include 'koneksi.php';

// Proteksi Keamanan
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

// Proses persetujuan/penolakan dokumen
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_surat = intval($_GET['id']);
    $status_baru = ($_GET['aksi'] == 'setuju') ? 'Disetujui' : 'Ditolak';
    
    $stmt = $conn->prepare("UPDATE surat_pengantar SET status_pengajuan = ? WHERE id_surat = ?");
    $stmt->bind_param("si", $status_baru, $id_surat);
    if ($stmt->execute()) {
        header("Location: admin_surat.php?status=sukses");
        exit;
    }
}

// Filter Jenis Surat
$filter_jenis = isset($_GET['jenis_surat']) ? $_GET['jenis_surat'] : 'Semua';

$query_surat = "SELECT s.*, w.nama, w.nik, w.alamat 
                FROM surat_pengantar s 
                JOIN warga w ON s.id_warga = w.id";

if ($filter_jenis !== 'Semua') {
    $query_surat .= " WHERE s.jenis_surat = '" . $conn->real_escape_string($filter_jenis) . "'";
}
$query_surat .= " ORDER BY s.tanggal_ajuan DESC";
$result_surat = $conn->query($query_surat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID-RUSUN | Manajemen Dokumen Warga</title>
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
            padding: 10px 14px;
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

        .badge-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            display: inline-block;
        }
        .pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .disetujui { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .ditolak { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
            display: inline-block;
            transition: 0.2s;
        }
        .btn-action:hover { opacity: 0.85; }

        .unit-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-menu-row" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; border-bottom: 1px solid #232326;">
        <div class="brand" style="font-weight: 700; font-size: 20px; text-transform: uppercase;">Rusun-AI [ADMIN]</div>
        <div class="menu" style="display: flex; align-items: center;">
            <a href="index_admin.php">Home</a>
            <a href="warga.php" style="margin-left: 20px;">Data Warga</a>
            <a href="admin_surat.php" class="active" style="margin-left: 20px;">Urus Dokumen</a>
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
        <div class="badge">Panel Kontrol</div>
        <h1>Manajemen Pengurusan Dokumen</h1>
        <p class="description">
            Proses peninjauan surat pengantar, domisili, atau surat kematian warga secara digital.
        </p>

        <div class="filter-box">
            <form method="GET" action="" style="display: flex; gap: 15px; align-items: center; width: 100%;">
                <label>Kategori Dokumen:</label>
                <select name="jenis_surat" class="select-custom" onchange="this.form.submit()">
                    <option value="Semua" <?php if($filter_jenis == 'Semua') echo 'selected'; ?>>-- Tampilkan Semua --</option>
                    <option value="Surat Pengantar" <?php if($filter_jenis == 'Surat Pengantar') echo 'selected'; ?>>Surat Pengantar</option>
                    <option value="Domisili" <?php if($filter_jenis == 'Domisili') echo 'selected'; ?>>Domisili</option>
                    <option value="Surat Kematian" <?php if($filter_jenis == 'Surat Kematian') echo 'selected'; ?>>Surat Kematian</option>
                </select>
            </form>
        </div>

        <div class="table-wrapper">
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tanggal Ajuan</th>
                            <th>Pemohon (Warga)</th>
                            <th>Unit</th>
                            <th>Kategori Dokumen</th>
                            <th>Keperluan / Keterangan</th>
                            <th>Status</th>
                            <th style="text-align: center;">Tindakan Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_surat->num_rows == 0): ?>
                            <tr><td colspan="7" style="text-align:center; color:#71717a; padding: 30px;">Tidak ada permohonan dokumen dalam kategori ini.</td></tr>
                        <?php else: ?>
                            <?php while($row = $result_surat->fetch_assoc()): ?>
                                <tr>
                                    <td style="color:#a1a1aa; font-size:13px;"><?php echo date('d-m-Y H:i', strtotime($row['tanggal_ajuan'])); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong><br><span style="font-size:11px; color:#a1a1aa;">NIK: <?php echo $row['nik']; ?></span></td>
                                    <td><span class='unit-badge'><?php echo htmlspecialchars($row['alamat']); ?></span></td>
                                    <td style="color: #fff; font-weight:600;"><?php echo htmlspecialchars($row['jenis_surat']); ?></td>
                                    <td><?php echo htmlspecialchars($row['keperluan']); ?></td>
                                    <td>
                                        <span class="badge-status <?php echo ($row['status_pengajuan'] == 'Pending') ? 'pending' : (($row['status_pengajuan'] == 'Disetujui') ? 'disetujui' : 'ditolak'); ?>">
                                            <?php echo $row['status_pengajuan']; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if($row['status_pengajuan'] == 'Pending'): ?>
                                            <a href="admin_surat.php?aksi=setuju&id=<?php echo $row['id_surat']; ?>" class="btn-action" style="background-color: #10b981;">Setujui</a>
                                            <a href="admin_surat.php?aksi=tolak&id=<?php echo $row['id_surat']; ?>" class="btn-action" style="background-color: #ef4444;">Tolak</a>
                                        <?php else: ?>
                                            <span style="color: #71717a; font-size: 12px; font-weight: 600;">Selesai Diproses</span>
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

</body>
</html>