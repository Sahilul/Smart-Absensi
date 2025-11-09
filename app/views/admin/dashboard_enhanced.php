<?php 
// File: app/views/admin/dashboard_enhanced.php
?>

<!-- Page Content -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Admin</h2>
            <p class="text-gray-600 mt-1">Monitoring real-time sistem absensi sekolah</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="text-sm text-gray-600 bg-white px-3 py-2 rounded-lg border">
                <span class="font-medium">Sesi:</span> 
                <span class="text-indigo-600 font-semibold"><?= $_SESSION['nama_semester_aktif'] ?? 'Belum ada sesi aktif'; ?></span>
            </div>
            <button onclick="refreshDashboard()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors flex items-center">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- System Health Alert -->
    <?php if ($data['system_health']['status'] !== 'healthy'): ?>
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
        <div class="flex items-center">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-400 mr-2"></i>
            <h3 class="text-sm font-medium text-yellow-800">Peringatan Sistem</h3>
        </div>
        <div class="mt-2 text-sm text-yellow-700">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach ($data['system_health']['issues'] as $issue): ?>
                    <li><?= htmlspecialchars($issue); ?></li>
                <?php endforeach; ?>
                <?php foreach ($data['system_health']['warnings'] as $warning): ?>
                    <li class="text-yellow-600"><?= htmlspecialchars($warning); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Guru -->
        <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Guru</p>
                    <p class="text-3xl font-bold text-gray-900" id="total-guru"><?= $data['stats']['total_guru']; ?></p>
                    <p class="text-sm text-green-600 mt-1">
                        <?= $data['teacher_status']['guru_sudah_jurnal']; ?> aktif hari ini
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i data-lucide="user-check" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Siswa Aktif -->
        <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Siswa Aktif</p>
                    <p class="text-3xl font-bold text-gray-900" id="total-siswa"><?= $data['stats']['siswa']['aktif']; ?></p>
                    <p class="text-sm text-blue-600 mt-1">
                        <?= $data['stats']['total_kelas']; ?> kelas | 
                        <?= $data['stats']['siswa']['lulus']; ?> lulus
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i data-lucide="users" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Kehadiran Hari Ini -->
        <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Kehadiran Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900" id="attendance-percentage">
                        <?= $data['attendance_today']['percentage']; ?>%
                    </p>
                    <p class="text-sm text-green-600 mt-1">
                        <?= $data['attendance_today']['hadir']; ?>/<?= $data['attendance_today']['total']; ?> hadir
                    </p>
                </div>
                <div class="bg-emerald-100 p-3 rounded-full">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>

        <!-- Jurnal Mengajar -->
        <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jurnal Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900" id="journal-count">
                        <?= $data['teacher_status']['guru_sudah_jurnal']; ?>
                    </p>
                    <p class="text-sm mt-1 <?= $data['teacher_status']['guru_belum_jurnal'] > 0 ? 'text-orange-600' : 'text-green-600'; ?>">
                        <?= $data['teacher_status']['guru_belum_jurnal']; ?> belum input
                    </p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i data-lucide="book-open" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Trend Kehadiran -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Trend Kehadiran 7 Hari</h3>
                <button onclick="refreshChart('attendance')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="h-80">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Breakdown Kehadiran Hari Ini -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Status Kehadiran Hari Ini</h3>
                <div class="text-sm text-gray-500">
                    Total: <?= $data['attendance_today']['total']; ?> data
                </div>
            </div>
            <div class="h-80">
                <canvas id="todayChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Quick Actions -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="<?= BASEURL; ?>/admin/tambahSiswa" class="w-full flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-all duration-200 group">
                    <i data-lucide="user-plus" class="w-5 h-5 text-indigo-600 mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-gray-700">Tambah Siswa Baru</span>
                </a>
                <a href="<?= BASEURL; ?>/admin/tambahPenugasan" class="w-full flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-all duration-200 group">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-green-600 mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-gray-700">Buat Penugasan</span>
                </a>
                <a href="<?= BASEURL; ?>/admin/laporan" class="w-full flex items-center p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition-all duration-200 group">
                    <i data-lucide="file-text" class="w-5 h-5 text-orange-600 mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-gray-700">Lihat Laporan</span>
                </a>
                <a href="<?= BASEURL; ?>/admin/monitoringAbsensi" class="w-full flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-all duration-200 group">
                    <i data-lucide="eye" class="w-5 h-5 text-purple-600 mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-gray-700">Monitoring Real-time</span>
                </a>
            </div>
        </div>

        <!-- Jurnal Terbaru -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                    Live
                </span>
            </div>
            <div class="space-y-4 max-h-80 overflow-y-auto" id="recent-activity">
                <?php if (empty($data['recent_journals'])): ?>
                    <div class="text-center py-8">
                        <i data-lucide="calendar-x" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                        <p class="text-gray-500 text-sm">Belum ada aktivitas hari ini</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($data['recent_journals'], 0, 8) as $index => $journal): ?>
                        <div class="flex items-start space-x-3 animate-fade-in" style="animation-delay: <?= $index * 0.1; ?>s">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-white">
                                    <?= strtoupper(substr($journal['nama_guru'], 0, 2)); ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($journal['nama_guru']); ?></p>
                                <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($journal['nama_mapel']); ?> - <?= htmlspecialchars($journal['nama_kelas']); ?></p>
                                <p class="text-xs text-gray-400"><?= date('H:i', strtotime($journal['timestamp'])); ?></p>
                            </div>
                            <div class="flex-shrink-0">
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="<?= BASEURL; ?>/admin/monitoringJurnal" class="w-full mt-4 text-sm text-indigo-600 hover:text-indigo-800 font-medium block text-center py-2 border-t">
                Lihat Semua Aktivitas →
            </a>
        </div>

        <!-- Class Performance & Alerts -->
        <div class="bg-white p-6 rounded-xl shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kelas Terbaik</h3>
            
            <!-- Top Classes -->
            <div class="space-y-3 mb-6">
                <?php if (empty($data['class_stats'])): ?>
                    <p class="text-gray-500 text-sm">Data tidak tersedia</p>
                <?php else: ?>
                    <?php foreach (array_slice($data['class_stats'], 0, 5) as $index => $class): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-bold text-indigo-600"><?= $index + 1; ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($class['nama_kelas']); ?></p>
                                    <p class="text-xs text-gray-500"><?= $class['total_jurnal']; ?> jurnal</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600"><?= round($class['avg_kehadiran'] ?? 0, 1); ?>%</p>
                                <p class="text-xs text-gray-500">kehadiran</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- System Alerts -->
            <div class="border-t pt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Notifikasi Sistem</h4>
                <div class="space-y-3">
                    <?php foreach (array_slice($data['alerts'], 0, 3) as $alert): ?>
                        <div class="flex items-start space-x-3 p-3 bg-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : ($alert['type'] == 'success' ? 'green' : 'blue')); ?>-50 rounded-lg border border-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : ($alert['type'] == 'success' ? 'green' : 'blue')); ?>-200">
                            <i data-lucide="<?= $alert['icon']; ?>" class="w-4 h-4 text-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : ($alert['type'] == 'success' ? 'green' : 'blue')); ?>-500 mt-0.5 flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : ($alert['type'] == 'success' ? 'green' : 'blue')); ?>-800"><?= htmlspecialchars($alert['title']); ?></p>
                                <p class="text-xs text-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : ($alert['type'] == 'success' ? 'green' : 'blue')); ?>-600 mt-1"><?= htmlspecialchars($alert['message']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="mt-8 bg-gradient-to-r from-indigo-500 to-purple-600 p-6 rounded-xl text-white">
        <h3 class="text-lg font-semibold mb-4">Ringkasan Performa Sistem</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold"><?= $data['attendance_today']['percentage']; ?>%</div>
                <div class="text-sm opacity-80">Kehadiran Hari Ini</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold">
                    <?= $data['stats']['total_guru'] > 0 ? round(($data['teacher_status']['guru_sudah_jurnal'] / $data['stats']['total_guru']) * 100, 1) : 0; ?>%
                </div>
                <div class="text-sm opacity-80">Kelengkapan Jurnal</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold"><?= $data['stats']['total_kelas']; ?></div>
                <div class="text-sm opacity-80">Kelas Aktif</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold"><?= count($data['recent_journals']); ?></div>
                <div class="text-sm opacity-80">Total Aktivitas</div>
            </div>
        </div>
    </div>

</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Global chart instances
let attendanceChart, todayChart;

// Data dari PHP
const attendanceTrendData = <?= json_encode($data['attendance_trend'] ?? []); ?>;
const attendanceTodayData = <?= json_encode($data['attendance_today'] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0]); ?>;

