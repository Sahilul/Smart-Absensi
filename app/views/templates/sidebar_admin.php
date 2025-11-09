<?php  
// File: app/views/templates/sidebar_admin.php - DENGAN DATA REAL + MENU BARU (FIXED MOBILE)
// Data sidebar sudah dikirim dari controller melalui $data

// Set default values jika data sidebar tidak ada
$sidebarData = [
    'attendance_percentage' => 0,
    'total_kelas' => 0,
    'total_siswa' => 0,
    'total_guru' => 0
];

// Override dengan data dari controller jika ada
if (isset($data['sidebar_data'])) {
    $sidebarData = array_merge($sidebarData, $data['sidebar_data']);
}
?>
<aside
  id="sidebar"
  class="sidebar fixed top-0 left-0 md:relative z-[60]
         w-72 md:w-64 bg-white md:bg-transparent md:glass-effect
         flex-shrink-0 h-screen md:h-auto flex flex-col
         border-r border-white/20 shadow-2xl
         transition-transform duration-300 ease-in-out
         -translate-x-full md:translate-x-0 overflow-y-auto isolate"
  aria-expanded="false"
>
    <!-- Logo Header -->
    <div class="p-6 border-b border-white/20 flex items-center justify-between h-20 bg-white/95 md:bg-transparent backdrop-blur-sm">
        <div class="flex items-center">
            <div class="gradient-primary p-2 rounded-xl shadow-lg">
                <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
            </div>
            <div class="ml-3">
                <h1 class="text-lg font-bold text-secondary-800 whitespace-nowrap">Smart Absensi</h1>
                <p class="text-xs text-secondary-500 font-medium">Admin Panel</p>
            </div>
        </div>
        <button id="sidebar-toggle-btn" class="p-2 rounded-lg text-secondary-400 hover:bg-white/50 transition-colors duration-200 md:hidden" aria-label="Tutup sidebar">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    
    <!-- Session Info Quick Access -->
    <div class="px-6 py-3 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-white/20">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-secondary-500 font-medium">Sesi Aktif</p>
                <p class="text-sm font-bold text-secondary-800"><?= $_SESSION['nama_semester_aktif'] ?? '2024/2025 - Ganjil'; ?></p>
            </div>
            <?php if (isset($data['daftar_semester']) && !empty($data['daftar_semester'])): ?>
            <form method="POST" action="<?= BASEURL; ?>/admin/setAktifTP" class="inline">
                <select name="id_semester" onchange="this.form.submit()" class="text-xs bg-primary-100 text-primary-700 px-2 py-1 rounded-lg hover:bg-primary-200 transition-colors border-0">
                    <?php foreach ($data['daftar_semester'] as $semester): ?>
                        <option value="<?= $semester['id_semester']; ?>" 
                                <?= (isset($_SESSION['id_semester_aktif']) && $_SESSION['id_semester_aktif'] == $semester['id_semester']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($semester['nama_tp'] . ' - ' . $semester['semester']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto sidebar-nav p-4 bg-white/90 md:bg-transparent backdrop-blur-sm md:backdrop-blur-none">
        <ul class="space-y-2">
            <?php 
            $judul = $data['judul'] ?? '';
            
            // Helper function untuk cek judul
            function isActive($judul, $keyword) {
                return !empty($judul) && strpos($judul, $keyword) !== false;
            }
            ?>
            
            <!-- Dashboard dengan Statistics -->
            <li>
                <a href="<?= BASEURL; ?>/admin/dashboard" 
                   class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Dashboard Admin') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= ($judul == 'Dashboard Admin') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 <?= ($judul == 'Dashboard Admin') ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap flex-1">Dashboard</span>
                    <?php if ($judul == 'Dashboard Admin'): ?>
                        <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
                    <?php else: ?>
                        <div class="ml-auto flex space-x-1">
                            <span class="bg-green-100 text-green-600 text-xs px-1.5 py-0.5 rounded-full font-medium">
                                <?= $sidebarData['attendance_percentage']; ?>%
                            </span>
                        </div>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Section: Data Master -->
            <li class="pt-6 pb-2">
                <div class="flex items-center px-3">
                    <i data-lucide="database" class="w-4 h-4 text-secondary-400 mr-2"></i>
                    <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Data Master</span>
                    <div class="ml-auto h-px bg-secondary-200 flex-1"></div>
                </div>
            </li>
            
            <!-- Tahun Pelajaran -->
            <li>
                <a href="<?= BASEURL; ?>/admin/tahunPelajaran" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Tahun Pelajaran') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Tahun Pelajaran') ? 'bg-white/20' : 'bg-blue-100 group-hover:bg-blue-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="calendar-days" class="w-4 h-4 <?= isActive($judul, 'Tahun Pelajaran') ? 'text-white' : 'text-blue-600 group-hover:text-blue-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Tahun Pelajaran</span>
                </a>
            </li>
            
            <!-- Kelas -->
            <li>
                <a href="<?= BASEURL; ?>/admin/kelas" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Kelas') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Kelas') ? 'bg-white/20' : 'bg-purple-100 group-hover:bg-purple-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="school" class="w-4 h-4 <?= isActive($judul, 'Kelas') ? 'text-white' : 'text-purple-600 group-hover:text-purple-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Kelas</span>
                    <?php if (!isActive($judul, 'Kelas')): ?>
                        <span class="ml-auto bg-purple-100 text-purple-600 text-xs px-1.5 py-0.5 rounded-full font-medium">
                            <?= $sidebarData['total_kelas']; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Siswa -->
            <li>
                <a href="<?= BASEURL; ?>/admin/siswa" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Siswa') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Siswa') ? 'bg-white/20' : 'bg-blue-100 group-hover:bg-blue-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="users" class="w-4 h-4 <?= isActive($judul, 'Siswa') ? 'text-white' : 'text-blue-600 group-hover:text-blue-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Siswa</span>
                    <?php if (!isActive($judul, 'Siswa')): ?>
                        <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-1.5 py-0.5 rounded-full font-medium">
                            <?= $sidebarData['total_siswa']; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Guru -->
            <li>
                <a href="<?= BASEURL; ?>/admin/guru" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Guru') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Guru') ? 'bg-white/20' : 'bg-green-100 group-hover:bg-green-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="user-check" class="w-4 h-4 <?= isActive($judul, 'Guru') ? 'text-white' : 'text-green-600 group-hover:text-green-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Guru</span>
                    <?php if (!isActive($judul, 'Guru')): ?>
                        <span class="ml-auto bg-green-100 text-green-600 text-xs px-1.5 py-0.5 rounded-full font-medium">
                            <?= $sidebarData['total_guru']; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Mata Pelajaran -->
            <li>
                <a href="<?= BASEURL; ?>/admin/mapel" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Mata Pelajaran') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Mata Pelajaran') ? 'bg-white/20' : 'bg-orange-100 group-hover:bg-orange-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="book-copy" class="w-4 h-4 <?= isActive($judul, 'Mata Pelajaran') ? 'text-white' : 'text-orange-600 group-hover:text-orange-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Mata Pelajaran</span>
                </a>
            </li>
            
            <!-- Section: Pembelajaran -->
            <li class="pt-6 pb-2">
                <div class="flex items-center px-3">
                    <i data-lucide="book-open" class="w-4 h-4 text-secondary-400 mr-2"></i>
                    <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Pembelajaran</span>
                    <div class="ml-auto h-px bg-secondary-200 flex-1"></div>
                </div>
            </li>
            
            <!-- Penugasan Guru -->
            <li>
                <a href="<?= BASEURL; ?>/admin/penugasan" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Penugasan') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Penugasan') ? 'bg-white/20' : 'bg-indigo-100 group-hover:bg-indigo-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="link" class="w-4 h-4 <?= isActive($judul, 'Penugasan') ? 'text-white' : 'text-indigo-600 group-hover:text-indigo-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Penugasan Guru</span>
                </a>
            </li>
            
            <!-- Anggota Kelas -->
            <li>
                <a href="<?= BASEURL; ?>/admin/keanggotaan" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Anggota Kelas') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Anggota Kelas') ? 'bg-white/20' : 'bg-teal-100 group-hover:bg-teal-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="users-2" class="w-4 h-4 <?= isActive($judul, 'Anggota Kelas') ? 'text-white' : 'text-teal-600 group-hover:text-teal-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Anggota Kelas</span>
                </a>
            </li>

            <!-- Section: Performa -->
            <li class="pt-6 pb-2">
                <div class="flex items-center px-3">
                    <i data-lucide="trending-up" class="w-4 h-4 text-secondary-400 mr-2"></i>
                    <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Performa</span>
                    <div class="ml-auto h-px bg-secondary-200 flex-1"></div>
                </div>
            </li>

            <!-- Performa Siswa -->
            <li>
                <a href="<?= BASEURL; ?>/PerformaSiswa" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= ($judul == 'Performa Kehadiran Siswa') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= ($judul == 'Performa Kehadiran Siswa') ? 'bg-white/20' : 'bg-cyan-100 group-hover:bg-cyan-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="user-check" class="w-4 h-4 <?= ($judul == 'Performa Kehadiran Siswa') ? 'text-white' : 'text-cyan-600 group-hover:text-cyan-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Performa Siswa</span>
                </a>
            </li>
            
            <!-- Performa Guru -->
            <li>
                <a href="<?= BASEURL; ?>/PerformaGuru" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= ($judul == 'Performa Kinerja Guru') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= ($judul == 'Performa Kinerja Guru') ? 'bg-white/20' : 'bg-emerald-100 group-hover:bg-emerald-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="user-check-2" class="w-4 h-4 <?= ($judul == 'Performa Kinerja Guru') ? 'text-white' : 'text-emerald-600 group-hover:text-emerald-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Performa Guru</span>
                </a>
            </li>

            <!-- Section: Sistem -->
            <li class="pt-6 pb-2">
                <div class="flex items-center px-3">
                    <i data-lucide="settings" class="w-4 h-4 text-secondary-400 mr-2"></i>
                    <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Sistem</span>
                    <div class="ml-auto h-px bg-secondary-200 flex-1"></div>
                </div>
            </li>
            
            <!-- Naik Kelas -->
            <li>
                <a href="<?= BASEURL; ?>/admin/naikKelas" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Naik Kelas') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Naik Kelas') ? 'bg-white/20' : 'bg-success-100 group-hover:bg-success-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="trending-up" class="w-4 h-4 <?= isActive($judul, 'Naik Kelas') ? 'text-white' : 'text-success-600 group-hover:text-success-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Naik Kelas</span>
                </a>
            </li>
            
            <!-- Kelulusan -->
            <li>
                <a href="<?= BASEURL; ?>/admin/kelulusan" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Kelulusan') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Kelulusan') ? 'bg-white/20' : 'bg-warning-100 group-hover:bg-warning-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="graduation-cap" class="w-4 h-4 <?= isActive($judul, 'Kelulusan') ? 'text-white' : 'text-warning-600 group-hover:text-warning-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Kelulusan Siswa</span>
                </a>
            </li>
            
            <!-- Config QR Code -->
            <li>
                <a href="<?= BASEURL; ?>/admin/configQR" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Konfigurasi QR Code') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Konfigurasi QR Code') ? 'bg-white/20' : 'bg-purple-100 group-hover:bg-purple-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="qr-code" class="w-4 h-4 <?= isActive($judul, 'Konfigurasi QR Code') ? 'text-white' : 'text-purple-600 group-hover:text-purple-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Config QR Code</span>
                </a>
            </li>
            
            <!-- Pengaturan Menu -->
            <li>
                <a href="<?= BASEURL; ?>/admin/pengaturanMenu" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= isActive($judul, 'Pengaturan Menu') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= isActive($judul, 'Pengaturan Menu') ? 'bg-white/20' : 'bg-blue-100 group-hover:bg-blue-200'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="eye" class="w-4 h-4 <?= isActive($judul, 'Pengaturan Menu') ? 'text-white' : 'text-blue-600 group-hover:text-blue-700'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Pengaturan Menu</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Footer dengan Info & Logout -->
    <div class="p-4 border-t border-white/20 mt-auto space-y-2 bg-white/90 md:bg-transparent backdrop-blur-sm md:backdrop-blur-none">
        <!-- System Status -->
        <div class="px-3 py-2 bg-green-50 rounded-lg">
            <div class="flex items-center text-xs">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                <span class="text-green-700 font-medium">Sistem Normal</span>
                <span class="ml-auto text-green-600">v2.1</span>
            </div>
        </div>
        
        <!-- Logout -->
        <a href="<?= BASEURL; ?>/auth/logout" 
           class="group flex items-center p-3 text-sm font-medium text-danger-600 hover:bg-danger-50 rounded-xl transition-all duration-200 w-full">
            <div class="bg-danger-100 group-hover:bg-danger-200 p-2 rounded-lg transition-colors duration-200">
                <i data-lucide="log-out" class="w-4 h-4 text-danger-600"></i>
            </div>
            <span class="ml-3 whitespace-nowrap font-semibold">Logout</span>
            <i data-lucide="arrow-right" class="w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
        </a>
    </div>
