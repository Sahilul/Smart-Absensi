<?php /* File: app/views/guru/rekap_absen_detail.php */ ?>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                <i data-lucide="bar-chart" class="w-8 h-8 mr-3 text-primary-500"></i>
                Rekap Absensi - Detail Penugasan
            </h2>
            <p class="text-secondary-600 mt-2">Ringkasan kehadiran untuk mapel & kelas ini (Semester <?= htmlspecialchars($_SESSION['nama_semester_aktif'] ?? '-') ?>)</p>
        </div>
        <?php if (!empty($data['id_penugasan'])): ?>
        <div class="flex gap-2">
            <a href="<?= BASEURL; ?>/guru/downloadRekapAbsenPDF/<?= $data['id_penugasan']; ?>" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm" title="Download Rekap PDF">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                PDF
            </a>
            <a href="<?= BASEURL; ?>/guru/rekapAbsen" class="inline-flex items-center px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white rounded-lg transition-colors duration-200 font-medium text-sm">
                <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                Semua Rekap
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if (empty($data['rekap_detail'])): ?>
        <div class="glass-effect rounded-xl p-12 border border-white/20 shadow-lg text-center">
            <div class="gradient-warning p-4 rounded-2xl inline-flex mb-6">
                <i data-lucide="calendar-x" class="w-12 h-12 text-white"></i>
            </div>
            <h3 class="text-xl font-semibold text-secondary-800 mb-3">Tidak Ada Data</h3>
            <p class="text-secondary-600 mb-6">Belum ada jurnal atau absensi pada penugasan ini.</p>
        </div>
    <?php else: ?>
        <?php $r = $data['rekap_detail']; $totalPert = (int)$r['total_pertemuan']; $pct = $totalPert>0 ? round(($r['hadir']/$totalPert)*100, 1) : 0; ?>
        <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-4">
                <div>
                    <h3 class="text-2xl font-semibold text-secondary-800 flex items-center">
                        <i data-lucide="book-open" class="w-6 h-6 mr-2"></i>
                        <?= htmlspecialchars($r['nama_mapel']); ?>
                    </h3>
                    <p class="text-secondary-600 mt-1">Kelas: <strong><?= htmlspecialchars($r['nama_kelas']); ?></strong></p>
                </div>
                <div class="text-right">
                    <div class="text-xs text-secondary-500">Total Pertemuan</div>
                    <div class="text-3xl font-bold text-secondary-800"><?= $totalPert; ?></div>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-xs text-green-700">Hadir</div>
                    <div class="text-2xl font-bold text-green-700"><?= (int)$r['hadir']; ?></div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="text-xs text-blue-700">Izin</div>
                    <div class="text-2xl font-bold text-blue-700"><?= (int)$r['izin']; ?></div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <div class="text-xs text-yellow-700">Sakit</div>
                    <div class="text-2xl font-bold text-yellow-700"><?= (int)$r['sakit']; ?></div>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <div class="text-xs text-red-700">Alpha</div>
                    <div class="text-2xl font-bold text-red-700"><?= (int)$r['alpha']; ?></div>
                </div>
            </div>
            <div class="mt-6">
                <div class="w-full bg-secondary-100 rounded-full h-4 overflow-hidden">
                    <div class="h-4 rounded-full gradient-primary" style="width: <?= max(0, min(100, $pct)); ?>%"></div>
                </div>
                <div class="mt-2 text-sm text-secondary-700">Persentase Hadir: <strong><?= $pct; ?>%</strong></div>
            </div>
            <div class="mt-6 flex gap-3">
                <a href="<?= BASEURL; ?>/guru/rincianAbsen?id_mapel=<?= urlencode($r['id_mapel']); ?>&id_kelas=<?= urlencode($r['id_kelas']); ?>&periode=semester" class="inline-flex items-center px-3 py-2 bg-secondary-600 hover:bg-secondary-700 text-white rounded-lg text-sm" title="Lihat Rincian Absensi">
                    <i data-lucide="table" class="w-4 h-4 mr-2"></i> Lihat Rincian Pertemuan
                </a>
            </div>
        </div>
    <?php endif; ?>
</main>
<script>if (typeof lucide!=='undefined'){lucide.createIcons();}</script>
