<?php

// File: app/controllers/KepalaMadrasahController.php - UPDATED WITH NEW FEATURES
class KepalaMadrasahController extends Controller
{
    private $data = [];
    private $kepalaSekolahModel;

    public function __construct()
    {
        // Middleware untuk memastikan hanya kepala madrasah yang bisa akses
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'kepala_madrasah') {
            error_log("KepalaMadrasahController: Access denied. user_role = " . ($_SESSION['user_role'] ?? 'not set'));
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        // Set semester data jika belum ada
        if (!isset($_SESSION['id_semester_aktif'])) {
            $this->setDefaultSemester();
        }

        // Initialize model
        $this->kepalaSekolahModel = $this->model('KepalaSekolah_model');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->data['judul'] = 'Dashboard Monitoring';
        
        try {
            // Get session data
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 1;
            $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
            
            // Get comprehensive statistics from new model
            $dashboardStats = $this->kepalaSekolahModel->getDashboardStatistics($id_tp_aktif, $id_semester_aktif);
            
            $this->data['total_guru'] = $dashboardStats['total_guru'];
            $this->data['total_siswa'] = $dashboardStats['total_siswa'];
            $this->data['total_kelas'] = $dashboardStats['total_kelas'];
            $this->data['total_mapel'] = $dashboardStats['total_mapel'];
            $this->data['total_jurnal'] = $dashboardStats['total_jurnal'];
            
            // Get additional monitoring data
            $monitoringData = $this->kepalaSekolahModel->getMonitoringData($id_tp_aktif, $id_semester_aktif);
            $this->data['monitoring'] = $monitoringData;
            
            // Get session info for display
            $this->data['session_info'] = [
                'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Tidak Diketahui',
                'id_tp_aktif' => $id_tp_aktif,
                'id_semester_aktif' => $id_semester_aktif
            ];
            
            error_log("KepalaMadrasahController::dashboard - Stats loaded successfully");
            
        } catch (Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            // Set default values if error
            $this->data['total_guru'] = 0;
            $this->data['total_siswa'] = 0;
            $this->data['total_kelas'] = 0;
            $this->data['total_mapel'] = 0;
            $this->data['total_jurnal'] = 0;
            $this->data['monitoring'] = [];
        }
        
        // Load view
        $this->view('templates/header', $this->data);
        $this->view('kepala_madrasah/dashboard', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Method untuk mendapatkan data dashboard berdasarkan periode - UPDATED WITH CLASS FILTER
     */
    public function getDashboardData()
    {
        // Set proper headers for AJAX
        header('Content-Type: application/json');
        
        // Pastikan hanya AJAX request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $period = $input['period'] ?? 'today';
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');
        $kelasFilter = $input['kelas_filter'] ?? ''; // NEW: Class filter
        
        // Get session data
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 1;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
        
        error_log("getDashboardData called: period=$period, startDate=$startDate, endDate=$endDate, semester=$id_semester_aktif, kelas=$kelasFilter");
        
        try {
            // Get jurnal count using new model with class filter
            $jurnalCount = $this->kepalaSekolahModel->getJurnalCountByDateRange($startDate, $endDate, $id_semester_aktif, $kelasFilter);
            
            // Get attendance statistics using new model with class filter
            $attendanceStats = $this->kepalaSekolahModel->getAttendanceStatsByDateRange($startDate, $endDate, $id_semester_aktif, $kelasFilter);
            
            // Get trend data using new model with class filter
            $trendData = $this->kepalaSekolahModel->getAttendanceTrendData($period, $startDate, $endDate, $id_semester_aktif, $kelasFilter);
            
            // Get total pertemuan using new model with class filter
            $totalPertemuan = $this->kepalaSekolahModel->getTotalPertemuanByDateRange($startDate, $endDate, $id_semester_aktif, $kelasFilter);
            
            // Get guru mengajar stats for the period
            $guruMengajarStats = $this->kepalaSekolahModel->getGuruMengajarStatsByDateRange($startDate, $endDate, $id_semester_aktif);
            
            $response = [
                'status' => 'success',
                'jurnal_count' => $jurnalCount,
                'total_pertemuan' => $totalPertemuan,
                'hadir' => $attendanceStats['hadir'] ?? 0,
                'sakit' => $attendanceStats['sakit'] ?? 0,
                'izin' => $attendanceStats['izin'] ?? 0,
                'alfa' => $attendanceStats['alfa'] ?? 0,
                'total_absensi' => $attendanceStats['total'] ?? 0,
                'trend_labels' => $trendData['labels'] ?? [],
                'trend_data' => $trendData['data'] ?? [],
                'guru_mengajar_count' => $guruMengajarStats['guru_mengajar'] ?? 0,
                'guru_persentase' => $guruMengajarStats['persentase'] ?? 0,
                'debug_info' => [
                    'session_semester' => $id_semester_aktif,
                    'session_tp' => $id_tp_aktif,
                    'query_date_range' => "$startDate to $endDate",
                    'kelas_filter' => $kelasFilter
                ]
            ];
            
            error_log("getDashboardData response: " . json_encode($response));
            
        } catch (Exception $e) {
            error_log("Error in getDashboardData: " . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Gagal memuat data dashboard: ' . $e->getMessage(),
                'jurnal_count' => 0,
                'total_pertemuan' => 0,
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alfa' => 0,
                'total_absensi' => 0,
                'trend_labels' => [],
                'trend_data' => [],
                'guru_mengajar_count' => 0,
                'guru_persentase' => 0
            ];
        }
        
        echo json_encode($response);
    }

    /**
     * NEW: Method untuk mendapatkan data top kelas berdasarkan periode
     */
    public function getTopKelasData()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');
        
        // Get session data
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
        
        try {
            $topKelas = $this->kepalaSekolahModel->getTopKelasByDateRange(
                $startDate, 
                $endDate, 
                $id_semester_aktif
            );
            
            $response = [
                'status' => 'success',
                'top_kelas' => $topKelas,
                'debug_info' => [
                    'semester_filter' => $id_semester_aktif,
                    'date_range' => "$startDate to $endDate"
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Error in getTopKelasData: " . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Gagal memuat data top kelas: ' . $e->getMessage(),
                'top_kelas' => []
            ];
        }
        
        echo json_encode($response);
    }

    /**
     * UPDATED: Method untuk mendapatkan daftar siswa yang tidak masuk dalam format summary
     */
    public function getAbsentStudentsSummary()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');
        $statusFilter = $input['status_filter'] ?? '';
        $kelasFilter = $input['kelas_filter'] ?? '';
        $page = intval($input['page'] ?? 0);
        $limit = intval($input['limit'] ?? 20);
        
        // Get session data
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
        
        try {
            // Get summary statistics
            $summary = $this->kepalaSekolahModel->getAbsentStudentsSummary(
                $startDate, 
                $endDate, 
                $id_semester_aktif, 
                $statusFilter, 
                $kelasFilter
            );
            
            // Get detailed student list
            $students = $this->kepalaSekolahModel->getAbsentStudentsDetailedByDateRange(
                $startDate, 
                $endDate, 
                $id_semester_aktif, 
                $statusFilter, 
                $kelasFilter,
                $page,
                $limit
            );
            
            // Check if there are more results
            $totalStudents = $this->kepalaSekolahModel->getAbsentStudentsCount(
                $startDate, 
                $endDate, 
                $id_semester_aktif, 
                $statusFilter, 
                $kelasFilter
            );
            
            $response = [
                'status' => 'success',
                'summary' => $summary,
                'students' => $students,
                'count' => count($students),
                'has_more' => ($page + 1) * $limit < $totalStudents,
                'total_count' => $totalStudents,
                'debug_info' => [
                    'semester_filter' => $id_semester_aktif,
                    'date_range' => "$startDate to $endDate",
                    'page' => $page,
                    'limit' => $limit
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Error in getAbsentStudentsSummary: " . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Gagal memuat data siswa tidak masuk: ' . $e->getMessage(),
                'summary' => [
                    'total' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alfa' => 0
                ],
                'students' => [],
                'count' => 0,
                'has_more' => false,
                'total_count' => 0
            ];
        }
        
        echo json_encode($response);
    }

    /**
     * LEGACY: Method untuk mendapatkan daftar siswa yang tidak masuk - UPDATED
     */
    public function getAbsentStudents()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');
        $statusFilter = $input['status_filter'] ?? '';
        $kelasFilter = $input['kelas_filter'] ?? '';
        
        // Get session data
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
        
        try {
            $students = $this->kepalaSekolahModel->getAbsentStudentsByDateRange(
                $startDate, 
                $endDate, 
                $id_semester_aktif, 
                $statusFilter, 
                $kelasFilter
            );
            
            $response = [
                'status' => 'success',
                'students' => $students,
                'count' => count($students),
                'debug_info' => [
                    'semester_filter' => $id_semester_aktif,
                    'date_range' => "$startDate to $endDate"
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Error in getAbsentStudents: " . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Gagal memuat data siswa tidak masuk: ' . $e->getMessage(),
                'students' => [],
                'count' => 0
            ];
        }
        
        echo json_encode($response);
    }

    /**
     * Method untuk mendapatkan opsi kelas - UPDATED
     */
    public function getKelasOptions()
    {
        header('Content-Type: application/json');
        
        try {
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 1;
            $kelas = $this->kepalaSekolahModel->getKelasOptionsForActiveTP($id_tp_aktif);
            
            $response = [
                'status' => 'success',
                'kelas' => $kelas,
                'debug_info' => [
                    'tp_filter' => $id_tp_aktif
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Error in getKelasOptions: " . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Gagal memuat data kelas: ' . $e->getMessage(),
                'kelas' => []
            ];
        }
        
        echo json_encode($response);
    }

    /**
     * Method untuk mendapatkan statistik lengkap - NEW
     */
    public function getComprehensiveStats()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        try {
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 1;
            $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
            
            // Get monthly stats
            $monthlyStats = $this->kepalaSekolahModel->getMonthlyAttendanceStats($id_semester_aktif);
            
            // Get subject performance
            $subjectStats = $this->kepalaSekolahModel->getSubjectPerformanceStats($id_semester_aktif);
            
            // Get monitoring data
            $monitoringData = $this->kepalaSekolahModel->getMonitoringData($id_tp_aktif, $id_semester_aktif);
            
            $response = [
                'status' => 'success',
                'monthly_stats' => $monthlyStats,
                'subject_stats' => $subjectStats,
                'monitoring_data' => $monitoringData
            ];
            
        } catch (Exception $e) {
            error_log("Error in getComprehensiveStats: " . $e->getMessage());
            $response = [
                'status' => 'error',
                'message' => 'Gagal memuat statistik lengkap: ' . $e->getMessage(),
                'monthly_stats' => [],
                'subject_stats' => [],
                'monitoring_data' => []
            ];
        }
        
        echo json_encode($response);
    }

    /**
     * Set default semester if not exists - UPDATED
     */
    private function setDefaultSemester()
    {
        try {
            $tpModel = $this->model('TahunPelajaran_model');
            
            // Get active semester first
            $activeSemester = $tpModel->getSemesterAktif();
            
            if ($activeSemester) {
                $_SESSION['id_semester_aktif'] = $activeSemester['id_semester'];
                $_SESSION['nama_semester_aktif'] = $activeSemester['nama_tp'] . ' - ' . $activeSemester['semester'];
                $_SESSION['id_tp_aktif'] = $activeSemester['id_tp'];
                
                error_log("Set active semester: " . $_SESSION['id_semester_aktif']);
            } else {
                // If no active semester, get the latest one
                $allSemester = $tpModel->getAllSemester();
                
                if (!empty($allSemester)) {
                    $latestSemester = $allSemester[0];
                    $_SESSION['id_semester_aktif'] = $latestSemester['id_semester'];
                    $_SESSION['nama_semester_aktif'] = $latestSemester['nama_tp'] . ' - ' . $latestSemester['semester'];
                    $_SESSION['id_tp_aktif'] = $latestSemester['id_tp'];
                    
                    error_log("Set latest semester: " . $_SESSION['id_semester_aktif']);
                }
            }
        } catch (Exception $e) {
            error_log("Error setting default semester: " . $e->getMessage());
            // Set minimal fallback
            $_SESSION['id_semester_aktif'] = 1;
            $_SESSION['id_tp_aktif'] = 1;
            $_SESSION['nama_semester_aktif'] = 'Default Semester';
        }
    }

    // =================================================================
    // MONITORING PAGES (EXISTING) - Updated to use session filters
    // =================================================================

    public function riwayat_mengajar()
    {
        $this->data['judul'] = 'Pantau Riwayat Mengajar';
        $this->data['guru'] = $this->model('Guru_model')->getAllGuru();
        $this->data['riwayat'] = [];
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Tidak Diketahui'
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id_guru'])) {
            $id_guru = $_POST['id_guru'];
            $this->data['riwayat'] = $this->model('Jurnal_model')->getRiwayatMengajarByGuruId($id_guru);
            $this->data['selected_guru'] = $id_guru;
        }

        $this->view('templates/header', $this->data);
        $this->view('kepala_madrasah/riwayat_mengajar', $this->data);
        $this->view('templates/footer');
    }
    
    public function riwayat_per_mapel_with_stats()
    {
        $this->data['judul'] = 'Pantau Riwayat Per Mapel';
        $this->data['guru'] = $this->model('Guru_model')->getAllGuru();
        $this->data['mapel_guru'] = [];
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Tidak Diketahui'
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id_guru'])) {
            $id_guru = $_POST['id_guru'];
            $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
            $this->data['mapel_guru'] = $this->model('Jurnal_model')->getMapelByGuruId($id_guru, $id_semester_aktif);
            $this->data['selected_guru'] = $id_guru;
        }

        $this->view('templates/header', $this->data);
        $this->view('kepala_madrasah/riwayat_per_mapel_with_stats', $this->data);
        $this->view('templates/footer');
    }

