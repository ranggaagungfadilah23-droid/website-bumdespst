{{-- theme/navbar.blade.php --}}
<header class="app-topbar">

    {{-- Hamburger mobile --}}
    <button class="topbar-hamburger" onclick="toggleSidebar()" aria-label="Toggle menu">
        <i class="fas fa-bars" style="font-size: 16px;"></i>
    </button>

    {{-- Page title --}}
    <h2 class="topbar-title">
        Dashboard {{ auth()->check() ? ucwords(str_replace('-', ' ', auth()->user()->role)) : 'Guest' }}
    </h2>

    <div class="topbar-spacer"></div>

    {{-- Slot search dari page --}}
    @stack('navbar-search')

    {{-- Cart (customer only) --}}
    @auth
        @if(auth()->user()->role == 'customer')
            <a href="{{ route('customer.pesanan') }}" class="topbar-icon-btn topbar-link" title="Pesanan saya">
                <i class="fas fa-receipt"></i>
            </a>
            <a href="{{ route('customer.cart') }}" class="topbar-icon-btn topbar-link" style="position:relative;" title="Keranjang">
                <i class="fas fa-shopping-cart"></i>
                @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count(); @endphp
                @if($cartCount > 0)
                    <span class="topbar-badge">{{ $cartCount }}</span>
                @endif
            </a>
        @endif
    @endauth

    {{-- Notifikasi --}}
    @php
        $unreadCount = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    @endphp
    <a href="{{ route('notifications.index') }}" class="topbar-icon-btn topbar-link" style="position:relative;" title="Notifikasi">
        <i class="fas fa-bell"></i>
        <span id="notif-badge" class="topbar-badge {{ $unreadCount > 0 ? '' : 'topbar-badge-hidden' }}">
            {{ $unreadCount }}
        </span>
    </a>

    {{-- Profile dropdown --}}
    @php
        $browserToken = request()->cookie('browser_token');
        $aktifAkuns = $browserToken
            ? \App\Models\UserSession::with('user')
                ->where('browser_token', $browserToken)
                ->get()
            : collect();
    @endphp

    <div class="topbar-profile-wrap">
        <div id="profileTrigger" class="topbar-avatar" title="Menu akun">
            @if(Auth::check())
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            @else
                <i class="fas fa-user"></i>
            @endif
        </div>

        <div id="profileMenu" class="topbar-dropdown topbar-dropdown-hidden">

            {{-- Header akun aktif --}}
            <div class="topbar-dropdown-header">
                <p class="topbar-dropdown-label">Akun Aktif</p>
                <p class="topbar-dropdown-name">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="topbar-dropdown-role">{{ ucwords(str_replace('-', ' ', Auth::user()->role ?? '')) }}</p>
            </div>

            {{-- Daftar akun lain yang sudah login --}}
            @if($aktifAkuns->count() > 1)
                <p class="topbar-section-label">Ganti Akun</p>
                @foreach($aktifAkuns as $sesi)
                    @if($sesi->user && $sesi->user_id !== Auth::id())
                        <form method="POST" action="{{ route('switch.account', $sesi->user_id) }}">
                            @csrf
                            <button type="submit" class="topbar-dropdown-item topbar-account-item">
                                <span class="topbar-mini-avatar">
                                    {{ strtoupper(substr($sesi->user->name, 0, 1)) }}
                                </span>
                                <span class="topbar-account-info">
                                    <span class="topbar-account-name">{{ $sesi->user->name }}</span>
                                    <span class="topbar-account-role">{{ ucwords(str_replace('-', ' ', $sesi->user->role)) }}</span>
                                </span>
                                <i class="fas fa-chevron-right" style="font-size:10px;color:#8b949e;margin-left:auto;"></i>
                            </button>
                        </form>
                    @endif
                @endforeach
                <div class="topbar-dropdown-divider"></div>
            @endif

            {{-- Tambah akun lain --}}
            <a href="{{ route('login') }}" class="topbar-dropdown-item">
                <i class="fas fa-plus-circle"></i> Tambah Akun Lain
            </a>

            <div class="topbar-dropdown-divider"></div>

            {{-- Edit profil --}}
            <a href="{{ route('profile.edit') }}" class="topbar-dropdown-item">
                <i class="fas fa-user-edit"></i> Edit Profil
            </a>

            <div class="topbar-dropdown-divider"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="topbar-dropdown-item topbar-dropdown-danger">
                    <i class="fas fa-power-off"></i> Keluar
                </button>
            </form>

        </div>
    </div>

