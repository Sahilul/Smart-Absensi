<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="flex items-center mb-6">
        <a href="<?= BASEURL; ?>/admin/tahunPelajaran" class="text-gray-500 hover:text-indigo-600 mr-4">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Tambah Tahun Pelajaran Baru</h2>
    </div>

    <div class="bg-white rounded-xl shadow-md p-8 max-w-lg mx-auto">
        <form action="<?= BASEURL; ?>/admin/prosesTambahTP" method="POST">
            <div class="space-y-6">
                <div>
                    <label for="nama_tp" class="block text-sm font-medium text-gray-700">Nama Tahun Pelajaran (Contoh: 2024/2025)</label>
                    <input type="text" name="nama_tp" id="nama_tp" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="tgl_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" id="tgl_mulai" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="tgl_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" id="tgl_selesai" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= BASEURL; ?>/admin/tahunPelajaran" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Data</button>
            </div>
        </form>
    </div>
</main>
