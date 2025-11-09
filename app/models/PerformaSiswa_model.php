<?php
// File: app/models/PerformaSiswa_model.php - UPDATED WITH DETAIL METHOD

class PerformaSiswa_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    public function getPerformaSiswa($startDate, $endDate, $id_semester, $kelasFilter = '')
    {
        $sql = "SELECT 
                    s.id_siswa,
                    s.nisn,
                    s.nama_siswa,
                    k.nama_kelas,
                    COUNT(*) as total_pertemuan,
                    SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as alfa,
                    ROUND((SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
                FROM absensi a
                JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                JOIN siswa s ON a.id_siswa = s.id_siswa
                JOIN kelas k ON p.id_kelas = k.id_kelas
                WHERE j.tanggal BETWEEN ? AND ?
                AND p.id_semester = ?
                AND s.status_siswa = 'aktif'";
        
        $params = [$startDate, $endDate, $id_semester];
        
        if (!empty($kelasFilter)) {
            $sql .= " AND k.id_kelas = ?";
            $params[] = $kelasFilter;
        }
        
        $sql .= " GROUP BY s.id_siswa, s.nama_siswa, k.nama_kelas
                  ORDER BY persentase_hadir DESC, s.nama_siswa ASC";
        
        $this->db->query($sql);
        
        // Bind parameters
        foreach ($params as $i => $param) {
            $this->db->bind($i + 1, $param);
        }
        
        return $this->db->resultSet();
    }

    public function getDetailPerformaSiswa($id_siswa, $startDate, $endDate, $id_semester)
    {
        $sql = "SELECT 
                    s.id_siswa,
                    s.nisn,
                    s.nama_siswa,
                    k.nama_kelas,
                    m.nama_mapel,
                    m.kode_mapel,
                    g.nama_guru,
                    COUNT(*) as total_pertemuan,
                    SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as alfa,
                    ROUND((SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as persentase_hadir
                FROM absensi a
                JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                JOIN siswa s ON a.id_siswa = s.id_siswa
                JOIN kelas k ON p.id_kelas = k.id_kelas
                JOIN mapel m ON p.id_mapel = m.id_mapel
                JOIN guru g ON p.id_guru = g.id_guru
                WHERE a.id_siswa = ?
                AND j.tanggal BETWEEN ? AND ?
                AND p.id_semester = ?
                AND s.status_siswa = 'aktif'
                GROUP BY s.id_siswa, s.nisn, s.nama_siswa, k.nama_kelas, m.id_mapel, m.nama_mapel, m.kode_mapel, g.id_guru, g.nama_guru
                ORDER BY m.nama_mapel ASC";
        
        $this->db->query($sql);
        $this->db->bind(1, $id_siswa);
        $this->db->bind(2, $startDate);
        $this->db->bind(3, $endDate);
        $this->db->bind(4, $id_semester);
        
        return $this->db->resultSet();
    }

    public function getSiswaInfo($id_siswa)
    {
        $sql = "SELECT 
                    s.id_siswa,
                    s.nisn,
                    s.nama_siswa,
                    s.jenis_kelamin,
                    k.nama_kelas
                FROM siswa s
                LEFT JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa
                LEFT JOIN kelas k ON kk.id_kelas = k.id_kelas
                WHERE s.id_siswa = ?
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(1, $id_siswa);
        
        return $this->db->single();
    }

    public function getKelasOptions($id_tp)
    {
        $this->db->query('SELECT id_kelas, nama_kelas, jenjang 
                         FROM kelas 
                         WHERE id_tp = ? 
                         ORDER BY jenjang ASC, nama_kelas ASC');
        $this->db->bind(1, $id_tp);
        return $this->db->resultSet();
    }
}