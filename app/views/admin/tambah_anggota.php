<!-- admin/tambah_anggota.php - DIPERBAIKI -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="flex items-center mb-6">
        <a href="<?= BASEURL; ?>/admin/keanggotaan" class="text-gray-500 hover:text-indigo-600 mr-4">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Tambah Anggota Kelas: <?= htmlspecialchars($data['kelas_terpilih']['nama_kelas']); ?>
            </h2>
            <p class="text-gray-600 mt-1">
                Pilih siswa yang akan ditambahkan ke kelas ini untuk sesi <?= $_SESSION['nama_semester_aktif']; ?>
            </p>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <div class="bg-white rounded-xl shadow-md p-8 max-w-4xl mx-auto">
        
        <!-- Class Info Banner -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <h3 class="font-medium text-blue-800 mb-2">Informasi Kelas:</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-blue-600 font-medium">Nama Kelas:</span>
                            <span class="text-blue-800"><?= htmlspecialchars($data['kelas_terpilih']['nama_kelas']); ?></span>
                        </div>
                        <div>
                            <span class="text-blue-600 font-medium">Jenjang:</span>
                            <span class="text-blue-800"><?= htmlspecialchars($data['kelas_terpilih']['jenjang'] ?? 'Tidak diketahui'); ?></span>
                        </div>
                        <div>
                            <span class="text-blue-600 font-medium">Anggota Saat Ini:</span>
                            <span class="text-blue-800" id="currentMemberCount">
                                <?= isset($data['jumlah_anggota_saat_ini']) ? $data['jumlah_anggota_saat_ini'] : '0'; ?> siswa
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= BASEURL; ?>/admin/prosesTambahAnggota" method="POST" id="formTambahAnggota">
            <input type="hidden" name="id_kelas" value="<?= $data['kelas_terpilih']['id_kelas']; ?>">
            <input type="hidden" name="id_tp" value="<?= $_SESSION['id_tp_aktif']; ?>">
            
            <!-- Search and Filter Controls -->
            <div class="mb-6 space-y-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <label for="search-siswa" class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                            Cari Siswa
                        </label>
                        <input type="text" id="search-siswa" 
                               placeholder="Ketik nama atau NISN untuk mencari..." 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Filter by Gender -->
                    <div class="w-full sm:w-48">
                        <label for="filter-gender" class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                            Filter Jenis Kelamin
                        </label>
                        <select id="filter-gender" 
                                class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Selection Controls -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <button type="button" id="selectAll" 
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <i data-lucide="check-square" class="w-4 h-4 inline mr-1"></i>
                            Pilih Semua
                        </button>
                        <button type="button" id="deselectAll" 
                                class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                            <i data-lucide="square" class="w-4 h-4 inline mr-1"></i>
                            Batal Pilih
                        </button>
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        <span id="selectedCount">0</span> siswa dipilih dari 
                        <span id="totalVisible"><?= count($data['siswa_tersedia']); ?></span> siswa tersedia
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="border rounded-lg overflow-hidden">
                <!-- List Header -->
                <div class="bg-gray-50 px-4 py-3 border-b">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-800">
                            <i data-lucide="users-2" class="w-4 h-4 inline mr-2"></i>
                            Daftar Siswa Tersedia
                        </h4>
                        <span class="text-sm text-gray-500" id="listStatus">
                            Menampilkan semua siswa
                        </span>
                    </div>
                </div>

                <!-- Students Container -->
                <div class="max-h-96 overflow-y-auto" id="siswa-container">
                    <?php if (empty($data['siswa_tersedia'])) : ?>
                        <div class="p-8 text-center">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="user-x" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Siswa Tersedia</h3>
                            <p class="text-gray-500 mb-4">
                                Semua siswa aktif sudah menjadi anggota kelas atau sudah terdaftar di kelas lain.
                            </p>
                            <a href="<?= BASEURL; ?>/admin/siswa" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                Tambah Siswa Baru
                            </a>
                        </div>
                    <?php else : ?>
                        <div id="siswa-list" class="divide-y divide-gray-200">
                            <?php foreach ($data['siswa_tersedia'] as $index => $siswa) : ?>
                                <div class="siswa-item p-4 hover:bg-gray-50 transition-colors duration-150" 
                                     data-nama="<?= strtolower($siswa['nama_siswa']); ?>" 
                                     data-nisn="<?= $siswa['nisn']; ?>"
                                     data-gender="<?= $siswa['jenis_kelamin'] ?? ''; ?>">
                                    <div class="flex items-center">
                                        <input id="siswa-<?= $siswa['id_siswa']; ?>" 
                                               name="id_siswa[]" 
                                               value="<?= $siswa['id_siswa']; ?>" 
                                               type="checkbox" 
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 siswa-checkbox">
                                        
                                        <label for="siswa-<?= $siswa['id_siswa']; ?>" 
                                               class="ml-3 flex-1 cursor-pointer">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?= htmlspecialchars($siswa['nama_siswa']); ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1 space-x-3">
                                                        <span>
                                                            <i data-lucide="hash" class="w-3 h-3 inline mr-1"></i>
                                                            NISN: <?= htmlspecialchars($siswa['nisn']); ?>
                                                        </span>
                                                        <?php if (!empty($siswa['jenis_kelamin'])) : ?>
                                                            <span>
                                                                <i data-lucide="user" class="w-3 h-3 inline mr-1"></i>
                                                                <?= $siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($siswa['tgl_lahir'])) : ?>
                                                            <span>
                                                                <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                                                <?= date('d M Y', strtotime($siswa['tgl_lahir'])); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <?php if ($siswa['jenis_kelamin'] === 'L') : ?>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                                            L
                                                        </span>
                                                    <?php elseif ($siswa['jenis_kelamin'] === 'P') : ?>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                            <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                                            P
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                        Aktif
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($data['siswa_tersedia'])) : ?>
                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                        <div class="text-sm text-gray-600">
                            <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                            Pilih siswa yang ingin ditambahkan sebagai anggota kelas
                        </div>
                        
                        <div class="flex space-x-3 w-full sm:w-auto">
                            <a href="<?= BASEURL; ?>/admin/keanggotaan" 
                               class="flex-1 sm:flex-none bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    id="submitButton" disabled>
                                <i data-lucide="user-plus" class="w-4 h-4 inline mr-2"></i>
                                Tambahkan <span id="submitCount">0</span> Siswa
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Quick Stats -->
    <?php if (!empty($data['siswa_tersedia'])) : ?>
        <div class="max-w-4xl mx-auto mt-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600"><?= count($data['siswa_tersedia']); ?></div>
                    <div class="text-sm text-gray-600">Siswa Tersedia</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
                    <div class="text-2xl font-bold text-green-600" id="statSelected">0</div>
                    <div class="text-sm text-gray-600">Siswa Dipilih</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600" id="statAfterAdd">
                        <?= isset($data['jumlah_anggota_saat_ini']) ? $data['jumlah_anggota_saat_ini'] : '0'; ?>
                    </div>
                    <div class="text-sm text-gray-600">Total Setelah Ditambah</div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Elements
    const searchInput = document.getElementById('search-siswa');
    const genderFilter = document.getElementById('filter-gender');
    const siswaItems = document.querySelectorAll('.siswa-item');
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const submitButton = document.getElementById('submitButton');
    const selectedCount = document.getElementById('selectedCount');
    const totalVisible = document.getElementById('totalVisible');
    const listStatus = document.getElementById('listStatus');
    const submitCount = document.getElementById('submitCount');
    const statSelected = document.getElementById('statSelected');
    const statAfterAdd = document.getElementById('statAfterAdd');
    
    const currentMembers = <?= isset($data['jumlah_anggota_saat_ini']) ? $data['jumlah_anggota_saat_ini'] : '0'; ?>;

    // Update counters
    function updateCounters() {
        const checkedBoxes = document.querySelectorAll('.siswa-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = count;
        submitCount.textContent = count;
        statSelected.textContent = count;
        statAfterAdd.textContent = currentMembers + count;
        
        // Enable/disable submit button
        submitButton.disabled = count === 0;
        
        // Update button text
        if (count === 0) {
            submitButton.innerHTML = '<i data-lucide="user-plus" class="w-4 h-4 inline mr-2"></i>Pilih Siswa';
        } else if (count === 1) {
            submitButton.innerHTML = '<i data-lucide="user-plus" class="w-4 h-4 inline mr-2"></i>Tambahkan 1 Siswa';
        } else {
            submitButton.innerHTML = '<i data-lucide="user-plus" class="w-4 h-4 inline mr-2"></i>Tambahkan ' + count + ' Siswa';
        }
        
        // Recreate icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Filter function
    function filterStudents() {
        const searchTerm = searchInput.value.toLowerCase();
        const genderTerm = genderFilter.value;
        let visibleCount = 0;

        siswaItems.forEach(item => {
            const nama = item.dataset.nama || '';
            const nisn = item.dataset.nisn || '';
            const gender = item.dataset.gender || '';

            const matchSearch = !searchTerm || nama.includes(searchTerm) || nisn.includes(searchTerm);
            const matchGender = !genderTerm || gender === genderTerm;

            if (matchSearch && matchGender) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
                // Uncheck hidden items
                const checkbox = item.querySelector('.siswa-checkbox');
                if (checkbox && checkbox.checked) {
                    checkbox.checked = false;
                }
            }
        });

        totalVisible.textContent = visibleCount;
        
        // Update status
        if (searchTerm || genderTerm) {
            listStatus.textContent = `Menampilkan ${visibleCount} dari ${siswaItems.length} siswa`;
        } else {
            listStatus.textContent = 'Menampilkan semua siswa';
        }

        updateCounters();
    }

    // Select all visible
    function selectAllVisible() {
        siswaItems.forEach(item => {
            if (item.style.display !== 'none') {
                const checkbox = item.querySelector('.siswa-checkbox');
                if (checkbox) {
                    checkbox.checked = true;
                }
            }
        });
        updateCounters();
    }

    // Deselect all
    function deselectAll() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateCounters();
    }

    // Event listeners
    searchInput.addEventListener('input', filterStudents);
    genderFilter.addEventListener('change', filterStudents);
    selectAllBtn.addEventListener('click', selectAllVisible);
    deselectAllBtn.addEventListener('click', deselectAll);

    // Checkbox change events
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCounters);
    });

    // Form validation
    const form = document.getElementById('formTambahAnggota');
    form.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.siswa-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 siswa untuk ditambahkan.');
            return false;
        }

        // Confirmation
        const count = checkedBoxes.length;
        const message = `Apakah Anda yakin ingin menambahkan ${count} siswa ke kelas ${<?= json_encode($data['kelas_terpilih']['nama_kelas']); ?>}?`;
        
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Memproses...';
    });

    // Initial counter update
    updateCounters();
});
</script>