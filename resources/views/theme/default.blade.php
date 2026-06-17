<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="https://res.cloudinary.com/duxq5a40j/image/upload/v1779851100/logoBumdes_nsewm6.png" type="image/png">
    @include('theme.head')
    @stack('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        .sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 8px 8px 12px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.07) transparent;
        }
        .sidebar-body::-webkit-scrollbar { width: 4px; }
        .sidebar-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 4px; }

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
            background: rgba(0,0,0,0.6);
            z-index: 90;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        }
        .sidebar-overlay.is-open { display: block; }

        .customer-wrap { display: flex; flex-direction: column; min-height: 100vh; }

        /* ── Toast Notifikasi ── */
        @keyframes slide-in {
            from { opacity: 0; transform: translateX(110%); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in { animation: slide-in 0.4s ease-out; }
    </style>
</head>

<body class="bg-[#f6f8fa] text-slate-800 antialiased">
    @include('theme.preloader')

    {{-- Toast Container — muncul di pojok kanan atas semua halaman --}}
    <div id="toast-container"
         class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"
         style="max-width: 320px;">
    </div>

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

    {{-- ── Sidebar Toggle ── --}}
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
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href]');
            if (link && !link.target && !link.href.startsWith('#') && !link.href.startsWith('javascript')) {
                closeSidebar();
            }
        });
    </script>

    {{-- ══════════════════════════════════════════════════════
         SISTEM NOTIFIKASI REALTIME — PUSHER + WEB PUSH
         Aktif untuk semua role yang sudah login
    ══════════════════════════════════════════════════════ --}}
    @auth
    <script>
        // ── Konfigurasi warna per role/tipe notifikasi ──
        const WARNA = {
            orange: { bg: 'background:#fff7ed',  border: 'border-left:4px solid #f97316', text: '#f97316' },
            blue:   { bg: 'background:#eff6ff',  border: 'border-left:4px solid #3b82f6', text: '#3b82f6' },
            green:  { bg: 'background:#f0fdf4',  border: 'border-left:4px solid #22c55e', text: '#22c55e' },
            red:    { bg: 'background:#fef2f2',  border: 'border-left:4px solid #ef4444', text: '#ef4444' },
            purple: { bg: 'background:#faf5ff',  border: 'border-left:4px solid #a855f7', text: '#a855f7' },
        };

        // ══════════════════════════════════════
        // 1. PUSHER — notifikasi saat tab aktif
        // ══════════════════════════════════════
        if (typeof window.Echo !== 'undefined') {
            window.Echo.private(`user.{{ auth()->id() }}`)
                .listen('.notifikasi', (data) => {
                    tambahBadge();
                    tampilkanToast(data);
                    bunyikanNotif();
                });
        }

        // ══════════════════════════════════════════════════════
        // 2. WEB PUSH — notifikasi saat tab tidak aktif (OS)
        // ══════════════════════════════════════════════════════
        const VAPID_PUBLIC_KEY = '{{ env("VAPID_PUBLIC_KEY") }}';

        async function setupWebPush() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
            if (!VAPID_PUBLIC_KEY) return;

            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                const permission   = await Notification.requestPermission();
                if (permission !== 'granted') return;

                let subscription = await registration.pushManager.getSubscription();

                if (!subscription) {
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly:      true,
                        applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
                    });

                    await fetch('/webpush/subscribe', {
                        method:  'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(subscription),
                    });
                }
            } catch (err) {
                console.warn('Web Push setup gagal:', err);
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = atob(base64);
            return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
        }

        window.addEventListener('load', setupWebPush);

        // ══════════════════════════════════════
        // 3. SHARED FUNCTIONS
        // ══════════════════════════════════════

        /** Tambah angka badge di ikon notifikasi navbar */
        function tambahBadge() {
            const badge = document.getElementById('notif-badge');
            if (!badge) return;
            const current = parseInt(badge.innerText) || 0;
            badge.innerText = current + 1;
            badge.classList.remove('hidden');
        }

        /** Tampilkan toast popup di pojok kanan atas */
        function tampilkanToast(data) {
            const w     = WARNA[data.color] ?? WARNA.orange;
            const toast = document.createElement('div');

            toast.className = 'animate-slide-in pointer-events-auto';
            toast.style.cssText = `
                display:flex; align-items:flex-start; gap:12px;
                ${w.bg}; ${w.border};
                box-shadow:0 10px 40px rgba(0,0,0,0.12);
                border-radius:12px; padding:14px 16px;
                width:300px; position:relative;
                transition: opacity 0.4s, transform 0.4s;
            `;

            toast.innerHTML = `
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(0,0,0,0.06);
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas ${data.icon ?? 'fa-bell'}" style="color:${w.text};font-size:15px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="margin:0;font-size:13px;font-weight:700;color:#1f2328;">${data.title}</p>
                    <p style="margin:4px 0 0;font-size:12px;color:#57606a;line-height:1.5;">${data.message}</p>
                    ${data.url
                        ? `<a href="${data.url}"
                              style="display:inline-block;margin-top:8px;font-size:11px;
                                     font-weight:700;color:${w.text};text-decoration:none;">
                               Lihat Detail →
                           </a>`
                        : ''}
                </div>
                <button onclick="hapusToast(this)"
                        style="background:none;border:none;cursor:pointer;padding:0;
                               color:#8b949e;font-size:14px;flex-shrink:0;margin-top:2px;">
                    <i class="fas fa-times"></i>
                </button>
            `;

            const container = document.getElementById('toast-container');
            container.prepend(toast);

            // Auto-hilang setelah 6 detik
            setTimeout(() => hapusToast(toast.querySelector('button')), 6000);
        }

        /** Animasi hilang dan hapus toast */
        function hapusToast(btn) {
            const toast = btn?.closest?.('[class*=animate-slide-in]') ?? btn;
            if (!toast) return;
            toast.style.opacity  = '0';
            toast.style.transform = 'translateX(110%)';
            setTimeout(() => toast.remove(), 400);
        }

        /** Bunyi notifikasi sederhana via Web Audio API */
        function bunyikanNotif() {
            try {
                const ctx  = new AudioContext();
                const osc  = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.setValueAtTime(880,  ctx.currentTime);
                osc.frequency.setValueAtTime(1100, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.4);
            } catch(e) {}
        }
    </script>
    @endauth

</body>
</html>