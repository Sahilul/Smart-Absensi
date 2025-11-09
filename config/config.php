<?php

/**
 * Smart Absensi Configuration
 * 
 * IMPORTANT: Sesuaikan BASE URL dengan environment Anda
 */

// Auto-detect BASE URL (untuk development dan production)
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Untuk shared hosting, auto-detect folder
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $scriptName = str_replace('\\', '/', $scriptName); // Windows compatibility
    $scriptName = rtrim($scriptName, '/');
    
    // Remove /public jika ada
    $scriptName = str_replace('/public', '', $scriptName);
    
    return $protocol . '://' . $host . $scriptName;
}

// Set BASE URL
// Untuk production, Anda bisa hardcode URL di sini:
// define('BASEURL', 'https://sekolah.com');
// define('BASEURL', 'https://sekolah.com/absen');

// Untuk auto-detect (development & shared hosting):
define('BASEURL', getBaseUrl());

// Atau gunakan environment variable untuk fleksibilitas:
// define('BASEURL', getenv('APP_URL') ?: getBaseUrl());

// Secret key for QR token generation and security
// WAJIB GANTI untuk production!
define('SECRET_KEY', 'absen_qr_secret_key_2024_change_in_production');

// QR feature flag (set true to enable QR in PDFs)
define('QR_ENABLED', true);

// Menu visibility settings (can be changed via Admin > Pengaturan Menu)
define('MENU_INPUT_NILAI_ENABLED', true);
define('MENU_PEMBAYARAN_ENABLED', true);
define('MENU_RAPOR_ENABLED', true);

require_once 'database.php';
