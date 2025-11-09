<?php
// File: app/controllers/GuruController.php

class GuruController extends Controller
{
    private $data = [];

    public function __construct()
    {
        // Guard akses - Allow both 'guru' and 'wali_kelas' role
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? null, ['guru', 'wali_kelas'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        // Clear old flash messages yang mungkin tertinggal dari login
        if (isset($_SESSION['flash']) && strpos($_SESSION['flash']['pesan'] ?? '', 'Role tidak dikenal') !== false) {
            unset($_SESSION['flash']);
        }

        // Data umum
        $this->data['daftar_semester'] = $this->model('TahunPelajaran_model')->getAllSemester();
        $this->data['judul'] = 'Guru';
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
     * Default -> lempar ke dashboard
     */
    public function index()
    {
        error_log("GuruController::index() dipanggil");
        $this->dashboard();
    }

    /**
     * Dashboard - Dengan statistik dan daftar kelas mengajar
     */
    public function dashboard()
    {
        error_log("GuruController::dashboard() dipanggil");
        $this->data['judul'] = 'Dashboard Guru';

        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        // Ambil jadwal mengajar
        $this->data['jadwal_mengajar'] = [];
        if ($id_guru && $id_semester_aktif) {
            $jadwal = $this->model('Penugasan_model')->getPenugasanByGuru($id_guru, $id_semester_aktif);
            
            // Tambahkan info jumlah nilai STS dan SAS per penugasan
            $nilaiModel = $this->model('Nilai_model');
            foreach ($jadwal as &$item) {
                $id_penugasan = $item['id_penugasan'];
                $item['jumlah_nilai_sts'] = $nilaiModel->countNilaiByPenugasanAndJenis($id_penugasan, 'sts');
                $item['jumlah_nilai_sas'] = $nilaiModel->countNilaiByPenugasanAndJenis($id_penugasan, 'sas');
            }
            
            $this->data['jadwal_mengajar'] = $jadwal;
        }

        // Hitung statistik
        $this->data['total_penugasan'] = $this->getTotalPenugasan($id_guru, $id_semester_aktif);
        $this->data['total_hari_mengajar'] = $this->getTotalHariMengajar($id_guru, $id_semester_aktif);
        $this->data['kelas_mapel_info'] = $this->getKelasMapelInfo($id_guru, $id_semester_aktif);

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/dashboard', $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Edit Profil (Guru & Wali Kelas) - username/password
     */
    public function profil()
    {
        $this->data['judul'] = 'Profil';
        $id_guru = $_SESSION['id_ref'] ?? 0;
        if (!$id_guru) { header('Location: ' . BASEURL . '/guru'); exit; }

        $guru = $this->model('Guru_model')->getGuruById($id_guru);
        $this->data['guru'] = $guru;

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/profil', $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Simpan perubahan profil (POST)
     */
    public function simpanProfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/guru/profil');
            exit;
        }
        $id_guru = $_SESSION['id_ref'] ?? 0;
        if (!$id_guru) { header('Location: ' . BASEURL . '/guru/profil'); exit; }

        $nama_guru = trim($_POST['nama_guru'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($nama_guru)) {
            Flasher::setFlash('Gagal', 'Nama tidak boleh kosong', 'danger');
            header('Location: ' . BASEURL . '/guru/profil');
            exit;
        }

        $updated = $this->model('Guru_model')->updateProfilGuru($id_guru, [
            'nama_guru' => $nama_guru,
            'email' => $email,
        ]);

        if ($updated !== false) {
            // refresh nama di session untuk header
            $_SESSION['user_nama_lengkap'] = $nama_guru;
            Flasher::setFlash('Berhasil', $updated > 0 ? 'Profil diperbarui' : 'Tidak ada perubahan', 'success');
        } else {
            Flasher::setFlash('Gagal', 'Terjadi kesalahan saat menyimpan', 'danger');
        }

        header('Location: ' . BASEURL . '/guru/profil');
        exit;
    }

    /**
     * Ganti sandi (password only)
     */
    public function gantiSandi()
    {
        $this->data['judul'] = 'Ganti Sandi';
        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/ganti_sandi', $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Simpan sandi baru (POST) - username tidak boleh diganti di sini
     */
    public function simpanSandi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/guru/gantiSandi');
            exit;
        }

        $password = trim($_POST['password'] ?? '');
        $password2 = trim($_POST['password2'] ?? '');
        if (empty($password) || empty($password2)) {
            Flasher::setFlash('Gagal', 'Password dan konfirmasi wajib diisi', 'danger');
            header('Location: ' . BASEURL . '/guru/gantiSandi');
            exit;
        }
        if ($password !== $password2) {
            Flasher::setFlash('Gagal', 'Konfirmasi password tidak cocok', 'danger');
            header('Location: ' . BASEURL . '/guru/gantiSandi');
            exit;
        }

        $id_ref = $_SESSION['id_ref'] ?? 0;
        $role = $_SESSION['role'] ?? '';
        $ok = $this->model('User_model')->updatePassword($id_ref, $role, $password);
        if ($ok) {
            Flasher::setFlash('Berhasil', 'Password berhasil diperbarui', 'success');
        } else {
            Flasher::setFlash('Gagal', 'Tidak dapat memperbarui password', 'danger');
        }
        header('Location: ' . BASEURL . '/guru/gantiSandi');
        exit;
    }

    /**
     * Riwayat Jurnal Mengajar - REDIRECT ke RiwayatJurnalController
     */
    public function riwayat()
    {
        // Redirect ke controller yang benar
        header('Location: ' . BASEURL . '/riwayatJurnal');
        exit;
    }

    /**
     * Alias untuk test routing - REDIRECT
     */
    public function history()
    {
        error_log("GuruController::history() dipanggil - redirecting");
        header('Location: ' . BASEURL . '/riwayatJurnal');
        exit;
    }

    /**
     * Alias sederhana untuk test routing - REDIRECT
     */
    public function list()
    {
        error_log("GuruController::list() dipanggil - redirecting");
        header('Location: ' . BASEURL . '/riwayatJurnal');
        exit;
    }

    /**
     * Detail Riwayat per Mapel - REDIRECT ke RiwayatJurnalController dengan id_penugasan
     */
    public function detailRiwayat($id_mapel)
    {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester || !$id_mapel) {
            header('Location: ' . BASEURL . '/riwayatJurnal');
            exit;
        }

        try {
            // Cari id_penugasan berdasarkan guru, semester, dan mapel
            $db = new Database();
            $db->query("
                SELECT p.id_penugasan 
                FROM penugasan p
                WHERE p.id_guru = :g AND p.id_semester = :s AND p.id_mapel = :m
                ORDER BY p.id_penugasan DESC LIMIT 1
            ");
            $db->bind('g', $id_guru);
            $db->bind('s', $id_semester);
            $db->bind('m', (int)$id_mapel);
            $row = $db->single();

            if ($row && !empty($row['id_penugasan'])) {
                // Redirect ke RiwayatJurnalController::detail dengan id_penugasan
                header('Location: ' . BASEURL . '/riwayatJurnal/detail/' . $row['id_penugasan']);
                exit;
            }
        } catch (Exception $e) {
            error_log("Error finding penugasan: " . $e->getMessage());
        }

        // Fallback: kembali ke daftar riwayat
        header('Location: ' . BASEURL . '/riwayatJurnal');
        exit;
    }

    /**
     * Cetak per Mapel - REDIRECT ke RiwayatJurnalController::cetak dengan id_penugasan
     */
    public function cetakMapel($id_mapel)
    {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester || !$id_mapel) {
            header('Location: ' . BASEURL . '/riwayatJurnal');
            exit;
        }

        try {
            // Cari id_penugasan berdasarkan guru, semester, dan mapel
            $db = new Database();
            $db->query("
                SELECT p.id_penugasan 
                FROM penugasan p
                WHERE p.id_guru = :g AND p.id_semester = :s AND p.id_mapel = :m
                ORDER BY p.id_penugasan DESC LIMIT 1
            ");
            $db->bind('g', $id_guru);
            $db->bind('s', $id_semester);
            $db->bind('m', (int)$id_mapel);
            $row = $db->single();

            if ($row && !empty($row['id_penugasan'])) {
                // Preserve PDF parameter jika ada
                $pdfParam = isset($_GET['pdf']) && $_GET['pdf'] == '1' ? '?pdf=1' : '';
                
                // Redirect ke RiwayatJurnalController::cetak dengan id_penugasan
                header('Location: ' . BASEURL . '/riwayatJurnal/cetak/' . $row['id_penugasan'] . $pdfParam);
                exit;
            }
        } catch (Exception $e) {
            error_log("Error finding penugasan for cetak: " . $e->getMessage());
        }

        // Fallback: kembali ke daftar riwayat
        header('Location: ' . BASEURL . '/riwayatJurnal');
        exit;
    }

    /**
     * Halaman test routing
     */
    public function test()
    {
        error_log("GuruController::test() dipanggil");
        echo "<h1>Method test() berhasil dipanggil!</h1>";
        echo "<p>Routing berfungsi dengan baik.</p>";
        echo "<a href='" . BASEURL . "/guru/dashboard'>Kembali ke Dashboard</a>";
    }

    /**
     * Input Jurnal
     */
    public function jurnal()
    {
        $this->data['judul'] = 'Input Jurnal';

        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        $this->data['jadwal_mengajar'] = [];
        if ($id_guru && $id_semester_aktif) {
            $this->data['jadwal_mengajar'] = $this->model('Penugasan_model')->getPenugasanByGuru($id_guru, $id_semester_aktif);
        }

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/jurnal', $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Tambah Jurnal
     */
    public function tambahJurnal($id_penugasan)
    {
        $this->data['judul'] = 'Tambah Jurnal Mengajar';
        $this->data['id_penugasan'] = $id_penugasan;
        $this->data['pertemuan_selanjutnya'] =
            (int)$this->model('Jurnal_model')->getPertemuanTerakhir($id_penugasan) + 1;

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/tambah_jurnal', $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Proses Tambah Jurnal
     */
    public function prosesTambahJurnal()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idBaru = $this->model('Jurnal_model')->tambahDataJurnal($_POST);
            if ($idBaru) {
                header('Location: ' . BASEURL . '/guru/absensi/' . $idBaru);
                exit;
            }
        }
        // fallback kembali ke jurnal
        header('Location: ' . BASEURL . '/guru/jurnal');
        exit;
    }

    /**
     * Input Absensi untuk 1 jurnal
     * Jika dipanggil tanpa parameter, redirect ke halaman jurnal
     */
    public function absensi($id_jurnal = null)
    {
        // Jika tidak ada id_jurnal, redirect ke halaman jurnal untuk memilih kelas
        if ($id_jurnal === null) {
            header('Location: ' . BASEURL . '/guru/jurnal');
            exit;
        }

        $this->data['judul'] = 'Input Absensi';
        $this->data['jurnal'] = $this->model('Jurnal_model')->getJurnalDetailById($id_jurnal);
        $this->data['daftar_siswa'] = $this->model('Absensi_model')->getSiswaDanAbsensiByJurnal($id_jurnal);

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/absensi', $this->data);
        $this->view('templates/footer', $this->data);
    }

/**
 * Simpan Absensi
 */
public function prosesSimpanAbsensi()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($this->model('Absensi_model')->simpanAbsensi($_POST) > 0) {
            // Tambahkan notifikasi sukses
            if (class_exists('Flasher')) {
                Flasher::setFlash('Data absensi berhasil disimpan.', 'success');
            }
            
            // Arahkan ke dashboard guru
            header('Location: ' . BASEURL . '/guru/dashboard');
            exit;
        }
    }
    
    // Fallback jika gagal atau metode request salah
    if (class_exists('Flasher')) {
        Flasher::setFlash('Gagal menyimpan data absensi.', 'danger');
    }
    header('Location: ' . BASEURL . '/guru/dashboard');
    exit;
}    /**
     * Edit Jurnal
     */
    public function editJurnal($id_jurnal)
    {
        $this->data['judul'] = 'Edit Jurnal';
        $this->data['jurnal'] = $this->model('Jurnal_model')->getJurnalById($id_jurnal);

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/edit_jurnal', $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Update Jurnal - dengan redirect ke detail yang benar
     */
    public function prosesUpdateJurnal()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_jurnal = $_POST['id_jurnal'] ?? null;
            
            if ($this->model('Jurnal_model')->updateDataJurnal($_POST) > 0) {
                // Ambil id_penugasan dari id_jurnal untuk redirect yang tepat
                if ($id_jurnal) {
                    try {
                        $db = new Database();
                        $db->query("
                            SELECT j.id_penugasan 
                            FROM jurnal j
                            WHERE j.id_jurnal = :id_jurnal
                            LIMIT 1
                        ");
                        $db->bind('id_jurnal', $id_jurnal);
                        $row = $db->single();
                        
                        if ($row && !empty($row['id_penugasan'])) {
                            // Redirect ke halaman detail dengan id_penugasan
                            if (class_exists('Flasher')) {
                                Flasher::setFlash('Jurnal berhasil diperbarui.', 'success');
                            }
                            header('Location: ' . BASEURL . '/riwayatJurnal/detail/' . $row['id_penugasan']);
                            exit;
                        }
                    } catch (Exception $e) {
                        error_log("Error getting penugasan for updated jurnal: " . $e->getMessage());
                    }
                }
                
                // Fallback ke riwayat jika query gagal
                if (class_exists('Flasher')) {
                    Flasher::setFlash('Jurnal berhasil diperbarui.', 'success');
                }
                header('Location: ' . BASEURL . '/riwayatJurnal');
                exit;
            } else {
                // Gagal update
                if (class_exists('Flasher')) {
                    Flasher::setFlash('Gagal memperbarui jurnal.', 'danger');
                }
                
                // Redirect kembali ke form edit
                if ($id_jurnal) {
                    header('Location: ' . BASEURL . '/guru/editJurnal/' . $id_jurnal);
                    exit;
                }
            }
        }
        
        // fallback
        header('Location: ' . BASEURL . '/guru/dashboard');
        exit;
    }

