<?php
/**
 * Chat API Endpoint - Proxy to Groq AI
 * With real-time database data
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Disable error display (will be in JSON)
error_reporting(0);
ini_set('display_errors', 0);

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ========== RATE LIMITING ==========
// Limit: 10 requests per minute per IP
$rateLimitMax = 10;
$rateLimitWindow = 60; // seconds

// Get client IP
$clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$clientIP = preg_replace('/[^a-zA-Z0-9\.\:]/', '', $clientIP); // Sanitize

// Rate limit file storage (simple file-based for shared hosting compatibility)
$rateLimitDir = __DIR__ . '/../temp/ratelimit';
if (!is_dir($rateLimitDir)) {
    @mkdir($rateLimitDir, 0755, true);
}

$rateLimitFile = $rateLimitDir . '/' . md5($clientIP) . '.json';
$currentTime = time();
$requestData = [];

// Load existing rate limit data
if (file_exists($rateLimitFile)) {
    $content = @file_get_contents($rateLimitFile);
    if ($content) {
        $requestData = json_decode($content, true) ?: [];
    }
}

// Clean old entries (older than rate limit window)
$requestData = array_filter($requestData, function($timestamp) use ($currentTime, $rateLimitWindow) {
    return ($currentTime - $timestamp) < $rateLimitWindow;
});

// Check if rate limited
if (count($requestData) >= $rateLimitMax) {
    $waitTime = $rateLimitWindow - ($currentTime - min($requestData));
    echo json_encode([
        'error' => "Terlalu banyak permintaan. Silakan tunggu $waitTime detik.",
        'rate_limited' => true
    ]);
    exit;
}

// Add current request
$requestData[] = $currentTime;
@file_put_contents($rateLimitFile, json_encode($requestData));

// Load config
$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    echo json_encode(['error' => 'Config not found']);
    exit;
}
require_once $configPath;

// Load database
$koneksiPath = __DIR__ . '/../koneksi.php';
if (!file_exists($koneksiPath)) {
    echo json_encode(['error' => 'Koneksi not found']);
    exit;
}
require_once $koneksiPath;

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$message = isset($input['message']) ? trim($input['message']) : '';
$history = isset($input['history']) && is_array($input['history']) ? $input['history'] : [];

if (empty($message)) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// ========== LIBRARY INTEGRATION ==========
$libraryResults = "";
$lowerMessage = strtolower($message);

// Get website base URL for download links
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$basePath = dirname(dirname($_SERVER['REQUEST_URI']));
$downloadBaseUrl = rtrim($baseUrl . $basePath, '/') . '/uploads/';

// ========== 1. CATEGORY QUERY ==========
// "Buku kategori Novel apa saja?", "list buku novel", "buku apa saja di kategori X"
$categoryPatterns = ['kategori', 'buku kategori', 'list buku', 'daftar buku'];
$categories = ['Novel', 'Pendidikan', 'Sains & Teknologi', 'Sejarah', 'Agama', 'Bahasa', 'Buku Bacaan'];

foreach ($categories as $cat) {
    if (stripos($lowerMessage, strtolower($cat)) !== false && 
        (stripos($lowerMessage, 'kategori') !== false || stripos($lowerMessage, 'list') !== false || stripos($lowerMessage, 'daftar') !== false || stripos($lowerMessage, 'apa saja') !== false)) {
        
        $escapedCat = mysqli_real_escape_string($koneksi, $cat);
        $catQuery = @mysqli_query($koneksi, "SELECT judul, pengarang FROM buku WHERE LOWER(kategori) = LOWER('$escapedCat') LIMIT 10");
        
        if ($catQuery && mysqli_num_rows($catQuery) > 0) {
            $libraryResults .= "\n\n📂 BUKU KATEGORI '$cat':";
            $idx = 1;
            while ($book = mysqli_fetch_assoc($catQuery)) {
                $libraryResults .= "\n$idx. \"" . $book['judul'] . "\" oleh " . $book['pengarang'];
                $idx++;
            }
        } else {
            $libraryResults .= "\n\n📂 KATEGORI '$cat': Belum ada buku.";
        }
        break;
    }
}

// ========== 2. AUTHOR QUERY ==========
// "Siapa pengarang buku Naruto?", "penulis buku X", "author buku X"
if (preg_match('/(pengarang|penulis|author|siapa yang tulis|siapa yang menulis).*buku\s+(.+)/i', $message, $matches) ||
    preg_match('/buku\s+(.+?)\s+(pengarang|penulis|author|ditulis)/i', $message, $matches2)) {
    
    $bookTitle = isset($matches[2]) ? trim($matches[2]) : (isset($matches2[1]) ? trim($matches2[1]) : '');
    $bookTitle = preg_replace('/[?!.,\'"]/u', '', $bookTitle);
    
    if (!empty($bookTitle)) {
        $escapedTitle = mysqli_real_escape_string($koneksi, $bookTitle);
        $authorQuery = @mysqli_query($koneksi, "SELECT judul, pengarang, tahun_terbit, penerbit FROM buku WHERE LOWER(judul) LIKE LOWER('%$escapedTitle%') LIMIT 1");
        
        if ($authorQuery && mysqli_num_rows($authorQuery) > 0) {
            $book = mysqli_fetch_assoc($authorQuery);
            $libraryResults .= "\n\n✍️ INFO PENGARANG BUKU:";
            $libraryResults .= "\n- Judul: \"" . $book['judul'] . "\"";
            $libraryResults .= "\n- Pengarang: " . $book['pengarang'];
            if ($book['penerbit']) $libraryResults .= "\n- Penerbit: " . $book['penerbit'];
            if ($book['tahun_terbit']) $libraryResults .= "\n- Tahun Terbit: " . $book['tahun_terbit'];
        } else {
            $libraryResults .= "\n\n✍️ Buku '$bookTitle' tidak ditemukan di perpustakaan.";
        }
    }
}

// ========== 3. DOWNLOAD QUERY ==========
// "Download buku Naruto", "unduh buku X", "link download buku X"
if (preg_match('/(download|unduh|link.*download|mau.*download|bisa.*download).*buku\s+(.+)/i', $message, $matches) ||
    preg_match('/buku\s+(.+?)\s+(download|unduh)/i', $message, $matches2)) {
    
    $bookTitle = isset($matches[2]) ? trim($matches[2]) : (isset($matches2[1]) ? trim($matches2[1]) : '');
    $bookTitle = preg_replace('/[?!.,\'"]/u', '', $bookTitle);
    
    if (!empty($bookTitle)) {
        $escapedTitle = mysqli_real_escape_string($koneksi, $bookTitle);
        $downloadQuery = @mysqli_query($koneksi, "SELECT id, judul, pengarang, file_buku FROM buku WHERE LOWER(judul) LIKE LOWER('%$escapedTitle%') AND file_buku IS NOT NULL AND file_buku != '' LIMIT 1");
        
        if ($downloadQuery && mysqli_num_rows($downloadQuery) > 0) {
            $book = mysqli_fetch_assoc($downloadQuery);
            $libraryResults .= "\n\n📥 DOWNLOAD BUKU:";
            $libraryResults .= "\n- Judul: \"" . $book['judul'] . "\"";
            $libraryResults .= "\n- Pengarang: " . $book['pengarang'];
            $libraryResults .= "\n- Link: " . $downloadBaseUrl . $book['file_buku'];
            $libraryResults .= "\n- Atau akses: perpustakaan.php dan cari buku \"" . $book['judul'] . "\"";
            $libraryResults .= "\n\nBerikan informasi download di atas kepada pengguna dengan format yang rapi.";
        } else {
            $libraryResults .= "\n\n📥 Buku '$bookTitle' tidak tersedia untuk download atau tidak ditemukan.";
        }
    }
}

// ========== 4. GENERAL BOOK SEARCH ==========
// "cari buku Naruto", "ada buku tentang X"
$searchPatterns = ['cari buku', 'cari judul', 'ada buku', 'buku tentang', 'search buku', 'find buku'];
$isBookSearch = false;
$searchKeyword = '';

foreach ($searchPatterns as $pattern) {
    if (strpos($lowerMessage, $pattern) !== false) {
        $isBookSearch = true;
        $pos = strpos($lowerMessage, $pattern);
        $searchKeyword = trim(substr($message, $pos + strlen($pattern)));
        $searchKeyword = preg_replace('/[?!.,\'"]/u', '', $searchKeyword);
        break;
    }
}

if ($isBookSearch && !empty($searchKeyword)) {
    $escapedKeyword = mysqli_real_escape_string($koneksi, $searchKeyword);
    $searchQuery = @mysqli_query($koneksi, "
        SELECT judul, pengarang, kategori, tahun_terbit 
        FROM buku 
        WHERE LOWER(judul) LIKE LOWER('%$escapedKeyword%') 
           OR LOWER(pengarang) LIKE LOWER('%$escapedKeyword%')
           OR LOWER(kategori) LIKE LOWER('%$escapedKeyword%')
        LIMIT 5
    ");
    
    if ($searchQuery && mysqli_num_rows($searchQuery) > 0) {
        $libraryResults .= "\n\n📚 HASIL PENCARIAN BUKU untuk '$searchKeyword':";
        $idx = 1;
        while ($book = mysqli_fetch_assoc($searchQuery)) {
            $libraryResults .= "\n$idx. \"" . $book['judul'] . "\" oleh " . $book['pengarang'];
            if ($book['kategori']) $libraryResults .= " - Kategori: " . $book['kategori'];
            if ($book['tahun_terbit']) $libraryResults .= " (" . $book['tahun_terbit'] . ")";
            $idx++;
        }
    } else {
        $libraryResults .= "\n\n📚 HASIL PENCARIAN BUKU untuk '$searchKeyword': Tidak ditemukan buku yang cocok.";
    }
}

// ========== 5. NEWS/BERITA QUERY ==========
$newsResults = "";

// 5a. Latest news query - "berita terbaru", "kabar terbaru", "ada berita apa"
if (preg_match('/(berita|kabar|news).*(terbaru|terakhir|baru|terkini)/i', $message) ||
    preg_match('/(terbaru|terakhir|baru).*(berita|kabar|news)/i', $message) ||
    stripos($lowerMessage, 'ada berita apa') !== false) {
    
    $newsQuery = @mysqli_query($koneksi, "SELECT judul, isi, tanggal_buat FROM berita ORDER BY tanggal_buat DESC LIMIT 5");
    
    if ($newsQuery && mysqli_num_rows($newsQuery) > 0) {
        $newsResults .= "\n\n📰 BERITA TERBARU:";
        $idx = 1;
        while ($news = mysqli_fetch_assoc($newsQuery)) {
            $date = date('d M Y', strtotime($news['tanggal_buat']));
            $excerpt = substr(strip_tags($news['isi']), 0, 80) . '...';
            $newsResults .= "\n$idx. \"" . $news['judul'] . "\" ($date)";
            $newsResults .= "\n   " . $excerpt;
            $idx++;
        }
        $newsResults .= "\n\nUntuk berita lengkap, kunjungi halaman news.php";
    } else {
        $newsResults .= "\n\n📰 Belum ada berita terbaru.";
    }
}

// 5b. News search - "cari berita tentang X", "berita tentang X"
if (preg_match('/(cari berita|berita tentang|news tentang|kabar tentang)\s+(.+)/i', $message, $newsMatches)) {
    $newsKeyword = trim($newsMatches[2]);
    $newsKeyword = preg_replace('/[?!.,\'"]/u', '', $newsKeyword);
    
    if (!empty($newsKeyword)) {
        $escapedNews = mysqli_real_escape_string($koneksi, $newsKeyword);
        $newsSearchQuery = @mysqli_query($koneksi, "
            SELECT judul, isi, tanggal_buat 
            FROM berita 
            WHERE LOWER(judul) LIKE LOWER('%$escapedNews%') 
               OR LOWER(isi) LIKE LOWER('%$escapedNews%')
            ORDER BY tanggal_buat DESC
            LIMIT 3
        ");
        
        if ($newsSearchQuery && mysqli_num_rows($newsSearchQuery) > 0) {
            $newsResults .= "\n\n📰 BERITA TENTANG '$newsKeyword':";
            $idx = 1;
            while ($news = mysqli_fetch_assoc($newsSearchQuery)) {
                $date = date('d M Y', strtotime($news['tanggal_buat']));
                $newsResults .= "\n$idx. \"" . $news['judul'] . "\" ($date)";
                $idx++;
            }
        } else {
            $newsResults .= "\n\n📰 Tidak ditemukan berita tentang '$newsKeyword'.";
        }
    }
}

// 5c. News count - "berapa berita", "jumlah berita"
if (preg_match('/(berapa|jumlah|total).*(berita|news)/i', $message) ||
    preg_match('/(berita|news).*(berapa|jumlah|total)/i', $message)) {
    
    $countQuery = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM berita");
    if ($countQuery) {
        $count = mysqli_fetch_assoc($countQuery)['total'];
        $newsResults .= "\n\n📰 Total berita di website: $count artikel.";
    }
}

// ========== 6. ANNOUNCEMENT/PENGUMUMAN QUERY ==========
$announcementResults = "";

// 6a. Latest announcement - "pengumuman terbaru", "ada pengumuman apa"
if (preg_match('/(pengumuman|announcement).*(terbaru|terakhir|baru|terkini)/i', $message) ||
    preg_match('/(terbaru|terakhir|baru).*(pengumuman|announcement)/i', $message) ||
    stripos($lowerMessage, 'ada pengumuman apa') !== false) {
    
    $annQuery = @mysqli_query($koneksi, "SELECT judul, isi, prioritas, tanggal_buat FROM pengumuman ORDER BY prioritas DESC, tanggal_buat DESC LIMIT 5");
    
    if ($annQuery && mysqli_num_rows($annQuery) > 0) {
        $announcementResults .= "\n\n📢 PENGUMUMAN TERBARU:";
        $idx = 1;
        while ($ann = mysqli_fetch_assoc($annQuery)) {
            $date = date('d M Y', strtotime($ann['tanggal_buat']));
            $priority = ($ann['prioritas'] == 'penting') ? '⭐ PENTING' : '';
            $announcementResults .= "\n$idx. " . ($priority ? "[$priority] " : "") . "\"" . $ann['judul'] . "\" ($date)";
            $idx++;
        }
        $announcementResults .= "\n\nUntuk detail, kunjungi halaman pengumuman.php";
    } else {
        $announcementResults .= "\n\n📢 Belum ada pengumuman terbaru.";
    }
}

// 6b. Important announcements - "pengumuman penting"
if (preg_match('/(pengumuman|announcement).*(penting|urgent|important)/i', $message) ||
    preg_match('/(penting|urgent).*(pengumuman|announcement)/i', $message)) {
    
    $importantQuery = @mysqli_query($koneksi, "SELECT judul, isi, tanggal_buat FROM pengumuman WHERE prioritas = 'penting' ORDER BY tanggal_buat DESC LIMIT 3");
    
    if ($importantQuery && mysqli_num_rows($importantQuery) > 0) {
        $announcementResults .= "\n\n⭐ PENGUMUMAN PENTING:";
        $idx = 1;
        while ($ann = mysqli_fetch_assoc($importantQuery)) {
            $date = date('d M Y', strtotime($ann['tanggal_buat']));
            $excerpt = substr(strip_tags($ann['isi']), 0, 100) . '...';
            $announcementResults .= "\n$idx. \"" . $ann['judul'] . "\" ($date)";
            $announcementResults .= "\n   " . $excerpt;
            $idx++;
        }
    } else {
        $announcementResults .= "\n\n⭐ Tidak ada pengumuman penting saat ini.";
    }
}

// 6c. Announcement count
if (preg_match('/(berapa|jumlah|total).*(pengumuman|announcement)/i', $message) ||
    preg_match('/(pengumuman|announcement).*(berapa|jumlah|total)/i', $message)) {
    
    $countQuery = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengumuman");
    if ($countQuery) {
        $count = mysqli_fetch_assoc($countQuery)['total'];
        $announcementResults .= "\n\n📢 Total pengumuman di website: $count pengumuman.";
    }
}

// Combine all results
$libraryResults .= $newsResults . $announcementResults;

// ========== 7. ACHIEVEMENT/PRESTASI QUERY ==========
$prestasiResults = "";

// 7a. Latest achievements - "prestasi terbaru", "ada prestasi apa"
if (preg_match('/(prestasi|achievement|pencapaian).*(terbaru|terakhir|baru|terkini)/i', $message) ||
    preg_match('/(terbaru|terakhir|baru).*(prestasi|achievement|pencapaian)/i', $message) ||
    stripos($lowerMessage, 'ada prestasi apa') !== false) {
    
    $prestasiQuery = @mysqli_query($koneksi, "SELECT nama_prestasi, tingkat, tahun FROM prestasi ORDER BY tahun DESC, id DESC LIMIT 5");
    
    if ($prestasiQuery && mysqli_num_rows($prestasiQuery) > 0) {
        $prestasiResults .= "\n\n🏆 PRESTASI TERBARU SMAN 1 BENGKALIS:";
        $idx = 1;
        while ($p = mysqli_fetch_assoc($prestasiQuery)) {
            $tingkat = ucfirst($p['tingkat'] ?? 'Umum');
            $prestasiResults .= "\n$idx. \"" . $p['nama_prestasi'] . "\" - Tingkat $tingkat (" . $p['tahun'] . ")";
            $idx++;
        }
        $prestasiResults .= "\n\nUntuk prestasi lengkap, kunjungi halaman prestasi.php";
    } else {
        $prestasiResults .= "\n\n🏆 Belum ada data prestasi.";
    }
}

// 7b. Achievement by level - "prestasi tingkat nasional/provinsi/kabupaten"
if (preg_match('/(prestasi|achievement).*(nasional|provinsi|kabupaten|kota|internasional)/i', $message, $levelMatch) ||
    preg_match('/(nasional|provinsi|kabupaten|kota|internasional).*(prestasi|achievement)/i', $message, $levelMatch2)) {
    
    $level = '';
    foreach (['internasional', 'nasional', 'provinsi', 'kabupaten', 'kota'] as $l) {
        if (stripos($message, $l) !== false) {
            $level = $l;
            break;
        }
    }
    
    if (!empty($level)) {
        $escapedLevel = mysqli_real_escape_string($koneksi, $level);
        $levelQuery = @mysqli_query($koneksi, "SELECT nama_prestasi, tahun FROM prestasi WHERE LOWER(tingkat) = LOWER('$escapedLevel') ORDER BY tahun DESC LIMIT 5");
        
        if ($levelQuery && mysqli_num_rows($levelQuery) > 0) {
            $prestasiResults .= "\n\n🏆 PRESTASI TINGKAT " . strtoupper($level) . ":";
            $idx = 1;
            while ($p = mysqli_fetch_assoc($levelQuery)) {
                $prestasiResults .= "\n$idx. \"" . $p['nama_prestasi'] . "\" (" . $p['tahun'] . ")";
                $idx++;
            }
        } else {
            $prestasiResults .= "\n\n🏆 Belum ada prestasi tingkat $level.";
        }
    }
}

// 7c. Achievement search - "cari prestasi olimpiade"
if (preg_match('/(cari prestasi|prestasi tentang|achievement tentang)\s+(.+)/i', $message, $prestasiMatch)) {
    $prestasiKeyword = trim($prestasiMatch[2]);
    $prestasiKeyword = preg_replace('/[?!.,\'"]/u', '', $prestasiKeyword);
    
    if (!empty($prestasiKeyword)) {
        $escapedPrestasi = mysqli_real_escape_string($koneksi, $prestasiKeyword);
        $prestasiSearchQuery = @mysqli_query($koneksi, "
            SELECT nama_prestasi, tingkat, tahun 
            FROM prestasi 
            WHERE LOWER(nama_prestasi) LIKE LOWER('%$escapedPrestasi%')
            ORDER BY tahun DESC
            LIMIT 5
        ");
        
        if ($prestasiSearchQuery && mysqli_num_rows($prestasiSearchQuery) > 0) {
            $prestasiResults .= "\n\n🏆 PRESTASI TERKAIT '$prestasiKeyword':";
            $idx = 1;
            while ($p = mysqli_fetch_assoc($prestasiSearchQuery)) {
                $tingkat = ucfirst($p['tingkat'] ?? 'Umum');
                $prestasiResults .= "\n$idx. \"" . $p['nama_prestasi'] . "\" - Tingkat $tingkat (" . $p['tahun'] . ")";
                $idx++;
            }
        } else {
            $prestasiResults .= "\n\n🏆 Tidak ditemukan prestasi terkait '$prestasiKeyword'.";
        }
    }
}

// 7d. Achievement count
if (preg_match('/(berapa|jumlah|total).*(prestasi|achievement|pencapaian)/i', $message) ||
    preg_match('/(prestasi|achievement|pencapaian).*(berapa|jumlah|total)/i', $message)) {
    
    $countQuery = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM prestasi");
    if ($countQuery) {
        $count = mysqli_fetch_assoc($countQuery)['total'];
        $prestasiResults .= "\n\n🏆 Total prestasi SMAN 1 Bengkalis: $count prestasi.";
    }
}

// Add prestasi results
$libraryResults .= $prestasiResults;

// ========== 8. EKSTRAKURIKULER QUERY ==========
$ekskulResults = "";

// Helper: Determine column name (nama_ekskul or judul)
$ekskulColumn = 'nama_ekskul';
$checkCol = @mysqli_query($koneksi, "SHOW COLUMNS FROM ekstrakurikuler LIKE 'judul'");
if ($checkCol && mysqli_num_rows($checkCol) > 0) {
    $ekskulColumn = 'judul';
}

// 8a. List all ekskul or count - "ekskul apa saja", "ada berapa ekskul", "daftar eskul"
if (preg_match('/(eskul|ekskul|ekstrakurikuler|kegiatan siswa).*(apa saja|apa aja|ada apa|daftar|list)/i', $message) ||
    preg_match('/(apa saja|daftar|list).*(eskul|ekskul|ekstrakurikuler)/i', $message) ||
    preg_match('/(ada berapa|berapa).*(eskul|ekskul|ekstrakurikuler)/i', $message) ||
    preg_match('/(eskul|ekskul|ekstrakurikuler).*(berapa|jumlah|total)/i', $message) ||
    stripos($lowerMessage, 'daftar eskul') !== false ||
    stripos($lowerMessage, 'daftar ekskul') !== false ||
    stripos($lowerMessage, 'ada ekskul apa') !== false ||
    stripos($lowerMessage, 'eskul di sekolah') !== false ||
    stripos($lowerMessage, 'ekskul di sekolah') !== false) {
    
    // Get count first
    $countQ = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ekstrakurikuler");
    $totalCount = 0;
    if ($countQ) {
        $totalCount = mysqli_fetch_assoc($countQ)['total'];
    }
    
    // Get list
    $ekskulQuery = @mysqli_query($koneksi, "SELECT $ekskulColumn as nama, deskripsi FROM ekstrakurikuler ORDER BY $ekskulColumn ASC LIMIT 15");
    
    if ($ekskulQuery && mysqli_num_rows($ekskulQuery) > 0) {
        $ekskulResults .= "\n\n⚽ EKSTRAKURIKULER SMAN 1 BENGKALIS:";
        $ekskulResults .= "\nTotal: $totalCount ekstrakurikuler";
        $ekskulResults .= "\n\nDaftar Ekskul:";
        $idx = 1;
        while ($e = mysqli_fetch_assoc($ekskulQuery)) {
            $nama = $e['nama'] ?: 'Tidak ada nama';
            $deskripsi = $e['deskripsi'] ? ' - ' . substr(strip_tags($e['deskripsi']), 0, 50) . '...' : '';
            $ekskulResults .= "\n$idx. " . $nama . $deskripsi;
            $idx++;
        }
        $ekskulResults .= "\n\nUntuk info lengkap, kunjungi halaman ekstrakurikuler.php";
    } else {
        $ekskulResults .= "\n\n⚽ Total ekstrakurikuler: $totalCount. Belum ada data detail ekstrakurikuler.";
    }
}

// 8b. Search ekskul - "ekskul basket", "eskul pramuka", "info ekskul pramuka"
if (preg_match('/(eskul|ekskul|ekstrakurikuler)\s+(\w+)/i', $message, $ekskulMatch) ||
    preg_match('/(info|tentang)\s+(eskul|ekskul|ekstrakurikuler)\s+(\w+)/i', $message, $ekskulMatch2)) {
    
    $ekskulKeyword = '';
    if (!empty($ekskulMatch[2])) {
        $ekskulKeyword = trim($ekskulMatch[2]);
    } elseif (!empty($ekskulMatch2[3])) {
        $ekskulKeyword = trim($ekskulMatch2[3]);
    }
    
    // Skip common words
    $skipWords = ['apa', 'saja', 'aja', 'ada', 'yang', 'di', 'dan', 'atau', 'berapa', 'sekolah', 'ini'];
    if (!empty($ekskulKeyword) && !in_array(strtolower($ekskulKeyword), $skipWords)) {
        $escapedEkskul = mysqli_real_escape_string($koneksi, $ekskulKeyword);
        $ekskulSearchQuery = @mysqli_query($koneksi, "
            SELECT $ekskulColumn as nama, deskripsi 
            FROM ekstrakurikuler 
            WHERE LOWER($ekskulColumn) LIKE LOWER('%$escapedEkskul%')
               OR LOWER(deskripsi) LIKE LOWER('%$escapedEkskul%')
            LIMIT 3
        ");
        
        if ($ekskulSearchQuery && mysqli_num_rows($ekskulSearchQuery) > 0) {
            $ekskulResults .= "\n\n⚽ INFO EKSKUL '$ekskulKeyword':";
            while ($e = mysqli_fetch_assoc($ekskulSearchQuery)) {
                $ekskulResults .= "\n\n📌 " . $e['nama'];
                if ($e['deskripsi']) {
                    $desc = substr(strip_tags($e['deskripsi']), 0, 100);
                    $ekskulResults .= "\n   " . $desc . (strlen($e['deskripsi']) > 100 ? '...' : '');
                }
            }
        } else {
            $ekskulResults .= "\n\n⚽ Tidak ditemukan ekskul terkait '$ekskulKeyword'.";
        }
    }
}

// Add ekskul results
$libraryResults .= $ekskulResults;

// ========== 9. FOTO/GALERI QUERY ==========
$fotoResults = "";

// 9a. List all foto or count - "foto apa saja", "ada berapa foto", "galeri"
if (preg_match('/(foto|galeri|gambar).*(apa saja|apa aja|ada apa|daftar|list)/i', $message) ||
    preg_match('/(apa saja|daftar|list).*(foto|galeri|gambar)/i', $message) ||
    preg_match('/(ada berapa|berapa).*(foto|galeri|gambar)/i', $message) ||
    preg_match('/(foto|galeri|gambar).*(berapa|jumlah|total)/i', $message) ||
    stripos($lowerMessage, 'judul foto') !== false ||
    stripos($lowerMessage, 'apa judulnya') !== false ||
    stripos($lowerMessage, 'foto di galeri') !== false) {
    
    // Get count first
    $countQ = @mysqli_query($koneksi, "SELECT COUNT(*) as total FROM foto");
    $totalCount = 0;
    if ($countQ) {
        $totalCount = mysqli_fetch_assoc($countQ)['total'];
    }
    
    // Get list
    $fotoQuery = @mysqli_query($koneksi, "SELECT judul, tanggal_upload FROM foto ORDER BY tanggal_upload DESC LIMIT 10");
    
    if ($fotoQuery && mysqli_num_rows($fotoQuery) > 0) {
        $fotoResults .= "\n\n📷 GALERI FOTO SMAN 1 BENGKALIS:";
        $fotoResults .= "\nTotal: $totalCount foto";
        $fotoResults .= "\n\nDaftar Foto:";
        $idx = 1;
        while ($f = mysqli_fetch_assoc($fotoQuery)) {
            $judul = $f['judul'] ?: 'Tidak ada judul';
            $tanggal = $f['tanggal_upload'] ? date('d M Y', strtotime($f['tanggal_upload'])) : '';
            $fotoResults .= "\n$idx. " . $judul . ($tanggal ? " ($tanggal)" : '');
            $idx++;
        }
        $fotoResults .= "\n\nUntuk melihat foto, kunjungi halaman foto.php";
    } else {
        $fotoResults .= "\n\n📷 Total foto di galeri: $totalCount. Belum ada foto.";
    }
}

// 9b. Search foto - "foto upacara", "galeri olahraga"
if (preg_match('/(foto|galeri|gambar)\s+(\w+)/i', $message, $fotoMatch)) {
    
    $fotoKeyword = '';
    if (!empty($fotoMatch[2])) {
        $fotoKeyword = trim($fotoMatch[2]);
    }
    
    // Skip common words
    $skipWords = ['apa', 'saja', 'aja', 'ada', 'yang', 'di', 'dan', 'atau', 'berapa', 'sekolah', 'ini', 'nya'];
    if (!empty($fotoKeyword) && !in_array(strtolower($fotoKeyword), $skipWords)) {
        $escapedFoto = mysqli_real_escape_string($koneksi, $fotoKeyword);
        $fotoSearchQuery = @mysqli_query($koneksi, "
            SELECT judul, tanggal_upload 
            FROM foto 
            WHERE LOWER(judul) LIKE LOWER('%$escapedFoto%')
            LIMIT 5
        ");
        
        if ($fotoSearchQuery && mysqli_num_rows($fotoSearchQuery) > 0) {
            $fotoResults .= "\n\n📷 FOTO '$fotoKeyword':";
            $idx = 1;
            while ($f = mysqli_fetch_assoc($fotoSearchQuery)) {
                $tanggal = $f['tanggal_upload'] ? date('d M Y', strtotime($f['tanggal_upload'])) : '';
                $fotoResults .= "\n$idx. " . $f['judul'] . ($tanggal ? " ($tanggal)" : '');
                $idx++;
            }
        } else {
            $fotoResults .= "\n\n📷 Tidak ditemukan foto terkait '$fotoKeyword'.";
        }
    }
}

// Add foto results
$libraryResults .= $fotoResults;

// ========== 10. UNIFIED SMART SEARCH ==========

/**
 * Unified Search - Mencari dari semua tabel sekaligus
 */
