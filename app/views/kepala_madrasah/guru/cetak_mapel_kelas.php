<?php
// $meta, $rekap_siswa, $rekap_pertemuan, $total_siswa tersedia dari controller.
function pct($n,$d){ return $d>0 ? number_format($n*100/$d,1,',','.') : '0,0'; }

// Cek apakah ini mode PDF atau bukan
$isPdfMode = isset($_GET['pdf']) && $_GET['pdf'] == 1;

// Validasi data yang diperlukan dengan fallback yang aman
$meta = $meta ?? [];
$rekap_siswa = $rekap_siswa ?? [];
$rekap_pertemuan = $rekap_pertemuan ?? [];
$total_siswa = $total_siswa ?? count($rekap_siswa);
$id_penugasan = $id_penugasan ?? ($_GET['id_penugasan'] ?? null);

// Fallback untuk meta jika kosong
if (empty($meta)) {
    $meta = [
        'nama_mapel' => 'Mata Pelajaran',
        'nama_kelas' => 'Kelas',
        'nama_guru' => 'Guru',
        'semester' => 'Semester',
        'tp' => '',
        'tanggal' => date('d F Y')
    ];
}

// Debug untuk memastikan data tersedia
error_log("DEBUG cetak_mapel_kelas.php: Data available - rekap_siswa=" . count($rekap_siswa) . " rekap_pertemuan=" . count($rekap_pertemuan) . " meta=" . json_encode($meta));

