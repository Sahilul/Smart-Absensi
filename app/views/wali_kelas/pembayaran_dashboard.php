<?php // View expects $data array, not individual variables ?>
<div class="p-4 sm:p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Pembayaran Kelas - <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '') ?></h1>
    <div class="flex gap-2">
      <a href="<?= BASEURL ?>/waliKelas/pembayaranRiwayat" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">Riwayat</a>
      <a href="<?= BASEURL ?>/waliKelas/pembayaranExport" class="px-3 py-2 bg-green-100 hover:bg-green-200 rounded text-sm">Download PDF</a>
    </div>
  </div>

  <div class="bg-white rounded-lg shadow p-4 mb-6">
    <h2 class="font-medium mb-3">Buat Tagihan Kelas</h2>
    <form action="<?= BASEURL ?>/waliKelas/simpanTagihanKelas" method="POST" class="grid sm:grid-cols-5 gap-3">
      <input type="hidden" name="mode" value="baru" />
      <div class="sm:col-span-2">
        <label class="block text-sm text-gray-600 mb-1">Nama Tagihan</label>
        <input name="nama" type="text" class="w-full border rounded px-3 py-2" placeholder="Contoh: SPP November" required />
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Nominal Default</label>
        <input name="nominal_default" type="number" min="0" class="w-full border rounded px-3 py-2" required />
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Jatuh Tempo</label>
        <input name="jatuh_tempo" type="date" class="w-full border rounded px-3 py-2" />
      </div>
      <div class="flex items-end">
        <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Simpan</button>
      </div>
    </form>
  </div>

  <div class="bg-white rounded-lg shadow p-4">
    <h2 class="font-medium mb-3">Daftar Tagihan Kelas</h2>
    <?php if (empty($data['tagihan_list'])): ?>
      <p class="text-gray-500 text-sm">Belum ada tagihan. Buat tagihan baru di atas.</p>
    <?php else: ?>
      <!-- Mobile: Cards -->
      <div class="space-y-3 sm:hidden">
        <?php foreach ($data['tagihan_list'] as $t): ?>
          <div class="border rounded-lg p-3 flex items-center justify-between">
            <div>
              <div class="font-semibold"><?= htmlspecialchars($t['nama']) ?></div>
              <div class="text-xs text-gray-500">Rp <?= number_format((int)$t['nominal_default'], 0, ',', '.') ?> • <?= htmlspecialchars($t['tipe']) ?><?= $t['jatuh_tempo'] ? ' • JT ' . htmlspecialchars($t['jatuh_tempo']) : '' ?></div>
            </div>
            <a href="<?= BASEURL ?>/waliKelas/pembayaranTagihan/<?= (int)$t['id'] ?>" class="px-3 py-2 text-sm bg-blue-600 text-white rounded">Kelola</a>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Desktop: Table -->
      <div class="overflow-x-auto hidden sm:block">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-gray-50">
              <th class="text-left p-2">Nama</th>
              <th class="text-left p-2">Nominal</th>
              <th class="text-left p-2">Jatuh Tempo</th>
              <th class="text-left p-2">Tipe</th>
              <th class="text-left p-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($data['tagihan_list'] as $t): ?>
            <tr class="border-t">
              <td class="p-2"><?= htmlspecialchars($t['nama']) ?></td>
              <td class="p-2">Rp <?= number_format((int)$t['nominal_default'], 0, ',', '.') ?></td>
              <td class="p-2"><?= $t['jatuh_tempo'] ? htmlspecialchars($t['jatuh_tempo']) : '-' ?></td>
              <td class="p-2 capitalize"><?= htmlspecialchars($t['tipe']) ?></td>
              <td class="p-2">
                <a href="<?= BASEURL ?>/waliKelas/pembayaranTagihan/<?= (int)$t['id'] ?>" class="px-3 py-1 bg-blue-100 hover:bg-blue-200 rounded">Kelola</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
