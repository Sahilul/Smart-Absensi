<?php
// File: app/views/guru/edit_absensi.php
?>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-4 sm:p-6">
    <!-- Breadcrumb -->
    <div class="mb-4 sm:mb-6">
        <nav class="flex items-center space-x-2 text-sm text-secondary-600">
            <a href="<?= BASEURL; ?>/guru/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="<?= BASEURL; ?>/guru/riwayat" class="hover:text-primary-600 transition-colors">Riwayat Jurnal</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-secondary-800 font-medium">Edit Absensi</span>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-5 sm:mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <a href="javascript:history.back()" 
                   class="p-2 rounded-xl text-secondary-500 hover:text-primary-600 hover:bg-white/50 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-secondary-800 flex items-center">
                        <i data-lucide="edit-3" class="w-7 h-7 sm:w-8 sm:h-8 mr-2 sm:mr-3 text-warning-500"></i>
                        Edit Absensi Siswa
                    </h2>
                    <p class="text-secondary-600 mt-1 text-sm sm:text-base">Perbarui kehadiran siswa untuk pertemuan ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Jurnal Info Card -->
    <div class="mb-5 sm:mb-8">
        <div class="glass-effect flat-mobile rounded-none sm:rounded-xl px-0 sm:px-6 py-4 sm:py-6 border-0 sm:border border-white/20 shadow-none sm:shadow-lg animate-fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="lg:col-span-2">
                    <div class="flex items-start space-x-3 sm:space-x-4">
                        <div class="gradient-warning p-3 rounded-xl flex-shrink-0">
                            <i data-lucide="book-open" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-secondary-800 mb-2">
                                <?= htmlspecialchars($data['jurnal']['nama_mapel']); ?> - <?= htmlspecialchars($data['jurnal']['nama_kelas']); ?>
                            </h3>
                            <div class="space-y-2 text-sm text-secondary-600">
                                <p class="flex items-center">
                                    <i data-lucide="hash" class="w-4 h-4 mr-2 text-warning-500"></i>
                                    Pertemuan Ke-<?= htmlspecialchars($data['jurnal']['pertemuan_ke']); ?>
                                </p>
                                <p class="flex items-center">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-success-500"></i>
                                    <?= date('d F Y', strtotime($data['jurnal']['tanggal'])); ?>
                                </p>
                                <p class="flex items-center">
                                    <i data-lucide="book-text" class="w-4 h-4 mr-2 text-primary-500"></i>
                                    <strong>Topik:</strong> <?= htmlspecialchars($data['jurnal']['topik_materi']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:border-l lg:border-secondary-200 lg:pl-6">
                    <h4 class="font-semibold text-secondary-800 mb-3">Quick Stats</h4>
                    <div class="grid grid-cols-2 gap-2 sm:space-y-3 sm:block">
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary-600">Total Siswa:</span>
                            <span class="font-semibold" id="total-siswa"><?= count($data['daftar_siswa']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-success-600">Hadir:</span>
                            <span class="font-semibold text-success-700" id="count-hadir">0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-danger-600">Tidak Hadir:</span>
                            <span class="font-semibold text-danger-700" id="count-tidak-hadir"><?= count($data['daftar_siswa']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions & Search (sticky di mobile) -->
    <div class="mb-4 sm:mb-6">
        <div class="glass-effect flat-mobile rounded-none sm:rounded-xl px-0 sm:px-6 py-3 sm:py-6 border-0 sm:border border-white/20 shadow-none sm:shadow-lg animate-slide-up sticky top-[env(safe-area-inset-top,0)] z-30 backdrop-blur-sm bg-secondary-100/70 sm:bg-transparent">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 sm:gap-4">
                <div class="w-full">
                    <h3 class="text-xs sm:text-sm font-semibold text-secondary-700 mb-2 sm:mb-3 flex items-center">
                        <i data-lucide="zap" class="w-4 h-4 mr-2 text-warning-500"></i>
                        Aksi Cepat
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setBulkStatus('H')" class="btn-bulk bg-success-100 hover:bg-success-200 text-success-800 border-success-300">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i>Hadir Semua
                        </button>
                        <button type="button" onclick="setBulkStatus('A')" class="btn-bulk bg-danger-100 hover:bg-danger-200 text-danger-800 border-danger-300">
                            <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>Alpha Semua
                        </button>
                        <button type="button" onclick="setBulkStatus('I')" class="btn-bulk bg-blue-100 hover:bg-blue-200 text-blue-800 border-blue-300">
                            <i data-lucide="info" class="w-4 h-4 mr-2"></i>Izin Semua
                        </button>
                        <button type="button" onclick="setBulkStatus('S')" class="btn-bulk bg-yellow-100 hover:bg-yellow-200 text-yellow-800 border-yellow-300">
                            <i data-lucide="thermometer" class="w-4 h-4 mr-2"></i>Sakit Semua
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div class="w-full lg:w-auto">
                    <div class="relative">
                        <input type="text" id="search-siswa" placeholder="Cari nama siswa..." class="input-modern pl-10 w-full lg:w-64 h-11">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-secondary-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Absensi Form -->
    <form action="<?= BASEURL; ?>/guru/prosesEditAbsensi" method="POST" id="editAbsensiForm">
        <input type="hidden" name="id_jurnal" value="<?= $data['jurnal']['id_jurnal']; ?>">
        
        <!-- Students List -->
        <div class="glass-effect flat-mobile rounded-none sm:rounded-xl border-0 sm:border border-white/20 shadow-none sm:shadow-lg overflow-visible sm:overflow-hidden animate-slide-up">
            <div class="px-0 sm:px-6 py-4 sm:py-6 border-b border-secondary-200 gradient-warning">
                <h3 class="text-base sm:text-lg font-semibold text-white flex items-center">
                    <i data-lucide="users" class="w-5 h-5 mr-2"></i>
                    Daftar Siswa (<?= count($data['daftar_siswa']); ?> siswa)
                </h3>
            </div>
            
            <div class="max-h-none lg:max-h-[60vh] overflow-visible lg:overflow-y-auto">
                <div class="divide-y divide-secondary-100" id="students-list" role="list">
                    <?php foreach ($data['daftar_siswa'] as $index => $siswa) : ?>
                        <?php 
                        $current_status = $siswa['status_kehadiran'] ?? 'H';
                        $current_keterangan = $siswa['keterangan'] ?? '';
                        ?>
                        <div class="student-row p-4 sm:p-6 hover:bg-white/50 transition-all duration-200" 
                             data-name="<?= strtolower(htmlspecialchars($siswa['nama_siswa'])); ?>"
                             style="animation: slideInLeft 0.3s ease-out <?= $index * 0.05; ?>s both;">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <!-- Student Info -->
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-r from-warning-400 to-primary-400 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                            <?= substr(htmlspecialchars($siswa['nama_siswa']), 0, 1); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-base sm:text-lg font-semibold text-secondary-800">
                                            <?= htmlspecialchars($siswa['nama_siswa']); ?>
                                        </h4>
                                        <p class="text-sm text-secondary-500 flex items-center">
                                            <i data-lucide="id-card" class="w-4 h-4 mr-1"></i>
                                            NISN: <?= htmlspecialchars($siswa['nisn']); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Selection -->
                                <div class="w-full sm:w-auto">
                                    <div class="grid grid-cols-4 gap-2 sm:gap-4 sm:flex sm:items-center sm:space-x-6" role="radiogroup" aria-label="Status kehadiran">
                                        <!-- Hadir -->
                                        <label class="status-option status-hadir group cursor-pointer items-center">
                                            <input type="radio" 
                                                   name="absensi[<?= $siswa['id_siswa']; ?>]" 
                                                   value="H" 
                                                   <?= $current_status === 'H' ? 'checked' : ''; ?>
                                                   class="status-radio sr-only">
                                            <div class="status-button touch-target bg-success-100 border-success-300 text-success-700 group-hover:bg-success-200 group-hover:scale-105">
                                                <i data-lucide="check" class="w-5 h-5"></i>
                                                <span class="font-medium">H</span>
                                            </div>
                                            <span class="status-label">Hadir</span>
                                        </label>

                                        <!-- Izin -->
                                        <label class="status-option status-izin group cursor-pointer items-center">
                                            <input type="radio" 
                                                   name="absensi[<?= $siswa['id_siswa']; ?>]" 
                                                   value="I" 
                                                   <?= $current_status === 'I' ? 'checked' : ''; ?>
                                                   class="status-radio sr-only">
                                            <div class="status-button touch-target bg-blue-100 border-blue-300 text-blue-700 group-hover:bg-blue-200 group-hover:scale-105">
                                                <i data-lucide="info" class="w-5 h-5"></i>
                                                <span class="font-medium">I</span>
                                            </div>
                                            <span class="status-label">Izin</span>
                                        </label>

                                        <!-- Sakit -->
                                        <label class="status-option status-sakit group cursor-pointer items-center">
                                            <input type="radio" 
                                                   name="absensi[<?= $siswa['id_siswa']; ?>]" 
                                                   value="S" 
                                                   <?= $current_status === 'S' ? 'checked' : ''; ?>
                                                   class="status-radio sr-only">
                                            <div class="status-button touch-target bg-yellow-100 border-yellow-300 text-yellow-700 group-hover:bg-yellow-200 group-hover:scale-105">
                                                <i data-lucide="thermometer" class="w-5 h-5"></i>
                                                <span class="font-medium">S</span>
                                            </div>
                                            <span class="status-label">Sakit</span>
                                        </label>

                                        <!-- Alpha -->
                                        <label class="status-option status-alpha group cursor-pointer items-center">
                                            <input type="radio" 
                                                   name="absensi[<?= $siswa['id_siswa']; ?>]" 
                                                   value="A" 
                                                   <?= $current_status === 'A' ? 'checked' : ''; ?>
                                                   class="status-radio sr-only">
                                            <div class="status-button touch-target bg-danger-100 border-danger-300 text-danger-700 group-hover:bg-danger-200 group-hover:scale-105">
                                                <i data-lucide="x" class="w-5 h-5"></i>
                                                <span class="font-medium">A</span>
                                            </div>
                                            <span class="status-label">Alpha</span>
                                        </label>
                                    </div>

                                    <!-- Keterangan Input -->
                                    <div class="mt-3 sm:mt-0 sm:ml-6">
                                        <input type="text" 
                                               name="keterangan[<?= $siswa['id_siswa']; ?>]"
                                               value="<?= htmlspecialchars($current_keterangan); ?>"
                                               placeholder="Keterangan..."
                                               class="input-modern text-sm w-full sm:w-48 py-2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Form Actions (sticky bottom di mobile) -->
        <div class="mt-6 sm:mt-8 glass-effect flat-mobile rounded-none sm:rounded-xl px-0 sm:px-6 py-3 sm:py-6 border-0 sm:border border-white/20 shadow-none sm:shadow-lg animate-slide-up sticky bottom-0 z-30 bg-gradient-to-t from-secondary-100/90 to-transparent backdrop-blur-sm">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-0">
                <!-- Summary -->
                <div class="grid grid-cols-4 gap-3 text-sm w-full sm:w-auto">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-success-500 rounded-full"></div>
                        <span>Hadir: <strong id="summary-hadir">0</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span>Izin: <strong id="summary-izin">0</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span>Sakit: <strong id="summary-sakit">0</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-danger-500 rounded-full"></div>
                        <span>Alpha: <strong id="summary-alpha">0</strong></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex w-full sm:w-auto gap-3 sm:gap-4 mt-2 sm:mt-0">
                    <button type="button" onclick="history.back()" class="btn-secondary w-1/2 sm:w-auto px-6 py-3">
                        <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn-warning w-1/2 sm:w-auto px-8 py-3 group" id="submitBtn">
                        <i data-lucide="save" class="w-4 h-4 inline mr-2 group-hover:scale-110 transition-transform"></i>
                        Update Absensi
                    </button>
                </div>
            </div>
        </div>
    </form>
</main>

<style>
    /* ====== Mobile-first ergonomics ====== */
    .btn-bulk {
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: 2px solid;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
    }

    /* Input modern */
    .input-modern{
        border:1px solid rgb(226,232,240); background:#fff; border-radius:.75rem;
        padding:.6rem .9rem; outline:none; transition:border-color .18s ease, box-shadow .18s ease;
    }
    .input-modern:focus{
        border-color:#0ea5e9; box-shadow:0 0 0 4px rgba(14,165,233,.15);
    }

    /* Warna aksen per status (agar tidak putih saat dipilih) */
    .status-hadir { --accent: #16a34a; }
    .status-izin  { --accent: #2563eb; }
    .status-sakit { --accent: #ca8a04; }
    .status-alpha { --accent: #dc2626; }

    .status-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    .status-button {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        border: 2px solid;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        transition: all 0.2s;
    }
    .status-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #64748b;
    }
    /* PERUBAHAN UTAMA: gunakan --accent saat dipilih */
    .status-option .status-radio:checked + .status-button{
        background: var(--accent) !important;
        background-image: linear-gradient(135deg, var(--accent), var(--accent)) !important;
        color: #fff !important;
        border-color: var(--accent) !important;
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(0,0,0,.15);
    }

    /* Zebra */
    .student-row:nth-child(odd) { background: rgba(248, 250, 252, 0.5); }

    /* “Lepas dari box” di mobile, tapi desain tetap di desktop */
    @media (max-width: 640px) {
        .flat-mobile {
            background: transparent !important;
            backdrop-filter: none !important;
            border: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
    }

    /* Tombol submit edit */
    .btn-warning{
        background:#f59e0b; color:#fff;
        border-radius:.75rem; font-weight:700; line-height:1;
        box-shadow:0 6px 16px rgba(245,158,11,.25);
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .btn-warning:hover{ background:#d97706; box-shadow:0 10px 24px rgba(217,119,6,.28); transform: translateY(-1px); }
    .btn-warning:active{ transform: translateY(0); }
    .btn-warning:disabled{ opacity:.7; cursor:not-allowed; }
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        updateCounters(); // hitung status awal

        // Search (debounce + toggle)
        const searchInput = document.getElementById('search-siswa');
        let t;
        searchInput.addEventListener('input', function() {
            clearTimeout(t);
            t = setTimeout(() => {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('.student-row').forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    row.classList.toggle('hidden', !name.includes(term));
                });
            }, 120);
        });

        // Delegasi perubahan radio
        document.getElementById('students-list').addEventListener('change', function(e){
            if (e.target && e.target.classList.contains('status-radio')) updateCounters();
        });

        // UX submit
        const form = document.getElementById('editAbsensiForm');
        const submitBtn = document.getElementById('submitBtn');
        form.addEventListener('submit', function() {
            submitBtn.innerHTML = `
                <i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>
                Menyimpan...
            `;
            submitBtn.disabled = true;
            lucide.createIcons();
        });
    });

    // Bulk status setter (global)
    function setBulkStatus(status) {
        const radios = document.querySelectorAll('input.status-radio[value="'+status+'"]');
        radios.forEach(radio => {
            if (!radio.checked) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        updateCounters();
        showNotification('Semua siswa telah diubah ke ' + getStatusLabel(status), 'success');
    }

    // Hitung ringkasan
    function updateCounters() {
        const counts = { H:0, I:0, S:0, A:0 };
        document.querySelectorAll('.status-radio:checked').forEach(r => { counts[r.value] = (counts[r.value]||0) + 1; });

        setText('count-hadir', counts.H);
        setText('count-tidak-hadir', counts.I + counts.S + counts.A);

        setText('summary-hadir', counts.H);
        setText('summary-izin', counts.I);
        setText('summary-sakit', counts.S);
        setText('summary-alpha', counts.A);
    }

    function setText(id, val){ const el = document.getElementById(id); if (el) el.textContent = val; }
    function getStatusLabel(s){ return ({H:'Hadir', I:'Izin', S:'Sakit', A:'Alpha'})[s] || s; }

    function showNotification(message, type='info') {
        const n = document.createElement('div');
        n.className = 'fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transition-all duration-300 transform translate-x-full';
        const colors = {
            success:'bg-success-100 border-success-300 text-success-800',
            info:'bg-blue-100 border-blue-300 text-blue-800'
        };
        n.className += ' ' + (colors[type] || colors.info) + ' border-2';
        n.innerHTML = '<div class="flex items-center space-x-2"><i data-lucide="check-circle" class="w-5 h-5"></i><span class="font-medium">'+message+'</span></div>';
        document.body.appendChild(n);
        if (window.lucide && lucide.createIcons) lucide.createIcons();
        requestAnimationFrame(()=>{ n.style.transform='translateX(0)'; });
        setTimeout(()=>{ n.style.transform='translateX(100%)'; setTimeout(()=>n.remove(),300); }, 3000);
    }
</script>
