<main class="flex-1 overflow-x-hidden overflow-y-auto bg-secondary-50 p-4 md:p-6">
    <!-- Header -->
    <div class="mb-4">
        <div class="bg-white shadow-sm rounded-xl p-4 md:p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h4 class="text-lg md:text-xl font-bold text-slate-800 mb-1">Pengaturan Rapor</h4>
                    <p class="text-slate-500 text-sm">
                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '-') ?></span>
                        <span class="mx-2">•</span>
                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($data['session_info']['nama_semester']) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <!-- Form Pengaturan -->
    <div class="bg-white shadow-sm rounded-xl p-5 md:p-6">
        <form action="<?= BASEURL ?>/waliKelas/simpanPengaturanRapor" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Kop Rapor (Upload Gambar) -->
                <div class="md:col-span-2">
                    <label for="kop_rapor" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="image" class="w-4 h-4 inline-block mr-1"></i>
                        Kop Rapor (Gambar)
                    </label>
                    
                    <?php if (!empty($data['pengaturan']['kop_rapor'])): ?>
                    <div class="mb-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <img src="<?= BASEURL ?>/public/img/kop/<?= htmlspecialchars($data['pengaturan']['kop_rapor']) ?>" 
                             alt="Kop Rapor" 
                             class="w-full h-auto max-h-32 sm:max-h-48 object-contain mx-auto">
                        <p class="text-xs text-slate-500 mt-2 text-center break-all"><?= htmlspecialchars($data['pengaturan']['kop_rapor']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <input 
                        type="file" 
                        id="kop_rapor" 
                        name="kop_rapor" 
                        accept="image/*"
                        class="w-full border border-slate-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                    >
                    <p class="text-xs text-slate-500 mt-1">Upload gambar kop rapor (JPG, PNG, max 2MB)</p>
                </div>

                <!-- Nama Madrasah -->
                <div class="md:col-span-2">
                    <label for="nama_madrasah" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="school" class="w-4 h-4 inline-block mr-1"></i>
                        Nama Madrasah
                    </label>
                    <input 
                        type="text" 
                        id="nama_madrasah" 
                        name="nama_madrasah" 
                        value="<?= htmlspecialchars($data['pengaturan']['nama_madrasah'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                        placeholder="Contoh: MTsN 1 Kota Malang"
                    >
                </div>

                <!-- Tempat Cetak -->
                <div>
                    <label for="tempat_cetak" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="map-pin" class="w-4 h-4 inline-block mr-1"></i>
                        Tempat Cetak
                    </label>
                    <input 
                        type="text" 
                        id="tempat_cetak" 
                        name="tempat_cetak" 
                        value="<?= htmlspecialchars($data['pengaturan']['tempat_cetak'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                        placeholder="Contoh: Mojokerto"
                    >
                </div>

                <!-- Tanggal Cetak -->
                <div>
                    <label for="tanggal_cetak" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1"></i>
                        Tanggal Cetak
                    </label>
                    <input 
                        type="date" 
                        id="tanggal_cetak" 
                        name="tanggal_cetak" 
                        value="<?= $data['pengaturan']['tanggal_cetak'] ?? date('Y-m-d') ?>"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                    >
                </div>

                <!-- Nama Kepala Madrasah -->
                <div>
                    <label for="nama_kepala_madrasah" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="user" class="w-4 h-4 inline-block mr-1"></i>
                        Nama Kepala Madrasah
                    </label>
                    <input 
                        type="text" 
                        id="nama_kepala_madrasah" 
                        name="nama_kepala_madrasah" 
                        value="<?= htmlspecialchars($data['pengaturan']['nama_kepala_madrasah'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                        placeholder="Contoh: Drs. Ahmad Fauzi, M.Pd"
                    >
                </div>

                <!-- TTD Kepala Madrasah -->
                <div class="md:col-span-2">
                    <label for="ttd_kepala_madrasah" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="pen-tool" class="w-4 h-4 inline-block mr-1"></i>
                        Tanda Tangan Kepala Madrasah
                    </label>
                    
                    <?php if (!empty($data['pengaturan']['ttd_kepala_madrasah'])): ?>
                    <div class="mb-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <img src="<?= BASEURL ?>/public/img/ttd/<?= htmlspecialchars($data['pengaturan']['ttd_kepala_madrasah']) ?>" 
                             alt="TTD Kepala Madrasah" 
                             class="max-w-full h-auto max-h-32 mx-auto">
                        <p class="text-xs text-slate-500 mt-2 text-center">File: <?= htmlspecialchars($data['pengaturan']['ttd_kepala_madrasah']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <input 
                        type="file" 
                        id="ttd_kepala_madrasah" 
                        name="ttd_kepala_madrasah" 
                        accept="image/*"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                    >
                    <p class="text-xs text-slate-500 mt-1">Upload tanda tangan kepala madrasah (PNG dengan background transparan direkomendasikan, max 1MB)</p>
                </div>

                <!-- TTD Wali Kelas -->
                <div class="md:col-span-2">
                    <label for="ttd_wali_kelas" class="block text-sm font-semibold text-slate-700 mb-2">
                        <i data-lucide="pen-tool" class="w-4 h-4 inline-block mr-1"></i>
                        Tanda Tangan Wali Kelas
                    </label>
                    
                    <?php if (!empty($data['pengaturan']['ttd_wali_kelas'])): ?>
                    <div class="mb-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <img src="<?= BASEURL ?>/public/img/ttd/<?= htmlspecialchars($data['pengaturan']['ttd_wali_kelas']) ?>" 
                             alt="TTD Wali Kelas" 
                             class="max-w-full h-auto max-h-32 mx-auto">
                        <p class="text-xs text-slate-500 mt-2 text-center">File: <?= htmlspecialchars($data['pengaturan']['ttd_wali_kelas']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <input 
                        type="file" 
                        id="ttd_wali_kelas" 
                        name="ttd_wali_kelas" 
                        accept="image/*"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                    >
                    <p class="text-xs text-slate-500 mt-1">Upload tanda tangan wali kelas (PNG dengan background transparan direkomendasikan, max 1MB)</p>
                </div>

                <!-- Persentase Nilai Rapor STS -->
                <div class="md:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 mb-3 pb-2 border-b border-slate-200">
                        <i data-lucide="percent" class="w-4 h-4 inline-block mr-1"></i>
                        Persentase Nilai Rapor STS
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="persen_harian_sts" class="block text-sm font-semibold text-slate-700 mb-2">
                                Persentase Nilai Harian
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    id="persen_harian_sts" 
                                    name="persen_harian_sts" 
                                    value="<?= $data['pengaturan']['persen_harian_sts'] ?? 60 ?>"
                                    min="0" max="100"
                                    class="w-20 sm:w-24 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                                >
                                <span class="text-sm text-slate-600">%</span>
                            </div>
                        </div>
                        <div>
                            <label for="persen_sts" class="block text-sm font-semibold text-slate-700 mb-2">
                                Persentase Nilai STS
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    id="persen_sts" 
                                    name="persen_sts" 
                                    value="<?= $data['pengaturan']['persen_sts'] ?? 40 ?>"
                                    min="0" max="100"
                                    class="w-20 sm:w-24 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                                >
                                <span class="text-sm text-slate-600">%</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Total harus 100%. Contoh: Harian 60% + STS 40%</p>
                </div>

                <!-- Persentase Nilai Rapor SAS -->
                <div class="md:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 mb-3 pb-2 border-b border-slate-200">
                        <i data-lucide="percent" class="w-4 h-4 inline-block mr-1"></i>
                        Persentase Nilai Rapor SAS
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="persen_harian_sas" class="block text-sm font-semibold text-slate-700 mb-2">
                                Persentase Nilai Harian
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    id="persen_harian_sas" 
                                    name="persen_harian_sas" 
                                    value="<?= $data['pengaturan']['persen_harian_sas'] ?? 40 ?>"
                                    min="0" max="100"
                                    class="w-20 sm:w-24 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                                >
                                <span class="text-sm text-slate-600">%</span>
                            </div>
                        </div>
                        <div>
                            <label for="persen_sts_sas" class="block text-sm font-semibold text-slate-700 mb-2">
                                Persentase Nilai STS
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    id="persen_sts_sas" 
                                    name="persen_sts_sas" 
                                    value="<?= $data['pengaturan']['persen_sts_sas'] ?? 30 ?>"
                                    min="0" max="100"
                                    class="w-20 sm:w-24 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                                >
                                <span class="text-sm text-slate-600">%</span>
                            </div>
                        </div>
                        <div>
                            <label for="persen_sas" class="block text-sm font-semibold text-slate-700 mb-2">
                                Persentase Nilai SAS
                            </label>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    id="persen_sas" 
                                    name="persen_sas" 
                                    value="<?= $data['pengaturan']['persen_sas'] ?? 30 ?>"
                                    min="0" max="100"
                                    class="w-20 sm:w-24 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-200 focus:border-sky-400 transition-all"
                                >
                                <span class="text-sm text-slate-600">%</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Total harus 100%. Contoh: Harian 40% + STS 30% + SAS 30%</p>
                </div>

                <!-- Pilih Mapel yang Ditampilkan -->
                <div class="md:col-span-2">
                    <h3 class="text-sm font-bold text-slate-800 mb-3 pb-2 border-b border-slate-200">
                        <i data-lucide="book-open" class="w-4 h-4 inline-block mr-1"></i>
                        Mata Pelajaran yang Ditampilkan di Rapor
                    </h3>
                    <p class="text-xs text-slate-500 mb-3">Pilih mata pelajaran yang akan ditampilkan dalam rapor</p>
                    
                    <?php 
                    $selectedMapel = !empty($data['pengaturan']['mapel_rapor']) ? json_decode($data['pengaturan']['mapel_rapor'], true) : [];
                    if (!is_array($selectedMapel)) $selectedMapel = [];
                    ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php if (!empty($data['mapel_list'])): ?>
                            <?php foreach ($data['mapel_list'] as $mapel): ?>
                                <label class="flex items-center gap-2 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors">
                                    <input 
                                        type="checkbox" 
                                        name="mapel_rapor[]" 
                                        value="<?= $mapel['id_mapel'] ?>"
                                        <?= in_array($mapel['id_mapel'], $selectedMapel) ? 'checked' : '' ?>
                                        class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-200"
                                    >
                                    <span class="text-sm text-slate-700"><?= htmlspecialchars($mapel['nama_mapel']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm text-slate-500 col-span-full">Tidak ada mata pelajaran tersedia</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <button 
                    type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Pengaturan
                </button>
                <a 
                    href="<?= BASEURL ?>/waliKelas/dashboard" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg transition-colors"
                >
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-5 bg-sky-50 border border-sky-200 rounded-xl p-4">
        <div class="flex gap-3">
            <div class="shrink-0">
                <i data-lucide="info" class="w-5 h-5 text-sky-600"></i>
            </div>
            <div class="flex-1">
                <h6 class="font-semibold text-sky-900 text-sm mb-1">Informasi</h6>
                <ul class="text-xs text-sky-700 space-y-1 list-disc list-inside">
                    <li>Pengaturan ini akan digunakan untuk mencetak rapor STS dan SAS</li>
                    <li>Setiap wali kelas dapat memiliki pengaturan yang berbeda</li>
                    <li>Pastikan semua data sudah diisi dengan benar sebelum mencetak rapor</li>
                    <li>Kop rapor mendukung format multi-baris (tekan Enter untuk baris baru)</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
