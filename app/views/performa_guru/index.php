<?php
// File: app/views/performa_guru/index.php - CLEANED VERSION WITHOUT JENJANG FILTER
?>

<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 p-3 sm:p-6 space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent mb-2">
                    Performa Kinerja Guru
                </h1>
                <p class="text-gray-600 text-sm sm:text-base">Analisis kinerja guru berdasarkan jurnal pembelajaran dan penugasan</p>
            </div>
            <div class="mt-3 sm:mt-0">
                <span class="inline-block text-xs sm:text-sm text-blue-600 font-semibold bg-gradient-to-r from-blue-100 to-green-100 px-4 py-2 rounded-full border border-blue-200">
                    <?= $data['session_info']['nama_semester'] ?? 'Semester Tidak Diketahui'; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
            Filter & Pencarian
        </h2>
        
        <!-- Period and Date Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                <select id="period-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-green-500 focus:ring-0 transition-colors">
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month" selected>Bulan Ini</option>
                    <option value="this_semester">Semester Ini</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" id="start-date" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-green-500 focus:ring-0 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" id="end-date" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-green-500 focus:ring-0 transition-colors">
            </div>
        </div>
        
        <!-- Guru, Mapel, Kelas, Sort, and Search Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Guru</label>
                <select id="guru-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-green-500 focus:ring-0 transition-colors">
                    <option value="">Semua Guru</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                <select id="mapel-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-green-500 focus:ring-0 transition-colors">
                    <option value="">Semua Mapel</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select id="kelas-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-green-500 focus:ring-0 transition-colors">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
                <select id="sort-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-green-500 focus:ring-0 transition-colors">
                    <option value="total_jurnal_desc">📊 Terbanyak Jurnal</option>
                    <option value="total_jurnal_asc">📉 Tersedikit Jurnal</option>
                    <option value="total_hari_desc">📅 Terbanyak Hari Mengajar</option>
                    <option value="total_hari_asc">📅 Tersedikit Hari Mengajar</option>
                    <option value="total_penugasan_desc">📚 Terbanyak Penugasan</option>
                    <option value="total_penugasan_asc">📖 Tersedikit Penugasan</option>
                    <option value="nama_asc">🔤 Nama A-Z</option>
                    <option value="nama_desc">🔤 Nama Z-A</option>
                    <option value="jurnal_terakhir_desc">🕒 Jurnal Terbaru</option>
                    <option value="jurnal_terakhir_asc">🕒 Jurnal Terlama</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Guru</label>
                <div class="relative">
                    <input type="text" id="search-input" placeholder="Nama guru atau NIK..." 
                           class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 pl-10 focus:border-green-500 focus:ring-0 transition-colors">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                        🔍
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <button onclick="loadData()" class="flex-1 sm:flex-none bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]">
                📊 Tampilkan Data
            </button>
            <button onclick="exportData()" class="flex-1 sm:flex-none bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]">
                📄 Unduh PDF
            </button>
            <button onclick="resetFilter()" class="flex-1 sm:flex-none bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]">
                🔄 Reset
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Total Guru</div>
                    <div class="text-xl sm:text-3xl font-bold" id="total-guru">0</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">👨‍🏫</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Total Jurnal</div>
                    <div class="text-xl sm:text-3xl font-bold" id="total-jurnal">0</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">📋</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Guru Aktif</div>
                    <div class="text-xl sm:text-3xl font-bold" id="guru-aktif">0</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">✅</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs sm:text-sm opacity-90 mb-1">Rata Jurnal/Guru</div>
                    <div class="text-xl sm:text-3xl font-bold" id="rata-jurnal">0</div>
                </div>
                <div class="text-2xl sm:text-4xl opacity-80">📊</div>
            </div>
        </div>
    </div>

    <!-- Data Table/Cards -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                Data Performa Guru
            </h2>
        </div>
        
        <!-- Loading -->
        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-green-200 border-t-green-600 mb-4"></div>
            <div class="text-gray-600">Memuat data...</div>
        </div>

        <!-- Desktop Table -->
        <div id="table-container" class="hidden">
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">NIK</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Guru</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Penugasan</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Mapel</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Jurnal</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Hari</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Terakhir</th>
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
            <div class="text-6xl mb-4">👨‍🏫</div>
            <div class="text-xl font-medium mb-2">Tidak ada data</div>
            <div class="text-sm">Tidak ada data performa guru pada periode yang dipilih</div>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden text-center py-12 text-red-500">
            <div class="text-6xl mb-4">⚠️</div>
            <div class="text-xl font-medium mb-2">Terjadi kesalahan</div>
            <div class="text-sm mb-4" id="error-message"></div>
            <button onclick="loadData()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                Coba Lagi
            </button>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 p-2 sm:p-4 lg:p-8">
    <div class="flex items-center justify-center min-h-full">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-7xl max-h-[95vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-600 to-blue-600 px-4 sm:px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg sm:text-xl font-semibold">Detail Performa Guru</h3>
                        <p class="text-green-100 text-sm">Rincian penugasan dan jurnal pembelajaran</p>
                    </div>
                    <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 text-3xl font-light leading-none">&times;</button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(95vh-120px)]">
                <!-- Guru Info -->
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4" id="guru-info">
                        <!-- Guru info will be loaded here -->
                    </div>
                </div>

                <!-- Detail Loading -->
                <div id="detail-loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-green-200 border-t-green-600 mb-4"></div>
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
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Kelas</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Jurnal</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Hari</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Pertama</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Terakhir</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Aksi</th>
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
                    <div class="text-sm">Tidak ada data penugasan untuk guru ini pada periode yang dipilih</div>
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

<!-- Jurnal Modal -->
<div id="jurnal-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 p-2 sm:p-4 lg:p-8">
    <div class="flex items-center justify-center min-h-full">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 sm:px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg sm:text-xl font-semibold">Rincian Jurnal Pembelajaran</h3>
                        <p class="text-blue-100 text-sm" id="jurnal-subtitle">Detail jurnal per mata pelajaran dan kelas</p>
                    </div>
                    <button onclick="closeJurnalModal()" class="text-white hover:text-gray-200 text-3xl font-light leading-none">&times;</button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(95vh-120px)] p-4 sm:p-6">
                <!-- Jurnal Loading -->
                <div id="jurnal-loading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
                    <div class="text-gray-600">Memuat jurnal...</div>
                </div>

                <!-- Jurnal Content -->
                <div id="jurnal-content" class="hidden space-y-4">
                    <!-- Content will be loaded here -->
                </div>

                <!-- Jurnal Error -->
                <div id="jurnal-error" class="hidden text-center py-12 text-red-500">
                    <div class="text-6xl mb-4">⚠️</div>
                    <div class="text-xl font-medium mb-2">Terjadi kesalahan</div>
                    <div class="text-sm mb-4" id="jurnal-error-message"></div>
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
    loadGuru();
    loadMapel();
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

