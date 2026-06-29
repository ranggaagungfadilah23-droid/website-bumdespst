@extends('theme.default')

@section('title', 'Laporan Keuangan - BUMDes Patimban')

@include('admin.partials.styles')

@section('content')
<div class="admin-page">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Laporan Keuangan BUMDes</h1>
            <p class="admin-page-subtitle">Rekapitulasi pendapatan dan bagi hasil — {{ $bulanAktif }}</p>
        </div>
        <button type="button" onclick="openModal('modalKirimLaporan')" class="admin-btn admin-btn--success">
            <i class="fas fa-paper-plane"></i> Kirim ke Kepala
        </button>
    </div>

    @if(session('laporan_terkirim'))
    <div class="admin-alert admin-alert--success">
        <i class="fas fa-check-circle"></i>
        <div>
            <strong>Laporan berhasil dikirim!</strong>
            <p style="margin-top:2px;font-size:0.75rem;">Laporan bulan {{ $bulanAktif }} telah dikirim ke Kepala BUMDes.</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="admin-alert admin-alert--error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="admin-summary-grid">
        <div class="admin-summary-card admin-summary-card--primary">
            <p class="admin-summary-label">Kas Masuk BUMDes</p>
            <p class="admin-summary-value">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</p>
            <p class="admin-summary-note">Dari bagi hasil yang sudah selesai</p>
        </div>
        <div class="admin-summary-card">
            <p class="admin-summary-label">Total Omzet Mitra</p>
            <p class="admin-summary-value" style="color:#1f2328;">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</p>
            <p class="admin-summary-note">Bulan {{ $bulanAktif }}</p>
        </div>
        <div class="admin-summary-card">
            <p class="admin-summary-label">Mitra Aktif</p>
            <p class="admin-summary-value" style="color:#1f2328;">{{ $totalMitra }}</p>
            <p class="admin-summary-note">Terdaftar & aktif</p>
        </div>
    </div>

    @if($totalKasMasuk == 0 && $totalBagiHasil == 0)
    <div class="admin-card">
        <div class="admin-empty">
            <i class="fas fa-chart-line"></i>
            <p class="admin-empty-title">Grafik Belum Tersedia</p>
            <p>Belum ada bagi hasil yang selesai dikonfirmasi.</p>
        </div>
    </div>
    @else

    <div class="admin-chart-grid">
        <div class="admin-card admin-card-body">
            <p class="admin-card-title">Tren Omzet & Kas BUMDes</p>
            <p class="admin-card-subtitle">Perbandingan omzet mitra vs kas masuk BUMDes per bulan</p>
            <div style="position:relative;height:220px;margin-top:12px;">
                <canvas id="grafikBulanan"></canvas>
            </div>
        </div>
        <div class="admin-card admin-card-body">
            <p class="admin-card-title">Kontribusi per Mitra</p>
            <p class="admin-card-subtitle">Berdasarkan total omzet keseluruhan</p>
            <div style="position:relative;height:180px;margin-top:12px;">
                <canvas id="grafikMitra"></canvas>
            </div>
            <div style="margin-top:14px;display:flex;flex-direction:column;gap:8px;">
                @foreach($perMitra as $i => $pm)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:0.75rem;">
                    <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                        <span style="width:10px;height:10px;border-radius:50%;flex-shrink:0;background:{{ ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'][$i % 5] }};"></span>
                        <span style="color:#424a53;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $pm['nama'] }}</span>
                    </div>
                    <span style="font-weight:700;color:#1f2328;flex-shrink:0;">Rp {{ number_format($pm['omzet'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <p class="admin-card-title">Detail Bagi Hasil per Mitra</p>
                <p class="admin-card-subtitle">{{ $bulanAktif }}</p>
            </div>
            <a href="{{ route('admin.laporan.pdf') }}" target="_blank" class="admin-btn admin-btn--danger admin-btn--sm" style="background:#dc2626;color:#fff;border:none;">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
        </div>
        <div class="admin-table-wrap admin-table-wrap--cards">
            <table class="admin-table admin-table--responsive">
                <thead>
                    <tr>
                        <th>Mitra</th>
                        <th class="text-right">Total Omzet</th>
                        <th class="text-right">% BUMDes</th>
                        <th class="text-right">Kas Masuk</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perMitra as $pm)
                    <tr>
                        <td data-label="Mitra"><span class="admin-user-name">{{ $pm['nama'] }}</span></td>
                        <td data-label="Total Omzet" class="text-right">
                            <span style="font-family:ui-monospace,monospace;">Rp {{ number_format($pm['omzet'], 0, ',', '.') }}</span>
                        </td>
                        <td data-label="% BUMDes" class="text-right">{{ $pm['persen_bumdes'] ?? '-' }}%</td>
                        <td data-label="Kas Masuk" class="text-right">
                            <span style="font-weight:800;color:#0969da;font-family:ui-monospace,monospace;">Rp {{ number_format($pm['kas_bumdes'] ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td data-label="Status" class="text-center">
                            <span class="admin-badge admin-badge--status-ok">Selesai</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f6f8fa;border-top:2px solid #d0d7de;">
                        <td data-label="Mitra" style="font-weight:800;color:#1f2328;">TOTAL</td>
                        <td data-label="Total Omzet" class="text-right" style="font-weight:800;font-family:ui-monospace,monospace;">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</td>
                        <td data-label="% BUMDes"></td>
                        <td data-label="Kas Masuk" class="text-right" style="font-weight:800;color:#0969da;font-family:ui-monospace,monospace;font-size:0.9375rem;">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                        <td data-label="Status"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- MODAL KIRIM LAPORAN --}}
<div id="modalKirimLaporan" class="admin-modal">
    <div class="admin-modal-panel">
        <div class="admin-modal-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:44px;height:44px;background:#ecfdf5;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-paper-plane" style="color:#059669;"></i>
                </div>
                <div>
                    <h3 class="admin-modal-title">Kirim Laporan Kas</h3>
                    <p style="font-size:0.75rem;color:#656d76;margin-top:2px;">Laporan akan dikirim ke Kepala BUMDes</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalKirimLaporan')" class="admin-btn admin-btn--ghost admin-btn--icon" aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="admin-modal-body">
            <div style="background:#f6f8fa;border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:10px;font-size:0.8125rem;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;gap:12px;">
                    <span style="color:#656d76;">Periode</span>
                    <span style="font-weight:700;">{{ $bulanAktif }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:12px;">
                    <span style="color:#656d76;">Total Omzet Mitra</span>
                    <span style="font-weight:700;">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:12px;border-top:1px solid #d0d7de;padding-top:10px;">
                    <span style="color:#0969da;font-weight:700;">Kas Masuk BUMDes</span>
                    <span style="font-weight:800;color:#0550ae;">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:12px;">
                    <span style="color:#656d76;">Jumlah Mitra</span>
                    <span style="font-weight:700;">{{ $totalMitra }} mitra</span>
                </div>
            </div>

            <form action="{{ route('admin.laporan.kirim') }}" method="POST">
                @csrf
                <input type="hidden" name="bulan_aktif" value="{{ $bulanAktif }}">
                <input type="hidden" name="total_kas_masuk" value="{{ $totalKasMasuk }}">
                <input type="hidden" name="total_omzet" value="{{ $totalBagiHasil }}">
                <input type="hidden" name="total_mitra" value="{{ $totalMitra }}">

                <label class="admin-form-label">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" class="admin-form-control admin-form-textarea" placeholder="Tambahkan catatan untuk Kepala BUMDes..."></textarea>

                <div class="admin-modal-footer" style="margin:16px -18px -18px;padding:14px 18px;">
                    <button type="button" onclick="closeModal('modalKirimLaporan')" class="admin-btn admin-btn--ghost" style="flex:1;">Batal</button>
                    <button type="submit" class="admin-btn admin-btn--success" style="flex:1;">
                        <i class="fas fa-paper-plane"></i> Kirim Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id)?.classList.add('is-open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id)?.classList.remove('is-open'); document.body.style.overflow = ''; }

document.getElementById('modalKirimLaporan')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal('modalKirimLaporan');
});
</script>

@if($totalKasMasuk > 0 || $totalBagiHasil > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('grafikBulanan'), {
    type: 'line',
    data: {
        labels: {!! json_encode($labelGrafik) !!},
        datasets: [
            {
                label: 'Total Omzet Mitra',
                data: {!! json_encode($dataOmzet) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2.5,
                pointRadius: 4,
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Kas Masuk BUMDes',
                data: {!! json_encode($dataKasBumdes) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                borderWidth: 2.5,
                pointRadius: 4,
                tension: 0.4,
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
        },
        scales: {
            y: {
                ticks: { callback: val => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(val), maxTicksLimit: 6 },
                grid: { color: '#eaeef2' }
            },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('grafikMitra'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($perMitra->pluck('nama')) !!},
        datasets: [{
            data: {!! json_encode($perMitra->pluck('omzet')) !!},
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
        }
    }
});
</script>
@endif
@endsection
