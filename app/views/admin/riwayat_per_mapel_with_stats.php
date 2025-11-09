<?php /* File: app/views/admin/riwayat_per_mapel_with_stats.php */ ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                    <i data-lucide="bar-chart-3" class="w-8 h-8 mr-3 text-warning-500"></i>
                    Riwayat Jurnal & Statistik (Admin)
                </h2>
                <p class="text-secondary-600 mt-2">Analisis mendalam aktivitas mengajar dan kehadiran siswa - Semua Guru</p>
            </div>
            <div class="hidden md:block">
                <div class="gradient-warning p-3 rounded-xl">
                    <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
        
        <!-- Session Info -->
        <div class="mt-6 glass-effect rounded-xl p-4 border border-white/20 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="gradient-primary p-2 rounded-lg">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-secondary-800">Sesi Aktif</p>
                        <p class="text-sm text-secondary-600"><?= htmlspecialchars($_SESSION['nama_semester_aktif'] ?? '-'); ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-secondary-600">Total Kombinasi Guru-Mapel</p>
                    <p class="text-2xl font-bold text-secondary-800" id="total-mapel"><?= count($data['jurnal_per_mapel'] ?? []); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Panel Admin -->
    <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg mb-8">
        <h3 class="text-lg font-semibold text-secondary-800 mb-4 flex items-center">
            <i data-lucide="filter" class="w-5 h-5 mr-2"></i>
            Filter Data
        </h3>
        
        <form method="GET" action="<?= BASEURL; ?>/admin/riwayatPerMapel" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Filter Guru -->
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline mr-1"></i>
                        Guru
                    </label>
                    <select name="guru" class="w-full px-3 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                        <option value="">Semua Guru</option>
                        <?php foreach ($data['daftar_guru'] as $guru): ?>
                            <option value="<?= $guru['id_guru']; ?>" <?= ($data['filter']['guru'] == $guru['id_guru']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($guru['nama_guru']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        <i data-lucide="book-open" class="w-4 h-4 inline mr-1"></i>
                        Mata Pelajaran
                    </label>
                    <select name="mapel" class="w-full px-3 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                        <option value="">Semua Mapel</option>
                        <?php foreach ($data['daftar_mapel'] as $mapel): ?>
                            <option value="<?= $mapel['id_mapel']; ?>" <?= ($data['filter']['mapel'] == $mapel['id_mapel']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($mapel['nama_mapel']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Kelas -->
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                        Kelas
                    </label>
                    <select name="kelas" class="w-full px-3 py-2.5 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($data['daftar_kelas'] as $kelas): ?>
                            <option value="<?= $kelas['id_kelas']; ?>" <?= ($data['filter']['kelas'] == $kelas['id_kelas']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kelas['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 pt-4">
                <button type="submit" class="btn-primary">
                    <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                    Terapkan Filter
                </button>
                <a href="<?= BASEURL; ?>/admin/riwayatPerMapel" class="btn-secondary">
                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <?php if (empty($data['jurnal_per_mapel'])) : ?>
        <!-- Empty State -->
        <div class="glass-effect rounded-2xl p-12 border border-white/20 shadow-lg text-center animate-fade-in">
            <div class="max-w-md mx-auto">
                <div class="gradient-warning p-4 rounded-2xl inline-flex mb-6">
                    <i data-lucide="book-x" class="w-12 h-12 text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-secondary-800 mb-3">Belum Ada Data Jurnal</h3>
                <p class="text-secondary-600 mb-6">
                    Belum ada jurnal mengajar yang tersedia untuk semester ini dengan filter yang dipilih. 
                    Coba ubah filter atau periode lain.
                </p>
                <div class="space-y-3">
                    <a href="<?= BASEURL; ?>/admin/riwayatPerMapel" class="btn-primary w-full">
                        <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                        Reset Filter
                    </a>
                    <a href="<?= BASEURL; ?>/admin/dashboard" class="btn-secondary w-full">
                        <i data-lucide="home" class="w-4 h-4 inline mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    <?php else : ?>

        <!-- Overview Stats -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-secondary-800 mb-4 flex items-center">
                <i data-lucide="trending-up" class="w-5 h-5 mr-2 text-primary-500"></i>
                Ringkasan Keseluruhan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                $total_kombinasi = count($data['jurnal_per_mapel']);
                $total_pertemuan = 0;
                $total_hadir = 0;
                $total_records = 0;
                $avg_kehadiran = 0;

                foreach ($data['jurnal_per_mapel'] as $mapel) {
                    if (isset($mapel['statistik'])) {
                        $total_pertemuan += (int)($mapel['statistik']['total_pertemuan'] ?? 0);
                        $total_hadir += (int)($mapel['statistik']['total_hadir'] ?? 0);
                        $total_records += (int)($mapel['statistik']['total_absensi_records'] ?? 0);
                    }
                }

                if ($total_records > 0) {
                    $avg_kehadiran = round(($total_hadir / $total_records) * 100, 1);
                }
                ?>
                
                <!-- Total Kombinasi Guru-Mapel -->
                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Guru-Mapel</p>
                            <p class="text-2xl font-bold text-secondary-800" id="total-kombinasi"><?= $total_kombinasi; ?></p>
                            <p class="text-xs text-primary-600 flex items-center mt-2">
                                <i data-lucide="layers" class="w-3 h-3 mr-1"></i>
                                Kombinasi aktif
                            </p>
                        </div>
                        <div class="gradient-primary p-3 rounded-xl">
                            <i data-lucide="layers" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Pertemuan -->
                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Pertemuan</p>
                            <p class="text-2xl font-bold text-secondary-800" id="total-pertemuan"><?= $total_pertemuan; ?></p>
                            <p class="text-xs text-success-600 flex items-center mt-2">
                                <i data-lucide="calendar-check" class="w-3 h-3 mr-1"></i>
                                Semester ini
                            </p>
                        </div>
                        <div class="gradient-success p-3 rounded-xl">
                            <i data-lucide="calendar-check" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Kehadiran -->
                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Kehadiran</p>
                            <p class="text-2xl font-bold text-secondary-800" id="total-hadir"><?= $total_hadir; ?></p>
                            <p class="text-xs text-warning-600 flex items-center mt-2">
                                <i data-lucide="users" class="w-3 h-3 mr-1"></i>
                                Siswa hadir
                            </p>
                        </div>
                        <div class="gradient-warning p-3 rounded-xl">
                            <i data-lucide="users" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Rata-rata Kehadiran -->
                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between">
                        <div class="w-full">
                            <p class="text-sm font-medium text-secondary-600 mb-1">Rata-rata Kehadiran</p>
                            <p class="text-2xl font-bold text-secondary-800" id="avg-kehadiran" data-target="<?= $avg_kehadiran; ?>">0%</p>
                            <div class="w-full bg-secondary-200 rounded-full h-2 mt-2">
                                <div class="gradient-success h-2 rounded-full progress-animate" style="width: <?= $avg_kehadiran; ?>%"></div>
                            </div>
                        </div>
                        <div class="gradient-success p-3 rounded-xl ml-4">
                            <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Cards with Charts -->
        <div class="mb-8">
            <?php 
            $list_mapel = $data['jurnal_per_mapel'] ?? [];
            $jumlah_kombinasi = is_array($list_mapel) ? count($list_mapel) : 0;
            ?>
            <h3 class="text-xl font-bold text-secondary-800 mb-6 flex items-center">
                <i data-lucide="layers" class="w-5 h-5 mr-2 text-success-500"></i>
                Detail per Guru & Mata Pelajaran (<?= $jumlah_kombinasi; ?> kombinasi)
            </h3>
            
            <?php if ($jumlah_kombinasi === 0): ?>
                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                    <p class="text-secondary-700">Belum ada data untuk ditampilkan dengan filter yang dipilih.</p>
                </div>
            <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php foreach ($list_mapel as $index => $mapel) : 
                    $mapel = is_array($mapel) ? $mapel : [];
                    $stats = $mapel['statistik'] ?? [];
                    $persentase = isset($stats['persentase_kehadiran']) ? (float)$stats['persentase_kehadiran'] : 0.0;

                    // color mapping by status
                    if ($persentase >= 90) {
                        $color_class = 'gradient-success';
                        $progress_color = 'bg-success-500';
                        $badge_color = 'bg-success-100 text-success-800';
                        $status_text = 'Excellent';
                    } elseif ($persentase >= 75) {
                        $color_class = 'gradient-primary';
                        $progress_color = 'bg-primary-500';
                        $badge_color = 'bg-primary-100 text-primary-800';
                        $status_text = 'Good';
                    } elseif ($persentase >= 60) {
                        $color_class = 'gradient-warning';
                        $progress_color = 'bg-warning-500';
                        $badge_color = 'bg-warning-100 text-warning-800';
                        $status_text = 'Fair';
                    } else {
                        $color_class = 'bg-gradient-to-r from-danger-400 to-danger-600';
                        $progress_color = 'bg-danger-500';
                        $badge_color = 'bg-danger-100 text-danger-800';
                        $status_text = 'Poor';
                    }

                    // combo ID untuk admin (guru-mapel)
                    $combo_id = $mapel['id_guru'] . '-' . $mapel['id_mapel'];
                    $id_canvas = 'chart-' . $combo_id;
                    $nama_mapel = htmlspecialchars($mapel['nama_mapel'] ?? '-');
                    $nama_guru = htmlspecialchars($mapel['nama_guru'] ?? '-');
                    $nama_kelas = htmlspecialchars($mapel['nama_kelas'] ?? '-');
                    $total_siswa = (int)($stats['total_siswa'] ?? 0);
                    $jumlah_pertemuan = (int)($stats['total_pertemuan'] ?? 0);

                    $chart_data = $mapel['chart_data'] ?? null;
                    $cd_hadir = (int)($chart_data['hadir'] ?? 0);
                    $cd_izin  = (int)($chart_data['izin'] ?? 0);
                    $cd_sakit = (int)($chart_data['sakit'] ?? 0);
                    $cd_alpha = (int)($chart_data['alpha'] ?? 0);
                ?>
                <div class="glass-effect rounded-xl border border-white/20 shadow-lg card-hover animate-slide-up overflow-hidden" 
                     style="animation-delay: <?= $index * 0.1; ?>s;"
                     data-mapel-name="<?= $nama_mapel; ?>"
                     data-guru-name="<?= $nama_guru; ?>"
                     data-attendance-rate="<?= $persentase; ?>">
                    
                    <!-- Card Header -->
                    <div class="<?= $color_class; ?> p-4 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <div class="bg-white/20 p-2 rounded-lg">
                                    <i data-lucide="book-open" class="w-5 h-5"></i>
                                </div>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded-full font-medium">
                                    <?= $jumlah_pertemuan; ?> Pertemuan
                                </span>
                            </div>
                            <h3 class="text-lg font-bold mb-2"><?= $nama_mapel; ?></h3>
                            <p class="text-white/90 text-sm flex items-center mb-1">
                                <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                                <?= $nama_guru; ?>
                            </p>
                            <?php if (!empty($stats)) : ?>
                                <p class="text-white/90 text-sm flex items-center">
                                    <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                    <?= $nama_kelas; ?> • <?= $total_siswa; ?> Siswa
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="p-4 bg-white/30">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-secondary-800">Statistik Kehadiran</h4>
                            <span class="<?= $badge_color; ?> text-xs px-2 py-1 rounded-full font-semibold">
                                <?= $status_text; ?> (<?= $persentase; ?>%)
                            </span>
                        </div>
                        <?php if ($chart_data) : ?>
                            <div class="relative h-40">
                                <canvas id="<?= htmlspecialchars($id_canvas); ?>" class="w-full h-full"
                                        data-hadir="<?= $cd_hadir; ?>"
                                        data-izin="<?= $cd_izin; ?>"
                                        data-sakit="<?= $cd_sakit; ?>"
                                        data-alpha="<?= $cd_alpha; ?>"
                                ></canvas>
                            </div>
                        <?php else: ?>
                            <div class="text-sm text-secondary-700 bg-white/60 rounded-lg p-3">
                                Data chart belum tersedia untuk kombinasi ini.
                            </div>
                        <?php endif; ?>

                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-secondary-600 mb-1">
                                <span>Tingkat Kehadiran</span>
                                <span class="font-bold"><?= $persentase; ?>%</span>
                            </div>
                            <div class="w-full bg-secondary-200 rounded-full h-3">
                                <div class="<?= $progress_color; ?> h-3 rounded-full progress-animate transition-all duration-1000" 
                                     style="width: <?= $persentase; ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <?php if (!empty($stats)) : ?>
                    <div class="p-4">
                        <div class="grid grid-cols-4 gap-2 mb-4">
                            <div class="bg-success-50 rounded-lg p-2 text-center">
                                <div class="text-success-600 text-xs font-medium">Hadir</div>
                                <div class="text-success-900 font-bold text-lg"><?= (int)($stats['total_hadir'] ?? 0); ?></div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-2 text-center">
                                <div class="text-blue-600 text-xs font-medium">Izin</div>
                                <div class="text-blue-900 font-bold text-lg"><?= (int)($stats['total_izin'] ?? 0); ?></div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-2 text-center">
                                <div class="text-yellow-600 text-xs font-medium">Sakit</div>
                                <div class="text-yellow-900 font-bold text-lg"><?= (int)($stats['total_sakit'] ?? 0); ?></div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-2 text-center">
                                <div class="text-red-600 text-xs font-medium">Alpha</div>
                                <div class="text-red-900 font-bold text-lg"><?= (int)($stats['total_alpha'] ?? 0); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons - 3 Tombol dengan Grid untuk Admin -->
                    <div class="p-4 bg-white/50 border-t border-white/30">
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Detail Riwayat -->
                            <a href="<?= BASEURL; ?>/admin/detailRiwayatAdmin/<?= $mapel['id_guru']; ?>/<?= $mapel['id_mapel']; ?>" 
                               class="btn-primary text-center text-xs py-2.5 px-2"
                               title="Lihat detail riwayat jurnal">
                                <i data-lucide="eye" class="w-3 h-3 inline mr-1"></i>
                                Detail
                            </a>

                            <!-- Rincian Absen -->
                            <a href="<?= BASEURL; ?>/admin/rincianAbsenAdmin?id_mapel=<?= $mapel['id_mapel']; ?>&guru=<?= $mapel['id_guru']; ?>" 
                               class="btn-secondary text-center text-xs py-2.5 px-2"
                               title="Rincian Absen per Pertemuan">
                                <i data-lucide="calendar-days" class="w-3 h-3 inline mr-1"></i>
                                By Date
                            </a>

                            <!-- Cetak -->
                            <button
                              onclick="printMapelAdmin('<?= $combo_id; ?>')"
                              class="btn-print text-xs py-2.5 px-2"
                              title="Cetak Laporan">
                                <i data-lucide="printer" class="w-3 h-3 inline mr-1"></i>
                                Cetak
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Access CTA -->
        <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg text-center animate-slide-up" style="animation-delay: 0.6s;">
            <div class="max-w-md mx-auto">
                <div class="gradient-primary p-3 rounded-xl inline-flex mb-4">
                    <i data-lucide="eye" class="w-6 h-6 text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-secondary-800 mb-2">Monitoring Lengkap</h3>
                <p class="text-sm text-secondary-600 mb-4">Akses monitoring jurnal dan absensi secara menyeluruh</p>
                <div class="flex gap-3 justify-center">
                    <a href="<?= BASEURL; ?>/admin/monitoringJurnal" class="btn-primary inline-flex items-center">
                        <i data-lucide="book-open" class="w-4 h-4 mr-2"></i>
                        Monitoring Jurnal
                    </a>
                    <a href="<?= BASEURL; ?>/admin/monitoringAbsensi" class="btn-secondary inline-flex items-center">
                        <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                        Monitoring Absensi
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</main>

<!-- Libraries -->
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Initialize charts
    initializeCharts();
    
    // Animate counters
    animateCounters();
    
    // Initialize card hover effects
    initializeCardEffects();
  });

  function initializeCharts() {
    const canvases = document.querySelectorAll('canvas[id^="chart-"]');
    canvases.forEach(el => {
      const data = [
        parseInt(el.getAttribute('data-hadir') || '0', 10),
        parseInt(el.getAttribute('data-izin')  || '0', 10),
        parseInt(el.getAttribute('data-sakit') || '0', 10),
        parseInt(el.getAttribute('data-alpha') || '0', 10)
      ];

      const sum = data.reduce((a,b)=>a+b, 0);
      if (sum === 0) return;

      new Chart(el, {
        type: 'doughnut',
        data: {
          labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
          datasets: [{
            data,
            backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(0,0,0,0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              cornerRadius: 8,
              callbacks: {
                label: (ctx) => {
                  const total = ctx.dataset.data.reduce((a, b) => a + b, 0) || 1;
                  const pct = ((ctx.parsed / total) * 100).toFixed(1);
                  return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                }
              }
            }
          },
          cutout: '60%'
        }
      });
    });
  }

  function initializeCardEffects() {
    const cards = document.querySelectorAll('.card-hover');
    cards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-8px) scale(1.02)';
      });
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });
  }

  function animateCounters() {
    // Counter animation untuk statistik angka
    const counters = document.querySelectorAll('#total-kombinasi, #total-pertemuan, #total-hadir');
    counters.forEach(counter => {
      const target = parseInt(counter.textContent);
      let current = 0;
      const increment = target / 50;
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        counter.textContent = Math.floor(current);
      }, 40);
    });

    // Progress bar animation untuk persentase
    const avgElement = document.getElementById('avg-kehadiran');
    if (avgElement) {
      const targetPercent = parseFloat(avgElement.getAttribute('data-target')) || 0;
      let currentPercent = 0;
      const increment = targetPercent / 50;
      const timer = setInterval(() => {
        currentPercent += increment;
        if (currentPercent >= targetPercent) {
          currentPercent = targetPercent;
          clearInterval(timer);
        }
        avgElement.textContent = currentPercent.toFixed(1) + '%';
      }, 40);
    }
  }

  // Notification system
  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transition-all duration-300 transform translate-x-full`;
    
    const colors = {
      success: 'bg-success-100 border-success-300 text-success-800',
      info: 'bg-blue-100 border-blue-300 text-blue-800',
      warning: 'bg-warning-100 border-warning-300 text-warning-800'
    };

    notification.className += ` ${colors[type] || colors.info} border-2`;
    notification.innerHTML = `
      <div class="flex items-center space-x-2">
        <i data-lucide="info" class="w-5 h-5"></i>
        <span class="font-medium">${message}</span>
      </div>
    `;

    document.body.appendChild(notification);
    lucide.createIcons();

    setTimeout(() => {
      notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
      notification.style.transform = 'translateX(100%)';
      setTimeout(() => {
        if (document.body.contains(notification)) {
          document.body.removeChild(notification);
        }
      }, 300);
    }, 3000);
  }

  // CETAK untuk Admin - menggunakan combo_id
  function printMapelAdmin(comboId) {
    const url = '<?= BASEURL; ?>/admin/cetakMapelAdmin/' + encodeURIComponent(comboId);
    const w = window.open(url, '_blank');
    if (!w || w.closed || typeof w.closed === 'undefined') {
      showNotification('Izinkan pop-up untuk menampilkan layar cetak.', 'warning');
      window.location.href = url;
    }
  }
</script>

<style>
  /* Button Styles */
  .btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    padding: 0.6rem 1rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: 1px solid rgba(59, 130, 246, 0.3);
    box-shadow: 0 6px 14px rgba(59, 130, 246, 0.15);
    transition: all 0.15s ease;
    white-space: nowrap;
    text-decoration: none;
  }
  
  .btn-primary:hover {
    filter: brightness(1.05);
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
  }

  .btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    padding: 0.6rem 1rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: #4338ca;
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    border: 1px solid rgba(99, 102, 241, 0.25);
    box-shadow: 0 6px 14px rgba(99, 102, 241, 0.12);
    transition: all 0.15s ease;
    white-space: nowrap;
    text-decoration: none;
  }
  
  .btn-secondary:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.18);
    background: linear-gradient(135deg, #c7d2fe, #a5b4fc);
  }

  .btn-print {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    padding: 0.6rem 1rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: 1px solid rgba(34, 197, 94, 0.3);
    box-shadow: 0 6px 14px rgba(34, 197, 94, 0.15);
    transition: all 0.15s ease;
    white-space: nowrap;
    cursor: pointer;
  }
  
  .btn-print:hover {
    filter: brightness(1.05);
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(34, 197, 94, 0.2);
  }

  /* Card animations */
  .animate-slide-up {
    animation: slideUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(30px);
  }

  @keyframes slideUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate-fade-in {
    animation: fadeIn 0.8s ease-out forwards;
    opacity: 0;
  }

  @keyframes fadeIn {
    to {
      opacity: 1;
    }
  }

  .progress-animate {
    animation: progressGrow 1.5s ease-out forwards;
    width: 0 !important;
  }

  @keyframes progressGrow {
    to {
      width: var(--target-width) !important;
    }
  }

  /* Glass effect */
  .glass-effect {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  /* Gradient classes */
  .gradient-primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .gradient-success { background: linear-gradient(135deg, #22c55e, #16a34a); }
  .gradient-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

  /* Card hover effect */
  .card-hover {
    transition: all 0.3s ease;
  }
</style>