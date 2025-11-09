<?php 
// File: app/views/templates/sidebar_kepala_madrasah.php - UPDATED WITH PERFORMA GURU MENU
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
      <div class="gradient-primary p-2 rounded-xl shadow-lg">
        <i data-lucide="crown" class="w-6 h-6 text-white"></i>
      </div>
      <div class="ml-3">
        <h1 class="text-lg font-bold text-secondary-800 whitespace-nowrap">Smart Absensi</h1>
        <p class="text-xs text-secondary-500 font-medium">Kepala Madrasah</p>
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
        <a href="<?= BASEURL; ?>/KepalaMadrasah/dashboard" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Dashboard Monitoring') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Dashboard Monitoring') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="layout-dashboard" class="w-4 h-4 <?= ($judul == 'Dashboard Monitoring') ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Dashboard</span>
          <?= ($judul == 'Dashboard Monitoring') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full"></div>' : ''; ?>
        </a>
      </li>

      <!-- Section: Performa & Analytics -->
      <li class="pt-6 pb-2">
        <div class="flex items-center px-3">
          <i data-lucide="trending-up" class="w-4 h-4 text-secondary-400 mr-2"></i>
          <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Performa & Analytics</span>
        </div>
      </li>

      <!-- Performa Siswa -->
      <li>
        <a href="<?= BASEURL; ?>/PerformaSiswa" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Performa Kehadiran Siswa') ? 'bg-gradient-to-r from-purple-500 to-pink-600 text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Performa Kehadiran Siswa') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-purple-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="user-check" class="w-4 h-4 <?= ($judul == 'Performa Kehadiran Siswa') ? 'text-white' : 'text-secondary-500 group-hover:text-purple-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Performa Siswa</span>
          <?= ($judul == 'Performa Kehadiran Siswa') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>' : ''; ?>
        </a>
      </li>

      <!-- NEW: Performa Guru -->
      <li>
        <a href="<?= BASEURL; ?>/PerformaGuru" 
           class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Performa Kinerja Guru') ? 'bg-gradient-to-r from-green-500 to-blue-600 text-white shadow-lg' : 'text-secondary-600 hover:bg-white/60 hover:text-secondary-800'; ?>">
          <div class="<?= ($judul == 'Performa Kinerja Guru') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-green-100'; ?> p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="user-check-2" class="w-4 h-4 <?= ($judul == 'Performa Kinerja Guru') ? 'text-white' : 'text-secondary-500 group-hover:text-green-600'; ?>"></i>
          </div>
          <span class="ml-3 whitespace-nowrap">Performa Guru</span>
          <?= ($judul == 'Performa Kinerja Guru') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>' : ''; ?>
          <?php if ($judul != 'Performa Kinerja Guru'): ?>
          <div class="ml-auto bg-green-100 text-green-600 px-2 py-0.5 rounded-full text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            NEW
          </div>
          <?php endif; ?>
        </a>
      </li>

      <!-- Logout -->
      <li class="pt-4">
        <a href="<?= BASEURL; ?>/auth/logout" 
           class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 text-red-600 hover:bg-red-50 hover:text-red-700 border-2 border-red-200 hover:border-red-300">
          <div class="bg-red-100 group-hover:bg-red-200 p-2 rounded-lg transition-colors duration-200">
            <i data-lucide="log-out" class="w-4 h-4 text-red-600 group-hover:text-red-700"></i>
          </div>
          <span class="ml-3 whitespace-nowrap font-semibold">Logout</span>
          <i data-lucide="arrow-right" class="w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Quick Stats for Kepala Madrasah -->
  <div class="p-4 mx-4 mb-4 bg-gradient-to-r from-primary-50 to-success-50 rounded-xl border border-white/20">
    <div class="text-center">
      <div class="gradient-primary text-white text-xl font-bold py-1 px-3 rounded-lg inline-flex items-center">
        <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
        <span>Monitor</span>
      </div>
      <p class="text-xs text-secondary-600 mt-2 font-medium">Sistem Monitoring</p>
      <p class="text-xs text-secondary-500">Real-time</p>
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

  // Highlight menu performa dengan subtle animation jika baru
  const performaSiswaMenu = document.querySelector('a[href*="PerformaSiswa"]');
  const performaGuruMenu = document.querySelector('a[href*="PerformaGuru"]');
  
  [performaSiswaMenu, performaGuruMenu].forEach(menu => {
    if (menu && !menu.classList.contains('bg-gradient-to-r')) {
      // Add subtle pulse animation to indicate new feature
      setTimeout(() => {
        menu.style.animation = 'pulse 2s infinite';
      }, 1000);
      
      // Remove animation after 10 seconds
      setTimeout(() => {
        menu.style.animation = 'none';
      }, 10000);
    }
  });

  // Inisialisasi Lucide
  if (typeof lucide !== 'undefined') lucide.createIcons();

  // Auto-hide detail riwayat jika tidak aktif
  const detailRiwayatLi = document.querySelector('li.hidden');
  if (detailRiwayatLi && !['Detail Riwayat Mapel', 'Performa Kehadiran Siswa', 'Performa Kinerja Guru'].includes('<?= $judul; ?>')) {
    setTimeout(() => {
      detailRiwayatLi.style.display = 'none';
    }, 100);
  }
});

