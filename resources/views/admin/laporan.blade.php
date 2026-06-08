@extends('theme.default')
@section('title', 'Laporan Keuangan - BUMDes Patimban')
@section('content')
<div class="p-8 space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Laporan Keuangan BUMDes</h1>
            <p class="text-slate-400 text-sm mt-1">Rekapitulasi pendapatan dan bagi hasil — {{ $bulanAktif }}</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Tombol Kirim Laporan --}}
            <button onclick="document.getElementById('modalKirimLaporan').classList.remove('hidden')"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-md shadow-emerald-500/20 transition-all">
                <i class="fas fa-paper-plane"></i> Kirim Laporan ke Kepala
            </button>
        </div>
    </div>

    {{-- STATUS PENGIRIMAN --}}
    @if(session('laporan_terkirim'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 flex items-center gap-3">
        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
            <i class="fas fa-check text-emerald-600 text-sm"></i>
        </div>
        <div>
            <p class="text-emerald-800 font-bold text-sm">Laporan berhasil dikirim!</p>
            <p class="text-emerald-600 text-xs">Laporan bulan {{ $bulanAktif }} telah dikirim ke Kepala BUMDes.</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-sm"></i>
        <p class="text-red-700 font-semibold text-sm">{{ session('error') }}</p>
    </div>
    @endif

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white p-6 rounded-2xl shadow-lg shadow-blue-500/20">
            <div class="flex items-center justify-between mb-4">
                <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Kas Masuk BUMDes</p>
                <div class="bg-white/20 p-2 rounded-xl"><i class="fas fa-university text-white"></i></div>
            </div>
            <h3 class="text-3xl font-black font-mono">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</h3>
            <p class="text-blue-200 text-xs mt-2">Dari bagi hasil yang sudah selesai</p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Omzet Mitra</p>
                <div class="bg-emerald-50 p-2 rounded-xl"><i class="fas fa-chart-line text-emerald-500"></i></div>
            </div>
            <h3 class="text-3xl font-black font-mono text-slate-800">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-xs mt-2">Bulan {{ $bulanAktif }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Mitra Aktif</p>
                <div class="bg-amber-50 p-2 rounded-xl"><i class="fas fa-store text-amber-500"></i></div>
            </div>
            <h3 class="text-3xl font-black font-mono text-slate-800">{{ $totalMitra }}</h3>
            <p class="text-slate-400 text-xs mt-2">Terdaftar & aktif</p>
        </div>
    </div>

    @if($totalKasMasuk == 0 && $totalBagiHasil == 0)
    {{-- EMPTY STATE --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 text-center py-20">
        <i class="fas fa-chart-line text-5xl text-slate-200 mb-4 block"></i>
        <h3 class="text-lg font-bold text-slate-800">Grafik Laporan Belum Tersedia</h3>
        <p class="text-slate-400 text-sm">Belum ada bagi hasil yang selesai dikonfirmasi.</p>
    </div>
    @else

    {{-- GRAFIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Grafik Line: Tren Bulanan --}}
        <div class="md:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-base font-bold text-slate-700 mb-1">Tren Omzet & Kas BUMDes</h3>
            <p class="text-slate-400 text-xs mb-6">Perbandingan omzet mitra vs kas masuk BUMDes per bulan</p>
            <canvas id="grafikBulanan" height="120"></canvas>
        </div>

        {{-- Grafik Donut: Per Mitra --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-base font-bold text-slate-700 mb-1">Kontribusi per Mitra</h3>
            <p class="text-slate-400 text-xs mb-6">Berdasarkan total omzet keseluruhan</p>
            <canvas id="grafikMitra"></canvas>
            <div class="mt-4 space-y-2">
                @foreach($perMitra as $i => $pm)
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full inline-block" style="background: {{ ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'][$i % 5] }}"></span>
                        <span class="text-slate-600 font-medium">{{ $pm['nama'] }}</span>
                    </div>
                    <span class="font-bold text-slate-700">Rp {{ number_format($pm['omzet'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

   {{-- TABEL DETAIL BAGI HASIL --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-700">Detail Bagi Hasil per Mitra</h3>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400">{{ $bulanAktif }}</span>
            <a href="{{ route('admin.laporan.pdf') }}" target="_blank"
                class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-sm shadow-red-500/20">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50">
                    <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Mitra</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Total Omzet</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">% Bumdes</th>
                    <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Kas Masuk BUMDes</th>
                    <th class="text-center px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($perMitra as $pm)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-700">{{ $pm['nama'] }}</td>
                    <td class="px-6 py-4 text-right font-mono text-slate-600">Rp {{ number_format($pm['omzet'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-slate-500">{{ $pm['persen_bumdes'] ?? '-' }}%</td>
                    <td class="px-6 py-4 text-right font-black font-mono text-blue-700">Rp {{ number_format($pm['kas_bumdes'] ?? 0, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 text-xs font-bold px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Selesai
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 border-t-2 border-slate-200">
                    <td class="px-6 py-4 font-extrabold text-slate-700">TOTAL</td>
                    <td class="px-6 py-4 text-right font-black font-mono text-slate-700">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</td>
                    <td></td>
                    <td class="px-6 py-4 text-right font-black font-mono text-blue-700 text-base">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
    @endif

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- MODAL KIRIM LAPORAN                                            --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div id="modalKirimLaporan" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="document.getElementById('modalKirimLaporan').classList.add('hidden')"></div>

    {{-- Modal Card --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5 z-10">

        {{-- Icon & Judul --}}
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-paper-plane text-emerald-600 text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-800">Kirim Laporan Kas</h3>
                <p class="text-slate-400 text-sm mt-0.5">Laporan akan dikirim ke Kepala BUMDes</p>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-slate-50 rounded-xl p-4 space-y-2.5 text-sm">
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Periode</span>
                <span class="font-bold text-slate-700">{{ $bulanAktif }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Total Omzet Mitra</span>
                <span class="font-bold text-slate-700">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-2.5">
                <span class="text-blue-600 font-bold">Kas Masuk BUMDes</span>
                <span class="font-black text-blue-700">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 font-medium">Jumlah Mitra</span>
                <span class="font-bold text-slate-700">{{ $totalMitra }} mitra</span>
            </div>
        </div>

        {{-- Catatan opsional --}}
        <form action="{{ route('admin.laporan.kirim') }}" method="POST">
            @csrf
            <input type="hidden" name="bulan_aktif" value="{{ $bulanAktif }}">
            <input type="hidden" name="total_kas_masuk" value="{{ $totalKasMasuk }}">
            <input type="hidden" name="total_omzet" value="{{ $totalBagiHasil }}">
            <input type="hidden" name="total_mitra" value="{{ $totalMitra }}">

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan (opsional)</label>
                <textarea name="catatan" rows="3"
                    placeholder="Tambahkan catatan untuk Kepala BUMDes..."
                    class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 text-slate-700 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none"></textarea>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-3 mt-5">
                <button type="button"
                    onclick="document.getElementById('modalKirimLaporan').classList.add('hidden')"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i> Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($totalKasMasuk > 0 || $totalBagiHasil > 0)

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
                    pointBackgroundColor: '#3b82f6',
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Kas Masuk BUMDes',
                    data: {!! json_encode($dataKasBumdes) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { font: { weight: 'bold' } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    ticks: { callback: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) },
                    grid: { color: '#f1f5f9' }
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
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                    }
                }
            }
        }
    });

    @endif
</script>

@endsection
