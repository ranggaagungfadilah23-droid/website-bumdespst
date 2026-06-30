@extends('theme.pdf')

@section('title', 'Laporan Bulanan Kas Masuk BUMDes - ' . $tahunAktif)

@section('styles')
<style>
    .judul       { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 3px; text-decoration: underline; letter-spacing: 2px; text-transform: uppercase; }
    .sub-judul   { text-align: center; font-size: 12px; margin-bottom: 20px; color: #444; }
    .info-table td { padding: 2px 6px; font-size: 12px; }
    .info-table td:first-child { width: 140px; color: #555; }
    .ringkasan   { width: 100%; border-collapse: collapse; margin: 16px 0; }
    .ringkasan td { border: 1px solid #cbd5e1; padding: 10px 14px; width: 33.33%; }
    .ringkasan .label { font-size: 9px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
    .ringkasan .nilai { font-size: 14px; font-weight: bold; margin-top: 4px; }
    table.data   { width: 100%; border-collapse: collapse; margin: 16px 0; }
    table.data thead tr { background: #1e3a5f; color: white; }
    table.data thead th { padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
    table.data thead th.right { text-align: right; }
    table.data tbody tr:nth-child(even) { background: #f8fafc; }
    table.data tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
    table.data tbody td.right { text-align: right; }
    table.data tfoot td { padding: 8px 10px; font-weight: bold; background: #f1f5f9; border-top: 2px solid #1e3a5f; font-size: 11px; }
    table.data tfoot td.right { text-align: right; color: #1d4ed8; }
    .ttd-wrap    { margin-top: 30px; width: 100%; }
    .ttd-box     { text-align: center; }
    .footer-note { font-size: 9px; font-style: italic; color: #555; margin-top: 20px; text-align: center; border-top: 1px solid #ccc; padding-top: 8px; }
</style>
@endsection

@section('content')

    <div class="judul">Laporan Bulanan Kas Masuk BUMDes</div>
    <div class="sub-judul">Periode: Tahun {{ $tahunAktif }}</div>

    {{-- INFO DOKUMEN --}}
    <table class="info-table" style="margin-bottom: 14px;">
        <tr><td>Tanggal Cetak</td><td>:</td><td>{{ now()->translatedFormat('d F Y') }}</td></tr>
        <tr><td>Dicetak Oleh</td><td>:</td><td>{{ auth()->user()->name }} (Kepala BUMDes)</td></tr>
        <tr><td>Total Transaksi</td><td>:</td><td>{{ $totalTransaksiTahun }} Transaksi</td></tr>
    </table>

    {{-- RINGKASAN --}}
    <table class="ringkasan">
        <tr>
            <td style="background: #eff6ff;">
                <div class="label" style="color: #3b82f6;">Total Kas Masuk {{ $tahunAktif }}</div>
                <div class="nilai" style="color: #1d4ed8;">Rp {{ number_format($totalKasTahun, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Rata-rata per Bulan</div>
                <div class="nilai">Rp {{ number_format($rataRataPerBulan, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Bulan Terbaik</div>
                <div class="nilai">{{ $bulanTerbaik['nama'] ?? '-' }}</div>
                <div style="font-size:10px; color:#64748b; margin-top:2px;">Rp {{ number_format($bulanTerbaik['total'] ?? 0, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- TABEL DETAIL BULANAN --}}
    <table class="data">
        <thead>
            <tr>
                <th style="text-align:left">No</th>
                <th style="text-align:left">Bulan</th>
                <th class="right">Total Kas Masuk</th>
                <th class="right">Jumlah Transaksi</th>
                <th style="text-align:center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanBulanan as $l)
            <tr>
                <td>{{ $l['nomor_bulan'] }}</td>
                <td>{{ $l['nama_bulan'] }}</td>
                <td class="right">Rp {{ number_format($l['total_kas'], 0, ',', '.') }}</td>
                <td class="right">{{ $l['jumlah_transaksi'] }}</td>
                <td style="text-align:center">{{ $l['total_kas'] > 0 ? 'Ada Data' : 'Nihil' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td class="right">Rp {{ number_format($totalKasTahun, 0, ',', '.') }}</td>
                <td class="right">{{ $totalTransaksiTahun }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- KONTRIBUSI PER MITRA --}}
    @if(isset($perMitra) && count($perMitra) > 0)
    <table class="data">
        <thead>
            <tr>
                <th style="text-align:left">No</th>
                <th style="text-align:left">Nama Mitra</th>
                <th class="right">Total Kas Masuk</th>
                <th class="right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perMitra as $i => $pm)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pm['nama'] }}</td>
                <td class="right">Rp {{ number_format($pm['total_kas'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($pm['persen'] ?? 0, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td class="right">Rp {{ number_format($perMitra->sum('total_kas'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- TANDA TANGAN --}}
    <table class="ttd-wrap">
        <tr>
            <td width="55%">&nbsp;</td>
            <td width="45%" style="text-align:center;" class="ttd-box">
                <p style="margin:0; font-size:13px; line-height:1.8;">
                    Patimban, {{ now()->translatedFormat('d F Y') }}<br>
                    Kepala BUMDes Putra Samudra Patimban,
                </p>
                <div style="margin: 8px 0;">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                         alt="QR Code TTD Digital"
                         style="width: 90px; height: 90px;">
                </div>
                <p style="margin:0; font-size:13px; line-height:1.8;">
                    <strong><u>IQBAL NUR AFRIZAL</u></strong><br>
                    Kepala BUMDes
                </p>
            </td>
        </tr>
    </table>

    {{-- FOOTER VERIFIKASI --}}
    <div class="footer-note">
        Dokumen ini diterbitkan secara digital oleh Sistem Informasi BUMDes Patimban &bull;
        Keaslian dokumen dapat diverifikasi dengan memindai QR Code di atas &bull;
        Diterbitkan pada {{ now()->format('d/m/Y H:i') }} WIB
    </div>

@endsection