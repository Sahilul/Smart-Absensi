# Smart Absensi - Panduan Deployment

## 📦 Deployment ke Shared Hosting

### Langkah 1: Upload File
1. Compress seluruh folder `absen` menjadi ZIP
2. Upload ke shared hosting melalui cPanel File Manager atau FTP
3. Extract file di folder `public_html` atau `www` atau `htdocs`

### Langkah 2: Konfigurasi Database
1. Buat database MySQL baru melalui cPanel
2. Import file `absen.sql` ke database tersebut
3. Edit file `config/database.php`:
   ```php
   define('DB_HOST', 'localhost'); // atau IP server database
   define('DB_USER', 'username_database');
   define('DB_PASS', 'password_database');
   define('DB_NAME', 'nama_database');
   ```

### Langkah 3: Konfigurasi Base URL
1. Edit file `config/config.php`:
   ```php
   // Jika domain: https://sekolah.com
   define('BASEURL', 'https://sekolah.com');
   
   // Jika subfolder: https://sekolah.com/absen
   define('BASEURL', 'https://sekolah.com/absen');
   ```

### Langkah 4: Set Permission Folder
Pastikan folder berikut memiliki permission 755 atau 777:
```
public/uploads/
public/uploads/siswa/
public/uploads/guru/
public/uploads/rapor/
public/img/kop/
public/img/ttd/
```

Melalui cPanel File Manager atau FTP, klik kanan folder > Change Permissions > Set ke 755

## 🔧 Konfigurasi untuk Development (Laragon/XAMPP)

### Laragon
1. Letakkan folder `absen` di `C:\laragon\www\`
2. Akses: `http://localhost/absen` atau `http://absen.test`
3. Database sudah otomatis terkonfigurasi di `config/database.php`

### XAMPP
1. Letakkan folder `absen` di `C:\xampp\htdocs\`
2. Akses: `http://localhost/absen`
3. Sesuaikan `config/database.php` dengan username/password MySQL XAMPP

## 🌐 Struktur Folder

```
absen/
├── index.php              ← Entry point untuk shared hosting
├── .htaccess              ← Apache rewrite rules
├── absen.sql              ← Database backup
├── app/
│   ├── controllers/       ← Logic aplikasi
│   ├── models/            ← Database queries
│   ├── views/             ← HTML templates
│   └── core/              ← Framework core
├── config/
│   ├── config.php         ← Base URL & settings
│   └── database.php       ← Database connection
├── public/                ← Web root (jika bisa set document root)
│   ├── index.php          ← Main entry point
│   ├── css/
│   ├── js/
│   ├── img/
│   └── uploads/
└── database/
    └── migrations/        ← SQL migrations
```

## ⚙️ Setting Document Root (Recommended)

Jika hosting support custom document root (VPS/Cloud):
1. Set document root ke folder `/public/`
2. Tidak perlu `index.php` di root
3. Lebih aman karena folder `app/` tidak accessible dari web

## 🔐 Keamanan

1. **Ganti Secret Key** di `config/config.php`:
   ```php
   define('SECRET_KEY', 'ganti_dengan_random_string_panjang');
   ```

2. **Password Default Admin**:
   - Username: `admin`
   - Password: Sesuai database (biasanya `admin123` atau `password`)
   - **WAJIB diganti setelah login pertama kali!**

3. **Protect Sensitive Files**:
   File `.htaccess` sudah dikonfigurasi untuk:
   - Mencegah directory listing
   - Protect file konfigurasi
   - Hide .env dan .git files

## 🐛 Troubleshooting

### Error "Page Not Found" / 404
- Pastikan mod_rewrite Apache aktif
- Pastikan file `.htaccess` ada dan readable
- Cek BASE URL di `config/config.php`

### Error Database Connection
- Cek kredensial di `config/database.php`
- Pastikan MySQL service running
- Cek apakah database sudah di-import

### CSS/JS Tidak Load
- Cek BASE URL di `config/config.php`
- Pastikan path folder `public/` benar
- Clear browser cache

### Upload File Gagal
- Cek permission folder `public/uploads/` (755 atau 777)
- Cek PHP setting `upload_max_filesize` dan `post_max_size`
- Cek disk space hosting

## 📞 Support

Untuk bantuan lebih lanjut, hubungi tim development atau buat issue di repository.

---

**Version:** 2.1  
**Last Update:** November 2025
