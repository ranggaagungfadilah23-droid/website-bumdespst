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

        // ✅ Log
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
            $kepala_no_hp = $kepala->no_hp ?? '';
            if ($kepala_no_hp) {
                $namaUsaha  = $user->mitra->nama_usaha ?? '-';
                $jenisUsaha = $user->mitra->jenis_usaha ?? '-';
                $pesanKepala = "Halo Kepala BUMDes,\n\nAda pendaftaran Mitra baru yang telah *LOLOS VERIFIKASI ADMIN* dan memerlukan persetujuan serta pengesahan Anda:\n\nNama Pemilik: *{$user->name}*\nNama Usaha: *{$namaUsaha}*\nJenis Usaha: *{$jenisUsaha}*\n\nStatus berkas saat ini: *Menunggu Pengesahan Kepala BUMDes*.\nSilakan masuk ke Dashboard Kepala BUMDes untuk memeriksa data dan menandatangani sertifikat pengesahan resmi.\n\n*Sistem BUMDes Patimban*";
                $this->kirimWA($kepala_no_hp, $pesanKepala);
            }
        }

        return redirect()->route('admin.pengajuan')->with('success', 'Berkas valid dan diteruskan ke Kepala BUMDes!');
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

        // ✅ Log
        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Tolak',
            'details'   => 'Menolak pendaftaran mitra: ' . $namaUser . ' — Alasan: ' . $alasan,
        ]);

        if ($no_hp) {
            $pesanWA = "Halo *{$namaUser}*,\n\nMohon maaf, pendaftaran Mitra BUMDes Anda *DITOLAK* oleh Admin.\n\n*Alasan:* {$alasan}\n\nData berkas Anda telah kami bersihkan. Anda dapat mencoba mendaftar kembali setelah 30 hari.\n\nTerima kasih.\n\n*Admin BUMDes Patimban*";
            $this->kirimWA($no_hp, $pesanWA);
        }

        return redirect()->route('admin.pengajuan')->with('success', 'Pengajuan ditolak. Detail berkas dihapus.');
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
        $user      = User::with('mitra')->findOrFail($id);
        $namaUsaha = $user->mitra->nama_usaha ?? '-';
        $namaUser  = $user->name;

        DB::transaction(function () use ($user) {
            if ($user->mitra) {
                if ($user->mitra->sku) Storage::disk('public')->delete($user->mitra->sku);
                $user->mitra->delete();
            }
            $user->delete();
        });

        // ✅ Log
        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Hapus',
            'details'   => 'Menghapus data mitra: ' . $namaUsaha . ' (' . $namaUser . ')',
        ]);

        return redirect()->route('admin.mitra.index')->with('success', 'Data Mitra berhasil dihapus total.');
    }

    public function laporan()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $totalKasMasuk = BagiHasil::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'SELESAI')
            ->sum('nominal_bumdes');

        $totalBagiHasil = BagiHasil::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'SELESAI')
            ->sum('total_omzet');

        $totalMitra = Mitra::whereHas('user', fn($q) => $q->where('status', 'aktif'))->count();
        $bulanAktif = now()->translatedFormat('F Y');

        $grafikBulanan = BagiHasil::selectRaw('MONTH(tanggal) as bulan, SUM(total_omzet) as omzet, SUM(nominal_bumdes) as kas_bumdes')
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'SELESAI')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $namaBulan     = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $labelGrafik   = $grafikBulanan->map(fn($g) => $namaBulan[$g->bulan - 1]);
        $dataOmzet     = $grafikBulanan->pluck('omzet');
        $dataKasBumdes = $grafikBulanan->pluck('kas_bumdes');

        $perMitra = BagiHasil::where('status', 'SELESAI')
            ->get()
            ->groupBy('mitra_id')
            ->map(fn($group) => [
                'nama'          => optional(Mitra::where('user_id', $group->first()->mitra_id)->first())->nama_usaha ?? '-',
                'omzet'         => $group->sum('total_omzet'),
                'persen_bumdes' => $group->first()->persen_bumdes,
                'kas_bumdes'    => $group->sum('nominal_bumdes'),
            ])->values();

        return view('admin.laporan', compact(
            'totalKasMasuk', 'totalBagiHasil', 'totalMitra',
            'bulanAktif', 'labelGrafik', 'dataOmzet', 'dataKasBumdes', 'perMitra'
        ));
    }

    public function histori()
    {
        $aktivitas = \App\Models\ActivityLog::latest()->paginate(20);
        return view('admin.histori', compact('aktivitas'));
    }

    public function kirimLaporan(Request $request)
    {
        $request->validate([
            'bulan_aktif'     => 'required|string',
            'total_kas_masuk' => 'required|numeric',
            'total_omzet'     => 'required|numeric',
            'total_mitra'     => 'required|integer',
            'catatan'         => 'nullable|string|max:500',
        ]);

        \App\Models\LaporanKas::create([
            'dikirim_oleh'    => auth()->id(),
            'bulan_aktif'     => $request->bulan_aktif,
            'total_kas_masuk' => $request->total_kas_masuk,
            'total_omzet'     => $request->total_omzet,
            'total_mitra'     => $request->total_mitra,
            'catatan'         => $request->catatan,
            'status'          => 'terkirim',
            'dikirim_at'      => now(),
        ]);

        $kepalaBumdes = \App\Models\User::where('role', 'kepala-bumdes')->get();
        foreach ($kepalaBumdes as $kepala) {
            $kepala->notify(new \App\Notifications\LaporanKasDikirim(
                $request->bulan_aktif,
                $request->total_kas_masuk,
                $request->catatan,
            ));
        }

        // ✅ Log kirim laporan
        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Kirim',
            'details'   => 'Mengirim laporan keuangan bulan: ' . $request->bulan_aktif,
        ]);

        return redirect()->route('admin.laporan')->with('laporan_terkirim', true);
    }

    public function laporanPdf()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $totalKasMasuk = BagiHasil::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'SELESAI')
            ->sum('nominal_bumdes');

        $totalBagiHasil = BagiHasil::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'SELESAI')
            ->sum('total_omzet');

        $totalMitra = Mitra::whereHas('user', fn($q) => $q->where('status', 'aktif'))->count();
        $bulanAktif = now()->translatedFormat('F Y');

        $perMitra = BagiHasil::whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'SELESAI')
            ->get()
            ->groupBy('mitra_id')
            ->map(fn($group) => [
                'nama'          => optional(Mitra::where('user_id', $group->first()->mitra_id)->first())->nama_usaha ?? '-',
                'omzet'         => $group->sum('total_omzet'),
                'persen_bumdes' => $group->first()->persen_bumdes,
                'kas_bumdes'    => $group->sum('nominal_bumdes'),
            ])->values();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan_pdf', compact(
            'totalKasMasuk', 'totalBagiHasil', 'totalMitra', 'bulanAktif', 'perMitra'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_BagiHasil_' . now()->format('Y_m') . '.pdf');
    }

    private function kirimWA($no_hp, $pesan)
    {
        $token  = "obEnSgdDTVkALfwmMYTy";
        $target = preg_replace('/^0/', '62', $no_hp);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => [
                'target'      => $target,
                'message'     => $pesan,
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => ["Authorization: $token"],
        ]);

        curl_exec($curl);
        curl_close($curl);
    }
}
