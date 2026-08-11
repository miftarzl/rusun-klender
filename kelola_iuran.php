<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Admin') {
    die("Akses ditolak.");
}

$message = '';

// KODE BARU: Proses Buat Tagihan Massal untuk Semua Warga
if (isset($_POST['generate_tagihan'])) {
    $bulan_tahun = $_POST['bulan_tahun']; // Contoh: "September 2026"
    $jenis_iuran = $_POST['jenis_iuran'];
    $jumlah_bayar = $_POST['jumlah_bayar'];

    // 1. Ambil semua ID warga yang terdaftar di tabel warga
    $query_warga = "SELECT id FROM warga";
    $result_warga = $conn->query($query_warga);

    if ($result_warga->num_rows > 0) {
        $sukses = 0;
        // 2. Loop dan insert tagihan baru untuk masing-masing warga
        while ($warga = $result_warga->fetch_assoc()) {
            $id_warga = $warga['id'];
            
            // Cek dulu apakah warga ini sudah dibuatkan tagihan untuk periode & jenis yang sama (biar gak double)
            $cek = $conn->query("SELECT id_iuran FROM iuran WHERE id_warga = $id_warga AND bulan_tahun = '$bulan_tahun' AND jenis_iuran = '$jenis_iuran'");
            if ($cek->num_rows == 0) {
                $ins = $conn->query("INSERT INTO iuran (id_warga, bulan_tahun, jumlah_bayar, status_bayar, jenis_iuran) 
                                     VALUES ($id_warga, '$bulan_tahun', $jumlah_bayar, 'Belum Bayar', '$jenis_iuran')");
                if ($ins) $sukses++;
            }
        }
        $message = "<div style='color: green; font-weight: bold; margin-bottom: 15px;'>Berhasil membuat $sukses tagihan baru untuk seluruh warga periode $bulan_tahun!</div>";
    } else {
        $message = "<div style='color: red; margin-bottom: 15px;'>Gagal! Belum ada data warga terdaftar di database.</div>";
    }
}

// Proses Verifikasi Lunas oleh Admin
if (isset($_GET['approve_id'])) {
    $id_iuran = $_GET['approve_id'];
    $query = "UPDATE iuran SET status_bayar = 'Lunas', tanggal_bayar = NOW() WHERE id_iuran = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_iuran);
    if ($stmt->execute()) {
        $message = "<div style='color: green; font-weight: bold; margin-bottom: 15px;'>Pembayaran iuran berhasil diverifikasi!</div>";
    }
}

// Query Tampilkan Data untuk Tabel
$query = "SELECT i.*, w.kepala_keluarga, w.nik FROM iuran i 
          JOIN warga w ON i.id_warga = w.id 
          ORDER BY i.status_bayar ASC, i.id_iuran DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Iuran Rusun - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 30px; color: #333; }
        .box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px; }
        .form-inline { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; margin-top: 15px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        input, select, button { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        button { background: #007bff; color: white; cursor: pointer; border: none; font-weight: bold; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; }
        .btn-approve { background: #28a745; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; }
        .btn-approve:hover { background: #218838; }
        .img-bukti { width: 70px; height: auto; cursor: pointer; border: 1px solid #ccc; border-radius: 4px; }
        .img-bukti:hover { transform: scale(3.5); transition: 0.2s; position: relative; z-index: 999; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
    </style>
</head>
<body>

<!-- KODE BARU: Panel Khusus Admin Buat Rilis Tagihan Bulanan Baru -->
<div class="box">
    <h2>📢 Rilis Tagihan Iuran Bulanan Baru</h2>
    <p style="color: #666; font-size: 14px;">Fitur ini akan otomatis membuatkan surat tagihan "Belum Bayar" ke seluruh akun warga yang terdaftar secara serentak.</p>
    
    <form action="" method="POST" class="form-inline">
        <div class="form-group">
            <label>Periode (Bulan & Tahun):</label>
            <input type="text" name="bulan_tahun" placeholder="Contoh: Juli 2026" required>
        </div>
        <div class="form-group">
            <label>Jenis Iuran:</label>
            <select name="jenis_iuran" required>
                <option value="Kebersihan & Keamanan">Kebersihan & Keamanan</option>
                <option value="Kas RT">Kas RT</option>
                <option value="Sosial/Kematian">Sosial/Kematian</option>
            </select>
        </div>
        <div class="form-group">
            <label>Nominal Tarif (Rp):</label>
            <input type="number" name="jumlah_bayar" placeholder="Contoh: 50000" required>
        </div>
        <button type="submit" name="generate_tagihan">⚡ Rilis Tagihan Massal</button>
    </form>
</div>

<div class="box">
    <h2>📋 Halaman Verifikasi Iuran Warga (Admin RT)</h2>
    
    <?php echo $message; ?>

    <table>
        <thead>
            <tr>
                <th>Nama Warga (NIK)</th>
                <th>Periode</th>
                <th>Jenis</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Bukti Transfer</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['kepala_keluarga']); ?></strong><br><small style="color: #666;"><?php echo $row['nik']; ?></small></td>
                    <td><?php echo htmlspecialchars($row['bulan_tahun']); ?></td>
                    <td><?php echo htmlspecialchars($row['jenis_iuran']); ?></td>
                    <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                    <td>
                        <span style="font-weight: bold; color: <?php echo ($row['status_bayar'] == 'Lunas') ? 'green' : (($row['status_bayar'] == 'Menunggu Verifikasi') ? 'orange' : 'red'); ?>">
                            <?php echo $row['status_bayar']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if(!empty($row['bukti_bayar'])): ?>
                            <img src="uploads/bukti_iuran/<?php echo $row['bukti_bayar']; ?>" class="img-bukti" title="Arahkan kursor untuk zoom gambar">
                        <?php else: ?>
                            <small style="color: gray; font-style: italic;">Belum upload</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($row['status_bayar'] == 'Menunggu Verifikasi'): ?>
                            <a href="?approve_id=<?php echo $row['id_iuran']; ?>" class="btn-approve" onclick="return confirm('Apakah bukti transfer warga ini sudah sesuai?')">✓ Setujui</a>
                        <?php elseif($row['status_bayar'] == 'Lunas'): ?>
                            <span style="color: green; font-weight: bold;">✓ Terverifikasi</span>
                        <?php else: ?>
                            <span style="color: #d9534f; font-style: italic;">Menunggu Pembayaran</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #999; font-style: italic;">Belum ada riwayat tagihan iuran yang diterbitkan. Silakan gunakan panel di atas untuk membuat tagihan pertama.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>