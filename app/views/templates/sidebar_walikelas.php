<?php 
// File: app/views/templates/sidebar_walikelas.php
?>
<aside
  id="sidebar"
  class="sidebar fixed top-0 left-0 z-50 w-64 h-screen flex flex-col border-r border-white/20 shadow-2xl
         bg-white/95 md:bg-transparent md:glass-effect
         transform-gpu will-change-transform transition-transform duration-300 ease-in-out
         -translate-x-full md:translate-x-0 md:relative md:h-full md:flex-shrink-0
         overflow-y-auto md:overflow-visible">

  <!-- Header (sticky) -->
  <div class="sticky top-0 z-10 p-6 border-b border-white/20 flex items-center justify-between h-20 bg-white/95 md:bg-transparent">
    <div class="flex items-center">
      <div class="gradient-primary p-2 rounded-xl shadow-lg">
        <i data-lucide="user-check" class="w-6 h-6 text-white"></i>
      </div>
      <div class="ml-3">
        <h1 class="text-lg font-bold text-secondary-800 whitespace-nowrap">Smart Absensi</h1>
        <p class="text-xs text-secondary-500 font-medium">Wali Kelas</p>
      </div>
    </div>
    <button id="sidebar-toggle-btn" class="p-2 rounded-lg text-secondary-400 hover:bg-white/50 transition-colors duration-200 md:hidden" aria-label="Tutup sidebar">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
  </div>

  <!-- Session Info -->
  <div class="px-6 py-3 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-white/20">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs text-secondary-500 font-medium">Semester Aktif</p>
        <p class="text-sm font-bold text-secondary-800"><?= $_SESSION['nama_semester_aktif'] ?? ''; ?></p>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 sidebar-nav p-4">
    <ul class="space-y-2">
      <?php $judul = $data['judul'] ?? ''; ?>

      <!-- Dashboard -->
      <li>
        <a href="<?= BASEURL; ?>/waliKelas/dashboard" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Dashboard Guru') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Dashboard Guru') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="layout-dashboard" class="w-4 h-4 <?= ($judul == 'Dashboard Guru') ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Dashboard</span>
          <?= ($judul == 'Dashboard Guru') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full"></div>' : ''; ?>
        </a>
      </li>

      <!-- Section: Kelola Kelas (Menu Wali Kelas) -->
      <li class="pt-6 pb-2">
        <div class="flex items-center px-3">
          <i data-lucide="users" class="w-4 h-4 text-secondary-400 mr-2"></i>
          <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Wali Kelas</span>
        </div>
      </li>

      <!-- Daftar Siswa -->
      <li>
        <a href="<?= BASEURL; ?>/waliKelas/daftarSiswa" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Daftar Siswa') ? 'gradient-success text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Daftar Siswa') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-success-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="users-round" class="w-4 h-4 <?= ($judul == 'Daftar Siswa') ? 'text-white' : 'text-secondary-500 group-hover:text-success-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Daftar Siswa</span>
        </a>
      </li>

      <!-- Monitoring Absensi -->
      <li>
        <a href="<?= BASEURL; ?>/waliKelas/monitoringAbsensi" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Monitoring Absensi') ? 'gradient-warning text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Monitoring Absensi') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-warning-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="clipboard-check" class="w-4 h-4 <?= ($judul == 'Monitoring Absensi') ? 'text-white' : 'text-secondary-500 group-hover:text-warning-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Monitoring Absensi</span>
        </a>
      </li>

      <!-- Monitoring Nilai -->
      <?php if (defined('MENU_INPUT_NILAI_ENABLED') && MENU_INPUT_NILAI_ENABLED): ?>
      <li>
        <a href="<?= BASEURL; ?>/waliKelas/monitoringNilai" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Monitoring Nilai') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Monitoring Nilai') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="file-text" class="w-4 h-4 <?= ($judul == 'Monitoring Nilai') ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Monitoring Nilai</span>
        </a>
      </li>
      <?php endif; ?>

      <!-- Pembayaran -->
      <?php if (defined('MENU_PEMBAYARAN_ENABLED') && MENU_PEMBAYARAN_ENABLED): ?>
      <?php $isPembayaranActive = in_array($judul, ['Pembayaran Kelas', 'Kelola Tagihan Kelas', 'Riwayat Pembayaran', 'Input Pembayaran']); ?>
      <li>
        <a href="<?= BASEURL; ?>/waliKelas/pembayaran" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($isPembayaranActive) ? 'gradient-success text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($isPembayaranActive) ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-success-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="wallet" class="w-4 h-4 <?= ($isPembayaranActive) ? 'text-white' : 'text-secondary-500 group-hover:text-success-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Pembayaran</span>
        </a>
      </li>
      <?php endif; ?>

      <?php if (defined('MENU_INPUT_NILAI_ENABLED') && MENU_INPUT_NILAI_ENABLED): ?>
      <!-- Section: Cetak Rapor -->
      <li class="pt-6 pb-2">
        <div class="flex items-center px-3">
          <i data-lucide="printer" class="w-4 h-4 text-secondary-400 mr-2"></i>
          <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Cetak Rapor</span>
        </div>
      </li>

      <!-- Pengaturan Rapor -->
      <li>
        <a href="<?= BASEURL; ?>/waliKelas/pengaturanRapor" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Pengaturan Rapor') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Pengaturan Rapor') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="settings" class="w-4 h-4 <?= ($judul == 'Pengaturan Rapor') ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Pengaturan Rapor</span>
        </a>
      </li>

      <!-- Cetak Rapor -->
      <li>
        <a href="<?= BASEURL; ?>/waliKelas/cetakRapor" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Cetak Rapor') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Cetak Rapor') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="printer" class="w-4 h-4 <?= ($judul == 'Cetak Rapor') ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Cetak Rapor</span>
        </a>
      </li>
      <?php endif; ?>

      <!-- Divider -->
      <li class="pt-6">
        <div class="border-t border-white/20"></div>
      </li>

      <!-- Logout -->
      <li>
        <a href="<?= BASEURL; ?>/auth/logout" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl text-red-600 hover:bg-red-50 transition-all duration-200"
           onclick="return confirm('Yakin ingin logout?')">
          <div class="bg-red-100 group-hover:bg-red-200 p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="log-out" class="w-4 h-4 text-red-600"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Logout</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- User Info -->
  <div class="p-4 border-t border-white/20 bg-gradient-to-r from-indigo-50/50 to-purple-50/50">
    <div class="flex items-center gap-3">
      <div class="gradient-primary p-3 rounded-xl">
        <i data-lucide="user-circle" class="w-6 h-6 text-white"></i>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-secondary-800 truncate"><?= $_SESSION['nama_lengkap'] ?? 'User'; ?></p>
        <p class="text-xs text-secondary-500">Wali Kelas</p>
      </div>
    </div>
  </div>
</aside>
