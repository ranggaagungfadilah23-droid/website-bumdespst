@extends('theme.default')

@section('title', 'Dashboard Admin - BUMDes Patimban')

@include('admin.partials.styles')

@section('content')
<div class="admin-page">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Halo, {{ auth()->user()->name }}!</h1>
            <p class="admin-page-subtitle">Pantau performa BUMDes Patimban hari ini.</p>
        </div>
        <div class="admin-page-meta">
            <i class="fas fa-calendar-alt"></i>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    @php
        $mitraAktif   = \App\Models\Mitra::whereHas('user', fn($q)=>$q->where('status','aktif'))->count();
        $pengajuan    = \App\Models\User::where('role','mitra')->where('status','pending')->count();
        $omzetBulan   = \App\Models\BagiHasil::whereMonth('tanggal',now()->month)->whereYear('tanggal',now()->year)->sum('total_omzet');
        $kasBulan     = \App\Models\BagiHasil::whereMonth('tanggal',now()->month)->whereYear('tanggal',now()->year)->where('status','SELESAI')->sum('nominal_bumdes');
        $bhSelesai    = \App\Models\BagiHasil::whereMonth('tanggal',now()->month)->whereYear('tanggal',now()->year)->where('status','SELESAI')->count();
        $bhPending    = \App\Models\BagiHasil::whereMonth('tanggal',now()->month)->whereYear('tanggal',now()->year)->where('status','PENDING')->count();
    @endphp

    <div class="admin-stat-grid admin-stat-grid--6">
        <a href="{{ route('admin.mitra.index') }}" class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-store"></i></div>
            <p class="admin-stat-label">Mitra Aktif</p>
            <p class="admin-stat-value">{{ $mitraAktif }}</p>
        </a>
        <a href="{{ route('admin.pengajuan') }}" class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-clock"></i></div>
            <p class="admin-stat-label">Pengajuan Mitra</p>
            <p class="admin-stat-value">{{ $pengajuan }}</p>
        </a>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-chart-line"></i></div>
            <p class="admin-stat-label">Total Omzet</p>
            <p class="admin-stat-value admin-stat-value--sm">Rp {{ number_format($omzetBulan,0,',','.') }}</p>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#f0fdfa;color:#0d9488;"><i class="fas fa-university"></i></div>
            <p class="admin-stat-label">Kas BUMDes</p>
            <p class="admin-stat-value admin-stat-value--sm">Rp {{ number_format($kasBulan,0,',','.') }}</p>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#ecfdf5;color:#16a34a;"><i class="fas fa-check-circle"></i></div>
            <p class="admin-stat-label">Bagi Hasil Selesai</p>
            <p class="admin-stat-value">{{ $bhSelesai }}</p>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fff1f2;color:#e11d48;"><i class="fas fa-exclamation-circle"></i></div>
            <p class="admin-stat-label">Bagi Hasil Pending</p>
            <p class="admin-stat-value">{{ $bhPending }}</p>
        </div>
    </div>

    @php
        $namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $tren = \App\Models\Bagihasil::selectRaw('MONTH(tanggal) as bln, SUM(total_omzet) as omzet, SUM(nominal_bumdes) as kas')
            ->whereYear('tanggal', now()->year)->where('status','SELESAI')
            ->groupBy('bln')->orderBy('bln')->get();

        $mitraChart = \App\Models\Bagihasil::whereMonth('tanggal',now()->month)
            ->whereYear('tanggal',now()->year)->where('status','SELESAI')->get()
            ->groupBy('mitra_id')
            ->map(fn($g)=>['nama'=>optional(\App\Models\Mitra::where('user_id',$g->first()->mitra_id)->first())->nama_usaha??'-','omzet'=>$g->sum('total_omzet')])
            ->values();

        $recents = \App\Models\Bagihasil::latest()->take(5)->get();
    @endphp

    <div class="admin-chart-grid">
        <div class="admin-card admin-card-body">
            <p class="admin-card-title">Tren omzet & kas BUMDes</p>
            <p class="admin-card-subtitle">Tahun {{ now()->year }}</p>
            <div style="position:relative;height:220px;margin-top:12px;">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <div class="admin-card admin-card-body">
            <p class="admin-card-title">Kontribusi per mitra</p>
            <p class="admin-card-subtitle">{{ now()->translatedFormat('F Y') }}</p>
            <div style="position:relative;height:180px;margin-top:12px;">
                <canvas id="donutChart"></canvas>
            </div>
            <div id="donutLegend" style="margin-top:12px;"></div>
        </div>
    </div>

    <div class="admin-chart-grid--2">
        <div class="admin-card admin-card-body">
            <p class="admin-card-title" style="margin-bottom:14px;">Omzet per mitra — bulan ini</p>
            <div id="barRows"></div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header">
                <p class="admin-card-title">Bagi hasil terbaru</p>
            </div>
            <div class="admin-table-wrap admin-table-wrap--cards">
                <table class="admin-table admin-table--responsive">
                    <thead>
                        <tr>
                            <th>Mitra</th>
                            <th class="text-right">Omzet</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recents as $bh)
                        @php $m = \App\Models\Mitra::where('user_id',$bh->mitra_id)->first(); @endphp
                        <tr>
                            <td data-label="Mitra">
                                <span class="admin-user-name">{{ $m->nama_usaha ?? '-' }}</span>
                            </td>
                            <td data-label="Omzet" class="text-right">
                                <span style="font-family:ui-monospace,monospace;font-size:0.75rem;">Rp {{ number_format($bh->total_omzet,0,',','.') }}</span>
                            </td>
                            <td data-label="Status" class="text-center">
                                @if($bh->status=='SELESAI')
                                    <span class="admin-badge admin-badge--status-ok">Selesai</span>
                                @else
                                    <span class="admin-badge admin-badge--status-pending">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr class="admin-table-empty">
                            <td colspan="3">
                                <div class="admin-empty" style="padding:24px;">
                                    <p>Belum ada data</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const palette = ['#185FA5','#0F6E56','#854F0B','#993556','#3B6D11','#A32D2D'];
