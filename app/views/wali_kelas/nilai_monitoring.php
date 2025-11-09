<main class="flex-1 overflow-x-hidden overflow-y-auto bg-secondary-50 p-4 md:p-6">
    <!-- Header -->
    <div class="mb-4">
        <div class="bg-white shadow-sm rounded-xl p-4 md:p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h4 class="text-lg font-semibold text-slate-800 mb-1">Monitoring Nilai</h4>
                    <p class="text-slate-500 text-sm">
                        Kelas: <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['nama_kelas']) ?></span>
                        <span class="mx-2">•</span>
                        Semester: <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['session_info']['nama_semester']) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-4">
        <div class="bg-white shadow-sm rounded-xl p-4 md:p-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Urutkan</label>
                    <select class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all" id="sortBy">
                        <option value="nama">Nama Siswa (A-Z)</option>
                        <option value="nisn">NISN</option>
                    </select>
                </div>
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Cari Siswa</label>
                    <input type="text" id="searchInput" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all" placeholder="Cari berdasarkan nama atau NISN..." />
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4" id="statsCards" style="display:none;">
        <div class="bg-gradient-to-br from-sky-500 to-sky-600 text-white rounded-xl shadow p-4">
            <div class="text-sm opacity-90 mb-1">Total Siswa</div>
            <div class="text-2xl font-bold" id="totalSiswa">0</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-xl shadow p-4">
            <div class="text-sm opacity-90 mb-1">Rata-rata Kelas</div>
            <div class="text-2xl font-bold" id="rataKelas">0</div>
        </div>
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-xl shadow p-4">
            <div class="text-sm opacity-90 mb-1">Nilai Tertinggi</div>
            <div class="text-2xl font-bold" id="nilaiMax">0</div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-xl shadow p-4">
            <div class="text-sm opacity-90 mb-1">Nilai Terendah</div>
            <div class="text-2xl font-bold" id="nilaiMin">0</div>
        </div>
    </div>

    <!-- Data Table -->
    <div>
        <div class="bg-white shadow-sm rounded-xl overflow-hidden">
            <div id="loadingIndicator" class="text-center py-10">
                <svg class="animate-spin h-6 w-6 text-sky-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                <p class="mt-2 text-slate-500 text-sm">Memuat data...</p>
            </div>

            <div id="dataContainer" style="display:none;">
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-700" id="nilaiTable">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr id="nilaiHeadRow"></tr>
                        </thead>
                        <tbody id="nilaiTableBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
                <!-- Mobile Cards -->
                <div class="lg:hidden p-4 space-y-3" id="nilaiCards"></div>
            </div>

            <div id="emptyState" style="display:none;" class="text-center py-10 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m16 0v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6M3 17h18"/></svg>
                <p class="text-sm">Tidak ada data yang ditemukan</p>
            </div>
        </div>
    </div>
</main>

<!-- Modal Detail Nilai -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" onclick="closeModal(event)">
    <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-sky-600 to-sky-700 px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-white text-lg font-bold" id="modalNamaSiswa">Detail Nilai</h3>
                <p class="text-sky-100 text-sm mt-0.5" id="modalNISN"></p>
            </div>
            <button onclick="closeModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
            <!-- Stats Summary -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4">
                    <div class="text-emerald-600 text-xs font-semibold mb-1">Nilai Tertinggi</div>
                    <div class="text-emerald-900 text-2xl font-bold" id="modalNilaiMax">-</div>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg p-4">
                    <div class="text-amber-600 text-xs font-semibold mb-1">Nilai Terendah</div>
                    <div class="text-amber-900 text-2xl font-bold" id="modalNilaiMin">-</div>
                </div>
                <div class="bg-gradient-to-br from-sky-50 to-sky-100 rounded-lg p-4">
                    <div class="text-sky-600 text-xs font-semibold mb-1">Rata-rata</div>
                    <div class="text-sky-900 text-2xl font-bold" id="modalRataRata">-</div>
                </div>
            </div>

            <!-- Mapel List -->
            <h4 class="text-gray-700 font-semibold mb-3 text-sm">Daftar Nilai Per Mata Pelajaran</h4>
            <div class="grid gap-3" id="modalMapelList">
                <!-- Will be populated by JS -->
            </div>
        </div>
    </div>
</div>

