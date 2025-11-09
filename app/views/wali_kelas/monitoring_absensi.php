<?php
// File: app/views/wali_kelas/monitoring_absensi.php - Monitoring Absensi Wali Kelas
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 p-3 sm:p-6 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                    Monitoring Absensi Kelas <?= htmlspecialchars($data['nama_kelas'] ?? ''); ?>
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
        
        <!-- Sort and Search Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
                <select id="sort-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
                    <option value="persentase_desc">📈 Persentase Tertinggi</option>
                    <option value="persentase_asc">📉 Persentase Terendah</option>
                    <option value="hadir_desc">✅ Terbanyak Hadir</option>
                    <option value="alfa_desc">🚨 Terbanyak Alpha</option>
                    <option value="sakit_desc">🤒 Terbanyak Sakit</option>
                    <option value="izin_desc">📝 Terbanyak Izin</option>
                    <option value="nama_asc">🔤 Nama A-Z</option>
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
            <button onclick="exportPDF()" class="flex-1 sm:flex-none bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]">
                📥 Download PDF
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
        
        <!-- Loading State -->
        <div id="loading-state" class="p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
            <p class="mt-4 text-gray-600">Memuat data...</p>
        </div>
        
        <!-- Empty State -->
        <div id="empty-state" class="hidden p-12 text-center">
            <div class="text-6xl mb-4">📊</div>
            <p class="text-gray-600 text-lg font-medium">Belum ada data</p>
            <p class="text-gray-500 text-sm mt-2">Silakan pilih filter dan klik "Tampilkan Data"</p>
        </div>
        
        <!-- Desktop Table View -->
        <div id="table-container" class="hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-blue-50 to-purple-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Siswa</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">NISN</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Kelas</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Total</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Hadir</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Sakit</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Izin</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Alpha</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">%</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Card View -->
        <div id="card-container" class="hidden p-4 space-y-4">
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white rounded-t-2xl">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Detail Absensi Siswa</h3>
                    <div id="modal-student-info" class="text-sm opacity-90"></div>
                </div>
                <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="modal-content" class="p-6">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
// Base URL untuk AJAX
const BASE_URL = '<?= BASEURL; ?>';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    initializeDates();
    loadData();
});

function initializeDates() {
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    document.getElementById('start-date').value = firstDayOfMonth.toISOString().split('T')[0];
    document.getElementById('end-date').value = today.toISOString().split('T')[0];
    
    // Period select handler
    document.getElementById('period-select').addEventListener('change', function() {
        const value = this.value;
        const today = new Date();
        let startDate, endDate = today;
        
        switch(value) {
            case 'today':
                startDate = today;
                break;
            case 'this_week':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - today.getDay());
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                break;
            case 'this_semester':
                startDate = new Date(today.getFullYear(), today.getMonth() >= 7 ? 7 : 0, 1);
                break;
            case 'custom':
                return;
        }
        
        document.getElementById('start-date').value = startDate.toISOString().split('T')[0];
        document.getElementById('end-date').value = endDate.toISOString().split('T')[0];
    });
}

async function loadData() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    // Show loading
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('table-container').classList.add('hidden');
    document.getElementById('card-container').classList.add('hidden');
    
    try {
        const response = await fetch(`${BASE_URL}/waliKelas/getDataAbsensi`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ start_date: startDate, end_date: endDate })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            displayData(result.data);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data');
    } finally {
        document.getElementById('loading-state').classList.add('hidden');
    }
}

function displayData(rawData) {
    if (!rawData || rawData.length === 0) {
        document.getElementById('empty-state').classList.remove('hidden');
        return;
    }
    
    // Apply filters and sorting
    let data = filterAndSort(rawData);
    
    // Update stats
    updateStats(data);
    
    // Display table and cards
    displayTable(data);
    displayCards(data);
    
    document.getElementById('table-container').classList.remove('hidden');
    document.getElementById('card-container').classList.remove('hidden');
}

