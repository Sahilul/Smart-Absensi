<?php

/**
 * QR Code Configuration
 * Auto-generated on 2025-11-09 15:22:09
 */

define('QR_API_PROVIDER', 'qrserver');
define('QR_API_QRSERVER', 'https://api.qrserver.com/v1/create-qr-code/');
define('QR_CUSTOM_URL', '');
define('QR_WEBSITE_URL', 'http://localhost/absen');
define('QR_SIZE', '250x250');
define('QR_DISPLAY_SIZE', '50px');
define('QR_TOKEN_EXPIRY', 365);
define('QR_POSITION', 'bottom-right');
define('QR_DISPLAY_TEXT', 'Scan untuk validasi');
define('QR_TOKEN_SALT', 'absen_qr_secret_key_2024_change_in_production');

function getQRCodeApiUrl($data) {
    $encodedData = urlencode($data);
    // Parse size dari format "250x250" ke integer untuk provider yang membutuhkan
    $sizeInt = (int)explode('x', QR_SIZE)[0];
    
    switch (QR_API_PROVIDER) {
        case 'qrserver':
            return QR_API_QRSERVER . '?size=' . QR_SIZE . '&data=' . $encodedData;
        case 'quickchart':
            return 'https://quickchart.io/qr?text=' . $encodedData . '&size=' . $sizeInt;
        case 'goqr':
            return 'https://api.qrserver.com/v1/create-qr-code/?size=' . QR_SIZE . '&data=' . $encodedData;
        case 'custom':
            return str_replace(['{DATA}', '{SIZE}'], [$encodedData, QR_SIZE], QR_CUSTOM_URL);
        default:
            return QR_API_QRSERVER . '?size=' . QR_SIZE . '&data=' . $encodedData;
    }
}

function generateQRToken($siswaId, $jenisRapor, $nisn) {
    $data = $siswaId . '|' . $jenisRapor . '|' . $nisn . '|' . QR_TOKEN_SALT;
    return hash('sha256', $data);
}

/**
 * Generate PDF QR Code with validation token
 * @param string $docType Document type (rapor, pembayaran, absensi, performa_guru, performa_siswa, etc)
 * @param mixed $docId Document identifier
 * @param array $additionalData Extra metadata for validation
 * @return string Base64 QR code image data URL
 */
function generatePDFQRCode($docType, $docId, $additionalData = []) {
    try {
        // Create validation token
        $tokenData = [
            'doc_type' => $docType,
            'doc_id' => $docId,
            'timestamp' => time(),
            'expires' => time() + (QR_TOKEN_EXPIRY * 24 * 60 * 60)
        ];
        
        // Merge additional data
        if (!empty($additionalData)) {
            $tokenData = array_merge($tokenData, $additionalData);
        }
        
        // Create secure token
        $token = hash_hmac('sha256', json_encode($tokenData), QR_TOKEN_SALT);
        
        // Save token to database for validation
        try {
            $APPROOT = realpath(__DIR__ . '/..');
            require_once $APPROOT . '/config/database.php';
            require_once $APPROOT . '/app/core/Database.php';
            require_once $APPROOT . '/app/models/QRValidation_model.php';
            $qrModel = new QRValidation_model();
            $qrModel->ensureTables(); // Create table if not exists
            
            // Calculate expiry days
            $expiryDays = QR_TOKEN_EXPIRY > 0 ? QR_TOKEN_EXPIRY : 0;
            
            // Use doc_id as identifier
            $identifier = (string)$docId;
            
            // Save token using storeToken method
            $qrModel->storeToken($docType, $docId, $identifier, $token, $expiryDays, $additionalData);
        } catch (Exception $e) {
            error_log('Failed to save QR token to database: ' . $e->getMessage());
            // Continue anyway - QR will still be generated
        }
        
        // Create validation URL
        $validationUrl = QR_WEBSITE_URL . '/validate?token=' . $token . '&type=' . urlencode($docType);
        
        // Get QR code image from API
        $qrApiUrl = getQRCodeApiUrl($validationUrl);
        
        // Fetch QR code image
        $qrImageData = @file_get_contents($qrApiUrl);
        
        if ($qrImageData === false) {
            error_log('Failed to generate QR code from API: ' . $qrApiUrl);
            return '';
        }
        
        // Convert to base64 data URL
        $base64 = base64_encode($qrImageData);
        return 'data:image/png;base64,' . $base64;
        
    } catch (Exception $e) {
        error_log('QR code generation error: ' . $e->getMessage());
        return '';
    }
}

function getQRCodeHTML($qrCodeDataUrl) {
    $position = QR_POSITION;
    $displaySize = QR_DISPLAY_SIZE;
    $displayText = QR_DISPLAY_TEXT;
    
    // Position styles - menggunakan absolute agar hanya di halaman terakhir
    $positionStyles = [
        'bottom-right' => 'bottom: 5mm; right: 5mm;',
        'bottom-left' => 'bottom: 5mm; left: 5mm;',
        'top-right' => 'top: 5mm; right: 5mm;',
        'top-left' => 'top: 5mm; left: 5mm;',
    ];
    
    $style = $positionStyles[$position] ?? $positionStyles['bottom-right'];
    
    // Gunakan position absolute dan taruh di akhir document
    $html = '<div style="position: absolute; ' . $style . ' text-align: center; background: white; padding: 5px; border: 1px solid #ddd; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
    $html .= '<img src="' . htmlspecialchars($qrCodeDataUrl) . '" style="width: ' . $displaySize . '; height: ' . $displaySize . '; display: block;" alt="QR Code">';
    if (!empty($displayText)) {
        $html .= '<div style="font-size: 7px; color: #666; margin-top: 2px;">' . htmlspecialchars($displayText) . '</div>';
    }
    $html .= '</div>';
    
    return $html;
}