function loadGuru() {
    fetch('<?= BASEURL; ?>/PerformaGuru/getGuru')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const select = document.getElementById('guru-select');
            select.innerHTML = '<option value="">Semua Guru</option>';
            data.guru.forEach(guru => {
                const option = document.createElement('option');
                option.value = guru.id_guru;
                option.textContent = guru.nama_guru;
                select.appendChild(option);
            });
        }
    })
    .catch(error => console.error('Error loading guru:', error));
}

function loadMapel() {
    fetch('<?= BASEURL; ?>/PerformaGuru/getMapel')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const select = document.getElementById('mapel-select');
            select.innerHTML = '<option value="">Semua Mapel</option>';
            data.mapel.forEach(mapel => {
                const option = document.createElement('option');
                option.value = mapel.id_mapel;
                option.textContent = mapel.nama_mapel;
                select.appendChild(option);
            });
        }
    })
    .catch(error => console.error('Error loading mapel:', error));
}

function loadKelas() {
    fetch('<?= BASEURL; ?>/PerformaGuru/getKelas')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const select = document.getElementById('kelas-select');
            select.innerHTML = '<option value="">Semua Kelas</option>';
            data.kelas.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas.id_kelas;
                option.textContent = `${kelas.nama_kelas} (Kelas ${kelas.jenjang})`;
                select.appendChild(option);
            });
        }
    })
    .catch(error => console.error('Error loading kelas:', error));
}

