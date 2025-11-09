<?php /* File: app/views/admin/profil.php */ ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                    <i data-lucide="user-circle" class="w-8 h-8 mr-3 text-primary-500"></i>
                    Profil Admin
                </h2>
                <p class="text-secondary-600 mt-2">Kelola informasi profil dan username Anda</p>
            </div>
            <div class="hidden md:block">
                <div class="gradient-primary p-3 rounded-xl">
                    <i data-lucide="id-card" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Form Profil -->
    <div class="max-w-3xl mx-auto">
        <div class="glass-effect rounded-xl p-8 border border-white/20 shadow-lg">
            <form method="POST" action="<?= BASEURL; ?>/admin/simpanProfil">
            
            <div class="space-y-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-secondary-700 mb-2">
                        <i data-lucide="at-sign" class="w-4 h-4 inline mr-1"></i>
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="<?= htmlspecialchars($data['admin']['username'] ?? ''); ?>"
                           required
                           class="w-full px-4 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                    <p class="text-xs text-secondary-500 mt-1">Username untuk login ke sistem</p>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-secondary-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline mr-1"></i>
                        Nama Lengkap
                    </label>
                    <input type="text" 
                           id="nama_lengkap" 
                           name="nama_lengkap" 
                           value="<?= htmlspecialchars($data['admin']['nama_lengkap'] ?? ''); ?>"
                           class="w-full px-4 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                    <p class="text-xs text-secondary-500 mt-1">Nama lengkap Anda (opsional)</p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Catatan Penting</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pastikan username tidak mengandung spasi atau karakter khusus</li>
                            <li>Username akan digunakan untuk login ke sistem</li>
                            <li>Jika mengubah username, gunakan username baru saat login berikutnya</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-secondary-200 flex justify-between items-center">
                <a href="<?= BASEURL; ?>/admin/dashboard" 
                   class="text-secondary-600 hover:text-secondary-800 font-medium flex items-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Kembali ke Dashboard
                </a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
        </div>
    </div>

    <!-- Link ke Ganti Sandi -->
    <div class="mt-6 max-w-3xl mx-auto">
        <div class="bg-gradient-to-r from-warning-50 to-orange-50 border border-warning-200 rounded-lg p-4 flex items-center justify-between">
            <div class="flex items-center">
                <i data-lucide="lock" class="w-5 h-5 text-warning-600 mr-3"></i>
                <div>
                    <p class="font-medium text-secondary-800">Ingin mengubah password?</p>
                    <p class="text-sm text-secondary-600">Gunakan halaman Ganti Sandi untuk keamanan</p>
                </div>
            </div>
            <a href="<?= BASEURL; ?>/admin/gantiSandi" 
               class="inline-flex items-center px-4 py-2 bg-warning-600 hover:bg-warning-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm">
                <i data-lucide="key" class="w-4 h-4 mr-2"></i>
                Ganti Sandi
            </a>
        </div>
    </div>
</main>
