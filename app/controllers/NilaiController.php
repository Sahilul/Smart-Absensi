<?php
// File: app/controllers/NilaiController.php

class NilaiController extends Controller {
    private $nilaiModel;
    private $data = [];

    public function __construct() {
        // Guard akses: Guru dan Wali Kelas bisa mengakses
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? null, ['guru', 'wali_kelas'])) {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }

        // Set data umum
        $this->data['daftar_semester'] = $this->model('TahunPelajaran_model')->getAllSemester();
        $this->data['judul'] = 'Nilai';

        // Load model
        $this->nilaiModel = $this->model('Nilai_model');
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
     * Halaman utama menu Nilai - Redirect ke dashboard
     */
    public function index() {
        // Input nilai sudah dipindahkan ke dashboard guru
        // Redirect ke dashboard
        header('Location: ' . BASEURL . '/guru');
        exit;
    }

    /**
     * Halaman pilih kelas untuk input nilai
     */
    public function pilihKelas() {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester_aktif) {
            Flasher::setFlash('Gagal mengakses data. Pastikan Anda login dan semester aktif.', 'danger');
            header('Location: ' . BASEURL . '/nilai/index');
            exit;
        }

        // Ambil parameter jenis nilai dari URL (opsional)
        $jenis = $_GET['jenis'] ?? null; // harian, sts, sas
        $this->data['jenis_nilai'] = $jenis;

        $this->data['judul'] = 'Pilih Kelas - Input Nilai';
        $this->data['jadwal_mengajar'] = $this->model('Penugasan_model')->getPenugasanByGuru($id_guru, $id_semester_aktif);

        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/nilai/pilih_kelas', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Tampilkan daftar jurnal untuk dipilih sebelum input nilai harian
     */
    public function tugasHarian() {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester_aktif) {
            Flasher::setFlash('Gagal mengakses data. Pastikan Anda login dan semester aktif.', 'danger');
            header('Location: ' . BASEURL . '/guru/dashboard');
            exit;
        }

        // Ambil parameter dari URL
        $id_penugasan = $_GET['id_penugasan'] ?? null;

        if (!$id_penugasan) {
            Flasher::setFlash('ID Penugasan tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/nilai');
            exit;
        }

        // Ambil data penugasan
        $penugasan = $this->model('Penugasan_model')->getPenugasanById($id_penugasan);
        $this->data['penugasan'] = $penugasan;
        
        // Ambil daftar jurnal untuk penugasan ini
        $jurnal_list = $this->model('Jurnal_model')->getJurnalByPenugasan($id_penugasan);
        
        // Cek setiap jurnal apakah sudah ada nilainya
        foreach ($jurnal_list as &$jurnal) {
            $nilai_count = $this->nilaiModel->countNilaiByJurnal($jurnal['id_jurnal']);
            $jurnal['has_nilai'] = $nilai_count > 0;
            $jurnal['jumlah_nilai'] = $nilai_count;
        }
        
        $this->data['jurnal_list'] = $jurnal_list;

        // Tampilkan view pilih jurnal
        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/nilai/pilih_jurnal', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Form input nilai harian berdasarkan jurnal yang dipilih
     */
    public function inputNilaiHarian() {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester_aktif) {
            Flasher::setFlash('Gagal mengakses data. Pastikan Anda login dan semester aktif.', 'danger');
            header('Location: ' . BASEURL . '/guru/dashboard');
            exit;
        }

        // Ambil parameter dari URL
        $id_jurnal = $_GET['id_jurnal'] ?? null;

        if (!$id_jurnal) {
            Flasher::setFlash('ID Jurnal tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/nilai');
            exit;
        }

        // Ambil data jurnal dengan detail
        $jurnal = $this->model('Jurnal_model')->getJurnalDetailById($id_jurnal);
        if (!$jurnal) {
            Flasher::setFlash('Jurnal tidak ditemukan.', 'danger');
            header('Location: ' . BASEURL . '/nilai');
            exit;
        }
        $this->data['jurnal'] = $jurnal;
        
        // Ambil data penugasan
        $penugasan = $this->model('Penugasan_model')->getPenugasanById($jurnal['id_penugasan']);
        $this->data['penugasan'] = $penugasan;
        
        // Ambil siswa dan absensi berdasarkan jurnal
        $this->data['siswa_list'] = $this->model('Absensi_model')->getSiswaDanAbsensiByJurnal($id_jurnal);
        
        // Ambil nilai yang sudah ada
        $this->data['nilai_tugas_harian'] = $this->nilaiModel->getNilaiTugasHarianByJurnal($id_jurnal);

        // Siapkan data siswa
        $this->data['filtered_siswa'] = $this->data['siswa_list'];

        // Tampilkan view input nilai
        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/nilai/nilai_tugas_harian', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Proses penyimpanan nilai tugas harian
     */
    public function prosesSimpanTugasHarian() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDASI INPUT
            $id_jurnal = InputValidator::sanitizeInt($_POST['id_jurnal'] ?? 0);
            $id_penugasan = InputValidator::sanitizeInt($_POST['id_penugasan'] ?? 0);
            $nilai_array = $_POST['nilai'] ?? [];

            if (!$id_jurnal || !$id_penugasan) {
                Flasher::setFlash('Data tidak lengkap.', 'danger');
                header('Location: ' . BASEURL . '/nilai');
                exit;
            }

            // Validasi array nilai
            if (!is_array($nilai_array)) {
                Flasher::setFlash('Format data nilai tidak valid.', 'danger');
                header('Location: ' . BASEURL . '/nilai');
                exit;
            }

            // Get jurnal data (with detail including id_semester)
            $jurnal = $this->model('Jurnal_model')->getJurnalDetailById($id_jurnal);
            $penugasan = $this->model('Penugasan_model')->getPenugasanById($id_penugasan);
            
            // Ambil id_semester dari penugasan (lebih reliable)
            $id_semester = $penugasan['id_semester'] ?? null;
            
            if (!$id_semester) {
                Flasher::setFlash('Data semester tidak ditemukan.', 'danger');
                header('Location: ' . BASEURL . '/guru');
                exit;
            }
            
            $sukses = 0;
            $gagal = 0;

            foreach ($nilai_array as $id_siswa => $nilai) {
                // Sanitize ID siswa
                $id_siswa = InputValidator::sanitizeInt($id_siswa);
                if (!$id_siswa) {
                    $gagal++;
                    continue;
                }

                // Skip jika nilai kosong
                if (empty($nilai)) continue;
                
                // Validasi dan sanitasi nilai
                $nilai = InputValidator::sanitizeNilai($nilai);
                if ($nilai === false) {
                    $gagal++;
                    continue;
                }

                $data = [
                    'id_siswa' => $id_siswa,
                    'id_guru' => (int)$penugasan['id_guru'],
                    'id_mapel' => (int)$penugasan['id_mapel'],
                    'id_semester' => (int)$id_semester,
                    'jenis_nilai' => 'harian',
                    'keterangan' => $id_jurnal, // Simpan id_jurnal di keterangan
                    'nilai' => $nilai,
                    'tanggal_input' => date('Y-m-d')
                ];

                if ($this->nilaiModel->simpanNilaiHarian($data)) {
                    $sukses++;
                } else {
                    $gagal++;
                }
            }

            if ($sukses > 0) {
                Flasher::setFlash("Berhasil menyimpan $sukses nilai.", 'success');
            }
            if ($gagal > 0) {
                Flasher::setFlash("Gagal menyimpan $gagal nilai.", 'danger');
            }

            header('Location: ' . BASEURL . '/nilai/inputNilaiHarian?id_jurnal=' . $id_jurnal);
            exit;
        }

        header('Location: ' . BASEURL . '/nilai');
        exit;
    }

    /**
     * Proses edit nilai tugas harian
     */
    public function prosesEditTugasHarian() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDASI INPUT
            $id_nilai = InputValidator::sanitizeInt($_POST['id_nilai'] ?? 0);
            $nilai_baru = InputValidator::sanitizeNilai($_POST['nilai'] ?? 0);

            if (!$id_nilai || $nilai_baru === false) {
                Flasher::setFlash('Data tidak lengkap.', 'danger');
                header('Location: ' . BASEURL . '/nilai/tugasHarian?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            if (!is_numeric($nilai_baru) || $nilai_baru < 0 || $nilai_baru > 100) {
                Flasher::setFlash('Nilai harus antara 0-100.', 'danger');
                header('Location: ' . BASEURL . '/nilai/tugasHarian?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            // Edit nilai
            if ($this->nilaiModel->editNilai($id_nilai, $nilai_baru)) {
                Flasher::setFlash('Nilai berhasil diupdate.', 'success');
            } else {
                Flasher::setFlash('Gagal mengupdate nilai.', 'danger');
            }

            header('Location: ' . BASEURL . '/nilai/tugasHarian?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
            exit;
        }

        header('Location: ' . BASEURL . '/nilai');
        exit;
    }

    /**
     * Proses hapus nilai tugas harian
     */
    public function prosesHapusTugasHarian() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_nilai = $_POST['id_nilai'] ?? null;

            if (!$id_nilai) {
                Flasher::setFlash('Data tidak lengkap.', 'danger');
                header('Location: ' . BASEURL . '/nilai/tugasHarian?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            // Hapus nilai
            if ($this->nilaiModel->hapusNilai($id_nilai)) {
                Flasher::setFlash('Nilai berhasil dihapus.', 'success');
            } else {
                Flasher::setFlash('Gagal menghapus nilai.', 'danger');
            }

            header('Location: ' . BASEURL . '/nilai/tugasHarian?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
            exit;
        }

        header('Location: ' . BASEURL . '/nilai');
        exit;
    }

    /**
     * Halaman detail nilai tengah semester
     */
    public function tengahSemester() {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester_aktif) {
            Flasher::setFlash('Gagal mengakses data. Pastikan Anda login dan semester aktif.', 'danger');
            header('Location: ' . BASEURL . '/guru/dashboard');
            exit;
        }

        // Ambil parameter dari URL
        $id_penugasan = $_GET['id_penugasan'] ?? null;

        if (!$id_penugasan) {
            Flasher::setFlash('ID Penugasan tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/nilai');
            exit;
        }

        // Ambil data penugasan dan siswa
        $penugasan = $this->model('Penugasan_model')->getPenugasanById($id_penugasan);
        $this->data['penugasan'] = $penugasan;
        $this->data['siswa_list'] = $this->model('Siswa_model')->getSiswaByKelas($penugasan['id_kelas'], $id_semester_aktif);
        $this->data['nilai_tengah_semester'] = $this->nilaiModel->getNilaiTengahSemesterByPenugasan($id_penugasan);

        // Siapkan data siswa
        $this->data['filtered_siswa'] = $this->data['siswa_list'];

        // Tampilkan view
        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/nilai/nilai_tengah_semester', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Proses penyimpanan nilai tengah semester
     */
    public function prosesSimpanTengahSemester() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_penugasan = $_POST['id_penugasan'] ?? null;
            $nilai_array = $_POST['nilai'] ?? [];

            if (!$id_penugasan) {
                Flasher::setFlash('Data penugasan tidak valid.', 'danger');
                header('Location: ' . BASEURL . '/guru');
                exit;
            }

            if (empty($nilai_array)) {
                Flasher::setFlash('Tidak ada nilai yang diisi.', 'warning');
                header('Location: ' . BASEURL . '/nilai/tengahSemester?id_penugasan=' . $id_penugasan);
                exit;
            }

            // Get penugasan data
            $penugasan = $this->model('Penugasan_model')->getPenugasanById($id_penugasan);
            
            // Ambil id_semester dari penugasan
            $id_semester = $penugasan['id_semester'] ?? null;
            
            if (!$id_semester) {
                Flasher::setFlash('Data semester tidak ditemukan.', 'danger');
                header('Location: ' . BASEURL . '/guru');
                exit;
            }
            
            $sukses = 0;
            $gagal = 0;

            foreach ($nilai_array as $id_siswa => $nilai) {
                // Skip jika nilai kosong
                if (empty($nilai)) continue;
                
                // Validasi nilai
                if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
                    $gagal++;
                    continue;
                }

                $data = [
                    'id_siswa' => $id_siswa,
                    'id_guru' => $penugasan['id_guru'],
                    'id_mapel' => $penugasan['id_mapel'],
                    'id_semester' => $id_semester,
                    'jenis_nilai' => 'sts',
                    'keterangan' => null,
                    'nilai' => $nilai,
                    'tanggal_input' => date('Y-m-d')
                ];

                if ($this->nilaiModel->simpanNilaiTengahSemester($data)) {
                    $sukses++;
                } else {
                    $gagal++;
                }
            }

            if ($sukses > 0) {
                Flasher::setFlash("Berhasil menyimpan $sukses nilai tengah semester.", 'success');
            }
            if ($gagal > 0) {
                Flasher::setFlash("Gagal menyimpan $gagal nilai.", 'warning');
            }

            header('Location: ' . BASEURL . '/nilai/tengahSemester?id_penugasan=' . $id_penugasan);
            exit;
        }

        header('Location: ' . BASEURL . '/guru');
        exit;
    }

    /**
     * Proses edit nilai tengah semester
     */
    public function prosesEditTengahSemester() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_nilai = $_POST['id_nilai'] ?? null;
            $nilai_baru = $_POST['nilai'] ?? null;

            if (!$id_nilai || !$nilai_baru) {
                Flasher::setFlash('Data tidak lengkap.', 'danger');
                header('Location: ' . BASEURL . '/nilai/tengahSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            if (!is_numeric($nilai_baru) || $nilai_baru < 0 || $nilai_baru > 100) {
                Flasher::setFlash('Nilai harus antara 0-100.', 'danger');
                header('Location: ' . BASEURL . '/nilai/tengahSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            // Edit nilai
            if ($this->nilaiModel->editNilai($id_nilai, $nilai_baru)) {
                Flasher::setFlash('Nilai berhasil diupdate.', 'success');
            } else {
                Flasher::setFlash('Gagal mengupdate nilai.', 'danger');
            }

            header('Location: ' . BASEURL . '/nilai/tengahSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
            exit;
        }

        header('Location: ' . BASEURL . '/nilai');
        exit;
    }

    /**
     * Proses hapus nilai tengah semester
     */
    public function prosesHapusTengahSemester() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_nilai = $_POST['id_nilai'] ?? null;

            if (!$id_nilai) {
                Flasher::setFlash('Data tidak lengkap.', 'danger');
                header('Location: ' . BASEURL . '/nilai/tengahSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            // Hapus nilai
            if ($this->nilaiModel->hapusNilai($id_nilai)) {
                Flasher::setFlash('Nilai berhasil dihapus.', 'success');
            } else {
                Flasher::setFlash('Gagal menghapus nilai.', 'danger');
            }

            header('Location: ' . BASEURL . '/nilai/tengahSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
            exit;
        }

        header('Location: ' . BASEURL . '/nilai');
        exit;
    }

    /**
     * Halaman detail nilai akhir semester
     */
    public function akhirSemester() {
        $id_guru = $_SESSION['id_ref'] ?? null;
        $id_semester_aktif = $_SESSION['id_semester_aktif'] ?? null;

        if (!$id_guru || !$id_semester_aktif) {
            Flasher::setFlash('Gagal mengakses data. Pastikan Anda login dan semester aktif.', 'danger');
            header('Location: ' . BASEURL . '/guru/dashboard');
            exit;
        }

        // Ambil parameter dari URL
        $id_penugasan = $_GET['id_penugasan'] ?? null;

        if (!$id_penugasan) {
            Flasher::setFlash('ID Penugasan tidak valid.', 'danger');
            header('Location: ' . BASEURL . '/nilai');
            exit;
        }

        // Ambil data penugasan dan siswa
        $penugasan = $this->model('Penugasan_model')->getPenugasanById($id_penugasan);
        $this->data['penugasan'] = $penugasan;
        $this->data['siswa_list'] = $this->model('Siswa_model')->getSiswaByKelas($penugasan['id_kelas'], $id_semester_aktif);
        $this->data['nilai_akhir_semester'] = $this->nilaiModel->getNilaiAkhirSemesterByPenugasan($id_penugasan);

        // Siapkan data siswa
        $this->data['filtered_siswa'] = $this->data['siswa_list'];

        // Tampilkan view
        $this->view('templates/header', $this->data);
        $this->loadSidebar();
        $this->view('guru/nilai/nilai_akhir_semester', $this->data);
        $this->view('templates/footer');
    }

    /**
     * Proses penyimpanan nilai akhir semester
     */
    public function prosesSimpanAkhirSemester() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_penugasan = $_POST['id_penugasan'] ?? null;
            $nilai_array = $_POST['nilai'] ?? [];

            if (!$id_penugasan) {
                Flasher::setFlash('Data penugasan tidak valid.', 'danger');
                header('Location: ' . BASEURL . '/guru');
                exit;
            }

            if (empty($nilai_array)) {
                Flasher::setFlash('Tidak ada nilai yang diisi.', 'warning');
                header('Location: ' . BASEURL . '/nilai/akhirSemester?id_penugasan=' . $id_penugasan);
                exit;
            }

            // Get penugasan data
            $penugasan = $this->model('Penugasan_model')->getPenugasanById($id_penugasan);
            
            // Ambil id_semester dari penugasan
            $id_semester = $penugasan['id_semester'] ?? null;
            
            if (!$id_semester) {
                Flasher::setFlash('Data semester tidak ditemukan.', 'danger');
                header('Location: ' . BASEURL . '/guru');
                exit;
            }
            
            $sukses = 0;
            $gagal = 0;

            foreach ($nilai_array as $id_siswa => $nilai) {
                // Skip jika nilai kosong
                if (empty($nilai)) continue;
                
                // Validasi nilai
                if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
                    $gagal++;
                    continue;
                }

                $data = [
                    'id_siswa' => $id_siswa,
                    'id_guru' => $penugasan['id_guru'],
                    'id_mapel' => $penugasan['id_mapel'],
                    'id_semester' => $id_semester,
                    'jenis_nilai' => 'sas',
                    'keterangan' => null,
                    'nilai' => $nilai,
                    'tanggal_input' => date('Y-m-d')
                ];

                if ($this->nilaiModel->simpanNilaiAkhirSemester($data)) {
                    $sukses++;
                } else {
                    $gagal++;
                }
            }

            if ($sukses > 0) {
                Flasher::setFlash("Berhasil menyimpan $sukses nilai akhir semester.", 'success');
            }
            if ($gagal > 0) {
                Flasher::setFlash("Gagal menyimpan $gagal nilai.", 'warning');
            }

            header('Location: ' . BASEURL . '/nilai/akhirSemester?id_penugasan=' . $id_penugasan);
            exit;
        }

        header('Location: ' . BASEURL . '/guru');
        exit;
    }

    /**
     * Proses edit nilai akhir semester
     */
    public function prosesEditAkhirSemester() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_nilai = $_POST['id_nilai'] ?? null;
            $nilai_baru = $_POST['nilai'] ?? null;

            if (!$id_nilai || !$nilai_baru) {
                Flasher::setFlash('Data tidak lengkap.', 'danger');
                header('Location: ' . BASEURL . '/nilai/akhirSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            if (!is_numeric($nilai_baru) || $nilai_baru < 0 || $nilai_baru > 100) {
                Flasher::setFlash('Nilai harus antara 0-100.', 'danger');
                header('Location: ' . BASEURL . '/nilai/akhirSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            // Edit nilai
            if ($this->nilaiModel->editNilai($id_nilai, $nilai_baru)) {
                Flasher::setFlash('Nilai berhasil diupdate.', 'success');
            } else {
                Flasher::setFlash('Gagal mengupdate nilai.', 'danger');
            }

            header('Location: ' . BASEURL . '/nilai/akhirSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
            exit;
        }

        header('Location: ' . BASEURL . '/nilai');
        exit;
    }

    /**
     * Proses hapus nilai akhir semester
     */
    public function prosesHapusAkhirSemester() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_nilai = $_POST['id_nilai'] ?? null;

            if (!$id_nilai) {
                Flasher::setFlash('Data tidak lengkap.', 'danger');
                header('Location: ' . BASEURL . '/nilai/akhirSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
                exit;
            }

            // Hapus nilai
            if ($this->nilaiModel->hapusNilai($id_nilai)) {
                Flasher::setFlash('Nilai berhasil dihapus.', 'success');
            } else {
                Flasher::setFlash('Gagal menghapus nilai.', 'danger');
            }

            header('Location: ' . BASEURL . '/nilai/akhirSemester?id_jurnal=' . $_POST['id_jurnal'] . '&id_penugasan=' . $_POST['id_penugasan']);
            exit;
        }

        header('Location: ' . BASEURL . '/nilai');
        exit;
    }
}
