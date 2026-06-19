@extends('theme.default')

@section('title', 'Monitoring Keuangan - BUMDes Patimban')
@section('content')
<div class="min-h-screen bg-slate-50 p-3 sm:p-6 md:p-10 space-y-4 sm:space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 sm:gap-4">
        <div>
            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-blue-500 mb-1">BUMDes — Laporan Resmi</p>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-800 leading-tight">Laporan Bulanan</h1>
            <p class="text-slate-400 text-xs sm:text-sm mt-1">Rekapitulasi kas masuk BUMDes per bulan</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Filter Tahun --}}
            <form method="GET" action="" class="flex items-center gap-2">
                <label class="text-xs text-slate-400 font-semibold hidden sm:inline">Tahun:</label>
                <select name="tahun" onchange="this.form.submit()"
                    class="text-xs sm:text-sm border border-slate-200 rounded-lg sm:rounded-xl px-2.5 sm:px-3 py-1.5 sm:py-2 bg-white text-slate-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $tahunAktif == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <a href="#" onclick="window.print()"
                class="inline-flex items-center gap-1.5 sm:gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl shadow transition">
                <i class="fas fa-print"></i> Cetak
            </a>
        </div>
    </div>

    {{-- KARTU RINGKASAN TAHUNAN — 2 kolom di mobile, "Bulan Terbaik" full-width di bawahnya --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-5">
        <div class="col-span-2 sm:col-span-1 bg-gradient-to-br from-blue-600 to-blue-800 text-white p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg shadow-blue-500/20">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <p class="text-blue-100 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Total Kas Masuk {{ $tahunAktif }}</p>
                <div class="bg-white/20 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shrink-0"><i class="fas fa-university text-white text-xs sm:text-sm"></i></div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black font-mono">Rp {{ number_format($totalKasTahun, 0, ',', '.') }}</h3>
            <p class="text-blue-200 text-[10px] sm:text-xs mt-1 sm:mt-2">Akumulasi 12 bulan</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider">Rata-rata</p>
                <div class="bg-emerald-50 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shrink-0"><i class="fas fa-calculator text-emerald-500 text-xs sm:text-sm"></i></div>
            </div>
            <h3 class="text-base sm:text-2xl font-black font-mono text-slate-800 truncate">Rp {{ number_format($rataRataPerBulan, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-[9px] sm:text-xs mt-1 sm:mt-2">Per bulan aktif</p>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider">Bulan Terbaik</p>
                <div class="bg-amber-50 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shrink-0"><i class="fas fa-trophy text-amber-500 text-xs sm:text-sm"></i></div>
            </div>
            <h3 class="text-base sm:text-xl font-black font-mono text-slate-800 truncate">{{ $bulanTerbaik['nama'] ?? '-' }}</h3>
            <p class="text-slate-400 text-[9px] sm:text-xs mt-1 sm:mt-2 truncate">
                Rp {{ number_format($bulanTerbaik['total'] ?? 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- GRAFIK BATANG TAHUNAN --}}
    <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-1">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-700">Grafik Kas Masuk per Bulan</h3>
                <p class="text-slate-400 text-[10px] sm:text-xs mt-0.5">Tahun {{ $tahunAktif }}</p>
            </div>
        </div>
        <div class="mt-3 sm:mt-5">
            <canvas id="grafikTahunan" height="160"></canvas>
        </div>
    </div>

    {{-- TABEL DETAIL BULANAN --}}
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm sm:text-base font-bold text-slate-700">Detail Kas Masuk per Bulan</h3>
            <span class="text-[10px] sm:text-xs text-slate-400 font-medium">Tahun {{ $tahunAktif }}</span>
        </div>

        {{-- Tablet ke atas: tabel penuh --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Bulan</th>
                        <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kas Masuk</th>
                        <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah Transaksi</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Progres</th>
                        <th class="text-center px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($laporanBulanan as $laporan)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <span class="text-blue-600 text-xs font-black">{{ $laporan['nomor_bulan'] }}</span>
                                </div>
                                <span class="font-semibold text-slate-700">{{ $laporan['nama_bulan'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-black font-mono text-slate-800">
                            Rp {{ number_format($laporan['total_kas'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-slate-500 font-semibold">
                            {{ $laporan['jumlah_transaksi'] }} transaksi
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full transition-all"
                                     style="width: {{ $laporan['persen_dari_max'] }}%"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($laporan['total_kas'] > 0)
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-xs font-bold px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ada Data
                                </span>
                            @elseif($laporan['nomor_bulan'] > date('n') && $tahunAktif == date('Y'))
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-400 text-xs font-bold px-2.5 py-1 rounded-full">
                                    Belum
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-500 text-xs font-bold px-2.5 py-1 rounded-full">
                                    Nihil
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400 text-sm">
                            <i class="fas fa-inbox text-3xl mb-3 block text-slate-200"></i>
                            Tidak ada data untuk tahun {{ $tahunAktif }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-200">
                        <td class="px-6 py-4 font-extrabold text-slate-700">TOTAL</td>
                        <td class="px-6 py-4 text-right font-black font-mono text-blue-700 text-base">
                            Rp {{ number_format($totalKasTahun, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-slate-500">
                            {{ $totalTransaksiTahun }} transaksi
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Mobile: list ringkas per bulan --}}
        <div class="md:hidden divide-y divide-slate-50">
            @forelse($laporanBulanan as $laporan)
            <div class="px-4 py-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                            <span class="text-blue-600 text-[10px] font-black">{{ $laporan['nomor_bulan'] }}</span>
                        </div>
                        <span class="font-semibold text-slate-700 text-sm truncate">{{ $laporan['nama_bulan'] }}</span>
                    </div>
                    @if($laporan['total_kas'] > 0)
                        <span class="shrink-0 inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-[9px] font-bold px-2 py-0.5 rounded-full">
                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span> Ada Data
                        </span>
                    @elseif($laporan['nomor_bulan'] > date('n') && $tahunAktif == date('Y'))
                        <span class="shrink-0 inline-flex items-center gap-1 bg-slate-100 text-slate-400 text-[9px] font-bold px-2 py-0.5 rounded-full">
                            Belum
                        </span>
                    @else
                        <span class="shrink-0 inline-flex items-center gap-1 bg-amber-50 text-amber-500 text-[9px] font-bold px-2 py-0.5 rounded-full">
                            Nihil
                        </span>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-2 text-xs">
                    <span class="font-black font-mono text-slate-800">Rp {{ number_format($laporan['total_kas'], 0, ',', '.') }}</span>
                    <span class="text-slate-400 font-medium">{{ $laporan['jumlah_transaksi'] }} transaksi</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                    <div class="bg-blue-500 h-1.5 rounded-full transition-all"
                         style="width: {{ $laporan['persen_dari_max'] }}%"></div>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-slate-400 text-sm">
                <i class="fas fa-inbox text-3xl mb-3 block text-slate-200"></i>
                Tidak ada data untuk tahun {{ $tahunAktif }}
            </div>
            @endforelse

            @if(count($laporanBulanan) > 0)
            <div class="px-4 py-3 bg-slate-50 flex items-center justify-between">
                <span class="font-extrabold text-slate-700 text-xs">TOTAL</span>
                <div class="text-right">
                    <div class="font-black font-mono text-blue-700 text-sm">Rp {{ number_format($totalKasTahun, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-slate-400 font-semibold">{{ $totalTransaksiTahun }} transaksi</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- KONTRIBUSI PER MITRA --}}
    @if(isset($perMitra) && count($perMitra) > 0)
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100">
            <h3 class="text-sm sm:text-base font-bold text-slate-700">Kontribusi Kas Masuk per Mitra</h3>
            <p class="text-slate-400 text-[10px] sm:text-xs mt-0.5">Total omzet mitra yang menghasilkan kas BUMDes — {{ $tahunAktif }}</p>
        </div>
        <div class="p-4 sm:p-6">
            <div class="space-y-3 sm:space-y-3">
                @foreach($perMitra as $i => $pm)
                @php $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316']; @endphp

                {{-- Mobile: stacked --}}
                <div class="sm:hidden">
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="text-xs font-semibold text-slate-600 truncate">{{ $pm['nama'] }}</span>
                        <span class="text-xs font-bold shrink-0" style="color: {{ $colors[$i % count($colors)] }}">
                            {{ number_format($pm['persen'] ?? 0, 1) }}%
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden mb-1">
                        <div class="h-2.5 rounded-full transition-all duration-700"
                             style="width: {{ $pm['persen'] ?? 0 }}%; background: {{ $colors[$i % count($colors)] }}"></div>
                    </div>
                    <div class="text-right font-black font-mono text-slate-700 text-xs">
                        Rp {{ number_format($pm['total_kas'], 0, ',', '.') }}
                    </div>
                </div>

                {{-- Desktop/tablet: satu baris --}}
                <div class="hidden sm:flex items-center gap-4">
                    <div class="w-32 text-xs font-semibold text-slate-600 truncate shrink-0">{{ $pm['nama'] }}</div>
                    <div class="flex-1 bg-slate-100 rounded-full h-3 relative overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-700"
                             style="width: {{ $pm['persen'] ?? 0 }}%; background: {{ $colors[$i % count($colors)] }}"></div>
                    </div>
                    <div class="w-36 text-right font-black font-mono text-slate-700 text-xs shrink-0">
                        Rp {{ number_format($pm['total_kas'], 0, ',', '.') }}
                    </div>
                    <div class="w-12 text-right text-xs font-bold shrink-0" style="color: {{ $colors[$i % count($colors)] }}">
                        {{ number_format($pm['persen'] ?? 0, 1) }}%
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- FOOTER CETAK --}}
    <div class="hidden print:block text-center text-xs text-slate-400 mt-8 pt-4 border-t border-slate-200">
        Dicetak pada {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} — BUMDes
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = {!! json_encode($labelGrafik) !!};
const dataKas = {!! json_encode($dataKasGrafik) !!};

const maxVal = Math.max(...dataKas, 1);

new Chart(document.getElementById('grafikTahunan'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Kas Masuk BUMDes',
            data: dataKas,
            backgroundColor: dataKas.map(v =>
                v === maxVal ? '#2563eb' : 'rgba(59,130,246,0.18)'
            ),
            borderColor: dataKas.map(v =>
                v === maxVal ? '#1d4ed8' : '#93c5fd'
            ),
            borderWidth: 1.5,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val),
                    font: { size: 10 },
                    maxTicksLimit: 5
                },
                grid: { color: '#f1f5f9' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10, weight: '600' } }
            }
        }
    }
});
</script>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
}
</style>

@endsection