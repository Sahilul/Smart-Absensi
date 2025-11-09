<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapor Tengah Semester</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .container {
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10pt;
            margin: 2px 0;
        }
        
        .kop-surat {
            margin-bottom: 15px;
        }
        
        .identitas {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .identitas table {
            width: 100%;
        }
        
        .identitas td {
            padding: 3px 0;
            font-size: 11pt;
        }
        
        .identitas td:first-child {
            width: 150px;
        }
        
        .identitas td:nth-child(2) {
            width: 10px;
        }
        
        h3 {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 15px 0;
            text-decoration: underline;
        }
        
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .nilai-table th,
        .nilai-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 10pt;
        }
        
        .nilai-table th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        
        .nilai-table td:nth-child(2) {
            text-align: left;
            padding-left: 10px;
        }
        
        .rekap-absensi {
            width: 50%;
            margin: 20px 0;
        }
        
        .rekap-absensi table {
            width: 100%;
        }
        
        .rekap-absensi td {
            padding: 3px 5px;
            font-size: 10pt;
        }
        
        .rekap-absensi td:first-child {
            width: 100px;
        }
        
        .rekap-absensi td:nth-child(2) {
            width: 10px;
        }
        
        .ttd-section {
            margin-top: 30px;
            width: 100%;
        }
        
        .ttd-box {
            display: inline-block;
            width: 48%;
            text-align: center;
            vertical-align: top;
        }
        
        .ttd-box.right {
            float: right;
        }
        
        .ttd-box p {
            margin: 5px 0;
            font-size: 10pt;
        }
        
        .ttd-space {
            height: 60px;
        }
        
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .catatan {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #000;
            min-height: 80px;
        }
        
        .catatan p {
            font-size: 10pt;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .predikat-A { background-color: #4CAF50 !important; color: white !important; font-weight: bold; }
        .predikat-B { background-color: #8BC34A !important; color: white !important; font-weight: bold; }
        .predikat-C { background-color: #FFC107 !important; color: #000 !important; font-weight: bold; }
        .predikat-D { background-color: #F44336 !important; color: white !important; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <!-- KOP SURAT -->
        <div class="header kop-surat">
            <h2>MADRASAH SABILILLAH</h2>
            <p>Jl. Contoh No. 123, Kota, Provinsi 12345</p>
            <p>Telp: (021) 12345678 | Email: info@madrasahsabilillah.sch.id</p>
        </div>

        <!-- JUDUL RAPOR -->
        <h3>LAPORAN HASIL BELAJAR SISWA<br>TENGAH SEMESTER (STS)</h3>

        <!-- IDENTITAS SISWA -->
        <div class="identitas">
            <table>
                <tr>
                    <td>Nama Siswa</td>
                    <td>:</td>
                    <td><strong><?= htmlspecialchars($siswa['nama_siswa'] ?? '-'); ?></strong></td>
                </tr>
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($wali_kelas['nama_kelas'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>Semester</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($semester['semester'] ?? '-'); ?> - Tahun Pelajaran <?= htmlspecialchars($semester['nama_tp'] ?? '-'); ?></td>
                </tr>
            </table>
        </div>

        <!-- TABEL NILAI -->
        <table class="nilai-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Mata Pelajaran</th>
                    <th width="15%">Nilai Harian<br>(40%)</th>
                    <th width="15%">Nilai STS<br>(60%)</th>
                    <th width="15%">Nilai Akhir</th>
                    <th width="15%">Predikat</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $total_nilai = 0;
                $jumlah_mapel = 0;
                
                if (!empty($nilai_mapel)) {
                    foreach ($nilai_mapel as $nilai) {
                        $nilai_harian = $nilai['nilai_harian'] ?? 0;
                        $nilai_sts = $nilai['nilai_sts'] ?? 0;
                        $nilai_akhir = ($nilai_harian * 0.4) + ($nilai_sts * 0.6);
                        
                        // Tentukan predikat
                        if ($nilai_akhir >= 90) {
                            $predikat = 'A';
                            $class = 'predikat-A';
                        } elseif ($nilai_akhir >= 80) {
                            $predikat = 'B';
                            $class = 'predikat-B';
                        } elseif ($nilai_akhir >= 70) {
                            $predikat = 'C';
                            $class = 'predikat-C';
                        } else {
                            $predikat = 'D';
                            $class = 'predikat-D';
                        }
                        
                        $total_nilai += $nilai_akhir;
                        $jumlah_mapel++;
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($nilai['nama_mapel'] ?? '-'); ?></td>
                        <td><?= number_format($nilai_harian, 1); ?></td>
                        <td><?= number_format($nilai_sts, 1); ?></td>
                        <td><strong><?= number_format($nilai_akhir, 1); ?></strong></td>
                        <td class="<?= $class; ?>"><?= $predikat; ?></td>
                    </tr>
                <?php 
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Belum ada data nilai</td>
                    </tr>
                <?php } ?>
                
                <?php if ($jumlah_mapel > 0): ?>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="4" style="text-align: right; padding-right: 10px;">RATA-RATA</td>
                    <td><strong><?= number_format($total_nilai / $jumlah_mapel, 1); ?></strong></td>
                    <td>-</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- REKAP ABSENSI -->
        <div class="rekap-absensi">
            <strong>Rekap Kehadiran:</strong>
            <table>
                <tr>
                    <td>Sakit</td>
                    <td>:</td>
                    <td><?= $rekap_absensi['sakit'] ?? 0; ?> hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td>:</td>
                    <td><?= $rekap_absensi['izin'] ?? 0; ?> hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td>:</td>
                    <td><?= $rekap_absensi['alpa'] ?? 0; ?> hari</td>
                </tr>
            </table>
        </div>

        <!-- CATATAN WALI KELAS -->
        <div class="catatan">
            <p>Catatan Wali Kelas:</p>
            <div style="margin-top: 10px; font-size: 10pt; font-weight: normal;">
                <?php if ($jumlah_mapel > 0): ?>
                    <?php 
                    $rata_rata = $total_nilai / $jumlah_mapel;
                    if ($rata_rata >= 90) {
                        echo "Prestasi sangat memuaskan. Pertahankan dan tingkatkan terus!";
                    } elseif ($rata_rata >= 80) {
                        echo "Prestasi baik. Tingkatkan lagi di semester berikutnya.";
                    } elseif ($rata_rata >= 70) {
                        echo "Prestasi cukup. Perlu lebih giat belajar lagi.";
                    } else {
                        echo "Perlu bimbingan lebih intensif untuk meningkatkan prestasi.";
                    }
                    ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </div>
        </div>

        <!-- TANDA TANGAN -->
        <div class="ttd-section">
            <div class="ttd-box">
                <p>Mengetahui,</p>
                <p>Orang Tua/Wali</p>
                <div class="ttd-space"></div>
                <p class="ttd-name">(...........................)</p>
            </div>
            
            <div class="ttd-box right">
                <p><?= htmlspecialchars($pengaturan['tempat_pembagian_rapor'] ?? 'Kota'); ?>, <?= date('d F Y', strtotime($pengaturan['tanggal_pembagian_rapor'] ?? date('Y-m-d'))); ?></p>
                <p>Wali Kelas</p>
                <div class="ttd-space"></div>
                <p class="ttd-name"><?= htmlspecialchars($wali_kelas['nama_guru'] ?? '(...................)'); ?></p>
            </div>
        </div>

        <div style="clear: both;"></div>

        <!-- KEPALA MADRASAH -->
        <div class="ttd-section" style="margin-top: 20px;">
            <div class="ttd-box right">
                <p>Mengetahui,</p>
                <p>Kepala Madrasah</p>
                <div class="ttd-space"></div>
                <p class="ttd-name"><?= htmlspecialchars($pengaturan['nama_kepala_madrasah'] ?? '(...................)'); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
