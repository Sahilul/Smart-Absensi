<?php
// File: app/views/guru/nilai/nilai_tugas_harian.php

// Helper: Format tanggal Indonesia (contoh: Rabu, 6 November 2025)
if (!function_exists('tanggal_indo')) {
  function tanggal_indo($dateStr) {
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan = [
      1=>'Januari','Februari','Maret','April','Mei','Juni',
      'Juli','Agustus','September','Oktober','November','Desember'
    ];
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
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-4 sm:p-6">
  <!-- Breadcrumb -->
  <div class="mb-4 sm:mb-6">
    <nav class="flex items-center space-x-2 text-sm text-secondary-600">
      <a href="<?= BASEURL; ?>/guru/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <a href="<?= BASEURL; ?>/nilai" class="hover:text-primary-600 transition-colors">Nilai</a>
      <i data-lucide="chevron-right" class="w-4 h-4"></i>
      <span class="text-secondary-800 font-medium">Tugas Harian</span>
    </nav>
  </div>

  <!-- Header -->
  <div class="mb-8">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <a href="<?= BASEURL; ?>/nilai" class="p-2 rounded-xl text-secondary-500 hover:text-primary-600 hover:bg-white/50 transition-all duration-200">
          <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
          <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
            <i data-lucide="file-text" class="w-8 h-8 mr-3 text-warning-500"></i>
            Input Nilai Tugas Harian
          </h2>
          <p class="text-secondary-600 mt-1">Catat nilai tugas harian untuk pertemuan ini</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Form Container -->
  <div class="max-w-4xl mx-auto">
    <div class="glass-effect rounded-2xl border border-white/20 shadow-xl overflow-hidden animate-fade-in">
      <!-- Form Header -->
      <div class="bg-gradient-to-r from-warning-500 to-orange-600 p-6">
        <div class="flex items-center justify-between text-white">
          <div>
            <h3 class="text-xl font-bold mb-2 drop-shadow-sm">Tugas Harian - Pertemuan Ke-<?= $data['jurnal']['pertemuan_ke']; ?></h3>
            <p class="text-white/95 text-sm font-medium drop-shadow-sm">Mata Pelajaran: <?= htmlspecialchars($data['jurnal']['nama_mapel']); ?> | Kelas: <?= htmlspecialchars($data['jurnal']['nama_kelas']); ?></p>
            <p class="text-white/90 text-xs mt-1 drop-shadow-sm">Tanggal: <?= tanggal_indo($data['jurnal']['tanggal']); ?></p>
          </div>
          <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
            <i data-lucide="clock" class="w-6 h-6"></i>
          </div>
        </div>
      </div>

      <!-- Form Body -->
      <div class="p-8">
        <form action="<?= BASEURL; ?>/nilai/prosesSimpanTugasHarian" method="POST" id="nilaiForm">
          <input type="hidden" name="id_jurnal" value="<?= $data['jurnal']['id_jurnal']; ?>">
          <input type="hidden" name="id_penugasan" value="<?= $data['jurnal']['id_penugasan']; ?>">

          <!-- Quick Fill Section -->
          <div class="glass-effect rounded-xl p-4 mb-6 border border-white/20 bg-gradient-to-r from-warning-50 to-primary-50">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
              <div class="flex items-center gap-2 text-warning-700">
                <i data-lucide="zap" class="w-5 h-5"></i>
                <span class="font-semibold">Isi Cepat:</span>
              </div>
              <div class="flex items-center gap-3 flex-1">
                <input type="number" id="fillAllValue" min="0" max="100" step="1" 
                       class="input-modern w-32" placeholder="Nilai">
                <button type="button" onclick="fillAllNilai()" class="btn-warning text-sm py-2 px-4">
                  <i data-lucide="copy" class="w-4 h-4 mr-2"></i>
                  Isi Semua
                </button>
                <button type="button" onclick="clearAllNilai()" class="btn-secondary text-sm py-2 px-4">
                  <i data-lucide="eraser" class="w-4 h-4 mr-2"></i>
                  Hapus Semua
                </button>
              </div>
            </div>
          </div>

          <!-- Students List - Table Format -->
          <div class="glass-effect rounded-xl border border-white/20 shadow-lg overflow-hidden animate-slide-up">
            <div class="px-6 py-4 border-b border-secondary-200 bg-gradient-to-r from-warning-500 to-orange-600">
              <h3 class="text-lg font-bold text-white flex items-center drop-shadow-sm">
                <i data-lucide="users" class="w-5 h-5 mr-2"></i>
                Daftar Siswa (<?= count($data['filtered_siswa']); ?> siswa)
              </h3>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full responsive-table">
                <thead class="bg-secondary-50 border-b-2 border-secondary-200">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">NISN</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-700 uppercase tracking-wider w-32">Nilai</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-secondary-100">
                  <?php 
                  // Buat array nilai untuk akses cepat
                  $nilai_siswa = [];
                  if (!empty($data['nilai_tugas_harian'])) {
                    foreach ($data['nilai_tugas_harian'] as $nilai) {
                      $nilai_siswa[$nilai['id_siswa']] = $nilai;
                    }
                  }
                  
                  foreach ($data['filtered_siswa'] as $index => $siswa): 
                    // Cek apakah siswa ini sudah punya nilai
                    $nilai_existing = $nilai_siswa[$siswa['id_siswa']] ?? null;
                    $nilai_value = $nilai_existing ? $nilai_existing['nilai'] : '';
                  ?>
                    <tr class="hover:bg-secondary-50 transition-colors">
                      <td data-label="No" class="px-4 py-3 text-sm text-secondary-600"><?= $index + 1; ?></td>
                      <td data-label="NISN" class="px-4 py-3 text-sm text-secondary-600"><?= htmlspecialchars($siswa['nisn']); ?></td>
                      <td data-label="Nama Siswa" class="px-4 py-3">
                        <div class="flex items-center">
                          <div class="w-8 h-8 bg-gradient-to-r from-warning-400 to-primary-400 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                            <?= substr(htmlspecialchars($siswa['nama_siswa']), 0, 1); ?>
                          </div>
                          <span class="font-medium text-secondary-800"><?= htmlspecialchars($siswa['nama_siswa']); ?></span>
                        </div>
                      </td>
                      <td data-label="Nilai" class="px-4 py-3">
                        <div class="relative">
                          <input type="number" 
                                 id="nilai_<?= $siswa['id_siswa']; ?>" 
                                 name="nilai[<?= $siswa['id_siswa']; ?>]" 
                                 value="<?= $nilai_value; ?>" 
                                 min="0" 
                                 max="100" 
                                 step="1" 
                                 inputmode="numeric" pattern="[0-9]*"
                                 class="nilai-input input-modern w-full sm:w-24 text-center font-semibold <?= $nilai_value !== '' ? 'bg-success-50 border-success-300' : ''; ?>" 
                                 placeholder="0-100">
                          <?php if ($nilai_value !== ''): ?>
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-success-600">
                              <i data-lucide="check-circle" class="w-4 h-4"></i>
                            </span>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="form-actions mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <button type="button" onclick="window.location.href='<?= BASEURL; ?>/guru'" class="btn-secondary w-full sm:w-auto order-2 sm:order-1">
              <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i> Kembali ke Dashboard
            </button>
            <button type="submit" class="btn-primary w-full sm:w-auto order-1 sm:order-2 shadow-xl hover:shadow-2xl">
              <i data-lucide="save" class="w-5 h-5 mr-2"></i> Simpan Semua Nilai
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Fill all nilai with same value
    function fillAllNilai() {
      const value = document.getElementById('fillAllValue').value;
      if (!value || value < 0 || value > 100) {
        alert('Masukkan nilai antara 0-100');
        return;
      }
      
      const inputs = document.querySelectorAll('input[name^="nilai["]');
      inputs.forEach(input => {
        input.value = value;
      });
    }

    // Clear all nilai
    function clearAllNilai() {
      if (!confirm('Hapus semua nilai yang sudah diisi?')) return;
      
      const inputs = document.querySelectorAll('input[name^="nilai["]');
      inputs.forEach(input => {
        input.value = '';
      });
      document.getElementById('fillAllValue').value = '';
    }

    // Validate before submit
    document.getElementById('nilaiForm').addEventListener('submit', function(e) {
      const inputs = document.querySelectorAll('input[name^="nilai["]');
      let hasValue = false;
      
      inputs.forEach(input => {
        if (input.value) hasValue = true;
      });
      
      if (!hasValue) {
        e.preventDefault();
        alert('Minimal isi 1 nilai siswa!');
        return;
      }
    });
  </script>

  <style>
    @media (max-width: 640px) {
      .responsive-table thead { display: none; }
      .responsive-table, .responsive-table tbody, .responsive-table tr, .responsive-table td { display: block; width: 100%; }
      .responsive-table tr { margin-bottom: .75rem; border: 1px solid #e5e7eb; border-radius: .75rem; background: #ffffff; padding: .25rem .5rem; box-shadow: 0 6px 14px rgba(2,6,23,.06); }
      .responsive-table td { border: none; border-bottom: 1px solid #f1f5f9; padding: .6rem .75rem; display: flex; justify-content: space-between; align-items: center; }
      .responsive-table td:last-child { border-bottom: none; }
      .responsive-table td::before { content: attr(data-label); font-weight: 700; color: #64748b; margin-right: .75rem; }
      .responsive-table .nilai-input { max-width: 8.5rem; width: 100%; }
    }
  </style>
  </div>

  <!-- Modal Edit -->
  <div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-6 rounded-xl shadow-2xl w-full max-w-md">
      <h3 class="text-xl font-bold text-secondary-800 mb-4">Edit Nilai</h3>
      <form id="editForm">
        <input type="hidden" id="edit_id_siswa" name="id_siswa">
        <input type="hidden" id="edit_id_jurnal" name="id_jurnal" value="<?= $data['jurnal']['id_jurnal']; ?>">
        <input type="hidden" id="edit_id_penugasan" name="id_penugasan" value="<?= $data['jurnal']['id_penugasan']; ?>">
        <input type="hidden" id="edit_jenis_nilai" name="jenis_nilai" value="tugas_harian">
        <div class="mb-4">
          <label for="edit_nilai" class="block text-sm font-medium text-secondary-700 mb-1">Nilai</label>
          <input type="number" id="edit_nilai" name="nilai" min="0" max="100" step="1" class="input-modern w-full" placeholder="Masukkan nilai baru">
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
          <button type="submit" class="btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Hapus -->
  <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-6 rounded-xl shadow-2xl w-full max-w-md">
      <h3 class="text-xl font-bold text-secondary-800 mb-4">Konfirmasi Hapus</h3>
      <p class="text-secondary-600 mb-4">Apakah Anda yakin ingin menghapus nilai ini?</p>
      <input type="hidden" id="delete_id_nilai" name="id_nilai">
      <input type="hidden" id="delete_jenis_nilai" name="jenis_nilai">
      <div class="flex justify-end space-x-3">
        <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
        <button type="button" onclick="confirmDeleteAction()" class="btn-danger">Hapus</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Inisialisasi Lucide
      lucide.createIcons();

      // Fungsi untuk membuka modal edit
      window.openEditModal = function(id_siswa, nilai) {
        document.getElementById('edit_id_siswa').value = id_siswa;
        document.getElementById('edit_nilai').value = nilai;
        document.getElementById('editModal').classList.remove('hidden');
      };

      // Fungsi untuk menutup modal edit
      window.closeModal = function() {
        document.getElementById('editModal').classList.add('hidden');
      };

      // Fungsi untuk konfirmasi penghapusan
      window.confirmDelete = function(id_siswa, jenis_nilai) {
        document.getElementById('delete_id_nilai').value = id_siswa;
        document.getElementById('delete_jenis_nilai').value = jenis_nilai;
        document.getElementById('deleteModal').classList.remove('hidden');
      };

      // Fungsi untuk menutup modal hapus
      window.closeDeleteModal = function() {
        document.getElementById('deleteModal').classList.add('hidden');
      };

      // Fungsi untuk mengonfirmasi penghapusan
      window.confirmDeleteAction = function() {
        const id_nilai = document.getElementById('delete_id_nilai').value;
        const jenis_nilai = document.getElementById('delete_jenis_nilai').value;
        const url = jenis_nilai === 'tugas_harian' 
                     ? '<?= BASEURL; ?>/nilai/prosesHapusTugasHarian' 
                     : jenis_nilai === 'tengah_semester' 
                       ? '<?= BASEURL; ?>/nilai/prosesHapusTengahSemester' 
                       : '<?= BASEURL; ?>/nilai/prosesHapusAkhirSemester';

        fetch(url, {
          method: 'POST',
          body: new FormData(document.getElementById('deleteForm'))
        }).then(response => {
          if (response.ok) {
            location.reload();
          } else {
            alert('Gagal menghapus nilai.');
          }
        });
      };

      // Event listener untuk form edit
      document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = '<?= BASEURL; ?>/nilai/prosesEditTugasHarian';

        fetch(url, {
          method: 'POST',
          body: formData
        }).then(response => {
          if (response.ok) {
            location.reload();
          } else {
            alert('Gagal mengupdate nilai.');
          }
        });
      });

      // Event listener untuk form delete
      document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = '<?= BASEURL; ?>/nilai/prosesHapusTugasHarian';

        fetch(url, {
          method: 'POST',
          body: formData
        }).then(response => {
          if (response.ok) {
            location.reload();
          } else {
            alert('Gagal menghapus nilai.');
          }
        });
      });
    });
  </script>
</main>