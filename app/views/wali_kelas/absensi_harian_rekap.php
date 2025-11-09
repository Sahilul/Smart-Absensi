<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 p-3 sm:p-6 space-y-4 sm:space-y-6">
  <!-- Header -->
  <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
          Performa Kehadiran Harian
        </h1>
        <p class="text-gray-600 text-sm sm:text-base">Rekap status per hari: Sakit > Izin > Alfa > Hadir</p>
      </div>
      <div class="mt-3 sm:mt-0">
        <span class="inline-block text-xs sm:text-sm text-purple-600 font-semibold bg-gradient-to-r from-purple-100 to-blue-100 px-4 py-2 rounded-full border border-purple-200">
          <?= $_SESSION['nama_semester_aktif'] ?? 'Semester Tidak Diketahui'; ?> · <?= htmlspecialchars($data['wali_kelas_info']['nama_kelas'] ?? '-'); ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Filter -->
  <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100">
    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
      <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
      Filter & Pencarian
    </h2>
  <form onsubmit="return false;" class="space-y-4">
      <input type="hidden" name="mode" value="rekap" />
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
          <select id="period-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
            <option value="today">Hari Ini</option>
            <option value="this_week">Minggu Ini</option>
            <option value="this_month">Bulan Ini</option>
            <option value="this_semester" selected>Semester Ini</option>
            <option value="custom">Custom</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
          <input type="date" id="start-date" name="start_date" value="<?= htmlspecialchars($data['start_date'] ?? date('Y-m-d')); ?>" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
          <input type="date" id="end-date" name="end_date" value="<?= htmlspecialchars($data['end_date'] ?? date('Y-m-d')); ?>" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
          <select id="sort-select" class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 focus:border-blue-500 focus:ring-0 transition-colors">
            <option value="persentase_desc">📈 Persentase Tertinggi</option>
            <option value="persentase_asc">📉 Persentase Terendah</option>
            <option value="hadir_desc">✅ Terbanyak Hadir</option>
            <option value="hadir_asc">❌ Tersedikit Hadir</option>
            <option value="alfa_desc">🚨 Terbanyak Alpha</option>
            <option value="alfa_asc">⭐ Tersedikit Alpha</option>
            <option value="sakit_desc">🤒 Terbanyak Sakit</option>
            <option value="sakit_asc">💪 Tersedikit Sakit</option>
            <option value="izin_desc">📝 Terbanyak Izin</option>
            <option value="izin_asc">🎯 Tersedikit Izin</option>
            <option value="total_desc">📚 Terbanyak Hari</option>
            <option value="total_asc">📖 Tersedikit Hari</option>
            <option value="nama_asc">🔤 Nama A-Z</option>
            <option value="nama_desc">🔤 Nama Z-A</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa</label>
          <div class="relative">
            <input type="text" id="search-input" placeholder="Nama siswa atau NISN..." 
                   class="w-full border-2 border-gray-200 rounded-lg px-3 py-2.5 pl-10 focus:border-blue-500 focus:ring-0 transition-colors">
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</div>
          </div>
        </div>
        <div class="flex items-end gap-2">
          <button type="button" onclick="loadData()" class="flex-1 sm:flex-none bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all">📊 Tampilkan Data</button>
          <button type="button" onclick="openDetailHari()" class="flex-1 sm:flex-none bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all text-center">📋 Detail Hari</button>
          <button type="button" onclick="exportRekap('pdf')" class="flex-1 sm:flex-none bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all text-center">� PDF</button>
          <button type="button" onclick="exportRekap('excel')" class="flex-1 sm:flex-none bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white px-6 py-2.5 rounded-lg font-medium transition-all text-center">📥 Excel</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Stats Cards -->
  <?php $st = $data['statistik'] ?? ['total_siswa'=>0,'rata_persen'=>0,'sangat_baik'=>0,'perlu_perhatian'=>0]; ?>
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs sm:text-sm opacity-90 mb-1">Total Siswa</div>
          <div class="text-xl sm:text-3xl font-bold" id="total-siswa"><?= (int)$st['total_siswa']; ?></div>
        </div>
        <div class="text-2xl sm:text-4xl opacity-80">👥</div>
      </div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs sm:text-sm opacity-90 mb-1">Rata-rata Hadir</div>
          <div class="text-xl sm:text-3xl font-bold" id="rata-hadir"><?= number_format((float)$st['rata_persen'],1); ?>%</div>
        </div>
        <div class="text-2xl sm:text-4xl opacity-80">📈</div>
      </div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs sm:text-sm opacity-90 mb-1">Sangat Baik</div>
          <div class="text-xl sm:text-3xl font-bold" id="sangat-baik"><?= (int)$st['sangat_baik']; ?></div>
        </div>
        <div class="text-2xl sm:text-4xl opacity-80">⭐</div>
      </div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 p-4 sm:p-6 rounded-xl shadow-lg text-white">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs sm:text-sm opacity-90 mb-1">Perlu Perhatian</div>
          <div class="text-xl sm:text-3xl font-bold" id="perlu-perhatian"><?= (int)$st['perlu_perhatian']; ?></div>
        </div>
        <div class="text-2xl sm:text-4xl opacity-80">🚨</div>
      </div>
    </div>
  </div>

  <!-- Data Table/Cards -->
  <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-gray-100">
      <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
        Data Performa Harian
      </h2>
      <p id="periode-label" class="text-xs text-gray-500 mt-1">Periode: <?= date('d/m/Y', strtotime($data['start_date'])); ?> - <?= date('d/m/Y', strtotime($data['end_date'])); ?></p>
    </div>
    <div id="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
      <div class="text-gray-600">Memuat data...</div>
    </div>
    <div id="error-state" class="hidden text-center py-12 text-red-500">
      <div class="text-6xl mb-4">⚠️</div>
      <div class="text-lg font-medium mb-2">Terjadi kesalahan</div>
      <div class="text-sm mb-4" id="error-message"></div>
      <button onclick="loadData()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">Coba Lagi</button>
    </div>
    <div id="empty-state" class="hidden text-center py-12 text-gray-500">
      <div class="text-6xl mb-3">📊</div>
      <div class="text-lg font-medium mb-1">Tidak ada data</div>
      <div class="text-sm">Tidak ada data absensi pada periode yang dipilih</div>
    </div>
    <div id="table-container" class="hidden">
      <!-- Desktop table -->
      <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
              <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">NISN</th>
              <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
              <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kelas</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Hari</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Hadir</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Sakit</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Izin</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Alpha</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Persentase</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody id="table-body" class="divide-y divide-gray-200"></tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
      <div id="mobile-cards" class="lg:hidden p-4 space-y-4"></div>
    </div>
  </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 p-2 sm:p-4 lg:p-8">
  <div class="flex items-center justify-center min-h-full">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl max-h-[95vh] overflow-hidden">
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 sm:px-6 py-4 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-lg sm:text-xl font-semibold">Detail Kehadiran Harian</h3>
            <p class="text-blue-100 text-sm">Rincian status per tanggal</p>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" onclick="exportDetailPDF()" class="bg-white/15 hover:bg-white/25 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">Download PDF</button>
            <button type="button" onclick="closeDetailModal()" class="text-white hover:text-gray-200 text-3xl font-light leading-none">&times;</button>
          </div>
        </div>
      </div>
      <div class="overflow-y-auto max-h-[calc(95vh-120px)]">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="student-info"></div>
          <div id="daily-summary" class="grid grid-cols-5 gap-2 mt-3 text-xs">
            <!-- Summary badges injected via JS -->
          </div>
        </div>
        <div id="detail-loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-600 mb-4"></div>
          <div class="text-gray-600">Memuat detail...</div>
        </div>
        <div id="detail-daily-container" class="hidden">
          <!-- Desktop: per tanggal table -->
          <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                  <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase">No</th>
                  <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                  <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                </tr>
              </thead>
              <tbody id="detail-daily-body" class="divide-y divide-gray-200"></tbody>
            </table>
          </div>
          <!-- Mobile: per tanggal cards -->
          <div id="detail-daily-cards" class="lg:hidden p-4 space-y-2"></div>
        </div>
        <div id="detail-empty" class="hidden text-center py-12 text-gray-500">
          <div class="text-6xl mb-4">📚</div>
          <div class="text-lg font-medium mb-2">Tidak ada data detail</div>
          <div class="text-sm">Tidak ada data kehadiran untuk siswa ini pada periode yang dipilih</div>
        </div>
        <div id="detail-error" class="hidden text-center py-12 text-red-500">
          <div class="text-6xl mb-4">⚠️</div>
          <div class="text-lg font-medium mb-2">Terjadi kesalahan</div>
          <div class="text-sm mb-4" id="detail-error-message"></div>
        </div>
      </div>
    </div>
  </div>
  
