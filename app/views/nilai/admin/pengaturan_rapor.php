<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Pengaturan Cetak Rapor STS</h4>
                        <p class="card-description">
                            Informasi ini akan ditampilkan pada halaman cetak rapor.
                        </p>

                        <?php Flasher::flash(); ?>

                        <form class="forms-sample" action="<?= BASEURL; ?>/nilaicontroller/simpan_pengaturan_rapor" method="post">
                            <?php 
                                // Membuat array asosiatif agar mudah diakses
                                $pengaturan_rapi = [];
                                foreach ($data['pengaturan'] as $p) {
                                    $pengaturan_rapi[$p['nama_pengaturan']] = $p['nilai_pengaturan'];
                                }
                            ?>
                            <div class="form-group">
                                <label for="nama_kepala_madrasah">Nama Kepala Madrasah</label>
                                <input type="text" class="form-control" id="nama_kepala_madrasah" name="nama_kepala_madrasah" value="<?= htmlspecialchars($pengaturan_rapi['nama_kepala_madrasah'] ?? ''); ?>" placeholder="Masukkan nama kepala madrasah">
                            </div>
                            <div class="form-group">
                                <label for="nip_kepala_madrasah">NIP Kepala Madrasah</label>
                                <input type="text" class="form-control" id="nip_kepala_madrasah" name="nip_kepala_madrasah" value="<?= htmlspecialchars($pengaturan_rapi['nip_kepala_madrasah'] ?? ''); ?>" placeholder="Masukkan NIP (jika ada)">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_pembagian_rapor">Tanggal Pembagian Rapor</label>
                                <input type="date" class="form-control" id="tanggal_pembagian_rapor" name="tanggal_pembagian_rapor" value="<?= htmlspecialchars($pengaturan_rapi['tanggal_pembagian_rapor'] ?? ''); ?>">
                            </div>
                             <div class="form-group">
                                <label for="tempat_pembagian_rapor">Tempat Pembagian Rapor</label>
                                <input type="text" class="form-control" id="tempat_pembagian_rapor" name="tempat_pembagian_rapor" value="<?= htmlspecialchars($pengaturan_rapi['tempat_pembagian_rapor'] ?? ''); ?>" placeholder="Contoh: Pacet, Mojokerto">
                            </div>
                            
                            <button type="submit" class="btn btn-primary mr-2">Simpan Pengaturan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>