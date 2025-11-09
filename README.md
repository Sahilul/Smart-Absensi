# 📚 Smart Absensi - Sistem Informasi Absensi & Penilaian Sekolah

Sistem informasi terintegrasi untuk manajemen absensi, jurnal mengajar, penilaian siswa, dan rapor berbasis web dengan teknologi QR Code validation.

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38bdf8.svg)](https://tailwindcss.com/)

---

## 🎯 Deskripsi

**Smart Absensi** adalah aplikasi manajemen sekolah berbasis web yang dirancang untuk mempermudah administrasi pendidikan, khususnya dalam pencatatan kehadiran siswa, jurnal pembelajaran, penilaian, dan pembuatan rapor. Sistem ini dilengkapi dengan validasi QR Code untuk memastikan keaslian dokumen rapor.

Aplikasi ini cocok untuk:
- 🏫 Sekolah Dasar (SD/MI)
- 🎓 Sekolah Menengah (SMP/MTs, SMA/MA)
- 📖 Lembaga Pendidikan Formal & Non-Formal

---

## ✨ Fitur Utama

### 👨‍💼 Administrator
- **Dashboard Interaktif**
  - Statistik real-time kehadiran, siswa, guru, dan kelas
  - Grafik persentase kehadiran
  - Overview tahun pelajaran dan semester aktif
  
- **Manajemen Data Master**
  - 📅 Tahun Pelajaran & Semester
  - 🏛️ Kelas (dengan tingkat dan jurusan)
  - 👨‍🎓 Data Siswa (NISN, biodata lengkap, foto)
  - 👨‍🏫 Data Guru (NIK, NIP, biodata lengkap, foto)
  - 📚 Mata Pelajaran (kelompok A/B/C)

- **Manajemen Pembelajaran**
  - 🔗 Penugasan Guru ke Kelas & Mapel
  - 👥 Anggota Kelas per Tahun Pelajaran
  - 📊 Monitoring Jurnal & Absensi

- **Performa & Analisis**
  - 📈 Analisis Kehadiran Siswa per Kelas
  - 📊 Analisis Kinerja Guru (jurnal & kehadiran)
  - 📉 Grafik & Statistik Visual

- **Sistem & Utilitas**
  - ⬆️ Naik Kelas Otomatis
  - 🎓 Kelulusan Siswa
  - 🔐 QR Code Configuration
  - 👁️ Pengaturan Visibilitas Menu

### 👨‍🏫 Guru
- **Jurnal Mengajar**
  - ✍️ Input jurnal harian (topik materi, catatan)
  - 📝 Pencatatan pertemuan ke-n
  - 🖨️ Cetak jurnal per mapel/kelas

- **Absensi Siswa**
  - ✅ Input kehadiran siswa (Hadir, Izin, Sakit, Alfa, Terlambat)
  - 📊 Rekap absensi per pertemuan
  - 📄 Cetak daftar hadir
  - 🔍 Rincian absensi dengan filter periode

- **Penilaian**
  - 📝 Input Nilai Harian (per pertemuan)
  - 📊 Input Nilai STS (Sumatif Tengah Semester)
  - 📈 Input Nilai SAS (Sumatif Akhir Semester)
  - 🎯 Perhitungan otomatis nilai akhir dengan bobot

- **Riwayat & Laporan**
  - 📚 Riwayat Mengajar per Mapel
  - 📊 Statistik Kehadiran Siswa
  - 📄 Cetak Laporan Pembelajaran

### 🎓 Kepala Sekolah
- **Dashboard Eksekutif**
  - 📊 Overview seluruh kelas dan guru
  - 📈 Statistik kehadiran global
  - 📉 Analisis performa sekolah

- **Monitoring**
  - 👀 Monitoring Jurnal Seluruh Guru
  - 📊 Monitoring Absensi Seluruh Kelas
  - 📈 Laporan Kinerja Guru
  - 📊 Performa Kehadiran Siswa

- **Validasi**
  - ✅ Validasi Rapor Siswa
  - 🔍 Verifikasi Data Nilai
  - 🖨️ Cetak Rapor dengan QR Code

### 👨‍👩‍👧 Wali Kelas
- **Manajemen Kelas**
  - 👥 Daftar Siswa di Kelas
  - 📊 Monitoring Absensi Kelas
  - 📈 Monitoring Nilai Siswa

- **Pembayaran (Optional)**
  - 💰 Input Tagihan Kelas
  - 💳 Input Pembayaran Siswa
  - 📊 Riwayat Transaksi
  - 🖨️ Cetak Kwitansi

- **Rapor**
  - ⚙️ Pengaturan Bobot Nilai (Harian, STS, SAS)
  - 🖨️ Cetak Rapor per Siswa
  - 📄 Cetak Rapor Kelas (batch)
  - 🔐 Rapor dengan QR Code validation

### 👨‍🎓 Siswa
- **Dashboard Personal**
  - 👤 Profil & Biodata
  - 📊 Statistik Kehadiran Pribadi
  - 📈 Grafik Performa

- **Absensi**
  - 📅 Riwayat Kehadiran
  - 📊 Rekap per Mapel
  - 📈 Persentase Kehadiran

- **Nilai**
  - 📝 Lihat Nilai Harian
  - 📊 Lihat Nilai STS & SAS
  - 📈 Nilai Akhir per Mapel
  - 🎯 Rapor Semester

---

## 🔐 Fitur Keamanan

- **Authentication & Authorization**
  - 🔑 Login dengan username & password (bcrypt hash)
  - 🛡️ Role-based access control (5 role)
  - 🔄 Session management dengan regeneration

- **Input Validation**
  - ✅ Server-side validation untuk semua input
  - 🧹 Sanitization untuk mencegah XSS
  - 🔒 Prepared statements untuk mencegah SQL Injection
  - 📏 Length & type validation

- **QR Code Security**
  - 🔐 Token-based validation untuk rapor
  - ⏰ Expiry time untuk token
  - 📝 Audit log validasi QR
  - 🔗 Unique validation URL

- **File Security**
  - 📁 Protected configuration files
  - 🚫 Directory listing disabled
  - 🔒 .htaccess protection
  - 📤 Upload validation (type & size)

---

## 🛠️ Teknologi yang Digunakan

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Database management
- **MVC Architecture** - Clean code structure
- **PDO** - Prepared statements untuk database security

### Frontend
- **Tailwind CSS 3.x** - Utility-first CSS framework
- **Lucide Icons** - Beautiful icon set
- **Vanilla JavaScript** - No framework dependencies
- **Responsive Design** - Mobile-first approach

### Libraries
- **Dompdf** - PDF generation untuk rapor & laporan
- **QR Code Generator** - QR Code validation system

---

## 📋 Persyaratan Sistem

- PHP >= 7.4 (8.x recommended)
- MySQL >= 5.7 atau MariaDB >= 10.2
- Apache 2.4+ dengan mod_rewrite enabled
- PHP Extensions:
  - PDO & PDO_MySQL
  - mbstring
  - GD (untuk manipulasi gambar)
  - fileinfo
  - json

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/smart-absensi.git
cd smart-absensi
```

### 2. Konfigurasi Database

```bash
# Buat database baru
mysql -u root -p
CREATE DATABASE absen CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
exit;

# Import database
mysql -u root -p absen < absen.sql
```

### 3. Konfigurasi Aplikasi

Edit file `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'absen');
```

Edit file `config/config.php`:

```php
// Untuk production, hardcode BASE URL
define('BASEURL', 'https://yourdomain.com');

