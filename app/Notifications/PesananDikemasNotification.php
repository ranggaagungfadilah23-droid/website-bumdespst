@extends('theme.default')
@section('content')
<main class="max-w-4xl mx-auto py-10 px-4">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Notifikasi Saya</h1>

        @if($notifications->count() > 0)
        <form action="{{ route('notifications.destroyAll') }}" method="POST"
              onsubmit="return confirm('Hapus semua notifikasi?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="flex items-center gap-2 text-xs font-bold text-rose-500 hover:text-white hover:bg-rose-500 border border-rose-200 hover:border-rose-500 px-3 py-2 rounded-xl transition">
                <i class="fas fa-trash-alt"></i> Hapus Semua
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" id="notif-list">
        @forelse($notifications as $notif)
            <div id="notif-{{ $notif->id }}"
                 class="p-4 border-b border-slate-100 flex items-start gap-4 hover:bg-slate-50 transition cursor-pointer group {{ is_null($notif->read_at) ? 'bg-blue-50/50' : '' }}"
                 onclick="hapusNotif('{{ $notif->id }}', this)">

                <div class="w-2 h-2 mt-2 rounded-full flex-shrink-0 {{ is_null($notif->read_at) ? 'bg-blue-500' : 'bg-slate-300' }}"></div>

                <div class="flex-grow">
                    <p class="text-sm font-bold text-slate-800">{{ $notif->data['title'] ?? 'Informasi' }}</p>

                    {{-- ✅ PERBAIKAN: Menggunakan {!! !!} agar tag <b> atau <strong> bisa terbaca --}}
                    <p class="text-sm text-slate-600 mt-1">{!! $notif->data['message'] ?? 'Anda memiliki pesan baru.' !!}</p>

                    <p class="text-[10px] text-slate-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                </div>

                <span class="text-[10px] text-rose-400 font-bold opacity-0 group-hover:opacity-100 transition flex-shrink-0 mt-1">
                    <i class="fas fa-times"></i> Hapus
                </span>
            </div>
        @empty
            <div class="p-10 text-center text-slate-500">
                <i class="fas fa-bell-slash text-4xl mb-4 opacity-20"></i>
                <p>Belum ada notifikasi baru.</p>
            </div>
        @endforelse
    </div>

</main>

<script>
function updateNavbarBadge(jumlahDikurangi = 1) {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;

    const current = parseInt(badge.innerText) || 0;
    const newCount = current - jumlahDikurangi;

    if (newCount <= 0) {
        badge.classList.add('hidden');
        badge.innerText = '0';
    } else {
        badge.innerText = newCount;
    }
}

function hapusNotif(id, el) {
    el.style.transition = 'opacity 0.3s, transform 0.3s, max-height 0.3s, padding 0.3s';
    el.style.opacity = '0';
    el.style.transform = 'translateX(20px)';
    el.style.maxHeight = el.offsetHeight + 'px';

    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(() => {
        const isUnread = el.classList.contains('bg-blue-50/50');
        if (isUnread) updateNavbarBadge(1);

        setTimeout(() => {
            el.style.maxHeight = '0';
            el.style.padding = '0';
            el.style.borderBottom = 'none';
            el.style.overflow = 'hidden';
        }, 250);

        setTimeout(() => {
            el.remove();
            const list = document.getElementById('notif-list');
            const remaining = list.querySelectorAll('[id^="notif-"]');
            if (remaining.length === 0) {
                list.innerHTML = `
                    <div class="p-10 text-center text-slate-500">
                        <i class="fas fa-bell-slash text-4xl mb-4 opacity-20"></i>
                        <p>Belum ada notifikasi baru.</p>
                    </div>`;
                const btn = document.querySelector('form[action*="destroyAll"]');
                if (btn) btn.remove();

                // Set badge ke 0
                updateNavbarBadge(999);
            }
        }, 550);
    })
    .catch(() => {
        el.style.opacity = '1';
        el.style.transform = 'none';
        alert('Gagal menghapus notifikasi.');
    });
}
</script>
@endsection
