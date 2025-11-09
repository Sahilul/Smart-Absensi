<?php

// File: app/controllers/SiswaController.php
class SiswaController extends Controller {

    private $data = [];

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->data['judul'] = 'Dashboard Siswa';
        $id_siswa = $_SESSION['id_ref'];
        $id_semester_aktif = $_SESSION['id_semester_aktif'];

        $this->data['rekap_absensi'] = $this->model('Absensi_model')->getRekapAbsensiSiswa($id_siswa, $id_semester_aktif);
        
        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_siswa', $this->data);
        $this->view('siswa/dashboard', $this->data);
        $this->view('templates/footer', $this->data);
    }

    public function absensiHarian()
    {
        $this->data['judul'] = 'Absensi Harian';
        $id_siswa = $_SESSION['id_ref'];
        $id_semester_aktif = $_SESSION['id_semester_aktif'];

        $this->data['absensi_harian'] = $this->model('Absensi_model')->getAbsensiHarianSiswa($id_siswa, $id_semester_aktif);

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_siswa', $this->data);
        $this->view('siswa/absensi_harian', $this->data);
        $this->view('templates/footer', $this->data);
    }

    // FUNGSI BARU: Untuk menampilkan halaman rekap absensi per mapel
    public function rekapAbsensi()
    {
        $this->data['judul'] = 'Rekap Absensi';
        $id_siswa = $_SESSION['id_ref'];
        $id_semester_aktif = $_SESSION['id_semester_aktif'];

        $this->data['rekap_per_mapel'] = $this->model('Absensi_model')->getRekapAbsensiSiswaPerMapel($id_siswa, $id_semester_aktif);

        $this->view('templates/header', $this->data);
        $this->view('templates/sidebar_siswa', $this->data);
        $this->view('siswa/rekap_absensi', $this->data);
        $this->view('templates/footer', $this->data);
    }
}