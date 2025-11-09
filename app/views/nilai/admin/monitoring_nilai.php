<?php
// File: app/views/nilai/admin/monitoring_nilai.php
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $data['judul']; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASEURL; ?>/admin">Dashboard</a></li>
                        <li class="breadcrumb-item active">Monitoring Nilai</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Data Nilai</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= BASEURL; ?>/nilai/monitoring_nilai">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="id_semester" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Semester</option>
                                        <?php foreach($data['semester'] as $semester): ?>
                                            <option value="<?= $semester['id_semester']; ?>" 
                                                    <?= ($data['filter']['id_semester'] == $semester['id_semester']) ? 'selected' : ''; ?>>
                                                <?= $semester['semester']; ?> - <?= $semester['nama_tp']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="id_kelas" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Kelas</option>
                                        <?php foreach($data['kelas'] as $kelas): ?>
                                            <option value="<?= $kelas['id_kelas']; ?>" 
                                                    <?= ($data['filter']['id_kelas'] == $kelas['id_kelas']) ? 'selected' : ''; ?>>
                                                <?= $kelas['nama_kelas']; ?> (<?= $kelas['nama_tp']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mata Pelajaran</label>
                                    <select name="id_mapel" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Mapel</option>
                                        <?php foreach($data['mapel'] as $mapel): ?>
                                            <option value="<?= $mapel['id_mapel']; ?>" 
                                                    <?= ($data['filter']['id_mapel'] == $mapel['id_mapel']) ? 'selected' : ''; ?>>
                                                <?= $mapel['nama_mapel']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jenis Nilai</label>
                                    <select name="jenis_nilai" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Jenis</option>
                                        <option value="harian" <?= ($data['filter']['jenis_nilai'] == 'harian') ? 'selected' : ''; ?>>Harian</option>
                                        <option value="sts" <?= ($data['filter']['jenis_nilai'] == 'sts') ? 'selected' : ''; ?>>STS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="<?= BASEURL; ?>/nilai/monitoring_nilai" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Nilai -->
            <?php if (!empty($data['nilai'])): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Nilai Siswa</h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= count($data['nilai']); ?> record ditemukan</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="nilaiTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">NISN</th>
                                    <th width="20%">Nama Siswa</th>
                                    <th width="15%">Kelas</th>
                                    <th width="15%">Mata Pelajaran</th>
                                    <th width="10%">Jenis Nilai</th>
                                    <th width="8%">Nilai</th>
                                    <th width="12%">Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($data['nilai'] as $nilai): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $nilai['nisn']; ?></td>
                                    <td><strong><?= $nilai['nama_siswa']; ?></strong></td>
                                    <td>
                                        <span class="badge badge-info"><?= $nilai['nama_kelas']; ?></span>
                                    </td>
                                    <td><?= $nilai['nama_mapel']; ?></td>
                                    <td>
                                        <?php if($nilai['jenis_nilai'] == 'harian'): ?>
                                            <span class="badge badge-success">Harian</span>
                                        <?php elseif($nilai['jenis_nilai'] == 'sts'): ?>
                                            <span class="badge badge-warning">STS</span>
                                        <?php else: ?>
                                            <span class="badge badge-primary"><?= strtoupper($nilai['jenis_nilai']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="<?= ($nilai['nilai'] >= 75) ? 'text-success' : 'text-danger'; ?>">
                                            <?= number_format($nilai['nilai'], 1); ?>
                                        </strong>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($nilai['tanggal_input'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php elseif ($data['filter']['id_semester'] && $data['filter']['id_kelas']): ?>
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Tidak ada data nilai</h4>
                        <p class="text-muted">Tidak ditemukan data nilai dengan filter yang dipilih.</p>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                        <h4>Pilih Filter</h4>
                        <p class="text-muted">Silakan pilih semester dan kelas untuk melihat data nilai siswa.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#nilaiTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[ 7, "desc" ]] // Sort by tanggal input descending
    });
});
</script>