<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mitra;
use App\Models\BagiHasil;
use App\Models\LaporanKas;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function pengajuan()
    {
        $pengajuans = User::with('mitra')
            ->where('role', 'mitra')
            ->where('status', 'pending')
            ->get();

        return view('admin.pengajuan', compact('pengajuans'));
    }

    public function approve($id)
    {
        $user = User::with('mitra')->findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->update(['status' => 'menunggu_kepala']);
            if ($user->mitra) {
                $user->mitra->update(['status' => 'menunggu_kepala']);
            }
        });

        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Setuju',
            'details'   => 'Menyetujui pendaftaran mitra: ' . $user->name,
        ]);

        $no_hp = $user->mitra->no_hp ?? '';
        if ($no_hp) {
            $pesan = "Halo *{$user->name}*,\n\nBerkas pendaftaran Mitra BUMDes Anda telah lolos verifikasi tahap pertama oleh Admin. Saat ini berkas Anda sedang diteruskan dan menunggu persetujuan akhir dari *Kepala BUMDes*.\n\nMohon kesediaannya menunggu. Terima kasih.\n\n*Admin BUMDes Patimban*";
            $this->kirimWA($no_hp, $pesan);
        }

        $kepalaBumdes = User::where('role', 'kepala-bumdes')->get();
        foreach ($kepalaBumdes as $kepala) {
            if ($kepala->no_hp) {
                $namaUsaha  = $user->mitra->nama_usaha ?? '-';
                $jenisUsaha = $user->mitra->jenis_usaha ?? '-';
                $pesanKepala = "Halo Kepala BUMDes,\n\nAda pendaftaran Mitra baru yang telah *LOLOS VERIFIKASI ADMIN* dan memerlukan persetujuan serta pengesahan Anda:\n\nNama Pemilik: *{$user->name}*\nNama Usaha: *{$namaUsaha}*\nJenis Usaha: *{$jenisUsaha}*\n\nStatus berkas saat ini: *Menunggu Pengesahan Kepala BUMDes*.\nSilakan masuk ke Dashboard Kepala BUMDes untuk memeriksa data dan menandatangani sertifikat pengesahan resmi.\n\n*Sistem BUMDes Patimban*";
                $this->kirimWA($kepala->no_hp, $pesanKepala);
            }
        }

        return redirect()->route('admin.pengajuan')->with('success', 'Berkas valid dan diteruskan ke Kepala BUMDes!');
    }

    public function kirimLaporan(Request $request)
    {
        $request->validate([
            'bulan_aktif'      => 'required|string',
            'total_kas_masuk'  => 'required|numeric',
            'total_omzet'      => 'required|numeric',
            'total_mitra'      => 'required|integer',
            'catatan'          => 'nullable|string',
        ]);

        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Kirim Laporan',
            'details'   => 'Mengirim laporan keuangan periode ' . $request->bulan_aktif . ' ke Kepala BUMDes.'
                . ($request->catatan ? ' Catatan: ' . $request->catatan : ''),
        ]);

        $kepalaBumdes = User::where('role', 'kepala-bumdes')->get();
        foreach ($kepalaBumdes as $kepala) {
            if ($kepala->no_hp) {
                $pesan = "Halo Kepala BUMDes,\n\n"
                    . "Laporan keuangan periode *{$request->bulan_aktif}* telah dikirim oleh Admin:\n\n"
                    . "Total Omzet Mitra: Rp " . number_format($request->total_omzet, 0, ',', '.') . "\n"
                    . "Kas Masuk BUMDes: Rp " . number_format($request->total_kas_masuk, 0, ',', '.') . "\n"
                    . "Jumlah Mitra: {$request->total_mitra} mitra\n"
                    . ($request->catatan ? "\nCatatan: {$request->catatan}\n" : '')
                    . "\nSilakan cek Dashboard Kepala BUMDes untuk detail lengkap.\n\n"
                    . "*Sistem BUMDes Patimban*";
                $this->kirimWA($kepala->no_hp, $pesan);
            }
        }

        return redirect()
            ->route('admin.laporan')
            ->with('laporan_terkirim', true);
    }

    public function reject(Request $request, $id)
    {
        $user     = User::with('mitra')->findOrFail($id);
        $alasan   = $request->pesan_penolakan ?? 'Tidak disebutkan';
        $no_hp    = $user->mitra->no_hp ?? '';
        $namaUser = $user->name;

        DB::transaction(function () use ($user) {
            if ($user->mitra) {
                if ($user->mitra->sku) Storage::disk('public')->delete($user->mitra->sku);
                $user->mitra->delete();
            }
            $user->update(['status' => 'rejected']);
        });

        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Tolak',
            'details'   => 'Menolak pendaftaran mitra: ' . $namaUser . ' — Alasan: ' . $alasan,
        ]);

        if ($no_hp) {
            $pesanWA = "Halo *{$namaUser}*,\n\nMohon maaf, pendaftaran Mitra BUMDes Anda *DITOLAK* oleh Admin.\n\n*Alasan:* {$alasan}\n\nData berkas Anda telah kami bersihkan.\n\nTerima kasih.\n\n*Admin BUMDes Patimban*";
            $this->kirimWA($no_hp, $pesanWA);
        }

        return redirect()->route('admin.pengajuan')->with('success', 'Pengajuan ditolak.');
    }

    public function dataMitra()
    {
        $mitras = Mitra::whereHas('user', function ($q) {
            $q->where('status', 'aktif');
        })->latest()->get();

        return view('admin.data-mitra', compact('mitras'));
    }

