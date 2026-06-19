@extends('theme.default')
@section('title', 'Monitoring Keuangan - BUMDes Patimban')
@section('content')
<div class="min-h-screen bg-slate-50 p-3 sm:p-6 md:p-10 space-y-4 sm:space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col gap-3 sm:gap-4">
        <div>
            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">BUMDes — Keuangan Operasional</p>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-800 leading-tight">Monitoring Keuangan</h1>
            <p class="text-slate-400 text-xs sm:text-sm mt-1">Pantau arus kas pemasukan & pengeluaran operasional BUMDes</p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:gap-3">
            {{-- Tombol Aksi Input Data --}}
            <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-3">
                <button type="button" onclick="openModal('modalSaldoAwal')" class="inline-flex items-center justify-center gap-1.5 sm:gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl shadow transition active:scale-95">
                    <i class="fas fa-coins"></i> <span class="truncate">Saldo Awal</span>
                </button>
                <button type="button" onclick="openModal('modalPengeluaran')" class="inline-flex items-center justify-center gap-1.5 sm:gap-2 bg-red-500 hover:bg-red-600 text-white text-xs sm:text-sm font-bold px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl shadow transition active:scale-95">
                    <i class="fas fa-file-invoice-dollar"></i> <span class="truncate">Pengeluaran</span>
                </button>
            </div>

            {{-- Filter Bulan & Tahun --}}
            <form method="GET" action="" class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-2">
                <select name="bulan" onchange="this.form.submit()"
                    class="text-xs sm:text-sm border border-slate-200 rounded-lg sm:rounded-xl px-2.5 sm:px-3 py-2 bg-white text-slate-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                        <option value="{{ $i+1 }}" {{ $bulanAktif == ($i+1) ? 'selected' : '' }}>{{ $bln }}</option>
                    @endforeach
                </select>
                <select name="tahun" onchange="this.form.submit()"
                    class="text-xs sm:text-sm border border-slate-200 rounded-lg sm:rounded-xl px-2.5 sm:px-3 py-2 bg-white text-slate-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $tahunAktif == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="p-3 sm:p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KARTU RINGKASAN — 2 kolom di mobile --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        {{-- Saldo Akhir --}}
        <div class="col-span-2 lg:col-span-1 bg-gradient-to-br {{ $saldoAkhir >= 0 ? 'from-emerald-600 to-emerald-800' : 'from-red-500 to-red-700' }} text-white p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg {{ $saldoAkhir >= 0 ? 'shadow-emerald-500/20' : 'shadow-red-500/20' }}">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <p class="text-white/80 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Saldo Akhir</p>
                <div class="bg-white/20 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shrink-0"><i class="fas fa-wallet text-white text-xs sm:text-sm"></i></div>
            </div>
            <h3 class="text-xl sm:text-2xl font-black font-mono">Rp {{ number_format(abs($saldoAkhir), 0, ',', '.') }}</h3>
            <p class="text-white/70 text-[10px] sm:text-xs mt-1 sm:mt-2">{{ $saldoAkhir >= 0 ? 'Surplus bulan ini' : 'Defisit bulan ini' }}</p>
        </div>

        {{-- Total Pemasukan --}}
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider">Pemasukan</p>
                <div class="bg-blue-50 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shrink-0"><i class="fas fa-arrow-down text-blue-500 text-xs sm:text-sm"></i></div>
            </div>
            <h3 class="text-base sm:text-2xl font-black font-mono text-slate-800 truncate">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-[9px] sm:text-xs mt-1 sm:mt-2">{{ $jumlahTransaksiMasuk }} transaksi</p>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider">Pengeluaran</p>
                <div class="bg-red-50 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shrink-0"><i class="fas fa-arrow-up text-red-400 text-xs sm:text-sm"></i></div>
            </div>
            <h3 class="text-base sm:text-2xl font-black font-mono text-slate-800 truncate">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-[9px] sm:text-xs mt-1 sm:mt-2">{{ $jumlahTransaksiKeluar }} transaksi</p>
        </div>

        {{-- Saldo Awal --}}
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <p class="text-slate-400 text-[9px] sm:text-xs font-bold uppercase tracking-wider">Saldo Awal</p>
                <div class="bg-slate-100 p-1.5 sm:p-2 rounded-lg sm:rounded-xl shrink-0"><i class="fas fa-coins text-slate-400 text-xs sm:text-sm"></i></div>
            </div>
            <h3 class="text-base sm:text-2xl font-black font-mono text-slate-800 truncate">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-[9px] sm:text-xs mt-1 sm:mt-2 truncate">Per awal {{ $namaBulan }}</p>
        </div>
    </div>

    {{-- GRAFIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        {{-- Grafik Arus Kas Harian --}}
        <div class="md:col-span-2 bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100">
            <h3 class="text-sm sm:text-base font-bold text-slate-700 mb-1">Arus Kas Harian</h3>
            <p class="text-slate-400 text-[10px] sm:text-xs mb-3 sm:mb-5">Pemasukan vs pengeluaran — {{ $namaBulan }} {{ $tahunAktif }}</p>
            <div class="relative h-44 sm:h-64">
                <canvas id="grafikArusKas"></canvas>
            </div>
        </div>

        {{-- Grafik Donut Kategori --}}
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100">
            <h3 class="text-sm sm:text-base font-bold text-slate-700 mb-1">Pengeluaran per Kategori</h3>
            <p class="text-slate-400 text-[10px] sm:text-xs mb-3 sm:mb-5">{{ $namaBulan }} {{ $tahunAktif }}</p>
            @if(count($kategoriPengeluaran) > 0)
            <div class="relative h-36 sm:h-48 mb-3 sm:mb-4">
                <canvas id="grafikKategori"></canvas>
            </div>
            <div class="space-y-2 mt-3 sm:mt-4">
                @foreach($kategoriPengeluaran as $i => $kat)
                @php $warna = ['#ef4444','#f59e0b','#8b5cf6','#3b82f6','#10b981','#f97316','#06b6d4']; @endphp
                <div class="flex items-center justify-between text-[11px] sm:text-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $warna[$i % count($warna)] }}"></span>
                        <span class="text-slate-600 font-medium truncate">{{ $kat['kategori'] }}</span>
                    </div>
                    <span class="font-bold text-slate-700 shrink-0">Rp {{ number_format($kat['total'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 sm:py-10 text-slate-300">
                <i class="fas fa-chart-pie text-3xl sm:text-4xl mb-2 block"></i>
                <p class="text-xs">Belum ada data</p>
            </div>
            @endif
        </div>
    </div>

    {{-- TABS: PEMASUKAN & PENGELUARAN --}}
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        {{-- Tab Header --}}
        <div class="flex border-b border-slate-100">
            <button onclick="switchTab('pemasukan')" id="tab-pemasukan"
                class="flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-blue-600 border-b-2 border-blue-600 bg-blue-50/40 transition">
                <i class="fas fa-arrow-circle-down mr-1 sm:mr-2"></i>Pemasukan
                <span class="ml-1 sm:ml-2 bg-blue-100 text-blue-700 text-[10px] sm:text-xs font-black px-1.5 sm:px-2 py-0.5 rounded-full">{{ $jumlahTransaksiMasuk }}</span>
            </button>
            <button onclick="switchTab('pengeluaran')" id="tab-pengeluaran"
                class="flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition">
                <i class="fas fa-arrow-circle-up mr-1 sm:mr-2"></i>Pengeluaran
                <span class="ml-1 sm:ml-2 bg-slate-100 text-slate-500 text-[10px] sm:text-xs font-black px-1.5 sm:px-2 py-0.5 rounded-full">{{ $jumlahTransaksiKeluar }}</span>
            </button>
        </div>

        {{-- Tab Pemasukan --}}
        <div id="panel-pemasukan">
            {{-- Tablet ke atas: tabel --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Sumber</th>
                            <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($dataPemasukan as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5 text-slate-500 text-xs font-medium whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 font-medium">{{ $item->keterangan }}</td>
                            <td class="px-6 py-3.5">
                                <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-1 rounded-full">
                                    {{ $item->sumber ?? 'Bagi Hasil Mitra' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-black font-mono text-blue-700">
                                + Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center text-slate-300 text-sm">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                Tidak ada pemasukan pada bulan ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($dataPemasukan) > 0)
                    <tfoot>
                        <tr class="bg-blue-50 border-t-2 border-blue-100">
                            <td colspan="3" class="px-6 py-4 font-extrabold text-blue-700">TOTAL PEMASUKAN</td>
                            <td class="px-6 py-4 text-right font-black font-mono text-blue-700 text-base">
                                + Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            {{-- Mobile: list ringkas --}}
            <div class="md:hidden divide-y divide-slate-50">
                @forelse($dataPemasukan as $item)
                <div class="px-4 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-slate-700 font-semibold text-sm truncate">{{ $item->keterangan }}</p>
                            <p class="text-slate-400 text-[10px] mt-0.5">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY') }}</p>
                        </div>
                        <span class="shrink-0 bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap">
                            {{ $item->sumber ?? 'Bagi Hasil' }}
                        </span>
                    </div>
                    <p class="text-right font-black font-mono text-blue-700 text-sm mt-1.5">
                        + Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                    </p>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-slate-300 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    Tidak ada pemasukan pada bulan ini
                </div>
                @endforelse

                @if(count($dataPemasukan) > 0)
                <div class="px-4 py-3 bg-blue-50 flex items-center justify-between">
                    <span class="font-extrabold text-blue-700 text-xs">TOTAL PEMASUKAN</span>
                    <span class="font-black font-mono text-blue-700 text-sm">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Tab Pengeluaran --}}
        <div id="panel-pengeluaran" class="hidden">
            {{-- Tablet ke atas: tabel --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="text-left px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                            <th class="text-right px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah</th>
                            <th class="text-center px-6 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($dataPengeluaran as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5 text-slate-500 text-xs font-medium whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY') }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 font-medium">{{ $item->keterangan }}</td>
                            <td class="px-6 py-3.5">
                                @php
                                    $catColors = [
                                        'Operasional' => 'bg-amber-50 text-amber-600',
                                        'Gaji' => 'bg-purple-50 text-purple-600',
                                        'Pembelian' => 'bg-blue-50 text-blue-600',
                                        'Lain-lain' => 'bg-slate-100 text-slate-500',
                                    ];
                                    $cat = $item->kategori ?? 'Lain-lain';
                                    $catClass = $catColors[$cat] ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span class="{{ $catClass }} text-xs font-bold px-2.5 py-1 rounded-full">
                                    {{ $cat }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-black font-mono text-red-500">
                                - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <form action="{{ route('kepala-bumdes.hapus-pengeluaran', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan rekap pengeluaran operasional ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-500 text-xs font-bold px-3 py-1.5 rounded-xl transition active:scale-95">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center text-slate-300 text-sm">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                Tidak ada pengeluaran pada bulan ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($dataPengeluaran) > 0)
                    <tfoot>
                        <tr class="bg-red-50 border-t-2 border-red-100">
                            <td colspan="3" class="px-6 py-4 font-extrabold text-red-600">TOTAL PENGELUARAN</td>
                            <td class="px-6 py-4 text-right font-black font-mono text-red-600 text-base">
                                - Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            {{-- Mobile: list ringkas + tombol hapus --}}
            <div class="md:hidden divide-y divide-slate-50">
                @forelse($dataPengeluaran as $item)
                @php
                    $catColors = [
                        'Operasional' => 'bg-amber-50 text-amber-600',
                        'Gaji' => 'bg-purple-50 text-purple-600',
                        'Pembelian' => 'bg-blue-50 text-blue-600',
                        'Lain-lain' => 'bg-slate-100 text-slate-500',
                    ];
                    $cat = $item->kategori ?? 'Lain-lain';
                    $catClass = $catColors[$cat] ?? 'bg-slate-100 text-slate-500';
                @endphp
                <div class="px-4 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-slate-700 font-semibold text-sm truncate">{{ $item->keterangan }}</p>
                            <p class="text-slate-400 text-[10px] mt-0.5">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY') }}</p>
                        </div>
                        <span class="{{ $catClass }} shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap">
                            {{ $cat }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mt-1.5">
                        <p class="font-black font-mono text-red-500 text-sm">
                            - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        </p>
                        <form action="{{ route('kepala-bumdes.hapus-pengeluaran', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan rekap pengeluaran operasional ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-500 text-[11px] font-bold px-2.5 py-1 rounded-lg transition active:scale-95">
                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center text-slate-300 text-sm">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    Tidak ada pengeluaran pada bulan ini
                </div>
                @endforelse

                @if(count($dataPengeluaran) > 0)
                <div class="px-4 py-3 bg-red-50 flex items-center justify-between">
                    <span class="font-extrabold text-red-600 text-xs">TOTAL PENGELUARAN</span>
                    <span class="font-black font-mono text-red-600 text-sm">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- RINGKASAN ARUS KAS --}}
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-100 p-4 sm:p-6">
        <h3 class="text-sm sm:text-base font-bold text-slate-700 mb-3 sm:mb-5">Ringkasan Arus Kas — {{ $namaBulan }} {{ $tahunAktif }}</h3>
        <div class="space-y-2 sm:space-y-3">
            <div class="flex items-center justify-between py-2.5 sm:py-3 border-b border-slate-100">
                <span class="text-xs sm:text-sm text-slate-500 font-medium">Saldo Awal Bulan</span>
                <span class="font-black font-mono text-slate-700 text-sm sm:text-base">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-2.5 sm:py-3 border-b border-slate-100">
                <span class="text-xs sm:text-sm text-blue-600 font-semibold">
                    <i class="fas fa-plus-circle mr-1.5 sm:mr-2"></i>Total Pemasukan
                </span>
                <span class="font-black font-mono text-blue-700 text-sm sm:text-base">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-2.5 sm:py-3 border-b border-slate-100">
                <span class="text-xs sm:text-sm text-red-500 font-semibold">
                    <i class="fas fa-minus-circle mr-1.5 sm:mr-2"></i>Total Pengeluaran
                </span>
                <span class="font-black font-mono text-red-500 text-sm sm:text-base">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-3 sm:py-4 bg-slate-50 rounded-xl px-3 sm:px-4 mt-2">
                <span class="text-sm sm:text-base font-extrabold text-slate-800">Saldo Akhir Bulan</span>
                <span class="text-base sm:text-xl font-black font-mono {{ $saldoAkhir >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

</div>

{{-- ───────────────────────────────────────────────────────────── --}}
{{-- MODAL 1: INPUT SALDO AWAL --}}
{{-- ───────────────────────────────────────────────────────────── --}}
<div id="modalSaldoAwal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
    <div class="bg-white w-full max-w-md rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden transform transition-all p-4 sm:p-6 space-y-3 sm:space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base sm:text-lg font-bold text-slate-800"><i class="fas fa-coins text-blue-500 mr-2"></i>Input Saldo Awal</h3>
            <button type="button" onclick="closeModal('modalSaldoAwal')" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <form action="{{ route('kepala-bumdes.simpan-saldo-awal') }}" method="POST" class="space-y-3 sm:space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Bulan</label>
                    <select name="bulan" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium" required>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ date('m') == ($i+1) ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Tahun</label>
                    <select name="tahun" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium" required>
                        @for($y = date('Y')+1; $y >= date('Y')-2; $y--)
                            <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Nominal Saldo Awal (Rp)</label>
                <input type="text" name="saldo_awal" id="input_saldo_awal_format" placeholder="Contoh: 10.000.000" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 font-bold font-mono outline-none focus:border-blue-500" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" placeholder="Catatan saldo awal periode..." class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:border-blue-500">
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modalSaldoAwal')" class="flex-1 border border-slate-200 text-slate-500 rounded-xl py-2.5 text-sm font-bold bg-white hover:bg-slate-50 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl py-2.5 text-sm font-bold shadow transition">Simpan Saldo</button>
            </div>
        </form>
    </div>
</div>

{{-- ───────────────────────────────────────────────────────────── --}}
{{-- MODAL 2: CATAT REKAP PENGELUARAN (MINGGUAN / BULANAN) --}}
{{-- ───────────────────────────────────────────────────────────── --}}
<div id="modalPengeluaran" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden transform transition-all p-4 sm:p-6 space-y-3 sm:space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base sm:text-lg font-bold text-slate-800"><i class="fas fa-file-invoice-dollar text-red-500 mr-2"></i>Catat Pengeluaran Operasional</h3>
            <button type="button" onclick="closeModal('modalPengeluaran')" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>

        {{-- PEMBUKA TAG FORM --}}
        <form id="form_rekap_pengeluaran" action="{{ route('kepala-bumdes.simpan-pengeluaran') }}" method="POST" class="space-y-3 sm:space-y-4">
            @csrf

            {{-- Tipe Periode Toggle --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Tipe Periode Pengeluaran</label>
                <select name="tipe_periode" id="tipe_periode_select" onchange="toggleTipePeriode(this.value)" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-bold text-slate-700" required>
                    <option value="mingguan">Mingguan (Rincian Item)</option>
                    <option value="bulanan">Bulanan (Total Langsung)</option>
                </select>
            </div>

            {{-- Blok Form Dinamis Berdasarkan Tipe Periode --}}
            <div class="grid grid-cols-3 gap-2">
                <div id="div_minggu_ke">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Minggu Ke</label>
                    <select name="minggu_ke" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium">
                        <option value="1">Minggu 1</option>
                        <option value="2">Minggu 2</option>
                        <option value="3">Minggu 3</option>
                        <option value="4">Minggu 4</option>
                    </select>
                </div>
                <div id="div_bulan_pengeluaran" class="col-span-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Bulan</label>
                    <select name="bulan_pengeluaran" id="bulan_p" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ date('m') == ($i+1) ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                    <select name="bulan_pengeluaran_b" id="bulan_p_b" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium hidden">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ date('m') == ($i+1) ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Tahun</label>
                    <select name="tahun_pengeluaran" id="tahun_p" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium">
                        @for($y = date('Y'); $y >= date('Y')-2; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="tahun_pengeluaran_b" id="tahun_p_b" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium hidden">
                        @for($y = date('Y'); $y >= date('Y')-2; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Kategori</label>
                    <select name="kategori" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium" required>
                        <option value="Operasional">Operasional Kantor</option>
                        <option value="Gaji">Gaji / Insentif</option>
                        <option value="Pembelian">Pembelian Inventaris</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 font-medium outline-none focus:border-emerald-400" required>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Keterangan Ringkas</label>
                <input type="text" name="keterangan_pengeluaran" placeholder="Contoh: Pembelian ATK dan Konsumsi Pleno BUMDes" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:border-emerald-400" required>
            </div>

            {{-- BLOK DINAMIS: DAFTAR RINCIAN ITEM (HANYA UNTUK MINGGUAN) --}}
            <div id="wrapper_rincian_item" class="border-t pt-3 space-y-2">
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Rincian Item Pengeluaran</label>
                    <button type="button" onclick="tambahBarisItem()" class="text-xs bg-slate-100 text-slate-700 font-bold px-2.5 py-1 rounded-lg hover:bg-slate-200 transition">+ Tambah Item</button>
                </div>
                <div id="container_item_rows" class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                    <div class="flex items-center gap-2 item-row">
                        <input type="text" name="item_nama[]" placeholder="Nama Barang / Keperluan" class="w-2/3 text-xs border border-slate-200 rounded-lg px-3 py-2 outline-none item-field-required">
                        <input type="text" name="item_jumlah[]" placeholder="Harga (Rp)" class="w-1/3 text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono font-bold text-right input-harga-item item-field-required" oninput="hitungTotalOtomatis()">
                    </div>
                </div>
            </div>

            {{-- TOTAL PENGELUARAN YANG AKAN DI-SUBMIT --}}
            <div class="bg-red-50/60 p-4 rounded-2xl border border-red-100">
                <label class="text-xs font-bold text-red-500 uppercase tracking-wide block mb-1">Total Pengeluaran (Fix)</label>
                <input type="hidden" name="total_pengeluaran" id="total_pengeluaran_raw" value="0">
                <input type="text" id="total_pengeluaran_format" placeholder="Rp 0" class="w-full bg-transparent border-0 text-xl sm:text-2xl font-black font-mono text-red-600 p-0 focus:ring-0 outline-none" readonly required>
            </div>

            {{-- STRUKTUR UTAMA: TOMBOL AKSI HARUS BERADA DI DALAM TAG FORM --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modalPengeluaran')" class="flex-1 border border-slate-200 text-slate-500 rounded-xl py-2.5 text-sm font-bold bg-white hover:bg-slate-50 transition">Batal</button>
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white rounded-xl py-2.5 text-sm font-bold shadow transition">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// JavaScript Modal Control
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('flex');
    document.getElementById(id).classList.add('hidden');
}

// Format Rupiah Input Saat Diketik
document.getElementById('input_saldo_awal_format').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, "");
    this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
});

// Set default required saat pertama di-load
document.addEventListener('DOMContentLoaded', function() {
    toggleTipePeriode('mingguan');
});

// Kontrol Tipe Periode Dinamis (Mingguan vs Bulanan)
function toggleTipePeriode(tipe) {
    const isMingguan = (tipe === 'mingguan');

    document.getElementById('div_minggu_ke').classList.toggle('hidden', !isMingguan);
    document.getElementById('wrapper_rincian_item').classList.toggle('hidden', !isMingguan);

    document.getElementById('bulan_p').classList.toggle('hidden', !isMingguan);
    document.getElementById('bulan_p_b').classList.toggle('hidden', isMingguan);
    document.getElementById('tahun_p').classList.toggle('hidden', !isMingguan);
    document.getElementById('tahun_p_b').classList.toggle('hidden', isMingguan);

    const formatInput = document.getElementById('total_pengeluaran_format');
    const rawInput = document.getElementById('total_pengeluaran_raw');

    formatInput.value = '';
    rawInput.value = 0;

    const rincianFields = document.querySelectorAll('.item-field-required');

    if (!isMingguan) {
        formatInput.removeAttribute('readonly');
        formatInput.classList.remove('bg-transparent', 'p-0');
        formatInput.classList.add('bg-white', 'border', 'border-slate-200', 'px-3', 'py-2', 'rounded-xl', 'text-base');
        formatInput.placeholder = "Masukkan total pengeluaran...";

        rincianFields.forEach(field => {
            field.removeAttribute('required');
        });

        formatInput.addEventListener('input', handleBulananInput);
    } else {
        formatInput.setAttribute('readonly', true);
        formatInput.classList.add('bg-transparent', 'p-0');
        formatInput.classList.remove('bg-white', 'border', 'border-slate-200', 'px-3', 'py-2', 'rounded-xl', 'text-base');
        formatInput.placeholder = "Rp 0";

        rincianFields.forEach(field => {
            field.setAttribute('required', 'required');
        });

        formatInput.removeEventListener('input', handleBulananInput);
        hitungTotalOtomatis();
    }
}

function handleBulananInput() {
    let value = this.value.replace(/\D/g, "");
    document.getElementById('total_pengeluaran_raw').value = value || 0;
    this.value = value ? 'Rp ' + new Intl.NumberFormat('id-ID').format(value) : '';
}

function tambahBarisItem() {
    const container = document.getElementById('container_item_rows');
    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-2 item-row';

    const isMingguan = (document.getElementById('tipe_periode_select').value === 'mingguan');
    const reqAttr = isMingguan ? 'required="required"' : '';

    newRow.innerHTML = `
        <input type="text" name="item_nama[]" placeholder="Nama Barang / Keperluan" class="w-2/3 text-xs border border-slate-200 rounded-lg px-3 py-2 outline-none item-field-required" ${reqAttr}>
        <input type="text" name="item_jumlah[]" placeholder="Harga (Rp)" class="w-1/3 text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono font-bold text-right input-harga-item item-field-required" oninput="hitungTotalOtomatis()" ${reqAttr}>
        <button type="button" onclick="this.parentElement.remove(); hitungTotalOtomatis();" class="text-red-400 text-sm px-1 hover:text-red-600">&times;</button>
    `;
    container.appendChild(newRow);
    container.scrollTop = container.scrollHeight;
}

// Hitung Total Otomatis dari Rincian Item (Tipe Mingguan)
function hitungTotalOtomatis() {
    if (document.getElementById('tipe_periode_select').value !== 'mingguan') return;

    let total = 0;
    const rows = document.querySelectorAll('.input-harga-item');

    rows.forEach(input => {
        let val = input.value.replace(/\D/g, "");
        input.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
        total += parseInt(val || 0);
    });

    document.getElementById('total_pengeluaran_raw').value = total;
    document.getElementById('total_pengeluaran_format').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
}

function switchTab(tab) {
    document.getElementById('panel-pemasukan').classList.toggle('hidden', tab !== 'pemasukan');
    document.getElementById('panel-pengeluaran').classList.toggle('hidden', tab !== 'pengeluaran');

    const tabPemasukan = document.getElementById('tab-pemasukan');
    const tabPengeluaran = document.getElementById('tab-pengeluaran');

    if (tab === 'pemasukan') {
        tabPemasukan.className = 'flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-blue-600 border-b-2 border-blue-600 bg-blue-50/40 transition';
        tabPengeluaran.className = 'flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition';
    } else {
        tabPengeluaran.className = 'flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-red-500 border-b-2 border-red-500 bg-red-50/40 transition';
        tabPemasukan.className = 'flex-1 px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-bold text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition';
    }
}

// Grafik Arus Kas Harian
const arusLabels = {!! json_encode($labelHarian) !!};
const arusMasuk = {!! json_encode($dataMasukHarian) !!};
const arusKeluar = {!! json_encode($dataKeluarHarian) !!};

new Chart(document.getElementById('grafikArusKas'), {
    type: 'bar',
    data: {
        labels: arusLabels,
        datasets: [
            {
                label: 'Pemasukan',
                data: arusMasuk,
                backgroundColor: 'rgba(59,130,246,0.70)',
                borderRadius: 5,
                borderSkipped: false,
            },
            {
                label: 'Pengeluaran',
                data: arusKeluar,
                backgroundColor: 'rgba(239,68,68,0.65)',
                borderRadius: 5,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { weight: '700', size: 10 }, boxWidth: 12 } },
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
                    font: { size: 9 },
                    maxTicksLimit: 5
                },
                grid: { color: '#f1f5f9' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 9 } }
            }
        }
    }
});

// Grafik Donut Kategori
@if(count($kategoriPengeluaran) > 0)
new Chart(document.getElementById('grafikKategori'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($kategoriPengeluaran->pluck('kategori')) !!},
        datasets: [{
            data: {!! json_encode($kategoriPengeluaran->pluck('total')) !!},
            backgroundColor: ['#ef4444','#f59e0b','#8b5cf6','#3b82f6','#10b981','#f97316','#06b6d4'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
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