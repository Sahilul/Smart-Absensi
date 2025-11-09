<!-- Main Content -->
<main class="flex-1 overflow-y-auto bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-4 md:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-secondary-800 flex items-center gap-3">
                    <div class="gradient-primary p-3 rounded-2xl shadow-lg">
                        <i data-lucide="user-check" class="w-8 h-8 text-white"></i>
                    </div>
                    Kelola Wali Kelas
                </h1>
                <p class="text-secondary-600 mt-2 ml-1">Atur penugasan wali kelas untuk setiap kelas</p>
            </div>
            <button onclick="openTambahModal()" class="gradient-success text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                Tambah Wali Kelas
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Info TP Aktif -->
    <div class="mb-6 glass-effect p-4 rounded-xl border border-white/20">
        <div class="flex items-center gap-3">
            <div class="bg-primary-100 p-2 rounded-lg">
                <i data-lucide="calendar" class="w-5 h-5 text-primary-600"></i>
            </div>
            <div>
                <p class="text-sm text-secondary-600">Tahun Pelajaran Aktif:</p>
                <p class="font-bold text-secondary-800"><?= htmlspecialchars($data['tp']['nama_tp'] ?? 'Tidak ada'); ?></p>
            </div>
        </div>
    </div>

    <!-- Tabel Wali Kelas -->
    <div class="glass-effect rounded-2xl shadow-xl overflow-hidden border border-white/20">
        <div class="p-6 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border-b border-white/20">
            <h2 class="text-xl font-bold text-secondary-800 flex items-center gap-2">
                <i data-lucide="list" class="w-6 h-6 text-primary-600"></i>
                Daftar Wali Kelas
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-secondary-50 border-b border-secondary-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">Nama Guru</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-secondary-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-secondary-600 uppercase tracking-wider">Tahun Pelajaran</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-secondary-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-secondary-100">
                    <?php if (empty($data['wali_kelas'])): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-secondary-500">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="inbox" class="w-16 h-16 text-secondary-300"></i>
                                    <p class="text-lg font-medium">Belum ada data wali kelas</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($data['wali_kelas'] as $wk): ?>
                            <tr class="hover:bg-white/80 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-secondary-600"><?= $no++; ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-success-100 p-2 rounded-lg">
                                            <i data-lucide="user" class="w-4 h-4 text-success-600"></i>
                                        </div>
                                        <span class="font-semibold text-secondary-800"><?= htmlspecialchars($wk['nama_guru']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-primary-100 text-primary-700">
                                        <?= htmlspecialchars($wk['nama_kelas']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-secondary-600">
                                    <?= htmlspecialchars($wk['nama_tp']); ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="confirmHapus(<?= $wk['id_walikelas']; ?>, '<?= htmlspecialchars($wk['nama_guru']); ?>')" 
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 shadow-md hover:shadow-lg">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Tambah Wali Kelas -->
<div id="tambahModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="glass-effect rounded-2xl shadow-2xl max-w-md w-full border border-white/20 animate-scale-in">
        <div class="p-6 border-b border-white/20 bg-gradient-to-r from-indigo-500/10 to-purple-500/10">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-secondary-800 flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-6 h-6 text-primary-600"></i>
                    Tambah Wali Kelas
                </h3>
                <button onclick="closeTambahModal()" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        
        <form action="<?= BASEURL; ?>/waliKelas/tambah" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id_tp" value="<?= $data['tp']['id_tp'] ?? ''; ?>">
            
            <div>
                <label class="block text-sm font-semibold text-secondary-700 mb-2">Tahun Pelajaran</label>
                <input type="text" value="<?= htmlspecialchars($data['tp']['nama_tp'] ?? ''); ?>" readonly 
                       class="w-full px-4 py-3 bg-secondary-50 border border-secondary-200 rounded-lg text-secondary-600">
            </div>

            <div>
                <label class="block text-sm font-semibold text-secondary-700 mb-2">Pilih Guru <span class="text-red-500">*</span></label>
                <select name="id_guru" required 
                        class="w-full px-4 py-3 bg-white border border-secondary-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach($data['guru'] as $guru): ?>
                        <option value="<?= $guru['id_guru']; ?>"><?= htmlspecialchars($guru['nama_guru']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-secondary-700 mb-2">Pilih Kelas <span class="text-red-500">*</span></label>
                <select name="id_kelas" required 
                        class="w-full px-4 py-3 bg-white border border-secondary-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($data['kelas'] as $kelas): ?>
                        <option value="<?= $kelas['id_kelas']; ?>"><?= htmlspecialchars($kelas['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeTambahModal()" 
                        class="flex-1 px-4 py-3 bg-secondary-100 text-secondary-700 rounded-lg font-semibold hover:bg-secondary-200 transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-3 gradient-success text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTambahModal() {
        document.getElementById('tambahModal').classList.remove('hidden');
        document.getElementById('tambahModal').classList.add('flex');
    }

    function closeTambahModal() {
        document.getElementById('tambahModal').classList.add('hidden');
        document.getElementById('tambahModal').classList.remove('flex');
    }

    function confirmHapus(id, namaGuru) {
        if (confirm(`Apakah Anda yakin ingin menghapus ${namaGuru} sebagai wali kelas?`)) {
            window.location.href = '<?= BASEURL; ?>/waliKelas/hapus/' + id;
        }
    }

    // Close modal when clicking outside
    document.getElementById('tambahModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTambahModal();
        }
    });
</script>
