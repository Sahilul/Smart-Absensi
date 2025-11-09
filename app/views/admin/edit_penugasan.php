<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penugasan Guru</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
        <!-- Page Header with Breadcrumb -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <div class="flex items-center">
                <a href="<?= BASEURL; ?>/admin/penugasan" 
                   class="text-gray-500 hover:text-indigo-600 mr-4 p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit Penugasan Guru</h2>
                    <p class="text-gray-600 mt-1">Perbarui penugasan guru mengajar</p>
                </div>
            </div>
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?= BASEURL; ?>/admin/penugasan" class="hover:text-indigo-600">Penugasan Guru</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
                <span class="text-gray-900 font-medium">Edit Penugasan</span>
            </div>
        </div>

        <!-- Current Assignment Info -->
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5 text-indigo-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700">
                        <span class="font-medium">Mengedit Penugasan:</span><br>
                        <strong><?= htmlspecialchars($data['penugasan']['nama_guru']); ?></strong> mengajar 
                        <strong><?= htmlspecialchars($data['penugasan']['nama_mapel']); ?></strong> di kelas 
                        <strong><?= htmlspecialchars($data['penugasan']['nama_kelas']); ?></strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Active Semester Info -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5 text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-800">
                        <span class="font-medium">Semester Aktif:</span> 
                        <?= $_SESSION['nama_semester_aktif']; ?><br>
                        <span class="text-xs">Penugasan ini terkait dengan semester yang sedang aktif.</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden max-w-4xl mx-auto">
            <!-- Form Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                        <i data-lucide="edit-3" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Form Edit Penugasan</h3>
                        <p class="text-sm text-gray-600">Perbarui informasi penugasan mengajar</p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <form action="<?= BASEURL; ?>/admin/prosesUpdatePenugasan" method="POST">
                    <!-- Hidden fields -->
                    <input type="hidden" name="id_penugasan" value="<?= $data['penugasan']['id_penugasan']; ?>">
                    <input type="hidden" name="id_semester" value="<?= $_SESSION['id_semester_aktif']; ?>">
                    <input type="hidden" name="id_mapel" value="<?= $data['penugasan']['id_mapel']; ?>">
                    <input type="hidden" name="id_kelas" value="<?= $data['penugasan']['id_kelas']; ?>">
                    
                    <div class="space-y-6">
                        <!-- Assignment Information Section -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="clipboard-list" class="w-4 h-4 mr-2"></i>
                                Detail Penugasan
                            </h4>

                            <div class="space-y-4">
                                <!-- Pilih Guru -->
                                <div>
                                    <label for="id_guru" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-2"></i>
                                        Pilih Guru
                                    </label>
                                    <select name="id_guru" 
                                            id="id_guru" 
                                            required 
                                            class="mt-1 block w-full px-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <option value="">-- Pilih Guru --</option>
                                        <?php foreach ($data['guru'] as $guru) : ?>
                                            <option value="<?= $guru['id_guru']; ?>" 
                                                    <?= ($guru['id_guru'] == $data['penugasan']['id_guru']) ? 'selected' : ''; ?>>
                                                👨‍🏫 <?= htmlspecialchars($guru['nama_guru']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Guru yang akan mengampu mata pelajaran</p>
                                </div>

                                <!-- Mata Pelajaran (Read-only) -->
                                <div>
                                    <label for="mapel_display" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="book-open" class="w-4 h-4 inline mr-2"></i>
                                        Mata Pelajaran
                                        <span class="text-xs text-gray-500">(tidak dapat diubah)</span>
                                    </label>
                                    <div class="mt-1 block w-full px-4 py-3 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                                        <div class="flex items-center">
                                            <i data-lucide="lock" class="w-4 h-4 mr-2 text-gray-400"></i>
                                            📚 <?= htmlspecialchars($data['penugasan']['nama_mapel']); ?>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Mata pelajaran tidak dapat diubah setelah penugasan dibuat</p>
                                </div>

                                <!-- Kelas (Read-only) -->
                                <div>
                                    <label for="kelas_display" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                                        Kelas
                                        <span class="text-xs text-gray-500">(tidak dapat diubah)</span>
                                    </label>
                                    <div class="mt-1 block w-full px-4 py-3 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed">
                                        <div class="flex items-center">
                                            <i data-lucide="lock" class="w-4 h-4 mr-2 text-gray-400"></i>
                                            🏫 <?= htmlspecialchars($data['penugasan']['nama_kelas']); ?>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Kelas tidak dapat diubah setelah penugasan dibuat</p>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Preview Section -->
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200" id="preview-section">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                                Pratinjau Penugasan
                            </h4>
                            <div class="bg-white rounded-lg p-3 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i data-lucide="graduation-cap" class="w-4 h-4 text-indigo-600 mr-2"></i>
                                        <div>
                                            <span class="text-gray-500">Guru:</span>
                                            <div class="font-medium" id="preview-guru"><?= htmlspecialchars($data['penugasan']['nama_guru']); ?></div>
                                        </div>
                                    </div>
                                    <div class="text-right text-sm">
                                        <div class="text-gray-500">Mengajar:</div>
                                        <div class="font-medium text-gray-800">
                                            📚 <?= htmlspecialchars($data['penugasan']['nama_mapel']); ?> di 🏫 <?= htmlspecialchars($data['penugasan']['nama_kelas']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Info -->
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2 flex items-center">
                                <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                                Validasi Edit Penugasan
                            </h4>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Hanya guru yang dapat diubah dalam penugasan
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Mata pelajaran dan kelas tidak dapat diubah
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Guru baru tidak boleh sudah mengajar mapel dan kelas yang sama
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="<?= BASEURL; ?>/admin/penugasan" 
                               class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition-colors duration-200 flex items-center justify-center">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow-sm flex items-center justify-center" 
                                    id="submit-btn">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Update Penugasan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer Tips -->
        <div class="mt-6 text-center text-sm text-gray-500 max-w-4xl mx-auto">
            <div class="bg-gray-100 rounded-lg p-4">
                <p class="flex items-center justify-center">
                    <i data-lucide="lightbulb" class="w-4 h-4 mr-2"></i>
                    <strong>Tips:</strong> Perubahan penugasan akan mempengaruhi akses guru untuk melakukan jurnal dan absensi
                </p>
            </div>
        </div>
    </main>

    <script>
    // Form validation and enhancement
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const guruSelect = document.getElementById('id_guru');
        const submitBtn = document.getElementById('submit-btn');
        const previewGuru = document.getElementById('preview-guru');

        // Update preview when guru selection changes
        function updatePreview() {
            const guruText = guruSelect.options[guruSelect.selectedIndex]?.text || '-';
            previewGuru.textContent = guruText.replace('👨‍🏫 ', '');

            // Enable/disable submit button
            const guruSelected = guruSelect.value;
            submitBtn.disabled = !guruSelected;
            
            if (guruSelected) {
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.add('hover:bg-indigo-700');
            } else {
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:bg-indigo-700');
            }
        }

        // Event listener for guru selection
        guruSelect.addEventListener('change', updatePreview);

        // Form submission confirmation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const guruName = guruSelect.options[guruSelect.selectedIndex].text.replace('👨‍🏫 ', '');

            const confirmed = confirm(
                `Apakah Anda yakin ingin mengubah guru penugasan menjadi:\n\n` +
                `Guru Baru: ${guruName}\n\n` +
                `Untuk mengajar:\n` +
                `Mata Pelajaran: <?= htmlspecialchars($data['penugasan']['nama_mapel']); ?>\n` +
                `Kelas: <?= htmlspecialchars($data['penugasan']['nama_kelas']); ?>\n\n` +
                `Semester: <?= $_SESSION['nama_semester_aktif']; ?>?`
            );

            if (!confirmed) {
                e.preventDefault();
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Menyimpan...';
        });

        // Initial state
        updatePreview();
    });
    </script>
</body>
</html>