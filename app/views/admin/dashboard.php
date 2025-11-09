<?php 
// File: app/views/admin/dashboard.php - Dashboard Admin Informatif
?>

<!-- Page Content -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    Selamat Datang, <?= htmlspecialchars($_SESSION['user_nama_lengkap'] ?? 'Admin'); ?>! 👋
                </h1>
                <p class="text-indigo-100 text-lg">
                    <?= date('l, d F Y'); ?> • Sesi: <span class="font-semibold"><?= $_SESSION['nama_semester_aktif'] ?? 'Belum ada sesi aktif'; ?></span>
                </p>
            </div>
            <button onclick="refreshDashboard()" class="bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 backdrop-blur-sm border border-white/30">
                <i data-lucide="refresh-cw" class="w-5 h-5 inline mr-2"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Summary Cards dengan Data Real - ENHANCED -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card Total Guru -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="users" class="w-7 h-7 text-white"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Guru</p>
                    <p class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        <?= $data['stats']['total_guru']; ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-xs text-green-600 font-semibold flex items-center">
                    <i data-lucide="trending-up" class="w-3 h-3 mr-1"></i>
                    Aktif mengajar
                </span>
                <a href="<?= BASEURL; ?>/admin/guru" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Lihat Detail →
                </a>
            </div>
        </div>

        <!-- Card Total Siswa -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-green-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-4 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="graduation-cap" class="w-7 h-7 text-white"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500 mb-1">Siswa Aktif</p>
                    <p class="text-4xl font-extrabold bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                        <?= $data['stats']['total_siswa_aktif']; ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-600 font-medium">
                    <?= $data['stats']['total_kelas']; ?> kelas tersedia
                </span>
                <a href="<?= BASEURL; ?>/admin/siswa" class="text-xs text-green-600 hover:text-green-800 font-medium">
                    Lihat Detail →
                </a>
            </div>
        </div>

        <!-- Card Kehadiran Hari Ini -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-emerald-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-4 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="check-circle" class="w-7 h-7 text-white"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500 mb-1">Kehadiran Hari Ini</p>
                    <p class="text-4xl font-extrabold bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent">
                        <?= $data['stats']['kehadiran_hari_ini']['percentage']; ?>%
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-600 font-medium">
                    <?= $data['stats']['kehadiran_hari_ini']['hadir']; ?> dari <?= $data['stats']['kehadiran_hari_ini']['total']; ?> siswa
                </span>
                <a href="<?= BASEURL; ?>/admin/monitoringAbsensi" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                    Monitor →
                </a>
            </div>
        </div>

        <!-- Card Jurnal Hari Ini -->
        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-4 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i data-lucide="book-open" class="w-7 h-7 text-white"></i>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-500 mb-1">Jurnal Hari Ini</p>
                    <p class="text-4xl font-extrabold bg-gradient-to-r from-orange-600 to-orange-800 bg-clip-text text-transparent">
                        <?= $data['stats']['jurnal_hari_ini']; ?>
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-600 font-medium">
                    <?= round(($data['stats']['jurnal_hari_ini'] / max($data['stats']['total_guru'], 1)) * 100, 0); ?>% guru telah mengisi
                </span>
                <a href="<?= BASEURL; ?>/admin/monitoringJurnal" class="text-xs text-orange-600 hover:text-orange-800 font-medium">
                    Lihat Semua →
                </a>
            </div>
        </div>
    </div>

    <!-- Charts Section - ENHANCED -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Trend Kehadiran 7 Hari -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Trend Kehadiran</h3>
                    <p class="text-sm text-gray-500 mt-1">7 Hari Terakhir</p>
                </div>
                <div class="bg-indigo-50 p-3 rounded-xl">
                    <i data-lucide="trending-up" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <div class="h-80">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Status Kehadiran Hari Ini -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Breakdown Kehadiran</h3>
                    <p class="text-sm text-gray-500 mt-1">Status Hari Ini</p>
                </div>
                <div class="bg-emerald-50 p-3 rounded-xl">
                    <i data-lucide="pie-chart" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
            <div class="h-80">
                <canvas id="todayChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity - ENHANCED -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Quick Actions -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-3 rounded-xl shadow-lg mr-4">
                    <i data-lucide="zap" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Aksi Cepat</h3>
                    <p class="text-sm text-gray-500">Shortcut menu utama</p>
                </div>
            </div>
            <div class="space-y-3">
                <a href="<?= BASEURL; ?>/admin/tambahSiswa" class="group w-full flex items-center p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-xl transition-all duration-200 border border-indigo-200">
                    <div class="bg-white p-2 rounded-lg mr-4 shadow-sm group-hover:shadow-md transition-shadow">
                        <i data-lucide="user-plus" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Tambah Siswa Baru</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="<?= BASEURL; ?>/admin/tambahKelas" class="group w-full flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-xl transition-all duration-200 border border-green-200">
                    <div class="bg-white p-2 rounded-lg mr-4 shadow-sm group-hover:shadow-md transition-shadow">
                        <i data-lucide="layout-grid" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Buat Kelas Baru</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="<?= BASEURL; ?>/admin/tambahPenugasan" class="group w-full flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl transition-all duration-200 border border-blue-200">
                    <div class="bg-white p-2 rounded-lg mr-4 shadow-sm group-hover:shadow-md transition-shadow">
                        <i data-lucide="calendar-plus" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Buat Penugasan</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="<?= BASEURL; ?>/admin/laporan" class="group w-full flex items-center p-4 bg-gradient-to-r from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 rounded-xl transition-all duration-200 border border-orange-200">
                    <div class="bg-white p-2 rounded-lg mr-4 shadow-sm group-hover:shadow-md transition-shadow">
                        <i data-lucide="file-text" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Lihat Laporan</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="<?= BASEURL; ?>/admin/naikKelas" class="group w-full flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-xl transition-all duration-200 border border-purple-200">
                    <div class="bg-white p-2 rounded-lg mr-4 shadow-sm group-hover:shadow-md transition-shadow">
                        <i data-lucide="trending-up" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Naik/Lulus Siswa</span>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- Recent Jurnal dari Database -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl shadow-lg mr-4">
                    <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Jurnal Terbaru</h3>
                    <p class="text-sm text-gray-500">Aktivitas pembelajaran</p>
                </div>
            </div>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <?php if (empty($data['recent_journals'])): ?>
                    <div class="text-center py-8">
                        <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">Belum ada jurnal hari ini</p>
                        <p class="text-gray-400 text-xs mt-1">Guru belum mengisi jurnal pembelajaran</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($data['recent_journals'], 0, 5) as $journal): ?>
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border border-gray-100">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                <span class="text-sm font-bold text-white">
                                    <?= substr($journal['nama_guru'], 0, 2); ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate"><?= htmlspecialchars($journal['nama_guru']); ?></p>
                                <p class="text-xs text-gray-600 mt-1">
                                    <span class="font-medium"><?= htmlspecialchars($journal['nama_mapel']); ?></span> • <?= htmlspecialchars($journal['nama_kelas']); ?>
                                </p>
                                <div class="flex items-center mt-2">
                                    <i data-lucide="clock" class="w-3 h-3 text-gray-400 mr-1"></i>
                                    <p class="text-xs text-gray-400"><?= date('H:i', strtotime($journal['timestamp'])); ?> WIB</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="<?= BASEURL; ?>/admin/monitoringJurnal" class="w-full mt-6 text-sm text-blue-600 hover:text-blue-800 font-semibold block text-center py-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                Lihat Semua Jurnal →
            </a>
        </div>

        <!-- Sistem Alerts Real -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-shadow">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-orange-500 to-red-600 p-3 rounded-xl shadow-lg mr-4">
                    <i data-lucide="alert-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Notifikasi</h3>
                    <p class="text-sm text-gray-500">Peringatan sistem</p>
                </div>
            </div>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <?php if (empty($data['alerts'])): ?>
                    <div class="text-center py-8">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                        </div>
                        <p class="text-green-800 text-sm font-semibold">Semua Normal</p>
                        <p class="text-green-600 text-xs mt-1">Tidak ada peringatan sistem</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['alerts'] as $alert): ?>
                        <div class="flex items-start space-x-4 p-4 bg-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : 'blue'); ?>-50 rounded-xl border border-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : 'blue'); ?>-200">
                            <div class="bg-white p-2 rounded-lg shadow-sm flex-shrink-0">
                                <i data-lucide="<?= $alert['icon']; ?>" class="w-5 h-5 text-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : 'blue'); ?>-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : 'blue'); ?>-900"><?= htmlspecialchars($alert['title']); ?></p>
                                <p class="text-xs text-<?= $alert['type'] == 'warning' ? 'yellow' : ($alert['type'] == 'error' ? 'red' : 'blue'); ?>-700 mt-1"><?= htmlspecialchars($alert['message']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Performance Overview - ENHANCED -->
    <div class="bg-gradient-to-br from-white to-gray-50 p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center mb-8">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-xl shadow-lg mr-4">
                <i data-lucide="bar-chart-3" class="w-7 h-7 text-white"></i>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Ringkasan Performa Sekolah</h3>
                <p class="text-sm text-gray-500 mt-1">Metrik kinerja sistem pembelajaran</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i data-lucide="user-check" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">Hari Ini</span>
                </div>
                <div class="text-center mt-4">
                    <div class="text-4xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        <?= $data['stats']['kehadiran_hari_ini']['percentage']; ?>%
                    </div>
                    <div class="text-sm text-gray-600 mt-2 font-medium">Kehadiran Siswa</div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            <?= $data['stats']['kehadiran_hari_ini']['hadir']; ?> dari <?= $data['stats']['kehadiran_hari_ini']['total']; ?> siswa hadir
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Aktif</span>
                </div>
                <div class="text-center mt-4">
                    <div class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        <?= round(($data['stats']['jurnal_hari_ini'] / max($data['stats']['total_guru'], 1)) * 100, 0); ?>%
                    </div>
                    <div class="text-sm text-gray-600 mt-2 font-medium">Kelengkapan Jurnal</div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            <?= $data['stats']['jurnal_hari_ini']; ?> dari <?= $data['stats']['total_guru']; ?> guru mengisi
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i data-lucide="layout-grid" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Total</span>
                </div>
                <div class="text-center mt-4">
                    <div class="text-4xl font-extrabold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        <?= $data['stats']['total_kelas']; ?>
                    </div>
                    <div class="text-sm text-gray-600 mt-2 font-medium">Kelas Aktif</div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            Tahun Pelajaran <?= $_SESSION['nama_tp_aktif'] ?? 'Aktif'; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-orange-100 p-2 rounded-lg">
                        <i data-lucide="calendar" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">Live</span>
                </div>
                <div class="text-center mt-4">
                    <div class="text-4xl font-extrabold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                        <?= count($data['recent_journals']); ?>
                    </div>
                    <div class="text-sm text-gray-600 mt-2 font-medium">Pertemuan Hari Ini</div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            Total pembelajaran berlangsung
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Data untuk chart dari PHP
const attendanceTrendData = <?= json_encode($data['attendance_trend'] ?? []); ?>;
const attendanceTodayData = <?= json_encode($data['attendance_today'] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0]); ?>;

// Attendance Trend Chart
const attendanceCtx = document.getElementById('attendanceChart');
if (attendanceCtx) {
    const labels = attendanceTrendData.map(item => {
        const date = new Date(item.tanggal);
        return date.toLocaleDateString('id-ID', { weekday: 'short' });
    });
    const data = attendanceTrendData.map(item => item.persentase || 0);

    new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kehadiran (%)',
                data: data,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 0,
                    max: 100
                }
            }
        }
    });
}

// Today's Attendance Chart
const todayCtx = document.getElementById('todayChart');
if (todayCtx) {
    new Chart(todayCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
            datasets: [{
                data: [
                    attendanceTodayData.hadir || 0,
                    attendanceTodayData.izin || 0,
                    attendanceTodayData.sakit || 0,
                    attendanceTodayData.alfa || 0
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Refresh Dashboard Function
function refreshDashboard() {
    location.reload();
}

// Auto-refresh setiap 5 menit
setInterval(refreshDashboard, 300000);

console.log('Dashboard loaded with real data:', {
    stats: <?= json_encode($data['stats']); ?>,
    journals: <?= json_encode(count($data['recent_journals'])); ?>,
    alerts: <?= json_encode(count($data['alerts'])); ?>
});
</script>