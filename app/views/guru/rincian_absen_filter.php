<?php /* File: app/views/guru/rincian_absen_filter.php */ ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-3 sm:p-4 lg:p-6">


    <!-- Data Display -->
    <?php if (!empty($data['rincian_data']['siswa_data'])): ?>
        <!-- Info Mapel & Actions -->
        <div class="glass-effect rounded-xl p-4 sm:p-6 border border-white/20 shadow-lg mb-4 sm:mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4 mb-3 sm:mb-4">
                <h3 class="text-base sm:text-xl font-semibold text-secondary-800 flex items-center">
                    <i data-lucide="book-open" class="w-5 h-5 sm:w-6 sm:h-6 mr-2"></i>
                    <span class="line-clamp-2"><?= htmlspecialchars($data['mapel_info']['nama_mapel'] ?? '-'); ?> - <?= htmlspecialchars($data['mapel_info']['nama_kelas'] ?? '-'); ?></span>
                </h3>
                <div class="flex gap-2">
                    <a href="<?= BASEURL; ?>/guru/dashboard" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white rounded-lg transition-colors duration-200 font-medium text-xs sm:text-sm whitespace-nowrap">
                        <i data-lucide="home" class="w-4 h-4 mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Home</span>
                        <span class="sm:hidden">Home</span>
                    </a>
                    <a href="<?= BASEURL; ?>/guru/downloadRincianAbsenPDF/<?= urlencode($data['filter']['id_mapel']); ?>?periode=<?= urlencode($data['filter']['periode'] ?? 'semester'); ?><?= !empty($data['filter']['id_kelas']) ? '&id_kelas=' . urlencode($data['filter']['id_kelas']) : '' ?><?= !empty($data['filter']['tanggal_mulai']) ? '&tanggal_mulai=' . urlencode($data['filter']['tanggal_mulai']) : '' ?><?= !empty($data['filter']['tanggal_akhir']) ? '&tanggal_akhir=' . urlencode($data['filter']['tanggal_akhir']) : '' ?>" 
                       class="inline-flex items-center px-3 sm:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium text-xs sm:text-sm whitespace-nowrap">
                        <i data-lucide="download" class="w-4 h-4 mr-1 sm:mr-2"></i>
                        <span class="hidden sm:inline">Download PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                <div>
                    <p class="text-xs sm:text-sm text-secondary-600">Tahun Pelajaran</p>
                    <p class="font-semibold text-sm sm:text-base text-secondary-800"><?= htmlspecialchars($data['mapel_info']['nama_tp'] ?? '-'); ?></p>
                </div>
                <div>
                    <p class="text-xs sm:text-sm text-secondary-600">Semester</p>
                    <p class="font-semibold text-sm sm:text-base text-secondary-800"><?= htmlspecialchars($data['mapel_info']['semester'] ?? '-'); ?></p>
                </div>
                <div class="col-span-2 sm:col-span-2 lg:col-span-1">
                    <p class="text-xs sm:text-sm text-secondary-600">Total Siswa</p>
                    <p class="font-semibold text-sm sm:text-base text-secondary-800"><?= count($data['rincian_data']['siswa_data']); ?> siswa</p>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian -->
        <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-white/30">
                <h3 class="text-lg sm:text-xl font-semibold text-secondary-800 flex items-center">
                    <i data-lucide="table" class="w-5 h-5 mr-2"></i>
                    Rincian Absen per Pertemuan
                </h3>
                <p class="text-xs sm:text-sm text-secondary-600 mt-1">
                    Total <?= count($data['rincian_data']['siswa_data']); ?> siswa • 
                    <?= count($data['rincian_data']['pertemuan_headers']); ?> pertemuan
                </p>
            </div>

            <!-- Mobile View: Card Layout -->
            <div class="block lg:hidden p-4 space-y-4">
                <?php 
                $no = 1;
                foreach ($data['rincian_data']['siswa_data'] as $siswa): 
                    $total_pertemuan = count($data['rincian_data']['pertemuan_headers']);
                    $persentase_hadir = $total_pertemuan > 0 ? round(($siswa['total_hadir'] / $total_pertemuan) * 100, 1) : 0;
                ?>
                    <div class="bg-white rounded-lg border border-secondary-200 p-4 shadow-sm">
                        <!-- Header Siswa -->
                        <div class="flex items-start justify-between mb-3 pb-3 border-b border-secondary-100">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="bg-secondary-100 text-secondary-700 text-xs font-bold px-2 py-1 rounded">#<?= $no++; ?></span>
                                    <h4 class="font-semibold text-secondary-900"><?= htmlspecialchars($siswa['nama_siswa']); ?></h4>
                                </div>
                                <p class="text-xs text-secondary-500">NIS: <?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold <?= $persentase_hadir >= 75 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= $persentase_hadir; ?>%
                                </div>
                                <p class="text-xs text-secondary-500">Kehadiran</p>
                            </div>
                        </div>

                        <!-- Summary Stats -->
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            <div class="text-center bg-green-50 rounded-lg p-2">
                                <div class="text-lg font-bold text-green-700"><?= $siswa['total_hadir']; ?></div>
                                <div class="text-xs text-green-600">Hadir</div>
                            </div>
                            <div class="text-center bg-blue-50 rounded-lg p-2">
                                <div class="text-lg font-bold text-blue-700"><?= $siswa['total_izin']; ?></div>
                                <div class="text-xs text-blue-600">Izin</div>
                            </div>
                            <div class="text-center bg-yellow-50 rounded-lg p-2">
                                <div class="text-lg font-bold text-yellow-700"><?= $siswa['total_sakit']; ?></div>
                                <div class="text-xs text-yellow-600">Sakit</div>
                            </div>
                            <div class="text-center bg-red-50 rounded-lg p-2">
                                <div class="text-lg font-bold text-red-700"><?= $siswa['total_alpha']; ?></div>
                                <div class="text-xs text-red-600">Alpha</div>
                            </div>
                        </div>

                        <!-- Detail Per Pertemuan -->
                        <div class="bg-secondary-50 rounded-lg p-3">
                            <button onclick="toggleDetail<?= $siswa['id_siswa']; ?>()" class="w-full flex items-center justify-between text-sm font-medium text-secondary-700 hover:text-secondary-900">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                    Detail Pertemuan
                                </span>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" id="icon<?= $siswa['id_siswa']; ?>"></i>
                            </button>
                            <div id="detail<?= $siswa['id_siswa']; ?>" class="hidden mt-3 space-y-2">
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
                                        'H' => 'bg-green-100 text-green-800 border-green-200',
                                        'I' => 'bg-blue-100 text-blue-800 border-blue-200', 
                                        'S' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'A' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    
                                    $status_text = [
                                        'H' => 'Hadir',
                                        'I' => 'Izin', 
                                        'S' => 'Sakit',
                                        'A' => 'Alpha'
                                    ];
                                ?>
                                    <div class="flex items-center justify-between bg-white rounded p-2 border <?= $status_colors[$status] ?? $status_colors['A']; ?>">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-xs font-bold bg-secondary-100 px-2 py-1 rounded">P<?= $pertemuan['pertemuan_ke']; ?></span>
                                            <div>
                                                <div class="text-xs font-medium text-secondary-900"><?= date('d/m/Y', strtotime($pertemuan['tanggal'])); ?></div>
                                                <?php if ($status == 'H' && !empty($pertemuan_data['waktu_absen'])): ?>
                                                    <div class="text-xs text-secondary-500"><?= substr($pertemuan_data['waktu_absen'], 0, 5); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold px-3 py-1 rounded-full <?= $status_colors[$status] ?? $status_colors['A']; ?>">
                                            <?= $status_text[$status]; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden lg:block overflow-x-auto">
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
                Tidak ada data absensi untuk mata pelajaran dan kelas ini pada periode yang dipilih.
            </p>
            <a href="<?= BASEURL; ?>/guru/dashboard" class="inline-flex items-center px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm">
                <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>
    <?php endif; ?>

</main>

<script>
// Toggle detail pertemuan di mobile
<?php foreach ($data['rincian_data']['siswa_data'] as $siswa): ?>
function toggleDetail<?= $siswa['id_siswa']; ?>() {
    const detail = document.getElementById('detail<?= $siswa['id_siswa']; ?>');
    const icon = document.getElementById('icon<?= $siswa['id_siswa']; ?>');
    
    if (detail.classList.contains('hidden')) {
        detail.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        detail.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
    
    // Reinitialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}
<?php endforeach; ?>

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>