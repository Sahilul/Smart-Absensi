<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-secondary-50 to-secondary-100 p-6">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-sm text-secondary-600">
            <a href="<?= BASEURL; ?>/guru/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="<?= BASEURL; ?>/guru/jurnal" class="hover:text-primary-600 transition-colors">Input Jurnal</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-secondary-800 font-medium">Buat Jurnal Baru</span>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="<?= BASEURL; ?>/guru/jurnal" 
                   class="p-2 rounded-xl text-secondary-500 hover:text-primary-600 hover:bg-white/50 transition-all duration-200">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-bold text-secondary-800 flex items-center">
                        <i data-lucide="edit-3" class="w-8 h-8 mr-3 text-primary-500"></i>
                        Buat Jurnal Pertemuan
                    </h2>
                    <p class="text-secondary-600 mt-1">Isi informasi pertemuan dan materi pembelajaran</p>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="gradient-success p-3 rounded-xl animate-pulse">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="max-w-4xl mx-auto">
        <div class="glass-effect rounded-2xl border border-white/20 shadow-xl overflow-hidden animate-fade-in">
            
            <!-- Form Header -->
            <div class="gradient-primary p-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h3 class="text-xl font-semibold mb-1">Jurnal Mengajar Baru</h3>
                        <p class="text-primary-100 text-sm">Pertemuan Ke-<?= $data['pertemuan_selanjutnya']; ?></p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i data-lucide="book-open-check" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <div class="p-8">
                <form action="<?= BASEURL; ?>/guru/prosesTambahJurnal" method="POST" id="jurnalForm">
                    <input type="hidden" name="id_penugasan" value="<?= $data['id_penugasan']; ?>">
                    
                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Pertemuan Ke -->
                            <div class="animate-slide-up" style="animation-delay: 0.1s;">
                                <label class="block text-sm font-semibold text-secondary-700 mb-3 flex items-center">
                                    <i data-lucide="hash" class="w-4 h-4 mr-2 text-primary-500"></i>
                                    Pertemuan Ke
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           name="pertemuan_ke" 
                                           id="pertemuan_ke" 
                                           readonly 
                                           value="<?= $data['pertemuan_selanjutnya']; ?>" 
                                           class="input-modern w-full bg-secondary-50 cursor-not-allowed text-center text-lg font-bold">
                                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                        <span class="status-badge bg-primary-100 text-primary-800 text-xs">Auto</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <div class="animate-slide-up" style="animation-delay: 0.2s;">
                                <label for="tanggal" class="block text-sm font-semibold text-secondary-700 mb-3 flex items-center">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-success-500"></i>
                                    Tanggal Pertemuan <span class="text-danger-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" 
                                           name="tanggal" 
                                           id="tanggal" 
                                           required 
                                           value="<?= date('Y-m-d'); ?>" 
                                           class="input-modern w-full pl-12">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <i data-lucide="calendar-days" class="w-5 h-5 text-secondary-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Time Display -->
                            <div class="animate-slide-up" style="animation-delay: 0.3s;">
                                <div class="bg-gradient-to-r from-success-50 to-primary-50 p-4 rounded-xl border border-white/30">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="gradient-success p-2 rounded-lg">
                                                <i data-lucide="clock" class="w-4 h-4 text-white"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-secondary-700">Waktu Saat Ini</p>
                                                <p class="text-lg font-bold text-secondary-800" id="current-time"><?= date('H:i:s'); ?></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-secondary-500">Hari</p>
                                            <p class="font-semibold text-secondary-700"><?= date('l'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Topik Materi -->
                            <div class="animate-slide-up" style="animation-delay: 0.4s;">
                                <label for="topik_materi" class="block text-sm font-semibold text-secondary-700 mb-3 flex items-center">
                                    <i data-lucide="book-open" class="w-4 h-4 mr-2 text-warning-500"></i>
                                    Topik / Materi Pembahasan <span class="text-danger-500">*</span>
                                </label>
                                <textarea name="topik_materi" 
                                          id="topik_materi" 
                                          rows="4" 
                                          required 
                                          placeholder="Contoh: Fungsi Kuadrat dan Grafiknya"
                                          class="input-modern w-full resize-none"></textarea>
                                <p class="text-xs text-secondary-500 mt-2 flex items-center">
                                    <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                                    Jelaskan materi yang akan diajarkan hari ini
                                </p>
                            </div>

                            <!-- Catatan -->
                            <div class="animate-slide-up" style="animation-delay: 0.5s;">
                                <label for="catatan" class="block text-sm font-semibold text-secondary-700 mb-3 flex items-center">
                                    <i data-lucide="sticky-note" class="w-4 h-4 mr-2 text-primary-500"></i>
                                    Catatan / Refleksi <span class="text-secondary-400 text-xs">(Opsional)</span>
                                </label>
                                <textarea name="catatan" 
                                          id="catatan" 
                                          rows="3" 
                                          placeholder="Catatan tambahan tentang pertemuan ini..."
                                          class="input-modern w-full resize-none"></textarea>
                                <p class="text-xs text-secondary-500 mt-2 flex items-center">
                                    <i data-lucide="lightbulb" class="w-3 h-3 mr-1"></i>
                                    Tambahkan refleksi atau catatan khusus
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-10 pt-6 border-t border-secondary-200 animate-slide-up" style="animation-delay: 0.6s;">
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                            <a href="<?= BASEURL; ?>/guru/jurnal" 
                               class="btn-secondary text-center px-6 py-3">
                                <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="btn-primary px-8 py-3 group" 
                                    id="submitBtn">
                                <i data-lucide="arrow-right" class="w-4 h-4 inline mr-2 group-hover:translate-x-1 transition-transform"></i>
                                Simpan & Lanjut Absensi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 glass-effect rounded-xl p-6 border border-white/20 shadow-lg animate-slide-up" style="animation-delay: 0.7s;">
            <h3 class="text-lg font-semibold text-secondary-800 mb-4 flex items-center">
                <i data-lucide="help-circle" class="w-5 h-5 mr-2 text-primary-500"></i>
                Tips Mengisi Jurnal
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-secondary-600">
                <div class="flex items-start space-x-3">
                    <div class="gradient-success p-1 rounded-full flex-shrink-0 mt-1">
                        <i data-lucide="check" class="w-3 h-3 text-white"></i>
                    </div>
                    <p>Pastikan tanggal sesuai dengan jadwal mengajar</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="gradient-primary p-1 rounded-full flex-shrink-0 mt-1">
                        <i data-lucide="check" class="w-3 h-3 text-white"></i>
                    </div>
                    <p>Tulis materi secara jelas dan detail</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="gradient-warning p-1 rounded-full flex-shrink-0 mt-1">
                        <i data-lucide="check" class="w-3 h-3 text-white"></i>
                    </div>
                    <p>Catatan bisa berisi metode atau kendala</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="gradient-danger p-1 rounded-full flex-shrink-0 mt-1">
                        <i data-lucide="check" class="w-3 h-3 text-white"></i>
                    </div>
                    <p>Setelah simpan, langsung input absensi</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Update time every second
        setInterval(updateTime, 1000);

        // Form validation
        const form = document.getElementById('jurnalForm');
        const submitBtn = document.getElementById('submitBtn');
        
        form.addEventListener('submit', function(e) {
            // Add loading state to button
            submitBtn.innerHTML = `
                <i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>
                Menyimpan...
            `;
            submitBtn.disabled = true;
            
            // Re-initialize icons for the new loader icon
            lucide.createIcons();
        });

        // Auto-resize textareas
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });

        // Character counter for topik_materi
        const topikMateri = document.getElementById('topik_materi');
        if (topikMateri) {
            const counter = document.createElement('div');
            counter.className = 'text-xs text-secondary-400 text-right mt-1';
            counter.id = 'topik-counter';
            topikMateri.parentNode.appendChild(counter);

            topikMateri.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = `${length} karakter`;
                
                if (length > 200) {
                    counter.className = 'text-xs text-danger-500 text-right mt-1';
                } else if (length > 150) {
                    counter.className = 'text-xs text-warning-600 text-right mt-1';
                } else {
                    counter.className = 'text-xs text-secondary-400 text-right mt-1';
                }
            });
        }

        // Form field animations
        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.style.borderColor = '#0ea5e9';
                this.style.boxShadow = '0 0 0 4px rgba(14, 165, 233, 0.1)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
                this.style.borderColor = '';
                this.style.boxShadow = '';
            });
        });

        // Save draft functionality (localStorage)
        const saveAsDraft = () => {
            const formData = {
                tanggal: document.getElementById('tanggal').value,
                topik_materi: document.getElementById('topik_materi').value,
                catatan: document.getElementById('catatan').value,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem('jurnal_draft', JSON.stringify(formData));
        };

        // Load draft if exists
        const loadDraft = () => {
            const draft = localStorage.getItem('jurnal_draft');
            if (draft) {
                const data = JSON.parse(draft);
                // Only load if draft is less than 24 hours old
                const draftAge = new Date() - new Date(data.timestamp);
                if (draftAge < 24 * 60 * 60 * 1000) {
                    if (confirm('Ditemukan draft jurnal yang belum tersimpan. Muat draft tersebut?')) {
                        document.getElementById('tanggal').value = data.tanggal;
                        document.getElementById('topik_materi').value = data.topik_materi;
                        document.getElementById('catatan').value = data.catatan;
                    }
                }
                localStorage.removeItem('jurnal_draft');
            }
        };

        // Auto-save draft every 30 seconds
        setInterval(saveAsDraft, 30000);

        // Save draft when leaving page
        window.addEventListener('beforeunload', saveAsDraft);

        // Load draft on page load
        loadDraft();
    });
</script>