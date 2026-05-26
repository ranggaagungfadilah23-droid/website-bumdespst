<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        h2, h4 { margin: 4px 0; }
    </style>
</head>
<body>

    <h2>Laporan Rekapitulasi Penjualan</h2>
    <h4>Mitra: {{ $mitra->nama ?? $mitra->user->name ?? '-' }}</h4>
    <h4>Periode: {{ ucfirst($periode) }}</h4>
    <hr>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Total Omzet</th>
                <th>% Mitra</th>
                <th>Pendapatan Mitra</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotalOmzet = 0; $grandTotalPendapatan = 0; @endphp
            @forelse ($transaksi as $item)
            @php
                $omzet      = $item->total_omzet ?? 0;       // ✅ kolom di bagihasils
                $pendapatan = $item->nominal_mitra ?? 0;      // ✅ kolom di bagihasils
                $persen     = $item->persen_mitra ?? 0;       // ✅ kolom di bagihasils
                $grandTotalOmzet      += $omzet;
                $grandTotalPendapatan += $pendapatan;
            @endphp
            <tr>
                <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d M Y') : '-' }}</td>
                <td class="text-right">Rp {{ number_format($omzet, 0, ',', '.') }}</td>
                <td class="text-right">{{ $persen }}%</td>
                <td class="text-right">Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($grandTotalOmzet, 0, ',', '.') }}</strong></td>
                <td></td>
                <td class="text-right"><strong>Rp {{ number_format($grandTotalPendapatan, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
