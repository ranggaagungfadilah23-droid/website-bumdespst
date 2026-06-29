@extends('theme.default')

@section('content')

@php
    // Helper format angka besar jadi singkat (mis. 12.300.000 -> "12,3 Jt") khusus tampilan mobile
    $formatRupiah = function ($value) {
        if ($value >= 1000000000) {
            return number_format($value / 1000000000, 1, ',', '.') . ' M';
        }
        if ($value >= 1000000) {
            return number_format($value / 1000000, 1, ',', '.') . ' Jt';
        }
        return number_format($value, 0, ',', '.');
    };
@endphp

<div class="min-h-screen bg-slate-50 p-3 sm:p-6 md:p-10 space-y-4 sm:space-y-6">

    {{-- HEADER EXECUTIVE --}}
    <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl sm:rounded-3xl p-5 sm:p-8 text-white shadow-lg shadow-emerald-600/20 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 sm:gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-32 -mb-10 w-32 h-32 bg-emerald-300 opacity-20 rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <p class="text-emerald-100 text-[11px] sm:text-sm font-bold tracking-widest uppercase mb-1">Executive Dashboard</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Halo, {{ auth()->user()->name }}! 👋
            </h1>
            {{-- Deskripsi panjang disembunyikan di mobile biar header gak makan tempat --}}
            <p class="hidden sm:block text-emerald-50 text-sm mt-2 max-w-xl leading-relaxed">
                Ini adalah ringkasan performa BUMDes Putra Samudra Patimban hari ini. Pantau pendaftaran mitra baru dan arus pendapatan bagi hasil secara real-time.
            </p>
        </div>

        <div class="relative z-10 flex items-center gap-3 bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2.5 sm:px-5 sm:py-3 rounded-xl sm:rounded-2xl shadow-sm">
            <div class="bg-emerald-500/50 p-2 rounded-xl">
                <i class="fas fa-calendar-day text-white text-base sm:text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] sm:text-xs text-emerald-100 font-medium">Hari ini</p>
                <p class="text-xs sm:text-sm font-bold">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
            </div>
        </div>
    </div>

    {{-- 4 KARTU STATISTIK UTAMA — 2 kolom di mobile, 4 kolom di desktop --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">

        {{-- Card 1: Pengajuan Baru --}}
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-amber-300 transition-all">
            <div class="absolute top-0 right-0 w-16 sm:w-24 h-16 sm:h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="flex justify-between items-start relative z-10">
                <div class="min-w-0">
                    <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider mb-1">Butuh Persetujuan</p>
                    <h3 class="text-xl sm:text-3xl font-black text-slate-800">{{ $totalMenunggu }}</h3>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-amber-500 text-white rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30 shrink-0">
                    <i class="fas fa-user-clock text-sm sm:text-xl"></i>
                </div>
            </div>
            <a href="{{ route('kepala-bumdes.pengajuan') }}" class="mt-3 sm:mt-5 inline-flex items-center text-[10px] sm:text-xs font-bold text-amber-500 hover:text-amber-600 transition relative z-10">
                Tinjau <i class="fas fa-arrow-right ml-1 sm:ml-1.5"></i>
            </a>
        </div>

        {{-- Card 2: Mitra Aktif --}}
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-300 transition-all">
            <div class="absolute top-0 right-0 w-16 sm:w-24 h-16 sm:h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="flex justify-between items-start relative z-10">
                <div class="min-w-0">
                    <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider mb-1">Mitra Aktif</p>
                    <h3 class="text-xl sm:text-3xl font-black text-slate-800">{{ $totalAktif }}</h3>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-500 text-white rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0">
                    <i class="fas fa-store text-sm sm:text-xl"></i>
                </div>
            </div>
            <a href="{{ route('kepala-bumdes.data-mitra') }}" class="mt-3 sm:mt-5 inline-flex items-center text-[10px] sm:text-xs font-bold text-blue-500 hover:text-blue-600 transition relative z-10">
                Direktori <i class="fas fa-arrow-right ml-1 sm:ml-1.5"></i>
            </a>
        </div>

        {{-- Card 3: Total Pemasukan BUMDes --}}
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition-all">
            <div class="absolute top-0 right-0 w-16 sm:w-24 h-16 sm:h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="flex justify-between items-start relative z-10">
                <div class="min-w-0">
                    <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider mb-1">Pemasukan</p>
                    <h3 class="text-base sm:text-2xl font-black text-slate-800 font-mono mt-1 truncate">
                        <span class="sm:hidden">Rp {{ $formatRupiah($totalPemasukanBumdes) }}</span>
                        <span class="hidden sm:inline">Rp {{ number_format($totalPemasukanBumdes, 0, ',', '.') }}</span>
                    </h3>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-emerald-500 text-white rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 shrink-0">
                    <i class="fas fa-hand-holding-usd text-sm sm:text-xl"></i>
                </div>
            </div>
            <p class="mt-3 sm:mt-5 text-[10px] sm:text-xs font-semibold text-slate-400 relative z-10">
                Bagi hasil
            </p>
        </div>

        {{-- Card 4: Total Pengeluaran BUMDes --}}
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-red-300 transition-all">
            <div class="absolute top-0 right-0 w-16 sm:w-24 h-16 sm:h-24 bg-red-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div class="flex justify-between items-start relative z-10">
                <div class="min-w-0">
                    <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider mb-1">Pengeluaran</p>
                    <h3 class="text-base sm:text-2xl font-black text-slate-800 font-mono mt-1 truncate">
                        <span class="sm:hidden">Rp {{ $formatRupiah($totalPengeluaranBumdes) }}</span>
                        <span class="hidden sm:inline">Rp {{ number_format($totalPengeluaranBumdes, 0, ',', '.') }}</span>
                    </h3>
                </div>
                <div class="w-8 h-8 sm:w-12 sm:h-12 bg-red-500 text-white rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 shrink-0">
                    <i class="fas fa-file-invoice-dollar text-sm sm:text-xl"></i>
                </div>
            </div>
            <a href="{{ route('kepala-bumdes.monitoring-keuangan') }}" class="mt-3 sm:mt-5 inline-flex items-center text-[10px] sm:text-xs font-bold text-red-500 hover:text-red-600 transition relative z-10">
                Monitoring <i class="fas fa-arrow-right ml-1 sm:ml-1.5"></i>
            </a>
        </div>

    </div>

    {{-- BAGIAN TENGAH: GRAFIK & DIAGRAM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

        {{-- Area Chart: Tren Pemasukan --}}
        <div class="lg:col-span-2 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 p-4 sm:p-6">
            <div class="flex justify-between items-center mb-3 sm:mb-6">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800">Tren Pemasukan</h3>
                    <p class="hidden sm:block text-xs text-slate-400 mt-1">Pergerakan omzet bersih BUMDes selama 6 bulan terakhir.</p>
                </div>
                <div class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-[10px] sm:text-xs font-bold shrink-0">
                    6 Bulan
                </div>
            </div>
            <div class="relative h-44 sm:h-64 w-full">
                <canvas id="trenPendapatanChart"></canvas>
            </div>
        </div>

        {{-- Doughnut Chart: Komposisi Mitra --}}
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 p-4 sm:p-6 flex flex-col">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">Status Kemitraan</h3>
                <p class="hidden sm:block text-xs text-slate-400 mt-1">Persentase pengajuan mitra Patimban.</p>
            </div>
            <div class="relative flex-1 flex items-center justify-center mt-3 sm:mt-4">
                <div class="w-32 h-32 sm:w-48 sm:h-48">
                    <canvas id="statusMitraChart"></canvas>
                </div>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-lg sm:text-2xl font-black text-slate-800">{{ $totalAktif + $totalMenunggu + $totalDitolak }}</span>
                    <span class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase">Total Akun</span>
                </div>
            </div>
            <div class="mt-3 sm:mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-3 sm:pt-4">
                <div class="text-center">
                    <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-blue-500 mx-auto mb-1"></div>
                    <p class="text-[11px] sm:text-xs font-bold text-slate-700">{{ $totalAktif }}</p>
                    <p class="text-[9px] sm:text-[10px] text-slate-400">Aktif</p>
                </div>
                <div class="text-center border-x border-slate-100">
                    <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-amber-400 mx-auto mb-1"></div>
                    <p class="text-[11px] sm:text-xs font-bold text-slate-700">{{ $totalMenunggu }}</p>
                    <p class="text-[9px] sm:text-[10px] text-slate-400">Proses</p>
                </div>
                <div class="text-center">
                    <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-red-400 mx-auto mb-1"></div>
                    <p class="text-[11px] sm:text-xs font-bold text-slate-700">{{ $totalDitolak }}</p>
                    <p class="text-[9px] sm:text-[10px] text-slate-400">Ditolak</p>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN BAWAH: TABEL (desktop) / LIST RINGKAS (mobile) PENGAJUAN TERBARU --}}
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-sm sm:text-base font-bold text-slate-800">
                <i class="fas fa-bell text-amber-500 mr-1.5 sm:mr-2"></i>
                <span class="hidden sm:inline">Perlu Tindakan Anda </span>(Pengajuan Baru)
            </h3>
            <a href="{{ route('kepala-bumdes.pengajuan') }}" class="text-[10px] sm:text-xs font-bold text-blue-600 hover:text-blue-700 transition bg-blue-50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg shrink-0">
                Lihat Semua
            </a>
        </div>

        @if(count($pengajuanTerbaru) > 0)

        {{-- Tablet ke atas: tabel penuh --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-slate-400 text-xs uppercase font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Nama Mitra / Pemilik</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Waktu Daftar</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($pengajuanTerbaru as $mitra)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-800">{{ $mitra->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $mitra->email }}</td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $mitra->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Anda
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('kepala-bumdes.pengajuan') }}" class="inline-flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm">
                                Tinjau
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: list ringkas, gak perlu scroll horizontal --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach($pengajuanTerbaru as $mitra)
            <div class="px-4 py-3 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $mitra->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ $mitra->email }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $mitra->created_at->diffForHumans() }}</p>
                </div>
                <a href="{{ route('kepala-bumdes.pengajuan') }}" class="shrink-0 inline-flex items-center justify-center bg-slate-800 text-white text-[11px] font-bold px-3 py-2 rounded-lg shadow-sm">
                    Tinjau
                </a>
            </div>
            @endforeach
        </div>

        @else
        <div class="px-6 py-10 sm:py-16 text-center">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                <i class="fas fa-check-double text-2xl sm:text-3xl text-emerald-400"></i>
            </div>
            <h4 class="text-sm sm:text-base font-bold text-slate-700 mb-1">Semua Beres!</h4>
            <p class="text-xs sm:text-sm text-slate-400">Saat ini tidak ada pengajuan mitra baru yang menunggu persetujuan Anda.</p>
        </div>
        @endif
    </div>

</div>

{{-- SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxTren = document.getElementById('trenPendapatanChart').getContext('2d');

    let gradientArea = ctxTren.createLinearGradient(0, 0, 0, 300);
    gradientArea.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
    gradientArea.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctxTren, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartBulan) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartPendapatan) !!},
                borderColor: '#10b981',
                backgroundColor: gradientArea,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#10b981',
                pointBorderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 10 },
                        maxTicksLimit: 5,
                        callback: function(value) {
                            if(value >= 1000000) return 'Rp ' + (value/1000000) + ' Jt';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10 } }
                }
            }
        }
    });

    const ctxStatus = document.getElementById('statusMitraChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Proses', 'Ditolak'],
            datasets: [{
                data: [{{ $totalAktif }}, {{ $totalMenunggu }}, {{ $totalDitolak }}],
                backgroundColor: [
                    '#3b82f6',
                    '#fbbf24',
                    '#f87171'
                ],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    bodyFont: { size: 13, weight: 'bold' },
                    displayColors: true,
                    boxPadding: 4
                }
            }
        }
    });
});
</script>
@endsection