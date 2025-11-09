<?php
// File: app/views/kepala_madrasah/dashboard.php - PRODUCTION FIXES
?>

<div class="min-h-screen p-3 md:p-6 space-y-4 md:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-2 rounded-xl">
                    <i data-lucide="crown" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                </div>
                Dashboard Monitoring
            </h1>
            <p class="text-gray-600 mt-2 font-medium text-sm md:text-base">
                Monitoring sistem absensi secara real-time
            </p>
            <div class="mt-2">
                <span class="text-xs md:text-sm text-blue-600 font-semibold bg-blue-50 px-3 py-1 rounded-full">
                    <?= $data['session_info']['nama_semester'] ?? 'Semester Tidak Diketahui'; ?>
                </span>
            </div>
        </div>
        
        <!-- Filter Period -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-lg border border-white/20">
            <i data-lucide="filter" class="w-5 h-5 text-gray-500"></i>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                <select id="filter-period" class="border border-gray-300 rounded-lg px-3 py-2 bg-transparent font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 text-sm w-full sm:w-auto" onchange="handlePeriodChange()">
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="this_semester">Semester Ini</option>
                    <option value="custom">Custom Date</option>
                </select>
                
                <!-- Custom Date Inputs (Hidden by default) -->
                <div id="custom-date-inputs" class="hidden flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                    <input type="date" id="start-date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-auto" value="<?= date('Y-m-d'); ?>">
                    <span class="text-gray-500 hidden sm:inline">s/d</span>
                    <input type="date" id="end-date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-auto" value="<?= date('Y-m-d'); ?>">
                </div>
                
                <button onclick="applyFilter()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm w-full sm:w-auto transition-colors">
                    <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                    Terapkan
                </button>
            </div>
        </div>
    </div>

    <!-- Period Info Banner -->
    <div class="bg-gradient-to-r from-blue-50 to-green-50 border border-blue-200 rounded-xl p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <i data-lucide="calendar-days" class="w-5 h-5 text-blue-600"></i>
                <div>
                    <p class="font-semibold text-blue-800" id="period-title">Data Hari Ini</p>
                    <p class="text-sm text-blue-600" id="period-date"><?= date('d F Y'); ?></p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-sm text-blue-600">Total Pertemuan</p>
                <p class="text-2xl font-bold text-blue-800" id="total-pertemuan">0</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 md:gap-6">
        <!-- Total Guru -->
        <div class="bg-white/80 backdrop-blur-sm hover:bg-white/90 transition-all duration-300 hover:shadow-lg rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <p class="text-gray-500 text-xs md:text-sm font-medium mb-1">Total Guru</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800">
                        <?= $data['total_guru'] ?? 0; ?>
                    </p>
                    <div class="flex items-center mt-2">
                        <div class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">MENGAJAR</div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-2 md:p-3 rounded-xl">
                    <i data-lucide="users" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white/80 backdrop-blur-sm hover:bg-white/90 transition-all duration-300 hover:shadow-lg rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <p class="text-gray-500 text-xs md:text-sm font-medium mb-1">Total Siswa</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800">
                        <?= $data['total_siswa'] ?? 0; ?>
                    </p>
                    <div class="flex items-center mt-2">
                        <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">AKTIF</div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-2 md:p-3 rounded-xl">
                    <i data-lucide="graduation-cap" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="bg-white/80 backdrop-blur-sm hover:bg-white/90 transition-all duration-300 hover:shadow-lg rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <p class="text-gray-500 text-xs md:text-sm font-medium mb-1">Total Kelas</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800">
                        <?= $data['total_kelas'] ?? 0; ?>
                    </p>
                    <div class="flex items-center mt-2">
                        <div class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">TERSEDIA</div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-2 md:p-3 rounded-xl">
                    <i data-lucide="building-2" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Total Mapel -->
        <div class="bg-white/80 backdrop-blur-sm hover:bg-white/90 transition-all duration-300 hover:shadow-lg rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <p class="text-gray-500 text-xs md:text-sm font-medium mb-1">Mata Pelajaran</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800">
                        <?= $data['total_mapel'] ?? 0; ?>
                    </p>
                    <div class="flex items-center mt-2">
                        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">DIAJARKAN</div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-2 md:p-3 rounded-xl">
                    <i data-lucide="book-open" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Jurnal dalam Period -->
        <div class="bg-white/80 backdrop-blur-sm hover:bg-white/90 transition-all duration-300 hover:shadow-lg rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex-1">
                    <p class="text-gray-500 text-xs md:text-sm font-medium mb-1" id="jurnal-label">Jurnal Hari Ini</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-800" id="jurnal-count">
                        <span class="loading-dots">...</span>
                    </p>
                    <div class="flex items-center mt-2">
                        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">TERISI</div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-pink-500 p-2 md:p-3 rounded-xl">
                    <i data-lucide="clipboard-pen" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        <!-- Statistik Kehadiran with Class Filter -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-blue-600"></i>
                    <span id="chart1-title">Statistik Kehadiran Hari Ini</span>
                </h2>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                    <select id="filter-kelas-chart" class="border border-gray-300 rounded-lg px-2 md:px-3 py-1 md:py-2 text-xs md:text-sm">
                        <option value="">Semua Kelas</option>
                        <!-- Will be populated dynamically -->
                    </select>
                    <div class="text-xs text-gray-500 font-medium" id="last-updated">
                        Update: <?= date('H:i'); ?>
                    </div>
                </div>
            </div>
            <div class="relative h-48 md:h-64">
                <canvas id="attendanceChart"></canvas>
            </div>
            <div id="attendance-summary" class="mt-4 grid grid-cols-2 gap-4 text-center">
                <div class="bg-green-50 rounded-lg p-3">
                    <p class="text-green-700 font-bold text-lg md:text-xl" id="hadir-count">0</p>
                    <p class="text-green-600 text-xs md:text-sm">Hadir</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-red-700 font-bold text-lg md:text-xl" id="tidak-hadir-count">0</p>
                    <p class="text-red-600 text-xs md:text-sm">Tidak Hadir</p>
                </div>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-5 h-5 text-green-600"></i>
                    <span id="chart2-title">Trend Kehadiran</span>
                </h2>
                <div class="flex gap-2">
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-xs text-gray-600">Persentase Hadir</span>
                    </div>
                </div>
            </div>
            <div class="relative h-48 md:h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Performers (Dynamic Period) -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 md:p-6 border border-white/20 shadow-md" id="top-kelas-section">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="trophy" class="w-5 h-5 text-yellow-600"></i>
                <span id="top-kelas-title">Top Kelas Kehadiran</span>
            </h2>
        </div>
        <div id="top-kelas-content" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="text-center py-8 text-gray-500 col-span-full">
                <div class="flex flex-col items-center gap-3">
                    <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                    <span>Memuat data...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Siswa Tidak Masuk - Summary Version -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-4 md:p-6 border border-white/20 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="user-x" class="w-5 h-5 text-red-600"></i>
                <span id="absent-title">Ringkasan Siswa Tidak Masuk</span>
                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs ml-2" id="absent-count">0 SISWA</span>
            </h2>
            
            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <select id="filter-status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    <option value="S">Sakit</option>
                    <option value="I">Izin</option>
                    <option value="A">Alfa</option>
                </select>
                <select id="filter-kelas-absent" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Kelas</option>
                    <!-- Will be populated dynamically -->
                </select>
                <button onclick="refreshAbsentList()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div id="absent-summary" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 text-sm font-medium">Total Tidak Masuk</p>
                        <p class="text-2xl font-bold text-red-800" id="summary-total">0</p>
                    </div>
                    <div class="bg-red-500 p-2 rounded-lg">
                        <i data-lucide="user-minus" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-600 text-sm font-medium">Sakit</p>
                        <p class="text-2xl font-bold text-orange-800" id="summary-sakit">0</p>
                    </div>
                    <div class="bg-orange-500 p-2 rounded-lg">
                        <i data-lucide="thermometer" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">Izin</p>
                        <p class="text-2xl font-bold text-blue-800" id="summary-izin">0</p>
                    </div>
                    <div class="bg-blue-500 p-2 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 text-sm font-medium">Alfa</p>
                        <p class="text-2xl font-bold text-red-800" id="summary-alfa">0</p>
                    </div>
                    <div class="bg-red-500 p-2 rounded-lg">
                        <i data-lucide="user-minus" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student List with Details -->
        <div id="student-absence-list" class="space-y-3">
            <!-- Will be populated dynamically -->
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-4" id="load-more-section" style="display: none;">
            <button onclick="loadMoreAbsentStudents()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg text-sm transition-colors">
                <i data-lucide="chevron-down" class="w-4 h-4 mr-2"></i>
                Lihat Lebih Banyak
            </button>
        </div>
    </div>
