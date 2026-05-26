<?php
namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Bagihasil; // <-- JANGAN LUPA TAMBAHKAN INI
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
        // ... (Kode index tetap sama seperti milikmu) ...
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
        // ... (Kode pdf tetap sama seperti milikmu) ...
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

    /**
     * Kirim laporan ke admin
     */
    public function kirimKeAdmin(Request $request)
    {
        $mitra   = Auth::user()->mitra;
        $mitraId = $mitra->user_id;
        $periode = $request->get('periode', 'bulanan');

        // 1. Ambil transaksi yang sudah Lunas menggunakan fungsi getQuery bawaanmu
        $transaksi  = $this->getQuery($mitraId, $periode)->get();
        $totalOmzet = $transaksi->sum('total');

        // Jika tidak ada omzet, tolak pengiriman
        if ($totalOmzet == 0) {
            return back()->with('error', 'Belum ada transaksi lunas untuk dilaporkan.');
        }

        // 2. Set persentase bagi hasil (Ubah angka 10 ini sesuai aturan BUMDes)
        $persenBumdes = 10;
        $persenMitra  = 100 - $persenBumdes;

        // 3. Cari laporan di bulan ini agar tidak ada duplikat / data tertimpa
        $bh = Bagihasil::where('mitra_id', $mitraId)
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->first();

        if ($bh) {
            // Jika sudah di-ACC Admin, blokir perubahan
            if ($bh->status === 'SELESAI') {
                return back()->with('error', 'Laporan bulan ini sudah disetujui Admin dan tidak bisa diubah.');
            }

            // Update jika masih PENDING
            $bh->update([
                'total_omzet'    => $totalOmzet,
                'persen_bumdes'  => $persenBumdes,
                'persen_mitra'   => $persenMitra,
                'nominal_bumdes' => $totalOmzet * ($persenBumdes / 100),
                'nominal_mitra'  => $totalOmzet * ($persenMitra / 100),
                'tanggal'        => now(),
            ]);
        } else {
            // Buat laporan baru jika belum pernah klik kirim bulan ini
            Bagihasil::create([
                'mitra_id'       => $mitraId,
                'total_omzet'    => $totalOmzet,
                'persen_bumdes'  => $persenBumdes,
                'persen_mitra'   => $persenMitra,
                'nominal_bumdes' => $totalOmzet * ($persenBumdes / 100),
                'nominal_mitra'  => $totalOmzet * ($persenMitra / 100),
                'status'         => 'PENDING',
                'tanggal'        => now(),
            ]);
        }

        return back()->with('success', 'Laporan berhasil dikirim ke Admin. Total Omzet: Rp ' . number_format($totalOmzet, 0, ',', '.'));
    }
}
