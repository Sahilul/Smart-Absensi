<?php
// File: app/views/admin/detail_riwayat_admin.php
?>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-secondary-600">
            <a href="<?= BASEURL; ?>/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="<?= BASEURL; ?>/admin/riwayatJurnal" class="hover:text-primary-600 transition-colors">Riwayat Jurnal</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-secondary-800 font-medium">Detail Statistik</span>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <a href="javascript:history.back()" 
                   class="p-2 rounded-xl text-secondary-500 hover:text-primary-600 hover:bg-white/50 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-bold text-secondary-800">Detail Riwayat Jurnal</h2>
                    <p class="text-secondary-600 mt-1">
                        <span class="font-medium"><?= htmlspecialchars($data['nama_guru']); ?></span> - 
                        <?= htmlspecialchars($data['nama_mapel']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($data['detail_jurnal'])): ?>
        <!-- Empty State -->
        <div class="glass-effect rounded-xl p-12 border border-white/20 shadow-lg text-center">
            <div class="gradient-secondary p-4 rounded-xl inline-flex mb-6">
                <i data-lucide="book-x" class="w-12 h-12 text-white"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary-800 mb-3">Belum Ada Data Jurnal</h3>
            <p class="text-secondary-600">Belum ada jurnal untuk mata pelajaran ini.</p>
        </div>
    <?php else: ?>
        
        <!-- Summary Stats -->
        <?php 
        $total_pertemuan = count($data['detail_jurnal']);
        $total_siswa = count($data['detail_absensi_siswa']);
        $total_hadir = array_sum(array_column($data['detail_absensi_siswa'], 'total_hadir'));
        $total_alpha = array_sum(array_column($data['detail_absensi_siswa'], 'total_alpha'));
        $total_izin = array_sum(array_column($data['detail_absensi_siswa'], 'total_izin'));
        $total_sakit = array_sum(array_column($data['detail_absensi_siswa'], 'total_sakit'));
        $total_kehadiran = $total_hadir + $total_alpha + $total_izin + $total_sakit;
        $persentase_hadir = $total_kehadiran > 0 ? round(($total_hadir / $total_kehadiran) * 100, 1) : 0;
        ?>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Pertemuan -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary-600">Total Pertemuan</p>
                        <p class="text-3xl font-bold text-primary-600"><?= $total_pertemuan; ?></p>
                    </div>
                    <div class="gradient-primary p-3 rounded-xl">
                        <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary-600">Total Siswa</p>
                        <p class="text-3xl font-bold text-success-600"><?= $total_siswa; ?></p>
                    </div>
                    <div class="gradient-success p-3 rounded-xl">
                        <i data-lucide="users" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Persentase Kehadiran -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary-600">Tingkat Kehadiran</p>
                        <p class="text-3xl font-bold text-warning-600"><?= $persentase_hadir; ?>%</p>
                    </div>
                    <div class="gradient-warning p-3 rounded-xl">
                        <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Total Kehadiran -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary-600">Total Record</p>
                        <p class="text-3xl font-bold text-secondary-600"><?= $total_kehadiran; ?></p>
                    </div>
                    <div class="gradient-secondary p-3 rounded-xl">
                        <i data-lucide="database" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Stats -->
        <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg mb-8">
            <h3 class="text-lg font-semibold text-secondary-800 mb-6 flex items-center">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2 text-primary-600"></i>
                Statistik Kehadiran Detail
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2"><?= $total_hadir; ?></div>
                    <div class="text-sm text-secondary-600">Hadir (H)</div>
                    <div class="text-xs text-green-600 mt-1">
                        <?= $total_kehadiran > 0 ? round(($total_hadir / $total_kehadiran) * 100, 1) : 0; ?>%
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2"><?= $total_izin; ?></div>
                    <div class="text-sm text-secondary-600">Izin (I)</div>
                    <div class="text-xs text-blue-600 mt-1">
                        <?= $total_kehadiran > 0 ? round(($total_izin / $total_kehadiran) * 100, 1) : 0; ?>%
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-600 mb-2"><?= $total_sakit; ?></div>
                    <div class="text-sm text-secondary-600">Sakit (S)</div>
                    <div class="text-xs text-yellow-600 mt-1">
                        <?= $total_kehadiran > 0 ? round(($total_sakit / $total_kehadiran) * 100, 1) : 0; ?>%
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2"><?= $total_alpha; ?></div>
                    <div class="text-sm text-secondary-600">Alpha (A)</div>
                    <div class="text-xs text-red-600 mt-1">
                        <?= $total_kehadiran > 0 ? round(($total_alpha / $total_kehadiran) * 100, 1) : 0; ?>%
                    </div>
                </div>
            </div>
        </div>

        <!-- Jurnal History -->
        <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 p-6 text-white">
                <h3 class="text-xl font-bold flex items-center">
                    <i data-lucide="book-open" class="w-6 h-6 mr-2"></i>
                    Riwayat Jurnal Mengajar
                </h3>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($data['detail_jurnal'] as $index => $jurnal): ?>
                        <div class="border border-secondary-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 bg-white/50">
                            <div class="flex items-start justify-between flex-wrap gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <div class="bg-primary-100 text-primary-600 px-3 py-1 rounded-lg text-sm font-semibold">
                                            Pertemuan <?= $jurnal['pertemuan_ke'] ?? 0; ?>
                                        </div>
                                        <div class="text-sm text-secondary-600">
                                            <?= isset($jurnal['tanggal']) ? date('d M Y', strtotime($jurnal['tanggal'])) : date('d M Y'); ?>
                                        </div>
                                        <div class="text-sm text-secondary-600">
                                            <?= isset($jurnal['timestamp']) ? date('H:i', strtotime($jurnal['timestamp'])) : 'Waktu tidak tercatat'; ?>
                                        </div>
                                    </div>
                                    <h4 class="font-semibold text-secondary-800 mb-2"><?= htmlspecialchars($jurnal['materi'] ?? 'Materi tidak tercatat'); ?></h4>
                                    <?php if (!empty($jurnal['catatan'])): ?>
                                        <p class="text-sm text-secondary-600 italic"><?= htmlspecialchars($jurnal['catatan']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-lg font-bold text-success-600"><?= $jurnal['total_hadir'] ?? 0; ?></div>
                                    <div class="text-xs text-secondary-600">Siswa hadir</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Absensi per Siswa -->
        <?php if (!empty($data['detail_absensi_siswa'])): ?>
            <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-success-500 to-success-600 p-6 text-white">
                    <h3 class="text-xl font-bold flex items-center">
                        <i data-lucide="user-check" class="w-6 h-6 mr-2"></i>
                        Statistik Kehadiran per Siswa
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-secondary-200">
                                    <th class="text-left py-3 px-4 font-semibold text-secondary-800">Nama Siswa</th>
                                    <th class="text-center py-3 px-4 font-semibold text-green-600">H</th>
                                    <th class="text-center py-3 px-4 font-semibold text-blue-600">I</th>
                                    <th class="text-center py-3 px-4 font-semibold text-yellow-600">S</th>
                                    <th class="text-center py-3 px-4 font-semibold text-red-600">A</th>
                                    <th class="text-center py-3 px-4 font-semibold text-secondary-600">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['detail_absensi_siswa'] as $siswa): ?>
                                    <?php
                                    $total_record = $siswa['total_hadir'] + $siswa['total_izin'] + $siswa['total_sakit'] + $siswa['total_alpha'];
                                    $persentase = $total_record > 0 ? round(($siswa['total_hadir'] / $total_record) * 100, 1) : 0;
                                    ?>
                                    <tr class="border-b border-secondary-100 hover:bg-secondary-50">
                                        <td class="py-3 px-4 font-medium text-secondary-800"><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                                        <td class="text-center py-3 px-4 text-green-600 font-bold"><?= $siswa['total_hadir']; ?></td>
                                        <td class="text-center py-3 px-4 text-blue-600 font-bold"><?= $siswa['total_izin']; ?></td>
                                        <td class="text-center py-3 px-4 text-yellow-600 font-bold"><?= $siswa['total_sakit']; ?></td>
                                        <td class="text-center py-3 px-4 text-red-600 font-bold"><?= $siswa['total_alpha']; ?></td>
                                        <td class="text-center py-3 px-4">
                                            <span class="px-2 py-1 rounded-lg text-xs font-medium <?= $persentase >= 80 ? 'bg-green-100 text-green-600' : ($persentase >= 60 ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600'); ?>">
                                                <?= $persentase; ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>