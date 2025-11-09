<?php

// File: app/models/Penugasan_model.php
class Penugasan_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    /**
     * Mengambil semua data penugasan untuk semester yang aktif.
     * @param int $id_semester ID semester yang sedang aktif.
     * @return array Daftar penugasan.
     */
    public function getAllPenugasanBySemester($id_semester)
    {
        $query = "SELECT 
                    penugasan.id_penugasan, 
                    guru.nama_guru, 
                    mapel.nama_mapel, 
                    kelas.nama_kelas
                  FROM penugasan
                  JOIN guru ON penugasan.id_guru = guru.id_guru
                  JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                  JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                  WHERE penugasan.id_semester = :id_semester
                  ORDER BY guru.nama_guru, kelas.nama_kelas";
        
        $this->db->query($query);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Menambahkan data penugasan baru.
     * @param array $data Data dari form (id_guru, id_mapel, id_kelas, id_semester).
     * @return int Jumlah baris yang terpengaruh (1 jika berhasil).
     */
    public function tambahDataPenugasan($data)
    {
        $query = "INSERT INTO penugasan (id_guru, id_mapel, id_kelas, id_semester) 
                  VALUES (:id_guru, :id_mapel, :id_kelas, :id_semester)";

        $this->db->query($query);
        $this->db->bind('id_guru', $data['id_guru']);
        $this->db->bind('id_mapel', $data['id_mapel']);
        $this->db->bind('id_kelas', $data['id_kelas']);
        $this->db->bind('id_semester', $data['id_semester']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Menghapus data penugasan berdasarkan ID.
     * @param int $id ID penugasan.
     * @return int Jumlah baris yang terpengaruh.
     */
    public function hapusDataPenugasan($id)
    {
        $query = "DELETE FROM penugasan WHERE id_penugasan = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Mengambil semua penugasan seorang guru di semester tertentu.
     * @param int $id_guru ID guru.
     * @param int $id_semester ID semester.
     * @return array Daftar penugasan guru.
     */
    public function getPenugasanByGuru($id_guru, $id_semester)
    {
        $query = "SELECT 
                    penugasan.id_penugasan,
                    penugasan.id_mapel,
                    penugasan.id_kelas,
                    mapel.nama_mapel, 
                    kelas.nama_kelas
                  FROM penugasan
                  JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                  JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                  WHERE penugasan.id_guru = :id_guru AND penugasan.id_semester = :id_semester
                  ORDER BY kelas.nama_kelas, mapel.nama_mapel";
        
        $this->db->query($query);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Mengambil detail penugasan berdasarkan ID.
     * @param int $id ID penugasan.
     * @return array Data penugasan lengkap dengan nama guru, mapel, dan kelas.
     */
    public function getPenugasanById($id)
    {
        $this->db->query('SELECT p.*, g.nama_guru, m.nama_mapel, k.nama_kelas 
                         FROM penugasan p
                         JOIN guru g ON p.id_guru = g.id_guru
                         JOIN mapel m ON p.id_mapel = m.id_mapel  
                         JOIN kelas k ON p.id_kelas = k.id_kelas
                         WHERE p.id_penugasan = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    /**
     * Memperbarui data penugasan.
     * @param array $data Data yang diperbarui (termasuk id_penugasan).
     * @return int Jumlah baris yang terpengaruh.
     */
    public function updateDataPenugasan($data)
    {
        $query = "UPDATE penugasan SET 
                  id_guru = :id_guru,
                  id_mapel = :id_mapel,
                  id_kelas = :id_kelas,
                  id_semester = :id_semester
                  WHERE id_penugasan = :id_penugasan";
        
        $this->db->query($query);
        $this->db->bind('id_guru', $data['id_guru']);
        $this->db->bind('id_mapel', $data['id_mapel']);
        $this->db->bind('id_kelas', $data['id_kelas']);
        $this->db->bind('id_semester', $data['id_semester']);
        $this->db->bind('id_penugasan', $data['id_penugasan']);
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Mengecek apakah kombinasi penugasan (guru, mapel, kelas, semester) sudah ada — digunakan saat TAMBAH.
     * @param int $id_guru
     * @param int $id_mapel
     * @param int $id_kelas
     * @param int $id_semester
     * @return bool true jika sudah ada (duplikat), false jika belum.
     */
    public function cekDuplikasiPenugasan($id_guru, $id_mapel, $id_kelas, $id_semester)
    {
        $this->db->query('SELECT COUNT(*) as total FROM penugasan 
                         WHERE id_guru = :id_guru 
                         AND id_mapel = :id_mapel 
                         AND id_kelas = :id_kelas 
                         AND id_semester = :id_semester');
        
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_mapel', $id_mapel);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_semester', $id_semester);
        
        $result = $this->db->single();
        return $result['total'] > 0;
    }

    /**
     * Mengecek duplikasi penugasan saat EDIT (mengabaikan ID yang sedang diedit).
     * @param int $id_guru
     * @param int $id_mapel
     * @param int $id_kelas
     * @param int $id_semester
     * @param int $id_penugasan ID yang sedang diedit (dikecualikan dari pengecekan).
     * @return bool true jika duplikat ditemukan, false jika aman.
     */
    public function cekDuplikasiPenugasanEdit($id_guru, $id_mapel, $id_kelas, $id_semester, $id_penugasan)
    {
        $this->db->query('SELECT COUNT(*) as total FROM penugasan 
                         WHERE id_guru = :id_guru 
                         AND id_mapel = :id_mapel 
                         AND id_kelas = :id_kelas 
                         AND id_semester = :id_semester
                         AND id_penugasan != :id_penugasan');
        
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_mapel', $id_mapel);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('id_penugasan', $id_penugasan);
        
        $result = $this->db->single();
        return $result['total'] > 0;
    }

    /**
     * Ambil daftar mapel untuk kelas tertentu dalam semester
     */
    public function getMapelByKelas($id_kelas, $id_semester) {
        $query = "SELECT 
                    p.id_penugasan,
                    p.id_guru,
                    p.id_mapel,
                    m.nama_mapel,
                    g.nama_guru
                  FROM penugasan p
                  JOIN mapel m ON p.id_mapel = m.id_mapel
                  JOIN guru g ON p.id_guru = g.id_guru
                  WHERE p.id_kelas = :id_kelas 
                    AND p.id_semester = :id_semester
                  ORDER BY m.nama_mapel";
        
        $this->db->query($query);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }
}