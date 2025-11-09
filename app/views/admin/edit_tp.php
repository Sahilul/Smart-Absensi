<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tahun Pelajaran</title>
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
                <a href="<?= BASEURL; ?>/admin/tahunPelajaran" 
                   class="text-gray-500 hover:text-indigo-600 mr-4 p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit Tahun Pelajaran</h2>
                    <p class="text-gray-600 mt-1">Perbarui periode tahun pelajaran</p>
                </div>
            </div>
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?= BASEURL; ?>/admin/tahunPelajaran" class="hover:text-indigo-600">Tahun Pelajaran</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
                <span class="text-gray-900 font-medium">Edit</span>
            </div>
        </div>

        <!-- Current Academic Year Info Card -->
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-indigo-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700">
                        <span class="font-medium">Mengedit Tahun Pelajaran:</span> 
                        <?= htmlspecialchars($data['tp']['nama_tp']); ?><br>
                        <span class="text-xs">
                            Periode: <?= date('d M Y', strtotime($data['tp']['tgl_mulai'])); ?> - <?= date('d M Y', strtotime($data['tp']['tgl_selesai'])); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Status Warning (if applicable) -->
        <?php 
        $today = date('Y-m-d');
        $isActive = ($today >= $data['tp']['tgl_mulai'] && $today <= $data['tp']['tgl_selesai']);
        if ($isActive): 
        ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-800">
                        <span class="font-medium">Perhatian:</span> 
                        Tahun pelajaran ini sedang aktif. Perubahan tanggal dapat mempengaruhi sistem absensi yang sedang berjalan.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden max-w-4xl mx-auto">
            <!-- Form Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center">
                    <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                        <i data-lucide="edit-3" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Form Edit Tahun Pelajaran</h3>
                        <p class="text-sm text-gray-600">Perbarui informasi periode akademik</p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <form action="<?= BASEURL; ?>/admin/prosesUpdateTP" method="POST">
                    <input type="hidden" name="id_tp" value="<?= $data['tp']['id_tp']; ?>">
                    
                    <div class="space-y-6">
                        <!-- Nama Tahun Pelajaran -->
                        <div>
                            <label for="nama_tp" class="block text-sm font-medium text-gray-700 mb-2">
                                <i data-lucide="bookmark" class="w-4 h-4 inline mr-2"></i>
                                Nama Tahun Pelajaran
                            </label>
                            <input type="text" 
                                   name="nama_tp" 
                                   id="nama_tp" 
                                   required 
                                   value="<?= htmlspecialchars($data['tp']['nama_tp']); ?>"
                                   placeholder="Contoh: 2024/2025"
                                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            <p class="text-xs text-gray-500 mt-1">Format: YYYY/YYYY (contoh: 2024/2025)</p>
                        </div>

                        <!-- Period Section -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="calendar-range" class="w-4 h-4 mr-2"></i>
                                Periode Tahun Pelajaran
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tanggal Mulai -->
                                <div>
                                    <label for="tgl_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="calendar-days" class="w-4 h-4 inline mr-2"></i>
                                        Tanggal Mulai
                                    </label>
                                    <input type="date" 
                                           name="tgl_mulai" 
                                           id="tgl_mulai" 
                                           required 
                                           value="<?= $data['tp']['tgl_mulai']; ?>"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <p class="text-xs text-gray-500 mt-1">Mulai tahun pelajaran</p>
                                </div>

                                <!-- Tanggal Selesai -->
                                <div>
                                    <label for="tgl_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="calendar-x" class="w-4 h-4 inline mr-2"></i>
                                        Tanggal Selesai
                                    </label>
                                    <input type="date" 
                                           name="tgl_selesai" 
                                           id="tgl_selesai" 
                                           required 
                                           value="<?= $data['tp']['tgl_selesai']; ?>"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <p class="text-xs text-gray-500 mt-1">Akhir tahun pelajaran</p>
                                </div>
                            </div>

                            <!-- Duration Info -->
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-sm text-blue-800" id="duration-info">
                                    <i data-lucide="clock" class="w-4 h-4 inline mr-2"></i>
                                    <span id="duration-text">Durasi akan dihitung otomatis</span>
                                </p>
                            </div>
                        </div>

                        <!-- Semester Info -->
                        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2 flex items-center">
                                <i data-lucide="layers" class="w-4 h-4 mr-2"></i>
                                Informasi Semester
                            </h4>
                            <p class="text-sm text-indigo-800 mb-3">
                                Setiap tahun pelajaran otomatis memiliki 2 semester:
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-white rounded-lg p-3 border border-indigo-200">
                                    <div class="flex items-center">
                                        <i data-lucide="sun" class="w-4 h-4 text-orange-500 mr-2"></i>
                                        <span class="font-medium text-gray-800">Semester 1 (Ganjil)</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Juli - Desember</p>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-indigo-200">
                                    <div class="flex items-center">
                                        <i data-lucide="moon" class="w-4 h-4 text-blue-500 mr-2"></i>
                                        <span class="font-medium text-gray-800">Semester 2 (Genap)</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Januari - Juni</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="<?= BASEURL; ?>/admin/tahunPelajaran" 
                               class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition-colors duration-200 flex items-center justify-center">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow-sm flex items-center justify-center">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Update Tahun Pelajaran
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
                    <strong>Tips:</strong> Pastikan tanggal selesai lebih dari tanggal mulai dan tidak tumpang tindih dengan tahun pelajaran lain
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

        const tglMulai = document.getElementById('tgl_mulai');
        const tglSelesai = document.getElementById('tgl_selesai');
        const namaTP = document.getElementById('nama_tp');
        const durationText = document.getElementById('duration-text');

        // Calculate and display duration
        function updateDuration() {
            if (tglMulai.value && tglSelesai.value) {
                const startDate = new Date(tglMulai.value);
                const endDate = new Date(tglSelesai.value);
                
                if (endDate > startDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    const months = Math.floor(diffDays / 30);
                    const remainingDays = diffDays % 30;
                    
                    durationText.textContent = `Durasi: ${months} bulan ${remainingDays} hari (${diffDays} hari total)`;
                    durationText.className = 'text-sm text-blue-800';
                } else {
                    durationText.textContent = 'Tanggal selesai harus setelah tanggal mulai';
                    durationText.className = 'text-sm text-red-800';
                }
            }
        }

        // Auto-generate academic year name
        function updateAcademicYearName() {
            if (tglMulai.value) {
                const startYear = new Date(tglMulai.value).getFullYear();
                const endYear = startYear + 1;
                const suggestedName = `${startYear}/${endYear}`;
                
                // Only suggest if field is empty or follows the pattern
                if (!namaTP.value || namaTP.value.match(/^\d{4}\/\d{4}$/)) {
                    namaTP.value = suggestedName;
                }
            }
        }

        // Date validation
        function validateDates() {
            if (tglMulai.value && tglSelesai.value) {
                const startDate = new Date(tglMulai.value);
                const endDate = new Date(tglSelesai.value);
                
                if (endDate <= startDate) {
                    tglSelesai.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
                    return false;
                } else {
                    tglSelesai.setCustomValidity('');
                    return true;
                }
            }
            return true;
        }

        // Event listeners
        tglMulai.addEventListener('change', function() {
            updateDuration();
            updateAcademicYearName();
            validateDates();
            
            // Set minimum date for end date
            if (this.value) {
                tglSelesai.min = this.value;
            }
        });

        tglSelesai.addEventListener('change', function() {
            updateDuration();
            validateDates();
        });

        // Academic year name formatting
        namaTP.addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Remove non-numeric and non-slash characters
            value = value.replace(/[^\d/]/g, '');
            
            // Format as YYYY/YYYY
            if (value.length >= 4 && !value.includes('/')) {
                value = value.substring(0, 4) + '/' + value.substring(4);
            }
            
            // Limit to 9 characters (YYYY/YYYY)
            if (value.length > 9) {
                value = value.substring(0, 9);
            }
            
            e.target.value = value;
        });

        // Form submission validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
                alert('Pastikan tanggal selesai berada setelah tanggal mulai');
                return;
            }

            // Confirmation for active academic year
            <?php if ($isActive): ?>
            const confirmed = confirm(
                'Tahun pelajaran ini sedang aktif. Perubahan dapat mempengaruhi sistem yang sedang berjalan.\n\n' +
                'Apakah Anda yakin ingin melanjutkan?'
            );
            
            if (!confirmed) {
                e.preventDefault();
                return;
            }
            <?php endif; ?>

            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Menyimpan...';
        });

        // Initial calculations
        updateDuration();
    });
    </script>
</body>
</html>