function loadData() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const guruFilter = document.getElementById('guru-select').value;
    const mapelFilter = document.getElementById('mapel-select').value;
    const kelasFilter = document.getElementById('kelas-select').value;
    
    if (!startDate || !endDate) {
        alert('Pilih tanggal terlebih dahulu');
        return;
    }
    
    showLoading();
    
    fetch('<?= BASEURL; ?>/PerformaGuru/getData', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            start_date: startDate,
            end_date: endDate,
            guru_filter: guruFilter,
            mapel_filter: mapelFilter,
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
            case 'total_jurnal_desc':
                return parseInt(b.total_jurnal) - parseInt(a.total_jurnal);
            case 'total_jurnal_asc':
                return parseInt(a.total_jurnal) - parseInt(b.total_jurnal);
            case 'total_hari_desc':
                return parseInt(b.total_hari_mengajar) - parseInt(a.total_hari_mengajar);
            case 'total_hari_asc':
                return parseInt(a.total_hari_mengajar) - parseInt(b.total_hari_mengajar);
            case 'total_penugasan_desc':
                return parseInt(b.total_penugasan) - parseInt(a.total_penugasan);
            case 'total_penugasan_asc':
                return parseInt(a.total_penugasan) - parseInt(b.total_penugasan);
            case 'nama_asc':
                return a.nama_guru.localeCompare(b.nama_guru);
            case 'nama_desc':
                return b.nama_guru.localeCompare(a.nama_guru);
            case 'jurnal_terakhir_desc':
                return new Date(b.jurnal_terakhir || '1900-01-01') - new Date(a.jurnal_terakhir || '1900-01-01');
            case 'jurnal_terakhir_asc':
                return new Date(a.jurnal_terakhir || '9999-12-31') - new Date(b.jurnal_terakhir || '9999-12-31');
            default:
                return parseInt(b.total_jurnal) - parseInt(a.total_jurnal);
        }
    });
    
    showTable();
}

function filterData() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    
    if (!searchTerm) {
        currentData = [...allData];
    } else {
        currentData = allData.filter(guru => 
            (guru.nama_guru && guru.nama_guru.toLowerCase().includes(searchTerm)) ||
            (guru.nik && guru.nik.toLowerCase().includes(searchTerm))
        );
    }
    
    sortData();
    updateStats();
}

