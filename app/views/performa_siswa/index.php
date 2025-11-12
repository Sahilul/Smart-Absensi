<?php
// File: app/views/performa_siswa/index.php - MODERN RESPONSIVE DESIGN
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 p-3 sm:p-6 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                    Performa Kehadiran Siswa
                </h1>
                <p class="text-gray-600 text-sm sm:text-base">Analisis kehadiran siswa berdasarkan semua mata pelajaran</p>
            </div>
            <div class="mt-3 sm:mt-0">
                <span class="inline-block text-xs sm:text-sm text-purple-600 font-semibold bg-gradient-to-r from-purple-100 to-blue-100 px-4 py-2 rounded-full border border-purple-200">
                    <?= $data['session_info']['nama_semester'] ?? 'Semester Tidak Diketahui'; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
            Filter & Pencarian
        </h2>
        
        <!-- Period and Date Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select id="period-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month" selected>Bulan Ini</option>
                    <option value="this_semester">Semester Ini</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" id="start-date" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" id="end-date" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
            </div>
        </div>
        
        <!-- Class, Sort, and Search Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select id="kelas-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
                <select id="sort-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
                    <option value="persentase_desc">📈 Persentase Tertinggi</option>
                    <option value="persentase_asc">📉 Persentase Terendah</option>
                    <option value="hadir_desc">✅ Terbanyak Hadir</option>
                    <option value="hadir_asc">❌ Tersedikit Hadir</option>
                    <option value="alfa_desc">🚨 Terbanyak Alpha</option>
                    <option value="alfa_asc">⭐ Tersedikit Alpha</option>
                    <option value="sakit_desc">🤒 Terbanyak Sakit</option>
                    <option value="sakit_asc">💪 Tersedikit Sakit</option>
                    <option value="izin_desc">📝 Terbanyak Izin</option>
                    <option value="izin_asc">🎯 Tersedikit Izin</option>
                    <option value="total_desc">📚 Terbanyak Pertemuan</option>
                    <option value="total_asc">📖 Tersedikit Pertemuan</option>
                    <option value="nama_asc">🔤 Nama A-Z</option>
                    <option value="nama_desc">🔤 Nama Z-A</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa</label>
                <div class="relative">
                    <input type="text" id="search-input" placeholder="Nama siswa atau NISN..." 
                           class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 pl-10 focus:border-blue-500 focus:ring-0 transition-colors">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        🔍
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <button onclick="loadData()" class="flex-1 sm:flex-none bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]">
                📊 Tampilkan Data
            </button>
            <button onclick="exportData()" class="flex-1 sm:flex-none bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]">
                📥 Export PDF
            </button>
            <button onclick="resetFilter()" class="flex-1 sm:flex-none bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]">
                🔄 Reset
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Total Siswa</div>
                    <div class="text-xl sm:text-3xl font-bold" id="total-siswa">0</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">👥</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Rata-rata Hadir</div>
                    <div class="text-xl sm:text-3xl font-bold" id="rata-hadir">0%</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">📈</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Siswa Terbaik</div>
                    <div class="text-xl sm:text-3xl font-bold" id="siswa-terbaik">0</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">⭐</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Perlu Perhatian</div>
                    <div class="text-xl sm:text-3xl font-bold" id="perlu-perhatian">0</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">🚨</div>
            </div>
        </div>
    </div>

    <!-- Data Table/Cards -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                Data Performa Siswa
            </h2>
        </div>
        
        <!-- Loading -->
        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
            <div class="text-gray-600">Memuat data...</div>
        </div>

        <!-- Desktop Table -->
        <div id="table-container" class="hidden">
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">NISN</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Hadir</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Sakit</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Izin</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Alpha</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Persentase</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div id="mobile-cards" class="lg:hidden p-4 space-y-4">
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12 text-gray-500">
            <div class="text-6xl mb-4">📊</div>
            <div class="text-xl font-medium mb-2">Tidak ada data</div>
            <div class="text-sm">Tidak ada data performa siswa pada periode yang dipilih</div>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden text-center py-12 text-red-500">
            <div class="text-6xl mb-4">⚠️</div>
            <div class="text-xl font-medium mb-2">Terjadi kesalahan</div>
            <div class="text-sm mb-4" id="error-message"></div>
            <button onclick="loadData()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Coba Lagi
            </button>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] p-2 sm:p-4 lg:p-8">
    <div class="flex items-center justify-center min-h-full">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-7xl max-h-[95vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 sm:px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg sm:text-xl font-semibold">Detail Performa Siswa</h3>
                        <p class="text-blue-100 text-sm">Rincian per mata pelajaran</p>
                    </div>
                    <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 text-3xl font-light leading-none">&times;</button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(95vh-120px)]">
                <!-- Student Info -->
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="student-info">
                        <!-- Student info will be loaded here -->
                    </div>
                </div>

                <!-- Detail Loading -->
                <div id="detail-loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
                    <div class="text-gray-600">Memuat detail...</div>
                </div>

                <!-- Detail Table -->
                <div id="detail-table-container" class="hidden">
                    <!-- Desktop Detail Table -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Mata Pelajaran</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Guru</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Total</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Hadir</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Sakit</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Izin</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Alpha</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Persentase</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody id="detail-table-body" class="divide-y divide-gray-200">
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Detail Cards -->
                    <div id="detail-mobile-cards" class="lg:hidden p-4 space-y-4">
                    </div>
                </div>

                <!-- Detail Empty -->
                <div id="detail-empty" class="hidden text-center py-12 text-gray-500">
                    <div class="text-6xl mb-4">📚</div>
                    <div class="text-xl font-medium mb-2">Tidak ada data detail</div>
                    <div class="text-sm">Tidak ada data kehadiran untuk siswa ini pada periode yang dipilih</div>
                </div>

                <!-- Detail Error -->
                <div id="detail-error" class="hidden text-center py-12 text-red-500">
                    <div class="text-6xl mb-4">⚠️</div>
                    <div class="text-xl font-medium mb-2">Terjadi kesalahan</div>
                    <div class="text-sm mb-4" id="detail-error-message"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let allData = [];
let currentData = [];

document.addEventListener('DOMContentLoaded', function() {
    setDefaultDates();
    loadKelas();
    loadData();
    
    document.getElementById('period-select').addEventListener('change', handlePeriodChange);
    document.getElementById('search-input').addEventListener('input', filterData);
    document.getElementById('sort-select').addEventListener('change', sortData);
});

function setDefaultDates() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    document.getElementById('start-date').value = firstDay.toISOString().split('T')[0];
    document.getElementById('end-date').value = lastDay.toISOString().split('T')[0];
}