</div>

<style>
.loading-dots::after {
    content: '';
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0%, 20% { content: '.'; }
    40% { content: '..'; }
    60%, 100% { content: '...'; }
}

.status-badge-S {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge-I {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge-A {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Student absence card styles */
.student-absence-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 1rem;
    transition: all 0.2s ease;
}

.student-absence-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: #d1d5db;
}

.student-absence-details {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.student-absence-details.expanded {
    max-height: 500px;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    .grid-cols-2 > .card-hover {
        min-height: auto;
    }
    
    .text-3xl {
        font-size: 1.5rem;
    }
    
    .text-2xl {
        font-size: 1.25rem;
    }
}

@media (max-width: 768px) {
    .lg\:grid-cols-5 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    
    .lg\:grid-cols-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    .lg\:grid-cols-2 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}
</style>

<script>
let attendanceChart = null;
let trendChart = null;
let currentPeriod = 'today';
let startDate = '';
let endDate = '';
let currentAbsentPage = 0;
let absentStudentsData = [];

// PRODUCTION FIX: Add base URL configuration
const BASE_URL = '<?= BASEURL; ?>';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard initialized with BASE_URL:', BASE_URL);
    
    initializeCharts();
    loadKelasOptions();
    applyFilter(); // Load initial data
    
    // Setup auto-refresh for current period only
    setInterval(() => {
        if (currentPeriod === 'today') {
            applyFilter();
        }
    }, 60000); // Refresh every minute for today only
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

function initializeCharts() {
    // Attendance Pie Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    attendanceChart = new Chart(attendanceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alfa'],
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: ['#22c55e', '#f59e0b', '#0ea5e9', '#ef4444'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: window.innerWidth < 768 ? 10 : 11, weight: '500' }
                    }
                }
            }
        }
    });

    // Trend Line Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Kehadiran (%)',
                data: [],
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: { 
                        callback: function(value) { return value + '%'; },
                        font: { size: window.innerWidth < 768 ? 10 : 12 }
                    }
                },
                x: {
                    ticks: {
                        font: { size: window.innerWidth < 768 ? 10 : 12 }
                    }
                }
            }
        }
    });
}