function resetFilter() {
    document.getElementById('period-select').value = 'this_month';
    document.getElementById('guru-select').value = '';
    document.getElementById('mapel-select').value = '';
    document.getElementById('kelas-select').value = '';
    document.getElementById('sort-select').value = 'total_jurnal_desc';
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
        const jurnalTerakhir = row.jurnal_terakhir ? new Date(row.jurnal_terakhir).toLocaleDateString('id-ID') : '-';
        
        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4 text-sm">${index + 1}</td>
                <td class="px-4 py-4 text-sm">${row.nik || '-'}</td>
                <td class="px-4 py-4 text-sm font-medium text-gray-900">${row.nama_guru}</td>
                <td class="px-4 py-4 text-sm text-center">${row.total_penugasan}</td>
                <td class="px-4 py-4 text-sm text-center text-blue-600 font-semibold">${row.total_mapel}</td>
                <td class="px-4 py-4 text-sm text-center text-purple-600 font-semibold">${row.total_kelas}</td>
                <td class="px-4 py-4 text-sm text-center text-green-600 font-bold">${row.total_jurnal}</td>
                <td class="px-4 py-4 text-sm text-center text-orange-600 font-semibold">${row.total_hari_mengajar}</td>
                <td class="px-4 py-4 text-sm text-center text-gray-600">${jurnalTerakhir}</td>
                <td class="px-4 py-4 text-sm text-center">
                    <button onclick="showDetail(${row.id_guru})" 
                            class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-[1.02]">
                        📋 Detail
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    // Mobile cards
    const mobileCards = document.getElementById('mobile-cards');
    mobileCards.innerHTML = currentData.map((row, index) => {
        const jurnalTerakhir = row.jurnal_terakhir ? new Date(row.jurnal_terakhir).toLocaleDateString('id-ID') : 'Belum ada';
        
        return `
            <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-bold text-gray-900 text-base">${row.nama_guru}</div>
                        <div class="text-sm text-gray-600">NIK: ${row.nik || '-'}</div>
                        <div class="text-xs text-gray-500">Jurnal terakhir: ${jurnalTerakhir}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-green-600">${row.total_jurnal}</div>
                        <div class="text-xs text-green-700 font-medium">Jurnal</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-4 gap-2 text-center text-xs mb-3">
                    <div class="bg-white rounded-lg p-2">
                        <div class="font-semibold text-gray-600">Penugasan</div>
                        <div class="text-gray-800 font-bold">${row.total_penugasan}</div>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-2">
                        <div class="font-semibold text-blue-700">Mapel</div>
                        <div class="text-blue-800 font-bold">${row.total_mapel}</div>
                    </div>
                    <div class="bg-purple-100 rounded-lg p-2">
                        <div class="font-semibold text-purple-700">Kelas</div>
                        <div class="text-purple-800 font-bold">${row.total_kelas}</div>
                    </div>
                    <div class="bg-orange-100 rounded-lg p-2">
                        <div class="font-semibold text-orange-700">Hari</div>
                        <div class="text-orange-800 font-bold">${row.total_hari_mengajar}</div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button onclick="showDetail(${row.id_guru})" 
                            class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all duration-200">
                        📋 Detail
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function showDetail(id_guru) {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const kelasFilter = document.getElementById('kelas-select').value;
    
    document.getElementById('detail-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    document.getElementById('detail-loading').classList.remove('hidden');
    document.getElementById('detail-table-container').classList.add('hidden');
    document.getElementById('detail-empty').classList.add('hidden');
    document.getElementById('detail-error').classList.add('hidden');
    
    fetch('<?= BASEURL; ?>/PerformaGuru/getDetail', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id_guru: id_guru,
            start_date: startDate,
            end_date: endDate,
            kelas_filter: kelasFilter
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('detail-loading').classList.add('hidden');
        
        if (data.status === 'success') {
            showDetailData(data.guru_info, data.detail_data);
        } else {
            showDetailError(data.message || 'Gagal memuat detail');
        }
    })
    .catch(error => {
        document.getElementById('detail-loading').classList.add('hidden');
        showDetailError('Terjadi kesalahan: ' + error.message);
    });
}

function showDetailData(guruInfo, detailData) {
    const guruInfoDiv = document.getElementById('guru-info');
    guruInfoDiv.innerHTML = `
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">👨‍🏫 Nama Guru</div>
            <div class="font-bold text-gray-800">${guruInfo.nama_guru || '-'}</div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">🆔 NIK</div>
            <div class="font-bold text-gray-800">${guruInfo.nik || '-'}</div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">📚 Total Mapel</div>
            <div class="font-bold text-gray-800">${guruInfo.total_mapel_diampu || '0'}</div>
        </div>
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500 mb-1">🏫 Total Kelas</div>
            <div class="font-bold text-gray-800">${guruInfo.total_kelas_diampu || '0'}</div>
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
        const jurnalPertama = row.jurnal_pertama ? new Date(row.jurnal_pertama).toLocaleDateString('id-ID') : '-';
        const jurnalTerakhir = row.jurnal_terakhir ? new Date(row.jurnal_terakhir).toLocaleDateString('id-ID') : '-';
        
        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4 text-sm">${index + 1}</td>
                <td class="px-4 py-4 text-sm">
                    <div class="font-medium text-gray-900">${row.nama_mapel}</div>
                    <div class="text-xs text-gray-500">${row.kode_mapel}</div>
                </td>
                <td class="px-4 py-4 text-sm text-gray-700">
                    <div class="font-medium">${row.nama_kelas}</div>
                    <div class="text-xs text-gray-500">Kelas ${row.jenjang}</div>
                </td>
                <td class="px-4 py-4 text-sm text-center text-green-600 font-bold">${row.total_jurnal}</td>
                <td class="px-4 py-4 text-sm text-center text-orange-600 font-semibold">${row.total_hari_mengajar}</td>
                <td class="px-4 py-4 text-sm text-center text-gray-600">${jurnalPertama}</td>
                <td class="px-4 py-4 text-sm text-center text-gray-600">${jurnalTerakhir}</td>
                <td class="px-4 py-4 text-sm text-center">
                    <button onclick="showJurnalDetail(${row.id_penugasan}, '${row.nama_mapel}', '${row.nama_kelas}')" 
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-3 py-1 rounded-lg text-xs font-medium transition-all duration-200">
                        📖 Jurnal
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    // Mobile detail cards
    const mobileDetailCards = document.getElementById('detail-mobile-cards');
    mobileDetailCards.innerHTML = detailData.map((row, index) => {
        const jurnalPertama = row.jurnal_pertama ? new Date(row.jurnal_pertama).toLocaleDateString('id-ID') : 'Belum ada';
        const jurnalTerakhir = row.jurnal_terakhir ? new Date(row.jurnal_terakhir).toLocaleDateString('id-ID') : 'Belum ada';
        
        return `
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 border border-gray-200 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="font-bold text-gray-900 text-sm">${row.nama_mapel}</div>
                        <div class="text-xs text-gray-600">${row.kode_mapel}</div>
                        <div class="text-xs text-gray-600 mt-1">🏫 ${row.nama_kelas} (Kelas ${row.jenjang})</div>
                    </div>
                    <div class="text-right ml-3">
                        <div class="text-xl font-bold text-green-600">${row.total_jurnal}</div>
                        <div class="text-xs text-green-700 font-medium">Jurnal</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-center text-xs mb-3">
                    <div class="bg-white rounded p-2">
                        <div class="font-semibold text-gray-600 text-xs">Hari</div>
                        <div class="text-gray-800 font-bold">${row.total_hari_mengajar}</div>
                    </div>
                    <div class="bg-blue-100 rounded p-2">
                        <div class="font-semibold text-blue-700 text-xs">Periode</div>
                        <div class="text-blue-800 text-xs">${jurnalPertama} - ${jurnalTerakhir}</div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button onclick="showJurnalDetail(${row.id_penugasan}, '${row.nama_mapel}', '${row.nama_kelas}')" 
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all duration-200">
                        📖 Lihat Jurnal
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function showJurnalDetail(id_penugasan, nama_mapel, nama_kelas) {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    document.getElementById('jurnal-modal').classList.remove('hidden');
    document.getElementById('jurnal-subtitle').textContent = `${nama_mapel} - ${nama_kelas}`;
    
    document.getElementById('jurnal-loading').classList.remove('hidden');
    document.getElementById('jurnal-content').classList.add('hidden');
    document.getElementById('jurnal-error').classList.add('hidden');
    
    fetch('<?= BASEURL; ?>/PerformaGuru/getJurnalDetail', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id_penugasan: id_penugasan,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('jurnal-loading').classList.add('hidden');
        
        if (data.status === 'success') {
            showJurnalData(data.jurnal_data);
        } else {
            showJurnalError(data.message || 'Gagal memuat jurnal');
        }
    })
    .catch(error => {
        document.getElementById('jurnal-loading').classList.add('hidden');
        showJurnalError('Terjadi kesalahan: ' + error.message);
    });
}

function showJurnalData(jurnalData) {
    document.getElementById('jurnal-content').classList.remove('hidden');
    
    const jurnalContent = document.getElementById('jurnal-content');
    
    if (jurnalData.length === 0) {
        jurnalContent.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <div class="text-6xl mb-4">📚</div>
                <div class="text-xl font-medium mb-2">Tidak ada jurnal</div>
                <div class="text-sm">Belum ada jurnal yang dibuat untuk mata pelajaran ini pada periode yang dipilih</div>
            </div>
        `;
        return;
    }
    
    jurnalContent.innerHTML = jurnalData.map((jurnal, index) => {
        const tanggal = new Date(jurnal.tanggal).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const waktuInput = new Date(jurnal.timestamp).toLocaleString('id-ID');
        
        // Status kehadiran berdasarkan persentase
        const persentase = parseFloat(jurnal.persentase_hadir);
        let statusClass, statusText;
        if (persentase >= 90) {
            statusClass = 'bg-green-100 text-green-800';
            statusText = 'Sangat Baik';
        } else if (persentase >= 80) {
            statusClass = 'bg-blue-100 text-blue-800';
            statusText = 'Baik';
        } else if (persentase >= 70) {
            statusClass = 'bg-yellow-100 text-yellow-800';
            statusText = 'Cukup';
        } else {
            statusClass = 'bg-red-100 text-red-800';
            statusText = 'Perlu Perhatian';
        }
        
        return `
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 border border-blue-200">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-bold text-gray-900">Pertemuan ke-${jurnal.pertemuan_ke}</div>
                        <div class="text-sm text-gray-600">${tanggal}</div>
                        <div class="text-xs text-gray-500">Input: ${waktuInput}</div>
                    </div>
                    <div class="text-right">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                            ${jurnal.nama_mapel}
                        </span>
                        <div class="mt-2">
                            <span class="${statusClass} px-2 py-1 rounded-full text-xs font-medium">
                                ${persentase.toFixed(1)}% - ${statusText}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Statistik Kehadiran -->
                <div class="mb-4 bg-white rounded-lg p-3 border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        📊 Statistik Kehadiran Siswa
                    </div>
                    <div class="grid grid-cols-5 gap-2 text-center text-xs">
                        <div class="bg-gray-100 rounded p-2">
                            <div class="font-semibold text-gray-600">Total</div>
                            <div class="text-gray-800 font-bold text-lg">${jurnal.total_siswa}</div>
                        </div>
                        <div class="bg-green-100 rounded p-2">
                            <div class="font-semibold text-green-700">Hadir</div>
                            <div class="text-green-800 font-bold text-lg">${jurnal.hadir}</div>
                        </div>
                        <div class="bg-orange-100 rounded p-2">
                            <div class="font-semibold text-orange-700">Sakit</div>
                            <div class="text-orange-800 font-bold text-lg">${jurnal.sakit}</div>
                        </div>
                        <div class="bg-blue-100 rounded p-2">
                            <div class="font-semibold text-blue-700">Izin</div>
                            <div class="text-blue-800 font-bold text-lg">${jurnal.izin}</div>
                        </div>
                        <div class="bg-red-100 rounded p-2">
                            <div class="font-semibold text-red-700">Alpha</div>
                            <div class="text-red-800 font-bold text-lg">${jurnal.alfa}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="text-sm font-semibold text-gray-700 mb-1">📝 Topik Materi:</div>
                    <div class="text-gray-800 bg-white rounded p-3 border">${jurnal.topik_materi || 'Tidak ada topik'}</div>
                </div>
                
                ${jurnal.catatan ? `
                <div>
                    <div class="text-sm font-semibold text-gray-700 mb-1">💭 Catatan:</div>
                    <div class="text-gray-700 bg-yellow-50 rounded p-3 border border-yellow-200">${jurnal.catatan}</div>
                </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

function showJurnalError(message) {
    document.getElementById('jurnal-error').classList.remove('hidden');
    document.getElementById('jurnal-error-message').textContent = message;
}

function showDetailError(message) {
    document.getElementById('detail-error').classList.remove('hidden');
    document.getElementById('detail-error-message').textContent = message;
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function closeJurnalModal() {
    document.getElementById('jurnal-modal').classList.add('hidden');
}

document.getElementById('detail-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetailModal();
    }
});

document.getElementById('jurnal-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeJurnalModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('jurnal-modal').classList.contains('hidden')) {
            closeJurnalModal();
        } else if (!document.getElementById('detail-modal').classList.contains('hidden')) {
            closeDetailModal();
        }
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
        document.getElementById('total-guru').textContent = '0';
        document.getElementById('total-jurnal').textContent = '0';
        document.getElementById('guru-aktif').textContent = '0';
        document.getElementById('rata-jurnal').textContent = '0';
        return;
    }
    
    const totalGuru = currentData.length;
    const totalJurnal = currentData.reduce((sum, g) => sum + parseInt(g.total_jurnal), 0);
    const guruAktif = currentData.filter(g => parseInt(g.total_jurnal) > 0).length;
    const rataJurnal = totalGuru > 0 ? (totalJurnal / totalGuru).toFixed(1) : '0';
    
    document.getElementById('total-guru').textContent = totalGuru;
    document.getElementById('total-jurnal').textContent = totalJurnal;
    document.getElementById('guru-aktif').textContent = guruAktif;
    document.getElementById('rata-jurnal').textContent = rataJurnal;
}

function exportData() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const guruFilter = document.getElementById('guru-select').value;
    const mapelFilter = document.getElementById('mapel-select').value;
    const kelasFilter = document.getElementById('kelas-select').value;
    
    if (!startDate || !endDate) {
        alert('Pilih tanggal terlebih dahulu');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= BASEURL; ?>/PerformaGuru/exportPdf';
    
    const fields = { 
        start_date: startDate, 
        end_date: endDate, 
        guru_filter: guruFilter,
        mapel_filter: mapelFilter,
        kelas_filter: kelasFilter
    };
    
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

* {
    transition: all 0.2s ease-in-out;
}

input:focus, select:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

@media (max-width: 640px) {
    .min-h-screen {
        padding: 0.75rem;
    }
    
    #detail-modal .bg-white, #jurnal-modal .bg-white {
        margin: 0.5rem;
        max-height: calc(100vh - 1rem);
    }
}

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #059669, #047857);
}

button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>