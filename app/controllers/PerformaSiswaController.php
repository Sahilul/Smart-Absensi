<?php
// File: app/controllers/PerformaSiswaController.php - UPDATED WITH DETAIL METHOD

class PerformaSiswaController extends Controller
{
    private $performaModel;

    public function __construct()
    {
        // Check auth - Allow both admin and kepala_madrasah
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'kepala_madrasah'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        // Set default semester if not exists
        if (!isset($_SESSION['id_semester_aktif'])) {
            $_SESSION['id_semester_aktif'] = 5; // Default to semester 5
            $_SESSION['nama_semester_aktif'] = '2025/2026 - Ganjil';
            $_SESSION['id_tp_aktif'] = 5;
        }

        $this->performaModel = $this->model('PerformaSiswa_model');
    }

    public function index()
    {
        $data['judul'] = 'Performa Kehadiran Siswa';
        $data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui'
        ];

        $this->view('templates/header', $data);
        
        // Load sidebar based on role
        if ($_SESSION['user_role'] === 'admin') {
            $this->view('templates/sidebar_admin', $data);
        } else {
            $this->view('templates/sidebar_kepala_madrasah', $data);
        }
        
        $this->view('performa_siswa/index', $data);
        $this->view('templates/footer');
    }

    public function getData()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');
        $kelasFilter = $input['kelas_filter'] ?? '';

        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 5;

        try {
            $data = $this->performaModel->getPerformaSiswa($startDate, $endDate, $id_semester_aktif, $kelasFilter);
            
            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getDetail()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id_siswa = $input['id_siswa'] ?? 0;
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');

        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 5;

        if (!$id_siswa) {
            echo json_encode(['status' => 'error', 'message' => 'ID Siswa tidak valid']);
            return;
        }

        try {
            $siswaInfo = $this->performaModel->getSiswaInfo($id_siswa);
            $detailData = $this->performaModel->getDetailPerformaSiswa($id_siswa, $startDate, $endDate, $id_semester_aktif);
            
            echo json_encode([
                'status' => 'success',
                'siswa_info' => $siswaInfo,
                'detail_data' => $detailData
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getKelas()
    {
        header('Content-Type: application/json');
        
        try {
            $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 5;
            $kelas = $this->performaModel->getKelasOptions($id_tp_aktif);
            
            echo json_encode([
                'status' => 'success',
                'kelas' => $kelas
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function exportPdf()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/PerformaSiswa');
            return;
        }

        $startDate = $_POST['start_date'] ?? date('Y-m-d');
        $endDate = $_POST['end_date'] ?? date('Y-m-d');
        $kelasFilter = $_POST['kelas_filter'] ?? '';
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 5;

        try {
            // Get data
            $data = $this->performaModel->getPerformaSiswa($startDate, $endDate, $id_semester_aktif, $kelasFilter);
            
            // Get kelas info if filtered
            $kelasInfo = 'Semua Kelas';
            if (!empty($kelasFilter)) {
                $kelas = $this->performaModel->getKelasOptions($_SESSION['id_tp_aktif'] ?? 5);
                foreach ($kelas as $k) {
                    if ($k['id_kelas'] == $kelasFilter) {
                        $kelasInfo = $k['nama_kelas'];
                        break;
                    }
                }
            }

            // Include dompdf
            require_once __DIR__ . '/../core/dompdf/autoload.inc.php';

            // Configure dompdf options
            $options = new \Dompdf\Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            
            // Create dompdf instance
            $dompdf = new \Dompdf\Dompdf($options);

            // Generate HTML
            $html = $this->generatePdfHtml($data, $startDate, $endDate, $kelasInfo, $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui');
            
            // Add QR code for document validation
            require_once APPROOT . '/app/core/PDFQRHelper.php';
            $html = PDFQRHelper::addQRToPDF($html, 'performa_siswa', $id_kelas . '_' . $startDate);
            
            // Load HTML
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'landscape');
            
            // Render PDF
            $dompdf->render();
            
            // Generate filename
            $filename = 'Laporan_Performa_Siswa_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Output PDF
            $dompdf->stream($filename, array("Attachment" => true));
            
        } catch (Exception $e) {
            header('Location: ' . BASEURL . '/PerformaSiswa?error=export_failed');
        }
    }

    private function generatePdfHtml($data, $startDate, $endDate, $kelasInfo, $semesterInfo)
    {
        // Format dates
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
        <div class="info-row"><span class="info-label">Kelas:</span> <span class="info-value">' . htmlspecialchars($kelasInfo) . '</span></div>
        <div class="info-row"><span class="info-label">Semester:</span> <span class="info-value">' . htmlspecialchars($semesterInfo) . '</span></div>
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
}