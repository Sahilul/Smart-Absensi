<?php

// File: app/core/Controller.php
// Ini adalah kelas Controller dasar.

class Controller {

    /**
     * Method untuk memuat dan menampilkan file view.
     */
    public function view($view, $data = [])
    {
        // PERBAIKAN: Menggunakan APPROOT untuk path absolut yang pasti benar
        if (file_exists(APPROOT . '/app/views/' . $view . '.php')) {
            require_once APPROOT . '/app/views/' . $view . '.php';
        } else {
            die('View tidak ditemukan: ' . $view);
        }
    }

    /**
     * Method untuk memuat file model.
     */
    public function model($model)
    {
        // PERBAIKAN: Menggunakan APPROOT untuk path absolut
        if (file_exists(APPROOT . '/app/models/' . $model . '.php')) {
            require_once APPROOT . '/app/models/' . $model . '.php';
            return new $model();
        } else {
            die('Model tidak ditemukan: ' . $model);
        }
    }
}