<?php

namespace App\Http\Controllers\Mitra\Jasa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;

class DashboardController extends Controller
{
 public function dashboard()
{
    $userId = Auth::id();

    // ✅ Konsisten pakai mitra_id = user_id, sama seperti controller lain
    $query = Transaksi::with(['customer', 'jasa'])
        ->where('mitra_id', $userId);

    $pesananBaru = (clone $query)
        ->where('status_pengiriman', 'menunggu')
        ->count();

    $pesananSelesai = \App\Models\Pendapatan::where('mitra_id', $userId)->count();
    $totalPendapatanBersih = (clone $query)
        ->where('status_pembayaran', 'Lunas')
        ->sum('total');

    $statusPesanan = (clone $query)
        ->select('status_pengiriman', DB::raw('count(*) as total'))
        ->groupBy('status_pengiriman')
        ->pluck('total', 'status_pengiriman');

   $pendapatanBulanan = (clone $query)
    ->where('status_pembayaran', 'Lunas')
    ->select(
        DB::raw('MONTHNAME(created_at) as bulan'),
        DB::raw('MONTH(created_at) as bulan_num'), // ← tambahkan ini
        DB::raw('SUM(total) as total')
    )
    ->groupBy('bulan', 'bulan_num') // ← tambahkan bulan_num
    ->orderBy('bulan_num')          // ← ganti orderBy ini
    ->get();

    
    $pesananTerbaru = (clone $query)
        ->latest()
        ->take(5)
        ->get();

    return view('mitra.jasa.dashboard', compact(
        'pesananBaru', 'pesananSelesai', 'totalPendapatanBersih',
        'pesananTerbaru', 'pendapatanBulanan', 'statusPesanan'
    ));
}
}
