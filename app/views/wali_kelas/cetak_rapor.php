<main class="flex-1 overflow-x-hidden overflow-y-auto bg-secondary-50 p-4 md:p-6">
    <!-- Header -->
    <div class="mb-4">
        <div class="bg-white shadow-sm rounded-xl p-4 md:p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex-1">
                    <h4 class="text-lg md:text-xl font-bold text-slate-800 mb-1">
                        <?= $data['judul'] ?>
                    </h4>
                    <p class="text-slate-500 text-sm">
                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['nama_kelas']) ?></span>
                        <span class="mx-2">•</span>
                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['session_info']['nama_semester']) ?></span>
                    </p>
                </div>
                <!-- Tombol Cetak All -->
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <button onclick="cetakAllRapor('sts')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span class="hidden sm:inline">Cetak STS All</span>
                        <span class="sm:hidden">STS All</span>
                    </button>
                    <button onclick="cetakAllRapor('sas')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span class="hidden sm:inline">Cetak SAS All</span>
                        <span class="sm:hidden">SAS All</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
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

    <!-- Data Table -->
    <div>
        <div class="bg-white shadow-sm rounded-xl overflow-hidden">
            <div id="loadingIndicator" class="text-center py-10">
                <svg class="animate-spin h-6 w-6 text-sky-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                <p class="mt-2 text-slate-500 text-sm">Memuat data siswa...</p>
            </div>

            <div id="dataContainer" style="display:none;">
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full text-sm text-slate-700" id="siswaTable">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-center" style="width: 60px;">No</th>
                                <th class="px-4 py-3 text-left" style="width: 150px;">NISN</th>
                                <th class="px-4 py-3 text-left">Nama Siswa</th>
                                <th class="px-4 py-3 text-center" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="siswaTableBody" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
                <!-- Mobile Cards -->
                <div class="lg:hidden p-4 space-y-3" id="siswaCards"></div>
            </div>

            <div id="emptyState" style="display:none;" class="text-center py-10 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m16 0v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6M3 17h18"/></svg>
                <p class="text-sm">Tidak ada data siswa</p>
            </div>
        </div>
    </div>
</main>

<!-- Loading Popup untuk Generate PDF -->
<div id="pdfLoadingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 animate-fade-in">
        <div class="text-center">
            <!-- Animated Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full animate-pulse">
                    <svg class="w-10 h-10 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Title -->
            <h3 class="text-2xl font-bold text-slate-800 mb-2">
                Memproses PDF...
            </h3>
            
            <!-- Description -->
            <div class="text-slate-600 mb-4 leading-relaxed" id="loadingMessage">
                Sedang membuat file PDF rapor
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-slate-200 rounded-full h-2.5 mb-4">
                <div class="bg-blue-600 h-2.5 rounded-full animate-progress" style="width: 0%; animation: progress 3s ease-in-out infinite;"></div>
            </div>
            
            <!-- Info Text -->
            <p class="text-sm text-slate-500 mb-4">
                📄 Jangan tutup halaman ini sampai PDF selesai dibuat...
            </p>
            
            <!-- Manual Close Button -->
            <button onclick="hideLoadingModal()" class="mt-2 px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                Tutup Jendela Ini
            </button>
            
            <p class="text-xs text-slate-400 mt-3">
                💡 Popup akan otomatis tertutup saat PDF selesai
            </p>
        </div>
    </div>
</div>

<style>
@keyframes progress {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 95%; }
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
    transition: all 0.3s ease-out;
}