</div>
<script>
let allData = [];
let currentData = [];

document.addEventListener('DOMContentLoaded', function() {
  // Set periode default (bulan ini)
  setDefaultDates();
  document.getElementById('period-select').addEventListener('change', handlePeriodChange);
  document.getElementById('search-input').addEventListener('input', filterData);
  document.getElementById('sort-select').addEventListener('change', sortData);
  
  // Use server-side data if available
  const initialData = <?= json_encode($data['rekap_harian'] ?? []); ?>;
  if (initialData && initialData.length > 0) {
    allData = initialData;
    currentData = [...allData];
    sortData();
    showTable();
    updateStatsCards();
    // update periode label
    const sd = new Date('<?= $data['start_date']; ?>');
    const ed = new Date('<?= $data['end_date']; ?>');
    const fmt = d => ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2)+'/'+d.getFullYear();
    const label = document.getElementById('periode-label');
    if (label) label.textContent = 'Periode: ' + fmt(sd) + ' - ' + fmt(ed);
  }
});

function setDefaultDates() {
  const startInput = document.getElementById('start-date');
  const endInput = document.getElementById('end-date');
  // Jika nilai sudah di-set dari server, biarkan. Jika kosong, set semester ini.
  if (!startInput.value || !endInput.value) {
    const now = new Date();
    const month = now.getMonth();
    let start, end;
    if (month >= 6) { // Jul-Dec
      start = new Date(now.getFullYear(), 6, 1);
      end = new Date(now.getFullYear(), 11, 31);
    } else { // Jan-Jun
      start = new Date(now.getFullYear(), 0, 1);
      end = new Date(now.getFullYear(), 5, 30);
    }
    startInput.value = start.toISOString().split('T')[0];
    endInput.value = end.toISOString().split('T')[0];
    document.getElementById('period-select').value = 'this_semester';
  }
}

