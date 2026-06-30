@include('theme.partials.kop-surat')

<div style="margin-top: 20px; font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b;">

    <h3 style="text-align:center; text-decoration: underline; margin-bottom: 4px;">
        LAPORAN BULANAN KAS MASUK BUMDes
    </h3>
    <p style="text-align:center; margin-top:0;">Tahun {{ $tahunAktif }}</p>

    <p>
        Dengan ini disampaikan rekapitulasi kas masuk BUMDes Putra Samudra Patimban
        untuk periode Januari s.d. Desember {{ $tahunAktif }}, sebagai berikut:
    </p>

    <table style="width:100%; border-collapse: collapse; margin: 15px 0;">
        <tr>
            <td style="padding:4px 0; width:55%;">Total Kas Masuk Tahun {{ $tahunAktif }}</td>
            <td style="padding:4px 0;">: <strong>Rp {{ number_format($totalKasTahun, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td style="padding:4px 0;">Rata-rata Kas Masuk per Bulan</td>
            <td style="padding:4px 0;">: <strong>Rp {{ number_format($rataRataPerBulan, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td style="padding:4px 0;">Bulan dengan Kas Masuk Tertinggi</td>
            <td style="padding:4px 0;">: <strong>{{ $bulanTerbaik['nama'] ?? '-' }} (Rp {{ number_format($bulanTerbaik['total'] ?? 0, 0, ',', '.') }})</strong></td>
        </tr>
        <tr>
            <td style="padding:4px 0;">Total Transaksi Tahun {{ $tahunAktif }}</td>
            <td style="padding:4px 0;">: <strong>{{ $totalTransaksiTahun }} transaksi</strong></td>
        </tr>
    </table>

    <table style="width:100%; border-collapse: collapse; margin-top: 10px;" border="1" cellpadding="6">
        <thead>
            <tr style="background:#f1f5f9;">
                <th style="text-align:left;">Bulan</th>
                <th style="text-align:right;">Total Kas Masuk</th>
                <th style="text-align:right;">Jumlah Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanBulanan as $l)
            <tr>
                <td>{{ $l['nama_bulan'] }}</td>
                <td style="text-align:right;">Rp {{ number_format($l['total_kas'], 0, ',', '.') }}</td>
                <td style="text-align:right;">{{ $l['jumlah_transaksi'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#f8fafc;">
                <td>TOTAL</td>
                <td style="text-align:right;">Rp {{ number_format($totalKasTahun, 0, ',', '.') }}</td>
                <td style="text-align:right;">{{ $totalTransaksiTahun }}</td>
            </tr>
        </tfoot>
    </table>

    @if(isset($perMitra) && count($perMitra) > 0)
    <h4 style="margin-top:25px;">Kontribusi Kas Masuk per Mitra</h4>
    <table style="width:100%; border-collapse: collapse;" border="1" cellpadding="6">
        <thead>
            <tr style="background:#f1f5f9;">
                <th style="text-align:left;">Nama Mitra</th>
                <th style="text-align:right;">Total Kas Masuk</th>
                <th style="text-align:right;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perMitra as $pm)
            <tr>
                <td>{{ $pm['nama'] }}</td>
                <td style="text-align:right;">Rp {{ number_format($pm['total_kas'], 0, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($pm['persen'] ?? 0, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <p style="margin-top:30px;">
        Demikian laporan ini dibuat untuk dipergunakan sebagaimana mestinya.
    </p>

    <div style="margin-top:50px; width:250px; margin-left:auto; text-align:center;">
        <p>Patimban, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p>Kepala BUMDes,</p>
        <div style="height:60px;"></div>
        <p style="border-top:1px solid #1e293b; padding-top:4px;"><strong>( ......................... )</strong></p>
    </div>

</div>