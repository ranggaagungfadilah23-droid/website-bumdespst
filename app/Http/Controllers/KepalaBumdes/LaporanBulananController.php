<?php

namespace App\Http\Controllers\KepalaBumdes;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class LaporanBulananController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = $request->get('tahun', date('Y'));
        $data = $this->hitungLaporan($tahunAktif);

        return view('kepala-bumdes.laporan-bulanan', array_merge($data, [
            'tahunAktif' => $tahunAktif,
        ]));
    }

    public function cetakPdf(Request $request)
    {
        $tahunAktif = $request->get('tahun', date('Y'));
        $data = $this->hitungLaporan($tahunAktif);

        $namaKepala = "IQBAL NUR AFRIZAL";
        $jabatan    = "Kepala BUMDes Putra Samudra Patimban";
        $idKepala   = "BUMDES-ID: 9201.0101.2024";

        $isiQR = "=== VERIFIKASI DOKUMEN DIGITAL ===\n" .
                 "BUMDes Putra Samudra Patimban\n" .
                 "----------------------------------\n" .
                 "Disahkan Oleh  : " . $namaKepala . "\n" .
                 "Jabatan        : " . $jabatan . "\n" .
                 "ID BUMDes      : " . $idKepala . "\n" .
                 "----------------------------------\n" .
                 "Dokumen        : Laporan Bulanan Kas Masuk\n" .
                 "Periode        : Tahun " . $tahunAktif . "\n" .
                 "Total Kas      : Rp " . number_format($data['totalKasTahun'], 0, ',', '.') . "\n" .
                 "----------------------------------\n" .
                 "Tanggal        : " . now()->translatedFormat('d F Y') . "\n" .
                 "Pukul          : " . now()->format('H:i') . " WIB\n" .
                 "==================================";

        $qrCode   = new QrCode($isiQR);
        $writer   = new SvgWriter();
        $result   = $writer->write($qrCode);
        $qrBase64 = base64_encode($result->getString());

        $pdf = Pdf::loadView('kepala-bumdes.laporan-bulanan-pdf', array_merge($data, [
            'tahunAktif' => $tahunAktif,
            'qrCode'     => $qrBase64,
        ]))->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan-Bulanan-BUMDes-{$tahunAktif}.pdf");
    }

    private function hitungLaporan($tahunAktif)
    {
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

        // PENTING: mitra_id di tabel bagihasils menyimpan user_id (bukan mitras.id),
        // konsisten dengan BagihasilController & view admin.bagihasil
        $perMitraRaw = BagiHasil::selectRaw('mitra_id, SUM(nominal_bumdes) as total_kas, SUM(total_omzet) as total_omzet')
            ->whereYear('created_at', $tahunAktif)
            ->where('status', 'selesai')
            ->groupBy('mitra_id')
            ->get();

        $totalKasSemua = $perMitraRaw->sum('total_kas') ?: 1;

        $mitraMap = Mitra::whereIn('user_id', $perMitraRaw->pluck('mitra_id'))
            ->get()
            ->groupBy('user_id')
            ->map(fn($group) => $group->first()->nama_usaha);

        $perMitra = $perMitraRaw->map(function ($row) use ($totalKasSemua, $mitraMap) {
            return [
                'nama'      => $mitraMap->get($row->mitra_id, 'Mitra #' . $row->mitra_id),
                'total_kas' => (float) $row->total_kas,
                'omzet'     => (float) $row->total_omzet,
                'persen'    => round($row->total_kas / $totalKasSemua * 100, 1),
            ];
        })->sortByDesc('total_kas')->values();

        return compact(
            'laporanBulanan', 'totalKasTahun', 'totalTransaksiTahun',
            'rataRataPerBulan', 'bulanTerbaik', 'labelGrafik', 'dataKasGrafik', 'perMitra',
        );
    }
}