</aside>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 md:hidden hidden"></div>

<style>
  /* Mobile */
  @media (max-width: 767px) {
    .sidebar {
      background: linear-gradient(135deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.98) 100%);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }
    .sidebar::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.9);
      z-index: 0;            /* layer blur di bawah konten */
      pointer-events: none;  /* tidak menghalangi interaksi */
    }
    .sidebar > * {
      position: relative;
      z-index: 1;            /* konten di atas layer blur */
    }
  }

  /* Smooth scroll untuk navigation */
  .sidebar-nav { scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.3) transparent; }
  .sidebar-nav::-webkit-scrollbar { width: 4px; }
  .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
  .sidebar-nav::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.3); border-radius: 2px; }
  .sidebar-nav::-webkit-scrollbar-thumb:hover { background-color: rgba(156, 163, 175, 0.5); }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    const menuButton = document.getElementById('menu-button'); // tombol hamburger di header (pastikan ada)
    const overlay = document.getElementById('sidebar-overlay');

    const hideSidebar = () => {
      sidebar.classList.add('-translate-x-full');
      sidebar.classList.remove('translate-x-0');
      overlay.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      sidebar.setAttribute('aria-expanded', 'false');
    };

    const showSidebar = () => {
      sidebar.classList.remove('-translate-x-full');
      sidebar.classList.add('translate-x-0');
      overlay.classList.remove('hidden');
      if (window.innerWidth < 768) document.body.classList.add('overflow-hidden');
      sidebar.setAttribute('aria-expanded', 'true');
    };

    // Toggle dari tombol hamburger di header
    if (menuButton) {
      menuButton.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        showSidebar();
      });
    }

    // Toggle dari tombol X di sidebar
    if (sidebarToggleBtn) {
      sidebarToggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        hideSidebar();
      });
    }

    // Tutup sidebar saat overlay diklik
    if (overlay) overlay.addEventListener('click', hideSidebar);

    // Tutup dengan ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && window.innerWidth < 768 && !overlay.classList.contains('hidden')) {
        hideSidebar();
      }
    });

    // Tutup otomatis saat link diklik (mobile)
    const sidebarLinks = sidebar.querySelectorAll('a[href]');
    sidebarLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 768) setTimeout(hideSidebar, 100);
      });
    });

    // Handle resize window
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768) {
        // Desktop mode - restore body scroll
        document.body.classList.remove('overflow-hidden');
        overlay.classList.add('hidden');
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.remove('translate-x-0'); // biarkan md:translate-x-0 yang berlaku
      } else {
        // Mobile mode - hide sidebar by default jika overlay terbuka
        if (!overlay.classList.contains('hidden')) hideSidebar();
      }
    });

    // Prevent clicks inside sidebar from bubbling to overlay
    sidebar.addEventListener('click', (e) => e.stopPropagation());

    // Real-time update data sidebar setiap 60 detik
    const updateSidebarData = async () => {
      try {
        const response = await fetch('<?= BASEURL; ?>/admin/getStats');
        if (response.ok) {
          const data = await response.json();
          // TODO: Update elemen badge jika diperlukan
          console.log('Sidebar data updated:', data);
        }
      } catch (error) {
        console.error('Error updating sidebar data:', error);
      }
    };
    setInterval(updateSidebarData, 60000);

    // Touch events untuk mobile (swipe)
    let touchStartX = null;
    document.addEventListener('touchstart', (e) => {
      touchStartX = e.touches[0].clientX;
    }, { passive: true });

    document.addEventListener('touchmove', (e) => {
      if (touchStartX === null) return;
      const touchX = e.touches[0].clientX;
      const diffX = touchX - touchStartX;

      // Swipe dari kiri untuk buka sidebar (hanya di mobile)
      if (window.innerWidth < 768 && touchStartX < 20 && diffX > 50) {
        showSidebar();
        touchStartX = null;
      }
      // Swipe ke kiri untuk tutup sidebar (ketika sidebar terbuka)
      if (window.innerWidth < 768 && !overlay.classList.contains('hidden') && diffX < -50) {
        hideSidebar();
        touchStartX = null;
      }
    }, { passive: true });

    document.addEventListener('touchend', () => { touchStartX = null; }, { passive: true });

    // Inisialisasi Lucide Icons
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  });
</script>

