<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Pengaturan Wali Kelas</h4>
                        <p class="card-description">
                            Menetapkan guru sebagai wali kelas untuk tahun pelajaran aktif.
                        </p>
                        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahWaliKelasModal">
                            <i class="mdi mdi-account-plus"></i> Tambah Wali Kelas
                        </button>

                        <?php Flasher::flash(); ?>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Nama Guru</th>
                                        <th>Kelas yang Diampu</th>
                                        <th>Tahun Pelajaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($data['wali_kelas'] as $wk) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($wk['nama_guru']); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($wk['nama_kelas']); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($wk['nama_tp']); ?></td>
                                            <td class="text-center">
                                                <a href="<?= BASEURL; ?>/nilaicontroller/hapus_wali_kelas/<?= $wk['id_walikelas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="mdi mdi-delete"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahWaliKelasModal" tabindex="-1" role="dialog" aria-labelledby="judulModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="judulModal">Tambah Wali Kelas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= BASEURL; ?>/nilaicontroller/tambah_wali_kelas" method="post">
                        <div class="form-group">
                            <label for="id_tp">Tahun Pelajaran (Aktif)</label>
                            <input type="hidden" name="id_tp" value="<?= $data['tp']['id_tp']; ?>">
                            <input type="text" class="form-control" id="nama_tp" value="<?= $data['tp']['nama_tp']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="id_guru">Pilih Guru</label>
                            <select class="form-control" id="id_guru" name="id_guru" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach($data['guru'] as $guru): ?>
                                    <option value="<?= $guru['id_guru'] ?>"><?= htmlspecialchars($guru['nama_guru']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_kelas">Pilih Kelas</label>
                            <select class="form-control" id="id_kelas" name="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach($data['kelas'] as $kelas): ?>
                                    <option value="<?= $kelas['id_kelas'] ?>"><?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>