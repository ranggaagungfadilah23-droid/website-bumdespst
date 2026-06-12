<aside class="app-sidebar" id="app-sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon" style="background:#059669">
            <i class="fas fa-user-tie"></i>
        </div>
        <span class="sidebar-logo-text">BUMDes <span style="color:#3fb950">Patimban</span></span>
        <button class="sidebar-logo-close" onclick="closeSidebar()">
            <i class="fas fa-times" style="font-size:14px"></i>
        </button>
    </div>

    <div class="sidebar-body">

        <div class="nav-section">
            <div class="nav-section-label">Utama</div>
            <a href="{{ route('kepala-bumdes.dashboard') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Otorisasi</div>
            <a href="{{ route('kepala-bumdes.pengajuan') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.pengajuan') ? 'active' : '' }}">
                <i class="fas fa-file-signature"></i> Persetujuan Mitra
                <span class="nav-badge nav-badge-amber">
                    {{ \App\Models\User::where('role', 'mitra')->where('status', 'menunggu_kepala')->count() }}
                </span>
            </a>
            <a href="{{ route('kepala-bumdes.mitra.index') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.mitra.index') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Data Mitra
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-label">Laporan</div>
            <a href="{{ route('kepala-bumdes.laporan-bulanan') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.laporan-bulanan') ? 'active' : '' }}">
                <i class="fas fa-file-contract"></i> Laporan Bulanan
            </a>
            <a href="{{ route('kepala-bumdes.monitoring-keuangan') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.monitoring-keuangan') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> Monitoring Keuangan
            </a>
        </div>

    </div>

    <div class="sidebar-footer">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
            </div>
            <div style="overflow:hidden">
                <div class="sidebar-profile-name">{{ Auth::user()->name ?? 'Kepala BUMDes' }}</div>
                <div class="sidebar-profile-role">Kepala BUMDes</div>
            </div>
        </div>
    </div>
</aside>

{{-- Overlay mobile --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/60 z-[90] hidden md:hidden backdrop-blur-sm"></div>

{{-- Tombol hamburger mobile --}}
<button id="sidebarToggle" class="md:hidden fixed top-5 left-4 z-[200] bg-emerald-600 hover:bg-emerald-700 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-600/40 transition-colors">
    <i class="fas fa-bars"></i>
</button>

<script>
    const toggle   = document.getElementById('sidebarToggle');
    const close    = document.getElementById('sidebarClose');
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    toggle.addEventListener('click', openSidebar);
    close.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
</script>