const fmt = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
const fmtC = v => 'Rp ' + new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v);

new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($tren->map(fn($t)=>$namaBulan[$t->bln-1])) !!},
        datasets: [
            { label: 'Omzet Mitra', data: {!! json_encode($tren->pluck('omzet')) !!}, borderColor:'#185FA5', backgroundColor:'rgba(24,95,165,0.07)', borderWidth:2, pointRadius:3, tension:0.4, fill:true },
            { label: 'Kas BUMDes', data: {!! json_encode($tren->pluck('kas')) !!}, borderColor:'#0F6E56', backgroundColor:'rgba(15,110,86,0.07)', borderWidth:2, pointRadius:3, tension:0.4, fill:true }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position:'top', labels:{ font:{size:11}, boxWidth:12 } },
            tooltip: { callbacks: { label: c => fmt(c.raw) } }
        },
        scales: {
            y: { ticks: { callback: fmtC, font:{size:10}, maxTicksLimit:6 }, grid:{color:'rgba(0,0,0,0.04)'} },
            x: { ticks: { font:{size:10} }, grid:{display:false} }
        }
    }
});

const mc = {!! json_encode($mitraChart) !!};
if (mc.length) {
    new Chart(document.getElementById('donutChart'), {
        type:'doughnut',
        data:{ labels:mc.map(m=>m.nama), datasets:[{data:mc.map(m=>m.omzet),backgroundColor:palette,borderWidth:0,hoverOffset:4}] },
        options:{ responsive:true, maintainAspectRatio:false, cutout:'68%', plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>fmt(c.raw)}}} }
    });
    const leg = document.getElementById('donutLegend');
    mc.forEach((m,i)=> leg.innerHTML += `<div style="display:flex;align-items:center;gap:6px;font-size:11px;color:#656d76;margin-bottom:6px;">
        <span style="width:8px;height:8px;border-radius:50%;background:${palette[i%palette.length]};flex-shrink:0;"></span>
        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${m.nama}</span>
        <span style="font-weight:600;color:#1f2328;">${fmtC(m.omzet)}</span></div>`);

    const maxO = Math.max(...mc.map(m=>m.omzet),1);
    const br = document.getElementById('barRows');
    mc.forEach((m,i)=> br.innerHTML += `<div class="admin-bar-row">
        <div class="admin-bar-label">${m.nama}</div>
        <div class="admin-bar-track"><div class="admin-bar-fill" style="width:${Math.round(m.omzet/maxO*100)}%;background:${palette[i%palette.length]};"></div></div>
        <div class="admin-bar-value">${fmtC(m.omzet)}</div></div>`);
}
</script>
@endsection