    /**
     * Cetak Absensi (tanpa layout)
     */
    public function cetakAbsensi($id_jurnal)
    {
        $this->data['judul'] = 'Cetak Laporan Absensi';
        $this->data['jurnal'] = $this->model('Jurnal_model')->getJurnalDetailById($id_jurnal);
        $this->data['daftar_absensi'] = $this->model('Absensi_model')->getAbsensiByJurnalId($id_jurnal);

        $this->view('guru/cetak_absensi', $this->data);
    }

    public function editAbsensi($id_jurnal)
    {
        $this->data['judul'] = 'Edit Absensi';
        $this->data['jurnal'] = $this->model('Jurnal_model')->getJurnalDetailById($id_jurnal);
        $this->data['daftar_siswa'] = $this->model('Absensi_model')->getSiswaDanAbsensiByJurnal($id_jurnal);

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/edit_absensi', $this->data);
        $this->view('templates/footer', $this->data);
    }

/**
 * Proses Edit Absensi - Update data absensi yang sudah ada
 */
public function prosesEditAbsensi()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_jurnal = $_POST['id_jurnal'] ?? null;
        
        if ($this->model('Absensi_model')->simpanAbsensi($_POST) > 0) {
            // SUKSES: Set notifikasi dan redirect ke dashboard
            if (class_exists('Flasher')) {
                Flasher::setFlash('Data absensi berhasil diperbarui.', 'success');
            }
            header('Location: ' . BASEURL . '/guru/dashboard');
            exit;
            
        } else {
            // GAGAL: Set notifikasi dan kembali ke halaman edit
            if (class_exists('Flasher')) {
                Flasher::setFlash('Gagal memperbarui data absensi. Tidak ada perubahan yang disimpan.', 'danger');
            }
            
            if ($id_jurnal) {
                header('Location: ' . BASEURL . '/guru/editAbsensi/' . $id_jurnal);
                exit;
            }
        }
    }
    
    // Fallback jika metode request bukan POST, arahkan ke dashboard
    header('Location: ' . BASEURL . '/guru/dashboard');
    exit;
}
    /**
     * Rincian Absen - Menampilkan halaman filter rincian absen
     */
    public function rincianAbsen($id_mapel = null)
    {
        $this->data['judul'] = 'Rincian Absen per Pertemuan';
        
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;
        
        // Parameter filter dari GET
        $periode = $_GET['periode'] ?? 'semester';
        $tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
        $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
        $id_mapel_filter = $_GET['id_mapel'] ?? $id_mapel;
        
        $this->data['filter'] = [
            'periode' => $periode,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'id_mapel' => $id_mapel_filter
        ];
        
        // Ambil daftar mapel yang diajar guru
        $this->data['daftar_mapel'] = $this->getDaftarMapelGuru($id_guru, $id_semester);
        
        // Jika ada mapel yang dipilih, ambil rincian absen
        $this->data['rincian_data'] = [];
        $this->data['mapel_info'] = null;
        
        if ($id_mapel_filter) {
            $this->data['mapel_info'] = $this->getMapelInfo($id_guru, $id_semester, $id_mapel_filter);
            $this->data['rincian_data'] = $this->getRincianAbsenPerPertemuan($id_guru, $id_semester, $id_mapel_filter, $periode, $tanggal_mulai, $tanggal_akhir);
        }
        
        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/rincian_absen_filter', $this->data);
        $this->view('templates/footer', $this->data);
    }

    /**
     * Download Rincian Absen per Pertemuan dalam format PDF dengan QR Code
     */
    public function downloadRincianAbsenPDF($id_mapel = null)
    {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester) {
            echo "Sesi tidak valid. Silakan login ulang.";
            return;
        }

        // Ambil id_mapel dari parameter GET jika tidak ada di URL
        if (!$id_mapel) {
            $id_mapel = $_GET['id_mapel'] ?? null;
        }

        if (!$id_mapel) {
            echo "Parameter id_mapel tidak ditemukan.";
            return;
        }

        try {
            // Ambil info mapel
            $mapel_info = $this->getMapelInfo($id_guru, $id_semester, $id_mapel);
            if (empty($mapel_info)) {
                echo "Data mapel tidak ditemukan atau Anda tidak memiliki akses.";
                return;
            }

            // Parameter filter
            $periode = $_GET['periode'] ?? 'semester';
            $tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
            $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

            // Ambil data rincian
            $rincian_data = $this->getRincianAbsenPerPertemuan($id_guru, $id_semester, $id_mapel, $periode, $tanggal_mulai, $tanggal_akhir);

            if (empty($rincian_data['siswa_data'])) {
                echo "Tidak ada data absensi untuk dicetak.";
                return;
            }

            // Render HTML untuk PDF
            $filter_info = [
                'periode' => $periode,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_akhir' => $tanggal_akhir,
                'tanggal_cetak' => date('d F Y'),
                'id_mapel' => $id_mapel
            ];

            $renderView = function($viewPath, $data) {
                extract($data);
                ob_start();
                $fullPath = __DIR__ . "/../views/$viewPath.php";
                if (!file_exists($fullPath)) {
                    throw new Exception("View file tidak ditemukan: $fullPath");
                }
                require $fullPath;
                return ob_get_clean();
            };

            $html = $renderView('guru/cetak_rincian_absen', [
                'mapel_info' => $mapel_info,
                'rincian_data' => $rincian_data,
                'filter_info' => $filter_info
            ]);

            // Generate PDF dengan QR Code
            $dompdfPath = __DIR__ . '/../core/dompdf/autoload.inc.php';
            
            if (!file_exists($dompdfPath)) {
                echo "Library Dompdf tidak tersedia.";
                return;
            }

            require_once $dompdfPath;

            if (!class_exists('\\Dompdf\\Dompdf')) {
                echo "Class Dompdf tidak ditemukan.";
                return;
            }

            // Load QR Helper
            require_once APPROOT . '/app/core/PDFQRHelper.php';
            
            // Generate metadata for QR with fingerprint (consistent with rapor format)
            $semesterName = $_SESSION['nama_semester_aktif'] ?? '';
            $tpName = $mapel_info['nama_tp'] ?? '';
            $namaMapel = $mapel_info['nama_mapel'] ?? '';
            $namaKelas = $mapel_info['nama_kelas'] ?? '';
            $printedBy = $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Unknown';
            
            $metadata = [
                'doc' => 'rincian_absen',
                'id_mapel' => $id_mapel,
                'id_guru' => $id_guru,
                'nama_mapel' => $namaMapel,
                'kelas' => $namaKelas,
                'semester' => $semesterName,
                'tahun_pelajaran' => $tpName,
                'periode' => $periode,
                'printed_by' => $printedBy,
                'printed_at' => date('Y-m-d H:i:s')
            ];
            
            // Generate fingerprint for document verification
            $fingerprintBase = implode('|', [
                $id_mapel,
                $namaMapel,
                $namaKelas,
                $semesterName,
                $periode,
                date('Y-m-d')
            ]);
            $metadata['fingerprint'] = hash('sha256', $fingerprintBase);

            // Add QR to PDF
            $html = PDFQRHelper::addQRToPDF($html, 'rincian_absen', $id_mapel, $metadata);

            // Generate PDF
            $dompdf = new \Dompdf\Dompdf([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Download filename
            $nama_file = 'Rincian_Absen_' . 
                         preg_replace('/[^A-Za-z0-9_-]/', '_', $namaMapel) . '_' .
                         preg_replace('/[^A-Za-z0-9_-]/', '_', $namaKelas) . '_' .
                         date('Y-m-d') . '.pdf';

            $dompdf->stream($nama_file, ['Attachment' => true]);

        } catch (Exception $e) {
            error_log("Error in downloadRincianAbsenPDF(): " . $e->getMessage());
            echo "Terjadi kesalahan saat membuat PDF: " . htmlspecialchars($e->getMessage());
        }
    }

    /**
     * Helper: Hitung total penugasan guru pada semester aktif
     */
    private function getTotalPenugasan($id_guru, $id_semester)
    {
        if (!$id_guru || !$id_semester) return 0;

        try {
            $db = new Database();
            $db->query("
                SELECT COUNT(DISTINCT p.id_penugasan) as total
                FROM penugasan p
                WHERE p.id_guru = :id_guru 
                AND p.id_semester = :id_semester
            ");
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            
            $result = $db->single();
            return (int)($result['total'] ?? 0);
        } catch (Exception $e) {
            error_log("Error in getTotalPenugasan(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Helper: Hitung total hari mengajar (distinct tanggal) sejak awal penugasan
     */
    private function getTotalHariMengajar($id_guru, $id_semester)
    {
        if (!$id_guru || !$id_semester) return 0;

        try {
            $db = new Database();
            $db->query("
                SELECT COUNT(DISTINCT DATE(j.tanggal)) as total_hari
                FROM jurnal j
                JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                WHERE p.id_guru = :id_guru 
                AND p.id_semester = :id_semester
            ");
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            
            $result = $db->single();
            return (int)($result['total_hari'] ?? 0);
        } catch (Exception $e) {
            error_log("Error in getTotalHariMengajar(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Helper: Ambil info kelas dan mapel yang diajar guru
     */
    private function getKelasMapelInfo($id_guru, $id_semester)
    {
        if (!$id_guru || !$id_semester) return [];

        try {
            $db = new Database();
            $db->query("
                SELECT DISTINCT 
                    k.nama_kelas,
                    m.nama_mapel
                FROM penugasan p
                JOIN kelas k ON p.id_kelas = k.id_kelas
                JOIN mapel m ON p.id_mapel = m.id_mapel
                WHERE p.id_guru = :id_guru 
                AND p.id_semester = :id_semester
                ORDER BY k.nama_kelas ASC, m.nama_mapel ASC
            ");
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            
            return $db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getKelasMapelInfo(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: Ambil daftar mapel yang diajar guru
     */
    private function getDaftarMapelGuru($id_guru, $id_semester)
    {
        try {
            $db = new Database();
            $sql = "SELECT DISTINCT m.id_mapel, m.nama_mapel, k.nama_kelas
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel  
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    WHERE p.id_guru = :id_guru AND p.id_semester = :id_semester
                    ORDER BY m.nama_mapel, k.nama_kelas";
            
            $db->query($sql);
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            
            return $db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getDaftarMapelGuru(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: Ambil info mapel (nama, kelas, dll)
     */
    private function getMapelInfo($id_guru, $id_semester, $id_mapel)
    {
        try {
            $db = new Database();
            $sql = "SELECT m.nama_mapel, k.nama_kelas, g.nama_guru
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas  
                    JOIN guru g ON p.id_guru = g.id_guru
                    WHERE p.id_guru = :id_guru AND p.id_semester = :id_semester AND m.id_mapel = :id_mapel
                    LIMIT 1";
            
            $db->query($sql);
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            $db->bind('id_mapel', $id_mapel);
            
            $result = $db->single() ?: [];
            
            // Tambahkan info semester dari session jika tersedia
            if (!empty($result)) {
                $result['nama_tp'] = $_SESSION['nama_tp_aktif'] ?? 'Tahun Pelajaran';
                $result['semester'] = $_SESSION['nama_semester_aktif'] ?? 'Semester';
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error in getMapelInfo(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: Ambil rincian absen per pertemuan dengan filter
     */
    private function getRincianAbsenPerPertemuan($id_guru, $id_semester, $id_mapel, $periode, $tanggal_mulai, $tanggal_akhir)
    {
        try {
            $db = new Database();
            
            // Build WHERE clause berdasarkan periode
            $whereClause = "p.id_guru = :id_guru AND p.id_semester = :id_semester AND m.id_mapel = :id_mapel";
            $params = [
                'id_guru' => $id_guru,
                'id_semester' => $id_semester, 
                'id_mapel' => $id_mapel
            ];
            
            switch ($periode) {
                case 'hari_ini':
                    $whereClause .= " AND DATE(j.tanggal) = CURDATE()";
                    break;
                case 'minggu_ini':
                    $whereClause .= " AND YEARWEEK(j.tanggal, 1) = YEARWEEK(CURDATE(), 1)";
                    break;
                case 'bulan_ini':
                    $whereClause .= " AND YEAR(j.tanggal) = YEAR(CURDATE()) AND MONTH(j.tanggal) = MONTH(CURDATE())";
                    break;
                case 'custom':
                    if ($tanggal_mulai && $tanggal_akhir) {
                        $whereClause .= " AND j.tanggal BETWEEN :tanggal_mulai AND :tanggal_akhir";
                        $params['tanggal_mulai'] = $tanggal_mulai;
                        $params['tanggal_akhir'] = $tanggal_akhir;
                    }
                    break;
                default: // semester - tidak ada filter tambahan
                    break;
            }
            
            // Query utama
            $sql = "SELECT 
                        s.id_siswa,
                        s.nama_siswa,
                        s.nisn,
                        j.id_jurnal,
                        j.tanggal,
                        j.pertemuan_ke,
                        j.topik_materi,
                        COALESCE(a.status_kehadiran, 'A') as status_kehadiran,
                        a.waktu_input as waktu_absen,
                        a.keterangan
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                    JOIN keanggotaan_kelas kk ON k.id_kelas = kk.id_kelas
                    JOIN siswa s ON kk.id_siswa = s.id_siswa
                    LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal AND s.id_siswa = a.id_siswa
                    WHERE $whereClause
                    ORDER BY s.nama_siswa ASC, j.tanggal ASC, j.pertemuan_ke ASC";
            
            $db->query($sql);
            foreach ($params as $key => $value) {
                $db->bind($key, $value);
            }
            
            $result = $db->resultSet();
            
            // Restructure data: group by siswa, dengan detail per pertemuan
            $structured_data = [];
            $pertemuan_list = [];
            
            foreach ($result as $row) {
                $id_siswa = $row['id_siswa'];
                $id_jurnal = $row['id_jurnal'];
                
                // Simpan info siswa
                if (!isset($structured_data[$id_siswa])) {
                    $structured_data[$id_siswa] = [
                        'id_siswa' => $id_siswa,
                        'nama_siswa' => $row['nama_siswa'],
                        'nisn' => $row['nisn'],
                        'pertemuan' => [],
                        'total_hadir' => 0,
                        'total_izin' => 0,
                        'total_sakit' => 0,
                        'total_alpha' => 0
                    ];
                }
                
                // Simpan detail pertemuan
                $structured_data[$id_siswa]['pertemuan'][$id_jurnal] = [
                    'tanggal' => $row['tanggal'],
                    'pertemuan_ke' => $row['pertemuan_ke'],
                    'topik_materi' => $row['topik_materi'],
                    'status' => $row['status_kehadiran'],
                    'waktu_absen' => $row['waktu_absen'],
                    'keterangan' => $row['keterangan']
                ];
                
                // Hitung total per status
                switch ($row['status_kehadiran']) {
                    case 'H': $structured_data[$id_siswa]['total_hadir']++; break;
                    case 'I': $structured_data[$id_siswa]['total_izin']++; break;
                    case 'S': $structured_data[$id_siswa]['total_sakit']++; break;
                    default: $structured_data[$id_siswa]['total_alpha']++; break;
                }
                
                // Simpan daftar pertemuan untuk header tabel
                if (!isset($pertemuan_list[$id_jurnal])) {
                    $pertemuan_list[$id_jurnal] = [
                        'tanggal' => $row['tanggal'],
                        'pertemuan_ke' => $row['pertemuan_ke'],
                        'topik_materi' => $row['topik_materi']
                    ];
                }
            }
            
            // Sort pertemuan by tanggal
            uasort($pertemuan_list, function($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });
            
            return [
                'siswa_data' => array_values($structured_data),
                'pertemuan_headers' => array_values($pertemuan_list)
            ];
            
        } catch (Exception $e) {
            error_log("Error in getRincianAbsenPerPertemuan(): " . $e->getMessage());
            return [
                'siswa_data' => [],
                'pertemuan_headers' => []
            ];
        }
    }
}
?>
