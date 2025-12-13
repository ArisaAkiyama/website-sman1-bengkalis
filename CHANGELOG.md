# 📋 CHANGELOG - Website SMAN 1 Bengkalis

## Version 1.3.0 (13 Desember 2025)
**AI Chatbot Integration & Perpustakaan Enhancement**

---

### 🤖 AI Chatbot (Groq API)
- ✅ **Chatbot Terintegrasi** di halaman Beranda dan Berita
- ✅ **Groq API Integration** untuk respons AI cerdas
- ✅ **Real-time Database Query** - AI membaca data buku, berita, pengumuman langsung dari database
- ✅ **System Prompt Kustom** - AI hanya menjawab tentang SMAN 1 Bengkalis
- ✅ **Quick Replies** - Tombol pertanyaan cepat
- ✅ **FAB Integration** - Tombol chatbot di Floating Action Button
- ✅ **Error Handling** - Fallback ke FAQ lokal jika API gagal
- ✅ **Responsive Design** - Tampilan chatbot lebih besar dan mudah dibaca

### 📚 Perpustakaan Digital Enhancement
- ✅ **Toggle Kategori** - Klik "Semua" untuk expand/collapse daftar kategori
- ✅ **Filter Kategori Aktif** - Klik Novel/Sejarah/dll langsung filter buku
- ✅ **Validasi Pencarian** - Notifikasi error jika field kosong saat klik "Cari"
- ✅ **Subcategory Links** - Link kategori dengan styling hover dan active state
- ✅ **Case-insensitive Search** - Filter kategori tidak sensitif huruf besar/kecil

### 🔧 Perbaikan Bug
- ✅ **Fixed:** `e.preventDefault()` di perpustakaan.js memblokir navigasi kategori
- ✅ **Fixed:** Query kategori sekarang case-insensitive

### 📁 File Baru
- ✅ `config.php` - Konfigurasi Groq API key dan system prompt
- ✅ `api/chat.php` - Backend proxy untuk AI chatbot
- ✅ `js/chatbot.js` - Frontend chatbot logic
- ✅ `css/chatbot.css` - Styling chatbot

---

## Version 1.2.0 (12 Desember 2025)
**Perpustakaan Digital dengan Database Integration**

---

### 📚 Perpustakaan Digital (perpustakaan.php)
- ✅ **Halaman baru** dengan desain modern dan card layout
- ✅ **Database Integration** - Tabel `buku` dengan auto-create
- ✅ **Fitur pencarian** - By judul, pengarang, kategori
- ✅ **7 Kategori buku** - Novel, Pendidikan, Sains & Teknologi, Sejarah, Agama, Bahasa, Buku Bacaan
- ✅ **Sidebar** dengan kategori dan buku populer
- ✅ **Pagination** untuk daftar buku
- ✅ **View counter** untuk tracking popularitas
- ✅ **Cover upload** dan **PDF file** support
- ✅ **Modal Detail Buku** - Klik untuk lihat info lengkap

### 🎛️ Admin Panel - Perpustakaan
- ✅ **Tab baru** di admin panel untuk manajemen buku
- ✅ **CRUD lengkap** - Tambah, edit, hapus buku
- ✅ **Styled upload boxes** - Drag & drop area untuk cover dan PDF
- ✅ **Display nama file** yang user-friendly (Kategori - Judul.pdf)
- ✅ **Preview cover** sebelum upload

### 🐛 Bug Fixes
- ✅ **Fixed:** Mobile menu button tidak hilang saat sidebar aktif

---

## Version 1.1.0 (12 Desember 2025)
**Update Admin Panel dengan Sidebar Layout & Dashboard Statistik**

### 🎨 Admin Panel Redesign

#### Layout Baru - Sidebar Navigation
- ✅ Navigasi dipindah ke **sidebar kiri** (dari horizontal tabs)
- ✅ Sidebar width: 220px (desktop), 260px (mobile)
- ✅ Menu dikelompokkan dalam kategori:
  - **Menu Utama**: Dashboard, Berita, Prestasi, Ekskul
  - **Konten**: Galeri Foto, Pengumuman
  - **Komunikasi**: Pesan Masuk (dengan badge unread)
- ✅ User info dengan avatar di footer sidebar
- ✅ Tombol "Lihat Web" dan "Keluar" di sidebar

#### Dashboard Statistik
- ✅ 6 kartu statistik: Berita, Prestasi, Ekskul, Foto, Pengumuman, Pesan
- ✅ Setiap kartu menampilkan total data dan clickable
- ✅ Badge notifikasi untuk pesan belum dibaca
- ✅ Tampilan "Recent Activity":
  - 3 Berita terbaru dengan thumbnail
  - 3 Pesan terbaru dengan badge "Baru"
- ✅ Welcome banner dengan nama user

#### Responsive Mobile
- ✅ Hamburger menu button (☰) di mobile
- ✅ Sidebar slide-in dengan animasi
- ✅ Dark overlay saat sidebar terbuka
- ✅ Auto-close sidebar saat klik menu item
- ✅ Footer full-width di mobile

---

### 🔧 Perbaikan Lainnya

- ✅ Email FAB menggunakan Gmail Compose URL
- ✅ Hapus file HTML yang tidak digunakan (7 file)
- ✅ Hapus folder Website_SMA yang tidak dipakai
- ✅ Login redirect langsung ke Dashboard (bukan Berita)
- ✅ File `.gitignore` dan `README.md` untuk GitHub