function unifiedSearch($koneksi, $keyword) {
    $results = [];
    $escapedKeyword = mysqli_real_escape_string($koneksi, $keyword);
    
    // Search berita
    $q = @mysqli_query($koneksi, "SELECT 'berita' as type, id, judul as title, SUBSTRING(isi, 1, 100) as excerpt FROM berita WHERE LOWER(judul) LIKE LOWER('%$escapedKeyword%') OR LOWER(isi) LIKE LOWER('%$escapedKeyword%') ORDER BY id DESC LIMIT 2");
    while ($q && $r = mysqli_fetch_assoc($q)) { $results[] = $r; }
    
    // Search pengumuman
    $q = @mysqli_query($koneksi, "SELECT 'pengumuman' as type, id, judul as title, SUBSTRING(isi, 1, 100) as excerpt FROM pengumuman WHERE LOWER(judul) LIKE LOWER('%$escapedKeyword%') OR LOWER(isi) LIKE LOWER('%$escapedKeyword%') ORDER BY id DESC LIMIT 2");
    while ($q && $r = mysqli_fetch_assoc($q)) { $results[] = $r; }
    
    // Search prestasi
    $q = @mysqli_query($koneksi, "SELECT 'prestasi' as type, id, judul as title, SUBSTRING(isi, 1, 100) as excerpt FROM prestasi WHERE LOWER(judul) LIKE LOWER('%$escapedKeyword%') OR LOWER(isi) LIKE LOWER('%$escapedKeyword%') ORDER BY id DESC LIMIT 2");
    while ($q && $r = mysqli_fetch_assoc($q)) { $results[] = $r; }
    
    // Search buku
    $q = @mysqli_query($koneksi, "SELECT 'buku' as type, id, judul as title, CONCAT(pengarang, ' - ', COALESCE(kategori, 'Umum')) as excerpt FROM buku WHERE LOWER(judul) LIKE LOWER('%$escapedKeyword%') OR LOWER(pengarang) LIKE LOWER('%$escapedKeyword%') OR LOWER(kategori) LIKE LOWER('%$escapedKeyword%') ORDER BY id DESC LIMIT 2");
    while ($q && $r = mysqli_fetch_assoc($q)) { $results[] = $r; }
    
    // Search ekstrakurikuler - check column name first
    $ekskulCol = 'nama_ekskul';
    $checkCol = @mysqli_query($koneksi, "SHOW COLUMNS FROM ekstrakurikuler LIKE 'judul'");
    if ($checkCol && mysqli_num_rows($checkCol) > 0) {
        $ekskulCol = 'judul';
    }
    $q = @mysqli_query($koneksi, "SELECT 'ekskul' as type, id, $ekskulCol as title, SUBSTRING(deskripsi, 1, 100) as excerpt FROM ekstrakurikuler WHERE LOWER($ekskulCol) LIKE LOWER('%$escapedKeyword%') OR LOWER(deskripsi) LIKE LOWER('%$escapedKeyword%') LIMIT 2");
    while ($q && $r = mysqli_fetch_assoc($q)) { $results[] = $r; }
    
    // Search foto
    $q = @mysqli_query($koneksi, "SELECT 'foto' as type, id, judul as title, '' as excerpt FROM foto WHERE LOWER(judul) LIKE LOWER('%$escapedKeyword%') ORDER BY id DESC LIMIT 2");
    while ($q && $r = mysqli_fetch_assoc($q)) { $results[] = $r; }
    
    return $results;
}

