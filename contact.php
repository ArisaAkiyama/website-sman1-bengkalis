<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - SMAN 1 Bengkalis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
    <link rel="stylesheet" href="css/contact.css?v=1">
</head>
<body>
    <?php
    // Process form submission
    $success = false;
    $error = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
        $email = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
        $subjek = mysqli_real_escape_string($koneksi, $_POST['subjek'] ?? '');
        $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan'] ?? '');
        
        if (!empty($nama) && !empty($email) && !empty($pesan)) {
            $query = "INSERT INTO pesan (nama, email, subjek, pesan) VALUES ('$nama', '$email', '$subjek', '$pesan')";
            if (mysqli_query($koneksi, $query)) {
                $success = true;
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        } else {
            $error = 'Mohon lengkapi semua field yang wajib diisi.';
        }
    }
    ?>

    <div class="top-bar"><div class="container"><div class="top-bar-left"><a href="#" class="top-bar-item"><i class="fas fa-calendar-alt"></i><span>Senin - Jumat: 07.00 - 15.00 WIB</span></a></div><div class="top-bar-right"><a href="tel:+62766123456" class="top-bar-item"><i class="fas fa-phone"></i> (0766) 21234</a><a href="mailto:info@sman1bengkalis.sch.id" class="top-bar-item"><i class="fas fa-envelope"></i> info@sman1bengkalis.sch.id</a><div class="top-bar-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div></div></div></div>

    <header class="header" id="header"><div class="container"><a href="index.php" class="logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h1>SMAN 1 Bengkalis</h1><span>Unggul dalam Prestasi</span></div></a><nav class="nav" id="nav"><ul class="nav-menu" id="navMenu"><li class="nav-item"><a href="index.php" class="nav-link">BERANDA</a></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">PROFIL <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="profile.php">Sambutan Kepala Sekolah</a></li><li><a href="karyawan.php">Guru & Staff</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">KESISWAAN <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="ekstrakurikuler.php">Ekstrakurikuler</a></li><li><a href="prestasi.php">Prestasi Siswa</a></li></ul></li><li class="nav-item has-dropdown"><a href="#" class="nav-link">GALERI <i class="fas fa-chevron-down"></i></a><ul class="dropdown-menu"><li><a href="foto.php">Foto</a></li></ul></li><li class="nav-item"><a href="news.php" class="nav-link">BERITA</a></li><li class="nav-item active"><a href="contact.php" class="nav-link">KONTAK</a></li></ul><button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button><button class="nav-toggle" id="navToggle"><span></span><span></span><span></span></button></nav></div></header>

    <section style="background: linear-gradient(135deg, rgba(0,64,41,0.9), rgba(0,64,41,0.7)), url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1600'); background-size: cover; padding: 8rem 0; text-align: center; color: white;"><div class="container"><h1 style="font-size: 4rem; margin-bottom: 1rem;">Hubungi Kami</h1><p style="font-size: 1.8rem; opacity: 0.9;">Kami siap membantu Anda. Jangan ragu untuk menghubungi kami.</p></div></section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h3>Informasi Kontak</h3>
                    <p>Hubungi kami melalui informasi di bawah ini atau kirim pesan langsung.</p>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Alamat</h4>
                            <p>Jl. Lembaga, Senggoro, Kec. Bengkalis,<br>Kab. Bengkalis, Riau 28711</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Telepon</h4>
                            <p>(0766) 21234</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>info@sman1bengkalis.sch.id</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h4>Jam Operasional</h4>
                            <p>Senin - Jumat: 07.00 - 15.00 WIB</p>
                        </div>
                    </div>

                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <div class="contact-form">
                    <h3>Kirim Pesan</h3>
                    <p>Isi formulir di bawah ini dan kami akan segera merespons.</p>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success" id="successAlert">
                        <i class="fas fa-check-circle"></i> Pesan Anda berhasil dikirim! Kami akan segera merespons.
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form action="contact.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap *</label>
                                <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subjek">Subjek</label>
                            <input type="text" id="subjek" name="subjek" placeholder="Subjek pesan Anda">
                        </div>
                        <div class="form-group">
                            <label for="pesan">Pesan *</label>
                            <textarea id="pesan" name="pesan" placeholder="Tulis pesan Anda di sini..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim Pesan</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="map-section">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6971.833122330116!2d102.11112649904271!3d1.4739812306839275!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d15fce0d1431f7%3A0xd5c0ffdd6c3ff13b!2sSMA%20Negeri%201%20Bengkalis!5e1!3m2!1sen!2sid!4v1765442701600!5m2!1sen!2sid" allowfullscreen="" loading="lazy"></iframe>
    </section>

    <footer class="footer"><div class="footer-main"><div class="container"><div class="footer-grid"><div class="footer-col footer-about"><div class="footer-logo"><div class="logo-icon"><i class="fas fa-graduation-cap"></i></div><div class="logo-text"><h3>SMAN 1 Bengkalis</h3><span>Unggul dalam Prestasi</span></div></div><p>Sekolah menengah atas negeri yang berkomitmen mencetak generasi unggul.</p></div><div class="footer-col"><h4>Link Cepat</h4><ul class="footer-links"><li><a href="index.php">Beranda</a></li><li><a href="profile.php">Profil</a></li><li><a href="news.php">Berita</a></li><li><a href="contact.php">Kontak</a></li></ul></div><div class="footer-col"><h4>Kontak</h4><ul class="footer-contact"><li><i class="fas fa-map-marker-alt"></i><span>Jl. Lembaga, Senggoro</span></li><li><i class="fas fa-phone"></i><span>(0766) 21234</span></li></ul></div></div></div></div><div class="footer-bottom"><div class="container"><div class="footer-bottom-content"><p>&copy; 2025 SMAN 1 Bengkalis.</p></div></div></div></footer>
    <button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>
    <script src="js/main.js?v=2"></script>
    <script src="js/contact.js?v=1"></script>
    <?php include 'includes/chatbot_widget.php'; ?>
</body>
</html>
