    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="https://res.cloudinary.com/duxq5a40j/image/upload/v1779851100/logoBumdes_nsewm6.png" type="image/png">
        @include('theme.head')
        @stack('styles')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017.umd.js"></script>

        <style>
            *, *::before, *::after { box-sizing: border-box; }

            :root {
                --sidebar-w: 240px;
                --sidebar-bg: #0d1117;
                --sidebar-border: rgba(255,255,255,0.07);
                --sidebar-text: #8b949e;
                --sidebar-text-hover: #e6edf3;
                --sidebar-active-bg: rgba(33,139,255,0.12);
                --sidebar-active-color: #58a6ff;
                --header-h: 52px;
                --header-bg: #ffffff;
                --header-border: #d0d7de;
                --main-bg: #f6f8fa;
                --text-primary: #1f2328;
                --text-muted: #656d76;
                --border-color: #d0d7de;
                --accent: #0969da;
            }

            html, body { height: 100%; margin: 0; padding: 0; }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
                font-size: 14px;
                background: var(--main-bg);
                color: var(--text-primary);
                -webkit-font-smoothing: antialiased;
            }

            .app-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: var(--sidebar-w);
                max-width: 100%;
                height: 100vh;
                height: 100dvh;
                background: var(--sidebar-bg);
                border-right: 1px solid var(--sidebar-border);
                display: flex;
                flex-direction: column;
                z-index: 110;
                transform: translateX(-100%);
                transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
                -webkit-overflow-scrolling: touch;
                padding-bottom: env(safe-area-inset-bottom, 0);
            }

            @media (min-width: 768px) {
                .app-sidebar {
                    transform: translateX(0);
                    position: sticky;
                    top: 0;
                    height: 100vh;
                    flex-shrink: 0;
                    z-index: 100;
                }
            }

            .app-sidebar.is-open { transform: translateX(0); }

            /* Warna aksen per role */
            .app-sidebar--admin {
                --sidebar-active-color: #60a5fa;
                --sidebar-active-bg: rgba(29, 78, 216, 0.12);
                --sidebar-logo-accent: #1d4ed8;
                --sidebar-logo-highlight: #60a5fa;
                --sidebar-avatar-border-hover: #60a5fa;
            }
            .app-sidebar--mitra {
                --sidebar-active-color: #58a6ff;
                --sidebar-active-bg: rgba(33, 139, 255, 0.12);
                --sidebar-logo-accent: #1f6feb;
                --sidebar-logo-highlight: #58a6ff;
                --sidebar-avatar-border-hover: #58a6ff;
            }
            .app-sidebar--kepala {
                --sidebar-active-color: #3fb950;
                --sidebar-active-bg: rgba(5, 150, 105, 0.12);
                --sidebar-logo-accent: #059669;
                --sidebar-logo-highlight: #3fb950;
                --sidebar-avatar-border-hover: #3fb950;
            }

            .sidebar-logo {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 16px;
                border-bottom: 1px solid var(--sidebar-border);
                flex-shrink: 0;
            }
            .sidebar-logo-icon {
                width: 32px; height: 32px;
                border-radius: 8px;
                background: var(--sidebar-logo-accent, #1f6feb);
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0;
                font-size: 14px;
                color: #fff;
            }
            .sidebar-logo-text {
                font-size: 13px;
                font-weight: 600;
                color: #e6edf3;
                letter-spacing: -0.01em;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                min-width: 0;
            }
            .sidebar-logo-highlight { color: var(--sidebar-logo-highlight, #58a6ff); }
            .sidebar-logo-close {
                margin-left: auto;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 36px; height: 36px;
                border: none;
                background: transparent;
                border-radius: 8px;
                cursor: pointer;
                color: var(--sidebar-text);
                transition: background 0.1s, color 0.1s;
                flex-shrink: 0;
                -webkit-tap-highlight-color: transparent;
            }
            .sidebar-logo-close i { font-size: 16px; }
            .sidebar-logo-close:hover,
            .sidebar-logo-close:active { background: rgba(255,255,255,0.07); color: var(--sidebar-text-hover); }

            @media (min-width: 768px) {
                .sidebar-logo-close { display: none; }
                .sidebar-logo-icon { width: 28px; height: 28px; border-radius: 6px; }
            }

            .sidebar-body {
                flex: 1;
                overflow-y: auto;
                padding: 8px 8px 12px;
                scrollbar-width: none;
            }
            .sidebar-body::-webkit-scrollbar { display: none; }

            .nav-section { padding: 12px 0 4px; }
            .nav-section:first-child { padding-top: 4px; }

            .nav-section-label {
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 0.07em;
                text-transform: uppercase;
                color: #484f58;
                padding: 0 8px;
                margin-bottom: 4px;
            }

            .nav-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 10px;
                min-height: 44px;
                border-radius: 8px;
                color: var(--sidebar-text);
                font-size: 13px;
                font-weight: 500;
                text-decoration: none;
                transition: background 0.1s, color 0.1s;
                position: relative;
                margin-bottom: 2px;
                -webkit-tap-highlight-color: transparent;
            }

            @media (min-width: 768px) {
                .nav-link {
                    gap: 8px;
                    padding: 7px 8px;
                    min-height: unset;
                    border-radius: 6px;
                    margin-bottom: 1px;
                }
            }
            .nav-link:hover { background: rgba(255,255,255,0.06); color: var(--sidebar-text-hover); }
            .nav-link.active {
                background: var(--sidebar-active-bg);
                color: var(--sidebar-active-color);
            }
            .nav-link.active::before {
                content: '';
                position: absolute;
                left: 0; top: 5px; bottom: 5px;
                width: 2px;
                background: var(--sidebar-active-color);
                border-radius: 0 2px 2px 0;
            }
            .nav-link i { font-size: 15px; width: 18px; text-align: center; flex-shrink: 0; }

            .nav-badge {
                margin-left: auto;
                font-size: 10px;
                font-weight: 600;
                padding: 1px 7px;
                border-radius: 20px;
                line-height: 1.6;
            }
            .nav-badge-amber { background: rgba(154,103,0,0.2); color: #f0883e; }
            .nav-badge-blue { background: rgba(31,111,235,0.15); color: #58a6ff; }
            .nav-badge-green { background: rgba(31,136,61,0.15); color: #3fb950; }

            .sidebar-footer {
                border-top: 1px solid var(--sidebar-border);
                padding: 10px;
                flex-shrink: 0;
            }
            .sidebar-profile {
                display: flex;
                align-items: center;
                gap: 9px;
                padding: 7px 8px;
                border-radius: 7px;
                cursor: pointer;
                transition: background 0.1s;
            }
            .sidebar-profile:hover { background: rgba(255,255,255,0.06); }
            .sidebar-avatar {
                width: 28px; height: 28px;
                background: #21262d;
                border: 1px solid #30363d;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-size: 11px;
                font-weight: 600;
                color: #e6edf3;
                flex-shrink: 0;
                transition: border-color 0.1s;
            }
            .sidebar-profile:hover .sidebar-avatar { border-color: var(--sidebar-avatar-border-hover, #58a6ff); }
            .sidebar-profile-info { overflow: hidden; flex: 1; min-width: 0; }
            .sidebar-profile-name { font-size: 12px; font-weight: 600; color: #e6edf3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .sidebar-profile-role { font-size: 10px; color: #484f58; margin-top: 1px; }

            .app-layout {
                display: flex;
                height: 100vh;
                overflow: hidden;
            }

            .app-main {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-width: 0;
                overflow: hidden;
            }

            .app-topbar {
                height: var(--header-h);
                background: var(--header-bg);
                border-bottom: 1px solid var(--header-border);
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 0 20px;
                flex-shrink: 0;
                position: sticky;
                top: 0;
                z-index: 50;
            }

            .topbar-hamburger {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px; height: 40px;
                border: none;
                background: transparent;
                border-radius: 8px;
                cursor: pointer;
                color: var(--text-muted);
                transition: background 0.1s, color 0.1s;
                flex-shrink: 0;
                -webkit-tap-highlight-color: transparent;
            }
            .topbar-hamburger:hover,
            .topbar-hamburger:active { background: #f3f4f6; color: var(--text-primary); }

            @media (min-width: 768px) {
                .topbar-hamburger { display: none; }
            }

            @media (max-width: 767px) {
                .app-topbar { padding: 0 12px; gap: 8px; }
                .content-inner { padding: 16px 14px; }
            }

            .app-content {
                flex: 1;
                overflow-y: auto;
                scroll-behavior: smooth;
                scrollbar-width: thin;
                scrollbar-color: #d0d7de transparent;
            }
            .app-content::-webkit-scrollbar { width: 5px; }
            .app-content::-webkit-scrollbar-thumb { background: #d0d7de; border-radius: 10px; }

            .content-inner {
                max-width: 1200px;
                margin: 0 auto;
                padding: 24px 20px;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.55);
                z-index: 100;
                backdrop-filter: blur(2px);
                -webkit-backdrop-filter: blur(2px);
                opacity: 0;
                transition: opacity 0.25s ease;
            }
            .sidebar-overlay.is-open {
                display: block;
                opacity: 1;
            }

            .customer-wrap { display: flex; flex-direction: column; min-height: 100vh; }

            @media (max-width: 767px) {
                :root { --sidebar-w: min(280px, 88vw); }
                .sidebar-logo { padding: 14px 12px; }
                .sidebar-body { padding: 6px 10px 12px; }
                .sidebar-footer { padding: 10px 10px calc(10px + env(safe-area-inset-bottom, 0)); }
                .nav-section-label { padding: 0 10px; font-size: 10px; }
            }
            .sidebar-body { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.07) transparent; }
            .sidebar-body::-webkit-scrollbar { width: 4px; }
            .sidebar-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 4px; }
        </style>
    </head>

    <body class="bg-[#f6f8fa] text-slate-800 antialiased">
        @include('theme.preloader')

        @php
            $isCustomer = Auth::check() && Auth::user()->role === 'customer';
            $hideSearch = request()->routeIs('customer.pesanan*');
        @endphp

        {{-- ── CUSTOMER LAYOUT ── --}}
        @if($isCustomer)
            <div class="customer-wrap">
                @include('theme.partials.customer-navbar', ['hideSearch' => $hideSearch])
                <main class="flex-1 w-full">
                    @yield('content')
                </main>
                @include('theme.footer')
            </div>

        {{-- ── ADMIN / MITRA / KEPALA BUMDES LAYOUT ── --}}
        @else
            <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

            <div class="app-layout">

                @if(Auth::check())
                    @if(Auth::user()->role === 'admin')             @include('theme.partials.sidebar-admin')
                    @elseif(Auth::user()->role === 'kepala-bumdes') @include('theme.partials.sidebar-kepala')
                    @elseif(Auth::user()->role === 'mitra')         @include('theme.partials.sidebar-mitra')
                    @endif
                @endif

                <div class="app-main">
                    @include('theme.navbar')

                    <div class="app-content" id="main-scroll">
                        <div class="content-inner">
                            @yield('content')
                        </div>
                    </div>

                    @include('theme.footer')
                </div>

            </div>
        @endif

        @include('theme.foot')
        @stack('scripts')

        <script>
            function openSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                const toggle  = document.querySelector('.topbar-hamburger');

                sidebar?.classList.add('is-open');
                overlay?.classList.add('is-open');
                sidebar?.setAttribute('aria-hidden', 'false');
                toggle?.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                const toggle  = document.querySelector('.topbar-hamburger');

                sidebar?.classList.remove('is-open');
                overlay?.classList.remove('is-open');
                sidebar?.setAttribute('aria-hidden', 'true');
                toggle?.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar?.classList.contains('is-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            function initMobileSidebar() {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar) return;
                sidebar.setAttribute('aria-hidden', window.innerWidth >= 768 ? 'false' : 'true');
            }

            document.addEventListener('DOMContentLoaded', initMobileSidebar);
            document.addEventListener('turbo:load', initMobileSidebar);

            document.addEventListener('click', function (e) {
                if (window.innerWidth >= 768) return;
                if (e.target.closest('.app-sidebar .nav-link')) closeSidebar();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    closeSidebar();
                    document.getElementById('sidebar')?.setAttribute('aria-hidden', 'false');
                }
            });

            {{-- Tutup sidebar otomatis saat Turbo navigasi --}}
            document.addEventListener('turbo:visit', closeSidebar);
        </script>
    </body>
    </html>
