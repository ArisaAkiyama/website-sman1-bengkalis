<?php
session_start();

// ===== SESSION SECURITY CHECKS =====
$session_timeout = 30 * 60; // 30 minutes

// Check if user is logged in
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}

// Check session timeout
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $session_timeout) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION['login_time'] = time();

// Optional: IP validation (uncomment if needed - may cause issues with dynamic IPs)
// if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
//     session_unset();
//     session_destroy();
//     header("Location: login.php?security=1");
//     exit;
// }


include 'koneksi.php';

// Auto-create pesan table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS pesan (
  id int(11) NOT NULL AUTO_INCREMENT,
  nama varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  subjek varchar(200) DEFAULT NULL,
  pesan text NOT NULL,
  status enum('belum_dibaca','dibaca') DEFAULT 'belum_dibaca',
  tanggal_kirim datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($koneksi, $create_table);

// Auto-create pengumuman table if not exists
$create_pengumuman = "CREATE TABLE IF NOT EXISTS pengumuman (
    id INT(11) NOT NULL AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    prioritas ENUM('penting', 'normal') DEFAULT 'normal',
    lampiran VARCHAR(255) DEFAULT NULL,
    tanggal_buat DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($koneksi, $create_pengumuman);

// Auto-create buku table if not exists (Perpustakaan)
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

// Handle pesan actions
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    mysqli_query($koneksi, "UPDATE pesan SET status='dibaca' WHERE id=".(int)$_GET['mark_read']);
    header('Location: input_berita.php?page=pesan'); exit;
}
if (isset($_GET['delete_pesan']) && is_numeric($_GET['delete_pesan'])) {
    mysqli_query($koneksi, "DELETE FROM pesan WHERE id=".(int)$_GET['delete_pesan']);
    header('Location: input_berita.php?page=pesan'); exit;
}
// Bulk actions
if (isset($_GET['mark_all_read'])) {
    mysqli_query($koneksi, "UPDATE pesan SET status='dibaca' WHERE status='belum_dibaca'");
    header('Location: input_berita.php?page=pesan&success=bulk_read'); exit;
}
if (isset($_GET['delete_all_pesan'])) {
    mysqli_query($koneksi, "DELETE FROM pesan");
    header('Location: input_berita.php?page=pesan&success=bulk_delete'); exit;
}

// Menentukan halaman aktif (default: dashboard)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$id_edit = isset($_GET['id']) ? $_GET['id'] : '';

// DATA EDIT DEFAULT
$edit_judul = "";
$edit_isi   = "";
$edit_gambar = "";
$is_edit    = false;

// JIKA SEDANG EDIT, Cek data
if ($aksi == 'edit' && $id_edit != '') {
    $is_edit = true;
    $table = ($page == 'ekskul') ? "ekstrakurikuler" : $page;
    
    $q_edit = mysqli_query($koneksi, "SELECT * FROM $table WHERE id='$id_edit'");
    if(mysqli_num_rows($q_edit) > 0){
        $data_edit = mysqli_fetch_array($q_edit);
        if($page == 'ekskul') {
            $edit_judul = $data_edit['nama_ekskul'];
            $edit_isi   = $data_edit['deskripsi'];
        } else {
            $edit_judul = $data_edit['judul'];
            if(isset($data_edit['isi'])) $edit_isi = $data_edit['isi'];
        }
        
        $edit_gambar = ($page == 'foto') ? $data_edit['file_foto'] : $data_edit['gambar'];
    }
}

