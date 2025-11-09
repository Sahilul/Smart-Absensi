<?php
// Proses data rekap untuk ditampilkan dengan safety check
$rekap = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];

// Safety check untuk memastikan data ada
if (isset($data['rekap_absensi']) && is_array($data['rekap_absensi'])) {
    foreach ($data['rekap_absensi'] as $row) {
        if (isset($row['status_kehadiran']) && isset($row['total'])) {
            $rekap[$row['status_kehadiran']] = (int)$row['total'];
        }
    }
}

$total_pertemuan = array_sum($rekap);
$persentase_hadir = ($total_pertemuan > 0) ? round(($rekap['H'] / $total_pertemuan) * 100) : 0;

// Debug information (dapat dihapus setelah testing)
// echo "<!-- DEBUG: Total pertemuan: $total_pertemuan, Rekap: " . json_encode($rekap) . " -->";

// Menentukan status berdasarkan persentase
$status_kehadiran = 'poor';
$status_text = 'Perlu Peningkatan';
$status_color = 'danger';

if ($persentase_hadir >= 90) {
    $status_kehadiran = 'excellent';
    $status_text = 'Excellent';
    $status_color = 'success';
} elseif ($persentase_hadir >= 75) {
    $status_kehadiran = 'good';
    $status_text = 'Baik';
    $status_color = 'primary';
} elseif ($persentase_hadir >= 60) {
    $status_kehadiran = 'fair';
    $status_text = 'Cukup';
    $status_color = 'warning';
}
?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Header dengan Greeting -->
    <div class="mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-white/20 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-800 mb-2">
                        Selamat <?= (date('H') < 12) ? 'Pagi' : ((date('H') < 17) ? 'Siang' : 'Sore'); ?>, 
                        <?= $_SESSION['nama_lengkap']; ?>! 👋
                    </h1>
                    <p class="text-secondary-600 text-lg">
                        Hari ini <?= date('d F Y'); ?> • <?= $_SESSION['nama_semester_aktif']; ?>
                    </p>
                </div>
                <div class="hidden md:block">
                    <div class="gradient-<?= $status_color; ?> text-white px-6 py-3 rounded-xl shadow-lg">
                        <div class="text-center">
                            <div class="text-2xl font-bold"><?= $persentase_hadir; ?>%</div>
                            <div class="text-sm opacity-90"><?= $status_text; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Statistik Utama -->
        <div class="xl:col-span-2 space-y-6">
            
            <?php if ($total_pertemuan === 0): ?>
                <!-- Empty State -->
                <div class="glass-effect rounded-xl p-12 border border-white/20 shadow-lg text-center">
                    <div class="w-24 h-24 bg-secondary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="calendar-x" class="w-12 h-12 text-secondary-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-secondary-800 mb-3">Belum Ada Data Absensi</h3>
                    <p class="text-secondary-600 mb-6">Belum ada data absensi untuk semester ini. Data akan muncul setelah guru menginput absensi.</p>
                    <a href="<?= BASEURL; ?>/siswa/absensiHarian" class="btn-primary inline-flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                        Cek Riwayat Absensi
                    </a>
                </div>
            <?php else: ?>
            
            <!-- Cards Ringkasan dengan Animasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <!-- Total Pertemuan -->
                <div class="glass-effect rounded-xl p-5 border border-white/20 shadow-lg card-hover group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-secondary-500 text-sm font-medium mb-1">Total Pertemuan</div>
                            <div class="text-3xl font-bold text-secondary-800 group-hover:text-primary-600 transition-colors" data-counter="true">
                                <?= $total_pertemuan; ?>
                            </div>
                        </div>
                        <div class="gradient-primary p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="calendar-days" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Kehadiran -->
                <div class="glass-effect rounded-xl p-5 border border-white/20 shadow-lg card-hover group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-secondary-500 text-sm font-medium mb-1">Hadir</div>
                            <div class="text-3xl font-bold text-success-600 group-hover:scale-105 transition-transform" data-counter="true">
                                <?= $rekap['H']; ?>
                            </div>
                        </div>
                        <div class="gradient-success p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Izin -->
                <div class="glass-effect rounded-xl p-5 border border-white/20 shadow-lg card-hover group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-secondary-500 text-sm font-medium mb-1">Izin</div>
                            <div class="text-3xl font-bold text-primary-600 group-hover:scale-105 transition-transform" data-counter="true">
                                <?= $rekap['I']; ?>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Sakit -->
                <div class="glass-effect rounded-xl p-5 border border-white/20 shadow-lg card-hover group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-secondary-500 text-sm font-medium mb-1">Sakit</div>
                            <div class="text-3xl font-bold text-warning-600 group-hover:scale-105 transition-transform" data-counter="true">
                                <?= $rekap['S']; ?>
                            </div>
                        </div>
                        <div class="gradient-warning p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="thermometer" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Alpha -->
                <div class="glass-effect rounded-xl p-5 border border-white/20 shadow-lg card-hover group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-secondary-500 text-sm font-medium mb-1">Alpha</div>
                            <div class="text-3xl font-bold text-danger-600 group-hover:scale-105 transition-transform" data-counter="true">
                                <?= $rekap['A']; ?>
                            </div>
                        </div>
                        <div class="gradient-danger p-3 rounded-xl shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="x-circle" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Kehadiran -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-secondary-800">Progress Kehadiran</h3>
                    <span class="status-<?= $status_kehadiran; ?> px-3 py-1 rounded-full text-sm font-semibold">
                        <?= $status_text; ?>
                    </span>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-secondary-600 font-medium">Tingkat Kehadiran</span>
                        <span class="text-2xl font-bold text-<?= $status_color; ?>-600"><?= $persentase_hadir; ?>%</span>
                    </div>
                    
                    <div class="progress-bar h-4 rounded-full">
                        <div class="progress-fill gradient-<?= $status_color; ?> rounded-full" 
                             style="width: <?= $persentase_hadir; ?>%"></div>
                    </div>
                </div>

                <!-- Detail Breakdown -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mt-6">
                    <div class="text-center">
                        <div class="w-12 h-12 gradient-success rounded-full flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="check" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="text-lg font-bold text-secondary-800"><?= $rekap['H']; ?></div>
                        <div class="text-sm text-secondary-500">Hadir</div>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="text-lg font-bold text-secondary-800"><?= $rekap['I']; ?></div>
                        <div class="text-sm text-secondary-500">Izin</div>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 gradient-warning rounded-full flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="thermometer" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="text-lg font-bold text-secondary-800"><?= $rekap['S']; ?></div>
                        <div class="text-sm text-secondary-500">Sakit</div>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 gradient-danger rounded-full flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="x" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="text-lg font-bold text-secondary-800"><?= $rekap['A']; ?></div>
                        <div class="text-sm text-secondary-500">Alpha</div>
                    </div>
                    <div class="text-center sm:col-span-5 lg:col-span-1">
                        <div class="w-12 h-12 gradient-primary rounded-full flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="percent" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="text-lg font-bold text-secondary-800"><?= $persentase_hadir; ?>%</div>
                        <div class="text-sm text-secondary-500">Persentase</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-xl font-bold text-secondary-800 mb-4">Aksi Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="<?= BASEURL; ?>/siswa/absensiHarian" class="btn-primary text-center block p-4 rounded-xl no-underline">
                        <i data-lucide="calendar-check" class="w-6 h-6 mx-auto mb-2"></i>
                        <div class="font-semibold">Absensi Harian</div>
                        <div class="text-sm opacity-90">Lihat riwayat harian</div>
                    </a>
                    <a href="<?= BASEURL; ?>/siswa/rekapAbsensi" class="btn-secondary text-center block p-4 rounded-xl no-underline">
                        <i data-lucide="bar-chart-3" class="w-6 h-6 mx-auto mb-2"></i>
                        <div class="font-semibold">Rekap Absensi</div>
                        <div class="text-sm opacity-70">Lihat per mata pelajaran</div>
                    </a>
                    <button onclick="window.print()" class="btn-secondary text-center block p-4 rounded-xl">
                        <i data-lucide="printer" class="w-6 h-6 mx-auto mb-2"></i>
                        <div class="font-semibold">Cetak Laporan</div>
                        <div class="text-sm opacity-70">Print dashboard</div>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Kolom Kanan: Grafik dan Info -->
        <div class="space-y-6">
            
            <!-- Grafik Pie -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-xl font-bold text-secondary-800 mb-4">Visualisasi Kehadiran</h3>
                <div class="h-64 relative">
                    <canvas id="absensiPieChart"></canvas>
                </div>
            </div>

            <!-- Motivasi Card -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-bold text-secondary-800 mb-3 flex items-center">
                    <i data-lucide="zap" class="w-5 h-5 mr-2 text-warning-500"></i>
                    Motivasi Hari Ini
                </h3>
                <div class="text-center p-4 bg-gradient-to-r from-primary-50 to-success-50 rounded-lg">
                    <?php if ($persentase_hadir >= 90): ?>
                        <div class="text-4xl mb-2">🌟</div>
                        <p class="text-secondary-700 font-medium">Luar biasa! Kehadiranmu sangat konsisten. Pertahankan semangat ini!</p>
                    <?php elseif ($persentase_hadir >= 75): ?>
                        <div class="text-4xl mb-2">📚</div>
                        <p class="text-secondary-700 font-medium">Bagus! Kamu hampir mencapai target kehadiran yang sempurna.</p>
                    <?php elseif ($persentase_hadir >= 60): ?>
                        <div class="text-4xl mb-2">💪</div>
                        <p class="text-secondary-700 font-medium">Ayo semangat! Tingkatkan lagi kehadiranmu untuk masa depan yang lebih baik.</p>
                    <?php else: ?>
                        <div class="text-4xl mb-2">🚀</div>
                        <p class="text-secondary-700 font-medium">Setiap hari adalah kesempatan baru. Mari mulai meningkatkan kehadiran!</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Semester -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
                <h3 class="text-lg font-bold text-secondary-800 mb-3 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2 text-primary-500"></i>
                    Info Akademik
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-secondary-50 rounded-lg">
                        <span class="text-secondary-600 font-medium">Semester Aktif</span>
                        <span class="font-bold text-primary-600"><?= $_SESSION['nama_semester_aktif']; ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-secondary-50 rounded-lg">
                        <span class="text-secondary-600 font-medium">Target Kehadiran</span>
                        <span class="font-bold text-success-600">≥ 75%</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-secondary-50 rounded-lg">
                        <span class="text-secondary-600 font-medium">Status Saat Ini</span>
                        <span class="font-bold text-<?= $status_color; ?>-600"><?= $status_text; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk Chart.js dengan safety check
        const rekapData = {
            hadir: parseInt(<?= $rekap['H']; ?>) || 0,
            izin: parseInt(<?= $rekap['I']; ?>) || 0,
            sakit: parseInt(<?= $rekap['S']; ?>) || 0,
            alfa: parseInt(<?= $rekap['A']; ?>) || 0
        };

        // Debug log untuk troubleshooting
        console.log('Rekap Data:', rekapData);

        const ctxPie = document.getElementById('absensiPieChart');
        if (ctxPie && typeof Chart !== 'undefined') {
            // Check if there's any data to show
            const totalData = rekapData.hadir + rekapData.izin + rekapData.sakit + rekapData.alfa;
            
            if (totalData === 0) {
                // Show empty state for chart
                ctxPie.parentElement.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-64 text-secondary-500">
                        <i data-lucide="pie-chart" class="w-16 h-16 mb-4"></i>
                        <p class="text-lg font-medium">Belum ada data absensi</p>
                        <p class="text-sm">Grafik akan muncul setelah ada data</p>
                    </div>
                `;
                // Re-initialize Lucide icons for the new content
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            } else {
                new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                        datasets: [{
                            label: 'Jumlah',
                            data: [rekapData.hadir, rekapData.izin, rekapData.sakit, rekapData.alfa],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.8)', // green
                                'rgba(59, 130, 246, 0.8)', // blue
                                'rgba(245, 158, 11, 0.8)',  // yellow
                                'rgba(239, 68, 68, 0.8)'   // red
                            ],
                            borderColor: [
                                'rgba(34, 197, 94, 1)',
                                'rgba(59, 130, 246, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(239, 68, 68, 1)'
                            ],
                            borderWidth: 2,
                            hoverBackgroundColor: [
                                'rgba(34, 197, 94, 0.9)',
                                'rgba(59, 130, 246, 0.9)',
                                'rgba(245, 158, 11, 0.9)',
                                'rgba(239, 68, 68, 0.9)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        family: 'Plus Jakarta Sans'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#fff',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((context.raw / total) * 100) : 0;
                                        return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true,
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        cutout: '50%'
                    }
                });
            }
        } else {
            console.warn('Chart.js not found or canvas element missing');
        }

        // Animasi counter HANYA untuk elemen statistik
        function animateCounters() {
            const counters = document.querySelectorAll('[data-counter="true"]');
            counters.forEach(counter => {
                const targetText = counter.textContent.trim();
                const target = parseInt(targetText) || 0;
                
                // Skip jika target 0 atau tidak valid
                if (target === 0 || isNaN(target)) {
                    return;
                }
                
                // Simpan nilai original
                counter.setAttribute('data-original', target);
                
                let current = 0;
                const increment = Math.ceil(target / 15); // Lebih smooth
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = current;
                    }
                }, 40);
            });
        }

        // Mulai animasi setelah halaman load
        setTimeout(animateCounters, 500);

        // Inisialisasi Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>