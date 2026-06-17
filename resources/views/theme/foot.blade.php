{{-- foot.blade.php --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
@stack('js')

{{-- ── Pusher + Laravel Echo ── --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Inisialisasi Echo manual (tanpa Vite)
    window.Pusher = Pusher;

    window.Echo = new (class {
        constructor() {
            this.pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                forceTLS: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                    }
                }
            });
        }
        private(channel) {
            const ch = this.pusher.subscribe('private-' + channel);
            return {
                listen(event, callback) {
                    ch.bind(event.startsWith('.') ? event.slice(1) : event, callback);
                    return this;
                }
            };
        }
    })();

    console.log('✅ Echo initialized');
</script>

{{-- ── Web Push + Notifikasi Realtime ── --}}
@auth
<script>
    const VAPID_PUBLIC_KEY = '{{ config("webpush.vapid.public_key") }}';

    const WARNA = {
        orange: { bg: '#fff7ed', border: '#f97316', text: '#f97316' },
        blue:   { bg: '#eff6ff', border: '#3b82f6', text: '#3b82f6' },
        green:  { bg: '#f0fdf4', border: '#22c55e', text: '#22c55e' },
        red:    { bg: '#fef2f2', border: '#ef4444', text: '#ef4444' },
        purple: { bg: '#faf5ff', border: '#a855f7', text: '#a855f7' },
    };

    // ── Pusher: notifikasi saat tab aktif ──
    window.Echo.private(`user.{{ auth()->id() }}`)
        .listen('.notifikasi', (data) => {
            console.log('🔔 Notifikasi masuk:', data);
            tambahBadge();
            tampilkanToast(data);
            bunyikanNotif();
        });

    // ── Web Push: notifikasi saat tab tidak aktif ──
    async function setupWebPush() {
        console.log('🔔 Setup Web Push dimulai...');

        if (!('serviceWorker' in navigator)) { console.error('❌ SW tidak didukung'); return; }
        if (!('PushManager' in window))      { console.error('❌ PushManager tidak didukung'); return; }
        if (!VAPID_PUBLIC_KEY)               { console.error('❌ VAPID_PUBLIC_KEY kosong'); return; }

        try {
            const reg = await navigator.serviceWorker.register('/sw.js');
            console.log('✅ Service Worker terdaftar');

            const permission = await Notification.requestPermission();
            console.log('🔔 Status izin:', permission);
            if (permission !== 'granted') return;

            let sub = await reg.pushManager.getSubscription();

            if (!sub) {
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly:      true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
                });
                console.log('✅ Subscribe berhasil');

                const res = await fetch('/webpush/subscribe', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(sub),
                });
                const json = await res.json();
                console.log('✅ Simpan ke server:', json);
            } else {
                console.log('ℹ️ Sudah subscribe sebelumnya');
            }
        } catch (err) {
            console.error('❌ Web Push setup gagal:', err);
        }
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        return Uint8Array.from([...atob(base64)].map(c => c.charCodeAt(0)));
    }

    window.addEventListener('load', setupWebPush);

    // ── Shared Functions ──
    function tambahBadge() {
        const badge = document.getElementById('notif-badge');
        if (!badge) return;
        badge.innerText = (parseInt(badge.innerText) || 0) + 1;
        badge.classList.remove('hidden');
    }

    function tampilkanToast(data) {
        const w     = WARNA[data.color] ?? WARNA.orange;
        const toast = document.createElement('div');
        toast.style.cssText = `
            position:fixed; top:20px; right:20px; z-index:9999;
            display:flex; align-items:flex-start; gap:12px;
            background:${w.bg}; border-left:4px solid ${w.border};
            box-shadow:0 10px 40px rgba(0,0,0,0.15);
            border-radius:12px; padding:14px 16px; width:300px;
            animation:slideIn 0.4s ease-out;
        `;
        toast.innerHTML = `
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(0,0,0,0.06);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas ${data.icon ?? 'fa-bell'}" style="color:${w.text};font-size:15px;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:13px;font-weight:700;color:#1f2328;">${data.title}</p>
                <p style="margin:4px 0 0;font-size:12px;color:#57606a;line-height:1.5;">${data.message}</p>
                ${data.url ? `<a href="${data.url}" style="display:inline-block;margin-top:8px;font-size:11px;font-weight:700;color:${w.text};text-decoration:none;">Lihat Detail →</a>` : ''}
            </div>
            <button onclick="this.parentElement.remove()"
                    style="background:none;border:none;cursor:pointer;color:#8b949e;font-size:14px;flex-shrink:0;">
                <i class="fas fa-times"></i>
            </button>
        `;

        // Stack toast — geser yang lama ke bawah
        document.querySelectorAll('.notif-toast').forEach(t => {
            t.style.top = (parseInt(t.style.top) + 90) + 'px';
        });
        toast.classList.add('notif-toast');
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity 0.4s'; setTimeout(()=>toast.remove(),400); }, 6000);
    }

    function bunyikanNotif() {
        try {
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            osc.frequency.setValueAtTime(1100, ctx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
            osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.4);
        } catch(e) {}
    }
</script>

<style>
@keyframes slideIn {
    from { opacity:0; transform:translateX(110%); }
    to   { opacity:1; transform:translateX(0); }
}
</style>
@endauth

<script>
    function hidePreloader() {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.opacity = '0';
            preloader.style.transition = 'opacity 0.4s';
            setTimeout(() => preloader.style.display = 'none', 400);
        }
    }
    window.addEventListener('load', hidePreloader);
    setTimeout(hidePreloader, 3000);
</script>