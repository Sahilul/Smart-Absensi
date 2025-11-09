<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    
    <!-- Header dengan Search dan Filter -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-secondary-800 mb-2">Riwayat Absensi Harian</h2>
                <p class="text-secondary-600">
                    Menampilkan riwayat untuk sesi: <span class="font-semibold text-primary-600"><?= $_SESSION['nama_semester_aktif']; ?></span>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="exportToCSV()" class="btn-secondary flex items-center justify-center gap-2 px-4 py-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV
                </button>
                <button onclick="window.print()" class="btn-primary flex items-center justify-center gap-2 px-4 py-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Filter dan Search Controls -->
    <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
            
            <!-- Search Box -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-secondary-700 mb-2">
                    <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                    Cari Mata Pelajaran atau Guru
                </label>
                <input type="text" id="searchInput" placeholder="Ketik untuk mencari..." 
                       class="input-modern w-full text-sm">
            </div>
            
            <!-- Filter Status -->
            <div>
                <label class="block text-sm font-medium text-secondary-700 mb-2">
                    <i data-lucide="filter" class="w-4 h-4 inline mr-1"></i>
                    Status
                </label>
                <select id="statusFilter" class="input-modern w-full text-sm">
                    <option value="">Semua Status</option>
                    <option value="H">Hadir</option>
                    <option value="I">Izin</option>
                    <option value="S">Sakit</option>
                    <option value="A">Alpha</option>
                </select>
            </div>
            
            <!-- Filter Bulan -->
            <div>
                <label class="block text-sm font-medium text-secondary-700 mb-2">
                    <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                    Bulan
                </label>
                <select id="monthFilter" class="input-modern w-full text-sm">
                    <option value="">Semua Bulan</option>
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            
            <!-- Reset Button -->
            <div>
                <button onclick="resetFilters()" class="btn-secondary w-full flex items-center justify-center gap-2 px-4 py-2 text-sm">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Reset
                </button>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-white/20">
            <div class="text-center">
                <div class="text-2xl font-bold text-success-600" id="totalHadir">0</div>
                <div class="text-sm text-secondary-500">Total Hadir</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600" id="totalIzin">0</div>
                <div class="text-sm text-secondary-500">Total Izin</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600" id="totalSakit">0</div>
                <div class="text-sm text-secondary-500">Total Sakit</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600" id="totalAlpha">0</div>
                <div class="text-sm text-secondary-500">Total Alpha</div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden">
        
        <!-- Loading State -->
        <div id="loadingState" class="hidden p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full mb-4">
                <i data-lucide="loader-2" class="w-8 h-8 text-primary-600 animate-spin"></i>
            </div>
            <p class="text-secondary-600">Memuat data...</p>
        </div>

        <!-- Table Header -->
        <div class="bg-gradient-to-r from-secondary-50 to-secondary-100 px-6 py-4 border-b border-white/20">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-secondary-800">
                    Data Absensi Harian
                </h3>
                <div class="flex items-center gap-4 text-sm text-secondary-600">
                    <span id="totalRecords">Total: 0 data</span>
                    <span id="filteredRecords" class="hidden">Ditampilkan: 0 data</span>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-secondary-200" id="absensiTable">
                <thead class="bg-secondary-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-secondary-600 uppercase tracking-wider cursor-pointer hover:bg-secondary-100 transition-colors" 
                            onclick="sortTable(0)">
                            <div class="flex items-center gap-2">
                                Tanggal
                                <i data-lucide="chevrons-up-down" class="w-4 h-4"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-secondary-600 uppercase tracking-wider">
                            Mata Pelajaran
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-secondary-600 uppercase tracking-wider">
                            Guru Pengajar
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-secondary-600 uppercase tracking-wider cursor-pointer hover:bg-secondary-100 transition-colors" 
                            onclick="sortTable(3)">
                            <div class="flex items-center justify-center gap-2">
                                Status
                                <i data-lucide="chevrons-up-down" class="w-4 h-4"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-secondary-600 uppercase tracking-wider">
                            Keterangan
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-secondary-100" id="tableBody">
                    <?php if (empty($data['absensi_harian'])) : ?>
                        <tr id="emptyState">
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center mb-4">
                                        <i data-lucide="calendar-x" class="w-8 h-8 text-secondary-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-secondary-800 mb-2">Belum Ada Data Absensi</h3>
                                    <p class="text-secondary-500">Belum ada data absensi untuk sesi ini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($data['absensi_harian'] as $index => $absen) : ?>
                            <tr class="hover:bg-secondary-50 transition-colors duration-200 animate-fade-in" style="animation-delay: <?= $index * 50; ?>ms" data-row>
                                <td class="px-6 py-4 text-sm font-medium text-secondary-800" data-date="<?= $absen['tanggal']; ?>">
                                    <div class="flex flex-col">
                                        <span class="font-semibold"><?= date('d M Y', strtotime($absen['tanggal'])); ?></span>
                                        <span class="text-xs text-secondary-500"><?= date('l', strtotime($absen['tanggal'])); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-secondary-800" data-mapel="<?= strtolower($absen['nama_mapel']); ?>">
                                    <div class="font-medium"><?= htmlspecialchars($absen['nama_mapel']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-secondary-700" data-guru="<?= strtolower($absen['nama_guru']); ?>">
                                    <?= htmlspecialchars($absen['nama_guru']); ?>
                                </td>
                                <td class="px-6 py-4 text-center" data-status="<?= $absen['status_kehadiran']; ?>">
                                    <?php 
                                    $statusConfig = [
                                        'H' => ['bg-success-100 text-success-800', 'check-circle', 'Hadir'],
                                        'I' => ['bg-blue-100 text-blue-800', 'clock', 'Izin'],
                                        'S' => ['bg-yellow-100 text-yellow-800', 'thermometer', 'Sakit'],
                                        'A' => ['bg-red-100 text-red-800', 'x-circle', 'Alpha']
                                    ];
                                    $config = $statusConfig[$absen['status_kehadiran']] ?? ['bg-gray-100 text-gray-800', 'help-circle', 'Unknown'];
                                    ?>
                                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-xs font-semibold <?= $config[0]; ?>">
                                        <i data-lucide="<?= $config[1]; ?>" class="w-3 h-3"></i>
                                        <?= $config[2]; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-secondary-700">
                                    <?= !empty($absen['keterangan']) ? htmlspecialchars($absen['keterangan']) : '<span class="text-secondary-400 italic">Tidak ada keterangan</span>'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination (jika diperlukan) -->
        <div class="bg-secondary-50/50 px-6 py-4 border-t border-white/20">
            <div class="flex items-center justify-between text-sm text-secondary-600">
                <div id="paginationInfo">
                    Menampilkan semua data
                </div>
                <div class="flex items-center gap-2">
                    <span>Urutkan:</span>
                    <select id="sortBy" class="text-xs border border-secondary-200 rounded px-2 py-1 bg-white">
                        <option value="date-desc">Terbaru</option>
                        <option value="date-asc">Terlama</option>
                        <option value="mapel-asc">Mata Pelajaran A-Z</option>
                        <option value="status-asc">Status A-Z</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk filtering dan statistik
    let allRows = Array.from(document.querySelectorAll('tbody tr[data-row]'));
    let filteredRows = [...allRows];
    
    // Inisialisasi
    updateStatistics();
    updateRecordCount();
    
    // Event listeners
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('monthFilter').addEventListener('change', filterTable);
    document.getElementById('sortBy').addEventListener('change', handleSort);
    
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const monthFilter = document.getElementById('monthFilter').value;
        
        filteredRows = allRows.filter(row => {
            // Search filter
            const mapel = row.querySelector('[data-mapel]')?.dataset.mapel || '';
            const guru = row.querySelector('[data-guru]')?.dataset.guru || '';
            const searchMatch = !searchTerm || mapel.includes(searchTerm) || guru.includes(searchTerm);
            
            // Status filter
            const status = row.querySelector('[data-status]')?.dataset.status || '';
            const statusMatch = !statusFilter || status === statusFilter;
            
            // Month filter
            const date = row.querySelector('[data-date]')?.dataset.date || '';
            const month = date.split('-')[1];
            const monthMatch = !monthFilter || month === monthFilter;
            
            return searchMatch && statusMatch && monthMatch;
        });
        
        // Hide/show rows
        allRows.forEach(row => {
            row.style.display = filteredRows.includes(row) ? '' : 'none';
        });
        
        updateStatistics();
        updateRecordCount();
        
        // Show/hide empty state
        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.style.display = filteredRows.length === 0 ? '' : 'none';
        }
    }
    
    function updateStatistics() {
        let stats = { H: 0, I: 0, S: 0, A: 0 };
        
        filteredRows.forEach(row => {
            const status = row.querySelector('[data-status]')?.dataset.status;
            if (stats.hasOwnProperty(status)) {
                stats[status]++;
            }
        });
        
        document.getElementById('totalHadir').textContent = stats.H;
        document.getElementById('totalIzin').textContent = stats.I;
        document.getElementById('totalSakit').textContent = stats.S;
        document.getElementById('totalAlpha').textContent = stats.A;
    }
    
    function updateRecordCount() {
        const total = allRows.length;
        const filtered = filteredRows.length;
        
        document.getElementById('totalRecords').textContent = `Total: ${total} data`;
        
        const filteredElement = document.getElementById('filteredRecords');
        if (filtered !== total) {
            filteredElement.textContent = `Ditampilkan: ${filtered} data`;
            filteredElement.classList.remove('hidden');
        } else {
            filteredElement.classList.add('hidden');
        }
    }
    
    function handleSort() {
        const sortBy = document.getElementById('sortBy').value;
        const tbody = document.querySelector('#tableBody');
        
        let sortedRows = [...filteredRows];
        
        switch(sortBy) {
            case 'date-desc':
                sortedRows.sort((a, b) => {
                    const dateA = new Date(a.querySelector('[data-date]')?.dataset.date);
                    const dateB = new Date(b.querySelector('[data-date]')?.dataset.date);
                    return dateB - dateA;
                });
                break;
            case 'date-asc':
                sortedRows.sort((a, b) => {
                    const dateA = new Date(a.querySelector('[data-date]')?.dataset.date);
                    const dateB = new Date(b.querySelector('[data-date]')?.dataset.date);
                    return dateA - dateB;
                });
                break;
            case 'mapel-asc':
                sortedRows.sort((a, b) => {
                    const mapelA = a.querySelector('[data-mapel]')?.dataset.mapel || '';
                    const mapelB = b.querySelector('[data-mapel]')?.dataset.mapel || '';
                    return mapelA.localeCompare(mapelB);
                });
                break;
            case 'status-asc':
                sortedRows.sort((a, b) => {
                    const statusA = a.querySelector('[data-status]')?.dataset.status || '';
                    const statusB = b.querySelector('[data-status]')?.dataset.status || '';
                    return statusA.localeCompare(statusB);
                });
                break;
        }
        
        // Reorder dalam DOM
        sortedRows.forEach(row => {
            tbody.appendChild(row);
        });
    }
    
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('monthFilter').value = '';
        document.getElementById('sortBy').value = 'date-desc';
        filterTable();
        handleSort();
    }
    
    function sortTable(columnIndex) {
        const table = document.getElementById('absensiTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr[data-row]'));
        
        // Toggle sort direction
        let ascending = table.dataset.sortDir !== 'asc';
        table.dataset.sortDir = ascending ? 'asc' : 'desc';
        
        rows.sort((a, b) => {
            let aVal, bVal;
            
            if (columnIndex === 0) { // Date
                aVal = new Date(a.querySelector('[data-date]').dataset.date);
                bVal = new Date(b.querySelector('[data-date]').dataset.date);
            } else if (columnIndex === 3) { // Status
                aVal = a.querySelector('[data-status]').dataset.status;
                bVal = b.querySelector('[data-status]').dataset.status;
            }
            
            if (aVal < bVal) return ascending ? -1 : 1;
            if (aVal > bVal) return ascending ? 1 : -1;
            return 0;
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }
    
    // Export to CSV
    window.exportToCSV = function() {
        const headers = ['Tanggal', 'Mata Pelajaran', 'Guru', 'Status', 'Keterangan'];
        const csvContent = [
            headers.join(','),
            ...filteredRows.map(row => {
                const date = row.querySelector('[data-date]').dataset.date;
                const mapel = row.cells[1].textContent.trim();
                const guru = row.cells[2].textContent.trim();
                const status = row.querySelector('[data-status]').dataset.status;
                const keterangan = row.cells[4].textContent.trim();
                return `"${date}","${mapel}","${guru}","${status}","${keterangan}"`;
            })
        ].join('\n');
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `absensi_harian_${new Date().getFullYear()}_${new Date().getMonth() + 1}.csv`;
        link.click();
    };
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
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
    .glass-effect:first-child,
    .glass-effect:nth-child(2) {
        display: none !important;
    }
    
    .glass-effect:last-child {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    
    .bg-gradient-to-br {
        background: white !important;
    }
    
    .hover\:bg-secondary-50:hover {
        background: transparent !important;
    }
}
</style>