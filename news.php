<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="css/fab.css?v=1">
    <link rel="stylesheet" href="css/chatbot.css?v=5">
</head>
<body>
    <div class="top-bar"><div class="container"><div class="top-bar-left"><a href="#" class="top-bar-item"><i class="fas fa-calendar-alt"></i><span>Senin - Jumat: 07.00 - 15.00 WIB</span></a></div><div class="top-bar-right"><a href="tel:+62766123456" class="top-bar-item"><i class="fas fa-phone"></i> (0766) 21234</a><a href="mailto:info@sman1bengkalis.sch.id" class="top-bar-item"><i class="fas fa-envelope"></i> info@sman1bengkalis.sch.id</a><div class="top-bar-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div></div></div></div>

    <header class="header" id="header"><div class="container"><a href="index.php" class="logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h1>SMAN 1 Bengkalis</h1><span>Unggul dalam Prestasi</span></div></a><nav class="nav" id="nav"><ul class="nav-menu" id="navMenu"><li class="nav-item"><a href="index.php" class="nav-link">BERANDA</a></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">PROFIL <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="profile.php">Sambutan Kepala Sekolah</a></li><li><a href="karyawan.php">Guru & Staff</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">KESISWAAN <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li><li><a href="prestasi.php">Prestasi Siswa</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">GALERI <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="foto.php">Foto</a></li></ul></li><li class="nav-item active"><a href="news.php" class="nav-link">BERITA</a></li><li class="nav-item"><a href="contact.php" class="nav-link">KONTAK</a></li></ul><button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button><button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button></nav></div></header>

    <?php
    // Handle search query
    $search_query = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, trim($_GET['q'])) : '';
    $search_condition = $search_query ? "WHERE judul LIKE '%$search_query%' OR isi LIKE '%$search_query%'" : '';
    ?>

    <section style="background: linear-gradient(135deg, rgba(0,64,41,0.9), rgba(0,64,41,0.7)), url('https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=1600'); background-size: cover; padding: 8rem 0; text-align: center; color: white;">
        <div class="container">
            <h1 style="font-size: 4rem; margin-bottom: 1rem;"><?php echo $search_query ? 'Hasil Pencarian' : 'Berita & Kegiatan'; ?></h1>
            <p style="font-size: 1.8rem; opacity: 0.9;">
                <?php 
                if($search_query) {
                    echo 'Menampilkan hasil untuk: "<strong>' . htmlspecialchars($search_query) . '</strong>"';
                } else {
                    echo 'Informasi terbaru seputar SMAN 1 Bengkalis';
                }
                ?>
            </p>
            <?php if($search_query): ?>
            <a href="news.php" style="display:inline-block; margin-top:20px; padding:12px 25px; background:white; color:#004029; border-radius:30px; text-decoration:none; font-weight:600;"><i class="fas fa-arrow-left"></i> Lihat Semua Berita</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="news-section" style="padding: 6rem 0;">
        <div class="container">
            <div class="news-grid" style="grid-template-columns: 1fr;">
                <div class="news-main" style="max-width: 100%;">
                    <div class="news-list" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 3rem;">
                        <?php
                        $jumlahDataPerHalaman = 6;
                        $query_count = mysqli_query($koneksi, "SELECT id FROM berita $search_condition");
                        $total_records = mysqli_num_rows($query_count);
                        $total_pages = ceil($total_records / $jumlahDataPerHalaman);
                        $page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                        $start = ($page - 1) * $jumlahDataPerHalaman;

                        $query = "SELECT * FROM berita $search_condition ORDER BY id DESC LIMIT $start, $jumlahDataPerHalaman";
                        $result = mysqli_query($koneksi, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $gambar = $row['gambar'] ? "uploads/" . $row['gambar'] : "https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=600&h=400&fit=crop";
                        ?>
                        <article class="news-card featured">
                            <div class="news-image">
                                <a href="detail_berita.php?id=<?php echo $row['id']; ?>">
                                    <img src="<?php echo $gambar; ?>" alt="<?php echo $row['judul']; ?>">
                                </a>
                                <div class="news-category">Berita</div>
                            </div>
                            <div class="news-content">
                                <div class="news-meta">
                                    <span><i class="far fa-calendar"></i> <?php echo date('d M Y', strtotime($row['tanggal_buat'])); ?></span>
                                    <span><i class="far fa-eye"></i> <?php echo $row['views'] ?? 0; ?> views</span>
                                </div>
                                <h4><a href="detail_berita.php?id=<?php echo $row['id']; ?>"><?php echo $row['judul']; ?></a></h4>
                                <p><?php echo substr(strip_tags($row['isi']), 0, 120); ?>...</p>
                                <a href="detail_berita.php?id=<?php echo $row['id']; ?>" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </article>
                        <?php
                            }
                        } else {
                            if($search_query) {
                                echo "<div style='text-align:center; padding: 4rem;'><i class='fas fa-search' style='font-size:4rem; color:#ddd; display:block; margin-bottom:20px;'></i><p style='font-size: 1.6rem; color:#666;'>Tidak ada berita dengan kata kunci \"<strong>" . htmlspecialchars($search_query) . "</strong>\"</p><a href='news.php' style='display:inline-block; margin-top:20px; padding:12px 25px; background:linear-gradient(135deg,#004029,#006644); color:white; border-radius:30px; text-decoration:none;'>Lihat Semua Berita</a></div>";
                            } else {
                                echo "<p style='text-align:center; font-size: 1.6rem;'>Belum ada berita.</p>";
                            }
                        }
                        ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                    <div style="text-align: center; margin-top: 4rem;">
                        <?php if ($page > 1): ?>
                            <a href="?halaman=<?php echo $page - 1; ?>" class="btn btn-outline" style="margin: 0 0.5rem;">&laquo; Sebelumnya</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?halaman=<?php echo $i; ?>" class="btn <?php echo ($i == $page) ? 'btn-primary' : 'btn-outline'; ?>" style="margin: 0 0.3rem;"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <a href="?halaman=<?php echo $page + 1; ?>" class="btn btn-outline" style="margin: 0 0.5rem;">Berikutnya &raquo;</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer"><div class="footer-main"><div class="container"><div class="footer-grid"><div class="footer-col footer-about"><div class="footer-logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h3>SMAN 1 Bengkalis</h3><span>Unggul dalam Prestasi</span></div></div><p>Sekolah menengah atas negeri yang berkomitmen mencetak generasi unggul.</p></div><div class="footer-col"><h4>Link Cepat</h4><ul class="footer-links"><li><a href="index.php">Beranda</a></li><li><a href="profile.php">Profil</a></li><li><a href="news.php">Berita</a></li><li><a href="contact.php">Kontak</a></li></ul></div><div class="footer-col"><h4>Kontak</h4><ul class="footer-contact"><li><i class="fas fa-map-marker-alt"></i><span>Jl. Lembaga, Senggoro</span></li><li><i class="fas fa-phone"></i><span>(0766) 21234</span></li></ul></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-bottom-content"><p>&copy; 2025 SMAN 1 Bengkalis.</p></div></div></div></footer>
    <button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>

    <!-- Floating Contact Button + Chatbot -->
    <div class="fab-container" id="fabContainer">
        <!-- Chatbot Window -->
        <div class="chatbot-container" id="chatbotContainer">
            <div class="chatbot-header">
                <div class="chatbot-header-info">
                    <div class="chatbot-avatar"><i class="fas fa-graduation-cap"></i></div>
                    <div class="chatbot-title">
                        <h4>SMAN 1 Bengkalis</h4>
                        <span class="chatbot-status"><i class="fas fa-circle"></i> Online</span>
                    </div>
                </div>
                <button class="chatbot-close" id="chatbotClose"><i class="fas fa-times"></i></button>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                <div class="chat-message bot">
                    <div class="message-avatar"><i class="fas fa-robot"></i></div>
                    <div class="message-content">
                        <p>Halo! 👋 Saya bisa membantu mencari berita di SMAN 1 Bengkalis. Silakan tanyakan tentang berita atau kegiatan sekolah!</p>
                    </div>
                </div>
            </div>
            <div class="chatbot-quick-replies" id="quickReplies">
                <button class="quick-reply" data-message="Berita terbaru apa saja">📰 Berita Terbaru</button>
                <button class="quick-reply" data-message="Ada berapa berita">📊 Jumlah Berita</button>
                <button class="quick-reply" data-message="Kegiatan sekolah">🎯 Kegiatan</button>
            </div>
            <div class="chatbot-input">
                <input type="text" id="chatInput" placeholder="Tanya tentang berita..." autocomplete="off">
                <button class="chatbot-send" id="chatSend"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>

        <!-- FAB Options -->
        <div class="fab-options">
            <button class="fab-option fab-chatbot" id="fabChatbot" title="Chatbot AI"><i class="fas fa-robot"></i></button>
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=info@sman1bengkalis.sch.id" target="_blank" class="fab-option fab-email" title="Email"><i class="fas fa-envelope"></i></a>
            <a href="https://wa.me/62766212340" target="_blank" class="fab-option fab-whatsapp" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
        <button class="fab-button" id="fabButton" title="Hubungi Kami">
            <i class="fas fa-comments"></i>
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script src="js/main.js?v=2"></script>
    <script src="js/fab.js?v=1"></script>
    <script src="js/chatbot.js?v=4"></script>
</body>
</html>
