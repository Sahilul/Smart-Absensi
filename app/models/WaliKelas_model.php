<?php

class WaliKelas_model {
    private $table = 'wali_kelas';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * Ambil semua data wali kelas
     */
    public function getAllWaliKelas()
    {
        $this->db->query('SELECT wali_kelas.*, guru.nama_guru, kelas.nama_kelas, tp.nama_tp 
                         FROM ' . $this->table . ' 
                         JOIN guru ON guru.id_guru = wali_kelas.id_guru 
                         JOIN kelas ON kelas.id_kelas = wali_kelas.id_kelas 
                         JOIN tp ON tp.id_tp = wali_kelas.id_tp
                         ORDER BY tp.nama_tp DESC, kelas.nama_kelas ASC');
        return $this->db->resultSet();
    }

    /**
     * Ambil data wali kelas berdasarkan guru dan tahun pelajaran
     */
    public function getWaliKelasByGuru($id_guru, $id_tp)
    {
        $this->db->query('SELECT wali_kelas.*, guru.nama_guru, kelas.nama_kelas, kelas.id_kelas, tp.nama_tp 
                         FROM ' . $this->table . ' 
                         JOIN guru ON guru.id_guru = wali_kelas.id_guru 
                         JOIN kelas ON kelas.id_kelas = wali_kelas.id_kelas 
                         JOIN tp ON tp.id_tp = wali_kelas.id_tp
                         WHERE wali_kelas.id_guru = :id_guru AND wali_kelas.id_tp = :id_tp');
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_tp', $id_tp);
        return $this->db->single();
    }

    /**
     * Tambah data wali kelas
     */
    public function tambahDataWaliKelas($data)
    {
        $query = "INSERT INTO wali_kelas (id_guru, id_kelas, id_tp) 
                  VALUES (:id_guru, :id_kelas, :id_tp)";
        
        $this->db->query($query);
        $this->db->bind('id_guru', $data['id_guru']);
        $this->db->bind('id_kelas', $data['id_kelas']);
        $this->db->bind('id_tp', $data['id_tp']);

        $this->db->execute();

        return $this->db->rowCount();
    }

    /**
     * Hapus data wali kelas
     */
    public function hapusDataWaliKelas($id)
    {
        $query = "DELETE FROM wali_kelas WHERE id_walikelas = :id";
        
        $this->db->query($query);
        $this->db->bind('id', $id);

        $this->db->execute();

        return $this->db->rowCount();
    }

    /**
     * Cek apakah guru sudah menjadi wali kelas di tahun pelajaran tertentu
     */
    public function cekWaliKelasExists($id_guru, $id_tp)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' 
                         WHERE id_guru = :id_guru AND id_tp = :id_tp');
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_tp', $id_tp);
        $result = $this->db->single();
        return $result !== false;
    }

    /**
     * Cek apakah kelas sudah memiliki wali kelas
     */
    public function cekKelasHasWaliKelas($id_kelas, $id_tp)
    {
        $this->db->query('SELECT * FROM ' . $this->table . ' 
                         WHERE id_kelas = :id_kelas AND id_tp = :id_tp');
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);
        $result = $this->db->single();
        return $result !== false;
    }

    /**
     * Ambil ID kelas yang diampu wali kelas
     */
    public function getKelasIdByWaliKelas($id_guru, $id_tp)
    {
        $this->db->query('SELECT id_kelas FROM ' . $this->table . ' 
                         WHERE id_guru = :id_guru AND id_tp = :id_tp');
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_tp', $id_tp);
        $result = $this->db->single();
        return $result ? $result['id_kelas'] : null;
    }

    /**
     * UPDATE wali kelas - Ganti guru untuk kelas tertentu
     * AMAN: Tidak menghapus data rapor dan pengaturan yang sudah ada
     */
    public function updateWaliKelas($id_kelas, $id_tp, $id_guru_baru)
    {
        // Update id_guru di tabel wali_kelas
        $query = "UPDATE wali_kelas 
                  SET id_guru = :id_guru_baru 
                  WHERE id_kelas = :id_kelas AND id_tp = :id_tp";
        
        $this->db->query($query);
        $this->db->bind('id_guru_baru', $id_guru_baru);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);

        $this->db->execute();

        return $this->db->rowCount();
    }

    /**
     * Ambil data wali kelas berdasarkan kelas
     */
    public function getWaliKelasByKelas($id_kelas, $id_tp)
    {
        $this->db->query('SELECT wali_kelas.*, guru.nama_guru, guru.nik 
                         FROM ' . $this->table . ' 
                         JOIN guru ON guru.id_guru = wali_kelas.id_guru 
                         WHERE wali_kelas.id_kelas = :id_kelas AND wali_kelas.id_tp = :id_tp');
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);
        return $this->db->single();
    }
}