// PROSES SIMPAN / UPDATE DATA
if (isset($_POST['simpan'])) {
    
    // Konfigurasi Upload
    $folder     = './uploads/';
    $nama_file  = $_FILES['gambar']['name'];
    $source     = $_FILES['gambar']['tmp_name'];
    $id_laman   = $_POST['id_edit']; // ID untuk update
    $status_ops = $_POST['status_ops']; // 'tambah' atau 'edit'
    
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
    
    // Logika Gambar
    $gambar_db = "";
    $upload_ok = true;

    if ($nama_file != '') {
        // Cek Ukuran File (Max 1MB = 1048576 bytes)
        if ($_FILES['gambar']['size'] > 1048576) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 1 MB.');</script>";
            $upload_ok = false;
        } else {
            // Jika upload gambar baru dan ukuran OK
            move_uploaded_file($source, $folder . $nama_file);
            $gambar_db = $nama_file;
        }
    } else {
        // Jika tidak upload, pakai gambar lama (hanya untuk edit)
        $gambar_db = $_POST['gambar_lama'];
    }

    if ($upload_ok) {
        if ($status_ops == 'tambah') {
        // === INSERT ===
        // Skip file requirement for pengumuman and perpustakaan (optional files)
        if ($nama_file == '' && $page != 'pengumuman' && $page != 'perpustakaan') {
             echo "<script>alert('Harap pilih gambar untuk data baru.');</script>";
        } else {
            if ($page == 'berita') {
                $judul = $_POST['judul']; $isi = $_POST['isi'];
                $query = "INSERT INTO berita (judul, slug, isi, gambar) VALUES ('$judul', '$judul', '$isi', '$gambar_db')";
            } elseif ($page == 'prestasi') {
                $judul = $_POST['judul']; $isi = $_POST['isi'];
                $query = "INSERT INTO prestasi (judul, isi, gambar) VALUES ('$judul', '$isi', '$gambar_db')";
            } elseif ($page == 'ekskul') {
                $nama_ekskul = $_POST['judul']; $deskripsi = $_POST['isi'];
                $query = "INSERT INTO ekstrakurikuler (nama_ekskul, deskripsi, gambar) VALUES ('$nama_ekskul', '$deskripsi', '$gambar_db')";
            } elseif ($page == 'foto') {
                $judul = $_POST['judul']; 
                $query = "INSERT INTO foto (judul, file_foto) VALUES ('$judul', '$gambar_db')";
            } elseif ($page == 'pengumuman') {
                $judul = $_POST['judul']; $isi = $_POST['isi'];
                $prioritas = isset($_POST['prioritas']) ? $_POST['prioritas'] : 'normal';
                $query = "INSERT INTO pengumuman (judul, isi, prioritas, lampiran) VALUES ('$judul', '$isi', '$prioritas', '$gambar_db')";
            } elseif ($page == 'perpustakaan') {
                $judul = $_POST['judul']; 
                $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
                $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
                $deskripsi = mysqli_real_escape_string($koneksi, $_POST['isi']);
                $tahun_terbit = isset($_POST['tahun_terbit']) ? (int)$_POST['tahun_terbit'] : 'NULL';
                $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
                // Handle file_buku (PDF) separately
                $file_buku_db = '';
                if (isset($_FILES['file_buku']) && $_FILES['file_buku']['error'] == 0) {
                    $file_buku_name = time() . '_' . basename($_FILES['file_buku']['name']);
                    move_uploaded_file($_FILES['file_buku']['tmp_name'], 'uploads/' . $file_buku_name);
                    $file_buku_db = $file_buku_name;
                }
                $query = "INSERT INTO buku (judul, pengarang, kategori, deskripsi, cover, file_buku, tahun_terbit, penerbit) VALUES ('$judul', '$pengarang', '$kategori', '$deskripsi', '$gambar_db', '$file_buku_db', $tahun_terbit, '$penerbit')";
            }
            
            $insert = mysqli_query($koneksi, $query);
            if ($insert) {
                header("Location: input_berita.php?page=$page&success=tambah");
                exit;
            } else { echo "<script>alert('Gagal menyimpan data.');</script>"; }
        }

    } else {
        // === UPDATE ===
        $query = "";
        if ($page == 'berita') {
            $judul = $_POST['judul']; $isi = $_POST['isi'];
            $query = "UPDATE berita SET judul='$judul', slug='$judul', isi='$isi', gambar='$gambar_db' WHERE id='$id_laman'";
        } elseif ($page == 'prestasi') {
            $judul = $_POST['judul']; $isi = $_POST['isi'];
            $query = "UPDATE prestasi SET judul='$judul', isi='$isi', gambar='$gambar_db' WHERE id='$id_laman'";
        } elseif ($page == 'ekskul') {
            $nama_ekskul = $_POST['judul']; $deskripsi = $_POST['isi'];
            $query = "UPDATE ekstrakurikuler SET nama_ekskul='$nama_ekskul', deskripsi='$deskripsi', gambar='$gambar_db' WHERE id='$id_laman'";
        } elseif ($page == 'foto') {
            $judul = $_POST['judul'];
            $query = "UPDATE foto SET judul='$judul', file_foto='$gambar_db' WHERE id='$id_laman'";
        } elseif ($page == 'pengumuman') {
            $judul = $_POST['judul']; $isi = $_POST['isi'];
            $prioritas = isset($_POST['prioritas']) ? $_POST['prioritas'] : 'normal';
            $query = "UPDATE pengumuman SET judul='$judul', isi='$isi', prioritas='$prioritas', lampiran='$gambar_db' WHERE id='$id_laman'";
        } elseif ($page == 'perpustakaan') {
            $judul = $_POST['judul']; 
            $pengarang = mysqli_real_escape_string($koneksi, $_POST['pengarang']);
            $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
            $deskripsi = mysqli_real_escape_string($koneksi, $_POST['isi']);
            $tahun_terbit = isset($_POST['tahun_terbit']) && $_POST['tahun_terbit'] != '' ? (int)$_POST['tahun_terbit'] : 'NULL';
            $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
            // Handle file_buku (PDF) update
            $file_buku_update = '';
            if (isset($_FILES['file_buku']) && $_FILES['file_buku']['error'] == 0) {
                $file_buku_name = time() . '_' . basename($_FILES['file_buku']['name']);
                move_uploaded_file($_FILES['file_buku']['tmp_name'], 'uploads/' . $file_buku_name);
                $file_buku_update = ", file_buku='$file_buku_name'";
            }
            $query = "UPDATE buku SET judul='$judul', pengarang='$pengarang', kategori='$kategori', deskripsi='$deskripsi', cover='$gambar_db', tahun_terbit=$tahun_terbit, penerbit='$penerbit' $file_buku_update WHERE id='$id_laman'";
        }
        
        $update = mysqli_query($koneksi, $query);
        if ($update) {
            header("Location: input_berita.php?page=$page&success=edit");
            exit;
        } else { echo "<script>alert('Gagal update data.');</script>"; }
    }
    } // End if($upload_ok)
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SMAN 1 Bengkalis</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Admin Styles -->
<link rel="stylesheet" href="css/admin.css?v=6">
</head>
<body>

<?php $unread = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM pesan WHERE status='belum_dibaca'"))['c']; ?>

