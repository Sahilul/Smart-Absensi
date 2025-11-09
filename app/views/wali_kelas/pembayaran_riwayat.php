<?php // View expects $data array, not individual variables ?>
<div class="p-4 sm:p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Riwayat Pembayaran - <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '') ?></h1>
    <div class="flex gap-2">
      <a href="<?= BASEURL ?>/waliKelas/pembayaran" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">Kembali</a>
      <a href="<?= BASEURL ?>/waliKelas/pembayaranExport" class="px-3 py-2 bg-green-100 hover:bg-green-200 rounded text-sm">Download PDF</a>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow p-4">
    <?php if (empty($data['riwayat'])): ?>
      <p class="text-gray-500 text-sm">Belum ada transaksi.</p>
    <?php else: ?>
      <!-- Mobile: Cards -->
      <div class="space-y-3 sm:hidden">
        <?php foreach ($data['riwayat'] as $r): ?>
          <div class="border rounded-lg p-3">
            <div class="flex items-center justify-between">
              <div class="font-semibold">Rp <?= number_format((int)$r['jumlah'], 0, ',', '.') ?></div>
              <div class="text-xs text-gray-500 whitespace-nowrap"><?= htmlspecialchars($r['tanggal']) ?></div>
            </div>
            <div class="text-xs text-gray-600 mt-1">Siswa: <?= htmlspecialchars($r['nama_siswa']) ?></div>
            <div class="text-xs text-gray-600">Tagihan: <?= htmlspecialchars($r['nama_tagihan']) ?></div>
            <div class="text-xs text-gray-500">Metode: <?= htmlspecialchars($r['metode'] ?? '-') ?><?= !empty($r['keterangan']) ? ' • ' . htmlspecialchars($r['keterangan']) : '' ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Desktop: Table -->
      <div class="overflow-x-auto hidden sm:block">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-gray-50">
              <th class="text-left p-2">Tanggal</th>
              <th class="text-left p-2">Siswa</th>
              <th class="text-left p-2">Tagihan</th>
              <th class="text-left p-2">Jumlah</th>
              <th class="text-left p-2">Metode</th>
              <th class="text-left p-2">Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data['riwayat'] as $r): ?>
              <tr class="border-t">
                <td class="p-2 whitespace-nowrap"><?= htmlspecialchars($r['tanggal']) ?></td>
                <td class="p-2"><?= htmlspecialchars($r['nama_siswa']) ?></td>
                <td class="p-2"><?= htmlspecialchars($r['nama_tagihan']) ?></td>
                <td class="p-2">Rp <?= number_format((int)$r['jumlah'], 0, ',', '.') ?></td>
                <td class="p-2"><?= htmlspecialchars($r['metode'] ?? '-') ?></td>
                <td class="p-2"><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
