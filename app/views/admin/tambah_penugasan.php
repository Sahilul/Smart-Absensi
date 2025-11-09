<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penugasan Guru</title>
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
                    <h2 class="text-2xl font-bold text-gray-800">Tambah Penugasan Guru</h2>
                    <p class="text-gray-600 mt-1">Tugaskan guru untuk mengajar mata pelajaran di kelas tertentu</p>
                </div>
            </div>
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?= BASEURL; ?>/admin/penugasan" class="hover:text-indigo-600">Penugasan Guru</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
                <span class="text-gray-900 font-medium">Tambah Penugasan</span>
            </div>
        </div>

        <!-- Active Semester Info -->
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5 text-indigo-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700">
                        <span class="font-medium">Semester Aktif:</span> 
                        <?= $_SESSION['nama_semester_aktif']; ?><br>
                        <span class="text-xs">Penugasan ini akan ditambahkan untuk semester yang sedang aktif.</span>
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
                        <i data-lucide="user-plus" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Form Penugasan Mengajar</h3>
                        <p class="text-sm text-gray-600">Pilih guru, mata pelajaran, dan kelas</p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <form action="<?= BASEURL; ?>/admin/prosesTambahPenugasan" method="POST">
                    <!-- Input tersembunyi untuk id_semester -->
                    <input type="hidden" name="id_semester" value="<?= $_SESSION['id_semester_aktif']; ?>">
                    
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
                                            <option value="<?= $guru['id_guru']; ?>">
                                                👨‍🏫 <?= htmlspecialchars($guru['nama_guru']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Guru yang akan mengampu mata pelajaran</p>
                                </div>

                                <!-- Pilih Mata Pelajaran -->
                                <div>
                                    <label for="id_mapel" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="book-open" class="w-4 h-4 inline mr-2"></i>
                                        Pilih Mata Pelajaran
                                    </label>
                                    <select name="id_mapel" 
                                            id="id_mapel" 
                                            required 
                                            class="mt-1 block w-full px-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        <?php foreach ($data['mapel'] as $mapel) : ?>
                                            <option value="<?= $mapel['id_mapel']; ?>">
                                                📚 <?= htmlspecialchars($mapel['nama_mapel']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Mata pelajaran yang akan diajarkan</p>
                                </div>

                                <!-- Pilih Kelas -->
                                <div>
                                    <label for="id_kelas" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                                        Pilih Kelas
                                    </label>
                                    <select name="id_kelas" 
                                            id="id_kelas" 
                                            required 
                                            class="mt-1 block w-full px-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php foreach ($data['kelas'] as $kelas) : ?>
                                            <option value="<?= $kelas['id_kelas']; ?>">
                                                🏫 <?= htmlspecialchars($kelas['nama_kelas']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Kelas yang akan menerima pengajaran</p>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Preview Section -->
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200" id="preview-section" style="display: none;">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                                Pratinjau Penugasan
                            </h4>
                            <div class="bg-white rounded-lg p-3 border border-blue-200">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                    <div class="flex items-center">
                                        <i data-lucide="graduation-cap" class="w-4 h-4 text-indigo-600 mr-2"></i>
                                        <div>
                                            <span class="text-gray-500">Guru:</span>
                                            <div class="font-medium" id="preview-guru">-</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="book" class="w-4 h-4 text-blue-600 mr-2"></i>
                                        <div>
                                            <span class="text-gray-500">Mapel:</span>
                                            <div class="font-medium" id="preview-mapel">-</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="users" class="w-4 h-4 text-purple-600 mr-2"></i>
                                        <div>
                                            <span class="text-gray-500">Kelas:</span>
                                            <div class="font-medium" id="preview-kelas">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Duplicate Warning (will be populated by JavaScript) -->
                        <div id="duplicate-warning" style="display: none;"></div>

                        <!-- Validation Info -->
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2 flex items-center">
                                <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                                Validasi Penugasan
                            </h4>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Pastikan guru belum ditugaskan untuk mapel dan kelas yang sama
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Satu guru bisa mengajar multiple kelas untuk mapel yang sama
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Penugasan berlaku untuk semester aktif
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
                                    id="submit-btn" disabled>
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Simpan Penugasan
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
                    <strong>Tips:</strong> Setelah penugasan disimpan, guru dapat melakukan absensi untuk mata pelajaran di kelas tersebut
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
        const mapelSelect = document.getElementById('id_mapel');
        const kelasSelect = document.getElementById('id_kelas');
        const submitBtn = document.getElementById('submit-btn');
        const previewSection = document.getElementById('preview-section');
        const previewGuru = document.getElementById('preview-guru');
        const previewMapel = document.getElementById('preview-mapel');
        const previewKelas = document.getElementById('preview-kelas');

        // Update preview and button state
        function updatePreview() {
            const guruText = guruSelect.options[guruSelect.selectedIndex]?.text || '-';
            const mapelText = mapelSelect.options[mapelSelect.selectedIndex]?.text || '-';
            const kelasText = kelasSelect.options[kelasSelect.selectedIndex]?.text || '-';

            previewGuru.textContent = guruText.replace('👨‍🏫 ', '');
            previewMapel.textContent = mapelText.replace('📚 ', '');
            previewKelas.textContent = kelasText.replace('🏫 ', '');

            // Show/hide preview section
            const allSelected = guruSelect.value && mapelSelect.value && kelasSelect.value;
            previewSection.style.display = allSelected ? 'block' : 'none';
            
            // Enable/disable submit button
            submitBtn.disabled = !allSelected;
            if (allSelected) {
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.add('hover:bg-indigo-700');
            } else {
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:bg-indigo-700');
            }
        }

        // Event listeners
        guruSelect.addEventListener('change', updatePreview);
        mapelSelect.addEventListener('change', updatePreview);
        kelasSelect.addEventListener('change', updatePreview);

        // Check for duplicate assignment when all fields are selected
        async function checkDuplicateAssignment() {
            if (!guruSelect.value || !mapelSelect.value || !kelasSelect.value) {
                return;
            }

            const warningDiv = document.getElementById('duplicate-warning');
            
            try {
                const response = await fetch('<?= BASEURL; ?>/admin/checkPenugasanDuplikat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_guru: guruSelect.value,
                        id_mapel: mapelSelect.value,
                        id_kelas: kelasSelect.value,
                        id_semester: '<?= $_SESSION['id_semester_aktif']; ?>'
                    })
                });

                const data = await response.json();

                if (data.isDuplicate) {
                    warningDiv.innerHTML = `
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <div class="flex items-start">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 mr-3 mt-0.5"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-red-800">Penugasan Sudah Ada!</h4>
                                    <p class="text-sm text-red-700 mt-1">
                                        Kombinasi guru, mata pelajaran, dan kelas ini sudah terdaftar untuk semester aktif.
                                        Silakan pilih kombinasi yang berbeda.
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    warningDiv.style.display = 'block';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.classList.remove('hover:bg-indigo-700');
                    
                    // Reinitialize lucide icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                } else {
                    warningDiv.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.classList.add('hover:bg-indigo-700');
                }
            } catch (error) {
                console.error('Error checking duplicate:', error);
            }
        }

        // Add duplicate check to change events
        guruSelect.addEventListener('change', () => {
            updatePreview();
            checkDuplicateAssignment();
        });
        mapelSelect.addEventListener('change', () => {
            updatePreview();
            checkDuplicateAssignment();
        });
        kelasSelect.addEventListener('change', () => {
            updatePreview();
            checkDuplicateAssignment();
        });

        // Form submission confirmation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const guruName = guruSelect.options[guruSelect.selectedIndex].text.replace('👨‍🏫 ', '');
            const mapelName = mapelSelect.options[mapelSelect.selectedIndex].text.replace('📚 ', '');
            const kelasName = kelasSelect.options[kelasSelect.selectedIndex].text.replace('🏫 ', '');

            const confirmed = confirm(
                `Apakah Anda yakin ingin menugaskan:\n\n` +
                `Guru: ${guruName}\n` +
                `Mata Pelajaran: ${mapelName}\n` +
                `Kelas: ${kelasName}\n\n` +
                `Untuk semester: <?= $_SESSION['nama_semester_aktif']; ?>?`
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