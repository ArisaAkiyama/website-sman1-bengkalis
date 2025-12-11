<?php
include 'koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = mysqli_query($koneksi, "SELECT * FROM prestasi WHERE id='$id'");
$data = mysqli_fetch_array($query);
if (!$data) { echo "Prestasi tidak ditemukan."; exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['judul']; ?> - SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="css/detail.css?v=1">
</head>
<body>
    <div class="top-bar"><div class="container"><div class="top-bar-left"><a href="#" class="top-bar-item"><i class="fas fa-calendar-alt"></i><span>Senin - Jumat: 07.00 - 15.00 WIB</span></a></div><div class="top-bar-right"><a href="tel:+62766123456" class="top-bar-item"><i class="fas fa-phone"></i> (0766) 21234</a><a href="mailto:info@sman1bengkalis.sch.id" class="top-bar-item"><i class="fas fa-envelope"></i> info@sman1bengkalis.sch.id</a><div class="top-bar-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div></div></div></div>

    <header class="header" id="header"><div class="container"><a href="index.php" class="logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h1>SMAN 1 Bengkalis</h1><span>Unggul dalam Prestasi</span></div></a><nav class="nav" id="nav"><ul class="nav-menu" id="navMenu"><li class="nav-item"><a href="index.php" class="nav-link">BERANDA</a></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">PROFIL <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="profile.php">Sambutan Kepala Sekolah</a></li><li><a href="karyawan.php">Guru & Staff</a></li></ul></li><li class="nav-item has-dropdown active"><a href="#" class="nav-link">KESISWAAN <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li><li><a href="prestasi.php">Prestasi Siswa</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">GALERI <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="foto.php">Foto</a></li></ul></li><li class="nav-item"><a href="news.php" class="nav-link">BERITA</a></li><li class="nav-item"><a href="contact.php" class="nav-link">KONTAK</a></li></ul><button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button><button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button></nav></div></header>

    <article class="detail-article">
        <h1><i class="fas fa-trophy" style="color: var(--color-accent); margin-right: 1rem;"></i><?php echo $data['judul']; ?></h1>
        <div class="detail-meta">
            <span><i class="far fa-calendar"></i> <?php echo date('d F Y', strtotime($data['tanggal_buat'])); ?></span>
            <span><i class="fas fa-medal"></i> Prestasi Siswa</span>
        </div>
        <?php if($data['gambar']) { ?>
            <img src="uploads/<?php echo $data['gambar']; ?>" alt="<?php echo $data['judul']; ?>" class="detail-image">
        <?php } ?>
        <div class="detail-content">
            <?php echo nl2br($data['isi']); ?>
        </div>
        <div style="text-align: center; margin-top: 4rem;">
            <a href="prestasi.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Kembali ke Prestasi</a>
        </div>
    </article>

    <footer class="footer"><div class="footer-main"><div class="container"><div class="footer-grid"><div class="footer-col footer-about"><div class="footer-logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h3>SMAN 1 Bengkalis</h3><span>Unggul dalam Prestasi</span></div></div><p>Sekolah menengah atas negeri yang berkomitmen mencetak generasi unggul.</p></div><div class="footer-col"><h4>Link Cepat</h4><ul class="footer-links"><li><a href="index.php">Beranda</a></li><li><a href="profile.php">Profil</a></li><li><a href="news.php">Berita</a></li><li><a href="contact.php">Kontak</a></li></ul></div><div class="footer-col"><h4>Kontak</h4><ul class="footer-contact"><li><i class="fas fa-map-marker-alt"></i><span>Jl. Lembaga, Senggoro</span></li><li><i class="fas fa-phone"></i><span>(0766) 21234</span></li></ul></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-bottom-content"><p>&copy; 2025 SMAN 1 Bengkalis.</p></div></div></div></footer>
    <button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>
    <script src="js/main.js?v=2"></script>
</body>
</html>