/**
 * Extract Keywords - Hapus kata-kata umum dari pertanyaan user
 */
function extractKeywords($message) {
    $stopWords = ['apa', 'ini', 'itu', 'yang', 'dan', 'atau', 'di', 'ke', 'dari', 'ada', 'tidak', 'saya', 'kamu', 'mau', 'ingin', 'cari', 'tentang', 'mengenai', 'soal', 'tolong', 'bisa', 'gimana', 'bagaimana', 'dimana', 'kapan', 'siapa', 'kenapa', 'mengapa', 'apakah', 'boleh', 'minta', 'kasih', 'tahu', 'info', 'informasi', 'dengan', 'untuk', 'pada', 'ya', 'dong', 'nih', 'deh', 'sih', 'lah', 'kan'];
    
    $words = preg_split('/\s+/', strtolower($message));
    $keywords = array_filter($words, function($w) use ($stopWords) {
        $w = preg_replace('/[?!.,\'"]/u', '', $w);
        return strlen($w) > 2 && !in_array($w, $stopWords);
    });
    
    return array_values($keywords);
}

// Smart Search Trigger
$smartSearchResults = "";
$smartSearchTriggered = false;

// Deteksi pertanyaan pencarian umum
if (preg_match('/(cari|temukan|search|find|carikan)\s+(.+)/i', $message, $searchMatch) ||
    preg_match('/(ada|punya).*(tentang|mengenai|soal)\s+(.+)/i', $message, $searchMatch2) ||
    preg_match('/^(apa|gimana|bagaimana).+(tentang|mengenai|soal)\s+(.+)/i', $message, $searchMatch3) ||
    preg_match('/info\s+(tentang\s+)?(.+)/i', $message, $searchMatch4)) {
    
    // Ekstrak keyword dari berbagai pola
    $searchTerm = '';
    if (!empty($searchMatch[2])) {
        $searchTerm = $searchMatch[2];
    } elseif (!empty($searchMatch2[3])) {
        $searchTerm = $searchMatch2[3];
    } elseif (!empty($searchMatch3[3])) {
        $searchTerm = $searchMatch3[3];
    } elseif (!empty($searchMatch4[2])) {
        $searchTerm = $searchMatch4[2];
    }
    
    $searchTerm = preg_replace('/[?!.,\'\"]/u', '', trim($searchTerm));
    
    // Hindari search jika keyword terlalu pendek atau sudah di-handle oleh query spesifik
    $specificTopics = ['buku', 'berita', 'pengumuman', 'prestasi', 'ekskul', 'ekstrakurikuler', 'foto', 'galeri', 'jam', 'alamat', 'kontak', 'telepon'];
    $isSpecificQuery = false;
    foreach ($specificTopics as $topic) {
        if (stripos($lowerMessage, $topic) !== false) {
            $isSpecificQuery = true;
            break;
        }
    }
    
    if (!empty($searchTerm) && strlen($searchTerm) > 2 && !$isSpecificQuery) {
        $smartSearchTriggered = true;
        $searchResults = unifiedSearch($koneksi, $searchTerm);
        
        if (!empty($searchResults)) {
            $smartSearchResults .= "\n\n🔍 HASIL PENCARIAN CERDAS untuk '$searchTerm':";
            
            $typeLabels = [
                'berita' => '📰 Berita',
                'pengumuman' => '📢 Pengumuman', 
                'prestasi' => '🏆 Prestasi',
                'buku' => '📚 Buku',
                'ekskul' => '⚽ Ekskul',
                'foto' => '📷 Foto'
            ];
            
            $typePages = [
                'berita' => 'news.php',
                'pengumuman' => 'pengumuman.php',
                'prestasi' => 'prestasi.php',
                'buku' => 'perpustakaan.php',
                'ekskul' => 'ekstrakurikuler.php',
                'foto' => 'foto.php'
            ];
            
            $idx = 1;
            $foundTypes = [];
            foreach ($searchResults as $r) {
                $label = $typeLabels[$r['type']] ?? ucfirst($r['type']);
                $smartSearchResults .= "\n$idx. [$label] " . $r['title'];
                if (!empty($r['excerpt'])) {
                    $excerptClean = substr(strip_tags($r['excerpt']), 0, 80);
                    if (strlen($r['excerpt']) > 80) $excerptClean .= "...";
                    $smartSearchResults .= "\n   " . $excerptClean;
                }
                $foundTypes[$r['type']] = $typePages[$r['type']] ?? '';
                $idx++;
            }
            
            // Navigation hints
            $smartSearchResults .= "\n\n📍 Halaman terkait:";
            foreach ($foundTypes as $type => $page) {
                if ($page) {
                    $label = $typeLabels[$type] ?? ucfirst($type);
                    $smartSearchResults .= "\n- $label: $page";
                }
            }
        } else {
            // Jika tidak ada hasil, coba ekstrak keyword dan cari lagi
            $keywords = extractKeywords($searchTerm);
            $fallbackResults = [];
            
            foreach ($keywords as $kw) {
                if (strlen($kw) > 3) {
                    $kwResults = unifiedSearch($koneksi, $kw);
                    $fallbackResults = array_merge($fallbackResults, $kwResults);
                }
            }
            
            if (!empty($fallbackResults)) {
                // Hapus duplikat
                $unique = [];
                foreach ($fallbackResults as $r) {
                    $key = $r['type'] . '_' . $r['id'];
                    if (!isset($unique[$key])) {
                        $unique[$key] = $r;
                    }
                }
                $fallbackResults = array_values($unique);
                
                $smartSearchResults .= "\n\n🔍 Tidak ditemukan hasil persis untuk '$searchTerm', tapi mungkin ini yang dicari:";
                $idx = 1;
                foreach (array_slice($fallbackResults, 0, 5) as $r) {
                    $label = $typeLabels[$r['type']] ?? ucfirst($r['type']);
                    $smartSearchResults .= "\n$idx. [$label] " . $r['title'];
                    $idx++;
                }
            } else {
                $smartSearchResults .= "\n\n🔍 Tidak ditemukan hasil untuk '$searchTerm'. Coba gunakan kata kunci lain atau tanyakan dengan cara berbeda.";
            }
        }
    }
}

