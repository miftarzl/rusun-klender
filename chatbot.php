<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

$wa_nomor_rt = "6281398714530"; 
$pesan_wa = urlencode("Halo Pengurus RT 008 Rusun Klender, saya ingin bertanya mengenai ketersediaan unit rusun dan informasi lebih lanjut.");
$whatsapp_url = "https://wa.me/" . $wa_nomor_rt . "?text=" . $pesan_wa;
$link_wa_html = "<br><br>Jika Anda berminat atau ingin menanyakan unit ini lebih lanjut, silakan hubungi pengurus melalui: <br><a href='$whatsapp_url' target='_blank' style='color: #25D366; font-weight: bold;'>Chat WhatsApp Pengurus RT</a>";

try {
    @include 'koneksi.php';

    $active_conn = null;
    if (isset($conn) && $conn) $active_conn = $conn;
    elseif (isset($koneksi) && $koneksi) $active_conn = $koneksi;
    elseif (isset($db) && $db) $active_conn = $db;
    elseif (isset($link) && $link) $active_conn = $link;
    elseif (isset($mysqli) && $mysqli) $active_conn = $mysqli;

    if (!$active_conn) {
        $host = getenv('DB_HOST') ?: "localhost";
        $user = getenv('DB_USER') ?: "root";
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
        $db   = getenv('DB_NAME') ?: "rusun_klender";
        $active_conn = @mysqli_connect($host, $user, $pass, $db);
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $user_message = trim($input['message'] ?? '');
    $user_role    = strtolower(trim($input['role'] ?? 'publik')); // pastikan lowercase

    if (empty($user_message)) {
        echo json_encode(['status' => 'success', 'reply' => 'Pesan tidak boleh kosong.']);
        exit;
    }

    $lower_msg = strtolower($user_message);
    $is_greeting = (in_array($lower_msg, ['hi', 'hai', 'halo', 'hallo', 'pagi', 'siang', 'sore', 'malam', 'p', 'assalamu\'alaikum', 'assalamualaikum']) || strlen($lower_msg) <= 4);
    $is_closing = (strpos($lower_msg, 'makasih') !== false || strpos($lower_msg, 'terima kasih') !== false || strpos($lower_msg, 'thanks') !== false || strpos($lower_msg, 'oke') !== false || strpos($lower_msg, 'sip') !== false || strpos($lower_msg, 'baik') !== false);
    $is_asking_unit = !$is_greeting && !$is_closing && (strpos($lower_msg, 'unit') !== false || strpos($lower_msg, 'kosong') !== false || strpos($lower_msg, 'tersedia') !== false || strpos($lower_msg, 'kamar') !== false || strpos($lower_msg, 'sewa') !== false || strpos($lower_msg, 'blok') !== false || strpos($lower_msg, 'daftar') !== false);

    // Deteksi pencarian alamat / data warga
    $is_asking_resident = !$is_greeting && !$is_closing && (
        strpos($lower_msg, 'alamat') !== false || 
        strpos($lower_msg, 'rumah') !== false || 
        strpos($lower_msg, 'tinggal') !== false || 
        strpos($lower_msg, 'dimana') !== false || 
        strpos($lower_msg, 'warga') !== false ||
        strpos($lower_msg, 'siapa') !== false ||
        strpos($lower_msg, 'ibu') !== false ||
        strpos($lower_msg, 'bapak') !== false
    );

    // ==========================================
    // VALIDASI KERAS (HARD BLOCK) UNTUK ROLE PUBLIK
    // ==========================================
    $is_warga_only_topic = ($user_role === 'publik') && (
        strpos($lower_msg, 'iuran') !== false || 
        strpos($lower_msg, 'surat') !== false || 
        strpos($lower_msg, 'domisili') !== false || 
        strpos($lower_msg, 'pengantar') !== false || 
        strpos($lower_msg, 'kematian') !== false || 
        strpos($lower_msg, 'lapor') !== false ||
        strpos($lower_msg, 'kerusakan') !== false ||
        strpos($lower_msg, 'alamat') !== false || 
        strpos($lower_msg, 'rumah warga') !== false || 
        strpos($lower_msg, 'dimana rumah') !== false || 
        strpos($lower_msg, 'lokasi warga') !== false ||
        strpos($lower_msg, 'tinggal di blok') !== false
    );

    if ($is_warga_only_topic) {
        $blocked_reply = "Maaf, informasi atau layanan terkait alamat warga serta administrasi internal bersifat privasi dan hanya dapat diakses setelah Anda login sebagai warga yang terdaftar di RT 008 Rusun Klender.<br><br>Silakan melakukan Login akun warga terlebih dahulu jika Anda sudah menghuni unit, atau hubungi pengurus melalui tombol di bawah ini." . $link_wa_html;
        echo json_encode(['status' => 'success', 'reply' => $blocked_reply]);
        exit;
    }
    
    $context_data = "";
    $found_valid_context = false;

    if ($active_conn) {
        if ($is_closing) {
            $context_data = "Kondisi: Pengguna mengucapkan terima kasih atau menutup percakapan. Berikan balasan ramah ala AI yang natural tanpa basa-basi.";
            $found_valid_context = true;
        } elseif ($is_greeting) {
            $context_data = "Kondisi: Pengguna menyapa. Berikan sambutan ramah sebagai Asisten Virtual Rusun Klender RT 008.";
            $found_valid_context = true;
        } else {
            // Filter berdasarkan role pada database FAQ
            if ($user_role === 'warga') {
                $role_filter = "(target_role = 'warga' OR target_role = 'publik' OR target_role = 'semua')";
            } else {
                $role_filter = "(target_role = 'publik' OR target_role = 'semua')";
            }
            
            $q_faq = "SELECT pertanyaan, jawaban, target_role FROM faq WHERE $role_filter";
            $res_faq = @mysqli_query($active_conn, $q_faq);
            
            $stopwords = ['di', 'yang', 'ada', 'apa', 'saja', 'kalo', 'kalau', 'ya', 'kah', 'ke', 'dari', 'untuk', 'dengan', 'ini', 'itu', 'dan', 'atau', 'sih', 'bagaimana'];
            $clean_msg = preg_replace('/[^a-z0-9\s]/', '', $lower_msg);
            $user_words = array_filter(explode(' ', $clean_msg), function($w) use ($stopwords) {
                return strlen($w) > 1 && !in_array($w, $stopwords);
            });

            $best_score = 0;
            $best_faq_q = "";
            $best_faq_a = "";

            if ($res_faq && mysqli_num_rows($res_faq) > 0) {
                while ($faq = mysqli_fetch_assoc($res_faq)) {
                    $q_lower = strtolower($faq['pertanyaan']);
                    $score = 0;

                    if (trim($q_lower) === trim($lower_msg)) {
                        $score += 100;
                    }

                    foreach ($user_words as $uw) {
                        if (strpos($q_lower, $uw) !== false) {
                            $score += 5; 
                        }
                    }

                    if ($score > $best_score) {
                        $best_score = $score;
                        $best_faq_q = $faq['pertanyaan'];
                        $best_faq_a = $faq['jawaban'];
                    }
                }
            }

            $is_rusun_topic = ($best_score >= 5) || $is_asking_unit || $is_asking_resident || 
                              strpos($lower_msg, 'rusun') !== false || 
                              strpos($lower_msg, 'rt') !== false || 
                              strpos($lower_msg, 'rw') !== false || 
                              strpos($lower_msg, 'klender') !== false || 
                              strpos($lower_msg, 'pengurus') !== false;

            if ($is_rusun_topic) {
                $found_valid_context = true;
                if (!empty($best_faq_a)) {
                    $context_data .= "INFORMASI UTAMA:\n";
                    $context_data .= "$best_faq_a\n\n";
                }

                // Ambil data unit kosong jika ditanyakan
                if ($is_asking_unit) {
                    $context_data .= "DATA UNIT KOSONG TERBARU:\n";
                    $q_empty = "SELECT * FROM unit_rusun WHERE status = 'Kosong' OR status = 'Tersedia' LIMIT 10";
                    $res_empty = @mysqli_query($active_conn, $q_empty);
                    if ($res_empty && mysqli_num_rows($res_empty) > 0) {
                        while ($r = mysqli_fetch_assoc($res_empty)) {
                            $context_data .= "- Blok " . ($r['blok'] ?? '') . ", No. Unit " . ($r['nomor_unit'] ?? '') . "\n";
                        }
                    } else {
                        $context_data .= "- Belum ada data unit kosong saat ini.\n";
                    }
                }

                // Ambil data warga dari tabel 'warga' sesuai kolom asli (nama, alamat, status_tinggal, pekerjaan)
                if ($user_role === 'warga' && $is_asking_resident) {
                    $context_data .= "DATA WARGA / PENGHUNI RUMAH SUSUN:\n";
                    
                    // Bersihkan keyword dari teks pertanyaan untuk pencarian LIKE
                    $clean_kw = preg_replace('/(saya|mau|tanya|nanya|tolong|cari|alamat|rumah|tinggal|di|dimana|siapa|ibu|bapak|saudara|rt|008|rusun|klender|\?)/ui', '', $lower_msg);
                    $clean_kw = trim($clean_kw);
                    
                    if (!empty($clean_kw)) {
                        $q_warga = "SELECT nama, alamat, status_tinggal, pekerjaan FROM warga WHERE nama LIKE '%" . mysqli_real_escape_string($active_conn, $clean_kw) . "%' OR alamat LIKE '%" . mysqli_real_escape_string($active_conn, $clean_kw) . "%' LIMIT 5";
                    } else {
                        $q_warga = "SELECT nama, alamat, status_tinggal, pekerjaan FROM warga LIMIT 10";
                    }
                    
                    $res_warga = @mysqli_query($active_conn, $q_warga);
                    if ($res_warga && mysqli_num_rows($res_warga) > 0) {
                        while ($rw = mysqli_fetch_assoc($res_warga)) {
                            $nama_warga = $rw['nama'] ?? '-';
                            $alamat_warga = $rw['alamat'] ?? '-';
                            $status_warga = $rw['status_tinggal'] ?? '-';
                            $pekerjaan_warga = $rw['pekerjaan'] ?? '-';
                            
                            $context_data .= "- Nama: $nama_warga | Alamat (Blok): $alamat_warga | Status: $status_warga | Pekerjaan: $pekerjaan_warga\n";
                        }
                    } else {
                        $context_data .= "- Data warga dengan kata kunci tersebut tidak ditemukan di database.\n";
                    }
                }

            } else {
                $context_data .= "TOPIK DILUAR BATASAN.\n";
            }
        }
    }

    $url = "https://coherence-elf-urchin.ngrok-free.dev/api/generate";
    
    $system_prompt = "Anda adalah Asisten Virtual resmi Rumah Susun Klender RT 008. 
Gaya bahasa Anda ramah, to the point, natural, dan profesional.

ATURAN MUTLAK PENULISAN JAWABAN:
1. Langsung berikan inti jawaban atau langkah-langkahnya tanpa kalimat pengantar sama sekali.
2. DILARANG KERAS MENGGUNAKAN:
   - Frasa seperti \"Menurut FAQ\", \"Berdasarkan data/informasi\", \"Dari sistem\", atau sejenisnya.
   - Kalimat penutup yang kaku seperti \"Dengan demikian...\", \"Jika ada pertanyaan lain silakan tanyakan\", atau basa-basi robotik lainnya.
3. Gunakan hanya informasi dari data di bawah ini:

" . $context_data;

    $payload = [
        "model" => "llama3",
        "prompt" => "Pertanyaan Pengguna: " . $user_message,
        "system" => $system_prompt,
        "stream" => false
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    $response = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_errno !== 0) {
        echo json_encode(['status' => 'success', 'reply' => "<b>cURL Error (#$curl_errno):</b> $curl_error"]);
        exit;
    }

    $bot_reply = "";
    if ($response) {
        $json = json_decode($response, true);
        $bot_reply = $json['response'] ?? '';
    }

    if (empty($bot_reply)) {
        $bot_reply = "Maaf, server AI mengembalikan respons kosong.";
    }

    if (!$found_valid_context && !$is_greeting && !$is_closing) {
        $bot_reply = "Maaf, sebagai Asisten Virtual Rusun Klender RT 008, saya hanya dapat membantu menjawab pertanyaan seputar informasi kependudukan, ketersediaan unit, dan layanan publik yang berkaitan dengan Rumah Susun Klender.";
    }

    // PEMBERSIHAN EKSTRA DI PHP (POST-PROCESSING)
    $bot_reply = preg_replace('/^(langsung\s+jawab\s*[\:\-]?\s*)/ui', '', $bot_reply);
    $bot_reply = preg_replace('/^(menurut\s+[^\n]*[\.\,\:]?\s*)/ui', '', $bot_reply);
    $bot_reply = preg_replace('/^(berdasarkan\s+[^\n]*[\.\,\:]?\s*)/ui', '', $bot_reply);
    $bot_reply = preg_replace('/(dengan\s+demikian[^\n]*)$/ui', '', $bot_reply);
    $bot_reply = trim($bot_reply);

    if ($user_role === 'publik' && ($is_asking_unit || strpos($lower_msg, 'ngontrak') !== false || strpos($lower_msg, 'beli') !== false || strpos($lower_msg, 'daftar') !== false || strpos($lower_msg, 'sewa') !== false)) {
        $bot_reply .= $link_wa_html;
    }

    echo json_encode(['status' => 'success', 'reply' => $bot_reply]);

} catch (Exception $e) {
    echo json_encode(['status' => 'success', 'reply' => 'Maaf, terjadi gangguan sistem: ' . $e->getMessage()]);
}
exit;
?>