function handlePeriodChange() {
    const period = document.getElementById('period-select').value;
    const now = new Date();
    let start, end;
    
    switch(period) {
        case 'today':
            start = end = now.toISOString().split('T')[0];
            break;
        case 'this_week':
            const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            const endOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 6));
            start = startOfWeek.toISOString().split('T')[0];
            end = endOfWeek.toISOString().split('T')[0];
            break;
        case 'this_month':
            start = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            end = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'this_semester':
            const semesterStart = now.getMonth() >= 6 ? new Date(now.getFullYear(), 6, 1) : new Date(now.getFullYear(), 0, 1);
            const semesterEnd = now.getMonth() >= 6 ? new Date(now.getFullYear(), 11, 31) : new Date(now.getFullYear(), 5, 30);
            start = semesterStart.toISOString().split('T')[0];
            end = semesterEnd.toISOString().split('T')[0];
            break;
        case 'custom':
            return;
    }
    
    document.getElementById('start-date').value = start;
    document.getElementById('end-date').value = end;
}

function loadKelas() {
    fetch('<?= BASEURL; ?>/PerformaSiswa/getKelas')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const select = document.getElementById('kelas-select');
            select.innerHTML = '<option value="">Semua Kelas</option>';
            data.kelas.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas.id_kelas;
                option.textContent = kelas.nama_kelas;
                select.appendChild(option);
            });
        }
    })
    .catch(error => console.error('Error loading kelas:', error));
}

