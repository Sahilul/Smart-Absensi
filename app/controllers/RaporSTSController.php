<?php
// File: app/controllers/RaporSTSController.php

class RaporSTSController extends Controller {
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

        $this->data['daftar_semester'] = $this->model('TahunPelajaran_model')->getAllSemester();
    }

    /**
     * Halaman Utama Rapor STS (Wali Kelas)
     */
    public function index() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/admin/dashboard');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        // Ambil info wali kelas
        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        
        if (!$waliKelasInfo) {
            Flasher::setFlash('Anda belum ditugaskan sebagai wali kelas!', 'warning');
            header('Location: ' . BASEURL . '/waliKelas/dashboard');
            exit;
        }

        $this->data['judul'] = 'Rapor STS';
        $this->data['wali_kelas_info'] = $waliKelasInfo;
        $this->data['id_kelas'] = $waliKelasInfo['id_kelas'];
        
        // Ambil daftar siswa dengan nilai STS mereka
        $this->data['siswa_list'] = $this->model('Siswa_model')->getSiswaByKelas($waliKelasInfo['id_kelas'], $id_tp_aktif);
        $this->data['mapel_list'] = $this->model('Penugasan_model')->getMapelByKelas($waliKelasInfo['id_kelas'], $id_semester_aktif);

        $this->view('templates/header', $this->data);
        $this->view('wali_kelas/rapor_sts', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Generate Rapor STS per Siswa
     */
    public function generate($id_siswa) {
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        // Ambil data siswa
        $siswa = $this->model('Siswa_model')->getSiswaById($id_siswa);
        if (!$siswa) {
            Flasher::setFlash('Siswa tidak ditemukan!', 'danger');
            header('Location: ' . BASEURL . '/raporSTS');
            exit;
        }

        // Ambil nilai-nilai siswa
        $this->data['judul'] = 'Rapor STS - ' . $siswa['nama_siswa'];
        $this->data['siswa'] = $siswa;
        $this->data['semester'] = $this->model('TahunPelajaran_model')->getSemesterById($id_semester_aktif);
        $this->data['nilai_mapel'] = $this->model('NilaiSiswa_model')->getNilaiRaporSTS($id_siswa, $id_semester_aktif);
        $this->data['rekap_absensi'] = $this->model('Absensi_model')->getRekapAbsensiSiswa($id_siswa, $id_semester_aktif);

        $this->view('templates/header', $this->data);
        $this->view('wali_kelas/rapor_sts_generate', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Cetak Rapor STS PDF
     */
    public function cetak($id_siswa) {
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;

        // Ambil data siswa
        $siswa = $this->model('Siswa_model')->getSiswaById($id_siswa);
        if (!$siswa) {
            Flasher::setFlash('Siswa tidak ditemukan!', 'danger');
            header('Location: ' . BASEURL . '/raporSTS');
            exit;
        }

        // Ambil data untuk rapor
        $semester = $this->model('TahunPelajaran_model')->getSemesterById($id_semester_aktif);
        $nilai_mapel = $this->model('NilaiSiswa_model')->getNilaiRaporSTS($id_siswa, $id_semester_aktif);
        $rekap_absensi = $this->model('Absensi_model')->getRekapAbsensiSiswa($id_siswa, $id_semester_aktif);
        $wali_kelas = $this->model('WaliKelas_model')->getWaliKelasByKelas($siswa['id_kelas'], $id_tp_aktif);
        $pengaturan = $this->model('PengaturanRapor_model')->getPengaturan($id_semester_aktif);

        // Load DOMPDF
        require_once APPROOT . '/app/core/dompdf/autoload.inc.php';
        
        use Dompdf\Dompdf;
        use Dompdf\Options;

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);

        // Generate HTML untuk PDF
        ob_start();
        include APPROOT . '/app/views/wali_kelas/rapor_sts_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Rapor_STS_' . $siswa['nama_siswa'] . '_' . $semester['nama_tp'] . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Cetak Massal Rapor STS untuk Seluruh Kelas
     */
    public function cetakKelas() {
        if ($_SESSION['role'] !== 'wali_kelas') {
            header('Location: ' . BASEURL . '/admin/dashboard');
            exit;
        }

        $id_guru = $_SESSION['id_ref'] ?? 0;
        $id_tp_aktif = $_SESSION['id_tp_aktif'] ?? 0;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? 0;

        $waliKelasInfo = $this->model('WaliKelas_model')->getWaliKelasByGuru($id_guru, $id_tp_aktif);
        
        if (!$waliKelasInfo) {
            Flasher::setFlash('Anda belum ditugaskan sebagai wali kelas!', 'warning');
            header('Location: ' . BASEURL . '/waliKelas/dashboard');
            exit;
        }

        // Ambil semua siswa di kelas
        $siswa_list = $this->model('Siswa_model')->getSiswaByKelas($waliKelasInfo['id_kelas'], $id_tp_aktif);
        
        // Load DOMPDF
        require_once APPROOT . '/app/core/dompdf/autoload.inc.php';
        
        use Dompdf\Dompdf;
        use Dompdf\Options;

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);

        $html = '';
        
        // Generate PDF untuk setiap siswa
        foreach ($siswa_list as $siswa) {
            $id_siswa = $siswa['id_siswa'];
            $semester = $this->model('TahunPelajaran_model')->getSemesterById($id_semester_aktif);
            $nilai_mapel = $this->model('NilaiSiswa_model')->getNilaiRaporSTS($id_siswa, $id_semester_aktif);
            $rekap_absensi = $this->model('Absensi_model')->getRekapAbsensiSiswa($id_siswa, $id_semester_aktif);
            $wali_kelas = $waliKelasInfo;
            $pengaturan = $this->model('PengaturanRapor_model')->getPengaturan($id_semester_aktif);

            ob_start();
            include APPROOT . '/app/views/wali_kelas/rapor_sts_pdf.php';
            $html .= ob_get_clean();
            $html .= '<div style="page-break-after: always;"></div>';
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Rapor_STS_Kelas_' . $waliKelasInfo['nama_kelas'] . '_' . $semester['nama_tp'] . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }
}