// Add smart search results
$libraryResults .= $smartSearchResults;

// ========== SAFE DATABASE QUERIES ==========
$realTimeData = "\n\nDATA WEBSITE:" . $libraryResults;

// Function to safely count table rows
function safeTableCount($conn, $table) {
    // Check if table exists first
    $check = @mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (!$check || mysqli_num_rows($check) == 0) {
        return false; // Table doesn't exist
    }
    
    $result = @mysqli_query($conn, "SELECT COUNT(*) as total FROM `$table`");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)$row['total'];
    }
    return 0;
}

// Perpustakaan (buku table)
$bukuCount = safeTableCount($koneksi, 'buku');
if ($bukuCount !== false) {
    $realTimeData .= "\n- PERPUSTAKAAN: $bukuCount buku tersedia";
    
    // Get categories
    $catQuery = @mysqli_query($koneksi, "SELECT kategori, COUNT(*) as jumlah FROM buku WHERE kategori IS NOT NULL AND kategori != '' GROUP BY kategori");
    if ($catQuery && mysqli_num_rows($catQuery) > 0) {
        $cats = [];
        while ($cat = mysqli_fetch_assoc($catQuery)) {
            $cats[] = $cat['kategori'] . " (" . $cat['jumlah'] . ")";
        }
        $realTimeData .= "\n- Kategori buku: " . implode(", ", $cats);
    }
    
    // Get 1 latest book
    $latestBook = @mysqli_query($koneksi, "SELECT judul, pengarang, kategori FROM buku ORDER BY id DESC LIMIT 1");
    if ($latestBook && mysqli_num_rows($latestBook) > 0) {
        $book = mysqli_fetch_assoc($latestBook);
        $realTimeData .= "\n- BUKU TERBARU: \"" . $book['judul'] . "\" oleh " . $book['pengarang'] . " (Kategori: " . ($book['kategori'] ?: 'Umum') . ")";
    }
    
    // Get 3 popular books
    $popularBooks = @mysqli_query($koneksi, "SELECT judul, pengarang FROM buku ORDER BY view_count DESC LIMIT 3");
    if ($popularBooks && mysqli_num_rows($popularBooks) > 0) {
        $popList = [];
        while ($pb = mysqli_fetch_assoc($popularBooks)) {
            $popList[] = "\"" . $pb['judul'] . "\"";
        }
        $realTimeData .= "\n- Buku populer: " . implode(", ", $popList);
    }
}

