<?php
// File: app/models/PengaturanRapor_model.php

class PengaturanRapor_model {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Get pengaturan rapor by guru dan tahun pelajaran
     */
    public function getPengaturanByGuru($id_guru, $id_tp) {
        $this->db->query("SELECT * FROM pengaturan_rapor 
            WHERE id_guru = :id_guru AND id_tp = :id_tp");
        $this->db->bind(':id_guru', $id_guru);
        $this->db->bind(':id_tp', $id_tp);
        return $this->db->single();
    }

    /**
     * Insert pengaturan rapor baru
     */
    public function insert($data) {
        $this->db->query("INSERT INTO pengaturan_rapor 
            (id_guru, id_tp, kop_rapor, nama_madrasah, tempat_cetak, nama_kepala_madrasah, ttd_kepala_madrasah, ttd_wali_kelas, tanggal_cetak, mapel_rapor, persen_harian_sts, persen_sts, persen_harian_sas, persen_sts_sas, persen_sas) 
            VALUES 
            (:id_guru, :id_tp, :kop_rapor, :nama_madrasah, :tempat_cetak, :nama_kepala_madrasah, :ttd_kepala_madrasah, :ttd_wali_kelas, :tanggal_cetak, :mapel_rapor, :persen_harian_sts, :persen_sts, :persen_harian_sas, :persen_sts_sas, :persen_sas)");
        
        $this->db->bind(':id_guru', $data['id_guru']);
        $this->db->bind(':id_tp', $data['id_tp']);
        $this->db->bind(':kop_rapor', $data['kop_rapor'] ?? '');
        $this->db->bind(':nama_madrasah', $data['nama_madrasah'] ?? '');
        $this->db->bind(':tempat_cetak', $data['tempat_cetak'] ?? '');
        $this->db->bind(':nama_kepala_madrasah', $data['nama_kepala_madrasah'] ?? '');
        $this->db->bind(':ttd_kepala_madrasah', $data['ttd_kepala_madrasah'] ?? '');
        $this->db->bind(':ttd_wali_kelas', $data['ttd_wali_kelas'] ?? '');
        $this->db->bind(':tanggal_cetak', $data['tanggal_cetak'] ?? date('Y-m-d'));
        $this->db->bind(':mapel_rapor', $data['mapel_rapor'] ?? '[]');
        $this->db->bind(':persen_harian_sts', $data['persen_harian_sts'] ?? 60);
        $this->db->bind(':persen_sts', $data['persen_sts'] ?? 40);
        $this->db->bind(':persen_harian_sas', $data['persen_harian_sas'] ?? 40);
        $this->db->bind(':persen_sts_sas', $data['persen_sts_sas'] ?? 30);
        $this->db->bind(':persen_sas', $data['persen_sas'] ?? 30);
        
        return $this->db->execute();
    }

    /**
     * Update pengaturan rapor
     */
    public function update($data) {
        $this->db->query("UPDATE pengaturan_rapor SET 
            kop_rapor = :kop_rapor,
            nama_madrasah = :nama_madrasah,
            tempat_cetak = :tempat_cetak,
            nama_kepala_madrasah = :nama_kepala_madrasah,
            ttd_kepala_madrasah = :ttd_kepala_madrasah,
            ttd_wali_kelas = :ttd_wali_kelas,
            tanggal_cetak = :tanggal_cetak,
            mapel_rapor = :mapel_rapor,
            persen_harian_sts = :persen_harian_sts,
            persen_sts = :persen_sts,
            persen_harian_sas = :persen_harian_sas,
            persen_sts_sas = :persen_sts_sas,
            persen_sas = :persen_sas
            WHERE id_guru = :id_guru AND id_tp = :id_tp");
        
        $this->db->bind(':kop_rapor', $data['kop_rapor'] ?? '');
        $this->db->bind(':nama_madrasah', $data['nama_madrasah'] ?? '');
        $this->db->bind(':tempat_cetak', $data['tempat_cetak'] ?? '');
        $this->db->bind(':nama_kepala_madrasah', $data['nama_kepala_madrasah'] ?? '');
        $this->db->bind(':ttd_kepala_madrasah', $data['ttd_kepala_madrasah'] ?? '');
        $this->db->bind(':ttd_wali_kelas', $data['ttd_wali_kelas'] ?? '');
        $this->db->bind(':tanggal_cetak', $data['tanggal_cetak'] ?? date('Y-m-d'));
        $this->db->bind(':mapel_rapor', $data['mapel_rapor'] ?? '[]');
        $this->db->bind(':persen_harian_sts', $data['persen_harian_sts'] ?? 60);
        $this->db->bind(':persen_sts', $data['persen_sts'] ?? 40);
        $this->db->bind(':persen_harian_sas', $data['persen_harian_sas'] ?? 40);
        $this->db->bind(':persen_sts_sas', $data['persen_sts_sas'] ?? 30);
        $this->db->bind(':persen_sas', $data['persen_sas'] ?? 30);
        $this->db->bind(':id_guru', $data['id_guru']);
        $this->db->bind(':id_tp', $data['id_tp']);
        
        return $this->db->execute();
    }

    /**
     * Simpan atau update pengaturan (upsert)
     */
    public function save($data) {
        $existing = $this->getPengaturanByGuru($data['id_guru'], $data['id_tp']);
        
        if ($existing) {
            return $this->update($data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Delete pengaturan rapor
     */
    public function delete($id_guru, $id_tp) {
        $this->db->query("DELETE FROM pengaturan_rapor 
            WHERE id_guru = :id_guru AND id_tp = :id_tp");
        $this->db->bind(':id_guru', $id_guru);
        $this->db->bind(':id_tp', $id_tp);
        return $this->db->execute();
    }
}