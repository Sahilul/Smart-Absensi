<?php
// View: app/views/guru/profil.php (Identitas Guru, tanpa ubah NIK)
$guru = $data['guru'] ?? [];
$nik = $guru['nik'] ?? '';
$nama = $guru['nama_guru'] ?? ($_SESSION['user_nama_lengkap'] ?? '');
$email = $guru['email'] ?? '';
?>
<div class="p-4 sm:p-6">
  <div class="max-w-xl mx-auto bg-white rounded-xl shadow p-5">
    <h1 class="text-xl font-semibold mb-1">Profil</h1>
    <p class="text-gray-500 text-sm mb-4">Perbarui identitas Anda. NIK tidak dapat diubah.</p>

    <form action="<?= BASEURL ?>/guru/simpanProfil" method="POST" class="space-y-4">
      <div>
        <label class="block text-sm text-gray-700 mb-1">NIK</label>
        <input type="text" value="<?= htmlspecialchars($nik) ?>" class="w-full border rounded px-3 py-2 bg-gray-100" disabled />
      </div>
      <div>
        <label class="block text-sm text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="nama_guru" value="<?= htmlspecialchars($nama) ?>" class="w-full border rounded px-3 py-2" required />
      </div>
      <div>
        <label class="block text-sm text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="w-full border rounded px-3 py-2" placeholder="opsional" />
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <a href="<?= BASEURL ?>/guru" class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200">Batal</a>
        <button class="px-4 py-2 rounded bg-primary-600 hover:bg-primary-700 text-white">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
