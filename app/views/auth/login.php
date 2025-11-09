<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($data['judul'] ?? 'Login'); ?></title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    :root{
      --radius-md:.8rem; --radius-xl:1.1rem;
      --shadow-card:0 10px 24px rgba(2,8,23,.08);
      --shadow-strong:0 18px 40px rgba(2,8,23,.14);
    }
    body{ font-family: 'Inter', sans-serif; }

    /* Card kaca */
    .glass{
      background: rgba(255,255,255,.78);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,.45);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-card);
    }

    /* Input / Select */
    .input-modern{
      width:100%; border:1px solid rgb(226,232,240); background:#fff; border-radius: var(--radius-md);
      padding:.68rem .9rem; outline:none;
      transition:border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .input-modern:focus{
      border-color:#0ea5e9; box-shadow:0 0 0 4px rgba(14,165,233,.15);
      background:#fff;
    }

    /* Field dengan ikon kiri */
    .field{ position:relative; }
    .field-icon{
      position:absolute; left:.75rem; top:50%; transform: translateY(-50%); pointer-events:none;
      color:#94a3b8;
    }
    /* PENTING: padding untuk input & select agar teks tidak menabrak ikon */
    .field input,
    .field select,
    .field textarea{
      padding-left: 2.6rem; /* ~pl-10 */
    }

    /* Select: hilangkan panah default & kasih caret sendiri di kanan */
    .field select{
      appearance: none; -webkit-appearance: none; -moz-appearance: none;
      padding-right: 2.6rem; /* ruang untuk caret kanan */
      background-color:#fff;
    }
    .select-caret{
      position:absolute; right:.6rem; top:50%; transform: translateY(-50%);
      pointer-events:none; color:#94a3b8;
    }

    /* Tombol utama */
    .btn-primary{
      display:inline-flex; align-items:center; justify-content:center; gap:.5rem;
      width:100%; padding:.85rem 1rem; border-radius: .9rem; color:#fff; font-weight:700;
      background:#4f46e5; /* indigo-600 */
      box-shadow:0 10px 22px rgba(79,70,229,.22);
      transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .btn-primary:hover{ background:#4338ca; box-shadow:0 16px 30px rgba(67,56,202,.26); transform: translateY(-1px); }
    .btn-primary:active{ transform: translateY(0); }
    .btn-primary:disabled{ opacity:.75; cursor:not-allowed; }

    /* Toggle visibility */
    .toggle-visibility{
      position:absolute; right:.4rem; top:50%; transform: translateY(-50%);
      padding:.4rem .5rem; border-radius:.5rem; color:#64748b;
    }

    /* Caps Lock hint */
    .caps-hint{ font-size:.78rem; color:#ef4444; display:none; margin-top:.3rem; }

    /* Badge brand */
    .brand-badge{
      background: linear-gradient(135deg, #22c55e, #0ea5e9);
      color:#fff; border-radius: 1rem; padding:.7rem; box-shadow:0 10px 24px rgba(2,8,23,.2);
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-sky-50 to-emerald-50 relative overflow-hidden">

  <!-- Dekorasi blur -->
  <div class="pointer-events-none absolute -top-24 -left-24 w-80 h-80 rounded-full bg-indigo-200/50 blur-3xl"></div>
  <div class="pointer-events-none absolute -bottom-24 -right-24 w-[28rem] h-[28rem] rounded-full bg-sky-200/50 blur-3xl"></div>

  <main class="relative z-10 flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md">
      <!-- Flash message -->
      <div class="mb-4">
        <?php Flasher::flash(); ?>
      </div>

      <!-- Kartu login -->
      <div class="glass p-8 space-y-7">
        <!-- Header -->
        <div class="text-center">
          <div class="flex justify-center mb-4">
            <div class="brand-badge">
              <i data-lucide="book-user" class="w-8 h-8"></i>
            </div>
          </div>
          <h1 class="text-2xl font-bold text-slate-800">Selamat Datang</h1>
          <p class="text-slate-500 mt-1">Silakan login ke akun Anda</p>
        </div>

        <!-- Form -->
        <form action="<?= BASEURL; ?>/auth/prosesLogin" method="POST" class="space-y-5" id="loginForm" autocomplete="on">
          <!-- Username -->
          <div>
            <label for="username" class="text-sm font-semibold text-slate-700">Username</label>
            <div class="field mt-1">
              <span class="field-icon">
                <i data-lucide="user" class="w-5 h-5"></i>
              </span>
              <input id="username" name="username" type="text" required
                     class="input-modern"
                     placeholder="Masukkan username" autofocus />
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="text-sm font-semibold text-slate-700">Password</label>
            <div class="field mt-1">
              <span class="field-icon">
                <i data-lucide="lock" class="w-5 h-5"></i>
              </span>
              <input id="password" name="password" type="password" required
                     class="input-modern pr-10"
                     placeholder="Masukkan password" />
              <button type="button" class="toggle-visibility hover:bg-slate-100"
                      aria-label="Tampilkan/sembunyikan password"
                      onclick="togglePassword()">
                <i data-lucide="eye" id="eye-open" class="w-5 h-5"></i>
                <i data-lucide="eye-off" id="eye-closed" class="w-5 h-5 hidden"></i>
              </button>
            </div>
            <div id="caps-hint" class="caps-hint">Caps Lock aktif</div>
          </div>

          <!-- Semester -->
          <div>
            <label for="id_semester" class="text-sm font-semibold text-slate-700">Pilih Sesi</label>
            <div class="field mt-1">
              <span class="field-icon">
                <i data-lucide="calendar" class="w-5 h-5"></i>
              </span>
              <select id="id_semester" name="id_semester" required class="input-modern">
                <?php if (empty($data['daftar_semester'])): ?>
                  <option disabled selected>Belum ada data semester</option>
                <?php else: ?>
                  <?php 
                    // Tentukan semester aktif berdasarkan bulan dan tahun saat ini
                    $bulanSekarang = (int)date('m');
                    $tahunSekarang = (int)date('Y');
                    
                    // Logika tahun pelajaran dan semester:
                    // Juli-Desember (bulan 7-12) = Semester Ganjil, Tahun Pelajaran: tahun_sekarang/(tahun_sekarang+1)
                    // Januari-Juni (bulan 1-6) = Semester Genap, Tahun Pelajaran: (tahun_sekarang-1)/tahun_sekarang
                    
                    if ($bulanSekarang >= 7 && $bulanSekarang <= 12) {
                      // Juli-Desember = Semester Ganjil
                      $tahunPelajaranAwal = $tahunSekarang;
                      $tahunPelajaranAkhir = $tahunSekarang + 1;
                      $semesterTarget = 'Ganjil';
                    } else {
                      // Januari-Juni = Semester Genap
                      $tahunPelajaranAwal = $tahunSekarang - 1;
                      $tahunPelajaranAkhir = $tahunSekarang;
                      $semesterTarget = 'Genap';
                    }
                    
                    $tahunPelajaranString = $tahunPelajaranAwal . '/' . $tahunPelajaranAkhir;
                    $firstSelected = false;
                  ?>
                  
                  <?php foreach ($data['daftar_semester'] as $smt): ?>
                    <?php
                      $selected = '';
                      
                      // Cek apakah semester ini sesuai dengan semester target
                      $namaTp = $smt['nama_tp'] ?? '';
                      $semester = $smt['semester'] ?? '';
                      
                      // Cocokkan dengan tahun pelajaran dan semester target
                      $isMatch = (strpos($namaTp, $tahunPelajaranString) !== false) && 
                                 (stripos($semester, $semesterTarget) !== false);
                      
                      if ($isMatch && !$firstSelected) { 
                        $selected = 'selected'; 
                        $firstSelected = true; 
                      }
                      
                      // Fallback: jika tidak ada yang cocok, pilih yang pertama
                      if (!$firstSelected && !$selected) {
                        $isActive = !empty($smt['is_aktif']);
                        if ($isActive) { 
                          $selected = 'selected'; 
                          $firstSelected = true; 
                        }
                      }
                    ?>
                    <option value="<?= (int)$smt['id_semester']; ?>" <?= $selected; ?>>
                      <?= htmlspecialchars(($smt['nama_tp'] ?? 'TP') . ' - ' . ($smt['semester'] ?? 'Semester')); ?>
                    </option>
                  <?php endforeach; ?>
                  
                  <?php 
                    // Jika masih belum ada yang terpilih, pilih yang pertama
                    if (!$firstSelected && !empty($data['daftar_semester'])) {
                      echo '<script>document.querySelector("#id_semester option").selected = true;</script>';
                    }
                  ?>
                <?php endif; ?>
              </select>
              <!-- caret kanan custom -->
              <span class="select-caret">
                <i data-lucide="chevron-down" class="w-5 h-5"></i>
              </span>
            </div>
            
            <!-- Info semester yang dipilih otomatis -->
            <div class="mt-2 text-xs text-slate-600 bg-blue-50 rounded-lg p-2">
              <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
              Auto-selected: 
              <?php
                echo ($bulanSekarang >= 7 && $bulanSekarang <= 12) 
                  ? "Semester Ganjil $tahunPelajaranString" 
                  : "Semester Genap $tahunPelajaranString";
              ?>
            </div>
          </div>

          <!-- Submit -->
          <div class="pt-1">
            <button type="submit" class="btn-primary" id="submitBtn">
              <i data-lucide="log-in" class="w-5 h-5"></i>
              <span>Login</span>
            </button>
          </div>
        </form>

        <!-- Footer kecil -->
        <div class="text-center text-xs text-slate-500">
          &copy; <?= date('Y'); ?> Smart Absensi • Semua hak dilindungi
        </div>
      </div>
    </div>
  </main>

  <script>
    // Icon init
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }

    // Toggle show/hide password
    function togglePassword(){
      const pwd = document.getElementById('password');
      const eyeOpen = document.getElementById('eye-open');
      const eyeClosed = document.getElementById('eye-closed');
      const nowText = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
      pwd.setAttribute('type', nowText);
      eyeOpen.classList.toggle('hidden');
      eyeClosed.classList.toggle('hidden');
    }

    // Caps Lock hint
    const pwdInput = document.getElementById('password');
    const capsHint = document.getElementById('caps-hint');
    ['keydown','keyup'].forEach(evt => {
      pwdInput.addEventListener(evt, (e) => {
        const caps = e.getModifierState && e.getModifierState('CapsLock');
        capsHint.style.display = caps ? 'block' : 'none';
      });
    });

    // Loading state on submit
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    form.addEventListener('submit', function(){
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i><span>Memproses...</span>';
      if (typeof lucide !== 'undefined') { lucide.createIcons(); }
    });
  </script>
</body>
</html>