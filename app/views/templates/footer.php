<script>
    // Initialize Lucide icons when loaded
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    } else {
        // Fallback: wait for lucide to load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }

    // Tambahkan script ini di footer template atau sebagai file JS terpisah

// Enhanced Statistics Functions
class StatisticsManager {
    constructor() {
        this.init();
    }

    init() {
        this.animateProgressBars();
        this.setupTooltips();
        this.setupFilters();
        this.setupSorting();
        this.setupExport();
    }

    // Animate progress bars with stagger effect
    animateProgressBars() {
        const progressBars = document.querySelectorAll('[style*="width:"]');
        progressBars.forEach((bar, index) => {
            const width = bar.style.width;
            bar.style.width = '0%';
            bar.style.transition = 'width 1s ease-in-out';
            
            setTimeout(() => {
                bar.style.width = width;
            }, 300 + (index * 100));
        });
    }

    // Setup tooltips untuk informasi tambahan
    setupTooltips() {
        // Simple tooltip implementation
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            let tooltip = null;

            element.addEventListener('mouseenter', (e) => {
                tooltip = document.createElement('div');
                tooltip.className = 'fixed z-50 px-2 py-1 text-xs text-white bg-black rounded shadow-lg pointer-events-none';
                tooltip.textContent = e.target.getAttribute('data-tooltip');
                document.body.appendChild(tooltip);

                const updatePosition = (event) => {
                    tooltip.style.left = event.clientX + 10 + 'px';
                    tooltip.style.top = event.clientY - 30 + 'px';
                };

                updatePosition(e);
                element.addEventListener('mousemove', updatePosition);
            });

            element.addEventListener('mouseleave', () => {
                if (tooltip) {
                    document.body.removeChild(tooltip);
                    tooltip = null;
                }
            });
        });
    }

    // Setup filter berdasarkan tingkat kehadiran
    setupFilters() {
        const filterContainer = document.getElementById('attendance-filter');
        if (!filterContainer) return;

        const filters = [
            { label: 'Semua', value: 'all', color: 'bg-gray-500' },
            { label: 'Excellent (≥90%)', value: 'excellent', color: 'bg-green-500' },
            { label: 'Good (75-89%)', value: 'good', color: 'bg-blue-500' },
            { label: 'Fair (60-74%)', value: 'fair', color: 'bg-yellow-500' },
            { label: 'Poor (<60%)', value: 'poor', color: 'bg-red-500' }
        ];

        filters.forEach(filter => {
            const button = document.createElement('button');
            button.className = `px-3 py-1 text-xs rounded-full text-white ${filter.color} hover:opacity-80 transition`;
            button.textContent = filter.label;
            button.addEventListener('click', () => this.filterByAttendance(filter.value));
            filterContainer.appendChild(button);
        });
    }

    // Filter cards berdasarkan tingkat kehadiran
    filterByAttendance(filterValue) {
        const cards = document.querySelectorAll('[data-attendance-rate]');
        
        cards.forEach(card => {
            const rate = parseFloat(card.getAttribute('data-attendance-rate'));
            let show = true;

            switch(filterValue) {
                case 'excellent':
                    show = rate >= 90;
                    break;
                case 'good':
                    show = rate >= 75 && rate < 90;
                    break;
                case 'fair':
                    show = rate >= 60 && rate < 75;
                    break;
                case 'poor':
                    show = rate < 60;
                    break;
                default:
                    show = true;
            }

            card.style.display = show ? 'block' : 'none';
        });
    }

    // Setup sorting untuk table
    setupSorting() {
        const tables = document.querySelectorAll('table[data-sortable="true"]');
        
        tables.forEach(table => {
            const headers = table.querySelectorAll('th[data-sort]');
            
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    const column = header.getAttribute('data-sort');
                    const currentOrder = header.getAttribute('data-order') || 'asc';
                    const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
                    
                    this.sortTable(table, column, newOrder);
                    
                    // Update header attributes
                    headers.forEach(h => h.removeAttribute('data-order'));
                    header.setAttribute('data-order', newOrder);
                    
                    // Update header arrow
                    headers.forEach(h => {
                        const arrow = h.querySelector('.sort-arrow');
                        if (arrow) arrow.remove();
                    });
                    
                    const arrow = document.createElement('span');
                    arrow.className = 'sort-arrow ml-1';
                    arrow.innerHTML = newOrder === 'asc' ? '↑' : '↓';
                    header.appendChild(arrow);
                });
            });
        });
    }

    // Sort table by column
    sortTable(table, column, order) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.querySelector(`td[data-${column}]`)?.getAttribute(`data-${column}`) || 
                          a.querySelector(`td:nth-child(${this.getColumnIndex(table, column)})`)?.textContent || '';
            const bValue = b.querySelector(`td[data-${column}]`)?.getAttribute(`data-${column}`) || 
                          b.querySelector(`td:nth-child(${this.getColumnIndex(table, column)})`)?.textContent || '';
            
            const aNum = parseFloat(aValue);
            const bNum = parseFloat(bValue);
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                return order === 'asc' ? aNum - bNum : bNum - aNum;
            }
            
            return order === 'asc' ? 
                aValue.localeCompare(bValue) : 
                bValue.localeCompare(aValue);
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }

    // Get column index by data-sort attribute
    getColumnIndex(table, column) {
        const headers = table.querySelectorAll('th[data-sort]');
        for (let i = 0; i < headers.length; i++) {
            if (headers[i].getAttribute('data-sort') === column) {
                return i + 1;
            }
        }
        return 1;
    }

    // Setup export functionality
    setupExport() {
        const exportBtn = document.getElementById('export-stats');
        if (!exportBtn) return;

        exportBtn.addEventListener('click', () => {
            this.exportToCSV();
        });
    }

    // Export statistics to CSV
    exportToCSV() {
        const data = this.collectStatisticsData();
        const csv = this.convertToCSV(data);
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `statistik-kehadiran-${new Date().getFullYear()}-${new Date().getMonth() + 1}-${new Date().getDate()}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    // Collect statistics data from DOM
    collectStatisticsData() {
        const data = [];
        const cards = document.querySelectorAll('[data-mapel-name]');
        
        cards.forEach(card => {
            const mapelName = card.getAttribute('data-mapel-name');
            const attendanceRate = card.getAttribute('data-attendance-rate');
            const totalMeetings = card.querySelector('[data-total-meetings]')?.textContent || '0';
            const totalStudents = card.querySelector('[data-total-students]')?.textContent || '0';
            const totalPresent = card.querySelector('[data-total-present]')?.textContent || '0';
            const totalPermission = card.querySelector('[data-total-permission]')?.textContent || '0';
            const totalSick = card.querySelector('[data-total-sick]')?.textContent || '0';
            const totalAbsent = card.querySelector('[data-total-absent]')?.textContent || '0';
            
            data.push({
                'Mata Pelajaran': mapelName,
                'Tingkat Kehadiran (%)': attendanceRate,
                'Total Pertemuan': totalMeetings,
                'Total Siswa': totalStudents,
                'Hadir': totalPresent,
                'Izin': totalPermission,
                'Sakit': totalSick,
                'Alpha': totalAbsent
            });
        });
        
        return data;
    }

    // Convert data to CSV format
    convertToCSV(data) {
        if (data.length === 0) return '';
        
        const headers = Object.keys(data[0]);
        const csvContent = [
            headers.join(','),
            ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
        ].join('\n');
        
        return csvContent;
    }
}

// Enhanced toggle function
function toggleStatDetail(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const isHidden = element.classList.contains('hidden');
    
    if (isHidden) {
        element.classList.remove('hidden');
        element.style.maxHeight = '0px';
        element.style.opacity = '0';
        element.style.overflow = 'hidden';
        element.style.transition = 'max-height 0.3s ease, opacity 0.3s ease';
        
        // Trigger reflow
        element.offsetHeight;
        
        element.style.maxHeight = element.scrollHeight + 'px';
        element.style.opacity = '1';
        
        setTimeout(() => {
            element.style.maxHeight = 'none';
            element.style.overflow = 'visible';
        }, 300);
    } else {
        element.style.maxHeight = element.scrollHeight + 'px';
        element.style.opacity = '1';
        element.style.overflow = 'hidden';
        element.style.transition = 'max-height 0.3s ease, opacity 0.3s ease';
        
        // Trigger reflow
        element.offsetHeight;
        
        element.style.maxHeight = '0px';
        element.style.opacity = '0';
        
        setTimeout(() => {
            element.classList.add('hidden');
        }, 300);
    }
}

// Sidebar mobile toggle handlers
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var openBtn = document.getElementById('menu-button');
    var closeBtn = document.getElementById('sidebar-toggle-btn');
    var overlay = document.getElementById('mobile-overlay');

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (openBtn) {
        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openSidebar();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            closeSidebar();
        });
    }

    // Close sidebar on navigation (mobile)
    if (sidebar) {
        sidebar.querySelectorAll('a[href]').forEach(function(link) {
            link.addEventListener('click', function() {
                closeSidebar();
            });
        });
    }
});

// Print with better formatting
function printStatistics() {
    const printContent = document.querySelector('main').innerHTML;
    const originalContent = document.body.innerHTML;
    
    // Create print-friendly version
    const printWindow = window.open('', '_blank');
    const tailwindCDN = '<script src="https://cdn.tailwindcss.com"></' + 'script>';
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Statistik Kehadiran - ${new Date().toLocaleDateString('id-ID')}</title>
            <link href="https://cdn.tailwindcss.com/2.2.19/tailwind.min.css" rel="stylesheet">
            <style>
                @media print {
                    body { font-size: 12px; }
                    .no-print { display: none !important; }
                    .grid { display: block !important; }
                    .grid > div { page-break-inside: avoid; margin-bottom: 20px; }
                }
            </style>
        </head>
        <body class="p-4">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold">Statistik Kehadiran Siswa</h1>
                <p class="text-gray-600">Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
            </div>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const statsManager = new StatisticsManager();
    console.log('Statistics Manager initialized');
});

// Global functions
window.toggleStatDetail = toggleStatDetail;
window.printStatistics = printStatistics;

// Profile dropdown toggle (top-right menu)
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('profile-button');
    const dropdown = document.getElementById('profile-dropdown');
    const chevron = document.getElementById('chevron-icon');

    if (!btn || !dropdown) return;

    function openDropdown() {
        dropdown.classList.add('active');
        if (chevron) chevron.style.transform = 'rotate(180deg)';
        btn.setAttribute('aria-expanded', 'true');
    }

    function closeDropdown() {
        dropdown.classList.remove('active');
        if (chevron) chevron.style.transform = '';
        btn.setAttribute('aria-expanded', 'false');
    }

    function toggleDropdown(e) {
        e.preventDefault();
        e.stopPropagation();
        if (dropdown.classList.contains('active')) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    btn.addEventListener('click', toggleDropdown);
    // Also support touchstart for some mobile browsers
    btn.addEventListener('touchstart', function(e) {
        // Prevent the subsequent click from double-toggling
        e.preventDefault();
        toggleDropdown(e);
    }, { passive: false });

    // Close when clicking outside
    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
            closeDropdown();
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    // Close on page scroll (useful on mobile)
    window.addEventListener('scroll', function() {
        if (dropdown.classList.contains('active')) {
            closeDropdown();
        }
    }, { passive: true });
});
</script>
