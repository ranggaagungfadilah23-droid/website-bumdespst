{{-- theme/partials/sidebar-account-menu.blade.php --}}
@php
    $avatarLetter = strtoupper(substr(Auth::user()->name ?? ($fallbackInitial ?? 'U'), 0, 1));
    $displayName  = Auth::user()->name ?? ($fallbackName ?? 'User');
    $displayEmail = Auth::user()->email ?? null;
@endphp

<div class="sidebar-profile-wrap">
    <button type="button" class="sidebar-profile" id="profileMenuTrigger" onclick="toggleProfileMenu()" aria-haspopup="true" aria-expanded="false">
        <div class="sidebar-avatar">{{ $avatarLetter }}</div>
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name">{{ $displayName }}</div>
            <div class="sidebar-profile-role">{{ $roleLabel ?? '-' }}</div>
        </div>
        <i class="fas fa-chevron-up sidebar-profile-caret" id="profileMenuCaret"></i>
    </button>

    <div class="sidebar-profile-menu" id="profileMenu" role="menu">
        <div class="sidebar-profile-menu-header">
            <div class="sidebar-avatar sidebar-avatar--lg">{{ $avatarLetter }}</div>
            <div class="sidebar-profile-menu-header-info">
                <div class="sidebar-profile-menu-name">{{ $displayName }}</div>
                @if($displayEmail)
                    <div class="sidebar-profile-menu-email">{{ $displayEmail }}</div>
                @endif
            </div>
        </div>

        <div class="sidebar-profile-menu-divider"></div>

        @if(Route::has('profile.edit'))
            <a href="{{ route('profile.edit') }}" class="sidebar-profile-menu-item" role="menuitem">
                <i class="fas fa-user-circle"></i> Edit Profil
            </a>
        @endif

        @if(Route::has('password.edit'))
            <a href="{{ route('password.edit') }}" class="sidebar-profile-menu-item" role="menuitem">
                <i class="fas fa-key"></i> Ganti Password
            </a>
        @endif

        <div class="sidebar-profile-menu-divider"></div>

        @if(Route::has('logout'))
            <form action="{{ route('logout') }}" method="POST" class="sidebar-profile-menu-form">
                @csrf
                <button type="submit" class="sidebar-profile-menu-item sidebar-profile-menu-item--danger" role="menuitem">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        @endif
    </div>
</div>

@once
    @push('styles')
    <style>
        .sidebar-profile-wrap { position: relative; }

        .sidebar-profile {
            width: 100%;
            border: none;
            background: transparent;
            text-align: left;
            font: inherit;
        }

        .sidebar-profile-caret {
            font-size: 10px;
            color: #484f58;
            margin-left: auto;
            flex-shrink: 0;
            transition: transform 0.15s ease;
        }
        .sidebar-profile-caret.is-open { transform: rotate(180deg); }

        .sidebar-profile-menu {
            position: absolute;
            left: 0;
            right: 0;
            bottom: calc(100% + 8px);
            background: #161b22;
            border: 1px solid var(--sidebar-border);
            border-radius: 10px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.4);
            padding: 6px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s;
            z-index: 200;
        }
        .sidebar-profile-menu.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .sidebar-profile-menu-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 8px;
        }
        .sidebar-avatar--lg { width: 36px; height: 36px; font-size: 14px; }
        .sidebar-profile-menu-header-info { overflow: hidden; min-width: 0; }
        .sidebar-profile-menu-name {
            font-size: 13px;
            font-weight: 600;
            color: #e6edf3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-profile-menu-email {
            font-size: 11px;
            color: #8b949e;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-profile-menu-divider {
            height: 1px;
            background: var(--sidebar-border);
            margin: 4px 4px;
        }

        .sidebar-profile-menu-form { margin: 0; }

        .sidebar-profile-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 10px;
            border: none;
            background: transparent;
            border-radius: 7px;
            color: var(--sidebar-text-hover, #e6edf3);
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
            transition: background 0.1s;
        }
        .sidebar-profile-menu-item:hover { background: rgba(255,255,255,0.07); }
        .sidebar-profile-menu-item i { width: 16px; text-align: center; font-size: 13px; color: #8b949e; }

        .sidebar-profile-menu-item--danger { color: #f85149; }
        .sidebar-profile-menu-item--danger i { color: #f85149; }
        .sidebar-profile-menu-item--danger:hover { background: rgba(248,81,73,0.12); }
    </style>
    @endpush

    @push('scripts')
    <script>
        function toggleProfileMenu() {
            const menu   = document.getElementById('profileMenu');
            const caret  = document.getElementById('profileMenuCaret');
            const trigger = document.getElementById('profileMenuTrigger');
            const isOpen = menu.classList.toggle('is-open');
            caret.classList.toggle('is-open', isOpen);
            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        document.addEventListener('click', function (e) {
            const wrap = document.querySelector('.sidebar-profile-wrap');
            if (!wrap) return;
            if (!wrap.contains(e.target)) {
                document.getElementById('profileMenu')?.classList.remove('is-open');
                document.getElementById('profileMenuCaret')?.classList.remove('is-open');
                document.getElementById('profileMenuTrigger')?.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.getElementById('profileMenu')?.classList.remove('is-open');
                document.getElementById('profileMenuCaret')?.classList.remove('is-open');
                document.getElementById('profileMenuTrigger')?.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
    @endpush
@endonce