<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="<?= $data['valid'] ? 'bg-gradient-to-r from-green-500 to-emerald-600' : 'bg-gradient-to-r from-red-500 to-rose-600' ?> p-6 text-center">
                <?php if ($data['valid']): ?>
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Rapor Valid</h1>
                    <p class="text-green-50">Dokumen Terverifikasi</p>
                <?php else: ?>
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Validasi Gagal</h1>
                    <p class="text-red-50">Dokumen Tidak Valid</p>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-slate-600 text-sm leading-relaxed">
                        <?= htmlspecialchars($data['message']) ?>
                    </p>
                </div>

                <?php if ($data['valid']): ?>
                    <!-- Info Validasi -->
                    <div class="bg-slate-50 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-slate-600">Token Validasi</span>
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">Aktif</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 mb-3">
                            <code class="text-xs text-slate-500 break-all"><?= htmlspecialchars($data['token']) ?></code>
                        </div>
                        <div class="text-xs text-slate-500">
                            <i data-lucide="clock" class="w-3 h-3 inline"></i>
                            Diverifikasi pada: <?= $data['verified_at'] ?>
                        </div>
                    </div>

                    <!-- Info Tambahan -->
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-700">Dokumen Asli</p>
                                <p class="text-slate-500 text-xs">Rapor ini diterbitkan oleh sistem resmi</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-700">Data Terenkripsi</p>
                                <p class="text-slate-500 text-xs">Menggunakan teknologi keamanan modern</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-700">Tidak Dapat Diubah</p>
                                <p class="text-slate-500 text-xs">Dokumen terlindungi dari pemalsuan</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Peringatan -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-red-700">
                                <p class="font-medium mb-1">Perhatian!</p>
                                <p class="text-red-600">Dokumen ini mungkin palsu atau sudah tidak berlaku. Hubungi pihak sekolah untuk verifikasi lebih lanjut.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Footer -->
                <div class="mt-6 pt-6 border-t border-slate-200 text-center">
                    <p class="text-xs text-slate-500">
                        Sistem Validasi Rapor Digital
                    </p>
                    <p class="text-xs text-slate-400 mt-1">
                        © <?= date('Y') ?> - Secure Document Verification
                    </p>
                </div>
            </div>
        </div>

        <!-- Info Tambahan -->
        <div class="mt-6 text-center">
            <a href="<?= BASEURL ?>" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
