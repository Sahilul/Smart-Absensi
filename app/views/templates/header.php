<?php
// File: app/views/templates/header.php - COMPLETE FIXED VERSION
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul'] ?? 'Aplikasi Absensi'; ?></title>
    
    <!-- Preconnect untuk faster loading -->
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    
    <!-- Tailwind CSS v3 - GUNAKAN UNTUK SEMUA ENVIRONMENT -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Preload -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"></noscript>
    
    <!-- Chart.js - Load only when needed -->
    <?php if (isset($data['load_chartjs']) && $data['load_chartjs']): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <?php endif; ?>
    
    <!-- Lucide Icons - Single load -->
    <script src="https://unpkg.com/lucide@latest" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'jakarta': ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        warning: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-gentle': 'bounceGentle 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        bounceGentle: {
                            '0%, 20%, 53%, 80%, 100%': { transform: 'translateY(0)' },
                            '40%, 43%': { transform: 'translateY(-8px)' },
                            '70%': { transform: 'translateY(-4px)' },
                            '90%': { transform: 'translateY(-2px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        .sidebar { 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
        }
        
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { 
            background: linear-gradient(45deg, #0ea5e9, #0284c7); 
            border-radius: 2px; 
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }
        
        .gradient-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }
        
        .gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px 0 rgba(14, 165, 233, 0.25);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(14, 165, 233, 0.35);
        }
        
        .btn-secondary {
            background: rgba(148, 163, 184, 0.1);
            color: #475569;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(148, 163, 184, 0.2);
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.15);
            transform: translateY(-1px);
        }
        
        .input-modern {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }
        
        .input-modern:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
            background: rgba(255, 255, 255, 1);
        }
        
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        
        .status-excellent { 
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
        }
        .status-good { 
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
        }
        .status-fair { 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        .status-poor { 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .progress-fill::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        .notification-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 50%;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
            z-index: 50;
            min-width: 200px;
            max-height: 70vh;
            overflow-y: auto;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dropdown-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #475569;
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 0.5rem;
            margin: 0.25rem;
        }
        
        .dropdown-item:hover {
            background: rgba(14, 165, 233, 0.1);
            color: #0ea5e9;
        }
        
        .dropdown-item i {
            width: 16px;
            height: 16px;
            margin-right: 0.75rem;
        }

        /* Mobile tweaks */
        @media (max-width: 640px) {
            .dropdown-menu {
                right: 0.5rem;
                left: auto;
                width: calc(100vw - 1rem);
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-secondary-50 to-secondary-100 text-secondary-900 font-jakarta">
    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="mobile-menu-overlay md:hidden"></div>
    
    <div class="flex h-screen">
        <?php
        // Memanggil sidebar yang sesuai dengan peran pengguna
        if(isset($_SESSION['user_role'])) {
            switch($_SESSION['user_role']) {
                case 'admin':
                    $this->view('templates/sidebar_admin');
                    break;
                case 'guru':
                    $this->view('templates/sidebar_guru');
                    break;
                case 'siswa':
                    $this->view('templates/sidebar_siswa');
                    break;
                case 'kepala_madrasah':
                    $this->view('templates/sidebar_kepala_madrasah');
                    break;
                case 'wali_kelas':
                    $this->view('templates/sidebar_walikelas');
                    break;
                default:
                    // Default fallback
                    echo '<div class="w-64 bg-red-500 text-white p-4">Role tidak dikenal</div>';
                    break;
            }
        } else {
            // Jika tidak ada session, redirect ke login
            echo '<script>window.location.href = "' . BASEURL . '/auth/login";</script>';
        }
        ?>
        
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">
            <!-- Header Utama dengan Glass Effect -->
            <header class="glass-effect p-4 flex justify-between items-center h-16 border-b border-white/20 shadow-lg relative z-30">
                <button id="menu-button" class="md:hidden p-2 rounded-xl text-secondary-600 hover:bg-white/50 transition-all duration-200">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                
                <div class="hidden md:block">
                    <!-- Breadcrumb atau title bisa ditambahkan di sini -->
                    <h1 class="text-lg font-semibold text-secondary-700">
                        <?= $data['judul'] ?? 'Dashboard'; ?>
                    </h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Session Badge dengan Gradient -->
                    <div class="gradient-primary text-white text-sm font-semibold py-2.5 px-4 rounded-xl shadow-lg">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                        <?= $_SESSION['nama_semester_aktif'] ?? 'Sesi Tidak Diketahui'; ?>
                    </div>
                    
                    <!-- Notifications (Optional) -->
                    <button class="relative p-2 rounded-xl text-secondary-600 hover:bg-white/50 transition-all duration-200 hidden md:block">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <div class="notification-dot"></div>
                    </button>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button id="profile-button" class="flex items-center space-x-3 bg-white/80 hover:bg-white/90 p-2.5 pr-4 rounded-xl transition-all duration-200 shadow-md" aria-haspopup="true" aria-expanded="false">
                            <div class="relative">
                                <?php 
                                // FIX: Perbaikan untuk urlencode error dengan validasi lengkap
                                $user_name = $_SESSION['user_nama_lengkap'] ?? null;
                                
                                // Validasi dan sanitasi nama user
                                if (empty($user_name) || is_null($user_name)) {
                                    $safe_user_name = 'User';
                                } else {
                                    // Pastikan string valid dan bersihkan karakter yang tidak diinginkan
                                    $safe_user_name = trim($user_name);
                                    if (empty($safe_user_name)) {
                                        $safe_user_name = 'User';
                                    }
                                }
                                
                                $encoded_name = urlencode($safe_user_name);
                                $html_safe_name = htmlspecialchars($safe_user_name, ENT_QUOTES, 'UTF-8');
                                ?>
                                <img class="h-9 w-9 rounded-lg object-cover border-2 border-white" 
                                     src="https://ui-avatars.com/api/?name=<?= $encoded_name; ?>&background=0ea5e9&color=fff&size=128" 
                                     alt="<?= $html_safe_name; ?> Profile">
                                <div class="notification-dot"></div>
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-sm font-semibold text-secondary-700"><?= $html_safe_name; ?></div>
                                <div class="text-xs text-secondary-500 capitalize font-medium">
                                    <?php
                                    $role_display = $_SESSION['user_role'] ?? 'Role';
                                    // Convert underscore to space and capitalize properly
                                    $role_display = str_replace('_', ' ', $role_display);
                                    $role_display = ucwords($role_display);
                                    echo htmlspecialchars($role_display, ENT_QUOTES, 'UTF-8');
                                    ?>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-secondary-400 hidden md:block transform transition-transform duration-200" id="chevron-icon"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="profile-dropdown" class="dropdown-menu">
                            <div class="py-2">
                                <?php $role_current = $_SESSION['role'] ?? ''; ?>
                                <a href="<?= in_array($role_current, ['guru','wali_kelas']) ? BASEURL . '/guru/profil' : '#' ?>" class="dropdown-item">
                                    <i data-lucide="id-card"></i>
                                    <span>Profil</span>
                                </a>
                                <a href="<?= in_array($role_current, ['guru','wali_kelas']) ? BASEURL . '/guru/gantiSandi' : '#' ?>" class="dropdown-item">
                                    <i data-lucide="lock"></i>
                                    <span>Ganti Sandi</span>
                                </a>
                                <div class="border-t border-secondary-200 my-2 mx-4"></div>
                                <a href="<?= BASEURL; ?>/auth/logout" class="dropdown-item text-danger-600 hover:bg-danger-50 hover:text-danger-700">
                                    <i data-lucide="log-out"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
                <!-- Flash Messages -->
                <?php
                if (class_exists('Flasher')) {
                    Flasher::flash();
                }
                ?>
                
                <!-- Content will be loaded here -->
                <div class="animate-fade-in">
                    <!-- Page content starts here -->