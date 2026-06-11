<link href="{{ asset('css/customer/dashboard.css') }}" rel="stylesheet">

<div class="top-bar">
    <div class="top-bar-inner">
        <div class="logo-text">BUMDes <span>Patimban</span></div>

        @if(!isset($hideSearch) || !$hideSearch)
            <div class="search-bar">
                <select id="search-type">
                    <option value="all">Semua</option>
                    <option value="produk">Produk</option>
                    <option value="jasa">Jasa</option>
                </select>
                <input type="text" id="search-input" placeholder="Cari produk, jasa, atau layanan BUMDes...">
                <button type="button" onclick="if(typeof doSearch === 'function') doSearch();"><i class="fas fa-search"></i></button>
            </div>
        @else
            <div class="flex-1"></div>
        @endif

        <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
            <a href="{{ route('customer.pesanan') }}" style="color:#fff; font-size:13px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px;">
                <i class="fas fa-receipt"></i> <span class="hide-mobile">Pesanan</span>
            </a>

            <a href="{{ route('customer.cart.index') }}" style="position:relative; width:36px; height:36px; background:rgba(255,255,255,0.2); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff;">
                <i class="fas fa-shopping-cart"></i>
                @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count(); @endphp
                @if($cartCount > 0)
                    <span style="position:absolute; top:-4px; right:-4px; width:18px; height:18px; background:#ff4444; color:#fff; font-size:10px; font-weight:700; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #ee4d2d;">{{ $cartCount }}</span>
                @endif
            </a>

            <a href="{{ route('notifications.index') }}" style="position:relative; width:36px; height:36px; background:rgba(255,255,255,0.2); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff;">
                <i class="fas fa-bell"></i>
            </a>

            <div style="position:relative;">
                <div id="profileTrigger" style="width:36px; height:36px; background:rgba(255,255,255,0.25); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; cursor:pointer; font-size:14px; border:2px solid rgba(255,255,255,0.5); user-select:none;">
                    {{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}
                </div>

                <div id="profileMenu" style="display:none; position:absolute; right:0; top:calc(100% + 10px); width:200px; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.15); z-index:9999; padding:8px 0; border:1px solid #eee;">
                    <div style="padding:12px 16px; border-bottom:1px solid #f0f0f0;">
                        <div style="font-size:10px; color:#aaa; font-weight:700; text-transform:uppercase;">Akun Saya</div>
                        <div style="font-size:13px; font-weight:700; color:#333;">{{ Auth::user()->name }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" style="display:flex; align-items:center; gap:10px; padding:10px 16px; font-size:13px; color:#555; text-decoration:none;">
                        <i class="fas fa-user-edit"></i> Edit Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="width:100%; padding:10px 16px; font-size:13px; color:#e53e3e; font-weight:700; background:none; border:none; cursor:pointer; text-align:left;">
                            <i class="fas fa-power-off"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function initProfileMenu() {
        const trigger = document.getElementById('profileTrigger');
        const menu = document.getElementById('profileMenu');

        if (!trigger || !menu) return;

        // Clone untuk hapus listener lama (hindari duplikasi saat Turbo re-render)
        const newTrigger = trigger.cloneNode(true);
        trigger.parentNode.replaceChild(newTrigger, trigger);

        newTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });

        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target) && e.target !== newTrigger) {
                menu.style.display = 'none';
            }
        });
    }

    // Support Turbo maupun non-Turbo
    document.addEventListener('DOMContentLoaded', initProfileMenu);
    document.addEventListener('turbo:load', initProfileMenu);
</script>