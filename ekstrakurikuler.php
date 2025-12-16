<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekstrakurikuler - SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>

<body>
    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-left">
                <a href="#" class="top-bar-item"><i class="fas fa-calendar-alt"></i><span>Senin - Jumat: 07.00 - 15.00 WIB</span></a>
            </div>
            <div class="top-bar-right">
                <a href="tel:+62766123456" class="top-bar-item"><i class="fas fa-phone"></i> (0766) 21234</a>
                <a href="mailto:info@sman1bengkalis.sch.id" class="top-bar-item"><i class="fas fa-envelope"></i> info@sman1bengkalis.sch.id</a>
                <div class="top-bar-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header class="header" id="header">
        <div class="container">
            <a href="index.php" class="logo">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="logo-text"><h1>SMAN 1 Bengkalis</h1><span>Unggul dalam Prestasi</span></div>
            </a>
            <nav class="nav" id="nav">
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item"><a href="index.php" class="nav-link">BERANDA</a></li>
                    <li class="nav-item has-dropdown">
                        <a href="#" class="nav-link">PROFIL <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="profile.php">Sambutan Kepala Sekolah</a></li>
                            <li><a href="karyawan.php">Guru & Staff</a></li>
                        </ul>
                    </li>
                    <li class="nav-item has-dropdown active">
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
                    <li class="nav-item"><a href="news.php" class="nav-link">BERITA</a></li>
                    <li class="nav-item"><a href="contact.php" class="nav-link">KONTAK</a></li>
                </ul>
                <button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button>
                <button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button>
            </nav>
        </div>
    </header>

    <!-- PAGE HEADER -->
    <section class="page-header" style="background: linear-gradient(135deg, rgba(0,64,41,0.9), rgba(0,64,41,0.7)), url('https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=1600'); background-size: cover; background-position: center; padding: 8rem 0; text-align: center; color: white;">
        <div class="container">
            <h1 style="font-size: 4rem; margin-bottom: 1rem;">Ekstrakurikuler</h1>
            <p style="font-size: 1.8rem; opacity: 0.9;">Berbagai kegiatan untuk mengembangkan bakat dan minat siswa</p>
        </div>
    </section>

    <!-- EKSKUL CONTENT -->
    <section class="achievements-section" style="padding: 6rem 0; background: var(--color-gray-100);">
        <div class="container">
            <div class="achievements-grid">
                <?php
                $jumlahDataPerHalaman = 8;
                $query_count = mysqli_query($koneksi, "SELECT id FROM ekstrakurikuler");
                $total_records = mysqli_num_rows($query_count);
                $total_pages = ceil($total_records / $jumlahDataPerHalaman);
                $page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                $start = ($page - 1) * $jumlahDataPerHalaman;

                $query = mysqli_query($koneksi, "SELECT * FROM ekstrakurikuler ORDER BY id DESC LIMIT $start, $jumlahDataPerHalaman");
                $icons = ['fa-futbol', 'fa-music', 'fa-paint-brush', 'fa-laptop-code', 'fa-microscope', 'fa-book-reader', 'fa-users', 'fa-flag'];
                $i = 0;
                if(mysqli_num_rows($query) > 0){
                    while($data = mysqli_fetch_array($query)){
                        $icon = $icons[$i % count($icons)];
                        $gambar = $data['gambar'] ? "uploads/" . $data['gambar'] : "https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=400&h=300&fit=crop";
                ?>
                <a href="detail_ekskul.php?id=<?php echo $data['id']; ?>" class="achievement-card" style="text-decoration: none; color: inherit; display: block;">
                    <div class="achievement-image">
                        <img src="<?php echo $gambar; ?>" alt="<?php echo $data['nama_ekskul']; ?>">
                    </div>
                    <div class="achievement-content">
                        <h4><?php echo $data['nama_ekskul']; ?></h4>
                        <p><?php echo substr($data['deskripsi'], 0, 50); ?>...</p>
                        <span style="color: var(--color-accent); font-weight: 600; font-size: 1.3rem; display: inline-flex; align-items: center; gap: 5px; margin-top: 10px;">Lihat Detail <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                <?php 
                        $i++;
                    }
                } else {
                    echo "<p style='text-align:center; font-size: 1.6rem; grid-column: 1/-1;'>Belum ada data ekstrakurikuler.</p>";
                } 
                ?>
            </div>

            <!-- Pagination -->
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
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-col footer-about">
                        <div class="footer-logo">
                            <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                            <div class="logo-text"><h3>SMAN 1 Bengkalis</h3><span>Unggul dalam Prestasi</span></div>
                        </div>
                        <p>SMAN 1 Bengkalis adalah sekolah menengah atas negeri yang berkomitmen untuk mencetak generasi unggul, berkarakter, dan berprestasi.</p>
                    </div>
                    <div class="footer-col">
                        <h4>Link Cepat</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Beranda</a></li>
                            <li><a href="profile.php">Profil Sekolah</a></li>
                            <li><a href="news.php">Berita</a></li>
                            <li><a href="contact.php">Kontak</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Kontak Kami</h4>
                        <ul class="footer-contact">
                            <li><i class="fas fa-map-marker-alt"></i><span>Jl. Lembaga, Senggoro, Bengkalis</span></li>
                            <li><i class="fas fa-phone"></i><span>(0766) 21234</span></li>
                            <li><i class="fas fa-envelope"></i><span>info@sman1bengkalis.sch.id</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>&copy; 2025 SMAN 1 Bengkalis. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>
    <script src="js/main.js?v=2"></script>
    <?php include 'includes/chatbot_widget.php'; ?>
</body>
</html>
