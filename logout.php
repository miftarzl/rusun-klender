<?php
session_start();
session_unset();
session_destroy();
header("Location: warga.php"); // Lempar kembali ke halaman warga publik
exit;
?>