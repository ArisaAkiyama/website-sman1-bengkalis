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

### Admin Panel
- 📊 Dashboard admin dengan tab navigation
- ✏️ CRUD Berita, Prestasi, Ekskul, Foto, Pengumuman
- 📬 Inbox pesan dari pengunjung
- 📎 Upload gambar dan file PDF

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
   git clone https://github.com/username/sman1bengkalis.git
   ```

2. **Import database**
   - Buat database baru di phpMyAdmin
   - Import file SQL (jika ada) atau tabel akan dibuat otomatis

3. **Konfigurasi database**
   - Buat file `koneksi.php`:
   ```php
   <?php
   $koneksi = mysqli_connect("localhost", "root", "", "nama_database");
   if (!$koneksi) {
       die("Koneksi gagal: " . mysqli_connect_error());
   }
   ?>
   ```

4. **Setup admin user**
   - Buat file `setup_admin.php`:
   ```php
   <?php
   include 'koneksi.php';
   $username = 'admin';
   $password = password_hash('password_anda', PASSWORD_BCRYPT);
   // ... (lihat dokumentasi)
   ?>
   ```
   - Jalankan di browser: `http://localhost/project/setup_admin.php`
   - **HAPUS file setelah selesai!**

5. **Akses website**
   - Frontend: `http://localhost/project/`
   - Admin: `http://localhost/project/login.php`

---

## 📁 Struktur Folder

```
├── css/           # File stylesheet
├── js/            # File JavaScript
├── uploads/       # Folder upload gambar/PDF
├── index.php      # Halaman utama
├── login.php      # Halaman login admin
├── input_berita.php  # Admin panel
└── ...
```

---

## 📸 Screenshot


![](screenshots/Screenshot%202025-12-11%20231852.png)


![](screenshots/Screenshot%202025-12-11%20231918.png)


![](screenshots/Screenshot%202025-12-11%20231935.png)


![](screenshots/Screenshot%202025-12-11%20231958.png)


![](screenshots/Screenshot%202025-12-11%20232020.png)


![](screenshots/Screenshot%202025-12-11%20232044.png)


![](screenshots/Screenshot%202025-12-11%20232106.png)


![](screenshots/Screenshot%202025-12-11%20232135.png)

![](screenshots/Screenshot%202025-12-11%20232144.png)

![](screenshots/Screenshot%202025-12-11%20232201.png)

---

## 📝 License

Copyright © 2025 SMAN 1 Bengkalis. All rights reserved.

---

## 👨‍💻 Kontributor

- Developer: [ArisaAkiyama]

---

**Dibuat dengan ❤️ untuk SMAN 1 Bengkalis**
