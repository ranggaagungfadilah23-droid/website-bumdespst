@extends('theme.default')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-800">Histori Aktivitas</h1>
        <p class="text-slate-400 text-sm mt-1">Semua aktivitas yang tercatat di sistem</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-50">
            @forelse($aktivitas as $log)
            @php
                $icon = 'fa-circle-info';
                $color = 'bg-slate-100 text-slate-500';

                if (str_contains($log->action, 'Setuju') || str_contains($log->action, 'Approve')) {
                    $icon = 'fa-check';
                    $color = 'bg-emerald-100 text-emerald-600';
                } elseif (str_contains($log->action, 'Tolak') || str_contains($log->action, 'Reject')) {
                    $icon = 'fa-times';
                    $color = 'bg-rose-100 text-rose-600';
                } elseif (str_contains($log->action, 'Tambah') || str_contains($log->action, 'Daftar')) {
                    $icon = 'fa-plus';
                    $color = 'bg-amber-100 text-amber-600';
                } elseif (str_contains($log->action, 'Ubah') || str_contains($log->action, 'Update')) {
                    $icon = 'fa-pen';
                    $color = 'bg-purple-100 text-purple-600';
                } elseif (str_contains($log->action, 'Hapus') || str_contains($log->action, 'Delete')) {
                    $icon = 'fa-trash';
                    $color = 'bg-rose-100 text-rose-600';
                } elseif (str_contains($log->action, 'Login')) {
                    $icon = 'fa-right-to-bracket';
                    $color = 'bg-blue-100 text-blue-600';
                } elseif (str_contains($log->action, 'Bayar') || str_contains($log->action, 'Transaksi')) {
                    $icon = 'fa-money-bill';
                    $color = 'bg-green-100 text-green-600';
                }
            @endphp
            <div class="flex items-start gap-4 p-5 hover:bg-slate-50/50 transition">
                <div class="w-10 h-10 rounded-full {{ $color }} flex items-center justify-center shrink-0">
                    <i class="fas {{ $icon }} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-700 font-medium">{{ $log->details }}</p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs text-slate-400">
                            <i class="fas fa-user mr-1"></i>
                            {{ $log->user_name }}
                        </span>
                        <span class="text-slate-200">•</span>
                        <span class="text-xs text-slate-400">
                            <i class="fas fa-clock mr-1"></i>
                            <span class="realtime-diff" data-time="{{ $log->created_at->toIso8601String() }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </span>
                        <span class="text-slate-200">•</span>
                        <span class="text-xs text-slate-400">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-slate-100 text-slate-400 shrink-0">
                    {{ $log->action }}
                </span>
            </div>
            @empty
            <div class="p-16 text-center text-slate-400">
                <i class="fas fa-history text-4xl mb-3 block text-slate-200"></i>
                <p>Belum ada histori aktivitas.</p>
            </div>
            @endforelse
        </div>

        @if($aktivitas->hasPages())
        <div class="p-4 bg-slate-50 border-t border-slate-100">
            {{ $aktivitas->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function updateRelativeTimes() {
        document.querySelectorAll('.realtime-diff').forEach(el => {
            const time = new Date(el.dataset.time);
            const now  = new Date();
            const diff = Math.floor((now - time) / 1000);
            let result;
            if (diff < 60) result = diff + ' detik yang lalu';
            else if (diff < 3600) result = Math.floor(diff / 60) + ' menit yang lalu';
            else if (diff < 86400) result = Math.floor(diff / 3600) + ' jam yang lalu';
            else if (diff < 2592000) result = Math.floor(diff / 86400) + ' hari yang lalu';
            else result = Math.floor(diff / 2592000) + ' bulan yang lalu';
            el.innerText = result;
        });
    }
    updateRelativeTimes();
    setInterval(updateRelativeTimes, 30000);
</script>
@endsection