// Enhanced menu interaction tracking
document.addEventListener('DOMContentLoaded', () => {
  // Track menu clicks for analytics
  const menuLinks = document.querySelectorAll('#sidebar a[href]');
  menuLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      const menuName = link.querySelector('span').textContent.trim();
      console.log('Menu accessed:', menuName, 'at', new Date().toLocaleString());
      
      // Optional: Send to analytics
      if (window.analytics && typeof window.analytics.track === 'function') {
        window.analytics.track('Menu Navigation', {
          menu_name: menuName,
          user_role: 'kepala_madrasah',
          timestamp: new Date().toISOString()
        });
      }
    });
  });
});
</script>

<style>
/* Additional styles untuk performa menu highlights */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .8;
  }
}

/* Enhanced hover effects untuk performa siswa menu */
a[href*="PerformaSiswa"]:not(.bg-gradient-to-r) {
  position: relative;
  overflow: hidden;
}

a[href*="PerformaSiswa"]:not(.bg-gradient-to-r):hover::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
  border-radius: 0.75rem;
  transition: opacity 0.2s ease;
  opacity: 1;
}

/* Enhanced hover effects untuk performa guru menu */
a[href*="PerformaGuru"]:not(.bg-gradient-to-r) {
  position: relative;
  overflow: hidden;
}

a[href*="PerformaGuru"]:not(.bg-gradient-to-r):hover::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%);
  border-radius: 0.75rem;
  transition: opacity 0.2s ease;
  opacity: 1;
}

/* Badge styling for NEW indicator */
.group:hover .opacity-0 {
  opacity: 1;
}

/* Smooth transitions untuk section headers */
.uppercase.tracking-wider {
  transition: all 0.2s ease;
}

li:hover .uppercase.tracking-wider {
  color: rgb(107 114 128);
  transform: translateX(2px);
}

/* Enhanced active state untuk performa siswa */
.bg-gradient-to-r.from-purple-500.to-pink-600 {
  box-shadow: 0 8px 25px -8px rgba(168, 85, 247, 0.6);
  position: relative;
}

.bg-gradient-to-r.from-purple-500.to-pink-600::after {
  content: '';
  position: absolute;
  top: 50%;
  right: -12px;
  transform: translateY(-50%);
  width: 0;
  height: 0;
  border-left: 6px solid #a855f7;
  border-top: 6px solid transparent;
  border-bottom: 6px solid transparent;
  opacity: 0.8;
}

/* Enhanced active state untuk performa guru */
.bg-gradient-to-r.from-green-500.to-blue-600 {
  box-shadow: 0 8px 25px -8px rgba(34, 197, 94, 0.6);
  position: relative;
}

.bg-gradient-to-r.from-green-500.to-blue-600::after {
  content: '';
  position: absolute;
  top: 50%;
  right: -12px;
  transform: translateY(-50%);
  width: 0;
  height: 0;
  border-left: 6px solid #22c55e;
  border-top: 6px solid transparent;
  border-bottom: 6px solid transparent;
  opacity: 0.8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .sidebar {
    backdrop-filter: blur(20px);
  }
  
  .whitespace-nowrap {
    white-space: normal;
    line-height: 1.2;
  }
}

/* Section spacing improvement */
.pt-6.pb-2 {
  margin-top: 0.5rem;
}

/* Menu item spacing optimization */
.space-y-2 > li + li {
  margin-top: 0.375rem;
}
</style>