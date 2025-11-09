<?php

// File: app/models/Siswa_model.php - Sesuai Database Schema yang Ada
class Siswa_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    // =================================================================
    // EXISTING METHODS - TETAP SAMA
    // =================================================================
    
    public function getJumlahSiswa() {
        $this->db->query("SELECT COUNT(*) as total FROM siswa WHERE status_siswa = 'aktif'");
        return $this->db->single()['total'];
    }

    public function getAllSiswa() {
        $this->db->query('SELECT s.*, u.password_plain 
                         FROM siswa s 
                         LEFT JOIN users u ON s.id_siswa = u.id_ref AND u.role = "siswa" 
                         ORDER BY s.nama_siswa ASC');
        return $this->db->resultSet();
    }

    public function getSiswaById($id) {
        $this->db->query('SELECT * FROM siswa WHERE id_siswa = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function getSiswaKelasAkhir($id_tp) {
        // Ambil siswa dari kelas terakhir (misal jenjang XII atau 9)
        $this->db->query('SELECT s.*, k.nama_kelas 
                         FROM siswa s 
                         JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa 
                         JOIN kelas k ON kk.id_kelas = k.id_kelas 
                         WHERE kk.id_tp = :id_tp 
                         AND s.status_siswa = "aktif" 
                         AND (k.jenjang = "XII" OR k.jenjang = "9") 
                         ORDER BY s.nama_siswa ASC');
        $this->db->bind('id_tp', $id_tp);
        return $this->db->resultSet();
    }

    public function tambahDataSiswa($data) {
        $this->db->query('INSERT INTO siswa (nisn, nama_siswa, jenis_kelamin, tgl_lahir, status_siswa) 
                         VALUES (:nisn, :nama, :jk, :tgl, "aktif")');
        $this->db->bind('nisn', $data['nisn']);
        $this->db->bind('nama', $data['nama_siswa']);
        $this->db->bind('jk', $data['jenis_kelamin']);
        $this->db->bind('tgl', $data['tgl_lahir']);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function updateDataSiswa($data) {
        $this->db->query('UPDATE siswa SET nisn = :nisn, nama_siswa = :nama, jenis_kelamin = :jk, tgl_lahir = :tgl WHERE id_siswa = :id');
        $this->db->bind('nisn', $data['nisn']);
        $this->db->bind('nama', $data['nama_siswa']);
        $this->db->bind('jk', $data['jenis_kelamin']);
        $this->db->bind('tgl', $data['tgl_lahir']);
        $this->db->bind('id', $data['id_siswa']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataSiswa($id) {
        $this->db->query('DELETE FROM siswa WHERE id_siswa = :id');
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function prosesKelulusan($daftar_id_siswa) {
        if (empty($daftar_id_siswa)) return 0;
        
        $placeholders = implode(',', array_fill(0, count($daftar_id_siswa), '?'));
        $this->db->query("UPDATE siswa SET status_siswa = 'lulus' WHERE id_siswa IN ($placeholders)");
        
        foreach ($daftar_id_siswa as $k => $id) {
            $this->db->bind($k + 1, $id);
        }
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    // ALIAS untuk compatibility dengan kode lama
    public function luluskanSiswaByIds($daftar_id_siswa) {
        return $this->prosesKelulusan($daftar_id_siswa);
    }

    // =================================================================
    // NEW METHODS UNTUK IMPORT EXCEL - SESUAI SCHEMA DATABASE
    // =================================================================

    /**
     * Cek apakah NISN sudah ada di database
     */
    public function cekNisnExists($nisn) {
        try {
            $this->db->query('SELECT COUNT(*) as total FROM siswa WHERE nisn = :nisn');
            $this->db->bind('nisn', $nisn);
            $result = $this->db->single();
            return $result['total'] > 0;
        } catch (Exception $e) {
            error_log("cekNisnExists error: " . $e->getMessage());
            return true; // Return true untuk safety
        }
    }

    /**
     * Get siswa by NISN
     */
    public function getSiswaByNisn($nisn) {
        try {
            $this->db->query('SELECT * FROM siswa WHERE nisn = :nisn LIMIT 1');
            $this->db->bind('nisn', $nisn);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("getSiswaByNisn error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendapatkan daftar siswa berdasarkan kelas dan tahun pelajaran
     * @param int $id_kelas ID Kelas
     * @param int $id_tp ID Tahun Pelajaran
     * @return array Daftar siswa aktif di kelas tersebut
     */
    public function getSiswaByKelas($id_kelas, $id_tp) {
        try {
            $this->db->query('SELECT s.*, kk.id_keanggotaan 
                             FROM siswa s
                             JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa
                             WHERE kk.id_kelas = :id_kelas 
                             AND kk.id_tp = :id_tp 
                             AND s.status_siswa = "aktif"
                             ORDER BY s.nama_siswa ASC');
            $this->db->bind('id_kelas', $id_kelas);
            $this->db->bind('id_tp', $id_tp);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getSiswaByKelas error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Batch insert siswa untuk import Excel
     */
    public function batchInsertSiswa($dataSiswa) {
        if (empty($dataSiswa)) {
            return ['success' => 0, 'errors' => [], 'ids' => []];
        }

        $successCount = 0;
        $errors = [];
        $createdIds = [];
        
        foreach ($dataSiswa as $index => $data) {
            try {
                // Validasi data sebelum insert
                if (empty($data['nisn']) || empty($data['nama_siswa'])) {
                    $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap";
                    continue;
                }
                
                // Cek duplikasi NISN
                if ($this->cekNisnExists($data['nisn'])) {
                    $errors[] = "Baris " . ($index + 1) . ": NISN {$data['nisn']} sudah terdaftar";
                    continue;
                }
                
                // Insert siswa
                $this->db->query('INSERT INTO siswa (nisn, nama_siswa, jenis_kelamin, tgl_lahir, status_siswa) 
                                 VALUES (:nisn, :nama, :jk, :tgl, "aktif")');
                $this->db->bind('nisn', $data['nisn']);
                $this->db->bind('nama', trim($data['nama_siswa']));
                $this->db->bind('jk', strtoupper($data['jenis_kelamin']));
                $this->db->bind('tgl', $data['tgl_lahir'] ?? null);
                
                if ($this->db->execute()) {
                    $newId = $this->db->lastInsertId();
                    $successCount++;
                    $createdIds[] = $newId;
                } else {
                    $errors[] = "Baris " . ($index + 1) . ": Gagal menyimpan data {$data['nama_siswa']}";
                }
                
            } catch (Exception $e) {
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                error_log("Batch insert error for row " . ($index + 1) . ": " . $e->getMessage());
            }
        }
        
        return [
            'success' => $successCount,
            'errors' => $errors,
            'ids' => $createdIds,
            'total_processed' => count($dataSiswa)
        ];
    }

    /**
     * Cek multiple NISN sekaligus untuk validasi batch
     */
    public function cekMultipleNisnExists($arrayNisn) {
        if (empty($arrayNisn)) return [];
        
        try {
            $placeholders = implode(',', array_fill(0, count($arrayNisn), '?'));
            $this->db->query("SELECT nisn FROM siswa WHERE nisn IN ($placeholders)");
            
            foreach ($arrayNisn as $k => $nisn) {
                $this->db->bind($k + 1, $nisn);
            }
            
            $result = $this->db->resultSet();
            return array_column($result, 'nisn');
        } catch (Exception $e) {
            error_log("cekMultipleNisnExists error: " . $e->getMessage());
            return $arrayNisn; // Return semua sebagai existing untuk safety
        }
    }

    /**
     * Get semua NISN yang ada untuk validasi import
     */
    public function getAllNisn() {
        try {
            $this->db->query('SELECT nisn FROM siswa WHERE nisn IS NOT NULL AND nisn != ""');
            $result = $this->db->resultSet();
            return array_column($result, 'nisn');
        } catch (Exception $e) {
            error_log("getAllNisn error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update status siswa (aktif/lulus/pindah)
     */
    public function updateStatusSiswa($id_siswa, $status) {
        $allowedStatus = ['aktif', 'lulus', 'pindah'];
        
        if (!in_array($status, $allowedStatus)) {
            return false;
        }
        
        try {
            $this->db->query('UPDATE siswa SET status_siswa = :status WHERE id_siswa = :id');
            $this->db->bind('status', $status);
            $this->db->bind('id', $id_siswa);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("updateStatusSiswa error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get siswa by status
     */
    public function getSiswaByStatus($status = 'aktif') {
        try {
            $this->db->query('SELECT s.*, u.password_plain 
                             FROM siswa s 
                             LEFT JOIN users u ON s.id_siswa = u.id_ref AND u.role = "siswa" 
                             WHERE s.status_siswa = :status
                             ORDER BY s.nama_siswa ASC');
            $this->db->bind('status', $status);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getSiswaByStatus error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search siswa by nama or NISN
     */
    public function searchSiswa($keyword) {
        try {
            $this->db->query('SELECT s.*, u.password_plain 
                             FROM siswa s 
                             LEFT JOIN users u ON s.id_siswa = u.id_ref AND u.role = "siswa" 
                             WHERE (s.nama_siswa LIKE :keyword OR s.nisn LIKE :keyword)
                             AND s.status_siswa = "aktif"
                             ORDER BY s.nama_siswa ASC');
            $this->db->bind('keyword', '%' . $keyword . '%');
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("searchSiswa error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get statistik siswa untuk dashboard
     */
    public function getStatistikSiswa() {
        try {
            $stats = [];
            
            // Total siswa aktif
            $this->db->query('SELECT COUNT(*) as total FROM siswa WHERE status_siswa = "aktif"');
            $stats['total_aktif'] = $this->db->single()['total'];
            
            // Total siswa lulus
            $this->db->query('SELECT COUNT(*) as total FROM siswa WHERE status_siswa = "lulus"');
            $stats['total_lulus'] = $this->db->single()['total'];
            
            // Total siswa pindah
            $this->db->query('SELECT COUNT(*) as total FROM siswa WHERE status_siswa = "pindah"');
            $stats['total_pindah'] = $this->db->single()['total'];
            
            // Total siswa dengan akun
            $this->db->query('SELECT COUNT(*) as total FROM siswa s 
                             JOIN users u ON s.id_siswa = u.id_ref 
                             WHERE u.role = "siswa" AND s.status_siswa = "aktif"');
            $stats['total_dengan_akun'] = $this->db->single()['total'];
            
            // Total siswa tanpa akun
            $stats['total_tanpa_akun'] = $stats['total_aktif'] - $stats['total_dengan_akun'];
            
            // Per jenis kelamin
            $this->db->query('SELECT jenis_kelamin, COUNT(*) as total 
                             FROM siswa 
                             WHERE status_siswa = "aktif" 
                             GROUP BY jenis_kelamin');
            $jkData = $this->db->resultSet();
            
            $stats['laki_laki'] = 0;
            $stats['perempuan'] = 0;
            foreach ($jkData as $jk) {
                if ($jk['jenis_kelamin'] === 'L') {
                    $stats['laki_laki'] = $jk['total'];
                } elseif ($jk['jenis_kelamin'] === 'P') {
                    $stats['perempuan'] = $jk['total'];
                }
            }
            
            return $stats;
        } catch (Exception $e) {
            error_log("getStatistikSiswa error: " . $e->getMessage());
            return [
                'total_aktif' => 0,
                'total_lulus' => 0,
                'total_pindah' => 0,
                'total_dengan_akun' => 0,
                'total_tanpa_akun' => 0,
                'laki_laki' => 0,
                'perempuan' => 0
            ];
        }
    }

    /**
     * Batch delete siswa berdasarkan array ID
     */
    public function batchDeleteSiswa($arrayIds) {
        if (empty($arrayIds)) return 0;
        
        try {
            $placeholders = implode(',', array_fill(0, count($arrayIds), '?'));
            $this->db->query("DELETE FROM siswa WHERE id_siswa IN ($placeholders)");
            
            foreach ($arrayIds as $k => $id) {
                $this->db->bind($k + 1, $id);
            }
            
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("batchDeleteSiswa error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get siswa yang belum memiliki akun user
     */
    public function getSiswaWithoutAccount() {
        try {
            $this->db->query('SELECT s.* 
                             FROM siswa s 
                             LEFT JOIN users u ON s.id_siswa = u.id_ref AND u.role = "siswa"
                             WHERE u.id_user IS NULL AND s.status_siswa = "aktif"
                             ORDER BY s.nama_siswa ASC');
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getSiswaWithoutAccount error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Validasi data import Excel
     */
    public function validateImportData($excelData) {
        $validData = [];
        $errors = [];
        $warnings = [];
        
        // Ambil NISN yang sudah ada
        $existingNisn = $this->getAllNisn();
        $currentBatchNisn = [];
        
        foreach ($excelData as $index => $row) {
            $rowErrors = [];
            $rowWarnings = [];
            $rowNum = $index + 1;
            
            // Sanitize data
            $cleanData = [
                'nisn' => trim($row['nisn'] ?? ''),
                'nama_siswa' => trim($row['nama_siswa'] ?? ''),
                'jenis_kelamin' => strtoupper(trim($row['jenis_kelamin'] ?? '')),
                'password' => trim($row['password'] ?? ''),
                'tgl_lahir' => $row['tgl_lahir'] ?? null
            ];
            
            // Validasi NISN
            if (empty($cleanData['nisn'])) {
                $rowErrors[] = "NISN tidak boleh kosong";
            } elseif (!preg_match('/^\d+$/', $cleanData['nisn'])) {
                $rowErrors[] = "NISN harus berisi angka";
            } elseif (in_array($cleanData['nisn'], $existingNisn)) {
                $rowErrors[] = "NISN sudah terdaftar di database";
            } elseif (in_array($cleanData['nisn'], $currentBatchNisn)) {
                $rowErrors[] = "NISN duplikat dalam file Excel";
            } else {
                $currentBatchNisn[] = $cleanData['nisn'];
                
                // Warning jika NISN bukan 10 digit
                if (strlen($cleanData['nisn']) != 10) {
                    $rowWarnings[] = "NISN sebaiknya 10 digit";
                }
            }
            
            // Validasi Nama
            if (empty($cleanData['nama_siswa'])) {
                $rowErrors[] = "Nama siswa tidak boleh kosong";
            } elseif (strlen($cleanData['nama_siswa']) < 2) {
                $rowErrors[] = "Nama siswa minimal 2 karakter";
            } elseif (strlen($cleanData['nama_siswa']) > 100) {
                $rowErrors[] = "Nama siswa maksimal 100 karakter";
            }
            
            // Validasi Jenis Kelamin
            if (empty($cleanData['jenis_kelamin'])) {
                $rowErrors[] = "Jenis kelamin tidak boleh kosong";
            } else {
                $jk = strtoupper($cleanData['jenis_kelamin']);
                if (in_array($jk, ['L', 'LAKI-LAKI', 'LAKI', 'M', 'MALE'])) {
                    $cleanData['jenis_kelamin'] = 'L';
                    if ($jk !== 'L') {
                        $rowWarnings[] = "Jenis kelamin dinormalisasi menjadi 'L'";
                    }
                } elseif (in_array($jk, ['P', 'PEREMPUAN', 'WANITA', 'F', 'FEMALE'])) {
                    $cleanData['jenis_kelamin'] = 'P';
                    if ($jk !== 'P') {
                        $rowWarnings[] = "Jenis kelamin dinormalisasi menjadi 'P'";
                    }
                } else {
                    $rowErrors[] = "Jenis kelamin harus L atau P";
                }
            }
            
            // Validasi Password
            if (empty($cleanData['password'])) {
                $rowErrors[] = "Password tidak boleh kosong";
            } elseif (strlen($cleanData['password']) < 6) {
                $rowErrors[] = "Password minimal 6 karakter";
            } elseif (strlen($cleanData['password']) > 255) {
                $rowErrors[] = "Password maksimal 255 karakter";
            }
            
            // Validasi Tanggal Lahir (opsional)
            if (!empty($cleanData['tgl_lahir'])) {
                if (!$this->validateDate($cleanData['tgl_lahir'])) {
                    $rowWarnings[] = "Format tanggal lahir tidak valid, akan diabaikan";
                    $cleanData['tgl_lahir'] = null;
                }
            }
            
            if (empty($rowErrors)) {
                $validData[] = $cleanData;
            } else {
                $errors[] = "Baris {$rowNum}: " . implode(', ', $rowErrors);
            }
            
            if (!empty($rowWarnings)) {
                $warnings[] = "Baris {$rowNum}: " . implode(', ', $rowWarnings);
            }
        }
        
        return [
            'valid_data' => $validData,
            'valid_count' => count($validData),
            'error_count' => count($errors),
            'warning_count' => count($warnings),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Validasi format tanggal
     */
    private function validateDate($date) {
        if (empty($date)) return true;
        
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'm/d/Y'];
        
        foreach ($formats as $format) {
            $dateObj = DateTime::createFromFormat($format, $date);
            if ($dateObj && $dateObj->format($format) === $date) {
                return true;
            }
        }
        return false;
    }

    /**
     * Import siswa dengan auto-create user accounts (sesuai schema database)
     */
    public function importSiswaWithAccounts($validData) {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $createdIds = [];
        
        foreach ($validData as $index => $data) {
            try {
                // 1. Insert siswa
                $idSiswaBaru = $this->tambahDataSiswa($data);
                
                if ($idSiswaBaru) {
                    // 2. Buat akun user - gunakan Database class langsung untuk konsistensi
                    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                    
                    $this->db->query('INSERT INTO users (username, password, password_plain, nama_lengkap, role, id_ref, status) 
                                     VALUES (:username, :password, :password_plain, :nama_lengkap, :role, :id_ref, "aktif")');
                    $this->db->bind('username', $data['nisn']);
                    $this->db->bind('password', $hashedPassword);
                    $this->db->bind('password_plain', $data['password']);
                    $this->db->bind('nama_lengkap', $data['nama_siswa']);
                    $this->db->bind('role', 'siswa');
                    $this->db->bind('id_ref', $idSiswaBaru);
                    
                    if ($this->db->execute()) {
                        $successCount++;
                        $createdIds[] = $idSiswaBaru;
                    } else {
                        // Rollback siswa jika gagal buat akun
                        $this->hapusDataSiswa($idSiswaBaru);
                        $errorCount++;
                        $errors[] = "Baris " . ($index + 1) . ": Gagal membuat akun untuk {$data['nama_siswa']}";
                    }
                } else {
                    $errorCount++;
                    $errors[] = "Baris " . ($index + 1) . ": Gagal menyimpan data {$data['nama_siswa']}";
                }
                
            } catch (Exception $e) {
                $errorCount++;
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                error_log("Import with accounts error: " . $e->getMessage());
            }
        }
        
        return [
            'success' => $successCount > 0,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'total_processed' => count($validData),
            'errors' => $errors,
            'created_ids' => $createdIds,
            'message' => "Import selesai. {$successCount} siswa berhasil ditambahkan dengan akun."
        ];
    }

    /**
     * Generate password default untuk siswa
     */
    public function generateDefaultPassword($nisn, $nama) {
        // Format: 3 digit terakhir NISN + 3 huruf pertama nama (lowercase)
        $lastDigits = substr($nisn, -3);
        $namePrefix = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $nama), 0, 3));
        return $lastDigits . $namePrefix . '123'; // Tambah 123 untuk keamanan
    }

    /**
     * Cleanup data siswa yang tidak valid
     */
    public function cleanupInvalidData() {
        try {
            $cleanupCount = 0;
            
            // Hapus siswa dengan NISN kosong atau null
            $this->db->query('DELETE FROM siswa WHERE nisn IS NULL OR nisn = ""');
            $this->db->execute();
            $cleanupCount += $this->db->rowCount();
            
            // Hapus siswa dengan nama kosong
            $this->db->query('DELETE FROM siswa WHERE nama_siswa IS NULL OR nama_siswa = ""');
            $this->db->execute();
            $cleanupCount += $this->db->rowCount();
            
            return $cleanupCount;
        } catch (Exception $e) {
            error_log("cleanupInvalidData error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get siswa untuk export dengan format yang bersih
     */
    public function getSiswaForExport() {
        try {
            $this->db->query('SELECT 
                                s.nisn,
                                s.nama_siswa,
                                CASE 
                                    WHEN s.jenis_kelamin = "L" THEN "Laki-laki"
                                    WHEN s.jenis_kelamin = "P" THEN "Perempuan"
                                    ELSE s.jenis_kelamin
                                END as jenis_kelamin_display,
                                s.jenis_kelamin,
                                s.tgl_lahir,
                                s.status_siswa,
                                u.password_plain,
                                CASE 
                                    WHEN u.password_plain IS NOT NULL THEN "Ada"
                                    ELSE "Belum Ada"
                                END as status_akun
                             FROM siswa s 
                             LEFT JOIN users u ON s.id_siswa = u.id_ref AND u.role = "siswa" 
                             ORDER BY s.status_siswa, s.nama_siswa ASC');
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getSiswaForExport error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update NISN untuk siswa (dengan validasi unik)
     */
    public function updateNisn($id_siswa, $nisn_baru) {
        try {
            // Cek apakah NISN baru sudah ada (kecuali untuk siswa yang sama)
            $this->db->query('SELECT COUNT(*) as total FROM siswa WHERE nisn = :nisn AND id_siswa != :id');
            $this->db->bind('nisn', $nisn_baru);
            $this->db->bind('id', $id_siswa);
            $result = $this->db->single();
            
            if ($result['total'] > 0) {
                return ['success' => false, 'error' => 'NISN sudah digunakan siswa lain'];
            }
            
            // Update NISN siswa
            $this->db->query('UPDATE siswa SET nisn = :nisn WHERE id_siswa = :id');
            $this->db->bind('nisn', $nisn_baru);
            $this->db->bind('id', $id_siswa);
            $this->db->execute();
            
            if ($this->db->rowCount() > 0) {
                // Update username di tabel users juga
                $this->db->query('UPDATE users SET username = :username WHERE id_ref = :id_ref AND role = "siswa"');
                $this->db->bind('username', $nisn_baru);
                $this->db->bind('id_ref', $id_siswa);
                $this->db->execute();
                
                return ['success' => true, 'message' => 'NISN berhasil diupdate'];
            } else {
                return ['success' => false, 'error' => 'Gagal update NISN'];
            }
        } catch (Exception $e) {
            error_log("updateNisn error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get duplicate NISN dalam database
     */
    public function getDuplicateNisn() {
        try {
            $this->db->query('SELECT nisn, COUNT(*) as jumlah 
                             FROM siswa 
                             WHERE nisn IS NOT NULL AND nisn != ""
                             GROUP BY nisn 
                             HAVING COUNT(*) > 1
                             ORDER BY jumlah DESC, nisn');
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getDuplicateNisn error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fix duplicate NISN dengan auto-generate NISN baru
     */
    public function fixDuplicateNisn() {
        try {
            $duplicates = $this->getDuplicateNisn();
            $fixedCount = 0;
            
            foreach ($duplicates as $duplicate) {
                $nisn = $duplicate['nisn'];
                
                // Ambil semua siswa dengan NISN duplikat
                $this->db->query('SELECT * FROM siswa WHERE nisn = :nisn ORDER BY id_siswa');
                $this->db->bind('nisn', $nisn);
                $siswaList = $this->db->resultSet();
                
                // Skip siswa pertama (biarkan tetap), fix yang lainnya
                for ($i = 1; $i < count($siswaList); $i++) {
                    $siswa = $siswaList[$i];
                    $newNisn = $this->generateUniqueNisn($nisn, $i);
                    
                    // Update NISN
                    $this->db->query('UPDATE siswa SET nisn = :new_nisn WHERE id_siswa = :id');
                    $this->db->bind('new_nisn', $newNisn);
                    $this->db->bind('id', $siswa['id_siswa']);
                    
                    if ($this->db->execute()) {
                        // Update username di users juga
                        $this->db->query('UPDATE users SET username = :username WHERE id_ref = :id_ref AND role = "siswa"');
                        $this->db->bind('username', $newNisn);
                        $this->db->bind('id_ref', $siswa['id_siswa']);
                        $this->db->execute();
                        
                        $fixedCount++;
                    }
                }
            }
            
            return $fixedCount;
        } catch (Exception $e) {
            error_log("fixDuplicateNisn error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Generate NISN unik untuk fix duplikasi
     */
    private function generateUniqueNisn($originalNisn, $suffix) {
        $newNisn = $originalNisn . sprintf('%02d', $suffix);
        
        // Pastikan tidak duplikat lagi
        if ($this->cekNisnExists($newNisn)) {
            // Jika masih duplikat, tambah timestamp
            $newNisn = substr($originalNisn, 0, 7) . date('His');
        }
        
        return $newNisn;
    }

    /**
     * Update data siswa lengkap (untuk wali kelas)
     */
    public function updateSiswa($id_siswa, $data) {
        $this->db->query('UPDATE siswa SET 
            nisn = :nisn,
            nama_siswa = :nama_siswa,
            jenis_kelamin = :jenis_kelamin,
            tgl_lahir = :tgl_lahir
            WHERE id_siswa = :id_siswa');
        
        $this->db->bind('nisn', $data['nisn']);
        $this->db->bind('nama_siswa', $data['nama_siswa']);
        $this->db->bind('jenis_kelamin', $data['jenis_kelamin']);
        $this->db->bind('tgl_lahir', $data['tanggal_lahir'] ?? $data['tgl_lahir'] ?? null);
        $this->db->bind('id_siswa', $id_siswa);
        
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}
