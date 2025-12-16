<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru & Karyawan - SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="css/staff.css?v=1">
</head>
<body>
    <div class="top-bar"><div class="container"><div class="top-bar-left"><a href="#" class="top-bar-item"><i class="fas fa-calendar-alt"></i><span>Senin - Jumat: 07.00 - 15.00 WIB</span></a></div><div class="top-bar-right"><a href="tel:+62766123456" class="top-bar-item"><i class="fas fa-phone"></i> (0766) 21234</a><a href="mailto:info@sman1bengkalis.sch.id" class="top-bar-item"><i class="fas fa-envelope"></i> info@sman1bengkalis.sch.id</a><div class="top-bar-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div></div></div></div>

    <header class="header" id="header"><div class="container"><a href="index.php" class="logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h1>SMAN 1 Bengkalis</h1><span>Unggul dalam Prestasi</span></div></a><nav class="nav" id="nav"><ul class="nav-menu" id="navMenu"><li class="nav-item"><a href="index.php" class="nav-link">BERANDA</a></li><li class="nav-item has-dropdown active"><a href="#" class="nav-link">PROFIL <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="profile.php">Sambutan Kepala Sekolah</a></li><li><a href="karyawan.php">Guru & Staff</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">KESISWAAN <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li><li><a href="prestasi.php">Prestasi Siswa</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">GALERI <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="foto.php">Foto</a></li></ul></li><li class="nav-item"><a href="news.php" class="nav-link">BERITA</a></li><li class="nav-item"><a href="contact.php" class="nav-link">KONTAK</a></li></ul><button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button><button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button></nav></div></header>

    <section style="background: linear-gradient(135deg, rgba(0,64,41,0.9), rgba(0,64,41,0.7)), url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1600'); background-size: cover; padding: 8rem 0; text-align: center; color: white;"><div class="container"><h1 style="font-size: 4rem; margin-bottom: 1rem;">Guru & Karyawan</h1><p style="font-size: 1.8rem; opacity: 0.9;">Tenaga pendidik dan kependidikan SMAN 1 Bengkalis</p></div></section>

    <section style="padding: 6rem 0;"><div class="container">
        <div class="section-header"><h2>Tim Kami</h2><p>Guru dan karyawan yang berdedikasi</p><div class="divider"></div></div>
        <div class="staff-grid">
            <div class="staff-card"><img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&h=350&fit=crop" alt="Staff"><div class="staff-info"><h3>Etty Marzani, S.E., M.Pd.</h3><p>Wakil Kepala Sekolah</p></div></div>
            <div class="staff-card"><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=350&fit=crop" alt="Staff"><div class="staff-info"><h3>Abdurrahman, S.Ag.</h3><p>Guru PAI</p></div></div>
            <div class="staff-card"><img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500&h=350&fit=crop" alt="Staff"><div class="staff-info"><h3>Abdan Syakuro, S.T.</h3><p>Guru TIK</p></div></div>
            <div class="staff-card"><img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=500&h=350&fit=crop" alt="Staff"><div class="staff-info"><h3>Devy Tri Anisa Marita, S.Pd.</h3><p>Guru Bahasa Inggris</p></div></div>
            <div class="staff-card"><img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=500&h=350&fit=crop" alt="Staff"><div class="staff-info"><h3>Dra. Liza Maryeni</h3><p>Guru Matematika</p></div></div>
            <div class="staff-card"><img src="https://images.unsplash.com/photo-1554151228-14d9def656ec?w=500&h=350&fit=crop" alt="Staff"><div class="staff-info"><h3>Lisa Herawati, S.Pd.I</h3><p>Guru PAI</p></div></div>
        </div>
    </div></section>

    <footer class="footer"><div class="footer-main"><div class="container"><div class="footer-grid"><div class="footer-col footer-about"><div class="footer-logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h3>SMAN 1 Bengkalis</h3><span>Unggul dalam Prestasi</span></div></div><p>Sekolah menengah atas negeri yang berkomitmen mencetak generasi unggul.</p></div><div class="footer-col"><h4>Link Cepat</h4><ul class="footer-links"><li><a href="index.php">Beranda</a></li><li><a href="profile.php">Profil</a></li><li><a href="news.php">Berita</a></li><li><a href="contact.php">Kontak</a></li></ul></div><div class="footer-col"><h4>Kontak</h4><ul class="footer-contact"><li><i class="fas fa-map-marker-alt"></i><span>Jl. Lembaga, Senggoro</span></li><li><i class="fas fa-phone"></i><span>(0766) 21234</span></li></ul></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-bottom-content"><p>&copy; 2025 SMAN 1 Bengkalis.</p></div></div></div></footer>
    <button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>
    <script src="js/main.js?v=2"></script>
    <?php include 'includes/chatbot_widget.php'; ?>
</body>
</html>
