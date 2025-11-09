<?php 
// File: app/views/templates/sidebar_guru.php (Bantuan dihapus, ganti Logout di menu)
?>
<aside
  id="sidebar"
  class="sidebar fixed top-0 left-0 z-40 w-64 h-screen flex flex-col border-r border-white/20 shadow-2xl
         bg-white/95 md:bg-transparent md:glass-effect
         transform-gpu will-change-transform transition-transform duration-300 ease-in-out
         -translate-x-full md:translate-x-0 md:relative md:h-full md:flex-shrink-0
         overflow-y-auto md:overflow-visible">

  <!-- Header (sticky) -->
  <div class="sticky top-0 z-10 p-6 border-b border-white/20 flex items-center justify-between h-20 bg-white/95 md:bg-transparent">
    <div class="flex items-center">
      <div class="gradient-success p-2 rounded-xl shadow-lg">
        <i data-lucide="book-open-check" class="w-6 h-6 text-white"></i>
      </div>
      <div class="ml-3">
        <h1 class="text-lg font-bold text-secondary-800 whitespace-nowrap">Smart Absensi</h1>
        <p class="text-xs text-secondary-500 font-medium">Madrasah Sabilillah</p>
      </div>
    </div>
    <button id="sidebar-toggle-btn" class="p-2 rounded-lg text-secondary-400 hover:bg-white/50 transition-colors duration-200 md:hidden" aria-label="Tutup sidebar">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 sidebar-nav p-4">
    <ul class="space-y-2">
      <?php $judul = $data['judul'] ?? ''; ?>

      <!-- Dashboard -->
      <li>
        <a href="<?= BASEURL; ?>/guru/dashboard" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Dashboard Guru') ? 'gradient-success text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Dashboard Guru') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-success-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="layout-dashboard" class="w-4 h-4 <?= ($judul == 'Dashboard Guru') ? 'text-white' : 'text-secondary-500 group-hover:text-success-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Dashboard</span>
          <?= ($judul == 'Dashboard Guru') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full"></div>' : ''; ?>
        </a>
      </li>

      <!-- Section: Aktivitas Mengajar 
      <li class="pt-6 pb-2">
        <div class="flex items-center px-3">
          <i data-lucide="clipboard-list" class="w-4 h-4 text-secondary-400 mr-2"></i>
          <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Aktivitas Mengajar</span>
        </div>
      </li>

       Input Jurnal & Absensi 
      <li>
        <a href="<?= BASEURL; ?>/guru/jurnal" 
           class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= (in_array($judul, ['Input Jurnal', 'Tambah Jurnal Mengajar', 'Input Absensi', 'Edit Jurnal'])) ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= (in_array($judul, ['Input Jurnal', 'Tambah Jurnal Mengajar', 'Input Absensi', 'Edit Jurnal'])) ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="clipboard-pen" class="w-4 h-4 <?= (in_array($judul, ['Input Jurnal', 'Tambah Jurnal Mengajar', 'Input Absensi', 'Edit Jurnal'])) ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Jurnal & Absensi</span>
          <?= (in_array($judul, ['Input Jurnal', 'Tambah Jurnal Mengajar', 'Input Absensi', 'Edit Jurnal'])) ? '<div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>' : ''; ?>
        </a>
      </li>

      <!-- Section: Tools -->
      <li class="pt-6 pb-2">
        <div class="flex items-center px-3">
          <i data-lucide="wrench" class="w-4 h-4 text-secondary-400 mr-2"></i>
          <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Tools</span>
        </div>
      </li>

      <!-- Cetak Laporan 
      <li>
        <a href="#" onclick="window.print()" 
           class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 text-secondary-600 hover:bg-white/60 hover:text-secondary-800">
          <div class="bg-secondary-100 group-hover:bg-blue-100 p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="printer" class="w-4 h-4 text-secondary-500 group-hover:text-blue-600"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Cetak Laporan</span>
          <i data-lucide="external-link" class="w-3 h-3 ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-secondary-400"></i>
        </a>
      </li>

      <!-- Export Data 
      <li>
        <a href="#" 
           class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 text-secondary-600 hover:bg-white/60 hover:text-secondary-800">
          <div class="bg-secondary-100 group-hover:bg-green-100 p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="download" class="w-4 h-4 text-secondary-500 group-hover:text-green-600"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Export Data</span>
        </a>
      </li>

      <!-- Section: Personal 
      <li class="pt-6 pb-2">
        <div class="flex items-center px-3">
          <i data-lucide="user" class="w-4 h-4 text-secondary-400 mr-2"></i>
          <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Personal</span>
        </div>
      </li>

      <!-- Profil 
      <li>
        <a href="#" 
           class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 text-secondary-600 hover:bg-white/60 hover:text-secondary-800">
          <div class="bg-secondary-100 group-hover:bg-purple-100 p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="user-circle" class="w-4 h-4 text-secondary-500 group-hover:text-purple-600"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Profil Saya</span>
        </a>
      </li>-->

      <!-- Logout (menggantikan Bantuan) -->
      <li>
        <a href="<?= BASEURL; ?>/auth/logout" 
           class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 text-red-600 hover:bg-red-50 hover:text-red-700">
          <div class="bg-red-100 group-hover:bg-red-200 p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="log-out" class="w-4 h-4 text-red-600 group-hover:text-red-700"></i>
          </div>
          <span class="ml-3 whitespace-nowrap font-semibold">Logout</span>
          <i data-lucide="arrow-right" class="w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Quick Stats (tetap) -->
  <div class="p-4 mx-4 mb-4 bg-gradient-to-r from-success-50 to-primary-50 rounded-xl border border-white/20">
    <div class="text-center">
      <div class="gradient-success text-white text-xl font-bold py-1 px-3 rounded-lg inline-flex items-center">
        <i data-lucide="calendar-check" class="w-4 h-4 mr-2"></i>
        <span id="today-date"><?= date('d'); ?></span>
      </div>
      <p class="text-xs text-secondary-600 mt-2 font-medium"><?= date('M Y'); ?></p>
      <p class="text-xs text-secondary-500">Hari ini</p>
    </div>
  </div>
</aside>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-30 md:hidden hidden"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  const closeBtn = document.getElementById('sidebar-toggle-btn');
  const openBtn = document.getElementById('menu-button') || document.querySelector('[data-sidebar-open]');

  // iOS smooth scrolling for the aside
  sidebar.style.webkitOverflowScrolling = 'touch';

  const lockScroll = (lock) => {
    if (lock) {
      document.documentElement.classList.add('overflow-hidden', 'touch-none');
      document.body.classList.add('overflow-hidden', 'touch-none');
    } else {
      document.documentElement.classList.remove('overflow-hidden', 'touch-none');
      document.body.classList.remove('overflow-hidden', 'touch-none');
    }
  };

  const showSidebar = () => {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    lockScroll(true);
  };

  const hideSidebar = () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    lockScroll(false);
  };

  if (openBtn) openBtn.addEventListener('click', showSidebar);
  if (closeBtn) closeBtn.addEventListener('click', hideSidebar);
  if (overlay) overlay.addEventListener('click', hideSidebar);

  // Tutup otomatis saat klik link (mobile)
  sidebar.querySelectorAll('a[href]').forEach(a => {
    a.addEventListener('click', () => {
      if (window.innerWidth < 768) hideSidebar();
    });
  });

  // Inisialisasi Lucide
  if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
