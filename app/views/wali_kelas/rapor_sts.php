<!-- Main Content -->
<main class="flex-1 overflow-y-auto bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-4 md:p-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-secondary-800 flex items-center gap-3">
                    <div class="gradient-danger p-3 rounded-2xl shadow-lg">
                        <i data-lucide="file-bar-chart" class="w-8 h-8 text-white"></i>
                    </div>
                    Rapor Tengah Semester (STS)
                </h1>
                <p class="text-secondary-600 mt-2 ml-1">Kelas: <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '-'); ?></p>
            </div>
            <button onclick="window.location.href='<?= BASEURL; ?>/raporSTS/cetakKelas'" 
                    class="gradient-danger text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Cetak Semua Rapor
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-effect rounded-2xl shadow-xl p-6 border border-white/20">
            <div class="flex items-center justify-between">
                <div class="bg-primary-100 p-3 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8 text-primary-600"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm text-secondary-600 font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold text-secondary-800"><?= count($data['siswa_list'] ?? []); ?></p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-xl p-6 border border-white/20">
            <div class="flex items-center justify-between">
                <div class="bg-success-100 p-3 rounded-xl">
                    <i data-lucide="book-open" class="w-8 h-8 text-success-600"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm text-secondary-600 font-medium">Mata Pelajaran</p>
                    <p class="text-3xl font-bold text-secondary-800"><?= count($data['mapel_list'] ?? []); ?></p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl shadow-xl p-6 border border-white/20">
            <div class="flex items-center justify-between">
                <div class="bg-warning-100 p-3 rounded-xl">
                    <i data-lucide="calendar" class="w-8 h-8 text-warning-600"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm text-secondary-600 font-medium">Semester</p>
                    <p class="text-lg font-bold text-secondary-800"><?= $_SESSION['nama_semester_aktif'] ?? '-'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Siswa -->
    <div class="glass-effect rounded-2xl shadow-xl overflow-hidden border border-white/20">
        <div class="p-6 bg-gradient-to-r from-danger-500/10 to-red-500/10 border-b border-white/20">
            <h2 class="text-xl font-bold text-secondary-800 flex items-center gap-2">
                <i data-lucide="list" class="w-6 h-6 text-danger-600"></i>
                Daftar Siswa - Rapor STS
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-secondary-50 border-b border-secondary-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">NISN</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-secondary-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-secondary-100">
                    <?php if (empty($data['siswa_list'])): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-secondary-500">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="inbox" class="w-16 h-16 text-secondary-300"></i>
                                    <p class="text-lg font-medium">Belum ada data siswa</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($data['siswa_list'] as $siswa): ?>
                            <tr class="hover:bg-white/80 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-secondary-600"><?= $no++; ?></td>
                                <td class="px-6 py-4 text-sm text-secondary-600"><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-primary-100 p-2 rounded-lg">
                                            <i data-lucide="user" class="w-4 h-4 text-primary-600"></i>
                                        </div>
                                        <span class="font-semibold text-secondary-800"><?= htmlspecialchars($siswa['nama_siswa'] ?? '-'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= BASEURL; ?>/raporSTS/generate/<?= $siswa['id_siswa']; ?>" 
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 shadow-md hover:shadow-lg">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                            Lihat
                                        </a>
                                        <a href="<?= BASEURL; ?>/raporSTS/cetak/<?= $siswa['id_siswa']; ?>" 
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 shadow-md hover:shadow-lg">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                            Cetak PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Informasi -->
    <div class="mt-6 glass-effect rounded-xl p-4 border border-white/20 bg-blue-50">
        <div class="flex items-start gap-3">
            <div class="bg-blue-100 p-2 rounded-lg">
                <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-secondary-800 mb-2">Informasi Rapor STS</h3>
                <ul class="text-sm text-secondary-600 space-y-1">
                    <li>• Rapor STS berisi nilai tengah semester dari semua mata pelajaran</li>
                    <li>• Nilai dihitung dari: Rata-rata Harian (40%) + Nilai STS (60%)</li>
                    <li>• Pastikan semua nilai harian dan STS sudah diinput sebelum mencetak rapor</li>
                    <li>• Gunakan tombol "Cetak Semua Rapor" untuk cetak rapor seluruh kelas sekaligus</li>
                </ul>
            </div>
        </div>
    </div>
</main>
