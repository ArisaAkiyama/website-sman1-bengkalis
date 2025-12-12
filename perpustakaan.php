<?php
include 'koneksi.php';

// Auto-create buku table if not exists
$create_buku = "CREATE TABLE IF NOT EXISTS buku (
    id INT(11) NOT NULL AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    pengarang VARCHAR(255) NOT NULL,
    kategori VARCHAR(100) DEFAULT NULL,
    deskripsi TEXT,
    cover VARCHAR(255) DEFAULT NULL,
    file_buku VARCHAR(255) DEFAULT NULL,
    tahun_terbit INT(4) DEFAULT NULL,
    penerbit VARCHAR(255) DEFAULT NULL,
    view_count INT DEFAULT 0,
    download_count INT DEFAULT 0,
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($koneksi, $create_buku);

// Handle search parameters
$search_judul = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';
$search_kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';
$search_pengarang = isset($_GET['pengarang']) ? mysqli_real_escape_string($koneksi, $_GET['pengarang']) : '';

// Build WHERE clause
$where = "1=1";
if ($search_judul) $where .= " AND (judul LIKE '%$search_judul%' OR pengarang LIKE '%$search_judul%' OR deskripsi LIKE '%$search_judul%')";
if ($search_kategori) $where .= " AND kategori = '$search_kategori'";
if ($search_pengarang) $where .= " AND pengarang = '$search_pengarang'";

// Pagination
$limit = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total
$count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku WHERE $where");
$total_data = mysqli_fetch_assoc($count_query)['total'];
$total_pages = ceil($total_data / $limit);

// Get books
$books_query = mysqli_query($koneksi, "SELECT * FROM buku WHERE $where ORDER BY id DESC LIMIT $offset, $limit");

// Get unique authors for dropdown
$authors_query = mysqli_query($koneksi, "SELECT DISTINCT pengarang FROM buku WHERE pengarang != '' ORDER BY pengarang");

// Get categories count
$categories = ['Novel', 'Pendidikan', 'Sains & Teknologi', 'Sejarah', 'Agama', 'Bahasa', 'Buku Bacaan'];
$category_counts = [];
foreach ($categories as $cat) {
    $cat_escaped = mysqli_real_escape_string($koneksi, $cat);
    $cat_result = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM buku WHERE kategori = '$cat_escaped'");
    $category_counts[$cat] = mysqli_fetch_assoc($cat_result)['c'];
}

// Get popular books (most views)
$popular_query = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY view_count DESC, id DESC LIMIT 3");

// Stats
$total_books = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM buku"))['c'];
$total_authors = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT pengarang) as c FROM buku"))['c'];
$total_categories = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT kategori) as c FROM buku WHERE kategori IS NOT NULL AND kategori != ''"))['c'];

