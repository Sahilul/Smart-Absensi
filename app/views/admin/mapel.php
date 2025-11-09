<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Mata Pelajaran</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Mata Pelajaran</h2>
                <p class="text-gray-600 mt-1">Kelola daftar mata pelajaran sekolah</p>
            </div>
            <a href="<?= BASEURL; ?>/admin/tambahMapel" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors duration-200 shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                Tambah Mapel
            </a>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                        <i data-lucide="book-open" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Mata Pelajaran</p>
                        <p class="text-xl font-semibold text-gray-900"><?= count($data['mapel']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                        <i data-lucide="hash" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kode Mapel Aktif</p>
                        <p class="text-xl font-semibold text-gray-900"><?= count($data['mapel']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-2 rounded-lg mr-3">
                        <i data-lucide="library" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kurikulum</p>
                        <p class="text-xl font-semibold text-gray-900">Aktif</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Mata Pelajaran</h3>
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500">
                            Menampilkan <?= count($data['mapel']); ?> mata pelajaran
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <?php if (!empty($data['mapel'])): ?>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode Mapel
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Mata Pelajaran
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($data['mapel'] as $index => $mapel) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-xs font-semibold text-indigo-600"><?= $index + 1; ?></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($mapel['kode_mapel']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= 'ID-' . str_pad($mapel['id_mapel'], 3, '0', STR_PAD_LEFT); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <i data-lucide="book" class="w-4 h-4 text-gray-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($mapel['nama_mapel']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Mata Pelajaran
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="<?= BASEURL; ?>/admin/editMapel/<?= $mapel['id_mapel']; ?>" 
                                       class="text-indigo-600 hover:text-indigo-800 transition-colors duration-150"
                                       title="Edit mata pelajaran">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= BASEURL; ?>/admin/hapusMapel/<?= $mapel['id_mapel']; ?>" 
                                       class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                       title="Hapus mata pelajaran"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran <?= htmlspecialchars($mapel['nama_mapel']); ?>?\n\nSemua data terkait akan ikut terhapus!')">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="max-w-sm mx-auto">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="book-x" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Pelajaran</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan mata pelajaran pertama untuk kurikulum.</p>
                    <a href="<?= BASEURL; ?>/admin/tambahMapel" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                        Tambah Mapel
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>💡 <strong>Tips:</strong> Kode mata pelajaran digunakan untuk identifikasi unik dalam sistem absensi</p>
        </div>
    </main>

    <script>
    // Auto refresh dan inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
    </script>
</body>
</html>