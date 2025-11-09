<main class="flex-1 overflow-x-hidden overflow-y-auto bg-secondary-50 p-4 md:p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <h4 class="text-xl font-bold text-slate-800 mb-2">
                <i data-lucide="qr-code" class="inline-block w-6 h-6 mr-2"></i>
                <?= $data['judul'] ?>
            </h4>
            <p class="text-slate-500 text-sm">
                Konfigurasi QR Code untuk validasi rapor digital
            </p>
        </div>
    </div>

    <!-- Flash Message -->
    <?php Flasher::flash(); ?>

    <!-- Form Konfigurasi -->
    <div class="bg-white shadow-sm rounded-xl overflow-hidden">
        <form action="<?= BASEURL; ?>/admin/simpanConfigQR" method="POST" class="p-6 space-y-6">
            <!-- Enable/Disable QR -->
            <div class="flex items-center justify-between p-4 border rounded-lg bg-slate-50">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Aktifkan QR di PDF</label>
                    <p class="text-xs text-slate-500">Nonaktifkan bila ingin mematikan sementara penyisipan QR ke semua PDF.</p>
                </div>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="qr_enabled" class="sr-only peer" <?= (($data['config']['QR_ENABLED'] ?? '1') === '1') ? 'checked' : '' ?>>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 relative"></div>
                </label>
            </div>
            
            <!-- QR Code Provider -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-3">
                    <i data-lucide="server" class="inline w-4 h-4 mr-1"></i>
                    QR Code API Provider
                </label>
                <div class="space-y-3">
                    <label class="flex items-start gap-3 p-4 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 transition-colors">
                        <input type="radio" name="qr_provider" value="qrserver" 
                               <?= ($data['config']['QR_API_PROVIDER'] ?? 'qrserver') === 'qrserver' ? 'checked' : '' ?>
                               class="mt-1 w-4 h-4 text-blue-600">
                        <div class="flex-1">
                            <div class="font-semibold text-slate-800">QR Server (Recommended)</div>
                            <div class="text-sm text-slate-500 mt-1">https://api.qrserver.com - Free & Reliable ✅</div>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-4 border-2 border-slate-200 rounded-xl opacity-50 cursor-not-allowed">
                        <input type="radio" name="qr_provider" value="quickchart" disabled
                               class="mt-1 w-4 h-4 text-blue-600">
                        <div class="flex-1">
                            <div class="font-semibold text-slate-800">QuickChart <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Sementara Disabled</span></div>
                            <div class="text-sm text-slate-500 mt-1">https://quickchart.io/qr – Gunakan QR Server saja</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start gap-3 p-4 border-2 border-slate-200 rounded-xl opacity-50 cursor-not-allowed">
                        <input type="radio" name="qr_provider" value="goqr" disabled
                               class="mt-1 w-4 h-4 text-blue-600">
                        <div class="flex-1">
                            <div class="font-semibold text-slate-800">GoQR.me <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Sementara Disabled</span></div>
                            <div class="text-sm text-slate-500 mt-1">Gunakan QR Server saja</div>
                        </div>
                    </label>
                    
                    <label class="flex items-start gap-3 p-4 border-2 border-slate-200 rounded-xl opacity-50 cursor-not-allowed">
                        <input type="radio" name="qr_provider" value="custom" disabled
                               class="mt-1 w-4 h-4 text-blue-600">
                        <div class="flex-1">
                            <div class="font-semibold text-slate-800">Custom URL <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Sementara Disabled</span></div>
                            <div class="text-sm text-slate-500 mt-1">Gunakan QR Server saja</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Custom URL Input (shown only when custom is selected) -->
            <div id="customUrlSection" class="hidden">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Custom API URL</label>
                <input type="url" name="qr_custom_url" 
                       value="<?= $data['config']['QR_CUSTOM_URL'] ?? '' ?>"
                       placeholder="https://your-api.com/qr-code?data={DATA}&size={SIZE}"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-slate-500 mt-2">
                    Gunakan <code class="px-1 py-0.5 bg-slate-100 rounded">{DATA}</code> untuk data QR dan 
                    <code class="px-1 py-0.5 bg-slate-100 rounded">{SIZE}</code> untuk ukuran
                </p>
            </div>

            <!-- QR Code Settings -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="globe" class="inline w-4 h-4 mr-1"></i>
                    URL Website
                </label>
                <input type="url" name="qr_website_url" 
                       value="<?= $data['config']['QR_WEBSITE_URL'] ?? 'http://localhost/absen' ?>"
                       placeholder="https://example.com atau http://192.168.1.100/absen"
                       required
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-slate-500 mt-2">
                    <i data-lucide="info" class="inline w-3 h-3"></i>
                    URL ini akan digunakan dalam QR Code. Ganti dengan domain/IP server yang bisa diakses dari luar.
                    <br>Contoh: <code class="px-1 py-0.5 bg-slate-100 rounded">https://sekolah.com</code> atau 
                    <code class="px-1 py-0.5 bg-slate-100 rounded">http://192.168.1.10/absen</code>
                           min="40" max="120" step="5"
            </div>

            <!-- QR Code Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Margin QR (px)</label>
                    <input type="number" name="qr_margin"
                           value="<?= $data['config']['QR_MARGIN'] ?? '1' ?>"
                           min="0" max="10" step="1"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-slate-500 mt-1">Margin putih di sekitar QR (0-10)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Error Correction Level</label>
                    <select name="qr_error_correction" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <?php $ec = $data['config']['QR_ERROR_CORRECTION'] ?? 'M'; ?>
                        <option value="L" <?= ($ec === 'L') ? 'selected' : '' ?>>L (Low)</option>
                        <option value="M" <?= ($ec === 'M') ? 'selected' : '' ?>>M (Medium)</option>
                        <option value="Q" <?= ($ec === 'Q') ? 'selected' : '' ?>>Q (Quartile)</option>
                        <option value="H" <?= ($ec === 'H') ? 'selected' : '' ?>>H (High)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ukuran QR Code</label>
                    <select name="qr_size" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="150x150" <?= ($data['config']['QR_SIZE'] ?? '200x200') === '150x150' ? 'selected' : '' ?>>150 x 150 px</option>
                        <option value="200x200" <?= ($data['config']['QR_SIZE'] ?? '200x200') === '200x200' ? 'selected' : '' ?>>200 x 200 px (Default)</option>
                        <option value="250x250" <?= ($data['config']['QR_SIZE'] ?? '') === '250x250' ? 'selected' : '' ?>>250 x 250 px</option>
                        <option value="300x300" <?= ($data['config']['QR_SIZE'] ?? '') === '300x300' ? 'selected' : '' ?>>300 x 300 px</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ukuran Tampilan di PDF</label>
                    <input type="number" name="qr_display_size" 
                           value="<?= $data['config']['QR_DISPLAY_SIZE'] ?? '60' ?>"
                           min="40" max="120" step="5"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-slate-500 mt-1">Dalam pixel (40-120)</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Token Expiry</label>
                    <select name="qr_token_expiry" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="0" <?= ($data['config']['QR_TOKEN_EXPIRY'] ?? '365') == '0' ? 'selected' : '' ?>>Tidak Pernah Kadaluarsa</option>
                        <option value="30" <?= ($data['config']['QR_TOKEN_EXPIRY'] ?? '') == '30' ? 'selected' : '' ?>>30 Hari</option>
                        <option value="90" <?= ($data['config']['QR_TOKEN_EXPIRY'] ?? '') == '90' ? 'selected' : '' ?>>90 Hari</option>
                        <option value="180" <?= ($data['config']['QR_TOKEN_EXPIRY'] ?? '') == '180' ? 'selected' : '' ?>>180 Hari</option>
                        <option value="365" <?= ($data['config']['QR_TOKEN_EXPIRY'] ?? '365') == '365' ? 'selected' : '' ?>>365 Hari (1 Tahun)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Posisi QR di PDF</label>
                    <select name="qr_position" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="bottom-left" <?= ($data['config']['QR_POSITION'] ?? 'bottom-left') === 'bottom-left' ? 'selected' : '' ?>>Kiri Bawah</option>
                        <option value="bottom-right" <?= ($data['config']['QR_POSITION'] ?? '') === 'bottom-right' ? 'selected' : '' ?>>Kanan Bawah</option>
                        <option value="top-left" <?= ($data['config']['QR_POSITION'] ?? '') === 'top-left' ? 'selected' : '' ?>>Kiri Atas</option>
                        <option value="top-right" <?= ($data['config']['QR_POSITION'] ?? '') === 'top-right' ? 'selected' : '' ?>>Kanan Atas</option>
                    </select>
                </div>
            </div>

            <!-- Display Text -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Text di Bawah QR Code</label>
                <input type="text" name="qr_display_text" 
                       value="<?= $data['config']['QR_DISPLAY_TEXT'] ?? 'Scan untuk validasi' ?>"
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       maxlength="50">
            </div>

            <!-- Security Salt -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <i data-lucide="shield" class="inline w-4 h-4 mr-1"></i>
                    Security Salt (Secret Key)
                </label>
                <div class="flex gap-2">
                    <input type="text" name="qr_token_salt" 
                           value="<?= $data['config']['QR_TOKEN_SALT'] ?? 'rapor_2024_secret_key' ?>"
                           class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           maxlength="100">
                    <button type="button" onclick="generateRandomSalt()" 
                            class="px-4 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1">Kunci rahasia untuk enkripsi token validasi</p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                <button type="reset" class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                    Reset
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm">
                    <i data-lucide="save" class="inline w-4 h-4 mr-2"></i>
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>

    <!-- Test QR Code Section -->
    <div class="mt-6 bg-white shadow-sm rounded-xl p-6">
        <h5 class="text-lg font-bold text-slate-800 mb-4">Test QR Code Generator</h5>
        <div class="flex items-center gap-4">
            <button onclick="testQRCode()" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <i data-lucide="play-circle" class="inline w-4 h-4 mr-2"></i>
                Test Generate QR
            </button>
            <div id="testResult" class="text-sm text-slate-600"></div>
        </div>
        <div id="testQRDisplay" class="mt-4"></div>
    </div>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