// Gradient classes for covers without images
$gradients = ['gradient-1', 'gradient-2', 'gradient-3', 'gradient-4', 'gradient-5', 'gradient-6'];
$icons = ['fa-book', 'fa-lightbulb', 'fa-coins', 'fa-heart', 'fa-flask', 'fa-calculator', 'fa-globe', 'fa-landmark'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - SMAN 1 Bengkalis</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Styles -->
    <link rel="stylesheet" href="css/perpustakaan.css?v=3">
</head>

<body>
    <!-- Header Navigation -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="perpustakaan.php" class="nav-brand">
                    <i class="fas fa-book-open"></i>
                    <span>Perpustakaan Digital</span>
                </a>
                <ul class="nav-menu">
                    <li><a href="perpustakaan.php" class="active"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="index.php"><i class="fas fa-globe"></i> Website Utama</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Kontak</a></li>
                </ul>

                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><i class="fas fa-book-reader"></i> Perpustakaan Digital</h1>
            <p>Akses ribuan buku dan e-book kapan saja, dimana saja</p>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <form class="search-form" method="GET" action="perpustakaan.php">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search_judul); ?>" placeholder="Cari judul, pengarang...">
                </div>
                <div class="search-select">
                    <select name="pengarang">
                        <option value="">Semua Pengarang</option>
                        <?php while($author = mysqli_fetch_assoc($authors_query)): ?>
                        <option value="<?php echo $author['pengarang']; ?>" <?php if($search_pengarang == $author['pengarang']) echo 'selected'; ?>><?php echo $author['pengarang']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="search-select">
                    <select name="kategori">
                        <option value="">Semua Kategori</option>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php if($search_kategori == $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-wrapper">
                <!-- Book Grid -->
                <section class="books-section">
                    <div class="section-header">
                        <h2><i class="fas fa-book"></i> Koleksi Buku</h2>
                        <span class="book-count">
                            <?php if($search_judul || $search_kategori || $search_pengarang): ?>
                            Ditemukan <?php echo $total_data; ?> buku
                            <?php else: ?>
                            Menampilkan <?php echo min($limit, $total_data); ?> dari <?php echo $total_data; ?> buku
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if(mysqli_num_rows($books_query) > 0): ?>
                    <div class="books-grid" id="booksGrid">
                        <?php 
                        $idx = 0;
                        while($book = mysqli_fetch_assoc($books_query)): 
                            $gradient_class = $gradients[$idx % count($gradients)];
                            $icon_class = $icons[$idx % count($icons)];
                            $idx++;
                        ?>
                        <!-- Book Card -->
                        <div class="book-card">
                            <div class="book-cover">
                                <?php if($book['cover']): ?>
                                <img src="uploads/<?php echo $book['cover']; ?>" alt="<?php echo htmlspecialchars($book['judul']); ?>">
                                <?php else: ?>
                                <div class="book-cover-placeholder <?php echo $gradient_class; ?>">
                                    <i class="fas <?php echo $icon_class; ?>"></i>
                                    <span><?php echo strtoupper($book['kategori'] ?: 'E-BOOK'); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="book-actions">
                                    <?php if($book['file_buku']): ?>
                                    <a href="uploads/<?php echo $book['file_buku']; ?>" target="_blank" class="btn-read" title="Baca"><i class="fas fa-eye"></i></a>
                                    <a href="uploads/<?php echo $book['file_buku']; ?>" download class="btn-download" title="Download"><i class="fas fa-download"></i></a>
                                    <?php else: ?>
                                    <span class="btn-read" style="opacity:0.5;" title="Tidak ada file"><i class="fas fa-eye-slash"></i></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="book-info">
                                <h3 class="book-title"><?php echo $book['judul']; ?></h3>
                                <p class="book-author"><i class="fas fa-user"></i> <?php echo $book['pengarang']; ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if($total_pages > 1): ?>
                    <div class="pagination">
                        <?php 
                        // Build query string for pagination
                        $query_params = [];
                        if($search_judul) $query_params['q'] = $search_judul;
                        if($search_kategori) $query_params['kategori'] = $search_kategori;
                        if($search_pengarang) $query_params['pengarang'] = $search_pengarang;
                        ?>
                        
                        <?php if($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($query_params, ['page' => $page - 1])); ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                        <?php else: ?>
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                        <?php endif; ?>
                        
                        <?php 
                        $start_pg = max(1, $page - 2);
                        $end_pg = min($total_pages, $page + 2);
                        for($i = $start_pg; $i <= $end_pg; $i++): 
                        ?>
                        <a href="?<?php echo http_build_query(array_merge($query_params, ['page' => $i])); ?>" class="page-btn <?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        
                        <?php if($end_pg < $total_pages): ?>
                        <span class="page-dots">...</span>
                        <a href="?<?php echo http_build_query(array_merge($query_params, ['page' => $total_pages])); ?>" class="page-btn"><?php echo $total_pages; ?></a>
                        <?php endif; ?>
                        
                        <?php if($page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($query_params, ['page' => $page + 1])); ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                        <?php else: ?>
                        <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <!-- No Books Found -->
                    <div style="text-align:center; padding:60px 20px; background:white; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                        <i class="fas fa-book-open" style="font-size:4rem; color:#ddd; margin-bottom:20px;"></i>
                        <h3 style="color:#666; margin-bottom:10px;">Tidak Ada Buku Ditemukan</h3>
                        <p style="color:#999; margin-bottom:20px;">
                            <?php if($search_judul || $search_kategori || $search_pengarang): ?>
                            Coba ubah kata kunci atau filter pencarian Anda.
                            <?php else: ?>
                            Belum ada buku yang ditambahkan ke perpustakaan.
                            <?php endif; ?>
                        </p>
                        <?php if($search_judul || $search_kategori || $search_pengarang): ?>
                        <a href="perpustakaan.php" style="display:inline-block; padding:12px 25px; background:linear-gradient(135deg, #004029, #006644); color:white; text-decoration:none; border-radius:10px; font-weight:600;">
                            <i class="fas fa-redo"></i> Reset Pencarian
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Categories -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-folder"></i> Kategori</h3>
                        <ul class="category-list">
                            <li><a href="perpustakaan.php" class="<?php if(!$search_kategori) echo 'active'; ?>"><i class="fas fa-chevron-right"></i> Semua <span><?php echo $total_books; ?></span></a></li>
                            <?php foreach($categories as $cat): ?>
                            <li><a href="perpustakaan.php?kategori=<?php echo urlencode($cat); ?>" class="<?php if($search_kategori == $cat) echo 'active'; ?>">
                                <i class="fas fa-chevron-right"></i> <?php echo $cat; ?>
                                <span><?php echo $category_counts[$cat]; ?></span>
                            </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Popular Books -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-fire"></i> Buku Populer</h3>
                        <ul class="popular-list">
                            <?php 
                            $rank = 1;
                            if(mysqli_num_rows($popular_query) > 0):
                            while($pop = mysqli_fetch_assoc($popular_query)): 
                            ?>
                            <li>
                                <span class="rank"><?php echo $rank++; ?></span>
                                <div class="popular-info">
                                    <strong><?php echo $pop['judul']; ?></strong>
                                    <small><?php echo $pop['pengarang']; ?></small>
                                </div>
                            </li>
                            <?php endwhile; else: ?>
                            <li style="padding:15px; color:#999; text-align:center;">Belum ada buku</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Stats -->
                    <div class="sidebar-card stats-card">
                        <h3><i class="fas fa-chart-bar"></i> Statistik</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $total_books; ?></span>
                                <span class="stat-label">Total Buku</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $total_authors; ?></span>
                                <span class="stat-label">Pengarang</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo $total_categories ?: count($categories); ?></span>
                                <span class="stat-label">Kategori</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo number_format(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(view_count) as c FROM buku"))['c'] ?: 0); ?></span>
                                <span class="stat-label">Pembaca</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <i class="fas fa-book-open"></i>
                    <span>Perpustakaan Digital SMAN 1 Bengkalis</span>
                </div>
                <p>&copy; <?php echo date('Y'); ?> SMAN 1 Bengkalis. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/perpustakaan.js"></script>
</body>

</html>
