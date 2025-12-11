<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Website Resmi SMAN 1 Bengkalis - Sekolah Menengah Atas Negeri terbaik di Bengkalis, Riau. Unggul dalam prestasi akademik dan non-akademik.">
    <meta name="keywords"
        content="SMAN 1 Bengkalis, SMA Negeri 1 Bengkalis, Sekolah Bengkalis, Pendidikan Bengkalis, Riau">
    <meta name="author" content="SMAN 1 Bengkalis">

    <title>SMAN 1 Bengkalis - Unggul dalam Prestasi</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500&family=Roboto+Slab:wght@400;500&display=swap"
        rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="css/fab.css?v=1">
</head>

<body>
    <!-- ========== TOP BAR (Hijau Tua) ========== -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-left">
                <a href="#" class="top-bar-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Senin - Jumat: 07.00 - 15.00 WIB</span>
                </a>
            </div>
            <div class="top-bar-right">
                <a href="tel:+62766123456" class="top-bar-item">
                    <i class="fas fa-phone"></i> (0766) 21234
                </a>
                <a href="mailto:info@sman1bengkalis.sch.id" class="top-bar-item">
                    <i class="fas fa-envelope"></i> info@sman1bengkalis.sch.id
                </a>
                <div class="top-bar-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== HEADER ========== -->
    <header class="header" id="header">
        <div class="container">
            <a href="index.php" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="logo-text">
                    <h1>SMAN 1 Bengkalis</h1>
                    <span>Unggul dalam Prestasi</span>
                </div>
            </a>

            <!-- Navigation -->
            <nav class="nav" id="nav">
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item active">
                        <a href="index.php" class="nav-link">BERANDA</a>
                    </li>
                    <li class="nav-item has-dropdown">
                        <a href="#" class="nav-link">PROFIL <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="profile.php">Sambutan Kepala Sekolah</a></li>
                            <li><a href="karyawan.php">Guru & Staff</a></li>
                        </ul>
                    </li>
                    <li class="nav-item has-dropdown">
                        <a href="#" class="nav-link">KESISWAAN <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li>
                            <li><a href="prestasi.php">Prestasi Siswa</a></li>
                        </ul>
                    </li>
                    <li class="nav-item has-dropdown">
                        <a href="#" class="nav-link">GALERI <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="foto.php">Foto</a></li>
                         
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="news.php" class="nav-link">BERITA</a>
                    </li>
                    <li class="nav-item">
                        <a href="contact.php" class="nav-link">KONTAK</a>
                    </li>
                </ul>

                <!-- Search Button -->
                <button class="search-btn" id="searchBtn">
                    <i class="fas fa-search"></i>
                </button>

                <!-- Mobile Menu Toggle -->
                <button class="nav-toggle" id="navToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- ========== HERO SLIDER ========== -->
    <section class="hero-slider" id="heroSlider">
        <div class="slide active"
            style="background-image: linear-gradient(135deg, rgba(0,64,41,0.8) 0%, rgba(0,64,41,0.4) 100%), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1600');">
            <div class="slide-content">
                <h1>Selamat Datang di</h1>
                <h2>SMAN 1 Bengkalis</h2>
                <p>Mencetak Generasi Unggul, Berkarakter, dan Berprestasi</p>
                <a href="profile.php" class="btn btn-primary">
                    <span>Jelajahi Sekolah Kami</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="slide"
            style="background-image: linear-gradient(135deg, rgba(0,64,41,0.8) 0%, rgba(0,64,41,0.4) 100%), url('https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1600');">
            <div class="slide-content">
                <h1>Pendaftaran PPDB</h1>
                <h2>Tahun Ajaran 2026/2027</h2>
                <p>Bergabunglah bersama kami untuk masa depan yang cerah</p>
                <a href="#" class="btn btn-primary">
                    <span>Daftar Sekarang</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="slide"
            style="background-image: linear-gradient(135deg, rgba(0,64,41,0.8) 0%, rgba(0,64,41,0.4) 100%), url('https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=1600');">
            <div class="slide-content">
                <h1>Prestasi Gemilang</h1>
                <h2>Tingkat Nasional & Internasional</h2>
                <p>Raihan prestasi siswa-siswi kami di berbagai bidang</p>
                <a href="prestasi.php" class="btn btn-primary">
                    <span>Lihat Prestasi</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Slider Controls -->
        <div class="slider-controls">
            <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
            <div class="slider-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
            <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

    <!-- ========== INFO BAR (Quick Links) ========== -->
    <section class="info-bar">
        <div class="container">
            <div class="info-grid">
                <a href="#" class="info-item">
                    <div class="info-icon"><i class="fas fa-user-graduate"></i></div>
                    <span>PPDB Online</span>
                </a>
                <a href="#" class="info-item">
                    <div class="info-icon"><i class="fas fa-book-open"></i></div>
                    <span>E-Learning</span>
                </a>
                <a href="#" class="info-item">
                    <div class="info-icon"><i class="fas fa-calendar-check"></i></div>
                    <span>Jadwal Pelajaran</span>
                </a>
                <a href="prestasi.php" class="info-item">
                    <div class="info-icon"><i class="fas fa-trophy"></i></div>
                    <span>Prestasi</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ========== KATA SAMBUTAN ========== -->
    <section class="welcome-section" id="welcome">
        <div class="container">
            <div class="section-header">
                <h2>Kata Sambutan</h2>
                <p>Kepala Sekolah SMAN 1 Bengkalis</p>
                <div class="divider"></div>
            </div>

            <div class="welcome-grid">
                <div class="welcome-image">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=500&fit=crop"
                        alt="Kepala Sekolah SMAN 1 Bengkalis">
                    <div class="welcome-badge">
                        <i class="fas fa-quote-left"></i>
                    </div>
                </div>
                <div class="welcome-content">
                    <h3>Dra. Mairustuti, M.Pd</h3>
                    <h4>Kepala Sekolah</h4>
                    <div class="welcome-text">
                        <p>Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
                        <p>Puji syukur kita panjatkan kehadirat Allah SWT, atas limpahan rahmat dan karunia-Nya sehingga
                            website SMAN 1 Bengkalis dapat hadir sebagai media informasi dan komunikasi antara sekolah
                            dengan masyarakat.</p>
                        <p>SMAN 1 Bengkalis berkomitmen untuk terus meningkatkan kualitas pendidikan dan menghasilkan
                            lulusan yang tidak hanya unggul dalam bidang akademik, tetapi juga memiliki karakter yang
                            kuat, berakhlak mulia, dan siap menghadapi tantangan masa depan.</p>
                        <p>Wassalamu'alaikum Warahmatullahi Wabarakatuh.</p>
                    </div>
                    <a href="profile.php" class="btn btn-outline">
                        <span>Selengkapnya</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== STATISTIK ========== -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" data-count="1250">0</div>
                    <div class="stat-label">Siswa Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="85">0</div>
                    <div class="stat-label">Guru & Staff</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-count="25">0</div>
                    <div class="stat-label">Ekstrakurikuler</div>
                </div>
                <div class="stat-item highlight">
                    <div class="stat-number" data-count="150">0</div>
                    <div class="stat-label">Prestasi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== PROGRAM UNGGULAN ========== -->
    <section class="programs-section" id="programs">
        <div class="container">
            <div class="section-header">
                <h2>Program Unggulan</h2>
                <p>Berbagai program pendidikan berkualitas untuk masa depan siswa</p>
                <div class="divider"></div>
            </div>

            <div class="programs-grid">
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h4>Program IPA</h4>
                    <p>Pembelajaran sains dan teknologi dengan fasilitas laboratorium modern untuk mengembangkan
                        kemampuan riset siswa.</p>
                    <a href="#" class="program-link">
                        Selengkapnya <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Program IPS</h4>
                    <p>Memahami dinamika sosial dan ekonomi dengan pendekatan praktis dan studi kasus terkini.</p>
                    <a href="#" class="program-link">
                        Selengkapnya <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h4>Literasi & Karakter</h4>
                    <p>Program pengembangan literasi dan pembentukan karakter siswa melalui berbagai kegiatan positif.
                    </p>
                    <a href="#" class="program-link">
                        Selengkapnya <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="program-card">
                    <div class="program-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Ekstrakurikuler</h4>
                    <p>Lebih dari 25 pilihan ekstrakurikuler untuk mengembangkan bakat dan minat siswa.</p>
                    <a href="ekstrakurikuler.php" class="program-link">
                        Selengkapnya <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== BERITA & PENGUMUMAN (DYNAMIC) ========== -->
    <section class="news-section" id="news">
        <div class="container">
            <div class="section-header">
                <h2>Berita & Pengumuman</h2>
                <p>Informasi terbaru seputar kegiatan dan pengumuman sekolah</p>
                <div class="divider"></div>
            </div>

            <div class="news-grid">
                <!-- Berita Utama -->
                <div class="news-main">
                    <h3 class="news-section-title">
                        <i class="fas fa-newspaper"></i> Berita Terbaru
                    </h3>

                    <div class="news-list">
                        <?php
                        $berita_query = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC LIMIT 3");
                        $first = true;
                        while ($berita = mysqli_fetch_array($berita_query)) {
                            $gambar = $berita['gambar'] ? "uploads/" . $berita['gambar'] : "https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=600&h=400&fit=crop";
                            $tanggal = date('d F Y', strtotime($berita['tanggal_buat']));
                            if ($first) {
                        ?>
                        <article class="news-card featured">
                            <div class="news-image">
                                <img src="<?php echo $gambar; ?>" alt="<?php echo $berita['judul']; ?>">
                                <div class="news-category">Berita</div>
                            </div>
                            <div class="news-content">
                                <div class="news-meta">
                                    <span><i class="far fa-calendar"></i> <?php echo $tanggal; ?></span>
                                    <span><i class="far fa-eye"></i> <?php echo $berita['views'] ?? 0; ?> views</span>
                                </div>
                                <h4><a href="detail_berita.php?id=<?php echo $berita['id']; ?>"><?php echo $berita['judul']; ?></a></h4>
                                <p><?php echo substr(strip_tags($berita['isi']), 0, 150); ?>...</p>
                                <a href="detail_berita.php?id=<?php echo $berita['id']; ?>" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </article>
                        <?php
                                $first = false;
                            } else {
                        ?>
                        <article class="news-card">
                            <div class="news-image">
                                <img src="<?php echo $gambar; ?>" alt="<?php echo $berita['judul']; ?>">
                            </div>
                            <div class="news-content">
                                <div class="news-meta">
                                    <span><i class="far fa-calendar"></i> <?php echo $tanggal; ?></span>
                                </div>
                                <h4><a href="detail_berita.php?id=<?php echo $berita['id']; ?>"><?php echo $berita['judul']; ?></a></h4>
                            </div>
                        </article>
                        <?php
                            }
                        }
                        ?>
                    </div>

                    <a href="news.php" class="btn btn-primary">
                        <span>Lihat Semua Berita</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- Pengumuman Sidebar -->
                <div class="news-sidebar">
                    <h3 class="news-section-title">
                        <i class="fas fa-bullhorn"></i> Pengumuman
                    </h3>

                    <div class="announcement-list">
                        <?php
                        $query_pengumuman = mysqli_query($koneksi, "SELECT * FROM pengumuman ORDER BY prioritas DESC, tanggal_buat DESC LIMIT 4");
                        if(mysqli_num_rows($query_pengumuman) > 0) {
                            while($p = mysqli_fetch_array($query_pengumuman)) {
                                $day = date('d', strtotime($p['tanggal_buat']));
                                $month = date('M', strtotime($p['tanggal_buat']));
                                $kategori = ($p['prioritas'] == 'penting') ? '⭐ Penting' : 'Umum';
                        ?>
                        <a href="pengumuman.php" class="announcement-item">
                            <div class="announcement-date">
                                <span class="day"><?php echo $day; ?></span>
                                <span class="month"><?php echo $month; ?></span>
                            </div>
                            <div class="announcement-content">
                                <h5><?php echo $p['judul']; ?></h5>
                                <span class="announcement-category"><?php echo $kategori; ?></span>
                            </div>
                        </a>
                        <?php 
                            }
                        } else { ?>
                        <p style="text-align: center; color: #888; padding: 20px;">Belum ada pengumuman.</p>
                        <?php } ?>
                    </div>

                    <a href="pengumuman.php" class="btn btn-secondary">
                        <span>Lihat Semua</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== PRESTASI (DYNAMIC) ========== -->
    <section class="achievements-section" id="achievements">
        <div class="container">
            <div class="section-header light">
                <h2>Prestasi Kami</h2>
                <p>Berbagai penghargaan yang telah diraih siswa dan sekolah</p>
                <div class="divider"></div>
            </div>

            <div class="achievements-grid">
                <?php
                $prestasi_query = mysqli_query($koneksi, "SELECT * FROM prestasi ORDER BY id DESC LIMIT 4");
                $badge_classes = ['', 'gold', 'green', ''];
                $badge_icons = ['fa-medal', 'fa-trophy', 'fa-leaf', 'fa-comments'];
                $i = 0;
                while ($prestasi = mysqli_fetch_array($prestasi_query)) {
                    $gambar = $prestasi['gambar'] ? "uploads/" . $prestasi['gambar'] : "https://images.unsplash.com/photo-1567168544646-208fa05f4e0e?w=400&h=300&fit=crop";
                    $badge_class = $badge_classes[$i % 4];
                    $badge_icon = $badge_icons[$i % 4];
                ?>
                <div class="achievement-card">
                    <div class="achievement-image">
                        <img src="<?php echo $gambar; ?>" alt="<?php echo $prestasi['judul']; ?>">
                    </div>
                    <div class="achievement-content">
                        <h4><?php echo $prestasi['judul']; ?></h4>
                        <p><?php echo date('Y', strtotime($prestasi['tanggal_buat'])); ?></p>
                    </div>
                </div>
                <?php
                    $i++;
                }
                ?>
            </div>

            <div class="achievements-cta">
                <a href="prestasi.php" class="btn btn-primary">
                    <span>Lihat Semua Prestasi</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ========== CTA / PPDB ========== -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Bergabunglah Bersama Kami</h2>
                <p>Daftarkan putra-putri Anda di SMAN 1 Bengkalis untuk masa depan yang cerah</p>
                <a href="#" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus"></i>
                    <span>Pendaftaran PPDB 2026</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <!-- About -->
                    <div class="footer-col footer-about">
                        <div class="footer-logo">
                            <div class="logo-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="logo-text">
                                <h3>SMAN 1 Bengkalis</h3>
                                <span>Unggul dalam Prestasi</span>
                            </div>
                        </div>
                        <p>SMAN 1 Bengkalis adalah sekolah menengah atas negeri yang berkomitmen untuk mencetak generasi
                            unggul, berkarakter, dan berprestasi.</p>
                        <div class="footer-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-col">
                        <h4>Link Cepat</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Beranda</a></li>
                            <li><a href="profile.php">Profil Sekolah</a></li>
                            <li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li>
                            <li><a href="news.php">Berita & Kegiatan</a></li>
                            <li><a href="#">PPDB Online</a></li>
                        </ul>
                    </div>

                    <!-- Galeri -->
                    <div class="footer-col">
                        <h4>Galeri</h4>
                        <ul class="footer-links">
                            <li><a href="foto.php">Galeri Foto</a></li>
                            <li><a href="prestasi.php">Prestasi</a></li>
                            <li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div class="footer-col">
                        <h4>Kontak Kami</h4>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Jl. Lembaga, Senggoro, Kec. Bengkalis, Kab. Bengkalis, Riau 28711</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <span>(0766) 21234</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span>info@sman1bengkalis.sch.id</span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Senin - Jumat: 07.00 - 15.00 WIB</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>&copy; 2025 SMAN 1 Bengkalis. All Rights Reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Floating Contact Button -->
    <div class="fab-container" id="fabContainer">
        <div class="fab-options">
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=info@sman1bengkalis.sch.id&su=Pertanyaan%20dari%20Website" target="_blank" class="fab-option fab-email" title="Kirim Email via Gmail">
                <i class="fas fa-envelope"></i>
            </a>
            <a href="https://wa.me/62766212340?text=Halo%20SMAN%201%20Bengkalis" target="_blank" class="fab-option fab-whatsapp" title="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>
        <button class="fab-button" id="fabButton" title="Hubungi Kami">
            <i class="fas fa-comments"></i>
            <i class="fas fa-times"></i>
        </button>
    </div>


    <!-- JavaScript -->
    <script src="js/main.js?v=2"></script>
    <script src="js/fab.js?v=1"></script>
</body>

</html>
