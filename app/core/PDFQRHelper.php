<?php
/**
 * PDF QR Helper
 * Universal helper for adding QR codes to all PDF exports
 */

class PDFQRHelper {
    
    /**
     * Add QR code to PDF HTML
     * @param string $html Original HTML
     * @param string $docType Type of document (rapor, pembayaran, absensi, etc)
     * @param mixed $docId Document identifier
     * @param array $additionalData Extra data for token generation
     * @return string HTML with QR code injected
     */
    public static function addQRToPDF($html, $docType, $docId, $additionalData = []) {
        try {
            // Safely include QR config; suppress any accidental output to avoid corrupting PDF
            $obLevel = ob_get_level();
            ob_start();
            require_once __DIR__ . '/../../config/qrcode.php';
            // Clean any output produced by the config file
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            
            // Check if QR is enabled (can be disabled for debugging)
            if (defined('QR_ENABLED') && !QR_ENABLED) {
                return $html; // QR disabled, return original HTML
            }
            
            // Enrich additionalData with user info for provenance if available
            if (session_status() === PHP_SESSION_ACTIVE) {
                // Try common session keys used across the app for display name
                $printedBy = $_SESSION['user_nama_lengkap']
                    ?? $_SESSION['nama_lengkap']
                    ?? $_SESSION['nama_guru']
                    ?? $_SESSION['nama_user']
                    ?? $_SESSION['nama']
                    ?? $_SESSION['username']
                    ?? null;
                $printedRole = $_SESSION['role'] ?? null;
                if ($printedBy) {
                    $additionalData['printed_by'] = $printedBy;
                }
                if ($printedRole) {
                    $additionalData['printed_role'] = $printedRole;
                }
                if (!isset($additionalData['printed_at'])) {
                    // store UTC time; validator will convert to WIB
                    $additionalData['printed_at'] = gmdate('Y-m-d H:i:s');
                }
            }

            // Auto fingerprint if metadata present and none provided yet
            if (!isset($additionalData['fingerprint'])) {
                // Select stable keys (exclude volatile like printed_at)
                $keys = ['nisn','nama_siswa','kelas','semester','jenis','rata_rata','jumlah_siswa'];
                $parts = [];
                foreach ($keys as $k) {
                    if (isset($additionalData[$k]) && $additionalData[$k] !== '') {
                        $parts[] = (is_scalar($additionalData[$k]) ? (string)$additionalData[$k] : json_encode($additionalData[$k]));
                    }
                }
                if (!empty($parts)) {
                    $additionalData['fingerprint'] = hash('sha256', implode('|', $parts));
                }
            }

            // Generate QR code
            $qrCode = generatePDFQRCode($docType, $docId, $additionalData);
            
            if (empty($qrCode)) {
                return $html; // Return original if QR fails
            }
            
            // Add position: relative to body if not present (avoid duplicating style)
            if (stripos($html, '<body') !== false && stripos($html, 'position: relative') === false) {
                $html = preg_replace('/<body(\s[^>]*)?>/i', '<body$1 style="position: relative">', $html, 1) ?: $html;
            }
            
            // Get QR HTML
            $qrHtml = getQRCodeHTML($qrCode);
            
            // Insert QR code before closing body; if </body> missing append at end
            if (stripos($html, '</body>') !== false) {
                $html = str_ireplace('</body>', $qrHtml . '</body>', $html);
            } else {
                $html .= $qrHtml; // fallback
            }
            
            return $html;
        } catch (Exception $e) {
            error_log('PDFQRHelper error: ' . $e->getMessage());
            return $html; // Return original on error
        }
    }
    
    /**
     * Quick add QR with auto-detection
     */
    public static function injectQR(&$html, $type, $id) {
        $html = self::addQRToPDF($html, $type, $id);
    }
}