function filterAndSort(data) {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const sortBy = document.getElementById('sort-select').value;
    
    // Filter by search
    let filtered = data.filter(item => {
        return item.nama_siswa.toLowerCase().includes(searchTerm) || 
               (item.nisn && item.nisn.includes(searchTerm));
    });
    
    // Sort
    filtered.sort((a, b) => {
        switch(sortBy) {
            case 'persentase_desc': return b.persentase_hadir - a.persentase_hadir;
            case 'persentase_asc': return a.persentase_hadir - b.persentase_hadir;
            case 'hadir_desc': return b.total_hadir - a.total_hadir;
            case 'alfa_desc': return b.total_alfa - a.total_alfa;
            case 'sakit_desc': return b.total_sakit - a.total_sakit;
            case 'izin_desc': return b.total_izin - a.total_izin;
            case 'nama_asc': return a.nama_siswa.localeCompare(b.nama_siswa);
            default: return 0;
        }
    });
    
    return filtered;
}

function updateStats(data) {
    const totalSiswa = data.length;
    const avgHadir = totalSiswa > 0 ? 
        (data.reduce((sum, item) => sum + parseFloat(item.persentase_hadir), 0) / totalSiswa).toFixed(1) : 0;
    const siswaTerbaik = data.filter(item => parseFloat(item.persentase_hadir) >= 95).length;
    const perluPerhatian = data.filter(item => parseFloat(item.persentase_hadir) < 75).length;
    
    document.getElementById('total-siswa').textContent = totalSiswa;
    document.getElementById('rata-hadir').textContent = avgHadir + '%';
    document.getElementById('siswa-terbaik').textContent = siswaTerbaik;
    document.getElementById('perlu-perhatian').textContent = perluPerhatian;
}