public function destroyMitra($id)
{
    // 1. Cari user berdasarkan ID
    $user = User::findOrFail($id);

    // 2. Simpan data untuk log sebelum dihapus
    $namaUsaha = $user->mitra->nama_usaha ?? '-';
    $namaUser  = $user->name;

    DB::transaction(function () use ($user) {
        if ($user->mitra) {
            // Hapus Jasa dan Cart yang terkait
            // PENTING: jasas.user_id & produks.user_id merujuk ke users.id, BUKAN mitras.id
            $jasas = \App\Models\Jasa::where('user_id', $user->id)->get();
            foreach ($jasas as $jasa) {
                \App\Models\Cart::where('jasa_id', $jasa->id)->delete();
                $jasa->delete();
            }

            // Hapus Produk dan Cart yang terkait
            $produks = \App\Models\Produk::where('user_id', $user->id)->get();
            foreach ($produks as $produk) {
                \App\Models\Cart::where('produk_id', $produk->id)->delete();
                $produk->delete();
            }

            // Hapus SKU
            if ($user->mitra->sku) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->mitra->sku);
            }

            // Hapus Mitra
            $user->mitra->delete();
        }

        // Hapus User
        $user->delete();
    });

    \App\Models\ActivityLog::create([
        'user_name' => auth()->user()->name,
        'action'    => 'Hapus',
        'details'   => 'Menghapus data mitra: ' . $namaUsaha . ' (' . $namaUser . ')',
    ]);

    return redirect()->route('admin.mitra.index')->with('success', 'Data Mitra berhasil dihapus.');
}

    public function laporan()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $stats = Cache::remember("laporan_stats_{$bulanIni}_{$tahunIni}", 600, function () use ($bulanIni, $tahunIni) {
            return [
                'totalKasMasuk'  => BagiHasil::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'SELESAI')->sum('nominal_bumdes'),
                'totalBagiHasil' => BagiHasil::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'SELESAI')->sum('total_omzet'),
                'totalMitra'     => Mitra::whereHas('user', fn($q) => $q->where('status', 'aktif'))->count(),
            ];
        });

        $grafikBulanan = BagiHasil::selectRaw('MONTH(tanggal) as bulan, SUM(total_omzet) as omzet, SUM(nominal_bumdes) as kas_bumdes')
            ->whereYear('tanggal', $tahunIni)->where('status', 'SELESAI')
            ->groupBy('bulan')->orderBy('bulan')->get();

        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $perMitra = BagiHasil::where('status', 'SELESAI')
            ->with('mitra')
            ->get()
            ->groupBy('mitra_id')
            ->map(fn($group) => [
                'nama'          => $group->first()->mitra->nama_usaha ?? '-',
                'omzet'         => $group->sum('total_omzet'),
                'persen_bumdes' => $group->first()->persen_bumdes,
                'kas_bumdes'    => $group->sum('nominal_bumdes'),
            ])->values();

        return view('admin.laporan', array_merge($stats, [
            'bulanAktif'   => now()->translatedFormat('F Y'),
            'labelGrafik'  => $grafikBulanan->map(fn($g) => $namaBulan[$g->bulan - 1]),
            'dataOmzet'    => $grafikBulanan->pluck('omzet'),
            'dataKasBumdes'=> $grafikBulanan->pluck('kas_bumdes'),
            'perMitra'     => $perMitra
        ]));
    }

    public function laporanPdf()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $bulanAktif = now()->translatedFormat('F Y');

        $totalKasMasuk  = BagiHasil::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'SELESAI')->sum('nominal_bumdes');
        $totalBagiHasil = BagiHasil::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'SELESAI')->sum('total_omzet');
        $totalMitra     = Mitra::whereHas('user', fn($q) => $q->where('status', 'aktif'))->count();

        $perMitra = BagiHasil::whereMonth('tanggal', $bulanIni)->whereYear('tanggal', $tahunIni)->where('status', 'SELESAI')
            ->with('mitra')
            ->get()
            ->groupBy('mitra_id')
            ->map(fn($group) => [
                'nama'          => $group->first()->mitra->nama_usaha ?? '-',
                'omzet'         => $group->sum('total_omzet'),
                'persen_bumdes' => $group->first()->persen_bumdes,
                'kas_bumdes'    => $group->sum('nominal_bumdes'),
            ])->values();

        $pdf = Pdf::loadView('admin.laporan_pdf', compact('totalKasMasuk', 'totalBagiHasil', 'totalMitra', 'perMitra', 'bulanAktif'));
        return $pdf->stream('Laporan_BagiHasil.pdf');
    }

    public function histori()
    {
        $aktivitas = ActivityLog::latest()->paginate(20);
        return view('admin.histori', compact('aktivitas'));
    }

    private function kirimWA($no_hp, $pesan)
    {
        $token  = "obEnSgdDTVkALfwmMYTy";
        $target = preg_replace('/^0/', '62', $no_hp);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => ['target' => $target, 'message' => $pesan, 'countryCode' => '62'],
            CURLOPT_HTTPHEADER     => ["Authorization: $token"],
        ]);
        curl_exec($curl);
        curl_close($curl);
    }
}