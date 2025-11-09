<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="flex items-center mb-6">
        <a href="<?= BASEURL; ?>/admin/guru" class="text-gray-500 hover:text-indigo-600 mr-4">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Tambah Data Guru Baru</h2>
    </div>

    <div class="bg-white rounded-xl shadow-md p-8 max-w-lg mx-auto">
        <form action="<?= BASEURL; ?>/admin/prosesTambahGuru" method="POST">
            <div class="space-y-6">
                <div>
                    <label for="nama_guru" class="block text-sm font-medium text-gray-700">Nama Lengkap Guru</label>
                    <input type="text" name="nama_guru" id="nama_guru" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK (akan menjadi Username)</label>
                    <input type="text" name="nik" id="nik" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email (Opsional)</label>
                    <input type="email" name="email" id="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="border-t pt-6">
                    <p class="text-lg font-semibold text-gray-700">Informasi Akun Login</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= BASEURL; ?>/admin/guru" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg">Batal</a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Data</button>
            </div>
        </form>
    </div>
</main>