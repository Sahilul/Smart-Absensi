<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Welcome Header (Guru-style) -->
    <div class="mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-white/20 shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-800 mb-2">
                        Selamat Datang, <?= $_SESSION['nama_lengkap'] ?? 'Wali Kelas'; ?>!
                    </h1>
                    <p class="text-secondary-600 font-medium">
                        <i data-lucide="calendar-check" class="w-4 h-4 inline mr-2"></i>
                        Sesi Aktif: <span class="gradient-success text-white px-3 py-1 rounded-lg text-sm font-semibold"><?= $_SESSION['nama_semester_aktif'] ?? '-'; ?></span>
                    </p>
                    <p class="text-sm text-secondary-500 mt-2">Pantau perkembangan kelas Anda dengan cepat</p>
                </div>
                <div class="hidden md:block">
                    <div class="gradient-success p-4 rounded-2xl shadow-lg">
                        <i data-lucide="school" class="w-12 h-12 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <!-- Statistics Dashboard (Guru-style) -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Siswa -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up">
                <div class="flex items-center justify-between mb-4">
                    <div class="gradient-primary p-3 rounded-xl">
                        <i data-lucide="users" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="status-badge bg-primary-100 text-primary-800">Siswa</span>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-1"><?= (int)($data['total_siswa'] ?? 0); ?></h3>
                <p class="text-sm text-secondary-600">Total Siswa Kelas <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '-'); ?></p>
            </div>

            <!-- Hadir Hari Ini -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="gradient-success p-3 rounded-xl">
                        <i data-lucide="clipboard-check" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="status-badge bg-success-100 text-success-800">Hadir</span>
                </div>
                <?php $rekap = $data['rekap_absensi_kelas'] ?? []; $hadir_persen = isset($rekap['persentase_hadir']) ? number_format($rekap['persentase_hadir'], 1) : 0; ?>
                <h3 class="text-2xl font-bold text-secondary-800 mb-1"><?= $hadir_persen; ?>%</h3>
                <p class="text-sm text-secondary-600">Persentase Kehadiran Hari Ini</p>
            </div>

            <!-- Jurnal Hari Ini -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="gradient-warning p-3 rounded-xl">
                        <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="status-badge bg-warning-100 text-warning-800">Jurnal</span>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-1"><?= count($data['jurnal_hari_ini'] ?? []); ?></h3>
                <p class="text-sm text-secondary-600">Mata Pelajaran Hari Ini</p>
            </div>
        </div>
    </div>

    <!-- Aksi Wali Kelas (Segmented Buttons) -->
    <div class="glass-effect rounded-2xl p-6 border border-white/20 shadow-lg mb-8">
        <div class="flex items-center mb-4">
            <i data-lucide="menu" class="w-4 h-4 mr-2 text-secondary-400"></i>
            <span class="text-xs font-bold text-secondary-600 uppercase tracking-wider">Aksi Wali Kelas</span>
        </div>
        <div class="seg-actions grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Daftar Siswa -->
            <a href="<?= BASEURL; ?>/waliKelas/daftarSiswa" class="seg-btn seg-primary" title="Daftar Siswa">
                <div class="seg-left">
                    <span class="seg-icon">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </span>
                    <span class="seg-label">Daftar Siswa</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 seg-right"></i>
            </a>

            <!-- Monitoring Absensi -->
            <a href="<?= BASEURL; ?>/waliKelas/monitoringAbsensi" class="seg-btn seg-secondary" title="Monitoring Absensi">
                <div class="seg-left">
                    <span class="seg-icon">
                        <i data-lucide="calendar-days" class="w-5 h-5"></i>
                    </span>
                    <span class="seg-label">Monitoring Absensi</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 seg-right"></i>
            </a>

            <!-- Monitoring Nilai -->
            <a href="<?= BASEURL; ?>/waliKelas/monitoringNilai" class="seg-btn seg-success" title="Monitoring Nilai">
                <div class="seg-left">
                    <span class="seg-icon">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    </span>
                    <span class="seg-label">Nilai Kelas</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 seg-right"></i>
            </a>

            <!-- Rapor STS -->
            <a href="<?= BASEURL; ?>/waliKelas/raporSTS" class="seg-btn seg-dark" title="Rapor STS">
                <div class="seg-left">
                    <span class="seg-icon">
                        <i data-lucide="file-bar-chart" class="w-5 h-5"></i>
                    </span>
                    <span class="seg-label">Rapor STS</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 seg-right"></i>
            </a>
        </div>
    </div>

    <!-- Quick View Siswa (tetap ada, dirapikan) -->
    <div class="glass-effect rounded-2xl shadow-lg overflow-hidden border border-white/20 mb-8">
        <div class="p-6 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border-b border-white/20 flex items-center justify-between">
            <h2 class="text-xl font-bold text-secondary-800 flex items-center gap-2">
                <i data-lucide="users-round" class="w-6 h-6 text-primary-600"></i>
                Daftar Siswa Kelas <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? ''); ?>
            </h2>
            <a href="<?= BASEURL; ?>/waliKelas/daftarSiswa" class="text-sm text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-1">
                Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-secondary-50 border-b border-secondary-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">NISN</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-secondary-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white/50 divide-y divide-secondary-100">
                    <?php if (empty($data['siswa_list'])): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-secondary-500">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="inbox" class="w-16 h-16 text-secondary-300"></i>
                                    <p class="text-lg font-medium">Belum ada siswa di kelas ini</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no=1; $max_show=10; foreach (array_slice($data['siswa_list'], 0, $max_show) as $siswa): ?>
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
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-success-100 text-success-700">Aktif</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($data['siswa_list']) > $max_show): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-secondary-600">
                                    Dan <?= count($data['siswa_list']) - $max_show; ?> siswa lainnya...
                                    <a href="<?= BASEURL; ?>/waliKelas/daftarSiswa" class="text-primary-600 hover:text-primary-700 font-semibold ml-2">Lihat Semua</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rekap Absensi Semester Ini -->
    <div class="glass-effect rounded-2xl shadow-lg overflow-hidden border border-white/20">
        <div class="p-6 bg-gradient-to-r from-success-500/10 to-emerald-500/10 border-b border-white/20">
            <h2 class="text-xl font-bold text-secondary-800 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-success-600"></i>
                Rekap Absensi Semester Ini
            </h2>
        </div>
        <div class="p-6">
            <?php $rekap = $data['rekap_absensi_kelas'] ?? []; ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-success-50 rounded-xl">
                    <div class="bg-success-100 p-3 rounded-xl inline-block mb-2">
                        <i data-lucide="check-circle" class="w-6 h-6 text-success-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-success-700"><?= $rekap['total_hadir'] ?? 0; ?></p>
                    <p class="text-sm text-secondary-600">Hadir</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <div class="bg-blue-100 p-3 rounded-xl inline-block mb-2">
                        <i data-lucide="mail" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-blue-700"><?= $rekap['total_izin'] ?? 0; ?></p>
                    <p class="text-sm text-secondary-600">Izin</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-xl">
                    <div class="bg-yellow-100 p-3 rounded-xl inline-block mb-2">
                        <i data-lucide="thermometer" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-yellow-700"><?= $rekap['total_sakit'] ?? 0; ?></p>
                    <p class="text-sm text-secondary-600">Sakit</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-xl">
                    <div class="bg-red-100 p-3 rounded-xl inline-block mb-2">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <p class="text-2xl font-bold text-red-700"><?= $rekap['total_alpa'] ?? 0; ?></p>
                    <p class="text-sm text-secondary-600">Alpa</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  lucide.createIcons();
  // Animate stats on load (match guru)
  const stats = document.querySelectorAll('.text-2xl.font-bold.text-secondary-800');
  stats.forEach((stat, index) => {
    setTimeout(() => {
      stat.style.opacity = '0';
      stat.style.transform = 'scale(1.2)';
      stat.style.transition = 'all 0.3s ease';
      setTimeout(() => { stat.style.opacity = '1'; stat.style.transform = 'scale(1)'; }, 100);
    }, index * 200);
  });
});
</script>