// Ganti secret key
define('SECRET_KEY', 'your_random_secret_key_32_chars_min');
```

### 4. Set Permissions

```bash
chmod 755 public/uploads
chmod 755 public/img/kop
chmod 755 public/img/ttd
```

### 5. Akses Aplikasi

**Development:**
```
http://localhost/absen
```

**Production:**
```
https://yourdomain.com
```

**Login Default:**
- Username: `admin`
- Password: `admin123`

⚠️ **PENTING:** Ganti password admin setelah login pertama kali!

---

## 📁 Struktur Folder

```
smart-absensi/
├── app/
│   ├── controllers/         # Logic aplikasi
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── GuruController.php
│   │   └── ...
│   ├── models/              # Database queries
│   │   ├── Siswa_model.php
│   │   ├── Guru_model.php
│   │   └── ...
│   ├── views/               # HTML templates
│   │   ├── admin/
│   │   ├── guru/
│   │   ├── siswa/
│   │   └── templates/
│   └── core/                # Framework core
│       ├── App.php
│       ├── Controller.php
│       ├── Database.php
│       └── InputValidator.php
├── config/
│   ├── config.php           # Base URL & settings
│   └── database.php         # Database credentials
├── public/                  # Web root
│   ├── index.php            # Entry point
│   ├── css/
│   ├── js/
│   ├── img/
│   └── uploads/
├── database/
│   └── migrations/          # SQL migrations
├── .htaccess                # Apache configuration
├── index.php                # Root entry (untuk shared hosting)
├── absen.sql                # Database structure
├── DEPLOYMENT.md            # Panduan deployment
└── README.md                # This file
```

---

## 📖 Panduan Penggunaan

### Untuk Administrator

1. **Setup Awal**
   - Login sebagai admin
   - Buat Tahun Pelajaran baru
   - Tambah Semester (Ganjil/Genap)
   - Input data Kelas
   - Input data Guru
   - Input data Siswa (manual atau import CSV)

2. **Konfigurasi Pembelajaran**
   - Buat Mata Pelajaran
   - Assign Penugasan (Guru → Mapel → Kelas)
   - Atur Anggota Kelas per Tahun Pelajaran

3. **Monitoring**
   - Monitor jurnal mengajar guru
   - Monitor absensi siswa
   - Analisis performa

### Untuk Guru

1. **Mengajar**
   - Pilih penugasan aktif
   - Input jurnal (topik, pertemuan ke-n)
   - Input absensi siswa
   - Input nilai harian (optional)

2. **Penilaian**
   - Input Nilai Harian per pertemuan
   - Input Nilai STS (tengah semester)
   - Input Nilai SAS (akhir semester)

### Untuk Wali Kelas

1. **Rapor**
   - Set bobot nilai (Harian:STS:SAS = 40:30:30)
   - Pastikan semua nilai lengkap
   - Cetak rapor per siswa atau batch
   - Rapor otomatis include QR Code

---

## 🔧 Konfigurasi Lanjutan

### Upload Limits

Edit `.htaccess`:

```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
```

### QR Code Settings

Edit `config/config.php`:

```php
define('QR_ENABLED', true);  // Enable/disable QR in rapor
define('SECRET_KEY', 'change_this_to_random_string');
```

### Menu Visibility

Edit `config/config.php`:

```php
define('MENU_INPUT_NILAI_ENABLED', true);
define('MENU_PEMBAYARAN_ENABLED', true);
define('MENU_RAPOR_ENABLED', true);
```

---

## 🐛 Troubleshooting

### Error 404 / Page Not Found
- Pastikan mod_rewrite Apache aktif
- Cek file `.htaccess` ada dan readable
- Verifikasi BASE URL di `config/config.php`

### Database Connection Error
- Cek kredensial di `config/database.php`
- Pastikan MySQL service running
- Verifikasi database sudah di-import

### CSS/JS Tidak Load
- Cek BASE URL di `config/config.php`
- Clear browser cache (Ctrl+Shift+Del)
- Pastikan folder `public/` accessible

### Upload File Gagal
- Cek permission folder `public/uploads/` (755 atau 777)
- Cek PHP settings `upload_max_filesize`
- Verifikasi disk space

---

## 📝 Changelog

### Version 2.1 (November 2025)
- ✅ Clean database structure
- ✅ Input validation & sanitization
- ✅ QR Code validation system
- ✅ Responsive sidebar with flat menu
- ✅ Auto-detect BASE URL
- ✅ Deployment ready for shared hosting

### Version 2.0
- 🎨 Modern UI dengan Tailwind CSS
- 📊 Dashboard interaktif
- 🔐 Enhanced security
- 📱 Responsive design

### Version 1.0
- 🚀 Initial release
- ✨ Basic CRUD functionality

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` file for more information.