</header>

<style>
.app-topbar {
    height: 52px;
    background: #ffffff;
    border-bottom: 1px solid #d0d7de;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 16px;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    z-index: 50;
}

.topbar-hamburger {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px; height: 32px;
    border-radius: 6px;
    border: none;
    background: none;
    cursor: pointer;
    color: #656d76;
    transition: background 0.1s, color 0.1s;
    flex-shrink: 0;
}
.topbar-hamburger:hover { background: #f3f4f6; color: #1f2328; }
@media (min-width: 768px) { .topbar-hamburger { display: none; } }

.topbar-title {
    font-size: 13px;
    font-weight: 600;
    color: #1f2328;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
}
@media (min-width: 768px) { .topbar-title { max-width: 300px; font-size: 14px; } }

.topbar-spacer { flex: 1; }

.topbar-icon-btn {
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px;
    border: 1px solid #d0d7de;
    background: #fff;
    color: #656d76;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.1s, color 0.1s;
    flex-shrink: 0;
    text-decoration: none;
}
.topbar-icon-btn:hover { background: #f3f4f6; color: #1f2328; }

.topbar-badge {
    position: absolute;
    top: -5px; right: -5px;
    min-width: 17px; height: 17px;
    background: #cf222e;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    border-radius: 20px;
    border: 1.5px solid #fff;
    line-height: 1;
    padding: 0 3px;
}
.topbar-badge-hidden { display: none; }

.topbar-profile-wrap { position: relative; flex-shrink: 0; margin-left: 2px; }

.topbar-avatar {
    width: 32px; height: 32px;
    background: #0d4a2f;
    color: #6ee7b7;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    border: 2px solid #d0d7de;
    transition: border-color 0.15s;
    user-select: none;
}
.topbar-avatar:hover { border-color: #0969da; }

.topbar-dropdown {
    position: absolute;
    right: 0; top: calc(100% + 8px);
    width: 230px;
    background: #fff;
    border: 1px solid #d0d7de;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 200;
    overflow: hidden;
}
.topbar-dropdown-hidden { display: none; }

.topbar-dropdown-header {
    padding: 12px 14px;
    border-bottom: 1px solid #f0f2f4;
}
.topbar-dropdown-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #8b949e;
    margin-bottom: 3px;
}
.topbar-dropdown-name {
    font-size: 13px;
    font-weight: 600;
    color: #1f2328;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.topbar-dropdown-role {
    font-size: 11px;
    color: #656d76;
    margin-top: 1px;
}

/* Label section "Ganti Akun" */
.topbar-section-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #8b949e;
    padding: 8px 14px 2px;
    margin: 0;
}

/* Item akun lain */
.topbar-account-item { align-items: center; gap: 8px; }

.topbar-mini-avatar {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: #0d4a2f;
    color: #6ee7b7;
    font-size: 11px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

.topbar-account-info {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: hidden;
}
.topbar-account-name {
    font-size: 12px;
    font-weight: 600;
    color: #1f2328;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.topbar-account-role {
    font-size: 10px;
    color: #8b949e;
}

.topbar-dropdown-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 14px;
    font-size: 13px;
    color: #1f2328;
    text-decoration: none;
    transition: background 0.1s;
    width: 100%;
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    text-align: left;
}
.topbar-dropdown-item i { font-size: 12px; color: #8b949e; width: 14px; text-align: center; }
.topbar-dropdown-item:hover { background: #f6f8fa; }

.topbar-dropdown-danger { color: #cf222e; }
.topbar-dropdown-danger i { color: #cf222e; }
.topbar-dropdown-danger:hover { background: #fff0f0; }

.topbar-dropdown-divider { border: none; border-top: 1px solid #f0f2f4; margin: 2px 0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.getElementById('profileTrigger');
    const menu    = document.getElementById('profileMenu');
    if (!trigger || !menu) return;

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('topbar-dropdown-hidden');
    });
    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target) && e.target !== trigger) {
            menu.classList.add('topbar-dropdown-hidden');
        }
    });
});
</script>