<style>
/* Table styling */
#nilaiTable {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
    font-size: 14px;
    white-space: nowrap;
}
#nilaiTable thead th {
    background: #f8fafc;
    border-bottom: 2px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 2;
    padding: 12px 10px;
    font-weight: 600;
    font-size: 14px;
}
#nilaiTable th, #nilaiTable td {
    padding: 10px 8px;
    border-bottom: 1px solid #f1f5f9;
}
#nilaiTable tbody tr:hover { 
    background: #f9fafb; 
}
.table-responsive {
    overflow-x: auto;
}

/* Badge styling */
.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    padding: 4px 10px;
    border-radius: 6px;
    min-width: 50px;
}

/* Mobile card styling */
.card-siswa {
    border-left: 4px solid #0ea5e9;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s;
}
.card-siswa:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

/* Responsive button sizing */
@media (max-width: 1024px) {
    .btn-detail {
        font-size: 12px;
        padding: 6px 10px;
    }
}

@media (min-width: 1024px) {
    .btn-detail {
        font-size: 13px;
        padding: 8px 12px;
    }
}
</style>

<script>
let currentData = [];
const idKelas = <?= $data['id_kelas'] ?>;

// Load data siswa
async function loadData() {
    document.getElementById('loadingIndicator').style.display = 'block';
    document.getElementById('dataContainer').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';

    try {
        const response = await fetch('<?= BASEURL ?>/waliKelas/getDaftarSiswa', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_kelas: idKelas
            })
        });

        const result = await response.json();
        
        if (result.status === 'success') {
            currentData = result.data || [];
            document.getElementById('emptyState').style.display = currentData.length ? 'none' : 'block';
            filterAndSort();
        } else {
            alert('Error: ' + result.message);
            document.getElementById('emptyState').style.display = 'block';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal memuat data');
        document.getElementById('emptyState').style.display = 'block';
    }

    document.getElementById('loadingIndicator').style.display = 'none';
}