function loadData() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const kelasFilter = document.getElementById('kelas-select').value;
    
    if (!startDate || !endDate) {
        alert('Pilih tanggal terlebih dahulu');
        return;
    }
    
    showLoading();
    
    fetch('<?= BASEURL; ?>/PerformaSiswa/getData', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            start_date: startDate,
            end_date: endDate,
            kelas_filter: kelasFilter
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            allData = data.data;
            currentData = [...allData];
            sortData();
            updateStats();
        } else {
            showError(data.message || 'Gagal memuat data');
        }
    })
    .catch(error => {
        showError('Terjadi kesalahan: ' + error.message);
    });
}

function sortData() {
    const sortBy = document.getElementById('sort-select').value;
    
    currentData.sort((a, b) => {
        switch(sortBy) {
            case 'persentase_desc':
                return parseFloat(b.persentase_hadir) - parseFloat(a.persentase_hadir);
            case 'persentase_asc':
                return parseFloat(a.persentase_hadir) - parseFloat(b.persentase_hadir);
            case 'hadir_desc':
                return parseInt(b.hadir) - parseInt(a.hadir);
            case 'hadir_asc':
                return parseInt(a.hadir) - parseInt(b.hadir);
            case 'alfa_desc':
                return parseInt(b.alfa) - parseInt(a.alfa);
            case 'alfa_asc':
                return parseInt(a.alfa) - parseInt(b.alfa);
            case 'sakit_desc':
                return parseInt(b.sakit) - parseInt(a.sakit);
            case 'sakit_asc':
                return parseInt(a.sakit) - parseInt(b.sakit);
            case 'izin_desc':
                return parseInt(b.izin) - parseInt(a.izin);
            case 'izin_asc':
                return parseInt(a.izin) - parseInt(b.izin);
            case 'total_desc':
                return parseInt(b.total_pertemuan) - parseInt(a.total_pertemuan);
            case 'total_asc':
                return parseInt(a.total_pertemuan) - parseInt(b.total_pertemuan);
            case 'nama_asc':
                return a.nama_siswa.localeCompare(b.nama_siswa);
            case 'nama_desc':
                return b.nama_siswa.localeCompare(a.nama_siswa);
            default:
                return parseFloat(b.persentase_hadir) - parseFloat(a.persentase_hadir);
        }
    });
    
    showTable();
}

function filterData() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    if (!searchTerm) {
        currentData = [...allData];
    } else {
        currentData = allData.filter(siswa => 
            (siswa.nama_siswa && siswa.nama_siswa.toLowerCase().includes(searchTerm)) ||
            (siswa.nisn && siswa.nisn.toLowerCase().includes(searchTerm))
        );
    }
    
    sortData();
    updateStats();
}

function resetFilter() {
    document.getElementById('period-select').value = 'this_month';
    document.getElementById('kelas-select').value = '';
    document.getElementById('sort-select').value = 'persentase_desc';
    document.getElementById('search-input').value = '';
    setDefaultDates();
    loadData();
}

