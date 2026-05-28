<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Pendapatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    public function confirm(Request $request)
    {
        $cartIds = $request->input('cart_ids');

        if (empty($cartIds)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Item tidak dipilih.');
        }

        $selectedCarts = Cart::with(['produk', 'jasa'])
            ->whereIn('id', $cartIds)
            ->where('user_id', auth()->id())
            ->get();

        if ($selectedCarts->isEmpty()) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Item tidak ditemukan di keranjang.');
        }

        return view('customer.checkout-confirm', compact('selectedCarts'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'cart_ids'          => 'required|array|min:1',
            'cart_ids.*'        => 'exists:carts,id',
            'metode_pembayaran' => 'required|in:bayar_sekarang,po',
            'alamat'            => 'required|string|min:5|max:500',
        ]);

        $carts = Cart::with(['produk', 'jasa'])
            ->whereIn('id', $request->cart_ids)
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Item keranjang tidak ditemukan atau sudah diproses.');
        }

        foreach ($carts as $cart) {
            Transaksi::where('customer_id', auth()->id())
                ->where('status_pembayaran', 'pending')
                ->where('metode_pembayaran', 'bayar_sekarang')
                ->where(function ($q) use ($cart) {
                    if ($cart->produk_id) {
                        $q->where('produk_id', $cart->produk_id);
                    } elseif ($cart->jasa_id) {
                        $q->where('jasa_id', $cart->jasa_id);
                    }
                })
                ->delete();
        }

        $invoiceNumber = 'INV-' . strtoupper(Str::random(8)) . '-' . now()->format('dmY');

        DB::beginTransaction();
        try {
            $itemDetails = [];
            $totalAmount = 0;

            foreach ($carts as $cart) {

                if ($cart->produk_id) {
                    $produk = Produk::lockForUpdate()->find($cart->produk_id);
                    if (!$produk || $produk->jumlah < $cart->jumlah) {
                        throw new \Exception(
                            "Stok produk \"" . ($produk->nama_produk ?? 'tidak diketahui') . "\" tidak mencukupi."
                        );
                    }
                    if ($request->metode_pembayaran === 'po') {
                        $produk->decrement('jumlah', $cart->jumlah);
                    }
                }

                $harga   = $cart->produk->harga ?? $cart->jasa->harga ?? 0;
                $total   = $harga * $cart->jumlah;
                $mitraId = $cart->produk->user_id ?? $cart->jasa->user_id ?? null;

                Transaksi::create([
                    'invoice_number'    => $invoiceNumber,
                    'customer_id'       => auth()->id(),
                    'mitra_id'          => $mitraId,
                    'produk_id'         => $cart->produk_id,
                    'jasa_id'           => $cart->jasa_id,
                    'jumlah'            => $cart->jumlah,
                    'harga'             => $harga,
                    'total'             => $total,
                    'alamat'            => $request->alamat,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'status_pembayaran' => 'pending',
                    'status_pengiriman' => 'menunggu',
                ]);

                // Notif ke mitra untuk pesanan PO baru
                if ($request->metode_pembayaran === 'po') {
                    $mitraUser = \App\Models\User::find($mitraId);
                    if ($mitraUser) {
                        $namaItem     = $cart->produk->nama_produk ?? $cart->jasa->nama_jasa ?? 'item';
                        $namaCustomer = auth()->user()->name;
                        $mitraUser->notify(new \App\Notifications\PesananBaruNotification(
                            $invoiceNumber,
                            $namaCustomer,
                            $namaItem
                        ));
                    }
                }

                $itemDetails[] = [
                    'id'       => $cart->produk_id ?? 'jasa-' . $cart->jasa_id,
                    'price'    => (int) $harga,
                    'quantity' => (int) $cart->jumlah,
                    'name'     => substr($cart->produk->nama_produk ?? $cart->jasa->nama_jasa ?? '-', 0, 50),
                ];

                $totalAmount += $total;

                if ($request->metode_pembayaran === 'po') {
                    $cart->delete();
                }
            }

            if ($request->metode_pembayaran === 'bayar_sekarang') {
                $params = [
                    'transaction_details' => [
                        'order_id'     => $invoiceNumber,
                        'gross_amount' => (int) $totalAmount,
                    ],
                    'item_details'     => $itemDetails,
                    'customer_details' => [
                        'first_name' => auth()->user()->name,
                        'email'      => auth()->user()->email,
                    ],
                    'callbacks' => [
                        'finish' => route('customer.invoice', $invoiceNumber),
                    ],
                ];

                $snapToken = Snap::getSnapToken($params);

                Transaksi::where('invoice_number', $invoiceNumber)
                    ->update(['snap_token' => $snapToken]);

                DB::commit();
                return redirect()->route('customer.checkout.payment', $invoiceNumber);
            }

            DB::commit();
            return redirect()->route('customer.invoice', $invoiceNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            Transaksi::where('invoice_number', $invoiceNumber)->delete();
            \Log::error("Checkout error [{$invoiceNumber}]: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function payment($invoice)
    {
        $transaksis = Transaksi::where('invoice_number', $invoice)->get();
        if ($transaksis->isEmpty()) {
            return redirect()->route('index');
        }

        $snapToken   = $transaksis->first()->snap_token;
        $totalAmount = $transaksis->sum('total');

        return view('customer.checkout_payment', compact('transaksis', 'snapToken', 'totalAmount', 'invoice'));
    }

    public function callback(Request $request)
    {
        $serverKey   = config('services.midtrans.server_key') ?? env('MIDTRANS_SERVER_KEY');
        $orderId     = $request->order_id;
        $statusCode  = $request->status_code;
        $grossAmount = $request->gross_amount;

        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signature !== $request->signature_key) {
            \Log::warning("Midtrans: Signature tidak valid untuk order {$orderId}");
            return response()->json(['status' => 'invalid signature'], 403);
        }

        $isSettled = $request->transaction_status === 'settlement' ||
                     ($request->transaction_status === 'capture' && $request->fraud_status === 'accept');

        if ($isSettled) {
            DB::transaction(function () use ($orderId) {
                $transaksis = Transaksi::where('invoice_number', $orderId)
                    ->where('status_pembayaran', '!=', 'Lunas')
                    ->get();

                foreach ($transaksis as $transaksi) {
                    if ($transaksi->metode_pembayaran !== 'po' && $transaksi->produk_id) {
                        $produk = Produk::find($transaksi->produk_id);
                        if ($produk && $produk->jumlah >= $transaksi->jumlah) {
                            $produk->decrement('jumlah', $transaksi->jumlah);
                        }
                    }

                    $transaksi->update([
                        'status_pembayaran' => 'Lunas',
                        'status_pengiriman' => 'Diproses',
                        'tanggal_bayar'     => now(),
                    ]);

                    // Notif ke mitra — pesanan bayar sekarang sudah lunas
                    $mitraUser = \App\Models\User::find($transaksi->mitra_id);
                    if ($mitraUser) {
                        $namaItem     = $transaksi->produk->nama_produk
                            ?? $transaksi->jasa->nama_jasa
                            ?? 'item';
                        $namaCustomer = \App\Models\User::find($transaksi->customer_id)?->name ?? 'Customer';
                        $mitraUser->notify(new \App\Notifications\PesananBaruNotification(
                            $transaksi->invoice_number,
                            $namaCustomer,
                            $namaItem
                        ));
                    }

                    $sudahAda = Pendapatan::where('transaksi_id', $transaksi->id)->exists();
                    if (!$sudahAda) {
                        Pendapatan::create([
                            'transaksi_id'   => $transaksi->id,
                            'mitra_id'       => $transaksi->mitra_id,
                            'total_diterima' => $transaksi->total,
                            'keterangan'     => 'Bayar Sekarang (Midtrans) - Invoice: ' . $transaksi->invoice_number,
                            'tanggal_masuk'  => now(),
                        ]);
                    }

                    if ($transaksi->metode_pembayaran === 'bayar_sekarang') {
                        Cart::where('user_id', $transaksi->customer_id)
                            ->where(function ($q) use ($transaksi) {
                                if ($transaksi->produk_id) {
                                    $q->where('produk_id', $transaksi->produk_id);
                                } elseif ($transaksi->jasa_id) {
                                    $q->where('jasa_id', $transaksi->jasa_id);
                                }
                            })
                            ->delete();
                    }
                }
            });

            \Log::info("Midtrans OK: Invoice {$orderId} → Lunas");

        } elseif (in_array($request->transaction_status, ['cancel', 'deny', 'expire'])) {
            Transaksi::where('invoice_number', $orderId)
                ->where('status_pembayaran', '!=', 'Lunas')
                ->update(['status_pembayaran' => 'Gagal']);

            \Log::info("Midtrans: Invoice {$orderId} → Gagal");
        }

        return response()->json(['status' => 'success']);
    }

    public function buyNowRedirect()
    {
        $cartId = session('buy_now_cart_id');

        if (!$cartId) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Sesi Buy Now habis, silakan coba lagi.');
        }

        $cart = Cart::where('id', $cartId)->where('user_id', auth()->id())->first();

        if (!$cart) {
            session()->forget('buy_now_cart_id');
            return redirect()->route('customer.cart.index')
                ->with('error', 'Item tidak ditemukan.');
        }

        session(['buynow_cart_ids' => [$cart->id]]);
        session()->forget('buy_now_cart_id');

        return redirect()->route('customer.checkout.buynow.confirm');
    }

    public function buyNowConfirm()
    {
        $cartIds = session('buynow_cart_ids');

        if (empty($cartIds)) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Sesi Buy Now habis, silakan coba lagi.');
        }

        $selectedCarts = Cart::with(['produk', 'jasa'])
            ->whereIn('id', $cartIds)
            ->where('user_id', auth()->id())
            ->get();

        if ($selectedCarts->isEmpty()) {
            session()->forget('buynow_cart_ids');
            return redirect()->route('customer.cart.index')
                ->with('error', 'Item tidak ditemukan. Mungkin sudah diproses sebelumnya.');
        }

        return view('customer.checkout-confirm', compact('selectedCarts'));
    }

    public function invoice($invoice)
    {
        $transaksis = Transaksi::where('invoice_number', $invoice)->get();

        if ($transaksis->isEmpty()) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'Invoice tidak ditemukan.');
        }

        return view('customer.invoice', compact('transaksis', 'invoice'));
    }
}
