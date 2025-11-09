<?php

class Pembayaran_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    // =============================
    // TAGIHAN (class-scoped or global derived)
    // =============================

    public function getTagihanKelas($id_kelas, $id_tp, $id_semester = null) {
        $sql = "SELECT * FROM pembayaran_tagihan 
                WHERE id_kelas = :id_kelas AND id_tp = :id_tp";
        if ($id_semester) {
            $sql .= " AND (id_semester = :id_semester OR id_semester IS NULL)";
        }
        $sql .= " ORDER BY created_at DESC";
        $this->db->query($sql);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);
        if ($id_semester) $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    public function getTagihanById($id) {
        $this->db->query("SELECT * FROM pembayaran_tagihan WHERE id = :id");
        $this->db->bind('id', $id);
        return $this->db->single();
    }

    public function createTagihanKelas($data) {
        // data: nama, kategori_id?, id_tp, id_semester?, id_kelas, tipe, nominal_default, jatuh_tempo, created_by_user, created_by_role, ref_global_id?
        $this->db->query("INSERT INTO pembayaran_tagihan (nama, kategori_id, is_global, ref_global_id, id_tp, id_semester, id_kelas, tipe, nominal_default, jatuh_tempo, created_by_user, created_by_role)
                          VALUES (:nama, :kategori_id, 0, :ref_global_id, :id_tp, :id_semester, :id_kelas, :tipe, :nominal_default, :jatuh_tempo, :created_by_user, :created_by_role)");
        $this->db->bind('nama', $data['nama']);
        $this->db->bind('kategori_id', $data['kategori_id'] ?? null);
        $this->db->bind('ref_global_id', $data['ref_global_id'] ?? null);
        $this->db->bind('id_tp', $data['id_tp']);
        $this->db->bind('id_semester', $data['id_semester'] ?? null);
        $this->db->bind('id_kelas', $data['id_kelas']);
        $this->db->bind('tipe', $data['tipe'] ?? 'sekali');
        $this->db->bind('nominal_default', $data['nominal_default'] ?? 0);
        $this->db->bind('jatuh_tempo', $data['jatuh_tempo'] ?? null);
        $this->db->bind('created_by_user', $data['created_by_user'] ?? null);
        $this->db->bind('created_by_role', $data['created_by_role'] ?? 'wali_kelas');
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function deriveTagihanFromGlobal($id_tagihan_global, $overrides) {
        // Copy fields from global to class-scoped
        $this->db->query("SELECT * FROM pembayaran_tagihan WHERE id = :id AND is_global = 1");
        $this->db->bind('id', $id_tagihan_global);
        $global = $this->db->single();
        if (!$global) return false;

        $data = [
            'nama' => $global['nama'],
            'kategori_id' => $global['kategori_id'],
            'ref_global_id' => $global['id'],
            'id_tp' => $overrides['id_tp'],
            'id_semester' => $overrides['id_semester'] ?? null,
            'id_kelas' => $overrides['id_kelas'],
            'tipe' => $global['tipe'],
            'nominal_default' => $overrides['nominal_default'] ?? $global['nominal_default'],
            'jatuh_tempo' => $overrides['jatuh_tempo'] ?? $global['jatuh_tempo'],
            'created_by_user' => $overrides['created_by_user'] ?? null,
            'created_by_role' => 'wali_kelas',
        ];
        return $this->createTagihanKelas($data);
    }

    // =============================
    // PER-SISWA MAPPING & STATUS
    // =============================

    public function getTagihanSiswaList($tagihan_id) {
        $this->db->query("SELECT tgs.*, s.nama_siswa 
                          FROM pembayaran_tagihan_siswa tgs
                          JOIN siswa s ON s.id_siswa = tgs.id_siswa
                          WHERE tgs.tagihan_id = :tid
                          ORDER BY s.nama_siswa ASC");
        $this->db->bind('tid', $tagihan_id);
        return $this->db->resultSet();
    }

    public function ensureTagihanSiswa($tagihan_id, $id_siswa, $nominal_default = 0, $jatuh_tempo = null, $periode_bulan = null, $periode_tahun = null) {
        // Normalize period to 0 if null
        $pb = ($periode_bulan === null) ? 0 : (int)$periode_bulan;
        $pt = ($periode_tahun === null) ? 0 : (int)$periode_tahun;

        // If not exists, insert default row
        $this->db->query("SELECT id FROM pembayaran_tagihan_siswa WHERE tagihan_id = :tid AND id_siswa = :sid AND periode_bulan = :bln AND periode_tahun = :thn LIMIT 1");
        $this->db->bind('tid', $tagihan_id);
        $this->db->bind('sid', $id_siswa);
        $this->db->bind('bln', $pb);
        $this->db->bind('thn', $pt);
        $row = $this->db->single();
        if ($row) return $row['id'];

        $this->db->query("INSERT INTO pembayaran_tagihan_siswa (tagihan_id, id_siswa, nominal, diskon, total_terbayar, status, jatuh_tempo, periode_bulan, periode_tahun)
                          VALUES (:tid, :sid, :nom, 0, 0, 'belum', :jt, :bln, :thn)");
        $this->db->bind('tid', $tagihan_id);
        $this->db->bind('sid', $id_siswa);
        $this->db->bind('nom', $nominal_default);
        $this->db->bind('jt', $jatuh_tempo);
        $this->db->bind('bln', $pb);
        $this->db->bind('thn', $pt);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    public function updateDiskonSiswa($tagihan_id, $id_siswa, $diskon) {
        $this->db->query("UPDATE pembayaran_tagihan_siswa SET diskon = :diskon WHERE tagihan_id = :tid AND id_siswa = :sid");
        $this->db->bind('diskon', $diskon);
        $this->db->bind('tid', $tagihan_id);
        $this->db->bind('sid', $id_siswa);
        $this->db->execute();
        return $this->db->rowCount();
    }

    // =============================
    // TRANSAKSI
    // =============================

    public function createTransaksi($tagihan_id, $id_siswa, $jumlah, $metode = null, $keterangan = null, $bukti_path = null, $user_input_id = null) {
        // Ensure mapping exists to know nominal & diskon
        $tagihan = $this->getTagihanById($tagihan_id);
        if (!$tagihan) return false;

        // Create transaksi
        $this->db->query("INSERT INTO pembayaran_transaksi (tagihan_id, id_siswa, jumlah, metode, keterangan, bukti_path, user_input_id)
                          VALUES (:tid, :sid, :jml, :metode, :ket, :bukti, :uid)");
        $this->db->bind('tid', $tagihan_id);
        $this->db->bind('sid', $id_siswa);
        $this->db->bind('jml', $jumlah);
        $this->db->bind('metode', $metode);
        $this->db->bind('ket', $keterangan);
        $this->db->bind('bukti', $bukti_path);
        $this->db->bind('uid', $user_input_id);
        $this->db->execute();

        // Upsert tagihan_siswa
        $mapId = $this->ensureTagihanSiswa($tagihan_id, $id_siswa, $tagihan['nominal_default'], $tagihan['jatuh_tempo']);

        // Update akumulasi
        $this->db->query("UPDATE pembayaran_tagihan_siswa 
                          SET total_terbayar = total_terbayar + :jml
                          WHERE id = :id");
        $this->db->bind('jml', $jumlah);
        $this->db->bind('id', $mapId);
        $this->db->execute();

        // Refresh and set status
        $this->db->query("SELECT nominal, diskon, total_terbayar FROM pembayaran_tagihan_siswa WHERE id = :id");
        $this->db->bind('id', $mapId);
        $st = $this->db->single();
        $target = max(0, ((int)$st['nominal']) - ((int)$st['diskon']));
        $status = 'belum';
        if ((int)$st['total_terbayar'] <= 0) {
            $status = 'belum';
        } else if ((int)$st['total_terbayar'] < $target) {
            $status = 'sebagian';
        } else {
            $status = 'lunas';
        }
        $this->db->query("UPDATE pembayaran_tagihan_siswa SET status = :status WHERE id = :id");
        $this->db->bind('status', $status);
        $this->db->bind('id', $mapId);
        $this->db->execute();

        return true;
    }

    public function getRiwayat($id_kelas, $id_tp, $limit = 100) {
        $this->db->query("SELECT trx.*, s.nama_siswa, t.nama AS nama_tagihan
                          FROM pembayaran_transaksi trx
                          JOIN pembayaran_tagihan t ON t.id = trx.tagihan_id
                          JOIN siswa s ON s.id_siswa = trx.id_siswa
                          WHERE t.id_kelas = :id_kelas AND t.id_tp = :id_tp
                          ORDER BY trx.tanggal DESC
                          LIMIT :lim");
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_tp', $id_tp);
        // For LIMIT binding, ensure integer binding
        $this->db->bind('lim', (int)$limit);
        return $this->db->resultSet();
    }

}
