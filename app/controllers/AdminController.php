<?php
// File: app/controllers/AdminController.php - ALL SQL QUERIES FIXED
class AdminController extends Controller {
    private $data = [];
    public function __construct()
   {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
        
        // Cache daftar semester di session (refresh setiap 1 jam)
        $cacheKey = 'admin_daftar_semester';
        $cacheTime = $_SESSION[$cacheKey . '_time'] ?? 0;
        
        if (!isset($_SESSION[$cacheKey]) || (time() - $cacheTime) > 3600) {
            $this->data['daftar_semester'] = $this->model('TahunPelajaran_model')->getAllSemester();
            $_SESSION[$cacheKey] = $this->data['daftar_semester'];
            $_SESSION[$cacheKey . '_time'] = time();
        } else {
            $this->data['daftar_semester'] = $_SESSION[$cacheKey];
        }
        
        // Set default semester jika belum ada
        if (!isset($_SESSION['id_semester_aktif']) && !empty($this->data['daftar_semester'])) {
            $defaultSemester = $this->data['daftar_semester'][0];
            $_SESSION['id_semester_aktif'] = $defaultSemester['id_semester'];
            $_SESSION['nama_semester_aktif'] = $defaultSemester['nama_tp'] . ' - ' . $defaultSemester['semester'];
            $_SESSION['id_tp_aktif'] = $defaultSemester['id_tp'];
        }
    }
    // =================================================================
    // TAMBAHAN METHOD INDEX UNTUK ROUTING
    // =================================================================
    public function index()
    {
        error_log("AdminController::index() dipanggil - URL: " . ($_SERVER['REQUEST_URI'] ?? ''));
        header('Location: ' . BASEURL . '/admin/dashboard');
        exit;
    }
    // =================================================================
    // DASHBOARD - ONLY ONE VERSION
    // =================================================================
    public function dashboard()
    {
        $this->data['judul'] = 'Dashboard Admin';
        $this->data['load_chartjs'] = true; // Flag untuk load Chart.js
        
        // Cache stats dashboard (refresh setiap 5 menit)
        $cacheKey = 'admin_dashboard_stats';
        $cacheTime = $_SESSION[$cacheKey . '_time'] ?? 0;
        
        if (!isset($_SESSION[$cacheKey]) || (time() - $cacheTime) > 300) {
            // Ambil data real dari database
            $this->data['jumlah_guru'] = $this->model('Guru_model')->getJumlahGuru();
            $this->data['jumlah_siswa'] = $this->model('Siswa_model')->getJumlahSiswa();
            $this->data['jumlah_kelas'] = $this->model('Kelas_model')->getJumlahKelas();
            $this->data['stats'] = $this->getDashboardStats();
            
            // Simpan ke session cache
            $_SESSION[$cacheKey] = [
                'jumlah_guru' => $this->data['jumlah_guru'],
                'jumlah_siswa' => $this->data['jumlah_siswa'],
                'jumlah_kelas' => $this->data['jumlah_kelas'],
                'stats' => $this->data['stats']
            ];
            $_SESSION[$cacheKey . '_time'] = time();
        } else {
            // Load dari cache
            $cache = $_SESSION[$cacheKey];
            $this->data['jumlah_guru'] = $cache['jumlah_guru'];
            $this->data['jumlah_siswa'] = $cache['jumlah_siswa'];
            $this->data['jumlah_kelas'] = $cache['jumlah_kelas'];
            $this->data['stats'] = $cache['stats'];
        }
        
        // Data yang harus realtime (tidak di-cache)
        $this->data['recent_journals'] = $this->getRecentJournals();
        $this->data['attendance_today'] = $this->getAttendanceToday();
        $this->data['attendance_trend'] = $this->getAttendanceTrend();
        $this->data['alerts'] = $this->getSystemAlerts();
        
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/dashboard', $this->data);
        $this->view('templates/footer', $this->data);
    }
    // =================================================================
    // HELPER METHODS UNTUK DASHBOARD - FIXED SQL
    // =================================================================
    private function getDashboardStats()
    {
        $db = new Database();
        try {
            // Total Guru
            $db->query('SELECT COUNT(*) as total FROM guru');
            $total_guru = $db->single()['total'];
            // Total Siswa Aktif
            $db->query('SELECT COUNT(*) as total FROM siswa WHERE status_siswa = "aktif"');
            $total_siswa_aktif = $db->single()['total'];
            // Total Kelas
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
            $db->query('SELECT COUNT(*) as total FROM kelas WHERE id_tp = :id_tp');
            $db->bind('id_tp', $id_tp_aktif);
            $total_kelas = $db->single()['total'];
            // Jurnal Hari Ini
            $db->query('SELECT COUNT(*) as total FROM jurnal WHERE DATE(tanggal) = CURDATE()');
            $jurnal_hari_ini = $db->single()['total'];
            // Kehadiran Hari Ini
            $attendance_today = $this->calculateAttendanceToday();
            return [
                'total_guru' => $total_guru,
                'total_siswa_aktif' => $total_siswa_aktif,
                'total_kelas' => $total_kelas,
                'jurnal_hari_ini' => $jurnal_hari_ini,
                'kehadiran_hari_ini' => $attendance_today
            ];
        } catch (Exception $e) {
            error_log("Error in getDashboardStats: " . $e->getMessage());
            return [
                'total_guru' => 0,
                'total_siswa_aktif' => 0,
                'total_kelas' => 0,
                'jurnal_hari_ini' => 0,
                'kehadiran_hari_ini' => ['percentage' => 0, 'hadir' => 0, 'total' => 0]
            ];
        }
    }
    private function calculateAttendanceToday()
    {
        $db = new Database();
        try {
            $db->query('SELECT COUNT(*) as total FROM absensi a 
                       JOIN jurnal j ON a.id_jurnal = j.id_jurnal 
                       WHERE DATE(j.tanggal) = CURDATE()');
            $total_absensi = $db->single()['total'];
            if ($total_absensi == 0) {
                return ['percentage' => 0, 'hadir' => 0, 'total' => 0];
            }
            $db->query('SELECT COUNT(*) as hadir FROM absensi a 
                       JOIN jurnal j ON a.id_jurnal = j.id_jurnal 
                       WHERE DATE(j.tanggal) = CURDATE() AND a.status_kehadiran = "H"');
            $total_hadir = $db->single()['hadir'];
            $percentage = round(($total_hadir / $total_absensi) * 100, 1);
            return [
                'percentage' => $percentage,
                'hadir' => $total_hadir,
                'total' => $total_absensi
            ];
        } catch (Exception $e) {
            error_log("Error in calculateAttendanceToday: " . $e->getMessage());
            return ['percentage' => 0, 'hadir' => 0, 'total' => 0];
        }
    }
    private function getRecentJournals()
    {
        $db = new Database();
        try {
            $db->query('SELECT j.id_jurnal, j.tanggal, j.topik_materi, j.timestamp,
                               g.nama_guru, m.nama_mapel, k.nama_kelas
                       FROM jurnal j
                       JOIN penugasan p ON j.id_penugasan = p.id_penugasan
                       JOIN guru g ON p.id_guru = g.id_guru
                       JOIN mapel m ON p.id_mapel = m.id_mapel
                       JOIN kelas k ON p.id_kelas = k.id_kelas
                       ORDER BY j.timestamp DESC
                       LIMIT 10');
            return $db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getRecentJournals: " . $e->getMessage());
            return [];
        }
    }
    private function getAttendanceToday()
    {
        $db = new Database();
        try {
            $db->query('SELECT 
                          SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                          SUM(CASE WHEN a.status_kehadiran = "I" THEN 1 ELSE 0 END) as izin,
                          SUM(CASE WHEN a.status_kehadiran = "S" THEN 1 ELSE 0 END) as sakit,
                          SUM(CASE WHEN a.status_kehadiran = "A" THEN 1 ELSE 0 END) as alfa,
                          COUNT(*) as total
                        FROM absensi a
                        JOIN jurnal j ON a.id_jurnal = j.id_jurnal
                        WHERE DATE(j.tanggal) = CURDATE()');
            $result = $db->single();
            return $result ?: ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0, 'total' => 0];
        } catch (Exception $e) {
            error_log("Error in getAttendanceToday: " . $e->getMessage());
            return ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0, 'total' => 0];
        }
    }
    private function getAttendanceTrend()
    {
        $db = new Database();
        try {
            $db->query('SELECT 
                          DATE(j.tanggal) as tanggal,
                          COUNT(a.id_absensi) as total_absensi,
                          SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) as hadir,
                          ROUND(
                            CASE 
                              WHEN COUNT(a.id_absensi) > 0 
                              THEN (SUM(CASE WHEN a.status_kehadiran = "H" THEN 1 ELSE 0 END) / COUNT(a.id_absensi)) * 100
                              ELSE 0 
                            END, 1
                          ) as persentase
                        FROM jurnal j
                        LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
                        WHERE j.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY DATE(j.tanggal)
                        ORDER BY DATE(j.tanggal) ASC');
            return $db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAttendanceTrend: " . $e->getMessage());
            return [];
        }
    }
    private function getSystemAlerts()
    {
        $alerts = [];
        try {
            $db = new Database();
            // Cek guru yang mengajar di semester aktif
            $db->query('SELECT COUNT(DISTINCT id_guru) as total_guru_mengajar
                       FROM penugasan 
                       WHERE id_semester = :id_semester');
            $db->bind('id_semester', $_SESSION['id_semester_aktif'] ?? 0);
            $total_guru_mengajar = $db->single()['total_guru_mengajar'] ?? 0;
            // Guru yang sudah input jurnal hari ini
            $db->query('SELECT COUNT(DISTINCT p.id_guru) as guru_sudah_jurnal
                       FROM penugasan p
                       JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                       WHERE p.id_semester = :id_semester 
                       AND DATE(j.tanggal) = CURDATE()');
            $db->bind('id_semester', $_SESSION['id_semester_aktif'] ?? 0);
            $guru_sudah_jurnal = $db->single()['guru_sudah_jurnal'] ?? 0;
            $guru_belum_jurnal = $total_guru_mengajar - $guru_sudah_jurnal;
            if ($guru_belum_jurnal > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Jurnal Belum Lengkap',
                    'message' => "$guru_belum_jurnal guru belum input jurnal hari ini",
                    'icon' => 'alert-circle'
                ];
            }
            // Info tahun pelajaran aktif
            $db->query('SELECT nama_tp FROM tp WHERE id_tp = :id_tp');
            $db->bind('id_tp', $_SESSION['id_tp_aktif'] ?? 0);
            $tp_info = $db->single();
            if ($tp_info) {
                $alerts[] = [
                    'type' => 'info',
                    'title' => 'Tahun Pelajaran Aktif',
                    'message' => "Sesi: " . ($tp_info['nama_tp'] ?? ''),
                    'icon' => 'info'
                ];
            }
        } catch (Exception $e) {
            error_log("Error in getSystemAlerts: " . $e->getMessage());
            $alerts[] = [
                'type' => 'error',
                'title' => 'System Error',
                'message' => 'Terjadi kesalahan dalam mengambil data sistem',
                'icon' => 'alert-triangle'
            ];
        }
        return $alerts;
    }
    // =================================================================
    // SIDEBAR DATA - FIXED SQL
    // =================================================================
    public function getSidebarData()
    {
        $db = new Database();
        try {
            $db->query('SELECT COUNT(*) as total FROM guru');
            $total_guru = $db->single()['total'];
            $db->query('SELECT COUNT(*) as total FROM siswa WHERE status_siswa = "aktif"');
            $total_siswa = $db->single()['total'];
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
            $db->query('SELECT COUNT(*) as total FROM kelas WHERE id_tp = :id_tp');
            $db->bind('id_tp', $id_tp_aktif);
            $total_kelas = $db->single()['total'];
            $db->query('SELECT COUNT(*) as total FROM jurnal WHERE DATE(tanggal) = CURDATE()');
            $jurnal_today = $db->single()['total'];
            $attendance = $this->calculateAttendanceToday();
            return [
                'total_guru' => $total_guru,
                'total_siswa' => $total_siswa,
                'total_kelas' => $total_kelas,
                'jurnal_today' => $jurnal_today,
                'guru_belum_jurnal' => 0,
                'attendance_percentage' => $attendance['percentage']
            ];
        } catch (Exception $e) {
            error_log("Error in getSidebarData: " . $e->getMessage());
            return [
                'total_guru' => 0,
                'total_siswa' => 0,
                'total_kelas' => 0,
                'jurnal_today' => 0,
                'guru_belum_jurnal' => 0,
                'attendance_percentage' => 0
            ];
        }
    }
    // =================================================================
    // API ENDPOINTS
    // =================================================================
    public function getStats()
    {
        header('Content-Type: application/json');
        echo json_encode($this->getDashboardStats());
        exit;
    }
    // =================================================================
    // SESI AKTIF
    // =================================================================
    public function setAktifTP()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_semester'])) {
            $allSemester = $this->data['daftar_semester'];
            foreach ($allSemester as $smt) {
                if ($smt['id_semester'] == $_POST['id_semester']) {
                    $_SESSION['id_semester_aktif'] = $smt['id_semester'];
                    $_SESSION['nama_semester_aktif'] = $smt['nama_tp'] . ' - ' . $smt['semester'];
                    $_SESSION['id_tp_aktif'] = $smt['id_tp'];
                    break;
                }
            }
        }
        $previousPage = $_SERVER['HTTP_REFERER'] ?? (BASEURL . '/admin/dashboard');
        header('Location: ' . $previousPage);
        exit;
    }
    // =================================================================
    // CRUD SISWA
    // =================================================================
    public function siswa()
    {
        $this->data['judul'] = 'Manajemen Siswa';
        $this->data['siswa'] = $this->model('Siswa_model')->getAllSiswa();
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/siswa', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function tambahSiswa()
    {
        $this->data['judul'] = 'Tambah Data Siswa';
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tambah_siswa', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesTambahSiswa()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // VALIDASI INPUT
            $nisn = InputValidator::validateNISN($_POST['nisn'] ?? '');
            $nama_siswa = InputValidator::sanitizeNama($_POST['nama_siswa'] ?? '');
            $jenis_kelamin = InputValidator::validateJenisKelamin($_POST['jenis_kelamin'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            // Cek input wajib
            if (!$nisn || empty($nama_siswa) || !$jenis_kelamin || empty($password)) {
                Flasher::setFlash('Data tidak lengkap atau tidak valid', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahSiswa');
                exit;
            }

            // Validasi panjang password minimal
            if (strlen($password) < 6) {
                Flasher::setFlash('Password minimal 6 karakter', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahSiswa');
                exit;
            }

            // Sanitize data lainnya
            $dataSiswa = [
                'nisn' => $nisn,
                'nama_siswa' => $nama_siswa,
                'jenis_kelamin' => $jenis_kelamin,
                'tgl_lahir' => InputValidator::validateDate($_POST['tgl_lahir'] ?? '') ? $_POST['tgl_lahir'] : null
            ];

            $idSiswaBaru = $this->model('Siswa_model')->tambahDataSiswa($dataSiswa);
            if ($idSiswaBaru) {
                $dataAkun = [
                    'username' => $nisn,
                    'password' => $password,
                    'nama_lengkap' => $nama_siswa,
                    'role' => 'siswa',
                    'id_ref' => $idSiswaBaru
                ];
                $this->model('User_model')->buatAkun($dataAkun);
                Flasher::setFlash('Siswa berhasil ditambahkan', 'success');
                header('Location: ' . BASEURL . '/admin/siswa');
                exit;
            } else {
                Flasher::setFlash('Gagal menambahkan siswa', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahSiswa');
                exit;
            }
        }
    }
    public function editSiswa($id)
    {
        $this->data['judul'] = 'Edit Data Siswa';
        $this->data['siswa'] = $this->model('Siswa_model')->getSiswaById($id);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/edit_siswa', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesUpdateSiswa()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Siswa_model')->updateDataSiswa($_POST);
            if (!empty($_POST['password_baru'])) {
                $this->model('User_model')->updatePassword($_POST['id_siswa'], 'siswa', $_POST['password_baru']);
            }
            header('Location: ' . BASEURL . '/admin/siswa');
            exit;
        }
    }
    public function hapusSiswa($id)
    {
        $this->model('User_model')->hapusAkun($id, 'siswa');
        if ($this->model('Siswa_model')->hapusDataSiswa($id) > 0) {
            $this->clearDashboardCache(); // Clear cache setelah hapus
            header('Location: ' . BASEURL . '/admin/siswa');
            exit;
        }
    }
    // =================================================================
    // CRUD GURU
    // =================================================================
    public function guru()
    {
        $this->data['judul'] = 'Manajemen Guru';
        $this->data['guru'] = $this->model('Guru_model')->getAllGuru();
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/guru', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function tambahGuru()
    {
        $this->data['judul'] = 'Tambah Data Guru';
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tambah_guru', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesTambahGuru()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // VALIDASI INPUT
            $nik = InputValidator::validateNIK($_POST['nik'] ?? '');
            $nama_guru = InputValidator::sanitizeNama($_POST['nama_guru'] ?? '');
            $email = InputValidator::validateEmail($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            // Cek input wajib
            if (!$nik || empty($nama_guru) || empty($password)) {
                Flasher::setFlash('NIK, nama, dan password wajib diisi', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahGuru');
                exit;
            }

            // Validasi panjang password minimal
            if (strlen($password) < 6) {
                Flasher::setFlash('Password minimal 6 karakter', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahGuru');
                exit;
            }

            // Sanitize data
            $dataGuru = [
                'nik' => $nik,
                'nama_guru' => $nama_guru,
                'email' => $email ?: null
            ];

            $idGuruBaru = $this->model('Guru_model')->tambahDataGuru($dataGuru);
            if ($idGuruBaru) {
                $dataAkun = [
                    'username' => $nik,
                    'password' => $password,
                    'nama_lengkap' => $nama_guru,
                    'role' => 'guru',
                    'id_ref' => $idGuruBaru
                ];
                $this->model('User_model')->buatAkun($dataAkun);
                Flasher::setFlash('Guru berhasil ditambahkan', 'success');
                header('Location: ' . BASEURL . '/admin/guru');
                exit;
            } else {
                Flasher::setFlash('Gagal menambahkan guru', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahGuru');
                exit;
            }
        }
    }
    
    public function editGuru($id)
    {
        $this->data['judul'] = 'Edit Data Guru';
        $this->data['guru'] = $this->model('Guru_model')->getGuruById($id);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/edit_guru', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesUpdateGuru()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Guru_model')->updateDataGuru($_POST);
            if (!empty($_POST['password_baru'])) {
                $this->model('User_model')->updatePassword($_POST['id_guru'], 'guru', $_POST['password_baru']);
            }
            header('Location: ' . BASEURL . '/admin/guru');
            exit;
        }
    }
    public function hapusGuru($id)
    {
        if ($this->model('Guru_model')->cekKeterkaitanData($id) > 0) {
            Flasher::setFlash('Gagal menghapus! Guru ini masih memiliki data penugasan mengajar.', 'danger');
            header('Location: ' . BASEURL . '/admin/guru');
            exit;
        }
        $this->model('User_model')->hapusAkun($id, 'guru');
        if ($this->model('Guru_model')->hapusDataGuru($id) > 0) {
            $this->clearDashboardCache(); // Clear cache setelah hapus
            Flasher::setFlash('Data guru berhasil dihapus.', 'success');
            header('Location: ' . BASEURL . '/admin/guru');
            exit;
        }
    }
    // =================================================================
    // CRUD TAHUN PELAJARAN
    // =================================================================
    public function tahunPelajaran()
    {
        $this->data['judul'] = 'Manajemen Tahun Pelajaran';
        $this->data['tp'] = $this->model('TahunPelajaran_model')->getAllTahunPelajaran();
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tahun_pelajaran', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function tambahTP()
    {
        $this->data['judul'] = 'Tambah Tahun Pelajaran';
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tambah_tp', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesTambahTP()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('TahunPelajaran_model')->tambahDataTahunPelajaranDanSemester($_POST) > 0) {
                header('Location: ' . BASEURL . '/admin/tahunPelajaran');
                exit;
            }
        }
    }
    public function editTP($id)
    {
        $this->data['judul'] = 'Edit Tahun Pelajaran';
        $this->data['tp'] = $this->model('TahunPelajaran_model')->getTahunPelajaranById($id);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/edit_tp', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesUpdateTP()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('TahunPelajaran_model')->updateDataTahunPelajaran($_POST) > 0) {
                header('Location: ' . BASEURL . '/admin/tahunPelajaran');
                exit;
            }
        }
    }
    public function hapusTP($id)
    {
        if ($this->model('TahunPelajaran_model')->hapusDataTahunPelajaran($id) > 0) {
            header('Location: ' . BASEURL . '/admin/tahunPelajaran');
            exit;
        }
    }
    // =================================================================
    // CRUD KELAS - METHOD TAMBAH KELAS DIPERBAIKI
    // =================================================================
    public function kelas()
    {
        error_log("AdminController::kelas() method dipanggil");
        $this->data['judul'] = 'Manajemen Kelas';
        // Ambil TP aktif dari session
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        error_log("ID TP Aktif: " . $id_tp_aktif);
        // Ambil semua kelas dengan data tambahan (jumlah siswa & guru)
        $this->data['kelas'] = $this->model('Kelas_model')->getAllKelasWithDetails($id_tp_aktif);
        error_log("Jumlah kelas ditemukan: " . count($this->data['kelas']));
        // Data untuk statistics
        $this->data['total_kelas_aktif'] = count($this->data['kelas']);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/kelas', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function tambahKelas()
    {
        $this->data['judul'] = 'Tambah Kelas';
        // Ambil semua tahun pelajaran untuk dropdown
        $this->data['daftar_tp'] = $this->model('TahunPelajaran_model')->getAllTahunPelajaran();
        // Ambil semua guru untuk dropdown wali kelas
        $this->data['daftar_guru'] = $this->model('Guru_model')->getAllGuru();
        // Set default TP aktif jika ada
        $this->data['id_tp_default'] = $_SESSION['id_tp_aktif'] ?? 0;
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tambah_kelas', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesTambahKelas()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi input
            $errors = [];
            if (empty($_POST['nama_kelas'])) {
                $errors[] = 'Nama kelas harus diisi';
            }
            if (empty($_POST['jenjang'])) {
                $errors[] = 'Jenjang harus diisi';
            }
            if (empty($_POST['id_tp'])) {
                $errors[] = 'Tahun pelajaran harus dipilih';
            }
            // Cek duplikasi nama kelas dalam TP yang sama
            if (!empty($_POST['nama_kelas']) && !empty($_POST['id_tp'])) {
                if ($this->model('Kelas_model')->cekDuplikasiKelas($_POST['nama_kelas'], $_POST['id_tp'])) {
                    $errors[] = 'Nama kelas sudah ada untuk tahun pelajaran ini';
                }
            }
            if (!empty($errors)) {
                // Set flash message dengan error
                Flasher::setFlash(implode(', ', $errors), 'danger');
                header('Location: ' . BASEURL . '/admin/tambahKelas');
                exit;
            }
            // Proses insert data kelas
            $id_kelas_baru = $this->model('Kelas_model')->tambahDataKelas($_POST);
            
            if ($id_kelas_baru > 0) {
                // Jika wali kelas dipilih, assign wali kelas
                if (!empty($_POST['id_guru_walikelas'])) {
                    $this->model('Kelas_model')->assignWaliKelas($id_kelas_baru, $_POST['id_guru_walikelas']);
                }
                
                Flasher::setFlash('Data kelas berhasil ditambahkan.', 'success');
                header('Location: ' . BASEURL . '/admin/kelas');
                exit;
            } else {
                Flasher::setFlash('Gagal menambahkan data kelas.', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahKelas');
                exit;
            }
        }
        header('Location: ' . BASEURL . '/admin/tambahKelas');
        exit;
    }
    public function editKelas($id)
    {
        $this->data['judul'] = 'Edit Data Kelas';
        // Ambil data kelas berdasarkan ID
        $this->data['kelas'] = $this->model('Kelas_model')->getKelasById($id);
        // Jika kelas tidak ditemukan
        if (empty($this->data['kelas'])) {
            Flasher::setFlash('Data kelas tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/kelas');
            exit;
        }
        // Ambil info tambahan jika ada
        $kelasDetail = $this->model('Kelas_model')->getAllKelasWithDetails(0);
        foreach ($kelasDetail as $detail) {
            if ($detail['id_kelas'] == $id) {
                $this->data['kelas']['nama_tp'] = $detail['nama_tp'];
                $this->data['kelas']['jumlah_siswa'] = $detail['jumlah_siswa'];
                $this->data['kelas']['jumlah_guru'] = $detail['jumlah_guru'];
                $this->data['kelas']['nama_guru_walikelas'] = $detail['nama_guru_walikelas'] ?? null;
                break;
            }
        }
        
        // Ambil daftar guru untuk dropdown wali kelas
        $this->data['daftar_guru'] = $this->model('Guru_model')->getAllGuru();
        
        // Ambil wali kelas saat ini jika ada
        $this->data['wali_kelas_current'] = $this->model('Kelas_model')->getWaliKelasByKelasId($id);
        
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/edit_kelas', $this->data);
        $this->view('templates/footer', $this->data);
    }

    public function hapusKelas($id)
    {
        // Panggil method di model untuk cek keterkaitan data
        if ($this->model('Kelas_model')->cekKeterkaitanData($id) > 0) {
            // Jika ada, beri pesan error dan jangan hapus
            Flasher::setFlash('Gagal menghapus! Kelas ini masih memiliki data siswa atau penugasan mengajar.', 'danger');
            header('Location: ' . BASEURL . '/admin/kelas');
            exit;
        }

        // Jika tidak ada keterkaitan, lanjutkan proses hapus
        if ($this->model('Kelas_model')->hapusDataKelas($id) > 0) {
            Flasher::setFlash('Data kelas berhasil dihapus.', 'success');
        } else {
            Flasher::setFlash('Gagal menghapus data kelas.', 'danger');
        }
        
        // Redirect kembali ke halaman manajemen kelas
        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }

    public function prosesUpdateKelas()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi input
            $errors = [];
            if (empty($_POST['nama_kelas'])) {
                $errors[] = 'Nama kelas harus diisi';
            }
            if (empty($_POST['jenjang'])) {
                $errors[] = 'Jenjang harus diisi';
            }
            if (empty($_POST['id_kelas'])) {
                $errors[] = 'ID kelas tidak valid';
            }
            // Ambil data kelas lama untuk perbandingan
            $kelasLama = $this->model('Kelas_model')->getKelasById($_POST['id_kelas']);
            if (empty($kelasLama)) {
                $errors[] = 'Data kelas tidak ditemukan';
            }
            // Cek duplikasi nama kelas jika nama diubah
            if (!empty($_POST['nama_kelas']) && !empty($kelasLama)) {
                if ($_POST['nama_kelas'] !== $kelasLama['nama_kelas']) {
                    // Cek duplikasi dengan kelas lain dalam TP yang sama
                    if ($this->model('Kelas_model')->cekDuplikasiKelasEdit($_POST['nama_kelas'], $kelasLama['id_tp'], $_POST['id_kelas'])) {
                        $errors[] = 'Nama kelas sudah ada untuk tahun pelajaran ini';
                    }
                }
            }
            if (!empty($errors)) {
                Flasher::setFlash(implode(', ', $errors), 'danger');
                header('Location: ' . BASEURL . '/admin/editKelas/' . $_POST['id_kelas']);
                exit;
            }
            
            // Proses update data kelas
            $updateResult = $this->model('Kelas_model')->updateDataKelas($_POST);
            error_log("Update kelas result: " . $updateResult);

            // Dapatkan informasi TP dan wali kelas sebelumnya
            $kelasInfo = $this->model('Kelas_model')->getKelasById($_POST['id_kelas']);
            $id_tp_kelas = $kelasInfo['id_tp'] ?? ($_SESSION['id_tp_aktif'] ?? 0);
            $waliSebelumnya = $this->model('Kelas_model')->getWaliKelasByKelasId($_POST['id_kelas']);
            $id_guru_sebelumnya = $waliSebelumnya['id_guru'] ?? null;

            // Handle wali kelas assignment (terpisah dari update data kelas)
            $waliKelasResult = 0;
            if (!empty($_POST['id_guru_walikelas'])) {
                $id_guru_baru = $_POST['id_guru_walikelas'];
                error_log("Assigning wali kelas: id_kelas=" . $_POST['id_kelas'] . ", id_guru_baru=" . $id_guru_baru . ", id_tp=" . $id_tp_kelas);
                // Assign or update wali kelas
                $waliKelasResult = $this->model('Kelas_model')->assignWaliKelas($_POST['id_kelas'], $id_guru_baru);
                error_log("Assign wali kelas result: " . $waliKelasResult);

                // Update role guru baru menjadi wali_kelas
                $this->model('User_model')->updateRoleToWaliKelas($id_guru_baru);

                // Jika ada wali sebelumnya dan berbeda, cek apakah masih menjadi wali di TP ini
                if (!empty($id_guru_sebelumnya) && $id_guru_sebelumnya != $id_guru_baru) {
                    $masihWali = $this->model('WaliKelas_model')->cekWaliKelasExists($id_guru_sebelumnya, $id_tp_kelas);
                    if (!$masihWali) {
                        // Kembalikan role ke guru
                        $this->model('User_model')->updateRoleToGuru($id_guru_sebelumnya);
                        error_log("Revert role ke guru untuk id_guru=" . $id_guru_sebelumnya);
                    }
                }
            } else {
                error_log("Removing wali kelas: id_kelas=" . $_POST['id_kelas'] . ", id_tp=" . $id_tp_kelas);
                // Remove wali kelas if dropdown is empty
                $waliKelasResult = $this->model('Kelas_model')->removeWaliKelas($_POST['id_kelas']);
                error_log("Remove wali kelas result: " . $waliKelasResult);

                // Jika ada wali sebelumnya, dan setelah di-remove tidak lagi menjadi wali di TP ini, kembalikan role
                if (!empty($id_guru_sebelumnya)) {
                    $masihWali = $this->model('WaliKelas_model')->cekWaliKelasExists($id_guru_sebelumnya, $id_tp_kelas);
                    if (!$masihWali) {
                        $this->model('User_model')->updateRoleToGuru($id_guru_sebelumnya);
                        error_log("Revert role ke guru untuk id_guru=" . $id_guru_sebelumnya);
                    }
                }
            }
            
            error_log("Total changes: updateResult=$updateResult, waliKelasResult=$waliKelasResult");
            
            // Cek apakah ada perubahan (baik data kelas atau wali kelas)
            if ($updateResult > 0 || $waliKelasResult > 0) {
                Flasher::setFlash('Data kelas berhasil diperbarui.', 'success');
                header('Location: ' . BASEURL . '/admin/kelas');
                exit;
            } else {
                Flasher::setFlash('Tidak ada perubahan data yang perlu disimpan.', 'info');
                header('Location: ' . BASEURL . '/admin/editKelas/' . $_POST['id_kelas']);
                exit;
            }
        }
        header('Location: ' . BASEURL . '/admin/kelas');
        exit;
    }
    // =================================================================
    // CRUD MATA PELAJARAN
    // =================================================================
    public function mapel()
    {
        $this->data['judul'] = 'Manajemen Mata Pelajaran';
        $this->data['mapel'] = $this->model('Mapel_model')->getAllMapel();
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/mapel', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function tambahMapel()
    {
        $this->data['judul'] = 'Tambah Mata Pelajaran';
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tambah_mapel', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesTambahMapel()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('Mapel_model')->tambahDataMapel($_POST) > 0) {
                header('Location: ' . BASEURL . '/admin/mapel');
                exit;
            }
        }
    }
    public function editMapel($id)
    {
        $this->data['judul'] = 'Edit Mata Pelajaran';
        $this->data['mapel'] = $this->model('Mapel_model')->getMapelById($id);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/edit_mapel', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesUpdateMapel()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->model('Mapel_model')->updateDataMapel($_POST) > 0) {
                header('Location: ' . BASEURL . '/admin/mapel');
                exit;
            }
        }
    }
    public function hapusMapel($id)
    {
        if ($this->model('Mapel_model')->hapusDataMapel($id) > 0) {
            header('Location: ' . BASEURL . '/admin/mapel');
            exit;
        }
    }
    // =================================================================
    // PENUGASAN - DIPERBAIKI DENGAN VALIDASI DUPLIKASI
    // =================================================================
    public function penugasan()
    {
        $this->data['judul'] = 'Penugasan Guru';
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;
        $this->data['penugasan'] = $this->model('Penugasan_model')->getAllPenugasanBySemester($id_semester_aktif);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/penugasan', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function tambahPenugasan()
    {
        $this->data['judul'] = 'Tambah Penugasan';
        $this->data['guru'] = $this->model('Guru_model')->getAllGuru();
        $this->data['mapel'] = $this->model('Mapel_model')->getAllMapel();
        // PERBAIKAN: Filter kelas berdasarkan TP aktif
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $this->data['kelas'] = $this->model('Kelas_model')->getKelasByTP($id_tp_aktif);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tambah_penugasan', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesTambahPenugasan()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi input
            $errors = [];
            if (empty($_POST['id_guru'])) {
                $errors[] = 'Guru harus dipilih';
            }
            if (empty($_POST['id_mapel'])) {
                $errors[] = 'Mata pelajaran harus dipilih';
            }
            if (empty($_POST['id_kelas'])) {
                $errors[] = 'Kelas harus dipilih';
            }
            if (empty($_POST['id_semester'])) {
                $errors[] = 'Semester harus dipilih';
            }

            // Cek duplikasi penugasan
            if (empty($errors)) {
                $isDuplicate = $this->model('Penugasan_model')->cekDuplikasiPenugasan(
                    $_POST['id_guru'],
                    $_POST['id_mapel'],
                    $_POST['id_kelas'],
                    $_POST['id_semester']
                );
                if ($isDuplicate) {
                    $errors[] = 'Penugasan dengan kombinasi guru, mata pelajaran, kelas, dan semester ini sudah ada.';
                }
            }

            if (!empty($errors)) {
                Flasher::setFlash(implode(', ', $errors), 'danger');
                header('Location: ' . BASEURL . '/admin/tambahPenugasan');
                exit;
            }

            // Jika lolos validasi, simpan data
            if ($this->model('Penugasan_model')->tambahDataPenugasan($_POST) > 0) {
                Flasher::setFlash('Penugasan berhasil ditambahkan.', 'success');
                header('Location: ' . BASEURL . '/admin/penugasan');
                exit;
            } else {
                Flasher::setFlash('Gagal menambahkan penugasan.', 'danger');
                header('Location: ' . BASEURL . '/admin/tambahPenugasan');
                exit;
            }
        }
        header('Location: ' . BASEURL . '/admin/tambahPenugasan');
        exit;
    }

    // API endpoint untuk cek duplikasi penugasan via AJAX
    public function checkPenugasanDuplikat()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request method']);
            exit;
        }

        // Ambil data JSON dari request body
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['error' => 'Invalid JSON']);
            exit;
        }

        // Validasi parameter yang diperlukan
        $id_guru = $input['id_guru'] ?? '';
        $id_mapel = $input['id_mapel'] ?? '';
        $id_kelas = $input['id_kelas'] ?? '';
        $id_semester = $_SESSION['id_semester_aktif'] ?? '';

        // Jika salah satu field kosong, tidak perlu cek duplikasi
        if (empty($id_guru) || empty($id_mapel) || empty($id_kelas) || empty($id_semester)) {
            echo json_encode(['isDuplicate' => false]);
            exit;
        }

        // Cek duplikasi menggunakan model
        $isDuplicate = $this->model('Penugasan_model')->cekDuplikasiPenugasan(
            $id_guru,
            $id_mapel,
            $id_kelas,
            $id_semester
        );

        echo json_encode(['isDuplicate' => $isDuplicate]);
        exit;
    }

    public function hapusPenugasan($id)
    {
        if ($this->model('Penugasan_model')->hapusDataPenugasan($id) > 0) {
            header('Location: ' . BASEURL . '/admin/penugasan');
            exit;
        }
    }
    public function editPenugasan($id)
    {
        $this->data['judul'] = 'Edit Penugasan';
        $this->data['penugasan'] = $this->model('Penugasan_model')->getPenugasanById($id);
        // Jika data tidak ditemukan
        if (empty($this->data['penugasan'])) {
            Flasher::setFlash('Data penugasan tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/penugasan');
            exit;
        }
        $this->data['guru'] = $this->model('Guru_model')->getAllGuru();
        $this->data['mapel'] = $this->model('Mapel_model')->getAllMapel();
        // Filter kelas berdasarkan TP aktif
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $this->data['kelas'] = $this->model('Kelas_model')->getKelasByTP($id_tp_aktif);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/edit_penugasan', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesUpdatePenugasan()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi input
            $errors = [];
            if (empty($_POST['id_penugasan'])) {
                $errors[] = 'ID penugasan tidak valid';
            }
            if (empty($_POST['id_guru'])) {
                $errors[] = 'Guru harus dipilih';
            }
            if (empty($_POST['id_mapel'])) {
                $errors[] = 'Mata pelajaran harus dipilih';
            }
            if (empty($_POST['id_kelas'])) {
                $errors[] = 'Kelas harus dipilih';
            }
            // Cek duplikasi penugasan (kecuali penugasan yang sedang diedit)
            if (!empty($_POST['id_guru']) && !empty($_POST['id_mapel']) && !empty($_POST['id_kelas'])) {
                $isDuplicate = $this->model('Penugasan_model')->cekDuplikasiPenugasanEdit(
                    $_POST['id_guru'], 
                    $_POST['id_mapel'], 
                    $_POST['id_kelas'], 
                    $_POST['id_semester'],
                    $_POST['id_penugasan']
                );
                if ($isDuplicate) {
                    $errors[] = 'Penugasan dengan kombinasi guru, mata pelajaran, dan kelas ini sudah ada';
                }
            }
            if (!empty($errors)) {
                Flasher::setFlash(implode(', ', $errors), 'danger');
                header('Location: ' . BASEURL . '/admin/editPenugasan/' . $_POST['id_penugasan']);
                exit;
            }
            // Proses update
            if ($this->model('Penugasan_model')->updateDataPenugasan($_POST) > 0) {
                Flasher::setFlash('Data penugasan berhasil diperbarui.', 'success');
                header('Location: ' . BASEURL . '/admin/penugasan');
                exit;
            } else {
                Flasher::setFlash('Gagal memperbarui data penugasan.', 'danger');
                header('Location: ' . BASEURL . '/admin/editPenugasan/' . $_POST['id_penugasan']);
                exit;
            }
        }
        header('Location: ' . BASEURL . '/admin/penugasan');
        exit;
    }
    // =================================================================
    // KEANGGOTAAN KELAS
    // =================================================================
    public function keanggotaan()
    {
        $this->data['judul'] = 'Anggota Kelas';
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $this->data['daftar_kelas'] = $this->model('Kelas_model')->getKelasByTP($id_tp_aktif);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_kelas'])) {
            $id_kelas = $_POST['id_kelas'];
            $this->data['kelas_terpilih'] = $this->model('Kelas_model')->getKelasById($id_kelas);
            $this->data['anggota_kelas'] = $this->model('Keanggotaan_model')->getSiswaByKelas($id_kelas, $id_tp_aktif);
        }
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/keanggotaan', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function tambahAnggota($id_kelas)
    {
        $this->data['judul'] = 'Tambah Anggota Kelas';
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $this->data['kelas_terpilih'] = $this->model('Kelas_model')->getKelasById($id_kelas);
        $this->data['siswa_tersedia'] = $this->model('Keanggotaan_model')->getSiswaNotInAnyClass($id_tp_aktif);
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/tambah_anggota', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesTambahAnggota()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_siswa'])) {
            if ($this->model('Keanggotaan_model')->tambahAnggotaKelas($_POST) > 0) {
                header('Location: ' . BASEURL . '/admin/keanggotaan');
                exit;
            }
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
    public function hapusAnggota($id_keanggotaan)
    {
        if ($this->model('Keanggotaan_model')->hapusAnggotaKelas($id_keanggotaan) > 0) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
    // =================================================================
    // NAIK KELAS
    // =================================================================
    public function naikKelas()
    {
        $this->data['judul'] = 'Naik Kelas';
        $this->data['daftar_tp'] = $this->model('TahunPelajaran_model')->getAllTahunPelajaran();
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/naik_kelas', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function getKelasByTP($id_tp)
    {
        $dataKelas = $this->model('Kelas_model')->getKelasByTP($id_tp);
        header('Content-Type: application/json');
        echo json_encode($dataKelas);
    }
    public function getSiswaByKelas($id_kelas, $id_tp)
    {
        $dataSiswa = $this->model('Keanggotaan_model')->getSiswaByKelas($id_kelas, $id_tp);
        header('Content-Type: application/json');
        echo json_encode($dataSiswa);
    }
    public function prosesNaikKelas()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siswa_terpilih'])) {
            $id_tp_tujuan = $_POST['id_tp_tujuan'];
            $id_kelas_tujuan = $_POST['id_kelas_tujuan'];
            $daftar_siswa = $_POST['siswa_terpilih'];
            $jumlahSiswa = $this->model('Keanggotaan_model')->prosesPromosiSiswaTerpilih($id_tp_tujuan, $id_kelas_tujuan, $daftar_siswa);
            Flasher::setFlash("Proses kenaikan kelas berhasil. Sebanyak $jumlahSiswa siswa telah dipindahkan.", 'success');
            header('Location: ' . BASEURL . '/admin/naikKelas');
            exit;
        } else {
            Flasher::setFlash('Gagal! Tidak ada siswa yang dipilih atau kelas tujuan belum ditentukan.', 'danger');
            header('Location: ' . BASEURL . '/admin/naikKelas');
            exit;
        }
    }
    // =================================================================
    // KELULUSAN
    // =================================================================
    public function kelulusan()
    {
        $this->data['judul'] = 'Kelulusan Siswa';
        $this->data['daftar_tp'] = $this->model('TahunPelajaran_model')->getAllTahunPelajaran();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tampilkan_siswa'])) {
            $id_tp = $_POST['id_tp'];
            $id_kelas = $_POST['id_kelas'];
            $this->data['id_tp_pilihan'] = $id_tp;
            $this->data['id_kelas_pilihan'] = $id_kelas;
            $this->data['daftar_siswa'] = $this->model('Keanggotaan_model')->getSiswaByKelas($id_kelas, $id_tp);
        }
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/kelulusan', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function prosesKelulusan()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siswa_terpilih'])) {
            $daftar_siswa = $_POST['siswa_terpilih'];
            $jumlahSiswa = $this->model('Siswa_model')->luluskanSiswaByIds($daftar_siswa);
            Flasher::setFlash("Proses kelulusan berhasil. Sebanyak $jumlahSiswa siswa telah diubah statusnya menjadi Lulus.", 'success');
            header('Location: ' . BASEURL . '/admin/kelulusan');
            exit;
        } else {
            Flasher::setFlash('Gagal! Tidak ada siswa yang dipilih.', 'danger');
            header('Location: ' . BASEURL . '/admin/kelulusan');
            exit;
        }
    }
    // =================================================================
    // RIWAYAT JURNAL & STATISTIK ADMIN - 4 METHOD BARU - FIXED SQL
    // =================================================================
    /**
     * Riwayat Per Mapel dengan Statistik - Admin melihat semua mapel
     */
    public function riwayatPerMapel()
    {
        $this->data['judul'] = 'Riwayat Jurnal & Statistik';
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;
        // Filter dari GET parameters
        $filter_guru = $_GET['guru'] ?? null;
        $filter_mapel = $_GET['mapel'] ?? null;
        $filter_kelas = $_GET['kelas'] ?? null;
        // Data untuk dropdown filter
        $this->data['daftar_guru'] = $this->model('Guru_model')->getAllGuru();
        $this->data['daftar_mapel'] = $this->model('Mapel_model')->getAllMapel();
        $this->data['daftar_kelas'] = $this->model('Kelas_model')->getKelasByTP($_SESSION['id_tp_aktif'] ?? 0);
        // Data riwayat jurnal dengan statistik untuk admin
        $this->data['jurnal_per_mapel'] = $this->getAllJurnalPerMapelAdmin($id_semester_aktif, $filter_guru, $filter_mapel, $filter_kelas);
        // Data filter yang dipilih
        $this->data['filter'] = [
            'guru' => $filter_guru,
            'mapel' => $filter_mapel, 
            'kelas' => $filter_kelas
        ];
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/riwayat_per_mapel_with_stats', $this->data);
        $this->view('templates/footer', $this->data);
    }
    /**
     * Rincian Absen per Pertemuan - Admin dengan filter guru/kelas
     */
    /**
     * Cetak Mapel Admin - Cetak laporan mapel dari guru tertentu
     */
    public function cetakMapelAdmin($combo_id)
    {
        // Format combo_id: "guru_id-mapel_id" 
        $parts = explode('-', $combo_id);
        if (count($parts) < 2) {
            echo "<div style='padding:20px;color:#ef4444;'>Error: Format combo_id tidak valid. Harus berupa 'guru_id-mapel_id'</div>";
            return;
        }
        $id_guru = $parts[0];
        $id_mapel = $parts[1];
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;
        // Ambil data untuk laporan
        $meta = $this->getMetaLaporanAdmin($id_guru, $id_mapel, $id_semester);
        $rekap_siswa = $this->getRekapSiswaAdmin($id_guru, $id_mapel, $id_semester);
        $rekap_pertemuan = $this->getRekapPertemuanAdmin($id_guru, $id_mapel, $id_semester);
        $this->data = [
            'meta' => $meta,
            'rekap_siswa' => $rekap_siswa,
            'rekap_pertemuan' => $rekap_pertemuan,
            'total_siswa' => count($rekap_siswa),
            'id_mapel' => $combo_id
        ];
        // Render view
        $wantPdf = isset($_GET['pdf']) && $_GET['pdf'] == 1;
        $renderView = function($view, $data) {
            extract($data);
            ob_start();
            require __DIR__ . "/../views/$view.php";
            return ob_get_clean();
        };
        $html = $renderView('admin/cetak_mapel', $this->data);
        if ($wantPdf) {
            // Setup Dompdf
            $dompdfPath = __DIR__ . '/../core/dompdf/autoload.inc.php';
            if (!file_exists($dompdfPath)) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;font-family:Arial,sans-serif;'>Library Dompdf tidak ditemukan di core/dompdf/</div>";
                echo $html;
                return;
            }
            require_once $dompdfPath;
            try {
                $dompdf = new \Dompdf\Dompdf([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'Arial'
                ]);
                $dompdf->loadHtml($html, 'UTF-8');
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $mapel_name = $meta['nama_mapel'] ?? 'Mapel';
                $guru_name = $meta['nama_guru'] ?? 'Guru';
                $filename = 'Laporan_' . preg_replace('/\s+/', '_', $mapel_name) . '_' . preg_replace('/\s+/', '_', $guru_name) . '_' . date('Y-m-d') . '.pdf';
                $dompdf->stream($filename, ['Attachment' => true]);
                return;
            } catch (Exception $e) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;color:#ef4444;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo $html;
                return;
            }
        }
        // Tampilkan halaman cetak HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }
    /**
     * Cetak Rincian Absen Admin - dengan filter guru
     */
    public function cetakRincianAbsenAdmin($combo_id)
    {
        // Format combo_id: "guru_id-mapel_id"
        $parts = explode('-', $combo_id);
        if (count($parts) < 2) {
            echo "<div style='padding:20px;color:#ef4444;'>Error: Format combo_id tidak valid. Harus berupa 'guru_id-mapel_id'</div>";
            return;
        }
        $id_guru = $parts[0];
        $id_mapel = $parts[1];
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;
        // Parameter filter
        $periode = $_GET['periode'] ?? 'semester';
        $tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
        $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
        // Ambil data
        $this->data['mapel_info'] = $this->getMapelInfoAdmin($id_semester, $id_mapel, $id_guru);
        $this->data['rincian_data'] = $this->getRincianAbsenAdmin($id_semester, $id_mapel, $id_guru, $periode, $tanggal_mulai, $tanggal_akhir);
        $this->data['filter_info'] = [
            'periode' => $periode,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'tanggal_cetak' => date('d F Y')
        ];
        // Render view
        $wantPdf = isset($_GET['pdf']) && $_GET['pdf'] == 1;
        $renderView = function($view, $data) {
            extract($data);
            ob_start();
            require __DIR__ . "/../views/$view.php";
            return ob_get_clean();
        };
        $html = $renderView('admin/cetak_rincian_absen', $this->data);
        if ($wantPdf) {
            // Setup Dompdf
            $dompdfPath = __DIR__ . '/../core/dompdf/autoload.inc.php';
            if (!file_exists($dompdfPath)) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;font-family:Arial,sans-serif;'>Library Dompdf tidak ditemukan di core/dompdf/</div>";
                echo $html;
                return;
            }
            require_once $dompdfPath;
            try {
                $dompdf = new \Dompdf\Dompdf([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'Arial'
                ]);
                $dompdf->loadHtml($html, 'UTF-8');
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $mapel_name = $this->data['mapel_info']['nama_mapel'] ?? 'Mapel';
                $guru_name = $this->data['mapel_info']['nama_guru'] ?? 'Guru';
                $filename = 'Rincian_Absen_' . preg_replace('/\s+/', '_', $mapel_name) . '_' . preg_replace('/\s+/', '_', $guru_name) . '_' . date('Y-m-d') . '.pdf';
                $dompdf->stream($filename, ['Attachment' => true]);
                return;
            } catch (Exception $e) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;color:#ef4444;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo $html;
                return;
            }
        }
        // Tampilkan halaman cetak HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }
    // =================================================================
    // HELPER METHODS UNTUK ADMIN RIWAYAT & CETAK - FIXED SQL
    // =================================================================
    /**
     * Ambil semua jurnal per mapel untuk admin dengan filter - FIXED SQL
     */
    private function getAllJurnalPerMapelAdmin($id_semester, $filter_guru = null, $filter_mapel = null, $filter_kelas = null)
    {
        $db = new Database();
        try {
            // Build WHERE clause
            $whereClause = "p.id_semester = :id_semester";
            $params = ['id_semester' => $id_semester];
            if ($filter_guru) {
                $whereClause .= " AND p.id_guru = :id_guru";
                $params['id_guru'] = $filter_guru;
            }
            if ($filter_mapel) {
                $whereClause .= " AND p.id_mapel = :id_mapel";
                $params['id_mapel'] = $filter_mapel;
            }
            if ($filter_kelas) {
                $whereClause .= " AND p.id_kelas = :id_kelas";
                $params['id_kelas'] = $filter_kelas;
            }
            // Query statistik absensi per mapel-guru kombinasi
            $sql = "SELECT 
                        p.id_penugasan,
                        g.id_guru,
                        g.nama_guru,
                        m.id_mapel,
                        m.nama_mapel,
                        k.nama_kelas,
                        COUNT(DISTINCT j.id_jurnal) as total_pertemuan,
                        COUNT(DISTINCT siswa.id_siswa) as total_siswa,
                        SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as total_hadir,
                        SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as total_izin,
                        SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as total_sakit,
                        SUM(CASE WHEN a.status_kehadiran = 'A' OR a.status_kehadiran IS NULL THEN 1 ELSE 0 END) as total_alpha,
                        COUNT(a.id_absensi) as total_absensi_records
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN guru g ON p.id_guru = g.id_guru
                    LEFT JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                    LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
                    LEFT JOIN siswa ON k.id_kelas = siswa.id_kelas
                    WHERE {$whereClause}
                    GROUP BY p.id_penugasan, g.id_guru, m.id_mapel, k.id_kelas
                    HAVING total_pertemuan > 0
                    ORDER BY g.nama_guru, m.nama_mapel, k.nama_kelas";
            $db->query($sql);
            foreach ($params as $key => $value) {
                $db->bind($key, $value);
            }
            $statistik_results = $db->resultSet();
            // Transform data untuk view (mirip struktur guru)
            $jurnal_per_mapel = [];
            foreach ($statistik_results as $stat) {
                $persentase_kehadiran = $stat['total_absensi_records'] > 0 ? 
                    round(($stat['total_hadir'] / $stat['total_absensi_records']) * 100, 1) : 0;
                $chart_data = [
                    'hadir' => (int)$stat['total_hadir'],
                    'izin' => (int)$stat['total_izin'],
                    'sakit' => (int)$stat['total_sakit'],
                    'alpha' => (int)$stat['total_alpha']
                ];
                $jurnal_per_mapel[] = [
                    'id_mapel_untuk_link' => $stat['id_guru'] . '-' . $stat['id_mapel'], // Format combo untuk link
                    'id_guru' => $stat['id_guru'],
                    'nama_guru' => $stat['nama_guru'],
                    'id_mapel' => $stat['id_mapel'],
                    'nama_mapel' => $stat['nama_mapel'],
                    'nama_kelas' => $stat['nama_kelas'],
                    'statistik' => [
                        'total_pertemuan' => $stat['total_pertemuan'],
                        'total_siswa' => $stat['total_siswa'],
                        'total_hadir' => $stat['total_hadir'],
                        'total_izin' => $stat['total_izin'],
                        'total_sakit' => $stat['total_sakit'],
                        'total_alpha' => $stat['total_alpha'],
                        'total_absensi_records' => $stat['total_absensi_records'],
                        'persentase_kehadiran' => $persentase_kehadiran
                    ],
                    'chart_data' => $chart_data,
                    'pertemuan' => [] // Bisa diisi jika diperlukan detail pertemuan
                ];
            }
            return $jurnal_per_mapel;
        } catch (Exception $e) {
            error_log("Error in getAllJurnalPerMapelAdmin: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Ambil daftar mapel untuk admin dengan info guru - FIXED SQL
     */
    private function getDaftarMapelAdmin($id_semester)
    {
        $db = new Database();
        try {
            $sql = "SELECT DISTINCT 
                        CONCAT(p.id_guru, '-', m.id_mapel) as combo_id,
                        m.id_mapel, 
                        m.nama_mapel, 
                        k.nama_kelas,
                        g.nama_guru,
                        g.id_guru
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN guru g ON p.id_guru = g.id_guru
                    WHERE p.id_semester = :id_semester
                    ORDER BY g.nama_guru, m.nama_mapel, k.nama_kelas";
            $db->query($sql);
            $db->bind('id_semester', $id_semester);
            return $db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getDaftarMapelAdmin: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Ambil info mapel admin dengan guru - FIXED SQL
     */
    private function getMapelInfoAdmin($id_semester, $id_mapel, $id_guru)
    {
        $db = new Database();
        try {
            // FIXED: JOIN semester dengan tp untuk mendapatkan nama_tp
            $sql = "SELECT m.nama_mapel, k.nama_kelas, g.nama_guru, tp.nama_tp, smt.semester
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN guru g ON p.id_guru = g.id_guru
                    JOIN semester smt ON p.id_semester = smt.id_semester
                    JOIN tp ON smt.id_tp = tp.id_tp
                    WHERE p.id_semester = :id_semester AND m.id_mapel = :id_mapel";
            // Jika id_guru tidak kosong, tambahkan filter guru
            if (!empty($id_guru)) {
                $sql .= " AND p.id_guru = :id_guru";
            }
            $sql .= " LIMIT 1";
            $db->query($sql);
            $db->bind('id_semester', $id_semester);
            $db->bind('id_mapel', $id_mapel);
            if (!empty($id_guru)) {
                $db->bind('id_guru', $id_guru);
            }
            return $db->single() ?: [];
        } catch (Exception $e) {
            error_log("Error in getMapelInfoAdmin: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Ambil rincian absen admin dengan filter guru - FIXED SQL
     */
    private function getRincianAbsenAdmin($id_semester, $id_mapel, $id_guru, $periode, $tanggal_mulai, $tanggal_akhir)
    {
        $db = new Database();
        try {
            // Build WHERE clause berdasarkan periode
            $whereClause = "p.id_semester = :id_semester AND m.id_mapel = :id_mapel AND p.id_guru = :id_guru";
            $params = [
                'id_semester' => $id_semester,
                'id_mapel' => $id_mapel,
                'id_guru' => $id_guru
            ];
            // Tambahkan filter periode
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
            }
            // Query sama seperti method guru
            $sql = "SELECT 
                        s.id_siswa,
                        s.nama_siswa,
                        s.nisn,
                        j.id_jurnal,
                        j.tanggal,
                        j.pertemuan_ke,
                        j.topik_materi,
                        COALESCE(a.status_kehadiran, 'A') as status_kehadiran,
                        a.waktu_absen,
                        a.keterangan
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                    JOIN siswa s ON s.id_kelas = k.id_kelas
                    LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal AND s.id_siswa = a.id_siswa
                    WHERE $whereClause
                    ORDER BY s.nama_siswa ASC, j.tanggal ASC, j.pertemuan_ke ASC";
            $db->query($sql);
            foreach ($params as $key => $value) {
                $db->bind($key, $value);
            }
            $result = $db->resultSet();
            // Structure data sama seperti method guru
            $structured_data = [];
            $pertemuan_list = [];
            foreach ($result as $row) {
                $id_siswa = $row['id_siswa'];
                $id_jurnal = $row['id_jurnal'];
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
                $structured_data[$id_siswa]['pertemuan'][$id_jurnal] = [
                    'tanggal' => $row['tanggal'],
                    'pertemuan_ke' => $row['pertemuan_ke'],
                    'topik_materi' => $row['topik_materi'],
                    'status' => $row['status_kehadiran'],
                    'waktu_absen' => $row['waktu_absen'],
                    'keterangan' => $row['keterangan']
                ];
                switch ($row['status_kehadiran']) {
                    case 'H': $structured_data[$id_siswa]['total_hadir']++; break;
                    case 'I': $structured_data[$id_siswa]['total_izin']++; break;
                    case 'S': $structured_data[$id_siswa]['total_sakit']++; break;
                    default: $structured_data[$id_siswa]['total_alpha']++; break;
                }
                if (!isset($pertemuan_list[$id_jurnal])) {
                    $pertemuan_list[$id_jurnal] = [
                        'tanggal' => $row['tanggal'],
                        'pertemuan_ke' => $row['pertemuan_ke'],
                        'topik_materi' => $row['topik_materi']
                    ];
                }
            }
            uasort($pertemuan_list, function($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });
            return [
                'siswa_data' => array_values($structured_data),
                'pertemuan_headers' => array_values($pertemuan_list)
            ];
        } catch (Exception $e) {
            error_log("Error in getRincianAbsenAdmin: " . $e->getMessage());
            return [
                'siswa_data' => [],
                'pertemuan_headers' => []
            ];
        }
    }
    /**
     * Helper untuk meta laporan admin - FIXED SQL
     */
    private function getMetaLaporanAdmin($id_guru, $id_mapel, $id_semester)
    {
        $db = new Database();
        try {
            // FIXED: JOIN semester dengan tp
            $sql = "SELECT m.nama_mapel, k.nama_kelas, g.nama_guru, tp.nama_tp, smt.semester
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN guru g ON p.id_guru = g.id_guru
                    JOIN semester smt ON p.id_semester = smt.id_semester
                    JOIN tp ON smt.id_tp = tp.id_tp
                    WHERE p.id_guru = :id_guru AND p.id_semester = :id_semester AND m.id_mapel = :id_mapel
                    LIMIT 1";
            $db->query($sql);
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            $db->bind('id_mapel', $id_mapel);
            $result = $db->single();
            if ($result) {
                $result['tanggal'] = date('d F Y');
                $result['tp'] = $result['nama_tp'] ?? '';
            }
            return $result ?: [];
        } catch (Exception $e) {
            error_log("Error in getMetaLaporanAdmin: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Helper untuk rekap siswa admin - FIXED SQL
     */
    private function getRekapSiswaAdmin($id_guru, $id_mapel, $id_semester)
    {
        $db = new Database();
        try {
            $sql = "SELECT 
                        s.nama_siswa,
                        SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN a.status_kehadiran = 'A' OR a.status_kehadiran IS NULL THEN 1 ELSE 0 END) as alpha,
                        COUNT(j.id_jurnal) as total
                    FROM penugasan p
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN siswa s ON k.id_kelas = s.id_kelas
                    LEFT JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                    LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal AND s.id_siswa = a.id_siswa
                    WHERE p.id_guru = :id_guru AND p.id_semester = :id_semester AND p.id_mapel = :id_mapel
                    GROUP BY s.id_siswa, s.nama_siswa
                    ORDER BY s.nama_siswa";
            $db->query($sql);
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            $db->bind('id_mapel', $id_mapel);
            return $db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getRekapSiswaAdmin: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Helper untuk rekap pertemuan admin - FIXED SQL
     */
    private function getRekapPertemuanAdmin($id_guru, $id_mapel, $id_semester)
    {
        $db = new Database();
        try {
            $sql = "SELECT 
                        j.pertemuan_ke,
                        j.tanggal,
                        j.topik_materi,
                        SUM(CASE WHEN a.status_kehadiran = 'H' THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN a.status_kehadiran = 'I' THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN a.status_kehadiran = 'S' THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN a.status_kehadiran = 'A' OR a.status_kehadiran IS NULL THEN 1 ELSE 0 END) as alpha
                    FROM penugasan p
                    JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                    LEFT JOIN absensi a ON j.id_jurnal = a.id_jurnal
                    WHERE p.id_guru = :id_guru AND p.id_semester = :id_semester AND p.id_mapel = :id_mapel
                    GROUP BY j.id_jurnal, j.pertemuan_ke, j.tanggal
                    ORDER BY j.tanggal, j.pertemuan_ke";
            $db->query($sql);
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            $db->bind('id_mapel', $id_mapel);
            return $db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getRekapPertemuanAdmin: " . $e->getMessage());
            return [];
        }
    }
    // =================================================================
    // LEGACY METHODS - DIPERBAIKI SQL
    // =================================================================
    /**
     * Helper: Ambil daftar mapel yang diajar guru - FIXED SQL
     */
    private function getDaftarMapelGuru($id_guru, $id_semester)
    {
        $db = new Database();
        try {
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
            error_log("Error in getDaftarMapelGuru: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Helper: Ambil info mapel (nama, kelas, dll) - FIXED SQL
     */
    private function getMapelInfo($id_guru, $id_semester, $id_mapel)
    {
        $db = new Database();
        try {
            // FIXED: Gunakan nama tabel yang benar
            $sql = "SELECT m.nama_mapel, k.nama_kelas, g.nama_guru, tp.nama_tp, smt.semester
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas  
                    JOIN guru g ON p.id_guru = g.id_guru
                    JOIN semester smt ON p.id_semester = smt.id_semester
                    JOIN tp ON smt.id_tp = tp.id_tp
                    WHERE p.id_guru = :id_guru AND p.id_semester = :id_semester AND m.id_mapel = :id_mapel
                    LIMIT 1";
            $db->query($sql);
            $db->bind('id_guru', $id_guru);
            $db->bind('id_semester', $id_semester);
            $db->bind('id_mapel', $id_mapel);
            return $db->single() ?: [];
        } catch (Exception $e) {
            error_log("Error in getMapelInfo: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Helper: Ambil rincian absen per pertemuan dengan filter - FIXED SQL
     */
    private function getRincianAbsenPerPertemuan($id_guru, $id_semester, $id_mapel, $periode, $tanggal_mulai, $tanggal_akhir)
    {
        $db = new Database();
        try {
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
            // Query utama - ambil data absen per siswa per pertemuan
            $sql = "SELECT 
                        s.id_siswa,
                        s.nama_siswa,
                        s.nisn,
                        j.id_jurnal,
                        j.tanggal,
                        j.pertemuan_ke,
                        j.topik_materi,
                        COALESCE(a.status_kehadiran, 'A') as status_kehadiran,
                        a.waktu_absen,
                        a.keterangan
                    FROM penugasan p
                    JOIN mapel m ON p.id_mapel = m.id_mapel
                    JOIN kelas k ON p.id_kelas = k.id_kelas
                    JOIN jurnal j ON p.id_penugasan = j.id_penugasan
                    JOIN siswa s ON s.id_kelas = k.id_kelas
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
            error_log("Error in getRincianAbsenPerPertemuan: " . $e->getMessage());
            return [
                'siswa_data' => [],
                'pertemuan_headers' => []
            ];
        }
    }
    // =================================================================
    // LEGACY METHODS - MUNGKIN MASIH DIGUNAKAN
    // =================================================================
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
        $this->view('templates/sidebar_guru', $this->data);
        $this->view('guru/rincian_absen_filter', $this->data);
        $this->view('templates/footer', $this->data);
    }
    public function cetakRincianAbsen($id_mapel)
    {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester = $_SESSION['id_semester_aktif'] ?? null;
        // Parameter filter
        $periode = $_GET['periode'] ?? 'semester';
        $tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
        $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
        // Ambil data
        $this->data['mapel_info'] = $this->getMapelInfo($id_guru, $id_semester, $id_mapel);
        $this->data['rincian_data'] = $this->getRincianAbsenPerPertemuan($id_guru, $id_semester, $id_mapel, $periode, $tanggal_mulai, $tanggal_akhir);
        $this->data['filter_info'] = [
            'periode' => $periode,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'tanggal_cetak' => date('d F Y')
        ];
        // Render view
        $wantPdf = isset($_GET['pdf']) && $_GET['pdf'] == 1;
        $renderView = function($view, $data) {
            extract($data);
            ob_start();
            require __DIR__ . "/../views/$view.php";
            return ob_get_clean();
        };
        $html = $renderView('guru/cetak_rincian_absen', $this->data);
        if ($wantPdf) {
            // Setup Dompdf (sama seperti method cetakMapel)
            $dompdfPath = __DIR__ . '/../core/dompdf/autoload.inc.php';
            if (!file_exists($dompdfPath)) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;font-family:Arial,sans-serif;'>Library Dompdf tidak ditemukan di core/dompdf/</div>";
                echo $html;
                return;
            }
            require_once $dompdfPath;
            try {
                $dompdf = new \Dompdf\Dompdf([
                    'isRemoteEnabled' => true, 
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'Arial'
                ]);
                $dompdf->loadHtml($html, 'UTF-8');
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();
                $mapel_name = $this->data['mapel_info']['nama_mapel'] ?? 'Mapel';
                $filename = 'Rincian_Absen_' . preg_replace('/\s+/', '_', $mapel_name) . '_' . date('Y-m-d') . '.pdf';
                $dompdf->stream($filename, ['Attachment' => true]);
                return;
            } catch (Exception $e) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;color:#ef4444;'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo $html;
                return;
            }
        }
        // Tampilkan halaman cetak HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }
    /**
     * Detail Riwayat Jurnal per Mapel untuk Admin - FIXED SQL
     */
    public function detailRiwayatAdmin($id_guru, $id_mapel)
    {
        $this->data['judul'] = 'Detail Riwayat Jurnal Admin';
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;
        $this->data['detail_jurnal'] = [];
        $this->data['detail_absensi_siswa'] = [];
        $this->data['nama_mapel'] = 'Mapel Tidak Ditemukan';
        $this->data['nama_guru'] = 'Guru Tidak Ditemukan';
        if ($id_guru && $id_semester_aktif && $id_mapel) {
            // Ambil detail jurnal menggunakan method yang sudah ada di model
            $this->data['detail_jurnal'] = 
                $this->model('Jurnal_model')->getDetailRiwayatByMapel($id_guru, $id_semester_aktif, $id_mapel);
            // Ambil detail absensi per siswa
            $this->data['detail_absensi_siswa'] = 
                $this->model('Jurnal_model')->getDetailAbsensiPerMapel($id_guru, $id_semester_aktif, $id_mapel);
            // Set nama guru dan mapel dari data yang diambil
            if (!empty($this->data['detail_jurnal'])) {
                $this->data['nama_mapel'] = $this->data['detail_jurnal'][0]['nama_mapel'] ?? 'Mapel';
                $this->data['nama_guru'] = $this->data['detail_jurnal'][0]['nama_guru'] ?? 'Guru';
            } else {
                // Fallback: ambil nama dari master data
                $guruInfo = $this->model('Guru_model')->getGuruById($id_guru);
                $mapelInfo = $this->model('Mapel_model')->getMapelById($id_mapel);
                if (!empty($guruInfo['nama_guru'])) {
                    $this->data['nama_guru'] = $guruInfo['nama_guru'];
                }
                if (!empty($mapelInfo['nama_mapel'])) {
                    $this->data['nama_mapel'] = $mapelInfo['nama_mapel'];
                }
            }
        }
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/detail_riwayat_admin', $this->data);
        $this->view('templates/footer', $this->data);
    }
    // --- Helper aman untuk ambil daftar guru dari berbagai versi model
private function _getGuruListSafe() {
    $m = $this->model('Guru_model');
    if (method_exists($m, 'getAll'))       return $m->getAll();
    if (method_exists($m, 'getAllGuru'))   return $m->getAllGuru();
    if (method_exists($m, 'getGuru'))      return $m->getGuru();
    if (method_exists($m, 'getAllData'))   return $m->getAllData();
    if (method_exists($m, 'all'))          return $m->all();
    // fallback super aman (silakan sesuaikan nama tabel kolom)
    if (property_exists($m, 'db')) {
        $m->db->query("SELECT id_guru, nama_guru FROM guru ORDER BY nama_guru ASC");
        return $m->db->resultSet();
    }
    return [];
}
// --- Helper aman untuk ambil daftar mapel
private function _getMapelListSafe() {
    $m = $this->model('Mapel_model');
    if (method_exists($m, 'getAll'))        return $m->getAll();
    if (method_exists($m, 'getAllMapel'))   return $m->getAllMapel();
    if (method_exists($m, 'getMapel'))      return $m->getMapel();
    if (method_exists($m, 'getAllData'))    return $m->getAllData();
    if (method_exists($m, 'all'))           return $m->all();
    // fallback super aman (silakan sesuaikan)
    if (property_exists($m, 'db')) {
        $m->db->query("SELECT id_mapel, nama_mapel FROM mapel ORDER BY nama_mapel ASC");
        return $m->db->resultSet();
    }
    return [];
}
    // =================================================================
    // IMPORT SISWA EXCEL - SESUAI DATABASE SCHEMA YANG ADA
    // =================================================================
    /**
     * Halaman import siswa dari Excel
     */
    public function importSiswa()
    {
        $this->data['judul'] = 'Import Data Siswa Excel';
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/import_siswa', $this->data);
        $this->view('templates/footer', $this->data);
    }
    /**
     * Proses import siswa dari Excel via AJAX
     */
    public function prosesImportSiswa()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
            exit;
        }
        // Baca input JSON
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['data'])) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }
        $excelData = $input['data'];
        if (empty($excelData)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada data untuk diimport']);
            exit;
        }
        // Validasi dan proses import
        $result = $this->processImportData($excelData);
        echo json_encode($result);
        exit;
    }
    /**
     * Proses validasi dan import data siswa
     */
    private function processImportData($excelData)
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $processedData = [];
        // Ambil NISN yang sudah ada untuk validasi duplikasi
        $existingNisn = $this->getExistingNisn();
        $currentBatchNisn = [];
        try {
            foreach ($excelData as $index => $row) {
                $rowNum = $index + 1;
                $rowErrors = [];
                // Sanitize data
                $cleanData = [
                    'nisn' => trim($row['nisn'] ?? ''),
                    'nama_siswa' => trim($row['nama_siswa'] ?? ''),
                    'jenis_kelamin' => strtoupper(trim($row['jenis_kelamin'] ?? '')),
                    'password' => trim($row['password'] ?? ''),
                    'tgl_lahir' => !empty($row['tgl_lahir']) ? $row['tgl_lahir'] : null
                ];
                // Validasi NISN
                if (empty($cleanData['nisn'])) {
                    $rowErrors[] = "Baris {$rowNum}: NISN tidak boleh kosong";
                } elseif (!preg_match('/^\d{10}$/', $cleanData['nisn'])) {
                    // Allow flexible NISN length, but warn if not 10 digits
                    if (!preg_match('/^\d+$/', $cleanData['nisn'])) {
                        $rowErrors[] = "Baris {$rowNum}: NISN harus berisi angka";
                    }
                } elseif (in_array($cleanData['nisn'], $existingNisn)) {
                    $rowErrors[] = "Baris {$rowNum}: NISN {$cleanData['nisn']} sudah terdaftar";
                } elseif (in_array($cleanData['nisn'], $currentBatchNisn)) {
                    $rowErrors[] = "Baris {$rowNum}: NISN {$cleanData['nisn']} duplikat dalam file";
                } else {
                    $currentBatchNisn[] = $cleanData['nisn'];
                }
                // Validasi Nama
                if (empty($cleanData['nama_siswa'])) {
                    $rowErrors[] = "Baris {$rowNum}: Nama siswa tidak boleh kosong";
                } elseif (strlen($cleanData['nama_siswa']) < 2) {
                    $rowErrors[] = "Baris {$rowNum}: Nama siswa minimal 2 karakter";
                }
                // Validasi Jenis Kelamin
                if (empty($cleanData['jenis_kelamin'])) {
                    $rowErrors[] = "Baris {$rowNum}: Jenis kelamin tidak boleh kosong";
                } else {
                    // Normalize jenis kelamin
                    $jk = strtoupper($cleanData['jenis_kelamin']);
                    if (in_array($jk, ['L', 'LAKI-LAKI', 'LAKI', 'M', 'MALE'])) {
                        $cleanData['jenis_kelamin'] = 'L';
                    } elseif (in_array($jk, ['P', 'PEREMPUAN', 'WANITA', 'F', 'FEMALE'])) {
                        $cleanData['jenis_kelamin'] = 'P';
                    } else {
                        $rowErrors[] = "Baris {$rowNum}: Jenis kelamin harus L atau P";
                    }
                }
                // Validasi Password
                if (empty($cleanData['password'])) {
                    $rowErrors[] = "Baris {$rowNum}: Password tidak boleh kosong";
                } elseif (strlen($cleanData['password']) < 6) {
                    $rowErrors[] = "Baris {$rowNum}: Password minimal 6 karakter";
                }
                // Jika valid, proses insert
                if (empty($rowErrors)) {
                    $insertResult = $this->insertSiswaWithAccount($cleanData);
                    if ($insertResult['success']) {
                        $successCount++;
                        $processedData[] = $cleanData;
                    } else {
                        $errorCount++;
                        $errors[] = "Baris {$rowNum}: " . $insertResult['error'];
                    }
                } else {
                    $errorCount++;
                    $errors = array_merge($errors, $rowErrors);
                }
            }
            return [
                'success' => true,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_processed' => count($excelData),
                'errors' => $errors,
                'message' => "Import selesai. {$successCount} siswa berhasil ditambahkan, {$errorCount} error."
            ];
        } catch (Exception $e) {
            error_log("processImportData error: " . $e->getMessage());
            return [
                'success' => false,
                'success_count' => $successCount,
                'error_count' => count($excelData) - $successCount,
                'total_processed' => count($excelData),
                'errors' => ["Error sistem: " . $e->getMessage()],
                'message' => 'Import gagal karena error sistem'
            ];
        }
    }
    /**
     * Insert siswa beserta akun dalam satu transaksi
     */
    private function insertSiswaWithAccount($data)
    {
        try {
            // Insert siswa terlebih dahulu
            $idSiswaBaru = $this->model('Siswa_model')->tambahDataSiswa($data);
            if ($idSiswaBaru) {
                // Buat akun user
                $dataAkun = [
                    'username' => $data['nisn'],
                    'password' => $data['password'],
                    'nama_lengkap' => $data['nama_siswa'],
                    'role' => 'siswa',
                    'id_ref' => $idSiswaBaru
                ];
                $userModel = $this->model('User_model');
                $akunId = $userModel->buatAkun($dataAkun);
                if ($akunId) {
                    return ['success' => true, 'siswa_id' => $idSiswaBaru, 'user_id' => $akunId];
                } else {
                    // Rollback siswa jika gagal buat akun
                    $this->model('Siswa_model')->hapusDataSiswa($idSiswaBaru);
                    return ['success' => false, 'error' => 'Gagal membuat akun untuk ' . $data['nama_siswa']];
                }
            } else {
                return ['success' => false, 'error' => 'Gagal menyimpan data siswa ' . $data['nama_siswa']];
            }
        } catch (Exception $e) {
            error_log("insertSiswaWithAccount error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    /**
     * Ambil semua NISN yang sudah ada di database
     */
    private function getExistingNisn()
    {
        try {
            $siswaModel = $this->model('Siswa_model');
            $allSiswa = $siswaModel->getAllSiswa();
            return array_column($allSiswa, 'nisn');
        } catch (Exception $e) {
            error_log("getExistingNisn error: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Download template Excel untuk import siswa
     */
    public function downloadTemplateSiswa()
    {
        // Set headers untuk download CSV (kompatibel dengan Excel)
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Template_Import_Siswa.csv"');
        header('Cache-Control: max-age=0');
        // Template data
        $templateData = [
            ['NISN', 'Nama Siswa', 'Jenis Kelamin', 'Password'],
            ['0123456789', 'Ahmad Fauzi', 'L', 'password123'],
            ['0123456790', 'Siti Nurhaliza', 'P', 'password456'],
            ['0123456791', 'Budi Santoso', 'L', 'password789'],
            ['', '', '', ''],
            ['KETERANGAN:', '', '', ''],
            ['NISN: Nomor Induk Siswa Nasional (angka)', '', '', ''],
            ['Jenis Kelamin: L (Laki-laki) atau P (Perempuan)', '', '', ''],
            ['Password: Minimal 6 karakter', '', '', ''],
            ['Hapus baris contoh dan keterangan sebelum import', '', '', '']
        ];
        $output = fopen('php://output', 'w');
        // Add BOM untuk Excel UTF-8 support
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($templateData as $row) {
            fputcsv($output, $row, ';'); // Semicolon separator untuk Excel Indonesia
        }
        fclose($output);
        exit;
    }
    /**
     * Cek ketersediaan NISN via AJAX
     */
    public function cekNisnTersedia()
    {
        header('Content-Type: application/json');
        if (!isset($_GET['nisn'])) {
            echo json_encode(['available' => false, 'message' => 'NISN tidak diberikan']);
            exit;
        }
        $nisn = trim($_GET['nisn']);
        if (empty($nisn)) {
            echo json_encode(['available' => false, 'message' => 'NISN kosong']);
            exit;
        }
        // Cek di database menggunakan model
        $siswaModel = $this->model('Siswa_model');
        $exists = $siswaModel->cekNisnExists($nisn);
        if ($exists) {
            echo json_encode(['available' => false, 'message' => 'NISN sudah terdaftar']);
        } else {
            echo json_encode(['available' => true, 'message' => 'NISN tersedia']);
        }
        exit;
    }
    /**
     * Export data siswa ke Excel/CSV
     */
    public function exportSiswaExcel()
    {
        try {
            $dataSiswa = $this->model('Siswa_model')->getAllSiswa();
            // Set headers untuk download CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="Export_Data_Siswa_' . date('Y-m-d') . '.csv"');
            header('Cache-Control: max-age=0');
            $output = fopen('php://output', 'w');
            // Add BOM untuk Excel UTF-8 support
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            // Header
            fputcsv($output, ['NISN', 'Nama Siswa', 'Jenis Kelamin', 'Tanggal Lahir', 'Status', 'Password', 'ID Siswa'], ';');
            // Data
            foreach ($dataSiswa as $siswa) {
                $row = [
                    $siswa['nisn'],
                    $siswa['nama_siswa'],
                    $siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
                    $siswa['tgl_lahir'] ?? '',
                    $siswa['status_siswa'],
                    $siswa['password_plain'] ?? '',
                    $siswa['id_siswa']
                ];
                fputcsv($output, $row, ';');
            }
            fclose($output);
            exit;
        } catch (Exception $e) {
            error_log("Export error: " . $e->getMessage());
            header('Location: ' . BASEURL . '/admin/siswa?error=export_failed');
            exit;
        }
    }
    /**
     * Preview import data untuk validasi
     */
    public function previewImportSiswa()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['data'])) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }
        $excelData = $input['data'];
        $validatedData = $this->validateExcelData($excelData);
        echo json_encode([
            'success' => true,
            'preview' => $validatedData,
            'summary' => [
                'total' => count($excelData),
                'valid' => $validatedData['valid_count'],
                'error' => $validatedData['error_count']
            ]
        ]);
        exit;
    }
    /**
     * Validasi data Excel sebelum import
     */
    private function validateExcelData($excelData)
    {
        $validData = [];
        $errors = [];
        $existingNisn = $this->getExistingNisn();
        $currentBatchNisn = [];
        foreach ($excelData as $index => $row) {
            $rowErrors = [];
            $rowNum = $index + 1;
            // Sanitize data
            $cleanData = [
                'nisn' => trim($row['nisn'] ?? ''),
                'nama_siswa' => trim($row['nama_siswa'] ?? ''),
                'jenis_kelamin' => strtoupper(trim($row['jenis_kelamin'] ?? '')),
                'password' => trim($row['password'] ?? ''),
                'tgl_lahir' => !empty($row['tgl_lahir']) ? $row['tgl_lahir'] : null
            ];
            // Validasi NISN
            if (empty($cleanData['nisn'])) {
                $rowErrors[] = "NISN tidak boleh kosong";
            } elseif (!preg_match('/^\d+$/', $cleanData['nisn'])) {
                $rowErrors[] = "NISN harus berisi angka";
            } elseif (in_array($cleanData['nisn'], $existingNisn)) {
                $rowErrors[] = "NISN sudah terdaftar";
            } elseif (in_array($cleanData['nisn'], $currentBatchNisn)) {
                $rowErrors[] = "NISN duplikat dalam file";
            } else {
                $currentBatchNisn[] = $cleanData['nisn'];
            }
            // Validasi Nama
            if (empty($cleanData['nama_siswa'])) {
                $rowErrors[] = "Nama siswa tidak boleh kosong";
            } elseif (strlen($cleanData['nama_siswa']) < 2) {
                $rowErrors[] = "Nama siswa minimal 2 karakter";
            }
            // Validasi Jenis Kelamin
            if (empty($cleanData['jenis_kelamin'])) {
                $rowErrors[] = "Jenis kelamin tidak boleh kosong";
            } else {
                $jk = strtoupper($cleanData['jenis_kelamin']);
                if (in_array($jk, ['L', 'LAKI-LAKI', 'LAKI', 'M', 'MALE'])) {
                    $cleanData['jenis_kelamin'] = 'L';
                } elseif (in_array($jk, ['P', 'PEREMPUAN', 'WANITA', 'F', 'FEMALE'])) {
                    $cleanData['jenis_kelamin'] = 'P';
                } else {
                    $rowErrors[] = "Jenis kelamin harus L atau P";
                }
            }
            // Validasi Password
            if (empty($cleanData['password'])) {
                $rowErrors[] = "Password tidak boleh kosong";
            } elseif (strlen($cleanData['password']) < 6) {
                $rowErrors[] = "Password minimal 6 karakter";
            }
            if (empty($rowErrors)) {
                $validData[] = $cleanData;
            } else {
                $errors[] = "Baris {$rowNum}: " . implode(', ', $rowErrors);
            }
        }
        return [
            'valid_data' => $validData,
            'valid_count' => count($validData),
            'error_count' => count($errors),
            'errors' => $errors
        ];
    }
    // =================================================================
    // UPDATE METHOD SISWA YANG SUDAH ADA - TAMBAH FLASH MESSAGE
    // =================================================================
    // =================================================================
    // UTILITY METHODS UNTUK IMPORT
    // =================================================================
    /**
     * Generate batch ID untuk tracking import
     */
    private function generateBatchId()
    {
        return 'IMP_' . date('YmdHis') . '_' . uniqid();
    }
    /**
     * Log import activity (simple version tanpa tabel khusus)
     */
    private function logImportActivity($action, $details)
    {
        try {
            // Log ke file jika tidak ada tabel activity_log
            $logMessage = date('Y-m-d H:i:s') . " - User: " . ($_SESSION['username'] ?? 'unknown') . 
                         " - Action: {$action} - Details: " . json_encode($details) . "
";
            $logFile = APPROOT . '/logs/import_activity.log';
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
            return true;
        } catch (Exception $e) {
            error_log("logImportActivity error: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Cleanup import logs lama
     */
    public function cleanupImportLogs()
    {
        try {
            $logFile = APPROOT . '/logs/import_activity.log';
            if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) { // 10MB
                // Backup dan truncate log file
                $backupFile = $logFile . '.' . date('Y-m-d-H-i-s') . '.backup';
                copy($logFile, $backupFile);
                file_put_contents($logFile, '');
            }
            return true;
        } catch (Exception $e) {
            error_log("cleanupImportLogs error: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Validasi format file upload
     */
    private function validateUploadedFile($file)
    {
        $allowedTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv'
        ];
        $allowedExtensions = ['xls', 'xlsx', 'csv'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            return ['valid' => false, 'error' => 'Format file tidak didukung. Gunakan .xlsx, .xls, atau .csv'];
        }
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            return ['valid' => false, 'error' => 'Ukuran file terlalu besar. Maksimal 5MB'];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'Error upload file: ' . $file['error']];
        }
        return ['valid' => true, 'error' => null];
    }
    /**
     * Process upload file dan return data
     */
    public function processUploadedExcel()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
            exit;
        }
        if (!isset($_FILES['excel_file'])) {
            echo json_encode(['success' => false, 'message' => 'File tidak ditemukan']);
            exit;
        }
        $file = $_FILES['excel_file'];
        // Validasi file
        $validation = $this->validateUploadedFile($file);
        if (!$validation['valid']) {
            echo json_encode(['success' => false, 'message' => $validation['error']]);
            exit;
        }
        try {
            // Process file berdasarkan extension
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($fileExtension === 'csv') {
                $data = $this->processCSVFile($file['tmp_name']);
            } else {
                // Untuk .xls/.xlsx, perlu library tambahan atau convert ke CSV dulu
                $data = $this->processExcelFile($file['tmp_name']);
            }
            echo json_encode([
                'success' => true,
                'data' => $data,
                'filename' => $file['name'],
                'message' => count($data) . ' baris data berhasil dibaca'
            ]);
        } catch (Exception $e) {
            error_log("processUploadedExcel error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error memproses file: ' . $e->getMessage()]);
        }
        exit;
    }
    /**
     * Process CSV file
     */
    private function processCSVFile($filePath)
    {
        $data = [];
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $isFirstRow = true;
            while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Skip header row
                if ($isFirstRow) {
                    $isFirstRow = false;
                    continue;
                }
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                // Map ke struktur yang diharapkan
                $data[] = [
                    'nisn' => $row[0] ?? '',
                    'nama_siswa' => $row[1] ?? '',
                    'jenis_kelamin' => $row[2] ?? '',
                    'password' => $row[3] ?? '',
                    'tgl_lahir' => $row[4] ?? null
                ];
            }
            fclose($handle);
        }
        return $data;
    }
    /**
     * Process Excel file (basic implementation)
     */
    private function processExcelFile($filePath)
    {
        // Untuk implementasi sederhana, convert Excel ke CSV dulu
        // Atau gunakan library PHP Excel seperti PhpSpreadsheet
        // Implementasi fallback: return empty atau error
        throw new Exception("Excel file processing memerlukan library tambahan. Gunakan format CSV untuk sementara.");
    }
    // =================================================================
    // BATCH OPERATIONS
    // =================================================================
    /**
     * Batch delete siswa (untuk cleanup import yang error)
     */
    public function batchDeleteSiswa()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['ids']) || !is_array($input['ids'])) {
            echo json_encode(['success' => false, 'message' => 'Data ID tidak valid']);
            exit;
        }
        $ids = array_filter($input['ids'], 'is_numeric');
        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada ID yang valid']);
            exit;
        }
        try {
            $deletedCount = 0;
            $userModel = $this->model('User_model');
            $siswaModel = $this->model('Siswa_model');
            foreach ($ids as $id) {
                // Hapus akun user terlebih dahulu
                $userModel->hapusAkun($id, 'siswa');
                // Hapus data siswa
                if ($siswaModel->hapusDataSiswa($id) > 0) {
                    $deletedCount++;
                }
            }
            echo json_encode([
                'success' => true,
                'message' => "{$deletedCount} siswa berhasil dihapus",
                'deleted_count' => $deletedCount
            ]);
        } catch (Exception $e) {
            error_log("Batch delete error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    /**
     * Generate password otomatis untuk siswa yang belum punya
     */
    public function generatePasswordSiswa()
    {
        header('Content-Type: application/json');
        try {
            $siswaModel = $this->model('Siswa_model');
            $userModel = $this->model('User_model');
            // Ambil siswa yang belum punya password
            $siswaList = $siswaModel->getAllSiswa();
            $siswaWithoutPassword = array_filter($siswaList, function($siswa) {
                return empty($siswa['password_plain']);
            });
            $updatedCount = 0;
            foreach ($siswaWithoutPassword as $siswa) {
                // Generate password: 3 digit terakhir NISN + nama depan
                $password = $this->generateSimplePassword($siswa['nisn'], $siswa['nama_siswa']);
                // Update atau buat akun
                $existingUser = $userModel->getUserByIdRef($siswa['id_siswa'], 'siswa');
                if ($existingUser) {
                    // Update password existing user
                    if ($userModel->updatePassword($siswa['id_siswa'], 'siswa', $password)) {
                        $updatedCount++;
                    }
                } else {
                    // Buat akun baru
                    $dataAkun = [
                        'username' => $siswa['nisn'],
                        'password' => $password,
                        'nama_lengkap' => $siswa['nama_siswa'],
                        'role' => 'siswa',
                        'id_ref' => $siswa['id_siswa']
                    ];
                    if ($userModel->buatAkun($dataAkun)) {
                        $updatedCount++;
                    }
                }
            }
            echo json_encode([
                'success' => true,
                'message' => "Password berhasil digenerate untuk {$updatedCount} siswa",
                'updated_count' => $updatedCount
            ]);
        } catch (Exception $e) {
            error_log("generatePasswordSiswa error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    /**
     * Generate password sederhana
     */
    private function generateSimplePassword($nisn, $nama)
    {
        $lastDigits = substr($nisn, -3);
        $namePrefix = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $nama), 0, 3));
        return $lastDigits . $namePrefix;
    }
// =================================================================
    // CETAK LAPORAN REKAP ADMIN - METHOD BARU
    // =================================================================
    /**
     * Cetak Laporan Rekap Absensi untuk Admin
     * Format sederhana sesuai template PDF
     */
    public function cetakLaporanRekap()
    {
        // Ambil parameter dari GET
        $id_kelas = $_GET['id_kelas'] ?? null;
        $id_mapel = $_GET['id_mapel'] ?? null;
        $periode = $_GET['periode'] ?? 'semester';
        $tanggal_mulai = $_GET['tanggal_mulai'] ?? null;
        $tanggal_akhir = $_GET['tanggal_akhir'] ?? null;
        $mode = $_GET['mode'] ?? 'rekap';
        $isPdfMode = isset($_GET['pdf']) && $_GET['pdf'] == '1';
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;
        // Validasi input minimal
        if (empty($id_kelas)) {
            echo "<div style='padding:20px;color:#ef4444;'>Error: Kelas harus dipilih</div>";
            return;
        }
        // Ambil info kelas
        $kelasModel = $this->model('Kelas_model');
        $this->data['kelas_info'] = $kelasModel->getKelasById($id_kelas);
        // Ambil info mapel jika dipilih
        $this->data['mapel_info'] = null;
        $this->data['guru_info'] = null;
        if (!empty($id_mapel)) {
            $mapelModel = $this->model('Mapel_model');
            $this->data['mapel_info'] = $mapelModel->getMapelById($id_mapel);
            // Ambil guru yang mengajar mapel di kelas ini
            $penugasanModel = $this->model('Penugasan_model');
            $guru_pengampu = $penugasanModel->getGuruByMapelKelas($id_mapel, $id_kelas, $id_semester_aktif);
            if (!empty($guru_pengampu)) {
                $this->data['guru_info'] = $guru_pengampu;
            }
        }
        // Ambil info semester dan TP
        $semesterModel = $this->model('TahunPelajaran_model');
        $this->data['semester_info'] = $semesterModel->getSemesterById($id_semester_aktif);
        $this->data['tp_info'] = null;
        if (isset($this->data['semester_info']['id_tp'])) {
            $this->data['tp_info'] = $semesterModel->getTahunPelajaranById($this->data['semester_info']['id_tp']);
        }
        // Setup filter info untuk template
        $this->data['filter_info'] = [
            'periode' => $periode,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_akhir' => $tanggal_akhir,
            'tanggal_cetak' => date('d F Y')
        ];
        // Ambil data rekap absensi
        $filter = [
            'id_kelas' => $id_kelas,
            'id_mapel' => $id_mapel,
            'periode' => $periode,
            'tgl_mulai' => $tanggal_mulai,
            'tgl_selesai' => $tanggal_akhir,
            'id_semester' => $id_semester_aktif
        ];
        $laporanModel = $this->model('Laporan_model');
        $this->data['rekap_absensi'] = $laporanModel->getRekapAbsensiPerKelas($filter);
        // Jika mode rincian dan ada mapel spesifik, ambil data rincian
        if ($mode === 'rincian' && !empty($id_mapel) && !empty($this->data['guru_info']['id_guru'])) {
            $this->data['rincian_data'] = $this->getRincianAbsenAdmin(
                $id_semester_aktif, 
                $id_mapel, 
                $this->data['guru_info']['id_guru'], 
                $periode, 
                $tanggal_mulai, 
                $tanggal_akhir
            );
        }
        // Render view dengan template cetak
        $renderView = function($view, $data) {
            extract($data);
            ob_start();
            require __DIR__ . "/../views/$view.php";
            return ob_get_clean();
        };
        // Gunakan template cetak admin
        $html = $renderView('admin/cetak_laporan_rekap', $this->data);
        if ($isPdfMode) {
            // Setup Dompdf untuk PDF
            $dompdfPath = __DIR__ . '/../core/dompdf/autoload.inc.php';
            if (!file_exists($dompdfPath)) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;font-family:Arial,sans-serif;'>Library Dompdf tidak ditemukan di core/dompdf/</div>";
                echo $html;
                return;
            }
            require_once $dompdfPath;
            try {
                $dompdf = new \Dompdf\Dompdf([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'defaultFont' => 'Arial'
                ]);
                $dompdf->loadHtml($html, 'UTF-8');
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $kelas_name = $this->data['kelas_info']['nama_kelas'] ?? 'Kelas';
                $mapel_name = $this->data['mapel_info']['nama_mapel'] ?? 'Semua_Mapel';
                $filename = 'Laporan_Kehadiran_' . preg_replace('/\s+/', '_', $kelas_name) . '_' . preg_replace('/\s+/', '_', $mapel_name) . '_' . date('Y-m-d') . '.pdf';
                $dompdf->stream($filename, ['Attachment' => true]);
                return;
            } catch (Exception $e) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<div style='padding:20px;color:#ef4444;'>Error PDF: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo $html;
                return;
            }
        }
        // Tampilkan halaman cetak HTML
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }

    /**
     * Konfigurasi QR Code
     */
    public function configQR() {
        $this->data['judul'] = 'Konfigurasi QR Code';
        
        // Load config file
        $configFile = __DIR__ . '/../../config/qrcode.php';
        $config = [];
        
        if (file_exists($configFile)) {
            include $configFile;
            $config = [
                'QR_API_PROVIDER' => defined('QR_API_PROVIDER') ? QR_API_PROVIDER : 'qrserver',
                'QR_CUSTOM_URL' => defined('QR_CUSTOM_URL') ? QR_CUSTOM_URL : '',
                'QR_WEBSITE_URL' => defined('QR_WEBSITE_URL') ? QR_WEBSITE_URL : 'http://localhost/absen',
                'QR_SIZE' => defined('QR_SIZE') ? QR_SIZE : '200x200',
                'QR_DISPLAY_SIZE' => defined('QR_DISPLAY_SIZE') ? str_replace('px', '', QR_DISPLAY_SIZE) : '60',
                'QR_TOKEN_EXPIRY' => defined('QR_TOKEN_EXPIRY') ? QR_TOKEN_EXPIRY : '365',
                'QR_POSITION' => defined('QR_POSITION') ? QR_POSITION : 'bottom-left',
                'QR_DISPLAY_TEXT' => defined('QR_DISPLAY_TEXT') ? QR_DISPLAY_TEXT : 'Scan untuk validasi',
                'QR_TOKEN_SALT' => defined('QR_TOKEN_SALT') ? QR_TOKEN_SALT : 'rapor_2024_secret_key'
            ];
        }
        
        $this->data['config'] = $config;
        
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/config_qr', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Simpan Konfigurasi QR Code
     */
    public function simpanConfigQR() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/configQR');
            exit;
        }

        $provider = $_POST['qr_provider'] ?? 'qrserver';
        $customUrl = $_POST['qr_custom_url'] ?? '';
        $websiteUrl = $_POST['qr_website_url'] ?? 'http://localhost/absen';
        $size = $_POST['qr_size'] ?? '200x200';
        $displaySize = $_POST['qr_display_size'] ?? '60';
        $tokenExpiry = $_POST['qr_token_expiry'] ?? '365';
        $position = $_POST['qr_position'] ?? 'bottom-left';
        $displayText = $_POST['qr_display_text'] ?? 'Scan untuk validasi';
        $tokenSalt = $_POST['qr_token_salt'] ?? 'rapor_2024_secret_key';

        // Generate config file content
        $configContent = "<?php\n\n";
        $configContent .= "/**\n * QR Code Configuration\n * Auto-generated on " . date('Y-m-d H:i:s') . "\n */\n\n";
        $configContent .= "define('QR_API_PROVIDER', '{$provider}');\n";
        $configContent .= "define('QR_API_QRSERVER', 'https://api.qrserver.com/v1/create-qr-code/');\n";
        $configContent .= "define('QR_CUSTOM_URL', '{$customUrl}');\n";
        $configContent .= "define('QR_WEBSITE_URL', '{$websiteUrl}');\n";
        $configContent .= "define('QR_SIZE', '{$size}');\n";
        $configContent .= "define('QR_DISPLAY_SIZE', '{$displaySize}px');\n";
        $configContent .= "define('QR_TOKEN_EXPIRY', {$tokenExpiry});\n";
        $configContent .= "define('QR_POSITION', '{$position}');\n";
        $configContent .= "define('QR_DISPLAY_TEXT', '" . addslashes($displayText) . "');\n";
        $configContent .= "define('QR_TOKEN_SALT', '" . addslashes($tokenSalt) . "');\n\n";
        
        // Add helper functions
        $configContent .= "function getQRCodeApiUrl(\$data) {\n";
        $configContent .= "    \$encodedData = urlencode(\$data);\n";
        $configContent .= "    // Parse size dari format \"250x250\" ke integer untuk provider yang membutuhkan\n";
        $configContent .= "    \$sizeInt = (int)explode('x', QR_SIZE)[0];\n";
        $configContent .= "    \n";
        $configContent .= "    switch (QR_API_PROVIDER) {\n";
        $configContent .= "        case 'qrserver':\n";
        $configContent .= "            return QR_API_QRSERVER . '?size=' . QR_SIZE . '&data=' . \$encodedData;\n";
        $configContent .= "        case 'quickchart':\n";
        $configContent .= "            return 'https://quickchart.io/qr?text=' . \$encodedData . '&size=' . \$sizeInt;\n";
        $configContent .= "        case 'goqr':\n";
        $configContent .= "            return 'https://api.qrserver.com/v1/create-qr-code/?size=' . QR_SIZE . '&data=' . \$encodedData;\n";
        $configContent .= "        case 'custom':\n";
        $configContent .= "            return str_replace(['{DATA}', '{SIZE}'], [\$encodedData, QR_SIZE], QR_CUSTOM_URL);\n";
        $configContent .= "        default:\n";
        $configContent .= "            return QR_API_QRSERVER . '?size=' . QR_SIZE . '&data=' . \$encodedData;\n";
        $configContent .= "    }\n";
        $configContent .= "}\n\n";
        $configContent .= "function generateQRToken(\$siswaId, \$jenisRapor, \$nisn) {\n";
        $configContent .= "    \$data = \$siswaId . '|' . \$jenisRapor . '|' . \$nisn . '|' . QR_TOKEN_SALT;\n";
        $configContent .= "    return hash('sha256', \$data);\n";
        $configContent .= "}\n\n";
        
        // Add generatePDFQRCode function
        $configContent .= "/**\n";
        $configContent .= " * Generate PDF QR Code with validation token\n";
        $configContent .= " * @param string \$docType Document type (rapor, pembayaran, absensi, performa_guru, performa_siswa, etc)\n";
        $configContent .= " * @param mixed \$docId Document identifier\n";
        $configContent .= " * @param array \$additionalData Extra metadata for validation\n";
        $configContent .= " * @return string Base64 QR code image data URL\n";
        $configContent .= " */\n";
        $configContent .= "function generatePDFQRCode(\$docType, \$docId, \$additionalData = []) {\n";
        $configContent .= "    try {\n";
        $configContent .= "        // Create validation token\n";
        $configContent .= "        \$tokenData = [\n";
        $configContent .= "            'doc_type' => \$docType,\n";
        $configContent .= "            'doc_id' => \$docId,\n";
        $configContent .= "            'timestamp' => time(),\n";
        $configContent .= "            'expires' => time() + (QR_TOKEN_EXPIRY * 24 * 60 * 60)\n";
        $configContent .= "        ];\n";
        $configContent .= "        \n";
        $configContent .= "        // Merge additional data\n";
        $configContent .= "        if (!empty(\$additionalData)) {\n";
        $configContent .= "            \$tokenData = array_merge(\$tokenData, \$additionalData);\n";
        $configContent .= "        }\n";
        $configContent .= "        \n";
        $configContent .= "        // Create secure token\n";
        $configContent .= "        \$token = hash_hmac('sha256', json_encode(\$tokenData), QR_TOKEN_SALT);\n";
        $configContent .= "        \n";
        $configContent .= "        // Save token to database for validation\n";
        $configContent .= "        try {\n";
        $configContent .= "            \$APPROOT = realpath(__DIR__ . '/..');\n";
        $configContent .= "            require_once \$APPROOT . '/config/database.php';\n";
        $configContent .= "            require_once \$APPROOT . '/app/core/Database.php';\n";
        $configContent .= "            require_once \$APPROOT . '/app/models/QRValidation_model.php';\n";
        $configContent .= "            \$qrModel = new QRValidation_model();\n";
        $configContent .= "            \$qrModel->ensureTables(); // Create table if not exists\n";
        $configContent .= "            \n";
        $configContent .= "            // Store token with correct parameters\n";
        $configContent .= "            \$expiryDays = QR_TOKEN_EXPIRY > 0 ? (int)QR_TOKEN_EXPIRY : 0;\n";
        $configContent .= "            \$identifier = \$docId; // Use doc ID as identifier\n";
        $configContent .= "            \n";
        $configContent .= "            // Save token using storeToken method\n";
        $configContent .= "            \$qrModel->storeToken(\$docType, \$docId, \$identifier, \$token, \$expiryDays, \$additionalData);\n";
        $configContent .= "        } catch (Exception \$e) {\n";
        $configContent .= "            error_log('Failed to save QR token to database: ' . \$e->getMessage());\n";
        $configContent .= "            // Continue anyway - QR will still be generated\n";
        $configContent .= "        }\n";
        $configContent .= "        \n";
        $configContent .= "        // Create validation URL\n";
        $configContent .= "        \$validationUrl = QR_WEBSITE_URL . '/validate?token=' . \$token . '&type=' . urlencode(\$docType);\n";
        $configContent .= "        \n";
        $configContent .= "        // Get QR code image from API\n";
        $configContent .= "        \$qrApiUrl = getQRCodeApiUrl(\$validationUrl);\n";
        $configContent .= "        \n";
        $configContent .= "        // Fetch QR code image\n";
        $configContent .= "        \$qrImageData = @file_get_contents(\$qrApiUrl);\n";
        $configContent .= "        \n";
        $configContent .= "        if (\$qrImageData === false) {\n";
        $configContent .= "            error_log('Failed to generate QR code from API: ' . \$qrApiUrl);\n";
        $configContent .= "            return '';\n";
        $configContent .= "        }\n";
        $configContent .= "        \n";
        $configContent .= "        // Convert to base64 data URL\n";
        $configContent .= "        \$base64 = base64_encode(\$qrImageData);\n";
        $configContent .= "        return 'data:image/png;base64,' . \$base64;\n";
        $configContent .= "        \n";
        $configContent .= "    } catch (Exception \$e) {\n";
        $configContent .= "        error_log('QR code generation error: ' . \$e->getMessage());\n";
        $configContent .= "        return '';\n";
        $configContent .= "    }\n";
        $configContent .= "}\n\n";
        
        // Add getQRCodeHTML function
        $configContent .= "function getQRCodeHTML(\$qrCodeDataUrl) {\n";
        $configContent .= "    \$position = QR_POSITION;\n";
        $configContent .= "    \$displaySize = QR_DISPLAY_SIZE;\n";
        $configContent .= "    \$displayText = QR_DISPLAY_TEXT;\n";
        $configContent .= "    \n";
        $configContent .= "    // Position styles - menggunakan absolute agar hanya di halaman terakhir\n";
        $configContent .= "    \$positionStyles = [\n";
        $configContent .= "        'bottom-right' => 'bottom: 5mm; right: 5mm;',\n";
        $configContent .= "        'bottom-left' => 'bottom: 5mm; left: 5mm;',\n";
        $configContent .= "        'top-right' => 'top: 5mm; right: 5mm;',\n";
        $configContent .= "        'top-left' => 'top: 5mm; left: 5mm;',\n";
        $configContent .= "    ];\n";
        $configContent .= "    \n";
        $configContent .= "    \$style = \$positionStyles[\$position] ?? \$positionStyles['bottom-right'];\n";
        $configContent .= "    \n";
        $configContent .= "    // Gunakan position absolute dan taruh di akhir document\n";
        $configContent .= "    \$html = '<div style=\"position: absolute; ' . \$style . ' text-align: center; background: white; padding: 5px; border: 1px solid #ddd; border-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);\">';\n";
        $configContent .= "    \$html .= '<img src=\"' . htmlspecialchars(\$qrCodeDataUrl) . '\" style=\"width: ' . \$displaySize . '; height: ' . \$displaySize . '; display: block;\" alt=\"QR Code\">';\n";
        $configContent .= "    if (!empty(\$displayText)) {\n";
        $configContent .= "        \$html .= '<div style=\"font-size: 7px; color: #666; margin-top: 2px;\">' . htmlspecialchars(\$displayText) . '</div>';\n";
        $configContent .= "    }\n";
        $configContent .= "    \$html .= '</div>';\n";
        $configContent .= "    \n";
        $configContent .= "    return \$html;\n";
        $configContent .= "}\n";

        // Save to file
        $configFile = __DIR__ . '/../../config/qrcode.php';
        $result = file_put_contents($configFile, $configContent);

        if ($result !== false) {
            Flasher::setFlash('Konfigurasi QR Code berhasil disimpan!', 'success');
        } else {
            Flasher::setFlash('Gagal menyimpan konfigurasi QR Code!', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/configQR');
        exit;
    }
    
    // =================================================================
    // HELPER: CLEAR CACHE
    // =================================================================
    private function clearDashboardCache()
    {
        // Clear semua cache dashboard
        unset($_SESSION['admin_dashboard_stats']);
        unset($_SESSION['admin_dashboard_stats_time']);
        unset($_SESSION['admin_daftar_semester']);
        unset($_SESSION['admin_daftar_semester_time']);
    }
    
    // Method untuk manual clear cache (bisa dipanggil dari menu)
    public function clearCache()
    {
        $this->clearDashboardCache();
        Flasher::setFlash('Cache berhasil dibersihkan!', 'success');
        header('Location: ' . BASEURL . '/admin/dashboard');
        exit;
    }

    /**
     * Test QR Code Generation (AJAX)
     */
    public function testQRCode() {
        header('Content-Type: application/json');
        
        // Load config
        require_once __DIR__ . '/../../config/qrcode.php';
        
        try {
            $testUrl = BASEURL . '/test-validation';
            $qrApiUrl = getQRCodeApiUrl($testUrl);
            
            // Download QR Code
            $imageData = @file_get_contents($qrApiUrl);
            
            if ($imageData !== false) {
                $base64 = 'data:image/png;base64,' . base64_encode($imageData);
                echo json_encode([
                    'success' => true,
                    'qr_code' => $base64,
                    'api_url' => $qrApiUrl
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Tidak dapat mengakses API QR Code'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // =================================================================
    // PENGATURAN MENU - ENABLE/DISABLE MENU
    // =================================================================
    public function pengaturanMenu()
    {
        $this->data['judul'] = 'Pengaturan Menu';

        $configPath = __DIR__ . '/../../config/config.php';
        $configContent = is_file($configPath) ? file_get_contents($configPath) : '';

        $inputNilaiEnabled = true;
        $pembayaranEnabled = true;

        if ($configContent !== false && $configContent !== '') {
            if (preg_match("/define\('MENU_INPUT_NILAI_ENABLED',\s*(true|false)\)/", $configContent, $matchNilai)) {
                $inputNilaiEnabled = $matchNilai[1] === 'true';
            }
            if (preg_match("/define\('MENU_PEMBAYARAN_ENABLED',\s*(true|false)\)/", $configContent, $matchPembayaran)) {
                $pembayaranEnabled = $matchPembayaran[1] === 'true';
            }
        }

        $this->data['menu_input_nilai_enabled'] = $inputNilaiEnabled;
        $this->data['menu_pembayaran_enabled'] = $pembayaranEnabled;

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/pengaturan_menu', $this->data);
        $this->view('templates/footer', $this->data);
    }

    public function simpanPengaturanMenu()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/pengaturanMenu');
            exit;
        }

        $inputNilaiEnabled = isset($_POST['menu_input_nilai']) ? 'true' : 'false';
        $pembayaranEnabled = isset($_POST['menu_pembayaran']) ? 'true' : 'false';

        try {
            $configPath = __DIR__ . '/../../config/config.php';
            if (!is_file($configPath)) {
                throw new Exception('File konfigurasi tidak ditemukan.');
            }

            $configContent = file_get_contents($configPath);
            if ($configContent === false) {
                throw new Exception('Gagal membaca file konfigurasi.');
            }

            $patterns = [
                "/define\('MENU_INPUT_NILAI_ENABLED',\s*(true|false)\);/" => "define('MENU_INPUT_NILAI_ENABLED', {$inputNilaiEnabled});",
                "/define\('MENU_PEMBAYARAN_ENABLED',\s*(true|false)\);/" => "define('MENU_PEMBAYARAN_ENABLED', {$pembayaranEnabled});",
                "/define\('MENU_RAPOR_ENABLED',\s*(true|false)\);/" => "define('MENU_RAPOR_ENABLED', {$inputNilaiEnabled});"
            ];

            foreach ($patterns as $pattern => $replacement) {
                if (preg_match($pattern, $configContent)) {
                    $updated = preg_replace($pattern, $replacement, $configContent, 1);
                    if ($updated === null) {
                        throw new Exception('Gagal memperbarui konfigurasi menu.');
                    }
                    $configContent = $updated;
                } else {
                    $configContent .= PHP_EOL . $replacement . PHP_EOL;
                }
            }

            if (file_put_contents($configPath, $configContent) === false) {
                throw new Exception('Gagal menulis file konfigurasi.');
            }

            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            Flasher::setFlash('Pengaturan menu berhasil disimpan.', 'success');
        } catch (Exception $e) {
            error_log('Error simpanPengaturanMenu: ' . $e->getMessage());
            Flasher::setFlash('Gagal menyimpan pengaturan menu.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/pengaturanMenu');
        exit;
    }

    // =================================================================
    // PROFIL & GANTI SANDI ADMIN
    // =================================================================
    
    public function profil()
    {
        $this->data['judul'] = 'Profil Admin';
        $id_user = $_SESSION['user_id'] ?? 0;
        
        if (!$id_user) {
            header('Location: ' . BASEURL . '/admin/dashboard');
            exit;
        }

        try {
            $db = new Database();
            $db->query("SELECT * FROM users WHERE id_user = :id_user AND role = 'admin' LIMIT 1");
            $db->bind('id_user', $id_user);
            $admin = $db->single();
            
            if (!$admin) {
                Flasher::setFlash('Data admin tidak ditemukan.', 'danger');
                header('Location: ' . BASEURL . '/admin/dashboard');
                exit;
            }
            
            $this->data['admin'] = $admin;
        } catch (Exception $e) {
            error_log('Error profil admin: ' . $e->getMessage());
            Flasher::setFlash('Terjadi kesalahan saat memuat profil.', 'danger');
            header('Location: ' . BASEURL . '/admin/dashboard');
            exit;
        }

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/profil', $this->data);
        $this->view('templates/footer', $this->data);
    }

    public function simpanProfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/profil');
            exit;
        }

        $id_user = $_SESSION['user_id'] ?? 0;
        if (!$id_user) {
            header('Location: ' . BASEURL . '/admin/profil');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

        if (empty($username)) {
            Flasher::setFlash('Username tidak boleh kosong.', 'danger');
            header('Location: ' . BASEURL . '/admin/profil');
            exit;
        }

        try {
            $db = new Database();
            
            // Cek apakah username sudah digunakan oleh user lain
            $db->query("SELECT id_user FROM users WHERE username = :username AND id_user != :id_user LIMIT 1");
            $db->bind('username', $username);
            $db->bind('id_user', $id_user);
            $exists = $db->single();
            
            if ($exists) {
                Flasher::setFlash('Username sudah digunakan oleh user lain.', 'danger');
                header('Location: ' . BASEURL . '/admin/profil');
                exit;
            }
            
            // Update profil
            $db->query("UPDATE users SET username = :username, nama_lengkap = :nama_lengkap WHERE id_user = :id_user");
            $db->bind('username', $username);
            $db->bind('nama_lengkap', $nama_lengkap);
            $db->bind('id_user', $id_user);
            $db->execute();
            
            // Update session
            $_SESSION['username'] = $username;
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            
            Flasher::setFlash('Profil berhasil diperbarui.', 'success');
        } catch (Exception $e) {
            error_log('Error simpanProfil admin: ' . $e->getMessage());
            Flasher::setFlash('Terjadi kesalahan saat menyimpan profil.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/profil');
        exit;
    }

    public function gantiSandi()
    {
        $this->data['judul'] = 'Ganti Sandi';
        
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/ganti_sandi', $this->data);
        $this->view('templates/footer', $this->data);
    }

    public function simpanSandi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/admin/gantiSandi');
            exit;
        }

        $id_user = $_SESSION['user_id'] ?? 0;
        if (!$id_user) {
            header('Location: ' . BASEURL . '/admin/gantiSandi');
            exit;
        }

        $password = trim($_POST['password'] ?? '');
        $password2 = trim($_POST['password2'] ?? '');

        if (empty($password) || empty($password2)) {
            Flasher::setFlash('Password dan konfirmasi wajib diisi.', 'danger');
            header('Location: ' . BASEURL . '/admin/gantiSandi');
            exit;
        }

        if ($password !== $password2) {
            Flasher::setFlash('Konfirmasi password tidak cocok.', 'danger');
            header('Location: ' . BASEURL . '/admin/gantiSandi');
            exit;
        }

        if (strlen($password) < 6) {
            Flasher::setFlash('Password minimal 6 karakter.', 'danger');
            header('Location: ' . BASEURL . '/admin/gantiSandi');
            exit;
        }

        try {
            $db = new Database();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $db->query("UPDATE users SET password = :password WHERE id_user = :id_user");
            $db->bind('password', $hashedPassword);
            $db->bind('id_user', $id_user);
            $db->execute();
            
            Flasher::setFlash('Password berhasil diperbarui.', 'success');
        } catch (Exception $e) {
            error_log('Error simpanSandi admin: ' . $e->getMessage());
            Flasher::setFlash('Terjadi kesalahan saat menyimpan password.', 'danger');
        }

        header('Location: ' . BASEURL . '/admin/gantiSandi');
        exit;
    }
}