// Other tables (only if they exist)
$beritaCount = safeTableCount($koneksi, 'berita');
if ($beritaCount !== false) {
    $realTimeData .= "\n- BERITA: $beritaCount artikel";
    
    // Get only 1 latest news
    $latestNews = @mysqli_query($koneksi, "SELECT judul, isi FROM berita ORDER BY id DESC LIMIT 1");
    if ($latestNews && mysqli_num_rows($latestNews) > 0) {
        $news = mysqli_fetch_assoc($latestNews);
        $excerpt = substr(strip_tags($news['isi']), 0, 150) . '...';
        $realTimeData .= "\n- BERITA TERBARU: \"" . $news['judul'] . "\" - " . $excerpt;
    }
}

$pengumumanCount = safeTableCount($koneksi, 'pengumuman');
if ($pengumumanCount !== false) $realTimeData .= "\n- PENGUMUMAN: $pengumumanCount";

$prestasiCount = safeTableCount($koneksi, 'prestasi');
if ($prestasiCount !== false) $realTimeData .= "\n- PRESTASI: $prestasiCount";

$ekskulCount = safeTableCount($koneksi, 'ekstrakurikuler');
if ($ekskulCount !== false) $realTimeData .= "\n- EKSTRAKURIKULER: $ekskulCount";

