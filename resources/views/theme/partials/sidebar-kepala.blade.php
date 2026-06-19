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
            <a href="{{ route('kepala-bumdes.data-mitra') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.data-mitra') ? 'active' : '' }}">
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
<div id="sidebarOverlay" class="sidebar-overlay"></div>

{{-- Tombol hamburger mobile --}}
<button id="sidebarToggle" class="sidebar-toggle-btn" aria-label="Buka menu">
    <i class="fas fa-bars"></i>
</button>

{{-- ========================================================
     CSS khusus toggle mobile — inline biar gak gantung file
     style.css / build pipeline lain. Aman ditaruh di sini.
     ======================================================== --}}
<style>
.sidebar-toggle-btn {
    display: none;
    position: fixed;
    top: 20px;
    left: 16px;
    z-index: 200;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 12px;
    background: #059669;
    color: #fff;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    box-shadow: 0 8px 20px rgba(5, 150, 105, 0.35);
    cursor: pointer;
    transition: background 0.2s ease;
}
.sidebar-toggle-btn:hover { background: #047857; }

.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(2px);
    z-index: 90;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.sidebar-overlay.show {
    display: block;
    opacity: 1;
}

@media (max-width: 768px) {
    .app-sidebar {
        position: fixed !important;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        max-width: 80vw;
        z-index: 100;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto;
    }

    .app-sidebar.sidebar-open {
        transform: translateX(0);
        box-shadow: 8px 0 30px rgba(0, 0, 0, 0.25);
    }

    .sidebar-toggle-btn {
        display: flex;
    }

    .sidebar-logo-close {
        display: inline-flex;
    }

    .app-main,
    .main-content {
        margin-left: 0 !important;
    }
}

@media (min-width: 769px) {
    .sidebar-toggle-btn,
    .sidebar-overlay {
        display: none !important;
    }
}
</style>

<script>
(function () {
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('app-sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggle.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);

    // dipakai juga oleh tombol X di dalam sidebar (onclick="closeSidebar()")
    window.closeSidebar = closeSidebar;

    // tutup otomatis kalau resize ke desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) closeSidebar();
    });
})();
</script>