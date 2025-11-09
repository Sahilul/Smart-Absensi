<?php

// File: app/models/KepalaSekolah_model.php - UPDATED WITH NEW FEATURES
class KepalaSekolah_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    /**
     * Get comprehensive dashboard statistics for current TP and semester
     */
    public function getDashboardStatistics($id_tp_aktif, $id_semester_aktif)
    {
        $stats = [];
        
        try {
            // Total guru yang mengajar di TP aktif
            $this->db->query('SELECT COUNT(DISTINCT p.id_guru) as total 
                             FROM penugasan p 
                             JOIN semester s ON p.id_semester = s.id_semester 
                             WHERE s.id_tp = :id_tp');
            $this->db->bind('id_tp', $id_tp_aktif);
            $stats['total_guru'] = $this->db->single()['total'] ?? 0;
            
            // Total siswa aktif di TP aktif
            $this->db->query('SELECT COUNT(DISTINCT kk.id_siswa) as total 
                             FROM keanggotaan_kelas kk 
                             JOIN siswa s ON kk.id_siswa = s.id_siswa 
                             WHERE kk.id_tp = :id_tp AND s.status_siswa = "aktif"');
            $this->db->bind('id_tp', $id_tp_aktif);
            $stats['total_siswa'] = $this->db->single()['total'] ?? 0;
            
            // Total kelas aktif di TP aktif
            $this->db->query('SELECT COUNT(*) as total FROM kelas WHERE id_tp = :id_tp');
            $this->db->bind('id_tp', $id_tp_aktif);
            $stats['total_kelas'] = $this->db->single()['total'] ?? 0;
            
            // Total mata pelajaran yang diajarkan di semester aktif
            $this->db->query('SELECT COUNT(DISTINCT p.id_mapel) as total 
                             FROM penugasan p 
                             WHERE p.id_semester = :id_semester');
            $this->db->bind('id_semester', $id_semester_aktif);
            $stats['total_mapel'] = $this->db->single()['total'] ?? 0;
            
            // Total jurnal di semester aktif
            $this->db->query('SELECT COUNT(*) as total 
                             FROM jurnal j 
                             JOIN penugasan p ON j.id_penugasan = p.id_penugasan 
                             WHERE p.id_semester = :id_semester');
            $this->db->bind('id_semester', $id_semester_aktif);
            $stats['total_jurnal'] = $this->db->single()['total'] ?? 0;
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error in getDashboardStatistics: " . $e->getMessage());
            return [
                'total_guru' => 0,
                'total_siswa' => 0,
                'total_kelas' => 0,
                'total_mapel' => 0,
                'total_jurnal' => 0
            ];
        }
    }

    /**
     * Get jurnal count by date range for active semester - WITH CLASS FILTER
     */
    public function getJurnalCountByDateRange($startDate, $endDate, $id_semester_aktif, $kelasFilter = '')
    {
        try {
            $sql = 'SELECT COUNT(*) as total 
                    FROM jurnal j 
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan 
                    WHERE j.tanggal BETWEEN :start_date AND :end_date 
                    AND p.id_semester = :id_semester';
            
            if (!empty($kelasFilter)) {
                $sql .= ' AND p.id_kelas = :id_kelas';
            }
            
            $this->db->query($sql);
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            
            if (!empty($kelasFilter)) {
                $this->db->bind('id_kelas', $kelasFilter);
            }
            
            $result = $this->db->single();
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Error in getJurnalCountByDateRange: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total pertemuan by date range for active semester - WITH CLASS FILTER
     */
    public function getTotalPertemuanByDateRange($startDate, $endDate, $id_semester_aktif, $kelasFilter = '')
    {
        try {
            $sql = 'SELECT COUNT(DISTINCT CONCAT(j.id_penugasan, "-", j.pertemuan_ke)) as total 
                    FROM jurnal j 
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan 
                    WHERE j.tanggal BETWEEN :start_date AND :end_date 
                    AND p.id_semester = :id_semester';
            
            if (!empty($kelasFilter)) {
                $sql .= ' AND p.id_kelas = :id_kelas';
            }
            
            $this->db->query($sql);
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            
            if (!empty($kelasFilter)) {
                $this->db->bind('id_kelas', $kelasFilter);
            }
            
            $result = $this->db->single();
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Error in getTotalPertemuanByDateRange: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get attendance statistics by date range for active semester - WITH CLASS FILTER
     */
    public function getAttendanceStatsByDateRange($startDate, $endDate, $id_semester_aktif, $kelasFilter = '')
    {
        try {
            $sql = 'SELECT 
                        SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN a.status_kehadiran = "S" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN a.status_kehadiran = "I" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN a.status_kehadiran = "A" THEN 1 ELSE 0 END) as alfa,
                        COUNT(*) as total
                      FROM absensi a
                      JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                      JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                      WHERE j.tanggal BETWEEN :start_date AND :end_date
                      AND p.id_semester = :id_semester';
            
            if (!empty($kelasFilter)) {
                $sql .= ' AND p.id_kelas = :id_kelas';
            }
            
            $this->db->query($sql);
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            
            if (!empty($kelasFilter)) {
                $this->db->bind('id_kelas', $kelasFilter);
            }
            
            $result = $this->db->single();
            return $result ?: [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alfa' => 0,
                'total' => 0
            ];
        } catch (Exception $e) {
            error_log("Error in getAttendanceStatsByDateRange: " . $e->getMessage());
            return [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alfa' => 0,
                'total' => 0
            ];
        }
    }

    /**
     * Get trend data for attendance based on period - WITH CLASS FILTER
     */
    public function getAttendanceTrendData($period, $startDate, $endDate, $id_semester_aktif, $kelasFilter = '')
    {
        try {
            $labels = [];
            $data = [];
            $kelasCondition = !empty($kelasFilter) ? ' AND p.id_kelas = :id_kelas' : '';
            
            switch ($period) {
                case 'today':
                    $sql = 'SELECT 
                                COUNT(*) as total,
                                SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir
                              FROM absensi a
                              JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                              JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                              WHERE j.tanggal = :tanggal
                              AND p.id_semester = :id_semester' . $kelasCondition;
                    
                    $this->db->query($sql);
                    $this->db->bind('tanggal', $startDate);
                    $this->db->bind('id_semester', $id_semester_aktif);
                    
                    if (!empty($kelasFilter)) {
                        $this->db->bind('id_kelas', $kelasFilter);
                    }
                    
                    $result = $this->db->single();
                    
                    $labels = ['Hari Ini'];
                    $total = $result['total'] ?? 0;
                    $hadir = $result['hadir'] ?? 0;
                    $data = [$total > 0 ? round(($hadir / $total) * 100, 1) : 0];
                    break;
                    
                case 'this_week':
                    // 7 hari terakhir
                    for ($i = 6; $i >= 0; $i--) {
                        $checkDate = date('Y-m-d', strtotime("-$i days", strtotime($endDate)));
                        
                        $sql = 'SELECT 
                                    COUNT(*) as total,
                                    SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir
                                  FROM absensi a
                                  JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                                  JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                                  WHERE j.tanggal = :tanggal
                                  AND p.id_semester = :id_semester' . $kelasCondition;
                        
                        $this->db->query($sql);
                        $this->db->bind('tanggal', $checkDate);
                        $this->db->bind('id_semester', $id_semester_aktif);
                        
                        if (!empty($kelasFilter)) {
                            $this->db->bind('id_kelas', $kelasFilter);
                        }
                        
                        $dayResult = $this->db->single();
                        
                        $labels[] = date('D', strtotime($checkDate));
                        $total = $dayResult['total'] ?? 0;
                        $hadir = $dayResult['hadir'] ?? 0;
                        $data[] = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
                    }
                    break;
                    
                case 'this_month':
                case 'this_semester':
                case 'custom':
                default:
                    // Group by tanggal dalam range
                    $sql = 'SELECT 
                                j.tanggal,
                                COUNT(*) as total,
                                SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                                ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase
                              FROM absensi a
                              JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                              JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                              WHERE j.tanggal BETWEEN :start_date AND :end_date
                              AND p.id_semester = :id_semester' . $kelasCondition . '
                              GROUP BY j.tanggal
                              ORDER BY j.tanggal ASC
                              LIMIT 20';
                    
                    $this->db->query($sql);
                    $this->db->bind('start_date', $startDate);
                    $this->db->bind('end_date', $endDate);
                    $this->db->bind('id_semester', $id_semester_aktif);
                    
                    if (!empty($kelasFilter)) {
                        $this->db->bind('id_kelas', $kelasFilter);
                    }
                    
                    $trendResult = $this->db->resultSet();
                    
                    foreach ($trendResult as $row) {
                        $labels[] = date('M d', strtotime($row['tanggal']));
                        $data[] = $row['persentase'] ?? 0;
                    }
                    
                    // Jika tidak ada data, beri default
                    if (empty($labels)) {
                        $labels = ['No Data'];
                        $data = [0];
                    }
                    break;
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
            
        } catch (Exception $e) {
            error_log("Error in getAttendanceTrendData: " . $e->getMessage());
            return [
                'labels' => ['No Data'],
                'data' => [0]
            ];
        }
    }

    /**
     * NEW: Get guru mengajar statistics by date range
     */
    public function getGuruMengajarStatsByDateRange($startDate, $endDate, $id_semester_aktif)
    {
        try {
            // Guru yang mengajar dalam periode
            $this->db->query('SELECT COUNT(DISTINCT p.id_guru) as total 
                             FROM jurnal j
                             JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                             WHERE j.tanggal BETWEEN :start_date AND :end_date
                             AND p.id_semester = :id_semester');
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            $guru_mengajar = $this->db->single()['total'] ?? 0;
            
            // Total guru yang seharusnya mengajar
            $this->db->query('SELECT COUNT(DISTINCT id_guru) as total 
                             FROM penugasan 
                             WHERE id_semester = :id_semester');
            $this->db->bind('id_semester', $id_semester_aktif);
            $total_guru = $this->db->single()['total'] ?? 0;
            
            return [
                'guru_mengajar' => $guru_mengajar,
                'total_guru' => $total_guru,
                'persentase' => $total_guru > 0 ? round(($guru_mengajar / $total_guru) * 100, 1) : 0
            ];
            
        } catch (Exception $e) {
            error_log("Error in getGuruMengajarStatsByDateRange: " . $e->getMessage());
            return [
                'guru_mengajar' => 0,
                'total_guru' => 0,
                'persentase' => 0
            ];
        }
    }

    /**
     * NEW: Get top kelas by date range (dynamic period)
     */
    public function getTopKelasByDateRange($startDate, $endDate, $id_semester_aktif)
    {
        try {
            $this->db->query('SELECT 
                                k.nama_kelas,
                                COUNT(*) as total_absensi,
                                SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                                ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
                              FROM absensi a
                              JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                              JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                              JOIN kelas k ON p.id_kelas = k.id_kelas
                              WHERE j.tanggal BETWEEN :start_date AND :end_date
                              AND p.id_semester = :id_semester
                              GROUP BY k.id_kelas, k.nama_kelas
                              HAVING total_absensi > 0
                              ORDER BY persentase_hadir DESC, hadir DESC
                              LIMIT 5');
            
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getTopKelasByDateRange: " . $e->getMessage());
            return [];
        }
    }

    /**
     * NEW: Get absent students summary by date range
     */
    public function getAbsentStudentsSummary($startDate, $endDate, $id_semester_aktif, $statusFilter = '', $kelasFilter = '')
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as alfa
                    FROM absensi a
                    JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE j.tanggal BETWEEN :start_date AND :end_date
                    AND p.id_semester = :id_semester
                    AND a.status_kehadiran IN ('S', 'I', 'A')
                    AND s.status_siswa = 'aktif'";
            
            if (!empty($statusFilter)) {
                $sql .= " AND a.status_kehadiran = :status_filter";
            }
            
            if (!empty($kelasFilter)) {
                $sql .= " AND p.id_kelas = :kelas_filter";
            }
            
            $this->db->query($sql);
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            
            if (!empty($statusFilter)) {
                $this->db->bind('status_filter', $statusFilter);
            }
            
            if (!empty($kelasFilter)) {
                $this->db->bind('kelas_filter', $kelasFilter);
            }
            
            $result = $this->db->single();
            return [
                'total' => $result['total'] ?? 0,
                'sakit' => $result['sakit'] ?? 0,
                'izin' => $result['izin'] ?? 0,
                'alfa' => $result['alfa'] ?? 0
            ];
        } catch (Exception $e) {
            error_log("Error in getAbsentStudentsSummary: " . $e->getMessage());
            return [
                'total' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alfa' => 0
            ];
        }
    }

    /**
     * NEW: Get detailed absent students by date range with pagination
     */
    public function getAbsentStudentsDetailedByDateRange($startDate, $endDate, $id_semester_aktif, $statusFilter = '', $kelasFilter = '', $page = 0, $limit = 20)
    {
        try {
            $offset = $page * $limit;
            
            $sql = "SELECT 
                        s.id_siswa,
                        s.nisn,
                        s.nama_siswa,
                        k.nama_kelas,
                        a.status_kehadiran,
                        a.keterangan,
                        j.tanggal,
                        m.nama_mapel,
                        g.nama_guru
                    FROM absensi a
                    JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN guru g ON p.id_guru = g.id_guru
                    WHERE j.tanggal BETWEEN :start_date AND :end_date
                    AND p.id_semester = :id_semester
                    AND a.status_kehadiran IN ('S', 'I', 'A')
                    AND s.status_siswa = 'aktif'";
            
            if (!empty($statusFilter)) {
                $sql .= " AND a.status_kehadiran = :status_filter";
            }
            
            if (!empty($kelasFilter)) {
                $sql .= " AND p.id_kelas = :kelas_filter";
            }
            
            $sql .= " ORDER BY s.nama_siswa ASC, j.tanggal DESC LIMIT :offset, :limit";
            
            $this->db->query($sql);
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            $this->db->bind('offset', $offset);
            $this->db->bind('limit', $limit);
            
            if (!empty($statusFilter)) {
                $this->db->bind('status_filter', $statusFilter);
            }
            
            if (!empty($kelasFilter)) {
                $this->db->bind('kelas_filter', $kelasFilter);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAbsentStudentsDetailedByDateRange: " . $e->getMessage());
            return [];
        }
    }

    /**
     * NEW: Get absent students count for pagination
     */
    public function getAbsentStudentsCount($startDate, $endDate, $id_semester_aktif, $statusFilter = '', $kelasFilter = '')
    {
        try {
            $sql = "SELECT COUNT(*) as total
                    FROM absensi a
                    JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    WHERE j.tanggal BETWEEN :start_date AND :end_date
                    AND p.id_semester = :id_semester
                    AND a.status_kehadiran IN ('S', 'I', 'A')
                    AND s.status_siswa = 'aktif'";
            
            if (!empty($statusFilter)) {
                $sql .= " AND a.status_kehadiran = :status_filter";
            }
            
            if (!empty($kelasFilter)) {
                $sql .= " AND p.id_kelas = :kelas_filter";
            }
            
            $this->db->query($sql);
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            
            if (!empty($statusFilter)) {
                $this->db->bind('status_filter', $statusFilter);
            }
            
            if (!empty($kelasFilter)) {
                $this->db->bind('kelas_filter', $kelasFilter);
            }
            
            $result = $this->db->single();
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Error in getAbsentStudentsCount: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * LEGACY: Get absent students by date range for active semester (keeping for backward compatibility)
     */
    public function getAbsentStudentsByDateRange($startDate, $endDate, $id_semester_aktif, $statusFilter = '', $kelasFilter = '')
    {
        try {
            $sql = "SELECT 
                        s.nisn,
                        s.nama_siswa,
                        k.nama_kelas,
                        a.status_kehadiran,
                        a.keterangan,
                        j.tanggal,
                        m.nama_mapel,
                        g.nama_guru
                    FROM absensi a
                    JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN guru g ON p.id_guru = g.id_guru
                    WHERE j.tanggal BETWEEN :start_date AND :end_date
                    AND p.id_semester = :id_semester
                    AND a.status_kehadiran IN ('S', 'I', 'A')
                    AND s.status_siswa = 'aktif'";
            
            if (!empty($statusFilter)) {
                $sql .= " AND a.status_kehadiran = :status_filter";
            }
            
            if (!empty($kelasFilter)) {
                $sql .= " AND k.id_kelas = :kelas_filter";
            }
            
            $sql .= " ORDER BY j.tanggal DESC, s.nama_siswa ASC LIMIT 100";
            
            $this->db->query($sql);
            $this->db->bind('start_date', $startDate);
            $this->db->bind('end_date', $endDate);
            $this->db->bind('id_semester', $id_semester_aktif);
            
            if (!empty($statusFilter)) {
                $this->db->bind('status_filter', $statusFilter);
            }
            
            if (!empty($kelasFilter)) {
                $this->db->bind('kelas_filter', $kelasFilter);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAbsentStudentsByDateRange: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get kelas options for active TP
     */
    public function getKelasOptionsForActiveTP($id_tp_aktif)
    {
        try {
            $this->db->query('SELECT id_kelas, nama_kelas, jenjang 
                             FROM kelas 
                             WHERE id_tp = :id_tp 
                             ORDER BY jenjang ASC, nama_kelas ASC');
            $this->db->bind('id_tp', $id_tp_aktif);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getKelasOptionsForActiveTP: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get comprehensive monitoring data for kepala madrasah
     */
    public function getMonitoringData($id_tp_aktif, $id_semester_aktif)
    {
        try {
            // Statistik guru mengajar hari ini
            $this->db->query('SELECT COUNT(DISTINCT p.id_guru) as total 
                             FROM jurnal j
                             JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                             WHERE DATE(j.tanggal) = CURDATE()
                             AND p.id_semester = :id_semester');
            $this->db->bind('id_semester', $id_semester_aktif);
            $guru_mengajar_hari_ini = $this->db->single()['total'] ?? 0;
            
            // Total guru yang seharusnya mengajar
            $this->db->query('SELECT COUNT(DISTINCT id_guru) as total 
                             FROM penugasan 
                             WHERE id_semester = :id_semester');
            $this->db->bind('id_semester', $id_semester_aktif);
            $total_guru_mengajar = $this->db->single()['total'] ?? 0;
            
            // Kelas dengan kehadiran tertinggi hari ini
            $this->db->query('SELECT 
                                k.nama_kelas,
                                COUNT(*) as total_absensi,
                                SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                                ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
                              FROM absensi a
                              JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                              JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                              JOIN kelas k ON p.id_kelas = k.id_kelas
                              WHERE DATE(j.tanggal) = CURDATE()
                              AND p.id_semester = :id_semester
                              GROUP BY k.id_kelas, k.nama_kelas
                              HAVING total_absensi > 0
                              ORDER BY persentase_hadir DESC, hadir DESC
                              LIMIT 5');
            $this->db->bind('id_semester', $id_semester_aktif);
            $top_kelas_hari_ini = $this->db->resultSet();
            
            // Siswa dengan kehadiran terbaik bulan ini
            $this->db->query('SELECT 
                                s.nama_siswa,
                                k.nama_kelas,
                                COUNT(*) as total_absensi,
                                SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                                ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
                              FROM absensi a
                              JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                              JOIN siswa s ON a.id_siswa = s.id_siswa
                              JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                              JOIN kelas k ON p.id_kelas = k.id_kelas
                              WHERE MONTH(j.tanggal) = MONTH(CURDATE())
                              AND YEAR(j.tanggal) = YEAR(CURDATE())
                              AND p.id_semester = :id_semester
                              AND s.status_siswa = "aktif"
                              GROUP BY s.id_siswa, s.nama_siswa, k.nama_kelas
                              HAVING total_absensi >= 5
                              ORDER BY persentase_hadir DESC, hadir DESC
                              LIMIT 10');
            $this->db->bind('id_semester', $id_semester_aktif);
            $top_siswa_bulan_ini = $this->db->resultSet();
            
            return [
                'guru_mengajar_hari_ini' => $guru_mengajar_hari_ini,
                'total_guru_mengajar' => $total_guru_mengajar,
                'persentase_guru_aktif' => $total_guru_mengajar > 0 ? round(($guru_mengajar_hari_ini / $total_guru_mengajar) * 100, 1) : 0,
                'top_kelas_hari_ini' => $top_kelas_hari_ini,
                'top_siswa_bulan_ini' => $top_siswa_bulan_ini
            ];
        } catch (Exception $e) {
            error_log("Error in getMonitoringData: " . $e->getMessage());
            return [
                'guru_mengajar_hari_ini' => 0,
                'total_guru_mengajar' => 0,
                'persentase_guru_aktif' => 0,
                'top_kelas_hari_ini' => [],
                'top_siswa_bulan_ini' => []
            ];
        }
    }

    /**
     * Get monthly attendance statistics for active semester
     */
    public function getMonthlyAttendanceStats($id_semester_aktif)
    {
        try {
            $this->db->query('SELECT 
                                MONTH(j.tanggal) as bulan,
                                YEAR(j.tanggal) as tahun,
                                MONTHNAME(j.tanggal) as nama_bulan,
                                COUNT(*) as total_absensi,
                                SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as total_hadir,
                                ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
                              FROM absensi a
                              JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                              JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                              WHERE p.id_semester = :id_semester
                              GROUP BY YEAR(j.tanggal), MONTH(j.tanggal)
                              ORDER BY tahun ASC, bulan ASC');
            
            $this->db->bind('id_semester', $id_semester_aktif);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getMonthlyAttendanceStats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get subject performance statistics for active semester
     */
    public function getSubjectPerformanceStats($id_semester_aktif)
    {
        try {
            $this->db->query('SELECT 
                                m.nama_mapel,
                                COUNT(DISTINCT j.id_jurnal) as total_pertemuan,
                                COUNT(*) as total_absensi,
                                SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as total_hadir,
                                ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
                              FROM absensi a
                              JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                              JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                              JOIN mapel m ON p.id_mapel = m.id_mapel
                              WHERE p.id_semester = :id_semester
                              GROUP BY m.id_mapel, m.nama_mapel
                              ORDER BY persentase_hadir DESC');
            
            $this->db->bind('id_semester', $id_semester_aktif);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getSubjectPerformanceStats: " . $e->getMessage());
            return [];
        }
    }
}