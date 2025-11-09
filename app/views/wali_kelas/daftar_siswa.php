<?php
// File: app/views/wali_kelas/daftar_siswa.php
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-secondary-800 mb-2">
                    <i data-lucide="users-round" class="inline-block w-8 h-8 mr-2 text-primary-600"></i>
                    Daftar Siswa Kelas
                </h1>
                <p class="text-secondary-600">Kelola dan monitor siswa di kelas Anda</p>
            </div>
        </div>
    </div>

    <!-- Info Kelas -->
    <div class="glass-effect rounded-2xl p-6 mb-6 border border-white/20">
        <div class="flex items-center gap-4">
            <div class="gradient-primary p-4 rounded-xl">
                <i data-lucide="school" class="w-8 h-8 text-white"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-secondary-800">
                    Kelas <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '-'); ?>
                </h3>
                <p class="text-secondary-600">
                    Tahun Pelajaran: <?= htmlspecialchars($data['wali_kelas_info']['nama_tp'] ?? '-'); ?>
                </p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-sm text-secondary-500">Total Siswa</p>
                <p class="text-3xl font-bold text-primary-600">
                    <?= count($data['siswa_list'] ?? []); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Statistik Gender -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <?php 
        $totalSiswa = count($data['siswa_list'] ?? []);
        $lakiLaki = 0;
        $perempuan = 0;
        
        foreach ($data['siswa_list'] ?? [] as $siswa) {
            if ($siswa['jenis_kelamin'] == 'L') {
                $lakiLaki++;
            } else {
                $perempuan++;
            }
        }
        ?>
        
        <!-- Total Siswa -->
        <div class="glass-effect rounded-2xl p-6 border border-white/20 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary-500 mb-1">Total Siswa</p>
                    <h3 class="text-3xl font-bold text-secondary-800"><?= $totalSiswa; ?></h3>
                    <p class="text-xs text-secondary-400 mt-2">Siswa Aktif</p>
                </div>
                <div class="gradient-primary p-4 rounded-xl">
                    <i data-lucide="users" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Laki-laki -->
        <div class="glass-effect rounded-2xl p-6 border border-white/20 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary-500 mb-1">Laki-laki</p>
                    <h3 class="text-3xl font-bold text-blue-600"><?= $lakiLaki; ?></h3>
                    <p class="text-xs text-secondary-400 mt-2">
                        <?= $totalSiswa > 0 ? round(($lakiLaki / $totalSiswa) * 100, 1) : 0; ?>% dari total
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-4 rounded-xl">
                    <i data-lucide="user" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Perempuan -->
        <div class="glass-effect rounded-2xl p-6 border border-white/20 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary-500 mb-1">Perempuan</p>
                    <h3 class="text-3xl font-bold text-pink-600"><?= $perempuan; ?></h3>
                    <p class="text-xs text-secondary-400 mt-2">
                        <?= $totalSiswa > 0 ? round(($perempuan / $totalSiswa) * 100, 1) : 0; ?>% dari total
                    </p>
                </div>
                <div class="bg-gradient-to-br from-pink-400 to-pink-600 p-4 rounded-xl">
                    <i data-lucide="user" class="w-8 h-8 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Siswa -->
    <div class="glass-effect rounded-2xl border border-white/20 overflow-hidden">
        <!-- Header Tabel -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i data-lucide="list" class="w-5 h-5 mr-2"></i>
                Daftar Siswa
            </h2>
        </div>

        <!-- Search & Filter -->
        <div class="p-4 bg-white/50 border-b border-white/20">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        id="searchSiswa" 
                        placeholder="Cari nama atau NISN siswa..."
                        class="w-full px-4 py-2 rounded-lg border border-secondary-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                </div>
                <button class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-secondary-200">
                <thead class="bg-secondary-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-secondary-200" id="tableSiswa">
                    <?php if (!empty($data['siswa_list'])): ?>
                        <?php $no = 1; foreach ($data['siswa_list'] as $siswa): ?>
                            <tr class="hover:bg-secondary-50 transition-colors siswa-row">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900"><?= $no++; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900 siswa-nisn">
                                    <?= htmlspecialchars($siswa['nisn']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap siswa-nama">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold mr-3">
                                            <?= strtoupper(substr($siswa['nama_siswa'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-secondary-900">
                                                <?= htmlspecialchars($siswa['nama_siswa']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900">
                                    <?php if ($siswa['jenis_kelamin'] == 'L'): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i data-lucide="user" class="w-3 h-3 inline"></i> Laki-laki
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">
                                            <i data-lucide="user" class="w-3 h-3 inline"></i> Perempuan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($siswa['status_siswa'] == 'aktif'): ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <?= ucfirst($siswa['status_siswa']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <a href="<?= BASEURL; ?>/waliKelas/editSiswa/<?= $siswa['id_siswa']; ?>" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                            <i data-lucide="edit" class="w-3 h-3"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-secondary-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-secondary-300"></i>
                                <p>Belum ada siswa di kelas ini</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-white/50 border-t border-white/20">
            <p class="text-sm text-secondary-600">
                Menampilkan <span class="font-semibold"><?= count($data['siswa_list'] ?? []); ?></span> siswa
            </p>
        </div>
    </div>
</div>

<!-- Script Search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchSiswa');
    const tableRows = document.querySelectorAll('.siswa-row');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();

        tableRows.forEach(row => {
            const nama = row.querySelector('.siswa-nama').textContent.toLowerCase();
            const nisn = row.querySelector('.siswa-nisn').textContent.toLowerCase();

            if (nama.includes(searchTerm) || nisn.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
