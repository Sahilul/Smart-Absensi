<?php
// File: app/models/NilaiGuru_model.php
class NilaiGuru_model {
    private $table = 'guru';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllGuru() {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY nama_guru");
        return $this->db->resultSet();
    }

    public function getGuruById($id_guru) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id_guru = :id_guru");
        $this->db->bind('id_guru', $id_guru);
        return $this->db->single();
    }
}
?>