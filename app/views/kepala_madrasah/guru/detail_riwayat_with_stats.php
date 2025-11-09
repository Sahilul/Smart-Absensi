<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Breadcrumb & Back Button -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="<?= BASEURL; ?>/riwayatJurnal" 
                   class="p-2 rounded-xl text-secondary-500 hover:text-primary-600 hover:bg-white/50 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <nav class="flex items-center space-x-2 text-sm text-secondary-600">
                    <a href="<?= BASEURL; ?>/guru/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <a href="<?= BASEURL; ?>/riwayatJurnal" class="hover:text-primary-600 transition-colors">Riwayat Jurnal</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span class="text-secondary-800 font-medium">Detail <?= htmlspecialchars($data['nama_mapel'] ?? 'Mata Pelajaran'); ?> - <?= htmlspecialchars($data['nama_kelas'] ?? 'Kelas'); ?></span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-white/20 shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-800 flex items-center mb-2">
                        <i data-lucide="book-open-check" class="w-8 h-8 mr-3 text-success-500"></i>
                        <?= htmlspecialchars($data['nama_mapel'] ?? 'Mata Pelajaran'); ?>
                    </h2>
                    <p class="text-secondary-600 font-medium mb-1">
                        <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                        Kelas: <span class="gradient-success text-white px-3 py-1 rounded-lg text-sm font-semibold"><?= htmlspecialchars($data['nama_kelas'] ?? 'Kelas'); ?></span>
                    </p>
                    <p class="text-secondary-600 font-medium">
                        <i data-lucide="calendar-check" class="w-4 h-4 inline mr-2"></i>
                        Sesi: <span class="gradient-primary text-white px-3 py-1 rounded-lg text-sm font-semibold"><?= $_SESSION['nama_semester_aktif'] ?? 'Semester Aktif'; ?></span>
                    </p>
                    <p class="text-sm text-secondary-500 mt-2">Detail jurnal mengajar dan analisis kehadiran siswa untuk penugasan spesifik</p>
                </div>
                <div class="hidden md:block">
                    <div class="gradient-success p-4 rounded-2xl shadow-lg animate-pulse">
                        <i data-lucide="trending-up" class="w-12 h-12 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <?php 
    $absensi_data = $data['detail_absensi_siswa'] ?? [];
    $jurnal_data = $data['detail_jurnal'] ?? [];
    ?>
    
    <?php if (!empty($absensi_data)) : ?>
        <div class="mb-8">
            <h3 class="text-xl font-bold text-secondary-800 mb-4 flex items-center">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-2 text-primary-500"></i>
                Statistik Kehadiran Siswa
            </h3>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <?php 
                $total_siswa = count($absensi_data);
                $total_pertemuan = 0;
                $total_hadir = 0;
                $total_records = 0;
                
                // Hitung statistik dari data absensi siswa
                foreach ($absensi_data as $siswa) {
                    $pertemuan_siswa = (int)($siswa['total_pertemuan'] ?? count($jurnal_data));
                    $total_pertemuan = max($total_pertemuan, $pertemuan_siswa);
                    
                    $hadir = (int)($siswa['hadir'] ?? 0);
                    $izin = (int)($siswa['izin'] ?? 0);
                    $sakit = (int)($siswa['sakit'] ?? 0);
                    $alpha = (int)($siswa['alpha'] ?? 0);
                    
                    $total_hadir += $hadir;
                    $total_records += ($hadir + $izin + $sakit + $alpha);
                }
                
                // Fallback ke data jurnal jika pertemuan masih 0
                if ($total_pertemuan === 0 && !empty($jurnal_data)) {
                    $total_pertemuan = count($jurnal_data);
                }
                
                $avg_kehadiran = $total_records > 0 ? round(($total_hadir / $total_records) * 100, 1) : 0;
                ?>
                
                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Siswa</p>
                            <p class="text-2xl font-bold text-secondary-800"><?= $total_siswa; ?></p>
                        </div>
                        <div class="gradient-primary p-3 rounded-xl">
                            <i data-lucide="users" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Pertemuan</p>
                            <p class="text-2xl font-bold text-secondary-800"><?= $total_pertemuan; ?></p>
                        </div>
                        <div class="gradient-success p-3 rounded-xl">
                            <i data-lucide="calendar-check" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Total Hadir</p>
                            <p class="text-2xl font-bold text-secondary-800"><?= $total_hadir; ?></p>
                        </div>
                        <div class="gradient-warning p-3 rounded-xl">
                            <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg card-hover animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-secondary-600 mb-1">Rata-rata Kehadiran</p>
                            <p class="text-2xl font-bold text-secondary-800"><?= $avg_kehadiran; ?>%</p>
                            <div class="w-full bg-secondary-200 rounded-full h-2 mt-2">
                                <div class="gradient-success h-2 rounded-full" style="width: <?= $avg_kehadiran; ?>%"></div>
                            </div>
                        </div>
                        <div class="gradient-success p-3 rounded-xl">
                            <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden animate-slide-up" style="animation-delay: 0.4s;">
                <div class="gradient-primary p-6 text-white">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold flex items-center">
                            <i data-lucide="users" class="w-5 h-5 mr-2"></i>
                            Detail Kehadiran per Siswa (<?= $total_siswa; ?> siswa)
                        </h4>
                        <div class="flex items-center space-x-4">
                            <input type="text" 
                                   id="search-student" 
                                   placeholder="Cari siswa..." 
                                   class="input-modern text-secondary-800 text-sm py-2 px-3 w-48">
                            <button onclick="exportStudentData()" class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-colors">
                                <i data-lucide="download" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-white/50 sticky top-0">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-secondary-500 uppercase">Siswa</th>
                                <th class="px-4 py-4 text-center text-xs font-medium text-secondary-500 uppercase">Pertemuan</th>
                                <th class="px-4 py-4 text-center text-xs font-medium text-secondary-500 uppercase">Hadir</th>
                                <th class="px-4 py-4 text-center text-xs font-medium text-secondary-500 uppercase">Izin</th>
                                <th class="px-4 py-4 text-center text-xs font-medium text-secondary-500 uppercase">Sakit</th>
                                <th class="px-4 py-4 text-center text-xs font-medium text-secondary-500 uppercase">Alpha</th>
                                <th class="px-4 py-4 text-center text-xs font-medium text-secondary-500 uppercase">Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100" id="students-table-body">
                            <?php foreach ($absensi_data as $siswa) : ?>
                                <?php 
                                $hadir = (int)($siswa['hadir'] ?? 0);
                                $izin = (int)($siswa['izin'] ?? 0);
                                $sakit = (int)($siswa['sakit'] ?? 0);
                                $alpha = (int)($siswa['alpha'] ?? 0);
                                $pertemuan_siswa = (int)($siswa['total_pertemuan'] ?? $total_pertemuan);
                                
                                $total_kehadiran_siswa = $hadir + $izin + $sakit + $alpha;
                                $persentase_siswa = $total_kehadiran_siswa > 0 ? round(($hadir / $total_kehadiran_siswa) * 100, 1) : 0;
                                
                                // Determine color based on attendance percentage
                                if ($persentase_siswa >= 90) $color_class = 'text-success-600 bg-success-50';
                                elseif ($persentase_siswa >= 75) $color_class = 'text-blue-600 bg-blue-50';
                                elseif ($persentase_siswa >= 60) $color_class = 'text-warning-600 bg-warning-50';
                                else $color_class = 'text-danger-600 bg-danger-50';
                                ?>
                                <tr class="hover:bg-white/50 transition-colors student-row" 
                                    data-name="<?= strtolower(htmlspecialchars($siswa['nama_siswa'] ?? 'Unknown')); ?>">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-primary-400 to-success-400 rounded-lg flex items-center justify-center text-white font-bold">
                                                <?= substr(htmlspecialchars($siswa['nama_siswa'] ?? 'U'), 0, 1); ?>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($siswa['nama_siswa'] ?? 'Unknown'); ?></div>
                                                <div class="text-xs text-secondary-500"><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm text-secondary-600">
                                        <?= $pertemuan_siswa; ?>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                            <?= $hadir; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= $izin; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <?= $sakit; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <?= $alpha; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <span class="<?= $color_class ?> font-semibold text-sm px-2 py-1 rounded-lg">
                                                <?= $persentase_siswa; ?>%
                                            </span>
                                            <div class="w-12 bg-secondary-200 rounded-full h-2">
                                                <div class="h-2 rounded-full <?= str_replace('text-', 'bg-', explode(' ', $color_class)[0]); ?>" 
                                                     style="width: <?= $persentase_siswa; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mb-8">
            <div class="glass-effect rounded-xl border border-white/20 shadow-lg p-12 text-center">
                <div class="gradient-warning p-4 rounded-2xl inline-flex mb-4">
                    <i data-lucide="users-x" class="w-8 h-8 text-white"></i>
                </div>
                <h4 class="text-lg font-semibold text-secondary-800 mb-2">Belum Ada Data Absensi</h4>
                <p class="text-secondary-600">Data absensi siswa belum tersedia untuk penugasan ini.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Jurnal History -->
    <div class="mb-8">
        <h3 class="text-xl font-bold text-secondary-800 mb-4 flex items-center">
            <i data-lucide="book-open" class="w-5 h-5 mr-2 text-success-500"></i>
            Riwayat Jurnal Mengajar
        </h3>
        
        <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden animate-slide-up" style="animation-delay: 0.5s;">
            <div class="gradient-success p-6 text-white">
                <h4 class="text-lg font-semibold flex items-center">
                    <i data-lucide="scroll" class="w-5 h-5 mr-2"></i>
                    Daftar Pertemuan (<?= count($jurnal_data); ?> pertemuan)
                </h4>
            </div>
            
            <?php if (empty($jurnal_data)) : ?>
                <div class="p-12 text-center">
                    <div class="gradient-warning p-4 rounded-2xl inline-flex mb-4">
                        <i data-lucide="calendar-x" class="w-8 h-8 text-white"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-secondary-800 mb-2">Belum Ada Jurnal</h4>
                    <p class="text-secondary-600">Belum ada jurnal untuk penugasan ini.</p>
                </div>
            <?php else : ?>
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full">
                        <thead class="bg-white/50 sticky top-0">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-secondary-500 uppercase">Pertemuan</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-secondary-500 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-secondary-500 uppercase">Topik Materi</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-secondary-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            <?php foreach ($jurnal_data as $index => $jurnal) : ?>
                                <tr class="hover:bg-white/50 transition-colors animate-slide-up" style="animation-delay: <?= $index * 0.05; ?>s;">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                            Ke-<?= $jurnal['pertemuan_ke'] ?? ($index + 1); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-800">
                                        <div class="flex items-center space-x-2">
                                            <i data-lucide="calendar" class="w-4 h-4 text-secondary-400"></i>
                                            <span><?= date('d M Y', strtotime($jurnal['tanggal'] ?? 'now')); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            <p class="text-sm font-medium text-secondary-900 truncate" title="<?= htmlspecialchars($jurnal['topik_materi'] ?? 'Materi tidak tersedia'); ?>">
                                                <?= htmlspecialchars($jurnal['topik_materi'] ?? 'Materi tidak tersedia'); ?>
                                            </p>
                                            <?php if (!empty($jurnal['catatan'])) : ?>
                                                <p class="text-xs text-secondary-500 mt-1 truncate" title="<?= htmlspecialchars($jurnal['catatan']); ?>">
                                                    <?= htmlspecialchars($jurnal['catatan']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Edit Jurnal -->
                                            <a href="<?= BASEURL; ?>/guru/editJurnal/<?= $jurnal['id_jurnal']; ?>" 
                                               class="p-2 rounded-lg bg-primary-100 text-primary-600 hover:bg-primary-200 transition-colors" 
                                               title="Edit Jurnal">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>

                                            <!-- Edit Absensi -->
                                            <a href="<?= BASEURL; ?>/guru/editAbsensi/<?= $jurnal['id_jurnal']; ?>" 
                                               class="p-2 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 transition-colors" 
                                               title="Edit Absensi">
                                                <i data-lucide="user-cog" class="w-4 h-4"></i>
                                            </a>

                                            <!-- Cetak -->
                                            <a href="<?= BASEURL; ?>/guru/cetakAbsensi/<?= $jurnal['id_jurnal']; ?>" target="_blank" 
                                               class="p-2 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors" 
                                               title="Cetak Laporan">
                                                <i data-lucide="printer" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg text-center animate-slide-up" style="animation-delay: 0.6s;">
        <div class="max-w-2xl mx-auto">
            <h3 class="text-lg font-semibold text-secondary-800 mb-2">Aksi Selanjutnya</h3>
            <p class="text-sm text-secondary-600 mb-6">Kelola jurnal dan buat pertemuan baru untuk penugasan ini</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a href="<?= BASEURL; ?>/guru/jurnal" class="btn-primary">
                    <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                    Buat Jurnal Baru
                </a>
                <?php if (isset($data['info_penugasan']['id_penugasan'])) : ?>
                <a href="<?= BASEURL; ?>/riwayatJurnal/cetak/<?= $data['info_penugasan']['id_penugasan']; ?>" target="_blank" class="btn-secondary">
                    <i data-lucide="printer" class="w-4 h-4 inline mr-2"></i>
                    Cetak Laporan
                </a>
                <?php endif; ?>
                <a href="<?= BASEURL; ?>/riwayatJurnal" class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                    Kembali ke Ringkasan
                </a>
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Search functionality for students
        const searchInput = document.getElementById('search-student');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const studentRows = document.querySelectorAll('.student-row');
                
                studentRows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    if (name && name.includes(searchTerm)) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        
        // Animate progress bars
        const progressBars = document.querySelectorAll('[style*="width:"]');
        progressBars.forEach((bar, index) => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
                bar.style.transition = 'width 1s ease-in-out';
            }, 500 + (index * 100));
        });

        // Initialize card hover effects
        const cards = document.querySelectorAll('.card-hover');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px) scale(1.02)';
                this.style.transition = 'transform 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    });

    function exportStudentData() {
        showNotification('Mengexport data kehadiran siswa...', 'success');
        
        // Create CSV export
        const rows = [];
        const headers = ['Nama Siswa', 'NISN', 'Total Pertemuan', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Persentase'];
        rows.push(headers.join(','));
        
        document.querySelectorAll('.student-row').forEach(row => {
            if (row.style.display !== 'none') {
                const cells = row.querySelectorAll('td');
                const nama = cells[0].querySelector('.text-sm').textContent.trim();
                const nisn = cells[0].querySelector('.text-xs').textContent.trim();
                const pertemuan = cells[1].textContent.trim();
                const hadir = cells[2].textContent.trim();
                const izin = cells[3].textContent.trim();
                const sakit = cells[4].textContent.trim();
                const alpha = cells[5].textContent.trim();
                const persentase = cells[6].querySelector('span').textContent.trim();
                
                rows.push([nama, nisn, pertemuan, hadir, izin, sakit, alpha, persentase].join(','));
            }
        });
        
        const csvContent = "data:text/csv;charset=utf-8," + rows.join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `kehadiran-siswa-${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transition-all duration-300 transform translate-x-full`;
        
        const colors = {
            success: 'bg-success-100 border-success-300 text-success-800',
            info: 'bg-blue-100 border-blue-300 text-blue-800',
            warning: 'bg-warning-100 border-warning-300 text-warning-800',
            error: 'bg-danger-100 border-danger-300 text-danger-800'
        };

        notification.className += ` ${colors[type] || colors.info} border-2`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
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

.input-modern {
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 0.5rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
}
</style>