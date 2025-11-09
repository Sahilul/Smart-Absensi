<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Welcome Header (Simplified) -->
    <div class="mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-white/20 shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-800 mb-2">
                        Selamat Datang, <?= $_SESSION['nama_lengkap']; ?>!
                    </h1>
                    <p class="text-secondary-600 font-medium">
                        <i data-lucide="calendar-check" class="w-4 h-4 inline mr-2"></i>
                        Sesi Aktif: <span class="gradient-success text-white px-3 py-1 rounded-lg text-sm font-semibold"><?= $_SESSION['nama_semester_aktif']; ?></span>
                    </p>
                    <p class="text-sm text-secondary-500 mt-2">Mari kelola jurnal dan absensi kelas Anda dengan mudah</p>
                </div>
                <div class="hidden md:block">
                    <div class="gradient-success p-4 rounded-2xl shadow-lg">
                        <i data-lucide="book-open-check" class="w-12 h-12 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Penugasan -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up">
                <div class="flex items-center justify-between mb-4">
                    <div class="gradient-primary p-3 rounded-xl">
                        <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="status-badge bg-primary-100 text-primary-800">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-1"><?= $data['total_penugasan'] ?? 0; ?></h3>
                <p class="text-sm text-secondary-600">Mata Pelajaran Diampu</p>
            </div>

            <!-- Total Hari Mengajar -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="gradient-warning p-3 rounded-xl">
                        <i data-lucide="calendar-days" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="status-badge bg-warning-100 text-warning-800">Hari</span>
                </div>
                <h3 class="text-2xl font-bold text-secondary-800 mb-1"><?= $data['total_hari_mengajar'] ?? 0; ?></h3>
                <p class="text-sm text-secondary-600">Hari Mengajar</p>
            </div>

            <!-- Mengajar Kelas & Mapel -->
            <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="gradient-success p-3 rounded-xl">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="status-badge bg-success-100 text-success-800">Mengajar</span>
                </div>
                <div class="space-y-2 max-h-24 overflow-y-auto">
                    <?php if (!empty($data['kelas_mapel_info'])): ?>
                        <?php foreach ($data['kelas_mapel_info'] as $info): ?>
                            <div class="text-sm bg-white/30 px-2 py-1 rounded">
                                <span class="font-semibold text-secondary-800"><?= htmlspecialchars($info['nama_kelas']); ?></span>
                                <span class="text-secondary-600"> - <?= htmlspecialchars($info['nama_mapel']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-secondary-600">Belum ada penugasan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    // ===== Helper functions dari jurnal.php =====
    function themeByKelas($nama_kelas) {
        $token = strtoupper(trim(strtok((string)$nama_kelas, ' '))); // VII/VIII/IX/X/XI/XII
        $isLower = in_array($token, ['VII','VIII','IX'], true) || preg_match('/^\s*[7-9]\b/', (string)$nama_kelas);
        return $isLower
            ? ['header' => 'from-emerald-500 to-teal-500', 'badge' => 'badge-smp']
            : ['header' => 'from-indigo-500 to-violet-500', 'badge' => 'badge-sma'];
    }

    function kelasSortKey($kelas) {
        $s = trim((string)$kelas);
        // 1) Romawi
        if (preg_match('/\b(VII|VIII|IX|X|XI|XII)\b/i', $s, $m)) {
            $map = ['VII'=>7,'VIII'=>8,'IX'=>9,'X'=>10,'XI'=>11,'XII'=>12];
            $g = $map[strtoupper($m[1])] ?? 99;
            // buang token di depan utk suffix
            $suffix = trim(preg_replace('/^(VII|VIII|IX|X|XI|XII)\s*/i', '', $s, 1));
            return [$g, $suffix ?: $s];
        }
        // 2) Angka di depan (7-12)
        if (preg_match('/^\s*(\d{1,2})\b/', $s, $m)) {
            $n = (int)$m[1];
            if ($n >= 7 && $n <= 12) {
                $suffix = trim(preg_replace('/^\s*\d{1,2}\s*/', '', $s, 1));
                return [$n, $suffix ?: $s];
            }
        }
        // 3) Tidak terdeteksi → taruh di akhir
        return [99, $s];
    }

    function cmpKelas($a, $b) {
        [$ga,$sa] = kelasSortKey($a);
        [$gb,$sb] = kelasSortKey($b);
        if ($ga !== $gb) return $ga <=> $gb;
        $cmp = strnatcasecmp($sa, $sb);
        if ($cmp !== 0) return $cmp;
        return strnatcasecmp((string)$a, (string)$b);
    }

    // ===== Hitung jumlah pertemuan (1x query) & siapkan data =====
    $jumlahPertemuanByPenugasan = [];
    $maxPertemuanByPenugasan    = [];
    $kelasOptions = [];
    $jadwalSorted = $data['jadwal_mengajar'] ?? [];

    if (!empty($jadwalSorted)) {
        // 1) Ambil id_penugasan & kumpulkan kelas unik
        $ids = [];
        $kelasSetAssoc = [];
        foreach ($jadwalSorted as $row) {
            if (isset($row['id_penugasan'])) $ids[] = (int)$row['id_penugasan'];
            if (!empty($row['nama_kelas']))  $kelasSetAssoc[$row['nama_kelas']] = true;
        }
        // 2) Ambil jumlah pertemuan per id_penugasan
        $ids = array_values(array_unique(array_filter($ids)));
        if (!empty($ids)) {
            require_once APPROOT . '/app/core/Database.php';
            $db = new Database;
            $in = implode(',', $ids);
            $sql = "
                SELECT id_penugasan,
                       COUNT(*) AS total_pertemuan,
                       COALESCE(MAX(pertemuan_ke),0) AS max_pertemuan
                FROM jurnal
                WHERE id_penugasan IN ($in)
                GROUP BY id_penugasan
            ";
            $db->query($sql);
            $rows = $db->resultSet();
            foreach ($rows as $r) {
                $pid = (int)$r['id_penugasan'];
                $jumlahPertemuanByPenugasan[$pid] = (int)$r['total_pertemuan'];
                $maxPertemuanByPenugasan[$pid]    = (int)$r['max_pertemuan'];
            }
        }
        // 3) Urutkan daftar kelas (filter) mulai VII→XII
        $kelasOptions = array_keys($kelasSetAssoc);
        usort($kelasOptions, 'cmpKelas');

        // 4) Urutkan kartu berdasarkan nama_kelas (VII→XII), lalu nama mapel
        usort($jadwalSorted, function($A, $B) {
            $ka = $A['nama_kelas'] ?? '';
            $kb = $B['nama_kelas'] ?? '';
            $c = cmpKelas($ka, $kb);
            if ($c !== 0) return $c;
            return strnatcasecmp($A['nama_mapel'] ?? '', $B['nama_mapel'] ?? '');
        });
    }
    ?>

    <!-- Class Selection + Filter -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-secondary-800 flex items-center">
                <i data-lucide="school" class="w-5 h-5 mr-2 text-success-500"></i>
                Kelas Mengajar
            </h3>
            <span class="status-badge bg-primary-100 text-primary-800">
                <?= count($data['jadwal_mengajar'] ?? []); ?> Kelas Tersedia
            </span>
        </div>

        <?php if (empty($jadwalSorted)): ?>
            <!-- Empty State -->
            <div class="glass-effect rounded-2xl p-12 border border-white/20 shadow-lg text-center animate-fade-in">
                <div class="max-w-md mx-auto">
                    <div class="gradient-warning p-4 rounded-2xl inline-flex mb-6">
                        <i data-lucide="calendar-x" class="w-12 h-12 text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-800 mb-3">Belum Ada Jadwal Mengajar</h3>
                    <p class="text-secondary-600 mb-6">
                        Anda belum memiliki jadwal mengajar untuk semester ini.
                        Silakan hubungi admin untuk mendapatkan penugasan kelas.
                    </p>
                    <div class="space-y-3">
                        <button onclick="window.location.reload()" class="btn-secondary w-full">
                            <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>Refresh Halaman
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>

            <!-- FILTER KELAS (otomatis & terurut VII→XII) -->
            <div class="glass-effect rounded-xl p-3 border border-white/20 shadow mb-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <label for="filter-kelas" class="text-sm font-medium text-secondary-700 flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4 text-secondary-500"></i>
                        Filter Kelas
                    </label>
                    <div class="relative w-full sm:w-72">
                        <select id="filter-kelas" class="filter-select">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($kelasOptions as $k): ?>
                                <option value="<?= htmlspecialchars($k) ?>"><?= htmlspecialchars($k) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                            <i data-lucide="chevron-down" class="w-4 h-4 text-secondary-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRID KARTU (sudah terurut) -->
            <div id="cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($jadwalSorted as $index => $jadwal): ?>
                    <?php
                        $pid   = (int)$jadwal['id_penugasan'];
                        $mapel = $jadwal['nama_mapel'] ?? '-';
                        $kelas = $jadwal['nama_kelas'] ?? '-';
                        $id_mapel = $jadwal['id_mapel'] ?? '';

                        $theme = themeByKelas($kelas);
                        $total = $jumlahPertemuanByPenugasan[$pid] ?? 0;
                        $kelasAttr = strtolower(trim($kelas));
                    ?>
                    <div class="class-card rounded-xl border border-white/20 shadow-md overflow-hidden card-hover group animate-slide-up"
                         data-kelas-label="<?= htmlspecialchars($kelas) ?>"
                         data-kelas="<?= htmlspecialchars($kelasAttr) ?>"
                         style="animation-delay: <?= $index * 0.06; ?>s;">
                        <!-- Header: Mapel di tengah atas -->
                        <div class="relative p-4 text-white bg-gradient-to-r <?= $theme['header']; ?>">
                            <!-- Badge jumlah pertemuan (pojok kanan) -->
                            <span class="pertemuan-badge <?= $theme['badge']; ?>" title="Total pertemuan berjalan">
                                <?= $total; ?>x
                            </span>
                            <h3 class="text-center text-lg sm:text-xl font-bold leading-tight">
                                <?= htmlspecialchars($mapel); ?>
                            </h3>
                        </div>

                        <!-- Konten: Kelas besar + info -->
                        <div class="p-4">
                            <div class="text-center">
                                <div class="text-lg sm:text-xl font-semibold text-secondary-800">
                                    <?= htmlspecialchars($kelas); ?>
                                </div>
                                <div class="mt-1 text-sm text-secondary-600 flex items-center justify-center gap-1">
                                    <i data-lucide="calendar-range" class="w-4 h-4 text-secondary-400"></i>
                                    <span><b><?= $total; ?></b> pertemuan</span>
                                </div>
                            </div>

                            <!-- Tombol besar: Buat Jurnal -->
                            <a href="<?= BASEURL; ?>/guru/tambahJurnal/<?= $pid; ?>"
                               class="btn-primary btn-primary-lg w-full mt-4 inline-flex items-center justify-center gap-2">
                                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                                Buat Jurnal
                            </a>

                            <!-- Action Icons Row -->
                            <?php if ($total > 0): // Hanya tampilkan jika ada pertemuan ?>
                            <div class="flex items-center justify-center gap-3 mt-3 pt-3 border-t border-gray-100">
                                <!-- Lihat Detail -->
                                <a href="<?= BASEURL; ?>/riwayatJurnal/detail/<?= $pid; ?>" 
                                   class="action-icon action-icon-primary" 
                                   title="Lihat Detail Riwayat">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>

                                <!-- By Date (Rincian per Pertemuan) -->
                                <a href="<?= BASEURL; ?>/guru/rincianAbsen/<?= $id_mapel; ?>" 
                                   class="action-icon action-icon-secondary" 
                                   title="Rincian Absen per Pertemuan">
                                    <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                </a>

                                <!-- Cetak -->
                                <button onclick="printPenugasan('<?= $pid; ?>')" 
                                        class="action-icon action-icon-success" 
                                        title="Cetak Laporan">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <?php else: ?>
                            <div class="mt-3 pt-3 border-t border-gray-100 text-center">
                                <p class="text-xs text-gray-500 italic">Buat jurnal pertama untuk mengakses riwayat</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  lucide.createIcons();

  // Hover lembut kartu
  document.querySelectorAll('.card-hover').forEach(card=>{
    card.addEventListener('mouseenter', ()=> card.style.transform='translateY(-6px) scale(1.015)');
    card.addEventListener('mouseleave', ()=> card.style.transform='translateY(0) scale(1)');
  });

  // === FILTER KELAS ===
  const select = document.getElementById('filter-kelas');
  const cards  = document.querySelectorAll('.class-card');

  function applyFilter() {
    const val = (select?.value || '').toLowerCase().trim();
    cards.forEach(card => {
      const kelas = (card.dataset.kelas || '');
      const show  = !val || kelas === val; // match exact nama_kelas (case-insensitive)
      card.style.display = show ? '' : 'none';
    });
  }

  if (select) {
    // Auto-select jika hanya 1 kelas unik (di luar "Semua Kelas")
    if (select.options.length === 2) select.selectedIndex = 1;
    applyFilter();
    select.addEventListener('change', applyFilter);
  }

  // Animate stats on load
  const stats = document.querySelectorAll('.text-2xl.font-bold.text-secondary-800');
  stats.forEach((stat, index) => {
    setTimeout(() => {
      stat.style.opacity = '0';
      stat.style.transform = 'scale(1.2)';
      stat.style.transition = 'all 0.3s ease';
      
      setTimeout(() => {
        stat.style.opacity = '1';
        stat.style.transform = 'scale(1)';
      }, 100);
    }, index * 200);
  });
});

