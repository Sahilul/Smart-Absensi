<!-- admin/edit_kelas.php - DIPERBAIKI -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="flex items-center mb-6">
        <a href="<?= BASEURL; ?>/admin/kelas" class="text-gray-500 hover:text-indigo-600 mr-4">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Data Kelas</h2>
            <p class="text-gray-600 mt-1">Perbarui informasi kelas: <strong><?= htmlspecialchars($data['kelas']['nama_kelas']); ?></strong></p>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-8 max-w-2xl mx-auto">
        
        <!-- Current Info Banner -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                <div>
                    <h3 class="font-medium text-blue-800">Data Saat Ini:</h3>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-blue-600 font-medium">Nama:</span>
                            <span class="text-blue-800"><?= htmlspecialchars($data['kelas']['nama_kelas']); ?></span>
                        </div>
                        <div>
                            <span class="text-blue-600 font-medium">Jenjang:</span>
                            <span class="text-blue-800"><?= htmlspecialchars($data['kelas']['jenjang']); ?></span>
                        </div>
                        <div>
                            <span class="text-blue-600 font-medium">ID:</span>
                            <span class="text-blue-800"><?= $data['kelas']['id_kelas']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= BASEURL; ?>/admin/prosesUpdateKelas" method="POST" id="formEditKelas">
            <input type="hidden" name="id_kelas" value="<?= $data['kelas']['id_kelas']; ?>">
            
            <div class="space-y-6">
                
                <!-- Nama Kelas -->
                <div>
                    <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="bookmark" class="w-4 h-4 inline mr-1"></i>
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_kelas" id="nama_kelas" required 
                           value="<?= htmlspecialchars($data['kelas']['nama_kelas']); ?>"
                           placeholder="Contoh: VII-A, VIII-IPA-1, IX-B"
                           class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <p class="mt-1 text-xs text-gray-500">
                        Format: [Jenjang]-[Kelas] (contoh: VII-A, X-IPA-1)
                    </p>
                </div>

                <!-- Tahun Pelajaran (Read Only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                        Tahun Pelajaran
                    </label>
                    <div class="mt-1 block w-full px-3 py-3 border border-gray-300 bg-gray-100 rounded-lg text-gray-600">
                        <?= isset($data['kelas']['nama_tp']) ? htmlspecialchars($data['kelas']['nama_tp']) : 'Tidak diketahui'; ?>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        <i data-lucide="lock" class="w-3 h-3 inline mr-1"></i>
                        Tahun pelajaran tidak dapat diubah saat edit
                    </p>
                </div>

                <!-- Jenjang -->
                <div>
                    <label for="jenjang" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="layers" class="w-4 h-4 inline mr-1"></i>
                        Jenjang/Tingkat <span class="text-red-500">*</span>
                    </label>
                    <select name="jenjang" id="jenjang" required 
                            class="mt-1 block w-full px-3 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">-- Pilih Jenjang --</option>
                        
                        <!-- SMP/MTs -->
                        <optgroup label="SMP/MTs">
                            <option value="VII" <?= ($data['kelas']['jenjang'] == 'VII') ? 'selected' : ''; ?>>Kelas VII (7)</option>
                            <option value="VIII" <?= ($data['kelas']['jenjang'] == 'VIII') ? 'selected' : ''; ?>>Kelas VIII (8)</option>
                            <option value="IX" <?= ($data['kelas']['jenjang'] == 'IX') ? 'selected' : ''; ?>>Kelas IX (9)</option>
                        </optgroup>
                        
                        <!-- SMA/MA/SMK -->
                        <optgroup label="SMA/MA/SMK">
                            <option value="X" <?= ($data['kelas']['jenjang'] == 'X') ? 'selected' : ''; ?>>Kelas X (10)</option>
                            <option value="XI" <?= ($data['kelas']['jenjang'] == 'XI') ? 'selected' : ''; ?>>Kelas XI (11)</option>
                            <option value="XII" <?= ($data['kelas']['jenjang'] == 'XII') ? 'selected' : ''; ?>>Kelas XII (12)</option>
                        </optgroup>
                        
                        <!-- SD -->
                        <optgroup label="SD/MI">
                            <option value="I" <?= ($data['kelas']['jenjang'] == 'I') ? 'selected' : ''; ?>>Kelas I (1)</option>
                            <option value="II" <?= ($data['kelas']['jenjang'] == 'II') ? 'selected' : ''; ?>>Kelas II (2)</option>
                            <option value="III" <?= ($data['kelas']['jenjang'] == 'III') ? 'selected' : ''; ?>>Kelas III (3)</option>
                            <option value="IV" <?= ($data['kelas']['jenjang'] == 'IV') ? 'selected' : ''; ?>>Kelas IV (4)</option>
                            <option value="V" <?= ($data['kelas']['jenjang'] == 'V') ? 'selected' : ''; ?>>Kelas V (5)</option>
                            <option value="VI" <?= ($data['kelas']['jenjang'] == 'VI') ? 'selected' : ''; ?>>Kelas VI (6)</option>
                        </optgroup>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Pilih jenjang yang sesuai dengan tingkat pendidikan
                    </p>
                </div>

                <!-- Wali Kelas -->
                <div>
                    <label for="id_guru_walikelas" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="user-circle" class="w-4 h-4 inline mr-1"></i>
                        Wali Kelas <span class="text-gray-500">(Opsional)</span>
                    </label>
                    <select name="id_guru_walikelas" id="id_guru_walikelas" 
                            class="mt-1 block w-full px-3 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">-- Tidak Ada Wali Kelas --</option>
                        <?php if (!empty($data['daftar_guru'])): ?>
                            <?php foreach ($data['daftar_guru'] as $guru) : ?>
                                <option value="<?= $guru['id_guru']; ?>"
                                    <?= (!empty($data['wali_kelas_current']) && $data['wali_kelas_current']['id_guru'] == $guru['id_guru']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($guru['nama_guru']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (!empty($data['wali_kelas_current'])): ?>
                        <p class="mt-1 text-xs text-blue-600">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Wali kelas saat ini: <strong><?= htmlspecialchars($data['wali_kelas_current']['nama_guru']); ?></strong>
                        </p>
                    <?php else: ?>
                        <p class="mt-1 text-xs text-gray-500">
                            Belum ada wali kelas yang ditentukan untuk kelas ini
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Preview Section -->
                <div id="previewSection" class="hidden">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <h4 class="font-medium text-gray-800 mb-2">
                            <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                            Preview Perubahan:
                        </h4>
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium" id="previewBadge">
                                <!-- Preview akan muncul di sini -->
                            </div>
                            <div class="text-xs text-gray-500">
                                <span class="font-medium">Sebelum:</span> 
                                <span class="bg-gray-200 px-2 py-1 rounded">
                                    <?= htmlspecialchars($data['kelas']['jenjang']); ?> - <?= htmlspecialchars($data['kelas']['nama_kelas']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning Section -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <h4 class="font-medium text-yellow-800">Perhatian:</h4>
                            <ul class="mt-2 text-sm text-yellow-700 space-y-1">
                                <li>• Perubahan nama kelas akan mempengaruhi semua data terkait</li>
                                <li>• Pastikan tidak ada duplikasi nama kelas dalam tahun pelajaran yang sama</li>
                                <li>• Data absensi dan jurnal akan tetap terhubung dengan kelas ini</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="border-t pt-6">
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                        <a href="<?= BASEURL; ?>/admin/kelas" 
                           class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                            <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                            Batal
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            Update Kelas
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Additional Info -->
    <div class="max-w-2xl mx-auto mt-8">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4">
                <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                Informasi Kelas:
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Tahun Pelajaran:</span>
                    <span class="font-medium text-gray-900">
                        <?= isset($data['kelas']['nama_tp']) ? htmlspecialchars($data['kelas']['nama_tp']) : 'Tidak diketahui'; ?>
                    </span>
                </div>
                <div>
                    <span class="text-gray-600">Jumlah Siswa:</span>
                    <span class="font-medium text-gray-900">
                        <?= isset($data['kelas']['jumlah_siswa']) ? $data['kelas']['jumlah_siswa'] : '0'; ?> siswa
                    </span>
                </div>
                <div>
                    <span class="text-gray-600">Guru Mengajar:</span>
                    <span class="font-medium text-gray-900">
                        <?= isset($data['kelas']['jumlah_guru']) ? $data['kelas']['jumlah_guru'] : '0'; ?> guru
                    </span>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                        Aktif
                    </span>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex flex-wrap gap-2">
                    <a href="<?= BASEURL; ?>/admin/keanggotaan" 
                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-lg hover:bg-blue-200 transition-colors">
                        <i data-lucide="users-2" class="w-3 h-3 mr-1"></i>
                        Kelola Anggota
                    </a>
                    <a href="<?= BASEURL; ?>/admin/penugasan" 
                       class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs rounded-lg hover:bg-green-200 transition-colors">
                        <i data-lucide="user-check" class="w-3 h-3 mr-1"></i>
                        Lihat Penugasan
                    </a>
                    <a href="<?= BASEURL; ?>/admin/laporan" 
                       class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-xs rounded-lg hover:bg-purple-200 transition-colors">
                        <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                        Laporan Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Form elements
    const namaKelasInput = document.getElementById('nama_kelas');
    const jenjangSelect = document.getElementById('jenjang');
    const previewSection = document.getElementById('previewSection');
    const previewBadge = document.getElementById('previewBadge');
    const walasSelect = document.getElementById('id_guru_walikelas');

    // Store original values
    const originalNama = namaKelasInput.value;
    const originalJenjang = jenjangSelect.value;
    const originalWalas = walasSelect ? walasSelect.value : '';

    // Update preview when form changes
    function updatePreview() {
        const namaKelas = namaKelasInput.value.trim();
        const jenjang = jenjangSelect.value;

        // Tampilkan preview jika ada perubahan nama kelas / jenjang
        if ((namaKelas !== originalNama || jenjang !== originalJenjang) && namaKelas && jenjang) {
            previewBadge.textContent = `${jenjang} - ${namaKelas}`;
            previewSection.classList.remove('hidden');
        } else {
            previewSection.classList.add('hidden');
        }
    }

    // Event listeners for preview
    namaKelasInput.addEventListener('input', updatePreview);
    jenjangSelect.addEventListener('change', updatePreview);

    // Form validation
    const form = document.getElementById('formEditKelas');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        let hasError = false;
        const errors = [];

        // Validate nama kelas
        if (!namaKelasInput.value.trim()) {
            errors.push('Nama kelas harus diisi');
            hasError = true;
        }

        // Validate jenjang
        if (!jenjangSelect.value) {
            errors.push('Jenjang harus dipilih');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            alert('Mohon lengkapi form:\n' + errors.join('\n'));
            return false;
        }

        // Check if there are any changes (termasuk perubahan wali kelas)
        const walasChanged = walasSelect ? (walasSelect.value !== originalWalas) : false;
        const namaChanged = namaKelasInput.value !== originalNama;
        const jenjangChanged = jenjangSelect.value !== originalJenjang;

        if (!namaChanged && !jenjangChanged && !walasChanged) {
            e.preventDefault();
            alert('Tidak ada perubahan data yang perlu disimpan.');
            return false;
        }

        // Confirm changes
        if (!confirm('Apakah Anda yakin ingin menyimpan perubahan ini?')) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Menyimpan...';
    });

    // Auto-format nama kelas
    namaKelasInput.addEventListener('input', function() {
        // Auto uppercase
        this.value = this.value.toUpperCase();
    });

    // Initial preview update
    updatePreview();
});
</script>