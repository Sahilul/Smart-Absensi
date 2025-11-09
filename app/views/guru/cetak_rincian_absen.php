<?php
// File: app/views/guru/cetak_rincian_absen.php

function getStatusSymbol($status) {
  // Pakai HTML entity agar aman untuk engine PDF (Chrome/DOMPDF/mPDF)
  switch ($status) {
    case 'H': return '&#10003;'; // ✓ Hadir
    case 'I': return 'I';
    case 'S': return 'S';
    case 'A': return '&#10007;'; // ✗ Alpha
    default:  return '&#10007;';
  }
}

$isPdfMode = isset($_GET['pdf']) && $_GET['pdf'] == '1';

// Periode header
$periode_text = '';
switch ($filter_info['periode'] ?? 'semester') {
  case 'hari_ini':   $periode_text = 'Hari Ini (' . date('d F Y') . ')'; break;
  case 'minggu_ini': $periode_text = 'Minggu Ini'; break;
  case 'bulan_ini':  $periode_text = 'Bulan Ini (' . date('F Y') . ')'; break;
  case 'custom':
    if (!empty($filter_info['tanggal_mulai']) && !empty($filter_info['tanggal_akhir'])) {
      $periode_text = 'Periode: ' . date('d/m/Y', strtotime($filter_info['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($filter_info['tanggal_akhir']));
    } else { $periode_text = 'Custom Range'; }
    break;
  default: $periode_text = 'Semester Ini'; break;
}

// Bagi kolom pertemuan per 10 kolom
$pertemuan_headers = $rincian_data['pertemuan_headers'] ?? [];
$chunks = array_chunk($pertemuan_headers, 10);
if (empty($chunks)) $chunks = [[]];

// Build URL kembali dengan parameter filter yang sama
$backUrl = BASEURL . '/guru/rincianAbsen';
$backParams = [];
if (!empty($filter_info['id_mapel'])) {
    $backParams['id_mapel'] = $filter_info['id_mapel'];
}
if (!empty($filter_info['periode'])) {
    $backParams['periode'] = $filter_info['periode'];
}
if (!empty($filter_info['tanggal_mulai'])) {
    $backParams['tanggal_mulai'] = $filter_info['tanggal_mulai'];
}
if (!empty($filter_info['tanggal_akhir'])) {
    $backParams['tanggal_akhir'] = $filter_info['tanggal_akhir'];
}
if (!empty($backParams)) {
    $backUrl .= '?' . http_build_query($backParams);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rincian Absen - <?= htmlspecialchars($mapel_info['nama_mapel'] ?? 'Mapel'); ?></title>
<style>
  /* ===== Halaman & Global ===== */
  @page { size: A4 landscape; margin: 8mm 8mm; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  :root{
    --student-col-w: 150px;   /* lebar kolom "Nama Siswa" (ubah jika perlu) */
    --summary-col-w: 30px;    /* lebar kolom H/I/S/A/% supaya ada ruang */
  }
  body{
    font-family: "Arial", "DejaVu Sans", "Segoe UI Symbol", "Arial Unicode MS", sans-serif;
    color:#000; font-size:12px; line-height:1.4; background:#fff;
  }

  /* ===== Action bar (screen only) ===== */
  .actions{ position:fixed; top:0; left:0; right:0; background:#f8f9fa; padding:8px 12px; border-bottom:1px solid #dee2e6; z-index:1000; }
  .actions-buttons{ display:flex; gap:8px; }
  .btn{ display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:4px; text-decoration:none; font-weight:500; font-size:12px; border:1px solid #ccc; background:#fff; color:#333; }
  .btn:hover{ background:#f0f0f0; }

  /* ===== Layout ===== */
  .main-content{ margin-top:52px; padding:12px; }

  /* ===== Header ===== */
  .header-section{ text-align:center; margin-bottom:4px; padding-bottom:4px; border-bottom:none !important; }
  .header-title{ font-size:18px; font-weight:bold; margin-bottom:4px; text-transform:uppercase; letter-spacing:1px; }
  .header-subtitle{ font-size:12px; margin-bottom:4px; color:#555; }
  .info-table{ width:100%; margin-bottom:4px; }
  .info-table td{ padding:4px 8px; vertical-align:top; }
  .info-label{ width:150px; font-weight:500; }
  .info-colon{ width:20px; text-align:center; }

  /* ===== Tabel ===== */
  .table-wrap{ margin:4px 0; }
  table{ width:100%; border-collapse:collapse; font-size:10px; border:2px solid #000; table-layout:auto; }
  th,td{ border:1px solid #000; padding:6px 4px; text-align:center; vertical-align:middle; }
  th{ background:#f8f9fa; font-weight:bold; color:#000; font-size:9px; }

  /* AUTO-FIT util */
  col.w-min{ width:1%; }           /* mengecil ke konten */
  .w-nowrap{ white-space:nowrap; } /* cegah wrap untuk kolom shrink */

  /* Kolom Nama Siswa fixed px (tidak mengecil lagi) */
  col.col-student{ width:var(--student-col-w); }
  th.th-student, td.td-student{
    width:var(--student-col-w);
    max-width:var(--student-col-w);
  }
  td.td-student{
    text-align:left; padding:6px 8px;
    white-space:normal;            /* boleh wrap */
    overflow-wrap:anywhere; word-break:break-word;
    line-height:1.35;
  }

  /* Kolom ringkasan H/I/S/A/% diberi ruang fixed */
  col.col-summary{ width:var(--summary-col-w); }
  .summary-cell, .percentage-cell{ font-weight:bold; background:#f8f9fa; }

  /* Status */
  .status-cell{ font-weight:bold; font-size:11px; font-family:"DejaVu Sans","Segoe UI Symbol","Arial Unicode MS","Arial",sans-serif; }
  .status-H{ background-color:#d4f7d4 !important; color:#2d5a2d !important; }
  .status-I{ background-color:#cce7ff !important; color:#1a4d80 !important; }
  .status-S{ background-color:#fff4cc !important; color:#806600 !important; }
  .status-A{ background-color:#ffcccc !important; color:#802626 !important; }
  .summary-H{ background-color:#d4f7d4 !important; color:#2d5a2d !important; }
  .summary-I{ background-color:#cce7ff !important; color:#1a4d80 !important; }
  .summary-S{ background-color:#fff4cc !important; color:#806600 !important; }
  .summary-A{ background-color:#ffcccc !important; color:#802626 !important; }

  /* Footer: Keterangan & TTD sejajar, tanpa border */
  .footer-section{ margin-top:4px; }
  .footer-table{ width:100%; border-collapse:collapse; border:none; margin-top:4px; }
  .footer-table td{ border:none; vertical-align:top; padding:4px; }
  .keterangan-title{ font-weight:bold; margin-bottom:4px; }
  .status-legend{ display:inline-block; min-width:16px; text-align:center; font-weight:bold; }
  .signature{ text-align:center; min-width:220px; }
  .signature-place{ margin-bottom:4px; font-size:11px; }
  .signature-role{ margin-bottom:60px; font-size:11px; }
  .signature-name{ font-weight:bold; font-size:12px; }

  /* ===== Print ===== */
  @media print{
    .actions{ display:none !important; }
    body{ font-size:10px; margin:0; padding:0; }
    .main-content{ margin-top:0; padding:0; }
    thead{ display:table-header-group; }   /* header terulang */
    tfoot{ display:table-footer-group; }
    tr{ page-break-inside:avoid; }
    .page-break{ page-break-before:always; } /* tabel chunk baru => halaman baru */

    .status-H,.status-I,.status-S,.status-A{
      -webkit-print-color-adjust:exact !important;
      print-color-adjust:exact !important;
      color-adjust:exact !important;
    }
  }

  @media (max-width:1200px){ .table-wrap{ overflow-x:auto; } }
</style>
</head>
<body>

    <!-- Actions Toolbar (hanya untuk non-PDF) -->
    <?php if (!$isPdfMode): ?>
    <div class="actions no-print">
        <div class="actions-buttons">
            <a class="btn" href="javascript:window.print()">
                <span class="btn-icon">🖨</span>
                Cetak Halaman
            </a>
            <?php
            // Link PDF - preserve semua parameter yang ada
            $pdfParams = $_GET;
            $pdfParams['pdf'] = 1;
            if (empty($pdfParams['id_mapel']) && !empty($filter_info['id_mapel'])) {
                $pdfParams['id_mapel'] = $filter_info['id_mapel'];
            }
            $pdfUrl = $_SERVER['REQUEST_URI'];
            if (strpos($pdfUrl, 'pdf=1') === false) {
                $separator = (strpos($pdfUrl, '?') !== false) ? '&' : '?';
                $pdfUrl .= $separator . 'pdf=1';
            }
            ?>
            <a class="btn" href="<?= htmlspecialchars($pdfUrl); ?>">
                <span class="btn-icon">📄</span>
                Unduh PDF
            </a>
            <a class="btn" href="<?= htmlspecialchars($backUrl); ?>">
                <span class="btn-icon">←</span>
                Kembali ke Rincian Absen
            </a>
        </div>
    </div>
    <?php endif; ?>

<div class="main-content">
  <!-- Header -->
  <div class="header-section">
    <div class="header-title">Rekapitulasi Absen Siswa</div>
    <div class="header-subtitle"><?= $periode_text; ?></div>

    <table class="info-table">
      <tr>
        <td class="info-label">Mata Pelajaran</td><td class="info-colon">:</td>
        <td><?= htmlspecialchars($mapel_info['nama_mapel'] ?? '-'); ?></td>
        <td width="100"></td>
        <td class="info-label">Kelas</td><td class="info-colon">:</td>
        <td><?= htmlspecialchars($mapel_info['nama_kelas'] ?? '-'); ?></td>
      </tr>
      <tr>
        <td class="info-label">Guru Pengampu</td><td class="info-colon">:</td>
        <td><?= htmlspecialchars($mapel_info['nama_guru'] ?? '-'); ?></td>
        <td width="100"></td>
        <td class="info-label">Semester</td><td class="info-colon">:</td>
        <td><?= htmlspecialchars($mapel_info['semester'] ?? '-'); ?></td>
      </tr>
    </table>
  </div>

  <?php $chunkIndex = 0; foreach ($chunks as $chunk): ?>
  <div class="table-wrap <?= $chunkIndex > 0 ? 'page-break' : '' ?>">
    <table>
      <!-- COLGROUP: No/NISN shrink; Nama fixed; ringkasan fixed; pertemuan fleksibel -->
      <colgroup>
        <col class="w-min w-nowrap"><!-- No -->
        <col class="w-min w-nowrap"><!-- NISN -->
        <col class="col-student"><!-- Nama Siswa (fixed px) -->

        <!-- Rangkuman H/I/S/A/% dipindah di sebelah Nama -->
        <col class="col-summary w-nowrap"><!-- H -->
        <col class="col-summary w-nowrap"><!-- I -->
        <col class="col-summary w-nowrap"><!-- S -->
        <col class="col-summary w-nowrap"><!-- A -->
        <col class="col-summary w-nowrap"><!-- % -->

        <!-- Pertemuan: auto-scaling sisa lebar -->
        <?php foreach ($chunk as $_): ?>
          <col><!-- Pertemuan -->
        <?php endforeach; ?>
      </colgroup>

      <thead>
        <tr>
          <th rowspan="2" class="w-nowrap">No</th>
          <th rowspan="2" class="w-nowrap">NISN</th>
          <th rowspan="2" class="th-student">Nama Siswa</th>

          <!-- HISA% di sebelah Nama -->
          <th rowspan="2" class="w-nowrap">H</th>
          <th rowspan="2" class="w-nowrap">I</th>
          <th rowspan="2" class="w-nowrap">S</th>
          <th rowspan="2" class="w-nowrap">A</th>
          <th rowspan="2" class="w-nowrap">%</th>

          <!-- Setelah itu kolom Pertemuan (nomor) -->
          <?php foreach ($chunk as $i => $pertemuan): ?>
            <th class="w-nowrap"><?= ($i + 1) + ($chunkIndex * 10); ?></th>
          <?php endforeach; ?>
        </tr>
        <tr>
          <!-- Subheader tanggal untuk kolom pertemuan -->
          <?php foreach ($chunk as $pertemuan): ?>
            <th class="w-nowrap" style="font-size:8px;"><?= date('d M Y', strtotime($pertemuan['tanggal'])); ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>

      <tbody>
        <?php
        $no = 1;
        foreach ($rincian_data['siswa_data'] as $siswa):
          $total_pertemuan = count($pertemuan_headers);
          $persentase_hadir = $total_pertemuan > 0 ? round(($siswa['total_hadir'] / $total_pertemuan) * 100, 1) : 0;
        ?>
        <tr>
          <td class="w-nowrap"><?= $no++; ?></td>
          <td class="w-nowrap"><?= htmlspecialchars($siswa['nisn'] ?? '-'); ?></td>
          <td class="td-student"><?= htmlspecialchars($siswa['nama_siswa']); ?></td>

          <!-- Rangkuman H/I/S/A/% -->
          <td class="summary-cell summary-H w-nowrap"><?= $siswa['total_hadir']; ?></td>
          <td class="summary-cell summary-I w-nowrap"><?= $siswa['total_izin']; ?></td>
          <td class="summary-cell summary-S w-nowrap"><?= $siswa['total_sakit']; ?></td>
          <td class="summary-cell summary-A w-nowrap"><?= $siswa['total_alpha']; ?></td>
          <td class="percentage-cell w-nowrap"><?= $persentase_hadir; ?>%</td>

          <!-- Status pertemuan untuk chunk ini -->
          <?php
          foreach ($chunk as $pertemuan) {
            $match = null;
            if (!empty($siswa['pertemuan'])) {
              foreach ($siswa['pertemuan'] as $p) {
                if ($p['pertemuan_ke'] == $pertemuan['pertemuan_ke'] && $p['tanggal'] == $pertemuan['tanggal']) { $match = $p; break; }
              }
            }
            $status = $match['status'] ?? 'A';
            echo '<td class="status-cell status-'.htmlspecialchars($status).'">'.getStatusSymbol($status).'</td>';
          }
          ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php $chunkIndex++; endforeach; ?>

  <!-- Footer -->
  <div class="footer-section">
    <table class="footer-table">
      <tr>
        <td style="width:60%;">
          <div class="keterangan">
            <div class="keterangan-title">Keterangan:</div>
            <div><span class="status-legend">H</span> = Hadir</div>
            <div><span class="status-legend">I</span> = Izin</div>
            <div><span class="status-legend">S</span> = Sakit</div>
            <div><span class="status-legend">A</span> = Alpha</div>
            <div style="font-size:9px; font-style:italic; margin-top:4px;">
              Persentase dihitung dari total pertemuan dalam periode yang dipilih
            </div>
          </div>
        </td>
        <td style="width:40%;">
          <div class="signature">
            <div class="signature-place">Mojokerto, <?= htmlspecialchars($filter_info['tanggal_cetak'] ?? date('d F Y')); ?></div>
            <div class="signature-role">Guru Mata Pelajaran</div>
            <div class="signature-name"><?= htmlspecialchars($mapel_info['nama_guru'] ?? '(Nama Guru)'); ?></div>
          </div>
        </td>
      </tr>
    </table>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener('beforeprint', function () {
      document.title = 'Rincian_Absen_<?= preg_replace('/[^A-Za-z0-9]/', '_', $mapel_info['nama_mapel'] ?? 'Mapel'); ?>_<?= date('Y-m-d'); ?>';
    });
  });
</script>
</body>
</html>