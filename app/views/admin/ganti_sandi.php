<?php /* File: app/views/admin/ganti_sandi.php */ ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                    <i data-lucide="lock" class="w-8 h-8 mr-3 text-warning-500"></i>
                    Ganti Sandi
                </h2>
                <p class="text-secondary-600 mt-2">Perbarui password Anda untuk keamanan akun</p>
            </div>
            <div class="hidden md:block">
                <div class="gradient-warning p-3 rounded-xl">
                    <i data-lucide="key" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Form Ganti Sandi -->
    <div class="max-w-2xl mx-auto"><div class="glass-effect rounded-xl p-8 border border-white/20 shadow-lg">
        <form method="POST" action="<?= BASEURL; ?>/admin/simpanSandi">
            
            <div class="space-y-6">
                <!-- Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-secondary-700 mb-2">
                        <i data-lucide="lock" class="w-4 h-4 inline mr-1"></i>
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           minlength="6"
                           class="w-full px-4 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                    <p class="text-xs text-secondary-500 mt-1">Minimal 6 karakter</p>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password2" class="block text-sm font-medium text-secondary-700 mb-2">
                        <i data-lucide="lock-keyhole" class="w-4 h-4 inline mr-1"></i>
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password2" 
                           name="password2" 
                           required
                           minlength="6"
                           class="w-full px-4 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                    <p class="text-xs text-secondary-500 mt-1">Ketik ulang password baru</p>
                </div>

                <!-- Security Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start">
                    <i data-lucide="shield-alert" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-1">Tips Keamanan Password</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Gunakan kombinasi huruf besar, kecil, dan angka</li>
                            <li>Hindari menggunakan informasi pribadi</li>
                            <li>Jangan bagikan password ke siapapun</li>
                            <li>Ganti password secara berkala</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-secondary-200 flex justify-between items-center">
                <a href="<?= BASEURL; ?>/admin/profil" 
                   class="text-secondary-600 hover:text-secondary-800 font-medium flex items-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Kembali ke Profil
                </a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Password Baru
                </button>
            </div>
        </form>
        </div>
    </div>
</main>