function handlePeriodChange() {
    const period = document.getElementById('filter-period').value;
    const customInputs = document.getElementById('custom-date-inputs');
    
    if (period === 'custom') {
        customInputs.classList.remove('hidden');
    } else {
        customInputs.classList.add('hidden');
    }
    
    // Auto apply for non-custom periods
    if (period !== 'custom') {
        applyFilter();
    }
}

function applyFilter() {
    currentPeriod = document.getElementById('filter-period').value;
    
    // Calculate date range based on period
    const now = new Date();
    let start, end;
    let periodTitle, periodDate, topKelasTitle;
    
    switch(currentPeriod) {
        case 'today':
            start = end = now.toISOString().split('T')[0];
            periodTitle = 'Data Hari Ini';
            periodDate = now.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
            topKelasTitle = 'Top Kelas Kehadiran Hari Ini';
            document.getElementById('jurnal-label').textContent = 'Jurnal Hari Ini';
            document.getElementById('chart1-title').textContent = 'Statistik Kehadiran Hari Ini';
            document.getElementById('chart2-title').textContent = 'Trend Kehadiran Hari Ini';
            document.getElementById('absent-title').textContent = 'Ringkasan Siswa Tidak Masuk Hari Ini';
            break;
            
        case 'this_week':
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            const endOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 6));
            start = startOfWeek.toISOString().split('T')[0];
            end = endOfWeek.toISOString().split('T')[0];
            periodTitle = 'Data Minggu Ini';
            periodDate = `${startOfWeek.toLocaleDateString('id-ID')} - ${endOfWeek.toLocaleDateString('id-ID')}`;
            topKelasTitle = 'Top Kelas Kehadiran Minggu Ini';
            document.getElementById('jurnal-label').textContent = 'Jurnal Minggu Ini';
            document.getElementById('chart1-title').textContent = 'Statistik Kehadiran Minggu Ini';
            document.getElementById('chart2-title').textContent = 'Trend Kehadiran Mingguan';
            document.getElementById('absent-title').textContent = 'Ringkasan Siswa Tidak Masuk (Minggu Ini)';
            break;
            
        case 'this_month':
            start = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            end = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
            periodTitle = 'Data Bulan Ini';
            periodDate = now.toLocaleDateString('id-ID', {month: 'long', year: 'numeric'});
            topKelasTitle = 'Top Kelas Kehadiran Bulan Ini';
            document.getElementById('jurnal-label').textContent = 'Jurnal Bulan Ini';
            document.getElementById('chart1-title').textContent = 'Statistik Kehadiran Bulan Ini';
            document.getElementById('chart2-title').textContent = 'Trend Kehadiran Bulanan';
            document.getElementById('absent-title').textContent = 'Ringkasan Siswa Tidak Masuk (Bulan Ini)';
            break;
            
        case 'this_semester':
            // Assume semester starts in July/January
            const semesterStart = now.getMonth() >= 6 ? new Date(now.getFullYear(), 6, 1) : new Date(now.getFullYear(), 0, 1);
            const semesterEnd = now.getMonth() >= 6 ? new Date(now.getFullYear(), 11, 31) : new Date(now.getFullYear(), 5, 30);
            start = semesterStart.toISOString().split('T')[0];
            end = semesterEnd.toISOString().split('T')[0];
            periodTitle = 'Data Semester Ini';
            periodDate = `${semesterStart.toLocaleDateString('id-ID')} - ${semesterEnd.toLocaleDateString('id-ID')}`;
            topKelasTitle = 'Top Kelas Kehadiran Semester Ini';
            document.getElementById('jurnal-label').textContent = 'Jurnal Semester Ini';
            document.getElementById('chart1-title').textContent = 'Statistik Kehadiran Semester Ini';
            document.getElementById('chart2-title').textContent = 'Trend Kehadiran Semesteran';
            document.getElementById('absent-title').textContent = 'Ringkasan Siswa Tidak Masuk (Semester Ini)';
            break;
            
        case 'custom':
            start = document.getElementById('start-date').value;
            end = document.getElementById('end-date').value;
            if (!start || !end) {
                alert('Harap pilih tanggal mulai dan selesai');
                return;
            }
            periodTitle = 'Data Custom Period';
            periodDate = `${new Date(start).toLocaleDateString('id-ID')} - ${new Date(end).toLocaleDateString('id-ID')}`;
            topKelasTitle = 'Top Kelas Kehadiran Custom Period';
            document.getElementById('jurnal-label').textContent = 'Jurnal Period';
            document.getElementById('chart1-title').textContent = 'Statistik Kehadiran Period';
            document.getElementById('chart2-title').textContent = 'Trend Kehadiran Period';
            document.getElementById('absent-title').textContent = 'Ringkasan Siswa Tidak Masuk (Custom Period)';
            break;
    }
    
    startDate = start;
    endDate = end;
    
    // Update UI
    document.getElementById('period-title').textContent = periodTitle;
    document.getElementById('period-date').textContent = periodDate;
    document.getElementById('top-kelas-title').textContent = topKelasTitle;
    
    // Reset pagination
    currentAbsentPage = 0;
    absentStudentsData = [];
    
    // Load data
    loadDashboardData();
    loadTopKelasData();
    loadAbsentStudents();
}

