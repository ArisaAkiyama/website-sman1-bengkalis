<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil nama gambar dulu untuk dihapus dari folder
    $query_gambar = mysqli_query($koneksi, "SELECT gambar FROM berita WHERE id='$id'");
    $data_gambar = mysqli_fetch_array($query_gambar);
    $file_gambar = './uploads/' . $data_gambar['gambar'];
    
    // Hapus file gambar jika ada
    if (file_exists($file_gambar)) {
        unlink($file_gambar);
    }
    
    // Hapus data dari database
    $hapus = mysqli_query($koneksi, "DELETE FROM berita WHERE id='$id'");
    
    if ($hapus) {
        echo "<script>alert('Berita berhasil dihapus!'); window.location='input_berita.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus berita.'); window.location='input_berita.php';</script>";
    }
} else {
    header("Location: input_berita.php");
}
?>
