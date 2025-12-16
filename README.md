# 🎓 Website SMAN 1 Bengkalis

Website resmi Sekolah Menengah Atas Negeri 1 Bengkalis, Riau.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

---

## 📋 Fitur

### Halaman Publik
- 🏠 Beranda dengan hero section, info bar, dan konten dinamis
- 📰 Berita & Pengumuman
- 🏆 Prestasi Siswa
- 🎯 Ekstrakurikuler
- 📸 Galeri Foto
- 👨‍🏫 Profil Guru & Staff
- 📞 Halaman Kontak dengan form pesan
- 📚 **Perpustakaan Digital** - Koleksi e-book dengan pencarian
- 🤖 **AI Chatbot** - Asisten virtual dengan Groq AI

### Admin Panel
- 📊 Dashboard dengan statistik lengkap
- 🎛️ Sidebar navigation dengan collapsible sections
- ✏️ CRUD Berita, Prestasi, Ekskul, Foto, Pengumuman
- 📚 **Manajemen Perpustakaan** - Upload buku, cover, dan file PDF
- 📬 Inbox pesan dari pengunjung
- 📎 Styled upload boxes untuk gambar dan PDF

### Perpustakaan Digital
- 🔍 Pencarian berdasarkan judul, pengarang, kategori
- 📂 7 kategori buku (Novel, Pendidikan, Sains, dll)
- 📖 Baca online atau download PDF
- 📊 Statistik buku, pengarang, dan pembaca
- ⭐ Buku populer berdasarkan views
- 🏷️ Toggle kategori dengan animasi
- ⚠️ Validasi form pencarian

### AI Chatbot
- 🤖 Integrasi Groq AI API
- 💬 Respons cerdas tentang sekolah dan website
- 📊 Real-time data dari database (buku, berita, dll)
- 🔍 **Pencarian Cerdas** - Cari dari semua tabel sekaligus dengan satu pertanyaan
- ⚡ Quick replies untuk pertanyaan umum
- 🔄 Fallback ke FAQ lokal jika API gagal

### Keamanan
- 🔐 Login dengan password hashing (bcrypt)
- 🛡️ Brute force protection
- 🔒 CSRF token protection
- ⏱️ Session timeout

---

## 🚀 Instalasi

### Persyaratan
- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/ArisaAkiyama/website-sman1-bengkalis.git
   ```

2. **Konfigurasi database**
   - Buat file `koneksi.php`:
   ```php
   <?php
   $koneksi = mysqli_connect("localhost", "root", "", "nama_database");
   if (!$koneksi) {
       die("Koneksi gagal: " . mysqli_connect_error());
   }
   ?>
   ```

3. **Setup admin user**
   - Jalankan di browser: `http://localhost/project/setup_admin.php`
   - **HAPUS file setelah selesai!**

4. **Akses website**
   - Frontend: `http://localhost/project/`
   - Perpustakaan: `http://localhost/project/perpustakaan.php`
   - Admin: `http://localhost/project/login.php`

> 💡 **Catatan:** Tabel database akan dibuat otomatis saat pertama kali diakses.

---

## 📁 Struktur Folder

```
├── css/
│   ├── styles.css          # Stylesheet utama
│   ├── admin.css           # Stylesheet admin panel
│   └── perpustakaan.css    # Stylesheet perpustakaan
├── js/
│   ├── script.js           # JavaScript utama
│   └── perpustakaan.js     # JavaScript perpustakaan
├── uploads/                # Folder upload gambar/PDF
├── screenshots/            # Screenshot untuk README
├── index.php               # Halaman utama
├── perpustakaan.php        # Perpustakaan digital
├── login.php               # Halaman login admin
├── input_berita.php        # Admin panel
├── hapus_data.php          # Handler hapus data
├── koneksi.php             # Konfigurasi database
└── setup_admin.php         # Setup user admin
```

---

## 📸 Screenshot

### Halaman Utama
![](screenshots/Screenshot%202025-12-11%20231852.png)

### Berita & Pengumuman
![](screenshots/Screenshot%202025-12-11%20231918.png)

### Galeri & Konten
![](screenshots/Screenshot%202025-12-11%20231935.png)

### Prestasi
![](screenshots/Screenshot%202025-12-11%20231958.png)

### Footer
![](screenshots/Screenshot%202025-12-11%20232020.png)

### Halaman Kontak
![](screenshots/Screenshot%202025-12-11%20232044.png)

### Admin Dashboard
![](screenshots/Screenshot%202025-12-11%20232106.png)

### Admin Panel - Manajemen Konten
![](screenshots/Screenshot%202025-12-11%20232135.png)

![](screenshots/Screenshot%202025-12-11%20232144.png)

### Pesan Masuk
![](screenshots/Screenshot%202025-12-11%20232201.png)

---

## 🔄 Changelog

Lihat [CHANGELOG.md](CHANGELOG.md) untuk daftar perubahan lengkap.

### v1.4.0 (2025-12-16)
- ✨ **Pencarian Cerdas** - AI chatbot bisa mencari dari semua tabel sekaligus
- ✨ Smart keyword extraction untuk pemahaman intent
- ✨ Navigation links ke halaman terkait

### v1.3.0 (2025-12-13)
- ✨ AI Chatbot dengan Groq API integration
- ✨ Toggle kategori perpustakaan
- ✨ Validasi form pencarian
- 🐛 Fixed navigasi kategori perpustakaan

### v1.2.0 (2025-12-12)
- ✨ Perpustakaan Digital dengan database integration
- ✨ Collapsible sidebar navigation
- ✨ Styled upload boxes
- 🐛 Mobile menu button visibility fix

---

## 📝 License

Copyright © 2025 SMAN 1 Bengkalis. All rights reserved.

---

## 👨‍💻 Kontributor

- Developer: [ArisaAkiyama](https://github.com/ArisaAkiyama)

---

**Dibuat dengan ❤️ untuk SMAN 1 Bengkalis**
