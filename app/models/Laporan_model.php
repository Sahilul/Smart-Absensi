<?php

// File: app/models/Laporan_model.php
class Laporan_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    /**
     * Mengambil rekap absensi dengan filter lengkap.
     */
    public function getRekapAbsensiPerKelas($filter = [])
    {
        // Query dasar
        $query = "SELECT 
                    s.id_siswa, s.nisn, s.nama_siswa,
                    SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as alfa
                  FROM siswa s
                  JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa
                  LEFT JOIN absensi a ON s.id_siswa = a.id_siswa
                  LEFT JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                  LEFT JOIN penugasan p ON j.id_penugasan = p.id_penugasan AND p.id_semester = :id_semester
                  WHERE kk.id_kelas = :id_kelas 
                  AND kk.id_tp = (SELECT id_tp FROM semester WHERE id_semester = :id_semester_tp)";

        // Tambahkan filter mata pelajaran jika dipilih
        if (!empty($filter['id_mapel'])) {
            $query .= " AND p.id_mapel = :id_mapel";
        }

        // Tambahkan filter rentang tanggal jika diisi
        if (!empty($filter['tgl_mulai']) && !empty($filter['tgl_selesai'])) {
            $query .= " AND j.tanggal BETWEEN :tgl_mulai AND :tgl_selesai";
        }

        $query .= " GROUP BY s.id_siswa, s.nisn, s.nama_siswa ORDER BY s.nama_siswa ASC";

        $this->db->query($query);
        $this->db->bind('id_kelas', $filter['id_kelas']);
        $this->db->bind('id_semester', $filter['id_semester']);
        $this->db->bind('id_semester_tp', $filter['id_semester']);

        if (!empty($filter['id_mapel'])) {
            $this->db->bind('id_mapel', $filter['id_mapel']);
        }
        if (!empty($filter['tgl_mulai']) && !empty($filter['tgl_selesai'])) {
            $this->db->bind('tgl_mulai', $filter['tgl_mulai']);
            $this->db->bind('tgl_selesai', $filter['tgl_selesai']);
        }
        
        return $this->db->resultSet();
    }
}