function handlePeriodChange() {
  const period = document.getElementById('period-select').value;
  const now = new Date();
  let start, end;
  switch(period) {
    case 'today':
      start = end = now.toISOString().split('T')[0];
      break;
    case 'this_week':
      const d = new Date();
      const day = d.getDay();
      const diffToMonday = (day === 0 ? 6 : day - 1);
      const startOfWeek = new Date(d);
      startOfWeek.setDate(d.getDate() - diffToMonday);
      const endOfWeek = new Date(startOfWeek);
      endOfWeek.setDate(startOfWeek.getDate() + 6);
      start = startOfWeek.toISOString().split('T')[0];
      end = endOfWeek.toISOString().split('T')[0];
      break;
    case 'this_month':
      start = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
      end = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
      break;
    case 'this_semester':
      const month = now.getMonth(); // 0-based
      if (month >= 6) { // Jul-Dec semester ganjil? atau genap tergantung definisi lokal
        start = new Date(now.getFullYear(), 6, 1).toISOString().split('T')[0];
        end = new Date(now.getFullYear(), 11, 31).toISOString().split('T')[0];
      } else {
        start = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
        end = new Date(now.getFullYear(), 5, 30).toISOString().split('T')[0];
      }
      break;
    case 'custom':
      return;
  }
  document.getElementById('start-date').value = start;
  document.getElementById('end-date').value = end;
  loadData(); // auto-reload when period changes
}

let allData = [];
let currentData = [];

function showLoading(){
  document.getElementById('loading').classList.remove('hidden');
  document.getElementById('table-container').classList.add('hidden');
  document.getElementById('empty-state').classList.add('hidden');
  document.getElementById('error-state').classList.add('hidden');
}
function showError(msg){
  document.getElementById('loading').classList.add('hidden');
  document.getElementById('table-container').classList.add('hidden');
  document.getElementById('empty-state').classList.add('hidden');
  document.getElementById('error-state').classList.remove('hidden');
  document.getElementById('error-message').textContent = msg || 'Terjadi kesalahan';
}
function showTable(){
  document.getElementById('loading').classList.add('hidden');
  if (!currentData.length){
    document.getElementById('empty-state').classList.remove('hidden');
    document.getElementById('table-container').classList.add('hidden');
    return;
  }
  document.getElementById('empty-state').classList.add('hidden');
  document.getElementById('table-container').classList.remove('hidden');
  renderTable();
}

