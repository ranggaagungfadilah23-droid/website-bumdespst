<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mitra;
use App\Models\ActivityLog;
use App\Mail\SertifikatMitraMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;


class KepalaBumdesController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_mitra'      => User::where('role', 'mitra')->where('status', 'aktif')->count(),
            'pending_approval' => User::where('role', 'mitra')->where('status', 'menunggu_kepala')->count(),
        ];
        return view('kepala-bumdes.dashboard', compact('stats'));
    }

    public function pengajuan()
    {
        $pengajuans = User::with('mitra')
            ->where('role', 'mitra')
            ->where('status', 'menunggu_kepala')
            ->latest()
            ->get();
        return view('kepala-bumdes.pengajuan', compact('pengajuans'));
    }

    public function previewSurat($id)
    {
        $user = User::with('mitra')->findOrFail($id);
        return response()->json([
            'name'         => $user->name,
            'nama_usaha'   => $user->mitra->nama_usaha ?? '-',
            'jenis_usaha'  => $user->mitra->jenis_usaha ?? '-',
            'alamat_usaha' => $user->mitra->alamat_usaha ?? '-',
            'rt_rw'        => $user->mitra->rt_rw ?? '-',
            'no_hp'        => $user->mitra->no_hp ?? '-',
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = User::with('mitra')->findOrFail($id);

        try {
            DB::beginTransaction();

            $namaFile = 'Sertifikat-' . Str::slug($user->name) . '-' . strtoupper(Str::random(5)) . '.pdf';

            $user->update([
                'status'           => 'aktif',
                'surat_pengesahan' => $namaFile,
            ]);

            if ($user->mitra) {
                $user->mitra->update(['status' => 'aktif']);
            }

            $isiQR = "DISAHKAN OLEH: IQBAL NUR AFRIZAL\n" .
                     "Direktur Utama BUMDes Putra Samudra Patimban\n" .
                     "Mitra: " . $user->name;

            $qrCode   = new QrCode($isiQR);
            $writer   = new SvgWriter();
            $result   = $writer->write($qrCode);
            $qrBase64 = base64_encode($result->getString());

            $pdf = Pdf::loadView('pdf.sertifikat', [
                'user'    => $user,
                'qrCode'  => $qrBase64,
                'tanggal' => now()->translatedFormat('d F Y'),
                'logo'    => extension_loaded('gd') ? public_path('asset/img/logoBumdes.png') : null,
                'noSurat' => $noSurat ?? null,
            ]);

            $pdfContent = $pdf->output();

            Storage::disk('public')->put('sertifikat/' . $namaFile, $pdfContent);

            $no_hp = $user->mitra->no_hp ?? '';
            if ($no_hp) {
                $pesanWA = "Halo *{$user->name}*,\n\nSelamat! Pendaftaran Mitra Anda telah *DISAHKAN* oleh Kepala BUMDes Putra Samudra Patimban.\n\nSurat Sertifikat Pengesahan resmi Anda telah kami kirimkan ke alamat email: *{$user->email}*.\n\nTerima kasih!\n\n*BUMDes Patimban*";
                $this->kirimWA($no_hp, $pesanWA);
            }

            Mail::to($user->email)->send(
                new SertifikatMitraMail($user, $pdfContent, $namaFile)
            );

            // ✅ Log
            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'action'    => 'Setuju',
                'details'   => 'Kepala BUMDes mengesahkan mitra: ' . $user->name,
            ]);

            DB::commit();

            return redirect()->route('kepala-bumdes.pengajuan')
                ->with('success', "Pendaftaran {$user->name} disahkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses sertifikat: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $user     = User::with('mitra')->findOrFail($id);
        $alasan   = $request->pesan_penolakan ?? 'Tidak memenuhi kriteria BUMDes';
        $no_hp    = $user->mitra->no_hp ?? '';
        $namaUser = $user->name;

        DB::transaction(function () use ($user) {
            if ($user->mitra) {
                if ($user->mitra->ktp_path) Storage::disk('public')->delete($user->mitra->ktp_path);
                if ($user->mitra->sku_path) Storage::disk('public')->delete($user->mitra->sku_path);
                $user->mitra->delete();
            }
            $user->update(['status' => 'rejected']);
        });

        if ($no_hp) {
            $pesanWA = "Halo *{$namaUser}*,\n\nMohon maaf, pendaftaran Mitra BUMDes Anda *DITOLAK* pada tahap pengesahan akhir.\n\n*Alasan:* {$alasan}\n\nTerima kasih.\n\n*BUMDes Patimban*";
            $this->kirimWA($no_hp, $pesanWA);
        }

        // ✅ Log
        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Tolak',
            'details'   => 'Kepala BUMDes menolak mitra: ' . $namaUser . ' — Alasan: ' . $alasan,
        ]);

        return redirect()->route('kepala-bumdes.pengajuan')
            ->with('success', 'Pengajuan ditolak dan notifikasi dikirim ke Mitra.');
    }

    public function dataMitra()
    {
        $mitras = Mitra::whereHas('user', function ($q) {
            $q->where('status', 'aktif');
        })->latest()->get();
        return view('kepala-bumdes.data-mitra', compact('mitras'));
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
            CURLOPT_HTTPHEADER     => ["Authorization: $token"],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) Log::error("Fonnte Error: " . $error);
        else Log::info("Fonnte Response: " . $response);
    }
}
