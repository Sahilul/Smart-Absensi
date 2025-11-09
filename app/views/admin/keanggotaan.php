<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengelolaan Anggota Kelas</title>
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
                <h2 class="text-2xl font-bold text-gray-800">Pengelolaan Anggota Kelas (Rombel)</h2>
                <p class="text-gray-600 mt-1">Kelola anggota rombongan belajar</p>
            </div>
        </div>

        <!-- Info Section -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <span class="font-medium">Pilih Kelas untuk Dikelola</span><br>
                        Data anggota kelas yang ditampilkan sesuai dengan sesi aktif: <strong><?= $_SESSION['nama_semester_aktif']; ?></strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Class Selection Form -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Pilih Kelas</h3>
            </div>
            <div class="p-6">
                <form action="<?= BASEURL; ?>/admin/keanggotaan" method="POST">
                    <div class="flex flex-col sm:flex-row items-start sm:items-end space-y-3 sm:space-y-0 sm:space-x-3">
                        <div class="flex-1 w-full sm:w-auto">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                            <select name="id_kelas" class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($data['daftar_kelas'] as $kelas) : ?>
                                    <option value="<?= $kelas['id_kelas']; ?>" <?= (isset($data['kelas_terpilih']) && $data['kelas_terpilih']['id_kelas'] == $kelas['id_kelas']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($kelas['nama_kelas']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors duration-200 shadow-sm">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($data['anggota_kelas'])) : ?>
        <!-- Student List Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Daftar Siswa Kelas: <?= htmlspecialchars($data['kelas_terpilih']['nama_kelas']); ?>
                    </h3>
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500">
                            Menampilkan <?= count($data['anggota_kelas']); ?> siswa
                        </div>
                        <a href="<?= BASEURL; ?>/admin/tambahAnggota/<?= $data['kelas_terpilih']['id_kelas']; ?>" 
                           class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors duration-200 shadow-sm">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                            Tambah Anggota
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <?php if (!empty($data['anggota_kelas'])): ?>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NISN
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Siswa
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($data['anggota_kelas'] as $index => $anggota) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-xs font-semibold text-blue-600"><?= $index + 1; ?></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($anggota['nisn']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                        <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($anggota['nama_siswa']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Siswa Aktif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="<?= BASEURL; ?>/admin/hapusAnggota/<?= $anggota['id_keanggotaan']; ?>/<?= $data['kelas_terpilih']['id_kelas']; ?>" 
                                       class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                       title="Keluarkan siswa dari kelas"
                                       onclick="return confirm('Keluarkan siswa ini dari kelas?')">
                                        <i data-lucide="user-minus" class="w-4 h-4"></i>
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
                        <i data-lucide="users-x" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Kelas Belum Memiliki Anggota</h3>
                    <p class="text-gray-500 mb-6">Tambahkan siswa untuk mulai mengelola anggota kelas ini.</p>
                    <a href="<?= BASEURL; ?>/admin/tambahAnggota/<?= $data['kelas_terpilih']['id_kelas']; ?>" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                        Tambah Anggota
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($data['anggota_kelas'])): ?>
        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Anggota</p>
                        <p class="text-xl font-semibold text-gray-900"><?= count($data['anggota_kelas']); ?> Siswa</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kelas</p>
                        <p class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($data['kelas_terpilih']['nama_kelas']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-2 rounded-lg mr-3">
                        <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Semester</p>
                        <p class="text-xl font-semibold text-gray-900"><?= $_SESSION['nama_semester_aktif']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>💡 <strong>Tips:</strong> Gunakan fitur "Tambah Anggota" untuk mengelola siswa dalam kelas</p>
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