lucide.createIcons();

// Show/hide custom URL section
document.querySelectorAll('input[name="qr_provider"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const customSection = document.getElementById('customUrlSection');
        if (this.value === 'custom') {
            customSection.classList.remove('hidden');
        } else {
            customSection.classList.add('hidden');
        }
    });
});

// Check on page load
if (document.querySelector('input[name="qr_provider"]:checked')?.value === 'custom') {
    document.getElementById('customUrlSection').classList.remove('hidden');
}

// Generate random salt
function generateRandomSalt() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
    let salt = 'rapor_';
    for (let i = 0; i < 20; i++) {
        salt += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.querySelector('input[name="qr_token_salt"]').value = salt;
}

// Test QR Code generation
async function testQRCode() {
    const result = document.getElementById('testResult');
    const display = document.getElementById('testQRDisplay');
    
    result.textContent = 'Generating...';
    display.innerHTML = '';
    
    try {
        const response = await fetch('<?= BASEURL ?>/admin/testQRCode', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'}
        });
        
        const data = await response.json();
        
        if (data.success) {
            result.innerHTML = '<span class="text-green-600">✓ QR Code berhasil di-generate!</span>';
            display.innerHTML = `
                <div class="border-2 border-green-200 rounded-lg p-4 inline-block">
                    <img src="${data.qr_code}" alt="Test QR" class="w-32 h-32">
                    <p class="text-xs text-center mt-2 text-slate-600">Test QR Code</p>
                </div>
            `;
        } else {
            result.innerHTML = '<span class="text-red-600">✗ Gagal: ' + data.message + '</span>';
        }
    } catch (error) {
        result.innerHTML = '<span class="text-red-600">✗ Error: ' + error.message + '</span>';
    }
}
</script>
