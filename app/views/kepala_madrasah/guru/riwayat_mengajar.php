<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6">
        <a href="<?= BASEURL; ?>/guru/riwayatJurnal" class="flex items-center text-sm font-semibold text-gray-600 hover:text-indigo-600 transition mb-2">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Ringkasan Mapel
        </a>
        <h2 class="text-2xl font-bold text-gray-800">
            Detail Riwayat: <?= htmlspecialchars($data['nama_mapel']); ?>
        </h2>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="mb-4">
            <span class="font-semibold text-gray-700">
                Menampilkan riwayat untuk sesi: <?= $_SESSION['nama_semester_aktif']; ?>
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Topik Materi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($data['detail_jurnal'])) : ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Belum ada riwayat jurnal untuk mata pelajaran ini.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($data['detail_jurnal'] as $jurnal) : ?>
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <?= date('d M Y', strtotime($jurnal['tanggal'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <?= htmlspecialchars($jurnal['nama_kelas']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($jurnal['topik_materi']); ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="<?= BASEURL; ?>/guru/editJurnal/<?= $jurnal['id_jurnal']; ?>" 
                                           class="p-2 rounded-full bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition" 
                                           title="Edit Jurnal">
                                            <i data-lucide="file-pen-line" class="w-5 h-5"></i>
                                        </a>

                                        <a href="<?= BASEURL; ?>/guru/absensi/<?= $jurnal['id_jurnal']; ?>" 
                                           class="p-2 rounded-full bg-green-100 text-green-600 hover:bg-green-200 transition" 
                                           title="Input Absensi">
                                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                                        </a>

                                        <a href="<?= BASEURL; ?>/guru/cetakAbsensi/<?= $jurnal['id_jurnal']; ?>" target="_blank" 
                                           class="p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition" 
                                           title="Cetak Laporan">
                                            <i data-lucide="printer" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>