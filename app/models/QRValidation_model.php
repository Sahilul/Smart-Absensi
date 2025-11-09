<?php
// File: app/models/QRValidation_model.php
// Menyimpan dan memvalidasi token QR untuk dokumen PDF

class QRValidation_model {
    private $db;
    private $table = 'qr_validation_tokens';
    private $logTable = 'qr_validation_logs';

    public function __construct() {
        $this->db = new Database;
    }

    public function ensureTables() {
        // Create tables if not exist (idempotent). For production, move to migration.
        $sqlTokens = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `doc_type` VARCHAR(50) NOT NULL,
            `doc_id` VARCHAR(100) NOT NULL,
            `identifier` VARCHAR(100) NOT NULL,
            `token` CHAR(64) NOT NULL,
            `issued_at` DATETIME NOT NULL,
            `expires_at` DATETIME NULL,
            `meta_json` JSON NULL,
            `revoked` TINYINT(1) NOT NULL DEFAULT 0,
            UNIQUE KEY `u_token` (`token`),
            KEY `k_doc` (`doc_type`,`doc_id`),
            KEY `k_expires` (`expires_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->db->query($sqlTokens); $this->db->execute();

        $sqlLogs = "CREATE TABLE IF NOT EXISTS `{$this->logTable}` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `token` CHAR(64) NOT NULL,
            `scanned_at` DATETIME NOT NULL,
            `ip_address` VARCHAR(45) NULL,
            `user_agent` VARCHAR(255) NULL,
            `is_valid` TINYINT(1) NOT NULL,
            `notes` VARCHAR(255) NULL,
            KEY `k_token` (`token`),
            KEY `k_scanned` (`scanned_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $this->db->query($sqlLogs); $this->db->execute();
    }

    public function storeToken($docType, $docId, $identifier, $token, $expiryDays, $meta = []) {
        $expires = null;
        if ($expiryDays > 0) {
            $expires = (new DateTime('+'.intval($expiryDays).' days'))->format('Y-m-d H:i:s');
        }
        $issued = (new DateTime())->format('Y-m-d H:i:s');
        $metaJson = !empty($meta) ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null;

        $this->db->query("INSERT INTO {$this->table} (doc_type, doc_id, identifier, token, issued_at, expires_at, meta_json) VALUES (:dt,:di,:idf,:tk,:is,:ex,:mj)");
        $this->db->bind(':dt', $docType);
        $this->db->bind(':di', $docId);
        $this->db->bind(':idf', $identifier);
        $this->db->bind(':tk', $token);
        $this->db->bind(':is', $issued);
        $this->db->bind(':ex', $expires);
        $this->db->bind(':mj', $metaJson);
        $this->db->execute();
        return true;
    }

    public function findByToken($token) {
        $this->db->query("SELECT * FROM {$this->table} WHERE token = :tk LIMIT 1");
        $this->db->bind(':tk', $token);
        return $this->db->single();
    }

    public function logScan($token, $isValid, $notes = null) {
        $this->db->query("INSERT INTO {$this->logTable} (token, scanned_at, ip_address, user_agent, is_valid, notes) VALUES (:tk, :sc, :ip, :ua, :iv, :nt)");
        $this->db->bind(':tk', $token);
        $this->db->bind(':sc', (new DateTime())->format('Y-m-d H:i:s'));
        $this->db->bind(':ip', $_SERVER['REMOTE_ADDR'] ?? null);
        $this->db->bind(':ua', substr($_SERVER['HTTP_USER_AGENT'] ?? '',0,250));
        $this->db->bind(':iv', $isValid ? 1 : 0);
        $this->db->bind(':nt', $notes);
        $this->db->execute();
    }

    public function revokeToken($token) {
        $this->db->query("UPDATE {$this->table} SET revoked = 1 WHERE token = :tk");
        $this->db->bind(':tk', $token);
        $this->db->execute();
    }
}
