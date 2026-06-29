{{-- sidebar-kepala.blade.php --}}
<aside class="app-sidebar app-sidebar--kepala" id="sidebar" aria-label="Navigasi Kepala BUMDes">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-user-tie"></i>
        </div>
        <span class="sidebar-logo-text">BUMDes <span class="sidebar-logo-highlight">Patimban</span></span>
        <button type="button" class="sidebar-logo-close" onclick="closeSidebar()" aria-label="Tutup menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <div class="sidebar-body">

        <div class="nav-section">
            <p class="nav-section-label">Utama</p>
            <a href="{{ route('kepala-bumdes.dashboard') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </div>

        <div class="nav-section">
            <p class="nav-section-label">Otorisasi</p>
            <a href="{{ route('kepala-bumdes.pengajuan') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.pengajuan') ? 'active' : '' }}">
                <i class="fas fa-file-signature"></i> Persetujuan Mitra
                @php $pendingKepala = \App\Models\User::where('role', 'mitra')->where('status', 'menunggu_kepala')->count(); @endphp
                @if($pendingKepala > 0)
                    <span class="nav-badge nav-badge-amber">{{ $pendingKepala }}</span>
                @endif
            </a>
            <a href="{{ route('kepala-bumdes.data-mitra') }}"
               class="nav-link {{ request()->routeIs('kepala-bumdes.data-mitra') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Data Mitra
            </a>
        </div>

        <div class="nav-section">
            <p class="nav-section-label">Laporan</p>
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

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
            </div>
            <div class="sidebar-profile-info">
                <div class="sidebar-profile-name">{{ Auth::user()->name ?? 'Kepala BUMDes' }}</div>
                <div class="sidebar-profile-role">Kepala BUMDes</div>
            </div>
        </div>
    </div>

</aside>
