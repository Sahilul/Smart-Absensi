<?php

// File: app/models/Kelas_model.php
class Kelas_model {
    private $db;

    public function __construct()
    {
        // Menggunakan Database core Anda
        $this->db = new Database;
    }

    // FUNGSI BARU: Mengambil kelas berdasarkan Tahun Pelajaran
    public function getKelasByTP($id_tp)
    {
        $this->db->query('SELECT * FROM kelas WHERE id_tp = :id_tp ORDER BY jenjang ASC, nama_kelas ASC');
        $this->db->bind('id_tp', $id_tp);
        return $this->db->resultSet();
    }

    public function getJumlahKelas()
    {
        $this->db->query('SELECT COUNT(*) as total FROM kelas');
        return $this->db->single()['total'];
    }

    /**
     * Mengambil semua kelas dengan detail jumlah siswa dan guru
     * @param int $id_tp ID Tahun Pelajaran (0 = semua)
     * @return array
     */
    public function getAllKelasWithDetails($id_tp = 0)
    {
        // Query yang diperbaiki - pisah subquery guru_count
        if ($id_tp > 0) {
            // Query untuk TP tertentu
            $sql = "SELECT 
                        k.*,
                        tp.nama_tp,
                        COALESCE(siswa_count.jumlah_siswa, 0) as jumlah_siswa,
                        COALESCE(guru_count.jumlah_guru, 0) as jumlah_guru,
                        g.nama_guru as nama_guru_walikelas
                    FROM kelas k
                    JOIN tp ON k.id_tp = tp.id_tp
                    LEFT JOIN wali_kelas wk ON k.id_kelas = wk.id_kelas
                    LEFT JOIN guru g ON wk.id_guru = g.id_guru
                    LEFT JOIN (
                        SELECT 
                            kk.id_kelas, 
                            COUNT(DISTINCT kk.id_siswa) as jumlah_siswa
                        FROM keanggotaan_kelas kk
                        JOIN siswa s ON kk.id_siswa = s.id_siswa
                        WHERE s.status_siswa = 'aktif'
                        GROUP BY kk.id_kelas
                    ) siswa_count ON k.id_kelas = siswa_count.id_kelas
                    LEFT JOIN (
                        SELECT 
                            p.id_kelas, 
                            COUNT(DISTINCT p.id_guru) as jumlah_guru
                        FROM penugasan p
                        JOIN semester sem ON p.id_semester = sem.id_semester
                        WHERE sem.id_tp = :id_tp_guru
                        GROUP BY p.id_kelas
                    ) guru_count ON k.id_kelas = guru_count.id_kelas
                    WHERE k.id_tp = :id_tp
                    ORDER BY k.jenjang ASC, k.nama_kelas ASC";
            
            $this->db->query($sql);
            $this->db->bind('id_tp', $id_tp);
            $this->db->bind('id_tp_guru', $id_tp);
            
        } else {
            // Query untuk semua TP
            $sql = "SELECT 
                        k.*,
                        tp.nama_tp,
                        COALESCE(siswa_count.jumlah_siswa, 0) as jumlah_siswa,
                        COALESCE(guru_count.jumlah_guru, 0) as jumlah_guru,
                        g.nama_guru as nama_guru_walikelas
                    FROM kelas k
                    JOIN tp ON k.id_tp = tp.id_tp
                    LEFT JOIN wali_kelas wk ON k.id_kelas = wk.id_kelas
                    LEFT JOIN guru g ON wk.id_guru = g.id_guru
                    LEFT JOIN (
                        SELECT 
                            kk.id_kelas, 
                            COUNT(DISTINCT kk.id_siswa) as jumlah_siswa
                        FROM keanggotaan_kelas kk
                        JOIN siswa s ON kk.id_siswa = s.id_siswa
                        WHERE s.status_siswa = 'aktif'
                        GROUP BY kk.id_kelas
                    ) siswa_count ON k.id_kelas = siswa_count.id_kelas
                    LEFT JOIN (
                        SELECT 
                            p.id_kelas, 
                            COUNT(DISTINCT p.id_guru) as jumlah_guru
                        FROM penugasan p
                        GROUP BY p.id_kelas
                    ) guru_count ON k.id_kelas = guru_count.id_kelas
                    ORDER BY tp.nama_tp DESC, k.jenjang ASC, k.nama_kelas ASC";
            
            $this->db->query($sql);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Method getAllKelas() - menggunakan getAllKelasWithDetails untuk kompatibilitas
     */
    public function getAllKelas()
    {
        return $this->getAllKelasWithDetails(0); // 0 = semua TP
    }

    public function getKelasById($id)
    {
        $this->db->query('SELECT * FROM kelas WHERE id_kelas = :id');
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function tambahDataKelas($data)
    {
        $query = "INSERT INTO kelas (nama_kelas, jenjang, id_tp) VALUES (:nama_kelas, :jenjang, :id_tp)";
        $this->db->query($query);
        $this->db->bind('nama_kelas', $data['nama_kelas']);
        $this->db->bind('jenjang', $data['jenjang']);
        $this->db->bind('id_tp', $data['id_tp']);
        $this->db->execute();
        
        // Return the last insert ID for wali_kelas assignment
        return $this->db->lastInsertId();
    }

    /**
     * Cek duplikasi nama kelas dalam tahun pelajaran yang sama
     */
    public function cekDuplikasiKelas($nama_kelas, $id_tp)
    {
        $this->db->query('SELECT COUNT(*) as total FROM kelas WHERE nama_kelas = :nama_kelas AND id_tp = :id_tp');
        $this->db->bind('nama_kelas', $nama_kelas);
        $this->db->bind('id_tp', $id_tp);
        $result = $this->db->single();
        return $result['total'] > 0;
    }

    /**
     * Cek duplikasi nama kelas untuk edit (exclude kelas yang sedang diedit)
     */
    public function cekDuplikasiKelasEdit($nama_kelas, $id_tp, $id_kelas_exclude)
    {
        $this->db->query('SELECT COUNT(*) as total FROM kelas WHERE nama_kelas = :nama_kelas AND id_tp = :id_tp AND id_kelas != :id_kelas');
        $this->db->bind('nama_kelas', $nama_kelas);
        $this->db->bind('id_tp', $id_tp);
        $this->db->bind('id_kelas', $id_kelas_exclude);
        $result = $this->db->single();
        return $result['total'] > 0;
    }

    public function updateDataKelas($data)
    {
        // Method update tanpa mengubah id_tp (TP tidak boleh diubah saat edit)
        $query = "UPDATE kelas SET nama_kelas = :nama_kelas, jenjang = :jenjang WHERE id_kelas = :id_kelas";
        $this->db->query($query);
        $this->db->bind('nama_kelas', $data['nama_kelas']);
        $this->db->bind('jenjang', $data['jenjang']);
        $this->db->bind('id_kelas', $data['id_kelas']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function hapusDataKelas($id)
    {
        $query = "DELETE FROM kelas WHERE id_kelas = :id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // ++ FUNGSI BARU UNTUK MEMERIKSA KETERKAITAN DATA ++
    /**
     * Cek keterkaitan data kelas dengan tabel lain (penugasan & keanggotaan_kelas).
     * Mencegah penghapusan kelas jika masih digunakan.
     * @param int $id_kelas ID kelas yang akan dicek.
     * @return int Jumlah data terkait yang ditemukan.
     */
    public function cekKeterkaitanData($id_kelas)
    {
        // Cek di tabel keanggotaan (apakah ada siswa di kelas ini)
        $this->db->query('SELECT COUNT(*) as total FROM keanggotaan_kelas WHERE id_kelas = :id_kelas');
        $this->db->bind('id_kelas', $id_kelas);
        $result1 = $this->db->single();
        if ($result1['total'] > 0) {
            return $result1['total'];
        }

        // Cek di tabel penugasan (apakah ada guru yang ditugaskan ke kelas ini)
        $this->db->query('SELECT COUNT(*) as total FROM penugasan WHERE id_kelas = :id_kelas');
        $this->db->bind('id_kelas', $id_kelas);
        $result2 = $this->db->single();
        if ($result2['total'] > 0) {
            return $result2['total'];
        }

        return 0; // Tidak ada keterkaitan
    }

    /**
     * Mendapatkan jumlah siswa di kelas tertentu pada tahun pelajaran tertentu
     * @param int $id_kelas ID Kelas
     * @param int $id_tp ID Tahun Pelajaran
     * @return int Jumlah siswa aktif
     */
    public function getJumlahSiswaKelas($id_kelas, $id_tp)
    {
        $this->db->query('SELECT COUNT(DISTINCT kk.id_siswa) as total 
                         FROM keanggotaan_kelas kk
                         JOIN siswa s ON kk.id_siswa = s.id_siswa
                         WHERE kk.id_kelas = :id_kelas 
                         AND kk.id_tp = :id_tp 
                         AND s.status_siswa = "aktif"');
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }

    public function naikkanSiswa($daftarSiswa, $id_tp_asal, $id_kelas_asal, $id_tp_tujuan, $id_kelas_tujuan)
    {
        foreach ($daftarSiswa as $id_siswa) {
            // Hapus keanggotaan lama
            $this->db->query("DELETE FROM keanggotaan_kelas WHERE id_siswa = :id_siswa AND id_kelas = :id_kelas AND id_tp = :id_tp");
            $this->db->bind('id_siswa', $id_siswa);
            $this->db->bind('id_kelas', $id_kelas_asal);
            $this->db->bind('id_tp', $id_tp_asal);
            $this->db->execute();

            // Tambahkan ke kelas tujuan
            $this->db->query("INSERT INTO keanggotaan_kelas (id_siswa, id_kelas, id_tp) VALUES (:id_siswa, :id_kelas, :id_tp)");
            $this->db->bind('id_siswa', $id_siswa);
            $this->db->bind('id_kelas', $id_kelas_tujuan);
            $this->db->bind('id_tp', $id_tp_tujuan);
            $this->db->execute();
        }
        return true;
    }

    public function luluskanSiswa($daftarSiswa, $id_tp_asal, $id_kelas_asal)
    {
        foreach ($daftarSiswa as $id_siswa) {
            // Update status siswa menjadi lulus
            $this->db->query("UPDATE siswa SET status_siswa = 'lulus' WHERE id_siswa = :id_siswa");
            $this->db->bind('id_siswa', $id_siswa);
            $this->db->execute();

            // Hapus dari keanggotaan kelas asal
            $this->db->query("DELETE FROM keanggotaan_kelas WHERE id_siswa = :id_siswa AND id_kelas = :id_kelas AND id_tp = :id_tp");
            $this->db->bind('id_siswa', $id_siswa);
            $this->db->bind('id_kelas', $id_kelas_asal);
            $this->db->bind('id_tp', $id_tp_asal);
            $this->db->execute();
        }
        return true;
    }

    /**
     * Assign wali kelas to a class
     */
    public function assignWaliKelas($id_kelas, $id_guru)
    {
        // Ambil id_tp dari kelas
        $this->db->query("SELECT id_tp FROM kelas WHERE id_kelas = :id_kelas");
        $this->db->bind('id_kelas', $id_kelas);
        $kelas = $this->db->single();
        $id_tp = $kelas['id_tp'];
        
        // Check if wali_kelas already exists for this class and TP
        $this->db->query("SELECT id_walikelas FROM wali_kelas WHERE id_kelas = :id_kelas AND id_tp = :id_tp");
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);
        $existing = $this->db->single();

        if ($existing) {
            // Update existing wali_kelas
            $this->db->query("UPDATE wali_kelas SET id_guru = :id_guru WHERE id_kelas = :id_kelas AND id_tp = :id_tp");
            $this->db->bind('id_guru', $id_guru);
            $this->db->bind('id_kelas', $id_kelas);
            $this->db->bind('id_tp', $id_tp);
        } else {
            // Insert new wali_kelas
            $this->db->query("INSERT INTO wali_kelas (id_kelas, id_guru, id_tp) VALUES (:id_kelas, :id_guru, :id_tp)");
            $this->db->bind('id_kelas', $id_kelas);
            $this->db->bind('id_guru', $id_guru);
            $this->db->bind('id_tp', $id_tp);
        }
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    /**
     * Get wali kelas by kelas ID
     */
    public function getWaliKelasByKelasId($id_kelas)
    {
        // Ambil id_tp dari kelas
        $this->db->query("SELECT id_tp FROM kelas WHERE id_kelas = :id_kelas");
        $this->db->bind('id_kelas', $id_kelas);
        $kelas = $this->db->single();
        
        if (!$kelas) {
            return null;
        }
        
        $this->db->query("SELECT wk.*, g.nama_guru FROM wali_kelas wk 
                         JOIN guru g ON wk.id_guru = g.id_guru 
                         WHERE wk.id_kelas = :id_kelas AND wk.id_tp = :id_tp");
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $kelas['id_tp']);
        return $this->db->single();
    }

    /**
     * Remove wali kelas from a class
     */
    public function removeWaliKelas($id_kelas)
    {
        // Ambil id_tp dari kelas
        $this->db->query("SELECT id_tp FROM kelas WHERE id_kelas = :id_kelas");
        $this->db->bind('id_kelas', $id_kelas);
        $kelas = $this->db->single();
        
        if (!$kelas) {
            return 0;
        }
        
        $this->db->query("DELETE FROM wali_kelas WHERE id_kelas = :id_kelas AND id_tp = :id_tp");
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $kelas['id_tp']);
        $this->db->execute();
        return $this->db->rowCount();
    }
}

