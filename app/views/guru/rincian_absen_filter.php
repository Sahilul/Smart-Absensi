<?php /* File: app/views/guru/rincian_absen_filter.php */ ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                    <i data-lucide="calendar-check" class="w-8 h-8 mr-3 text-primary-500"></i>
                    Rincian Absen per Pertemuan
                </h2>
                <p class="text-secondary-600 mt-2">Analisis detail kehadiran siswa per pertemuan dengan filter periode</p>
            </div>
            <div class="hidden md:block">
                <div class="gradient-primary p-3 rounded-xl">
                    <i data-lucide="table" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($data['filter']['id_mapel'])): ?>
    <!-- Filter Panel - Hanya muncul jika belum pilih mapel -->
    <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg mb-8">
        <h3 class="text-lg font-semibold text-secondary-800 mb-4 flex items-center">
            <i data-lucide="book-open" class="w-5 h-5 mr-2"></i>
            Pilih Mata Pelajaran
        </h3>
        
        <form method="GET" action="<?= BASEURL; ?>/guru/rincianAbsen" class="space-y-4">
            <input type="hidden" name="periode" value="semester">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Pilih Mapel -->
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Mata Pelajaran
                    </label>
                    <select name="id_mapel" required onchange="this.form.submit()" class="w-full px-3 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                        <option value="">Pilih Mata Pelajaran</option>
                        <?php foreach ($data['daftar_mapel'] as $mapel): ?>
                            <option value="<?= $mapel['id_mapel']; ?>">
                                <?= htmlspecialchars($mapel['nama_mapel'] . ' - ' . $mapel['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Data Display -->
    <?php if (!empty($data['rincian_data']['siswa_data'])): ?>
        <!-- Info Mapel & Actions -->
        <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                <h3 class="text-xl font-semibold text-secondary-800 flex items-center">
                    <i data-lucide="book-open" class="w-6 h-6 mr-2"></i>
                    <?= htmlspecialchars($data['mapel_info']['nama_mapel'] ?? '-'); ?> - <?= htmlspecialchars($data['mapel_info']['nama_kelas'] ?? '-'); ?>
                </h3>
                <div class="flex gap-2">
                    <a href="<?= BASEURL; ?>/guru/rincianAbsen" 
                       class="inline-flex items-center px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                        Ganti Mapel
                    </a>
                    <a href="<?= BASEURL; ?>/guru/downloadRincianAbsenPDF/<?= urlencode($data['filter']['id_mapel']); ?>?periode=semester" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Download PDF
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-secondary-600">Tahun Pelajaran</p>
                    <p class="font-semibold text-secondary-800"><?= htmlspecialchars($data['mapel_info']['nama_tp'] ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-secondary-600">Semester</p>
                    <p class="font-semibold text-secondary-800"><?= htmlspecialchars($data['mapel_info']['semester'] ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-secondary-600">Total Siswa</p>
                    <p class="font-semibold text-secondary-800"><?= count($data['rincian_data']['siswa_data']); ?> siswa</p>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-white/30">
                <h3 class="text-xl font-semibold text-secondary-800 flex items-center">
                    <i data-lucide="table" class="w-5 h-5 mr-2"></i>
                    Rincian Absen per Pertemuan
                </h3>
                <p class="text-sm text-secondary-600 mt-1">
                    Total <?= count($data['rincian_data']['siswa_data']); ?> siswa • 
                    <?= count($data['rincian_data']['pertemuan_headers']); ?> pertemuan
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="bg-secondary-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider sticky left-0 bg-secondary-50 z-10">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider sticky left-12 bg-secondary-50 z-10 min-w-48">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">NIS</th>
                            
                            <!-- Header Pertemuan -->
                            <?php foreach ($data['rincian_data']['pertemuan_headers'] as $pertemuan): ?>
                                <th class="px-3 py-3 text-center text-xs font-medium text-secondary-500 uppercase tracking-wider min-w-24">
                                    <div class="flex flex-col">
                                        <span>P<?= $pertemuan['pertemuan_ke']; ?></span>
                                        <span class="text-xs text-secondary-400"><?= date('d/m', strtotime($pertemuan['tanggal'])); ?></span>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                            
                            <!-- Summary Columns -->
                            <th class="px-3 py-3 text-center text-xs font-medium text-secondary-500 uppercase tracking-wider bg-green-50">H</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-secondary-500 uppercase tracking-wider bg-blue-50">I</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-secondary-500 uppercase tracking-wider bg-yellow-50">S</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-secondary-500 uppercase tracking-wider bg-red-50">A</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-secondary-500 uppercase tracking-wider bg-secondary-100">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-secondary-200">
                        <?php 
                        $no = 1;
                        foreach ($data['rincian_data']['siswa_data'] as $siswa): 
                            $total_pertemuan = count($data['rincian_data']['pertemuan_headers']);
                            $persentase_hadir = $total_pertemuan > 0 ? round(($siswa['total_hadir'] / $total_pertemuan) * 100, 1) : 0;
                        ?>
                            <tr class="hover:bg-secondary-50">
                                <td class="px-4 py-3 text-sm text-secondary-900 sticky left-0 bg-white z-10"><?= $no++; ?></td>
                                <td class="px-4 py-3 text-sm font-medium text-secondary-900 sticky left-12 bg-white z-10">
                                    <?= htmlspecialchars($siswa['nama_siswa']); ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-secondary-600"><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
                                
                                <!-- Status per Pertemuan -->
                                <?php foreach ($data['rincian_data']['pertemuan_headers'] as $key => $pertemuan): 
                                    $pertemuan_data = null;
                                    foreach ($siswa['pertemuan'] as $p) {
                                        if ($p['pertemuan_ke'] == $pertemuan['pertemuan_ke'] && $p['tanggal'] == $pertemuan['tanggal']) {
                                            $pertemuan_data = $p;
                                            break;
                                        }
                                    }
                                    $status = $pertemuan_data['status'] ?? 'A';
                                    
                                    $status_colors = [
                                        'H' => 'bg-green-100 text-green-800',
                                        'I' => 'bg-blue-100 text-blue-800', 
                                        'S' => 'bg-yellow-100 text-yellow-800',
                                        'A' => 'bg-red-100 text-red-800'
                                    ];
                                ?>
                                    <td class="px-3 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold rounded-full <?= $status_colors[$status] ?? $status_colors['A']; ?>">
                                            <?= $status; ?>
                                        </span>
                                        <?php if ($status == 'H' && !empty($pertemuan_data['waktu_absen'])): ?>
                                            <div class="text-xs text-secondary-400 mt-1"><?= substr($pertemuan_data['waktu_absen'], 0, 5); ?></div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                
                                <!-- Summary Columns -->
                                <td class="px-3 py-3 text-center text-sm font-bold text-green-700 bg-green-50"><?= $siswa['total_hadir']; ?></td>
                                <td class="px-3 py-3 text-center text-sm font-bold text-blue-700 bg-blue-50"><?= $siswa['total_izin']; ?></td>
                                <td class="px-3 py-3 text-center text-sm font-bold text-yellow-700 bg-yellow-50"><?= $siswa['total_sakit']; ?></td>
                                <td class="px-3 py-3 text-center text-sm font-bold text-red-700 bg-red-50"><?= $siswa['total_alpha']; ?></td>
                                <td class="px-3 py-3 text-center text-sm font-bold text-secondary-800 bg-secondary-100"><?= $persentase_hadir; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif (!empty($data['filter']['id_mapel'])): ?>
        <!-- No Data State -->
        <div class="glass-effect rounded-xl p-12 border border-white/20 shadow-lg text-center">
            <div class="gradient-warning p-4 rounded-2xl inline-flex mb-6">
                <i data-lucide="calendar-x" class="w-12 h-12 text-white"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary-800 mb-3">Tidak Ada Data</h3>
            <p class="text-secondary-600 mb-6">
                Tidak ada data absensi untuk periode yang dipilih. Coba ubah filter atau periode lain.
            </p>
        </div>

    <?php else: ?>
        <!-- Select Mapel First -->
        <div class="glass-effect rounded-xl p-12 border border-white/20 shadow-lg text-center">
            <div class="gradient-primary p-4 rounded-2xl inline-flex mb-6">
                <i data-lucide="book-open" class="w-12 h-12 text-white"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary-800 mb-3">Pilih Mata Pelajaran</h3>
            <p class="text-secondary-600 mb-6">
                Silakan pilih mata pelajaran terlebih dahulu untuk melihat rincian absensi per pertemuan.
            </p>
        </div>
    <?php endif; ?>

</main>

<script>
// Toggle custom date inputs
function toggleCustomDate() {
    const periode = document.getElementById('periode').value;
    const tanggalMulaiDiv = document.getElementById('tanggal-mulai-div');
    const tanggalAkhirDiv = document.getElementById('tanggal-akhir-div');
    
    if (periode === 'custom') {
        tanggalMulaiDiv.style.display = 'block';
        tanggalAkhirDiv.style.display = 'block';
    } else {
        tanggalMulaiDiv.style.display = 'none';
        tanggalAkhirDiv.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    toggleCustomDate();
});
</script>