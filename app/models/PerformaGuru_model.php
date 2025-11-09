<?php
// File: app/models/PerformaGuru_model.php - UPDATED MODEL WITH CLASS FILTER

class PerformaGuru_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    public function getPerformaGuru($startDate, $endDate, $id_semester, $guruFilter = '', $mapelFilter = '', $kelasFilter = '', $jenjangFilter = '')
    {
        $sql = "SELECT 
                    g.id_guru,
                    g.nama_guru,
                    g.nik,
                    COUNT(DISTINCT p.id_penugasan) as total_penugasan,
                    COUNT(DISTINCT p.id_mapel) as total_mapel,
                    COUNT(DISTINCT p.id_kelas) as total_kelas,
                    COUNT(j.id_jurnal) as total_jurnal,
                    COUNT(DISTINCT j.tanggal) as total_hari_mengajar,
                    ROUND(COUNT(j.id_jurnal) / COUNT(DISTINCT p.id_penugasan), 1) as rata_jurnal_per_penugasan,
                    MAX(j.tanggal) as jurnal_terakhir
                FROM guru g
                LEFT JOIN penugasan p ON g.id_guru = p.id_guru
                LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
                LEFT JOIN jurnal j ON p.id_penugasan = j.id_penugasan 
                    AND j.tanggal BETWEEN ? AND ?
                WHERE p.id_semester = ?";
        
        $params = [$startDate, $endDate, $id_semester];
        
        if (!empty($guruFilter)) {
            $sql .= " AND g.id_guru = ?";
            $params[] = $guruFilter;
        }
        
        if (!empty($mapelFilter)) {
            $sql .= " AND p.id_mapel = ?";
            $params[] = $mapelFilter;
        }
        
        if (!empty($kelasFilter)) {
            $sql .= " AND p.id_kelas = ?";
            $params[] = $kelasFilter;
        }
        
        // Filter berdasarkan jenjang
        if (!empty($jenjangFilter)) {
            if ($jenjangFilter === 'mts') {
                $sql .= " AND k.jenjang IN ('7', '8', '9')";
            } elseif ($jenjangFilter === 'ma') {
                $sql .= " AND k.jenjang IN ('10', '11', '12')";
            } else {
                $sql .= " AND k.jenjang = ?";
                $params[] = $jenjangFilter;
            }
        }
        
        $sql .= " GROUP BY g.id_guru, g.nama_guru, g.nik
                  ORDER BY total_jurnal DESC, g.nama_guru ASC";
        
        $this->db->query($sql);
        
        foreach ($params as $i => $param) {
            $this->db->bind($i + 1, $param);
        }
        
        return $this->db->resultSet();
    }

    public function getDetailPerformaGuru($id_guru, $startDate, $endDate, $id_semester, $kelasFilter = '', $jenjangFilter = '')
    {
        $sql = "SELECT 
                    g.nama_guru,
                    m.nama_mapel,
                    m.kode_mapel,
                    k.nama_kelas,
                    k.jenjang,
                    p.id_penugasan,
                    COUNT(j.id_jurnal) as total_jurnal,
                    COUNT(DISTINCT j.tanggal) as total_hari_mengajar,
                    MIN(j.tanggal) as jurnal_pertama,
                    MAX(j.tanggal) as jurnal_terakhir,
                    AVG(CHAR_LENGTH(j.topik_materi)) as rata_panjang_materi
                FROM guru g
                JOIN penugasan p ON g.id_guru = p.id_guru
                JOIN mapel m ON p.id_mapel = m.id_mapel
                JOIN kelas k ON p.id_kelas = k.id_kelas
                LEFT JOIN jurnal j ON p.id_penugasan = j.id_penugasan 
                    AND j.tanggal BETWEEN ? AND ?
                WHERE g.id_guru = ? AND p.id_semester = ?";
        
        $params = [$startDate, $endDate, $id_guru, $id_semester];
        
        if (!empty($kelasFilter)) {
            $sql .= " AND p.id_kelas = ?";
            $params[] = $kelasFilter;
        }
        
        // Filter berdasarkan jenjang
        if (!empty($jenjangFilter)) {
            if ($jenjangFilter === 'mts') {
                $sql .= " AND k.jenjang IN ('7', '8', '9')";
            } elseif ($jenjangFilter === 'ma') {
                $sql .= " AND k.jenjang IN ('10', '11', '12')";
            } else {
                $sql .= " AND k.jenjang = ?";
                $params[] = $jenjangFilter;
            }
        }
        
        $sql .= " GROUP BY p.id_penugasan, g.nama_guru, m.nama_mapel, m.kode_mapel, k.nama_kelas, k.jenjang
                  ORDER BY k.jenjang ASC, m.nama_mapel ASC, k.nama_kelas ASC";
        
        $this->db->query($sql);
        
        foreach ($params as $i => $param) {
            $this->db->bind($i + 1, $param);
        }
        
        return $this->db->resultSet();
    }

    public function getJurnalDetail($id_penugasan, $startDate, $endDate)
    {
        $sql = "SELECT 
                    j.id_jurnal,
                    j.pertemuan_ke,
                    j.tanggal,
                    j.topik_materi,
                    j.catatan,
                    j.timestamp,
                    m.nama_mapel,
                    k.nama_kelas,
                    k.jenjang,
                    g.nama_guru,
                    COUNT(a.id_absensi) as total_siswa,
                    SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as alfa,
                    CASE 
                        WHEN COUNT(a.id_absensi) > 0 THEN 
                            ROUND((SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) / COUNT(a.id_absensi)) * 100, 1)
                        ELSE 0 
                    END as persentase_hadir
                FROM jurnal j
                JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                JOIN mapel m ON p.id_mapel = m.id_mapel
                JOIN kelas k ON p.id_kelas = k.id_kelas
                JOIN guru g ON p.id_guru = g.id_guru
                LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
                WHERE j.id_penugasan = ?
                AND j.tanggal BETWEEN ? AND ?
                GROUP BY j.id_jurnal, j.pertemuan_ke, j.tanggal, j.topik_materi, j.catatan, j.timestamp, m.nama_mapel, k.nama_kelas, k.jenjang, g.nama_guru
                ORDER BY j.tanggal DESC, j.pertemuan_ke DESC";
        
        $this->db->query($sql);
        $this->db->bind(1, $id_penugasan);
        $this->db->bind(2, $startDate);
        $this->db->bind(3, $endDate);
        
        return $this->db->resultSet();
    }

    public function getGuruInfo($id_guru)
    {
        $sql = "SELECT 
                    g.id_guru,
                    g.nama_guru,
                    g.nik,
                    g.email,
                    COUNT(DISTINCT p.id_mapel) as total_mapel_diampu,
                    COUNT(DISTINCT p.id_kelas) as total_kelas_diampu
                FROM guru g
                LEFT JOIN penugasan p ON g.id_guru = p.id_guru
                WHERE g.id_guru = ?
                GROUP BY g.id_guru, g.nama_guru, g.nik, g.email";
        
        $this->db->query($sql);
        $this->db->bind(1, $id_guru);
        
        return $this->db->single();
    }

    public function getGuruOptions()
    {
        $this->db->query('SELECT id_guru, nama_guru, nik 
                         FROM guru 
                         ORDER BY nama_guru ASC');
        return $this->db->resultSet();
    }

    public function getMapelOptions()
    {
        $this->db->query('SELECT id_mapel, nama_mapel, kode_mapel 
                         FROM mapel 
                         ORDER BY nama_mapel ASC');
        return $this->db->resultSet();
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

    public function getJenjangOptions()
    {
        $this->db->query('SELECT DISTINCT jenjang 
                         FROM kelas 
                         ORDER BY jenjang ASC');
        return $this->db->resultSet();
    }
}