function displayTable(data) {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = data.map((item, index) => {
        const persen = parseFloat(item.persentase_hadir);
        const badgeClass = persen >= 95 ? 'bg-green-100 text-green-800' :
                          persen >= 85 ? 'bg-blue-100 text-blue-800' :
                          persen >= 75 ? 'bg-yellow-100 text-yellow-800' :
                          'bg-red-100 text-red-800';
        
        return `
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-gray-700">${index + 1}</td>
                <td class="px-4 py-3 font-medium text-gray-900">${item.nama_siswa}</td>
                <td class="px-4 py-3 text-center text-gray-600">${item.nisn || '-'}</td>
                <td class="px-4 py-3 text-center text-gray-600">${item.nama_kelas}</td>
                <td class="px-4 py-3 text-center font-semibold">${item.total_pertemuan}</td>
                <td class="px-4 py-3 text-center text-green-600 font-semibold">${item.total_hadir}</td>
                <td class="px-4 py-3 text-center text-yellow-600">${item.total_sakit}</td>
                <td class="px-4 py-3 text-center text-blue-600">${item.total_izin}</td>
                <td class="px-4 py-3 text-center text-red-600 font-semibold">${item.total_alfa}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                        ${persen}%
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <button onclick="showDetail(${item.id_siswa})" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-medium transition-colors">
                        Detail
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function displayCards(data) {
    const container = document.getElementById('card-container');
    container.innerHTML = data.map((item, index) => {
        const persen = parseFloat(item.persentase_hadir);
        const badgeClass = persen >= 95 ? 'bg-green-100 text-green-800' :
                          persen >= 85 ? 'bg-blue-100 text-blue-800' :
                          persen >= 75 ? 'bg-yellow-100 text-yellow-800' :
                          'bg-red-100 text-red-800';
        
        return `
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-4 shadow border border-gray-200">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-bold text-gray-900">${item.nama_siswa}</div>
                        <div class="text-xs text-gray-500">${item.nisn || '-'} • ${item.nama_kelas}</div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                        ${persen}%
                    </span>
                </div>
                <div class="grid grid-cols-4 gap-2 mb-3">
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Hadir</div>
                        <div class="font-semibold text-green-600">${item.total_hadir}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Sakit</div>
                        <div class="font-semibold text-yellow-600">${item.total_sakit}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Izin</div>
                        <div class="font-semibold text-blue-600">${item.total_izin}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Alpha</div>
                        <div class="font-semibold text-red-600">${item.total_alfa}</div>
                    </div>
                </div>
                <button onclick="showDetail(${item.id_siswa})" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    Lihat Detail
                </button>
            </div>
        `;
    }).join('');
}

async function showDetail(idSiswa) {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    try {
        const response = await fetch(`${BASE_URL}/waliKelas/getDetailAbsensi`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id_siswa: idSiswa,
                start_date: startDate,
                end_date: endDate
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            displayDetailModal(result.siswa_info, result.detail_data);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat detail');
    }
}

function displayDetailModal(siswaInfo, detailData) {
    document.getElementById('modal-student-info').innerHTML = `
        <div class="font-semibold">${siswaInfo.nama_siswa}</div>
        <div>NISN: ${siswaInfo.nisn || '-'} | Kelas: ${siswaInfo.nama_kelas}</div>
    `;
    
    const modalContent = document.getElementById('modal-content');
    
    if (!detailData || detailData.length === 0) {
        modalContent.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p>Tidak ada data absensi</p>
            </div>
        `;
        return;
    }
    
    modalContent.innerHTML = `
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-center font-semibold">Total</th>
                        <th class="px-4 py-3 text-center font-semibold">Hadir</th>
                        <th class="px-4 py-3 text-center font-semibold">Sakit</th>
                        <th class="px-4 py-3 text-center font-semibold">Izin</th>
                        <th class="px-4 py-3 text-center font-semibold">Alpha</th>
                        <th class="px-4 py-3 text-center font-semibold">%</th>
                    </tr>
                </thead>
                <tbody>
                    ${detailData.map(item => `
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium">${item.nama_mapel}</div>
                                <div class="text-xs text-gray-500">${item.nama_guru || '-'}</div>
                            </td>
                            <td class="px-4 py-3 text-center font-medium">${item.total_pertemuan || 0}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                    ${item.hadir || 0}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                    ${item.sakit || 0}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                    ${item.izin || 0}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                    ${item.alfa || 0}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold ${parseFloat(item.persentase_hadir) >= 75 ? 'text-green-600' : 'text-red-600'}">
                                    ${item.persentase_hadir || 0}%
                                </span>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    document.getElementById('detail-modal').classList.remove('hidden');
    
    // Reinitialize lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function getStatusClass(status) {
    const statusMap = {
        'Hadir': 'bg-green-100 text-green-800',
        'Sakit': 'bg-yellow-100 text-yellow-800',
        'Izin': 'bg-blue-100 text-blue-800',
        'Alpha': 'bg-red-100 text-red-800'
    };
    return statusMap[status] || 'bg-gray-100 text-gray-800';
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

function resetFilter() {
    document.getElementById('period-select').value = 'this_month';
    document.getElementById('sort-select').value = 'persentase_desc';
    document.getElementById('search-input').value = '';
    initializeDates();
    loadData();
}

function exportPDF() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    // Open PDF in new window
    const url = `${BASE_URL}/waliKelas/exportPDF?start_date=${startDate}&end_date=${endDate}`;
    window.open(url, '_blank');
}

// Search input handler
document.getElementById('search-input').addEventListener('input', function() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    if (document.getElementById('table-body').children.length > 0) {
        loadData();
    }
});

// Sort select handler
document.getElementById('sort-select').addEventListener('change', function() {
    if (document.getElementById('table-body').children.length > 0) {
        loadData();
    }
});
</script>

<style>
@media (max-width: 768px) {
    #table-container { display: none !important; }
    #card-container { display: block !important; }
}

@media (min-width: 769px) {
    #table-container { display: block !important; }
    #card-container { display: none !important; }
}
</style>
