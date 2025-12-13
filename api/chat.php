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

if (empty($message)) {
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// ========== SAFE DATABASE QUERIES ==========
$realTimeData = "\n\nDATA WEBSITE:";

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

$ekskulCount = safeTableCount($koneksi, 'ekskul');
if ($ekskulCount !== false) $realTimeData .= "\n- EKSKUL: $ekskulCount";

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

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . GROQ_API_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => GROQ_MODEL,
        'messages' => [
            ['role' => 'system', 'content' => $fullPrompt],
            ['role' => 'user', 'content' => $message]
        ],
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
