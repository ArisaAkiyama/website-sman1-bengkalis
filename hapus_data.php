<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    echo "<script>alert('Anda belum login!'); window.location='login.php';</script>";
    exit;
}

include 'koneksi.php';

if (isset($_GET['page']) && isset($_GET['id'])) {
    $page = $_GET['page'];
    $id   = $_GET['id'];

    $table = "";
    $img_col = "gambar";

    if ($page == 'berita') { $table = "berita"; }
    elseif ($page == 'prestasi') { $table = "prestasi"; }
    elseif ($page == 'ekskul') { $table = "ekstrakurikuler"; }
    elseif ($page == 'foto') { $table = "foto"; $img_col = "file_foto"; }
    elseif ($page == 'pengumuman') { $table = "pengumuman"; $img_col = "lampiran"; }

    if ($table) {
        // Hapus Gambar
        $q_img = mysqli_query($koneksi, "SELECT $img_col FROM $table WHERE id='$id'");
        $d_img = mysqli_fetch_array($q_img);
        if ($d_img && file_exists('./uploads/' . $d_img[$img_col])) {
            unlink('./uploads/' . $d_img[$img_col]);
        }

        // Hapus Data
        mysqli_query($koneksi, "DELETE FROM $table WHERE id='$id'");
        
        header("Location: input_berita.php?page=$page&success=hapus");
        exit;
    }
} else {
    header("Location: input_berita.php");
}
?>