function loadData(){
  const startDate = document.getElementById('start-date').value;
  const endDate = document.getElementById('end-date').value;
  if (!startDate || !endDate){ alert('Pilih tanggal terlebih dahulu'); return; }
  showLoading();
  fetch('<?= BASEURL; ?>/waliKelas/getRekapHarianData',{
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
    body: new URLSearchParams({start_date:startDate, end_date:endDate})
  }).then(r=>r.json()).then(res=>{
    if (res.status==='success'){
      allData = res.data || [];
      // update periode label
      const sd = new Date(startDate); const ed = new Date(endDate);
      const fmt = d=> ('0'+d.getDate()).slice(-2)+'/'+('0'+(d.getMonth()+1)).slice(-2)+'/'+d.getFullYear();
      const label = document.getElementById('periode-label');
      if (label) label.textContent = 'Periode: ' + fmt(sd) + ' - ' + fmt(ed);
      filterData(); // includes sorting & rendering
    } else { showError(res.message || 'Gagal memuat data'); }
  }).catch(e=> showError(e.message));
}

function sortData(){
  const sortBy = document.getElementById('sort-select').value;
  currentData.sort((a,b)=>{
    switch(sortBy){
      case 'persentase_desc': return parseFloat(b.persentase_hadir)-parseFloat(a.persentase_hadir);
      case 'persentase_asc': return parseFloat(a.persentase_hadir)-parseFloat(b.persentase_hadir);
      case 'hadir_desc': return (b.hadir|0)-(a.hadir|0);
      case 'hadir_asc': return (a.hadir|0)-(b.hadir|0);
      case 'alfa_desc': return (b.alfa|0)-(a.alfa|0);
      case 'alfa_asc': return (a.alfa|0)-(b.alfa|0);
      case 'sakit_desc': return (b.sakit|0)-(a.sakit|0);
      case 'sakit_asc': return (a.sakit|0)-(b.sakit|0);
      case 'izin_desc': return (b.izin|0)-(a.izin|0);
      case 'izin_asc': return (a.izin|0)-(b.izin|0);
      case 'total_desc': return (b.total_hari|0)-(a.total_hari|0);
      case 'total_asc': return (a.total_hari|0)-(b.total_hari|0);
      case 'nama_asc': return (a.nama_siswa||'').localeCompare(b.nama_siswa||'');
      case 'nama_desc': return (b.nama_siswa||'').localeCompare(a.nama_siswa||'');
      default: return 0;
    }
  });
  renderTable();
}

function filterData(){
  const q = (document.getElementById('search-input').value||'').toLowerCase();
  currentData = !q ? [...allData] : allData.filter(r=> (r.nama_siswa||'').toLowerCase().includes(q) || (r.nisn||'').toLowerCase().includes(q));
  sortData();
  showTable();
  updateStatsCards();
}