function showTable() {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
    
    if (currentData.length === 0) {
        document.getElementById('empty-state').classList.remove('hidden');
        document.getElementById('table-container').classList.add('hidden');
        return;
    }
    
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('table-container').classList.remove('hidden');
    
    // Desktop table
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = currentData.map((row, index) => {
        const persen = parseFloat(row.persentase_hadir);
        let status, statusClass;
        
        if (persen >= 95) {
            status = 'Sangat Baik'; statusClass = 'bg-green-100 text-green-800';
        } else if (persen >= 85) {
            status = 'Baik'; statusClass = 'bg-blue-100 text-blue-800';
        } else if (persen >= 75) {
            status = 'Cukup'; statusClass = 'bg-yellow-100 text-yellow-800';
        } else {
            status = 'Perlu Perhatian'; statusClass = 'bg-red-100 text-red-800';
        }
        
        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4 text-sm">${index + 1}</td>
                <td class="px-4 py-4 text-sm">${row.nisn || '-'}</td>
                <td class="px-4 py-4 text-sm font-medium text-gray-900">${row.nama_siswa}</td>
                <td class="px-4 py-4 text-sm">${row.nama_kelas}</td>
                <td class="px-4 py-4 text-sm text-center">${row.total_pertemuan}</td>
                <td class="px-4 py-4 text-sm text-center text-green-600 font-semibold">${row.hadir}</td>
                <td class="px-4 py-4 text-sm text-center text-orange-500">${row.sakit}</td>
                <td class="px-4 py-4 text-sm text-center text-blue-500">${row.izin}</td>
                <td class="px-4 py-4 text-sm text-center text-red-500 font-semibold">${row.alfa}</td>
                <td class="px-4 py-4 text-sm text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                        ${persen.toFixed(1)}%
                    </span>
                </td>
                <td class="px-4 py-4 text-sm text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                        ${status}
                    </span>
                </td>
                <td class="px-4 py-4 text-sm text-center">
                    <button onclick="showDetail(${row.id_siswa})" 
                            class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-[1.02]">
                        📋 Detail
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    // Mobile cards
    const mobileCards = document.getElementById('mobile-cards');
    mobileCards.innerHTML = currentData.map((row, index) => {
        const persen = parseFloat(row.persentase_hadir);
        let status, statusClass, bgGradient;
        
        if (persen >= 95) {
            status = 'Sangat Baik'; statusClass = 'text-green-700'; bgGradient = 'from-green-50 to-green-100';
        } else if (persen >= 85) {
            status = 'Baik'; statusClass = 'text-blue-700'; bgGradient = 'from-blue-50 to-blue-100';
        } else if (persen >= 75) {
            status = 'Cukup'; statusClass = 'text-yellow-700'; bgGradient = 'from-yellow-50 to-yellow-100';
        } else {
            status = 'Perlu Perhatian'; statusClass = 'text-red-700'; bgGradient = 'from-red-50 to-red-100';
        }
        
        return `
            <div class="bg-gradient-to-r ${bgGradient} rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-bold text-gray-900 text-base">${row.nama_siswa}</div>
                        <div class="text-sm text-gray-600">NISN: ${row.nisn || '-'}</div>
                        <div class="text-sm text-gray-600">Kelas: ${row.nama_kelas}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold ${statusClass}">${persen.toFixed(1)}%</div>
                        <div class="text-xs ${statusClass} font-medium">${status}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-4 gap-2 text-center text-xs mb-3">
                    <div class="bg-white rounded-lg p-2">
                        <div class="font-semibold text-gray-600">Total</div>
                        <div class="text-gray-800 font-bold">${row.total_pertemuan}</div>
                    </div>
                    <div class="bg-green-100 rounded-lg p-2">
                        <div class="font-semibold text-green-700">Hadir</div>
                        <div class="text-green-800 font-bold">${row.hadir}</div>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-2">
                        <div class="font-semibold text-orange-700">Sakit</div>
                        <div class="text-orange-800 font-bold">${row.sakit}</div>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-2">
                        <div class="font-semibold text-blue-700">Izin</div>
                        <div class="text-blue-800 font-bold">${row.izin}</div>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <div class="bg-red-100 rounded-lg px-3 py-1">
                        <span class="text-xs font-semibold text-red-700">Alpha: </span>
                        <span class="text-red-800 font-bold">${row.alfa}</span>
                    </div>
                    <button onclick="showDetail(${row.id_siswa})" 
                            class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all duration-200">
                        📋 Detail
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function showDetail(id_siswa) {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    document.getElementById('detail-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    document.getElementById('detail-loading').classList.remove('hidden');
    document.getElementById('detail-table-container').classList.add('hidden');
    document.getElementById('detail-empty').classList.add('hidden');
    document.getElementById('detail-error').classList.add('hidden');
    
    fetch('<?= BASEURL; ?>/PerformaSiswa/getDetail', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id_siswa: id_siswa,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail-loading').classList.add('hidden');
        
        if (data.status === 'success') {
            showDetailData(data.siswa_info, data.detail_data);
        } else {
            showDetailError(data.message || 'Gagal memuat detail');
        }
    })
    .catch(error => {
        document.getElementById('detail-loading').classList.add('hidden');
        showDetailError('Terjadi kesalahan: ' + error.message);
    });
}

function showDetailData(siswaInfo, detailData) {
    const studentInfoDiv = document.getElementById('student-info');
    studentInfoDiv.innerHTML = `
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">👤 Nama Siswa</div>
            <div class="font-bold text-gray-800">${siswaInfo.nama_siswa || '-'}</div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">🎫 NISN</div>
            <div class="font-bold text-gray-800">${siswaInfo.nisn || '-'}</div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">🏫 Kelas</div>
            <div class="font-bold text-gray-800">${siswaInfo.nama_kelas || '-'}</div>
        </div>
    `;
    
    if (detailData.length === 0) {
        document.getElementById('detail-empty').classList.remove('hidden');
        return;
    }
    
    document.getElementById('detail-table-container').classList.remove('hidden');
    
    // Desktop detail table
    const tbody = document.getElementById('detail-table-body');
    tbody.innerHTML = detailData.map((row, index) => {
        const persen = parseFloat(row.persentase_hadir);
        let status, statusClass;
        
        if (persen >= 95) {
            status = 'Sangat Baik'; statusClass = 'bg-green-100 text-green-800';
        } else if (persen >= 85) {
            status = 'Baik'; statusClass = 'bg-blue-100 text-blue-800';
        } else if (persen >= 75) {
            status = 'Cukup'; statusClass = 'bg-yellow-100 text-yellow-800';
        } else {
            status = 'Perlu Perhatian'; statusClass = 'bg-red-100 text-red-800';
        }
        
        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4 text-sm">${index + 1}</td>
                <td class="px-4 py-4 text-sm">
                    <div class="font-medium text-gray-900">${row.nama_mapel}</div>
                    <div class="text-xs text-gray-500">${row.kode_mapel}</div>
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">${row.nama_guru}</td>
                <td class="px-4 py-4 text-sm text-center">${row.total_pertemuan}</td>
                <td class="px-4 py-4 text-sm text-center text-green-600 font-semibold">${row.hadir}</td>
                <td class="px-4 py-4 text-sm text-center text-orange-500">${row.sakit}</td>
                <td class="px-4 py-4 text-sm text-center text-blue-500">${row.izin}</td>
                <td class="px-4 py-4 text-sm text-center text-red-500 font-semibold">${row.alfa}</td>
                <td class="px-4 py-4 text-sm text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                        ${persen.toFixed(1)}%
                    </span>
                </td>
                <td class="px-4 py-4 text-sm text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                        ${status}
                    </span>
                </td>
            </tr>
        `;
    }).join('');

    // Mobile detail cards
    const mobileDetailCards = document.getElementById('detail-mobile-cards');
    mobileDetailCards.innerHTML = detailData.map((row, index) => {
        const persen = parseFloat(row.persentase_hadir);
        let status, statusClass, bgGradient;
        
        if (persen >= 95) {
            status = 'Sangat Baik'; statusClass = 'text-green-700'; bgGradient = 'from-green-50 to-green-100';
        } else if (persen >= 85) {
            status = 'Baik'; statusClass = 'text-blue-700'; bgGradient = 'from-blue-50 to-blue-100';
        } else if (persen >= 75) {
            status = 'Cukup'; statusClass = 'text-yellow-700'; bgGradient = 'from-yellow-50 to-yellow-100';
        } else {
            status = 'Perlu Perhatian'; statusClass = 'text-red-700'; bgGradient = 'from-red-50 to-red-100';
        }
        
        return `
            <div class="bg-gradient-to-r ${bgGradient} rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="font-bold text-gray-900 text-sm">${row.nama_mapel}</div>
                        <div class="text-xs text-gray-600">${row.kode_mapel}</div>
                        <div class="text-xs text-gray-600 mt-1">👨‍🏫 ${row.nama_guru}</div>
                    </div>
                    <div class="text-right ml-3">
                        <div class="text-xl font-bold ${statusClass}">${persen.toFixed(1)}%</div>
                        <div class="text-xs ${statusClass} font-medium">${status}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-5 gap-1 text-center text-xs">
                    <div class="bg-white rounded p-2">
                        <div class="font-semibold text-gray-600 text-xs">Total</div>
                        <div class="text-gray-800 font-bold">${row.total_pertemuan}</div>
                    </div>
                    <div class="bg-green-100 rounded p-2">
                        <div class="font-semibold text-green-700 text-xs">Hadir</div>
                        <div class="text-green-800 font-bold">${row.hadir}</div>
                    </div>
                    <div class="bg-orange-100 rounded p-2">
                        <div class="font-semibold text-orange-700 text-xs">Sakit</div>
                        <div class="text-orange-800 font-bold">${row.sakit}</div>
                    </div>
                    <div class="bg-blue-100 rounded p-2">
                        <div class="font-semibold text-blue-700 text-xs">Izin</div>
                        <div class="text-blue-800 font-bold">${row.izin}</div>
                    </div>
                    <div class="bg-red-100 rounded p-2">
                        <div class="font-semibold text-red-700 text-xs">Alpha</div>
                        <div class="text-red-800 font-bold">${row.alfa}</div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function showDetailError(message) {
    document.getElementById('detail-error').classList.remove('hidden');
    document.getElementById('detail-error-message').textContent = message;
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('detail-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('detail-modal').classList.contains('hidden')) {
        closeDetailModal();
    }
});

function showLoading() {
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('table-container').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('error-state').classList.add('hidden');
}

function showError(message) {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('table-container').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('error-state').classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
}

function updateStats() {
    if (currentData.length === 0) {
        document.getElementById('total-siswa').textContent = '0';
        document.getElementById('rata-hadir').textContent = '0%';
        document.getElementById('siswa-terbaik').textContent = '0';
        document.getElementById('perlu-perhatian').textContent = '0';
        return;
    }
    
    const totalSiswa = currentData.length;
    const rataHadir = (currentData.reduce((sum, s) => sum + parseFloat(s.persentase_hadir), 0) / totalSiswa).toFixed(1);
    const siswaTerbaik = currentData.filter(s => parseFloat(s.persentase_hadir) >= 95).length;
    const perluPerhatian = currentData.filter(s => parseFloat(s.persentase_hadir) < 75).length;
    
    document.getElementById('total-siswa').textContent = totalSiswa;
    document.getElementById('rata-hadir').textContent = rataHadir + '%';
    document.getElementById('siswa-terbaik').textContent = siswaTerbaik;
    document.getElementById('perlu-perhatian').textContent = perluPerhatian;
}

function exportData() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const kelasFilter = document.getElementById('kelas-select').value;
    
    if (!startDate || !endDate) {
        alert('Pilih tanggal terlebih dahulu');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASEURL; ?>/PerformaSiswa/exportPdf';
    
    const fields = { start_date: startDate, end_date: endDate, kelas_filter: kelasFilter };
    
    Object.keys(fields).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

<style>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Custom focus styles */
input:focus, select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Mobile responsive */
@media (max-width: 640px) {
    .min-h-screen {
        padding: 0.75rem;
    }
    
    #detail-modal .bg-white {
        margin: 0.5rem;
        max-height: calc(100vh - 1rem);
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
}

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>