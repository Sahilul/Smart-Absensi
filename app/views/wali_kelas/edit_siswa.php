<main class="flex-1 overflow-x-hidden overflow-y-auto bg-secondary-50 p-4 md:p-6">
    <!-- Header -->
    <div class="mb-4">
        <div class="bg-white shadow-sm rounded-xl p-4 md:p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h4 class="text-lg md:text-xl font-bold text-slate-800 mb-1"><?= $data['judul'] ?></h4>
                    <p class="text-slate-500 text-sm">
                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '-') ?></span>
                        <span class="mx-2">•</span>
                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['session_info']['nama_semester'] ?? '') ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <!-- Form Edit Siswa -->
    <div class="bg-white shadow-sm rounded-xl p-4 md:p-6">
        <form action="<?= BASEURL ?>/waliKelas/updateSiswa" method="POST">
            <input type="hidden" name="id_siswa" value="<?= $data['siswa']['id_siswa'] ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                
                <!-- NISN -->
                <div class="md:col-span-2">
                    <label for="nisn" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="hash" class="w-4 h-4 inline-block mr-1"></i>
                        NISN <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nisn" 
                        name="nisn" 
                        value="<?= htmlspecialchars($data['siswa']['nisn'] ?? '') ?>"
                        required
                        class="w-full border border-slate-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                        placeholder="Contoh: 0012345678"
                    >
                </div>

                <!-- Nama Siswa -->
                <div class="md:col-span-2">
                    <label for="nama_siswa" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline-block mr-1"></i>
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nama_siswa" 
                        name="nama_siswa" 
                        value="<?= htmlspecialchars($data['siswa']['nama_siswa'] ?? '') ?>"
                        required
                        class="w-full border border-slate-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                        placeholder="Contoh: Ahmad Fauzi"
                    >
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="users" class="w-4 h-4 inline-block mr-1"></i>
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="jenis_kelamin" 
                        name="jenis_kelamin" 
                        required
                        class="w-full border border-slate-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                    >
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= ($data['siswa']['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($data['siswa']['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1"></i>
                        Tanggal Lahir
                    </label>
                    <input 
                        type="date" 
                        id="tanggal_lahir" 
                        name="tanggal_lahir" 
                        value="<?= htmlspecialchars($data['siswa']['tgl_lahir'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                    >
                </div>

                <!-- Password Baru (Optional) -->
                <div class="md:col-span-2">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h5 class="font-semibold text-yellow-900 text-sm mb-3 flex items-center gap-2">
                            <i data-lucide="key" class="w-4 h-4"></i>
                            Ubah Password Login (Opsional)
                        </h5>
                        <p class="text-xs text-yellow-700 mb-3">Kosongkan jika tidak ingin mengubah password</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password_baru" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Password Baru
                                </label>
                                <input 
                                    type="password" 
                                    id="password_baru" 
                                    name="password_baru" 
                                    class="w-full border border-slate-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                                    placeholder="Masukkan password baru"
                                >
                            </div>
                            <div>
                                <label for="konfirmasi_password" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Konfirmasi Password
                                </label>
                                <input 
                                    type="password" 
                                    id="konfirmasi_password" 
                                    name="konfirmasi_password" 
                                    class="w-full border border-slate-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                                    placeholder="Ulangi password baru"
                                >
                            </div>
                        </div>
                        <p class="text-xs text-yellow-700 mt-2">Password akan digunakan untuk login siswa ke aplikasi</p>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button 
                    type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm"
                    onclick="return validatePassword()"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
                <a 
                    href="<?= BASEURL ?>/waliKelas/daftarSiswa" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors"
                >
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-5 bg-sky-50 border border-sky-200 rounded-xl p-4">
        <div class="flex gap-3">
            <div class="shrink-0">
                <i data-lucide="info" class="w-5 h-5 text-sky-600"></i>
            </div>
            <div class="flex-1">
                <h6 class="font-semibold text-sky-900 text-sm mb-1">Informasi</h6>
                <ul class="text-xs text-sky-700 space-y-1 list-disc list-inside">
                    <li>Field dengan tanda <span class="text-red-500">*</span> wajib diisi</li>
                    <li>NISN digunakan sebagai username login siswa</li>
                    <li>Password hanya diubah jika Anda mengisi field password baru</li>
                    <li>Pastikan data yang diinput sudah benar sebelum menyimpan</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Validasi password
function validatePassword() {
    const passwordBaru = document.getElementById('password_baru').value;
    const konfirmasiPassword = document.getElementById('konfirmasi_password').value;
    
    if (passwordBaru || konfirmasiPassword) {
        if (passwordBaru !== konfirmasiPassword) {
            alert('Password dan konfirmasi password tidak sama!');
            return false;
        }
        
        if (passwordBaru.length < 6) {
            alert('Password minimal 6 karakter!');
            return false;
        }
    }
    
    return true;
}
</script>
