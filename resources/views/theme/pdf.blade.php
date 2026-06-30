<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Laporan BUMDes')</title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: "Times New Roman", Times, serif; font-size: 13px; line-height: 1.8; margin: 0; padding: 35px 45px; color: #000; }
        .border-double { border: 4px double #000; padding: 25px 30px; min-height: 92%; }

        /* KOP */
        .kop-surat { display: table; width: 100%; padding-bottom: 12px; margin-bottom: 5px; }
        .kop-logo { display: table-cell; vertical-align: middle; width: 95px; }
        .kop-logo img { width: 85px; height: 85px; }
        .kop-teks { display: table-cell; vertical-align: middle; text-align: center; padding-right: 95px; }
        .kop-teks .instansi { font-size: 13px; font-weight: normal; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .kop-teks .nama-bumdes { font-size: 22px; font-weight: bold; text-transform: uppercase; margin: 2px 0; letter-spacing: 1px; }
        .kop-teks .alamat { font-size: 11px; margin: 3px 0 0 0; line-height: 1.5; }
        .garis-kop { border: none; border-top: 4px solid #000; margin: 0 0 2px 0; }
        .garis-kop-tipis { border: none; border-top: 1px solid #000; margin: 0 0 20px 0; }

        /* KONTEN UMUM (dipakai semua laporan) */
        .judul { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 3px; text-decoration: underline; letter-spacing: 2px; text-transform: uppercase; }
        .sub-judul { text-align: center; font-size: 12px; margin-bottom: 20px; color: #444; }
        .info-table td { padding: 2px 6px; font-size: 12px; }
        .info-table td:first-child { width: 140px; color: #555; }
        .ringkasan { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .ringkasan td { border: 1px solid #cbd5e1; padding: 10px 14px; }
        .ringkasan .label { font-size: 9px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }
        .ringkasan .nilai { font-size: 14px; font-weight: bold; margin-top: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table.data thead tr { background: #1e3a5f; color: white; }
        table.data thead th { padding: 8px 10px; font-size: 10px; text-transform: uppercase; text-align: left; }
        table.data thead th.right { text-align: right; }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        table.data tbody td.right { text-align: right; }
        table.data tfoot td { padding: 8px 10px; font-weight: bold; background: #f1f5f9; border-top: 2px solid #1e3a5f; font-size: 11px; }
        table.data tfoot td.right { text-align: right; color: #1d4ed8; }
        .ttd-ruang { height: 55px; }
        .footer-note { font-size: 9px; font-style: italic; color: #555; margin-top: 30px; text-align: center; border-top: 1px solid #ccc; padding-top: 8px; }

        @yield('styles')
    </style>
</head>
<body>
<div class="border-double">

    @include('theme.partials.kop-surat')

    @yield('content')

    <div class="footer-note">
        Dokumen ini diterbitkan secara digital oleh Sistem Informasi BUMDes Patimban &bull;
        Diterbitkan pada {{ now()->format('d/m/Y H:i') }} WIB
    </div>

</div>
</body>
</html>