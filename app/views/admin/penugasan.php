<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penugasan Guru Mengajar</title>
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
                <h2 class="text-2xl font-bold text-gray-800">Penugasan Guru Mengajar</h2>
                <p class="text-gray-600 mt-1">Kelola penugasan guru untuk mata pelajaran dan kelas</p>
            </div>
            <a href="<?= BASEURL; ?>/admin/tambahPenugasan" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors duration-200 shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                Tambah Penugasan
            </a>
        </div>

        <!-- Active Semester Info -->
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5 text-indigo-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-indigo-700">
                        <span class="font-medium">Semester Aktif:</span> 
                        <?= $_SESSION['nama_semester_aktif']; ?><br>
                        <span class="text-xs">Data penugasan yang ditampilkan sesuai dengan semester yang sedang aktif.</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white p-4 rounded-lg shadow-sm border mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="filter_kelas" class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas</label>
                    <select id="filter_kelas" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Kelas</option>
                        <?php
                        // Ambil kelas unik dari data penugasan
                        $kelasUnik = array_unique(array_column($data['penugasan'], 'nama_kelas'));
                        // Fungsi untuk mengurutkan kelas dengan angka romawi
                        function kelasSort($a, $b) {
                            $romawi = [
                                'X' => 10, 'XI' => 11, 'XII' => 12,
                                'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5,
                                'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9
                            ];
                            // Coba urutkan berdasarkan angka romawi terlebih dahulu
                            $aNum = $romawi[strtoupper($a)] ?? 999;
                            $bNum = $romawi[strtoupper($b)] ?? 999;
                            if ($aNum !== $bNum) {
                                return $aNum - $bNum;
                            }
                            // Jika angka romawi sama, urutkan secara alfanumerik
                            return strcasecmp($a, $b);
                        }
                        usort($kelasUnik, 'kelasSort');
                        foreach ($kelasUnik as $kelas) : ?>
                            <option value="<?= htmlspecialchars($kelas); ?>"><?= htmlspecialchars($kelas); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filter_mapel" class="block text-sm font-medium text-gray-700 mb-1">Filter Mata Pelajaran</label>
                    <select id="filter_mapel" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Mata Pelajaran</option>
                        <?php
                        $mapelUnik = array_unique(array_column($data['penugasan'], 'nama_mapel'));
                        sort($mapelUnik);
                        foreach ($mapelUnik as $mapel) : ?>
                            <option value="<?= htmlspecialchars($mapel); ?>"><?= htmlspecialchars($mapel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                        <i data-lucide="user-check" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Penugasan</p>
                        <p class="text-xl font-semibold text-gray-900" id="total-penugasan"><?= count($data['penugasan']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Guru Aktif</p>
                        <p class="text-xl font-semibold text-gray-900" id="total-guru"><?= count(array_unique(array_column($data['penugasan'], 'nama_guru'))); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Mata Pelajaran</p>
                        <p class="text-xl font-semibold text-gray-900" id="total-mapel"><?= count(array_unique(array_column($data['penugasan'], 'nama_mapel'))); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-2 rounded-lg mr-3">
                        <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kelas Terlayani</p>
                        <p class="text-xl font-semibold text-gray-900" id="total-kelas"><?= count(array_unique(array_column($data['penugasan'], 'nama_kelas'))); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Penugasan Mengajar</h3>
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500">
                            Menampilkan <span id="jumlah-data"><?= count($data['penugasan']); ?></span> penugasan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" id="tabel-penugasan">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Guru
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mata Pelajaran
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kelas
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($data['penugasan'] as $index => $tugas) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150 row-data" 
                            data-kelas="<?= htmlspecialchars($tugas['nama_kelas']); ?>" 
                            data-mapel="<?= htmlspecialchars($tugas['nama_mapel']); ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-xs font-semibold text-indigo-600"><?= $index + 1; ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="graduation-cap" class="w-4 h-4 text-gray-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($tugas['nama_guru']); ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Guru Pengampu
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i data-lucide="book" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($tugas['nama_mapel']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Mata Pelajaran
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($tugas['nama_kelas']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Kelas
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="<?= BASEURL; ?>/admin/editPenugasan/<?= $tugas['id_penugasan']; ?>" 
                                       class="text-indigo-600 hover:text-indigo-800 transition-colors duration-150"
                                       title="Edit penugasan">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= BASEURL; ?>/admin/hapusPenugasan/<?= $tugas['id_penugasan']; ?>" 
                                       class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                       title="Hapus penugasan"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus penugasan <?= htmlspecialchars($tugas['nama_guru']); ?> mengajar <?= htmlspecialchars($tugas['nama_mapel']); ?> di kelas <?= htmlspecialchars($tugas['nama_kelas']); ?>?')">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($data['penugasan'])): ?>
            <!-- Empty State -->
            <div id="empty-state" class="text-center py-12">
                <div class="max-w-sm mx-auto">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="user-x" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data Penugasan</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menugaskan guru untuk mengajar mata pelajaran di kelas tertentu.</p>
                    <a href="<?= BASEURL; ?>/admin/tambahPenugasan" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i>
                        Tambah Penugasan
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div id="empty-state" class="text-center py-12 hidden">
                <div class="max-w-sm mx-auto">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="filter-x" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data yang Cocok</h3>
                    <p class="text-gray-500 mb-6">Coba ubah filter untuk menampilkan data.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>💡 <strong>Tips:</strong> Penugasan guru menentukan siapa yang bisa melakukan absensi untuk mata pelajaran tertentu</p>
        </div>
    </main>

    <script>
    // Auto refresh dan inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const filterKelas = document.getElementById('filter_kelas');
        const filterMapel = document.getElementById('filter_mapel');
        const rows = document.querySelectorAll('.row-data');
        const jumlahDataEl = document.getElementById('jumlah-data');
        const totalPenugasanEl = document.getElementById('total-penugasan');
        const totalGuruEl = document.getElementById('total-guru');
        const totalMapelEl = document.getElementById('total-mapel');
        const totalKelasEl = document.getElementById('total-kelas');
        const emptyState = document.getElementById('empty-state');
        const tabel = document.getElementById('tabel-penugasan');

        // Ambil data asli dari PHP untuk perhitungan dinamis
        const semuaData = [
            <?php foreach ($data['penugasan'] as $tugas): ?>
                {
                    id: <?= $tugas['id_penugasan']; ?>,
                    nama_guru: "<?= addslashes(htmlspecialchars($tugas['nama_guru'])); ?>",
                    nama_mapel: "<?= addslashes(htmlspecialchars($tugas['nama_mapel'])); ?>",
                    nama_kelas: "<?= addslashes(htmlspecialchars($tugas['nama_kelas'])); ?>"
                },
            <?php endforeach; ?>
        ];

        // Fungsi untuk menghitung ulang statistik berdasarkan data yang ditampilkan
        function hitungStatistik(data) {
            const guruUnik = [...new Set(data.map(d => d.nama_guru))];
            const mapelUnik = [...new Set(data.map(d => d.nama_mapel))];
            const kelasUnik = [...new Set(data.map(d => d.nama_kelas))];
            totalGuruEl.textContent = guruUnik.length;
            totalMapelEl.textContent = mapelUnik.length;
            totalKelasEl.textContent = kelasUnik.length;
        }

        // Fungsi filter
        function applyFilter() {
            let count = 0;
            const kelasValue = filterKelas.value;
            const mapelValue = filterMapel.value;

            rows.forEach(row => {
                const kelas = row.getAttribute('data-kelas');
                const mapel = row.getAttribute('data-mapel');
                const showKelas = kelasValue === '' || kelas === kelasValue;
                const showMapel = mapelValue === '' || mapel === mapelValue;

                if (showKelas && showMapel) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update jumlah data
            jumlahDataEl.textContent = count;
            totalPenugasanEl.textContent = count;

            // Filter data untuk perhitungan statistik
            const filteredData = semuaData.filter(d => {
                const showKelas = kelasValue === '' || d.nama_kelas === kelasValue;
                const showMapel = mapelValue === '' || d.nama_mapel === mapelValue;
                return showKelas && showMapel;
            });

            hitungStatistik(filteredData);

            // Tampilkan/hide empty state
            if (count === 0) {
                emptyState.classList.remove('hidden');
                tabel.style.display = 'none';
            } else {
                emptyState.classList.add('hidden');
                tabel.style.display = '';
            }
        }

        // Tambahkan event listener ke filter
        filterKelas.addEventListener('change', applyFilter);
        filterMapel.addEventListener('change', applyFilter);

        // Jalankan filter pertama kali
        applyFilter();
    });
    </script>
</body>
</html>