// Hitung total untuk validasi
$has_student_data = !empty($rekap_siswa);
$has_meeting_data = !empty($rekap_pertemuan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan - <?= htmlspecialchars($meta['nama_mapel'] ?? 'Mata Pelajaran'); ?> - <?= htmlspecialchars($meta['nama_kelas'] ?? 'Kelas'); ?></title>
<style>
  @page { size: A4; margin: 18mm 16mm; }
  * { box-sizing: border-box; }
  body { font-family: "Inter", Arial, sans-serif; color:#0f172a; font-size: 12px; line-height: 1.4; }
  h1,h2,h3 { margin:0; }
  .actions { position: sticky; top:0; background:#fff; padding:10px 0 14px; margin-bottom:10px; border-bottom:1px solid #e5e7eb; }
  .btn { display:inline-block; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:700; margin-right:8px; }
  .btn-print { background:#0ea5e9; color:#fff; }
  .btn-pdf { background:#22c55e; color:#fff; }
  .btn-back { background:#6b7280; color:#fff; }
  .meta { margin-bottom:14px; }
  .meta-grid { display:grid; grid-template-columns: 1fr 1fr; gap:6px 18px; }
  .card { border:1px solid #e5e7eb; border-radius:10px; padding:12px 14px; margin-top:12px; }
  .title { font-size:14px; font-weight:800; letter-spacing:.5px; margin-bottom:6px; text-transform:uppercase; }
  table { width:100%; border-collapse: collapse; margin-top: 8px; }
  th, td { border:1px solid #e5e7eb; padding:8px 9px; vertical-align: top; }
  th { background:#f8fafc; text-align:left; font-weight:700; font-size: 11px; }
  tfoot td { font-weight:700; background:#fafafa; }
  .right { text-align:right; }
  .center { text-align:center; }
  .sign { margin-top:28px; display:flex; justify-content:flex-end; }
  .sign .box { width:260px; text-align:center; }
  .muted { color:#64748b; }
  .mb4 { margin-bottom:4px; }
  .mb8 { margin-bottom:8px; }
  .mt10{ margin-top:10px; }
  .mt16{ margin-top:16px; }
  .w20{ width:20px; }
  .w30{ width:30px; }
  .empty-state { text-align:center; padding:20px; color:#64748b; font-style: italic; border:1px dashed #d1d5db; border-radius:8px; background:#f9fafb; }
  .warning-box { background:#fef3c7; border:1px solid #f59e0b; color:#92400e; padding:12px; border-radius:8px; margin:10px 0; }
  .info-box { background:#dbeafe; border:1px solid #3b82f6; color:#1e40af; padding:12px; border-radius:8px; margin:10px 0; }
  
  /* CSS untuk PDF - lebih eksplisit */
  .pdf-hide { display: none !important; }
  
  @media print {
    .actions { display:none !important; }
    .no-print { display:none !important; }
    a[href]:after { content:""; }
    .card { page-break-inside: avoid; }
  }
</style>
</head>
<body>

<!-- Toolbar HANYA tampil jika BUKAN mode PDF -->
<?php if (!$isPdfMode): ?>
<div class="actions no-print">
  <a class="btn btn-print" href="javascript:window.print()">🖨️ Cetak Halaman</a>
  <?php if ($id_penugasan): ?>
  <a class="btn btn-pdf" href="<?= BASEURL; ?>/riwayatJurnal/cetak/<?= (int)$id_penugasan; ?>?pdf=1">
    📄 Unduh PDF
  </a>
  <?php endif; ?>
  <a class="btn btn-back" href="<?= BASEURL; ?>/guru/jurnal">
    ← Kembali ke Input Jurnal
  </a>
</div>
<?php endif; ?>

<!-- Warning jika data tidak lengkap -->
<?php if (!$has_student_data && !$has_meeting_data && !$isPdfMode): ?>
<div class="warning-box no-print">
  <strong>⚠️ Peringatan:</strong> Data jurnal dan absensi kosong. Pastikan:
  <ul style="margin:8px 0 0 20px;">
    <li>Jurnal mengajar sudah dibuat melalui menu "Input Jurnal"</li>
    <li>Absensi siswa sudah diinput untuk setiap pertemuan</li>
    <li>ID penugasan valid: <?= htmlspecialchars($id_penugasan ?? 'tidak tersedia'); ?></li>
  </ul>
</div>
<?php endif; ?>

<!-- LAPORAN KEHADIRAN SISWA -->
<div class="card">
  <div class="title">📊 Laporan Kehadiran Siswa</div>
  <div class="meta">
    <div class="meta-grid">
      <div><strong>KELAS</strong> : <?= htmlspecialchars($meta['nama_kelas'] ?? '-'); ?></div>
      <div><strong>MATA PELAJARAN</strong> : <?= htmlspecialchars($meta['nama_mapel'] ?? '-'); ?></div>
      <div><strong>NAMA GURU</strong> : <?= htmlspecialchars($meta['nama_guru'] ?? '-'); ?></div>
      <div><strong>SEMESTER</strong> : <?= htmlspecialchars($meta['semester'] ?? '-'); ?></div>
      <?php if (!empty($meta['tp'])): ?>
        <div><strong>TAHUN PELAJARAN</strong> : <?= htmlspecialchars($meta['tp']); ?></div>
      <?php endif; ?>
      <div><strong>TANGGAL CETAK</strong> : <?= htmlspecialchars($meta['tanggal'] ?? date('d F Y')); ?></div>
    </div>
  </div>

  <?php if (!$has_student_data): ?>
    <div class="empty-state">
      <p><strong>⚠️ Belum ada data absensi siswa untuk penugasan ini.</strong></p>
      <p>Pastikan jurnal sudah dibuat dan absensi siswa sudah diinput.</p>
      <?php if ($id_penugasan && !$isPdfMode): ?>
        <p><small>ID Penugasan: <?= htmlspecialchars($id_penugasan); ?></small></p>
      <?php endif; ?>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th class="w20 center">No</th>
        <th>Nama Siswa</th>
        <th class="center w30">NISN</th>
        <th class="center w30">Hadir</th>
        <th class="center w30">Izin</th>
        <th class="center w30">Sakit</th>
        <th class="center w30">Alpha</th>
        <th class="center w30">Total</th>
        <th class="center">Kehadiran %</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i=1;
      $sumH=$sumI=$sumS=$sumA=$sumT=0;
      foreach ($rekap_siswa as $row):
        $hadir = (int)($row['hadir'] ?? 0);
        $izin = (int)($row['izin'] ?? 0);
        $sakit = (int)($row['sakit'] ?? 0);
        $alpha = (int)($row['alpha'] ?? 0);
        $total = (int)($row['total_absensi'] ?? $row['total'] ?? ($hadir + $izin + $sakit + $alpha));
        
        $sumH += $hadir;
        $sumI += $izin;
        $sumS += $sakit;
        $sumA += $alpha;
        $sumT += $total;
        $pct = pct($hadir, $total);
      ?>
      <tr>
        <td class="center"><?= $i++; ?></td>
        <td><?= htmlspecialchars($row['nama_siswa'] ?? 'Siswa #' . $i); ?></td>
        <td class="center"><?= htmlspecialchars($row['nisn'] ?? '-'); ?></td>
        <td class="center"><?= $hadir; ?></td>
        <td class="center"><?= $izin; ?></td>
        <td class="center"><?= $sakit; ?></td>
        <td class="center"><?= $alpha; ?></td>
        <td class="center"><?= $total; ?></td>
        <td class="center"><?= $pct; ?>%</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="right"><strong>TOTAL</strong></td>
        <td class="center"><strong><?= $sumH; ?></strong></td>
        <td class="center"><strong><?= $sumI; ?></strong></td>
        <td class="center"><strong><?= $sumS; ?></strong></td>
        <td class="center"><strong><?= $sumA; ?></strong></td>
        <td class="center"><strong><?= $sumT; ?></strong></td>
        <td class="center"><strong><?= pct($sumH, max(1,$sumT)); ?>%</strong></td>
      </tr>
    </tfoot>
  </table>
  <?php endif; ?>

  <div class="sign">
    <div class="box">
      <?= htmlspecialchars($meta['tanggal'] ?? date('d F Y')); ?><br>
      <span class="muted">Guru Mata Pelajaran</span><br><br><br>
      <strong><?= htmlspecialchars($meta['nama_guru'] ?? '-'); ?></strong>
    </div>
  </div>
</div>

<!-- LAPORAN JURNAL MENGAJAR -->
<div class="card mt16">
  <div class="title">📚 Laporan Jurnal Mengajar</div>
  <div class="meta mb8">
    <div class="meta-grid">
      <div><strong>KELAS</strong> : <?= htmlspecialchars($meta['nama_kelas'] ?? '-'); ?></div>
      <div><strong>MATA PELAJARAN</strong> : <?= htmlspecialchars($meta['nama_mapel'] ?? '-'); ?></div>
      <div><strong>NAMA GURU</strong> : <?= htmlspecialchars($meta['nama_guru'] ?? '-'); ?></div>
      <div><strong>SEMESTER</strong> : <?= htmlspecialchars($meta['semester'] ?? '-'); ?></div>
      <?php if (!empty($meta['tp'])): ?>
        <div><strong>TAHUN PELAJARAN</strong> : <?= htmlspecialchars($meta['tp']); ?></div>
      <?php endif; ?>
      <div><strong>TANGGAL CETAK</strong> : <?= htmlspecialchars($meta['tanggal'] ?? date('d F Y')); ?></div>
    </div>
  </div>

  <?php if (!$has_meeting_data): ?>
    <div class="empty-state">
      <p><strong>⚠️ Belum ada data jurnal mengajar untuk penugasan ini.</strong></p>
      <p>Silakan buat jurnal mengajar terlebih dahulu melalui menu Input Jurnal.</p>
      <?php if ($id_penugasan && !$isPdfMode): ?>
        <p><small>ID Penugasan: <?= htmlspecialchars($id_penugasan); ?></small></p>
        <div style="margin-top:10px;">
          <a href="<?= BASEURL; ?>/guru/jurnal" class="btn btn-print" style="font-size:11px;">
            ➕ Buat Jurnal Baru
          </a>
        </div>
      <?php endif; ?>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th class="center w30">Pert.</th>
        <th>Tanggal</th>
        <th>Topik Materi</th>
        <th class="center w30">H</th>
        <th class="center w30">I</th>
        <th class="center w30">S</th>
        <th class="center w30">A</th>
        <th class="center w30">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $totalH = $totalI = $totalS = $totalA = $totalAll = 0;
      foreach ($rekap_pertemuan as $row): 
        $hadir = (int)($row['hadir'] ?? 0);
        $izin = (int)($row['izin'] ?? 0);
        $sakit = (int)($row['sakit'] ?? 0);
        $alpha = (int)($row['alpha'] ?? 0);
        $total = (int)($row['total'] ?? ($hadir + $izin + $sakit + $alpha));
        
        $totalH += $hadir;
        $totalI += $izin;
        $totalS += $sakit;
        $totalA += $alpha;
        $totalAll += $total;
      ?>
      <tr>
        <td class="center"><?= (int)($row['pertemuan_ke'] ?? 0); ?></td>
        <td><?= !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-'; ?></td>
        <td>
          <?= htmlspecialchars($row['topik_materi'] ?? 'Tidak ada keterangan'); ?>
          <?php if (!empty($row['catatan'])): ?>
            <br><small class="muted"><?= htmlspecialchars($row['catatan']); ?></small>
          <?php endif; ?>
        </td>
        <td class="center"><?= $hadir; ?></td>
        <td class="center"><?= $izin; ?></td>
        <td class="center"><?= $sakit; ?></td>
        <td class="center"><?= $alpha; ?></td>
        <td class="center"><?= $total; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <?php if (count($rekap_pertemuan) > 1): ?>
    <tfoot>
      <tr>
        <td colspan="3" class="right"><strong>TOTAL</strong></td>
        <td class="center"><strong><?= $totalH; ?></strong></td>
        <td class="center"><strong><?= $totalI; ?></strong></td>
        <td class="center"><strong><?= $totalS; ?></strong></td>
        <td class="center"><strong><?= $totalA; ?></strong></td>
        <td class="center"><strong><?= $totalAll; ?></strong></td>
      </tr>
    </tfoot>
    <?php endif; ?>
  </table>
  <?php endif; ?>

  <div class="sign">
    <div class="box">
      <?= htmlspecialchars($meta['tanggal'] ?? date('d F Y')); ?><br>
      <span class="muted">Guru Mata Pelajaran</span><br><br><br>
      <strong><?= htmlspecialchars($meta['nama_guru'] ?? '-'); ?></strong>
    </div>
  </div>
</div>

<!-- RINGKASAN STATISTIK -->
<?php if ($has_student_data && $has_meeting_data): ?>
<div class="card mt16">
  <div class="title">📈 Ringkasan Statistik</div>
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 10px;">
    <div>
      <h4 style="margin: 0 0 8px 0; font-size: 12px;">Statistik Kehadiran:</h4>
      <ul style="margin: 0; padding-left: 16px; font-size: 11px;">
        <li>Total Siswa: <strong><?= $total_siswa; ?> orang</strong></li>
        <li>Total Pertemuan: <strong><?= count($rekap_pertemuan); ?> kali</strong></li>
        <li>Rata-rata Kehadiran: <strong><?= isset($sumT) && $sumT > 0 ? pct($sumH, $sumT) : '0,0'; ?>%</strong></li>
      </ul>
    </div>
    <div>
      <h4 style="margin: 0 0 8px 0; font-size: 12px;">Distribusi Absensi:</h4>
      <ul style="margin: 0; padding-left: 16px; font-size: 11px;">
        <li>Hadir: <strong><?= isset($sumH) ? $sumH : 0; ?></strong> (<?= isset($sumT) && $sumT > 0 ? pct($sumH, $sumT) : '0,0'; ?>%)</li>
        <li>Izin: <strong><?= isset($sumI) ? $sumI : 0; ?></strong> (<?= isset($sumT) && $sumT > 0 ? pct($sumI, $sumT) : '0,0'; ?>%)</li>
        <li>Sakit: <strong><?= isset($sumS) ? $sumS : 0; ?></strong> (<?= isset($sumT) && $sumT > 0 ? pct($sumS, $sumT) : '0,0'; ?>%)</li>
        <li>Alpha: <strong><?= isset($sumA) ? $sumA : 0; ?></strong> (<?= isset($sumT) && $sumT > 0 ? pct($sumA, $sumT) : '0,0'; ?>%)</li>
      </ul>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Footer dengan info sistem -->
<?php if (!$isPdfMode): ?>
<div style="margin-top: 20px; padding: 10px; background: #f8fafc; border-radius: 8px; font-size: 10px; color: #64748b; text-align: center;">
  Laporan ini dibuat otomatis oleh Sistem Absensi Digital pada <?= date('d F Y H:i:s'); ?><br>
  <?php if ($id_penugasan): ?>
    <small>Referensi: Penugasan ID <?= htmlspecialchars($id_penugasan); ?></small>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- Script hanya untuk browser, tidak untuk PDF -->
<?php if (!$isPdfMode): ?>
<script>
  // Script untuk interaksi browser
  console.log('PDF Mode: <?= $isPdfMode ? "true" : "false"; ?>');
  console.log('Data siswa: <?= count($rekap_siswa); ?> records');
  console.log('Data pertemuan: <?= count($rekap_pertemuan); ?> records');
  console.log('ID Penugasan: <?= $id_penugasan ? htmlspecialchars($id_penugasan) : "null"; ?>');
  
  // Auto print jika ada parameter print=1 di URL
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('print') === '1') {
    setTimeout(() => window.print(), 500);
  }

  // Show info jika data kosong
  <?php if (!$has_student_data && !$has_meeting_data): ?>
  console.warn('Data kosong - mungkin ID penugasan salah atau belum ada jurnal/absensi');
  <?php endif; ?>
</script>
<?php endif; ?>

</body>
</html>