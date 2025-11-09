<?php
/**
 * File: app/models/Guru_model.php
 * Model untuk mengelola data guru
 */
class Guru_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    /**
     * Helper: normalisasi email -> trim & jadikan NULL jika kosong
     */
    private function normalizeEmail($email) {
        if (!isset($email)) return null;
        $email = trim($email);
        return ($email === '') ? null : $email;
    }

    /**
     * Menghitung jumlah total guru
     */
    public function getJumlahGuru() {
        $this->db->query('SELECT COUNT(*) AS total FROM guru');
        return $this->db->single()['total'];
    }

    /**
     * Mengambil semua data guru dengan info akun
     */
    public function getAllGuru() {
        $this->db->query('SELECT g.*, u.password_plain
                          FROM guru g
                          LEFT JOIN users u ON g.id_guru = u.id_ref AND u.role = "guru"
                          ORDER BY g.nama_guru ASC');
        return $this->db->resultSet();
    }

    /**
     * Mengambil data guru berdasarkan ID
     */
    public function getGuruById($id) {
        $this->db->query('SELECT * FROM guru WHERE id_guru = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    /**
     * Menambah data guru baru
     * - Email opsional: jika kosong -> NULL (tidak menabrak UNIQUE)
     */
    public function tambahDataGuru($data) {
        $email = $this->normalizeEmail($data['email'] ?? null);

        $this->db->query('INSERT INTO guru (nik, nama_guru, email)
                          VALUES (:nik, :nama, :email)');

        $this->db->bind('nik',  $data['nik']);
        $this->db->bind('nama', $data['nama_guru']);

        if ($email === null) {
            // pastikan bind sebagai NULL
            $this->db->bind('email', null, PDO::PARAM_NULL);
        } else {
            $this->db->bind('email', $email);
        }

        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Update data guru
     * - Email opsional: jika kosong -> NULL (tidak menabrak UNIQUE)
     */
    public function updateDataGuru($data) {
        $email = $this->normalizeEmail($data['email'] ?? null);

        $this->db->query('UPDATE guru
                          SET nik = :nik, nama_guru = :nama, email = :email
                          WHERE id_guru = :id');

        $this->db->bind('nik',  $data['nik']);
        $this->db->bind('nama', $data['nama_guru']);

        if ($email === null) {
            $this->db->bind('email', null, PDO::PARAM_NULL);
        } else {
            $this->db->bind('email', $email);
        }

        $this->db->bind('id',   $data['id_guru']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Update profil guru tanpa mengubah NIK
     */
    public function updateProfilGuru($id_guru, $data) {
        $email = $this->normalizeEmail($data['email'] ?? null);

        $this->db->query('UPDATE guru
                          SET nama_guru = :nama, email = :email
                          WHERE id_guru = :id');

        $this->db->bind('nama', $data['nama_guru']);
        if ($email === null) {
            $this->db->bind('email', null, PDO::PARAM_NULL);
        } else {
            $this->db->bind('email', $email);
        }
        $this->db->bind('id', $id_guru);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Hapus data guru
     */
    public function hapusDataGuru($id) {
        $this->db->query('DELETE FROM guru WHERE id_guru = :id');
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Cek apakah guru masih memiliki penugasan
     */
    public function cekKeterkaitanData($id_guru) {
        $this->db->query('SELECT COUNT(*) AS total FROM penugasan WHERE id_guru = :id');
        $this->db->bind('id', $id_guru);
        return $this->db->single()['total'];
    }

    /**
     * Mengambil guru yang sudah melakukan input jurnal hari ini
     */
    public function getGuruWithTodayJournal($id_semester) {
        $today = date('Y-m-d');
        $this->db->query('SELECT DISTINCT g.id_guru, g.nama_guru
                          FROM guru g
                          JOIN penugasan p ON g.id_guru = p.id_guru
                          JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                          WHERE p.id_semester = :id_semester
                            AND DATE(j.tanggal) = :today');
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('today', $today);
        return $this->db->resultSet();
    }

    /**
     * Generic getter kompatibel dengan berbagai nama method
     */
    public function getAll() {
        if (method_exists($this, 'getAllGuru')) return $this->getAllGuru();
        if (method_exists($this, 'getGuru'))    return $this->getGuru();
        if (method_exists($this, 'getAllData')) return $this->getAllData();
        if (method_exists($this, 'all'))        return $this->all();
        // fallback
        $this->db->query('SELECT id_guru, nama_guru FROM guru ORDER BY nama_guru ASC');
        return $this->db->resultSet();
    }
}
?>