function loadDashboardData() {
    const kelasFilter = document.getElementById('filter-kelas-chart').value;
    
    // PRODUCTION FIX: Use proper URL construction
    const url = BASE_URL + '/KepalaMadrasah/getDashboardData';
    
    // Make AJAX call to backend
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            period: currentPeriod,
            start_date: startDate,
            end_date: endDate,
            kelas_filter: kelasFilter
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text(); // Get as text first to debug
    })
    .then(text => {
        console.log('Raw response:', text.substring(0, 200)); // Log first 200 chars
        try {
            const data = JSON.parse(text);
            console.log('Dashboard data received:', data);
            
            // Update jurnal count
            document.getElementById('jurnal-count').textContent = data.jurnal_count || 0;
            document.getElementById('total-pertemuan').textContent = data.total_pertemuan || 0;
            
            // Update attendance chart
            const attendanceData = [
                data.hadir || 0,
                data.sakit || 0, 
                data.izin || 0,
                data.alfa || 0
            ];
            
            attendanceChart.data.datasets[0].data = attendanceData;
            attendanceChart.update('none');
            
            // Update summary
            document.getElementById('hadir-count').textContent = data.hadir || 0;
            document.getElementById('tidak-hadir-count').textContent = (data.sakit || 0) + (data.izin || 0) + (data.alfa || 0);
            
            // Update trend chart
            if (data.trend_labels && data.trend_data) {
                trendChart.data.labels = data.trend_labels;
                trendChart.data.datasets[0].data = data.trend_data;
                trendChart.update('none');
            }
            
            // Update last updated time
            document.getElementById('last-updated').textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError);
            console.error('Response text:', text);
            throw new Error('Invalid JSON response');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('jurnal-count').textContent = '0';
        document.getElementById('total-pertemuan').textContent = '0';
    });
}

