-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Agu 2026 pada 16.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rusun_klender`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun`
--

CREATE TABLE `akun` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('Admin','Warga') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akun`
--

INSERT INTO `akun` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin01', 'admin123', 'Admin'),
(2, 'warga01', 'warga123', 'Warga'),
(3, 'warga02', 'warga002', 'Warga');

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota_keluarga`
--

CREATE TABLE `anggota_keluarga` (
  `id` int(11) NOT NULL,
  `id_warga` int(11) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `nama_anggota` varchar(100) NOT NULL,
  `hubungan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban` text NOT NULL,
  `target_role` enum('publik','warga','semua') DEFAULT 'semua'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `faq`
--

INSERT INTO `faq` (`id`, `pertanyaan`, `jawaban`, `target_role`) VALUES
(1, 'apa syarat daftar tinggal di rusun klender', 'Syarat pendaftaran tinggal di Rusun Klender meliputi: 1. Memiliki KTP dan Kartu Keluarga (KK) DKI Jakarta, 2. Surat Nikah atau akta keluarga, 3. Surat keterangan belum memiliki rumah (SKBR) dari kelurahan setempat, 4. Pas foto, dan 5. Mengisi formulir pendaftaran di kantor pengurus RT 008 / RW 001.', 'publik'),
(5, 'bagaimana cara melapor kerusakan fasilitas', 'Warga penghuni dapat melaporkan kerusakan fasilitas dengan masuk (login) ke akun warga Anda, lalu mengakses menu layanan pengaduan atau pelaporan kerusakan lingkungan.', 'warga'),
(8, 'apakah harus sudah menikah untuk menyewa unit', 'Pendaftaran unit rusun diutamakan bagi keluarga yang sudah menikah dengan melampirkan buku nikah atau akta keluarga yang sah.', 'publik'),
(10, 'apakah biaya sewa sudah termasuk listrik dan air', 'Tidak, tarif sewa bulanan belum termasuk pemakaian utilitas pribadi seperti token listrik PLN dan air bersih yang dikelola secara mandiri oleh masing-masing penghuni.', 'publik'),
(11, 'apakah ada daftar tunggu jika unit penuh', 'Ya, apabila seluruh unit terisi, calon pendaftar dapat masuk ke dalam daftar tunggu (waiting list) dan akan dihubungi pengurus jika ada unit yang kosong.', 'publik'),
(12, 'jam operasional kantor pengurus rusun', 'Kantor sekretariat pengurus RT 008 / RW 001 melayani keperluan administrasi pada hari Senin sampai Jumat pukul 08.00 hingga 16.00 WIB.', 'publik'),
(14, 'apakah ada aturan jam malam bagi tamu', 'Demi keamanan dan kenyamanan bersama lingkungan rusun, tamu yang berkunjung melebihi pukul 22.00 WIB diwajibkan melapor kepada petugas pos keamanan (satpam).', 'publik'),
(15, 'bagaimana cara membayar iuran bulanan', 'Untuk melakukan pembayaran atau mengecek riwayat iuran, silakan akses menu \"Iuran\" yang tersedia di dashboard akun warga Anda.', 'warga'),
(16, 'bagaimana cara membuat surat domisili', 'Pembuatan surat domisili dapat dilakukan dengan masuk ke menu layanan surat di dashboard warga, lalu pilih opsi \"Surat Domisili\" dan lengkapi formulir pengajuannya.', 'warga'),
(17, 'bagaimana cara membuat surat pengantar', 'Untuk mengajukan surat pengantar RT, silakan buka menu layanan surat pada akun Anda, pilih \"Surat Pengantar\", lalu isi data keperluan surat tersebut.', 'warga'),
(18, 'bagaimana cara mengurus surat kematian', 'Pengajuan surat kematian dapat dilakukan melalui menu layanan surat di dashboard warga dengan memilih \"Surat Kematian\" dan mengisi data pendukung yang diminta.', 'warga'),
(19, 'apa saja fitur yang tersedia untuk warga', 'Saat ini fitur yang dapat diakses pada akun warga meliputi pengelolaan iuran bulanan serta pembuatan surat domisili, surat kematian, dan surat pengantar.', 'warga'),
(20, 'harga unit kalo ngontrak berapa', 'harga tergantung yang punya unit mau berapa', 'publik'),
(21, 'kalo harga buat beli unit berapa', 'itu juga sama, tergantung yang punya unit tersebut', 'publik'),
(22, 'fasilitas apa saja yang tersedia di rusun rt 08', 'ada berbagai fasilitas, seperti gudang motor dan lapangan, masjid juga dekat, ada parkir dan parkir mobil tidak jauh', 'publik'),
(23, 'apakah rusun rt 08 dan rw 001 tempatnya strategis', 'iya, tidak terlalu jauh dari fasilitas umum seperti rumah sakit dan yang lainnya', 'publik'),
(24, 'bisa ngontrak gk?', 'tentu saja bisa', 'publik'),
(25, 'bisa beli unitnya gk?', 'sangat bisa', 'publik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `informasi`
--

CREATE TABLE `informasi` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `konten` text NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `informasi`
--

INSERT INTO `informasi` (`id`, `judul`, `konten`, `tanggal_dibuat`) VALUES
(1, 'Kerja Bakti Bulanan', 'Diberitahukan kepada seluruh warga RT 008\r\nRW 001 bahwa kerja bakti akan dilaksanakan pada hari Minggu besok\r\npukul 07.00 WIB.', '2026-06-07 10:10:31'),
(2, 'Iuran Keamanan', 'Pembayaran iuran bulanan warga paling lambat\r\ntanggal 10 setiap bulannya melalui bendahara RT.', '2026-06-07 10:10:31'),
(3, 'Pengumuman', 'PENGUMUMAN: Mari Menyambut dan Meriahkan HUT Kemerdekaan RI ke-81.', '2026-07-02 19:31:26'),
(4, 'Pengumuman', 'PENGUMUMAN: Mari Menyambut dan Meriahkan HUT Kemerdekaan RI ke-81.', '2026-07-02 19:39:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `iuran`
--

CREATE TABLE `iuran` (
  `id_iuran` int(11) NOT NULL,
  `id_warga` int(11) NOT NULL,
  `bulan_tahun` varchar(50) NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `status_bayar` enum('Lunas','Belum Bayar') DEFAULT 'Belum Bayar',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `tanggal_bayar` datetime DEFAULT NULL,
  `jenis_iuran` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `iuran`
--

INSERT INTO `iuran` (`id_iuran`, `id_warga`, `bulan_tahun`, `jumlah_bayar`, `status_bayar`, `bukti_bayar`, `tanggal_bayar`, `jenis_iuran`) VALUES
(6, 2, '2026-07', 25000.00, 'Belum Bayar', NULL, NULL, 'Dana Sosial / Duka'),
(7, 1, '2026-07', 10000.00, 'Belum Bayar', NULL, NULL, 'Iuran Perbulan'),
(8, 2, '2026-08', 10000.00, 'Belum Bayar', NULL, NULL, 'Iuran Perbulan'),
(9, 2, '2026-08', 15000.00, 'Belum Bayar', NULL, NULL, 'iuran kebersihan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `username_admin` varchar(50) NOT NULL,
  `aksi` varchar(255) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_chat`
--

CREATE TABLE `riwayat_chat` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pesan_user` text NOT NULL,
  `balasan_bot` text NOT NULL,
  `waktu_kirim` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `riwayat_chat`
--

INSERT INTO `riwayat_chat` (`id`, `username`, `pesan_user`, `balasan_bot`, `waktu_kirim`) VALUES
(1, 'warga01', 'saya mau nanya soal uat kk', 'Untuk pengurusan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok.\n3. Hubungi Ketua RT 008 pada jam pelayanan (19.00 - 21.00 WIB).', '2026-06-07 12:12:03'),
(2, 'warga01', 'misi', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"misi\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-06-07 12:12:33'),
(3, 'warga01', 'lokasi unit siti dimana ya?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"lokasi unit siti\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-06-07 12:13:01'),
(4, 'warga01', 'lokasi unit siti dimana ya?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"lokasi unit siti\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-06-07 12:13:44'),
(5, 'warga01', 'lokasi unit siti dimana ya?', '[Hasil Analisis TF-IDF: Entitas Nama Ditemukan 🔍]\n\n• Nama Lengkap: Siti Aminah\n• Lokasi Kamar: Blok B - Lantai 1 - Unit 101\n• No. HP Aktif: 081298765432\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-06-07 12:14:28'),
(6, 'warga01', 'dimana alamat siti?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Lengkap: Siti Aminah\n• Lokasi Kamar: Blok B - Lantai 1 - Unit 101\n• No. HP Aktif: 081298765432\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-06-07 12:21:50'),
(7, 'warga01', 'dimana rumah kak Fitria?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Lengkap: Fitria Syahrani Yuliawan\n• Lokasi Kamar: Blok A - Lantai 1 - Unit 102\n• No. HP Aktif: 085881545025\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-06-07 15:08:51'),
(8, 'warga01', 'dimana ibu siti tinggal', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Lengkap: Siti Aminah\n• Lokasi Kamar: Blok B - Lantai 1 - Unit 101\n• No. HP Aktif: 081298765432\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-06-22 14:33:35'),
(9, 'admin01', 'dimana ibu yulianti tinggal?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Lengkap: iwan surya\n• Lokasi Kamar: Blok A - Lantai 2 - Unit 201\n• No. HP Aktif: 078859472657\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-06-22 16:08:35'),
(10, 'warga01', 'yulianti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Lengkap: iwan surya\n• Lokasi Kamar: Blok A - Lantai 2 - Unit 201\n• No. HP Aktif: 078859472657\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-06-22 16:17:01'),
(11, 'warga01', 'yulianti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Lokasi Kamar: Blok A - Lantai 2 - Unit 201\n• No. HP Aktif: 078859472657\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-06-22 16:19:16'),
(12, 'admin01', 'siti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: Siti Aminah\n• Lokasi Kamar: Blok B - Lantai 1 - Unit 101\n• No. HP Aktif: 081298765432\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-07-01 11:37:34'),
(13, 'warga01', 'ingin bertanya', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"ingin bertanya\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-07-01 11:38:13'),
(14, 'warga01', 'siti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: Siti Aminah\n• Lokasi Kamar: Blok B - Lantai 1 - Unit 101\n• No. HP Aktif: 081298765432\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-07-01 11:49:42'),
(15, 'warga01', 'ibu yulianti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Lokasi Kamar: Blok A - Lantai 2 - Unit 201\n• No. HP Aktif: 078859472657\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-07-01 11:52:00'),
(16, 'warga01', 'ibu yulianti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Lokasi Kamar: Blok A - Lantai 2 - Unit 201\n• No. HP Aktif: 078859472657\n\nSilakan langsung mengunjungi unit rusun atau menghubungi kontak di atas.', '2026-07-01 12:06:26'),
(17, 'admin01', 'cek data rusun', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"cek data rusun\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-07-01 17:24:43'),
(18, 'admin01', 'keluarga bapak iwan', 'Untuk pengurusan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok.\n3. Hubungi Ketua RT 008 pada jam pelayanan (19.00 - 21.00 WIB).', '2026-07-01 17:25:29'),
(19, 'warga01', 'informasi iuran', 'Informasi Fasilitas & Biaya Iuran:\n• Iuran bulanan (Kebersihan & Keamanan): Rp 50.000 (Paling lambat tanggal 5 setiap bulan).\n• Fasilitas Warga: Area parkir terpadu, Aula serbaguna lantai 1, dan Mushola.', '2026-07-01 17:28:31'),
(20, 'warga01', 'berkas administrasi RT', 'Untuk pengurusan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok.\n3. Hubungi Ketua RT 008 pada jam pelayanan (19.00 - 21.00 WIB).', '2026-07-01 17:29:21'),
(21, 'warga01', 'kontak pengurus', 'Berikut adalah daftar kontak aktif pengurus RT 008 Rusun Klender:\n• Ketua RT: 0812-XXXX-XXXX\n• Sekretaris: 0856-XXXX-XXXX\n• Bendahara: 0813-XXXX-XXXX', '2026-07-01 17:30:19'),
(22, 'warga01', 'ketersediaan unit', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"ketersediaan unit\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-07-01 17:30:39'),
(23, 'admin01', 'cek rekap data rusun', '[Laporan Statistik Sistem - Eksklusif Admin 📊]\n\nBerikut adalah ringkasan data real-time pada lingkungan RT 008:\n• Total Kepala Keluarga Terdata: 4 KK\n• Unit Kamar Status Kosong : 1 Unit\n• Unit Kamar Status Terisi : 3 Unit\n\nSistem database berjalan normal dan terintegrasi penuh.', '2026-07-01 17:46:01'),
(24, 'admin01', 'statistik warga', '[Laporan Statistik Sistem - Eksklusif Admin 📊]\n\nBerikut adalah ringkasan data real-time pada lingkungan RT 008:\n• Total Kepala Keluarga Terdata: 4 KK\n• Unit Kamar Status Kosong : 1 Unit\n• Unit Kamar Status Terisi : 3 Unit\n\nSistem database berjalan normal dan terintegrasi penuh.', '2026-07-01 17:46:32'),
(25, 'admin01', 'keluarga bapak iwan', 'Untuk pengurusan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok.\n3. Hubungi Ketua RT 008 pada jam pelayanan (19.00 - 21.00 WIB).', '2026-07-01 17:46:58'),
(26, 'admin01', 'bapak iwan', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 305\n• No. HP Aktif: 078859472657\n\n💡 *Mode Pengurus*: Anda melihat nomor telepon penuh tanpa sensor.', '2026-07-01 17:49:39'),
(27, 'admin01', 'ibu yulianti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 305\n• No. HP Aktif: 078859472657\n\n💡 *Mode Pengurus*: Anda melihat nomor telepon penuh tanpa sensor.', '2026-07-01 17:49:52'),
(28, 'warga01', 'saya mau nanya rumahnya ibu yulianti', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"nanya rumahnya yulianti\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-07-02 18:56:48'),
(29, 'warga01', 'saya mau nanya alamatnya ibu yulianti', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"nanya alamatnya yulianti\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-07-02 18:57:10'),
(30, 'warga01', 'alamat ibu yulianti dimana ya?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Tempat, Tgl Lahir: Tidak Terdata\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 305\n• No. HP Aktif: 0788-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-02 18:58:03'),
(31, 'warga01', 'alamat bapak iwan dimana?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Tempat, Tgl Lahir: Tidak Terdata\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 305\n• No. HP Aktif: 0788-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-02 19:13:56'),
(32, 'warga01', 'alamat bapak ahmad', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: ahmad yani\n• Tempat, Tgl Lahir: Tidak Terdata\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 306\n• No. HP Aktif: 0967-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-02 19:14:17'),
(33, 'admin01', 'bapak iwan', '[Data Anggota Keluarga - Akses Admin 👨‍👩‍👧‍👦]\n\n• No. KK: Tidak Terdata\n• Kepala Keluarga: iwan surya (NIK: 0990123801289000)\n• Tempat, Tgl Lahir: Tidak Terdata\n• Unit Tinggal: Blok B - Lantai 3 - Unit 305\n• No. HP Utama: 078859472657\n\n🏠 Rincian Anggota Keluarga di Database:\n1. yulianti\n   - Hubungan: Istri\n   - NIK: -\n', '2026-07-02 19:15:01'),
(34, 'warga01', 'mau bayar iuran gimana?', 'Informasi Fasilitas & Biaya Iuran:\n• Iuran bulanan (Kebersihan & Keamanan): Rp 50.000 (Paling lambat tanggal 5 setiap bulan).\n• Fasilitas Warga: Area parkir terpadu, Aula serbaguna lantai 1, dan Mushola.', '2026-07-27 09:11:55'),
(35, 'warga01', 'mau ngurus dokumen kematian gimana?', 'Untuk pengurusan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok.\n3. Hubungi Ketua RT 008 pada jam pelayanan (19.00 - 21.00 WIB).', '2026-07-27 09:12:42'),
(36, 'warga01', 'ibu siti alamatnya dimana?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"siti alamatnya\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-07-27 09:13:05'),
(37, 'warga01', 'ibu siti alamat nya dimana?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: Siti Aminah\n• Tempat, Tgl Lahir: Tidak Terdata\n• Lokasi Kamar: Blok B - Lantai 1 - Unit 101\n• No. HP Aktif: 0812-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-27 09:13:20'),
(38, 'warga01', 'rumah ibu yuli dimana?', '[Hasil Analisis Ambigu 🔍]\n\nSaya menemukan beberapa data warga dengan ejaan nama yang mirip. Warga mana yang Anda maksud?\n\n- Fitria Syahrani Yuliawan\n- iwan surya\n\nSilakan tulis kembali menggunakan nama lengkap kepala keluarga secara lebih spesifik ya!', '2026-07-27 12:52:53'),
(39, 'warga01', 'ibu yuliantin', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"yuliantin\".\n\nPastikan penulisan nama atau pertanyaan Anda sudah benar.', '2026-07-27 12:53:33'),
(40, 'warga01', 'ibu yulianti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Tempat, Tgl Lahir: Tidak Terdata\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 305\n• No. HP Aktif: 0788-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-27 12:53:45'),
(41, 'warga01', 'rumah ibu yulianti dimana?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Tempat, Tgl Lahir: Tidak Terdata\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 305\n• No. HP Aktif: 0788-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-28 08:36:51'),
(42, 'warga01', 'dimana rumah bapak iwan?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Kepala Keluarga: iwan surya\n• Tempat, Tgl Lahir: Tidak Terdata\n• Lokasi Kamar: Blok B - Lantai 3 - Unit 305\n• No. HP Aktif: 0788-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-28 08:47:14'),
(43, 'warga01', 'mau kontak rt nya', 'Berikut adalah daftar kontak aktif pengurus RT 008 Rusun Klender:\n• Ketua RT: 0812-XXXX-XXXX\n• Sekretaris: 0856-XXXX-XXXX\n• Bendahara: 0813-XXXX-XXXX', '2026-07-28 08:48:00'),
(44, 'warga01', 'alamat ibu yulianti', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Warga   : yulianti\n• Status        : Istri di KK iwan surya\n• Tempat, TTL   : Tidak Terdata\n• Lokasi Kamar  : Blok B - Lantai 3 - Unit 305\n• No. HP Aktif  : 0788-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-28 08:51:54'),
(45, 'warga01', 'rumah ibu yulianti nomor berapa ya?', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Warga   : yulianti\n• Status        : Istri di KK iwan surya\n• Tempat, TTL   : Tidak Terdata\n• Lokasi Kamar  : Blok B - Lantai 3 - Unit 305\n• No. HP Aktif  : 0788-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-29 16:04:25'),
(46, 'warga01', 'fitria rumah nya dimana?', '[Hasil Analisis Ambigu 🔍]\n\nSaya menemukan beberapa data warga dengan kriteria nama yang mirip. Siapa yang Anda maksud?\n\n- Fitria Syahrani Yuliawan (Kepala Keluarga)\n- Fitria Syarani Yuliaw (Kepala Keluarga)\n\nSilakan tulis kembali menggunakan nama lengkap secara lebih spesifik ya!', '2026-07-29 16:34:14'),
(47, 'warga01', 'fitria syahrani yuliawan', '[Hasil Analisis Nama Ditemukan 🔍]\n\n• Nama Warga   : Fitria Syahrani Yuliawan\n• Status        : Kepala Keluarga di KK Fitria Syahrani Yuliawan\n• Tempat, TTL   : Tidak Terdata\n• Lokasi Kamar  : Blok A - Lantai 1 - Unit 102\n• No. HP Aktif  : 0858-XXXX-XXXX\n\nSilakan langsung mengunjungi unit rusun yang bersangkutan.', '2026-07-29 16:34:29'),
(48, 'warga01', 'rumah ibu yulianti dimana ya?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"yulianti\".\n\nPastikan penulisan nama atau kata kunci pencarian Anda sudah benar.', '2026-07-29 17:15:12'),
(49, 'warga01', 'rumah ibu yulianty dimana ya?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"yulianty\".\n\nPastikan penulisan nama atau kata kunci pencarian Anda sudah benar.', '2026-07-29 17:16:52'),
(50, 'warga01', 'rumah bapak iwan dimana ya?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Iwan Surya Negara\n• NIK            : 3175████████0000\n• No. KK         : ████████████████\n• Tempat, TTL    : Jakarta, 26-02-1969\n• Jenis Kelamin  : Laki-laki\n• Agama          : Islam\n• Pekerjaan      : Tidak Kerja\n• Alamat / Blok  : Blok 68/04/13\n• Status Tinggal : Warga Tetap\n', '2026-07-29 17:17:34'),
(51, 'warga01', 'rumah ibu yulianti dimana ya?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Yulianti\n• NIK            : 3175████████0008\n• No. KK         : ████████████████\n• Tempat, TTL    : Padang, 28-07-1975\n• Jenis Kelamin  : Perempuan\n• Agama          : Islam\n• Pekerjaan      : Ibu R tangga\n• Alamat / Blok  : Blok 68/04/13\n• Status Tinggal : Warga Tetap\n', '2026-07-29 17:18:47'),
(52, 'warga01', 'rumah ibu yulianti dimana ya?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Yulianti\n• Alamat / Blok  : Blok 68/04/13\n', '2026-07-29 17:20:58'),
(53, 'warga01', 'rumah fitria dimana?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Fitria Syarani Yulliaw\n• Alamat / Blok  : Blok 68/04/13\n', '2026-07-29 17:26:05'),
(54, 'warga01', 'hi, rumah ibu yulianti dimana ya?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Yulianti\n• Alamat / Blok  : Blok 68/04/13\n', '2026-07-29 18:53:28'),
(55, 'warga01', 'saya mau ngurus berkas ke pak rt, gimana ya cara nya?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"ngurus berkas ke pak gimana cara\".\n\nPastikan penulisan nama atau kata kunci pencarian Anda sudah benar.', '2026-07-29 18:54:02'),
(56, 'warga01', 'cara bayar iuran gimana ya?', 'Informasi & Tata Cara Pembayaran Iuran:\n• Iuran bulanan (Kebersihan, Keamanan, Kas RT, Dana Sosial): Rp 50.000 (Paling lambat tanggal 5 setiap bulan).\n• Pembayaran diserahkan langsung secara tunai ke Bendahara RT 008.\n\n👉 Untuk mengecek status verifikasi dan rekap rincian tagihan Anda, silakan kunjungi halaman **Informasi Iuran** (warga_iuran.php) di portal ini.', '2026-07-29 18:57:00'),
(57, 'warga01', 'cara minta surat gimana ya?', 'Untuk pengurusan berkas dan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok atau ajukan melalui sistem.\n3. Hubungi Ketua RT 008 pada jam pelayanan.\n\n👉 Silakan akses halaman **Layanan Dokumen** (warga_surat.php) pada menu di atas untuk memproses surat pengantar Anda secara digital.', '2026-07-29 18:57:31'),
(58, 'warga01', 'saya mau ngurus surat', 'Untuk pengurusan berkas dan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok atau ajukan melalui sistem.\n3. Hubungi Ketua RT 008 pada jam pelayanan.\n\n👉 Silakan akses halaman **Layanan Dokumen** (warga_surat.php) pada menu di atas untuk memproses surat pengantar Anda secara digital.', '2026-07-29 19:04:57'),
(59, 'warga01', 'saya mau bayar iuran', 'Informasi & Tata Cara Pembayaran Iuran:\n• Iuran bulanan (Kebersihan, Keamanan, Kas RT, Dana Sosial): Rp 50.000 (Paling lambat tanggal 5 setiap bulan).\n• Pembayaran diserahkan langsung secara tunai ke Bendahara RT 008.\n\n👉 Untuk mengecek status verifikasi dan rekap rincian tagihan Anda, silakan kunjungi halaman **Informasi Iuran** (warga_iuran.php) di portal ini.', '2026-07-29 19:05:09'),
(60, 'warga01', 'nomor pak rt apa ya?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"pak\".\n\nPastikan penulisan nama atau kata kunci pencarian Anda sudah benar.', '2026-07-29 19:05:24'),
(61, 'warga01', 'nomor wa pak rt', 'Berikut adalah daftar kontak aktif pengurus RT 008 Rusun Klender:\n• Ketua RT: 0812-XXXX-XXXX\n• Sekretaris: 0856-XXXX-XXXX\n• Bendahara: 0813-XXXX-XXXX', '2026-07-29 19:10:53'),
(62, 'warga01', 'nomor wa pak rt', 'Berikut adalah daftar kontak aktif pengurus RT 008 Rusun Klender:\n\n• **Ketua RT**: 0812-3456-7890\n  💬 [Chat WhatsApp Pak RT](https://wa.me/6281234567890?text=Halo%20Pak%20RT,%20saya%20warga%20Rusun%20Klender%20ingin%20bertanya...)\n\n• **Sekretaris**: 0856-9876-5432\n  💬 [Chat WhatsApp Sekretaris](https://wa.me/6285698765432?text=Halo%20Sekretaris%20RT,%20saya%20ingin%20berkonsultasi...)\n\n• **Bendahara**: 0813-1122-3344\n  💬 [Chat WhatsApp Bendahara](https://wa.me/6281311223344?text=Halo%20Bendahara%20RT,%20saya%20ingin%20konfirmasi%20iuran...)', '2026-07-29 19:12:51'),
(63, 'warga01', 'nomor wa pak rt', 'Berikut adalah kontak aktif pengurus RT 008 Rusun Klender:\n\n• **Ketua RT**: 0812-3456-7890\n  💬 [Chat WhatsApp Pak RT](https://wa.me/6281234567890?text=Halo%20Pak%20RT,%20saya%20warga%20Rusun%20Klender%20ingin%20bertanya...)', '2026-07-29 19:14:19'),
(64, 'warga01', 'nomor wa pak rt', 'Berikut adalah kontak aktif pengurus RT 008 Rusun Klender:\n\n• **Ketua RT**: 0812-3456-7890\n  💬 [Chat WhatsApp Pak RT](https://wa.me/6281234567890?)', '2026-07-29 19:17:54'),
(65, 'warga01', 'nomor wa pak rt', 'Berikut adalah kontak aktif pengurus RT 008 Rusun Klender:\n\n• **Ketua RT**: 0812-3456-7890\n  💬 <a href=\"https://wa.me/6281234567890?text=Halo%20Pak%20RT,%20saya%20warga%20Rusun%20Klender%20ingin%20bertanya...\" target=\"_blank\">Chat WhatsApp Pak RT</a>', '2026-07-29 19:19:27'),
(66, 'warga01', 'nomor wa pak rt', 'Berikut adalah kontak aktif pengurus RT 008 Rusun Klender:\n\n• **Ketua RT**: 0812-3456-7890\n  💬 <a href=\"https://wa.me/+6281234567890?text=Halo%20Pak%20RT,%20saya%20warga%20Rusun%20Klender%20ingin%20bertanya...\" target=\"_blank\">Chat WhatsApp Pak RT</a>', '2026-07-29 19:20:38'),
(67, 'warga01', 'nomor wa pak rt', 'Berikut adalah kontak aktif pengurus RT 008 Rusun Klender:\n\n• **Ketua RT**: 0812-3456-7890\n  💬 <a href=\"https://wa.me/+62 813-9871-4530?text=Halo%20Pak%20RT,%20saya%20warga%20Rusun%20Klender%20ingin%20bertanya...\" target=\"_blank\">Chat WhatsApp Pak RT</a>', '2026-07-29 19:21:16'),
(68, 'warga01', 'nomor wa pak rt', 'Berikut adalah kontak aktif pengurus RT 008 Rusun Klender:\n\n• **Ketua RT**: 0812-3456-7890\n  💬 <a href=\"https://wa.me/+6281398714530?text=Halo%20Pak%20RT,%20saya%20warga%20Rusun%20Klender%20ingin%20bertanya...\" target=\"_blank\">Chat WhatsApp Pak RT</a>', '2026-07-29 19:21:50'),
(69, 'warga01', 'ibu yulianti tinggal di unit mana?', 'Maaf, sistem tidak menemukan kecocokan informasi pada FAQ maupun database warga menggunakan kata kunci \"yulianti unit\".\n\nPastikan penulisan nama atau kata kunci pencarian Anda sudah benar.', '2026-07-29 19:22:49'),
(70, 'warga01', 'ibu yulianti tinggal dimana?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Yulianti\n• Alamat / Blok  : Blok 68/04/13\n', '2026-07-29 19:23:04'),
(71, 'warga01', 'ibu yulianti tinggal di unit mana?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Yulianti\n• Alamat / Blok  : Blok 68/04/13\n', '2026-07-29 19:23:51'),
(72, 'warga01', 'saya mau ngurus surat gimana ya?', 'Untuk pengurusan berkas dan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok atau ajukan melalui sistem.\n3. Hubungi Ketua RT 008 pada jam pelayanan.\n\n👉 Silakan akses halaman **Layanan Dokumen** (warga_surat.php) pada menu di atas untuk memproses surat pengantar Anda secara digital.', '2026-07-29 20:49:23'),
(73, 'warga01', 'kalo saya mau bayar iuran dimana?', 'Informasi & Tata Cara Pembayaran Iuran:\n• Iuran bulanan (Kebersihan, Keamanan, Kas RT, Dana Sosial): Rp 50.000 (Paling lambat tanggal 5 setiap bulan).\n• Pembayaran diserahkan langsung secara tunai ke Bendahara RT 008.\n\n👉 Untuk mengecek status verifikasi dan rekap rincian tagihan Anda, silakan kunjungi halaman **Informasi Iuran** (warga_iuran.php) di portal ini.', '2026-07-29 20:49:48'),
(74, 'warga01', 'kak fitria tinggal dimana?', '[Hasil Pencarian Data Warga 🔍]\n\n• Nama Lengkap   : Fitria Syarani Yuliawan\n• Alamat / Blok  : Blok 68/04/13\n', '2026-07-30 08:33:08'),
(75, 'warga01', 'kalo saya mau ngurus surat dimana?', 'Untuk pengurusan berkas dan dokumen administrasi (KTP/KK/Surat Pengantar):\n1. Siapkan fotokopi KTP lama & KK.\n2. Bawa surat pengantar dari perwakilan blok atau ajukan melalui sistem.\n3. Hubungi Ketua RT 008 pada jam pelayanan.\n\n👉 Silakan akses halaman **Layanan Dokumen** (warga_surat.php) pada menu di atas untuk memproses surat pengantar Anda secara digital.', '2026-07-30 08:33:30'),
(76, 'warga01', 'kalo saya mau bayar iuran gimana?', 'Informasi & Tata Cara Pembayaran Iuran:\n• Iuran bulanan (Kebersihan, Keamanan, Kas RT, Dana Sosial): Rp 50.000 (Paling lambat tanggal 5 setiap bulan).\n• Pembayaran diserahkan langsung secara tunai ke Bendahara RT 008.\n\n👉 Untuk mengecek status verifikasi dan rekap rincian tagihan Anda, silakan kunjungi halaman **Informasi Iuran** (warga_iuran.php) di portal ini.', '2026-07-30 08:33:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `saran`
--

CREATE TABLE `saran` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `saran`
--

INSERT INTO `saran` (`id`, `nama`, `pesan`, `tanggal`) VALUES
(1, 'rani', 'Email: jeongkukjeon97@gmail.com\n\nPesan:\nkdowoisaa', '2026-08-03 16:18:30'),
(2, 'rani', 'Email: winngookk@gmail.com\n\nPesan:\ningfokan cuy', '2026-08-04 09:59:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat_pengantar`
--

CREATE TABLE `surat_pengantar` (
  `id_surat` int(11) NOT NULL,
  `id_warga` int(11) NOT NULL,
  `jenis_surat` varchar(100) NOT NULL,
  `keperluan` text NOT NULL,
  `status_pengajuan` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  `tanggal_ajuan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `surat_pengantar`
--

INSERT INTO `surat_pengantar` (`id_surat`, `id_warga`, `jenis_surat`, `keperluan`, `status_pengajuan`, `tanggal_ajuan`) VALUES
(5, 2, 'Surat Keterangan Kematian', 'djwidsadklad', 'Disetujui', '2026-07-27 08:54:32'),
(6, 2, 'Domisili', 'buat domisili', 'Disetujui', '2026-08-04 06:09:42'),
(7, 2, 'Domisili', 'untuk keperluan tempat tinggal', 'Disetujui', '2026-08-08 06:29:32'),
(8, 2, 'Surat Pengantar', 'dadwad', 'Ditolak', '2026-08-08 09:19:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit_rusun`
--

CREATE TABLE `unit_rusun` (
  `id` int(11) NOT NULL,
  `blok` varchar(100) NOT NULL,
  `lantai` int(11) NOT NULL,
  `nomor_unit` varchar(100) NOT NULL,
  `status` enum('Terisi','Kosong') NOT NULL DEFAULT 'Kosong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `unit_rusun`
--

INSERT INTO `unit_rusun` (`id`, `blok`, `lantai`, `nomor_unit`, `status`) VALUES
(1, '67', 1, '5', 'Kosong'),
(2, '67', 4, '14', 'Kosong'),
(3, '68', 1, '3', 'Kosong'),
(4, '68', 2, '8', 'Kosong'),
(5, '68', 3, '9', 'Kosong'),
(6, '68', 4, '16', 'Kosong'),
(7, '67', 1, '01', 'Terisi'),
(12, '67', 1, '02', 'Terisi'),
(15, '67', 1, '03', 'Terisi'),
(20, '67', 1, '04', 'Terisi'),
(25, '67', 2, '05', 'Terisi'),
(30, '67', 2, '06', 'Terisi'),
(33, '67', 2, '07', 'Terisi'),
(34, '67', 2, '08', 'Terisi'),
(46, '67', 3, '09', 'Terisi'),
(50, '67', 3, '10', 'Terisi'),
(55, '67', 3, '11', 'Terisi'),
(57, '67', 3, '12', 'Terisi'),
(59, '67', 4, '13', 'Terisi'),
(62, '67', 4, '15', 'Terisi'),
(63, '67', 4, '16', 'Terisi'),
(69, '68', 1, '01', 'Terisi'),
(72, '68', 1, '02', 'Terisi'),
(75, '68', 1, '03', 'Terisi'),
(76, '68', 1, '04', 'Terisi'),
(79, '68', 2, '05', 'Terisi'),
(81, '68', 2, '06', 'Terisi'),
(84, '68', 2, '07', 'Terisi'),
(88, '68', 3, '09', 'Terisi'),
(92, '68', 3, '11', 'Terisi'),
(94, '68', 4, '13', 'Terisi'),
(99, '68', 4, '15', 'Terisi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Warga') DEFAULT 'Warga',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `warga`
--

CREATE TABLE `warga` (
  `id` int(11) NOT NULL,
  `nik` varchar(25) DEFAULT NULL,
  `no_kk` varchar(16) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nama_panggilan` varchar(50) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `agama` varchar(30) DEFAULT NULL,
  `pekerjaan` varchar(50) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `status_tinggal` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `warga`
--

INSERT INTO `warga` (`id`, `nik`, `no_kk`, `nama`, `nama_panggilan`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `pekerjaan`, `no_hp`, `alamat`, `status_tinggal`) VALUES
(1, '3175072211560006', '3175071102110120', 'Parsaoran Simorangkir', NULL, 'P. Sidempuan', '1958-11-22', 'Laki-laki', 'Kristen', 'Wiraswasta', NULL, 'Blok 67/01/01', 'Warga tetap'),
(2, '3175074505630010', '3175071102110120', 'Silvana Br Tambunan', NULL, 'Balimbing', '1963-05-05', 'Perempuan', 'Kristen', 'Ibu Rumah Tangga', NULL, 'Blok 67/01/01', 'Warga tetap'),
(3, '3175072606910014', '3175071102110120', 'Markus Aprilius S', NULL, 'Jakarta', '1990-04-17', 'Laki-laki', 'Kristen', 'Wiraswasta', NULL, 'Blok 67/01/01', 'Warga tetap'),
(4, '3175071704900010', '3175071102110120', 'Daniel Cristoper', NULL, 'Jakarta', '1991-08-28', 'Laki-laki', 'Kristen', 'Karyawan', NULL, 'Blok 67/01/01', 'Warga tetap'),
(5, '3175076609930004', '3175071102110120', 'Delia Stefany', NULL, 'Jakarta', '1993-06-28', 'Perempuan', 'Kristen', NULL, NULL, 'Blok 67/01/01', 'Warga tetap'),
(6, '3175071105760010', '3175070601092550', 'Hendra Kurniawan', NULL, 'Jakarta', '1976-05-11', 'Laki-laki', 'Islam', 'Wira Swasta', NULL, 'Blok 67/01/02', 'Warga tetap'),
(7, '3175074505760018', '3175070601092550', 'Lenny Magdalena', NULL, 'Jakarta', '1976-05-03', 'Perempuan', 'Islam', 'Ibu r.Tangga', NULL, 'Blok 67/01/02', 'Warga tetap'),
(8, '3175071109970008', '3175070601092550', 'Laurel Kiflehelen', NULL, 'Jakarta', '1997-09-11', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 67/01/02', 'Warga tetap'),
(9, '3175076302700010', '3175071002110070', 'Leniarti', NULL, 'Jakarta', '1970-02-23', 'Perempuan', 'Islam', 'Wiraswasta', NULL, 'Blok 67/01/03', 'Warga tetap'),
(10, '3175071202091008', '3175071002110070', 'Mahesa Pratama P', NULL, 'Bekasi', '2009-02-12', 'Laki-laki', 'Islam', 'Pelajar', NULL, 'Blok 67/01/03', 'Warga tetap'),
(11, '3175070809910004', '3175072303220020', 'Welly Septian M', NULL, 'Bogor', '1991-09-06', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/01/03', 'Warga tetap'),
(12, '3310145309950000', '3175072303220020', 'Pipin Adisty Nawangre', NULL, 'Klaten', '1995-09-13', 'Perempuan', 'Islam', 'Ibu r tangga', NULL, 'Blok 67/01/03', 'Warga tetap'),
(13, '3175071609220002', '3175072303220020', 'Ardian Adimayu P', NULL, 'Jakarta', '2022-09-16', 'Laki-laki', 'Islam', 'Balita', NULL, 'Blok 67/01/03', 'Warga tetap'),
(14, '3175070511710001', '3175070601095300', 'Obis Sahrifruddin', NULL, 'Lebak', '1971-11-05', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/02/08', 'Warga tetap'),
(15, '3175074709700008', '3175070601095300', 'Yatini', NULL, 'Jakarta', '1971-09-07', 'Perempuan', 'Islam', 'Ibu R Tangga', NULL, 'Blok 67/02/08', 'Warga tetap'),
(16, '3175075407071002', '3175070601095300', 'Isticomah', NULL, 'Lebak', '2007-07-14', 'Perempuan', 'Islam', 'Pelajar', NULL, 'Blok 67/02/08', 'Warga tetap'),
(17, '3175021402011001', '3175070730011100', 'Alwan Farhan', NULL, 'Jakarta', '2001-02-14', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/01/04', 'Warga tetap'),
(18, '3175085505780004', '3175070108160002', 'Leny Mei F Saragih SPD', NULL, 'Tiga Urung', '1978-06-14', 'Perempuan', 'Kristen', 'Wiraswasta', NULL, 'Blok 67/01/04', 'W-Kontrak'),
(19, '3175081701050001', '3175070108160002', 'Sion Lukas Ekan', NULL, 'Jakarta', '2005-01-17', 'Laki-laki', 'Kristen', 'Mahasiswa', NULL, 'Blok 67/01/04', 'W-Kontrak'),
(20, '3175080506070010', '3175070108160002', 'Crist Nathanel P', NULL, 'Jakarta', '2007-06-05', 'Laki-laki', 'Kristen', 'Mahasiswa', NULL, 'Blok 67/01/04', 'W-Kontrak'),
(21, '3175080111111903', '3175070108160002', 'Theo Jordhan P', NULL, 'Jakarta', '2011-11-30', 'Laki-laki', 'Kristen', 'Pelajar', NULL, 'Blok 67/01/04', 'W-Kontrak'),
(22, '325122903840004', NULL, 'Samsudin A. Rahman', NULL, 'Jakarta', '1984-03-29', 'Laki-laki', 'Kristen', 'Karyawan', NULL, 'Blok 67/02/05', 'Gak jelas'),
(23, '3171086610880006', NULL, 'Maria Hana Octaviani', NULL, 'Jakarta', '1988-10-26', 'Perempuan', 'Kristen', 'Karyawan', NULL, 'Blok 67/02/05', 'tempat tinggal dimana'),
(24, '3275125305090001', NULL, 'Aurora Callistha', NULL, 'Bekasi', '2009-05-18', 'Perempuan', 'Kristen', 'Pelajar', NULL, 'Blok 67/02/05', NULL),
(25, '31751310151005', NULL, 'Axel William Gregory', NULL, 'Bekasi', '2015-10-13', 'Laki-laki', 'Kristen', 'Pelajar', NULL, 'Blok 67/02/05', NULL),
(26, '317105440769007', NULL, 'J. Yesima Manurung', NULL, 'Jakarta', '1969-07-04', 'Perempuan', 'Kristen', NULL, NULL, 'Blok 67/02/05', NULL),
(27, '3175074507670003', '3175070501090370', 'Dinna L Julviera', NULL, 'Jakarta', '1967-07-06', 'Perempuan', 'Islam', 'Wiraswasta', NULL, 'Blok 67/02/06', 'Warga Tetap'),
(28, '3175076903690004', '3175070501090370', 'S Rizkyana Devi', NULL, 'Jakarta', '1969-03-28', 'Perempuan', 'Islam', 'Wiraswasta', NULL, 'Blok 67/02/06', 'Warga Tetap'),
(29, '3175071307710003', '3175070501090370', 'Dicky Busmanz', NULL, 'Jakarta', '1971-07-13', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/02/06', 'Warga Tetap'),
(30, '3175075904630006', '3175070501090370', 'Siti Jamilah', NULL, 'Leles', '1963-04-19', 'Perempuan', 'Islam', 'IBT', NULL, 'Blok 67/02/07', 'Tdk tau dimana'),
(31, '3175070406750013', '3175070601096113', 'Mohammad Djunaidi', NULL, 'Jakarta', '1975-06-04', 'Laki-laki', 'Islam', 'Kar. DKI', NULL, 'Blok 67/02/08', 'Domisili di Villa'),
(32, '3175076911720009', '3175070601096113', 'Kun Hardiyanti', NULL, 'Jakarta', '1972-11-29', 'Perempuan', 'Islam', 'IRT', NULL, 'Blok 67/02/08', 'Mutiara Gading'),
(33, '3175071510040011', '3175070601096113', 'Fadilla Syaugi', NULL, 'Jakarta', '2004-10-15', 'Laki-laki', 'Islam', 'Siswa', NULL, 'Blok 67/02/08', 'Taman Kabalen'),
(34, '3175075402091004', '3175070601096113', 'Asyifa Febriyanti', NULL, 'Jakarta', '2009-02-14', 'Perempuan', 'Islam', 'Siswa', NULL, 'Blok 67/02/08', 'Blok K/No.2'),
(35, '3175075504151003', '3175070601096113', 'Alisha Khana Wilda', NULL, 'Bekasi', '2015-04-15', 'Perempuan', 'Islam', 'Pelajar', NULL, 'Blok 67/02/08', NULL),
(36, '3175072601740008', '3175070501097599', 'Didik Kurniawan', NULL, 'Jakarta', '1974-01-26', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 67/02/08', 'Warga Kontrak'),
(37, '3175078701780004', '3175070501097599', 'Suzanti Damanna', NULL, 'M. Rawas', '1978-01-27', 'Perempuan', 'Islam', 'Ibu R.Tangga', NULL, 'Blok 67/02/08', 'Warga Kontrak'),
(38, '3175078104050003', '3175070501097599', 'Maulidia Anya Nabilah', NULL, 'Jakarta', '2008-04-21', 'Perempuan', 'Islam', 'Siswa', NULL, 'Blok 67/02/08', 'Warga Kontrak'),
(39, '317507440712108', '3175070501097599', 'Aliah Putri Khai ishah', NULL, 'Jakarta', '2012-07-04', 'Perempuan', 'Islam', 'Siswa', NULL, 'Blok 67/02/08', 'Warga Kontrak'),
(40, '3175076010540000', '3175070203110060', 'Hotnida Nainggolan', NULL, 'P. Siantar', '1954-10-20', 'Perempuan', 'Kristen', 'Ibu R tangga', NULL, 'Blok 67/03/09', 'Warga tetap'),
(41, '3175074301870010', '3175070203110060', 'Ruth Sitompul', NULL, 'Jakarta', '1987-01-03', 'Perempuan', 'Kristen', 'Wiraswasta', NULL, 'Blok 67/03/09', 'Warga tetap'),
(42, '3175073012051002', '3175070203110060', 'Aldo Desta R', NULL, 'Jakarta', '2005-12-30', 'Laki-laki', 'Kristen', 'Wiraswasta', NULL, 'Blok 67/03/09', 'Warga tetap'),
(43, '3175074106071004', '3175070203110060', 'Alya Jessica Cecelia', NULL, 'Jakarta', '2007-06-01', 'Perempuan', 'Kristen', 'Siswa', NULL, 'Blok 67/03/09', 'Warga tetap'),
(44, '317506504791002', '3175060201121036', 'Rama Yumatha', NULL, 'Jakarta', '1979-04-25', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/03/10', 'Warga kontrak'),
(45, '3175062012111036', '3175060201121036', 'Ananti Beorean Pitta', NULL, 'Rantepao', '1991-11-24', 'Perempuan', 'Islam', 'Ibu r tangga', NULL, 'Blok 67/03/10', 'Warga kontrak'),
(46, '3175062010031003', '3175060201121036', 'M Karel Poborsky Y', NULL, 'Jakarta', '2003-10-20', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 67/03/10', 'Warga kontrak'),
(47, '3175062807061000', '3175060201121036', 'M Rafi Akram Z', NULL, 'Jakarta', '2006-07-28', 'Laki-laki', 'Islam', 'Siswa', NULL, 'Blok 67/03/10', 'Warga kontrak'),
(48, '3175061212121036', '3175060201121036', 'M evansZamorano Y', NULL, 'Jakarta', '2012-12-12', 'Laki-laki', 'Islam', 'Pelajar', NULL, 'Blok 67/03/10', 'Warga kontrak'),
(49, '3175072250580004', '317507101097970', 'Amril M', NULL, 'Solok', '1958-05-25', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 67/03/11', 'Warga Tetap'),
(50, '3175074109500009', '317507101097970', 'Eva Irianti', NULL, 'Tegal', '1965-09-01', 'Perempuan', 'Islam', 'Ibu R. tangga', NULL, 'Blok 67/03/11', 'Warga tetap'),
(51, '3175071010750029', '3175070101095100', 'Ahmet Syahlaen', NULL, 'Plaju', '1975-10-10', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/03/12', 'Warga Tetap'),
(52, '3175070807770010', '3175070101095100', 'Zulkhoiry', NULL, 'Palembang', '1977-07-08', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/03/12', 'Dom. Palembang'),
(53, '3175070704570004', '3175070501090370', 'Suadi HS', NULL, 'Cilegon', '1957-04-07', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/04/13', 'Warga tetap'),
(54, '3175075602650003', '3175070501090370', 'Martinah', NULL, 'Sragen', '1965-02-16', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 67/04/13', 'Warga tetap'),
(55, '3175070911920001', '3175070501090370', 'Saifullah', NULL, 'Jakarta', '1992-11-09', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 67/04/13', 'Warga tetap'),
(56, '3175072411710000', '3175070601092380', 'M. Ridwan Rasahan', NULL, 'T. dalam', '1971-11-24', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 67/04/15', 'Warga tetap'),
(57, '3175072912520006', '3175070601090980', 'Sariman Ariemarto', NULL, 'Purworejo', '1952-04-07', 'Laki-laki', 'Islam', 'Pensiunan', NULL, 'Blok 67/04/16', 'Warga tetap'),
(58, '3175077012550014', '3175070601090980', 'Karni', NULL, 'Jakarta', '1955-12-30', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 67/04/16', 'Warga tetap'),
(59, '317404020770002', '3175070410111034', 'Andriansyah', NULL, 'Jakarta', '1979-07-02', 'Laki-laki', 'Islam', 'Wira swasta', NULL, 'Blok 67/04/16', 'Warga tetap'),
(60, '3175074105840005', '3175070410111034', 'Shinta Chandrawati S', NULL, 'Kar. RS', '1984-05-01', 'Perempuan', 'Islam', 'Kar. RS', NULL, 'Blok 67/04/16', 'Warga tetap'),
(61, '317507550131004', '3175070410111034', 'Ashilafathina F', NULL, 'Jakarta', '2013-05-15', 'Perempuan', 'Islam', 'Pelajar', NULL, 'Blok 67/04/16', 'Warga tetap'),
(62, '3175070000000000', '3175070410111034', 'Air Langga Attala', NULL, 'Jakarta', NULL, 'Laki-laki', 'Islam', 'Pelajar', NULL, 'Blok 67/04/16', 'Warga tetap'),
(63, '3172071909450005', '317507010904710', 'Agoes Marga Mulyana', NULL, 'Jakarta', '1946-09-19', 'Laki-laki', 'Kristen', 'Tidak Kerja', NULL, 'Blok 68/01/01', 'Warga Tetap'),
(64, '3175075803510002', '317507010904710', 'Hioe Njet Hian', NULL, 'Bandung', '1951-03-18', 'Perempuan', 'Kristen', 'Ibu R Tangga', NULL, 'Blok 68/01/01', 'Warga Tetap'),
(65, '3175075105850010', '317507010904710', 'Lisa Marga Mulyana', NULL, 'Jakarta', '1985-05-11', 'Perempuan', 'Kristen', 'Tidak tahu', NULL, 'Blok 68/01/01', 'Tidak tahu'),
(66, '317507610570004', '3175070501090370', 'Djoko Purnomo', NULL, 'Jakarta', '1957-10-04', 'Laki-laki', 'Islam', 'Pensiunan', NULL, 'Blok 68/01/02', 'Warga tetap'),
(67, '3175075404600003', '3175070501090370', 'Rochmaningsih', NULL, 'Kulon Projo', '1960-04-14', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 68/01/02', 'Warga tetap'),
(68, '3175072006900001', '3175070501090370', 'Agung Prasetio', NULL, 'Jakarta', '1990-06-20', 'Laki-laki', 'Islam', 'Tidak kerja', NULL, 'Blok 68/01/02', 'Warga tetap'),
(69, '3175070105600012', '3175071102110120', 'Berhanudin abbas', NULL, 'Mallino', '1960-06-01', 'Laki-laki', 'Islam', NULL, NULL, 'Blok 68/01/03', 'Tidak diketahui keberadaannya'),
(70, '3175077010640003', '3175071412200071', 'Erma Atmiany', NULL, 'Cilacap', '1964-10-30', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 68/01/04', 'Tidak tau keberadaannya'),
(71, '3175072811890004', '3175071412200032', 'Chairil Anwar', NULL, 'Jakarta', '1989-11-28', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 68/01/04', 'Warga tetap'),
(72, '3175065201960008', '3175071412200032', 'Intan Elmuri', NULL, 'Surabaya', '1967-02-06', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 68/01/04', 'Warga tetap'),
(73, '367814620670000', '3678152606140010', 'Taufan Henry', NULL, 'Semarang', '1964-10-30', 'Laki-laki', 'Islam', 'Wira Swasta', NULL, 'Blok 68/02/05', 'Warga Kontrak'),
(74, '3174042605860015', '3678152606140010', 'E Ayunita Sh MH', NULL, 'Surabaya', '1967-02-06', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 68/02/05', 'Warga Kontrak'),
(75, '3174042806210001', '3174042806210001', 'Bayu Putranto', NULL, 'Jakarta', '1986-05-26', 'Laki-laki', 'Islam', 'Tidak Kerja', NULL, 'Blok 68/02/06', 'Warga tetap'),
(76, '3175075412850000', '3174042806210001', 'Desiana', NULL, 'Jakarta', '1985-12-14', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 68/02/06', 'Warga tetap'),
(77, NULL, NULL, 'Dopi', NULL, 'Jakarta', NULL, 'Laki-laki', 'Islam', 'Balita', NULL, 'Blok 68/02/06', 'Warga tetap'),
(78, '3175071907640004', '317507160111102', 'Eko Parawiyanto', NULL, 'Purworejo', '1964-07-19', 'Laki-laki', 'Islam', 'Pensiunan', NULL, 'Blok 68/02/07', 'Warga tetap'),
(79, '3175070106600007', '317507160111102', 'Sri Waryanti', NULL, 'K anyar', '1966-10-30', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 68/02/07', 'Warga Tetap'),
(80, '3175070610970007', '317507160111102', 'Ghufron Wardiyanto', NULL, 'Jakarta', '1997-10-06', 'Laki-laki', 'Islam', 'Mahasiswa', NULL, 'Blok 68/02/07', 'Warga Tetap'),
(81, '3175071404000005', '317507160111102', 'Naufal Apriyanto', NULL, 'Jakarta', '2000-04-14', 'Laki-laki', 'Islam', 'Mahasiswa', NULL, 'Blok 68/02/07', 'Warga Tetap'),
(82, '3175076310550002', '3175070501090370', 'Henny Hendarsih', NULL, 'Malang', '1955-10-23', 'Perempuan', 'Islam', 'Ibu R rangga', NULL, 'Blok 68/03/09', 'Warga tetap'),
(83, '3175072012490001', '317107107141006', 'Hendry Putro Pratomo', NULL, 'Malang', '1979-12-08', 'Laki-laki', 'Islam', 'Tidak kerja', NULL, 'Blok 68/03/09', 'Warga tetap'),
(84, '3674040602540007', '317507107141006', 'Ferry Crisandika', NULL, 'P.mekasan', '1982-06-15', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 68/03/09', 'Numpang alamat'),
(85, '3674040602540000', '317507107141006', 'Firman Chrisandika', NULL, 'P.mekasan', '1982-06-15', 'Laki-laki', 'Islam', 'Karyawan', NULL, 'Blok 68/03/09', 'Numpang alamat'),
(86, '3272071308940001', '3272070809060021', 'Abdul Latif H', NULL, 'Sukabumi', '1994-08-13', 'Laki-laki', 'Islam', 'Wiraswasta', NULL, 'Blok 68/03/11', 'Warga kontrak'),
(87, NULL, NULL, 'Saliani', NULL, NULL, NULL, 'Laki-laki', 'Islam', NULL, NULL, 'Blok 68/03/11', 'Penghuni'),
(88, '3175072602690000', '3175070601092930', 'Iwan Surya Negara', NULL, 'Jakarta', '1969-02-26', 'Laki-laki', 'Islam', 'Tidak Kerja', NULL, 'Blok 68/04/13', 'Warga Tetap'),
(89, '3175076607750008', '3175070601092930', 'Yulianti', NULL, 'Padang', '1975-07-28', 'Perempuan', 'Islam', 'Ibu R tangga', NULL, 'Blok 68/04/13', 'Warga Tetap'),
(90, '3175070708031001', '3175070601092930', 'Sylvia Ananda Yuliawan', NULL, 'Jakarta', '2003-08-07', 'Perempuan', 'Islam', 'Pramugari', NULL, 'Blok 68/04/13', 'Warga Tetap'),
(91, '3175074511040006', '3175070601092930', 'Fitria Syarani Yuliawan', NULL, 'Jakarta', '2004-11-05', 'Perempuan', 'Islam', 'Mahasiswa', NULL, 'Blok 68/04/13', 'Warga Tetap'),
(92, '3175072912071002', '3175070601092930', 'Malvino Surya Yuliawan', NULL, 'Jakarta', '2007-12-29', 'Laki-laki', 'Islam', 'Siswa', NULL, 'Blok 68/04/13', 'Warga Tetap'),
(93, '3175076906650000', '3175070404110070', 'Kory Junikari H', NULL, 'P. Rapat', '1965-06-29', 'Perempuan', 'Kristen', 'Ibu R tangga', NULL, 'Blok 68/04/15', 'Warga Tetap'),
(94, '3175075410990010', '3175070404110070', 'Creansi Clarit R', NULL, 'Jakarta', '1999-10-14', 'Perempuan', 'Kristen', 'Karyan swasta', NULL, 'Blok 68/04/15', 'Warga Tetap');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `anggota_keluarga`
--
ALTER TABLE `anggota_keluarga`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_warga` (`id_warga`);

--
-- Indeks untuk tabel `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `informasi`
--
ALTER TABLE `informasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `iuran`
--
ALTER TABLE `iuran`
  ADD PRIMARY KEY (`id_iuran`),
  ADD KEY `id_warga` (`id_warga`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`);

--
-- Indeks untuk tabel `riwayat_chat`
--
ALTER TABLE `riwayat_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- Indeks untuk tabel `saran`
--
ALTER TABLE `saran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `surat_pengantar`
--
ALTER TABLE `surat_pengantar`
  ADD PRIMARY KEY (`id_surat`),
  ADD KEY `id_warga` (`id_warga`);

--
-- Indeks untuk tabel `unit_rusun`
--
ALTER TABLE `unit_rusun`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `warga`
--
ALTER TABLE `warga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `id_unit` (`alamat`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akun`
--
ALTER TABLE `akun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `anggota_keluarga`
--
ALTER TABLE `anggota_keluarga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `informasi`
--
ALTER TABLE `informasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `iuran`
--
ALTER TABLE `iuran`
  MODIFY `id_iuran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `riwayat_chat`
--
ALTER TABLE `riwayat_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `saran`
--
ALTER TABLE `saran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `surat_pengantar`
--
ALTER TABLE `surat_pengantar`
  MODIFY `id_surat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `unit_rusun`
--
ALTER TABLE `unit_rusun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `warga`
--
ALTER TABLE `warga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `anggota_keluarga`
--
ALTER TABLE `anggota_keluarga`
  ADD CONSTRAINT `anggota_keluarga_ibfk_1` FOREIGN KEY (`id_warga`) REFERENCES `warga` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `iuran`
--
ALTER TABLE `iuran`
  ADD CONSTRAINT `iuran_ibfk_1` FOREIGN KEY (`id_warga`) REFERENCES `warga` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `riwayat_chat`
--
ALTER TABLE `riwayat_chat`
  ADD CONSTRAINT `riwayat_chat_ibfk_1` FOREIGN KEY (`username`) REFERENCES `akun` (`username`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `surat_pengantar`
--
ALTER TABLE `surat_pengantar`
  ADD CONSTRAINT `surat_pengantar_ibfk_1` FOREIGN KEY (`id_warga`) REFERENCES `warga` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `warga` (`nik`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