$fotoCount = safeTableCount($koneksi, 'foto');
if ($fotoCount !== false) $realTimeData .= "\n- FOTO: $fotoCount";

// Combine prompts
$fullPrompt = CHATBOT_SYSTEM_PROMPT . $realTimeData;

// ========== CALL GROQ API ==========
if (!function_exists('curl_init')) {
    echo json_encode(['error' => 'cURL not available']);
    exit;
}

$ch = curl_init(GROQ_API_URL);

// Build messages array with history
$messages = [
    ['role' => 'system', 'content' => $fullPrompt]
];

// Add conversation history (limit to last 10 for token management)
$historyLimit = array_slice($history, -10);
foreach ($historyLimit as $msg) {
    if (isset($msg['role']) && isset($msg['content'])) {
        // Validate role
        $role = in_array($msg['role'], ['user', 'assistant']) ? $msg['role'] : 'user';
        $messages[] = ['role' => $role, 'content' => $msg['content']];
    }
}

// Add current message
$messages[] = ['role' => 'user', 'content' => $message];

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . GROQ_API_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => GROQ_MODEL,
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 500
    ]),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo json_encode(['error' => 'Connection failed: ' . $error]);
    exit;
}

if ($httpCode !== 200) {
    $errData = json_decode($response, true);
    echo json_encode(['error' => 'API error', 'code' => $httpCode, 'details' => $errData]);
    exit;
}

$data = json_decode($response, true);

if (isset($data['choices'][0]['message']['content'])) {
    $aiResponse = $data['choices'][0]['message']['content'];
    $aiResponse = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $aiResponse);
    $aiResponse = nl2br($aiResponse);
    
    echo json_encode(['success' => true, 'response' => $aiResponse]);
} else {
    echo json_encode(['error' => 'Invalid AI response']);
}
?>
