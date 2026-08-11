<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Amankan input dari pengguna
    $nama  = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pesan_asli = mysqli_real_escape_string($conn, trim($_POST['pesan']));

    // Menggabungkan informasi email ke dalam kolom pesan agar admin dapat melihat email pengirim
    $pesan_lengkap = "Email: " . $email . "\n\nPesan:\n" . $pesan_asli;

    // Query untuk menyimpan data ke tabel database 'saran'
    $query = "INSERT INTO saran (nama, pesan, tanggal) VALUES ('$nama', '$pesan_lengkap', NOW())";

    if ($conn->query($query) === TRUE) {
        echo "<script>
                alert('Terima kasih! Pesan Anda telah berhasil dikirim ke pengurus.');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengirim pesan: " . $conn->error . "');
                window.location.href = 'index.php';
              </script>";
    }
} else {
    header("Location: index.php");
    exit;
}
?>