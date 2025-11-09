<?php

// File: app/core/Flasher.php
class Flasher {

    public static function setFlash($pesan, $tipe)
    {
        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'tipe'  => $tipe
        ];
    }

    public static function flash()
    {
        if (isset($_SESSION['flash'])) {
            // PERBAIKAN DI SINI:
            // Teks judul (Gagal!/Berhasil!) dan warna sekarang dinamis
            if ($_SESSION['flash']['tipe'] == 'success') {
                $tipeAlert = 'green';
                $judul = 'Berhasil!';
            } else {
                $tipeAlert = 'red';
                $judul = 'Gagal!';
            }
            
            echo '<div class="bg-' . $tipeAlert . '-100 border border-' . $tipeAlert . '-400 text-' . $tipeAlert . '-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                    <strong class="font-bold">' . $judul . '</strong>
                    <span class="block sm:inline">' . $_SESSION['flash']['pesan'] . '</span>
                  </div>';
            
            // Hapus session setelah ditampilkan
            unset($_SESSION['flash']);
        }
    }
}