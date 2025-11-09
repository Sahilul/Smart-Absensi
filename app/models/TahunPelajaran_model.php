<?php

// File: app/models/TahunPelajaran_model.php
class TahunPelajaran_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    public function getSemesterAktif()
    {
        $this->db->query('SELECT semester.*, tp.nama_tp 
                         FROM semester 
                         JOIN tp ON semester.id_tp = tp.id_tp 
                         WHERE semester.is_active = :status');
        $this->db->bind(':status', 'ya');
        return $this->db->single();
    }

    public function getAllSemester()
    {
        $this->db->query('SELECT semester.*, tp.nama_tp 
                         FROM semester 
                         JOIN tp ON semester.id_tp = tp.id_tp 
                         ORDER BY tp.tgl_mulai DESC, semester.semester DESC');
        return $this->db->resultSet();
    }

    public function getAllTahunPelajaran()
    {
        $this->db->query('SELECT * FROM tp ORDER BY tgl_mulai DESC');
        return $this->db->resultSet();
    }

    public function getTahunPelajaranById($id)
    {
        $this->db->query('SELECT * FROM tp WHERE id_tp = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function tambahDataTahunPelajaran($data)
    {
        $query = "INSERT INTO tp (nama_tp, tgl_mulai, tgl_selesai) VALUES (:nama_tp, :tgl_mulai, :tgl_selesai)";
        $this->db->query($query);
        $this->db->bind('nama_tp', $data['nama_tp']);
        $this->db->bind('tgl_mulai', $data['tgl_mulai']);
        $this->db->bind('tgl_selesai', $data['tgl_selesai']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function updateDataTahunPelajaran($data)
    {
        $query = "UPDATE tp SET nama_tp = :nama_tp, tgl_mulai = :tgl_mulai, tgl_selesai = :tgl_selesai WHERE id_tp = :id_tp";
        $this->db->query($query);
        $this->db->bind('nama_tp', $data['nama_tp']);
        $this->db->bind('tgl_mulai', $data['tgl_mulai']);
        $this->db->bind('tgl_selesai', $data['tgl_selesai']);
        $this->db->bind('id_tp', $data['id_tp']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataTahunPelajaran($id)
    {
        $query = "DELETE FROM tp WHERE id_tp = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // FUNGSI BARU: Menambahkan TP baru beserta 2 semesternya
    public function tambahDataTahunPelajaranDanSemester($data)
    {
        // 1. Masukkan data ke tabel 'tp'
        $queryTP = "INSERT INTO tp (nama_tp, tgl_mulai, tgl_selesai) VALUES (:nama_tp, :tgl_mulai, :tgl_selesai)";
        $this->db->query($queryTP);
        $this->db->bind('nama_tp', $data['nama_tp']);
        $this->db->bind('tgl_mulai', $data['tgl_mulai']);
        $this->db->bind('tgl_selesai', $data['tgl_selesai']);
        $this->db->execute();
        
        // Ambil ID dari TP yang baru saja dimasukkan
        $id_tp_baru = $this->db->lastInsertId();

        // 2. Masukkan data semester Ganjil ke tabel 'semester'
        $queryGanjil = "INSERT INTO semester (id_tp, semester) VALUES (:id_tp, 'Ganjil')";
        $this->db->query($queryGanjil);
        $this->db->bind('id_tp', $id_tp_baru);
        $this->db->execute();

        // 3. Masukkan data semester Genap ke tabel 'semester'
        $queryGenap = "INSERT INTO semester (id_tp, semester) VALUES (:id_tp, 'Genap')";
        $this->db->query($queryGenap);
        $this->db->bind('id_tp', $id_tp_baru);
        $this->db->execute();

        return $this->db->rowCount(); // Mengembalikan hasil dari query terakhir
    }
}