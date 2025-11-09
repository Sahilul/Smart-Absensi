<?php

// File: app/models/User_model.php - Sesuai Database Schema yang Ada
class User_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    // =================================================================
    // EXISTING METHODS - DISESUAIKAN DENGAN SCHEMA
    // =================================================================

    public function login($username, $password) {
        try {
            $this->db->query('SELECT * FROM users WHERE username = :username AND status = "aktif"');
            $this->db->bind('username', $username);
            $user = $this->db->single();
            
            if ($user && password_verify($password, $user['password'])) {
                // Update last login jika ada kolom tersebut
                $this->updateLastActivity($user['id_user']);
                return $user;
            }
            return false;
        } catch (Exception $e) {
            error_log("login error: " . $e->getMessage());
            return false;
        }
    }

    public function buatAkun($data) {
        try {
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $this->db->query('INSERT INTO users (username, password, password_plain, nama_lengkap, role, id_ref, status, created_at) 
                             VALUES (:username, :password, :password_plain, :nama_lengkap, :role, :id_ref, "aktif", NOW())');
            $this->db->bind('username', $data['username']);
            $this->db->bind('password', $hashedPassword);
            $this->db->bind('password_plain', $data['password']); // Simpan plain untuk keperluan admin
            $this->db->bind('nama_lengkap', $data['nama_lengkap']);
            $this->db->bind('role', $data['role']);
            $this->db->bind('id_ref', $data['id_ref']);
            
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("buatAkun error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id_ref, $role, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $this->db->query('UPDATE users SET password = :password, password_plain = :password_plain 
                             WHERE id_ref = :id_ref AND role = :role');
            $this->db->bind('password', $hashedPassword);
            $this->db->bind('password_plain', $newPassword);
            $this->db->bind('id_ref', $id_ref);
            $this->db->bind('role', $role);
            
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("updatePassword error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update username + optional password sekaligus (profile edit oleh user sendiri)
     */
    public function updateCredentials($user_id, $newUsername, $newPassword = null) {
        try {
            // Ambil user dulu
            $this->db->query('SELECT * FROM users WHERE id_user = :id_user');
            $this->db->bind('id_user', $user_id);
            $existing = $this->db->single();
            if (!$existing) { return ['success' => false, 'message' => 'User tidak ditemukan']; }

            // Cek jika username berubah dan sudah dipakai
            if ($existing['username'] !== $newUsername) {
                if ($this->cekUsernameExists($newUsername)) {
                    return ['success' => false, 'message' => 'Username sudah digunakan'];
                }
            }

            if ($newPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->db->query('UPDATE users SET username = :username, password = :password, password_plain = :password_plain WHERE id_user = :id_user');
                $this->db->bind('username', $newUsername);
                $this->db->bind('password', $hashedPassword);
                $this->db->bind('password_plain', $newPassword);
                $this->db->bind('id_user', $user_id);
            } else {
                $this->db->query('UPDATE users SET username = :username WHERE id_user = :id_user');
                $this->db->bind('username', $newUsername);
                $this->db->bind('id_user', $user_id);
            }

            $this->db->execute();
            if ($this->db->rowCount() > 0) {
                return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
            }
            return ['success' => true, 'message' => 'Tidak ada perubahan'];
        } catch (Exception $e) {
            error_log('updateCredentials error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan'];
        }
    }

    public function hapusAkun($id_ref, $role) {
        try {
            $this->db->query('DELETE FROM users WHERE id_ref = :id_ref AND role = :role');
            $this->db->bind('id_ref', $id_ref);
            $this->db->bind('role', $role);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("hapusAkun error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByUsername($username) {
        try {
            $this->db->query('SELECT * FROM users WHERE username = :username');
            $this->db->bind('username', $username);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("getUserByUsername error: " . $e->getMessage());
            return false;
        }
    }

    // =================================================================
    // NEW METHODS UNTUK IMPORT EXCEL - SESUAI SCHEMA
    // =================================================================

    /**
     * Cek apakah username sudah ada
     */
    public function cekUsernameExists($username) {
        try {
            $this->db->query('SELECT COUNT(*) as total FROM users WHERE username = :username');
            $this->db->bind('username', $username);
            $result = $this->db->single();
            return $result['total'] > 0;
        } catch (Exception $e) {
            error_log("cekUsernameExists error: " . $e->getMessage());
            return true; // Return true untuk safety
        }
    }

    /**
     * Get user by id_ref dan role
     */
    public function getUserByIdRef($id_ref, $role) {
        try {
            $this->db->query('SELECT * FROM users WHERE id_ref = :id_ref AND role = :role');
            $this->db->bind('id_ref', $id_ref);
            $this->db->bind('role', $role);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("getUserByIdRef error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Batch create user accounts untuk import
     */
    public function batchCreateAccounts($accountData) {
        if (empty($accountData)) {
            return ['success' => 0, 'errors' => [], 'created_ids' => []];
        }

        $successCount = 0;
        $errors = [];
        $createdIds = [];
        
        foreach ($accountData as $index => $data) {
            try {
                // Validasi data
                if (empty($data['username']) || empty($data['password']) || empty($data['id_ref'])) {
                    $errors[] = "Data akun tidak lengkap untuk: " . ($data['nama_lengkap'] ?? 'Unknown');
                    continue;
                }
                
                // Cek duplikasi username
                if ($this->cekUsernameExists($data['username'])) {
                    $errors[] = "Username {$data['username']} sudah terdaftar";
                    continue;
                }
                
                // Create account
                $userId = $this->buatAkun($data);
                
                if ($userId) {
                    $successCount++;
                    $createdIds[] = $userId;
                } else {
                    $errors[] = "Gagal membuat akun untuk: " . ($data['nama_lengkap'] ?? 'Unknown');
                }
                
            } catch (Exception $e) {
                $errors[] = "Error akun " . ($data['nama_lengkap'] ?? 'Unknown') . ": " . $e->getMessage();
                error_log("Batch create account error: " . $e->getMessage());
            }
        }
        
        return [
            'success' => $successCount,
            'errors' => $errors,
            'created_ids' => $createdIds,
            'total_processed' => count($accountData)
        ];
    }

    /**
     * Batch cek multiple username sekaligus
     */
    public function cekMultipleUsernameExists($usernames) {
        if (empty($usernames)) return [];
        
        try {
            $placeholders = implode(',', array_fill(0, count($usernames), '?'));
            $this->db->query("SELECT username FROM users WHERE username IN ($placeholders)");
            
            foreach ($usernames as $k => $username) {
                $this->db->bind($k + 1, $username);
            }
            
            $result = $this->db->resultSet();
            return array_column($result, 'username');
        } catch (Exception $e) {
            error_log("cekMultipleUsernameExists error: " . $e->getMessage());
            return $usernames; // Return semua sebagai existing untuk safety
        }
    }

    /**
     * Batch delete user accounts
     */
    public function batchDeleteAccounts($idRefs, $role = 'siswa') {
        if (empty($idRefs)) return 0;
        
        try {
            $placeholders = implode(',', array_fill(0, count($idRefs), '?'));
            $this->db->query("DELETE FROM users WHERE id_ref IN ($placeholders) AND role = ?");
            
            foreach ($idRefs as $k => $id) {
                $this->db->bind($k + 1, $id);
            }
            $this->db->bind(count($idRefs) + 1, $role);
            
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("batchDeleteAccounts error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role, $limit = null) {
        try {
            $sql = 'SELECT u.*, 
                           CASE 
                               WHEN u.role = "siswa" THEN s.nama_siswa
                               WHEN u.role = "guru" THEN g.nama_guru  
                               ELSE u.nama_lengkap
                           END as nama_display,
                           CASE 
                               WHEN u.role = "siswa" THEN s.nisn
                               WHEN u.role = "guru" THEN g.nik  
                               ELSE u.username
                           END as identifier
                    FROM users u
                    LEFT JOIN siswa s ON u.id_ref = s.id_siswa AND u.role = "siswa"
                    LEFT JOIN guru g ON u.id_ref = g.id_guru AND u.role = "guru" 
                    WHERE u.role = :role
                    ORDER BY u.created_at DESC';
            
            if ($limit) {
                $sql .= " LIMIT :limit";
            }
            
            $this->db->query($sql);
            $this->db->bind('role', $role);
            
            if ($limit) {
                $this->db->bind('limit', $limit);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getUsersByRole error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get statistik user accounts
     */
    public function getAccountStatistics() {
        try {
            $stats = [];
            
            // Total akun per role
            $this->db->query('SELECT role, COUNT(*) as total FROM users WHERE status = "aktif" GROUP BY role');
            $roleStats = $this->db->resultSet();
            
            foreach ($roleStats as $stat) {
                $stats['total_' . $stat['role']] = $stat['total'];
            }
            
            // Set default jika tidak ada data
            $stats['total_admin'] = $stats['total_admin'] ?? 0;
            $stats['total_guru'] = $stats['total_guru'] ?? 0;
            $stats['total_siswa'] = $stats['total_siswa'] ?? 0;
            
            // Total akun dengan password
            $this->db->query('SELECT COUNT(*) as total FROM users WHERE password_plain IS NOT NULL AND password_plain != "" AND status = "aktif"');
            $stats['total_with_password'] = $this->db->single()['total'];
            
            // Akun dibuat hari ini
            $this->db->query('SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()');
            $stats['created_today'] = $this->db->single()['total'];
            
            return $stats;
        } catch (Exception $e) {
            error_log("getAccountStatistics error: " . $e->getMessage());
            return [
                'total_admin' => 0,
                'total_guru' => 0,
                'total_siswa' => 0,
                'total_with_password' => 0,
                'created_today' => 0
            ];
        }
    }

    /**
     * Update last activity/login (simple tracking)
     */
    public function updateLastActivity($userId) {
        try {
            // Karena schema asli tidak punya kolom last_login, kita bisa skip atau tambah kolom
            // Untuk sementara, kita log ke file atau skip
            return true;
        } catch (Exception $e) {
            error_log("updateLastActivity error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate random password
     */
    public function generateRandomPassword($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    /**
     * Reset password untuk multiple users (untuk admin)
     */
    public function batchResetPassword($idRefs, $role, $newPassword) {
        if (empty($idRefs)) return 0;
        
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $successCount = 0;
            
            foreach ($idRefs as $id_ref) {
                $this->db->query('UPDATE users SET password = :password, password_plain = :password_plain 
                                 WHERE id_ref = :id_ref AND role = :role');
                $this->db->bind('password', $hashedPassword);
                $this->db->bind('password_plain', $newPassword);
                $this->db->bind('id_ref', $id_ref);
                $this->db->bind('role', $role);
                
                if ($this->db->execute() && $this->db->rowCount() > 0) {
                    $successCount++;
                }
            }
            
            return $successCount;
        } catch (Exception $e) {
            error_log("batchResetPassword error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get users yang belum punya password (untuk auto-generate)
     */
    public function getUsersWithoutPassword($role = null) {
        try {
            $sql = 'SELECT u.*, 
                           CASE 
                               WHEN u.role = "siswa" THEN s.nama_siswa
                               WHEN u.role = "guru" THEN g.nama_guru  
                               ELSE u.nama_lengkap
                           END as nama_display,
                           CASE 
                               WHEN u.role = "siswa" THEN s.nisn
                               WHEN u.role = "guru" THEN g.nik  
                               ELSE u.username
                           END as identifier
                    FROM users u
                    LEFT JOIN siswa s ON u.id_ref = s.id_siswa AND u.role = "siswa"
                    LEFT JOIN guru g ON u.id_ref = g.id_guru AND u.role = "guru" 
                    WHERE (u.password_plain IS NULL OR u.password_plain = "") AND u.status = "aktif"';
            
            if ($role) {
                $sql .= ' AND u.role = :role';
            }
            
            $sql .= ' ORDER BY u.nama_lengkap';
            
            $this->db->query($sql);
            
            if ($role) {
                $this->db->bind('role', $role);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getUsersWithoutPassword error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync user data dengan master data (siswa/guru)
     */
    public function syncUserDataWithMaster($role = null) {
        try {
            $syncCount = 0;
            
            if (!$role || $role === 'siswa') {
                // Sync nama siswa
                $this->db->query('UPDATE users u 
                                 JOIN siswa s ON u.id_ref = s.id_siswa 
                                 SET u.nama_lengkap = s.nama_siswa 
                                 WHERE u.role = "siswa" AND u.nama_lengkap != s.nama_siswa');
                $this->db->execute();
                $syncCount += $this->db->rowCount();
                
                // Update username dengan NISN terbaru
                $this->db->query('UPDATE users u 
                                 JOIN siswa s ON u.id_ref = s.id_siswa 
                                 SET u.username = s.nisn 
                                 WHERE u.role = "siswa" AND u.username != s.nisn AND s.nisn IS NOT NULL');
                $this->db->execute();
                $syncCount += $this->db->rowCount();
            }
            
            if (!$role || $role === 'guru') {
                // Sync nama guru
                $this->db->query('UPDATE users u 
                                 JOIN guru g ON u.id_ref = g.id_guru 
                                 SET u.nama_lengkap = g.nama_guru 
                                 WHERE u.role = "guru" AND u.nama_lengkap != g.nama_guru');
                $this->db->execute();
                $syncCount += $this->db->rowCount();
                
                // Update username dengan NIK terbaru
                $this->db->query('UPDATE users u 
                                 JOIN guru g ON u.id_ref = g.id_guru 
                                 SET u.username = g.nik 
                                 WHERE u.role = "guru" AND u.username != g.nik AND g.nik IS NOT NULL');
                $this->db->execute();
                $syncCount += $this->db->rowCount();
            }
            
            return $syncCount;
        } catch (Exception $e) {
            error_log("syncUserDataWithMaster error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cleanup orphaned user accounts (users tanpa referensi yang valid)
     */
    public function cleanupOrphanedAccounts() {
        try {
            $orphanedCount = 0;
            
            // Hapus akun siswa yang tidak punya referensi ke tabel siswa
            $this->db->query('DELETE u FROM users u 
                             LEFT JOIN siswa s ON u.id_ref = s.id_siswa 
                             WHERE u.role = "siswa" AND s.id_siswa IS NULL');
            $this->db->execute();
            $orphanedCount += $this->db->rowCount();
            
            // Hapus akun guru yang tidak punya referensi ke tabel guru
            $this->db->query('DELETE u FROM users u 
                             LEFT JOIN guru g ON u.id_ref = g.id_guru 
                             WHERE u.role = "guru" AND g.id_guru IS NULL');
            $this->db->execute();
            $orphanedCount += $this->db->rowCount();
            
            return $orphanedCount;
        } catch (Exception $e) {
            error_log("cleanupOrphanedAccounts error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all usernames untuk validasi import
     */
    public function getAllUsernames() {
        try {
            $this->db->query('SELECT username FROM users WHERE username IS NOT NULL AND username != ""');
            $result = $this->db->resultSet();
            return array_column($result, 'username');
        } catch (Exception $e) {
            error_log("getAllUsernames error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create user account dengan advanced validation
     */
    public function createAccountAdvanced($data, $validateUnique = true) {
        try {
            $errors = [];
            
            // Validasi input
            if (empty($data['username'])) {
                $errors[] = 'Username tidak boleh kosong';
            } elseif (strlen($data['username']) < 3) {
                $errors[] = 'Username minimal 3 karakter';
            } elseif (strlen($data['username']) > 50) {
                $errors[] = 'Username maksimal 50 karakter';
            } elseif ($validateUnique && $this->cekUsernameExists($data['username'])) {
                $errors[] = 'Username sudah terdaftar';
            }
            
            if (empty($data['password'])) {
                $errors[] = 'Password tidak boleh kosong';
            } elseif (strlen($data['password']) < 6) {
                $errors[] = 'Password minimal 6 karakter';
            }
            
            if (empty($data['nama_lengkap'])) {
                $errors[] = 'Nama lengkap tidak boleh kosong';
            }
            
            if (empty($data['role']) || !in_array($data['role'], ['admin', 'guru', 'siswa'])) {
                $errors[] = 'Role tidak valid';
            }
            
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            // Buat akun
            $userId = $this->buatAkun($data);
            
            if ($userId) {
                return ['success' => true, 'user_id' => $userId, 'errors' => []];
            } else {
                return ['success' => false, 'errors' => ['Gagal membuat akun']];
            }
        } catch (Exception $e) {
            error_log("createAccountAdvanced error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Update user status (aktif/nonaktif)
     */
    public function updateUserStatus($userId, $status) {
        $allowedStatus = ['aktif', 'nonaktif'];
        
        if (!in_array($status, $allowedStatus)) {
            return false;
        }
        
        try {
            $this->db->query('UPDATE users SET status = :status WHERE id_user = :id');
            $this->db->bind('status', $status);
            $this->db->bind('id', $userId);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("updateUserStatus error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get users dengan detail referensi (siswa/guru)
     */
    public function getUsersWithDetails($role = null) {
        try {
            $sql = 'SELECT u.id_user, u.username, u.nama_lengkap, u.role, u.status, u.created_at,
                           CASE 
                               WHEN u.role = "siswa" THEN s.nama_siswa
                               WHEN u.role = "guru" THEN g.nama_guru  
                               ELSE u.nama_lengkap
                           END as nama_display,
                           CASE 
                               WHEN u.role = "siswa" THEN s.nisn
                               WHEN u.role = "guru" THEN g.nik  
                               ELSE u.username
                           END as identifier,
                           CASE 
                               WHEN u.password_plain IS NOT NULL AND u.password_plain != "" THEN "Ada"
                               ELSE "Belum Ada"
                           END as status_password
                    FROM users u
                    LEFT JOIN siswa s ON u.id_ref = s.id_siswa AND u.role = "siswa"
                    LEFT JOIN guru g ON u.id_ref = g.id_guru AND u.role = "guru"';
            
            if ($role) {
                $sql .= ' WHERE u.role = :role';
            }
            
            $sql .= ' ORDER BY u.role, u.nama_lengkap';
            
            $this->db->query($sql);
            
            if ($role) {
                $this->db->bind('role', $role);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getUsersWithDetails error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate default password berdasarkan role dan data referensi
     */
    public function generateDefaultPasswordByRole($role, $referenceData) {
        switch ($role) {
            case 'siswa':
                // Format: 3 digit terakhir NISN + 3 huruf pertama nama + 123
                $nisn = $referenceData['nisn'] ?? '';
                $nama = $referenceData['nama_siswa'] ?? '';
                $lastDigits = substr($nisn, -3);
                $namePrefix = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $nama), 0, 3));
                return $lastDigits . $namePrefix . '123';
                
            case 'guru':
                // Format: 4 digit terakhir NIK + 'guru'
                $nik = $referenceData['nik'] ?? '';
                $lastDigits = substr($nik, -4);
                return $lastDigits . 'guru';
                
            default:
                return $this->generateRandomPassword();
        }
    }

    /**
     * Validate user credentials untuk batch import
     */
    public function validateUserCredentials($username, $password, $checkUnique = true) {
        $errors = [];
        
        // Validasi username
        if (empty($username)) {
            $errors[] = 'Username tidak boleh kosong';
        } else if (strlen($username) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        } else if (strlen($username) > 50) {
            $errors[] = 'Username maksimal 50 karakter';
        } else if ($checkUnique && $this->cekUsernameExists($username)) {
            $errors[] = 'Username sudah terdaftar';
        }
        
        // Validasi password
        if (empty($password)) {
            $errors[] = 'Password tidak boleh kosong';
        } else if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        } else if (strlen($password) > 255) {
            $errors[] = 'Password maksimal 255 karakter';
        }
        
        return $errors;
    }

    /**
     * Get users yang tidak ada referensi master data
     */
    public function getOrphanedUsers() {
        try {
            $this->db->query('SELECT u.*, "siswa" as orphan_type
                             FROM users u 
                             LEFT JOIN siswa s ON u.id_ref = s.id_siswa 
                             WHERE u.role = "siswa" AND s.id_siswa IS NULL
                             
                             UNION ALL
                             
                             SELECT u.*, "guru" as orphan_type
                             FROM users u 
                             LEFT JOIN guru g ON u.id_ref = g.id_guru 
                             WHERE u.role = "guru" AND g.id_guru IS NULL
                             
                             ORDER BY role, nama_lengkap');
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("getOrphanedUsers error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Batch update username untuk siswa berdasarkan NISN terbaru
     */
    public function syncSiswaUsernames() {
        try {
            $this->db->query('UPDATE users u 
                             JOIN siswa s ON u.id_ref = s.id_siswa 
                             SET u.username = s.nisn 
                             WHERE u.role = "siswa" 
                             AND s.nisn IS NOT NULL 
                             AND s.nisn != ""
                             AND u.username != s.nisn');
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("syncSiswaUsernames error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Batch update username untuk guru berdasarkan NIK terbaru
     */
    public function syncGuruUsernames() {
        try {
            $this->db->query('UPDATE users u 
                             JOIN guru g ON u.id_ref = g.id_guru 
                             SET u.username = g.nik 
                             WHERE u.role = "guru" 
                             AND g.nik IS NOT NULL 
                             AND g.nik != ""
                             AND u.username != g.nik');
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("syncGuruUsernames error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check dan fix duplicate usernames
     */
    public function fixDuplicateUsernames() {
        try {
            $fixedCount = 0;
            
            // Cari username duplikat
            $this->db->query('SELECT username, COUNT(*) as total 
                             FROM users 
                             WHERE username IS NOT NULL AND username != ""
                             GROUP BY username 
                             HAVING COUNT(*) > 1');
            $duplicates = $this->db->resultSet();
            
            foreach ($duplicates as $duplicate) {
                $username = $duplicate['username'];
                
                // Ambil semua user dengan username duplikat
                $this->db->query('SELECT * FROM users WHERE username = :username ORDER BY id_user');
                $this->db->bind('username', $username);
                $userList = $this->db->resultSet();
                
                // Skip user pertama, fix yang lainnya
                for ($i = 1; $i < count($userList); $i++) {
                    $user = $userList[$i];
                    $newUsername = $username . '_' . $user['id_user'];
                    
                    // Update username
                    $this->db->query('UPDATE users SET username = :new_username WHERE id_user = :id');
                    $this->db->bind('new_username', $newUsername);
                    $this->db->bind('id', $user['id_user']);
                    
                    if ($this->db->execute()) {
                        $fixedCount++;
                    }
                }
            }
            
            return $fixedCount;
        } catch (Exception $e) {
            error_log("fixDuplicateUsernames error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Export user data untuk backup
     */
    public function exportUserData($role = null) {
        try {
            $sql = 'SELECT u.username, u.nama_lengkap, u.role, u.status, u.created_at,
                           u.password_plain,
                           CASE 
                               WHEN u.role = "siswa" THEN s.nisn
                               WHEN u.role = "guru" THEN g.nik  
                               ELSE u.username
                           END as identifier,
                           CASE 
                               WHEN u.role = "siswa" THEN s.nama_siswa
                               WHEN u.role = "guru" THEN g.nama_guru  
                               ELSE u.nama_lengkap
                           END as nama_master
                    FROM users u
                    LEFT JOIN siswa s ON u.id_ref = s.id_siswa AND u.role = "siswa"
                    LEFT JOIN guru g ON u.id_ref = g.id_guru AND u.role = "guru"';
            
            if ($role) {
                $sql .= ' WHERE u.role = :role';
            }
            
            $sql .= ' ORDER BY u.role, u.nama_lengkap';
            
            $this->db->query($sql);
            
            if ($role) {
                $this->db->bind('role', $role);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("exportUserData error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update role user menjadi wali_kelas
     */
    public function updateRoleToWaliKelas($id_guru) {
        try {
            $this->db->query('UPDATE users SET role = "wali_kelas" WHERE id_ref = :id_guru AND role = "guru"');
            $this->db->bind('id_guru', $id_guru);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("updateRoleToWaliKelas error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Revert role user menjadi guru (jika sebelumnya wali_kelas)
     */
    public function updateRoleToGuru($id_guru) {
        try {
            $this->db->query('UPDATE users SET role = "guru" WHERE id_ref = :id_guru AND role = "wali_kelas"');
            $this->db->bind('id_guru', $id_guru);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("updateRoleToGuru error: " . $e->getMessage());
            return false;
        }
    }
}