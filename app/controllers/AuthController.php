<?php

// File: app/controllers/AuthController.php - SIMPLE & WORKING VERSION
class AuthController extends Controller
{

    public function index()
    {
        $data['judul'] = 'Login - Aplikasi Absensi';
        $data['daftar_semester'] = $this->model('TahunPelajaran_model')->getAllSemester();
        $this->view('auth/login', $data);
    }

    public function prosesLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // INPUT VALIDATION & SANITIZATION
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $id_semester = filter_var($_POST['id_semester'] ?? 0, FILTER_VALIDATE_INT);

            // Validasi input tidak kosong
            if (empty($username) || empty($password) || !$id_semester) {
                Flasher::setFlash('Username, password, dan semester harus diisi.', 'danger');
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }

            // Validasi panjang untuk mencegah abuse
            if (strlen($username) > 50 || strlen($password) > 100) {
                Flasher::setFlash('Input tidak valid.', 'danger');
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }

            $userModel = $this->model('User_model');
            $user = $userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                
                // SECURITY: Regenerate session ID untuk mencegah session fixation
                session_regenerate_id(true);
                
                // === PENTING: Set semester session dulu ===
                $tpModel = $this->model('TahunPelajaran_model');
                $allSemester = $tpModel->getAllSemester();
                foreach ($allSemester as $smt) {
                    if ($smt['id_semester'] == $id_semester) {
                        $_SESSION['id_semester_aktif'] = (int)$smt['id_semester'];
                        $_SESSION['nama_semester_aktif'] = htmlspecialchars($smt['nama_tp'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($smt['semester'], ENT_QUOTES, 'UTF-8');
                        $_SESSION['id_tp_aktif'] = (int)$smt['id_tp'];
                        break;
                    }
                }

                // === PENTING: Set user session data ===
                $_SESSION['user_id'] = (int)$user['id_user'];
                $_SESSION['nama_lengkap'] = htmlspecialchars($user['nama_lengkap'], ENT_QUOTES, 'UTF-8');
                $_SESSION['user_nama_lengkap'] = htmlspecialchars($user['nama_lengkap'], ENT_QUOTES, 'UTF-8'); // Backward compatibility
                
                // === CRITICAL FIX: Set BOTH role variables untuk kompatibilitas ===
                $_SESSION['role'] = $user['role'];           // Untuk Admin, Guru, Siswa
                $_SESSION['user_role'] = $user['role'];      // Untuk KepalaMadrasah
                
                $_SESSION['id_ref'] = (int)$user['id_ref'];

                // === Debug log ===
                error_log("Login SUCCESS: " . $user['username'] . " | Role: " . $user['role'] . " | ID: " . $user['id_user']);
                error_log("Session role: " . $_SESSION['role'] . " | Session user_role: " . $_SESSION['user_role']);

                // Redirect berdasarkan role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: ' . BASEURL . '/admin/dashboard');
                        exit;
                    case 'guru':
                        header('Location: ' . BASEURL . '/guru/dashboard');
                        exit;
                    case 'siswa':
                        header('Location: ' . BASEURL . '/siswa/dashboard');
                        exit;
                    case 'kepala_madrasah':
                        header('Location: ' . BASEURL . '/KepalaMadrasah/dashboard');
                        exit;
                    case 'wali_kelas':
                        header('Location: ' . BASEURL . '/waliKelas/dashboard');
                        exit;
                    default:
                        error_log("Unknown role: " . $user['role']);
                        Flasher::setFlash('Role tidak dikenal: ' . $user['role'], 'danger');
                        header('Location: ' . BASEURL . '/auth/login');
                        exit;
                }
            } else {
                error_log("Login FAILED: " . $username . " | Password verification failed");
                Flasher::setFlash('Username atau password salah.', 'danger');
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }
        }
    }

    public function logout()
    {
        // Session sudah di-start di index.php, tidak perlu start lagi
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
            
            // Hapus session cookie
            if (isset($_COOKIE['ABSEN_SESSION'])) {
                setcookie('ABSEN_SESSION', '', time() - 3600, '/');
            }
        }
        header('Location: ' . BASEURL . '/auth/login');
        exit;
    }

    // =================================================================
    // DEBUG METHOD (hapus di production)
    // =================================================================
    
    public function debugSession()
    {
        echo "<h2>DEBUG SESSION DATA</h2>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        
        echo "<h3>Expected by Controllers:</h3>";
        echo "<ul>";
        echo "<li><strong>AdminController:</strong> \$_SESSION['role'] != 'admin'</li>";
        echo "<li><strong>GuruController:</strong> \$_SESSION['role'] !== 'guru'</li>";
        echo "<li><strong>SiswaController:</strong> \$_SESSION['role'] != 'siswa'</li>";
        echo "<li><strong>KepalaMadrasahController:</strong> \$_SESSION['user_role'] !== 'kepala_madrasah'</li>";
        echo "</ul>";
        
        if (isset($_SESSION['role'])) {
            echo "<p><strong>Current \$_SESSION['role']:</strong> " . $_SESSION['role'] . "</p>";
        } else {
            echo "<p style='color:red;'><strong>\$_SESSION['role'] NOT SET!</strong></p>";
        }
        
        if (isset($_SESSION['user_role'])) {
            echo "<p><strong>Current \$_SESSION['user_role']:</strong> " . $_SESSION['user_role'] . "</p>";
        } else {
            echo "<p style='color:red;'><strong>\$_SESSION['user_role'] NOT SET!</strong></p>";
        }
        
        echo "<p><a href='" . BASEURL . "/auth/login'>Back to Login</a></p>";
        echo "<p><a href='" . BASEURL . "/auth/logout'>Logout</a></p>";
    }
}