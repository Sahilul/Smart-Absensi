<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">
                                <i class="fas fa-chart-line text-primary"></i> Monitoring Nilai
                            </h4>
                            <p class="text-muted mb-0">
                                <i class="fas fa-school"></i> Kelas: <strong><?= htmlspecialchars($data['nama_kelas']) ?></strong> | 
                                <i class="fas fa-calendar"></i> <?= htmlspecialchars($data['session_info']['nama_semester']) ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button class="btn btn-success" onclick="exportPDF()">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Sort By -->
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-sort"></i> Urutkan Berdasarkan
                            </label>
                            <select class="form-select" id="sortBy">
                                <option value="nama">Nama Siswa (A-Z)</option>
                                <option value="rata_rata_desc">Nilai Tertinggi</option>
                                <option value="rata_rata_asc">Nilai Terendah</option>
                                <option value="nisn">NISN</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-search"></i> Cari Siswa
                            </label>
                            <input type="text" class="form-control" id="searchInput" 
                                placeholder="Cari berdasarkan nama atau NISN...">
                        </div>

                        <!-- Button -->
                        <div class="col-md-2">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="loadData()">
                                <i class="fas fa-sync-alt"></i> Muat Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4" id="statsCards" style="display: none;">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Siswa</h6>
                            <h3 class="mb-0" id="totalSiswa">0</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Rata-rata Kelas</h6>
                            <h3 class="mb-0" id="rataKelas">0</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Nilai Tertinggi</h6>
                            <h3 class="mb-0" id="nilaiMax">0</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Nilai Terendah</h6>
                            <h3 class="mb-0" id="nilaiMin">0</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="loadingIndicator" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>

                    <div id="dataContainer" style="display: none;">
                        <!-- Desktop View -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover" id="nilaiTable">
                                <thead class="table-light">
                                    <tr id="nilaiHeadRow">
                                        <!-- Header kolom akan di-generate via JS -->
                                    </tr>
                                </thead>
                                <tbody id="nilaiTableBody">
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="d-md-none" id="nilaiCards">
                        </div>
                    </div>

                    <div id="emptyState" style="display: none;" class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada data yang ditemukan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge-nilai {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    font-weight: 600;
}

/* Basic table cosmetics to avoid dependency on Bootstrap */
#nilaiTable {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
    font-size: 12px;
    white-space: nowrap;
}
#nilaiTable thead th {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 2;
}
#nilaiTable th, #nilaiTable td {
    padding: 6px 8px;
    border-bottom: 1px solid #f1f5f9;
}
#nilaiTable tbody tr:hover { background: #f9fafb; }

/* Badge base so Tailwind bg-* classes work nicely */
.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 6px;
}

.card-siswa {
    border-left: 4px solid #0d6efd;
    margin-bottom: 1rem;
    transition: all 0.3s;
}

.card-siswa:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.mapel-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 0.5rem;
}
.mapel-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.5rem;
}

.mapel-item {
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 0.25rem;
    text-align: center;
}

