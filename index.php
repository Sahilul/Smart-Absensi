<?php
/**
 * Front Controller untuk Shared Hosting
 * 
 * File ini akan redirect semua request ke folder public/
 * Gunakan file ini ketika deploy ke shared hosting yang tidak support
 * custom document root.
 * 
 * Untuk production optimal, set document root ke folder /public/
 */

// Redirect ke folder public
if (php_sapi_name() === 'cli-server') {
    // Untuk PHP built-in server (development)
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . '/public' . $url['path'];
    
    if (is_file($file)) {
        return false;
    }
    
    $_SERVER['SCRIPT_NAME'] = '/public/index.php';
    require_once __DIR__ . '/public/index.php';
} else {
    // Untuk shared hosting / production
    
    // Cek apakah sudah di dalam folder public
    if (strpos($_SERVER['REQUEST_URI'], '/public/') !== false) {
        // Sudah di public, redirect untuk hindari loop
        header('Location: ' . str_replace('/public/', '/', $_SERVER['REQUEST_URI']));
        exit;
    }
    
    // Simpan original request URI
    if (!isset($_SERVER['ORIGINAL_REQUEST_URI'])) {
        $_SERVER['ORIGINAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
    }
    
    // Adjust REQUEST_URI untuk aplikasi
    $_SERVER['REQUEST_URI'] = str_replace('/index.php', '', $_SERVER['REQUEST_URI']);
    
    // Adjust SCRIPT_NAME
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    
    // Load aplikasi dari folder public
    require_once __DIR__ . '/public/index.php';
}
