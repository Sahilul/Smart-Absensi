<?php

// File: app/models/Keanggotaan_model.php
class Keanggotaan_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    public function getSiswaByKelas($id_kelas, $id_tp)
    {
        $this->db->query('SELECT keanggotaan_kelas.*, siswa.nisn, siswa.nama_siswa 
                         FROM keanggotaan_kelas 
                         JOIN siswa ON keanggotaan_kelas.id_siswa = siswa.id_siswa
                         WHERE keanggotaan_kelas.id_kelas = :id_kelas AND keanggotaan_kelas.id_tp = :id_tp
                         ORDER BY siswa.nama_siswa ASC');
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);
        return $this->db->resultSet();
    }

    public function getSiswaNotInAnyClass($id_tp)
    {
        $this->db->query('SELECT * FROM siswa WHERE status_siswa = "aktif" AND id_siswa NOT IN 
                         (SELECT id_siswa FROM keanggotaan_kelas WHERE id_tp = :id_tp)');
        $this->db->bind('id_tp', $id_tp);
        return $this->db->resultSet();
    }

    // Tambah siswa ke kelas (array siswa)
    public function tambahAnggotaKelas($data)
    {
        $id_kelas = $data['id_kelas'];
        $id_tp = $data['id_tp'];
        $daftar_id_siswa = $data['id_siswa']; // array
        $rowCount = 0;

        foreach ($daftar_id_siswa as $id_siswa) {
            $query = "INSERT INTO keanggotaan_kelas (id_siswa, id_kelas, id_tp) 
                      VALUES (:id_siswa, :id_kelas, :id_tp)";
            $this->db->query($query);
            $this->db->bind('id_siswa', $id_siswa);
            $this->db->bind('id_kelas', $id_kelas);
            $this->db->bind('id_tp', $id_tp);
            $this->db->execute();
            $rowCount += $this->db->rowCount();
        }
        return $rowCount;
    }

    public function hapusAnggotaKelas($id_keanggotaan)
    {
        $query = "DELETE FROM keanggotaan_kelas WHERE id_keanggotaan = :id_keanggotaan";
        $this->db->query($query);
        $this->db->bind('id_keanggotaan', $id_keanggotaan);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Fungsi untuk mempromosikan siswa ke kelas baru
    public function prosesPromosiSiswaTerpilih($id_tp_tujuan, $id_kelas_tujuan, $daftar_siswa)
    {
        $totalSiswaDiproses = 0;

        foreach ($daftar_siswa as $id_siswa) {
            // Cek duplikat
            $this->db->query('SELECT COUNT(*) as total 
                              FROM keanggotaan_kelas 
                              WHERE id_siswa = :id_siswa AND id_tp = :id_tp_tujuan');
            $this->db->bind('id_siswa', $id_siswa);
            $this->db->bind('id_tp_tujuan', $id_tp_tujuan);
            $sudahAda = $this->db->single()['total'];

            if ($sudahAda == 0) {
                $queryInsert = "INSERT INTO keanggotaan_kelas (id_siswa, id_kelas, id_tp) 
                                VALUES (:id_siswa, :id_kelas_tujuan, :id_tp_tujuan)";
                $this->db->query($queryInsert);
                $this->db->bind('id_siswa', $id_siswa);
                $this->db->bind('id_kelas_tujuan', $id_kelas_tujuan);
                $this->db->bind('id_tp_tujuan', $id_tp_tujuan);
                $this->db->execute();
                $totalSiswaDiproses += $this->db->rowCount();
            }
        }

        return $totalSiswaDiproses;
    }

    // Fungsi meluluskan siswa (hapus dari keanggotaan kelas)
    public function luluskanSiswa($daftar_siswa, $id_tp_asal, $id_kelas_asal = null)
    {
        $total = 0;

        foreach ($daftar_siswa as $id_siswa) {
            $query = "DELETE FROM keanggotaan_kelas 
                      WHERE id_siswa = :id_siswa 
                      AND id_tp = :id_tp_asal";

            if ($id_kelas_asal !== null) {
                $query .= " AND id_kelas = :id_kelas_asal";
            }

            $this->db->query($query);
            $this->db->bind('id_siswa', $id_siswa);
            $this->db->bind('id_tp_asal', $id_tp_asal);

            if ($id_kelas_asal !== null) {
                $this->db->bind('id_kelas_asal', $id_kelas_asal);
            }

            $this->db->execute();
            $total += $this->db->rowCount();
        }

        return $total;
    }

    /**
     * Get keanggotaan siswa untuk tahun pelajaran tertentu
     */
    public function getKeanggotaanSiswa($id_siswa, $id_tp) {
        $this->db->query('SELECT kk.*, k.nama_kelas, k.jenjang 
                         FROM keanggotaan_kelas kk
                         JOIN kelas k ON kk.id_kelas = k.id_kelas
                         WHERE kk.id_siswa = :id_siswa AND kk.id_tp = :id_tp');
        $this->db->bind('id_siswa', $id_siswa);
        $this->db->bind('id_tp', $id_tp);
        return $this->db->single();
    }
}
