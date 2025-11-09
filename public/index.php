<?php

// File: public/index.php

// Session configuration untuk mencegah login ulang di tab baru
if (session_status() === PHP_SESSION_NONE) {
    // Set session configuration sebelum session_start()
    ini_set('session.cookie_lifetime', 0); // Cookie expire saat browser ditutup
    ini_set('session.gc_maxlifetime', 28800); // Session aktif 8 jam
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access
    ini_set('session.use_strict_mode', 1); // Prevent session fixation
    ini_set('session.cookie_samesite', 'Lax'); // CSRF protection
    
    session_name('ABSEN_SESSION'); // Custom session name
    session_start();
}

// Mendefinisikan path root aplikasi
define('APPROOT', dirname(dirname(__FILE__)));

// MEMANGGIL FILE KONFIGURASI BARU
require_once APPROOT . '/config/config.php';

// Early handling for public QR validation links like /validate/<type>/<token>
// This bypasses the MVC router and avoids auth redirects for scanned QR codes
$__uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
if (preg_match('#/validate/([^/]+)/([A-Fa-f0-9]{32,128})#', $__uriPath)) {
    require_once APPROOT . '/public/validate.php';
    exit;
}

// Memanggil file App.php
require_once APPROOT . '/app/core/App.php';

// Menjalankan aplikasi
$app = new App();