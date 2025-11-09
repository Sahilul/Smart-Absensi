<?php
// File: app/models/NilaiSemester_model.php
class NilaiSemester_model {
    private $table = 'semester';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllSemester() {
        $this->db->query("SELECT s.*, tp.nama_tp 
                         FROM {$this->table} s 
                         JOIN tp tp ON s.id_tp = tp.id_tp 
                         ORDER BY tp.nama_tp DESC, s.semester");
        return $this->db->resultSet();
    }

    public function getSemesterAktif() {
        $this->db->query("SELECT s.*, tp.nama_tp 
                         FROM {$this->table} s 
                         JOIN tp tp ON s.id_tp = tp.id_tp 
                         WHERE s.is_active = 'ya' 
                         LIMIT 1");
        return $this->db->single();
    }

    public function getSemesterByTP($id_tp) {
        $this->db->query("SELECT * FROM {$this->table} 
                         WHERE id_tp = :id_tp 
                         ORDER BY semester");
        $this->db->bind('id_tp', $id_tp);
        return $this->db->resultSet();
    }
}
?>