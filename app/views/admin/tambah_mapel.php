<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Pelajaran Baru</title>
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
                <a href="<?= BASEURL; ?>/admin/mapel" 
                   class="text-gray-500 hover:text-indigo-600 mr-4 p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Tambah Mata Pelajaran Baru</h2>
                    <p class="text-gray-600 mt-1">Lengkapi informasi mata pelajaran untuk kurikulum</p>
                </div>
            </div>
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?= BASEURL; ?>/admin/mapel" class="hover:text-indigo-600">Manajemen Mapel</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
                <span class="text-gray-900 font-medium">Tambah Mapel</span>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <span class="font-medium">Informasi Penting:</span><br>
                        Kode mata pelajaran harus unik dan akan digunakan untuk identifikasi dalam sistem absensi.
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
                        <i data-lucide="book-plus" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Form Data Mata Pelajaran</h3>
                        <p class="text-sm text-gray-600">Isi semua field yang diperlukan</p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <form action="<?= BASEURL; ?>/admin/prosesTambahMapel" method="POST">
                    <div class="space-y-6">
                        <!-- Data Mata Pelajaran Section -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="book" class="w-4 h-4 mr-2"></i>
                                Informasi Mata Pelajaran
                            </h4>

                            <div class="space-y-4">
                                <!-- Kode Mapel -->
                                <div>
                                    <label for="kode_mapel" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="hash" class="w-4 h-4 inline mr-2"></i>
                                        Kode Mata Pelajaran
                                    </label>
                                    <input type="text" 
                                           name="kode_mapel" 
                                           id="kode_mapel" 
                                           required 
                                           placeholder="Contoh: MTK001, IPA001, BIND001"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <p class="text-xs text-gray-500 mt-1">Kode unik untuk identifikasi mata pelajaran (maksimal 10 karakter)</p>
                                </div>

                                <!-- Nama Mapel -->
                                <div>
                                    <label for="nama_mapel" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="book-open" class="w-4 h-4 inline mr-2"></i>
                                        Nama Mata Pelajaran
                                    </label>
                                    <input type="text" 
                                           name="nama_mapel" 
                                           id="nama_mapel" 
                                           required 
                                           placeholder="Contoh: Matematika, Bahasa Indonesia, Ilmu Pengetahuan Alam"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <p class="text-xs text-gray-500 mt-1">Nama lengkap mata pelajaran sesuai kurikulum yang berlaku</p>
                                </div>
                            </div>
                        </div>

                        <!-- Examples Section -->
                        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i data-lucide="lightbulb" class="w-4 h-4 mr-2"></i>
                                Contoh Mata Pelajaran
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-white rounded-lg p-3 border border-indigo-200">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-800">Matematika</span>
                                        <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">MTK001</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-indigo-200">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-800">Bahasa Indonesia</span>
                                        <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">BIND001</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-indigo-200">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-800">Ilmu Pengetahuan Alam</span>
                                        <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">IPA001</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-3 border border-indigo-200">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-800">Pendidikan Jasmani</span>
                                        <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">PENJAS001</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Info -->
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2 flex items-center">
                                <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                                Validasi Data
                            </h4>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Kode mata pelajaran harus unik dan belum digunakan
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Nama mata pelajaran harus jelas dan deskriptif
                                </li>
                                <li class="flex items-center">
                                    <i data-lucide="check" class="w-3 h-3 mr-2"></i>
                                    Sesuaikan dengan kurikulum yang berlaku di sekolah
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="<?= BASEURL; ?>/admin/mapel" 
                               class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition-colors duration-200 flex items-center justify-center">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow-sm flex items-center justify-center">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Simpan Mata Pelajaran
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
                    <strong>Tips:</strong> Setelah mata pelajaran dibuat, Anda dapat menugaskan guru untuk mengajar mata pelajaran tersebut
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

        const kodeMapel = document.getElementById('kode_mapel');
        const namaMapel = document.getElementById('nama_mapel');

        // Kode mapel formatting
        kodeMapel.addEventListener('input', function(e) {
            // Convert to uppercase and remove spaces
            let value = e.target.value.toUpperCase().replace(/\s/g, '');
            
            // Limit to 10 characters
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            e.target.value = value;
        });

        // Name field enhancement
        namaMapel.addEventListener('input', function(e) {
            // Capitalize first letter of each word
            e.target.value = e.target.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        // Auto-suggest kode based on nama
        namaMapel.addEventListener('blur', function(e) {
            const nama = e.target.value;
            if (nama && !kodeMapel.value) {
                // Extract first letters of each word
                const words = nama.split(' ');
                let suggestion = '';
                
                if (words.length === 1) {
                    // Single word: take first 3-6 characters
                    suggestion = words[0].substring(0, Math.min(6, words[0].length)).toUpperCase();
                } else if (words.length === 2) {
                    // Two words: combine abbreviated forms
                    suggestion = words[0].substring(0, 3).toUpperCase() + words[1].substring(0, 3).toUpperCase();
                } else {
                    // Multiple words: take first letter of each
                    suggestion = words.map(word => word.charAt(0)).join('').toUpperCase();
                    // If too short, add more characters from first word
                    if (suggestion.length < 3) {
                        suggestion += words[0].substring(1, 4 - suggestion.length).toUpperCase();
                    }
                }
                
                // Add number suffix
                suggestion += '001';
                
                // Limit to 10 characters
                if (suggestion.length > 10) {
                    suggestion = suggestion.substring(0, 7) + '001';
                }
                
                kodeMapel.value = suggestion;
            }
        });

        // Real-time validation feedback
        function validateKode() {
            const value = kodeMapel.value;
            const isValid = value.length >= 3 && value.length <= 10 && /^[A-Z0-9]+$/.test(value);
            
            if (value && !isValid) {
                kodeMapel.setCustomValidity('Kode harus 3-10 karakter, hanya huruf kapital dan angka');
            } else {
                kodeMapel.setCustomValidity('');
            }
        }

        kodeMapel.addEventListener('input', validateKode);
        kodeMapel.addEventListener('blur', validateKode);

        // Form submission enhancement
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Menyimpan...';
        });
    });
    </script>
</body>
</html>