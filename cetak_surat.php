<?php
session_start();
include 'koneksi.php';

// Proteksi: Wajib login sebagai Warga atau Admin
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['Warga', 'Admin'])) {
    die("Akses ditolak. Anda tidak memiliki otoritas.");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID Surat tidak ditemukan.");
}

$id_surat = $_GET['id'];

// Jika login sebagai Warga, batasi hanya surat miliknya sendiri. Jika Admin, bisa akses semua.
if ($_SESSION['role'] === 'Warga') {
    $id_warga = $_SESSION['id_warga'];
    $query = "SELECT s.*, w.nama, w.nik FROM surat_pengantar s 
              JOIN warga w ON s.id_warga = w.id 
              WHERE s.id_surat = ? AND s.id_warga = ? AND s.status_pengajuan = 'Disetujui'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id_surat, $id_warga);
} else {
    $query = "SELECT s.*, w.nama, w.nik FROM surat_pengantar s 
              JOIN warga w ON s.id_warga = w.id 
              WHERE s.id_surat = ? AND s.status_pengajuan = 'Disetujui'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_surat);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Dokumen tidak ditemukan atau belum disetujui oleh RT.");
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak <?php echo htmlspecialchars($data['jenis_surat']); ?></title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 40px;
            font-size: 16px;
            line-height: 1.6;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .kop-surat h2 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .kop-surat p { margin: 5px 0 0 0; font-size: 14px; }
        
        .nomor-surat {
            text-align: center;
            margin-bottom: 30px;
        }
        .nomor-surat h3 { margin: 0; text-transform: uppercase; text-decoration: underline; font-size: 18px; }
        .nomor-surat p { margin: 5px 0 0 0; }

        .isi-surat {
            text-align: justify;
            margin-bottom: 30px;
        }
        .tabel-biodata {
            margin: 20px 0 20px 40px;
            width: 80%;
            border-collapse: collapse;
        }
        .tabel-biodata td {
            padding: 4px 0;
            vertical-align: top;
        }
        
        .ttd-container {
            margin-top: 50px;
            float: right;
            text-align: center;
            width: 250px;
        }
        .ttd-space {
            height: 80px;
        }

        @media print {
            body { padding: 20px; }
            @page { size: A4; margin: 1.5cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI RT -->
    <div class="kop-surat">
        <h2>PENGURUS RUKUN TETANGGA 008 / RW 001</h2>
        <h2>RUMAH SUSUN KLENDER - KELURAHAN DUREN SAWIT</h2>
        <p>Sekretariat: Blok 6 Lantai Dasar, Duren Sawit, Jakarta Timur, 13440</p>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="nomor-surat">
        <h3>SURAT PENGANTAR <?php echo strtoupper(htmlspecialchars($data['jenis_surat'])); ?></h3>
        <p>Nomor: <?php echo $data['id_surat']; ?>/SP/RT008/RW001/<?php echo date('m/Y', strtotime($data['tanggal_ajuan'])); ?></p>
    </div>

    <!-- ISI DOKUMEN -->
    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini, Pengurus RT 008 / RW 001 Kelurahan Duren Sawit, Kecamatan Duren Sawit, Kota Jakarta Timur, dengan ini menerangkan bahwa warga yang bersangkutan di bawah ini:</p>
        
        <table class="tabel-biodata">
            <tr>
                <td style="width: 35%;">Nama Lengkap</td>
                <td style="width: 3%;">:</td>
                <td><strong><?php echo htmlspecialchars($data['nama']); ?></strong></td>
            </tr>
            <tr>
                <td>Nomor Induk Kependudukan (NIK)</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($data['nik']); ?></td>
            </tr>
            <tr>
                <td>Kategori Pengajuan</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($data['jenis_surat']); ?></td>
            </tr>
        </table>

        <p>Berdasarkan catatan administrasi kami, data tersebut di atas benar merupakan warga resmi yang berdomisili di lingkungan RT 008 / RW 001 Rumah Susun Klender. Surat pengantar ini dibuat secara sah guna memenuhi keperluan/alasan: <strong><em><?php echo htmlspecialchars($data['keperluan']); ?></em></strong>.</p>
        
        <p>Demikian surat pengantar ini diberikan kepada yang bersangkutan untuk dapat dipergunakan sebagaimana mestinya dan dengan penuh tanggung jawab.</p>
    </div>

    <!-- TANDA TANGAN KETUA RT -->
    <div class="ttd-container">
        <p>Jakarta, <?php echo date('d F Y', strtotime($data['tanggal_ajuan'])); ?></p>
        <p>Ketua RT 008 / RW 001</p>
        <div class="ttd-space"></div>
        <p><strong>( _______________________ )</strong></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>
</html>