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
        /* ── Global Reset & Base ── */
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

        /* ── Sidebar ── */
        .app-sidebar {
            position: fixed;
            inset-y: 0;
            left: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transform: translateX(-100%);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @media (min-width: 768px) {
            .app-sidebar { transform: translateX(0); position: sticky; top: 0; height: 100vh; flex-shrink: 0; }
        }

        .app-sidebar.is-open { transform: translateX(0); }

        /* Sidebar logo */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .sidebar-logo-icon {
            width: 28px; height: 28px;
            border-radius: 6px;
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
        }
        .sidebar-logo-close {
            margin-left: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px; height: 28px;
            border-radius: 5px;
            cursor: pointer;
            color: var(--sidebar-text);
            transition: background 0.1s, color 0.1s;
        }
        .sidebar-logo-close:hover { background: rgba(255,255,255,0.07); color: var(--sidebar-text-hover); }

        @media (min-width: 768px) {
            .sidebar-logo-close { display: none; }
        }

        /* Sidebar nav */
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
            gap: 8px;
            padding: 7px 8px;
            border-radius: 6px;
            color: var(--sidebar-text);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.1s, color 0.1s;
            position: relative;
            margin-bottom: 1px;
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

        /* Sidebar footer */
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
        .sidebar-profile:hover .sidebar-avatar { border-color: #58a6ff; }
        .sidebar-profile-name { font-size: 12px; font-weight: 600; color: #e6edf3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-profile-role { font-size: 10px; color: #484f58; margin-top: 1px; }

        /* ── App layout wrapper ── */
        .app-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Main content column ── */
        .app-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
        }

        /* ── Topbar ── */
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
            width: 32px; height: 32px;
            border-radius: 6px;
            cursor: pointer;
            color: var(--text-muted);
            transition: background 0.1s, color 0.1s;
            flex-shrink: 0;
        }
        .topbar-hamburger:hover { background: #f3f4f6; color: var(--text-primary); }

        @media (min-width: 768px) {
            .topbar-hamburger { display: none; }
        }

        /* ── Scrollable content ── */
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

        /* ── Mobile overlay ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 90;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        .sidebar-overlay.is-open { display: block; }

        /* ── Customer layout: navbar + full width ── */
        .customer-wrap { display: flex; flex-direction: column; min-height: 100vh; }
        .customer-wrap .app-content { overflow: visible; }

        /* ── Scrollbar on sidebar ── */
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
                <div class="max-w-7xl mx-auto p-4 md:p-8">
                    @yield('content')
                </div>
            </main>
            @include('theme.footer')
        </div>

    {{-- ── ADMIN / MITRA / KEPALA BUMDES LAYOUT ── --}}
    @else
        {{-- Mobile overlay --}}
        <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

        <div class="app-layout">

            {{-- Sidebar --}}
            @if(Auth::check())
                @if(Auth::user()->role === 'admin')         @include('theme.partials.sidebar-admin')
                @elseif(Auth::user()->role === 'kepala-bumdes') @include('theme.partials.sidebar-kepala')
                @elseif(Auth::user()->role === 'mitra')     @include('theme.partials.sidebar-mitra')
                @endif
            @endif

            {{-- Main column --}}
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
            document.querySelector('.app-sidebar')?.classList.add('is-open');
            document.getElementById('sidebar-overlay')?.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.querySelector('.app-sidebar')?.classList.remove('is-open');
            document.getElementById('sidebar-overlay')?.classList.remove('is-open');
            document.body.style.overflow = '';
        }
        function toggleSidebar() {
            const sidebar = document.querySelector('.app-sidebar');
            if (sidebar?.classList.contains('is-open')) { closeSidebar(); } else { openSidebar(); }
        }
    </script>
</body>
</html>
