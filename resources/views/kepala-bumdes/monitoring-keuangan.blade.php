@extends('theme.default')
@section('title', 'Monitoring Keuangan - BUMDes Patimban')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════
   PRINT STYLES — hanya aktif saat window.print()
═══════════════════════════════════════════════════ */
@media print {

    /* Sembunyikan semua elemen UI yang tidak perlu */
    nav, header, aside, footer,
    .no-print,
    #modalSaldoAwal,
    #modalPengeluaran,
    [id^="tab-"],
    #panel-pengeluaran,
    .btn-aksi,
    form[method="GET"],
    canvas {
        display: none !important;
    }

    /* Reset body / background */
    body, html {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: 'Times New Roman', Times, serif;
        color: #000 !important;
        font-size: 11pt;
    }

    /* Wrapper utama full width */
    .print-wrapper {
        display: block !important;
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
    }

    /* Sembunyikan wrapper web */
    .web-only {
        display: none !important;
    }

    /* ── KOP SURAT ─────────────────────────────── */
    .print-kop {
        display: block !important;
        text-align: center;
        border-bottom: 3px double #000;
        padding-bottom: 8pt;
        margin-bottom: 10pt;
    }
    .print-kop h1 {
        font-size: 14pt;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0 0 2pt 0;
        letter-spacing: 1px;
    }
    .print-kop p {
        font-size: 9pt;
        margin: 1pt 0;
    }

    /* ── JUDUL LAPORAN ────────────────────────── */
    .print-judul {
        display: block !important;
        text-align: center;
        margin: 12pt 0 10pt 0;
    }
    .print-judul h2 {
        font-size: 13pt;
        font-weight: bold;
        text-transform: uppercase;
        text-decoration: underline;
        margin: 0 0 3pt 0;
    }
    .print-judul p {
        font-size: 10pt;
        margin: 0;
    }

    /* ── TABEL RINGKASAN ──────────────────────── */
    .print-ringkasan {
        display: table !important;
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12pt;
        font-size: 10pt;
    }
    .print-ringkasan td {
        border: 1px solid #333;
        padding: 5pt 8pt;
    }
    .print-ringkasan .label {
        font-weight: bold;
        width: 40%;
        background: #f0f0f0;
    }
    .print-ringkasan .value {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        text-align: right;
    }
    .print-ringkasan .value.plus { color: #000; }
    .print-ringkasan .value.minus { color: #000; }

    /* ── TABEL TRANSAKSI ──────────────────────── */
    .print-tabel-title {
        display: block !important;
        font-size: 11pt;
        font-weight: bold;
        margin: 12pt 0 4pt 0;
        text-transform: uppercase;
        border-bottom: 1px solid #000;
        padding-bottom: 2pt;
    }
    .print-tabel {
        display: table !important;
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt;
        margin-bottom: 12pt;
    }
    .print-tabel thead tr {
        background: #ddd !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .print-tabel th {
        border: 1px solid #333;
        padding: 4pt 6pt;
        font-weight: bold;
        text-align: left;
    }
    .print-tabel td {
        border: 1px solid #aaa;
        padding: 4pt 6pt;
    }
    .print-tabel .nominal {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    .print-tabel tfoot td {
        border: 1px solid #333;
        padding: 5pt 6pt;
        font-weight: bold;
        background: #eee !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .print-tabel tfoot .nominal {
        text-align: right;
        font-family: 'Courier New', monospace;
    }

    /* ── TANDA TANGAN ─────────────────────────── */
    .print-ttd {
        display: table !important;
        width: 100%;
        margin-top: 24pt;
        font-size: 10pt;
    }
    .print-ttd td {
        width: 50%;
        text-align: center;
        padding: 4pt 20pt;
        vertical-align: top;
    }
    .print-ttd .garis-ttd {
        border-top: 1px solid #000;
        margin-top: 40pt;
        padding-top: 3pt;
    }

    /* ── FOOTER PRINT ─────────────────────────── */
    .print-footer {
        display: block !important;
        text-align: center;
        font-size: 8pt;
        color: #555;
        margin-top: 16pt;
        border-top: 1px solid #ccc;
        padding-top: 6pt;
    }

    /* Page settings */
    @page {
        size: A4 portrait;
        margin: 1.5cm 2cm 2cm 2cm;
    }

    /* Hindari page break di dalam tabel */
    .print-tabel tr {
        page-break-inside: avoid;
    }
}

/* ── Sembunyikan elemen print di tampilan web ── */
.print-kop,
.print-judul,
.print-ringkasan,
.print-tabel,
.print-tabel-title,
.print-ttd,
.print-footer {
    display: none;
}
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════════════════════════════
     ELEMEN PRINT — hanya muncul saat @media print
════════════════════════════════════════════════════════════ --}}

{{-- Kop Surat --}}
<div class="print-kop">
    <h1>BUMDes Putra Samudra Patimban</h1>
    <p>Desa Patimban, Kecamatan Pusakanagara, Kabupaten Subang, Jawa Barat</p>
    <p>Website: www.bumdes-putrasamudrapatimban.my.id</p>
</div>

{{-- Judul Laporan --}}
<div class="print-judul">
    <h2>Laporan Monitoring Keuangan Operasional</h2>
    <p>Periode: {{ $namaBulan }} {{ $tahunAktif }}</p>
</div>

{{-- Tabel Ringkasan Arus Kas --}}
<table class="print-ringkasan">
    <tr>
        <td class="label">Saldo Awal Bulan</td>
        <td class="value plus">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="label">Total Pemasukan ({{ $jumlahTransaksiMasuk }} transaksi)</td>
        <td class="value plus">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="label">Total Pengeluaran ({{ $jumlahTransaksiKeluar }} transaksi)</td>
        <td class="value minus">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="label">Saldo Akhir Bulan</td>
        <td class="value plus">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
    </tr>
</table>

{{-- Tabel Detail Pemasukan --}}
<div class="print-tabel-title">Rincian Pemasukan</div>
<table class="print-tabel">
    <thead>
        <tr>
            <th style="width:5%">No.</th>
            <th style="width:18%">Tanggal</th>
            <th>Keterangan</th>
            <th style="width:18%">Sumber</th>
            <th style="width:20%">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dataPemasukan as $i => $item)
        <tr>
            <td style="text-align:center">{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY') }}</td>
            <td>{{ $item->keterangan }}</td>
            <td style="text-align:center">{{ $item->sumber ?? 'Bagi Hasil Mitra' }}</td>
            <td class="nominal">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;font-style:italic;color:#555">
                Tidak ada transaksi pemasukan pada periode ini
            </td>
        </tr>
        @endforelse
    </tbody>
    @if(count($dataPemasukan) > 0)
    <tfoot>
        <tr>
            <td colspan="4" class="label">TOTAL PEMASUKAN</td>
            <td class="nominal">{{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Tabel Detail Pengeluaran --}}
<div class="print-tabel-title">Rincian Pengeluaran</div>
<table class="print-tabel">
    <thead>
        <tr>
            <th style="width:5%">No.</th>
            <th style="width:18%">Tanggal</th>
            <th>Keterangan</th>
            <th style="width:18%">Kategori</th>
            <th style="width:20%">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dataPengeluaran as $i => $item)
        <tr>
            <td style="text-align:center">{{ $i + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM YYYY') }}</td>
            <td>{{ $item->keterangan }}</td>
            <td style="text-align:center">{{ $item->kategori ?? 'Lain-lain' }}</td>
            <td class="nominal">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;font-style:italic;color:#555">
                Tidak ada transaksi pengeluaran pada periode ini
            </td>
        </tr>
        @endforelse
    </tbody>
    @if(count($dataPengeluaran) > 0)
    <tfoot>
        <tr>
            <td colspan="4" class="label">TOTAL PENGELUARAN</td>
            <td class="nominal">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Tanda Tangan --}}