function loadTopKelasData() {
    // PRODUCTION FIX: Use proper URL construction
    const url = BASE_URL + '/KepalaMadrasah/getTopKelasData';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            period: currentPeriod,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text();
    })
    .then(text => {
        console.log('Top kelas raw response:', text.substring(0, 200));
        const data = JSON.parse(text);
        const topKelasContent = document.getElementById('top-kelas-content');
        const topKelas = data.top_kelas || [];
        
        if (topKelas.length === 0) {
            topKelasContent.innerHTML = `
                <div class="text-center py-8 text-gray-500 col-span-full">
                    <div class="flex flex-col items-center gap-3">
                        <i data-lucide="info" class="w-12 h-12 text-gray-400"></i>
                        <div>
                            <p class="font-semibold">Tidak ada data kehadiran</p>
                            <p class="text-sm">Pada periode yang dipilih</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            topKelasContent.innerHTML = topKelas.map((kelas, index) => {
                const rank = index + 1;
                const colorClass = rank === 1 ? 'yellow' : (rank === 2 ? 'gray' : 'orange');
                const bgColor = rank === 1 ? 'bg-gradient-to-r from-yellow-50 to-yellow-100 border-yellow-200' : 
                               rank === 2 ? 'bg-gradient-to-r from-gray-50 to-gray-100 border-gray-200' : 
                               'bg-gradient-to-r from-orange-50 to-orange-100 border-orange-200';
                const textColor = rank === 1 ? 'text-yellow-600' : 
                                 rank === 2 ? 'text-gray-600' : 
                                 'text-orange-600';
                
                return `
                    <div class="${bgColor} rounded-xl p-4 border animate-fade-in-up" style="animation-delay: ${index * 0.1}s">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xl font-bold ${textColor}">#${rank}</span>
                            <div class="${kelas.persentase_hadir >= 90 ? 'bg-green-100 text-green-800' : (kelas.persentase_hadir >= 75 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')} px-2 py-1 rounded-full text-xs font-semibold">
                                ${Number(kelas.persentase_hadir).toFixed(1)}%
                            </div>
                        </div>
                        <h3 class="font-bold text-gray-800">${kelas.nama_kelas}</h3>
                        <p class="text-sm text-gray-600">${kelas.hadir}/${kelas.total_absensi} siswa hadir</p>
                    </div>
                `;
            }).join('');
        }
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    })
    .catch(error => {
        console.error('Error loading top kelas:', error);
        document.getElementById('top-kelas-content').innerHTML = `
            <div class="text-center py-8 text-red-500 col-span-full">
                <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                <p>Gagal memuat data</p>
            </div>
        `;
    });
}

function loadAbsentStudents() {
    const statusFilter = document.getElementById('filter-status').value;
    const kelasFilter = document.getElementById('filter-kelas-absent').value;
    
    // Show loading in summary
    document.getElementById('summary-total').textContent = '...';
    document.getElementById('summary-sakit').textContent = '...';
    document.getElementById('summary-izin').textContent = '...';
    document.getElementById('summary-alfa').textContent = '...';
    
    const studentList = document.getElementById('student-absence-list');
    studentList.innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <div class="flex flex-col items-center gap-3">
                <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                <span>Memuat data siswa tidak masuk...</span>
            </div>
        </div>
    `;
    
    // PRODUCTION FIX: Use proper URL construction
    const url = BASE_URL + '/KepalaMadrasah/getAbsentStudentsSummary';
    
    // Make AJAX call to backend
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            period: currentPeriod,
            start_date: startDate,
            end_date: endDate,
            status_filter: statusFilter,
            kelas_filter: kelasFilter,
            page: currentAbsentPage,
            limit: 20
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text();
    })
    .then(text => {
        console.log('Absent students raw response:', text.substring(0, 200));
        const data = JSON.parse(text);
        
        // Update summary
        document.getElementById('summary-total').textContent = data.summary.total || 0;
        document.getElementById('summary-sakit').textContent = data.summary.sakit || 0;
        document.getElementById('summary-izin').textContent = data.summary.izin || 0;
        document.getElementById('summary-alfa').textContent = data.summary.alfa || 0;
        
        // Update count
        document.getElementById('absent-count').textContent = `${data.summary.total || 0} SISWA`;
        
        // Store data
        absentStudentsData = data.students || [];
        
        // Render student cards
        renderAbsentStudentCards(data.students || []);
        
        // Show/hide load more button
        const loadMoreSection = document.getElementById('load-more-section');
        if (data.has_more) {
            loadMoreSection.style.display = 'block';
        } else {
            loadMoreSection.style.display = 'none';
        }
        
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('summary-total').textContent = '0';
        document.getElementById('summary-sakit').textContent = '0';
        document.getElementById('summary-izin').textContent = '0';
        document.getElementById('summary-alfa').textContent = '0';
        
        document.getElementById('student-absence-list').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                <p>Gagal memuat data</p>
            </div>
        `;
    });
}

function renderAbsentStudentCards(students) {
    const studentList = document.getElementById('student-absence-list');
    
    if (students.length === 0) {
        studentList.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <div class="flex flex-col items-center gap-3">
                    <i data-lucide="check-circle" class="w-12 h-12 text-green-500"></i>
                    <div>
                        <p class="font-semibold">Tidak ada siswa yang tidak masuk</p>
                        <p class="text-sm">Pada periode yang dipilih</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    // Group students by name for summary
    const groupedStudents = {};
    students.forEach(student => {
        const key = `${student.id_siswa}_${student.nama_siswa}_${student.nama_kelas}`;
        if (!groupedStudents[key]) {
            groupedStudents[key] = {
                ...student,
                total_absent: 0,
                sakit_count: 0,
                izin_count: 0,
                alfa_count: 0,
                details: []
            };
        }
        
        groupedStudents[key].total_absent++;
        if (student.status_kehadiran === 'S') groupedStudents[key].sakit_count++;
        else if (student.status_kehadiran === 'I') groupedStudents[key].izin_count++;
        else if (student.status_kehadiran === 'A') groupedStudents[key].alfa_count++;
        
        groupedStudents[key].details.push({
            tanggal: student.tanggal,
            status: student.status_kehadiran,
            mapel: student.nama_mapel,
            guru: student.nama_guru,
            keterangan: student.keterangan
        });
    });
    
    // MODIFIED: Sort students by total_absent in descending order (highest first)
    const sortedStudents = Object.values(groupedStudents).sort((a, b) => b.total_absent - a.total_absent);
    
    const studentCards = sortedStudents.map((student, index) => `
        <div class="student-absence-card animate-fade-in-up" style="animation-delay: ${index * 0.05}s">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                        <h3 class="font-bold text-gray-800">${student.nama_siswa}</h3>
                        <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">${student.nama_kelas}</div>
                        ${student.total_absent > 3 ? '<div class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">PERHATIAN</div>' : ''}
                    </div>
                    <div class="flex flex-wrap gap-2 mb-2">
                        ${student.sakit_count > 0 ? `<span class="status-badge-S">Sakit: ${student.sakit_count}</span>` : ''}
                        ${student.izin_count > 0 ? `<span class="status-badge-I">Izin: ${student.izin_count}</span>` : ''}
                        ${student.alfa_count > 0 ? `<span class="status-badge-A">Alfa: ${student.alfa_count}</span>` : ''}
                    </div>
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-bold ${student.total_absent > 3 ? 'text-red-600' : 'text-gray-700'}">${student.total_absent}</span> kali tidak masuk
                        ${student.nisn ? `• NISN: ${student.nisn}` : ''}
                    </p>
                </div>
                <div class="flex gap-2">
                    <button onclick="toggleStudentDetails('student-${student.id_siswa}')" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition-colors whitespace-nowrap">
                        <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                        Detail
                    </button>
                </div>
            </div>
            
            <!-- Details Section -->
            <div id="student-${student.id_siswa}" class="student-absence-details mt-4 pt-4 border-t border-gray-200">
                <h4 class="font-semibold text-gray-800 mb-3">Rincian Ketidakhadiran:</h4>
                <div class="space-y-2">
                    ${student.details.map(detail => `
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-gray-50 rounded-lg p-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="status-badge-${detail.status}">
                                        ${detail.status === 'S' ? 'Sakit' : detail.status === 'I' ? 'Izin' : 'Alfa'}
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">${new Date(detail.tanggal).toLocaleDateString('id-ID')}</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    ${detail.mapel} • ${detail.guru}
                                </p>
                                ${detail.keterangan ? `<p class="text-xs text-gray-500 mt-1">${detail.keterangan}</p>` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `).join('');
    
    studentList.innerHTML = studentCards;
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function toggleStudentDetails(studentId) {
    const detailsElement = document.getElementById(studentId);
    const isExpanded = detailsElement.classList.contains('expanded');
    
    if (isExpanded) {
        detailsElement.classList.remove('expanded');
    } else {
        detailsElement.classList.add('expanded');
    }
}

function loadMoreAbsentStudents() {
    currentAbsentPage++;
    // Implementation would load more data and append to existing list
    // For now, just hide the button
    document.getElementById('load-more-section').style.display = 'none';
}

function loadKelasOptions() {
    // PRODUCTION FIX: Use proper URL construction
    const url = BASE_URL + '/KepalaMadrasah/getKelasOptions';
    
    fetch(url)
    .then(response => {
        console.log('Kelas options response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text();
    })
    .then(text => {
        console.log('Kelas options raw response:', text.substring(0, 200));
        const data = JSON.parse(text);
        const kelasSelectChart = document.getElementById('filter-kelas-chart');
        const kelasSelectAbsent = document.getElementById('filter-kelas-absent');
        
        // Clear existing options (except first one)
        kelasSelectChart.innerHTML = '<option value="">Semua Kelas</option>';
        kelasSelectAbsent.innerHTML = '<option value="">Semua Kelas</option>';
        
        data.kelas.forEach(kelas => {
            const option1 = document.createElement('option');
            option1.value = kelas.id_kelas;
            option1.textContent = kelas.nama_kelas;
            kelasSelectChart.appendChild(option1);
            
            const option2 = document.createElement('option');
            option2.value = kelas.id_kelas;
            option2.textContent = kelas.nama_kelas;
            kelasSelectAbsent.appendChild(option2);
        });
    })
    .catch(error => {
        console.error('Error loading kelas options:', error);
    });
}

// Additional functions
function refreshAbsentList() {
    currentAbsentPage = 0;
    loadAbsentStudents();
}

function refreshAllData() {
    applyFilter();
    loadKelasOptions();
}

function exportDashboardData() {
    // Create CSV export functionality
    const csvContent = "data:text/csv;charset=utf-8," 
        + "Period,Jurnal Count,Total Pertemuan,Hadir,Sakit,Izin,Alfa\n"
        + `${currentPeriod},${document.getElementById('jurnal-count').textContent},${document.getElementById('total-pertemuan').textContent},${document.getElementById('hadir-count').textContent},0,0,0`;
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `dashboard-data-${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Filter event listeners
document.getElementById('filter-status').addEventListener('change', loadAbsentStudents);
document.getElementById('filter-kelas-absent').addEventListener('change', loadAbsentStudents);
document.getElementById('filter-kelas-chart').addEventListener('change', loadDashboardData);
</script>