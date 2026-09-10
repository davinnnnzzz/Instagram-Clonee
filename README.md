# 📸 Instagram Clone — Web Application

**Instagram Clone** adalah aplikasi web berbasis *full-stack* sederhana yang terinspirasi dari platform media sosial populer Instagram. Dibuat menggunakan **PHP Native** dan **MySQL** tanpa bergantung pada *framework* berat, sehingga ringan dan dapat dijalankan dengan mudah melalui web server lokal seperti XAMPP.

---

## ✨ Fitur Utama

- **Autentikasi Pengguna:** Sistem pendaftaran akun baru (`registrasi.php`) dan masuk akun (`login.php`) dengan validasi data.
- **Manajemen Postingan & Media:**
  - 🖼️ **Upload Foto:** Mengunggah gambar/foto yang secara otomatis tersimpan di folder `uploads/`.
  - 📝 **Feed Beranda:** Menampilkan postingan gambar yang telah diunggah oleh pengguna.
- **Sistem API & Helper Scripts:** Dilengkapi dengan script pembantu (`scripts/`) untuk pemrosesan file serta endpoint API sederhana (`api.php`).
- **Database Terintegrasi:** Struktur tabel relasional untuk menyimpan data pengguna dan postingan.

---

## 🛠️ Teknologi yang Digunakan

- **PHP (Native):** Bahasa pemrograman utama untuk menangani logika *backend* dan *routing*.
- **MySQL:** Sistem manajemen basis data (*database*) relasional.
- **HTML5 & CSS3:** Struktur tampilan antarmuka (*UI*) dan penataan gaya visual halaman web.
- **JavaScript (Vanilla):** Menangani interaktivitas pada *frontend* (`public/js/script.js`).
- **Apache / XAMPP:** Lingkungan *web server* lokal untuk menjalankan aplikasi.

---

## 📁 Struktur Berkas

```text
Sistem_ig/
├── config/
│   └── koneksi.php       # Konfigurasi penghubung database MySQL
├── public/
│   ├── css/              # File styling (login.css, registrasi.css, style.css)
│   └── js/               # File skrip interaktif client-side
├── scripts/              # Skrip helper dan pengolahan internal
├── uploads/              # Folder tempat penyimpanan file gambar/media
├── api.php               # Endpoint API sederhana
├── db_init.sql           # Skrip inisialisasi awal database
├── instagram_clone.sql   # Dump data & tabel database MySQL
├── index.php             # Halaman utama / Feed
├── login.php             # Halaman masuk akun
├── registrasi.php        # Halaman pendaftaran akun
└── README.md             # Dokumentasi proyek