#nilaiTable-wrapper, .table-responsive {
    overflow-x: auto;
}
.mapel-item small {
    display: block;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.mapel-item .nilai {
    font-size: 1.1rem;
    font-weight: bold;
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
    document.getElementById('statsCards').style.display = 'none';

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
            })
        });

        const result = await response.json();
        
        if (result.status === 'success') {
            currentData = result.data || [];
            currentMapelList = result.mapel_list || [];
            document.getElementById('emptyState').style.display = currentData.length ? 'none' : 'block';
            displayData(currentData, currentMapelList);
            updateStats(currentData);
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
function displayData(data, mapelList) {
    if (!data || data.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
        return;
    }

    // Bangun header tabel lengkap
    const headHtml = [
        '<th style="width: 60px; position:sticky; left:0; background:#f8fafc; z-index:3;">No</th>',
        '<th style="width: 120px; position:sticky; left:60px; background:#f8fafc; z-index:3;">NISN</th>',
        '<th style="min-width: 200px; text-align: left; position:sticky; left:180px; background:#f8fafc; z-index:3;">Nama Siswa</th>'
    ]
    .concat(mapelList.map(m => `<th class="text-center">${m}</th>`))
    .concat(['<th style="width: 120px;">Rata-rata</th>'])
    .join('');
    document.getElementById('nilaiHeadRow').innerHTML = headHtml;

    // Table body
    const tableBody = data.map((siswa, index) => {
        // Buat peta nama_mapel -> nilai
        const mapelMap = {};
        (siswa.mapel || []).forEach(m => { mapelMap[m.nama_mapel] = m.nilai; });
        // Render sesuai urutan mapelList agar sejajar dengan header
        const mapelCells = mapelList.map(nama => {
            const val = Object.prototype.hasOwnProperty.call(mapelMap, nama) ? mapelMap[nama] : null;
            const nilaiTampil = (val !== null && val !== undefined) ? formatNilai(val) : '-';
            const badgeClass = getNilaiBadgeClass(val);
            return `<td class="text-center"><span class="badge ${badgeClass}">${nilaiTampil}</span></td>`;
        }).join('');

        const rataRataVal = (siswa.rata_rata !== null && siswa.rata_rata !== undefined) ? siswa.rata_rata : null;
        const rataRata = rataRataVal !== null ? formatNilai(rataRataVal) : '-';
        const rataClass = getNilaiBadgeClass(rataRataVal);

        return `
            <tr>
                <td style="position:sticky; left:0; background:#ffffff; z-index:1;">${index + 1}</td>
                <td style="position:sticky; left:60px; background:#ffffff; z-index:1;">${siswa.nisn}</td>
                <td class="text-start" style="position:sticky; left:180px; background:#ffffff; z-index:1;">${siswa.nama_siswa}</td>
                ${mapelCells}
                <td class="text-center"><span class="badge ${rataClass} badge-nilai">${rataRata}</span></td>
            </tr>
        `;
    }).join('');
    document.getElementById('nilaiTableBody').innerHTML = tableBody;

    // Mobile cards
    const cards = data.map((siswa, index) => {
        const mapelMap = {};
        (siswa.mapel || []).forEach(m => { mapelMap[m.nama_mapel] = m.nilai; });
        const mapelItems = mapelList.map(nama => {
            const val = Object.prototype.hasOwnProperty.call(mapelMap, nama) ? mapelMap[nama] : null;
            const nilaiTampil = (val !== null && val !== undefined) ? formatNilai(val) : '-';
            const badgeClass = getNilaiBadgeClass(val);
            return `
                <div class="mapel-item">
                    <small>${nama}</small>
                    <div class="nilai"><span class="badge ${badgeClass}">${nilaiTampil}</span></div>
                </div>
            `;
        }).join('');

        const rataRataVal = (siswa.rata_rata !== null && siswa.rata_rata !== undefined) ? siswa.rata_rata : null;
        const rataRata = rataRataVal !== null ? formatNilai(rataRataVal) : '-';
        const rataClass = getNilaiBadgeClass(rataRataVal);

        return `
            <div class="card card-siswa">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1">${siswa.nama_siswa}</h6>
                            <small class="text-muted">NISN: ${siswa.nisn}</small>
                        </div>
                        <div class="text-end">
                            <small class="d-block text-muted">Rata-rata</small>
                            <span class="badge ${rataClass} badge-nilai">${rataRata}</span>
                        </div>
                    </div>
                    <div class="mapel-grid">
                        ${mapelItems}
                    </div>
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
    
    document.getElementById('statsCards').style.display = 'flex';
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
            case 'rata_rata_desc':
                return (b.rata_rata || 0) - (a.rata_rata || 0);
            case 'rata_rata_asc':
                return (a.rata_rata || 0) - (b.rata_rata || 0);
            default:
                return 0;
        }
    });
    
    // Re-render menggunakan currentMapelList agar tetap sejajar
    displayData(filtered, currentMapelList);
}

// Export PDF
function exportPDF() {
    const jenisNilai = document.getElementById('jenisNilai').value;
    window.open(`<?= BASEURL ?>/waliKelas/exportPDFNilai?jenis=${jenisNilai}`, '_blank');
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterAndSort);
document.getElementById('sortBy').addEventListener('change', filterAndSort);
document.getElementById('jenisNilai').addEventListener('change', loadData);

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    loadData();
});
</script>
