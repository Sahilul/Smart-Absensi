<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    
    <!-- Header dengan Summary -->
    <div class="mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-white/20 shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-800 mb-2">Rekap Absensi per Mata Pelajaran</h2>
                    <p class="text-secondary-600 text-lg">
                        Menampilkan rekap untuk sesi: <span class="font-semibold text-primary-600"><?= $_SESSION['nama_semester_aktif']; ?></span>
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="exportDetailedReport()" class="btn-secondary flex items-center justify-center gap-2 px-4 py-2">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        Export Excel
                    </button>
                    <button onclick="generatePDFReport()" class="btn-primary flex items-center justify-center gap-2 px-4 py-2">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Cetak PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($data['rekap_per_mapel'])) : ?>
        <!-- Empty State -->
        <div class="glass-effect rounded-xl p-12 border border-white/20 shadow-lg text-center">
            <div class="w-24 h-24 bg-secondary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="book-x" class="w-12 h-12 text-secondary-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-secondary-800 mb-3">Belum Ada Data Rekap</h3>
            <p class="text-secondary-600 mb-6">Belum ada data absensi untuk sesi ini.</p>
            <a href="<?= BASEURL; ?>/siswa/dashboard" class="btn-primary inline-flex items-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i>
                Kembali ke Dashboard
            </a>
        </div>
    <?php else : ?>
        
        <!-- Overall Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <?php 
            $total_subjects = count($data['rekap_per_mapel']);
            $total_meetings = 0;
            $total_present = 0;
            $total_all = 0;
            
            foreach ($data['rekap_per_mapel'] as $rekap) {
                $subject_total = $rekap['hadir'] + $rekap['izin'] + $rekap['sakit'] + $rekap['alfa'];
                $total_meetings += $subject_total;
                $total_present += $rekap['hadir'];
                $total_all += $subject_total;
            }
            
            $overall_percentage = ($total_all > 0) ? round(($total_present / $total_all) * 100) : 0;
            ?>
            
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-secondary-500 text-sm font-medium mb-1">Total Mata Pelajaran</div>
                        <div class="text-3xl font-bold text-secondary-800 group-hover:text-primary-600 transition-colors">
                            <?= $total_subjects; ?>
                        </div>
                    </div>
                    <div class="gradient-primary p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                        <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-secondary-500 text-sm font-medium mb-1">Total Pertemuan</div>
                        <div class="text-3xl font-bold text-secondary-800 group-hover:text-blue-600 transition-colors">
                            <?= $total_meetings; ?>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                        <i data-lucide="calendar-days" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-secondary-500 text-sm font-medium mb-1">Total Kehadiran</div>
                        <div class="text-3xl font-bold text-success-600 group-hover:scale-105 transition-transform">
                            <?= $total_present; ?>
                        </div>
                    </div>
                    <div class="gradient-success p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                        <i data-lucide="user-check" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover group">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-secondary-500 text-sm font-medium mb-1">Persentase Keseluruhan</div>
                        <div class="text-3xl font-bold text-<?= ($overall_percentage >= 75) ? 'success' : (($overall_percentage >= 60) ? 'warning' : 'danger'); ?>-600 group-hover:scale-105 transition-transform">
                            <?= $overall_percentage; ?>%
                        </div>
                    </div>
                    <div class="gradient-<?= ($overall_percentage >= 75) ? 'success' : (($overall_percentage >= 60) ? 'warning' : 'danger'); ?> p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                        <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter dan Sort Controls -->
        <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-secondary-400 w-4 h-4"></i>
                        <input type="text" id="searchSubject" placeholder="Cari mata pelajaran..." 
                               class="input-modern pl-10 pr-4 py-2 text-sm w-full sm:w-64">
                    </div>
                    <select id="performanceFilter" class="input-modern text-sm">
                        <option value="">Semua Performa</option>
                        <option value="excellent">Excellent (≥90%)</option>
                        <option value="good">Baik (75-89%)</option>
                        <option value="fair">Cukup (60-74%)</option>
                        <option value="poor">Kurang (<60%)</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button onclick="showChartView()" id="chartViewBtn" class="btn-primary text-sm px-3 py-2 flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        Grafik
                    </button>
                    <button onclick="showTableView()" id="tableViewBtn" class="btn-secondary text-sm px-3 py-2 flex items-center gap-2">
                        <i data-lucide="table-2" class="w-4 h-4"></i>
                        Tabel
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div id="tableView" class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-secondary-50 to-secondary-100 px-6 py-4 border-b border-white/20">
                <h3 class="text-lg font-semibold text-secondary-800">Detail per Mata Pelajaran</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-secondary-200" id="rekapTable">
                    <thead class="bg-secondary-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-secondary-600 uppercase tracking-wider">
                                Mata Pelajaran
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider cursor-pointer hover:bg-secondary-100 transition-colors" onclick="sortTable('hadir')">
                                <div class="flex items-center justify-center gap-2">
                                    Hadir
                                    <i data-lucide="chevrons-up-down" class="w-4 h-4"></i>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider">
                                Izin
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider">
                                Sakit
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider">
                                Alpha
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider cursor-pointer hover:bg-secondary-100 transition-colors" onclick="sortTable('total')">
                                <div class="flex items-center justify-center gap-2">
                                    Total
                                    <i data-lucide="chevrons-up-down" class="w-4 h-4"></i>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider cursor-pointer hover:bg-secondary-100 transition-colors" onclick="sortTable('percentage')">
                                <div class="flex items-center justify-center gap-2">
                                    Kehadiran (%)
                                    <i data-lucide="chevrons-up-down" class="w-4 h-4"></i>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider">
                                Progress
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-secondary-100" id="tableBody">
                        <?php foreach ($data['rekap_per_mapel'] as $index => $rekap) : ?>
                            <?php
                                $total = $rekap['hadir'] + $rekap['izin'] + $rekap['sakit'] + $rekap['alfa'];
                                $persentase = ($total > 0) ? round(($rekap['hadir'] / $total) * 100) : 0;
                                
                                // Determine status
                                $status = 'poor';
                                $statusColor = 'danger';
                                $statusText = 'Kurang';
                                
                                if ($persentase >= 90) {
                                    $status = 'excellent';
                                    $statusColor = 'success';
                                    $statusText = 'Excellent';
                                } elseif ($persentase >= 75) {
                                    $status = 'good';
                                    $statusColor = 'primary';
                                    $statusText = 'Baik';
                                } elseif ($persentase >= 60) {
                                    $status = 'fair';
                                    $statusColor = 'warning';
                                    $statusText = 'Cukup';
                                }
                            ?>
                            <tr class="hover:bg-secondary-50 transition-colors duration-200 animate-fade-in" 
                                style="animation-delay: <?= $index * 100; ?>ms" 
                                data-row 
                                data-subject="<?= strtolower($rekap['nama_mapel']); ?>"
                                data-percentage="<?= $persentase; ?>"
                                data-performance="<?= $status; ?>"
                                data-hadir="<?= $rekap['hadir']; ?>"
                                data-total="<?= $total; ?>">
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 gradient-primary rounded-lg flex items-center justify-center mr-4">
                                            <i data-lucide="book" class="w-5 h-5 text-white"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-secondary-800">
                                                <?= htmlspecialchars($rekap['nama_mapel']); ?>
                                            </div>
                                            <div class="text-xs text-secondary-500">
                                                <?= $total; ?> pertemuan total
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 bg-success-100 rounded-full">
                                        <span class="text-sm font-bold text-success-700"><?= $rekap['hadir']; ?></span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                        <span class="text-sm font-bold text-blue-700"><?= $rekap['izin']; ?></span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-full">
                                        <span class="text-sm font-bold text-yellow-700"><?= $rekap['sakit']; ?></span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 bg-red-100 rounded-full">
                                        <span class="text-sm font-bold text-red-700"><?= $rekap['alfa']; ?></span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <span class="text-lg font-bold text-secondary-800"><?= $total; ?></span>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="text-xl font-bold text-<?= $statusColor; ?>-600"><?= $persentase; ?>%</span>
                                        <span class="status-<?= $status; ?> px-2 py-1 rounded-full text-xs font-semibold">
                                            <?= $statusText; ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="w-full">
                                        <div class="flex items-center justify-between text-xs text-secondary-500 mb-1">
                                            <span>Progress</span>
                                            <span><?= $persentase; ?>%</span>
                                        </div>
                                        <div class="progress-bar h-3 rounded-full">
                                            <div class="progress-fill gradient-<?= $statusColor; ?> rounded-full" style="width: <?= $persentase; ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chart View -->
        <div id="chartView" class="hidden grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Bar Chart -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-semibold text-secondary-800 mb-4">Persentase Kehadiran per Mata Pelajaran</h3>
                <div class="h-80">
                    <canvas id="attendanceBarChart"></canvas>
                </div>
            </div>

            <!-- Radar Chart -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-semibold text-secondary-800 mb-4">Distribusi Status Kehadiran</h3>
                <div class="h-80">
                    <canvas id="attendanceRadarChart"></canvas>
                </div>
            </div>

            <!-- Performance Distribution -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg xl:col-span-2">
                <h3 class="text-lg font-semibold text-secondary-800 mb-4">Distribusi Performa Kehadiran</h3>
                <div class="h-64">
                    <canvas id="performanceDistributionChart"></canvas>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari PHP
    const rekapData = <?= json_encode($data['rekap_per_mapel'] ?? []); ?>;
    
    // Initialize
    initializeFilters();
    initializeCharts();
    
    // Event listeners
    document.getElementById('searchSubject').addEventListener('input', filterTable);
    document.getElementById('performanceFilter').addEventListener('change', filterTable);
    
    function initializeFilters() {
        // Initialize filter functionality
        filterTable();
    }
    
    function filterTable() {
        const searchTerm = document.getElementById('searchSubject').value.toLowerCase();
        const performanceFilter = document.getElementById('performanceFilter').value;
        
        const rows = document.querySelectorAll('tbody tr[data-row]');
        
        rows.forEach(row => {
            const subject = row.dataset.subject || '';
            const performance = row.dataset.performance || '';
            
            const searchMatch = !searchTerm || subject.includes(searchTerm);
            const performanceMatch = !performanceFilter || performance === performanceFilter;
            
            row.style.display = searchMatch && performanceMatch ? '' : 'none';
        });
    }
    
    function sortTable(column) {
        const tbody = document.querySelector('#tableBody');
        const rows = Array.from(tbody.querySelectorAll('tr[data-row]'));
        
        // Toggle sort direction
        let ascending = tbody.dataset.sortDir !== 'asc';
        tbody.dataset.sortDir = ascending ? 'asc' : 'desc';
        
        rows.sort((a, b) => {
            let aVal, bVal;
            
            switch(column) {
                case 'hadir':
                    aVal = parseInt(a.dataset.hadir);
                    bVal = parseInt(b.dataset.hadir);
                    break;
                case 'total':
                    aVal = parseInt(a.dataset.total);
                    bVal = parseInt(b.dataset.total);
                    break;
                case 'percentage':
                    aVal = parseInt(a.dataset.percentage);
                    bVal = parseInt(b.dataset.percentage);
                    break;
                default:
                    return 0;
            }
            
            if (aVal < bVal) return ascending ? -1 : 1;
            if (aVal > bVal) return ascending ? 1 : -1;
            return 0;
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }
    
    function showTableView() {
        document.getElementById('tableView').classList.remove('hidden');
        document.getElementById('chartView').classList.add('hidden');
        
        document.getElementById('tableViewBtn').className = 'btn-primary text-sm px-3 py-2 flex items-center gap-2';
        document.getElementById('chartViewBtn').className = 'btn-secondary text-sm px-3 py-2 flex items-center gap-2';
    }
    
    function showChartView() {
        document.getElementById('tableView').classList.add('hidden');
        document.getElementById('chartView').classList.remove('hidden');
        
        document.getElementById('chartViewBtn').className = 'btn-primary text-sm px-3 py-2 flex items-center gap-2';
        document.getElementById('tableViewBtn').className = 'btn-secondary text-sm px-3 py-2 flex items-center gap-2';
        
        // Refresh charts
        setTimeout(initializeCharts, 100);
    }
    
    function initializeCharts() {
        if (rekapData.length === 0 || typeof Chart === 'undefined') return;
        
        // Bar Chart - Attendance Percentage
        const barCtx = document.getElementById('attendanceBarChart');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: rekapData.map(item => item.nama_mapel),
                    datasets: [{
                        label: 'Kehadiran (%)',
                        data: rekapData.map(item => {
                            const total = item.hadir + item.izin + item.sakit + item.alfa;
                            return total > 0 ? Math.round((item.hadir / total) * 100) : 0;
                        }),
                        backgroundColor: rekapData.map(item => {
                            const total = item.hadir + item.izin + item.sakit + item.alfa;
                            const percentage = total > 0 ? Math.round((item.hadir / total) * 100) : 0;
                            
                            if (percentage >= 90) return 'rgba(34, 197, 94, 0.8)';
                            if (percentage >= 75) return 'rgba(59, 130, 246, 0.8)';
                            if (percentage >= 60) return 'rgba(245, 158, 11, 0.8)';
                            return 'rgba(239, 68, 68, 0.8)';
                        }),
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Radar Chart - Overall Distribution
        const radarCtx = document.getElementById('attendanceRadarChart');
        if (radarCtx) {
            const totalHadir = rekapData.reduce((sum, item) => sum + item.hadir, 0);
            const totalIzin = rekapData.reduce((sum, item) => sum + item.izin, 0);
            const totalSakit = rekapData.reduce((sum, item) => sum + item.sakit, 0);
            const totalAlfa = rekapData.reduce((sum, item) => sum + item.alfa, 0);
            
            new Chart(radarCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                    datasets: [{
                        data: [totalHadir, totalIzin, totalSakit, totalAlfa],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Performance Distribution
        const perfCtx = document.getElementById('performanceDistributionChart');
        if (perfCtx) {
            let excellent = 0, good = 0, fair = 0, poor = 0;
            
            rekapData.forEach(item => {
                const total = item.hadir + item.izin + item.sakit + item.alfa;
                const percentage = total > 0 ? Math.round((item.hadir / total) * 100) : 0;
                
                if (percentage >= 90) excellent++;
                else if (percentage >= 75) good++;
                else if (percentage >= 60) fair++;
                else poor++;
            });
            
            new Chart(perfCtx, {
                type: 'bar',
                data: {
                    labels: ['Excellent (≥90%)', 'Baik (75-89%)', 'Cukup (60-74%)', 'Kurang (<60%)'],
                    datasets: [{
                        label: 'Jumlah Mata Pelajaran',
                        data: [excellent, good, fair, poor],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Export functions
    window.exportDetailedReport = function() {
        // Implementation for Excel export
        alert('Fitur export Excel akan segera tersedia!');
    };
    
    window.generatePDFReport = function() {
        window.print();
    };
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Make functions global
    window.sortTable = sortTable;
    window.showTableView = showTableView;
    window.showChartView = showChartView;
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
}

@media print {
    #chartView {
        display: none !important;
    }
    
    .glass-effect:nth-child(2) {
        display: none !important;
    }
    
    .bg-gradient-to-br {
        background: white !important;
    }
}
</style>