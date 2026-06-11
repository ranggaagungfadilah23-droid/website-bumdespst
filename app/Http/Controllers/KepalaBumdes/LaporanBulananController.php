<?php

namespace App\Http\Controllers\KepalaBumdes;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use Illuminate\Http\Request;

class LaporanBulananController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = $request->get('tahun', date('Y'));

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $labelGrafik = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $kasPerBulan = BagiHasil::selectRaw('MONTH(created_at) as bulan, SUM(nominal_bumdes) as total_kas, COUNT(*) as jml')
            ->whereYear('created_at', $tahunAktif)
            ->where('status', 'selesai')
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $maxKas = $kasPerBulan->max('total_kas') ?: 1;

        $laporanBulanan = collect(range(1, 12))->map(function ($b) use ($kasPerBulan, $namaBulanList, $maxKas) {
            $kas   = $kasPerBulan->get($b);
            $total = $kas ? (float) $kas->total_kas : 0;
            return [
                'nomor_bulan'      => $b,
                'nama_bulan'       => $namaBulanList[$b],
                'total_kas'        => $total,
                'jumlah_transaksi' => $kas ? (int) $kas->jml : 0,
                'persen_dari_max'  => round($total / $maxKas * 100),
            ];
        });

        $totalKasTahun       = $laporanBulanan->sum('total_kas');
        $totalTransaksiTahun = $laporanBulanan->sum('jumlah_transaksi');

        $bulanAdaData     = $laporanBulanan->where('total_kas', '>', 0);
        $rataRataPerBulan = $bulanAdaData->count() > 0 ? $bulanAdaData->avg('total_kas') : 0;
        $bulanTerbaik     = $laporanBulanan->sortByDesc('total_kas')->first();
        $dataKasGrafik    = $laporanBulanan->pluck('total_kas')->values();

        $perMitraRaw = BagiHasil::selectRaw('mitra_id, SUM(nominal_bumdes) as total_kas, SUM(total_omzet) as total_omzet')
            ->whereYear('created_at', $tahunAktif)
            ->where('status', 'selesai')
            ->groupBy('mitra_id')
            ->with('mitra:id,nama_usaha')  // ← fix: ganti 'name' → 'nama_usaha'
            ->get();

        $totalKasSemua = $perMitraRaw->sum('total_kas') ?: 1;

        $perMitra = $perMitraRaw->map(fn($row) => [
            'nama'      => $row->mitra->nama_usaha ?? 'Mitra #' . $row->mitra_id,  // ← fix
            'total_kas' => (float) $row->total_kas,
            'omzet'     => (float) $row->total_omzet,
            'persen'    => round($row->total_kas / $totalKasSemua * 100, 1),
        ])->sortByDesc('total_kas')->values();

        return view('kepala-bumdes.laporan-bulanan', compact(
            'tahunAktif', 'laporanBulanan', 'totalKasTahun', 'totalTransaksiTahun',
            'rataRataPerBulan', 'bulanTerbaik', 'labelGrafik', 'dataKasGrafik', 'perMitra',
        ));
    }
}