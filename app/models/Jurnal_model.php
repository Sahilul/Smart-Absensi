<?php
// File: app/models/Jurnal_model.php - VERSI BERSIH TANPA DUPLICATE

class Jurnal_model {
    private $db;
    private $table = 'jurnal';

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php'; 
        $this->db = new Database;
    }

    /**
     * Mengambil semua data jurnal milik seorang guru di semester aktif.
     */
    public function getJurnalByGuru($id_guru, $id_semester)
    {
        $query = "SELECT 
                    jurnal.*, 
                    penugasan.id_penugasan, 
                    mapel.nama_mapel, 
                    kelas.nama_kelas, 
                    mapel.id_mapel
                  FROM " . $this->table . "
                  JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                  JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                  JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                  WHERE penugasan.id_guru = :id_guru AND penugasan.id_semester = :id_semester
                  ORDER BY jurnal.tanggal DESC, jurnal.pertemuan_ke DESC";
        
        $this->db->query($query);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Mengambil statistik absensi per mata pelajaran untuk seorang guru
     */
    public function getStatistikAbsensiByGuru($id_guru, $id_semester)
    {
        $query = "SELECT 
                    mapel.id_mapel,
                    mapel.nama_mapel,
                    kelas.nama_kelas,
                    COUNT(DISTINCT jurnal.id_jurnal) as total_pertemuan,
                    COUNT(DISTINCT siswa.id_siswa) as total_siswa,
                    SUM(CASE WHEN absensi.status_kehadiran = 'H' THEN 1 ELSE 0 END) as total_hadir,
                    SUM(CASE WHEN absensi.status_kehadiran = 'I' THEN 1 ELSE 0 END) as total_izin,
                    SUM(CASE WHEN absensi.status_kehadiran = 'S' THEN 1 ELSE 0 END) as total_sakit,
                    SUM(CASE WHEN absensi.status_kehadiran = 'A' THEN 1 ELSE 0 END) as total_alpha,
                    COUNT(absensi.id_absensi) as total_absensi_records
                  FROM penugasan
                  JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                  JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                  LEFT JOIN jurnal ON penugasan.id_penugasan = jurnal.id_penugasan
                  LEFT JOIN absensi ON jurnal.id_jurnal = absensi.id_jurnal
                  LEFT JOIN siswa ON absensi.id_siswa = siswa.id_siswa
                  WHERE penugasan.id_guru = :id_guru 
                  AND penugasan.id_semester = :id_semester
                  GROUP BY mapel.id_mapel, mapel.nama_mapel, kelas.nama_kelas
                  HAVING total_pertemuan > 0
                  ORDER BY mapel.nama_mapel";
        
        $this->db->query($query);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Get detail jurnal by ID
     */
    public function getJurnalDetailById($id_jurnal)
    {
        $this->db->query('SELECT 
                            jurnal.*, 
                            mapel.nama_mapel, 
                            kelas.nama_kelas, 
                            penugasan.id_penugasan,
                            mapel.id_mapel
                          FROM ' . $this->table . '
                          JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                          JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                          JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                          WHERE jurnal.id_jurnal = :id_jurnal');
        $this->db->bind('id_jurnal', $id_jurnal);
        return $this->db->single();
    }

    /**
     * Get jurnal by ID (simple)
     */
    public function getJurnalById($id_jurnal)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id_jurnal = :id_jurnal');
        $this->db->bind('id_jurnal', $id_jurnal);
        return $this->db->single();
    }

    /**
     * Get pertemuan terakhir by penugasan
     */
    public function getPertemuanTerakhir($id_penugasan)
    {
        $this->db->query('SELECT MAX(pertemuan_ke) as pertemuan_max FROM ' . $this->table . ' WHERE id_penugasan = :id_penugasan');
        $this->db->bind('id_penugasan', $id_penugasan);
        $result = $this->db->single();
        return $result['pertemuan_max'] ?? 0;
    }

    /**
     * Tambah data jurnal baru
     */
    public function tambahDataJurnal($data)
    {
        $query = "INSERT INTO " . $this->table . " (id_penugasan, pertemuan_ke, tanggal, topik_materi, catatan) 
                  VALUES (:id_penugasan, :pertemuan_ke, :tanggal, :topik_materi, :catatan)";
        
        $this->db->query($query);
        $this->db->bind('id_penugasan', $data['id_penugasan']);
        $this->db->bind('pertemuan_ke', $data['pertemuan_ke']);
        $this->db->bind('tanggal', $data['tanggal']);
        $this->db->bind('topik_materi', $data['topik_materi']);
        $this->db->bind('catatan', $data['catatan']);
        
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Update data jurnal
     */
    public function updateDataJurnal($data)
    {
        $query = "UPDATE " . $this->table . " SET tanggal = :tanggal, topik_materi = :topik_materi, catatan = :catatan 
                  WHERE id_jurnal = :id_jurnal";
        
        $this->db->query($query);
        $this->db->bind('tanggal', $data['tanggal']);
        $this->db->bind('topik_materi', $data['topik_materi']);
        $this->db->bind('catatan', $data['catatan']);
        $this->db->bind('id_jurnal', $data['id_jurnal']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Hapus data jurnal
     */
    public function hapusDataJurnal($id_jurnal)
    {
        // Hapus absensi yang terkait terlebih dahulu (foreign key constraint)
        $this->db->query('DELETE FROM absensi WHERE id_jurnal = :id_jurnal');
        $this->db->bind('id_jurnal', $id_jurnal);
        $this->db->execute();

        // Kemudian hapus jurnal
        $query = "DELETE FROM " . $this->table . " WHERE id_jurnal = :id_jurnal";
        
        $this->db->query($query);
        $this->db->bind('id_jurnal', $id_jurnal);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Get detail riwayat jurnal by mapel - SESUAI DATABASE (SINGLE VERSION)
     */
    public function getDetailRiwayatByMapel($id_guru, $id_semester, $id_mapel)
    {
        $sql = "
            SELECT 
                j.id_jurnal,
                j.tanggal,
                j.pertemuan_ke,
                j.topik_materi,
                j.catatan,
                j.timestamp,
                m.nama_mapel,
                g.nama_guru,
                k.nama_kelas,
                COUNT(CASE WHEN a.status_kehadiran = 'H' THEN 1 END) as total_hadir
            FROM jurnal j
            JOIN penugasan p ON j.id_penugasan = p.id_penugasan
            JOIN mapel m ON p.id_mapel = m.id_mapel
            LEFT JOIN guru g ON p.id_guru = g.id_guru
            LEFT JOIN kelas k ON p.id_kelas = k.id_kelas
            LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
            WHERE p.id_guru = :id_guru 
              AND p.id_semester = :id_semester
              AND p.id_mapel = :id_mapel
            GROUP BY j.id_jurnal, j.tanggal, j.pertemuan_ke, j.topik_materi, j.catatan, j.timestamp, m.nama_mapel, g.nama_guru, k.nama_kelas
            ORDER BY j.tanggal DESC, j.pertemuan_ke DESC
        ";
        
        $this->db->query($sql);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('id_mapel', $id_mapel);
        
        $results = $this->db->resultSet();
        
        // MAP KOLOM DATABASE KE FORMAT YANG DIHARAPKAN VIEW
        $mapped_results = [];
        foreach ($results as $row) {
            $mapped_results[] = [
                'id_jurnal' => $row['id_jurnal'],
                'tanggal' => $row['tanggal'],
                'pertemuan_ke' => $row['pertemuan_ke'],
                'materi' => $row['topik_materi'] ?? 'Topik tidak tercatat', // MAP topik_materi -> materi
                'jam_mulai' => isset($row['timestamp']) ? date('H:i', strtotime($row['timestamp'])) : '07:00',
                'jam_selesai' => isset($row['timestamp']) ? date('H:i', strtotime($row['timestamp'] . ' +1 hour')) : '08:00',
                'catatan' => $row['catatan'],
                'timestamp' => $row['timestamp'],
                'total_hadir' => $row['total_hadir'] ?? 0,
                'nama_mapel' => $row['nama_mapel'],
                'nama_guru' => $row['nama_guru'],
                'nama_kelas' => $row['nama_kelas']
            ];
        }
        
        return $mapped_results;
    }

    /**
     * Get detail absensi per mapel - SESUAI DATABASE (SINGLE VERSION)
     */
    public function getDetailAbsensiPerMapel($id_guru, $id_semester, $id_mapel)
    {
        $sql = "
            SELECT 
                s.id_siswa,
                s.nama_siswa,
                SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as total_alpha,
                COUNT(a.id_absensi) as total_pertemuan_diikuti
            FROM penugasan p
            JOIN jurnal j ON p.id_penugasan = j.id_penugasan
            JOIN absensi a ON j.id_jurnal = a.id_jurnal
            JOIN siswa s ON a.id_siswa = s.id_siswa
            WHERE p.id_guru = :id_guru 
              AND p.id_semester = :id_semester
              AND p.id_mapel = :id_mapel
            GROUP BY s.id_siswa, s.nama_siswa
            ORDER BY s.nama_siswa
        ";
        
        $this->db->query($sql);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('id_mapel', $id_mapel);
        
        return $this->db->resultSet();
    }

    /**
     * Get all jurnal for admin monitoring - SESUAI DATABASE (SINGLE VERSION)
     */
    public function getAllJurnalForAdmin($id_semester)
    {
        $sql = "
            SELECT 
                j.id_jurnal,
                j.tanggal,
                j.pertemuan_ke,
                j.topik_materi,
                j.catatan,
                j.timestamp,
                g.nama_guru,
                m.nama_mapel,
                k.nama_kelas
            FROM jurnal j
            JOIN penugasan p ON j.id_penugasan = p.id_penugasan
            JOIN guru g ON p.id_guru = g.id_guru
            JOIN mapel m ON p.id_mapel = m.id_mapel
            JOIN kelas k ON p.id_kelas = k.id_kelas
            WHERE p.id_semester = :id_semester
            ORDER BY j.tanggal DESC, j.timestamp DESC
        ";
        
        $this->db->query($sql);
        $this->db->bind('id_semester', $id_semester);
        
        $results = $this->db->resultSet();
        
        // MAP KOLOM DATABASE KE FORMAT YANG DIHARAPKAN
        $mapped_results = [];
        foreach ($results as $row) {
            $mapped_results[] = [
                'id_jurnal' => $row['id_jurnal'],
                'tanggal' => $row['tanggal'],
                'pertemuan_ke' => $row['pertemuan_ke'],
                'materi' => $row['topik_materi'] ?? 'Topik tidak tercatat', // MAP topik_materi -> materi
                'catatan' => $row['catatan'],
                'timestamp' => $row['timestamp'],
                'nama_guru' => $row['nama_guru'],
                'nama_mapel' => $row['nama_mapel'],
                'nama_kelas' => $row['nama_kelas']
            ];
        }
        
        return $mapped_results;
    }

    /**
     * Mendapatkan jurnal hari ini untuk kelas tertentu
     * Digunakan untuk dashboard wali kelas
     * @param int $id_kelas ID Kelas
     * @return array Daftar jurnal hari ini
     */
    public function getJurnalHariIniByKelas($id_kelas)
    {
        $tanggal_hari_ini = date('Y-m-d');
        
        $query = "SELECT 
                    jurnal.*,
                    mapel.nama_mapel,
                    guru.nama_guru,
                    kelas.nama_kelas,
                    penugasan.id_penugasan
                  FROM " . $this->table . "
                  JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                  JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                  JOIN guru ON penugasan.id_guru = guru.id_guru
                  JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                  WHERE penugasan.id_kelas = :id_kelas
                  AND DATE(jurnal.tanggal) = :tanggal
                  ORDER BY jurnal.pertemuan_ke ASC";
        
        $this->db->query($query);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('tanggal', $tanggal_hari_ini);
        
        return $this->db->resultSet();
    }

    /**
     * Ambil daftar jurnal berdasarkan penugasan
     */
    public function getJurnalByPenugasan($id_penugasan)
    {
        $query = "SELECT 
                    jurnal.*,
                    mapel.nama_mapel,
                    guru.nama_guru,
                    kelas.nama_kelas
                  FROM " . $this->table . "
                  JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                  JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                  JOIN guru ON penugasan.id_guru = guru.id_guru
                  JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                  WHERE jurnal.id_penugasan = :id_penugasan
                  ORDER BY jurnal.tanggal DESC, jurnal.pertemuan_ke DESC";
        
        $this->db->query($query);
        $this->db->bind('id_penugasan', $id_penugasan);
        
        return $this->db->resultSet();
    }
}
?>