<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PesananController extends Controller
{
    /**
     * Menampilkan semua jenis pesanan customer
     */
    public function index()
    {
        $pesanan = Transaksi::where('customer_id', Auth::id())
            ->with(['produk', 'jasa'])
            ->latest()
            ->get();

        return view('customer.pesanan', compact('pesanan'));
    }

    /**
     * Filter: Belum Bayar (Status Pembayaran pending & Bukan PO)
     */
    public function pending()
    {
        $pesanan = Transaksi::where('customer_id', Auth::id())
            ->where('status_pembayaran', 'pending')
            ->where('metode_pembayaran', '!=', 'po')
            ->with(['produk', 'jasa'])
            ->latest()
            ->get();

        return view('customer.pesanan', compact('pesanan'));
    }

    /**
     * Filter: Diproses / Dikemas
     */
    public function dikemas()
    {
        $pesanan = Transaksi::where('customer_id', Auth::id())
            ->whereIn('status_pengiriman', ['Diproses', 'Dikemas'])
            ->with(['produk', 'jasa'])
            ->latest()
            ->get();

        return view('customer.pesanan', compact('pesanan'));
    }

    /**
     * Filter: Dikirim
     */
    public function dikirim()
    {
        $pesanan = Transaksi::where('customer_id', Auth::id())
            ->where('status_pengiriman', 'Dikirim')
            ->with(['produk', 'jasa'])
            ->latest()
            ->get();

        return view('customer.pesanan', compact('pesanan'));
    }

    /**
     * Filter: Selesai
     */
    public function selesai()
    {
        $pesanan = Transaksi::where('customer_id', Auth::id())
            ->whereIn('status_pengiriman', ['Diterima', 'Selesai']) // PO yang diterima juga masuk sini
            ->with(['produk', 'jasa'])
            ->latest()
            ->get();

        return view('customer.pesanan', compact('pesanan'));
    }

    /**
     * Aksi: Customer melakukan konfirmasi bahwa barang/jasa telah diterima.
     */
    public function konfirmasiDiterima($invoice_number)
    {
        $transaksi = Transaksi::where('invoice_number', $invoice_number)
            ->where('customer_id', Auth::id())
            ->firstOrFail();

        if ($transaksi->status_pengiriman !== 'Dikirim') {
            return back()->with('error', 'Pesanan belum dikirim atau sudah diproses.');
        }

        if ($transaksi->metode_pembayaran === 'po') {
            // ✅ PERBAIKAN: Jika PO, statusnya jadi 'Diterima' (menunggu pelunasan Mitra)
            $transaksi->update(['status_pengiriman' => 'Diterima']);
            $pesan = 'Konfirmasi berhasil. Menunggu konfirmasi lunas dari Mitra BUMDes.';
        } else {
            // ✅ Jika bayar instan/non-PO, langsung 'Selesai'
            $transaksi->update(['status_pengiriman' => 'Selesai']);
            $pesan = 'Terima kasih! Pesanan Anda telah selesai.';
        }

        // ✅ KIRIM NOTIFIKASI KE MITRA BAHWA BARANG SUDAH DITERIMA CUSTOMER
        $mitraUser = User::find($transaksi->mitra_id);
        if ($mitraUser) {
            $mitraUser->notify(new \App\Notifications\PesananDiterimaMitraNotification($transaksi->invoice_number, Auth::user()->name));
        }

        return redirect()->route('customer.pesanan.selesai')->with('success', $pesan);
    }
}