<!-- Mobile Menu Button -->
<button class="mobile-menu-btn" id="mobileMenuBtn">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="?page=dashboard" class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i>
            <div class="brand-text">
                <h1>Admin Panel</h1>
                <span>SMAN 1 Bengkalis</span>
            </div>
        </a>
        <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <!-- Toggle button for collapsed state -->
        <button class="sidebar-toggle-nav" id="sidebarToggleNav" title="Expand Sidebar">
            <i class="fas fa-chevron-right"></i>
        </button>
        <!-- Menu Utama Section -->
        <div class="nav-section open">
            <div class="nav-section-title" onclick="toggleNavSection(this)">
                <span><i class="fas fa-th-large"></i> Menu Utama</span>
                <i class="fas fa-chevron-down nav-section-arrow"></i>
            </div>
            <div class="nav-section-links">
                <a href="?page=dashboard" class="<?php if($page=='dashboard') echo 'active'; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="?page=berita" class="<?php if($page=='berita') echo 'active'; ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>Berita</span>
                </a>
                <a href="?page=prestasi" class="<?php if($page=='prestasi') echo 'active'; ?>">
                    <i class="fas fa-trophy"></i>
                    <span>Prestasi</span>
                </a>
                <a href="?page=ekskul" class="<?php if($page=='ekskul') echo 'active'; ?>">
                    <i class="fas fa-basketball-ball"></i>
                    <span>Ekstrakurikuler</span>
                </a>
            </div>
        </div>
        
        <!-- Konten Section -->
        <div class="nav-section open">
            <div class="nav-section-title" onclick="toggleNavSection(this)">
                <span><i class="fas fa-folder-open"></i> Konten</span>
                <i class="fas fa-chevron-down nav-section-arrow"></i>
            </div>
            <div class="nav-section-links">
                <a href="?page=foto" class="<?php if($page=='foto') echo 'active'; ?>">
                    <i class="fas fa-images"></i>
                    <span>Galeri Foto</span>
                </a>
                <a href="?page=pengumuman" class="<?php if($page=='pengumuman') echo 'active'; ?>">
                    <i class="fas fa-bullhorn"></i>
                    <span>Pengumuman</span>
                </a>
                <a href="?page=perpustakaan" class="<?php if($page=='perpustakaan') echo 'active'; ?>">
                    <i class="fas fa-book"></i>
                    <span>Perpustakaan</span>
                </a>
            </div>
        </div>
        
        <!-- Komunikasi Section -->
        <div class="nav-section open">
            <div class="nav-section-title" onclick="toggleNavSection(this)">
                <span><i class="fas fa-comments"></i> Komunikasi</span>
                <i class="fas fa-chevron-down nav-section-arrow"></i>
            </div>
            <div class="nav-section-links">
                <a href="?page=pesan" class="<?php if($page=='pesan') echo 'active'; ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Pesan Masuk</span>
                    <?php if($unread > 0): ?><span class="nav-badge"><?php echo $unread; ?></span><?php endif; ?>
                </a>
            </div>
        </div>
    </nav>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
    
    <!-- Admin Top Bar -->
    <div class="admin-topbar">
        <div class="topbar-left">
            <h2 class="page-title">
                <?php 
                switch($page) {
                    case 'dashboard': echo '<i class="fas fa-tachometer-alt"></i> Dashboard'; break;
                    case 'berita': echo '<i class="fas fa-newspaper"></i> Berita'; break;
                    case 'prestasi': echo '<i class="fas fa-trophy"></i> Prestasi'; break;
                    case 'ekskul': echo '<i class="fas fa-basketball-ball"></i> Ekstrakurikuler'; break;
                    case 'foto': echo '<i class="fas fa-images"></i> Galeri Foto'; break;
                    case 'pengumuman': echo '<i class="fas fa-bullhorn"></i> Pengumuman'; break;
                    case 'perpustakaan': echo '<i class="fas fa-book"></i> Perpustakaan'; break;
                    case 'pesan': echo '<i class="fas fa-inbox"></i> Pesan Masuk'; break;
                    default: echo '<i class="fas fa-cog"></i> ' . ucfirst($page);
                }
                ?>
            </h2>
        </div>
        <div class="topbar-right">
            <div class="topbar-user-dropdown">
                <div class="topbar-user" id="userDropdownBtn">
                    <i class="fas fa-user-circle"></i>
                    <div class="topbar-user-info">
                        <strong><?php echo $_SESSION['username']; ?></strong>
                        <span>Administrator</span>
                    </div>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="user-dropdown-menu" id="userDropdownMenu">
                    <a href="index.php" target="_blank" class="dropdown-item">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Lihat Website</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success Alert -->
    <?php if(isset($_GET['success'])): ?>
    <div class="alert-success">
        <i class="fas fa-check-circle"></i>
        <?php 
        if($_GET['success'] == 'tambah') echo 'Data berhasil ditambahkan!';
        elseif($_GET['success'] == 'edit') echo 'Data berhasil diupdate!';
        elseif($_GET['success'] == 'hapus') echo 'Data berhasil dihapus!';
        elseif($_GET['success'] == 'bulk_read') echo 'Semua pesan berhasil ditandai sudah dibaca!';
        elseif($_GET['success'] == 'bulk_delete') echo 'Semua pesan berhasil dihapus!';
        else echo 'Operasi berhasil!';
        ?>
    </div>
    <?php endif; ?>
    <?php if($page == 'dashboard'): ?>
    <!-- DASHBOARD PAGE -->
    <?php
    // Get statistics
    $stat_berita = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM berita"))['c'];
    $stat_prestasi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM prestasi"))['c'];
    $stat_ekskul = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM ekstrakurikuler"))['c'];
    $stat_foto = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM foto"))['c'];
    $stat_pengumuman = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM pengumuman"))['c'];
    $stat_pesan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM pesan"))['c'];
    $stat_pesan_unread = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM pesan WHERE status='belum_dibaca'"))['c'];
    ?>
    
    <div class="card" style="margin-top:0; background: linear-gradient(135deg, #004029, #006644); color: white;">
        <h2 style="color: white; border-left-color: #d4af37; margin-bottom: 10px;"><i class="fas fa-chart-line"></i> Dashboard Statistik</h2>
        <p style="opacity: 0.9; font-size: 1rem;">Selamat datang, <strong><?php echo $_SESSION['username']; ?></strong>! Berikut ringkasan data website.</p>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 25px;">
        
        <!-- Berita -->
        <div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 20px; transition: all 0.3s; cursor: pointer;" onclick="location.href='?page=berita'">
            <div style="width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, #3498db, #2980b9); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-newspaper" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #2c3e50;"><?php echo $stat_berita; ?></div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">Total Berita</div>
            </div>
        </div>

        <!-- Prestasi -->
        <div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 20px; transition: all 0.3s; cursor: pointer;" onclick="location.href='?page=prestasi'">
            <div style="width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, #f1c40f, #f39c12); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-trophy" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #2c3e50;"><?php echo $stat_prestasi; ?></div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">Total Prestasi</div>
            </div>
        </div>

        <!-- Ekskul -->
        <div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 20px; transition: all 0.3s; cursor: pointer;" onclick="location.href='?page=ekskul'">
            <div style="width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, #e74c3c, #c0392b); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-basketball-ball" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #2c3e50;"><?php echo $stat_ekskul; ?></div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">Total Ekskul</div>
            </div>
        </div>

        <!-- Foto -->
        <div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 20px; transition: all 0.3s; cursor: pointer;" onclick="location.href='?page=foto'">
            <div style="width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, #9b59b6, #8e44ad); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-images" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #2c3e50;"><?php echo $stat_foto; ?></div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">Total Foto</div>
            </div>
        </div>

        <!-- Pengumuman -->
        <div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 20px; transition: all 0.3s; cursor: pointer;" onclick="location.href='?page=pengumuman'">
            <div style="width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, #1abc9c, #16a085); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-bullhorn" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #2c3e50;"><?php echo $stat_pengumuman; ?></div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">Total Pengumuman</div>
            </div>
        </div>

        <!-- Pesan -->
        <div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 20px; transition: all 0.3s; cursor: pointer;" onclick="location.href='?page=pesan'">
            <div style="width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, #004029, #006644); display: flex; align-items: center; justify-content: center; position: relative;">
                <i class="fas fa-envelope" style="font-size: 1.5rem; color: white;"></i>
                <?php if($stat_pesan_unread > 0): ?>
                <span style="position: absolute; top: -5px; right: -5px; background: #e74c3c; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 10px; font-weight: bold;"><?php echo $stat_pesan_unread; ?></span>
                <?php endif; ?>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: 700; color: #2c3e50;"><?php echo $stat_pesan; ?></div>
                <div style="color: #7f8c8d; font-size: 0.9rem;">Total Pesan</div>
            </div>
        </div>

    </div>

    <!-- Recent Activity -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-top: 30px;">
        
        <!-- Recent Berita -->
        <div class="card">
            <h3 style="font-size: 1.1rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;"><i class="fas fa-newspaper" style="color: #3498db;"></i> Berita Terbaru</h3>
            <?php $recent_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY id DESC LIMIT 3"); ?>
            <?php if(mysqli_num_rows($recent_berita) > 0): ?>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php while($rb = mysqli_fetch_array($recent_berita)): ?>
                <li style="padding: 12px 0; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 12px;">
                    <?php if($rb['gambar']): ?>
                    <img src="uploads/<?php echo $rb['gambar']; ?>" style="width: 45px; height: 45px; border-radius: 8px; object-fit: cover;">
                    <?php else: ?>
                    <div style="width: 45px; height: 45px; border-radius: 8px; background: #eee; display: flex; align-items: center; justify-content: center;"><i class="fas fa-image" style="color: #bbb;"></i></div>
                    <?php endif; ?>
                    <div style="flex: 1; overflow: hidden;">
                        <div style="font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $rb['judul']; ?></div>
                        <div style="font-size: 0.8rem; color: #888;"><?php echo date('d M Y', strtotime($rb['tanggal_buat'])); ?></div>
                    </div>
                </li>
                <?php endwhile; ?>
            </ul>
            <?php else: ?>
            <p style="color: #888; text-align: center; padding: 20px;">Belum ada berita.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Pesan -->
        <div class="card">
            <h3 style="font-size: 1.1rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;"><i class="fas fa-envelope" style="color: #004029;"></i> Pesan Terbaru</h3>
            <?php $recent_pesan = mysqli_query($koneksi, "SELECT * FROM pesan ORDER BY tanggal_kirim DESC LIMIT 3"); ?>
            <?php if(mysqli_num_rows($recent_pesan) > 0): ?>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php while($rp = mysqli_fetch_array($recent_pesan)): ?>
                <li style="padding: 12px 0; border-bottom: 1px solid #eee;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <span style="font-weight: 600; font-size: 0.9rem;"><?php echo $rp['nama']; ?></span>
                        <?php if($rp['status'] == 'belum_dibaca'): ?>
                        <span style="background: #e74c3c; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 10px;">Baru</span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 0.85rem; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo substr($rp['pesan'], 0, 50); ?>...</div>
                    <div style="font-size: 0.75rem; color: #aaa; margin-top: 5px;"><?php echo date('d M Y H:i', strtotime($rp['tanggal_kirim'])); ?></div>
                </li>
                <?php endwhile; ?>
            </ul>
            <?php else: ?>
            <p style="color: #888; text-align: center; padding: 20px;">Belum ada pesan.</p>
            <?php endif; ?>
        </div>

    </div>

    <?php elseif($page == 'pesan'): ?>
    <!-- PESAN PAGE -->
    <?php
    // Pagination setup
    $limit = 5;
    $pesan_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    if($pesan_page < 1) $pesan_page = 1;
    $offset = ($pesan_page - 1) * $limit;
    
    // Count total
    $count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesan");
    $total_data = mysqli_fetch_assoc($count_query)['total'];
    $total_pages = ceil($total_data / $limit);
    
    // Handle search
    $search_pesan = isset($_GET['search_pesan']) ? trim($_GET['search_pesan']) : '';
    $search_condition = '';
    if (!empty($search_pesan)) {
        $escaped_search = mysqli_real_escape_string($koneksi, $search_pesan);
        $search_condition = "WHERE nama LIKE '%$escaped_search%' OR email LIKE '%$escaped_search%' OR subjek LIKE '%$escaped_search%' OR pesan LIKE '%$escaped_search%'";
        // Recalculate total for search
        $count_search = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesan $search_condition");
        $total_data = mysqli_fetch_assoc($count_search)['total'];
        $total_pages = ceil($total_data / $limit);
    }
    
    // Get data with limit and search
    $pesan_query = mysqli_query($koneksi, "SELECT * FROM pesan $search_condition ORDER BY tanggal_kirim DESC LIMIT $offset, $limit");
    ?>
    <div class="card" style="margin-top:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px;">
            <h2 style="margin-bottom:0;"><i class="fas fa-envelope"></i> Pesan Masuk <span style="font-weight:400; font-size:0.9rem; color:#666;">(<?php echo $total_data; ?> pesan)</span></h2>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <!-- Search Form -->
                <form action="" method="GET" style="display:flex; gap:8px;">
                    <input type="hidden" name="page" value="pesan">
                    <div style="position:relative;">
                        <input type="text" name="search_pesan" placeholder="Cari pesan..." value="<?php echo htmlspecialchars($search_pesan); ?>" style="padding:10px 15px 10px 40px; border:1px solid #ddd; border-radius:8px; font-size:0.9rem; width:220px; transition: all 0.3s;">
                        <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#888;"></i>
                    </div>
                    <button type="submit" style="padding:10px 16px; background:linear-gradient(135deg, #004029, #006644); color:white; border:none; border-radius:8px; cursor:pointer; font-size:0.85rem; font-weight:600;"><i class="fas fa-search"></i></button>
                    <?php if(!empty($search_pesan)): ?>
                    <a href="?page=pesan" style="padding:10px 14px; background:#f0f0f0; color:#666; border-radius:8px; text-decoration:none; font-size:0.85rem;"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
                <?php if($total_data > 0): ?>
                <a href="?page=pesan&mark_all_read=1" onclick="return confirm('Tandai semua pesan sebagai dibaca?')" style="padding:10px 18px; background:linear-gradient(135deg, #27ae60, #2ecc71); color:white; border-radius:8px; text-decoration:none; font-size:0.85rem; font-weight:600; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-check-double"></i> Tandai Baca Semua</a>
                <a href="?page=pesan&delete_all_pesan=1" onclick="return confirm('HAPUS SEMUA PESAN? Tindakan ini tidak dapat dibatalkan!')" style="padding:10px 18px; background:linear-gradient(135deg, #e74c3c, #c0392b); color:white; border-radius:8px; text-decoration:none; font-size:0.85rem; font-weight:600; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-trash-alt"></i> Hapus Semua</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table-admin" style="table-layout:fixed;">
                <thead>
                    <tr>
                        <th style="width:50px; text-align:center;">No</th>
                        <th style="width:200px;">Pengirim</th>
                        <th style="width:150px;">Subjek</th>
                        <th>Pesan</th>
                        <th style="width:120px;">Tanggal</th>
                        <th style="width:90px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = $offset + 1;
                    if(mysqli_num_rows($pesan_query) > 0) {
                        while($p = mysqli_fetch_assoc($pesan_query)) {
                            $row_style = $p['status'] == 'belum_dibaca' ? 'background:linear-gradient(135deg, #fffbf0 0%, #fff8e8 100%);' : '';
                    ?>
                    <tr style="<?php echo $row_style; ?>">
                        <td style="text-align:center; font-weight:600;"><?php echo $no++; ?></td>
                        <td>
                            <div style="font-weight:600; color:#004029; margin-bottom:3px;">
                                <?php echo htmlspecialchars($p['nama']); ?>
                                <?php if($p['status'] == 'belum_dibaca'): ?>
                                <span style="background:linear-gradient(135deg, #D4A84B, #e8c77a); color:#004029; font-size:0.65rem; padding:3px 8px; border-radius:12px; margin-left:6px; font-weight:700;">BARU</span>
                                <?php endif; ?>
                            </div>
                            <div style="color:#666; font-size:0.8rem; word-break:break-all;"><?php echo htmlspecialchars($p['email']); ?></div>
                        </td>
                        <td style="font-weight:500;"><?php echo htmlspecialchars($p['subjek'] ?: '-'); ?></td>
                        <td style="word-wrap:break-word; overflow-wrap:break-word; white-space:normal; line-height:1.5;">
                            <?php 
                            $pesan_text = htmlspecialchars($p['pesan']);
                            echo strlen($pesan_text) > 80 ? substr($pesan_text, 0, 80) . '...' : $pesan_text; 
                            ?>
                        </td>
                        <td style="font-size:0.85rem; color:#666;"><?php echo date('d M Y', strtotime($p['tanggal_kirim'])); ?><br><small><?php echo date('H:i', strtotime($p['tanggal_kirim'])); ?></small></td>
                        <td style="text-align:center;">
                            <div class="action-links" style="justify-content:center;">
                                <?php if($p['status'] == 'belum_dibaca'): ?>
                                <a href="?page=pesan&mark_read=<?php echo $p['id']; ?>" class="btn-edit-row" title="Tandai Dibaca"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="?page=pesan&delete_pesan=<?php echo $p['id']; ?>" onclick="return confirm('Hapus pesan ini?')" class="btn-delete-row"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php } } else { ?>
                    <tr><td colspan="6" class="empty-state"><i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:10px;"></i>Belum ada pesan masuk.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): 
        $search_param = !empty($search_pesan) ? '&search_pesan=' . urlencode($search_pesan) : '';
        ?>
        <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-top:25px; padding-top:20px; border-top:1px solid #eee;">
            <?php if($pesan_page > 1): ?>
            <a href="?page=pesan&p=1<?php echo $search_param; ?>" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-double-left"></i></a>
            <a href="?page=pesan&p=<?php echo $pesan_page-1; ?><?php echo $search_param; ?>" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-left"></i></a>
            <?php endif; ?>
            
            <?php 
            $start_pg = max(1, $pesan_page - 2);
            $end_pg = min($total_pages, $pesan_page + 2);
            for($i = $start_pg; $i <= $end_pg; $i++): 
            ?>
            <a href="?page=pesan&p=<?php echo $i; ?><?php echo $search_param; ?>" style="padding:8px 14px; background:<?php echo $i == $pesan_page ? 'linear-gradient(135deg, #004029, #006644)' : '#f0f0f0'; ?>; color:<?php echo $i == $pesan_page ? 'white' : '#333'; ?>; border-radius:8px; text-decoration:none; font-weight:<?php echo $i == $pesan_page ? '600' : '400'; ?>; font-size:0.9rem;"><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if($pesan_page < $total_pages): ?>
            <a href="?page=pesan&p=<?php echo $pesan_page+1; ?><?php echo $search_param; ?>" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-right"></i></a>
            <a href="?page=pesan&p=<?php echo $total_pages; ?><?php echo $search_param; ?>" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-double-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- FORM & DATA SECTION -->
    <div class="dashboard-grid">
        
        <!-- FORM SECTION -->
        <div class="card">
            <h2><?php echo $is_edit ? "Edit ".ucfirst($page) : "Tambah ".ucfirst($page); ?></h2>
            
            <form method="post" enctype="multipart/form-data">
                <!-- Hidden Fields -->
                <input type="hidden" name="id_edit" value="<?php echo $id_edit; ?>">
                <input type="hidden" name="status_ops" value="<?php echo $is_edit ? 'edit' : 'tambah'; ?>">
                <input type="hidden" name="gambar_lama" value="<?php echo $edit_gambar; ?>">

                <div class="form-group">
                    <label>Judul / Nama</label>
                    <input type="text" name="judul" class="form-control" value="<?php echo $edit_judul; ?>" required placeholder="Masukkan judul disini...">
                </div>

                <?php if ($page != 'foto') { ?>
                <div class="form-group">
                    <label>Isi Konten</label>
                    <textarea name="isi" class="form-control" required placeholder="Tulis deskripsi atau isi konten..."><?php echo $edit_isi; ?></textarea>
                </div>
                <?php } ?>

                <?php if ($page == 'pengumuman') { 
                    // Get prioritas if editing
                    $edit_prioritas = '';
                    if ($is_edit && $id_edit) {
                        $pq = mysqli_query($koneksi, "SELECT prioritas FROM pengumuman WHERE id='$id_edit'");
                        if ($pr = mysqli_fetch_assoc($pq)) $edit_prioritas = $pr['prioritas'];
                    }
                ?>
                <div class="form-group">
                    <label>Prioritas</label>
                    <select name="prioritas" class="form-control">
                        <option value="normal" <?php if($edit_prioritas == 'normal') echo 'selected'; ?>>Normal</option>
                        <option value="penting" <?php if($edit_prioritas == 'penting') echo 'selected'; ?>>⭐ Penting</option>
                    </select>
                </div>
                <?php } ?>

                <?php if ($page == 'pengumuman') { ?>
                <!-- Styled PDF Upload for Pengumuman -->
                <div class="form-group" style="background: #fff8f8; padding: 20px; border-radius: 12px; border: 2px dashed #ffcdd2;">
                    <label style="margin-bottom: 15px; display: block;"><i class="fas fa-file-pdf" style="color: #c62828;"></i> Lampiran PDF <span style="color: #999; font-weight: normal;">(Opsional)</span></label>
                    <input type="file" name="gambar" accept=".pdf,application/pdf" style="width: 100%;">
                    <?php if($is_edit && $edit_gambar) { ?>
                    <div style="margin-top: 12px; padding: 10px; background: #ffebee; border-radius: 8px; font-size: 0.85rem; color: #c62828;">
                        <i class="fas fa-file-pdf"></i> File saat ini: <strong><?php echo $edit_gambar; ?></strong>
                    </div>
                    <?php } ?>
                    <small style="display: block; margin-top: 10px; color: #666;">Format: PDF. Maks 2 MB</small>
                </div>
                <?php } elseif ($page != 'perpustakaan') { ?>
                <!-- Styled Image Upload for Other Pages -->
                <div class="form-group" style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 2px dashed #dee2e6;">
                    <label style="margin-bottom: 15px; display: block;"><i class="fas fa-image" style="color: #004029;"></i> Gambar <?php if(!$is_edit) echo '<span style="color: #e74c3c;">*</span>'; ?></label>
                    <input type="file" name="gambar" accept="image/*" <?php if(!$is_edit) echo "required"; ?> style="width: 100%;">
                    <?php if($is_edit && $edit_gambar) { ?>
                    <div style="margin-top: 12px; padding: 10px; background: #e8f5e9; border-radius: 8px; font-size: 0.85rem; color: #2e7d32;">
                        <i class="fas fa-check-circle"></i> File saat ini: <strong><?php echo $edit_gambar; ?></strong>
                    </div>
                    <?php } ?>
                    <small style="display: block; margin-top: 10px; color: #666;">Format: JPG, PNG, GIF. Maks 1 MB</small>
                </div>
                <?php } ?>

                <?php if ($page == 'perpustakaan') { 
                    // Get additional fields if editing
                    $edit_pengarang = ''; $edit_kategori = ''; $edit_tahun = ''; $edit_penerbit = ''; $edit_file_buku = '';
                    if ($is_edit && $id_edit) {
                        $bq = mysqli_query($koneksi, "SELECT * FROM buku WHERE id='$id_edit'");
                        if ($br = mysqli_fetch_assoc($bq)) {
                            $edit_pengarang = $br['pengarang'];
                            $edit_kategori = $br['kategori'];
                            $edit_tahun = $br['tahun_terbit'];
                            $edit_penerbit = $br['penerbit'];
                            $edit_file_buku = $br['file_buku'];
                        }
                    }
                ?>
                
                <!-- Pengarang -->
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Pengarang</label>
                    <input type="text" name="pengarang" class="form-control" value="<?php echo $edit_pengarang; ?>" required placeholder="Nama pengarang buku...">
                </div>
                
                <!-- Row: Kategori, Tahun, Penerbit -->
                <div class="form-row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label><i class="fas fa-folder"></i> Kategori</label>
                        <select name="kategori" class="form-control" style="height: 48px;">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Novel" <?php if($edit_kategori == 'Novel') echo 'selected'; ?>>Novel</option>
                            <option value="Pendidikan" <?php if($edit_kategori == 'Pendidikan') echo 'selected'; ?>>Pendidikan</option>
                            <option value="Sains & Teknologi" <?php if($edit_kategori == 'Sains & Teknologi') echo 'selected'; ?>>Sains & Teknologi</option>
                            <option value="Sejarah" <?php if($edit_kategori == 'Sejarah') echo 'selected'; ?>>Sejarah</option>
                            <option value="Agama" <?php if($edit_kategori == 'Agama') echo 'selected'; ?>>Agama</option>
                            <option value="Bahasa" <?php if($edit_kategori == 'Bahasa') echo 'selected'; ?>>Bahasa</option>
                            <option value="Buku Bacaan" <?php if($edit_kategori == 'Buku Bacaan') echo 'selected'; ?>>Buku Bacaan</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label><i class="fas fa-calendar-alt"></i> Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" class="form-control" value="<?php echo $edit_tahun; ?>" placeholder="<?php echo date('Y'); ?>" min="1900" max="2100" style="height: 48px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label><i class="fas fa-building"></i> Penerbit</label>
                        <input type="text" name="penerbit" class="form-control" value="<?php echo $edit_penerbit; ?>" placeholder="Nama penerbit..." style="height: 48px;">
                    </div>
                </div>
                
                <!-- Row: Cover & File E-Book -->
                <div class="form-row" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div class="form-group" style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 2px dashed #dee2e6;">
                        <label style="margin-bottom: 15px; display: block;"><i class="fas fa-image" style="color: #004029;"></i> Cover Buku <span style="color: #999; font-weight: normal;">(Opsional)</span></label>
                        <input type="file" name="gambar" accept="image/*" style="width: 100%;">
                        <?php if($is_edit && $edit_gambar) { ?>
                        <div style="margin-top: 12px; padding: 10px; background: #e8f5e9; border-radius: 8px; font-size: 0.85rem; color: #2e7d32;">
                            <i class="fas fa-check-circle"></i> File saat ini: <strong><?php echo $edit_gambar; ?></strong>
                        </div>
                        <?php } ?>
                        <small style="display: block; margin-top: 10px; color: #666;">Format: JPG, PNG, GIF. Maks 1 MB</small>
                    </div>
                    <div class="form-group" style="background: #fff8f8; padding: 20px; border-radius: 12px; border: 2px dashed #ffcdd2;">
                        <label style="margin-bottom: 15px; display: block;"><i class="fas fa-file-pdf" style="color: #c62828;"></i> File E-Book <span style="color: #999; font-weight: normal;">(PDF)</span></label>
                        <input type="file" name="file_buku" accept=".pdf,application/pdf" style="width: 100%;">
                        <?php if($is_edit && $edit_file_buku) { ?>
                        <div style="margin-top: 12px; padding: 10px; background: #ffebee; border-radius: 8px; font-size: 0.85rem; color: #c62828;">
                            <i class="fas fa-file-pdf"></i> File saat ini: <strong><?php echo $edit_file_buku; ?></strong>
                        </div>
                        <?php } ?>
                        <small style="display: block; margin-top: 10px; color: #666;">Format: PDF. Maks 10 MB</small>
                    </div>
                </div>
                
                <?php } ?>

                <button type="submit" name="simpan" class="btn-submit">
                    <i class="fas fa-save"></i> <?php echo $is_edit ? "Update Data" : "Simpan Data"; ?>
                </button>

                <?php if($is_edit) { ?>
                    <a href="input_berita.php?page=<?php echo $page; ?>" class="btn-cancel">Batal Edit</a>
                <?php } ?>
            </form>
        </div>

        <!-- TABLE SECTION -->
        <div class="card">
            <h2 style="border-left-color: var(--text-dark); margin-bottom: 15px;">Daftar <?php echo ucfirst($page); ?></h2>
            <div class="table-responsive">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%"><?php echo ($page == 'pengumuman') ? 'Lampiran' : 'Gambar'; ?></th>
                            <th>Judul / Informasi</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $table_name = "berita"; // default
                        if ($page == 'prestasi') $table_name = "prestasi";
                        if ($page == 'ekskul') $table_name = "ekstrakurikuler";
                        if ($page == 'foto') $table_name = "foto";
                        if ($page == 'pengumuman') $table_name = "pengumuman";
                        if ($page == 'perpustakaan') $table_name = "buku";
        
                        $query = mysqli_query($koneksi, "SELECT * FROM $table_name ORDER BY id DESC");
                        
                        if(mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_array($query)) {
                                $img_col = 'gambar';
                                if ($page == 'foto') $img_col = 'file_foto';
                                if ($page == 'pengumuman') $img_col = 'lampiran';
                                if ($page == 'perpustakaan') $img_col = 'cover';
                                $title_col = ($page == 'ekskul') ? 'nama_ekskul' : 'judul';
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <?php if ($page == 'pengumuman') { 
                                    if($row[$img_col]) { ?>
                                        <a href="uploads/<?php echo $row[$img_col]; ?>" target="_blank" style="display:inline-flex; align-items:center; gap:5px; padding:8px 12px; background:#e74c3c; color:white; border-radius:8px; text-decoration:none; font-size:0.85rem;">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    <?php } else { echo "<span style='color:#999; font-size:0.85rem;'><i class='fas fa-minus-circle'></i> No File</span>"; }
                                } elseif ($page == 'perpustakaan') {
                                    if($row['cover']) { ?>
                                        <img src="uploads/<?php echo $row['cover']; ?>" class="thumb-img">
                                    <?php } else { ?>
                                        <div style="width:60px;height:60px;background:linear-gradient(135deg,#004029,#006644);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    <?php } 
                                } else { 
                                    if($row[$img_col]) { ?>
                                        <img src="uploads/<?php echo $row[$img_col]; ?>" class="thumb-img">
                                    <?php } else { echo "-"; } 
                                } ?>
                            </td>
                            <td>
                                <strong><?php echo $row[$title_col]; ?></strong>
                                <?php if ($page == 'perpustakaan') { ?>
                                    <br><small style="color:#666;"><i class="fas fa-user"></i> <?php echo $row['pengarang']; ?></small>
                                    <?php if($row['file_buku']) { ?>
                                        <br><a href="uploads/<?php echo $row['file_buku']; ?>" target="_blank" style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; background:#e74c3c; color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; margin-top:5px;"><i class="fas fa-file-pdf"></i> <?php echo $row['kategori'] ?: 'E-Book'; ?></a>
                                    <?php } ?>
                                <?php } ?>
                            </td>
                            <td class="action-links">
                                <a href="input_berita.php?page=<?php echo $page; ?>&aksi=edit&id=<?php echo $row['id']; ?>" class="btn-edit-row">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="hapus_data.php?page=<?php echo $page; ?>&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin hapus?')" class="btn-delete-row">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='4' class='empty-state'><i class='fas fa-folder-open' style='font-size:2rem; display:block; margin-bottom:10px;'></i>Belum ada data.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <?php endif; ?>

</main>

<!-- Footer Credit -->
<footer style="position:fixed; bottom:0; left:220px; right:0; background:linear-gradient(135deg, #004029, #006644); color:white; text-align:center; padding:12px 0; font-size:0.8rem; transition: left 0.3s ease;" id="adminFooter">
    &copy; <?php echo date('Y'); ?> SMAN 1 Bengkalis | Admin Panel v1.1
</footer>

<!-- Mobile Menu Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const footer = document.getElementById('adminFooter');
    
    // ========== USER DROPDOWN MENU ==========
    const userDropdownBtn = document.getElementById('userDropdownBtn');
    const userDropdown = userDropdownBtn ? userDropdownBtn.closest('.topbar-user-dropdown') : null;
    
    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('open');
            }
        });
    }
    
    function toggleSidebar() {
        menuBtn.classList.toggle('active');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
    
    menuBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    
    // Close sidebar when clicking a nav link on mobile
    const navLinks = sidebar.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });
    });
});

