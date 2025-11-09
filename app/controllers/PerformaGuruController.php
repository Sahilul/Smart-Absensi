<?php
// File: app/controllers/PerformaGuruController.php - UPDATED CONTROLLER WITH CLASS FILTER

class PerformaGuruController extends Controller
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

        $this->performaModel = $this->model('PerformaGuru_model');
    }

    public function index()
    {
        $data['judul'] = 'Performa Kinerja Guru';
        $data['session_info'] = [
            'nama_semester' => $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui',
            'id_tp_aktif' => $_SESSION['id_tp_aktif'] ?? 5
        ];

        $this->view('templates/header', $data);
        
        // Load sidebar based on role
        if ($_SESSION['user_role'] === 'admin') {
            $this->view('templates/sidebar_admin', $data);
        } else {
            $this->view('templates/sidebar_kepala_madrasah', $data);
        }
        
        $this->view('performa_guru/index', $data);
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
        $guruFilter = $input['guru_filter'] ?? '';
        $mapelFilter = $input['mapel_filter'] ?? '';
        $kelasFilter = $input['kelas_filter'] ?? '';
        $jenjangFilter = $input['jenjang_filter'] ?? '';

        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 5;

        try {
            $data = $this->performaModel->getPerformaGuru($startDate, $endDate, $id_semester_aktif, $guruFilter, $mapelFilter, $kelasFilter, $jenjangFilter);
            
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
        $id_guru = $input['id_guru'] ?? 0;
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');
        $kelasFilter = $input['kelas_filter'] ?? '';
        $jenjangFilter = $input['jenjang_filter'] ?? '';

        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 5;

        if (!$id_guru) {
            echo json_encode(['status' => 'error', 'message' => 'ID Guru tidak valid']);
            return;
        }

        try {
            $guruInfo = $this->performaModel->getGuruInfo($id_guru);
            $detailData = $this->performaModel->getDetailPerformaGuru($id_guru, $startDate, $endDate, $id_semester_aktif, $kelasFilter, $jenjangFilter);
            
            echo json_encode([
                'status' => 'success',
                'guru_info' => $guruInfo,
                'detail_data' => $detailData
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getJurnalDetail()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id_penugasan = $input['id_penugasan'] ?? 0;
        $startDate = $input['start_date'] ?? date('Y-m-d');
        $endDate = $input['end_date'] ?? date('Y-m-d');

        if (!$id_penugasan) {
            echo json_encode(['status' => 'error', 'message' => 'ID Penugasan tidak valid']);
            return;
        }

        try {
            $jurnalData = $this->performaModel->getJurnalDetail($id_penugasan, $startDate, $endDate);
            
            echo json_encode([
                'status' => 'success',
                'jurnal_data' => $jurnalData
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getGuru()
    {
        header('Content-Type: application/json');
        
        try {
            $guru = $this->performaModel->getGuruOptions();
            
            echo json_encode([
                'status' => 'success',
                'guru' => $guru
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getMapel()
    {
        header('Content-Type: application/json');
        
        try {
            $mapel = $this->performaModel->getMapelOptions();
            
            echo json_encode([
                'status' => 'success',
                'mapel' => $mapel
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
            $id_tp = $_SESSION['id_tp_aktif'] ?? 5;
            $kelas = $this->performaModel->getKelasOptions($id_tp);
            
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

    public function getJenjang()
    {
        header('Content-Type: application/json');
        
        try {
            $jenjang = $this->performaModel->getJenjangOptions();
            
            echo json_encode([
                'status' => 'success',
                'jenjang' => $jenjang
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
            header('Location: ' . BASEURL . '/PerformaGuru');
            return;
        }

        $startDate = $_POST['start_date'] ?? date('Y-m-d');
        $endDate = $_POST['end_date'] ?? date('Y-m-d');
        $guruFilter = $_POST['guru_filter'] ?? '';
        $mapelFilter = $_POST['mapel_filter'] ?? '';
        $kelasFilter = $_POST['kelas_filter'] ?? '';
        $jenjangFilter = $_POST['jenjang_filter'] ?? '';
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 5;

        try {
            // Get data
            $data = $this->performaModel->getPerformaGuru($startDate, $endDate, $id_semester_aktif, $guruFilter, $mapelFilter, $kelasFilter, $jenjangFilter);
            
            // Get guru info if filtered
            $guruInfo = 'Semua Guru';
            if (!empty($guruFilter)) {
                $guru = $this->performaModel->getGuruOptions();
                foreach ($guru as $g) {
                    if ($g['id_guru'] == $guruFilter) {
                        $guruInfo = $g['nama_guru'];
                        break;
                    }
                }
            }

            // Get mapel info if filtered
            $mapelInfo = 'Semua Mata Pelajaran';
            if (!empty($mapelFilter)) {
                $mapel = $this->performaModel->getMapelOptions();
                foreach ($mapel as $m) {
                    if ($m['id_mapel'] == $mapelFilter) {
                        $mapelInfo = $m['nama_mapel'];
                        break;
                    }
                }
            }

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

            // Get jenjang info if filtered
            $jenjangInfo = 'Semua Jenjang';
            if (!empty($jenjangFilter)) {
                switch ($jenjangFilter) {
                    case 'mts':
                        $jenjangInfo = 'MTs Sabilillah (Kelas 7-9)';
                        break;
                    case 'ma':
                        $jenjangInfo = 'MA Sabilillah (Kelas 10-12)';
                        break;
                    default:
                        $jenjangInfo = 'Kelas ' . $jenjangFilter;
                        break;
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
            $html = $this->generatePdfHtml($data, $startDate, $endDate, $guruInfo, $mapelInfo, $kelasInfo, $jenjangInfo, $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui');
            
            // Add QR code for document validation
            require_once APPROOT . '/app/core/PDFQRHelper.php';
            $html = PDFQRHelper::addQRToPDF($html, 'performa_guru', $id_guru . '_' . $startDate);
            
            // Load HTML
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            
            // Render PDF
            $dompdf->render();
            
            // Generate filename
            $filename = 'Laporan_Performa_Guru_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Output PDF
            $dompdf->stream($filename, array("Attachment" => true));
            
        } catch (Exception $e) {
            header('Location: ' . BASEURL . '/PerformaGuru?error=export_failed');
        }
    }

    private function generatePdfHtml($data, $startDate, $endDate, $guruInfo, $mapelInfo, $kelasInfo, $jenjangInfo, $semesterInfo)
    {
        // Format dates
        $startDateFormatted = date('d/m/Y', strtotime($startDate));
        $endDateFormatted = date('d/m/Y', strtotime($endDate));
        
        // Calculate statistics
        $totalGuru = count($data);
        $totalJurnal = array_sum(array_column($data, 'total_jurnal'));
        $rataJurnal = $totalGuru > 0 ? number_format($totalJurnal / $totalGuru, 1) : 0;
        $guruAktif = count(array_filter($data, fn($g) => $g['total_jurnal'] > 0));
        $guruTidakAktif = $totalGuru - $guruAktif;

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Performa Guru</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 15px; font-size: 11px; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { color: #333; font-size: 18px; margin: 0; font-weight: bold; }
        .header h2 { color: #666; font-size: 12px; margin: 5px 0 0 0; font-weight: normal; }
        .info-section { background: #f9f9f9; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; }
        .info-row { margin-bottom: 3px; }
        .info-label { font-weight: bold; display: inline-block; width: 120px; color: #333; }
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
        .footer { margin-top: 15px; text-align: center; color: #666; font-size: 9px; border-top: 1px solid #ddd; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERFORMA KINERJA GURU</h1>
        <h2>Sistem Informasi Manajemen Sekolah</h2>
    </div>
    
    <div class="info-section">
        <div class="info-row"><span class="info-label">Periode:</span> <span class="info-value">' . $startDateFormatted . ' - ' . $endDateFormatted . '</span></div>
        <div class="info-row"><span class="info-label">Guru:</span> <span class="info-value">' . htmlspecialchars($guruInfo) . '</span></div>
        <div class="info-row"><span class="info-label">Mata Pelajaran:</span> <span class="info-value">' . htmlspecialchars($mapelInfo) . '</span></div>
        <div class="info-row"><span class="info-label">Kelas:</span> <span class="info-value">' . htmlspecialchars($kelasInfo) . '</span></div>
        <div class="info-row"><span class="info-label">Jenjang:</span> <span class="info-value">' . htmlspecialchars($jenjangInfo) . '</span></div>
        <div class="info-row"><span class="info-label">Semester:</span> <span class="info-value">' . htmlspecialchars($semesterInfo) . '</span></div>
        <div class="info-row"><span class="info-label">Tanggal Cetak:</span> <span class="info-value">' . date('d/m/Y H:i') . '</span></div>
    </div>
    
    <div class="stats">
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-number">' . $totalGuru . '</div>
                <div class="stat-label">Total Guru</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">' . $totalJurnal . '</div>
                <div class="stat-label">Total Jurnal</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">' . $guruAktif . '</div>
                <div class="stat-label">Guru Aktif</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">' . $rataJurnal . '</div>
                <div class="stat-label">Rata Jurnal/Guru</div>
            </div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="18%">NIK</th>
                <th width="27%">Nama Guru</th>
                <th width="8%">Penugasan</th>
                <th width="8%">Mapel</th>
                <th width="8%">Kelas</th>
                <th width="8%">Jurnal</th>
                <th width="8%">Hari</th>
                <th width="10%">Jurnal Terakhir</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($data as $index => $row) {
            $jurnalTerakhir = $row['jurnal_terakhir'] ? date('d/m/Y', strtotime($row['jurnal_terakhir'])) : '-';
            
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . htmlspecialchars($row['nik'] ?? '-') . '</td>
                <td class="text-left">' . htmlspecialchars($row['nama_guru']) . '</td>
                <td><strong>' . $row['total_penugasan'] . '</strong></td>
                <td>' . $row['total_mapel'] . '</td>
                <td>' . $row['total_kelas'] . '</td>
                <td style="color: #0066cc; font-weight: bold;">' . $row['total_jurnal'] . '</td>
                <td style="color: #009900;">' . $row['total_hari_mengajar'] . '</td>
                <td>' . $jurnalTerakhir . '</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p><strong>Keterangan:</strong> Penugasan = Mapel & Kelas yang diampu | Jurnal = Jurnal pembelajaran yang dibuat | Hari = Hari mengajar aktif</p>
        <p>Dicetak pada: ' . date('d/m/Y H:i:s') . '</p>
    </div>
</body>
</html>';

        return $html;
    }
}