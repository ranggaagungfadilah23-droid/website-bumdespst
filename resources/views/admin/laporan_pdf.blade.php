@extends('theme.pdf')

@section('title', 'Laporan Keuangan BUMDes - ' . $bulanAktif)

@section('content')

    <div class="judul">Laporan Keuangan Bagi Hasil Mitra</div>
    <div class="sub-judul">Periode: {{ $bulanAktif }}</div>

    <table class="info-table" style="margin-bottom:14px;">
        <tr><td>Tanggal Cetak</td><td>:</td><td>{{ now()->translatedFormat('d F Y') }}</td></tr>
        <tr><td>Dicetak Oleh</td><td>:</td><td>{{ auth()->user()->name }} (Admin)</td></tr>
        <tr><td>Jumlah Mitra Aktif</td><td>:</td><td>{{ $totalMitra }} Mitra</td></tr>
    </table>

    <table class="ringkasan">
        <tr>
            <td style="width:50%;">
                <div class="label">Total Omzet Mitra</div>
                <div class="nilai">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</div>
            </td>
            <td style="width:50%; background:#eff6ff;">
                <div class="label" style="color:#3b82f6;">Kas Masuk BUMDes</div>
                <div class="nilai" style="color:#1d4ed8;">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mitra</th>
                <th class="right">Total Omzet</th>
                <th class="right">% BUMDes</th>
                <th class="right">Kas Masuk BUMDes</th>
                <th class="right">Bagian Mitra</th>
                <th style="text-align:center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perMitra as $i => $pm)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pm['nama'] }}</td>
                <td class="right">Rp {{ number_format($pm['omzet'], 0, ',', '.') }}</td>
                <td class="right">{{ $pm['persen_bumdes'] ?? '-' }}%</td>
                <td class="right">Rp {{ number_format($pm['kas_bumdes'] ?? 0, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($pm['omzet'] - ($pm['kas_bumdes'] ?? 0), 0, ',', '.') }}</td>
                <td style="text-align:center">Selesai</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td class="right">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</td>
                <td></td>
                <td class="right">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($totalBagiHasil - $totalKasMasuk, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <table style="margin-top:30px; width:100%;">
        <tr>
            <td width="55%">&nbsp;</td>
            <td width="45%" style="text-align:center;">
                <p style="margin:0; font-size:13px;">Patimban, {{ now()->translatedFormat('d F Y') }}<br>Kepala BUMDes Putra Samudra Patimban,</p>
                <div class="ttd-ruang"></div>
                <p style="margin:0; font-size:13px;"><strong><u>IQBAL NUR AFRIZAL</u></strong><br>Kepala BUMDes</p>
            </td>
        </tr>
    </table>

@endsection