// Function untuk cetak
function printPenugasan(penugasanId) {
  const url = '<?= BASEURL; ?>/riwayatJurnal/cetak/' + encodeURIComponent(penugasanId);
  const w = window.open(url, '_blank');
  if (!w || w.closed || typeof w.closed === 'undefined') {
    showNotification('Izinkan pop-up untuk menampilkan layar cetak.', 'warning');
    window.location.href = url;
  }
}

// Notification system (simplified version)
function showNotification(message, type = 'info') {
  alert(message);
}
</script>

<style>
/* Glass effect */
.glass-effect {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

/* Kartu & animasi */
.card-hover { transition: transform .18s ease, box-shadow .18s ease; }
.card-hover:hover { box-shadow: 0 8px 26px rgba(2,6,23,.08); }
@keyframes slideUp { from {opacity:0; transform: translateY(10px)} to {opacity:1; transform:none} }
.animate-slide-up{ animation: slideUp .28s ease both; }
@keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
.animate-fade-in{ animation: fadeIn .4s ease both; }

/* Gradients util */
.gradient-primary{ background: linear-gradient(135deg,#6366f1,#22d3ee); }
.gradient-success{ background: linear-gradient(135deg,#10b981,#34d399); }
.gradient-warning{ background: linear-gradient(135deg,#f59e0b,#fbbf24); }

/* Header warna berdasarkan jenjang */
.badge-smp { background: rgba(16,185,129,.18); color:#fff; border:1px solid rgba(255,255,255,.3); }
.badge-sma { background: rgba(99,102,241,.22); color:#fff; border:1px solid rgba(255,255,255,.3); }

/* Badge jumlah pertemuan di header */
.pertemuan-badge{
  position:absolute; right:.75rem; top:.6rem;
  font-size:.75rem; line-height:1;
  padding:.25rem .5rem; border-radius:999px;
}

/* Button Styles */
.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  font-weight: 600;
  color: white;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border: 1px solid rgba(59, 130, 246, 0.3);
  box-shadow: 0 6px 14px rgba(59, 130, 246, 0.15);
  transition: all 0.15s ease;
  white-space: nowrap;
  text-decoration: none;
}

.btn-primary:hover {
  filter: brightness(1.05);
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
}

.btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  font-weight: 600;
  color: #4338ca;
  background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
  border: 1px solid rgba(99, 102, 241, 0.25);
  box-shadow: 0 6px 14px rgba(99, 102, 241, 0.12);
  transition: all 0.15s ease;
  white-space: nowrap;
  text-decoration: none;
}

.btn-secondary:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(99, 102, 241, 0.18);
  background: linear-gradient(135deg, #c7d2fe, #a5b4fc);
}

/* Tombol 'Buat Jurnal' diperbesar */
.btn-primary-lg{
  font-size: .9375rem; /* 15px */
  padding: .75rem 1rem;
  border-radius: .9rem;
  font-weight: 700;
  box-shadow: 0 8px 20px rgba(59,130,246,.15);
  transition: transform .15s ease, box-shadow .15s ease;
}
.btn-primary-lg:hover{ transform: translateY(-1px); box-shadow: 0 12px 26px rgba(59,130,246,.22); }
.btn-primary-lg:active{ transform: translateY(0); }

/* Action Icons Styles */
.action-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  transition: all 0.15s ease;
  text-decoration: none;
}

.action-icon:hover {
  transform: translateY(-1px) scale(1.05);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.action-icon-primary {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
}

.action-icon-secondary {
  background: linear-gradient(135deg, #6366f1, #4338ca);
  color: white;
}

.action-icon-success {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white;
}

/* Select filter */
.filter-select{
  width:100%;
  appearance:none;
  -webkit-appearance:none;
  -moz-appearance:none;
  background:#fff;
  border:1px solid rgb(226,232,240);
  border-radius:.75rem;
  padding:.6rem 2.25rem .6rem .9rem; /* space untuk icon kanan */
  font-size:.925rem;
  color:#0f172a;
  outline:none;
  transition:border-color .18s ease, box-shadow .18s ease;
}
.filter-select:focus{
  border-color:#0ea5e9; box-shadow:0 0 0 4px rgba(14,165,233,.15);
}

/* Status badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}
</style>