<table class="print-ttd">
    <tr>
        <td>
            <p>Mengetahui,</p>
            <p><strong>Kepala Desa Patimban</strong></p>
            <div class="garis-ttd">
                <p>( _________________________ )</p>
            </div>
        </td>
        <td>
            <p>Patimban, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
            <p><strong>Kepala BUMDes Putra Samudra Patimban</strong></p>
            <div class="garis-ttd">
                <p>( _________________________ )</p>
            </div>
        </td>
    </tr>
</table>

{{-- Footer Print --}}
<div class="print-footer">
    Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB &nbsp;|&nbsp;
    &copy; {{ date('Y') }} BUMDes Putra Samudra Patimban. Seluruh hak cipta dilindungi.
</div>


{{-- ════════════════════════════════════════════════════════════
     TAMPILAN WEB (sama seperti sebelumnya)
════════════════════════════════════════════════════════════ --}}
<div class="min-h-screen bg-slate-50 p-6 md:p-10 space-y-8 web-only">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">BUMDes — Keuangan Operasional</p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 leading-tight">Monitoring Keuangan</h1>
            <p class="text-slate-400 text-sm mt-1">Pantau arus kas pemasukan & pengeluaran operasional BUMDes</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Tombol Aksi --}}
            <button type="button" onclick="openModal('modalSaldoAwal')" class="btn-aksi inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow transition active:scale-95">
                <i class="fas fa-coins"></i> Input Saldo Awal
            </button>
            <button type="button" onclick="openModal('modalPengeluaran')" class="btn-aksi inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow transition active:scale-95">
                <i class="fas fa-file-invoice-dollar"></i> Catat Pengeluaran
            </button>

            {{-- Tombol Cetak — BARU --}}
            <button type="button" onclick="window.print()"
                class="btn-aksi inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold px-4 py-2.5 rounded-xl shadow transition active:scale-95">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>

            {{-- Filter Bulan & Tahun --}}
            <form method="GET" action="" class="flex items-center gap-2">
                <select name="bulan" onchange="this.form.submit()"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                        <option value="{{ $i+1 }}" {{ $bulanAktif == ($i+1) ? 'selected' : '' }}>{{ $bln }}</option>
                    @endforeach
                </select>
                <select name="tahun" onchange="this.form.submit()"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $tahunAktif == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="lg:col-span-1 bg-gradient-to-br {{ $saldoAkhir >= 0 ? 'from-emerald-600 to-emerald-800' : 'from-red-500 to-red-700' }} text-white p-6 rounded-2xl shadow-lg {{ $saldoAkhir >= 0 ? 'shadow-emerald-500/20' : 'shadow-red-500/20' }}">
            <div class="flex items-center justify-between mb-4">
                <p class="text-white/80 text-xs font-bold uppercase tracking-wider">Saldo Akhir</p>
                <div class="bg-white/20 p-2 rounded-xl"><i class="fas fa-wallet text-white text-sm"></i></div>
            </div>
            <h3 class="text-2xl font-black font-mono">Rp {{ number_format(abs($saldoAkhir), 0, ',', '.') }}</h3>
            <p class="text-white/70 text-xs mt-2">{{ $saldoAkhir >= 0 ? 'Surplus bulan ini' : 'Defisit bulan ini' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Pemasukan</p>
                <div class="bg-blue-50 p-2 rounded-xl"><i class="fas fa-arrow-down text-blue-500 text-sm"></i></div>
            </div>
            <h3 class="text-2xl font-black font-mono text-slate-800">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-xs mt-2">{{ $jumlahTransaksiMasuk }} transaksi masuk</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Pengeluaran</p>
                <div class="bg-red-50 p-2 rounded-xl"><i class="fas fa-arrow-up text-red-400 text-sm"></i></div>
            </div>
            <h3 class="text-2xl font-black font-mono text-slate-800">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-xs mt-2">{{ $jumlahTransaksiKeluar }} transaksi keluar</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Saldo Awal</p>
                <div class="bg-slate-100 p-2 rounded-xl"><i class="fas fa-coins text-slate-400 text-sm"></i></div>
            </div>
            <h3 class="text-2xl font-black font-mono text-slate-800">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</h3>
            <p class="text-slate-400 text-xs mt-2">Per awal {{ $namaBulan }}</p>
        </div>
    </div>

    {{-- GRAFIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-base font-bold text-slate-700 mb-1">Arus Kas Harian</h3>
            <p class="text-slate-400 text-xs mb-5">Pemasukan vs pengeluaran — {{ $namaBulan }} {{ $tahunAktif }}</p>
            <canvas id="grafikArusKas" height="110"></canvas>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h3 class="text-base font-bold text-slate-700 mb-1">Pengeluaran per Kategori</h3>
            <p class="text-slate-400 text-xs mb-5">{{ $namaBulan }} {{ $tahunAktif }}</p>
            @if(count($kategoriPengeluaran) > 0)
            <canvas id="grafikKategori" class="mb-4"></canvas>
            <div class="space-y-2 mt-4">
                @foreach($kategoriPengeluaran as $i => $kat)
                @php $warna = ['#ef4444','#f59e0b','#8b5cf6','#3b82f6','#10b981','#f97316','#06b6d4']; @endphp
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $warna[$i % count($warna)] }}"></span>
                        <span class="text-slate-600 font-medium">{{ $kat['kategori'] }}</span>
                    </div>
                    <span class="font-bold text-slate-700">Rp {{ number_format($kat['total'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-10 text-slate-300">
                <i class="fas fa-chart-pie text-4xl mb-2 block"></i>
                <p class="text-xs">Belum ada data</p>
            </div>
            @endif
        </div>
    </div>

    {{-- TABS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex border-b border-slate-100">
            <button onclick="switchTab('pemasukan')" id="tab-pemasukan"
                class="flex-1 px-6 py-4 text-sm font-bold text-blue-600 border-b-2 border-blue-600 bg-blue-50/40 transition">
                <i class="fas fa-arrow-circle-down mr-2"></i>Pemasukan
                <span class="ml-2 bg-blue-100 text-blue-700 text-xs font-black px-2 py-0.5 rounded-full">{{ $jumlahTransaksiMasuk }}</span>
            </button>
            <button onclick="switchTab('pengeluaran')" id="tab-pengeluaran"
                class="flex-1 px-6 py-4 text-sm font-bold text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition">
                <i class="fas fa-arrow-circle-up mr-2"></i>Pengeluaran
                <span class="ml-2 bg-slate-100 text-slate-500 text-xs font-black px-2 py-0.5 rounded-full">{{ $jumlahTransaksiKeluar }}</span>
            </button>
        </div>
        <div id="panel-pemasukan">
            <div class="overflow-x-auto">
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
        </div>
        <div id="panel-pengeluaran" class="hidden">
            <div class="overflow-x-auto">
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
                                    $catColors = ['Operasional'=>'bg-amber-50 text-amber-600','Gaji'=>'bg-purple-50 text-purple-600','Pembelian'=>'bg-blue-50 text-blue-600','Lain-lain'=>'bg-slate-100 text-slate-500'];
                                    $cat = $item->kategori ?? 'Lain-lain';
                                    $catClass = $catColors[$cat] ?? 'bg-slate-100 text-slate-500';
                                @endphp
                                <span class="{{ $catClass }} text-xs font-bold px-2.5 py-1 rounded-full">{{ $cat }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-black font-mono text-red-500">
                                - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <form action="{{ route('kepala-bumdes.hapus-pengeluaran', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf @method('DELETE')
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
        </div>
    </div>

    {{-- RINGKASAN ARUS KAS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-base font-bold text-slate-700 mb-5">Ringkasan Arus Kas — {{ $namaBulan }} {{ $tahunAktif }}</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-500 font-medium">Saldo Awal Bulan</span>
                <span class="font-black font-mono text-slate-700">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-blue-600 font-semibold"><i class="fas fa-plus-circle mr-2"></i>Total Pemasukan</span>
                <span class="font-black font-mono text-blue-700">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-red-500 font-semibold"><i class="fas fa-minus-circle mr-2"></i>Total Pengeluaran</span>
                <span class="font-black font-mono text-red-500">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-4 bg-slate-50 rounded-xl px-4 mt-2">
                <span class="text-base font-extrabold text-slate-800">Saldo Akhir Bulan</span>
                <span class="text-xl font-black font-mono {{ $saldoAkhir >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

</div>{{-- end .web-only --}}


{{-- ════ MODAL SALDO AWAL ════ --}}
<div id="modalSaldoAwal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-xl overflow-hidden transform transition-all p-6 space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-coins text-blue-500 mr-2"></i>Input Saldo Awal</h3>
            <button type="button" onclick="closeModal('modalSaldoAwal')" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <form action="{{ route('kepala-bumdes.simpan-saldo-awal') }}" method="POST" class="space-y-4">
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

{{-- ════ MODAL PENGELUARAN ════ --}}
<div id="modalPengeluaran" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-xl overflow-hidden transform transition-all p-6 space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-file-invoice-dollar text-red-500 mr-2"></i>Catat Pengeluaran Operasional</h3>
            <button type="button" onclick="closeModal('modalPengeluaran')" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <form id="form_rekap_pengeluaran" action="{{ route('kepala-bumdes.simpan-pengeluaran') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Tipe Periode Pengeluaran</label>
                <select name="tipe_periode" id="tipe_periode_select" onchange="toggleTipePeriode(this.value)" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-bold text-slate-700" required>
                    <option value="mingguan">Mingguan (Rincian Item)</option>
                    <option value="bulanan">Bulanan (Total Langsung)</option>
                </select>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div id="div_minggu_ke">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Minggu Ke</label>
                    <select name="minggu_ke" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium">
                        <option value="1">Minggu 1</option><option value="2">Minggu 2</option>
                        <option value="3">Minggu 3</option><option value="4">Minggu 4</option>
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
                        @for($y = date('Y'); $y >= date('Y')-2; $y--)<option value="{{ $y }}">{{ $y }}</option>@endfor
                    </select>
                    <select name="tahun_pengeluaran_b" id="tahun_p_b" class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white font-medium hidden">
                        @for($y = date('Y'); $y >= date('Y')-2; $y--)<option value="{{ $y }}">{{ $y }}</option>@endfor
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
            <div class="bg-red-50/60 p-4 rounded-2xl border border-red-100">
                <label class="text-xs font-bold text-red-500 uppercase tracking-wide block mb-1">Total Pengeluaran (Fix)</label>
                <input type="hidden" name="total_pengeluaran" id="total_pengeluaran_raw" value="0">
                <input type="text" id="total_pengeluaran_format" placeholder="Rp 0" class="w-full bg-transparent border-0 text-2xl font-black font-mono text-red-600 p-0 focus:ring-0 outline-none" readonly>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modalPengeluaran')" class="flex-1 border border-slate-200 text-slate-500 rounded-xl py-2.5 text-sm font-bold bg-white hover:bg-slate-50 transition">Batal</button>
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white rounded-xl py-2.5 text-sm font-bold shadow transition">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.getElementById(id).classList.add('flex'); }
function closeModal(id) { document.getElementById(id).classList.remove('flex'); document.getElementById(id).classList.add('hidden'); }

document.getElementById('input_saldo_awal_format').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, "");
    this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
});

document.addEventListener('DOMContentLoaded', function() { toggleTipePeriode('mingguan'); });

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
    formatInput.value = ''; rawInput.value = 0;
    const rincianFields = document.querySelectorAll('.item-field-required');
    if (!isMingguan) {
        formatInput.removeAttribute('readonly');
        formatInput.classList.remove('bg-transparent','p-0');
        formatInput.classList.add('bg-white','border','border-slate-200','px-3','py-2','rounded-xl','text-base');
        formatInput.placeholder = "Masukkan total pengeluaran...";
        rincianFields.forEach(f => f.removeAttribute('required'));
        formatInput.addEventListener('input', handleBulananInput);
    } else {
        formatInput.setAttribute('readonly', true);
        formatInput.classList.add('bg-transparent','p-0');
        formatInput.classList.remove('bg-white','border','border-slate-200','px-3','py-2','rounded-xl','text-base');
        formatInput.placeholder = "Rp 0";
        rincianFields.forEach(f => f.setAttribute('required','required'));
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
    const isMingguan = (document.getElementById('tipe_periode_select').value === 'mingguan');
    const reqAttr = isMingguan ? 'required="required"' : '';
    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-2 item-row';
    newRow.innerHTML = `
        <input type="text" name="item_nama[]" placeholder="Nama Barang / Keperluan" class="w-2/3 text-xs border border-slate-200 rounded-lg px-3 py-2 outline-none item-field-required" ${reqAttr}>
        <input type="text" name="item_jumlah[]" placeholder="Harga (Rp)" class="w-1/3 text-xs border border-slate-200 rounded-lg px-3 py-2 font-mono font-bold text-right input-harga-item item-field-required" oninput="hitungTotalOtomatis()" ${reqAttr}>
        <button type="button" onclick="this.parentElement.remove(); hitungTotalOtomatis();" class="text-red-400 text-sm px-1 hover:text-red-600">&times;</button>
    `;
    container.appendChild(newRow);
    container.scrollTop = container.scrollHeight;
}

function hitungTotalOtomatis() {
    if (document.getElementById('tipe_periode_select').value !== 'mingguan') return;
    let total = 0;
    document.querySelectorAll('.input-harga-item').forEach(input => {
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
    const tp = document.getElementById('tab-pemasukan');
    const tk = document.getElementById('tab-pengeluaran');
    if (tab === 'pemasukan') {
        tp.className = 'flex-1 px-6 py-4 text-sm font-bold text-blue-600 border-b-2 border-blue-600 bg-blue-50/40 transition';
        tk.className = 'flex-1 px-6 py-4 text-sm font-bold text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition';
    } else {
        tk.className = 'flex-1 px-6 py-4 text-sm font-bold text-red-500 border-b-2 border-red-500 bg-red-50/40 transition';
        tp.className = 'flex-1 px-6 py-4 text-sm font-bold text-slate-400 border-b-2 border-transparent hover:text-slate-600 transition';
    }
}

// Charts
const arusLabels = {!! json_encode($labelHarian) !!};
const arusMasuk  = {!! json_encode($dataMasukHarian) !!};
const arusKeluar = {!! json_encode($dataKeluarHarian) !!};

new Chart(document.getElementById('grafikArusKas'), {
    type: 'bar',
    data: {
        labels: arusLabels,
        datasets: [
            { label: 'Pemasukan',   data: arusMasuk,  backgroundColor: 'rgba(59,130,246,0.70)', borderRadius: 5, borderSkipped: false },
            { label: 'Pengeluaran', data: arusKeluar, backgroundColor: 'rgba(239,68,68,0.65)',  borderRadius: 5, borderSkipped: false }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top', labels: { font: { weight: '700', size: 11 } } },
            tooltip: { callbacks: { label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val), font: { size: 10 } }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});

@if(count($kategoriPengeluaran) > 0)
new Chart(document.getElementById('grafikKategori'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($kategoriPengeluaran->pluck('kategori')) !!},
        datasets: [{
            data: {!! json_encode($kategoriPengeluaran->pluck('total')) !!},
            backgroundColor: ['#ef4444','#f59e0b','#8b5cf6','#3b82f6','#10b981','#f97316','#06b6d4'],
            borderWidth: 0, hoverOffset: 6,
        }]
    },
    options: { responsive: true, cutout: '68%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } } } }
});
@endif
</script>
@endsection