<style>
/* Segmented pill action buttons (shared with guru) */
.seg-actions { width: 100%; }
.seg-btn {
    display: flex; align-items: center; justify-content: space-between;
    gap: .75rem; width: 100%; padding: .75rem .9rem;
    border-radius: .9rem; border: 1px solid rgba(0,0,0,.06);
    background: #fff; color: #0f172a; text-decoration: none;
    box-shadow: 0 4px 14px rgba(2,6,23,.06);
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
}
.seg-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(2,6,23,.10); filter: brightness(1.02); }
.seg-left { display: inline-flex; align-items: center; gap: .6rem; }
.seg-label { font-size: .9rem; font-weight: 700; letter-spacing: .2px; }
.seg-right { color: rgba(15,23,42,.55); }
.seg-icon { display:inline-flex; align-items:center; justify-content:center; width: 2rem; height: 2rem; border-radius: .65rem; color: #fff; }
.seg-badge { display:inline-flex; align-items:center; justify-content:center; min-width: 1.5rem; height: 1.5rem; padding: 0 .4rem; border-radius: 999px; font-size: .75rem; font-weight: 800; color:#fff; }

/* Variants (match guru) */
.seg-warning { background: linear-gradient(135deg,#fff7ed,#ffedd5); border-color: rgba(245,158,11,.25); }
.seg-warning .seg-icon { background: linear-gradient(135deg,#f59e0b,#d97706); }
.seg-warning .seg-label { color: #92400e; }

.seg-info { background: linear-gradient(135deg,#ecfeff,#cffafe); border-color: rgba(6,182,212,.25); }
.seg-info .seg-icon { background: linear-gradient(135deg,#06b6d4,#0891b2); }
.seg-info .seg-label { color: #155e75; }
.seg-info .seg-badge { background:#06b6d4; }

.seg-success { background: linear-gradient(135deg,#ecfdf5,#d1fae5); border-color: rgba(34,197,94,.25); }
.seg-success .seg-icon { background: linear-gradient(135deg,#22c55e,#16a34a); }
.seg-success .seg-label { color: #065f46; }
.seg-success .seg-badge { background:#22c55e; }

.seg-primary { background: linear-gradient(135deg,#eff6ff,#dbeafe); border-color: rgba(59,130,246,.25); }
.seg-primary .seg-icon { background: linear-gradient(135deg,#3b82f6,#1d4ed8); }
.seg-primary .seg-label { color:#1e40af; }

.seg-secondary { background: linear-gradient(135deg,#f1f5f9,#e2e8f0); border-color: rgba(100,116,139,.25); }
.seg-secondary .seg-icon { background: linear-gradient(135deg,#64748b,#475569); }
.seg-secondary .seg-label { color:#334155; }

.seg-dark { background: linear-gradient(135deg,#f8fafc,#e2e8f0); border-color: rgba(55,65,81,.25); }
.seg-dark .seg-icon { background: linear-gradient(135deg,#374151,#111827); }
.seg-dark .seg-label { color:#111827; }

/* Glass effect */
.glass-effect {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

/* Animations (match guru) */
@keyframes slideUp { from {opacity:0; transform: translateY(10px)} to {opacity:1; transform:none} }
.animate-slide-up{ animation: slideUp .28s ease both; }
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
.animate-fade-in{ animation: fadeIn .4s ease both; }

/* Gradients util */
.gradient-primary{ background: linear-gradient(135deg,#6366f1,#22d3ee); }
.gradient-success{ background: linear-gradient(135deg,#10b981,#34d399); }
.gradient-warning{ background: linear-gradient(135deg,#f59e0b,#fbbf24); }

/* Status badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}
</style>
