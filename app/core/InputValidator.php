<?php

/**
 * Input Validator & Sanitizer
 * Untuk mencegah SQL Injection, XSS, dan input invalid
 */
class InputValidator
{
    /**
     * Sanitize string input
     */
    public static function sanitizeString($input, $maxLength = 255)
    {
        if (empty($input)) return '';
        $input = trim($input);
        $input = strip_tags($input); // Hapus HTML tags
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); // Escape special chars
        
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }
        
        return $input;
    }

    /**
     * Validate & sanitize integer
     */
    public static function sanitizeInt($input)
    {
        return filter_var($input, FILTER_VALIDATE_INT) ?: 0;
    }

    /**
     * Validate & sanitize float/decimal
     */
    public static function sanitizeFloat($input)
    {
        return filter_var($input, FILTER_VALIDATE_FLOAT) ?: 0.0;
    }

    /**
     * Validate email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ?: false;
    }

    /**
     * Sanitize array of IDs
     */
    public static function sanitizeArrayInt($array)
    {
        if (!is_array($array)) return [];
        return array_map(function($item) {
            return filter_var($item, FILTER_VALIDATE_INT) ?: 0;
        }, $array);
    }

    /**
     * Validate date format (Y-m-d)
     */
    public static function validateDate($date)
    {
        if (empty($date)) return false;
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Sanitize untuk SQL LIKE query (escape wildcard)
     */
    public static function sanitizeLike($input)
    {
        $input = self::sanitizeString($input);
        $input = str_replace(['%', '_'], ['\\%', '\\_'], $input);
        return $input;
    }

    /**
     * Escape output untuk HTML (untuk di view)
     */
    public static function escapeHtml($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'escapeHtml'], $input);
        }
        return htmlspecialchars($input ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate NISN (10 digit)
     */
    public static function validateNISN($nisn)
    {
        $nisn = trim($nisn);
        return preg_match('/^\d{10}$/', $nisn) ? $nisn : false;
    }

    /**
     * Validate NIK (16 digit)
     */
    public static function validateNIK($nik)
    {
        $nik = trim($nik);
        return preg_match('/^\d{16}$/', $nik) ? $nik : false;
    }

    /**
     * Sanitize nama (hanya huruf, spasi, dan beberapa karakter)
     */
    public static function sanitizeNama($nama)
    {
        $nama = trim($nama);
        $nama = preg_replace('/[^a-zA-Z\s\.\,\'\-]/', '', $nama);
        return $nama;
    }

    /**
     * Validate jenis kelamin
     */
    public static function validateJenisKelamin($jk)
    {
        return in_array($jk, ['L', 'P']) ? $jk : false;
    }

    /**
     * Validate role
     */
    public static function validateRole($role)
    {
        $allowedRoles = ['admin', 'guru', 'siswa', 'kepala_madrasah', 'wali_kelas'];
        return in_array($role, $allowedRoles) ? $role : false;
    }

    /**
     * Validate jenis nilai
     */
    public static function validateJenisNilai($jenis)
    {
        $allowed = ['harian', 'sts', 'sas', 'tugas', 'uh', 'pts', 'pas'];
        return in_array(strtolower($jenis), $allowed) ? strtolower($jenis) : false;
    }

    /**
     * Sanitize nilai (0-100)
     */
    public static function sanitizeNilai($nilai)
    {
        $nilai = self::sanitizeFloat($nilai);
        if ($nilai < 0) return 0;
        if ($nilai > 100) return 100;
        return round($nilai, 2);
    }
}