    public function detail_riwayat_with_stats($id_penugasan)
    {
        $this->data['judul'] = 'Detail Riwayat Mapel';
        $this->data['detail_mapel'] = $this->model('Jurnal_model')->getDetailMapelByIdPenugasan($id_penugasan);
        $this->data['riwayat_jurnal'] = $this->model('Jurnal_model')->getJurnalByIdPenugasan($id_penugasan);
        $this->data['rekap_absensi'] = $this->model('Absensi_model')->getRekapAbsensiByIdPenugasan($id_penugasan);
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Tidak Diketahui'
        ];

        $this->view('templates/header', $this->data);
        $this->view('kepala_madrasah/detail_riwayat_with_stats', $this->data);
        $this->view('templates/footer');
    }

    public function rincian_absen_filter()
    {
        $this->data['judul'] = 'Pantau Rincian Absen Siswa';
        $this->data['guru'] = $this->model('Guru_model')->getAllGuru();
        $this->data['kelas_list'] = [];
        $this->data['absensi_siswa'] = [];
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Tidak Diketahui'
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id_guru'])) {
            $id_guru = $_POST['id_guru'];
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 1;
            $this->data['selected_guru'] = $id_guru;
            
            // Use new model to get kelas options
            $this->data['kelas_list'] = $this->kepalaSekolahModel->getKelasOptionsForActiveTP($id_tp_aktif);
            
            if (!empty($_POST['id_kelas'])) {
                $id_kelas = $_POST['id_kelas'];
                $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
                $this->data['selected_kelas'] = $id_kelas;
                $this->data['absensi_siswa'] = $this->model('Absensi_model')->getRincianAbsenSiswaByKelas($id_kelas, $id_semester_aktif);
            }
        }
        
        $this->view('templates/header', $this->data);
        $this->view('kepala_madrasah/rincian_absen_filter', $this->data);
        $this->view('templates/footer');
    }

    // =================================================================
    // DEBUG METHODS
    // =================================================================
    
    public function debugDashboard()
    {
        echo "<h2>DEBUG DASHBOARD KEPALA MADRASAH</h2>";
        
        // Check session
        echo "<h3>Session Data:</h3>";
        echo "<pre>";
        print_r([
            'id_semester_aktif' => $_SESSION['id_semester_aktif'] ?? 'NOT SET',
            'id_tp_aktif' => $_SESSION['id_tp_aktif'] ?? 'NOT SET',
            'nama_semester_aktif' => $_SESSION['nama_semester_aktif'] ?? 'NOT SET',
            'user_role' => $_SESSION['user_role'] ?? 'NOT SET'
        ]);
        echo "</pre>";
        
        // Test new model
        echo "<h3>Test New Model Methods:</h3>";
        try {
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 1;
            $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 1;
            
            $stats = $this->kepalaSekolahModel->getDashboardStatistics($id_tp_aktif, $id_semester_aktif);
            echo "<p><strong>Dashboard Statistics:</strong></p>";
            echo "<pre>";
            print_r($stats);
            echo "</pre>";
            
            $attendanceToday = $this->kepalaSekolahModel->getAttendanceStatsByDateRange(
                date('Y-m-d'), 
                date('Y-m-d'), 
                $id_semester_aktif
            );
            echo "<p><strong>Attendance Today:</strong></p>";
            echo "<pre>";
            print_r($attendanceToday);
            echo "</pre>";
            
            $monitoringData = $this->kepalaSekolahModel->getMonitoringData($id_tp_aktif, $id_semester_aktif);
            echo "<p><strong>Monitoring Data:</strong></p>";
            echo "<pre>";
            print_r($monitoringData);
            echo "</pre>";
            
        } catch (Exception $e) {
            echo "<p style='color:red;'>Error testing new model: " . $e->getMessage() . "</p>";
        }
        
        echo "<p><a href='" . BASEURL . "/KepalaMadrasah/dashboard'>Back to Dashboard</a></p>";
    }

    public function testSessionData()
    {
        header('Content-Type: application/json');
        
        echo json_encode([
            'session_data' => [
                'id_semester_aktif' => $_SESSION['id_semester_aktif'] ?? null,
                'id_tp_aktif' => $_SESSION['id_tp_aktif'] ?? null,
                'nama_semester_aktif' => $_SESSION['nama_semester_aktif'] ?? null,
                'user_role' => $_SESSION['user_role'] ?? null
            ],
            'server_info' => [
                'php_version' => PHP_VERSION,
                'current_date' => date('Y-m-d H:i:s'),
                'base_url' => BASEURL
            ]
        ]);
    }
}