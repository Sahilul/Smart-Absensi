<?php
// Helper tanggal Indonesia untuk tampilan kartu jurnal
if (!function_exists('tanggal_indo')) {
  function tanggal_indo($dateStr) {
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $ts = strtotime($dateStr);
    if (!$ts) return htmlspecialchars($dateStr);
    $h = $hari[(int)date('w', $ts)];
    $d = (int)date('j', $ts);
    $m = $bulan[(int)date('n', $ts)] ?? date('n', $ts);
    $y = date('Y', $ts);
    return "$h, $d $m $y";
  }
}
?>
<!-- Page Header -->
<div class="glass-effect rounded-2xl border border-white/20 shadow-xl p-6 mb-6 animate-slide-down">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div class="flex items-center space-x-4">
      <div class="icon-wrapper gradient-warning">
        <i data-lucide="book-open" class="w-7 h-7 text-white"></i>
      </div>
      <div>
        <h1 class="page-title mb-1">Pilih Jurnal - Nilai Harian</h1>
        <div class="flex flex-wrap items-center gap-3 text-sm">
          <div class="flex items-center text-secondary-600">
            <i data-lucide="book-marked" class="w-4 h-4 mr-1.5"></i>
            <span class="font-semibold"><?= htmlspecialchars($data['penugasan']['nama_mapel']); ?></span>
          </div>
          <span class="text-secondary-400">•</span>
          <div class="flex items-center text-secondary-600">
            <i data-lucide="users" class="w-4 h-4 mr-1.5"></i>
            <span class="font-semibold">Kelas <?= htmlspecialchars($data['penugasan']['nama_kelas']); ?></span>
          </div>
        </div>
      </div>
    </div>
    <a href="<?= BASEURL; ?>/guru" class="btn-secondary flex items-center">
      <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
      Kembali
    </a>
  </div>
</div>

