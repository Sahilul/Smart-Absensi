<?php
// File: app/views/guru/nilai/index.php
?>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-4 sm:p-6">
  <!-- Breadcrumb -->
  <div class="mb-4 sm:mb-6">
    <nav class="flex items-center space-x-2 text-sm text-secondary-600">
      <a href="<?= BASEURL; ?>/guru/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-secondary-800 font-medium">Nilai</span>
    </nav>
  </div>

  <!-- Header -->
  <div class="mb-6 sm:mb-8">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-3 sm:space-x-4">
        <a href="<?= BASEURL; ?>/guru/dashboard" class="p-2 rounded-xl text-secondary-500 hover:text-primary-600 hover:bg-white/50 transition-all duration-200">
          <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
          <h2 class="text-2xl font-bold text-secondary-800 flex items-center">
            <i data-lucide="book-open" class="w-7 h-7 mr-2 text-success-500"></i>
            Nilai Siswa
          </h2>
          <p class="text-secondary-600 mt-1">Kelola nilai tugas harian, tengah semester, dan akhir semester</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Card: Tugas Harian -->
    <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up">
      <div class="flex items-center justify-between mb-4">
        <div class="gradient-warning p-3 rounded-xl">
          <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
        </div>
        <span class="status-badge bg-warning-100 text-warning-800">Tugas Harian</span>
      </div>
      <h3 class="text-2xl font-bold text-secondary-800 mb-1">Tugas Harian</h3>
      <p class="text-sm text-secondary-600">Input dan lihat nilai tugas harian setiap pertemuan.</p>
      <?php if (!empty($data['nilai_tugas_harian'])): ?>
        <div class="mt-4 pt-4 border-t border-gray-100">
          <a href="<?= BASEURL; ?>/nilai/tugasHarian?id_jurnal=<?= $data['nilai_tugas_harian'][0]['id_jurnal']; ?>&id_penugasan=<?= $data['nilai_tugas_harian'][0]['id_penugasan']; ?>" class="btn-primary w-full inline-flex items-center justify-center gap-2">
            <i data-lucide="edit-3" class="w-5 h-5"></i> Lihat Detail
          </a>
        </div>
      <?php else: ?>
        <div class="mt-4 pt-4 border-t border-gray-100">
          <p class="text-center text-secondary-500">Belum ada nilai tugas harian.</p>
          <!-- Tombol Input Baru -->
          <a href="<?= BASEURL; ?>/nilai/pilihKelas" class="btn-primary w-full inline-flex items-center justify-center gap-2 mt-3">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Input Nilai Baru
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Card: Tengah Semester -->
    <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
      <div class="flex items-center justify-between mb-4">
        <div class="gradient-info p-3 rounded-xl">
          <i data-lucide="book" class="w-6 h-6 text-white"></i>
        </div>
        <span class="status-badge bg-info-100 text-info-800">Tengah Semester</span>
      </div>
      <h3 class="text-2xl font-bold text-secondary-800 mb-1">Tengah Semester</h3>
      <p class="text-sm text-secondary-600">Input dan lihat nilai ujian tengah semester.</p>
      <?php if (!empty($data['nilai_tengah_semester'])): ?>
        <div class="mt-4 pt-4 border-t border-gray-100">
          <a href="<?= BASEURL; ?>/nilai/tengahSemester?id_jurnal=<?= $data['nilai_tengah_semester'][0]['id_jurnal']; ?>&id_penugasan=<?= $data['nilai_tengah_semester'][0]['id_penugasan']; ?>" class="btn-primary w-full inline-flex items-center justify-center gap-2">
            <i data-lucide="edit-3" class="w-5 h-5"></i> Lihat Detail
          </a>
        </div>
      <?php else: ?>
        <div class="mt-4 pt-4 border-t border-gray-100">
          <p class="text-center text-secondary-500">Belum ada nilai tengah semester.</p>
          <!-- Tombol Input Baru -->
          <a href="<?= BASEURL; ?>/nilai/pilihKelas" class="btn-primary w-full inline-flex items-center justify-center gap-2 mt-3">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Input Nilai Baru
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Card: Akhir Semester -->
    <div class="glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
      <div class="flex items-center justify-between mb-4">
        <div class="gradient-success p-3 rounded-xl">
          <i data-lucide="award" class="w-6 h-6 text-white"></i>
        </div>
        <span class="status-badge bg-success-100 text-success-800">Akhir Semester</span>
      </div>
      <h3 class="text-2xl font-bold text-secondary-800 mb-1">Akhir Semester</h3>
      <p class="text-sm text-secondary-600">Input dan lihat nilai ujian akhir semester.</p>
      <?php if (!empty($data['nilai_akhir_semester'])): ?>
        <div class="mt-4 pt-4 border-t border-gray-100">
          <a href="<?= BASEURL; ?>/nilai/akhirSemester?id_jurnal=<?= $data['nilai_akhir_semester'][0]['id_jurnal']; ?>&id_penugasan=<?= $data['nilai_akhir_semester'][0]['id_penugasan']; ?>" class="btn-primary w-full inline-flex items-center justify-center gap-2">
            <i data-lucide="edit-3" class="w-5 h-5"></i> Lihat Detail
          </a>
        </div>
      <?php else: ?>
        <div class="mt-4 pt-4 border-t border-gray-100">
          <p class="text-center text-secondary-500">Belum ada nilai akhir semester.</p>
          <!-- Tombol Input Baru -->
          <a href="<?= BASEURL; ?>/nilai/pilihKelas" class="btn-primary w-full inline-flex items-center justify-center gap-2 mt-3">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Input Nilai Baru
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>