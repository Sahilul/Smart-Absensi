<?php
// File: app/models/NilaiKelas_model.php
class NilaiKelas_model {
    private $table = 'kelas';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllKelas() {
        $this->db->query("SELECT k.*, tp.nama_tp 
                         FROM {$this->table} k 
                         JOIN tp tp ON k.id_tp = tp.id_tp 
                         ORDER BY tp.nama_tp DESC, k.nama_kelas");
        return $this->db->resultSet();
    }

    public function getKelasByTP($id_tp) {
        $this->db->query("SELECT * FROM {$this->table} 
                         WHERE id_tp = :id_tp 
                         ORDER BY nama_kelas");
        $this->db->bind('id_tp', $id_tp);
        return $this->db->resultSet();
    }

    public function getKelasById($id_kelas) {
        $this->db->query("SELECT k.*, tp.nama_tp 
                         FROM {$this->table} k 
                         JOIN tp tp ON k.id_tp = tp.id_tp 
                         WHERE k.id_kelas = :id_kelas");
        $this->db->bind('id_kelas', $id_kelas);
        return $this->db->single();
    }
}
?>