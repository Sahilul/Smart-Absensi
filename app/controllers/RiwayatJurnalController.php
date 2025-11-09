<?php
// File: app/controllers/RiwayatJurnalController.php

class RiwayatJurnalController extends Controller
{
    private $data = [];

    public function __construct()
    {
        // Guard akses - Allow both 'guru' and 'wali_kelas' role
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? null, ['guru', 'wali_kelas'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        // Data umum
        $this->data['daftar_semester'] = $this->model('TahunPelajaran_model')->getAllSemester();
        $this->data['judul'] = 'Riwayat Jurnal';
    }

    /**
     * Helper method untuk menampilkan sidebar yang sesuai dengan role
     */
    private function loadSidebar()
    {
        $role = $_SESSION['role'] ?? 'guru';
        if ($role === 'wali_kelas') {
            $this->view('templates/sidebar_walikelas', $this->data);
        } else {
            $this->view('templates/sidebar_guru', $this->data);
        }
    }

    /**
     * Halaman utama riwayat jurnal per mapel-kelas
     */
    public function index()
    {
        $this->data['judul'] = 'Riwayat Jurnal Mengajar';

        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester_aktif) {
            error_log("RiwayatJurnalController::index() missing session keys");
            $this->data['jurnal_per_mapel'] = [];
            $this->renderView('riwayat_per_mapel_with_stats');
            return;
        }

        try {
            // Ambil data penugasan dengan statistik
            $this->data['jurnal_per_mapel'] = $this->getRiwayatPerMapelKelas($id_guru, $id_semester_aktif);

            error_log("DEBUG: Total mapel-kelas = " . count($this->data['jurnal_per_mapel']));
        } catch (Exception $e) {
            error_log("Error di riwayat(): " . $e->getMessage());
            $this->data['jurnal_per_mapel'] = [];
        }

        $this->renderView('riwayat_per_mapel_with_stats');
    }

    /**
     * Detail riwayat per penugasan spesifik
     */
    public function detail($id_penugasan)
    {
        $this->data['judul'] = 'Detail Riwayat Jurnal';

        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester_aktif || !$id_penugasan) {
            $this->data['detail_jurnal'] = [];
            $this->data['detail_absensi_siswa'] = [];
            $this->data['nama_mapel'] = 'Data Tidak Ditemukan';
            $this->renderView('detail_riwayat_with_stats');
            return;
        }

        try {
            // Ambil info penugasan
            $penugasanInfo = $this->getPenugasanInfo($id_penugasan);
            $this->data['nama_mapel'] = $penugasanInfo['nama_mapel'] ?? 'Mapel Tidak Ditemukan';
            $this->data['nama_kelas'] = $penugasanInfo['nama_kelas'] ?? 'Kelas Tidak Ditemukan';
            $this->data['info_penugasan'] = $penugasanInfo;

            // Ambil detail jurnal per penugasan
            $this->data['detail_jurnal'] = $this->getDetailJurnalByPenugasan($id_penugasan);
            
            // Ambil detail absensi siswa per penugasan
            $this->data['detail_absensi_siswa'] = $this->getDetailAbsensiByPenugasan($id_penugasan);

        } catch (Exception $e) {
            error_log("Error di detail(): " . $e->getMessage());
            $this->data['detail_jurnal'] = [];
            $this->data['detail_absensi_siswa'] = [];
            $this->data['nama_mapel'] = 'Error Loading Data';
        }

