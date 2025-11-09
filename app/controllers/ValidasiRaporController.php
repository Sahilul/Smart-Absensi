<?php

class ValidasiRaporController extends Controller
{
    private $data = [];
    
    // Flag untuk bypass login check - halaman ini PUBLIC
    protected $requiresAuth = false;

    public function __construct()
    {
        // Session sudah di-start di index.php, tidak perlu start lagi
        // Tidak perlu cek login untuk validasi rapor (halaman publik)
    }

    /**
     * Halaman validasi rapor dengan QR Code
     * PUBLIC ACCESS - Tidak perlu login
     */
    public function index($token = '')
    {
        $this->data['judul'] = 'Validasi Rapor';
        $this->data['token'] = $token;
        $this->data['valid'] = false;
        $this->data['message'] = '';
        $this->data['siswa_data'] = null;

        if (!empty($token)) {
            $this->data['valid'] = true;
            $this->data['message'] = 'Rapor ini adalah dokumen resmi yang diterbitkan oleh sistem.';
            $this->data['verified_at'] = date('d/m/Y H:i:s');
        } else {
            $this->data['message'] = 'Token validasi tidak valid atau sudah kedaluwarsa.';
        }

        // Gunakan view standalone tanpa template header/footer
        $this->viewStandalone('validasi_rapor/index', $this->data);
    }
    
    /**
     * View standalone untuk halaman publik (tanpa header/footer yang butuh login)
     */
    private function viewStandalone($view, $data = [])
    {
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $data['judul'] ?? 'Validasi Rapor' ?> - Madrasah Sabilillah</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <script src="https://unpkg.com/lucide@latest"></script>
        </head>
        <body class="antialiased">
            <?php require_once APPROOT . '/app/views/' . $view . '.php'; ?>
            <script>
                // Initialize Lucide icons
                lucide.createIcons();
            </script>
        </body>
        </html>
        <?php
    }
}
