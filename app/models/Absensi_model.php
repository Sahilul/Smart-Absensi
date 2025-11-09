<?php

// File: app/models/Absensi_model.php - VERSI DIPERBAIKI
class Absensi_model {
    private $db;

    public function __construct()
    {
        require_once APPROOT . '/app/core/Database.php';
        $this->db = new Database;
    }

    // PERBAIKAN 1: Fix binding parameter yang salah
    public function getSiswaByPenugasan($id_penugasan)
    {
        $this->db->query('SELECT siswa.id_siswa, siswa.nisn, siswa.nama_siswa
                         FROM siswa
                         JOIN keanggotaan_kelas ON siswa.id_siswa = keanggotaan_kelas.id_siswa
                         WHERE keanggotaan_kelas.id_kelas = (SELECT id_kelas FROM penugasan WHERE id_penugasan = :id_penugasan)
                         AND keanggotaan_kelas.id_tp = (SELECT tp.id_tp FROM penugasan JOIN semester ON penugasan.id_semester = semester.id_semester JOIN tp ON semester.id_tp = tp.id_tp WHERE penugasan.id_penugasan = :id_penugasan_tp)
                         ORDER BY siswa.nama_siswa ASC');
        
        // FIX: Hapus titik dua di awal parameter
        $this->db->bind('id_penugasan', $id_penugasan);
        $this->db->bind('id_penugasan_tp', $id_penugasan);
        return $this->db->resultSet();
    }

    // PERBAIKAN 2: Tambah method yang hilang - dipanggil di GuruController line 74
    public function getSiswaDanAbsensiByJurnal($id_jurnal)
    {
        $this->db->query('SELECT 
                            s.id_siswa, 
                            s.nisn, 
                            s.nama_siswa,
                            a.status_kehadiran,
                            a.keterangan
                         FROM siswa s
                         JOIN keanggotaan_kelas kk ON s.id_siswa = kk.id_siswa
                         JOIN penugasan p ON kk.id_kelas = p.id_kelas
                         JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                         LEFT JOIN absensi a ON s.id_siswa = a.id_siswa AND a.id_jurnal = :id_jurnal
                         WHERE j.id_jurnal = :id_jurnal_check
                         AND kk.id_tp = (SELECT tp.id_tp FROM semester JOIN tp ON semester.id_tp = tp.id_tp WHERE semester.id_semester = p.id_semester)
                         ORDER BY s.nama_siswa ASC');
        
        $this->db->bind('id_jurnal', $id_jurnal);
        $this->db->bind('id_jurnal_check', $id_jurnal);
        return $this->db->resultSet();
    }

    // PERBAIKAN 3: Upgrade method simpanAbsensi untuk handle UPDATE jika sudah ada
    public function simpanAbsensi($data)
    {
        $id_jurnal = $data['id_jurnal'];
        $absensi = $data['absensi'];
        $keterangan = $data['keterangan'] ?? [];

        $rowCount = 0;
        foreach ($absensi as $id_siswa => $status) {
            // Cek apakah sudah ada record absensi untuk siswa ini di jurnal ini
            $this->db->query('SELECT COUNT(*) as total FROM absensi WHERE id_jurnal = :id_jurnal AND id_siswa = :id_siswa');
            $this->db->bind('id_jurnal', $id_jurnal);
            $this->db->bind('id_siswa', $id_siswa);
            $existing = $this->db->single();

            if ($existing['total'] > 0) {
                // UPDATE jika sudah ada
                $query = "UPDATE absensi SET status_kehadiran = :status_kehadiran, keterangan = :keterangan
                          WHERE id_jurnal = :id_jurnal AND id_siswa = :id_siswa";
                $this->db->query($query);
                $this->db->bind('status_kehadiran', $status);
                $this->db->bind('keterangan', $keterangan[$id_siswa] ?? '');
                $this->db->bind('id_jurnal', $id_jurnal);
                $this->db->bind('id_siswa', $id_siswa);
                $this->db->execute();
                $rowCount += $this->db->rowCount();
            } else {
                // INSERT jika belum ada
                $query = "INSERT INTO absensi (id_jurnal, id_siswa, status_kehadiran, keterangan)
                          VALUES (:id_jurnal, :id_siswa, :status_kehadiran, :keterangan)";
                $this->db->query($query);
                $this->db->bind('id_jurnal', $id_jurnal);
                $this->db->bind('id_siswa', $id_siswa);
                $this->db->bind('status_kehadiran', $status);
                $this->db->bind('keterangan', $keterangan[$id_siswa] ?? '');
                $this->db->execute();
                $rowCount += $this->db->rowCount();
            }
        }
        return $rowCount;
    }

    public function getAbsensiByJurnalId($id_jurnal)
    {
        $this->db->query('SELECT absensi.*, siswa.nisn, siswa.nama_siswa 
                         FROM absensi 
                         JOIN siswa ON absensi.id_siswa = siswa.id_siswa
                         WHERE absensi.id_jurnal = :id_jurnal
                         ORDER BY siswa.nama_siswa ASC');
        $this->db->bind('id_jurnal', $id_jurnal);
        return $this->db->resultSet();
    }

    public function getRekapAbsensiSiswa($id_siswa, $id_semester)
    {
        $this->db->query('SELECT status_kehadiran, COUNT(*) as total 
                         FROM absensi
                         JOIN jurnal ON absensi.id_jurnal = jurnal.id_jurnal
                         JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                         WHERE absensi.id_siswa = :id_siswa AND penugasan.id_semester = :id_semester
                         GROUP BY status_kehadiran');
        $this->db->bind('id_siswa', $id_siswa);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    public function getAbsensiHarianSiswa($id_siswa, $id_semester)
    {
        $this->db->query('SELECT absensi.*, jurnal.tanggal, jurnal.pertemuan_ke, mapel.nama_mapel, guru.nama_guru
                         FROM absensi
                         JOIN jurnal ON absensi.id_jurnal = jurnal.id_jurnal
                         JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                         JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                         JOIN guru ON penugasan.id_guru = guru.id_guru
                         WHERE absensi.id_siswa = :id_siswa AND penugasan.id_semester = :id_semester
                         ORDER BY jurnal.tanggal DESC');
        $this->db->bind('id_siswa', $id_siswa);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    public function getRekapAbsensiSiswaPerMapel($id_siswa, $id_semester)
    {
        $this->db->query('SELECT 
                            mapel.nama_mapel,
                            SUM(CASE WHEN absensi.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                            SUM(CASE WHEN absensi.status_kehadiran = "I" THEN 1 ELSE 0 END) as izin,
                            SUM(CASE WHEN absensi.status_kehadiran = "S" THEN 1 ELSE 0 END) as sakit,
                            SUM(CASE WHEN absensi.status_kehadiran = "A" THEN 1 ELSE 0 END) as alfa
                         FROM absensi
                         JOIN jurnal ON absensi.id_jurnal = jurnal.id_jurnal
                         JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                         JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                         WHERE absensi.id_siswa = :id_siswa AND penugasan.id_semester = :id_semester
                         GROUP BY mapel.nama_mapel
                         ORDER BY mapel.nama_mapel ASC');
        $this->db->bind('id_siswa', $id_siswa);
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

    public function getAllAbsensiForAdmin($id_semester)
    {
        $this->db->query('SELECT 
                            absensi.status_kehadiran,
                            jurnal.tanggal,
                            siswa.nama_siswa,
                            kelas.nama_kelas,
                            mapel.nama_mapel,
                            guru.nama_guru
                         FROM absensi
                         JOIN jurnal ON absensi.id_jurnal = jurnal.id_jurnal
                         JOIN siswa ON absensi.id_siswa = siswa.id_siswa
                         JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                         JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                         JOIN mapel ON penugasan.id_mapel = mapel.id_mapel
                         JOIN guru ON penugasan.id_guru = guru.id_guru
                         WHERE penugasan.id_semester = :id_semester
                         ORDER BY jurnal.tanggal DESC, siswa.nama_siswa ASC');
        $this->db->bind('id_semester', $id_semester);
        return $this->db->resultSet();
    }

        /**
         * Ambil absensi harian per kelas pada tanggal tertentu (untuk wali kelas)
         * Mengembalikan list baris: satu baris per siswa-mapel pada tanggal itu
         * Kolom: id_siswa, nama_siswa, nama_mapel, status_kehadiran, keterangan, nama_guru, pertemuan_ke
         */
        public function getAbsensiHarianByTanggalKelas($id_kelas, $id_semester, $tanggal)
        {
                $sql = "SELECT 
                                        s.id_siswa,
                                        s.nama_siswa,
                                        m.nama_mapel,
                                        a.status_kehadiran,
                                        a.keterangan,
                                        g.nama_guru,
                                        j.pertemuan_ke
                                FROM absensi a
                                JOIN siswa s ON a.id_siswa = s.id_siswa
                                JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                                JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                                JOIN mapel m ON p.id_mapel = m.id_mapel
                                JOIN guru g ON p.id_guru = g.id_guru
                                WHERE p.id_kelas = :id_kelas
                                    AND p.id_semester = :id_semester
                                    AND j.tanggal = :tanggal
                                ORDER BY s.nama_siswa ASC, m.nama_mapel ASC";

                $this->db->query($sql);
                $this->db->bind('id_kelas', $id_kelas);
                $this->db->bind('id_semester', $id_semester);
                $this->db->bind('tanggal', $tanggal);
                return $this->db->resultSet();
        }

    public function getRekapAbsensi($id_kelas, $id_semester, $id_mapel = null, $tgl_mulai = null, $tgl_selesai = null)
    {
        $sql = "SELECT 
                    siswa.nisn,
                    siswa.nama_siswa,
                    SUM(CASE WHEN absensi.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN absensi.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN absensi.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN absensi.status_kehadiran = 'A' THEN 1 ELSE 0 END) as alfa
                FROM absensi
                JOIN jurnal ON absensi.id_jurnal = jurnal.id_jurnal
                JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                JOIN siswa ON absensi.id_siswa = siswa.id_siswa
                JOIN kelas ON penugasan.id_kelas = kelas.id_kelas
                WHERE penugasan.id_kelas = :id_kelas
                  AND penugasan.id_semester = :id_semester";

        if (!empty($id_mapel)) {
            $sql .= " AND penugasan.id_mapel = :id_mapel";
        }

        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $sql .= " AND jurnal.tanggal BETWEEN :tgl_mulai AND :tgl_selesai";
        }

        $sql .= " GROUP BY siswa.id_siswa
                  ORDER BY siswa.nama_siswa ASC";

        $this->db->query($sql);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_semester', $id_semester);

        if (!empty($id_mapel)) {
            $this->db->bind('id_mapel', $id_mapel);
        }

        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $this->db->bind('tgl_mulai', $tgl_mulai);
            $this->db->bind('tgl_selesai', $tgl_selesai);
        }

        return $this->db->resultSet();
    }

    /**
     * Rekap performa harian per siswa (per hari) untuk satu kelas dalam rentang tanggal
     * Aturan prioritas status harian: Sakit > Izin > Alfa > Hadir
     * Jika dalam satu hari ada Sakit dan Alfa, yang dihitung Sakit (Alpha kalah)
     */
    public function getPerformaHarianKelas($id_kelas, $id_semester, $start_date, $end_date)
    {
        $sql = "SELECT 
                    d.id_siswa,
                    d.nisn,
                    d.nama_siswa,
                    d.nama_kelas,
                    COUNT(*) AS total_hari,
                    SUM(CASE WHEN d.daily_status = 'H' THEN 1 ELSE 0 END) AS hadir,
                    SUM(CASE WHEN d.daily_status = 'S' THEN 1 ELSE 0 END) AS sakit,
                    SUM(CASE WHEN d.daily_status = 'I' THEN 1 ELSE 0 END) AS izin,
                    SUM(CASE WHEN d.daily_status = 'A' THEN 1 ELSE 0 END) AS alfa
                FROM (
                    SELECT 
                        s.id_siswa,
                        s.nisn,
                        s.nama_siswa,
                        k.nama_kelas,
                        j.tanggal,
                        CASE 
                            WHEN MAX(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) = 1 THEN 'S'
                            WHEN MAX(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) = 1 THEN 'I'
                            WHEN MAX(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) = 1 THEN 'A'
                            ELSE 'H'
                        END AS daily_status
                    FROM absensi a
                    JOIN siswa s ON a.id_siswa = s.id_siswa
                    JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    WHERE p.id_kelas = :id_kelas
                      AND p.id_semester = :id_semester
                      AND j.tanggal BETWEEN :start_date AND :end_date
                    GROUP BY s.id_siswa, s.nisn, s.nama_siswa, k.nama_kelas, j.tanggal
                ) d
                GROUP BY d.id_siswa, d.nisn, d.nama_siswa, d.nama_kelas
                ORDER BY d.nama_siswa ASC";

        $this->db->query($sql);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('start_date', $start_date);
        $this->db->bind('end_date', $end_date);
        $rows = $this->db->resultSet();

        // Tambahkan persentase hadir (berdasarkan hari, bukan pertemuan)
        foreach ($rows as &$r) {
            $total = max(0, (int)($r['total_hari'] ?? 0));
            $hadir = (int)($r['hadir'] ?? 0);
            $r['persentase_hadir'] = $total > 0 ? round(($hadir / $total) * 100, 1) : 0.0;
        }
        unset($r);

        return $rows;
    }

    /**
     * Detail harian per tanggal untuk satu siswa dalam rentang tanggal pada kelas dan semester tertentu.
     * Mengembalikan daftar tanggal dengan status harian (prioritas: S > I > A > H).
     */
    public function getDailyStatusBySiswa($id_kelas, $id_semester, $id_siswa, $start_date, $end_date)
    {
        $sql = "SELECT 
                    d.tanggal,
                    d.daily_status
                FROM (
                    SELECT 
                        j.tanggal,
                        CASE 
                            WHEN MAX(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) = 1 THEN 'S'
                            WHEN MAX(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) = 1 THEN 'I'
                            WHEN MAX(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) = 1 THEN 'A'
                            ELSE 'H'
                        END AS daily_status
                    FROM absensi a
                    JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                    JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                    WHERE p.id_kelas = :id_kelas
                      AND p.id_semester = :id_semester
                      AND a.id_siswa = :id_siswa
                      AND j.tanggal BETWEEN :start_date AND :end_date
                    GROUP BY j.tanggal
                ) d
                ORDER BY d.tanggal ASC";

        $this->db->query($sql);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_semester', $id_semester);
        $this->db->bind('id_siswa', $id_siswa);
        $this->db->bind('start_date', $start_date);
        $this->db->bind('end_date', $end_date);
        return $this->db->resultSet();
    }

    /**
     * Mendapatkan rekap absensi untuk kelas tertentu
     * Digunakan untuk dashboard wali kelas
     * @param int $id_kelas ID Kelas
     * @param int $id_semester ID Semester
     * @return array Total hadir, sakit, izin, alfa
     */
    public function getRekapAbsensiKelas($id_kelas, $id_semester)
    {
        $sql = "SELECT 
                    COUNT(DISTINCT absensi.id_siswa) as total_siswa,
                    SUM(CASE WHEN absensi.status_kehadiran = 'H' THEN 1 ELSE 0 END) as total_hadir,
                    SUM(CASE WHEN absensi.status_kehadiran = 'I' THEN 1 ELSE 0 END) as total_izin,
                    SUM(CASE WHEN absensi.status_kehadiran = 'S' THEN 1 ELSE 0 END) as total_sakit,
                    SUM(CASE WHEN absensi.status_kehadiran = 'A' THEN 1 ELSE 0 END) as total_alfa,
                    COUNT(absensi.id_absensi) as total_records
                FROM absensi
                JOIN jurnal ON absensi.id_jurnal = jurnal.id_jurnal
                JOIN penugasan ON jurnal.id_penugasan = penugasan.id_penugasan
                WHERE penugasan.id_kelas = :id_kelas
                  AND penugasan.id_semester = :id_semester";

        $this->db->query($sql);
        $this->db->bind('id_kelas', $id_kelas);
        $this->db->bind('id_semester', $id_semester);
        
        $result = $this->db->single();
        
        // Return default jika tidak ada data
        if (!$result || $result['total_records'] == 0) {
            return [
                'total_siswa' => 0,
                'total_hadir' => 0,
                'total_izin' => 0,
                'total_sakit' => 0,
                'total_alfa' => 0,
                'total_records' => 0,
                'persentase_hadir' => 0
            ];
        }
        
        // Hitung persentase kehadiran
        $result['persentase_hadir'] = $result['total_records'] > 0 
            ? round(($result['total_hadir'] / $result['total_records']) * 100, 1) 
            : 0;
        
        return $result;
    }
}
