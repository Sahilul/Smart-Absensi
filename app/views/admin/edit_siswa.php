<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
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
                <a href="<?= BASEURL; ?>/admin/siswa" 
                   class="text-gray-500 hover:text-indigo-600 mr-4 p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit Data Siswa</h2>
                    <p class="text-gray-600 mt-1">Perbarui informasi siswa yang sudah ada</p>
                </div>
            </div>
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?= BASEURL; ?>/admin/siswa" class="hover:text-indigo-600">Manajemen Siswa</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
                <span class="text-gray-900 font-medium">Edit Siswa</span>
            </div>
        </div>

        <!-- Current Student Info Card -->
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5 text-indigo-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700">
                        <span class="font-medium">Mengedit Data:</span> 
                        <?= htmlspecialchars($data['siswa']['nama_siswa']); ?> 
                        (NISN: <?= htmlspecialchars($data['siswa']['nisn']); ?>)<br>
                        <span class="text-xs">Pastikan perubahan data sudah benar sebelum menyimpan.</span>
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
                        <h3 class="text-lg font-semibold text-gray-800">Form Edit Data Siswa</h3>
                        <p class="text-sm text-gray-600">Perbarui informasi yang diperlukan</p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <form action="<?= BASEURL; ?>/admin/prosesUpdateSiswa" method="POST">
                    <input type="hidden" name="id_siswa" value="<?= $data['siswa']['id_siswa']; ?>">
                    
                    <div class="space-y-6">
                        <!-- Data Pribadi Section -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                Data Pribadi
                            </h4>

                            <div class="space-y-4">
                                <!-- Nama Lengkap -->
                                <div>
                                    <label for="nama_siswa" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                                        Nama Lengkap Siswa
                                    </label>
                                    <input type="text" 
                                           name="nama_siswa" 
                                           id="nama_siswa" 
                                           required 
                                           value="<?= htmlspecialchars($data['siswa']['nama_siswa']); ?>"
                                           placeholder="Masukkan nama lengkap siswa"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                </div>

                                <!-- NISN -->
                                <div>
                                    <label for="nisn" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="hash" class="w-4 h-4 inline mr-2"></i>
                                        NISN <span class="text-gray-500">(Username)</span>
                                    </label>
                                    <input type="text" 
                                           name="nisn" 
                                           id="nisn" 
                                           required 
                                           value="<?= htmlspecialchars($data['siswa']['nisn']); ?>"
                                           placeholder="Contoh: 0123456789"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <p class="text-xs text-gray-500 mt-1">NISN digunakan sebagai username untuk login</p>
                                </div>

                                <!-- Row: Jenis Kelamin dan Tanggal Lahir -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                                            Jenis Kelamin
                                        </label>
                                        <select name="jenis_kelamin" 
                                                id="jenis_kelamin" 
                                                required 
                                                class="mt-1 block w-full px-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                            <option value="L" <?= ($data['siswa']['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>👨 Laki-laki</option>
                                            <option value="P" <?= ($data['siswa']['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>👩 Perempuan</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="tgl_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                                            Tanggal Lahir
                                        </label>
                                        <input type="date" 
                                               name="tgl_lahir" 
                                               id="tgl_lahir" 
                                               value="<?= $data['siswa']['tgl_lahir']; ?>"
                                               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <p class="text-xs text-gray-500 mt-1">Opsional</p>
                                    </div>
                                </div>

                                <!-- Status Siswa -->
                                <div>
                                    <label for="status_siswa" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="shield-check" class="w-4 h-4 inline mr-2"></i>
                                        Status Siswa
                                    </label>
                                    <select name="status_siswa" 
                                            id="status_siswa" 
                                            required 
                                            class="mt-1 block w-full px-4 py-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <option value="aktif" <?= ($data['siswa']['status_siswa'] == 'aktif') ? 'selected' : ''; ?>>
                                            ✅ Aktif
                                        </option>
                                        <option value="pindah" <?= ($data['siswa']['status_siswa'] == 'pindah') ? 'selected' : ''; ?>>
                                            📦 Pindah
                                        </option>
                                        <option value="lulus" <?= ($data['siswa']['status_siswa'] == 'lulus') ? 'selected' : ''; ?>>
                                            🎓 Lulus
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Status mempengaruhi akses login siswa</p>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="lock" class="w-4 h-4 mr-2"></i>
                                Ubah Password
                            </h4>
                            
                            <div class="mb-4 p-3 bg-yellow-100 rounded-lg border-l-4 border-yellow-400">
                                <p class="text-sm text-yellow-800">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-2"></i>
                                    <strong>Perhatian:</strong> Kosongkan field ini jika tidak ingin mengubah password siswa.
                                </p>
                            </div>

                            <div>
                                <label for="password_baru" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="key" class="w-4 h-4 inline mr-2"></i>
                                    Password Baru
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           name="password_baru" 
                                           id="password_baru" 
                                           placeholder="Masukkan password baru (opsional)"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 pr-10">
                                    <button type="button" 
                                            onclick="togglePassword()" 
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
                                        <i data-lucide="eye" id="eye-icon" class="w-4 h-4 text-gray-400 hover:text-gray-600"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="<?= BASEURL; ?>/admin/siswa" 
                               class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition-colors duration-200 flex items-center justify-center">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow-sm flex items-center justify-center">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Update Data Siswa
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
                    <strong>Tips:</strong> Perubahan status siswa akan mempengaruhi kemampuan mereka untuk login ke sistem
                </p>
            </div>
        </div>
    </main>

    <script>
    // Toggle password visibility
    function togglePassword() {
        const passwordField = document.getElementById('password_baru');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordField.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    // Form validation and enhancement
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // NISN validation
        const nisnField = document.getElementById('nisn');
        nisnField.addEventListener('input', function(e) {
            // Only allow numbers
            e.target.value = e.target.value.replace(/\D/g, '');
            
            // Limit to 10 digits
            if (e.target.value.length > 10) {
                e.target.value = e.target.value.slice(0, 10);
            }
        });

        // Name field enhancement
        const namaField = document.getElementById('nama_siswa');
        namaField.addEventListener('input', function(e) {
            // Capitalize first letter of each word
            e.target.value = e.target.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        // Status change confirmation
        const statusField = document.getElementById('status_siswa');
        const originalStatus = statusField.value;
        
        statusField.addEventListener('change', function(e) {
            if (originalStatus !== e.target.value) {
                if (e.target.value === 'pindah' || e.target.value === 'lulus') {
                    const confirmed = confirm(
                        `Anda akan mengubah status siswa menjadi "${e.target.options[e.target.selectedIndex].text.slice(2)}".\n\n` +
                        'Siswa dengan status ini tidak akan bisa login ke sistem. Lanjutkan?'
                    );
                    
                    if (!confirmed) {
                        e.target.value = originalStatus;
                    }
                }
            }
        });

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