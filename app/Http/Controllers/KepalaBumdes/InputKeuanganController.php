<?php
// File: app/Http/Controllers/KepalaBumdes/InputKeuanganController.php

namespace App\Http\Controllers\KepalaBumdes;

use App\Http\Controllers\Controller;
use App\Models\SaldoAwal;
use App\Models\RekapPengeluaran;
use App\Models\BagiHasil;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InputKeuanganController extends Controller
{
    // ─────────────────────────────────────────────
    // Tampilkan Dashboard Monitoring + Form Input
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        // 1. Ambil Parameter Filter Bulan & Tahun Aktif
        $bulanAktif = $request->get('bulan', date('m'));
        $tahunAktif = $request->get('tahun', date('Y'));
        $filterRekap = $request->get('filter_rekap', 'semua');

        $namaBulan = Carbon::createFromDate($tahunAktif, $bulanAktif, 1)->locale('id')->isoFormat('MMMM');

        // ====================================================================
        // 2. LOGIKA SALDO AWAL OTOMATIS (AKUMULASI DARI BULAN LALU)
        // ====================================================================

        // Cek dulu, apakah Kepala BUMDes sengaja menginput Saldo Manual di bulan aktif ini?
        $saldoAwalManual = SaldoAwal::where('bulan', $bulanAktif)->where('tahun', $tahunAktif)->first();

        if ($saldoAwalManual) {
            // Jika ada input manual (misal suntikan dana/koreksi), gunakan angka tersebut
            $saldoAwal = $saldoAwalManual->saldo_awal;
        } else {
            // Jika tidak ada, sistem menghitung otomatis dari awal BUMDes berdiri sampai BULAN LALU
            $tanggalBatas = Carbon::createFromDate($tahunAktif, $bulanAktif, 1)->startOfMonth();

            // A. Ambil total semua Suntikan Saldo Awal di masa lalu
            $totalSuntikanLalu = SaldoAwal::where(function($q) use ($tahunAktif, $bulanAktif) {
                $q->where('tahun', '<', $tahunAktif)
                  ->orWhere(function($q2) use ($tahunAktif, $bulanAktif) {
                      $q2->where('tahun', $tahunAktif)
                         ->where('bulan', '<', $bulanAktif);
                  });
            })->sum('saldo_awal');

            // B. Ambil total Pemasukan (Bagi Hasil) di masa lalu
            $totalPemasukanLalu = BagiHasil::where('status', 'SELESAI')
                ->where('tanggal', '<', $tanggalBatas)
                ->sum('nominal_bumdes');

            // C. Ambil total Pengeluaran Operasional di masa lalu
            $totalPengeluaranLalu = RekapPengeluaran::where('tanggal', '<', $tanggalBatas)
                ->sum('total_pengeluaran');

            // SALDO AWAL OTOMATIS = (Suntikan Modal + Pemasukan Lalu) - Pengeluaran Lalu
            $saldoAwal = $totalSuntikanLalu + $totalPemasukanLalu - $totalPengeluaranLalu;
        }
        // ====================================================================

        // 3. Ambil Pemasukan BULAN INI
        $dataPemasukan = BagiHasil::whereMonth('tanggal', $bulanAktif)
            ->whereYear('tanggal', $tahunAktif)
            ->where('status', 'SELESAI')
            ->get()
            ->map(function($item) {
                return (object)[
                    'tanggal' => $item->tanggal,
                    'keterangan' => 'Bagi Hasil Keuntungan Toko/Mitra BUMDes',
                    'sumber' => 'Mitra BUMDes',
                    'jumlah' => $item->nominal_bumdes
                ];
            });

        $totalPemasukan = $dataPemasukan->sum('jumlah');
        $jumlahTransaksiMasuk = $dataPemasukan->count();

        // 4. Ambil Data Pengeluaran BULAN INI
        $queryRekap = RekapPengeluaran::where('bulan', $bulanAktif)->where('tahun', $tahunAktif)->orderByDesc('tanggal');
        if ($filterRekap !== 'semua') {
            $queryRekap->where('tipe_periode', $filterRekap);
        }
        $rekapPengeluaran = $queryRekap->get();

        $dataPengeluaran = $rekapPengeluaran->map(function ($item) {
            return (object)[
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'keterangan' => $item->keterangan,
                'kategori' => $item->kategori,
                'jumlah' => $item->total_pengeluaran
            ];
        });

        $totalPengeluaran = $dataPengeluaran->sum('jumlah');
        $jumlahTransaksiKeluar = $dataPengeluaran->count();

        // 5. Kalkulasi Saldo Akhir Komparatif Bulan Ini
        $saldoAkhir = $saldoAwal + $totalPemasukan - $totalPengeluaran;

        // 6. Ambil Data Pengeluaran Per Kategori untuk Grafik Donut
        $kategoriPengeluaran = RekapPengeluaran::where('bulan', $bulanAktif)
            ->where('tahun', $tahunAktif)
            ->select('kategori', DB::raw('SUM(total_pengeluaran) as total'))
            ->groupBy('kategori')
            ->get();

        // 7. Kalkulasi Distribusi Nilai untuk Grafik Arus Kas Harian
        $labelHarian = [];
        $dataMasukHarian = [];
        $dataKeluarHarian = [];
      $jumlahHari = Carbon::createFromDate($tahunAktif, $bulanAktif, 1)->daysInMonth;

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $labelHarian[] = "Tgl $d";

            $masukHariIni = $dataPemasukan->filter(function($item) use ($d) {
                return Carbon::parse($item->tanggal)->day == $d;
            })->sum('jumlah');

            $keluarHariIni = $rekapPengeluaran->filter(function($item) use ($d) {
                return Carbon::parse($item->tanggal)->day == $d;
            })->sum('total_pengeluaran');

            $dataMasukHarian[] = $masukHariIni;
            $dataKeluarHarian[] = $keluarHariIni;
        }

        // Riwayat saldo awal untuk tracking internal
        $riwayatSaldoAwal = SaldoAwal::orderByDesc('tahun')->orderByDesc('bulan')->get();

        // 8. Kirim ke View Tunggal Monitoring
        return view('kepala-bumdes.monitoring-keuangan', compact(
            'bulanAktif', 'tahunAktif', 'namaBulan', 'filterRekap',
            'saldoAwal', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir',
            'jumlahTransaksiMasuk', 'jumlahTransaksiKeluar',
            'dataPemasukan', 'dataPengeluaran', 'rekapPengeluaran', 'riwayatSaldoAwal',
            'kategoriPengeluaran', 'labelHarian', 'dataMasukHarian', 'dataKeluarHarian'
        ));
    }

    // ─────────────────────────────────────────────
    // Simpan Saldo Awal
    // ─────────────────────────────────────────────
    public function simpanSaldoAwal(Request $request)
    {
        $request->validate([
            'bulan'       => 'required|integer|between:1,12',
            'tahun'       => 'required|integer|min:2000|max:2099',
            'saldo_awal'  => 'required|string',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $nominal = (int) preg_replace('/\D/', '', $request->saldo_awal);

        SaldoAwal::updateOrCreate(
            [
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
            ],
            [
                'saldo_awal'  => $nominal,
                'keterangan'  => $request->keterangan,
                'created_by'  => auth()->id(),
            ]
        );

        return redirect()
            ->route('kepala-bumdes.monitoring-keuangan')
            ->with('success', 'Data suntikan saldo awal berhasil disimpan.');
    }

    // ─────────────────────────────────────────────
    // Hapus Saldo Awal
    // ─────────────────────────────────────────────
    public function hapusSaldoAwal($id)
    {
        SaldoAwal::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data saldo awal berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // Simpan Pengeluaran
    // ─────────────────────────────────────────────
    public function simpanPengeluaran(Request $request)
    {
        $request->validate([
            'tipe_periode'            => 'required|in:mingguan,bulanan',
            'kategori'                => 'required|string',
            'keterangan_pengeluaran'  => 'required|string|max:255',
            'tanggal'                 => 'required|date',
            'total_pengeluaran'       => 'required|integer|min:1',
            'item_nama'               => 'nullable|array',
            'item_jumlah'             => 'nullable|array',
        ]);

        $isMingguan = $request->tipe_periode === 'mingguan';
        $bulan   = $isMingguan ? $request->bulan_pengeluaran   : $request->bulan_pengeluaran_b;
        $tahun   = $isMingguan ? $request->tahun_pengeluaran   : $request->tahun_pengeluaran_b;
        $minggu  = $isMingguan ? $request->minggu_ke           : null;

        $items = [];
        if ($isMingguan && $request->has('item_nama')) {
            foreach ($request->item_nama as $idx => $nama) {
                if (empty($nama)) continue;
                $jumlah = (int) preg_replace('/\D/', '', $request->item_jumlah[$idx] ?? '0');
                $items[] = ['nama' => $nama, 'jumlah' => $jumlah];
            }
        }

        RekapPengeluaran::create([
            'tipe_periode'     => $request->tipe_periode,
            'minggu_ke'        => $minggu,
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'kategori'         => $request->kategori,
            'keterangan'       => $request->keterangan_pengeluaran,
            'detail_item'      => !empty($items) ? json_encode($items) : null,
            'total_pengeluaran'=> (int) $request->total_pengeluaran,
            'tanggal'          => $request->tanggal,
            'created_by'       => auth()->id(),
        ]);

        return redirect()
            ->route('kepala-bumdes.monitoring-keuangan')
            ->with('success', 'Rekap data pengeluaran operasional berhasil disimpan.');
    }

    // ─────────────────────────────────────────────
    // Hapus Rekap Pengeluaran
    // ─────────────────────────────────────────────
    public function hapusPengeluaran($id)
    {
        RekapPengeluaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data rekap pengeluaran operasional berhasil dihapus.');
    }
}
