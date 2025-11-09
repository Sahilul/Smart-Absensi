<!-- admin/tahun_pelajaran.php -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tahun Pelajaran</h2>
            <p class="text-gray-600 mt-1">Kelola tahun pelajaran dan semester</p>
        </div>
        <a href="<?= BASEURL; ?>/admin/tambahTP" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors duration-200 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Tambah Tahun Pelajaran
        </a>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Tahun Pelajaran</p>
                    <p class="text-xl font-semibold text-gray-900"><?= count($data['tp']); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Semester Aktif</p>
                    <p class="text-xl font-semibold text-gray-900"><?= $_SESSION['nama_semester_aktif'] ?? 'Belum ada'; ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="bg-orange-100 p-2 rounded-lg mr-3">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Semester</p>
                    <p class="text-xl font-semibold text-gray-900"><?= count($data['tp']) * 2; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <h3 class="text-lg font-semibold text-gray-800">Daftar Tahun Pelajaran</h3>
                <div class="flex items-center space-x-3">
                    <div class="text-sm text-gray-500">
                        Menampilkan <?= count($data['tp']); ?> tahun pelajaran
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <?php if (!empty($data['tp'])): ?>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tahun Pelajaran
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Periode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Semester
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($data['tp'] as $index => $tp): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($tp['nama_tp']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= 'TP-' . str_pad($tp['id_tp'], 3, '0', STR_PAD_LEFT); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center">
                                    <i data-lucide="calendar-days" class="w-4 h-4 text-gray-400 mr-2"></i>
                                    <?= date('d M Y', strtotime($tp['tgl_mulai'])); ?>
                                </div>
                                <div class="flex items-center mt-1">
                                    <i data-lucide="calendar-x" class="w-4 h-4 text-gray-400 mr-2"></i>
                                    <?= date('d M Y', strtotime($tp['tgl_selesai'])); ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $today = date('Y-m-d');
                            $start = $tp['tgl_mulai'];
                            $end = $tp['tgl_selesai'];
                            
                            if ($today < $start) {
                                $status = 'Belum Dimulai';
                                $color = 'bg-gray-100 text-gray-800';
                                $icon = 'clock';
                            } elseif ($today > $end) {
                                $status = 'Selesai';
                                $color = 'bg-red-100 text-red-800';
                                $icon = 'check-circle';
                            } else {
                                $status = 'Aktif';
                                $color = 'bg-green-100 text-green-800';
                                $icon = 'play-circle';
                            }
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color; ?>">
                                <i data-lucide="<?= $icon; ?>" class="w-3 h-3 mr-1"></i>
                                <?= $status; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-1">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i data-lucide="sun" class="w-3 h-3 mr-1"></i>
                                    Ganjil
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    <i data-lucide="moon" class="w-3 h-3 mr-1"></i>
                                    Genap
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <a href="<?= BASEURL; ?>/admin/editTP/<?= $tp['id_tp']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="Edit tahun pelajaran">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= BASEURL; ?>/admin/hapusTP/<?= $tp['id_tp']; ?>" 
                                   class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                   title="Hapus tahun pelajaran"
                                   onclick="return confirm('Yakin hapus tahun pelajaran <?= htmlspecialchars($tp['nama_tp']); ?>?\n\nSemua data semester, kelas, dan penugasan terkait akan ikut terhapus!')">
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
                    <i data-lucide="calendar-off" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Tahun Pelajaran</h3>
                <p class="text-gray-500 mb-6">Mulai dengan menambahkan tahun pelajaran pertama untuk sistem absensi.</p>
                <a href="<?= BASEURL; ?>/admin/tambahTP" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Tambah Tahun Pelajaran
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer Info -->
    <div class="mt-6 text-center text-sm text-gray-500">
        <p>💡 <strong>Tips:</strong> Setiap tahun pelajaran otomatis memiliki 2 semester (Ganjil & Genap)</p>
    </div>
</main>

<script>
// Auto refresh status jika ada perubahan tanggal
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>