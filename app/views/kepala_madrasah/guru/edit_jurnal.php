<?php
// File: app/views/guru/edit_jurnal.php
?>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Header dengan breadcrumb yang lebih baik -->
    <div class="mb-8">
        <div class="flex items-center mb-6">
            <a href="javascript:history.back()" class="p-2 rounded-xl text-secondary-500 hover:text-primary-600 hover:bg-white/50 transition-all duration-200 mr-4">
                <i data-lucide="arrow-left" class="w-6 h-6"></i>
            </a>
            <div>
                <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                    <i data-lucide="edit-3" class="w-8 h-8 mr-3 text-primary-500"></i>
                    Edit Jurnal Pertemuan
                </h2>
                <p class="text-secondary-600 mt-2">Perbarui informasi jurnal mengajar Anda</p>
            </div>
        </div>

        <!-- Info Jurnal -->
        <div class="glass-effect rounded-xl p-4 border border-white/20 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="gradient-primary p-2 rounded-lg">
                        <i data-lucide="book-open" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-secondary-800">
                            <?= htmlspecialchars($data['jurnal']['nama_mapel'] ?? 'Mata Pelajaran'); ?> - 
                            <?= htmlspecialchars($data['jurnal']['nama_kelas'] ?? 'Kelas'); ?>
                        </p>
                        <p class="text-sm text-secondary-600">Pertemuan ke-<?= htmlspecialchars($data['jurnal']['pertemuan_ke']); ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-secondary-600">Tanggal Sebelumnya</p>
                    <p class="font-semibold text-secondary-800">
                        <?= !empty($data['jurnal']['tanggal']) ? date('d M Y', strtotime($data['jurnal']['tanggal'])) : '-'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="glass-effect rounded-2xl shadow-lg p-8 max-w-3xl mx-auto border border-white/20 animate-fade-in">
        <form action="<?= BASEURL; ?>/guru/prosesUpdateJurnal" method="POST" class="space-y-6">
            <input type="hidden" name="id_jurnal" value="<?= htmlspecialchars($data['jurnal']['id_jurnal']); ?>">
            
            <!-- Form Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pertemuan Ke (readonly) -->
                <div class="form-group">
                    <label for="pertemuan_ke" class="form-label">
                        <i data-lucide="hash" class="w-4 h-4 inline mr-2 text-secondary-400"></i>
                        Pertemuan Ke-
                    </label>
                    <input type="number" 
                           name="pertemuan_ke" 
                           id="pertemuan_ke" 
                           readonly 
                           value="<?= htmlspecialchars($data['jurnal']['pertemuan_ke']); ?>" 
                           class="form-input-readonly">
                    <p class="form-hint">Nomor pertemuan tidak dapat diubah</p>
                </div>

                <!-- Tanggal -->
                <div class="form-group">
                    <label for="tanggal" class="form-label">
                        <i data-lucide="calendar" class="w-4 h-4 inline mr-2 text-secondary-400"></i>
                        Tanggal Mengajar
                    </label>
                    <input type="date" 
                           name="tanggal" 
                           id="tanggal" 
                           required 
                           value="<?= htmlspecialchars($data['jurnal']['tanggal']); ?>" 
                           class="form-input">
                </div>
            </div>

            <!-- Topik Materi -->
            <div class="form-group">
                <label for="topik_materi" class="form-label">
                    <i data-lucide="book-open" class="w-4 h-4 inline mr-2 text-secondary-400"></i>
                    Topik / Materi Pembahasan
                </label>
                <textarea name="topik_materi" 
                          id="topik_materi" 
                          rows="4" 
                          required 
                          placeholder="Jelaskan topik atau materi yang diajarkan pada pertemuan ini..."
                          class="form-textarea"><?= htmlspecialchars($data['jurnal']['topik_materi']); ?></textarea>
                <p class="form-hint">Deskripsikan secara singkat materi yang diajarkan</p>
            </div>

            <!-- Catatan -->
            <div class="form-group">
                <label for="catatan" class="form-label">
                    <i data-lucide="sticky-note" class="w-4 h-4 inline mr-2 text-secondary-400"></i>
                    Catatan / Refleksi <span class="text-secondary-400 font-normal">(Opsional)</span>
                </label>
                <textarea name="catatan" 
                          id="catatan" 
                          rows="3" 
                          placeholder="Tambahkan catatan khusus, kendala yang dihadapi, atau refleksi pembelajaran..."
                          class="form-textarea"><?= htmlspecialchars($data['jurnal']['catatan']); ?></textarea>
                <p class="form-hint">Catatan tambahan mengenai proses pembelajaran</p>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <button type="button" 
                            onclick="history.back()" 
                            class="btn-secondary">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" 
                            class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Jurnal
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="max-w-3xl mx-auto mt-6">
        <div class="glass-effect rounded-xl p-4 border border-white/20 shadow-sm">
            <div class="flex items-start space-x-3">
                <div class="gradient-warning p-2 rounded-lg flex-shrink-0">
                    <i data-lucide="info" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-secondary-800 mb-1">Tips Mengisi Jurnal</h4>
                    <ul class="text-sm text-secondary-600 space-y-1">
                        <li>• Pastikan tanggal sesuai dengan jadwal mengajar</li>
                        <li>• Topik materi sebaiknya mencakup kompetensi yang diajarkan</li>
                        <li>• Gunakan catatan untuk mencatat hal penting atau kendala pembelajaran</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Auto-resize textarea
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });

    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const topikMateri = document.getElementById('topik_materi');
            if (topikMateri && topikMateri.value.trim().length < 10) {
                e.preventDefault();
                alert('Topik materi harus diisi minimal 10 karakter');
                topikMateri.focus();
                return false;
            }
        });
    }

    // Auto-focus pada field pertama yang bisa diedit
    const tanggalField = document.getElementById('tanggal');
    if (tanggalField) {
        tanggalField.focus();
    }
});
</script>

<style>
/* Glass effect */
.glass-effect {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Animations */
.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
    opacity: 0;
}

@keyframes fadeIn {
    to {
        opacity: 1;
    }
}

/* Gradient classes */
.gradient-primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.gradient-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

/* Form Styles */
.form-group {
    space-y: 0.375rem;
}

.form-label {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-input, .form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    color: #111827;
    background-color: white;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input-readonly {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    color: #6b7280;
    background-color: #f9fafb;
    cursor: not-allowed;
}

.form-textarea {
    resize: vertical;
    min-height: 2.5rem;
}

.form-hint {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.form-actions {
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    margin-top: 2rem;
}

/* Button Styles */
.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: 1px solid rgba(59, 130, 246, 0.3);
    box-shadow: 0 6px 14px rgba(59, 130, 246, 0.15);
    transition: all 0.15s ease;
    text-decoration: none;
    cursor: pointer;
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
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: #4338ca;
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    border: 1px solid rgba(99, 102, 241, 0.25);
    box-shadow: 0 6px 14px rgba(99, 102, 241, 0.12);
    transition: all 0.15s ease;
    text-decoration: none;
    cursor: pointer;
}

.btn-secondary:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.18);
}
</style>