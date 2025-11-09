<!-- admin/tambah_kelas.php - DIPERBAIKI -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="flex items-center mb-6">
        <a href="<?= BASEURL; ?>/admin/kelas" class="text-gray-500 hover:text-indigo-600 mr-4">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Kelas Baru</h2>
            <p class="text-gray-600 mt-1">Buat kelas untuk tahun pelajaran tertentu</p>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-8 max-w-2xl mx-auto">
        <!-- Info Banner -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                <div>
                    <h3 class="font-medium text-blue-800">Tips Membuat Kelas:</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• Gunakan format penamaan yang konsisten (contoh: VII-A, VIII-IPA-1)</li>
                        <li>• Pilih tahun pelajaran dengan tepat</li>
                        <li>• Jenjang sesuai dengan tingkat pendidikan</li>
                    </ul>
                </div>
            </div>
        </div>

        <form action="<?= BASEURL; ?>/admin/prosesTambahKelas" method="POST" id="formTambahKelas">
            <div class="space-y-6">
                
                <!-- Tahun Pelajaran -->
                <div>
                    <label for="id_tp" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                        Tahun Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select name="id_tp" id="id_tp" required 
                            class="mt-1 block w-full px-3 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">-- Pilih Tahun Pelajaran --</option>
                        <?php foreach ($data['daftar_tp'] as $tp) : ?>
                            <option value="<?= $tp['id_tp']; ?>" 
                                <?= ($data['id_tp_default'] == $tp['id_tp']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($tp['nama_tp']); ?>
                                <small class="text-gray-500">
                                    (<?= date('d M Y', strtotime($tp['tgl_mulai'])); ?> - <?= date('d M Y', strtotime($tp['tgl_selesai'])); ?>)
                                </small>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Default: Sesi yang sedang aktif saat ini
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Nama Kelas -->
                    <div>
                        <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-2">
                            <i data-lucide="bookmark" class="w-4 h-4 inline mr-1"></i>
                            Nama Kelas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kelas" id="nama_kelas" required 
                               placeholder="Contoh: VII-A, VIII-IPA-1, IX-B"
                               class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <p class="mt-1 text-xs text-gray-500">
                            Nama kelas harus unik dalam satu tahun pelajaran
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
                                <option value="VII">Kelas VII (7)</option>
                                <option value="VIII">Kelas VIII (8)</option>
                                <option value="IX">Kelas IX (9)</option>
                            </optgroup>
                            
                            <!-- SMA/MA/SMK -->
                            <optgroup label="SMA/MA/SMK">
                                <option value="X">Kelas X (10)</option>
                                <option value="XI">Kelas XI (11)</option>
                                <option value="XII">Kelas XII (12)</option>
                            </optgroup>
                            
                            <!-- SD -->
                            <optgroup label="SD/MI">
                                <option value="I">Kelas I (1)</option>
                                <option value="II">Kelas II (2)</option>
                                <option value="III">Kelas III (3)</option>
                                <option value="IV">Kelas IV (4)</option>
                                <option value="V">Kelas V (5)</option>
                                <option value="VI">Kelas VI (6)</option>
                            </optgroup>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Pilih sesuai tingkat pendidikan sekolah
                        </p>
                    </div>
                </div>

                <!-- Wali Kelas -->
                <div>
                    <label for="id_guru_walikelas" class="block text-sm font-medium text-gray-700 mb-2">
                        <i data-lucide="user-circle" class="w-4 h-4 inline mr-1"></i>
                        Wali Kelas <span class="text-gray-500">(Opsional)</span>
                    </label>
                    <select name="id_guru_walikelas" id="id_guru_walikelas" 
                            class="mt-1 block w-full px-3 py-3 border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                        <?php if (!empty($data['daftar_guru'])): ?>
                            <?php foreach ($data['daftar_guru'] as $guru) : ?>
                                <option value="<?= $guru['id_guru']; ?>">
                                    <?= htmlspecialchars($guru['nama_guru']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Wali kelas dapat ditentukan nanti melalui menu Edit Kelas
                    </p>
                </div>

                <!-- Preview Section -->
                <div id="previewSection" class="hidden">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <h4 class="font-medium text-gray-800 mb-2">
                            <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                            Preview Kelas:
                        </h4>
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium" id="previewBadge">
                                <!-- Preview akan muncul di sini -->
                            </div>
                            <span class="text-gray-600" id="previewTP">
                                <!-- Preview TP akan muncul di sini -->
                            </span>
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
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                            Simpan Kelas
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Add Examples -->
    <div class="max-w-2xl mx-auto mt-8">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold text-gray-800 mb-4">
                <i data-lucide="lightbulb" class="w-4 h-4 inline mr-2"></i>
                Contoh Penamaan Kelas:
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="examples-group">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">SMP/MTs:</h4>
                    <div class="space-y-1">
                        <button type="button" onclick="quickFill('VII-A', 'VII')" 
                                class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors">
                            VII-A
                        </button>
                        <button type="button" onclick="quickFill('VIII-B', 'VIII')" 
                                class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                            VIII-B
                        </button>
                        <button type="button" onclick="quickFill('IX-C', 'IX')" 
                                class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded hover:bg-purple-200 transition-colors">
                            IX-C
                        </button>
                    </div>
                </div>
                
                <div class="examples-group">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">SMA/MA:</h4>
                    <div class="space-y-1">
                        <button type="button" onclick="quickFill('X-IPA-1', 'X')" 
                                class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded hover:bg-yellow-200 transition-colors">
                            X-IPA-1
                        </button>
                        <button type="button" onclick="quickFill('XI-IPS-2', 'XI')" 
                                class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded hover:bg-orange-200 transition-colors">
                            XI-IPS-2
                        </button>
                        <button type="button" onclick="quickFill('XII-BAHASA', 'XII')" 
                                class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200 transition-colors">
                            XII-BAHASA
                        </button>
                    </div>
                </div>
                
                <div class="examples-group">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">SMK:</h4>
                    <div class="space-y-1">
                        <button type="button" onclick="quickFill('X-TKJ-1', 'X')" 
                                class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded hover:bg-indigo-200 transition-colors">
                            X-TKJ-1
                        </button>
                        <button type="button" onclick="quickFill('XI-RPL-2', 'XI')" 
                                class="text-xs bg-cyan-100 text-cyan-800 px-2 py-1 rounded hover:bg-cyan-200 transition-colors">
                            XI-RPL-2
                        </button>
                        <button type="button" onclick="quickFill('XII-MM-1', 'XII')" 
                                class="text-xs bg-pink-100 text-pink-800 px-2 py-1 rounded hover:bg-pink-200 transition-colors">
                            XII-MM-1
                        </button>
                    </div>
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
    const tpSelect = document.getElementById('id_tp');
    const previewSection = document.getElementById('previewSection');
    const previewBadge = document.getElementById('previewBadge');
    const previewTP = document.getElementById('previewTP');

    // Update preview when form changes
    function updatePreview() {
        const namaKelas = namaKelasInput.value.trim();
        const jenjang = jenjangSelect.value;
        const tpText = tpSelect.options[tpSelect.selectedIndex]?.text || '';

        if (namaKelas && jenjang) {
            previewBadge.textContent = `${jenjang} - ${namaKelas}`;
            previewTP.textContent = tpText.replace('-- Pilih Tahun Pelajaran --', '');
            previewSection.classList.remove('hidden');
        } else {
            previewSection.classList.add('hidden');
        }
    }

    // Event listeners for preview
    namaKelasInput.addEventListener('input', updatePreview);
    jenjangSelect.addEventListener('change', updatePreview);
    tpSelect.addEventListener('change', updatePreview);

    // Form validation
    const form = document.getElementById('formTambahKelas');
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

        // Validate TP
        if (!tpSelect.value) {
            errors.push('Tahun pelajaran harus dipilih');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            alert('Mohon lengkapi form:\n' + errors.join('\n'));
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

// Quick fill function for examples
function quickFill(namaKelas, jenjang) {
    document.getElementById('nama_kelas').value = namaKelas;
    document.getElementById('jenjang').value = jenjang;
    
    // Trigger preview update
    const event = new Event('input');
    document.getElementById('nama_kelas').dispatchEvent(event);
    
    const changeEvent = new Event('change');
    document.getElementById('jenjang').dispatchEvent(changeEvent);
    
    // Scroll to form
    document.getElementById('formTambahKelas').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}
</script>