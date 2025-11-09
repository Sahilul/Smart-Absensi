<main class="flex-1 overflow-x-hidden overflow-y-auto bg-secondary-50 p-4 md:p-6">
  <div class="mb-6">
    <div class="bg-white shadow-sm rounded-xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <h4 class="text-xl font-bold text-slate-800 mb-1">
          <i data-lucide="calendar-days" class="inline-block w-6 h-6 mr-2"></i>
          Absensi Harian Kelas
        </h4>
        <p class="text-slate-500 text-sm">Ringkasan kehadiran per siswa pada tanggal tertentu per mapel.</p>
      </div>
      <form method="GET" action="<?= BASEURL; ?>/waliKelas/absensiHarian" class="flex flex-col md:flex-row items-stretch md:items-end gap-3 w-full md:w-auto">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
          <input type="date" name="tanggal" value="<?= htmlspecialchars($data['tanggal'] ?? date('Y-m-d')); ?>" class="px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm w-full md:w-auto" />
        </div>
        <button type="submit" class="md:mt-[22px] px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors">
          <i data-lucide="search" class="w-4 h-4 inline-block mr-1"></i> Tampilkan
        </button>
      </form>
    </div>
  </div>

  <?php if (!empty($data['error'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
      <?= htmlspecialchars($data['error']); ?>
    </div>
  <?php endif; ?>

  <div class="bg-white shadow-sm rounded-xl p-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h5 class="text-lg font-bold text-slate-800">Tanggal: <span class="text-blue-600 font-semibold"><?= date('d/m/Y', strtotime($data['tanggal'] ?? date('Y-m-d'))); ?></span></h5>
        <p class="text-xs text-slate-500 mt-1">Semester: <?= htmlspecialchars($_SESSION['nama_semester_aktif'] ?? ''); ?> | Kelas: <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '-'); ?></p>
      </div>
      <div class="text-xs text-slate-400">Status: H = Hadir, A = Alfa, I = Izin, S = Sakit</div>
    </div>

    <?php $siswaData = $data['data_siswa'] ?? []; ?>
    <?php if (empty($siswaData)): ?>
      <div class="text-center py-16">
        <div class="mx-auto w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
          <i data-lucide="clipboard-x" class="w-8 h-8 text-slate-400"></i>
        </div>
        <p class="text-slate-600 font-medium">Belum ada data absensi pada tanggal ini.</p>
        <p class="text-xs text-slate-500 mt-1">Pastikan guru sudah mengisi jurnal dan absensi.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full text-xs md:text-sm border border-slate-200 rounded-lg overflow-hidden">
          <thead class="bg-slate-50">
            <tr class="text-left text-slate-700">
              <th class="px-3 py-2 border-b border-slate-200">No</th>
              <th class="px-3 py-2 border-b border-slate-200">Nama Siswa</th>
              <th class="px-3 py-2 border-b border-slate-200">Hadir</th>
              <th class="px-3 py-2 border-b border-slate-200">Alfa</th>
              <th class="px-3 py-2 border-b border-slate-200">Izin</th>
              <th class="px-3 py-2 border-b border-slate-200">Sakit</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($siswaData as $i => $s): ?>
              <tr class="border-b last:border-0 hover:bg-slate-50">
                <td class="px-3 py-2 text-slate-600 font-medium"><?= $i+1; ?></td>
                <td class="px-3 py-2 font-semibold text-slate-800 whitespace-nowrap"><?= htmlspecialchars($s['nama_siswa']); ?></td>
                <td class="px-3 py-2">
                  <?php foreach ($s['hadir'] as $m): ?>
                    <span class="inline-flex items-center m-0.5 px-2 py-1 rounded-full bg-green-100 text-green-700 text-[11px] font-medium" title="Guru: <?= htmlspecialchars($m['guru']); ?> | Pertemuan: <?= $m['pertemuan_ke']; ?> | <?= htmlspecialchars($m['ket']); ?>">H-<?= htmlspecialchars($m['mapel']); ?></span>
                  <?php endforeach; ?>
                </td>
                <td class="px-3 py-2">
                  <?php foreach ($s['alfa'] as $m): ?>
                    <span class="inline-flex items-center m-0.5 px-2 py-1 rounded-full bg-red-100 text-red-700 text-[11px] font-medium" title="Guru: <?= htmlspecialchars($m['guru']); ?> | Pertemuan: <?= $m['pertemuan_ke']; ?> | <?= htmlspecialchars($m['ket']); ?>">A-<?= htmlspecialchars($m['mapel']); ?></span>
                  <?php endforeach; ?>
                </td>
                <td class="px-3 py-2">
                  <?php foreach ($s['izin'] as $m): ?>
                    <span class="inline-flex items-center m-0.5 px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-[11px] font-medium" title="Guru: <?= htmlspecialchars($m['guru']); ?> | Pertemuan: <?= $m['pertemuan_ke']; ?> | <?= htmlspecialchars($m['ket']); ?>">I-<?= htmlspecialchars($m['mapel']); ?></span>
                  <?php endforeach; ?>
                </td>
                <td class="px-3 py-2">
                  <?php foreach ($s['sakit'] as $m): ?>
                    <span class="inline-flex items-center m-0.5 px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-[11px] font-medium" title="Guru: <?= htmlspecialchars($m['guru']); ?> | Pertemuan: <?= $m['pertemuan_ke']; ?> | <?= htmlspecialchars($m['ket']); ?>">S-<?= htmlspecialchars($m['mapel']); ?></span>
                  <?php endforeach; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</main>
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
