<?php
namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\BagiHasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private function getQuery(int $mitraId, string $periode)
    {
        $query = Transaksi::with(['produk', 'jasa', 'customer'])
            ->where('mitra_id', $mitraId)
            ->where('status_pembayaran', 'Lunas');

        if ($periode == 'mingguan') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } else {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

        return $query->orderBy('created_at', 'asc');
    }

    public function index(Request $request)
    {
        $mitra      = Auth::user()->mitra;
        $mitraId    = $mitra->user_id;
        $periode    = $request->get('periode', 'bulanan');
        $jenisUsaha = $mitra->jenis_usaha ?? 'Produk';

        $transaksi      = $this->getQuery($mitraId, $periode)->get();
        $totalOmzet     = $transaksi->sum('total');
        $totalTransaksi = $transaksi->count();

        return view('mitra.laporan.index', compact(
            'transaksi', 'totalOmzet', 'totalTransaksi', 'periode', 'mitra', 'jenisUsaha'
        ));
    }

    public function pdf(Request $request)
    {
        $mitra   = Auth::user()->mitra;
        $mitraId = $mitra->user_id;
        $periode = $request->get('periode', 'bulanan');

        $transaksi  = $this->getQuery($mitraId, $periode)->get();
        $totalOmzet = $transaksi->sum('total');

        $pdf = Pdf::loadView('mitra.laporan.pdf_rekap', [
            'transaksi'  => $transaksi,
            'totalOmzet' => $totalOmzet,
            'periode'    => $periode,
            'mitra'      => $mitra,
        ]);

        return $pdf->stream('Laporan_Transaksi_' . $periode . '.pdf');
    }

    public function kirimKeAdmin(Request $request)
    {
        $mitra   = Auth::user()->mitra;
        $mitraId = $mitra->user_id;
        $periode = $request->get('periode', 'bulanan');

        $transaksi  = $this->getQuery($mitraId, $periode)->get();
        $totalOmzet = $transaksi->sum('total');

        if ($totalOmzet == 0) {
            return back()->with('error', 'Belum ada transaksi lunas untuk dilaporkan.');
        }

        $persenBumdes = 10;
        $persenMitra  = 100 - $persenBumdes;

        // Cek apakah laporan bulan ini sudah di-ACC admin
        $existing = BagiHasil::where('mitra_id', $mitraId)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->first();

        if ($existing && $existing->status === 'SELESAI') {
            return back()->with('error', 'Laporan bulan ini sudah disetujui Admin dan tidak bisa diubah.');
        }

        // Gunakan updateOrCreate untuk hindari duplicate
        BagiHasil::updateOrCreate(
            [
                'mitra_id' => $mitraId,
            ],
            [
                'total_omzet'    => $totalOmzet,
                'persen_bumdes'  => $persenBumdes,
                'persen_mitra'   => $persenMitra,
                'nominal_bumdes' => $totalOmzet * ($persenBumdes / 100),
                'nominal_mitra'  => $totalOmzet * ($persenMitra / 100),
                'status'         => 'PENDING',
                'tanggal'        => now(),
            ]
        );

        return back()->with('success', 'Laporan berhasil dikirim ke Admin. Total Omzet: Rp ' . number_format($totalOmzet, 0, ',', '.'));
    }
}
