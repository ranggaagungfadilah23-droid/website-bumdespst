<?php

namespace App\Http\Controllers;

use App\Models\BagiHasil;
use App\Models\Mitra;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BagihasilController extends Controller
{
    
 public function store(Request $request)
{
    $request->validate([
        'mitra_id'      => 'required|exists:mitras,id',
        'total_omzet'   => 'required|numeric|min:0',
        'persen_bumdes' => 'required|numeric|min:1|max:50',
    ]);

    $persenBumdes = $request->persen_bumdes;
    $persenMitra  = 100 - $persenBumdes;
    $omzet        = $request->total_omzet;

    BagiHasil::create([
        'mitra_id'       => $request->mitra_id, // = mitras.id
        'tanggal'        => now(),
        'total_omzet'    => $omzet,
        'persen_bumdes'  => $persenBumdes,
        'persen_mitra'   => $persenMitra,
        'nominal_bumdes' => $omzet * $persenBumdes / 100,
        'nominal_mitra'  => $omzet * $persenMitra / 100,
        'status'         => 'PENDING',
    ]);

    return redirect()->route('admin.bagihasil')->with('success', 'Data bagi hasil berhasil ditambahkan.');
}

public function index()
{
    $bagihasils = BagiHasil::with('mitra.user')->latest('tanggal')->get();
    $all_mitra  = Mitra::with('user')->whereHas('user', fn($q) => $q->where('status', 'aktif'))->get();

    return view('admin.bagihasil', compact('bagihasils', 'all_mitra'));
}

   public function confirm(Request $request)
{
    $bh    = BagiHasil::findOrFail($request->id);
    $mitra = Mitra::find($bh->mitra_id);   // langsung cari berdasarkan id mitra, bukan user_id

    $bh->update(['status' => 'SELESAI']);

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
