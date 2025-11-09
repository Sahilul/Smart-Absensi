<?php
// File: app/views/admin/cetak_laporan_rekap.php
// Template cetak untuk laporan rekap absensi admin

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Kehadiran Siswa - <?= htmlspecialchars($kelas_info['nama_kelas'] ?? ''); ?></title>
<style>
    /* ===== Halaman & Global ===== */
    @page { size: A4 portrait; margin: 15mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: "Arial", "DejaVu Sans", sans-serif;
        color: #000; font-size: 12px; line-height: 1.4; background: #fff;
    }

    /* ===== Action bar (screen only) ===== */
    .actions { 
        position: fixed; top: 0; left: 0; right: 0; 
        background: #f8f9fa; padding: 8px 12px; 
        border-bottom: 1px solid #dee2e6; z-index: 1000; 
    }
    .actions-buttons { display: flex; gap: 8px; }
    .btn { 
        display: inline-flex; align-items: center; gap: 6px; 
        padding: 6px 12px; border-radius: 4px; text-decoration: none; 
        font-weight: 500; font-size: 12px; border: 1px solid #ccc; 
        background: #fff; color: #333; 
    }
    .btn:hover { background: #f0f0f0; }

    /* ===== Layout ===== */
    .main-content { margin-top: 52px; padding: 20px; }

    /* ===== Header ===== */
    .header-section { text-align: center; margin-bottom: 25px; }
    .header-title { 
        font-size: 16px; font-weight: bold; margin-bottom: 15px; 
        text-transform: uppercase; letter-spacing: 1px; 
    }
    .header-subtitle { font-size: 12px; margin-bottom: 15px; color: #555; }
    
    .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
    .info-table td { padding: 3px 0; vertical-align: top; border: none; }
    .info-label { width: 150px; font-weight: bold; }
    .info-colon { width: 20px; text-align: center; }

    /* ===== Tabel Data ===== */
    .data-table { 
        width: 100%; border-collapse: collapse; margin-bottom: 30px; 
    }
    .data-table th, .data-table td { 
        border: 1px solid #000; padding: 8px; text-align: center; 
        vertical-align: middle; 
    }
    .data-table th { 
        background: #f5f5f5; font-weight: bold; font-size: 11px; 
    }
    .data-table .text-left { text-align: left; }

    /* ===== Footer ===== */
    .footer-section { margin-top: 40px; }
    .footer-table { width: 100%; border-collapse: collapse; border: none; }
    .footer-table td { border: none; vertical-align: top; padding: 10px; }
    
    .signature { text-align: center; min-width: 200px; }
    .signature-place { margin-bottom: 15px; font-size: 11px; }
    .signature-role { margin-bottom: 60px; font-size: 11px; }
    .signature-name { 
        font-weight: bold; font-size: 12px; 
        border-bottom: 1px solid #000; padding-bottom: 2px; 
        display: inline-block; min-width: 150px;
    }

    /* ===== Print ===== */
    @media print {
        .actions { display: none !important; }
        body { font-size: 10px; margin: 0; padding: 0; }
        .main-content { margin-top: 0; padding: 0; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    }

    @media (max-width: 1200px) { 
        .table-wrap { overflow-x: auto; } 
    }
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
            // Link PDF
            $currentParams = $_GET;
            $currentParams['pdf'] = 1;
            $pdfUrl = BASEURL . '/admin/cetakLaporanRekap?' . http_build_query($currentParams);
            ?>
            <a class="btn" href="<?= htmlspecialchars($pdfUrl); ?>">
                <span class="btn-icon">📄</span>
                Unduh PDF
            </a>
            <a class="btn" href="<?= BASEURL; ?>/admin/laporan">
                <span class="btn-icon">←</span>
                Kembali
            </a>
        </div>
    </div>
    <?php endif; ?>

<div class="main-content">
    <!-- Header -->
    <div class="header-section">
        <div class="header-title">Laporan Kehadiran Siswa</div>
        <?php if (!empty($periode_text)): ?>
            <div class="header-subtitle"><?= $periode_text; ?></div>
        <?php endif; ?>
        
        <table class="info-table">
            <tr>
                <td class="info-label">KELAS</td>
                <td class="info-colon">:</td>
                <td><?= htmlspecialchars($kelas_info['nama_kelas'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="info-label">MATA PELAJARAN</td>
                <td class="info-colon">:</td>
                <td><?= htmlspecialchars($mapel_info['nama_mapel'] ?? 'Semua Mata Pelajaran'); ?></td>
            </tr>
            <tr>
                <td class="info-label">NAMA GURU</td>
                <td class="info-colon">:</td>
                <td><?= htmlspecialchars($guru_info['nama_guru'] ?? '(diisi guru mata pelajaran)'); ?></td>
            </tr>
            <tr>
                <td class="info-label">SEMESTER</td>
                <td class="info-colon">:</td>
                <td><?= htmlspecialchars($semester_info['semester'] ?? $_SESSION['nama_semester_aktif']); ?></td>
            </tr>
            <tr>
                <td class="info-label">TAHUN PELAJARAN</td>
                <td class="info-colon">:</td>
                <td><?= htmlspecialchars($tp_info['nama_tp'] ?? '(diisi tahun pelajaran)'); ?></td>
            </tr>
        </table>
    </div>

    <!-- Tabel Data Kehadiran -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 200px;">Nama Siswa</th>
                <th style="width: 60px;">Hadir</th>
                <th style="width: 60px;">Izin</th>
                <th style="width: 60px;">Sakit</th>
                <th style="width: 60px;">Alpha</th>
                <th style="width: 80px;">Kehadiran %</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($rekap_absensi) && !empty($rekap_absensi)): ?>
                <?php foreach ($rekap_absensi as $index => $rekap): ?>
                    <?php
                        $total_pertemuan = $rekap['hadir'] + $rekap['izin'] + $rekap['sakit'] + $rekap['alfa'];
                        $persentase_hadir = ($total_pertemuan > 0) ? round(($rekap['hadir'] / $total_pertemuan) * 100) : 0;
                    ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td class="text-left"><?= htmlspecialchars($rekap['nama_siswa']); ?></td>
                        <td><?= $rekap['hadir']; ?></td>
                        <td><?= $rekap['izin']; ?></td>
                        <td><?= $rekap['sakit']; ?></td>
                        <td><?= $rekap['alfa']; ?></td>
                        <td><?= $persentase_hadir; ?>%</td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Tambahan baris kosong untuk template standar -->
                <?php 
                $jumlah_siswa = count($rekap_absensi);
                $min_rows = 15; // Minimal 15 baris untuk template
                for($i = $jumlah_siswa; $i < $min_rows; $i++): 
                ?>
                    <tr>
                        <td><?= $i + 1; ?></td>
                        <td class="text-left"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endfor; ?>
            <?php else: ?>
                <!-- Jika tidak ada data, tampilkan baris kosong sesuai template -->
                <?php for($i = 1; $i <= 15; $i++): ?>
                    <tr>
                        <td><?= $i; ?></td>
                        <td class="text-left"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endfor; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer dengan Tanda Tangan -->
    <div class="footer-section">
        <table class="footer-table">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <div class="signature">
                        <div class="signature-place">Mojokerto, <?= $filter_info['tanggal_cetak'] ?? date('d F Y'); ?></div>
                        <div class="signature-role">Guru Mata Pelajaran</div>
                        <div style="height: 60px;"></div>
                        <div class="signature-name"><?= htmlspecialchars($guru_info['nama_guru'] ?? '(diisi guru mata pelajaran)'); ?></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Set document title untuk PDF
    window.addEventListener('beforeprint', function () {
        const kelas = '<?= preg_replace('/[^A-Za-z0-9]/', '_', $kelas_info['nama_kelas'] ?? 'Kelas'); ?>';
        const mapel = '<?= preg_replace('/[^A-Za-z0-9]/', '_', $mapel_info['nama_mapel'] ?? 'Semua_Mapel'); ?>';
        const tanggal = '<?= date('Y-m-d'); ?>';
        document.title = `Laporan_Kehadiran_${kelas}_${mapel}_${tanggal}`;
    });
});
</script>
</body>
</html>