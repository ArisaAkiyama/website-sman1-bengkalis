<?php
include 'koneksi.php';

// Auto-create pengumuman table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS pengumuman (
    id INT(11) NOT NULL AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    prioritas ENUM('penting', 'normal') DEFAULT 'normal',
    lampiran VARCHAR(255) DEFAULT NULL,
    tanggal_buat DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($koneksi, $create_table);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman - SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
    <style>
        .announcement-section {
            padding: 6rem 0;
            background: var(--color-gray-100);
            min-height: 60vh;
        }
        .announcement-list {
            max-width: 900px;
            margin: 0 auto;
        }
        .announcement-item {
            background: white;
            border-radius: 16px;
            padding: 25px 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border-left: 5px solid var(--color-primary);
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        .announcement-item:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .announcement-item.penting {
            border-left-color: #e74c3c;
            background: linear-gradient(135deg, #fff 0%, #fff5f5 100%);
        }
        .announcement-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.5rem;
        }
        .announcement-item:not(.penting) .announcement-icon {
            background: linear-gradient(135deg, var(--color-primary), #006644);
            color: white;
        }
        .announcement-item.penting .announcement-icon {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        .announcement-content {
            flex: 1;
        }
        .announcement-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .announcement-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--color-text-dark);
            margin: 0;
        }
        .badge-penting {
            background: #e74c3c;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .announcement-date {
            color: #888;
            font-size: 1.3rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .announcement-text {
            color: #555;
            font-size: 1.4rem;
            line-height: 1.7;
            margin-bottom: 15px;
        }
        .announcement-attachment {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 10px 18px;
            border-radius: 8px;
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.3rem;
            transition: all 0.3s;
        }
        .announcement-attachment:hover {
            background: var(--color-primary);
            color: white;
        }
        .empty-announcement {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        .empty-announcement i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        .empty-announcement p {
            font-size: 1.6rem;
            color: #888;
        }
        @media (max-width: 768px) {
            .announcement-item {
                flex-direction: column;
                padding: 20px;
            }
            .announcement-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            .announcement-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar"><div class="container"><div class="top-bar-left"><a href="#" class="top-bar-item"><i class="fas fa-calendar-alt"></i><span>Senin - Jumat: 07.00 - 15.00 WIB</span></a></div><div class="top-bar-right"><a href="tel:+62766123456" class="top-bar-item"><i class="fas fa-phone"></i> (0766) 21234</a><a href="mailto:info@sman1bengkalis.sch.id" class="top-bar-item"><i class="fas fa-envelope"></i> info@sman1bengkalis.sch.id</a><div class="top-bar-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div></div></div></div>

    <header class="header" id="header"><div class="container"><a href="index.php" class="logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h1>SMAN 1 Bengkalis</h1><span>Unggul dalam Prestasi</span></div></a><nav class="nav" id="nav"><ul class="nav-menu" id="navMenu"><li class="nav-item"><a href="index.php" class="nav-link">BERANDA</a></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">PROFIL <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="profile.php">Sambutan Kepala Sekolah</a></li><li><a href="karyawan.php">Guru & Staff</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">KESISWAAN <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li><li><a href="prestasi.php">Prestasi Siswa</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">GALERI <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="foto.php">Foto</a></li></ul></li><li class="nav-item"><a href="news.php" class="nav-link">BERITA</a></li><li class="nav-item active"><a href="pengumuman.php" class="nav-link">PENGUMUMAN</a></li><li class="nav-item"><a href="contact.php" class="nav-link">KONTAK</a></li></ul><button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button><button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button></nav></div></header>

    <!-- Search Modal -->
    <div class="search-modal" id="searchModal"><div class="search-modal-content"><button class="search-close" id="searchClose"><i class="fas fa-times"></i></button><form action="news.php" method="GET" class="search-form"><input type="text" name="search" class="search-input" placeholder="Cari berita..." autofocus><button type="submit" class="search-submit"><i class="fas fa-search"></i></button></form></div></div>

    <!-- PAGE HEADER -->
    <section class="page-header" style="background: linear-gradient(135deg, rgba(0,64,41,0.9), rgba(0,64,41,0.7)), url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1600'); background-size: cover; background-position: center; padding: 8rem 0; text-align: center; color: white;">
        <div class="container">
            <h1 style="font-size: 4rem; margin-bottom: 1rem;"><i class="fas fa-bullhorn"></i> Pengumuman</h1>
            <p style="font-size: 1.8rem; opacity: 0.9;">Informasi penting untuk siswa, guru, dan orang tua</p>
        </div>
    </section>

    <!-- ANNOUNCEMENT CONTENT -->
    <section class="announcement-section">
        <div class="container">
            <div class="announcement-list">
                <?php
                $jumlahDataPerHalaman = 10;
                $query_count = mysqli_query($koneksi, "SELECT id FROM pengumuman");
                $total_records = mysqli_num_rows($query_count);
                $total_pages = ceil($total_records / $jumlahDataPerHalaman);
                $page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                $start = ($page - 1) * $jumlahDataPerHalaman;

                $query = mysqli_query($koneksi, "SELECT * FROM pengumuman ORDER BY prioritas DESC, tanggal_buat DESC LIMIT $start, $jumlahDataPerHalaman");
                
                if(mysqli_num_rows($query) > 0){
                    while($data = mysqli_fetch_array($query)){
                        $is_penting = $data['prioritas'] == 'penting';
                ?>
                <div class="announcement-item <?php echo $is_penting ? 'penting' : ''; ?>">
                    <div class="announcement-icon">
                        <i class="fas <?php echo $is_penting ? 'fa-exclamation-circle' : 'fa-bullhorn'; ?>"></i>
                    </div>
                    <div class="announcement-content">
                        <div class="announcement-header">
                            <h3 class="announcement-title"><?php echo $data['judul']; ?></h3>
                            <?php if($is_penting): ?>
                            <span class="badge-penting"><i class="fas fa-star"></i> Penting</span>
                            <?php endif; ?>
                        </div>
                        <div class="announcement-date">
                            <i class="far fa-calendar-alt"></i> <?php echo date('d F Y', strtotime($data['tanggal_buat'])); ?>
                        </div>
                        <p class="announcement-text"><?php echo nl2br($data['isi']); ?></p>
                        <?php if($data['lampiran']): ?>
                        <a href="uploads/<?php echo $data['lampiran']; ?>" class="announcement-attachment" target="_blank">
                            <i class="fas fa-file-pdf"></i> Download Lampiran
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div class="empty-announcement">
                    <i class="fas fa-bullhorn"></i>
                    <p>Belum ada pengumuman saat ini.</p>
                </div>
                <?php } ?>
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
                    <a href="?halaman=<?php echo $page + 1; ?>" class="btn btn-outline" style="margin: 0 0.5rem;">Selanjutnya &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer"><div class="footer-main"><div class="container"><div class="footer-grid"><div class="footer-col footer-about"><div class="footer-logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h3>SMAN 1 Bengkalis</h3><span>Unggul dalam Prestasi</span></div></div><p>Sekolah menengah atas negeri yang berkomitmen mencetak generasi unggul.</p></div><div class="footer-col"><h4>Link Cepat</h4><ul class="footer-links"><li><a href="index.php">Beranda</a></li><li><a href="profile.php">Profil</a></li><li><a href="news.php">Berita</a></li><li><a href="contact.php">Kontak</a></li></ul></div><div class="footer-col"><h4>Kontak</h4><ul class="footer-contact"><li><i class="fas fa-map-marker-alt"></i><span>Jl. Lembaga, Senggoro</span></li><li><i class="fas fa-phone"></i><span>(0766) 21234</span></li></ul></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-bottom-content"><p>&copy; 2025 SMAN 1 Bengkalis.</p></div></div></div></footer>
    <button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>
    <script src="js/main.js?v=2"></script>
</body>
</html>