// Initialize Charts
function initializeCharts() {
    // Attendance Trend Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx && !attendanceChart) {
        const labels = attendanceTrendData.map(item => {
            const date = new Date(item.tanggal);
            return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' });
        });
        const data = attendanceTrendData.map(item => parseFloat(item.persentase) || 0);

        attendanceChart = new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kehadiran (%)',
                    data: data,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#6366f1',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    },
                    x: { grid: { display: false } }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }

    // Today's Attendance Chart
    const todayCtx = document.getElementById('todayChart');
    if (todayCtx && !todayChart) {
        todayChart = new Chart(todayCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    data: [
                        attendanceTodayData.hadir || 0,
                        attendanceTodayData.izin || 0,
                        attendanceTodayData.sakit || 0,
                        attendanceTodayData.alfa || 0
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
}

// Refresh Dashboard
async function refreshDashboard() {
    try {
        const response = await fetch('<?= BASEURL; ?>/admin/api_getDashboardStats');
        const data = await response.json();
        
        // Update counters
        document.getElementById('total-guru').textContent = data.stats.total_guru;
        document.getElementById('total-siswa').textContent = data.stats.siswa.aktif;
        document.getElementById('attendance-percentage').textContent = data.attendance_today.percentage + '%';
        document.getElementById('journal-count').textContent = data.teacher_status.guru_sudah_jurnal;
        
        // Show success feedback
        const refreshBtn = document.querySelector('[onclick="refreshDashboard()"]');
        const icon = refreshBtn.querySelector('i');
        icon.style.animation = 'spin 1s linear';
        setTimeout(() => {
            icon.style.animation = '';
        }, 1000);
        
        console.log('Dashboard updated:', data.timestamp);
    } catch (error) {
        console.error('Error refreshing dashboard:', error);
    }
}

// Auto refresh every 2 minutes
setInterval(refreshDashboard, 120000);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    
    // Add animations to cards
    const cards = document.querySelectorAll('.transform');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate-slide-up');
    });
});

// CSS Animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
`;
document.head.appendChild(style);

console.log('Enhanced Dashboard loaded with real data');
</script>