<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Guru</title>
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
                <a href="<?= BASEURL; ?>/admin/guru" 
                   class="text-gray-500 hover:text-indigo-600 mr-4 p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit Data Guru</h2>
                    <p class="text-gray-600 mt-1">Perbarui informasi guru yang sudah ada</p>
                </div>
            </div>
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?= BASEURL; ?>/admin/guru" class="hover:text-indigo-600">Manajemen Guru</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
                <span class="text-gray-900 font-medium">Edit Guru</span>
            </div>
        </div>

        <!-- Current Teacher Info Card -->
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-indigo-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700">
                        <span class="font-medium">Mengedit Data:</span> 
                        <?= htmlspecialchars($data['guru']['nama_guru']); ?> 
                        (NIK: <?= htmlspecialchars($data['guru']['nik']); ?>)<br>
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
                        <h3 class="text-lg font-semibold text-gray-800">Form Edit Data Guru</h3>
                        <p class="text-sm text-gray-600">Perbarui informasi yang diperlukan</p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-8">
                <form action="<?= BASEURL; ?>/admin/prosesUpdateGuru" method="POST">
                    <input type="hidden" name="id_guru" value="<?= $data['guru']['id_guru']; ?>">
                    
                    <div class="space-y-6">
                        <!-- Data Pribadi Section -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <i data-lucide="graduation-cap" class="w-4 h-4 mr-2"></i>
                                Data Pribadi
                            </h4>

                            <div class="space-y-4">
                                <!-- Nama Lengkap -->
                                <div>
                                    <label for="nama_guru" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                                        Nama Lengkap Guru
                                    </label>
                                    <input type="text" 
                                           name="nama_guru" 
                                           id="nama_guru" 
                                           required 
                                           value="<?= htmlspecialchars($data['guru']['nama_guru']); ?>"
                                           placeholder="Masukkan nama lengkap guru"
                                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                </div>

                                <!-- Row: NIK dan Email -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i data-lucide="id-card" class="w-4 h-4 inline mr-2"></i>
                                            NIK <span class="text-gray-500">(Username)</span>
                                        </label>
                                        <input type="text" 
                                               name="nik" 
                                               id="nik" 
                                               required 
                                               value="<?= htmlspecialchars($data['guru']['nik']); ?>"
                                               placeholder="Contoh: 1234567890123456"
                                               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <p class="text-xs text-gray-500 mt-1">NIK digunakan sebagai username untuk login</p>
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i data-lucide="mail" class="w-4 h-4 inline mr-2"></i>
                                            Email
                                        </label>
                                        <input type="email" 
                                               name="email" 
                                               id="email" 
                                               value="<?= htmlspecialchars($data['guru']['email']); ?>"
                                               placeholder="contoh@email.com"
                                               class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                        <p class="text-xs text-gray-500 mt-1">Opsional</p>
                                    </div>
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
                                    <strong>Perhatian:</strong> Kosongkan field ini jika tidak ingin mengubah password guru.
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
                            <a href="<?= BASEURL; ?>/admin/guru" 
                               class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition-colors duration-200 flex items-center justify-center">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow-sm flex items-center justify-center">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Update Data Guru
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
                    <strong>Tips:</strong> Perubahan NIK akan mempengaruhi username untuk login guru
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

        // NIK validation
        const nikField = document.getElementById('nik');
        nikField.addEventListener('input', function(e) {
            // Only allow numbers
            e.target.value = e.target.value.replace(/\D/g, '');
            
            // Limit to 16 digits for NIK
            if (e.target.value.length > 16) {
                e.target.value = e.target.value.slice(0, 16);
            }
        });

        // Name field enhancement
        const namaField = document.getElementById('nama_guru');
        namaField.addEventListener('input', function(e) {
            // Capitalize first letter of each word
            e.target.value = e.target.value.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        });

        // Email validation enhancement
        const emailField = document.getElementById('email');
        emailField.addEventListener('blur', function(e) {
            const email = e.target.value;
            if (email && !email.includes('@')) {
                e.target.setCustomValidity('Format email tidak valid');
                e.target.reportValidity();
            } else {
                e.target.setCustomValidity('');
            }
        });

        // NIK change confirmation
        const originalNik = nikField.value;
        nikField.addEventListener('change', function(e) {
            if (originalNik !== e.target.value && originalNik !== '') {
                const confirmed = confirm(
                    `Anda akan mengubah NIK dari "${originalNik}" menjadi "${e.target.value}".\n\n` +
                    'NIK digunakan sebagai username untuk login. Pastikan perubahan ini benar. Lanjutkan?'
                );
                
                if (!confirmed) {
                    e.target.value = originalNik;
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