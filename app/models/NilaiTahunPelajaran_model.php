<?php
// File: app/models/NilaiTahunPelajaran_model.php
class NilaiTahunPelajaran_model {
    private $table = 'tp';
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllTahunPelajaran() {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY nama_tp DESC");
        return $this->db->resultSet();
    }

    public function getTahunPelajaranAktif() {
        // Asumsi TP aktif adalah yang paling baru
        $this->db->query("SELECT * FROM {$this->table} ORDER BY id_tp DESC LIMIT 1");
        return $this->db->single();
    }
}
?>