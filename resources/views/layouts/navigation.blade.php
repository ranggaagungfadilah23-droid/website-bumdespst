<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-store"></i></div>
        <h2>BUMDes Putra Samudra Patimban</h2>
        <span>BUMDes Marketplace</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="text-decoration: none;">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="{{ route('mitra.index') }}" class="nav-item {{ request()->routeIs('mitra.*') ? 'active' : '' }}" style="text-decoration: none;">
            <i class="fas fa-store-alt"></i> Data Mitra
        </a>
        <a href="{{ route('produk.index') }}" class="nav-item {{ request()->routeIs('produk.*') ? 'active' : '' }}" style="text-decoration: none;">
            <i class="fas fa-box"></i> Produk <span class="nav-badge">24</span>
        </a>
        <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}" style="text-decoration: none;">
            <i class="fas fa-shopping-bag"></i> Orders <span class="nav-badge">7</span>
        </a>
        <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}" style="text-decoration: none;">
            <i class="fas fa-users"></i> Customers
        </a>

        <div class="nav-section-label">Laporan</div>
        <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}" style="text-decoration: none;">
            <i class="fas fa-chart-bar"></i> Laporan
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
            <div class="sidebar-user-info">
                <h4>{{ Auth::user()->name ?? 'Admin BUMDes' }}</h4>
                <span>Super Admin</span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm btn-full" style="margin-top:8px">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </button>
        </form>
    </div>
</aside>