---

### 📦 Repository GitHub

- ✅ Repository: [github.com/ArisaAkiyama/website-sman1-bengkalis](https://github.com/ArisaAkiyama/website-sman1-bengkalis)
- ✅ 10 Screenshot terupload
- ✅ Documentation dengan badges

---

## Version 1.0.0 (11 Desember 2025)
**Release pertama website resmi SMAN 1 Bengkalis**

---

### 🎨 Frontend / Tampilan

#### Halaman Publik
- ✅ **Beranda (index.php)** - Hero section, info bar, berita, prestasi, ekstrakurikuler
- ✅ **Berita (news.php)** - Daftar berita dengan pagination
- ✅ **Detail Berita (detail_berita.php)** - Halaman detail artikel
- ✅ **Prestasi (prestasi.php)** - Galeri prestasi siswa
- ✅ **Detail Prestasi (detail_prestasi.php)** - Halaman detail prestasi
- ✅ **Ekstrakurikuler (ekstrakurikuler.php)** - Daftar kegiatan ekskul
- ✅ **Detail Ekskul (detail_ekskul.php)** - Halaman detail ekstrakurikuler
- ✅ **Pengumuman (pengumuman.php)** - Timeline pengumuman dengan prioritas
- ✅ **Galeri Foto (foto.php)** - Grid galeri foto kegiatan
- ✅ **Profil (profile.php)** - Sambutan Kepala Sekolah
- ✅ **Guru & Staff (karyawan.php)** - Daftar tenaga pendidik
- ✅ **Kontak (contact.php)** - Form kontak dan informasi

#### Fitur UI/UX
- ✅ Desain modern dan responsif (mobile-friendly)
- ✅ Animasi smooth scroll dan hover effects
- ✅ Floating Action Button (FAB) untuk WhatsApp & Email
- ✅ Back to top button
- ✅ Search modal
- ✅ Dropdown navigation menu

---

### 🔧 Backend / Admin Panel

#### Admin Dashboard (input_berita.php)
- ✅ **Manajemen Berita** - CRUD berita dengan gambar
- ✅ **Manajemen Prestasi** - CRUD prestasi siswa
- ✅ **Manajemen Ekstrakurikuler** - CRUD data ekskul
- ✅ **Manajemen Foto** - CRUD galeri foto
- ✅ **Manajemen Pengumuman** - CRUD dengan prioritas & lampiran PDF
- ✅ **Manajemen Pesan** - Inbox pesan dari form kontak

#### Fitur Admin
- ✅ Tab navigation untuk semua modul
- ✅ Upload gambar dan file PDF
- ✅ Edit dan hapus data
- ✅ Notifikasi pesan belum dibaca
- ✅ Responsive admin UI

---

### 🔐 Keamanan

#### Sistem Login
- ✅ Password hashing dengan **bcrypt**
- ✅ **Brute force protection** - Lock setelah 5x gagal (15 menit)
- ✅ **CSRF token** protection
- ✅ **Prepared statements** untuk query login
- ✅ **Session timeout** (30 menit tidak aktif)
- ✅ Session regeneration saat login
- ✅ Secure session configuration

#### File Keamanan
- ✅ `setup_admin.php` - Setup user admin (hapus setelah pakai!)
- ✅ `logout.php` - Logout dengan destroy session lengkap

---

### 📁 Struktur File

```
Project Kecil/
├── css/
│   ├── style.css (main styles)
│   ├── admin.css
│   ├── contact.css
│   ├── detail.css
│   ├── fab.css
│   ├── login.css
│   └── staff.css
├── js/
│   ├── main.js
│   ├── fab.js
│   └── contact.js
├── uploads/ (folder upload gambar/pdf)
├── index.php
├── news.php
├── detail_berita.php
├── prestasi.php
├── detail_prestasi.php
├── ekstrakurikuler.php
├── detail_ekskul.php
├── pengumuman.php
├── foto.php
├── profile.php
├── karyawan.php
├── contact.php
├── login.php
├── logout.php
├── input_berita.php (admin panel)
├── hapus_data.php
├── koneksi.php
└── setup_admin.php
```

---

### 🗄️ Database

#### Tabel yang Digunakan
- `berita` - Artikel berita
- `prestasi` - Data prestasi
- `ekstrakurikuler` - Data ekskul
- `foto` - Galeri foto
- `pengumuman` - Data pengumuman
- `pesan` - Pesan dari form kontak
- `users` - User admin

---

### 📝 Catatan Penting

1. **Sebelum Go-Live:**
   - Jalankan `setup_admin.php` untuk membuat user admin
   - **HAPUS** file `setup_admin.php` setelah selesai
   - Ganti password default (`admin123`) dengan yang lebih kuat

2. **Credential Default:**
   - Username: `admin`
   - Password: `admin123`

3. **Rekomendasi Server:**
   - PHP 7.4+ 
   - MySQL 5.7+
   - HTTPS (disarankan)

---

### 🚀 Pengembangan Selanjutnya (Roadmap v1.2)

- [ ] PPDB Online
- [ ] Perpustakaan Online/E-Book 
- [ ] Portal Siswa & Guru
- [ ] Sistem Polling/Voting
- [ ] Multi-language support
- [ ] Captcha untuk form login & kontak
- [x] Dashboard statistik admin ✅

---

**Dibuat dengan ❤️ untuk SMAN 1 Bengkalis**
