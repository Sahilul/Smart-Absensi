<!-- admin/naik_kelas.php - DIPERBAIKI -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Proses Kenaikan Kelas</h2>
            <p class="text-gray-600 mt-1">Kelola perpindahan siswa ke jenjang atau tahun ajaran berikutnya</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?= BASEURL; ?>/admin/kelas" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Active Semester Info -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <span class="font-medium">Semester Aktif:</span> 
                    <?= $_SESSION['nama_semester_aktif'] ?? 'Belum ada sesi aktif'; ?><br>
                    <span class="text-xs">Proses kenaikan kelas akan memindahkan siswa ke tahun ajaran dan kelas yang dipilih.</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php Flasher::flash(); ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Siswa Terpilih</p>
                    <p class="text-xl font-semibold text-gray-900" id="selectedCount">0</p>
                    <p class="text-xs text-gray-500">Untuk diproses</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <i data-lucide="school" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tahun Ajaran</p>
                    <p class="text-xl font-semibold text-gray-900"><?= count($data['daftar_tp']); ?></p>
                    <p class="text-xs text-gray-500">Tersedia</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                    <i data-lucide="arrow-up-right" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-xl font-semibold text-gray-900" id="processStatus">Siap</p>
                    <p class="text-xs text-gray-500">Proses</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-orange-100 p-2 rounded-lg mr-3">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Mode</p>
                    <p class="text-xl font-semibold text-gray-900">Naik Kelas</p>
                    <p class="text-xs text-gray-500">Operasi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <form action="<?= BASEURL; ?>/admin/prosesNaikKelas" method="POST">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
            <!-- Kelas Asal Card -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="arrow-left" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Kelas Asal</h3>
                            <p class="text-xs text-gray-600">Pilih siswa yang akan naik kelas</p>
                        </div>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-6">
                    <!-- Form Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                Tahun Ajaran Asal
                            </label>
                            <select name="id_tp_asal" id="tp_asal" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach ($data['daftar_tp'] as $tp) : ?>
                                    <option value="<?= $tp['id_tp']; ?>"><?= htmlspecialchars($tp['nama_tp']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="school" class="w-4 h-4 inline mr-1"></i>
                                Kelas Asal
                            </label>
                            <select name="id_kelas_asal" id="kelas_asal" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih TP Dulu --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Search and Control Bar -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3 mb-4">
                        <div class="flex-1">
                            <input type="text" id="searchSiswa" placeholder="Cari nama atau NISN siswa..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="flex items-center text-sm text-gray-600">
                                <input type="checkbox" id="select_all" class="mr-2 rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                                Pilih Semua
                            </label>
                        </div>
                    </div>

                    <!-- Student List -->
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Daftar Siswa</span>
                                <span class="text-xs text-gray-500" id="siswaCount">0 siswa</span>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-12">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Nama Siswa
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            NISN
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="daftar_siswa_asal" class="divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i data-lucide="user-search" class="w-8 h-8 text-gray-400 mb-2"></i>
                                                <span class="text-sm">Pilih tahun ajaran dan kelas untuk melihat daftar siswa</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kelas Tujuan Card -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="arrow-right" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Kelas Tujuan</h3>
                            <p class="text-xs text-gray-600">Tentukan kelas tujuan untuk siswa</p>
                        </div>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="p-6">
                    <!-- Form Selection -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                Tahun Ajaran Tujuan
                            </label>
                            <select name="id_tp_tujuan" id="tp_tujuan" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach ($data['daftar_tp'] as $tp) : ?>
                                    <option value="<?= $tp['id_tp']; ?>"><?= htmlspecialchars($tp['nama_tp']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="school" class="w-4 h-4 inline mr-1"></i>
                                Kelas Tujuan
                            </label>
                            <select name="id_kelas_tujuan" id="kelas_tujuan" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">-- Pilih TP Dulu --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Process Summary -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-800">Peringatan Penting</h4>
                                <div class="text-sm text-yellow-700 mt-1">
                                    <p>Proses kenaikan kelas akan:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-xs">
                                        <li>Memindahkan siswa ke kelas dan tahun ajaran baru</li>
                                        <li>Menghapus keanggotaan dari kelas lama</li>
                                        <li>Proses tidak dapat dibatalkan setelah dieksekusi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Process Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-800 mb-3 flex items-center">
                            <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                            Ringkasan Proses
                        </h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <span class="w-24 text-xs text-gray-500">Dari:</span>
                                <span id="fromInfo" class="text-gray-800">Belum dipilih</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-24 text-xs text-gray-500">Ke:</span>
                                <span id="toInfo" class="text-gray-800">Belum dipilih</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-24 text-xs text-gray-500">Siswa:</span>
                                <span id="studentsInfo" class="text-gray-800">0 terpilih</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Process Action Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Konfirmasi dan Proses</h3>
            </div>
            <div class="p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">
                            Pastikan semua data sudah benar sebelum memproses kenaikan kelas.
                            <span class="font-medium text-orange-600">Proses ini tidak dapat dibatalkan.</span>
                        </p>
                    </div>
                    <button type="submit" id="processBtn" disabled
                            class="bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium py-2 px-6 rounded-lg flex items-center transition-colors duration-200 shadow-sm"
                            onclick="return confirm('⚠️ KONFIRMASI KENAIKAN KELAS\n\nAnda akan memproses kenaikan kelas untuk siswa yang terpilih.\n\nTindakan ini tidak dapat dibatalkan!\n\nYakin ingin melanjutkan?');">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 mr-2"></i>
                        Proses Naik Kelas
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Footer Info -->
    <div class="mt-6 text-center text-sm text-gray-600">
        <p>💡 <strong>Tips:</strong> Pastikan kelas tujuan sudah tersedia sebelum memproses kenaikan kelas</p>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Element references
    const tpAsalSelect = document.getElementById('tp_asal');
    const kelasAsalSelect = document.getElementById('kelas_asal');
    const tpTujuanSelect = document.getElementById('tp_tujuan');
    const kelasTujuanSelect = document.getElementById('kelas_tujuan');
    const daftarSiswaTbody = document.getElementById('daftar_siswa_asal');
    const selectAllCheckbox = document.getElementById('select_all');
    const selectedCount = document.getElementById('selectedCount');
    const processBtn = document.getElementById('processBtn');
    const fromInfo = document.getElementById('fromInfo');
    const toInfo = document.getElementById('toInfo');
    const studentsInfo = document.getElementById('studentsInfo');
    const processStatus = document.getElementById('processStatus');
    const searchInput = document.getElementById('searchSiswa');
    const resetBtn = document.getElementById('resetSelection');
    const siswaCountSpan = document.getElementById('siswaCount');

    let allStudents = []; // Store all students for search functionality

    // Load classes by academic year
    function loadKelas(selectElement, id_tp, focusColor = 'blue') {
        if (!id_tp) {
            selectElement.innerHTML = '<option value="">-- Pilih TP Dulu --</option>';
            return;
        }

        selectElement.innerHTML = '<option value="">Memuat...</option>';
        selectElement.classList.add('opacity-50');

        fetch(`<?= BASEURL; ?>/admin/getKelasByTP/${id_tp}`)
            .then(response => response.json())
            .then(data => {
                selectElement.innerHTML = '<option value="">-- Pilih Kelas --</option>';
                data.forEach(kelas => {
                    selectElement.innerHTML += `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                });
                selectElement.classList.remove('opacity-50');
                updateProcessInfo();
            })
            .catch(error => {
                console.error('Error loading classes:', error);
                selectElement.innerHTML = '<option value="">Error memuat data</option>';
                selectElement.classList.remove('opacity-50');
            });
    }

    // Load students by class
    function loadSiswa(id_kelas, id_tp) {
        if (!id_kelas || !id_tp) {
            daftarSiswaTbody.innerHTML = `
                <tr>
                    <td colspan="2" class="p-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i data-lucide="user-search" class="w-8 h-8 text-gray-400 mb-2"></i>
                            <span class="text-sm">Pilih tahun ajaran dan kelas untuk melihat daftar siswa</span>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        daftarSiswaTbody.innerHTML = `
            <tr>
                <td colspan="2" class="p-8 text-center">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                        <span class="text-sm text-gray-600">Memuat daftar siswa...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch(`<?= BASEURL; ?>/admin/getSiswaByKelas/${id_kelas}/${id_tp}`)
            .then(response => response.json())
            .then(data => {
                daftarSiswaTbody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach((siswa, index) => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50 transition-colors duration-150';
                        row.innerHTML = `
                            <td class="p-3 text-center w-12">
                                <input type="checkbox" name="siswa_terpilih[]" value="${siswa.id_siswa}" 
                                       class="siswa-checkbox rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                            </td>
                            <td class="p-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-xs font-semibold text-gray-600">${index + 1}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">${siswa.nama_siswa}</div>
                                        <div class="text-xs text-gray-500">ID: ${siswa.id_siswa}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3">
                                <div class="text-sm text-gray-900">${siswa.nisn || '-'}</div>
                                <div class="text-xs text-gray-500">NISN</div>
                            </td>
                        `;
                        row.dataset.nama = siswa.nama_siswa.toLowerCase();
                        row.dataset.nisn = (siswa.nisn || '').toLowerCase();
                        daftarSiswaTbody.appendChild(row);
                    });
                    
                    // Add event listeners to checkboxes
                    addCheckboxListeners();
                } else {
                    daftarSiswaTbody.innerHTML = `
                        <tr>
                            <td colspan="2" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="users-x" class="w-8 h-8 text-gray-400 mb-2"></i>
                                    <span class="text-sm">Tidak ada siswa di kelas ini</span>
                                </div>
                            </td>
                        </tr>
                    `;
                }
                updateStudentCount();
                updateProcessInfo();
                lucide.createIcons(); // Reinitialize icons
            })
            .catch(error => {
                console.error('Error loading students:', error);
                daftarSiswaTbody.innerHTML = `
                    <tr>
                        <td colspan="2" class="p-8 text-center text-red-500">
                            <div class="flex flex-col items-center">
                                <i data-lucide="alert-circle" class="w-8 h-8 text-red-400 mb-2"></i>
                                <span class="text-sm">Error memuat data siswa</span>
                            </div>
                        </td>
                    </tr>
                `;
                lucide.createIcons();
            });
    }

    // Add event listeners to checkboxes
    function addCheckboxListeners() {
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateStudentCount);
        });
    }

    // Update student count and enable/disable process button
    function updateStudentCount() {
        const checkboxes = document.querySelectorAll('.siswa-checkbox:checked');
        const count = checkboxes.length;
        
        selectedCount.textContent = count;
        studentsInfo.textContent = `${count} terpilih`;
        
        // Update process button state
        const hasSelection = count > 0;
        const hasDestination = tpTujuanSelect.value && kelasTujuanSelect.value;
        
        processBtn.disabled = !(hasSelection && hasDestination);
        
        if (hasSelection && hasDestination) {
            processStatus.textContent = 'Siap';
            processStatus.className = 'text-xl font-semibold text-green-600';
        } else {
            processStatus.textContent = 'Menunggu';
            processStatus.className = 'text-xl font-semibold text-gray-900';
        }
    }

    // Update process information display
    function updateProcessInfo() {
        // Update source info
        const tpAsalText = tpAsalSelect.options[tpAsalSelect.selectedIndex]?.text || 'Belum dipilih';
        const kelasAsalText = kelasAsalSelect.options[kelasAsalSelect.selectedIndex]?.text || '';
        fromInfo.textContent = kelasAsalText ? `${kelasAsalText} (${tpAsalText})` : tpAsalText;

        // Update destination info
        const tpTujuanText = tpTujuanSelect.options[tpTujuanSelect.selectedIndex]?.text || 'Belum dipilih';
        const kelasTujuanText = kelasTujuanSelect.options[kelasTujuanSelect.selectedIndex]?.text || '';
        toInfo.textContent = kelasTujuanText ? `${kelasTujuanText} (${tpTujuanText})` : tpTujuanText;

        updateStudentCount();
    }

    // Event listeners for academic year selects
    tpAsalSelect.addEventListener('change', function() {
        loadKelas(kelasAsalSelect, this.value, 'blue');
        // Reset student list when changing academic year
        daftarSiswaTbody.innerHTML = `
            <tr>
                <td colspan="3" class="p-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i data-lucide="user-search" class="w-8 h-8 text-gray-400 mb-2"></i>
                        <span class="text-sm">Pilih kelas untuk melihat daftar siswa</span>
                    </div>
                </td>
            </tr>
        `;
        lucide.createIcons();
        updateProcessInfo();
    });

    tpTujuanSelect.addEventListener('change', function() {
        loadKelas(kelasTujuanSelect, this.value, 'green');
        updateProcessInfo();
    });

    // Event listeners for class selects
    kelasAsalSelect.addEventListener('change', function() {
        const id_tp = tpAsalSelect.value;
        loadSiswa(this.value, id_tp);
        updateProcessInfo();
    });

    kelasTujuanSelect.addEventListener('change', updateProcessInfo);

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        const visibleCheckboxes = document.querySelectorAll('.siswa-checkbox:not([style*="display: none"])');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateStudentCount();
    });

    // Search functionality
    searchInput.addEventListener('input', filterStudents);

    // Reset button functionality
    resetBtn.addEventListener('click', resetSelections);

    // Initial state
    updateProcessInfo();
});
</script>