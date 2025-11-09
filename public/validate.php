<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Validator - Absensi App</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .validator-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .card-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .card-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .card-body {
            padding: 40px;
        }
        .result-box {
            text-align: center;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .result-box.valid {
            background: #d4edda;
            border: 2px solid #28a745;
        }
        .result-box.invalid {
            background: #f8d7da;
            border: 2px solid #dc3545;
        }
        .result-box.pending {
            background: #fff3cd;
            border: 2px solid #ffc107;
        }
        .result-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        .result-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .result-message {
            font-size: 16px;
            color: #666;
        }
        .doc-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .doc-details h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .btn-primary {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: background 0.3s;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="validator-card">
        <div class="card-header">
            <h1>🔍 QR Code Validator</h1>
            <p>Validasi keaslian dokumen PDF</p>
        </div>

        <div class="card-body">
            <?php
            // Helper: humanize snake_case to Title Case, with special mappings for roles/doc types
            if (!function_exists('humanize_label')) {
                function humanize_label($str, $map = []) {
                    if (!$str) return '';
                    $key = strtolower($str);
                    if (isset($map[$key])) return $map[$key];
                    $label = str_replace(['_', '-'], ' ', $key);
                    return ucwords($label);
                }
            }
            // Get URL parameters
            $docType = $_GET['type'] ?? null;
            $token = $_GET['token'] ?? null;

            // Fallback: support path-style URLs like /validate/<type>/<token> or /validate.php/<type>/<token>
            if (!$docType || !$token) {
                $pathInfo = $_SERVER['PATH_INFO'] ?? '';
                $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
                $candidates = array_filter([$pathInfo, $uriPath]);
                foreach ($candidates as $p) {
                    if (preg_match('#/validate(?:\.php)?/([^/]+)/([A-Fa-f0-9]{32,128})#', $p, $m)) {
                        if (!$docType) $docType = $m[1];
                        if (!$token) $token = $m[2];
                        break;
                    }
                }
            }

            // Load DB + model for validation
            $APPROOT = dirname(__DIR__);
            require_once $APPROOT . '/config/database.php';
            require_once $APPROOT . '/app/core/Database.php';
            require_once $APPROOT . '/app/models/QRValidation_model.php';
            $qrModel = new QRValidation_model();
            // Don't call ensureTables() here to reduce overhead; assume created during generation

            if (!$docType || !$token) {
                // No parameters - show pending state
                ?>
                <div class="result-box pending">
                    <div class="result-icon">⏳</div>
                    <div class="result-title">Menunggu Scan</div>
                    <div class="result-message">Scan QR code pada dokumen PDF untuk memvalidasi</div>
                </div>

                <div class="warning-box">
                    <strong>⚠️ Endpoint Placeholder</strong><br>
                    Ini adalah contoh halaman validator. Untuk implementasi lengkap, tambahkan:
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <li>Database query untuk verify document</li>
                        <li>Token signature validation</li>
                        <li>Logging untuk audit trail</li>
                        <li>Rate limiting untuk security</li>
                    </ul>
                </div>
                <?php
            } else {
                // Production-grade validation
                $record = null;
                $isValid = false;
                $reason = '';
                if (preg_match('/^[A-Fa-f0-9]{64}$/', (string)$token)) {
                    $record = $qrModel->findByToken($token);
                    if (!$record) {
                        $reason = 'Token tidak ditemukan';
                    } else if ((int)$record['revoked'] === 1) {
                        $reason = 'Token sudah dicabut';
                    } else if (!empty($record['expires_at']) && strtotime($record['expires_at']) < time()) {
                        $reason = 'Token kadaluarsa';
                    } else {
                        // Anti-tamper minimal: regen expected hash signature test (cannot fully verify original doc now)
                        // Basic heuristic: doc_type must match parameter
                        if ($record['doc_type'] !== $docType) {
                            $reason = 'Tipe dokumen tidak cocok';
                        } else {
                            $isValid = true;
                            $reason = 'OK';
                        }
                    }
                } else {
                    $reason = 'Format token invalid';
                }

                // Log scan
                $qrModel->logScan($token, $isValid, $reason);
                
                if ($isValid) {
                    // Friendly labels
                    $roleMap = [
                        'wali_kelas' => 'Wali Kelas',
                        'kepala_madrasah' => 'Kepala Madrasah',
                        'guru' => 'Guru',
                        'siswa' => 'Siswa',
                        'admin' => 'Admin',
                    ];
                    $docMap = [
                        'monitoring_absensi' => 'Monitoring Absensi',
                        'performa_siswa' => 'Performa Siswa',
                        'performa_guru' => 'Performa Guru',
                        'riwayat_jurnal' => 'Riwayat Jurnal',
                        'rincian_absen' => 'Rincian Absensi',
                        'pembayaran' => 'Pembayaran',
                        'rapor' => 'Rapor',
                        'rapor_sts' => 'Rapor STS',
                        'rapor_sas' => 'Rapor SAS',
                        'rapor_all_sts' => 'Rapor STS (Semua Siswa)',
                        'rapor_all_sas' => 'Rapor SAS (Semua Siswa)',
                        'monitoring_nilai' => 'Monitoring Nilai',
                        'nilai' => 'Nilai',
                        'mapel' => 'Mata Pelajaran',
                    ];
                    $docTypeNice = humanize_label($docType, $docMap);
                    ?>
                    <div class="result-box valid">
                        <div class="result-icon">✅</div>
                        <div class="result-title">Dokumen Valid</div>
                        <div class="result-message">Dokumen ini terverifikasi. Token: <?= htmlspecialchars(substr($token,0,12)) ?>…</div>
                    </div>

                    <div class="doc-details">
                        <h3>📄 Detail Dokumen</h3>
                        <div class="detail-row">
                            <span class="detail-label">Tipe Dokumen:</span>
                            <span class="detail-value"><?= htmlspecialchars($docTypeNice) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Token:</span>
                            <span class="detail-value" style="font-family: monospace; font-size: 12px; word-break: break-all;">
                                <?= htmlspecialchars($token) ?>
                            </span>
                        </div>
                        <?php if (!empty($meta['fingerprint'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Fingerprint:</span>
                            <span class="detail-value" style="font-family: monospace; font-size: 12px; word-break: break-all;">
                                <?= htmlspecialchars(substr($meta['fingerprint'],0,16)) ?>…
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php
                        // Show rapor-specific fields if available
                        if (strpos($docType, 'rapor') === 0) {
                            if (!empty($meta['nisn']) || !empty($meta['nama_siswa'])) {
                        ?>
                        <div class="detail-row">
                            <span class="detail-label">Siswa:</span>
                            <span class="detail-value"><?= htmlspecialchars(($meta['nama_siswa'] ?? '') . (!empty($meta['nisn']) ? ' / ' . $meta['nisn'] : '')) ?></span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($meta['kelas'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Kelas:</span>
                            <span class="detail-value"><?= htmlspecialchars($meta['kelas']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($meta['semester'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Semester:</span>
                            <span class="detail-value"><?= htmlspecialchars($meta['semester']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($meta['jenis'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Jenis:</span>
                            <span class="detail-value"><?= htmlspecialchars(strtoupper($meta['jenis'])) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($meta['rata_rata'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Rata-rata:</span>
                            <span class="detail-value"><?= htmlspecialchars(number_format((float)$meta['rata_rata'], 2)) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($meta['jumlah_siswa'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Jumlah Siswa:</span>
                            <span class="detail-value"><?= (int)$meta['jumlah_siswa'] ?></span>
                        </div>
                        <?php endif; ?>
                        <?php } ?>
                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value" style="color: #28a745; font-weight: bold;">Terverifikasi</span>
                        </div>
                        <?php
                        // Decode meta if present
                        $meta = [];
                        if (!empty($record['meta_json'])) {
                            $decoded = json_decode($record['meta_json'], true);
                            if (is_array($decoded)) { $meta = $decoded; }
                        }
                        // Timezone handling: show WIB (Asia/Jakarta)
                        $dtValidate = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                        $printedAtLocal = null;
                        if (!empty($meta['printed_at'])) {
                            try {
                                $printedUtc = new DateTime($meta['printed_at'], new DateTimeZone('UTC'));
                                $printedUtc->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                $printedAtLocal = $printedUtc->format('d F Y H:i:s');
                            } catch (Exception $e) {}
                        }
                        ?>
                        <div class="detail-row">
                            <span class="detail-label">Divalidasi (WIB):</span>
                            <span class="detail-value"><?= $dtValidate->format('d F Y H:i:s') ?></span>
                        </div>
                        <?php if ($printedAtLocal): ?>
                        <div class="detail-row">
                            <span class="detail-label">Dicetak (WIB):</span>
                            <span class="detail-value"><?= htmlspecialchars($printedAtLocal) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($meta['printed_by'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Dicetak Oleh:</span>
                            <?php $roleNice = !empty($meta['printed_role']) ? humanize_label($meta['printed_role'], $roleMap) : null; ?>
                            <span class="detail-value"><?= htmlspecialchars($meta['printed_by']) ?><?= $roleNice ? ' ('.htmlspecialchars($roleNice).')' : '' ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="warning-box">
                        <strong>🔧 Development Note:</strong><br>
                        Log tercatat. Untuk verifikasi lanjutan (integritas konten), bisa ditambahkan checksum konten dokumen pada saat generate dan diverifikasi ulang di sini.
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="result-box invalid">
                        <div class="result-icon">❌</div>
                        <div class="result-title">Dokumen Tidak Valid</div>
                        <div class="result-message">Validasi gagal: <?= htmlspecialchars($reason) ?></div>
                    </div>

                    <div class="doc-details">
                        <h3>⚠️ Informasi Error</h3>
                        <div class="detail-row">
                            <span class="detail-label">Catatan:</span>
                            <span class="detail-value" style="color: #dc3545;"><?= htmlspecialchars($reason) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Waktu Check:</span>
                            <span class="detail-value"><?= date('d F Y H:i:s') ?></span>
                        </div>
                    </div>

                    <button class="btn-primary" onclick="window.location.href='<?= $_SERVER['PHP_SELF'] ?>'">
                        Scan Ulang QR Code
                    </button>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <script>
        console.log('QR Validator Page');
        console.log('Doc Type:', '<?= $docType ?? "N/A" ?>');
        console.log('Token:', '<?= $token ? substr($token, 0, 16) . "..." : "N/A" ?>');
    </script>
</body>
</html>
