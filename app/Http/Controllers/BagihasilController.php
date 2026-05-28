<?php

namespace App\Http\Controllers;

use App\Models\BagiHasil;
use App\Models\Mitra;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BagihasilController extends Controller
{
    public function index()
    {
        $bagihasils = BagiHasil::latest()->get();
        $all_mitra  = Mitra::with('user')->get();
        return view('admin.bagihasil', compact('bagihasils', 'all_mitra'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mitra_id'      => 'required',
            'total_omzet'   => 'required|numeric',
            'persen_bumdes' => 'required|numeric|min:1|max:99',
        ]);

        $omzet        = $request->total_omzet;
        $persenBumdes = $request->persen_bumdes;
        $persenMitra  = 100 - $persenBumdes;

        $bh = BagiHasil::where('mitra_id', $request->mitra_id)->latest()->first();

        if ($bh) {
            $bh->update([
                'total_omzet'    => $omzet,
                'persen_bumdes'  => $persenBumdes,
                'persen_mitra'   => $persenMitra,
                'nominal_bumdes' => $omzet * ($persenBumdes / 100),
                'nominal_mitra'  => $omzet * ($persenMitra / 100),
                'status'         => 'PENDING',
                'tanggal'        => now(),
            ]);
        } else {
            BagiHasil::create([
                'mitra_id'       => $request->mitra_id,
                'total_omzet'    => $omzet,
                'persen_bumdes'  => $persenBumdes,
                'persen_mitra'   => $persenMitra,
                'nominal_bumdes' => $omzet * ($persenBumdes / 100),
                'nominal_mitra'  => $omzet * ($persenMitra / 100),
                'status'         => 'PENDING',
                'tanggal'        => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Data bagi hasil berhasil disimpan!');
    }

    public function confirm(Request $request)
    {
        $bh    = BagiHasil::findOrFail($request->id);
        $mitra = Mitra::where('user_id', $bh->mitra_id)->first();

        $bh->update(['status' => 'SELESAI']);

        // ✅ Log
        ActivityLog::create([
            'user_name' => auth()->user()->name,
            'action'    => 'Konfirmasi',
            'details'   => 'Mengkonfirmasi bagi hasil mitra: ' . ($mitra->nama_usaha ?? '-') .
                           ' — Nominal BUMDes: Rp ' . number_format($bh->nominal_bumdes, 0, ',', '.'),
        ]);

        return redirect()->back()->with('success', 'Bagi hasil mitra BUMDes berhasil dikonfirmasi!');
    }

    public function getOmzet($mitra_id)
    {
        $omzet = \App\Models\Pendapatan::where('mitra_id', $mitra_id)->sum('total_diterima');
        return response()->json(['omzet' => $omzet]);
    }
}
