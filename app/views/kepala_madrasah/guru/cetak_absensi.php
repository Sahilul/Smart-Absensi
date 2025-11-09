<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $data['judul'] ?? 'Cetak Absensi'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto max-w-4xl my-8 bg-white p-8 shadow-lg">
        
        <div class="no-print mb-6 text-right">
            <button onclick="window.print()" class="bg-indigo-600 text-white py-2 px-4 rounded-lg">Cetak Halaman Ini</button>
        </div>

        <div class="text-center border-b-2 border-gray-800 pb-4 mb-6">
            <h1 class="text-2xl font-bold">Laporan Absensi Pertemuan</h1>
            <h2 class="text-xl">Aplikasi Absensi Siswa</h2>
        </div>

        <!-- Detail Jurnal -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Detail Pertemuan</h3>
            <table class="w-full text-sm">
                <tr>
                    <td class="font-semibold w-1/4 py-1">Mata Pelajaran</td>
                    <td>: <?= htmlspecialchars($data['jurnal']['nama_mapel']); ?></td>
                </tr>
                <tr>
                    <td class="font-semibold py-1">Kelas</td>
                    <td>: <?= htmlspecialchars($data['jurnal']['nama_kelas']); ?></td>
                </tr>
                <tr>
                    <td class="font-semibold py-1">Guru Pengajar</td>
                    <td>: <?= htmlspecialchars($_SESSION['nama_lengkap']); ?></td>
                </tr>
                <tr>
                    <td class="font-semibold py-1">Tanggal</td>
                    <td>: <?= date('d F Y', strtotime($data['jurnal']['tanggal'])); ?></td>
                </tr>
                <tr>
                    <td class="font-semibold py-1">Pertemuan Ke-</td>
                    <td>: <?= htmlspecialchars($data['jurnal']['pertemuan_ke']); ?></td>
                </tr>
                <tr>
                    <td class="font-semibold py-1 align-top">Topik Materi</td>
                    <td class="align-top">: <?= htmlspecialchars($data['jurnal']['topik_materi']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Daftar Absensi Siswa -->
        <div>
            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Daftar Kehadiran Siswa</h3>
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold border">No</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold border">NISN</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold border">Nama Siswa</th>
                        <th class="px-4 py-2 text-center text-sm font-semibold border">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold border">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($data['daftar_absensi'] as $absen) : ?>
                        <tr class="border-b">
                            <td class="px-4 py-2 border"><?= $no++; ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($absen['nisn']); ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($absen['nama_siswa']); ?></td>
                            <td class="px-4 py-2 text-center border font-bold"><?= htmlspecialchars($absen['status_kehadiran']); ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($absen['keterangan']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>