#siswaTable {
    width: 100%;
    border-collapse: collapse;
}
#siswaTable thead th {
    background: #f8fafc;
    border-bottom: 2px solid #e5e7eb;
    padding: 12px 10px;
    font-weight: 600;
    font-size: 14px;
}
#siswaTable td {
    padding: 12px 10px;
    border-bottom: 1px solid #f1f5f9;
}
#siswaTable tbody tr:hover {
    background: #f9fafb;
}

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

    // Table body
    const tableBody = data.map((siswa, index) => {
        return `
            <tr>
                <td class="text-center font-medium">${index + 1}</td>
                <td class="font-medium">${siswa.nisn}</td>
                <td class="font-medium">${siswa.nama_siswa}</td>
                <td class="text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="cetakRapor('sts', ${siswa.id_siswa})" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            STS
                        </button>
                        <button onclick="cetakRapor('sas', ${siswa.id_siswa})" class="inline-flex items-center gap-1.5 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            SAS
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    document.getElementById('siswaTableBody').innerHTML = tableBody;

    // Mobile cards
    const cards = data.map((siswa, index) => {
        return `
            <div class="card-siswa p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="flex-shrink-0 w-7 h-7 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold">${index + 1}</span>
                            <h6 class="text-sm font-bold text-slate-800 truncate">${siswa.nama_siswa}</h6>
                        </div>
                        <p class="text-slate-500 text-xs mt-1">NISN: ${siswa.nisn}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="cetakRapor('sts', ${siswa.id_siswa})" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        STS
                    </button>
                    <button onclick="cetakRapor('sas', ${siswa.id_siswa})" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        SAS
                    </button>
                </div>
            </div>
        `;
    }).join('');
    document.getElementById('siswaCards').innerHTML = cards;

    document.getElementById('dataContainer').style.display = 'block';
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

// Cetak rapor per siswa
async function cetakRapor(jenisRapor, idSiswa) {
    // Tampilkan loading modal
    showLoadingModal(`Membuat rapor ${jenisRapor.toUpperCase()} untuk siswa...`);
    
    try {
        // Fetch PDF via AJAX
        const response = await fetch(`<?= BASEURL ?>/waliKelas/generateRapor/${jenisRapor}/${idSiswa}`);
        
        if (!response.ok) {
            throw new Error('Gagal membuat PDF');
        }
        
        // Convert response to blob
        const blob = await response.blob();
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        
        // Get filename from response header or create default
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = `Rapor_${jenisRapor.toUpperCase()}_${idSiswa}.pdf`;
        
        if (contentDisposition) {
            // Try multiple parsing methods
            // Method 1: filename*=UTF-8''...
            const utf8Match = contentDisposition.match(/filename\*=UTF-8''([^;]+)/i);
            if (utf8Match) {
                filename = decodeURIComponent(utf8Match[1]);
            } else {
                // Method 2: filename="..."
                const quoteMatch = contentDisposition.match(/filename="([^"]+)"/i);
                if (quoteMatch) {
                    filename = quoteMatch[1];
                } else {
                    // Method 3: filename=... (no quotes)
                    const noQuoteMatch = contentDisposition.match(/filename=([^;]+)/i);
                    if (noQuoteMatch) {
                        filename = noQuoteMatch[1].trim();
                    }
                }
            }
        }
        
        // Ensure .pdf extension
        if (!filename.toLowerCase().endsWith('.pdf')) {
            filename = filename.replace(/\.pdf_.*/i, '.pdf'); // Fix .pdf_ issue
            if (!filename.toLowerCase().endsWith('.pdf')) {
                filename += '.pdf';
            }
        }
        
        a.download = filename;
        
        // Trigger download
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Hide loading setelah sukses
        setTimeout(() => {
            hideLoadingModal();
            showSuccessToast('PDF berhasil dibuat!');
        }, 1000);
        
    } catch (error) {
        console.error('Error:', error);
        hideLoadingModal();
        alert('❌ Gagal membuat PDF!\n\nSilakan coba lagi atau hubungi administrator.');
    }
}

// Cetak rapor semua siswa
async function cetakAllRapor(jenisRapor) {
    if (!confirm(`Anda akan mencetak rapor ${jenisRapor.toUpperCase()} untuk semua siswa.\n\nProses ini mungkin memakan waktu beberapa saat tergantung jumlah siswa.\n\nLanjutkan?`)) {
        return;
    }
    
    // Hitung estimasi waktu berdasarkan jumlah siswa
    const jumlahSiswa = currentData.length; // Fix: gunakan currentData bukan allData
    const estimasiDetik = Math.ceil(jumlahSiswa * 0.8); // ~0.8 detik per siswa
    const estimasiMenit = Math.ceil(estimasiDetik / 60);
    
    // Tampilkan loading modal dengan estimasi waktu
    const message = `Membuat rapor ${jenisRapor.toUpperCase()} untuk ${jumlahSiswa} siswa...<br><br>⏱️ Estimasi waktu: ${estimasiMenit > 0 ? estimasiMenit + ' menit' : estimasiDetik + ' detik'}`;
    showLoadingModal(message);
    
    try {
        // Fetch PDF via AJAX
        const response = await fetch(`<?= BASEURL ?>/waliKelas/generateRaporAll/${jenisRapor}`);
        
        if (!response.ok) {
            throw new Error('Gagal membuat PDF');
        }
        
        // Convert response to blob
        const blob = await response.blob();
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        
        // Get filename from response header
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = `Rapor_${jenisRapor.toUpperCase()}_Semua.pdf`;
        
        if (contentDisposition) {
            // Try multiple parsing methods
            // Method 1: filename*=UTF-8''...
            const utf8Match = contentDisposition.match(/filename\*=UTF-8''([^;]+)/i);
            if (utf8Match) {
                filename = decodeURIComponent(utf8Match[1]);
            } else {
                // Method 2: filename="..."
                const quoteMatch = contentDisposition.match(/filename="([^"]+)"/i);
                if (quoteMatch) {
                    filename = quoteMatch[1];
                } else {
                    // Method 3: filename=... (no quotes)
                    const noQuoteMatch = contentDisposition.match(/filename=([^;]+)/i);
                    if (noQuoteMatch) {
                        filename = noQuoteMatch[1].trim();
                    }
                }
            }
        }
        
        // Ensure .pdf extension
        if (!filename.toLowerCase().endsWith('.pdf')) {
            filename = filename.replace(/\.pdf_.*/i, '.pdf'); // Fix .pdf_ issue
            if (!filename.toLowerCase().endsWith('.pdf')) {
                filename += '.pdf';
            }
        }
        
        a.download = filename;
        
        // Trigger download
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Hide loading setelah sukses
        setTimeout(() => {
            hideLoadingModal();
            showSuccessToast(`PDF berhasil dibuat untuk ${jumlahSiswa} siswa!`);
        }, 1000);
        
    } catch (error) {
        console.error('Error:', error);
        hideLoadingModal();
        alert('❌ Gagal membuat PDF!\n\nSilakan coba lagi atau hubungi administrator.');
    }
}

// Show loading modal
function showLoadingModal(message = 'Sedang membuat file PDF rapor') {
    const modal = document.getElementById('pdfLoadingModal');
    const messageEl = document.getElementById('loadingMessage');
    
    // Format message dengan line breaks
    messageEl.innerHTML = message;
    modal.style.display = 'flex';
}

// Hide loading modal
function hideLoadingModal() {
    const modal = document.getElementById('pdfLoadingModal');
    modal.style.display = 'none';
}

// Show success toast notification
function showSuccessToast(message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 animate-slide-up';
    toast.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-semibold">${message}</span>
    `;
    
    // Add to body
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', filterAndSort);
document.getElementById('sortBy').addEventListener('change', filterAndSort);

// Load data saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    loadData();
});
</script>
