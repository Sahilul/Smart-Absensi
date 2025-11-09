<?php // View expects $data array, not individual variables ?>
<div class="p-4 sm:p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Tagihan: <?= htmlspecialchars($data['tagihan']['nama'] ?? 'Baru') ?></h1>
    <div class="flex gap-2">
      <?php if (!empty($data['tagihan']['id'])): ?>
        <a href="<?= BASEURL ?>/waliKelas/pembayaranTagihanPDF/<?= (int)$data['tagihan']['id'] ?>" class="px-3 py-2 bg-green-100 hover:bg-green-200 rounded text-sm">Download PDF</a>
      <?php endif; ?>
      <a href="<?= BASEURL ?>/waliKelas/pembayaran" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">Kembali</a>
    </div>
  </div>

  <?php if (empty($data['tagihan'])): ?>
    <div class="bg-white rounded-lg shadow p-4">
      <h2 class="font-medium mb-3">Aktifkan dari Tagihan Global</h2>
      <form action="<?= BASEURL ?>/waliKelas/simpanTagihanKelas" method="POST" class="grid sm:grid-cols-5 gap-3">
        <input type="hidden" name="mode" value="global" />
        <div class="sm:col-span-2">
          <label class="block text-sm text-gray-600 mb-1">ID Tagihan Global</label>
          <input name="id_global" type="number" class="w-full border rounded px-3 py-2" placeholder="Masukkan ID Global" required />
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Nominal Default</label>
          <input name="nominal_default" type="number" min="0" class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Jatuh Tempo</label>
          <input name="jatuh_tempo" type="date" class="w-full border rounded px-3 py-2" />
        </div>
        <div class="flex items-end">
          <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Aktifkan</button>
        </div>
      </form>
    </div>
  <?php else: ?>
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid sm:grid-cols-4 gap-4">
        <div>
          <div class="text-xs text-gray-500">Nominal</div>
          <div class="font-semibold">Rp <?= number_format((int)($data['tagihan']['nominal_default'] ?? 0), 0, ',', '.') ?></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Jatuh Tempo</div>
          <div class="font-semibold"><?= !empty($data['tagihan']['jatuh_tempo']) ? htmlspecialchars($data['tagihan']['jatuh_tempo']) : '-' ?></div>
        </div>
        <div>
          <div class="text-xs text-gray-500">Tipe</div>
          <div class="font-semibold capitalize"><?= htmlspecialchars($data['tagihan']['tipe'] ?? '-') ?></div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
      <h2 class="font-medium mb-3">Status Per Siswa</h2>
      <?php
        $map = [];
        foreach (($data['tagihan_siswa'] ?? []) as $ts) { $map[$ts['id_siswa']] = $ts; }
      ?>

      <!-- Mobile: Cards -->
      <div class="space-y-3 sm:hidden">
        <?php foreach (($data['siswa_list'] ?? []) as $s):
          $ts = $map[$s['id_siswa']] ?? null;
          $nominal = $ts['nominal'] ?? ($data['tagihan']['nominal_default'] ?? 0);
          $diskon = $ts['diskon'] ?? 0;
          $terbayar = $ts['total_terbayar'] ?? 0;
          $status = $ts['status'] ?? 'belum';
        ?>
          <div class="border rounded-lg p-3">
            <div class="flex items-center justify-between">
              <div class="font-semibold"><?= htmlspecialchars($s['nama_siswa']) ?></div>
              <?php if ($status === 'lunas'): ?>
                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Lunas</span>
              <?php elseif ($status === 'sebagian'): ?>
                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Sebagian</span>
              <?php else: ?>
                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Belum</span>
              <?php endif; ?>
            </div>
            <div class="text-xs text-gray-500 mt-1">Nominal: Rp <?= number_format((int)$nominal, 0, ',', '.') ?> • Diskon: Rp <?= number_format((int)$diskon, 0, ',', '.') ?> • Terbayar: Rp <?= number_format((int)$terbayar, 0, ',', '.') ?></div>
            <div class="mt-2">
              <a class="inline-block px-3 py-2 text-sm bg-blue-600 text-white rounded" href="<?= BASEURL ?>/waliKelas/pembayaranInput/<?= (int)($data['tagihan']['id'] ?? 0) ?>/<?= (int)$s['id_siswa'] ?>">Input</a>
            </div>
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
              <th class="text-left p-2">Diskon</th>
              <th class="text-left p-2">Terbayar</th>
              <th class="text-left p-2">Status</th>
              <th class="text-left p-2">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($data['siswa_list'] ?? []) as $s):
              $ts = $map[$s['id_siswa']] ?? null;
              $nominal = $ts['nominal'] ?? ($data['tagihan']['nominal_default'] ?? 0);
              $diskon = $ts['diskon'] ?? 0;
              $terbayar = $ts['total_terbayar'] ?? 0;
              $status = $ts['status'] ?? 'belum';
            ?>
            <tr class="border-t">
              <td class="p-2"><?= htmlspecialchars($s['nama_siswa']) ?></td>
              <td class="p-2">Rp <?= number_format((int)$nominal, 0, ',', '.') ?></td>
              <td class="p-2">Rp <?= number_format((int)$diskon, 0, ',', '.') ?></td>
              <td class="p-2">Rp <?= number_format((int)$terbayar, 0, ',', '.') ?></td>
              <td class="p-2">
                <?php if ($status === 'lunas'): ?>
                  <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Lunas</span>
                <?php elseif ($status === 'sebagian'): ?>
                  <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Sebagian</span>
                <?php else: ?>
                  <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Belum</span>
                <?php endif; ?>
              </td>
              <td class="p-2">
                <a class="px-3 py-1 bg-blue-100 hover:bg-blue-200 rounded" href="<?= BASEURL ?>/waliKelas/pembayaranInput/<?= (int)($data['tagihan']['id'] ?? 0) ?>/<?= (int)$s['id_siswa'] ?>">Input</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>
