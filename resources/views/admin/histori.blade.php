@extends('theme.default')

@section('title', 'Histori Aktivitas - BUMDes Patimban')

@include('admin.partials.styles')

@section('content')
<div class="admin-page">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Histori Aktivitas</h1>
            <p class="admin-page-subtitle">Semua aktivitas yang tercatat di sistem</p>
        </div>
    </div>

    <div class="admin-card">
        <div>
            @forelse($aktivitas as $log)
            @php
                $icon = 'fa-circle-info';
                $color = 'background:#f6f8fa;color:#656d76;';

                if (str_contains($log->action, 'Setuju') || str_contains($log->action, 'Approve')) {
                    $icon = 'fa-check'; $color = 'background:#ecfdf5;color:#059669;';
                } elseif (str_contains($log->action, 'Tolak') || str_contains($log->action, 'Reject')) {
                    $icon = 'fa-times'; $color = 'background:#fff1f2;color:#e11d48;';
                } elseif (str_contains($log->action, 'Tambah') || str_contains($log->action, 'Daftar')) {
                    $icon = 'fa-plus'; $color = 'background:#fffbeb;color:#d97706;';
                } elseif (str_contains($log->action, 'Ubah') || str_contains($log->action, 'Update')) {
                    $icon = 'fa-pen'; $color = 'background:#f5f3ff;color:#7c3aed;';
                } elseif (str_contains($log->action, 'Hapus') || str_contains($log->action, 'Delete')) {
                    $icon = 'fa-trash'; $color = 'background:#fff1f2;color:#e11d48;';
                } elseif (str_contains($log->action, 'Login')) {
                    $icon = 'fa-right-to-bracket'; $color = 'background:#eff6ff;color:#2563eb;';
                } elseif (str_contains($log->action, 'Bayar') || str_contains($log->action, 'Transaksi')) {
                    $icon = 'fa-money-bill'; $color = 'background:#ecfdf5;color:#16a34a;';
                }
            @endphp
            <div class="admin-activity-item">
                <div class="admin-activity-icon" style="{{ $color }}">
                    <i class="fas {{ $icon }}"></i>
                </div>
                <div class="admin-activity-body">
                    <p class="admin-activity-text">{{ $log->details }}</p>
                    <div class="admin-activity-meta">
                        <span><i class="fas fa-user" style="margin-right:4px;"></i>{{ $log->user_name }}</span>
                        <span><i class="fas fa-clock" style="margin-right:4px;"></i>
                            <span class="realtime-diff" data-time="{{ $log->created_at->toIso8601String() }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </span>
                        <span>{{ $log->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <span class="admin-activity-tag">{{ $log->action }}</span>
            </div>
            @empty
            <div class="admin-empty">
                <i class="fas fa-history"></i>
                <p class="admin-empty-title">Belum Ada Histori</p>
                <p>Belum ada histori aktivitas.</p>
            </div>
            @endforelse
        </div>

        @if($aktivitas->hasPages())
        <div style="padding:14px 16px;background:#f6f8fa;border-top:1px solid #eaeef2;">
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
