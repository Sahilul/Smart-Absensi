<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Jurnal per Mata Pelajaran</h2>
        <p class="text-sm text-gray-600 mt-2">
            Menampilkan ringkasan jurnal untuk sesi: <strong><?= $_SESSION['nama_semester_aktif']; ?></strong>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($data['jurnal_per_mapel'])) : ?>
            <div class="col-span-full bg-white rounded-xl shadow-md p-6 text-center">
                <div class="text-gray-400 mb-4">
                    <i data-lucide="book-open" class="w-16 h-16 mx-auto"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-600">Belum Ada Jurnal</h3>
                <p class="text-gray-500 mt-2">Anda belum memiliki jurnal mengajar untuk semester ini.</p>
                <a href="<?= BASEURL; ?>/guru/jurnal" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                    Buat Jurnal Baru
                </a>
            </div>
        <?php else : ?>
            <?php foreach ($data['jurnal_per_mapel'] as $mapel) : ?>
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-indigo-700">
                            <?= htmlspecialchars($mapel['nama_mapel']); ?>
                        </h3>
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            <?= count($mapel['pertemuan']); ?> Pertemuan
                        </span>
                    </div>

                    <!-- Preview 3 pertemuan terakhir -->
                    <div class="space-y-2 mb-4">
                        <?php 
                        $pertemuan_preview = array_slice($mapel['pertemuan'], 0, 3);
                        foreach ($pertemuan_preview as $jurnal) : 
                        ?>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">
                                    Pertemuan <?= $jurnal['pertemuan_ke']; ?>
                                </span>
                                <span class="text-gray-500">
                                    <?= date('d M', strtotime($jurnal['tanggal'])); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($mapel['pertemuan']) > 3) : ?>
                            <div class="text-xs text-gray-400 text-center pt-2">
                                ... dan <?= count($mapel['pertemuan']) - 3; ?> pertemuan lainnya
                            </div>
                        <?php endif; ?>
                    </div>

                    <a href="<?= BASEURL; ?>/guru/detailRiwayat/<?= $mapel['id_mapel_untuk_link']; ?>" 
                       class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-center block transition-colors">
                        Lihat Detail
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="mt-8 flex justify-center">
        <a href="<?= BASEURL; ?>/guru/jurnal" 
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg inline-flex items-center">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Buat Jurnal Baru
        </a>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>