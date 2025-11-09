<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Siswa</title>
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
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Data Siswa</h2>
                <p class="text-gray-600 mt-1">Kelola data dan akun siswa</p>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="<?= BASEURL; ?>/admin/importSiswa" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200 shadow-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
                    Import Excel
                </a>
                <a href="<?= BASEURL; ?>/admin/tambahSiswa" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200 shadow-sm">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                    Tambah Siswa
                </a>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                        <i data-lucide="users" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Siswa</p>
                        <p class="text-xl font-semibold text-gray-900"><?= count($data['siswa']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                        <i data-lucide="shield-check" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Akun Aktif</p>
                        <p class="text-xl font-semibold text-gray-900"><?= count(array_filter($data['siswa'], function($s) { return !empty($s['password_plain']); })); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-2 rounded-lg mr-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Belum Ada Password</p>
                        <p class="text-xl font-semibold text-gray-900"><?= count(array_filter($data['siswa'], function($s) { return empty($s['password_plain']); })); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash'])): ?>
        <div class="mb-6">
            <div class="bg-<?= $_SESSION['flash']['type'] === 'success' ? 'green' : 'red'; ?>-50 border border-<?= $_SESSION['flash']['type'] === 'success' ? 'green' : 'red'; ?>-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i data-lucide="<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'alert-circle'; ?>" 
                       class="w-5 h-5 text-<?= $_SESSION['flash']['type'] === 'success' ? 'green' : 'red'; ?>-500 mr-3"></i>
                    <p class="font-medium text-<?= $_SESSION['flash']['type'] === 'success' ? 'green' : 'red'; ?>-800">
                        <?= $_SESSION['flash']['message']; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Siswa</h3>
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500">
                            Menampilkan <?= count($data['siswa']); ?> siswa
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="exportToExcel()" 
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md text-sm font-medium transition-colors flex items-center">
                                <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                                Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <?php if (!empty($data['siswa'])): ?>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" id="siswa-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NISN
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Siswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Kelamin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status Akun
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($data['siswa'] as $index => $siswa) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-xs font-semibold text-indigo-600"><?= $index + 1; ?></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($siswa['nisn']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= 'ID-' . str_pad($siswa['id_siswa'], 4, '0', STR_PAD_LEFT); ?>
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
                                            <?= htmlspecialchars($siswa['nama_siswa']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Siswa
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php if ($siswa['jenis_kelamin'] === 'L'): ?>
                                        <div class="bg-blue-100 p-1.5 rounded-lg mr-2">
                                            <i data-lucide="user" class="w-3 h-3 text-blue-600"></i>
                                        </div>
                                        <span class="text-sm text-gray-900">Laki-laki</span>
                                    <?php elseif ($siswa['jenis_kelamin'] === 'P'): ?>
                                        <div class="bg-pink-100 p-1.5 rounded-lg mr-2">
                                            <i data-lucide="user" class="w-3 h-3 text-pink-600"></i>
                                        </div>
                                        <span class="text-sm text-gray-900">Perempuan</span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if (!empty($siswa['password_plain'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Akun Aktif
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Password: <span class="font-mono bg-gray-100 px-1 rounded"><?= htmlspecialchars($siswa['password_plain']); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Belum Ada Password
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="<?= BASEURL; ?>/admin/editSiswa/<?= $siswa['id_siswa']; ?>" 
                                       class="text-indigo-600 hover:text-indigo-800 transition-colors duration-150"
                                       title="Edit data siswa">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= BASEURL; ?>/admin/hapusSiswa/<?= $siswa['id_siswa']; ?>" 
                                       class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                       title="Hapus data siswa"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa <?= htmlspecialchars($siswa['nama_siswa']); ?>?\n\nSemua data terkait akan ikut terhapus!')">
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
                        <i data-lucide="users-x" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Siswa</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan data siswa atau import dari Excel.</p>
                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="<?= BASEURL; ?>/admin/importSiswa" 
                           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
                            Import Excel
                        </a>
                        <a href="<?= BASEURL; ?>/admin/tambahSiswa" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                            Tambah Manual
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p class="flex items-center justify-center">
                <i data-lucide="lightbulb" class="w-4 h-4 mr-2"></i>
                <strong>Tips:</strong> Gunakan fitur Import Excel untuk menambahkan banyak siswa sekaligus, atau tambah manual untuk data individual
            </p>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
    // Auto refresh dan inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Export to Excel function
    function exportToExcel() {
        const table = document.getElementById('siswa-table');
        if (!table) {
            alert('Tidak ada data untuk diexport');
            return;
        }
        
        // Prepare data for export
        const exportData = [];
        
        // Add header
        exportData.push(['NISN', 'Nama Siswa', 'Jenis Kelamin', 'Status Akun', 'Password']);
        
        // Add data rows
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 4) {
                const nisn = cells[0].querySelector('.text-sm.font-medium').textContent.trim();
                const nama = cells[1].querySelector('.text-sm.font-medium').textContent.trim();
                const jk = cells[2].textContent.includes('Laki-laki') ? 'L' : 
                           cells[2].textContent.includes('Perempuan') ? 'P' : '-';
                const status = cells[3].textContent.includes('Aktif') ? 'Aktif' : 'Belum Ada Password';
                const password = cells[3].querySelector('.font-mono') ? 
                               cells[3].querySelector('.font-mono').textContent.trim() : '-';
                
                exportData.push([nisn, nama, jk, status, password]);
            }
        });
        
        // Create workbook
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(exportData);
        
        // Style header
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let col = range.s.c; col <= range.e.c; col++) {
            const cellAddress = XLSX.utils.encode_cell({ r: 0, c: col });
            if (!ws[cellAddress]) continue;
            ws[cellAddress].s = {
                font: { bold: true },
                fill: { fgColor: { rgb: "E5E7EB" } }
            };
        }
        
        // Set column widths
        ws['!cols'] = [
            { width: 15 }, // NISN
            { width: 25 }, // Nama
            { width: 15 }, // JK
            { width: 18 }, // Status
            { width: 15 }  // Password
        ];
        
        XLSX.utils.book_append_sheet(wb, ws, "Data Siswa");
        
        // Download
        const filename = `Data_Siswa_${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, filename);
    }
    </script>
</body>
</html>