@extends('theme.default')

@section('content')
<div class="p-4 space-y-5">

    {{-- HEADER --}}
    <div>
        <h1 class="text-lg font-extrabold text-slate-800">
            Halo, {{ Auth::user()->name }}! 👋
        </h1>
        <p class="text-slate-400 text-xs mt-1">
            {{ now()->translatedFormat('l, d F Y') }} — Selamat datang di dashboard Anda.
        </p>
    </div>

    {{-- STAT CARDS: grid 2 kolom, compact --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-sm">
            <div class="bg-blue-50 w-8 h-8 flex items-center justify-center rounded-lg mb-3">
                <i class="fas fa-box text-blue-500 text-xs"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wide">Pesanan Baru</p>
            <h3 class="text-xl font-black text-slate-800 mt-1">{{ $pesananBaru }}</h3>
        </div>

        <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-sm">
            <div class="bg-amber-50 w-8 h-8 flex items-center justify-center rounded-lg mb-3">
                <i class="fas fa-check-circle text-amber-500 text-xs"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wide">Selesai</p>
            <h3 class="text-xl font-black text-slate-800 mt-1">{{ $pesananSelesai }}</h3>
        </div>

        <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-sm col-span-2">
            <div class="bg-emerald-50 w-8 h-8 flex items-center justify-center rounded-lg mb-3">
                <i class="fas fa-wallet text-emerald-500 text-xs"></i>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wide">Pendapatan Lunas</p>
            <h3 class="text-xl font-black text-slate-800 mt-1">
                Rp {{ number_format($totalPendapatanBersih, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    {{-- GRAFIK --}}
    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
        <h3 class="font-bold text-slate-700 text-sm mb-1">Tren Pendapatan</h3>
        <p class="text-slate-400 text-[11px] mb-3">Pendapatan bulanan yang sudah lunas</p>
        <div class="relative w-full" style="height: 180px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
        <h3 class="font-bold text-slate-700 text-sm mb-1">Status Pesanan</h3>
        <p class="text-slate-400 text-[11px] mb-3">Distribusi status seluruh pesanan</p>
        <div class="relative w-full" style="height: 180px;">
            <canvas id="orderChart"></canvas>
        </div>
    </div>

    {{-- TABEL PESANAN TERBARU --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-700 text-sm">Pesanan Terbaru</h3>
            <a href="{{ route('mitra.pesanan.index') }}" class="text-[11px] font-bold text-blue-600 hover:underline">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left min-w-[500px]">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-wide border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-2">Invoice</th>
                        <th class="px-4 py-2">Pelanggan</th>
                        <th class="px-4 py-2">Item</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pesananTerbaru as $item)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 py-3 font-bold text-blue-600">#{{ $item->invoice_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-700 font-medium">{{ $item->customer->name ?? 'Anonim' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $item->produk->nama_produk ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($item->status_pembayaran == 'Lunas')
                                <span class="bg-emerald-100 text-emerald-600 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Lunas</span>
                            @else
                                <span class="bg-amber-100 text-amber-600 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-slate-700">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                            <i class="fas fa-inbox text-2xl mb-2 block text-slate-200"></i>
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
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
            },
            scales: {
                y: { ticks: { font: { size: 10 }, callback: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
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
                legend: { position: 'bottom', labels: { font: { size: 10, weight: 'bold' }, padding: 10 } },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.raw + ' pesanan' } }
            }
        }
    });
</script>
@endsection