---

## 👥 Author

**Smart Absensi Development Team**

- Website: [https://yourwebsite.com](https://yourwebsite.com)
- Email: support@yourwebsite.com

---

## 🙏 Acknowledgments

- [TailwindCSS](https://tailwindcss.com/) - CSS Framework
- [Lucide Icons](https://lucide.dev/) - Icon Library
- [Dompdf](https://github.com/dompdf/dompdf) - PDF Generation
- [PHP](https://www.php.net/) - Backend Language
- [MySQL](https://www.mysql.com/) - Database

---

## 📸 Screenshots

### Dashboard Admin
![Dashboard Admin](docs/screenshots/dashboard-admin.png)

### Input Absensi Guru
![Absensi](docs/screenshots/absensi-guru.png)

### Rapor dengan QR Code
![Rapor](docs/screenshots/rapor-qr.png)

---

## 🔗 Links

- [Dokumentasi Lengkap](docs/README.md)
- [Panduan Deployment](DEPLOYMENT.md)
- [Checklist Deployment](CHECKLIST_DEPLOYMENT.md)
- [Issues](https://github.com/username/smart-absensi/issues)
- [Discussions](https://github.com/username/smart-absensi/discussions)

---

**⭐ Jika project ini bermanfaat, berikan star di GitHub!**

---

*Made with ❤️ for Indonesian Education*
