<?php
// View: app/views/guru/ganti_sandi.php (Ganti Password Only)
?>
<div class="p-4 sm:p-6">
	<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-5">
		<h1 class="text-xl font-semibold mb-1">Ganti Sandi</h1>
		<p class="text-gray-500 text-sm mb-4">Masukkan sandi baru Anda. Username tidak dapat diubah di sini.</p>

		<form action="<?= BASEURL ?>/guru/simpanSandi" method="POST" class="space-y-4">
			<div class="grid sm:grid-cols-2 gap-3">
				<div>
					<label class="block text-sm text-gray-700 mb-1">Sandi Baru</label>
					<input type="password" name="password" class="w-full border rounded px-3 py-2" required />
				</div>
				<div>
					<label class="block text-sm text-gray-700 mb-1">Ulangi Sandi</label>
					<input type="password" name="password2" class="w-full border rounded px-3 py-2" required />
				</div>
			</div>

			<div class="flex items-center justify-end gap-2 pt-2">
				<a href="<?= BASEURL ?>/guru" class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200">Batal</a>
				<button class="px-4 py-2 rounded bg-primary-600 hover:bg-primary-700 text-white">Simpan Sandi</button>
			</div>
		</form>
	</div>
	<div class="max-w-xl mx-auto text-xs text-gray-500 mt-3">
		Catatan: Untuk mengganti username atau identitas lain, gunakan menu Profil.
	</div>
</div>