function renderTable(){
  const tbody = document.getElementById('table-body');
  const cards = document.getElementById('mobile-cards');
  if (!tbody) return;
  tbody.innerHTML = currentData.map((row, i)=>{
    const persen = parseFloat(row.persentase_hadir||0);
    let statusTxt='Perlu Perhatian', cls='bg-red-100 text-red-800';
    if (persen>=95){ statusTxt='Sangat Baik'; cls='bg-green-100 text-green-800'; }
    else if (persen>=85){ statusTxt='Baik'; cls='bg-blue-100 text-blue-800'; }
    else if (persen>=75){ statusTxt='Cukup'; cls='bg-yellow-100 text-yellow-800'; }
    return `<tr class="hover:bg-gray-50 transition-colors" data-id_siswa="${row.id_siswa||0}">
      <td class="px-4 py-4 text-sm">${i+1}</td>
      <td class="px-4 py-4 text-sm">${row.nisn||'-'}</td>
      <td class="px-4 py-4 text-sm font-medium text-gray-900">${row.nama_siswa||'-'}</td>
      <td class="px-4 py-4 text-sm">${row.nama_kelas||'-'}</td>
      <td class="px-4 py-4 text-sm text-center">${row.total_hari||0}</td>
      <td class="px-4 py-4 text-sm text-center text-green-600 font-semibold">${row.hadir||0}</td>
      <td class="px-4 py-4 text-sm text-center text-orange-500">${row.sakit||0}</td>
      <td class="px-4 py-4 text-sm text-center text-blue-500">${row.izin||0}</td>
      <td class="px-4 py-4 text-sm text-center text-red-500 font-semibold">${row.alfa||0}</td>
      <td class="px-4 py-4 text-sm text-center"><span class="px-3 py-1 rounded-full text-xs font-medium ${cls}">${persen.toFixed(1)}%</span></td>
      <td class="px-4 py-4 text-sm text-center"><span class="px-3 py-1 rounded-full text-xs font-medium ${cls}">${statusTxt}</span></td>
      <td class="px-4 py-4 text-sm text-center"><button type="button" onclick="showDetail(${row.id_siswa||0})" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all">📋 Detail</button></td>
    </tr>`;
  }).join('');

  cards.innerHTML = currentData.map(row=>{
    const persen = parseFloat(row.persentase_hadir||0);
    let status='Perlu Perhatian', statusClass='text-red-700', bg='from-red-50 to-red-100';
    if (persen>=95){ status='Sangat Baik'; statusClass='text-green-700'; bg='from-green-50 to-green-100'; }
    else if (persen>=85){ status='Baik'; statusClass='text-blue-700'; bg='from-blue-50 to-blue-100'; }
    else if (persen>=75){ status='Cukup'; statusClass='text-yellow-700'; bg='from-yellow-50 to-yellow-100'; }
    return `<div class="bg-gradient-to-r ${bg} rounded-xl p-4 border border-gray-200 shadow-sm">
      <div class="flex justify-between items-start mb-3">
        <div>
          <div class="font-bold text-gray-900 text-base">${row.nama_siswa||'-'}</div>
          <div class="text-sm text-gray-600">NISN: ${row.nisn||'-'}</div>
          <div class="text-sm text-gray-600">Kelas: ${row.nama_kelas||'-'}</div>
        </div>
        <div class="text-right">
          <div class="text-2xl font-bold ${statusClass}">${persen.toFixed(1)}%</div>
          <div class="text-xs ${statusClass} font-medium">${status}</div>
        </div>
      </div>
      <div class="grid grid-cols-5 gap-2 text-center text-xs">
        <div class="bg-white rounded-lg p-2"><div class="font-semibold text-gray-600">Total Hari</div><div class="text-gray-800 font-bold">${row.total_hari||0}</div></div>
        <div class="bg-green-100 rounded-lg p-2"><div class="font-semibold text-green-700">Hadir</div><div class="text-green-800 font-bold">${row.hadir||0}</div></div>
        <div class="bg-orange-100 rounded-lg p-2"><div class="font-semibold text-orange-700">Sakit</div><div class="text-orange-800 font-bold">${row.sakit||0}</div></div>
        <div class="bg-blue-100 rounded-lg p-2"><div class="font-semibold text-blue-700">Izin</div><div class="text-blue-800 font-bold">${row.izin||0}</div></div>
        <div class="bg-red-100 rounded-lg p-2"><div class="font-semibold text-red-700">Alpha</div><div class="text-red-800 font-bold">${row.alfa||0}</div></div>
      </div>
      <div class="mt-3 flex justify-end"><button type="button" onclick="showDetail(${row.id_siswa||0})" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-lg text-xs font-medium transition-all">📋 Detail</button></div>
    </div>`;
  }).join('');
}

function updateStatsCards(){
  if (!currentData.length) return;
  const total = currentData.length;
  const avgHadir = (currentData.reduce((s,r)=>s+parseFloat(r.persentase_hadir||0),0)/total).toFixed(1);
  const sangat = currentData.filter(r=>parseFloat(r.persentase_hadir||0)>=95).length;
  const perlu = currentData.filter(r=>parseFloat(r.persentase_hadir||0)<75).length;
  document.getElementById('total-siswa').textContent = total;
  document.getElementById('rata-hadir').textContent = avgHadir + '%';
  document.getElementById('sangat-baik').textContent = sangat;
  document.getElementById('perlu-perhatian').textContent = perlu;
}