<!-- Content -->
<div class="content-wrapper">
  <div class="max-w-5xl mx-auto">
    
    <?php if (empty($data['jurnal_list'])): ?>
      <!-- Empty State -->
      <div class="glass-effect rounded-2xl border border-white/20 shadow-xl p-12 text-center animate-fade-in">
        <div class="w-24 h-24 bg-gradient-to-br from-secondary-100 to-secondary-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
          <i data-lucide="inbox" class="w-12 h-12 text-secondary-400"></i>
        </div>
        <h3 class="text-2xl font-bold text-secondary-800 mb-3">Belum Ada Jurnal</h3>
        <p class="text-secondary-600 mb-8 max-w-md mx-auto">Anda belum membuat jurnal untuk kelas ini. Silakan buat jurnal terlebih dahulu sebelum input nilai.</p>
        <a href="<?= BASEURL; ?>/guru/jurnal" class="btn-primary inline-flex items-center">
          <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
          Buat Jurnal Baru
        </a>
      </div>
    <?php else: ?>
      <!-- Info Banner -->
      <div class="glass-effect rounded-xl border border-warning-200 bg-gradient-to-r from-warning-50 to-orange-50 p-4 mb-6 animate-fade-in">
        <div class="flex items-start gap-3">
          <div class="flex-shrink-0 mt-0.5">
            <i data-lucide="info" class="w-5 h-5 text-warning-600"></i>
          </div>
          <div class="flex-1">
            <p class="text-sm text-warning-800">
              <span class="font-semibold">Pilih jurnal pembelajaran</span> yang ingin Anda isi nilainya. Nilai harian akan tersimpan per jurnal/pertemuan.
            </p>
          </div>
        </div>
      </div>

      <!-- Jurnal List -->
      <div class="space-y-4 animate-fade-in">
        <?php foreach ($data['jurnal_list'] as $index => $jurnal): ?>
          <div class="glass-effect rounded-xl border <?= $jurnal['has_nilai'] ? 'border-success-300 bg-success-50/30' : 'border-white/20'; ?> shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden hover:-translate-y-1 group" style="animation-delay: <?= $index * 0.1; ?>s;">
            <div class="p-6">
              <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Left Content -->
                <div class="flex-1">
                  <div class="flex items-start gap-4 mb-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-14 h-14 bg-gradient-to-br from-warning-400 to-orange-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300 relative">
                      <span class="text-white font-bold text-xl"><?= $jurnal['pertemuan_ke']; ?></span>
                      <?php if ($jurnal['has_nilai']): ?>
                        <div class="absolute -top-2 -right-2 bg-success-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg">
                          <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-lg font-bold text-secondary-800">
                          Pertemuan Ke-<?= $jurnal['pertemuan_ke']; ?>
                        </h3>
                        <?php if ($jurnal['has_nilai']): ?>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success-100 text-success-700 border border-success-300">
                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                            <?= $jurnal['jumlah_nilai']; ?> nilai
                          </span>
                        <?php else: ?>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-600 border border-secondary-200">
                            <i data-lucide="circle" class="w-3 h-3 mr-1"></i>
                            Belum ada nilai
                          </span>
                        <?php endif; ?>
                      </div>
                      <div class="flex items-center text-sm text-secondary-500">
                        <span class="date-chip inline-flex items-center">
                          <i data-lucide="calendar" class="w-4 h-4 mr-1.5"></i>
                          <?= tanggal_indo($jurnal['tanggal']); ?>
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="bg-gradient-to-r from-secondary-50 to-warning-50 rounded-lg p-4 border border-secondary-100">
                    <div class="flex items-start gap-2">
                      <i data-lucide="book-text" class="w-5 h-5 text-warning-600 mt-0.5 flex-shrink-0"></i>
                      <div>
                        <p class="text-xs font-semibold text-secondary-500 uppercase tracking-wide mb-1">Topik Materi</p>
                        <p class="text-secondary-900 font-medium leading-relaxed"><?= htmlspecialchars($jurnal['topik_materi']); ?></p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Right Action -->
                <div class="flex lg:flex-col items-center justify-end gap-3">
                  <a href="<?= BASEURL; ?>/nilai/inputNilaiHarian?id_jurnal=<?= $jurnal['id_jurnal']; ?>" 
                     class="seg-btn <?= $jurnal['has_nilai'] ? 'seg-success' : 'seg-warning'; ?> whitespace-nowrap">
                    <div class="seg-left">
                      <span class="seg-icon">
                        <i data-lucide="<?= $jurnal['has_nilai'] ? 'edit' : 'edit-3'; ?>" class="w-5 h-5"></i>
                      </span>
                      <span class="seg-label"><?= $jurnal['has_nilai'] ? 'Edit Nilai' : 'Input Nilai'; ?></span>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 seg-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
  </div>
</div>

<script>
  // Initialize Lucide icons
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
</script>

<style>
/* Segmented button styles (match dashboard) */
.seg-btn {
  display:flex; align-items:center; justify-content:space-between; gap:.75rem;
  width:100%; padding:.7rem .9rem; border-radius:.9rem; border:1px solid rgba(0,0,0,.06);
  background:#fff; color:#0f172a; text-decoration:none; box-shadow:0 4px 14px rgba(2,6,23,.06);
  transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
}
.seg-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(2,6,23,.10); filter: brightness(1.02); }
.seg-left { display:inline-flex; align-items:center; gap:.6rem; }
.seg-label { font-size:.9rem; font-weight:700; letter-spacing:.2px; }
.seg-right { color: rgba(15,23,42,.55); }
.seg-icon { display:inline-flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:.65rem; color:#fff; }

.seg-warning { background: linear-gradient(135deg,#fff7ed,#ffedd5); border-color: rgba(245,158,11,.25); }
.seg-warning .seg-icon { background: linear-gradient(135deg,#f59e0b,#d97706); }
.seg-warning .seg-label { color:#92400e; }

.seg-success { background: linear-gradient(135deg,#ecfdf5,#d1fae5); border-color: rgba(34,197,94,.25); }
.seg-success .seg-icon { background: linear-gradient(135deg,#22c55e,#16a34a); }
.seg-success .seg-label { color:#065f46; }

/* Date chip */
.date-chip { padding:.25rem .55rem; border-radius:999px; border:1px solid rgba(234,88,12,.25); background:linear-gradient(135deg,#fff7ed,#fffbeb); color:#92400e; font-weight:600; }
.date-chip i { color:#ea580c; }
</style>
