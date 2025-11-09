<?php 
// File: app/views/templates/sidebar_siswa.php - Final Clean Version
?>
<aside id="sidebar" class="sidebar fixed md:relative z-40 w-64 bg-white md:glass-effect flex-shrink-0 h-full flex flex-col border-r border-white/20 shadow-2xl transition-all duration-300 ease-in-out -translate-x-full md:translate-x-0">
    
    <!-- Logo Header -->
    <div class="p-6 border-b border-secondary-200 md:border-white/20 bg-white md:bg-transparent flex items-center justify-between h-20">
        <div class="flex items-center">
            <div class="gradient-warning p-2 rounded-xl shadow-lg">
                <i data-lucide="user-check-2" class="w-6 h-6 text-white"></i>
            </div>
            <div class="ml-3">
                <h1 class="text-lg font-bold text-secondary-800 whitespace-nowrap">Smart Absensi</h1>
                <p class="text-xs text-secondary-500 font-medium">Panel Siswa</p>
            </div>
        </div>
        <button id="sidebar-toggle-btn" class="p-2 rounded-lg text-secondary-400 hover:bg-white/50 transition-colors duration-200 md:hidden">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto sidebar-nav p-4">
        <ul class="space-y-2">
            <?php $judul = $data['judul']; ?>
            
            <!-- Dashboard -->
            <li>
                <a href="<?= BASEURL; ?>/siswa/dashboard" 
                   class="group flex items-center p-3 text-sm font-semibold rounded-xl transition-all duration-200 <?= ($judul == 'Dashboard Siswa') ? 'gradient-warning text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= ($judul == 'Dashboard Siswa') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-warning-100'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 <?= ($judul == 'Dashboard Siswa') ? 'text-white' : 'text-secondary-500 group-hover:text-warning-600'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Dashboard</span>
                    <?= ($judul == 'Dashboard Siswa') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full"></div>' : ''; ?>
                </a>
            </li>
            
            <!-- Section: Absensi Saya -->
            <li class="pt-6 pb-2">
                <div class="flex items-center px-3">
                    <i data-lucide="calendar-check" class="w-4 h-4 text-secondary-400 mr-2"></i>
                    <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Absensi Saya</span>
                </div>
            </li>
            
            <!-- Absensi Harian -->
            <li>
                <a href="<?= BASEURL; ?>/siswa/absensiHarian" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= ($judul == 'Absensi Harian') ? 'gradient-primary text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= ($judul == 'Absensi Harian') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-primary-100'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="calendar-days" class="w-4 h-4 <?= ($judul == 'Absensi Harian') ? 'text-white' : 'text-secondary-500 group-hover:text-primary-600'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Absensi Harian</span>
                    <?= ($judul == 'Absensi Harian') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>' : ''; ?>
                </a>
            </li>

            <!-- Rekap Absensi -->
            <li>
                <a href="<?= BASEURL; ?>/siswa/rekapAbsensi" 
                   class="group flex items-center p-3 text-sm font-medium rounded-xl transition-all duration-200 <?= ($judul == 'Rekap Absensi') ? 'gradient-success text-white shadow-lg' : 'text-secondary-600 hover:bg-white/50 hover:text-secondary-800'; ?>">
                    <div class="<?= ($judul == 'Rekap Absensi') ? 'bg-white/20' : 'bg-secondary-100 group-hover:bg-success-100'; ?> p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="pie-chart" class="w-4 h-4 <?= ($judul == 'Rekap Absensi') ? 'text-white' : 'text-secondary-500 group-hover:text-success-600'; ?>"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap">Rekap Absensi</span>
                    <?= ($judul == 'Rekap Absensi') ? '<div class="ml-auto w-2 h-2 bg-white rounded-full"></div>' : ''; ?>
                </a>
            </li>
            
            <!-- Logout - Moved Up -->
            <li class="pt-6">
                <a href="<?= BASEURL; ?>/auth/logout" 
                   class="group flex items-center p-3 text-sm font-medium text-danger-600 hover:bg-danger-50 rounded-xl transition-all duration-200 w-full">
                    <div class="bg-danger-100 group-hover:bg-danger-200 p-2 rounded-lg transition-colors duration-200">
                        <i data-lucide="log-out" class="w-4 h-4 text-danger-600"></i>
                    </div>
                    <span class="ml-3 whitespace-nowrap font-semibold">Logout</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Footer dengan info session -->
    <div class="p-4 mx-4 mb-4">
        <div class="bg-gradient-to-r from-warning-50 to-primary-50 rounded-xl border border-secondary-200 md:border-white/20 p-4">
            <div class="text-center">
                <div class="gradient-warning text-white text-xl font-bold py-1 px-3 rounded-lg inline-flex items-center mb-2">
                    <i data-lucide="calendar-check" class="w-4 h-4 mr-2"></i>
                    <span><?= date('d'); ?></span>
                </div>
                <p class="text-xs text-secondary-600 font-medium"><?= date('M Y'); ?></p>
                <p class="text-xs text-secondary-500">Hari ini</p>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 md:bg-black/20 md:backdrop-blur-sm z-30 md:hidden hidden"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
        const menuButton = document.getElementById('menu-button');
        const overlay = document.getElementById('sidebar-overlay');

        console.log('Sidebar elements:', {
            sidebar: !!sidebar,
            sidebarToggleBtn: !!sidebarToggleBtn,
            menuButton: !!menuButton,
            overlay: !!overlay
        });

        if (!sidebar) {
            console.error('Sidebar element not found!');
            return;
        }

        if (!overlay) {
            console.error('Overlay element not found!');
            return;
        }

        const hideSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden'); // Allow scroll
            console.log('Hiding sidebar');
        };

        const showSidebar = () => {
            console.log('Attempting to show sidebar...');
            console.log('Sidebar classes before:', sidebar.className);
            
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // Prevent background scroll
            
            console.log('Sidebar classes after:', sidebar.className);
            console.log('Overlay classes:', overlay.className);
            console.log('Sidebar showing');
            
            // Force a reflow to ensure changes take effect
            sidebar.offsetHeight;
        };

        // Toggle dari tombol hamburger di header
        if (menuButton) {
            menuButton.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Menu button clicked');
                showSidebar();
            });
        } else {
            console.warn('Menu button not found, trying alternative selectors');
            // Try alternative selectors
            const altMenuButton = document.querySelector('[data-sidebar-toggle]') || 
                                 document.querySelector('.menu-toggle') ||
                                 document.querySelector('#menu-btn');
            if (altMenuButton) {
                console.log('Found alternative menu button');
                altMenuButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    showSidebar();
                });
            }
        }

        // Double-click on overlay to close (sebagai backup)
        if (overlay) {
            overlay.addEventListener('dblclick', hideSidebar);
        }

        // Toggle dari tombol X di sidebar
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', hideSidebar);
        }

        // Tutup sidebar saat overlay diklik
        if (overlay) {
            overlay.addEventListener('click', hideSidebar);
        }

        // Tutup sidebar otomatis saat link diklik (mobile)
        const sidebarLinks = sidebar.querySelectorAll('a[href]');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    hideSidebar();
                }
            });
        });

        // Inisialisasi Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Global functions untuk debugging
        window.debugSidebar = {
            show: showSidebar,
            hide: hideSidebar,
            toggle: () => {
                if (sidebar.classList.contains('-translate-x-full')) {
                    showSidebar();
                } else {
                    hideSidebar();
                }
            },
            checkElements: () => {
                console.log('Sidebar check:', {
                    sidebar: !!sidebar,
                    overlay: !!overlay,
                    menuButton: !!menuButton,
                    sidebarClasses: sidebar?.className,
                    overlayClasses: overlay?.className
                });
            }
        };

        console.log('Sidebar initialized. Use window.debugSidebar for testing.');
    });
</script>

<!-- Mobile-specific CSS -->
<style>
    /* Fallback untuk browser yang tidak support backdrop-filter */
    @supports not (backdrop-filter: blur(10px)) {
        .glass-effect {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: none !important;
        }
    }
    
    /* Mobile-specific fixes */
    @media (max-width: 768px) {
        #sidebar {
            background: white !important;
            backdrop-filter: none !important;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1) !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            width: 16rem !important; /* 256px = w-64 */
            z-index: 50 !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s ease-in-out !important;
        }
        
        /* When sidebar is shown */
        #sidebar:not(.-translate-x-full) {
            transform: translateX(0) !important;
        }
        
        /* Ensure solid background untuk mobile */
        #sidebar .glass-effect {
            background: white !important;
            backdrop-filter: none !important;
        }
        
        /* Better mobile overlay */
        #sidebar-overlay {
            background: rgba(0, 0, 0, 0.5) !important;
            backdrop-filter: none !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 40 !important;
        }
        
        /* Ensure overlay is properly hidden when not needed */
        #sidebar-overlay.hidden {
            display: none !important;
        }
    }
</style>