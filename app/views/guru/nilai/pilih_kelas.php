<?php
// File: app/views/guru/nilai/pilih_kelas.php
?>

<main class="container-fluid px-6 py-8">
  <!-- Header -->
  <div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
      <a href="<?= BASEURL; ?>/nilai/index" class="p-2 hover:bg-white/50 rounded-lg transition-colors">
        <i data-lucide="arrow-left" class="w-5 h-5 text-secondary-600"></i>
      </a>
      <div>
        <h1 class="text-3xl font-bold text-secondary-800">
          <i data-lucide="clipboard-edit" class="inline-block w-8 h-8 text-primary-600 mr-2"></i>
          Pilih Kelas untuk Input Nilai
        </h1>
        <p class="text-secondary-600 mt-1">Pilih kelas yang akan diinput nilainya</p>
      </div>
    </div>
  </div>

  <!-- Info Session -->
  <div class="glass-effect rounded-xl p-4 mb-6 border border-white/20">
    <div class="flex items-center gap-3">
      <div class="gradient-primary p-2 rounded-lg">
        <i data-lucide="calendar" class="w-5 h-5 text-white"></i>
      </div>
      <div>
        <p class="text-sm text-secondary-500">Semester Aktif</p>
        <p class="font-semibold text-secondary-800"><?= $_SESSION['nama_semester_aktif'] ?? ''; ?></p>
      </div>
    </div>
  </div>

  <!-- Daftar Kelas -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($data['jadwal_mengajar'])): ?>
      <?php foreach ($data['jadwal_mengajar'] as $jadwal): ?>
        <div class="glass-effect rounded-2xl p-6 border border-white/20 hover:shadow-xl transition-all duration-300 animate-slide-up">
          <!-- Header Kelas -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="gradient-primary p-3 rounded-xl">
                <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
              </div>
              <div>
                <h3 class="font-bold text-lg text-secondary-800"><?= htmlspecialchars($jadwal['nama_mapel']); ?></h3>
                <p class="text-sm text-secondary-600"><?= htmlspecialchars($jadwal['nama_kelas']); ?></p>
              </div>
            </div>
          </div>

          <!-- Info Tambahan -->
          <div class="space-y-2 mb-4">
            <div class="flex items-center text-sm text-secondary-600">
              <i data-lucide="users" class="w-4 h-4 mr-2"></i>
              <span>Jumlah Siswa: <strong><?= $jadwal['jumlah_siswa'] ?? '0'; ?></strong></span>
            </div>
            <div class="flex items-center text-sm text-secondary-600">
              <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
              <span>Hari: <strong><?= htmlspecialchars($jadwal['hari'] ?? '-'); ?></strong></span>
            </div>
          </div>

          <!-- Tombol Aksi -->
          <?php 
          $jenis = $data['jenis_nilai'] ?? null;
          if ($jenis === 'harian'): ?>
            <!-- Hanya tombol Harian -->
            <a href="<?= BASEURL; ?>/nilai/tugasHarian?id_penugasan=<?= $jadwal['id_penugasan']; ?>" 
               class="btn-warning w-full text-center hover:scale-105 transition-transform flex items-center justify-center py-3">
              <i data-lucide="file-text" class="w-5 h-5 mr-2"></i>
              <span>Input Nilai Harian</span>
            </a>
          <?php elseif ($jenis === 'sts'): ?>
            <!-- Hanya tombol STS -->
            <a href="<?= BASEURL; ?>/nilai/tengahSemester?id_penugasan=<?= $jadwal['id_penugasan']; ?>" 
               class="btn-info w-full text-center hover:scale-105 transition-transform flex items-center justify-center py-3">
              <i data-lucide="book" class="w-5 h-5 mr-2"></i>
              <span>Input Nilai STS</span>
            </a>
          <?php elseif ($jenis === 'sas'): ?>
            <!-- Hanya tombol SAS -->
            <a href="<?= BASEURL; ?>/nilai/akhirSemester?id_penugasan=<?= $jadwal['id_penugasan']; ?>" 
               class="btn-success w-full text-center hover:scale-105 transition-transform flex items-center justify-center py-3">
              <i data-lucide="award" class="w-5 h-5 mr-2"></i>
              <span>Input Nilai SAS</span>
            </a>
          <?php else: ?>
            <!-- Default: tampilkan semua tombol -->
            <div class="grid grid-cols-3 gap-2">
              <!-- Input Tugas Harian -->
              <a href="<?= BASEURL; ?>/nilai/tugasHarian?id_penugasan=<?= $jadwal['id_penugasan']; ?>" 
                 class="btn-warning text-xs py-2 px-3 text-center hover:scale-105 transition-transform flex flex-col items-center justify-center">
                <i data-lucide="file-text" class="w-4 h-4 mb-1"></i>
                <span>Harian</span>
              </a>

              <!-- Input STS -->
              <a href="<?= BASEURL; ?>/nilai/tengahSemester?id_penugasan=<?= $jadwal['id_penugasan']; ?>" 
                 class="btn-info text-xs py-2 px-3 text-center hover:scale-105 transition-transform flex flex-col items-center justify-center">
                <i data-lucide="book" class="w-4 h-4 mb-1"></i>
                <span>STS</span>
              </a>
              
              <!-- Input SAS -->
              <a href="<?= BASEURL; ?>/nilai/akhirSemester?id_penugasan=<?= $jadwal['id_penugasan']; ?>" 
                 class="btn-success text-xs py-2 px-3 text-center hover:scale-105 transition-transform flex flex-col items-center justify-center">
                <i data-lucide="award" class="w-4 h-4 mb-1"></i>
                <span>SAS</span>
              </a>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-span-full glass-effect rounded-xl p-12 text-center border border-white/20">
        <i data-lucide="inbox" class="w-16 h-16 mx-auto text-secondary-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-secondary-800 mb-2">Belum Ada Kelas Mengajar</h3>
        <p class="text-secondary-600 mb-4">Anda belum ditugaskan untuk mengajar di semester ini.</p>
        <a href="<?= BASEURL; ?>/nilai/index" class="btn-primary inline-flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
          Kembali
        </a>
      </div>
    <?php endif; ?>
  </div>
</main>