        $this->renderView('detail_riwayat_with_stats');
    }

    /**
     * Cetak laporan per penugasan (mapel-kelas spesifik)
     */
    public function cetak($id_penugasan)
    {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester || !$id_penugasan) {
            echo "Data tidak lengkap untuk mencetak laporan.";
            return;
        }

        try {
            // Ambil info penugasan
            $penugasanInfo = $this->getPenugasanInfo($id_penugasan);
            if (empty($penugasanInfo)) {
                echo "Penugasan tidak ditemukan.";
                return;
            }

            // Ambil data untuk cetak
            $this->data['meta'] = [
                'nama_mapel' => $penugasanInfo['nama_mapel'],
                'nama_kelas' => $penugasanInfo['nama_kelas'],
                'nama_guru' => $penugasanInfo['nama_guru'],
                'semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester',
                'tp' => $_SESSION['nama_tp_aktif'] ?? 'TP',
                'tanggal' => date('d F Y')
            ];

            $this->data['id_penugasan'] = $id_penugasan;
            $this->data['rekap_siswa'] = $this->getRekapSiswaByPenugasan($id_penugasan);
            $this->data['rekap_pertemuan'] = $this->getRekapPertemuanByPenugasan($id_penugasan);
            $this->data['total_siswa'] = count($this->data['rekap_siswa']);

            // Render PDF atau HTML
            $wantPdf = isset($_GET['pdf']) && $_GET['pdf'] == 1;
            $html = $this->renderViewToString('cetak_mapel_kelas', $this->data);

            if ($wantPdf) {
                $this->generatePDF($html, $penugasanInfo['nama_mapel'] . '_' . $penugasanInfo['nama_kelas'], $id_penugasan);
                return;
            }

            // Tampilkan halaman cetak HTML
            header('Content-Type: text/html; charset=utf-8');
            echo $html;

        } catch (Exception $e) {
            error_log("Error di cetak(): " . $e->getMessage());
            echo "Terjadi kesalahan saat mencetak laporan: " . $e->getMessage();
        }
    }

    /**
     * Helper: Ambil riwayat per mapel-kelas dengan statistik
     */
    private function getRiwayatPerMapelKelas($id_guru, $id_semester)
    {
        $db = new Database();
        
        // Query untuk mendapatkan penugasan dengan statistik
        $sql = "
            SELECT 
                p.id_penugasan,
                m.id_mapel,
                m.nama_mapel,
                k.nama_kelas,
                COUNT(DISTINCT j.id_jurnal) as total_pertemuan,
                COUNT(DISTINCT siswa.id_siswa) as total_siswa,
                SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as total_alpha,
                COUNT(a.id_absensi) as total_absensi_records
            FROM penugasan p
            JOIN mapel m ON p.id_mapel = m.id_mapel
            JOIN kelas k ON p.id_kelas = k.id_kelas
            LEFT JOIN jurnal j ON p.id_penugasan = j.id_penugasan
            LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
            LEFT JOIN keanggotaan_kelas kk ON k.id_kelas = kk.id_kelas
            LEFT JOIN siswa ON kk.id_siswa = siswa.id_siswa
            WHERE p.id_guru = :id_guru 
              AND p.id_semester = :id_semester
            GROUP BY p.id_penugasan, m.id_mapel, m.nama_mapel, k.nama_kelas
            ORDER BY m.nama_mapel
        ";

        $db->query($sql);
        $db->bind('id_guru', $id_guru);
        $db->bind('id_semester', $id_semester);
        $penugasan_list = $db->resultSet();

        $result = [];
        foreach ($penugasan_list as $penugasan) {
            $total_absensi = (int)($penugasan['total_absensi_records'] ?? 0);
            $total_hadir = (int)($penugasan['total_hadir'] ?? 0);
            $persentase = $total_absensi > 0 ? round(($total_hadir / $total_absensi) * 100, 1) : 0;

            // Ambil pertemuan untuk penugasan ini
            $pertemuan = $this->getPertemuanByPenugasan($penugasan['id_penugasan']);

            $result[] = [
                'id_penugasan' => $penugasan['id_penugasan'],
                'id_mapel' => $penugasan['id_mapel'],
                'nama_mapel' => $penugasan['nama_mapel'],
                'nama_kelas' => $penugasan['nama_kelas'],
                'pertemuan' => $pertemuan,
                'statistik' => [
                    'total_pertemuan' => (int)($penugasan['total_pertemuan'] ?? 0),
                    'total_siswa' => (int)($penugasan['total_siswa'] ?? 0),
                    'total_hadir' => $total_hadir,
                    'total_izin' => (int)($penugasan['total_izin'] ?? 0),
                    'total_sakit' => (int)($penugasan['total_sakit'] ?? 0),
                    'total_alpha' => (int)($penugasan['total_alpha'] ?? 0),
                    'total_absensi_records' => $total_absensi,
                    'persentase_kehadiran' => $persentase,
                ],
                'chart_data' => [
                    'hadir' => $total_hadir,
                    'izin' => (int)($penugasan['total_izin'] ?? 0),
                    'sakit' => (int)($penugasan['total_sakit'] ?? 0),
                    'alpha' => (int)($penugasan['total_alpha'] ?? 0),
                ]
            ];
        }

        return $result;
    }

    /**
     * Helper: Ambil pertemuan by penugasan
     */
    private function getPertemuanByPenugasan($id_penugasan)
    {
        $db = new Database();
        $sql = "
            SELECT pertemuan_ke, tanggal, topik_materi 
            FROM jurnal 
            WHERE id_penugasan = :id_penugasan 
            ORDER BY tanggal DESC, pertemuan_ke DESC
        ";
        
        $db->query($sql);
        $db->bind('id_penugasan', $id_penugasan);
        return $db->resultSet();
    }

    /**
     * Helper: Ambil info penugasan
     */
    private function getPenugasanInfo($id_penugasan)
    {
        $db = new Database();
        $sql = "
            SELECT 
                p.id_penugasan,
                m.nama_mapel, 
                k.nama_kelas, 
                g.nama_guru
            FROM penugasan p
            JOIN mapel m ON p.id_mapel = m.id_mapel
            JOIN kelas k ON p.id_kelas = k.id_kelas  
            JOIN guru g ON p.id_guru = g.id_guru
            WHERE p.id_penugasan = :id_penugasan
            LIMIT 1
        ";
        
        $db->query($sql);
        $db->bind('id_penugasan', $id_penugasan);
        return $db->single() ?: [];
    }

    /**
     * Helper: Ambil detail jurnal by penugasan
     */
    private function getDetailJurnalByPenugasan($id_penugasan)
    {
        $db = new Database();
        $sql = "
            SELECT 
                j.*,
                m.nama_mapel,
                k.nama_kelas
            FROM jurnal j
            JOIN penugasan p ON j.id_penugasan = p.id_penugasan
            JOIN mapel m ON p.id_mapel = m.id_mapel
            JOIN kelas k ON p.id_kelas = k.id_kelas
            WHERE j.id_penugasan = :id_penugasan
            ORDER BY j.tanggal DESC, j.pertemuan_ke DESC
        ";

        $db->query($sql);
        $db->bind('id_penugasan', $id_penugasan);
        return $db->resultSet();
    }

    /**
     * Helper: Ambil detail absensi siswa by penugasan
     */
    private function getDetailAbsensiByPenugasan($id_penugasan)
    {
        $db = new Database();
        $sql = "
            SELECT 
                s.id_siswa,
                s.nama_siswa,
                s.nisn,
                COUNT(DISTINCT j.id_jurnal) as total_pertemuan,
                SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.status_kehadiran = 'A' THEN 1 ELSE 0 END) as alpha,
                COUNT(a.id_absensi) as total_absensi
            FROM penugasan p
            JOIN kelas k ON p.id_kelas = k.id_kelas
            JOIN keanggotaan_kelas kk ON k.id_kelas = kk.id_kelas
            JOIN siswa s ON kk.id_siswa = s.id_siswa
            LEFT JOIN jurnal j ON p.id_penugasan = j.id_penugasan
            LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal AND s.id_siswa = a.id_siswa
            WHERE p.id_penugasan = :id_penugasan
            GROUP BY s.id_siswa, s.nama_siswa, s.nisn
            ORDER BY s.nama_siswa ASC
        ";

        $db->query($sql);
        $db->bind('id_penugasan', $id_penugasan);
        return $db->resultSet();
    }

    /**
     * Helper: Ambil rekap siswa untuk cetak
     */
    private function getRekapSiswaByPenugasan($id_penugasan)
    {
        return $this->getDetailAbsensiByPenugasan($id_penugasan);
    }

    /**
     * Helper: Ambil rekap pertemuan untuk cetak
     */
    private function getRekapPertemuanByPenugasan($id_penugasan)
    {
        $db = new Database();
        $sql = "
            SELECT 
                j.id_jurnal, 
                j.tanggal, 
                j.pertemuan_ke, 
                j.topik_materi,
                SUM(CASE WHEN a.status_kehadiran='H' THEN 1 ELSE 0 END) AS hadir,
                SUM(CASE WHEN a.status_kehadiran='I' THEN 1 ELSE 0 END) AS izin,
                SUM(CASE WHEN a.status_kehadiran='S' THEN 1 ELSE 0 END) AS sakit,
                SUM(CASE WHEN a.status_kehadiran='A' THEN 1 ELSE 0 END) AS alpha,
                COUNT(a.id_absensi) AS total
            FROM jurnal j
            LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
            WHERE j.id_penugasan = :id_penugasan
            GROUP BY j.id_jurnal, j.tanggal, j.pertemuan_ke, j.topik_materi
            ORDER BY j.tanggal ASC, j.pertemuan_ke ASC
        ";

        $db->query($sql);
        $db->bind('id_penugasan', $id_penugasan);
        return $db->resultSet();
    }

    /**
     * Helper: render view dengan layout
     */
    private function renderView($viewName)
    {
        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/' . $viewName, $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Helper: render view ke string (untuk PDF)
     */
    private function renderViewToString($viewName, $data)
    {
        extract($data);
        ob_start();
        $viewPath = __DIR__ . "/../views/guru/$viewName.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new Exception("View file tidak ditemukan: $viewPath");
        }
        return ob_get_clean();
    }

    /**
     * Helper: generate PDF
     */
    private function generatePDF($html, $filename, $id_penugasan = null)
    {
        $dompdfPath = __DIR__ . '/../core/dompdf/autoload.inc.php';
        
        if (!file_exists($dompdfPath)) {
            header('Content-Type: text/html; charset=utf-8');
            echo "<p>Library Dompdf tidak tersedia. Menampilkan preview HTML:</p>";
            echo $html;
            return;
        }

        require_once $dompdfPath;
        
        if (!class_exists('\\Dompdf\\Dompdf')) {
            header('Content-Type: text/html; charset=utf-8');
            echo "<p>Error: Class Dompdf tidak ditemukan.</p>";
            echo $html;
            return;
        }

        try {
            $dompdf = new \Dompdf\Dompdf([
                'isRemoteEnabled' => true, 
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
            
            // Add QR code for document validation
            if ($id_penugasan) {
                require_once APPROOT . '/app/core/PDFQRHelper.php';
                $html = PDFQRHelper::addQRToPDF($html, 'jurnal_mapel', $id_penugasan);
            }
            
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $clean_filename = preg_replace('/[^A-Za-z0-9_-]/', '_', $filename) . '_' . date('Y-m-d') . '.pdf';
            $dompdf->stream($clean_filename, ['Attachment' => true]);
            
        } catch (Exception $e) {
            error_log("PDF Generation Error: " . $e->getMessage());
            header('Content-Type: text/html; charset=utf-8');
            echo "<p>Error generating PDF: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo $html;
        }
    }
}
?>
