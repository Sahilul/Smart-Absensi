<?php // View expects $data array, not individual variables ?>
<div class="p-4 sm:p-6 max-w-2xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Input Pembayaran</h1>
    <a href="<?= BASEURL ?>/waliKelas/pembayaranTagihan/<?= (int)($data['tagihan']['id'] ?? 0) ?>" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">Kembali</a>
  </div>

  <div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="grid sm:grid-cols-2 gap-4 text-sm">
      <div>
        <div class="text-gray-500">Siswa</div>
        <div class="font-semibold"><?= htmlspecialchars($data['siswa']['nama_siswa'] ?? '-') ?></div>
      </div>
      <div>
        <div class="text-gray-500">Tagihan</div>
        <div class="font-semibold"><?= htmlspecialchars($data['tagihan']['nama'] ?? '-') ?></div>
      </div>
    </div>
  </div>

  <form action="<?= BASEURL ?>/waliKelas/prosesPembayaran" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-4">
    <input type="hidden" name="tagihan_id" value="<?= (int)($data['tagihan']['id'] ?? 0) ?>" />
    <input type="hidden" name="id_siswa" value="<?= (int)($data['siswa']['id_siswa'] ?? 0) ?>" />

    <div class="mb-3">
      <label class="block text-sm text-gray-600 mb-1">Jumlah (Rp)</label>
      <input name="jumlah" type="number" min="0" required class="w-full border rounded px-3 py-2" />
    </div>

    <div class="mb-3">
      <label class="block text-sm text-gray-600 mb-1">Metode</label>
      <select name="metode" class="w-full border rounded px-3 py-2">
        <option value="tunai">Tunai</option>
        <option value="transfer">Transfer</option>
        <option value="lainnya">Lainnya</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="block text-sm text-gray-600 mb-1">Keterangan</label>
      <input name="keterangan" type="text" class="w-full border rounded px-3 py-2" placeholder="Opsional" />
    </div>

    <!-- Optional: upload bukti di tahap berikutnya -->

    <div class="flex justify-end gap-2">
      <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Simpan</button>
    </div>
  </form>
</div>
