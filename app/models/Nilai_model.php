<?php
// File: app/models/Nilai_model.php

class Nilai_model {
    private $db;

    public function __construct() {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database();
    }

    /**
     * Ambil semua nilai tugas harian untuk guru tertentu dalam semester aktif
     */
    public function getNilaiTugasHarian($id_guru, $id_semester) {
        $sql = "SELECT 
                    n.id_nilai,
                    n.id_siswa,
                    n.jenis_nilai,
                    n.nilai,
                    n.tanggal_input,
                    s.nama_siswa,
                    k.nama_kelas,
                    m.nama_mapel,
                    g.nama_guru
                FROM nilai_siswa n
                JOIN siswa s ON n.id_siswa = s.id_siswa
                JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa
                JOIN kelas k ON kk.id_kelas = k.id_kelas
                JOIN mapel m ON n.id_mapel = m.id_mapel
                JOIN guru g ON n.id_guru = g.id_guru
                WHERE n.id_guru = :id_guru
                  AND n.id_semester = :id_semester
                  AND n.jenis_nilai = 'harian'
                ORDER BY n.tanggal_input DESC, s.nama_siswa";
        
        $this->db->query($sql);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Ambil semua nilai tengah semester untuk guru tertentu dalam semester aktif
     */
    public function getNilaiTengahSemester($id_guru, $id_semester) {
        $sql = "SELECT 
                    n.id_nilai,
                    n.id_siswa,
                    n.jenis_nilai,
                    n.nilai,
                    n.tanggal_input,
                    s.nama_siswa,
                    k.nama_kelas,
                    m.nama_mapel,
                    g.nama_guru
                FROM nilai_siswa n
                JOIN siswa s ON n.id_siswa = s.id_siswa
                JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa
                JOIN kelas k ON kk.id_kelas = k.id_kelas
                JOIN mapel m ON n.id_mapel = m.id_mapel
                JOIN guru g ON n.id_guru = g.id_guru
                WHERE n.id_guru = :id_guru
                  AND n.id_semester = :id_semester
                  AND n.jenis_nilai = 'sts'
                ORDER BY s.nama_siswa";
        
        $this->db->query($sql);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Ambil semua nilai akhir semester untuk guru tertentu dalam semester aktif
     */
    public function getNilaiAkhirSemester($id_guru, $id_semester) {
        $sql = "SELECT 
                    n.id_nilai,
                    n.id_siswa,
                    n.jenis_nilai,
                    n.nilai,
                    n.tanggal_input,
                    s.nama_siswa,
                    k.nama_kelas,
                    m.nama_mapel,
                    g.nama_guru
                FROM nilai_siswa n
                JOIN siswa s ON n.id_siswa = s.id_siswa
                JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa
                JOIN kelas k ON kk.id_kelas = k.id_kelas
                JOIN mapel m ON n.id_mapel = m.id_mapel
                JOIN guru g ON n.id_guru = g.id_guru
                WHERE n.id_guru = :id_guru
                  AND n.id_semester = :id_semester
                  AND n.jenis_nilai = 'sas'
                ORDER BY s.nama_siswa";
        
        $this->db->query($sql);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Ambil nilai tugas harian berdasarkan penugasan
     */
    public function getNilaiTugasHarianByPenugasan($id_penugasan) {
        // Get guru, mapel, semester from penugasan
        $sqlPenugasan = "SELECT id_guru, id_mapel FROM penugasan WHERE id_penugasan = :id_penugasan";
        $this->db->query($sqlPenugasan);
        $this->db->bind('id_penugasan', $id_penugasan);
        $penugasan = $this->db->single();
        
        if (!$penugasan) {
            return [];
        }
        
        // Get current active semester
        $sqlSemester = "SELECT id_semester FROM semester WHERE is_active = 1 LIMIT 1";
        $this->db->query($sqlSemester);
        $semester = $this->db->single();
        $id_semester = $semester['id_semester'] ?? 0;
        
        $sql = "SELECT ns.*, s.nama_siswa, s.nisn
                FROM nilai_siswa ns
                JOIN siswa s ON ns.id_siswa = s.id_siswa
                WHERE ns.id_guru = :id_guru 
                AND ns.id_mapel = :id_mapel
                AND ns.id_semester = :id_semester
                AND ns.jenis_nilai = 'harian'
                ORDER BY ns.tanggal_input DESC, s.nama_siswa ASC";
        $this->db->query($sql);
        $this->db->bind('id_guru', $penugasan['id_guru']);
        $this->db->bind('id_mapel', $penugasan['id_mapel']);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    /**
     * Ambil nilai tugas harian berdasarkan jurnal
     */
    public function getNilaiTugasHarianByJurnal($id_jurnal) {
        // Get data from jurnal and penugasan
        $sqlJurnal = "SELECT p.id_guru, p.id_mapel, p.id_semester 
                      FROM jurnal j 
                      JOIN penugasan p ON j.id_penugasan = p.id_penugasan 
                      WHERE j.id_jurnal = :id_jurnal";
        $this->db->query($sqlJurnal);
        $this->db->bind('id_jurnal', $id_jurnal);
        $jurnal = $this->db->single();
        
        if (!$jurnal) {
            return [];
        }
        
        $sql = "SELECT ns.*, s.nama_siswa, s.nisn
                FROM nilai_siswa ns
                JOIN siswa s ON ns.id_siswa = s.id_siswa
                WHERE ns.id_guru = :id_guru 
                AND ns.id_mapel = :id_mapel
                AND ns.id_semester = :id_semester
                AND ns.jenis_nilai = 'harian'
                AND ns.keterangan = :id_jurnal
                ORDER BY s.nama_siswa ASC";
        $this->db->query($sql);
        $this->db->bind('id_guru', $jurnal['id_guru']);
        $this->db->bind('id_mapel', $jurnal['id_mapel']);
        $this->db->bind('id_semester', $jurnal['id_semester']);
        $this->db->bind('id_jurnal', $id_jurnal);
        return $this->db->resultSet();
    }

    /**
     * Hitung jumlah nilai yang sudah diinput untuk jurnal tertentu
     */
    public function countNilaiByJurnal($id_jurnal) {
        $sql = "SELECT COUNT(*) as total
                FROM nilai_siswa
                WHERE jenis_nilai = 'harian'
                AND keterangan = :id_jurnal";
        $this->db->query($sql);
        $this->db->bind('id_jurnal', $id_jurnal);
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }

    /**
     * Hitung jumlah nilai STS/SAS berdasarkan penugasan dan jenis
     */
    public function countNilaiByPenugasanAndJenis($id_penugasan, $jenis_nilai) {
        // Ambil detail penugasan dan hitung nilai dalam satu query
        $sql = "SELECT COUNT(n.id_nilai) as total
                FROM penugasan p
                LEFT JOIN nilai_siswa n ON (
                    n.id_guru = p.id_guru 
                    AND n.id_mapel = p.id_mapel 
                    AND n.id_semester = p.id_semester
                    AND n.jenis_nilai = :jenis_nilai
                )
                WHERE p.id_penugasan = :id_penugasan";
        
        $this->db->query($sql);
        $this->db->bind('jenis_nilai', $jenis_nilai);
        $this->db->bind('id_penugasan', $id_penugasan);
        
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }

    /**
     * Ambil nilai tengah semester berdasarkan penugasan
     */
    public function getNilaiTengahSemesterByPenugasan($id_penugasan) {
        // Get guru, mapel, semester from penugasan
        $sqlPenugasan = "SELECT id_guru, id_mapel, id_semester FROM penugasan WHERE id_penugasan = :id_penugasan";
        $this->db->query($sqlPenugasan);
        $this->db->bind('id_penugasan', $id_penugasan);
        $penugasan = $this->db->single();
        
        if (!$penugasan) {
            return [];
        }
        
        $sql = "SELECT ns.*, s.nama_siswa, s.nisn
                FROM nilai_siswa ns
                JOIN siswa s ON ns.id_siswa = s.id_siswa
                WHERE ns.id_guru = :id_guru 
                AND ns.id_mapel = :id_mapel
                AND ns.id_semester = :id_semester
                AND ns.jenis_nilai = 'sts'
                ORDER BY s.nama_siswa ASC";
        $this->db->query($sql);
        $this->db->bind('id_guru', $penugasan['id_guru']);
        $this->db->bind('id_mapel', $penugasan['id_mapel']);
        $this->db->bind('id_semester', $penugasan['id_semester']);
        return $this->db->resultSet();
    }

    /**
     * Ambil nilai akhir semester berdasarkan penugasan
     */
    public function getNilaiAkhirSemesterByPenugasan($id_penugasan) {
        // Get guru, mapel, semester from penugasan
        $sqlPenugasan = "SELECT id_guru, id_mapel, id_semester FROM penugasan WHERE id_penugasan = :id_penugasan";
        $this->db->query($sqlPenugasan);
        $this->db->bind('id_penugasan', $id_penugasan);
        $penugasan = $this->db->single();
        
        if (!$penugasan) {
            return [];
        }
        
        $sql = "SELECT ns.*, s.nama_siswa, s.nisn
                FROM nilai_siswa ns
                JOIN siswa s ON ns.id_siswa = s.id_siswa
                WHERE ns.id_guru = :id_guru 
                AND ns.id_mapel = :id_mapel
                AND ns.id_semester = :id_semester
                AND ns.jenis_nilai = 'sas'
                ORDER BY s.nama_siswa ASC";
        $this->db->query($sql);
        $this->db->bind('id_guru', $penugasan['id_guru']);
        $this->db->bind('id_mapel', $penugasan['id_mapel']);
        $this->db->bind('id_semester', $penugasan['id_semester']);
        return $this->db->resultSet();
    }

    /**
     * Cek apakah sudah ada nilai untuk tugas harian pada pertemuan tertentu
     */
    public function cekNilaiTugasHarian($id_jurnal, $id_siswa) {
        $sql = "SELECT COUNT(*) as total FROM nilai_siswa WHERE id_jurnal = :id_jurnal AND id_siswa = :id_siswa AND jenis_nilai = 'harian'";
        $this->db->query($sql);
        $this->db->bind('id_jurnal', $id_jurnal);
        $this->db->bind('id_siswa', $id_siswa);
        return $this->db->single()['total'] > 0;
    }

    /**
     * Simpan nilai harian (method baru untuk struktur tabel yang ada)
     */
    public function simpanNilaiHarian($data) {
        // Cek apakah sudah ada nilai untuk siswa, guru, mapel, semester, dan keterangan yang sama
        $sqlCheck = "SELECT id_nilai FROM nilai_siswa 
                     WHERE id_siswa = :id_siswa 
                     AND id_guru = :id_guru 
                     AND id_mapel = :id_mapel 
                     AND id_semester = :id_semester 
                     AND jenis_nilai = :jenis_nilai
                     AND keterangan = :keterangan";
        
        $this->db->query($sqlCheck);
        $this->db->bind('id_siswa', $data['id_siswa']);
        $this->db->bind('id_guru', $data['id_guru']);
        $this->db->bind('id_mapel', $data['id_mapel']);
        $this->db->bind('id_semester', $data['id_semester']);
        $this->db->bind('jenis_nilai', $data['jenis_nilai']);
        $this->db->bind('keterangan', $data['keterangan']);
        
        $existing = $this->db->single();
        
        if ($existing) {
            // Update nilai yang sudah ada
            $sqlUpdate = "UPDATE nilai_siswa 
                         SET nilai = :nilai, tanggal_input = :tanggal_input 
                         WHERE id_nilai = :id_nilai";
            
            $this->db->query($sqlUpdate);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
            $this->db->bind('id_nilai', $existing['id_nilai']);
        } else {
            // Insert nilai baru
            $sqlInsert = "INSERT INTO nilai_siswa 
                         (id_siswa, id_guru, id_mapel, id_semester, jenis_nilai, keterangan, nilai, tanggal_input) 
                         VALUES 
                         (:id_siswa, :id_guru, :id_mapel, :id_semester, :jenis_nilai, :keterangan, :nilai, :tanggal_input)";
            
            $this->db->query($sqlInsert);
            $this->db->bind('id_siswa', $data['id_siswa']);
            $this->db->bind('id_guru', $data['id_guru']);
            $this->db->bind('id_mapel', $data['id_mapel']);
            $this->db->bind('id_semester', $data['id_semester']);
            $this->db->bind('jenis_nilai', $data['jenis_nilai']);
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
        }
        
        return $this->db->execute();
    }

    /**
     * Simpan atau update nilai tugas harian
     */
    public function simpanNilaiTugasHarian($data) {
        // Dapatkan id_penugasan dari id_jurnal
        $sql = "SELECT p.id_penugasan, p.id_guru, p.id_mapel, p.id_semester
                FROM jurnal j
                JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                WHERE j.id_jurnal = :id_jurnal";
        $this->db->query($sql);
        $this->db->bind('id_jurnal', $data['id_jurnal']);
        $penugasan = $this->db->single();

        if (!$penugasan || empty($penugasan['id_penugasan'])) {
            throw new Exception("Penugasan tidak ditemukan untuk jurnal ini.");
        }

        $id_guru = $penugasan['id_guru'];
        $id_mapel = $penugasan['id_mapel'];
        $id_semester = $penugasan['id_semester'];
        $keterangan = $data['id_jurnal'];

        // Cek apakah nilai sudah ada untuk kombinasi siswa + mapel + jurnal ini
        $checkSql = "SELECT id_nilai
                     FROM nilai_siswa
                     WHERE id_siswa = :id_siswa
                       AND id_guru = :id_guru
                       AND id_mapel = :id_mapel
                       AND id_semester = :id_semester
                       AND jenis_nilai = 'harian'
                       AND keterangan = :keterangan";
        $this->db->query($checkSql);
        $this->db->bind('id_siswa', $data['id_siswa']);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_mapel', $id_mapel);
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('keterangan', $keterangan);
        $existing = $this->db->single();

        if ($existing) {
            $updateSql = "UPDATE nilai_siswa
                          SET nilai = :nilai,
                              tanggal_input = :tanggal_input
                          WHERE id_nilai = :id_nilai";
            $this->db->query($updateSql);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
            $this->db->bind('id_nilai', $existing['id_nilai']);
        } else {
            $insertSql = "INSERT INTO nilai_siswa
                          (id_siswa, id_guru, id_mapel, id_semester, jenis_nilai, keterangan, nilai, tanggal_input)
                          VALUES
                          (:id_siswa, :id_guru, :id_mapel, :id_semester, :jenis_nilai, :keterangan, :nilai, :tanggal_input)";
            $this->db->query($insertSql);
            $this->db->bind('id_siswa', $data['id_siswa']);
            $this->db->bind('id_guru', $id_guru);
            $this->db->bind('id_mapel', $id_mapel);
            $this->db->bind('id_semester', $id_semester);
            $this->db->bind('jenis_nilai', 'harian');
            $this->db->bind('keterangan', $keterangan);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
        }

        return $this->db->execute();
    }

    /**
     * Simpan atau update nilai tengah semester
     */
    public function simpanNilaiTengahSemester($data) {
        // Check if nilai already exists
        $checkSql = "SELECT id_nilai FROM nilai_siswa 
                     WHERE id_siswa = :id_siswa 
                     AND id_guru = :id_guru 
                     AND id_mapel = :id_mapel 
                     AND id_semester = :id_semester 
                     AND jenis_nilai = 'sts'";
        
        $this->db->query($checkSql);
        $this->db->bind('id_siswa', $data['id_siswa']);
        $this->db->bind('id_guru', $data['id_guru']);
        $this->db->bind('id_mapel', $data['id_mapel']);
        $this->db->bind('id_semester', $data['id_semester']);
        $existing = $this->db->single();

        if ($existing) {
            // Update existing nilai
            $sql = "UPDATE nilai_siswa 
                    SET nilai = :nilai, tanggal_input = :tanggal_input
                    WHERE id_nilai = :id_nilai";
            
            $this->db->query($sql);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
            $this->db->bind('id_nilai', $existing['id_nilai']);
        } else {
            // Insert new nilai
            $sql = "INSERT INTO nilai_siswa (id_siswa, id_guru, id_mapel, id_semester, jenis_nilai, keterangan, nilai, tanggal_input) 
                    VALUES (:id_siswa, :id_guru, :id_mapel, :id_semester, :jenis_nilai, :keterangan, :nilai, :tanggal_input)";
            
            $this->db->query($sql);
            $this->db->bind('id_siswa', $data['id_siswa']);
            $this->db->bind('id_guru', $data['id_guru']);
            $this->db->bind('id_mapel', $data['id_mapel']);
            $this->db->bind('id_semester', $data['id_semester']);
            $this->db->bind('jenis_nilai', 'sts');
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
        }
        
        return $this->db->execute();
    }

    /**
     * Simpan atau update nilai akhir semester
     */
    public function simpanNilaiAkhirSemester($data) {
        // Check if nilai already exists
        $checkSql = "SELECT id_nilai FROM nilai_siswa 
                     WHERE id_siswa = :id_siswa 
                     AND id_guru = :id_guru 
                     AND id_mapel = :id_mapel 
                     AND id_semester = :id_semester 
                     AND jenis_nilai = 'sas'";
        
        $this->db->query($checkSql);
        $this->db->bind('id_siswa', $data['id_siswa']);
        $this->db->bind('id_guru', $data['id_guru']);
        $this->db->bind('id_mapel', $data['id_mapel']);
        $this->db->bind('id_semester', $data['id_semester']);
        $existing = $this->db->single();

        if ($existing) {
            // Update existing nilai
            $sql = "UPDATE nilai_siswa 
                    SET nilai = :nilai, tanggal_input = :tanggal_input
                    WHERE id_nilai = :id_nilai";
            
            $this->db->query($sql);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
            $this->db->bind('id_nilai', $existing['id_nilai']);
        } else {
            // Insert new nilai
            $sql = "INSERT INTO nilai_siswa (id_siswa, id_guru, id_mapel, id_semester, jenis_nilai, keterangan, nilai, tanggal_input) 
                    VALUES (:id_siswa, :id_guru, :id_mapel, :id_semester, :jenis_nilai, :keterangan, :nilai, :tanggal_input)";
            
            $this->db->query($sql);
            $this->db->bind('id_siswa', $data['id_siswa']);
            $this->db->bind('id_guru', $data['id_guru']);
            $this->db->bind('id_mapel', $data['id_mapel']);
            $this->db->bind('id_semester', $data['id_semester']);
            $this->db->bind('jenis_nilai', 'sas');
            $this->db->bind('keterangan', $data['keterangan']);
            $this->db->bind('nilai', $data['nilai']);
            $this->db->bind('tanggal_input', $data['tanggal_input']);
        }
        
        return $this->db->execute();
    }

    /**
     * Edit nilai siswa
     */
    public function editNilai($id_nilai, $nilai_baru) {
        $sql = "UPDATE nilai_siswa SET nilai = :nilai, tanggal_input = :tanggal_input WHERE id_nilai = :id_nilai";
        $this->db->query($sql);
        $this->db->bind('nilai', $nilai_baru);
        $this->db->bind('tanggal_input', date('Y-m-d'));
        $this->db->bind('id_nilai', $id_nilai);
        return $this->db->execute();
    }

    /**
     * Hapus nilai
     */
    public function hapusNilai($id_nilai) {
        $sql = "DELETE FROM nilai_siswa WHERE id_nilai = :id_nilai";
        $this->db->query($sql);
        $this->db->bind('id_nilai', $id_nilai);
        return $this->db->execute();
    }

    /**
     * Ambil nilai harian berdasarkan penugasan dan siswa
     */
    public function getNilaiHarianByMapelSiswa($id_penugasan, $id_siswa) {
    $sqlPenugasan = "SELECT id_guru, id_mapel, id_semester
        FROM penugasan
        WHERE id_penugasan = :id_penugasan";
    $this->db->query($sqlPenugasan);
    $this->db->bind('id_penugasan', $id_penugasan);
    $penugasan = $this->db->single();

    if (!$penugasan) {
        return [];
    }

                // Primary: by guru/mapel/semester
                $sql = "SELECT n.nilai
                                FROM nilai_siswa n
                                WHERE n.id_siswa = :id_siswa
                                    AND n.id_guru = :id_guru
                                    AND n.id_mapel = :id_mapel
                                    AND n.id_semester = :id_semester
                                    AND n.jenis_nilai = 'harian'";

                $this->db->query($sql);
                $this->db->bind('id_siswa', $id_siswa);
                $this->db->bind('id_guru', $penugasan['id_guru']);
                $this->db->bind('id_mapel', $penugasan['id_mapel']);
                $this->db->bind('id_semester', $penugasan['id_semester']);
                $rows = $this->db->resultSet();

                if (!empty($rows)) {
                        return $rows;
                }

                // Fallback: legacy rows keyed via keterangan = id_jurnal for this penugasan
                $sql2 = "SELECT n.nilai
                                 FROM nilai_siswa n
                                 JOIN jurnal j ON j.id_jurnal = CAST(n.keterangan AS UNSIGNED)
                                 WHERE j.id_penugasan = :id_penugasan
                                     AND n.id_siswa = :id_siswa
                                     AND n.jenis_nilai = 'harian'";
                $this->db->query($sql2);
                $this->db->bind('id_penugasan', $id_penugasan);
                $this->db->bind('id_siswa', $id_siswa);
                return $this->db->resultSet();
    }

    /**
     * Ambil nilai berdasarkan jenis (sts/sas)
     */
    public function getNilaiByJenis($id_siswa, $id_guru, $id_mapel, $id_semester, $jenis_nilai) {
        $sql = "SELECT nilai
                FROM nilai_siswa
                WHERE id_siswa = :id_siswa
                  AND id_guru = :id_guru
                  AND id_mapel = :id_mapel
                  AND id_semester = :id_semester
                  AND jenis_nilai = :jenis_nilai
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind('id_siswa', $id_siswa);
        $this->db->bind('id_guru', $id_guru);
        $this->db->bind('id_mapel', $id_mapel);
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('jenis_nilai', $jenis_nilai);
        return $this->db->single();
    }
}