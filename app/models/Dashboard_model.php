<?php

// File: app/models/Dashboard_model.php
class Dashboard_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    /**
     * Ambil statistik lengkap untuk dashboard dalam 1 query
     */
    public function getDashboardStatistics($id_semester_aktif = null, $id_tp_aktif = null)
    {
        // Statistik dasar
        $stats = [];
        
        // Total guru
        $this->db->query('SELECT COUNT(*) as total FROM guru');
        $stats['total_guru'] = $this->db->single()['total'];
        
        // Total siswa berdasarkan status
        $this->db->query('SELECT status_siswa, COUNT(*) as total FROM siswa GROUP BY status_siswa');
        $siswa_status = $this->db->resultSet();
        $stats['siswa'] = [
            'aktif' => 0,
            'lulus' => 0,
            'pindah' => 0
        ];
        foreach ($siswa_status as $status) {
            $stats['siswa'][$status['status_siswa']] = $status['total'];
        }
        
        // Total kelas aktif
        if ($id_tp_aktif) {
            $this->db->query('SELECT COUNT(*) as total FROM kelas WHERE id_tp = :id_tp');
            $this->db->bind('id_tp', $id_tp_aktif);
            $stats['total_kelas'] = $this->db->single()['total'];
        } else {
            $stats['total_kelas'] = 0;
        }
        
        // Total mata pelajaran
        $this->db->query('SELECT COUNT(*) as total FROM mapel');
        $stats['total_mapel'] = $this->db->single()['total'];
        
        return $stats;
    }

    /**
     * Statistik absensi hari ini
     */
    public function getAttendanceToday()
    {
        $this->db->query('SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                            SUM(CASE WHEN a.status_kehadiran = "I" THEN 1 ELSE 0 END) as izin,
                            SUM(CASE WHEN a.status_kehadiran = "S" THEN 1 ELSE 0 END) as sakit,
                            SUM(CASE WHEN a.status_kehadiran = "A" THEN 1 ELSE 0 END) as alfa
                          FROM absensi a
                          JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                          WHERE DATE(j.tanggal) = CURDATE()');
        
        $result = $this->db->single();
        $total = $result['total'] ?? 0;
        $hadir = $result['hadir'] ?? 0;
        
        return [
            'total' => $total,
            'hadir' => $hadir,
            'izin' => $result['izin'] ?? 0,
            'sakit' => $result['sakit'] ?? 0,
            'alfa' => $result['alfa'] ?? 0,
            'percentage' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0
        ];
    }

    /**
     * Trend kehadiran 7 hari terakhir
     */
    public function getAttendanceTrend($days = 7)
    {
        $this->db->query('SELECT 
                            DATE(j.tanggal) as tanggal,
                            DAYNAME(j.tanggal) as hari,
                            COUNT(*) as total_absensi,
                            SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                            SUM(CASE WHEN a.status_kehadiran = "I" THEN 1 ELSE 0 END) as izin,
                            SUM(CASE WHEN a.status_kehadiran = "S" THEN 1 ELSE 0 END) as sakit,
                            SUM(CASE WHEN a.status_kehadiran = "A" THEN 1 ELSE 0 END) as alfa,
                            ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase
                          FROM absensi a
                          JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                          WHERE j.tanggal >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                          GROUP BY DATE(j.tanggal)
                          ORDER BY j.tanggal ASC');
        
        $this->db->bind('days', $days);
        return $this->db->resultSet();
    }

    /**
     * Jurnal mengajar terbaru
     */
    public function getRecentJournals($limit = 10)
    {
        $this->db->query('SELECT 
                            j.id_jurnal,
                            j.tanggal,
                            j.topik_materi,
                            j.timestamp,
                            g.nama_guru,
                            m.nama_mapel,
                            k.nama_kelas
                          FROM jurnal j
                          JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                          JOIN guru g ON p.id_guru = g.id_guru
                          JOIN mapel m ON p.id_mapel = m.id_mapel
                          JOIN kelas k ON p.id_kelas = k.id_kelas
                          ORDER BY j.timestamp DESC
                          LIMIT :limit');
        
        $this->db->bind('limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Monitoring guru yang belum input jurnal hari ini
     */
    public function getTeacherJournalStatus($id_semester_aktif)
    {
        // Total guru yang mengajar di semester aktif
        $this->db->query('SELECT COUNT(DISTINCT id_guru) as total FROM penugasan WHERE id_semester = :id_semester');
        $this->db->bind('id_semester', $id_semester_aktif);
        $total_guru_mengajar = $this->db->single()['total'];
        
        // Guru yang sudah input jurnal hari ini
        $this->db->query('SELECT COUNT(DISTINCT p.id_guru) as sudah_jurnal 
                          FROM penugasan p
                          JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                          WHERE p.id_semester = :id_semester AND DATE(j.tanggal) = CURDATE()');
        $this->db->bind('id_semester', $id_semester_aktif);
        $guru_sudah_jurnal = $this->db->single()['sudah_jurnal'];
        
        // Detail guru yang belum input jurnal
        $this->db->query('SELECT DISTINCT g.nama_guru, g.id_guru
                          FROM guru g
                          JOIN penugasan p ON g.id_guru = p.id_guru
                          WHERE p.id_semester = :id_semester
                          AND g.id_guru NOT IN (
                              SELECT DISTINCT p2.id_guru 
                              FROM penugasan p2
                              JOIN jurnal j2 ON p2.id_penugasan = j2.id_penugasan
                              WHERE p2.id_semester = :id_semester2 AND DATE(j2.tanggal) = CURDATE()
                          )');
        $this->db->bind('id_semester', $id_semester_aktif);
        $this->db->bind('id_semester2', $id_semester_aktif);
        $guru_belum_jurnal_detail = $this->db->resultSet();
        
        return [
            'total_guru_mengajar' => $total_guru_mengajar,
            'guru_sudah_jurnal' => $guru_sudah_jurnal,
            'guru_belum_jurnal' => $total_guru_mengajar - $guru_sudah_jurnal,
            'detail_guru_belum_jurnal' => $guru_belum_jurnal_detail
        ];
    }

    /**
     * Statistik kelas terpopuler (paling banyak jurnal)
     */
    public function getClassStatistics($id_semester_aktif, $limit = 5)
    {
        $this->db->query('SELECT 
                            k.nama_kelas,
                            k.jenjang,
                            COUNT(j.id_jurnal) as total_jurnal,
                            COUNT(DISTINCT j.tanggal) as hari_aktif,
                            AVG(
                                (SELECT COUNT(*) FROM absensi a2 
                                 WHERE a2.id_jurnal = j.id_jurnal AND a2.status_kehadiran = "H") /
                                (SELECT COUNT(*) FROM absensi a3 WHERE a3.id_jurnal = j.id_jurnal) * 100
                            ) as avg_kehadiran
                          FROM kelas k
                          JOIN penugasan p ON k.id_kelas = p.id_kelas
                          JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                          WHERE p.id_semester = :id_semester
                          GROUP BY k.id_kelas, k.nama_kelas, k.jenjang
                          ORDER BY total_jurnal DESC
                          LIMIT :limit');
        
        $this->db->bind('id_semester', $id_semester_aktif);
        $this->db->bind('limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Dashboard analytics untuk chart bulanan
     */
    public function getMonthlyAnalytics($id_semester_aktif)
    {
        // Jurnal per bulan
        $this->db->query('SELECT 
                            MONTH(j.tanggal) as bulan,
                            YEAR(j.tanggal) as tahun,
                            MONTHNAME(j.tanggal) as nama_bulan,
                            COUNT(j.id_jurnal) as total_jurnal,
                            COUNT(DISTINCT j.tanggal) as hari_aktif
                          FROM jurnal j
                          JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                          WHERE p.id_semester = :id_semester
                          GROUP BY YEAR(j.tanggal), MONTH(j.tanggal)
                          ORDER BY tahun, bulan');
        
        $this->db->bind('id_semester', $id_semester_aktif);
        $jurnal_bulanan = $this->db->resultSet();
        
        // Kehadiran per bulan
        $this->db->query('SELECT 
                            MONTH(j.tanggal) as bulan,
                            YEAR(j.tanggal) as tahun,
                            COUNT(a.id_absensi) as total_absensi,
                            SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as total_hadir,
                            ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(a.id_absensi)) * 100, 1) as persentase_hadir
                          FROM absensi a
                          JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                          JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                          WHERE p.id_semester = :id_semester
                          GROUP BY YEAR(j.tanggal), MONTH(j.tanggal)
                          ORDER BY tahun, bulan');
        
        $this->db->bind('id_semester', $id_semester_aktif);
        $kehadiran_bulanan = $this->db->resultSet();
        
        return [
            'jurnal' => $jurnal_bulanan,
            'kehadiran' => $kehadiran_bulanan
        ];
    }

    /**
     * Top performing students (kehadiran terbaik)
     */
    public function getTopStudents($id_semester_aktif, $limit = 10)
    {
        $this->db->query('SELECT 
                            s.nama_siswa,
                            s.nisn,
                            k.nama_kelas,
                            COUNT(a.id_absensi) as total_kehadiran,
                            SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                            ROUND((SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(a.id_absensi)) * 100, 1) as persentase_hadir
                          FROM siswa s
                          JOIN absensi a ON s.id_siswa = a.id_siswa
                          JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                          JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                          JOIN kelas k ON p.id_kelas = k.id_kelas
                          WHERE p.id_semester = :id_semester
                          AND s.status_siswa = "aktif"
                          GROUP BY s.id_siswa, s.nama_siswa, s.nisn, k.nama_kelas
                          HAVING total_kehadiran > 5
                          ORDER BY persentase_hadir DESC, hadir DESC
                          LIMIT :limit');
        
        $this->db->bind('id_semester', $id_semester_aktif);
        $this->db->bind('limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * System health check
     */
    public function getSystemHealth()
    {
        $health = [
            'status' => 'healthy',
            'issues' => [],
            'warnings' => []
        ];
        
        // Cek data kosong
        $this->db->query('SELECT COUNT(*) as total FROM siswa WHERE status_siswa = "aktif"');
        if ($this->db->single()['total'] == 0) {
            $health['issues'][] = 'Tidak ada siswa aktif';
            $health['status'] = 'warning';
        }
        
        $this->db->query('SELECT COUNT(*) as total FROM guru');
        if ($this->db->single()['total'] == 0) {
            $health['issues'][] = 'Tidak ada data guru';
            $health['status'] = 'error';
        }
        
        // Cek jurnal kosong hari ini
        $this->db->query('SELECT COUNT(*) as total FROM jurnal WHERE DATE(tanggal) = CURDATE()');
        if ($this->db->single()['total'] == 0) {
            $health['warnings'][] = 'Belum ada jurnal hari ini';
        }
        
        return $health;
    }
}