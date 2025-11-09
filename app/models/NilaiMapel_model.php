<?php
// File: app/models/NilaiMapel_model.php
class NilaiMapel_model {
    private $table = 'mapel';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllMapel() {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY nama_mapel");
        return $this->db->resultSet();
    }

    public function getMapelById($id_mapel) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id_mapel = :id_mapel");
        $this->db->bind('id_mapel', $id_mapel);
        return $this->db->single();
    }
}
?>