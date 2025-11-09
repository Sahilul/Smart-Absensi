<?php
// =================================================================
// Mapel_model.php - OPTIMIZED
// =================================================================

class Mapel_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    public function getJumlahMapel() {
        $this->db->query('SELECT COUNT(*) as total FROM mapel');
        return $this->db->single()['total'];
    }

    public function getAllMapel() {
        $this->db->query('SELECT * FROM mapel ORDER BY nama_mapel ASC');
        return $this->db->resultSet();
    }

    public function getMapelById($id) {
        $this->db->query('SELECT * FROM mapel WHERE id_mapel = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function tambahDataMapel($data) {
        $this->db->query('INSERT INTO mapel (kode_mapel, nama_mapel) VALUES (:kode, :nama)');
        $this->db->bind('kode', $data['kode_mapel']);
        $this->db->bind('nama', $data['nama_mapel']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function updateDataMapel($data) {
        $this->db->query('UPDATE mapel SET kode_mapel = :kode, nama_mapel = :nama WHERE id_mapel = :id');
        $this->db->bind('kode', $data['kode_mapel']);
        $this->db->bind('nama', $data['nama_mapel']);
        $this->db->bind('id', $data['id_mapel']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataMapel($id) {
        $this->db->query('DELETE FROM mapel WHERE id_mapel = :id');
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }
}



// =================================================================
// Penugasan_model.php - OPTIMIZED
// =================================================================


// =================================================================
// Jurnal_model.php - METHODS FOR ADMIN MONITORING
// =================================================================

class Jurnal_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    public function getAllJurnalForAdmin($id_semester) {
        $this->db->query('SELECT 
                            j.id_jurnal,
                            j.tanggal,
                            j.pertemuan_ke,
                            j.topik_materi,
                            g.nama_guru,
                            m.nama_mapel,
                            k.nama_kelas,
                            COUNT(a.id_absensi) as jumlah_absensi
                          FROM jurnal j
                          JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                          JOIN guru g ON p.id_guru = g.id_guru
                          JOIN mapel m ON p.id_mapel = m.id_mapel
                          JOIN kelas k ON p.id_kelas = k.id_kelas
                          LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
                          WHERE p.id_semester = :semester
                          GROUP BY j.id_jurnal
                          ORDER BY j.tanggal DESC, g.nama_guru, m.nama_mapel');
        $this->db->bind('semester', $id_semester);
        return $this->db->resultSet();
    }
}

// =================================================================
// Absensi_model.php - METHODS FOR ADMIN MONITORING
// =================================================================

class Absensi_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    public function getAllAbsensiForAdmin($id_semester) {
        $this->db->query('SELECT 
                            a.id_absensi,
                            a.status_kehadiran,
                            a.waktu_input,
                            s.nama_siswa,
                            s.nisn,
                            j.tanggal,
                            j.pertemuan_ke,
                            g.nama_guru,
                            m.nama_mapel,
                            k.nama_kelas
                          FROM absensi a
                          JOIN siswa s ON a.id_siswa = s.id_siswa
                          JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                          JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                          JOIN guru g ON p.id_guru = g.id_guru
                          JOIN mapel m ON p.id_mapel = m.id_mapel
                          JOIN kelas k ON p.id_kelas = k.id_kelas
                          WHERE p.id_semester = :semester
                          ORDER BY a.waktu_input DESC');
        $this->db->bind('semester', $id_semester);
        return $this->db->resultSet();
    }

    public function getStatistikAbsensiSemester($id_semester) {
        $this->db->query('SELECT 
                            COUNT(CASE WHEN a.status_kehadiran = "H" THEN 1 END) as total_hadir,
                            COUNT(CASE WHEN a.status_kehadiran = "I" THEN 1 END) as total_izin,
                            COUNT(CASE WHEN a.status_kehadiran = "S" THEN 1 END) as total_sakit,
                            COUNT(CASE WHEN a.status_kehadiran = "A" THEN 1 END) as total_alpha,
                            COUNT(*) as total_keseluruhan
                          FROM absensi a
                          JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                          JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                          WHERE p.id_semester = :semester');
        $this->db->bind('semester', $id_semester);
        return $this->db->single();
    }

    public function getAll() {
    if (method_exists($this, 'getAllMapel')) return $this->getAllMapel();
    if (method_exists($this, 'getMapel'))    return $this->getMapel();
    if (method_exists($this, 'getAllData'))  return $this->getAllData();
    if (method_exists($this, 'all'))         return $this->all();
    // fallback query langsung (sesuaikan)
    $this->db->query("SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel ASC");
    return $this->db->resultSet();
}

}