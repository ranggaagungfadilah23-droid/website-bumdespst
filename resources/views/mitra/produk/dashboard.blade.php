@extends('theme.default')

@section('content')
<div class="p-4 md:p-6 space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-xl md:text-2xl font-extrabold text-slate-800">
            Halo, {{ Auth::user()->name }}! 👋
        </h1>
        <p class="text-slate-400 text-xs md:text-sm mt-1">
            {{ now()->translatedFormat('l, d F Y') }} — Selamat datang di dashboard Anda.
        </p>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-5">
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white p-5 md:p-6 rounded-2xl shadow-lg shadow-blue-500/20">
            <div class="flex items-center justify-between mb-4">
                <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Pesanan Baru</p>
                <div class="bg-white/20 p-2 rounded-xl">
                    <i class="fas fa-box text-white text-sm"></i>
                </div>
            </div>
            <h3 class="text-3xl md:text-4xl font-black">{{ $pesananBaru }}</h3>
            <p class="text-blue-200 text-xs mt-2">Menunggu diproses</p>
        </div>

        <div class="bg-white border border-slate-100 p-5 md:p-6 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pendapatan Lunas</p>
                <div class="bg-emerald-50 p-2 rounded-xl">
                    <i class="fas fa-wallet text-emerald-500 text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl md:text-3xl font-black text-slate-800 break-words">
                Rp {{ number_format($totalPendapatanBersih, 0, ',', '.') }}
            </h3>
            <p class="text-slate-400 text-xs mt-2">Total terkonfirmasi</p>
        </div>

        <div class="bg-white border border-slate-100 p-5 md:p-6 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Transaksi Selesai</p>
                <div class="bg-amber-50 p-2 rounded-xl">
                    <i class="fas fa-check-circle text-amber-500 text-sm"></i>
                </div>
            </div>
            <h3 class="text-3xl md:text-4xl font-black text-slate-800">{{ $pesananSelesai }}</h3>
            <p class="text-slate-400 text-xs mt-2">Sudah dikonfirmasi lunas</p>
        </div>
    </div>

    {{-- GRAFIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="font-bold text-slate-700 mb-1">Tren Pendapatan</h3>
            <p class="text-slate-400 text-xs mb-5">Pendapatan bulanan yang sudah lunas</p>
            <div class="relative w-full" style="height: 220px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="font-bold text-slate-700 mb-1">Status Pesanan</h3>
            <p class="text-slate-400 text-xs mb-5">Distribusi status seluruh pesanan</p>
            <div class="relative w-full" style="height: 220px;">
                <canvas id="orderChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TABEL PESANAN TERBARU --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 md:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-700 text-sm md:text-base">Pesanan Terbaru</h3>
            <a href="{{ route('mitra.pesanan.index') }}"
               class="text-xs font-bold text-blue-600 hover:underline whitespace-nowrap">
                Lihat Semua →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[600px]">
                <thead class="bg-slate-50 text-slate-400 text-[11px] uppercase font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-4 md:px-6 py-3">No. Invoice</th>
                        <th class="px-4 md:px-6 py-3">Pelanggan</th>
                        <th class="px-4 md:px-6 py-3">Item</th>
                        <th class="px-4 md:px-6 py-3">Status Bayar</th>
                        <th class="px-4 md:px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pesananTerbaru as $item)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 md:px-6 py-4 font-bold text-blue-600">
                            #{{ $item->invoice_number ?? '-' }}
                        </td>
                        <td class="px-4 md:px-6 py-4 text-slate-700 font-medium">
                            {{ $item->customer->name ?? 'Anonim' }}
                        </td>
                        <td class="px-4 md:px-6 py-4 text-slate-500">
                            {{ $item->produk->nama_produk ?? '-' }}
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            @if($item->status_pembayaran == 'Lunas')
                                <span class="bg-emerald-100 text-emerald-600 text-[10px] font-black px-2 py-1 rounded-full uppercase">Lunas</span>
                            @else
                                <span class="bg-amber-100 text-amber-600 text-[10px] font-black px-2 py-1 rounded-full uppercase">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-4 text-right font-bold text-slate-700">
                            Rp {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-3 block text-slate-200"></i>
                            Belum ada pesanan masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($pendapatanBulanan->pluck('bulan')) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($pendapatanBulanan->pluck('total')) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 4,
                tension: 0.4,
                fill: true,
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
                    ticks: {
                        callback: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('orderChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusPesanan->keys()) !!},
            datasets: [{
                data: {!! json_encode($statusPesanan->values()) !!},
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11, weight: 'bold' },
                        padding: 15,
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.raw + ' pesanan'
                    }
                }
            }
        }
    });
</script>

@endsection