function exportRekap(format){
  const startDate = document.getElementById('start-date').value;
  const endDate = document.getElementById('end-date').value;
  const url = '<?= BASEURL; ?>/waliKelas/exportAbsensiHarianRekap?format=' + encodeURIComponent(format) + '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
  window.open(url, '_blank');
}

function openDetailHari(){
  const tanggal = document.getElementById('start-date').value;
  if (!tanggal) return;
  window.location.href = '<?= BASEURL; ?>/waliKelas/absensiHarian?mode=detail&tanggal=' + encodeURIComponent(tanggal);
}

// Detail modal logic
function showDetail(id_siswa) {
  if (!id_siswa) return;
  window.__currentDetailSiswaId = id_siswa;
  const startDate = document.getElementById('start-date').value;
  const endDate = document.getElementById('end-date').value;
  const modal = document.getElementById('detail-modal');
  modal.classList.remove('hidden');
  document.body.style.overflow = 'hidden';

  document.getElementById('detail-loading').classList.remove('hidden');
  var dailyContainer = document.getElementById('detail-daily-container');
  if (dailyContainer) dailyContainer.classList.add('hidden');
  document.getElementById('detail-empty').classList.add('hidden');
  document.getElementById('detail-error').classList.add('hidden');

  fetch('<?= BASEURL; ?>/waliKelas/getDetailAbsensi', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_siswa, start_date: startDate, end_date: endDate })
  })
  .then(r => r.json())
  .then(res => {
    document.getElementById('detail-loading').classList.add('hidden');
    if (res.status === 'success') {
      // Simpan summary agar bisa dipakai di render
      window.lastDailySummary = res.daily_summary || {};
      renderDetail(res.siswa_info || {}, res.detail_data || [], res.daily_status || []);
    } else {
      showDetailError(res.message || 'Gagal memuat detail');
    }
  })
  .catch(err => {
    document.getElementById('detail-loading').classList.add('hidden');
    showDetailError('Terjadi kesalahan: ' + err.message);
  })
  .finally(() => {
    // Safety timeout: jika dalam 8 detik belum render apa pun, tampilkan error umum
    setTimeout(() => {
      const loader = document.getElementById('detail-loading');
      const daily = document.getElementById('detail-daily-container');
      const errorState = document.getElementById('detail-error');
      if (loader && !loader.classList.contains('hidden') && daily && daily.classList.contains('hidden') && errorState && errorState.classList.contains('hidden')) {
        loader.classList.add('hidden');
        showDetailError('Timeout memuat data. Coba lagi.');
      }
    }, 8000);
  });
}

function exportDetailPDF() {
  const id_siswa = window.__currentDetailSiswaId;
  if (!id_siswa) return;
  const startDate = document.getElementById('start-date').value;
  const endDate = document.getElementById('end-date').value;
  const url = '<?= BASEURL; ?>/waliKelas/exportAbsensiHarianDetail?id_siswa=' + encodeURIComponent(id_siswa) + '&start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);
  window.open(url, '_blank');
}

