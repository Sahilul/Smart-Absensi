<?php
// File: app/models/NilaiSiswa_model.php
class NilaiSiswa_model {
    private $table = 'siswa';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getSiswaByKelas($id_kelas) {
        $this->db->query("SELECT s.* FROM {$this->table} s 
                         JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa 
                         WHERE kk.id_kelas = :id_kelas 
                         AND s.status_siswa = 'aktif' 
                         ORDER BY s.nama_siswa");
        $this->db->bind('id_kelas', $id_kelas);
        return $this->db->resultSet();
    }

    public function getSiswaById($id_siswa) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id_siswa = :id_siswa");
        $this->db->bind('id_siswa', $id_siswa);
        return $this->db->single();
    }
}
?>