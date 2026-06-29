{{-- sidebar-admin.blade.php --}}
<aside class="app-sidebar app-sidebar--admin" id="sidebar" aria-label="Navigasi Admin">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-shield-alt"></i>
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
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </div>

        <div class="nav-section">
            <p class="nav-section-label">Verifikasi</p>
            <a href="{{ route('admin.pengajuan') }}"
               class="nav-link {{ request()->routeIs('admin.pengajuan') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Pengajuan Mitra
                @php $pendingCount = \App\Models\User::where('role','mitra')->where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="nav-badge nav-badge-blue">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.mitra.index') }}"
               class="nav-link {{ request()->routeIs('admin.mitra.index') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Data Mitra
            </a>
        </div>

        <div class="nav-section">
            <p class="nav-section-label">Keuangan</p>
            <a href="{{ route('admin.bagihasil') }}"
               class="nav-link {{ request()->routeIs('admin.bagihasil') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd"></i> Bagi Hasil
            </a>
            <a href="{{ route('admin.laporan') }}"
               class="nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> Laporan
            </a>
        </div>

        <div class="nav-section">
            <p class="nav-section-label">Sistem</p>
            <a href="{{ route('admin.histori') }}"
               class="nav-link {{ request()->routeIs('admin.histori') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Histori Aktivitas
            </a>
        </div>

    </div>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-profile">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="sidebar-profile-info">
                <div class="sidebar-profile-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="sidebar-profile-role">Administrator</div>
            </div>
        </div>
    </div>

</aside>
