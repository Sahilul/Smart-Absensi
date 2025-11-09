<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Proses Kelulusan Siswa</h2>
    </div>

    <?php Flasher::flash(); ?>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="mb-6 border-b pb-6">
            <p class="font-semibold text-gray-700">Pilih Kelas</p>
            <p class="text-sm text-gray-500">Pilih tahun pelajaran dan kelas untuk menampilkan daftar siswa yang akan diluluskan.</p>
            
            <!-- Form untuk menampilkan siswa -->
            <form action="<?= BASEURL; ?>/admin/kelulusan" method="POST" class="mt-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                        <select name="id_tp" id="tp_kelulusan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($data['daftar_tp'] as $tp) : ?>
                                <option value="<?= $tp['id_tp']; ?>" <?= (isset($data['id_tp_pilihan']) && $data['id_tp_pilihan'] == $tp['id_tp']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($tp['nama_tp']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kelas</label>
                        <select name="id_kelas" id="kelas_kelulusan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                            <option value="">-- Pilih TP Dulu --</option>
                            <!-- Opsi kelas akan dimuat oleh JavaScript -->
                        </select>
                    </div>
                    <button type="submit" name="tampilkan_siswa" value="true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Tampilkan Siswa
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($data['daftar_siswa'])) : ?>
        <form action="<?= BASEURL; ?>/admin/prosesKelulusan" method="POST">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Siswa di Kelas Terpilih</h3>
            <div class="overflow-auto h-96 border rounded-lg">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="p-2 text-center"><input type="checkbox" id="select_all"></th>
                            <th class="p-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if (empty($data['daftar_siswa'])) : ?>
                            <tr><td colspan="2" class="p-4 text-center text-gray-500">Tidak ada siswa di kelas ini.</td></tr>
                        <?php else : ?>
                            <?php foreach ($data['daftar_siswa'] as $siswa) : ?>
                                <tr>
                                    <td class="p-2 text-center"><input type="checkbox" name="siswa_terpilih[]" value="<?= $siswa['id_siswa']; ?>" class="siswa-checkbox"></td>
                                    <td class="p-2 text-sm"><?= htmlspecialchars($siswa['nama_siswa']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg" onclick="return confirm('Luluskan semua siswa yang dipilih? Status mereka akan diubah menjadi Lulus.');">
                    Luluskan Siswa Terpilih
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</main>

<script>
    const tpSelect = document.getElementById('tp_kelulusan');
    const kelasSelect = document.getElementById('kelas_kelulusan');
    const selectAllCheckbox = document.getElementById('select_all');

    tpSelect.addEventListener('change', function() {
        const id_tp = this.value;
        kelasSelect.innerHTML = '<option>Memuat...</option>';
        if (id_tp) {
            fetch(`<?= BASEURL; ?>/admin/getKelasByTP/${id_tp}`)
                .then(response => response.json())
                .then(data => {
                    kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
                    if (data.length > 0) {
                        data.forEach(kelas => {
                            kelasSelect.innerHTML += `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                        });
                    } else {
                        kelasSelect.innerHTML = '<option value="">-- Tidak ada kelas --</option>';
                    }
                });
        } else {
            kelasSelect.innerHTML = '<option value="">-- Pilih TP Dulu --</option>';
        }
    });

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.siswa-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
</script>