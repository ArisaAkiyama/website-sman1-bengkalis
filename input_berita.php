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

// Menentukan halaman aktif (default: berita)
$page = isset($_GET['page']) ? $_GET['page'] : 'berita';
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
        // Skip file requirement for pengumuman (lampiran is optional)
        if ($nama_file == '' && $page != 'pengumuman') {
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
    <link rel="stylesheet" href="css/admin.css?v=1">
</head>
<body>

<header class="admin-header">
    <div class="container header-content">
        <div class="brand">
            <h1><i class="fas fa-graduation-cap"></i> Admin<span>Panel</span></h1>
        </div>
        <div class="user-actions">
            <span class="user-info"><i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?></span>
            <a href="index.php" class="btn-view" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Web</a>
            <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </div>
    </div>
</header>

<div class="container">
    
    <!-- Navigation Tabs -->
    <div class="nav-scroller">
        <nav class="nav-tabs">
            <a href="?page=berita" class="nav-link <?php if($page=='berita') echo 'active'; ?>"><i class="fas fa-newspaper"></i> Berita</a>
            <a href="?page=prestasi" class="nav-link <?php if($page=='prestasi') echo 'active'; ?>"><i class="fas fa-trophy"></i> Prestasi</a>
            <a href="?page=ekskul" class="nav-link <?php if($page=='ekskul') echo 'active'; ?>"><i class="fas fa-basketball-ball"></i> Ekskul</a>
            <a href="?page=foto" class="nav-link <?php if($page=='foto') echo 'active'; ?>"><i class="fas fa-images"></i> Foto</a>
            <a href="?page=pengumuman" class="nav-link <?php if($page=='pengumuman') echo 'active'; ?>"><i class="fas fa-bullhorn"></i> Pengumuman</a>
            <?php $unread = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM pesan WHERE status='belum_dibaca'"))['c']; ?>
            <a href="?page=pesan" class="nav-link <?php if($page=='pesan') echo 'active'; ?>"><i class="fas fa-envelope"></i> Pesan <?php if($unread > 0): ?><span class="nav-badge"><?php echo $unread; ?></span><?php endif; ?></a>
        </nav>
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
    <?php if($page == 'pesan'): ?>
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
    
    // Get data with limit
    $pesan_query = mysqli_query($koneksi, "SELECT * FROM pesan ORDER BY tanggal_kirim DESC LIMIT $offset, $limit");
    ?>
    <div class="card" style="margin-top:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px;">
            <h2 style="margin-bottom:0;"><i class="fas fa-envelope"></i> Pesan Masuk <span style="font-weight:400; font-size:0.9rem; color:#666;">(<?php echo $total_data; ?> pesan)</span></h2>
            <?php if($total_data > 0): ?>
            <div style="display:flex; gap:10px;">
                <a href="?page=pesan&mark_all_read=1" onclick="return confirm('Tandai semua pesan sebagai dibaca?')" style="padding:10px 18px; background:linear-gradient(135deg, #27ae60, #2ecc71); color:white; border-radius:8px; text-decoration:none; font-size:0.85rem; font-weight:600; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-check-double"></i> Tandai Baca Semua</a>
                <a href="?page=pesan&delete_all_pesan=1" onclick="return confirm('HAPUS SEMUA PESAN? Tindakan ini tidak dapat dibatalkan!')" style="padding:10px 18px; background:linear-gradient(135deg, #e74c3c, #c0392b); color:white; border-radius:8px; text-decoration:none; font-size:0.85rem; font-weight:600; display:inline-flex; align-items:center; gap:6px;"><i class="fas fa-trash-alt"></i> Hapus Semua</a>
            </div>
            <?php endif; ?>
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
        <?php if($total_pages > 1): ?>
        <div style="display:flex; justify-content:center; align-items:center; gap:8px; margin-top:25px; padding-top:20px; border-top:1px solid #eee;">
            <?php if($pesan_page > 1): ?>
            <a href="?page=pesan&p=1" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-double-left"></i></a>
            <a href="?page=pesan&p=<?php echo $pesan_page-1; ?>" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-left"></i></a>
            <?php endif; ?>
            
            <?php 
            $start_pg = max(1, $pesan_page - 2);
            $end_pg = min($total_pages, $pesan_page + 2);
            for($i = $start_pg; $i <= $end_pg; $i++): 
            ?>
            <a href="?page=pesan&p=<?php echo $i; ?>" style="padding:8px 14px; background:<?php echo $i == $pesan_page ? 'linear-gradient(135deg, #004029, #006644)' : '#f0f0f0'; ?>; color:<?php echo $i == $pesan_page ? 'white' : '#333'; ?>; border-radius:8px; text-decoration:none; font-weight:<?php echo $i == $pesan_page ? '600' : '400'; ?>; font-size:0.9rem;"><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if($pesan_page < $total_pages): ?>
            <a href="?page=pesan&p=<?php echo $pesan_page+1; ?>" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-right"></i></a>
            <a href="?page=pesan&p=<?php echo $total_pages; ?>" style="padding:8px 14px; background:#f0f0f0; border-radius:8px; text-decoration:none; color:#333; font-size:0.9rem;"><i class="fas fa-angle-double-right"></i></a>
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
                <div class="form-group">
                    <label><i class="fas fa-file-pdf"></i> Lampiran PDF (Opsional)</label>
                    <input type="file" name="gambar" accept=".pdf,application/pdf">
                    <?php if($is_edit && $edit_gambar) { ?>
                        <div style="margin-top:10px; font-size:0.85rem; color:#666;">
                            <i class="fas fa-file-pdf"></i> Saat ini: <?php echo $edit_gambar; ?>
                        </div>
                    <?php } ?>
                    <small style="color:#666; font-size:0.8rem;">Format: PDF, Maksimal 2 MB</small>
                </div>
                <?php } else { ?>
                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="gambar" <?php if(!$is_edit) echo "required"; ?>>
                    <?php if($is_edit && $edit_gambar) { ?>
                        <div style="margin-top:10px; font-size:0.85rem; color:#666;">
                            <i class="fas fa-image"></i> Saat ini: <?php echo $edit_gambar; ?>
                        </div>
                    <?php } ?>
                    <small style="color:red; font-size:0.8rem;">* Maksimal 1 MB</small>
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
        
                        $query = mysqli_query($koneksi, "SELECT * FROM $table_name ORDER BY id DESC");
                        
                        if(mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_array($query)) {
                                $img_col = 'gambar';
                                if ($page == 'foto') $img_col = 'file_foto';
                                if ($page == 'pengumuman') $img_col = 'lampiran';
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
                                } else { 
                                    if($row[$img_col]) { ?>
                                        <img src="uploads/<?php echo $row[$img_col]; ?>" class="thumb-img">
                                    <?php } else { echo "-"; } 
                                } ?>
                            </td>
                            <td>
                                <strong><?php echo $row[$title_col]; ?></strong>
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

</div>

<!-- Footer Credit -->
<footer style="background:linear-gradient(135deg, #004029, #006644); color:white; text-align:center; padding:20px 0; margin-top:40px;">
    <p style="margin:0; font-size:0.95rem;">&copy; <?php echo date('Y'); ?> SMAN 1 Bengkalis | Admin Panel v1.0</p>
</footer>

</body>
</html>