function renderDetail(siswaInfo, detailData, dailyStatus) {
  const info = document.getElementById('student-info');
  info.innerHTML = `
    <div class="bg-white rounded-lg p-4 shadow-sm">
      <div class="text-sm text-gray-500 mb-1">👤 Nama Siswa</div>
      <div class="font-bold text-gray-800">${(siswaInfo.nama_siswa || '-')}</div>
    </div>
    <div class="bg-white rounded-lg p-4 shadow-sm">
      <div class="text-sm text-gray-500 mb-1">🎫 NISN</div>
      <div class="font-bold text-gray-800">${(siswaInfo.nisn || '-')}</div>
    </div>
    <div class="bg-white rounded-lg p-4 shadow-sm">
      <div class="text-sm text-gray-500 mb-1">🏫 Kelas</div>
      <div class="font-bold text-gray-800">${(siswaInfo.nama_kelas || '-')}</div>
    </div>
  `;

  // Ringkasan status
  const summary = siswaInfo.daily_summary || window.lastDailySummary || {};
  const summaryDiv = document.getElementById('daily-summary');
  const mapLabel = { H: 'Hadir', S: 'Sakit', I: 'Izin', A: 'Alpha', BELUM: 'Belum Diisi' };
  const mapColor = { 
    H: 'bg-green-100 text-green-700', 
    S: 'bg-orange-100 text-orange-700', 
    I: 'bg-blue-100 text-blue-700', 
    A: 'bg-red-100 text-red-700', 
    BELUM: 'bg-gray-100 text-gray-600'
  };
  summaryDiv.innerHTML = Object.keys(mapLabel).map(k => {
    const val = summary[k] || 0;
    return `<div class="flex flex-col bg-white rounded-lg border border-gray-200 p-2 text-center">
      <div class="text-[10px] font-semibold text-gray-500">${mapLabel[k]}</div>
      <div class="mt-1 text-xs font-bold px-2 py-1 rounded ${mapColor[k]}">${val}</div>
    </div>`;
  }).join('');

  const dailyContainer = document.getElementById('detail-daily-container');
  dailyContainer.classList.remove('hidden');
  const tbody = document.getElementById('detail-daily-body');
  const cards = document.getElementById('detail-daily-cards');

  if (!dailyStatus || dailyStatus.length === 0) {
    document.getElementById('detail-empty').classList.remove('hidden');
    return;
  }

  // Desktop table rows
  tbody.innerHTML = dailyStatus.map((row, idx) => {
    const tanggalFormatted = new Date(row.tanggal).toLocaleDateString('id-ID');
    let label, cls;
    switch(row.daily_status) {
      case 'S': label='Sakit'; cls='bg-orange-100 text-orange-700'; break;
      case 'I': label='Izin'; cls='bg-blue-100 text-blue-700'; break;
      case 'A': label='Alpha'; cls='bg-red-100 text-red-700'; break;
      case 'H': label='Hadir'; cls='bg-green-100 text-green-700'; break;
      default: label='Belum Diisi'; cls='bg-gray-100 text-gray-600'; break;
    }
    return `<tr class="hover:bg-gray-50 transition-colors">
      <td class="px-4 py-3 text-sm">${idx+1}</td>
      <td class="px-4 py-3 text-sm font-medium text-gray-700">${tanggalFormatted}</td>
      <td class="px-4 py-3 text-sm text-center"><span class="px-3 py-1 rounded-full text-xs font-semibold ${cls}">${label}</span></td>
    </tr>`;
  }).join('');

  // Mobile cards
  cards.innerHTML = dailyStatus.map(row => {
    const tanggalFormatted = new Date(row.tanggal).toLocaleDateString('id-ID');
    let label, cls, gradient;
    switch(row.daily_status) {
      case 'S': label='Sakit'; cls='text-orange-700'; gradient='from-orange-50 to-orange-100'; break;
      case 'I': label='Izin'; cls='text-blue-700'; gradient='from-blue-50 to-blue-100'; break;
      case 'A': label='Alpha'; cls='text-red-700'; gradient='from-red-50 to-red-100'; break;
      case 'H': label='Hadir'; cls='text-green-700'; gradient='from-green-50 to-green-100'; break;
      default: label='Belum Diisi'; cls='text-gray-600'; gradient='from-gray-50 to-gray-100'; break;
    }
    return `<div class="bg-gradient-to-r ${gradient} rounded-lg p-3 border border-gray-200 flex items-center justify-between">
      <div class="text-xs font-medium text-gray-700">${tanggalFormatted}</div>
      <div class="text-xs font-semibold ${cls}">${label}</div>
    </div>`;
  }).join('');
}

function showDetailError(message) {
  document.getElementById('detail-error').classList.remove('hidden');
  document.getElementById('detail-error-message').textContent = message;
}

function closeDetailModal() {
  document.getElementById('detail-modal').classList.add('hidden');
  document.body.style.overflow = '';
}

document.getElementById('detail-modal').addEventListener('click', function(e) {
  if (e.target === this) closeDetailModal();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && !document.getElementById('detail-modal').classList.contains('hidden')) {
    closeDetailModal();
  }
});
</script>

<style>
/* Match performa siswa interactions */
.animate-spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
* { transition: all 0.2s ease-in-out; }
input:focus, select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
@media (max-width: 640px) {
  #detail-modal .bg-white { margin: 0.5rem; max-height: calc(100vh - 1rem); }
}
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
button:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>
