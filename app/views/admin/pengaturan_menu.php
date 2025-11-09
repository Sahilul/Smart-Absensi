<?php /* File: app/views/admin/pengaturan_menu.php */ ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                    <i data-lucide="settings" class="w-8 h-8 mr-3 text-primary-500"></i>
                    Pengaturan Menu
                </h2>
                <p class="text-secondary-600 mt-2">Kelola visibilitas menu untuk role Guru dan Wali Kelas</p>
            </div>
            <div class="hidden md:block">
                <div class="gradient-primary p-3 rounded-xl">
                    <i data-lucide="eye" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Form Pengaturan Menu -->
    <div class="glass-effect rounded-xl p-8 border border-white/20 shadow-lg">
        <form method="POST" action="<?= BASEURL; ?>/admin/simpanPengaturanMenu">
            
            <div class="space-y-6">
                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Tentang Pengaturan Menu</p>
                        <p>Nonaktifkan menu yang tidak diperlukan untuk menyederhanakan antarmuka bagi guru dan wali kelas. Perubahan akan berlaku setelah user login ulang atau refresh halaman.</p>
                    </div>
                </div>

                <!-- Menu Input Nilai & Rapor -->
                <div class="border border-secondary-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <i data-lucide="file-edit" class="w-5 h-5 text-warning-600 mr-2"></i>
                                <h3 class="text-lg font-semibold text-secondary-800">Menu Input Nilai & Rapor</h3>
                            </div>
                            <p class="text-secondary-600 text-sm mb-3">
                                Termasuk: Input Nilai (Harian, STS, SAS), Monitoring Nilai, Pengaturan Rapor, Cetak Rapor
                            </p>
                            <div class="bg-secondary-50 rounded px-3 py-2 text-xs text-secondary-600">
                                <p><strong>Role yang terpengaruh:</strong> Guru, Wali Kelas</p>
                                <p class="mt-1"><strong>Menu yang di-hide:</strong> Section "Input Nilai" di dashboard guru, "Monitoring Nilai" dan "Cetak Rapor" di sidebar wali kelas</p>
                            </div>
                        </div>
                        <div class="ml-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="menu_input_nilai" 
                                       value="1" 
                                       <?= $data['menu_input_nilai_enabled'] ? 'checked' : ''; ?>
                                       class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-secondary-700 whitespace-nowrap">
                                    <?= $data['menu_input_nilai_enabled'] ? 'Aktif' : 'Nonaktif'; ?>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Menu Pembayaran -->
                <div class="border border-secondary-200 rounded-lg p-6 hover:border-primary-300 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <i data-lucide="credit-card" class="w-5 h-5 text-success-600 mr-2"></i>
                                <h3 class="text-lg font-semibold text-secondary-800">Menu Pembayaran</h3>
                            </div>
                            <p class="text-secondary-600 text-sm mb-3">
                                Termasuk: Riwayat Pembayaran, Tagihan, Laporan Keuangan (khusus Wali Kelas)
                            </p>
                            <div class="bg-secondary-50 rounded px-3 py-2 text-xs text-secondary-600">
                                <p><strong>Role yang terpengaruh:</strong> Wali Kelas</p>
                                <p class="mt-1"><strong>Menu yang di-hide:</strong> Menu "Pembayaran" di sidebar wali kelas</p>
                            </div>
                        </div>
                        <div class="ml-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="menu_pembayaran" 
                                       value="1" 
                                       <?= $data['menu_pembayaran_enabled'] ? 'checked' : ''; ?>
                                       class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-secondary-700 whitespace-nowrap">
                                    <?= $data['menu_pembayaran_enabled'] ? 'Aktif' : 'Nonaktif'; ?>
                                </span>
                            </label>
                        </div>
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
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Warning Box -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5 flex-shrink-0"></i>
        <div class="text-sm text-yellow-800">
            <p class="font-medium mb-1">Perhatian</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Perubahan pengaturan akan mengubah file <code class="bg-yellow-100 px-1 rounded">config/config.php</code></li>
                <li>User yang sedang login perlu refresh halaman untuk melihat perubahan menu</li>
                <li>Pastikan menu yang dinonaktifkan memang tidak diperlukan</li>
            </ul>
        </div>
    </div>
</main>

<script>
// Update toggle label saat diubah
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const label = this.parentElement.querySelector('span');
        label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
    });
});
</script>