// Toggle Nav Section (Collapse/Expand)
function toggleNavSection(element) {
    const section = element.parentElement;
    section.classList.toggle('open');
    
    // Save state to localStorage
    const sectionIndex = Array.from(document.querySelectorAll('.nav-section')).indexOf(section);
    const openSections = JSON.parse(localStorage.getItem('adminNavSections') || '{}');
    openSections[sectionIndex] = section.classList.contains('open');
    localStorage.setItem('adminNavSections', JSON.stringify(openSections));
}

// Restore nav section states on page load
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.nav-section');
    const openSections = JSON.parse(localStorage.getItem('adminNavSections') || '{}');
    
    sections.forEach((section, index) => {
        // If we have a saved state, use it; otherwise keep open
        if (openSections.hasOwnProperty(index)) {
            if (openSections[index]) {
                section.classList.add('open');
            } else {
                section.classList.remove('open');
            }
        }
    });
    
    // ========== SIDEBAR TOGGLE (Desktop Collapse) ==========
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    // Restore collapsed state from localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed && window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('expanded');
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Only work on desktop
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('expanded');
                
                // Update footer position
                const adminFooter = document.getElementById('adminFooter');
                if (adminFooter) {
                    if (sidebar.classList.contains('collapsed')) {
                        adminFooter.style.left = '70px';
                    } else {
                        adminFooter.style.left = '220px';
                    }
                }
                
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                
                // Change icon
                const icon = this.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.className = 'fas fa-chevron-right';
                } else {
                    icon.className = 'fas fa-bars';
                }
            }
        });
        
        // Set initial icon based on state
        if (isCollapsed && window.innerWidth > 768) {
            sidebarToggle.querySelector('i').className = 'fas fa-chevron-right';
        }
    }
    
    // Toggle button inside nav (for expanding when collapsed)
    const sidebarToggleNav = document.getElementById('sidebarToggleNav');
    if (sidebarToggleNav) {
        sidebarToggleNav.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (window.innerWidth > 768) {
                sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
                localStorage.setItem('sidebarCollapsed', 'false');
                
                // Update footer position
                const adminFooter = document.getElementById('adminFooter');
                if (adminFooter) {
                    adminFooter.style.left = '220px';
                }
                
                // Reset header toggle icon
                if (sidebarToggle) {
                    sidebarToggle.querySelector('i').className = 'fas fa-bars';
                }
            }
        });
    }
    
    // Set initial footer position based on collapsed state
    const adminFooter = document.getElementById('adminFooter');
    if (adminFooter && isCollapsed && window.innerWidth > 768) {
        adminFooter.style.left = '70px';
    }
});
</script>

</body>
</html>
