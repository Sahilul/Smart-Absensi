# 📋 Checklist Deployment Smart Absensi

## Sebelum Upload

### 1. File & Folder
- [ ] Sudah hapus file backup/test yang tidak perlu
- [ ] File `.gitignore` sudah ada
- [ ] File `.htaccess` sudah dikonfigurasi
- [ ] File `index.php` di root sudah ada
- [ ] File `DEPLOYMENT.md` sudah dibaca

### 2. Konfigurasi
- [ ] Edit `config/config.php` - set BASE URL yang benar
- [ ] Edit `config/database.php` - kredensial database production
- [ ] Ganti `SECRET_KEY` di `config/config.php`
- [ ] Backup file `absen.sql`

### 3. Security Check
- [ ] Ganti password admin default
- [ ] SECRET_KEY sudah random (minimal 32 karakter)
- [ ] File `.env` tidak ikut di-upload (gunakan .gitignore)
- [ ] Folder `app/`, `config/`, `database/` tidak accessible dari browser

---

## Saat Upload ke Shared Hosting

### 1. Upload Files
- [ ] Compress folder `absen` menjadi ZIP
- [ ] Upload via cPanel File Manager atau FTP
- [ ] Extract di folder root (`public_html` atau `www`)

### 2. Database Setup
- [ ] Buat database MySQL baru via cPanel
- [ ] Import file `absen.sql`
- [ ] Update `config/database.php` dengan kredensial baru

### 3. Set Permissions
Folder berikut harus permission 755 atau 777:
- [ ] `public/uploads/`
- [ ] `public/uploads/siswa/`
- [ ] `public/uploads/guru/`
- [ ] `public/uploads/rapor/`
- [ ] `public/img/kop/`
- [ ] `public/img/ttd/`

### 4. Testing
- [ ] Akses URL utama (misal: `https://sekolah.com`)
- [ ] Test login admin
- [ ] Test menu dashboard
- [ ] Test upload file
- [ ] Test cetak laporan/rapor

---

## Setelah Deploy

### 1. Keamanan
- [ ] Login sebagai admin
- [ ] Ganti password admin di menu Profile
- [ ] Test semua role (admin, guru, siswa, wali kelas)
- [ ] Nonaktifkan mode debug (set `APP_DEBUG=false`)

### 2. Konfigurasi Lanjutan
- [ ] Set timezone yang benar
- [ ] Test QR Code generation (jika enabled)
- [ ] Test email notification (jika enabled)
- [ ] Backup database secara berkala

### 3. Monitoring
- [ ] Monitor error logs
- [ ] Check disk space untuk uploads
- [ ] Test performa loading page
- [ ] Setup SSL certificate (HTTPS)

---

## 🚨 Troubleshooting

### Masalah Umum

**Error 404 / Page Not Found**
- Pastikan `.htaccess` ada dan mod_rewrite aktif
- Cek BASE URL di `config/config.php`

**Database Connection Error**
- Cek kredensial di `config/database.php`
- Pastikan database sudah di-import
- Cek apakah user database punya privileges

**CSS/JS Tidak Load**
- Clear browser cache (Ctrl+Shift+Del)
- Cek BASE URL di `config/config.php`
- Pastikan path public/ benar

**Upload File Gagal**
- Cek permission folder uploads (755 atau 777)
- Cek PHP `upload_max_filesize` di `.htaccess`
- Cek disk space hosting

---

## 📞 Support

Jika masih ada masalah:
1. Cek PHP error log di cPanel
2. Enable error reporting sementara di `public/index.php`
3. Hubungi tim development

---

**Terakhir Update:** November 2025  
**Version:** 2.1
