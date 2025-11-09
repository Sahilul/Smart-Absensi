<!-- admin/kelas.php - DIPERBAIKI -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Kelas</h2>
            <p class="text-gray-600 mt-1">Kelola kelas untuk sesi: <strong><?= $_SESSION['nama_semester_aktif'] ?? 'Belum ada sesi'; ?></strong></p>
        </div>
        <a href="<?= BASEURL; ?>/admin/tambahKelas" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors duration-200 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Tambah Kelas
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <i data-lucide="school" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Kelas</p>
                    <p class="text-xl font-semibold text-gray-900" id="totalKelas"><?= count($data['kelas']); ?></p>
                    <p class="text-xs text-gray-500">Sesi aktif</p>
                </div>
            </div>
        </div>
        
        <?php 
        // Hitung statistik dari data yang ada
        $total_siswa = 0;
        $total_guru = 0;
        $jenjang_stats = [];
        
        foreach ($data['kelas'] as $kelas) {
            $total_siswa += (int)$kelas['jumlah_siswa'];
            $total_guru += (int)$kelas['jumlah_guru'];
            
            $jenjang = $kelas['jenjang'];
            if (!isset($jenjang_stats[$jenjang])) {
                $jenjang_stats[$jenjang] = 0;
            }
            $jenjang_stats[$jenjang]++;
        }
        ksort($jenjang_stats);
        ?>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Siswa</p>
                    <p class="text-xl font-semibold text-gray-900"><?= $total_siswa; ?></p>
                    <p class="text-xs text-gray-500">Semua kelas</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                    <i data-lucide="user-check" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Guru</p>
                    <p class="text-xl font-semibold text-gray-900"><?= $total_guru; ?></p>
                    <p class="text-xs text-gray-500">Mengajar</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-orange-100 p-2 rounded-lg mr-3">
                    <i data-lucide="layers" class="w-5 h-5 text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jenjang</p>
                    <p class="text-xl font-semibold text-gray-900"><?= count($jenjang_stats); ?></p>
                    <p class="text-xs text-gray-500">Tingkatan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary by Jenjang -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <?php 
        $colors = [
            'VII' => 'bg-green-100 text-green-800 border-green-200',
            'VIII' => 'bg-blue-100 text-blue-800 border-blue-200', 
            'IX' => 'bg-purple-100 text-purple-800 border-purple-200',
            'X' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'XI' => 'bg-orange-100 text-orange-800 border-orange-200',
            'XII' => 'bg-red-100 text-red-800 border-red-200'
        ];
        
        if (empty($jenjang_stats)): ?>
            <div class="col-span-full bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <p class="text-sm text-yellow-800">
                    <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                    Belum ada kelas untuk sesi <strong><?= $_SESSION['nama_semester_aktif'] ?? 'ini'; ?></strong>
                </p>
            </div>
        <?php else:
            foreach ($jenjang_stats as $jenjang => $count): 
                $color_class = $colors[$jenjang] ?? 'bg-gray-100 text-gray-800 border-gray-200';
            ?>
            <div class="bg-white border-2 <?= $color_class; ?> rounded-lg p-3 text-center">
                <div class="text-lg font-bold"><?= $count; ?></div>
                <div class="text-xs font-medium">Kelas <?= $jenjang; ?></div>
            </div>
            <?php endforeach;
        endif; ?>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <input type="text" id="searchKelas" placeholder="Cari kelas..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select id="filterJenjang" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Jenjang</option>
                <?php foreach (array_keys($jenjang_stats) as $jenjang): ?>
                    <option value="<?= $jenjang; ?>"><?= $jenjang; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <h3 class="text-lg font-semibold text-gray-800">Daftar Kelas</h3>
                <div class="text-sm text-gray-500">
                    Menampilkan <span id="shownCount"><?= count($data['kelas']); ?></span> dari <?= count($data['kelas']); ?> kelas
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <?php if (!empty($data['kelas'])): ?>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200" id="kelasTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kelas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jenjang
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Siswa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Guru
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Wali Kelas
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($data['kelas'] as $index => $kelas): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150" data-jenjang="<?= $kelas['jenjang']; ?>" data-nama="<?= strtolower($kelas['nama_kelas']); ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mr-3 <?= $colors[$kelas['jenjang']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                    <span class="text-sm font-bold"><?= $kelas['jenjang']; ?></span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($kelas['nama_kelas']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= htmlspecialchars($kelas['nama_tp']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colors[$kelas['jenjang']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                <i data-lucide="layers" class="w-3 h-3 mr-1"></i>
                                <?= $kelas['jenjang']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i data-lucide="users" class="w-4 h-4 text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-900">
                                    <?= (int)$kelas['jumlah_siswa']; ?> siswa
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <?php if ((int)$kelas['jumlah_siswa'] > 0): ?>
                                    <a href="<?= BASEURL; ?>/admin/keanggotaan" class="text-blue-600 hover:text-blue-800">
                                        Kelola anggota →
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASEURL; ?>/admin/keanggotaan" class="text-orange-600 hover:text-orange-800">
                                        Tambah anggota →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i data-lucide="user-check" class="w-4 h-4 text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-900">
                                    <?= (int)$kelas['jumlah_guru']; ?> guru
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <?php if ((int)$kelas['jumlah_guru'] > 0): ?>
                                    <a href="<?= BASEURL; ?>/admin/penugasan" class="text-green-600 hover:text-green-800">
                                        Lihat penugasan →
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASEURL; ?>/admin/penugasan" class="text-orange-600 hover:text-orange-800">
                                        Buat penugasan →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if (!empty($kelas['nama_guru_walikelas'])): ?>
                                <div class="flex items-center">
                                    <i data-lucide="user-circle" class="w-4 h-4 text-blue-400 mr-2"></i>
                                    <span class="text-sm text-gray-900">
                                        <?= htmlspecialchars($kelas['nama_guru_walikelas']); ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center text-gray-400">
                                    <i data-lucide="user-x" class="w-4 h-4 mr-2"></i>
                                    <span class="text-sm italic">Belum ditentukan</span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                Aktif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <a href="<?= BASEURL; ?>/admin/editKelas/<?= $kelas['id_kelas']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="Edit kelas">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= BASEURL; ?>/admin/keanggotaan" 
                                   class="text-green-600 hover:text-green-800 transition-colors duration-150"
                                   title="Kelola anggota">
                                    <i data-lucide="users-2" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= BASEURL; ?>/admin/hapusKelas/<?= $kelas['id_kelas']; ?>" 
                                   class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                   title="Hapus kelas"
                                   onclick="return confirm('⚠️ PERINGATAN!\n\nMenghapus kelas <?= htmlspecialchars($kelas['nama_kelas']); ?> akan:\n• Menghapus semua anggota kelas\n• Menghapus data penugasan guru\n• Menghapus jurnal dan absensi terkait\n\nTindakan ini tidak dapat dibatalkan!\n\nYakin ingin melanjutkan?')">
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
                <div class="mx-auto w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="school-2" class="w-8 h-8 text-yellow-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Kelas</h3>
                <p class="text-gray-500 mb-2">
                    Belum ada kelas untuk sesi <strong><?= $_SESSION['nama_semester_aktif'] ?? 'ini'; ?></strong>
                </p>
                <p class="text-sm text-gray-400 mb-6">
                    Mulai dengan membuat kelas untuk tahun pelajaran yang sedang aktif.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="<?= BASEURL; ?>/admin/tambahKelas" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Tambah Kelas
                    </a>
                    <a href="<?= BASEURL; ?>/admin/tahunPelajaran" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i data-lucide="calendar-days" class="w-4 h-4 mr-2"></i>
                        Ganti Sesi
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer Info -->
    <div class="mt-6 text-center text-sm text-gray-600">
        <p>💡 <strong>Tips:</strong> Data hanya menampilkan kelas untuk sesi yang sedang aktif</p>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Search and Filter functionality
    const searchInput = document.getElementById('searchKelas');
    const filterJenjang = document.getElementById('filterJenjang');
    const table = document.getElementById('kelasTable');
    const rows = table ? table.querySelectorAll('tbody tr') : [];
    const shownCount = document.getElementById('shownCount');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedJenjang = filterJenjang.value;
        let visibleCount = 0;

        rows.forEach(row => {
            const namaKelas = row.dataset.nama;
            const jenjang = row.dataset.jenjang;

            const matchSearch = !searchTerm || namaKelas.includes(searchTerm);
            const matchJenjang = !selectedJenjang || jenjang === selectedJenjang;

            if (matchSearch && matchJenjang) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (shownCount) {
            shownCount.textContent = visibleCount;
        }
    }

    // Event listeners
    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (filterJenjang) filterJenjang.addEventListener('change', filterTable);
});
</script>