// Display data
function displayData(data) {
    if (!data || data.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
        return;
    }

    // Header tabel
    const headHtml = [
        '<th style="width: 60px;">No</th>',
        '<th style="width: 150px;">NISN</th>',
        '<th style="text-align: left; min-width: 250px;">Nama Siswa</th>',
        '<th style="min-width: 400px; text-align: center;">Aksi</th>'
    ].join('');
    document.getElementById('nilaiHeadRow').innerHTML = headHtml;

    // Table body
    const tableBody = data.map((siswa, index) => {
        return `
            <tr>
                <td class="text-center font-medium">${index + 1}</td>
                <td class="font-medium">${siswa.nisn}</td>
                <td class="font-medium">${siswa.nama_siswa}</td>
                <td class="text-center">
                    <div class="inline-flex flex-wrap gap-2 justify-center">
                        <button onclick="showDetail(${siswa.id_siswa}, 'harian')" class="btn-detail inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Harian
                        </button>
                        <button onclick="showDetail(${siswa.id_siswa}, 'sts')" class="btn-detail inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            STS
                        </button>
                        <button onclick="showDetail(${siswa.id_siswa}, 'sas')" class="btn-detail inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            SAS
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    document.getElementById('nilaiTableBody').innerHTML = tableBody;

    // Mobile cards
    const cards = data.map((siswa, index) => {
        return `
            <div class="card-siswa p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h6 class="text-base font-bold text-slate-800">${siswa.nama_siswa}</h6>
                        <p class="text-slate-500 text-sm mt-1">NISN: ${siswa.nisn}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <button onclick="showDetail(${siswa.id_siswa}, 'harian')" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Nilai Harian
                    </button>
                    <button onclick="showDetail(${siswa.id_siswa}, 'sts')" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Nilai STS
                    </button>
                    <button onclick="showDetail(${siswa.id_siswa}, 'sas')" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Nilai SAS
                    </button>
                </div>
            </div>
        `;
    }).join('');
    document.getElementById('nilaiCards').innerHTML = cards;

    document.getElementById('dataContainer').style.display = 'block';
}

// Get badge class berdasarkan nilai
function getNilaiBadgeClass(nilai) {
    if (nilai === null || nilai === '-') return 'bg-gray-400';
    if (nilai >= 85) return 'bg-green-600';
    if (nilai >= 70) return 'bg-blue-600';
    if (nilai >= 60) return 'bg-yellow-500';
    return 'bg-red-600';
}

// Format angka nilai ke 2 desimal
function formatNilai(n) {
    const num = parseFloat(n);
    if (Number.isNaN(num)) return '-';
    return (Math.round(num * 100) / 100).toFixed(2);
}

// Update statistik
function updateStats(data) {
    const totalSiswa = data.length;
    const nilaiList = data.map(s => s.rata_rata).filter(n => n !== null);
    
    const rataKelas = nilaiList.length > 0 
        ? (nilaiList.reduce((a, b) => a + b, 0) / nilaiList.length).toFixed(2)
        : 0;
    
    const nilaiMax = nilaiList.length > 0 ? Math.max(...nilaiList).toFixed(2) : 0;
    const nilaiMin = nilaiList.length > 0 ? Math.min(...nilaiList).toFixed(2) : 0;

    document.getElementById('totalSiswa').textContent = totalSiswa;
    document.getElementById('rataKelas').textContent = rataKelas;
    document.getElementById('nilaiMax').textContent = nilaiMax;
    document.getElementById('nilaiMin').textContent = nilaiMin;
    
    document.getElementById('statsCards').style.display = 'grid';
}

// Filter dan sort
function filterAndSort() {
    let filtered = [...currentData];
    
    // Search
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    if (searchTerm) {
        filtered = filtered.filter(siswa => 
            siswa.nama_siswa.toLowerCase().includes(searchTerm) ||
            siswa.nisn.includes(searchTerm)
        );
    }
    
    // Sort
    const sortBy = document.getElementById('sortBy').value;
    filtered.sort((a, b) => {
        switch(sortBy) {
            case 'nama':
                return a.nama_siswa.localeCompare(b.nama_siswa);
            case 'nisn':
                return a.nisn.localeCompare(b.nisn);
            default:
                return 0;
        }
    });
    
    displayData(filtered);
}

// Show detail modal
async function showDetail(idSiswa, jenisNilai) {
    // Find siswa data
    const siswa = currentData.find(s => s.id_siswa == idSiswa);
    if (!siswa) return;

    // Show loading state
    document.getElementById('modalNamaSiswa').textContent = 'Memuat...';
    document.getElementById('modalNISN').textContent = `NISN: ${siswa.nisn}`;
    document.getElementById('modalMapelList').innerHTML = '<div class="text-center py-4 text-slate-500">Memuat data...</div>';
    document.getElementById('detailModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        // Fetch nilai data
        const response = await fetch('<?= BASEURL ?>/waliKelas/getNilaiSiswa', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_siswa: idSiswa,
                jenis_nilai: jenisNilai
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            
            // Populate student info with jenis nilai
            const jenisLabel = jenisNilai === 'harian' ? 'Nilai Harian' : jenisNilai === 'sts' ? 'Nilai STS' : 'Nilai SAS';
            document.getElementById('modalNamaSiswa').textContent = `${siswa.nama_siswa} - ${jenisLabel}`;

            // Calculate stats
            const nilaiList = (data.mapel || []).map(m => m.nilai).filter(n => n !== null && n !== undefined);
            const nilaiMax = nilaiList.length > 0 ? Math.max(...nilaiList) : 0;
            const nilaiMin = nilaiList.length > 0 ? Math.min(...nilaiList) : 0;
            const rataRata = data.rata_rata || 0;

            document.getElementById('modalNilaiMax').textContent = formatNilai(nilaiMax);
            document.getElementById('modalNilaiMin').textContent = formatNilai(nilaiMin);
            document.getElementById('modalRataRata').textContent = formatNilai(rataRata);

            // Populate mapel list
            const mapelListHtml = (data.mapel || []).map(m => {
                const nilaiTampil = (m.nilai !== null && m.nilai !== undefined) ? formatNilai(m.nilai) : '-';
                const badgeClass = getNilaiBadgeClass(m.nilai);

                return `
                    <div class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-lg p-3 transition">
                        <span class="text-gray-700 font-medium text-sm">${m.nama_mapel}</span>
                        <span class="badge ${badgeClass} text-sm">${nilaiTampil}</span>
                    </div>
                `;
            }).join('');

            document.getElementById('modalMapelList').innerHTML = mapelListHtml || '<div class="text-center py-4 text-slate-500">Tidak ada data nilai</div>';
        } else {
            alert('Gagal memuat data: ' + result.message);
            closeModal();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal memuat data nilai');
        closeModal();
    }
}

// Close modal
function closeModal(event) {
    if (!event || event.target.id === 'detailModal') {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterAndSort);
document.getElementById('sortBy').addEventListener('change', filterAndSort);

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    loadData();
});
</script>
