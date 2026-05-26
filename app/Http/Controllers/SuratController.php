<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mitra;
use App\Mail\SertifikatMitraMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class SuratController extends Controller
{
    public function setujuiMitra(Request $request, $id)
    {
        $user = User::with('mitra')->findOrFail($id);

        $namaFile = 'Sertifikat-' . Str::slug($user->name) . '-' . strtoupper(Str::random(5)) . '.pdf';

        try {
            DB::transaction(function () use ($user, $namaFile, $request) {
                $user->update([
                    'name'              => $request->name ?? $user->name,
                    'status'            => 'aktif',
                    'surat_pengesahan'  => $namaFile,
                    'email_verified_at' => now(),
                ]);

                if ($user->mitra) {
                    $user->mitra->update(['status' => 'aktif']);
                }
            });

            $namaKepala = "IQBAL NUR AFRIZAL";
            $jabatan    = "Kepala BUMDes Putra Samudra Patimban";
            $idKepala   = "BUMDES-ID: 9201.0101.2024";
            $noSurat    = sprintf('%03d', $user->id) . "/BUMDES-PTMB/SPM/" . date('Y');

            $isiQR = "=== VERIFIKASI DOKUMEN DIGITAL ===\n" .
                     "BUMDes Putra Samudra Patimban\n" .
                     "----------------------------------\n" .
                     "Disahkan Oleh  : " . $namaKepala . "\n" .
                     "Jabatan        : " . $jabatan . "\n" .
                     "ID BUMDes      : " . $idKepala . "\n" .
                     "----------------------------------\n" .
                     "Nama Mitra     : " . strtoupper($user->name) . "\n" .
                     "Usaha          : " . ($user->mitra->nama_usaha ?? '-') . "\n" .
                     "No. Surat      : " . $noSurat . "\n" .
                     "----------------------------------\n" .
                     "Tanggal        : " . now()->translatedFormat('d F Y') . "\n" .
                     "Pukul          : " . now()->format('H:i') . " WIB\n" .
                     "==================================";

            $qrCode   = new QrCode($isiQR);
            $writer   = new SvgWriter();
            $result   = $writer->write($qrCode);
            $qrBase64 = base64_encode($result->getString());

          $pdf = Pdf::loadView('pdf.sertifikat', [
    'user'    => $user,
    'qrCode'  => $qrBase64,
    'tanggal' => now()->translatedFormat('d F Y'),
    'logo'    => extension_loaded('gd') ? public_path('asset/img/logoBumdes.png') : null,
    'noSurat' => $noSurat,
]);

            $pdfContent = $pdf->output();

            Mail::to($user->email)->send(
                new SertifikatMitraMail($user, $pdfContent, $namaFile)
            );

            activity('admin')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log("Mengaktifkan & mengirim sertifikat mitra: {$user->name} — No. Surat: {$noSurat}");

            return redirect()->back()
                ->with('success', 'Mitra diaktifkan & sertifikat berhasil dikirim ke email!');

        } catch (\Exception $e) {
            \Log::error("Gagal proses sertifikat: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memproses sertifikat: ' . $e->getMessage());
        }
    }

    public function previewSurat($id)
    {
        $user = User::with('mitra')->findOrFail($id);

        if (!$user->surat_pengesahan) {
            return redirect()->back()->with('error', 'Surat pengesahan belum tersedia.');
        }

        $filePath = storage_path('app/public/surat_pengesahan/' . $user->surat_pengesahan);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File surat pengesahan tidak ditemukan di server.');
        }

        return response()->file($filePath);
    }

    public function apiPreview($id)
    {
        $user = User::with('mitra')->findOrFail($id);

        return response()->json([
            'name'         => $user->name,
            'nama_usaha'   => $user->mitra->nama_usaha ?? '-',
            'jenis_usaha'  => $user->mitra->jenis_usaha ?? '-',
            'alamat_usaha' => $user->mitra->alamat_usaha ?? '-',
            'no_hp'        => $user->mitra->no_hp ?? '-',
        ]);
    }

    public function verifikasi($id)
    {
        $user = User::with('mitra')->find($id);

        if (!$user || $user->status !== 'aktif') {
            return view('verifikasi.invalid');
        }

        return view('verifikasi.valid', compact('user'));
    }
}
