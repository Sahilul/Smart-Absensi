<?php
// $meta, $rekap_siswa, $rekap_pertemuan, $total_siswa tersedia dari controller.
function pct($n,$d){ return $d>0 ? number_format($n*100/$d,1,',','.') : '0,0'; }

// Cek apakah ini mode PDF atau bukan
$isPdfMode = isset($_GET['pdf']) && $_GET['pdf'] == 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan - <?= htmlspecialchars($meta['nama_mapel']); ?></title>
<style>
  @page { size: A4; margin: 18mm 16mm; }
  * { box-sizing: border-box; }
  body { font-family: "Inter", Arial, sans-serif; color:#0f172a; font-size: 12px; }
  h1,h2,h3 { margin:0; }
  .actions { position: sticky; top:0; background:#fff; padding:10px 0 14px; margin-bottom:10px; border-bottom:1px solid #e5e7eb; }
  .btn { display:inline-block; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:700; margin-right:8px; }
  .btn-print { background:#0ea5e9; color:#fff; }
  .btn-pdf { background:#22c55e; color:#fff; }
  .meta { margin-bottom:14px; }
  .meta-grid { display:grid; grid-template-columns: 1fr 1fr; gap:6px 18px; }
  .card { border:1px solid #e5e7eb; border-radius:10px; padding:12px 14px; margin-top:12px; }
  .title { font-size:14px; font-weight:800; letter-spacing:.5px; margin-bottom:6px; text-transform:uppercase; }
  table { width:100%; border-collapse: collapse; }
  th, td { border:1px solid #e5e7eb; padding:8px 9px; }
  th { background:#f8fafc; text-align:left; font-weight:700; }
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
  
  /* CSS untuk PDF - lebih eksplisit */
  .pdf-hide { display: none !important; }
  
  @media print {
    .actions { display:none !important; }
    .no-print { display:none !important; }
    a[href]:after { content:""; }
  }
</style>
</head>
<body>

<!-- Toolbar HANYA tampil jika BUKAN mode PDF -->
<?php if (!$isPdfMode): ?>
<div class="actions no-print">
  <a class="btn btn-print" href="javascript:window.print()">Cetak Halaman</a>
  <a class="btn btn-pdf" href="<?= BASEURL; ?>/guru/cetakMapel/<?= (int)$id_mapel; ?>?pdf=1">
    Unduh PDF
  </a>
</div>
<?php endif; ?>

<!-- LAPORAN KEHADIRAN SISWA -->
<div class="card">
  <div class="title">Laporan Kehadiran Siswa</div>
  <div class="meta">
    <div class="meta-grid">
      <div><strong>KELAS</strong> : <?= htmlspecialchars($meta['nama_kelas']); ?></div>
      <div><strong>MATA PELAJARAN</strong> : <?= htmlspecialchars($meta['nama_mapel']); ?></div>
      <div><strong>NAMA GURU</strong> : <?= htmlspecialchars($meta['nama_guru']); ?></div>
      <div><strong>SEMESTER</strong> : <?= htmlspecialchars($meta['semester']); ?></div>
      <?php if (!empty($meta['tp'])): ?>
        <div><strong>TAHUN PELAJARAN</strong> : <?= htmlspecialchars($meta['tp']); ?></div>
      <?php endif; ?>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th class="w20 center">No</th>
        <th>Nama Siswa</th>
        <th class="center">Hadir</th>
        <th class="center">Izin</th>
        <th class="center">Sakit</th>
        <th class="center">Alpha</th>
        <th class="center">Kehadiran %</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i=1;
      $sumH=$sumI=$sumS=$sumA=$sumT=0;
      foreach ($rekap_siswa as $row):
        $sumH += (int)$row['hadir'];
        $sumI += (int)$row['izin'];
        $sumS += (int)$row['sakit'];
        $sumA += (int)$row['alpha'];
        $sumT += (int)$row['total'];
        $pct = pct((int)$row['hadir'], (int)$row['total']);
      ?>
      <tr>
        <td class="center"><?= $i++; ?></td>
        <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
        <td class="center"><?= (int)$row['hadir']; ?></td>
        <td class="center"><?= (int)$row['izin']; ?></td>
        <td class="center"><?= (int)$row['sakit']; ?></td>
        <td class="center"><?= (int)$row['alpha']; ?></td>
        <td class="center"><?= $pct; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2" class="right">TOTAL</td>
        <td class="center"><?= $sumH; ?></td>
        <td class="center"><?= $sumI; ?></td>
        <td class="center"><?= $sumS; ?></td>
        <td class="center"><?= $sumA; ?></td>
        <td class="center"><?= pct($sumH, max(1,$sumT)); ?></td>
      </tr>
    </tfoot>
  </table>

  <div class="sign">
    <div class="box">
      Mojokerto, <?= htmlspecialchars($meta['tanggal']); ?><br>
      <span class="muted">Guru Mata Pelajaran</span><br><br><br>
      <strong><?= htmlspecialchars($meta['nama_guru']); ?></strong>
    </div>
  </div>
</div>

<!-- LAPORAN JURNAL MENGAJAR -->
<div class="card mt16">
  <div class="title">Laporan Jurnal Mengajar</div>
  <div class="meta mb8">
    <div class="meta-grid">
      <div><strong>KELAS</strong> : <?= htmlspecialchars($meta['nama_kelas']); ?></div>
      <div><strong>MATA PELAJARAN</strong> : <?= htmlspecialchars($meta['nama_mapel']); ?></div>
      <div><strong>NAMA GURU</strong> : <?= htmlspecialchars($meta['nama_guru']); ?></div>
      <div><strong>SEMESTER</strong> : <?= htmlspecialchars($meta['semester']); ?></div>
      <?php if (!empty($meta['tp'])): ?>
        <div><strong>TAHUN PELAJARAN</strong> : <?= htmlspecialchars($meta['tp']); ?></div>
      <?php endif; ?>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th class="center w20">Pert.</th>
        <th>Tanggal</th>
        <th>Topik Materi</th>
        <th class="center">H</th>
        <th class="center">I</th>
        <th class="center">S</th>
        <th class="center">A</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rekap_pertemuan as $row): ?>
      <tr>
        <td class="center"><?= (int)$row['pertemuan_ke']; ?></td>
        <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
        <td><?= htmlspecialchars($row['topik_materi'] ?? '-'); ?></td>
        <td class="center"><?= (int)$row['hadir']; ?></td>
        <td class="center"><?= (int)$row['izin']; ?></td>
        <td class="center"><?= (int)$row['sakit']; ?></td>
        <td class="center"><?= (int)$row['alpha']; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="sign">
    <div class="box">
      Mojokerto, <?= htmlspecialchars($meta['tanggal']); ?><br>
      <span class="muted">Guru Mata Pelajaran</span><br><br><br>
      <strong><?= htmlspecialchars($meta['nama_guru']); ?></strong>
    </div>
  </div>
</div>

<!-- Script hanya untuk browser, tidak untuk PDF -->
<?php if (!$isPdfMode): ?>
<script>
  // Script untuk interaksi browser
  console.log('PDF Mode: <?= $isPdfMode ? "true" : "false"; ?>');
</script>
<?php endif; ?>

</body>
</html>