<?php
// File: app/controllers/WaliKelasController.php

class WaliKelasController extends Controller {
    private $data = [];

    public function __construct() {
        // Guard: Admin atau Wali Kelas yang bisa akses
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, ['admin', 'wali_kelas'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        // Clear old flash messages yang mungkin tertinggal dari login
        if (isset($_SESSION['flash']) && strpos($_SESSION['flash']['pesan'] ?? '', 'Role tidak dikenal') !== false) {
            unset($_SESSION['flash']);
        }

        // Set data umum
        $this->data['daftar_semester'] = $this->model('TahunPelajaran_model')->getAllSemester();
        
        // Set default semester jika belum ada
        if (!isset($_SESSION['id_semester_aktif']) && !empty($this->data['daftar_semester'])) {
            $defaultSemester = $this->data['daftar_semester'][0];
            $_SESSION['id_semester_aktif'] = $defaultSemester['id_semester'];
            $_SESSION['nama_semester_aktif'] = $defaultSemester['nama_tp'] . ' - ' . $defaultSemester['semester'];
            $_SESSION['id_tp_aktif'] = $defaultSemester['id_tp'];
        }
    }

    // =================================================================
    // UNTUK ADMIN - KELOLA DATA WALI KELAS
    // =================================================================

    /**
     * Halaman daftar wali kelas (Admin only)
     */
    public function index() {
        if ($_SESSION['role'] !== 'admin') {
            header('Location: ' . BASEURL . '/waliKelas/dashboard');
            exit;
        }

        $this->data['judul'] = 'Kelola Wali Kelas';
        $this->data['wali_kelas'] = $this->model('WaliKelas_model')->getAllWaliKelas();
        $this->data['guru'] = $this->model('Guru_model')->getAllGuru();
        $this->data['kelas'] = $this->model('Kelas_model')->getKelasByTP($_SESSION['id_tp_aktif']);
        $this->data['tp'] = $this->model('TahunPelajaran_model')->getTahunPelajaranById($_SESSION['id_tp_aktif']);

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_admin', $this->data);
        $this->view('admin/wali_kelas/index', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Proses tambah wali kelas
     */
    public function tambah() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        $data = [
            'id_guru' => $_POST['id_guru'] ?? '',
            'id_kelas' => $_POST['id_kelas'] ?? '',
            'id_tp' => $_POST['id_tp'] ?? $_SESSION['id_tp_aktif']
        ];

        // Validasi
        if (empty($data['id_guru']) || empty($data['id_kelas'])) {
            Flasher::setFlash('Data tidak lengkap!', 'danger');
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        // Cek apakah guru sudah menjadi wali kelas di TP yang sama
        if ($this->model('WaliKelas_model')->cekWaliKelasExists($data['id_guru'], $data['id_tp'])) {
            Flasher::setFlash('Guru sudah menjadi wali kelas di tahun pelajaran ini!', 'warning');
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        // Cek apakah kelas sudah punya wali kelas
        if ($this->model('WaliKelas_model')->cekKelasHasWaliKelas($data['id_kelas'], $data['id_tp'])) {
            Flasher::setFlash('Kelas sudah memiliki wali kelas!', 'warning');
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        // Simpan data
        if ($this->model('WaliKelas_model')->tambahDataWaliKelas($data) > 0) {
            // Update user role jika guru belum punya role wali_kelas
            $this->model('User_model')->updateRoleToWaliKelas($data['id_guru']);
            
            Flasher::setFlash('Wali kelas berhasil ditambahkan!', 'success');
        } else {
            Flasher::setFlash('Gagal menambahkan wali kelas!', 'danger');
        }

        header('Location: ' . BASEURL . '/waliKelas');
        exit;
    }

    /**
     * Proses hapus wali kelas
     */
    public function hapus($id_walikelas) {
        if ($_SESSION['role'] !== 'admin') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        if ($this->model('WaliKelas_model')->hapusDataWaliKelas($id_walikelas) > 0) {
            Flasher::setFlash('Wali kelas berhasil dihapus!', 'success');
        } else {
            Flasher::setFlash('Gagal menghapus wali kelas!', 'danger');
        }

        header('Location: ' . BASEURL . '/waliKelas');
        exit;
    }

    // =================================================================
    // UNTUK WALI KELAS - DASHBOARD & MONITORING
    // =================================================================

    /**
     * Dashboard Wali Kelas - SAMA dengan Dashboard Guru (karena wali kelas juga guru yang mengajar)
     */
    public function dashboard() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        // Dashboard wali kelas = dashboard guru (karena wali kelas juga mengajar)
        // Gunakan logika yang sama dengan GuruController::dashboard()
        $this->data['judul'] = 'Dashboard Guru';

        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        // Ambil jadwal mengajar (SAMA seperti guru)
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

        // Hitung statistik (SAMA seperti guru)
        $this->data['total_penugasan'] = $this->getTotalPenugasan($id_guru, $id_semester_aktif);
        $this->data['total_hari_mengajar'] = $this->getTotalHariMengajar($id_guru, $id_semester_aktif);
        $this->data['kelas_mapel_info'] = $this->getKelasMapelInfo($id_guru, $id_semester_aktif);

        // Render dengan VIEW GURU (bukan view wali kelas terpisah)
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('guru/dashboard', $this->data); // PAKAI VIEW GURU!
        $this->view('templates/footer');
    }

    /**
     * Helper: Total penugasan (copy dari GuruController)
     */
    private function getTotalPenugasan($id_guru, $id_semester_aktif) {
        if (!$id_guru || !$id_semester_aktif) return 0;
        $data = $this->model('Penugasan_model')->getPenugasanByGuru($id_guru, $id_semester_aktif);
        return count($data);
    }

    /**
     * Helper: Total hari mengajar (copy dari GuruController)
     */
    private function getTotalHariMengajar($id_guru, $id_semester_aktif) {
        if (!$id_guru || !$id_semester_aktif) return 0;
        $data = $this->model('Penugasan_model')->getPenugasanByGuru($id_guru, $id_semester_aktif);
        $hari_unik = [];
        foreach ($data as $row) {
            if (!empty($row['hari'])) {
                $hari_unik[$row['hari']] = true;
            }
        }
        return count($hari_unik);
    }

    /**
     * Helper: Info kelas & mapel (copy dari GuruController)
     */
    private function getKelasMapelInfo($id_guru, $id_semester_aktif) {
        if (!$id_guru || !$id_semester_aktif) return [];
        return $this->model('Penugasan_model')->getPenugasanByGuru($id_guru, $id_semester_aktif);
    }

    /**
     * Monitoring Absensi Kelas - Gunakan tampilan Performa Siswa
     */
    public function monitoringAbsensi() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);

        $this->data['judul'] = 'Monitoring Absensi';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['id_kelas'] = $waliKelasInfo['id_kelas'] ?? 0;
        $this->data['nama_kelas'] = $waliKelasInfo['nama_kelas'] ?? '';
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui'
        ];

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/monitoring_absensi', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Absensi Harian (Wali Kelas): DEPRECATED - Method removed
     */
    public function absensiHarian() {
        header('Location: ' . BASEURL . '/waliKelas/monitoringAbsensi');
        exit;
    }

    /**
     * Export Rekap Absensi Harian: DEPRECATED - Method removed
     */
    public function exportAbsensiHarianRekap() {
        header('Location: ' . BASEURL . '/waliKelas/monitoringAbsensi');
        exit;
    }

    /**
     * Export Detail Absensi Harian: DEPRECATED - Method removed
     */
    public function exportAbsensiHarianDetail() {
        header('Location: ' . BASEURL . '/waliKelas/monitoringAbsensi');
        exit;
    }

    /**
     * Get Rekap Harian Data (AJAX): DEPRECATED - Method removed
     */
    public function getRekapHarianData() {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Method deprecated']);
        exit;
    }

    /**
     * Get Data Monitoring Absensi (AJAX) - untuk wali kelas
     */
    public function getDataAbsensi() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');
        
        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        // Ambil info kelas wali
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

        if (!$id_kelas) {
            echo json_encode(['status' => 'error', 'message' => 'Kelas tidak ditemukan']);
            return;
        }

        try {
            // Gunakan model PerformaSiswa dengan filter id_kelas (bukan nama_kelas!)
            $performaModel = $this->model('PerformaSiswa_model');
            $kelasFilter = $id_kelas; // Gunakan id_kelas, bukan nama_kelas
            $data = $performaModel->getPerformaSiswa($startDate, $endDate, $id_semester_aktif, $kelasFilter);
            
            // Format data untuk response
            $formattedData = [];
            foreach ($data as $item) {
                $formattedData[] = [
                    'id_siswa' => $item['id_siswa'],
                    'nisn' => $item['nisn'],
                    'nama_siswa' => $item['nama_siswa'],
                    'nama_kelas' => $item['nama_kelas'],
                    'total_pertemuan' => $item['total_pertemuan'],
                    'total_hadir' => $item['hadir'],
                    'total_sakit' => $item['sakit'],
                    'total_izin' => $item['izin'],
                    'total_alfa' => $item['alfa'],
                    'persentase_hadir' => $item['persentase_hadir']
                ];
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $formattedData
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get Detail Absensi Siswa (AJAX) - untuk wali kelas
     */
    public function getDetailAbsensi() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id_siswa = $input['id_siswa'] ?? 0;
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');

        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        if (!$id_siswa) {
            echo json_encode(['status' => 'error', 'message' => 'ID Siswa tidak valid']);
            return;
        }

        try {
            $performaModel = $this->model('PerformaSiswa_model');
            $siswaInfo = $performaModel->getSiswaInfo($id_siswa);

            // Ambil detail per mapel (existing)
            $detailData = $performaModel->getDetailPerformaSiswa($id_siswa, $startDate, $endDate, $id_semester_aktif);

            // Ambil status harian per tanggal (baru) untuk kebutuhan "tanggal 1: Alfa, tanggal 2: Hadir" dll
            $id_guru = $_SESSION['id_ref'] ?? 0;
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
            $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
            $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

            $absensiModel = $this->model('Absensi_model');
            $dailyStatus = [];
            if ($id_kelas) {
                $dailyStatus = $absensiModel->getDailyStatusBySiswa($id_kelas, $id_semester_aktif, $id_siswa, $startDate, $endDate);
            }

            // Bangun rentang tanggal lengkap agar hari tanpa absensi tetap muncul
            $expandedDaily = [];
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $map = [];
            foreach ($dailyStatus as $ds) {
                $map[$ds['tanggal']] = $ds['daily_status'];
            }
            for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
                $tgl = $d->format('Y-m-d');
                $status = $map[$tgl] ?? null; // null = belum ada absensi di hari itu
                $expandedDaily[] = [
                    'tanggal' => $tgl,
                    'daily_status' => $status // null -> Belum Diisi
                ];
            }

            // Hitung ringkasan
            $summary = ['H'=>0,'S'=>0,'I'=>0,'A'=>0,'BELUM'=>0];
            foreach ($expandedDaily as $row) {
                $st = $row['daily_status'];
                if ($st === null) { $summary['BELUM']++; continue; }
                if (isset($summary[$st])) $summary[$st]++;
            }

            echo json_encode([
                'status' => 'success',
                'siswa_info' => $siswaInfo,
                'detail_data' => $detailData, // tetap dikirim jika nanti diperlukan
                'daily_status' => $expandedDaily,
                'daily_summary' => $summary
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Export PDF Monitoring Absensi
     */
    public function exportPDF() {
        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Ambil info kelas wali
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

        if (!$id_kelas) {
            echo "Kelas tidak ditemukan";
            return;
        }

        // Ambil data absensi
        $performaModel = $this->model('PerformaSiswa_model');
        $data = $performaModel->getPerformaSiswa($startDate, $endDate, $id_semester_aktif, $id_kelas);

        // Load Dompdf
        require_once APPROOT . '/app/core/dompdf/autoload.inc.php';
        
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);

        // Build HTML
        $html = $this->buildPDFHtml($data, $waliKelasInfo, $startDate, $endDate);
        
        // Add QR code for document validation
        require_once APPROOT . '/app/core/PDFQRHelper.php';
        $html = PDFQRHelper::addQRToPDF($html, 'monitoring_absensi', $waliKelasInfo['id_kelas'] . '_' . $startDate . '_' . $endDate);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = 'Monitoring_Absensi_' . str_replace(' ', '_', $waliKelasInfo['nama_kelas']) . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Build HTML untuk PDF - SAMA dengan format PerformaSiswaController
     */
    private function buildPDFHtml($data, $kelasInfo, $startDate, $endDate) {
        $namaSemester = $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui';
        $namaKelas = $kelasInfo['nama_kelas'] ?? '';
        
        // Format tanggal Indonesia
        $startDateFormatted = date('d/m/Y', strtotime($startDate));
        $endDateFormatted = date('d/m/Y', strtotime($endDate));
        
        // Calculate statistics
        $totalSiswa = count($data);
        $rataHadir = $totalSiswa > 0 ? number_format(array_sum(array_column($data, 'persentase_hadir')) / $totalSiswa, 1) : 0;
        $siswaTerbaik = count(array_filter($data, fn($s) => $s['persentase_hadir'] >= 95));
        $perluPerhatian = count(array_filter($data, fn($s) => $s['persentase_hadir'] < 75));

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Performa Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 15px; font-size: 11px; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { color: #333; font-size: 18px; margin: 0; font-weight: bold; }
        .header h2 { color: #666; font-size: 12px; margin: 5px 0 0 0; font-weight: normal; }
        .info-section { background: #f9f9f9; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; }
        .info-row { margin-bottom: 3px; }
        .info-label { font-weight: bold; display: inline-block; width: 100px; color: #333; }
        .info-value { color: #666; }
        .stats { display: table; width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .stats-row { display: table-row; }
        .stat-box { display: table-cell; width: 25%; text-align: center; padding: 8px; background: #f5f5f5; border: 1px solid #ddd; }
        .stat-number { font-size: 16px; font-weight: bold; color: #333; }
        .stat-label { font-size: 10px; color: #666; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f0f0f0; color: #333; font-weight: bold; padding: 8px 4px; text-align: center; border: 1px solid #ccc; font-size: 10px; }
        td { padding: 6px 4px; border: 1px solid #ccc; text-align: center; font-size: 10px; }
        tr:nth-child(even) { background-color: #fafafa; }
        .text-left { text-align: left !important; }
        .status-sangat-baik { background-color: #d4edda; color: #155724; padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        .status-baik { background-color: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        .status-cukup { background-color: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        .status-perlu-perhatian { background-color: #f8d7da; color: #721c24; padding: 2px 6px; border-radius: 8px; font-size: 9px; font-weight: bold; }
        .footer { margin-top: 15px; text-align: center; color: #666; font-size: 9px; border-top: 1px solid #ddd; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERFORMA KEHADIRAN SISWA</h1>
        <h2>Sistem Informasi Manajemen Sekolah</h2>
    </div>
    
    <div class="info-section">
        <div class="info-row"><span class="info-label">Periode:</span> <span class="info-value">' . $startDateFormatted . ' - ' . $endDateFormatted . '</span></div>
        <div class="info-row"><span class="info-label">Kelas:</span> <span class="info-value">' . htmlspecialchars($namaKelas) . '</span></div>
        <div class="info-row"><span class="info-label">Semester:</span> <span class="info-value">' . htmlspecialchars($namaSemester) . '</span></div>
        <div class="info-row"><span class="info-label">Tanggal Cetak:</span> <span class="info-value">' . date('d/m/Y H:i') . '</span></div>
    </div>
    
    <div class="stats">
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-number">' . $totalSiswa . '</div>
                <div class="stat-label">Total Siswa</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">' . $rataHadir . '%</div>
                <div class="stat-label">Rata-rata Hadir</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">' . $siswaTerbaik . '</div>
                <div class="stat-label">Siswa Terbaik</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">' . $perluPerhatian . '</div>
                <div class="stat-label">Perlu Perhatian</div>
            </div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="15%">NISN</th>
                <th width="30%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="6%">Total</th>
                <th width="6%">Hadir</th>
                <th width="5%">Sakit</th>
                <th width="5%">Izin</th>
                <th width="5%">Alpha</th>
                <th width="8%">%</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($data as $index => $row) {
            $persen = floatval($row['persentase_hadir']);
            
            if ($persen >= 95) {
                $status = 'Sangat Baik';
                $statusClass = 'status-sangat-baik';
            } elseif ($persen >= 85) {
                $status = 'Baik';
                $statusClass = 'status-baik';
            } elseif ($persen >= 75) {
                $status = 'Cukup';
                $statusClass = 'status-cukup';
            } else {
                $status = 'Perlu Perhatian';
                $statusClass = 'status-perlu-perhatian';
            }

            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . htmlspecialchars($row['nisn'] ?? '-') . '</td>
                <td class="text-left">' . htmlspecialchars($row['nama_siswa']) . '</td>
                <td>' . htmlspecialchars($row['nama_kelas']) . '</td>
                <td><strong>' . $row['total_pertemuan'] . '</strong></td>
                <td style="color: #28a745; font-weight: bold;">' . $row['hadir'] . '</td>
                <td style="color: #fd7e14;">' . $row['sakit'] . '</td>
                <td style="color: #007bff;">' . $row['izin'] . '</td>
                <td style="color: #dc3545; font-weight: bold;">' . $row['alfa'] . '</td>
                <td><strong>' . number_format($persen, 1) . '%</strong></td>
                <td><span class="' . $statusClass . '">' . $status . '</span></td>
            </tr>';
        }

        $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p><strong>Keterangan:</strong> Sangat Baik: ≥95% | Baik: 85-94% | Cukup: 75-84% | Perlu Perhatian: <75%</p>
        <p>Dicetak pada: ' . date('d/m/Y H:i:s') . '</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Daftar Siswa Kelas
     */
    public function daftarSiswa() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);

        $this->data['judul'] = 'Daftar Siswa';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['siswa_list'] = $this->model('Siswa_model')->getSiswaByKelas($waliKelasInfo['id_kelas'], $id_tp_aktif);

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/daftar_siswa', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Monitoring Nilai Kelas
     */
    public function monitoringNilai() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);

        $this->data['judul'] = 'Monitoring Nilai';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['id_kelas'] = $waliKelasInfo['id_kelas'] ?? 0;
        $this->data['nama_kelas'] = $waliKelasInfo['nama_kelas'] ?? '';
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui'
        ];

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/nilai_monitoring', $this->data);
        $this->view('templates/footer');
    }

    // =================================================================
    // CETAK RAPOR
    // =================================================================

    /**
     * Pengaturan Rapor
     */
    public function pengaturanRapor() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

        // Get pengaturan rapor berdasarkan id_guru dan id_tp
        $pengaturanRapor = $this->model('PengaturanRapor_model')->getPengaturanByGuru($id_guru, $id_tp_aktif);
        
        // Get mapel list untuk kelas ini
        $mapelList = [];
        if ($id_kelas) {
            $mapelList = $this->model('Penugasan_model')->getMapelByKelas($id_kelas, $id_semester_aktif);
        }

        $this->data['judul'] = 'Pengaturan Rapor';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['pengaturan'] = $pengaturanRapor;
        $this->data['mapel_list'] = $mapelList;
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui'
        ];

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/pengaturan_rapor', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Simpan Pengaturan Rapor (AJAX)
     */
    public function simpanPengaturanRapor() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Flasher::setFlash('Method tidak diizinkan', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/pengaturanRapor');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        if (!$id_guru || !$id_tp_aktif) {
            Flasher::setFlash('Data guru atau tahun pelajaran tidak ditemukan', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/pengaturanRapor');
            exit;
        }

        // Handle upload gambar kop
        $pengaturanLama = $this->model('PengaturanRapor_model')->getPengaturanByGuru($id_guru, $id_tp_aktif);
        
        $kopFileName = $pengaturanLama['kop_rapor'] ?? '';
        $ttdKepalaFileName = $pengaturanLama['ttd_kepala_madrasah'] ?? '';
        $ttdWalasFileName = $pengaturanLama['ttd_wali_kelas'] ?? '';
        
        // Upload Kop Rapor
        if (isset($_FILES['kop_rapor']) && $_FILES['kop_rapor']['error'] === UPLOAD_ERR_OK) {
            $kopFileName = $this->handleImageUpload($_FILES['kop_rapor'], 'kop', 'kop_' . $id_guru . '_' . $id_tp_aktif, 2097152);
            if ($kopFileName === false) {
                Flasher::setFlash('Gagal upload gambar kop', 'danger');
                header('Location: ' . BASEURL . '/waliKelas/pengaturanRapor');
                exit;
            }
            // Hapus file lama
            if ($pengaturanLama && !empty($pengaturanLama['kop_rapor'])) {
                $this->deleteOldImage('kop', $pengaturanLama['kop_rapor']);
            }
        }
        
        // Upload TTD Kepala Madrasah
        if (isset($_FILES['ttd_kepala_madrasah']) && $_FILES['ttd_kepala_madrasah']['error'] === UPLOAD_ERR_OK) {
            $ttdKepalaFileName = $this->handleImageUpload($_FILES['ttd_kepala_madrasah'], 'ttd', 'ttd_kepala_' . $id_guru . '_' . $id_tp_aktif, 1048576);
            if ($ttdKepalaFileName === false) {
                Flasher::setFlash('Gagal upload tanda tangan kepala madrasah', 'danger');
                header('Location: ' . BASEURL . '/waliKelas/pengaturanRapor');
                exit;
            }
            // Hapus file lama
            if ($pengaturanLama && !empty($pengaturanLama['ttd_kepala_madrasah'])) {
                $this->deleteOldImage('ttd', $pengaturanLama['ttd_kepala_madrasah']);
            }
        }
        
        // Upload TTD Wali Kelas
        if (isset($_FILES['ttd_wali_kelas']) && $_FILES['ttd_wali_kelas']['error'] === UPLOAD_ERR_OK) {
            $ttdWalasFileName = $this->handleImageUpload($_FILES['ttd_wali_kelas'], 'ttd', 'ttd_walas_' . $id_guru . '_' . $id_tp_aktif, 1048576);
            if ($ttdWalasFileName === false) {
                Flasher::setFlash('Gagal upload tanda tangan wali kelas', 'danger');
                header('Location: ' . BASEURL . '/waliKelas/pengaturanRapor');
                exit;
            }
            // Hapus file lama
            if ($pengaturanLama && !empty($pengaturanLama['ttd_wali_kelas'])) {
                $this->deleteOldImage('ttd', $pengaturanLama['ttd_wali_kelas']);
            }
        }

        $data = [
            'id_guru' => $id_guru,
            'id_tp' => $id_tp_aktif,
            'kop_rapor' => $kopFileName,
            'nama_madrasah' => $_POST['nama_madrasah'] ?? '',
            'tempat_cetak' => $_POST['tempat_cetak'] ?? '',
            'nama_kepala_madrasah' => $_POST['nama_kepala_madrasah'] ?? '',
            'ttd_kepala_madrasah' => $ttdKepalaFileName,
            'ttd_wali_kelas' => $ttdWalasFileName,
            'tanggal_cetak' => $_POST['tanggal_cetak'] ?? date('Y-m-d'),
            'mapel_rapor' => isset($_POST['mapel_rapor']) ? json_encode($_POST['mapel_rapor']) : '[]',
            'persen_harian_sts' => $_POST['persen_harian_sts'] ?? 60,
            'persen_sts' => $_POST['persen_sts'] ?? 40,
            'persen_harian_sas' => $_POST['persen_harian_sas'] ?? 40,
            'persen_sts_sas' => $_POST['persen_sts_sas'] ?? 30,
            'persen_sas' => $_POST['persen_sas'] ?? 30
        ];

        if ($this->model('PengaturanRapor_model')->save($data)) {
            Flasher::setFlash('Pengaturan rapor berhasil disimpan', 'success');
        } else {
            Flasher::setFlash('Gagal menyimpan pengaturan rapor', 'danger');
        }

        header('Location: ' . BASEURL . '/waliKelas/pengaturanRapor');
        exit;
    }
    
    /**
     * Helper function untuk upload gambar
     */
    private function handleImageUpload($file, $folder, $prefix, $maxSize) {
        $uploadDir = 'public/img/' . $folder . '/';
        
        // Buat folder jika belum ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        $fileName = $prefix . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return false;
        }
        
        return $fileName;
    }
    
    /**
     * Helper function untuk hapus gambar lama
     */
    private function deleteOldImage($folder, $fileName) {
        $filePath = 'public/img/' . $folder . '/' . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Cetak Rapor (STS dan SAS dalam satu halaman)
     */
    public function cetakRapor() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);

        $this->data['judul'] = 'Cetak Rapor';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['id_kelas'] = $waliKelasInfo['id_kelas'] ?? 0;
        $this->data['nama_kelas'] = $waliKelasInfo['nama_kelas'] ?? '';
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui'
        ];

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/cetak_rapor', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Get Data Monitoring Nilai (AJAX) - untuk wali kelas
     */
    public function getDataNilai() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $jenisNilai = $input['jenis_nilai'] ?? 'harian';
        
        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        // Ambil info kelas wali
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

        if (!$id_kelas) {
            echo json_encode(['status' => 'error', 'message' => 'Kelas tidak ditemukan']);
            return;
        }

        try {
            $nilaiModel = $this->model('Nilai_model');
            $siswaModel = $this->model('Siswa_model');
            
            // Ambil daftar siswa
            $siswaList = $siswaModel->getSiswaByKelas($id_kelas, $id_tp_aktif);
            
            // Ambil daftar mapel untuk kelas ini
            $penugasanModel = $this->model('Penugasan_model');
            $mapelList = $penugasanModel->getMapelByKelas($id_kelas, $id_semester_aktif);
            
            $result = [];
            foreach ($siswaList as $siswa) {
                $siswaData = [
                    'id_siswa' => $siswa['id_siswa'],
                    'nisn' => $siswa['nisn'],
                    'nama_siswa' => $siswa['nama_siswa'],
                    'mapel' => []
                ];
                
                $totalNilai = 0;
                $jumlahMapel = 0;
                
                foreach ($mapelList as $mapel) {
                    $nilai = null;
                    
                    if ($jenisNilai === 'harian') {
                        // Rata-rata nilai harian
                        $nilaiHarian = $nilaiModel->getNilaiHarianByMapelSiswa($mapel['id_penugasan'], $siswa['id_siswa']);
                        if (!empty($nilaiHarian)) {
                            $nilai = array_sum(array_column($nilaiHarian, 'nilai')) / count($nilaiHarian);
                        }
                    } elseif ($jenisNilai === 'sts') {
                        // Nilai STS
                        $nilaiSTS = $nilaiModel->getNilaiByJenis($siswa['id_siswa'], $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sts');
                        $nilai = $nilaiSTS['nilai'] ?? null;
                    } elseif ($jenisNilai === 'sas') {
                        // Nilai SAS
                        $nilaiSAS = $nilaiModel->getNilaiByJenis($siswa['id_siswa'], $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sas');
                        $nilai = $nilaiSAS['nilai'] ?? null;
                    }
                    
                    $siswaData['mapel'][] = [
                        'nama_mapel' => $mapel['nama_mapel'],
                        'nilai' => $nilai
                    ];
                    
                    if ($nilai !== null) {
                        $totalNilai += $nilai;
                        $jumlahMapel++;
                    }
                }
                
                $siswaData['rata_rata'] = $jumlahMapel > 0 ? round($totalNilai / $jumlahMapel, 2) : null;
                $result[] = $siswaData;
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $result,
                'mapel_list' => array_column($mapelList, 'nama_mapel')
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get Daftar Siswa (AJAX)
     */
    public function getDaftarSiswa() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id_kelas = $input['id_kelas'] ?? 0;
        
        if (!$id_kelas) {
            echo json_encode(['status' => 'error', 'message' => 'ID Kelas tidak valid']);
            return;
        }

        try {
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
            $siswaModel = $this->model('Siswa_model');
            $siswaList = $siswaModel->getSiswaByKelas($id_kelas, $id_tp_aktif);
            
            echo json_encode([
                'status' => 'success',
                'data' => $siswaList
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Edit Siswa (Wali Kelas)
     */
    public function editSiswa($id_siswa) {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        // Verifikasi siswa ada di kelas wali kelas
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $siswa = $this->model('Siswa_model')->getSiswaById($id_siswa);
        
        if (!$siswa) {
            Flasher::setFlash('Siswa tidak ditemukan', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/daftarSiswa');
            exit;
        }

        // Cek apakah siswa ada di kelas wali kelas
        $siswaKelas = $this->model('Keanggotaan_model')->getKeanggotaanSiswa($id_siswa, $id_tp_aktif);
        if (!$siswaKelas || $siswaKelas['id_kelas'] != $waliKelasInfo['id_kelas']) {
            Flasher::setFlash('Anda tidak memiliki akses untuk mengedit siswa ini', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/daftarSiswa');
            exit;
        }

        $this->data['judul'] = 'Edit Data Siswa';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['siswa'] = $siswa;
        $this->data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui'
        ];

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/edit_siswa', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Update Siswa (Wali Kelas)
     */
    public function updateSiswa() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/waliKelas/daftarSiswa');
            exit;
        }

        $id_siswa = $_POST['id_siswa'] ?? 0;
        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        // Verifikasi siswa ada di kelas wali kelas
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $siswaKelas = $this->model('Keanggotaan_model')->getKeanggotaanSiswa($id_siswa, $id_tp_aktif);
        
        if (!$siswaKelas || $siswaKelas['id_kelas'] != $waliKelasInfo['id_kelas']) {
            Flasher::setFlash('Anda tidak memiliki akses untuk mengedit siswa ini', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/daftarSiswa');
            exit;
        }

        $data = [
            'nisn' => $_POST['nisn'] ?? '',
            'nama_siswa' => $_POST['nama_siswa'] ?? '',
            'jenis_kelamin' => $_POST['jenis_kelamin'] ?? '',
            'tanggal_lahir' => !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null
        ];

        try {
            $siswaModel = $this->model('Siswa_model');
            
            // Update data siswa
            if ($siswaModel->updateSiswa($id_siswa, $data)) {
                // Update password jika diisi
                $password_baru = $_POST['password_baru'] ?? '';
                if (!empty($password_baru)) {
                    $userModel = $this->model('User_model');
                    $userModel->updatePassword($id_siswa, 'siswa', $password_baru);
                }

                Flasher::setFlash('Data siswa berhasil diupdate', 'success');
            } else {
                Flasher::setFlash('Gagal update data siswa', 'danger');
            }
        } catch (Exception $e) {
            Flasher::setFlash('Error: ' . $e->getMessage(), 'danger');
        }

        header('Location: ' . BASEURL . '/waliKelas/daftarSiswa');
        exit;
    }

    /**
     * Get Nilai Siswa per Jenis (AJAX)
     */
    public function getNilaiSiswa() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $idSiswa = $input['id_siswa'] ?? 0;
        $jenisNilai = $input['jenis_nilai'] ?? 'harian';
        
        if (!$idSiswa) {
            echo json_encode(['status' => 'error', 'message' => 'ID Siswa tidak valid']);
            return;
        }

        try {
            $id_guru = $_SESSION['id_ref'] ?? 0;
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
            $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

            // Ambil info kelas wali
            $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
            $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

            if (!$id_kelas) {
                echo json_encode(['status' => 'error', 'message' => 'Kelas tidak ditemukan']);
                return;
            }

            $nilaiModel = $this->model('Nilai_model');
            $penugasanModel = $this->model('Penugasan_model');
            
            // Ambil daftar mapel untuk kelas ini
            $mapelList = $penugasanModel->getMapelByKelas($id_kelas, $id_semester_aktif);
            
            $result = [
                'mapel' => [],
                'rata_rata' => null
            ];
            
            $totalNilai = 0;
            $jumlahMapel = 0;
            
            foreach ($mapelList as $mapel) {
                $nilai = null;
                
                if ($jenisNilai === 'harian') {
                    // Rata-rata nilai harian
                    $nilaiHarian = $nilaiModel->getNilaiHarianByMapelSiswa($mapel['id_penugasan'], $idSiswa);
                    if (!empty($nilaiHarian)) {
                        $nilai = array_sum(array_column($nilaiHarian, 'nilai')) / count($nilaiHarian);
                    }
                } elseif ($jenisNilai === 'sts') {
                    // Nilai STS
                    $nilaiSTS = $nilaiModel->getNilaiByJenis($idSiswa, $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sts');
                    $nilai = $nilaiSTS['nilai'] ?? null;
                } elseif ($jenisNilai === 'sas') {
                    // Nilai SAS
                    $nilaiSAS = $nilaiModel->getNilaiByJenis($idSiswa, $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sas');
                    $nilai = $nilaiSAS['nilai'] ?? null;
                }
                
                $result['mapel'][] = [
                    'nama_mapel' => $mapel['nama_mapel'],
                    'nilai' => $nilai !== null ? round($nilai, 2) : null
                ];
                
                if ($nilai !== null) {
                    $totalNilai += $nilai;
                    $jumlahMapel++;
                }
            }
            
            $result['rata_rata'] = $jumlahMapel > 0 ? round($totalNilai / $jumlahMapel, 2) : null;
            
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate Rapor PDF
     */
    public function generateRapor($jenisRapor = 'sts', $idSiswa = 0) {
        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        // Get wali kelas info
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

        if (!$id_kelas || !$idSiswa) {
            die('Data tidak lengkap');
        }

        // Get pengaturan rapor berdasarkan id_guru dan id_tp
        $pengaturan = $this->model('PengaturanRapor_model')->getPengaturanByGuru($id_guru, $id_tp_aktif);
        
        // Get data siswa
        $siswaModel = $this->model('Siswa_model');
        $siswa = $siswaModel->getSiswaById($idSiswa);

        if (!$siswa) {
            die('Siswa tidak ditemukan');
        }

        // Get nilai siswa
        $nilaiModel = $this->model('Nilai_model');
        $penugasanModel = $this->model('Penugasan_model');
        
        // Get mapel list
        $mapelList = $penugasanModel->getMapelByKelas($id_kelas, $id_semester_aktif);
        
        // Filter mapel berdasarkan pengaturan rapor
        if (!empty($pengaturan['mapel_rapor'])) {
            $mapelRapor = json_decode($pengaturan['mapel_rapor'], true);
            if (is_array($mapelRapor) && !empty($mapelRapor)) {
                $mapelList = array_filter($mapelList, function($mapel) use ($mapelRapor) {
                    return in_array($mapel['id_mapel'], $mapelRapor);
                });
            }
        }
        
        // Get persentase dari pengaturan
        $persenHarianSTS = $pengaturan['persen_harian_sts'] ?? 60;
        $persenSTS = $pengaturan['persen_sts'] ?? 40;
        $persenHarianSAS = $pengaturan['persen_harian_sas'] ?? 40;
        $persenSTSSAS = $pengaturan['persen_sts_sas'] ?? 30;
        $persenSAS = $pengaturan['persen_sas'] ?? 30;
        
        $nilaiData = [];
        $totalNilai = 0;
        $jumlahMapel = 0;

        foreach ($mapelList as $mapel) {
            $nilaiAkhir = null;
            
            // Ambil nilai harian
            $nilaiHarianList = $nilaiModel->getNilaiHarianByMapelSiswa($mapel['id_penugasan'], $idSiswa);
            $rataHarian = 0;
            if (!empty($nilaiHarianList)) {
                $rataHarian = array_sum(array_column($nilaiHarianList, 'nilai')) / count($nilaiHarianList);
            }
            
            // Ambil nilai STS
            $nilaiSTSData = $nilaiModel->getNilaiByJenis($idSiswa, $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sts');
            $nilaiSTS = $nilaiSTSData['nilai'] ?? 0;
            
            if ($jenisRapor === 'sts') {
                // Nilai Rapor STS = (Harian × %H) + (STS × %S)
                if ($rataHarian > 0 && $nilaiSTS > 0) {
                    $nilaiAkhir = ($rataHarian * $persenHarianSTS / 100) + ($nilaiSTS * $persenSTS / 100);
                } elseif ($rataHarian > 0) {
                    $nilaiAkhir = $rataHarian;
                } elseif ($nilaiSTS > 0) {
                    $nilaiAkhir = $nilaiSTS;
                }
                
                $nilaiData[] = [
                    'nama_mapel' => $mapel['nama_mapel'],
                    'nilai_harian' => $rataHarian > 0 ? round($rataHarian, 2) : '-',
                    'nilai_sts' => $nilaiSTS > 0 ? round($nilaiSTS, 2) : '-',
                    'nilai_akhir' => $nilaiAkhir !== null ? round($nilaiAkhir, 2) : '-',
                    'predikat' => $nilaiAkhir !== null ? $this->getNilaiPredikat($nilaiAkhir) : '-'
                ];
                
            } elseif ($jenisRapor === 'sas') {
                // Nilai Rapor SAS = (Harian × %H) + (STS × %S) + (SAS × %S)
                $nilaiSASData = $nilaiModel->getNilaiByJenis($idSiswa, $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sas');
                $nilaiSAS = $nilaiSASData['nilai'] ?? 0;
                
                if ($rataHarian > 0 && $nilaiSTS > 0 && $nilaiSAS > 0) {
                    $nilaiAkhir = ($rataHarian * $persenHarianSAS / 100) + ($nilaiSTS * $persenSTSSAS / 100) + ($nilaiSAS * $persenSAS / 100);
                } else {
                    // Fallback ke rata-rata jika tidak semua nilai ada
                    $komponenNilai = [];
                    if ($rataHarian > 0) $komponenNilai[] = $rataHarian;
                    if ($nilaiSTS > 0) $komponenNilai[] = $nilaiSTS;
                    if ($nilaiSAS > 0) $komponenNilai[] = $nilaiSAS;
                    
                    if (!empty($komponenNilai)) {
                        $nilaiAkhir = array_sum($komponenNilai) / count($komponenNilai);
                    }
                }
                
                $nilaiData[] = [
                    'nama_mapel' => $mapel['nama_mapel'],
                    'nilai_harian' => $rataHarian > 0 ? round($rataHarian, 2) : '-',
                    'nilai_sts' => $nilaiSTS > 0 ? round($nilaiSTS, 2) : '-',
                    'nilai_sas' => $nilaiSAS > 0 ? round($nilaiSAS, 2) : '-',
                    'nilai_akhir' => $nilaiAkhir !== null ? round($nilaiAkhir, 2) : '-',
                    'predikat' => $nilaiAkhir !== null ? $this->getNilaiPredikat($nilaiAkhir) : '-'
                ];
            }
            
            if ($nilaiAkhir !== null) {
                $totalNilai += $nilaiAkhir;
                $jumlahMapel++;
            }
        }

        $rataRata = $jumlahMapel > 0 ? round($totalNilai / $jumlahMapel, 2) : 0;

        // Generate PDF
        require_once __DIR__ . '/../core/dompdf/autoload.inc.php';
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Build HTML content
        $html = $this->buildRaporHTML($siswa, $nilaiData, $rataRata, $pengaturan, $jenisRapor, $waliKelasInfo);

        // Inject QR using universal helper with fingerprint meta
        require_once __DIR__ . '/../core/PDFQRHelper.php';
        $semesterName = $_SESSION['nama_semester_aktif'] ?? '';
        $meta = [
            'doc' => 'rapor_'.$jenisRapor,
            'nisn' => $siswa['nisn'] ?? '',
            'nama_siswa' => $siswa['nama_siswa'] ?? '',
            'kelas' => $waliKelasInfo['nama_kelas'] ?? '',
            'semester' => $semesterName,
            'jenis' => $jenisRapor,
            'rata_rata' => $rataRata,
        ];
        $fingerprintBase = implode('|', [
            $meta['nisn'], $meta['nama_siswa'], $meta['kelas'], $meta['semester'], strtoupper($meta['jenis']), number_format((float)$meta['rata_rata'], 2)
        ]);
        $meta['fingerprint'] = hash('sha256', $fingerprintBase);
        $html = PDFQRHelper::addQRToPDF($html, 'rapor_'.$jenisRapor, $siswa['id_siswa'], $meta);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $jenisLabel = $jenisRapor === 'sts' ? 'STS' : 'SAS';
        
        // Sanitize filename untuk download via AJAX
        $namaSiswa = preg_replace('/[^a-zA-Z0-9_-]/', '_', $siswa['nama_siswa']);
        $filename = "Rapor_{$jenisLabel}_{$siswa['nisn']}_{$namaSiswa}.pdf";
        
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Generate Rapor untuk Semua Siswa
     */
    public function generateRaporAll($jenisRapor = 'sts') {
        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        // Get wali kelas info
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;

        if (!$id_kelas) {
            die('Kelas tidak ditemukan');
        }

        // Get pengaturan rapor
        $pengaturan = $this->model('PengaturanRapor_model')->getPengaturanByGuru($id_guru, $id_tp_aktif);
        
        // Get all siswa
        $siswaModel = $this->model('Siswa_model');
        $siswaList = $siswaModel->getSiswaByKelas($id_kelas, $id_tp_aktif);

        if (empty($siswaList)) {
            die('Tidak ada siswa di kelas ini');
        }

        // Load Dompdf
        require_once __DIR__ . '/../core/dompdf/autoload.inc.php';
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        $allHtml = '';
        $nilaiModel = $this->model('Nilai_model');
        $penugasanModel = $this->model('Penugasan_model');
        
        // Get mapel list
        $mapelListFull = $penugasanModel->getMapelByKelas($id_kelas, $id_semester_aktif);
        
        // Filter mapel berdasarkan pengaturan rapor
        $mapelList = $mapelListFull;
        if (!empty($pengaturan['mapel_rapor'])) {
            $mapelRapor = json_decode($pengaturan['mapel_rapor'], true);
            if (is_array($mapelRapor) && !empty($mapelRapor)) {
                $mapelList = array_filter($mapelListFull, function($mapel) use ($mapelRapor) {
                    return in_array($mapel['id_mapel'], $mapelRapor);
                });
            }
        }
        
        // Get persentase dari pengaturan
        $persenHarianSTS = $pengaturan['persen_harian_sts'] ?? 60;
        $persenSTS = $pengaturan['persen_sts'] ?? 40;
        $persenHarianSAS = $pengaturan['persen_harian_sas'] ?? 40;
        $persenSTSSAS = $pengaturan['persen_sts_sas'] ?? 30;
        $persenSAS = $pengaturan['persen_sas'] ?? 30;

        $totalSiswa = count($siswaList);
        $currentIndex = 0;

        foreach ($siswaList as $siswa) {
            $currentIndex++;
            $nilaiData = [];
            $totalNilai = 0;
            $jumlahMapel = 0;

            foreach ($mapelList as $mapel) {
                $nilaiAkhir = null;
                
                // Ambil nilai harian
                $nilaiHarianList = $nilaiModel->getNilaiHarianByMapelSiswa($mapel['id_penugasan'], $siswa['id_siswa']);
                $rataHarian = 0;
                if (!empty($nilaiHarianList)) {
                    $rataHarian = array_sum(array_column($nilaiHarianList, 'nilai')) / count($nilaiHarianList);
                }
                
                // Ambil nilai STS
                $nilaiSTSData = $nilaiModel->getNilaiByJenis($siswa['id_siswa'], $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sts');
                $nilaiSTS = $nilaiSTSData['nilai'] ?? 0;
                
                if ($jenisRapor === 'sts') {
                    // Nilai Rapor STS = (Harian × %H) + (STS × %S)
                    if ($rataHarian > 0 && $nilaiSTS > 0) {
                        $nilaiAkhir = ($rataHarian * $persenHarianSTS / 100) + ($nilaiSTS * $persenSTS / 100);
                    } elseif ($rataHarian > 0) {
                        $nilaiAkhir = $rataHarian;
                    } elseif ($nilaiSTS > 0) {
                        $nilaiAkhir = $nilaiSTS;
                    }
                    
                    $nilaiData[] = [
                        'nama_mapel' => $mapel['nama_mapel'],
                        'nilai_harian' => $rataHarian > 0 ? round($rataHarian, 2) : '-',
                        'nilai_sts' => $nilaiSTS > 0 ? round($nilaiSTS, 2) : '-',
                        'nilai_akhir' => $nilaiAkhir !== null ? round($nilaiAkhir, 2) : '-',
                        'predikat' => $nilaiAkhir !== null ? $this->getNilaiPredikat($nilaiAkhir) : '-'
                    ];
                    
                } elseif ($jenisRapor === 'sas') {
                    // Nilai Rapor SAS = (Harian × %H) + (STS × %S) + (SAS × %S)
                    $nilaiSASData = $nilaiModel->getNilaiByJenis($siswa['id_siswa'], $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sas');
                    $nilaiSAS = $nilaiSASData['nilai'] ?? 0;
                    
                    if ($rataHarian > 0 && $nilaiSTS > 0 && $nilaiSAS > 0) {
                        $nilaiAkhir = ($rataHarian * $persenHarianSAS / 100) + ($nilaiSTS * $persenSTSSAS / 100) + ($nilaiSAS * $persenSAS / 100);
                    } else {
                        // Fallback ke rata-rata jika tidak semua nilai ada
                        $komponenNilai = [];
                        if ($rataHarian > 0) $komponenNilai[] = $rataHarian;
                        if ($nilaiSTS > 0) $komponenNilai[] = $nilaiSTS;
                        if ($nilaiSAS > 0) $komponenNilai[] = $nilaiSAS;
                        
                        if (!empty($komponenNilai)) {
                            $nilaiAkhir = array_sum($komponenNilai) / count($komponenNilai);
                        }
                    }
                    
                    $nilaiData[] = [
                        'nama_mapel' => $mapel['nama_mapel'],
                        'nilai_harian' => $rataHarian > 0 ? round($rataHarian, 2) : '-',
                        'nilai_sts' => $nilaiSTS > 0 ? round($nilaiSTS, 2) : '-',
                        'nilai_sas' => $nilaiSAS > 0 ? round($nilaiSAS, 2) : '-',
                        'nilai_akhir' => $nilaiAkhir !== null ? round($nilaiAkhir, 2) : '-',
                        'predikat' => $nilaiAkhir !== null ? $this->getNilaiPredikat($nilaiAkhir) : '-'
                    ];
                }
                
                if ($nilaiAkhir !== null) {
                    $totalNilai += $nilaiAkhir;
                    $jumlahMapel++;
                }
            }

            $rataRata = $jumlahMapel > 0 ? round($totalNilai / $jumlahMapel, 2) : 0;

            // Build HTML untuk siswa ini dengan wrapper div
            $allHtml .= '<div class="rapor-page">';
            $allHtml .= $this->buildRaporHTML($siswa, $nilaiData, $rataRata, $pengaturan, $jenisRapor, $waliKelasInfo);
            $allHtml .= '</div>';
        }
        
        // Wrap semua dalam container dengan CSS page-break
        $finalHtml = '
        <style>
            .rapor-page {
                page-break-after: always;
                page-break-inside: avoid;
            }
            .rapor-page:last-child {
                page-break-after: auto;
            }
        </style>
        ' . $allHtml;
        
        // Inject QR for mass report (class level)
        require_once __DIR__ . '/../core/PDFQRHelper.php';
        $semesterName2 = $_SESSION['nama_semester_aktif'] ?? '';
        $metaAll = [
            'doc' => 'rapor_all_'.$jenisRapor,
            'kelas' => $waliKelasInfo['nama_kelas'] ?? '',
            'semester' => $semesterName2,
            'jenis' => $jenisRapor,
            'jumlah_siswa' => count($siswaList),
        ];
        $fingerprintAll = implode('|', [ $metaAll['kelas'], $metaAll['semester'], strtoupper($metaAll['jenis']), (string)$metaAll['jumlah_siswa'] ]);
        $metaAll['fingerprint'] = hash('sha256', $fingerprintAll);
        $finalHtml = PDFQRHelper::addQRToPDF($finalHtml, 'rapor_all_'.$jenisRapor, $waliKelasInfo['id_kelas'] ?? 0, $metaAll);

        $dompdf->loadHtml($finalHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $jenisLabel = $jenisRapor === 'sts' ? 'STS' : 'SAS';
        
        // Sanitize filename untuk download via AJAX
        $namaKelas = preg_replace('/[^a-zA-Z0-9_-]/', '_', $waliKelasInfo['nama_kelas'] ?? 'Kelas');
        $filename = "Rapor_{$jenisLabel}_{$namaKelas}_Semua.pdf";
        
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Get predikat nilai
     */
    private function getNilaiPredikat($nilai) {
        if ($nilai === null) return '-';
        if ($nilai >= 90) return 'A (Sangat Baik)';
        if ($nilai >= 80) return 'B (Baik)';
        if ($nilai >= 70) return 'C (Cukup)';
        if ($nilai >= 60) return 'D (Kurang)';
        return 'E (Sangat Kurang)';
    }

    /**
     * Build HTML for Rapor
     */
    private function buildRaporHTML($siswa, $nilaiData, $rataRata, $pengaturan, $jenisRapor, $waliKelasInfo) {
        $jenisLabel = $jenisRapor === 'sts' ? 'SUMATIF TENGAH SEMESTER (STS)' : 'SUMATIF AKHIR SEMESTER (SAS)';
        $semester = $_SESSION['nama_semester_aktif'] ?? '';
        $tanggalCetak = $pengaturan['tanggal_cetak'] ?? date('Y-m-d');
        
        // Format tanggal Indonesia
        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tglArray = explode('-', $tanggalCetak);
        $tanggalFormatted = $tglArray[2] . ' ' . $bulanIndo[(int)$tglArray[1]] . ' ' . $tglArray[0];
        
        $tempatCetak = $pengaturan['tempat_cetak'] ?? '';
        $tempatTanggal = $tempatCetak . ', ' . $tanggalFormatted;
        
        // Header dengan gambar kop
        $kopHTML = '';
        if (!empty($pengaturan['kop_rapor'])) {
            $kopPath = 'public/img/kop/' . $pengaturan['kop_rapor'];
            if (file_exists($kopPath)) {
                $imageData = base64_encode(file_get_contents($kopPath));
                $imageType = pathinfo($kopPath, PATHINFO_EXTENSION);
                $imageSrc = 'data:image/' . $imageType . ';base64,' . $imageData;
                $kopHTML = '<img src="' . $imageSrc . '" style="max-width: 100%; height: auto; max-height: 120px;">';
            }
        }
        
        // TTD Kepala Madrasah
        $ttdKepalaHTML = '';
        if (!empty($pengaturan['ttd_kepala_madrasah'])) {
            $ttdPath = 'public/img/ttd/' . $pengaturan['ttd_kepala_madrasah'];
            if (file_exists($ttdPath)) {
                $imageData = base64_encode(file_get_contents($ttdPath));
                $imageType = pathinfo($ttdPath, PATHINFO_EXTENSION);
                $imageSrc = 'data:image/' . $imageType . ';base64,' . $imageData;
                $ttdKepalaHTML = '<img src="' . $imageSrc . '" style="max-width: 120px; height: auto; max-height: 50px;">';
            }
        }
        
        // TTD Wali Kelas
        $ttdWalasHTML = '';
        if (!empty($pengaturan['ttd_wali_kelas'])) {
            $ttdPath = 'public/img/ttd/' . $pengaturan['ttd_wali_kelas'];
            if (file_exists($ttdPath)) {
                $imageData = base64_encode(file_get_contents($ttdPath));
                $imageType = pathinfo($ttdPath, PATHINFO_EXTENSION);
                $imageSrc = 'data:image/' . $imageType . ';base64,' . $imageData;
                $ttdWalasHTML = '<img src="' . $imageSrc . '" style="max-width: 120px; height: auto; max-height: 50px;">';
            }
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Rapor ' . $jenisLabel . '</title>
            <style>
                @page { margin: 10mm 15mm; }
                body { 
                    font-family: "Times New Roman", Times, serif; 
                    font-size: 12pt;
                    line-height: 1.1;
                    margin: 0;
                    padding: 0;
                }
                .header {
                    text-align: center;
                    margin-bottom: 5px;
                }
                .title {
                    text-align: center;
                    margin: 5px 0;
                    font-weight: bold;
                    font-size: 13pt;
                }
                .biodata {
                    margin: 5px 0;
                }
                .biodata table {
                    width: 100%;
                    font-size: 11pt;
                }
                .biodata td {
                    padding: 1px 0;
                }
                .biodata td:first-child {
                    width: 120px;
                }
                .nilai-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 5px 0;
                    font-size: 10pt;
                }
                .nilai-table th, .nilai-table td {
                    border: 1px solid #000;
                    padding: 3px;
                }
                .nilai-table th {
                    background-color: #f0f0f0;
                    font-weight: bold;
                    text-align: center;
                }
                .nilai-table td:nth-child(1) {
                    text-align: center;
                    width: 25px;
                }
                .nilai-table td:nth-child(3), 
                .nilai-table td:nth-child(4), 
                .nilai-table td:nth-child(5),
                .nilai-table td:nth-child(6) {
                    text-align: center;
                    width: 60px;
                }
                .rata-rata {
                    margin: 5px 0;
                    font-weight: bold;
                    font-size: 11pt;
                }
                .ttd-section {
                    margin-top: 10px;
                    font-size: 10pt;
                }
                .ttd-table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .ttd-table td {
                    text-align: center;
                    vertical-align: top;
                    padding: 0px 15px;
                }
                .ttd-line {
                    border-bottom: 1px solid #000;
                    width: 180px;
                    margin: 30px auto 0px;
                }
                .ttd-img {
                    margin: 5px auto;
                    height: 50px;
                }
                .ttd-kepala {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 10pt;
                }
                .qr-code {
                    position: absolute;
                    bottom: 10mm;
                    left: 15mm;
                    text-align: center;
                }
                .qr-code img {
                    width: 80px;
                    height: 80px;
                }
                .qr-code-text {
                    font-size: 8pt;
                    margin-top: 2px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">' . $kopHTML . '</div>
            <br>
            <div class="title">LAPORAN HASIL BELAJAR SISWA<br>' . $jenisLabel . '</div>
            <br>
            <div class="biodata">
                <table>
                    <tr><td>Nama Siswa</td><td>: ' . htmlspecialchars($siswa['nama_siswa']) . '</td></tr>
                    <tr><td>NISN</td><td>: ' . htmlspecialchars($siswa['nisn']) . '</td></tr>
                    <tr><td>Kelas</td><td>: ' . htmlspecialchars($waliKelasInfo['nama_kelas'] ?? '-') . '</td></tr>
                    <tr><td>Semester</td><td>: ' . htmlspecialchars($semester) . '</td></tr>
                </table>
            </div>
            <table class="nilai-table">
                <thead>';
        
        // Header tabel berbeda untuk STS dan SAS
        if ($jenisRapor === 'sts') {
            $html .= '
                    <tr>
                        <th>No</th>
                        <th>Mata Pelajaran</th>
                        <th>Nilai Harian</th>
                        <th>Nilai STS</th>
                        <th>Nilai Rapor</th>
                    </tr>';
        } else {
            $html .= '
                    <tr>
                        <th>No</th>
                        <th>Mata Pelajaran</th>
                        <th>Nilai Harian</th>
                        <th>Nilai STS</th>
                        <th>Nilai SAS</th>
                        <th>Nilai Rapor</th>
                    </tr>';
        }
        
        $html .= '
                </thead>
                <tbody>';
        
        foreach ($nilaiData as $index => $item) {
            if ($jenisRapor === 'sts') {
                $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . htmlspecialchars($item['nama_mapel']) . '</td>
                        <td>' . $item['nilai_harian'] . '</td>
                        <td>' . $item['nilai_sts'] . '</td>
                        <td>' . $item['nilai_akhir'] . '</td>
                    </tr>';
            } else {
                $html .= '
                    <tr>
                        <td>' . ($index + 1) . '</td>
                        <td>' . htmlspecialchars($item['nama_mapel']) . '</td>
                        <td>' . $item['nilai_harian'] . '</td>
                        <td>' . $item['nilai_sts'] . '</td>
                        <td>' . $item['nilai_sas'] . '</td>
                        <td>' . $item['nilai_akhir'] . '</td>
                    </tr>';
            }
        }
        
        $html .= '
                </tbody>
            </table>
            <div class="rata-rata">Rata-rata: ' . number_format($rataRata, 2) . '</div>
            
            <!-- QR Code disisipkan otomatis oleh PDFQRHelper -->
            
            <div class="ttd-section">
                <div style="text-align: right; margin-bottom: 3px;">' . htmlspecialchars($tempatTanggal) . '</div>
                <table class="ttd-table">
                    <tr>
                        <td style="width: 50%;"><div>Orang Tua / Wali</div><div class="ttd-img" style="height: 50px;"></div><div class="ttd-line"></div></td>
                        <td style="width: 50%;"><div>Wali Kelas</div><div class="ttd-img">' . $ttdWalasHTML . '</div><div><strong>' . htmlspecialchars($_SESSION['nama_lengkap'] ?? $waliKelasInfo['nama_guru'] ?? '-') . '</strong></div></td>
                    </tr>
                </table>
                <div class="ttd-kepala">
                    <div>Mengetahui,<br>Kepala Madrasah</div>
                    <div class="ttd-img">' . $ttdKepalaHTML . '</div>
                    <div><strong>' . htmlspecialchars($pengaturan['nama_kepala_madrasah'] ?? '-') . '</strong></div>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Generate QR Code untuk validasi rapor
     */
    private function generateQRCode($siswa, $jenisRapor) {
        // Load QR Code configuration
        require_once __DIR__ . '/../../config/qrcode.php';
        
        // Generate token using config helper
        $token = generateQRToken($siswa['id_siswa'], $jenisRapor, $siswa['nisn']);
        
        // URL validasi rapor - menggunakan QR_WEBSITE_URL dari config
        $validationUrl = QR_WEBSITE_URL . '/validasiRapor/index/' . $token;
        
        // Get QR Code API URL from config
        $qrApiUrl = getQRCodeApiUrl($validationUrl);
        
        // Download QR Code image with timeout to avoid PDF hang
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'ignore_errors' => true
            ]
        ]);
        $imageData = @file_get_contents($qrApiUrl, false, $context);
        
        if ($imageData !== false && !empty($imageData)) {
            $isPng = substr($imageData, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A";
            if ($isPng) {
                return 'data:image/png;base64,' . base64_encode($imageData);
            }
            $isJpeg = substr($imageData, 0, 2) === "\xFF\xD8";
            if ($isJpeg) {
                return 'data:image/jpeg;base64,' . base64_encode($imageData);
            }
        }
        
        // Fallback: generate simple text if QR Code fails
        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60"><rect width="60" height="60" fill="#ccc"/><text x="30" y="35" text-anchor="middle" font-size="10" fill="#666">QR</text></svg>');
    }

    /**
     * Export PDF Monitoring Nilai
     */
    public function exportPDFNilai() {
        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;
        $jenisNilai = $_GET['jenis'] ?? 'harian';
        
        // Ambil info kelas wali
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        $id_kelas = $waliKelasInfo['id_kelas'] ?? 0;
        
        if (!$id_kelas) {
            die('Kelas tidak ditemukan');
        }

        // Ambil data nilai
        $nilaiModel = $this->model('Nilai_model');
        $siswaModel = $this->model('Siswa_model');
        $penugasanModel = $this->model('Penugasan_model');
        
        $siswaList = $siswaModel->getSiswaByKelas($id_kelas, $id_tp_aktif);
        $mapelList = $penugasanModel->getMapelByKelas($id_kelas, $id_semester_aktif);
        
        $result = [];
        foreach ($siswaList as $siswa) {
            $siswaData = [
                'id_siswa' => $siswa['id_siswa'],
                'nisn' => $siswa['nisn'],
                'nama_siswa' => $siswa['nama_siswa'],
                'mapel' => []
            ];
            
            $totalNilai = 0;
            $jumlahMapel = 0;
            
            foreach ($mapelList as $mapel) {
                $nilai = null;
                
                if ($jenisNilai === 'harian') {
                    $nilaiHarian = $nilaiModel->getNilaiHarianByMapelSiswa($mapel['id_penugasan'], $siswa['id_siswa']);
                    if (!empty($nilaiHarian)) {
                        $nilai = array_sum(array_column($nilaiHarian, 'nilai')) / count($nilaiHarian);
                    }
                } elseif ($jenisNilai === 'sts') {
                    $nilaiSTS = $nilaiModel->getNilaiByJenis($siswa['id_siswa'], $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sts');
                    $nilai = $nilaiSTS['nilai'] ?? null;
                } elseif ($jenisNilai === 'sas') {
                    $nilaiSAS = $nilaiModel->getNilaiByJenis($siswa['id_siswa'], $mapel['id_guru'], $mapel['id_mapel'], $id_semester_aktif, 'sas');
                    $nilai = $nilaiSAS['nilai'] ?? null;
                }
                
                $siswaData['mapel'][] = [
                    'nama_mapel' => $mapel['nama_mapel'],
                    'nilai' => $nilai
                ];
                
                if ($nilai !== null) {
                    $totalNilai += $nilai;
                    $jumlahMapel++;
                }
            }
            
            $siswaData['rata_rata'] = $jumlahMapel > 0 ? round($totalNilai / $jumlahMapel, 2) : null;
            $result[] = $siswaData;
        }

        // Generate PDF
        require_once __DIR__ . '/../core/dompdf/autoload.inc.php';
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Build HTML
        $jenisLabel = [
            'harian' => 'Nilai Harian',
            'sts' => 'Nilai STS',
            'sas' => 'Nilai SAS'
        ];
        
        $html = $this->buildPDFHtmlNilai(
            $result, 
            $mapelList,
            $waliKelasInfo['nama_kelas'], 
            $_SESSION['nama_semester_aktif'],
            $jenisLabel[$jenisNilai] ?? 'Nilai'
        );
        
        // Add QR code for document validation
        require_once APPROOT . '/app/core/PDFQRHelper.php';
        $html = PDFQRHelper::addQRToPDF($html, 'monitoring_nilai', $waliKelasInfo['id_kelas'] . '_' . $jenisNilai);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $filename = "Monitoring_Nilai_{$jenisNilai}_{$waliKelasInfo['nama_kelas']}_" . date('Y-m-d') . ".pdf";
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Build HTML untuk PDF Monitoring Nilai
     */
    private function buildPDFHtmlNilai($data, $mapelList, $namaKelas, $namaSemester, $jenisNilai) {
        $totalSiswa = count($data);
        $nilaiList = array_filter(array_column($data, 'rata_rata'), function($n) { return $n !== null; });
        $rataKelas = count($nilaiList) > 0 ? round(array_sum($nilaiList) / count($nilaiList), 2) : 0;
        $nilaiMax = count($nilaiList) > 0 ? max($nilaiList) : 0;
        $nilaiMin = count($nilaiList) > 0 ? min($nilaiList) : 0;
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monitoring Nilai - ' . htmlspecialchars($namaKelas) . '</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 5px 0; font-size: 18px; }
        .header p { margin: 3px 0; color: #666; }
        
        .info-section { margin-bottom: 15px; }
        .info-row { display: inline-block; width: 48%; margin-bottom: 5px; }
        .info-label { font-weight: bold; display: inline-block; width: 120px; }
        
        .stats-container { margin: 15px 0; }
        .stat-box { 
            display: inline-block; 
            width: 23%; 
            margin-right: 2%; 
            padding: 10px; 
            border-radius: 5px;
            text-align: center;
            vertical-align: top;
        }
        .stat-box:last-child { margin-right: 0; }
        .stat-box.primary { background: #0d6efd; color: white; }
        .stat-box.success { background: #198754; color: white; }
        .stat-box.info { background: #0dcaf0; color: white; }
        .stat-box.warning { background: #ffc107; color: white; }
        .stat-label { font-size: 9px; margin-bottom: 5px; opacity: 0.9; }
        .stat-value { font-size: 20px; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        th { background-color: #f8f9fa; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        .badge { 
            padding: 3px 8px; 
            border-radius: 3px; 
            font-weight: bold; 
            font-size: 9px;
            display: inline-block;
        }
        .badge-success { background: #198754; color: white; }
        .badge-info { background: #0dcaf0; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN MONITORING NILAI</h2>
        <p style="font-size: 14px; font-weight: bold;">' . htmlspecialchars($jenisNilai) . '</p>
        <p>' . htmlspecialchars($namaKelas) . ' | ' . htmlspecialchars($namaSemester) . '</p>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span>' . date('d F Y, H:i') . ' WIB</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jumlah Siswa:</span>
            <span>' . $totalSiswa . ' siswa</span>
        </div>
    </div>
    
    <div class="stats-container">
        <div class="stat-box primary">
            <div class="stat-label">TOTAL SISWA</div>
            <div class="stat-value">' . $totalSiswa . '</div>
        </div>
        <div class="stat-box success">
            <div class="stat-label">RATA-RATA KELAS</div>
            <div class="stat-value">' . number_format($rataKelas, 2) . '</div>
        </div>
        <div class="stat-box info">
            <div class="stat-label">NILAI TERTINGGI</div>
            <div class="stat-value">' . number_format($nilaiMax, 2) . '</div>
        </div>
        <div class="stat-box warning">
            <div class="stat-label">NILAI TERENDAH</div>
            <div class="stat-value">' . number_format($nilaiMin, 2) . '</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">NISN</th>
                <th style="text-align: left;">Nama Siswa</th>';
        
        foreach ($mapelList as $mapel) {
            $html .= '<th style="width: 60px;">' . htmlspecialchars($mapel['nama_mapel']) . '</th>';
        }
        
        $html .= '<th style="width: 70px;">Rata-rata</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($data as $index => $siswa) {
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . htmlspecialchars($siswa['nisn']) . '</td>
                <td style="text-align: left;">' . htmlspecialchars($siswa['nama_siswa']) . '</td>';
            
            foreach ($siswa['mapel'] as $mapel) {
                $nilai = $mapel['nilai'];
                if ($nilai !== null) {
                    $badgeClass = 'badge-secondary';
                    if ($nilai >= 85) $badgeClass = 'badge-success';
                    elseif ($nilai >= 70) $badgeClass = 'badge-info';
                    elseif ($nilai >= 60) $badgeClass = 'badge-warning';
                    elseif ($nilai < 60) $badgeClass = 'badge-danger';
                    
                    $html .= '<td><span class="badge ' . $badgeClass . '">' . number_format($nilai, 2) . '</span></td>';
                } else {
                    $html .= '<td><span class="badge badge-secondary">-</span></td>';
                }
            }
            
            $rataRata = $siswa['rata_rata'];
            if ($rataRata !== null) {
                $badgeClass = 'badge-secondary';
                if ($rataRata >= 85) $badgeClass = 'badge-success';
                elseif ($rataRata >= 70) $badgeClass = 'badge-info';
                elseif ($rataRata >= 60) $badgeClass = 'badge-warning';
                elseif ($rataRata < 60) $badgeClass = 'badge-danger';
                
                $html .= '<td><span class="badge ' . $badgeClass . '">' . number_format($rataRata, 2) . '</span></td>';
            } else {
                $html .= '<td><span class="badge badge-secondary">-</span></td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: ' . date('d F Y, H:i:s') . ' WIB</p>
    </div>
</body>
</html>';
        
        return $html;
    }

    // ================================================================
    // PEMBAYARAN (Option B) - Wali Kelas
    // ================================================================
    public function pembayaran() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/dashboard');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        if (!$waliKelasInfo) {
            Flasher::setFlash('Anda belum terdaftar sebagai Wali Kelas di TP aktif.', 'warning');
            header('Location: ' . BASEURL . '/waliKelas/dashboard');
            exit;
        }

        $this->data['judul'] = 'Pembayaran Kelas';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['tagihan_list'] = $this->model('Pembayaran_model')->getTagihanKelas($waliKelasInfo['id_kelas'], $id_tp_aktif, $id_semester_aktif);

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/pembayaran_dashboard', $this->data);
        $this->view('templates/footer');
    }

    public function pembayaranTagihan($id = null) {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/dashboard');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        if (!$waliKelasInfo) {
            header('Location: ' . BASEURL . '/waliKelas/dashboard');
            exit;
        }

        $this->data['judul'] = 'Kelola Tagihan Kelas';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['tagihan'] = $id ? $this->model('Pembayaran_model')->getTagihanById($id) : null;
        if ($id && $this->data['tagihan'] && (int)$this->data['tagihan']['id_kelas'] !== (int)$waliKelasInfo['id_kelas']) {
            Flasher::setFlash('Anda tidak berhak mengakses tagihan ini.', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        // Daftar siswa kelas untuk tabel status
        if ($id) {
            $this->data['siswa_list'] = $this->model('Siswa_model')->getSiswaByKelas($waliKelasInfo['id_kelas'], $id_tp_aktif);
            $this->data['tagihan_siswa'] = $this->model('Pembayaran_model')->getTagihanSiswaList($id);
        }

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/pembayaran_tagihan', $this->data);
        $this->view('templates/footer');
    }

    public function simpanTagihanKelas() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        if (!$waliKelasInfo) {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $mode = $_POST['mode'] ?? 'baru'; // 'baru' atau 'global'
        $nama = trim($_POST['nama'] ?? '');
        $id_global = $_POST['id_global'] ?? null;
        $nominal_default = (int)($_POST['nominal_default'] ?? 0);
        $jatuh_tempo = $_POST['jatuh_tempo'] ?? null;
        $tipe = $_POST['tipe'] ?? 'sekali';

        try {
            if ($mode === 'global' && $id_global) {
                $newId = $this->model('Pembayaran_model')->deriveTagihanFromGlobal($id_global, [
                    'id_tp' => $id_tp_aktif,
                    'id_semester' => $id_semester_aktif,
                    'id_kelas' => $waliKelasInfo['id_kelas'],
                    'nominal_default' => $nominal_default,
                    'jatuh_tempo' => $jatuh_tempo,
                    'created_by_user' => $_SESSION['user_id'] ?? null,
                ]);
            } else {
                $newId = $this->model('Pembayaran_model')->createTagihanKelas([
                    'nama' => $nama,
                    'kategori_id' => $_POST['kategori_id'] ?? null,
                    'id_tp' => $id_tp_aktif,
                    'id_semester' => $id_semester_aktif,
                    'id_kelas' => $waliKelasInfo['id_kelas'],
                    'tipe' => $tipe,
                    'nominal_default' => $nominal_default,
                    'jatuh_tempo' => $jatuh_tempo,
                    'created_by_user' => $_SESSION['user_id'] ?? null,
                    'created_by_role' => 'wali_kelas',
                ]);
            }

            if ($newId) {
                Flasher::setFlash('Tagihan kelas berhasil disimpan.', 'success');
                header('Location: ' . BASEURL . '/waliKelas/pembayaranTagihan/' . $newId);
            } else {
                Flasher::setFlash('Gagal menyimpan tagihan.', 'danger');
                header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            }
        } catch (Exception $e) {
            error_log('simpanTagihanKelas error: ' . $e->getMessage());
            Flasher::setFlash('Terjadi kesalahan saat menyimpan tagihan.', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
        }
        exit;
    }

    public function pembayaranInput($tagihan_id, $id_siswa) {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        if (!$waliKelasInfo) {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        // Validate that siswa belongs to this kelas
        $siswa = $this->model('Siswa_model')->getSiswaById($id_siswa);
        if (!$siswa) {
            Flasher::setFlash('Siswa tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        // Optional: ensure keanggotaan matches kelas wali
        $anggota = $this->model('Siswa_model')->getSiswaByKelas($waliKelasInfo['id_kelas'], $id_tp_aktif);
        $allowed = false;
        foreach ($anggota as $row) { if ((int)$row['id_siswa'] === (int)$id_siswa) { $allowed = true; break; } }
        if (!$allowed) {
            Flasher::setFlash('Siswa bukan anggota kelas Anda.', 'warning');
            header('Location: ' . BASEURL . '/waliKelas/pembayaranTagihan/' . $tagihan_id);
            exit;
        }

        $this->data['judul'] = 'Input Pembayaran';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['tagihan'] = $this->model('Pembayaran_model')->getTagihanById($tagihan_id);
        if (!$this->data['tagihan'] || (int)$this->data['tagihan']['id_kelas'] !== (int)$waliKelasInfo['id_kelas']) {
            Flasher::setFlash('Tagihan tidak valid untuk kelas Anda.', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }
        $this->data['siswa'] = $siswa;

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/pembayaran_input', $this->data);
        $this->view('templates/footer');
    }

    public function prosesPembayaran() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $tagihan_id = (int)($_POST['tagihan_id'] ?? 0);
        $id_siswa = (int)($_POST['id_siswa'] ?? 0);
        $jumlah = (int)($_POST['jumlah'] ?? 0);
        $metode = $_POST['metode'] ?? null;
        $keterangan = $_POST['keterangan'] ?? null;

        // TODO (optional): handle upload bukti
        $bukti_path = null;

        try {
            $ok = $this->model('Pembayaran_model')->createTransaksi($tagihan_id, $id_siswa, $jumlah, $metode, $keterangan, $bukti_path, $_SESSION['user_id'] ?? null);
            if ($ok) {
                Flasher::setFlash('Pembayaran berhasil dicatat.', 'success');
                header('Location: ' . BASEURL . '/waliKelas/pembayaranTagihan/' . $tagihan_id);
            } else {
                Flasher::setFlash('Gagal mencatat pembayaran.', 'danger');
                header('Location: ' . BASEURL . '/waliKelas/pembayaranInput/' . $tagihan_id . '/' . $id_siswa);
            }
        } catch (Exception $e) {
            error_log('prosesPembayaran error: ' . $e->getMessage());
            Flasher::setFlash('Terjadi kesalahan saat menyimpan pembayaran.', 'danger');
            header('Location: ' . BASEURL . '/waliKelas/pembayaranTagihan/' . $tagihan_id);
        }
        exit;
    }

    public function pembayaranRiwayat() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        if (!$waliKelasInfo) {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $this->data['judul'] = 'Riwayat Pembayaran';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['riwayat'] = $this->model('Pembayaran_model')->getRiwayat($waliKelasInfo['id_kelas'], $id_tp_aktif, 200);

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_walikelas', $this->data);
        $this->view('wali_kelas/pembayaran_riwayat', $this->data);
        $this->view('templates/footer');
    }

    public function pembayaranExport() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        if (!$waliKelasInfo) { exit; }

        // Build riwayat PDF
        $rows = $this->model('Pembayaran_model')->getRiwayat($waliKelasInfo['id_kelas'], $id_tp_aktif, 10000);

        $namaSemester = $_SESSION['nama_semester_aktif'] ?? '';
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body { font-family: Arial, sans-serif; margin: 15px; font-size: 11px; line-height: 1.3; }
            .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .header h1 { color: #333; font-size: 18px; margin: 0; font-weight: bold; }
            .header h2 { color: #666; font-size: 12px; margin: 5px 0 0 0; font-weight: normal; }
            .info-section { background: #f9f9f9; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; }
            .info-row { margin-bottom: 3px; }
            .info-label { font-weight: bold; display: inline-block; width: 120px; color: #333; }
            .info-value { color: #666; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 6px; }
            th { background: #f5f5f5; }
            .right { text-align: right; }
        </style></head><body>';
        $html .= '<div class="header">
            <h1>Riwayat Pembayaran</h1>
            <h2>Kelas ' . htmlspecialchars($waliKelasInfo['nama_kelas'] ?? '-') . (!empty($namaSemester) ? ' • ' . htmlspecialchars($namaSemester) : '') . '</h2>
        </div>';
        $html .= '<div class="info-section">
            <div class="info-row"><span class="info-label">Tanggal Cetak</span><span class="info-value">' . date('d/m/Y H:i') . ' WIB</span></div>
        </div>';
        $html .= '<table><thead><tr>
            <th style="width: 20%">Tanggal</th>
            <th style="width: 22%">Siswa</th>
            <th>Tagihan</th>
            <th style="width: 12%">Jumlah</th>
            <th style="width: 12%">Metode</th>
            <th>Keterangan</th>
        </tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr>'
                . '<td>' . htmlspecialchars($r['tanggal']) . '</td>'
                . '<td>' . htmlspecialchars($r['nama_siswa']) . '</td>'
                . '<td>' . htmlspecialchars($r['nama_tagihan']) . '</td>'
                . '<td class="right">' . number_format((int)$r['jumlah'], 0, ',', '.') . '</td>'
                . '<td>' . htmlspecialchars($r['metode'] ?? '-') . '</td>'
                . '<td>' . htmlspecialchars($r['keterangan'] ?? '-') . '</td>'
                . '</tr>';
        }
        if (empty($rows)) {
            $html .= '<tr><td colspan="6" style="text-align:center;color:#666;">Belum ada transaksi</td></tr>';
        }
        $html .= '</tbody></table></body></html>';

        // Dompdf (consistent with existing usage)
        require_once APPROOT . '/app/core/dompdf/autoload.inc.php';
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Add QR code for document validation
        require_once APPROOT . '/app/core/PDFQRHelper.php';
        $html = PDFQRHelper::addQRToPDF($html, 'pembayaran_riwayat', $waliKelasInfo['id_kelas']);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $filename = 'Riwayat_Pembayaran_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $waliKelasInfo['nama_kelas'] ?? 'Kelas') . '_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    public function pembayaranTagihanPDF($tagihan_id) {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/waliKelas/pembayaran');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        if (!$waliKelasInfo) { exit; }

        $tagihan = $this->model('Pembayaran_model')->getTagihanById($tagihan_id);
        if (!$tagihan || (int)$tagihan['id_kelas'] !== (int)$waliKelasInfo['id_kelas']) { exit; }

        $siswa_list = $this->model('Siswa_model')->getSiswaByKelas($waliKelasInfo['id_kelas'], $id_tp_aktif);
        $tagihan_siswa = $this->model('Pembayaran_model')->getTagihanSiswaList($tagihan_id);
        $map = [];
        foreach ($tagihan_siswa as $ts) { $map[$ts['id_siswa']] = $ts; }

        $namaSemester = $_SESSION['nama_semester_aktif'] ?? '';
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body { font-family: Arial, sans-serif; margin: 15px; font-size: 11px; line-height: 1.3; }
            .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .header h1 { color: #333; font-size: 18px; margin: 0; font-weight: bold; }
            .header h2 { color: #666; font-size: 12px; margin: 5px 0 0 0; font-weight: normal; }
            .info-section { background: #f9f9f9; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; }
            .info-row { margin-bottom: 3px; }
            .info-label { font-weight: bold; display: inline-block; width: 120px; color: #333; }
            .info-value { color: #666; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 6px; }
            th { background: #f5f5f5; }
            .right { text-align: right; }
        </style></head><body>';
        $html .= '<div class="header">'
            . '<h1>Rekap Tagihan</h1>'
            . '<h2>Kelas ' . htmlspecialchars($waliKelasInfo['nama_kelas'] ?? '-') . (!empty($namaSemester) ? ' • ' . htmlspecialchars($namaSemester) : '') . '</h2>'
            . '</div>';
        $html .= '<div class="info-section">'
            . '<div class="info-row"><span class="info-label">Tagihan</span><span class="info-value">' . htmlspecialchars($tagihan['nama']) . '</span></div>'
            . '<div class="info-row"><span class="info-label">Nominal Default</span><span class="info-value">Rp ' . number_format((int)$tagihan['nominal_default'], 0, ',', '.') . '</span></div>'
            . '<div class="info-row"><span class="info-label">Jatuh Tempo</span><span class="info-value">' . (!empty($tagihan['jatuh_tempo']) ? htmlspecialchars($tagihan['jatuh_tempo']) : '-') . '</span></div>'
            . '<div class="info-row"><span class="info-label">Tanggal Cetak</span><span class="info-value">' . date('d/m/Y H:i') . ' WIB</span></div>'
            . '</div>';
        $html .= '<table><thead><tr>
            <th style="width:30px;">No</th>
            <th>Nama Siswa</th>
            <th style="width:16%">Nominal</th>
            <th style="width:16%">Diskon</th>
            <th style="width:16%">Terbayar</th>
            <th style="width:12%">Status</th>
        </tr></thead><tbody>';
        $no = 1;
        foreach ($siswa_list as $s) {
            $ts = $map[$s['id_siswa']] ?? null;
            $nominal = $ts['nominal'] ?? ($tagihan['nominal_default'] ?? 0);
            $diskon = $ts['diskon'] ?? 0;
            $terbayar = $ts['total_terbayar'] ?? 0;
            $status = $ts['status'] ?? 'belum';
            $html .= '<tr>'
                . '<td class="right">' . $no++ . '</td>'
                . '<td>' . htmlspecialchars($s['nama_siswa']) . '</td>'
                . '<td class="right">' . number_format((int)$nominal, 0, ',', '.') . '</td>'
                . '<td class="right">' . number_format((int)$diskon, 0, ',', '.') . '</td>'
                . '<td class="right">' . number_format((int)$terbayar, 0, ',', '.') . '</td>'
                . '<td>' . htmlspecialchars(ucfirst($status)) . '</td>'
                . '</tr>';
        }
        if (empty($siswa_list)) {
            $html .= '<tr><td colspan="6" style="text-align:center;color:#666;">Tidak ada siswa</td></tr>';
        }
        $html .= '</tbody></table></body></html>';

        // Dompdf (consistent with existing usage)
        require_once APPROOT . '/app/core/dompdf/autoload.inc.php';
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Add QR code for document validation
        require_once APPROOT . '/app/core/PDFQRHelper.php';
        $html = PDFQRHelper::addQRToPDF($html, 'pembayaran_tagihan', $tagihan_id);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = 'Rekap_Tagihan_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $tagihan['nama']) . '_' . date('Ymd_His') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
