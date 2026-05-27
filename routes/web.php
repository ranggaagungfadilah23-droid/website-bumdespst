<?php

use App\Http\Controllers\{
    ProfileController, GoogleController, ProductController, JasaController,
    AdminController, KepalaBumdesController, PencarianController, SuratController,
    MitraController, CheckoutController, CustomerController, NotificationsController,
    BagihasilController
};
use App\Http\Controllers\Mitra\PesananController;
use App\Http\Controllers\Mitra\PendapatanController;
use App\Http\Controllers\Mitra\LaporanController;
use App\Http\Controllers\Customer\PesananController as CustomerPesananController;
use Illuminate\Support\Facades\{Route, Auth};
use App\Http\Controllers\KepalaBumdes\LaporanBulananController;
use App\Http\Controllers\KepalaBumdes\MonitoringKeuanganController;
use App\Http\Controllers\Customer\UlasanController;
use App\Http\Controllers\Mitra\UlasanMitraController;
use Illuminate\Support\Facades\Artisan;

// =============================================================
// --- 1. PUBLIC AREA ---
// =============================================================
Route::get('/', [JasaController::class, 'landingPage'])->name('index');
Route::get('/produk', [ProductController::class, 'index'])->name('produk.index');
Route::get('/verifikasi/surat/{id}', [SuratController::class, 'verifikasi'])->name('verifikasi.surat');
Route::get('/panduan', fn() => view('auth.panduan-pendaftaran'))->name('panduan');

Route::post('/midtrans/callback', [CheckoutController::class, 'callback']);
Route::post('/midtrans/webhook',  [CheckoutController::class, 'callback']);

// API cek status pembayaran (untuk polling di frontend)
Route::get('/api/check-payment/{invoice}', function ($invoice) {
    $transaksi = \App\Models\Transaksi::where('invoice_number', $invoice)->first();
    return response()->json([
        'status' => $transaksi->status_pembayaran ?? 'pending'
    ]);
})->middleware(['auth']);

// --- GOOGLE OAUTH ---
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');


// =============================================================
// --- 2. GUEST AREA (SUDAH DIPERBAIKI SINTAKS ROUTE::VIEW) ---
// =============================================================
Route::middleware('guest')->group(function () {
    Route::view('/register/mitra', 'auth.register-mitra')->name('register.mitra');
    Route::view('/register/pelanggan', 'auth.register-pelanggan')->name('register.pelanggan');
    Route::post('/register/mitra', [GoogleController::class, 'storeMitra'])->name('mitra.store');
    Route::post('/register/pelanggan', [GoogleController::class, 'storePelanggan'])->name('pelanggan.store');
});


// =============================================================
// --- 3. AUTH AREA ---
// =============================================================
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'mitra') return redirect()->route('mitra.dashboard');
        if ($user->role === 'kepala-bumdes') return redirect()->route('kepala-bumdes.dashboard');
        return redirect()->route('customer.dashboard');
    })->name('dashboard');

    Route::get('/cari', [PencarianController::class, 'index'])->name('global.search');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/send-reset-link', [ProfileController::class, 'sendResetLink'])->name('profile.send-reset-link');

    // --- NOTIFICATIONS ---
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{id}', function ($id) {
        auth()->user()->notifications()->findOrFail($id)->delete();
        return response()->json(['success' => true]);
    })->name('notifications.destroy');
    Route::delete('/notifications', function () {
        auth()->user()->notifications()->delete();
        return back();
    })->name('notifications.destroyAll');

    // --- ADMIN ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard', [
            'pengajuans' => \App\Models\User::where('role', 'mitra')->where('status', 'pending')->get()
        ]))->name('dashboard');

        Route::get('/pengajuan', [AdminController::class, 'pengajuan'])->name('pengajuan');
        Route::post('/approve/{id}', [AdminController::class, 'approve'])->name('approve');
        Route::post('/reject/{id}', [AdminController::class, 'reject'])->name('reject');
        Route::get('/data-mitra', [AdminController::class, 'dataMitra'])->name('mitra.index');
        Route::delete('/data-mitra/{id}', [AdminController::class, 'destroyMitra'])->name('mitra.destroy');

        // Bagi Hasil
        Route::get('/bagihasil', [BagihasilController::class, 'index'])->name('bagihasil');
        Route::post('/bagihasil/store', [BagihasilController::class, 'store'])->name('bagihasil.store');
        Route::patch('/bagihasil/confirm', [BagihasilController::class, 'confirm'])->name('bagihasil.confirm');
        Route::get('/bagihasil/omzet/{mitra_id}', [BagihasilController::class, 'getOmzet'])->name('bagihasil.omzet');

        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        Route::post('/laporan/kirim', [AdminController::class, 'kirimLaporan'])->name('laporan.kirim');
      // ✅ BENAR
Route::get('/laporan/pdf', [AdminController::class, 'laporanPdf'])->name('laporan.pdf');
        Route::get('/histori', [AdminController::class, 'histori'])->name('histori');
    });

    // --- KEPALA BUMDES ---
    Route::middleware(['role:kepala-bumdes'])->prefix('kepala-bumdes')->name('kepala-bumdes.')->group(function () {
        Route::get('/dashboard', [KepalaBumdesController::class, 'dashboard'])->name('dashboard');
        Route::get('/pengajuan', [KepalaBumdesController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/data-mitra', [KepalaBumdesController::class, 'dataMitra'])->name('mitra.index');
        Route::get('/pengajuan/{id}/preview', [SuratController::class, 'apiPreview'])->name('preview-api');
        Route::patch('/pengajuan/{id}/setujui', [SuratController::class, 'setujuiMitra'])->name('setujui');
        Route::post('/pengajuan/{id}/reject', [KepalaBumdesController::class, 'reject'])->name('reject');

        Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporan-bulanan');
        Route::get('/monitoring-keuangan', [App\Http\Controllers\KepalaBumdes\InputKeuanganController::class, 'index'])->name('monitoring-keuangan');
        Route::get('/monitoring-keuangan/export', [App\Http\Controllers\KepalaBumdes\InputKeuanganController::class, 'export'])->name('monitoring-keuangan.export');

        // Rute Operasional Input Keuangan (Terintegrasi Penuh)
        Route::post('/simpan-saldo-awal', [App\Http\Controllers\KepalaBumdes\InputKeuanganController::class, 'simpanSaldoAwal'])->name('simpan-saldo-awal');
        Route::delete('/hapus-saldo-awal/{id}', [App\Http\Controllers\KepalaBumdes\InputKeuanganController::class, 'hapusSaldoAwal'])->name('hapus-saldo-awal');
        Route::post('/simpan-pengeluaran', [App\Http\Controllers\KepalaBumdes\InputKeuanganController::class, 'simpanPengeluaran'])->name('simpan-pengeluaran');
        Route::delete('/hapus-pengeluaran/{id}', [App\Http\Controllers\KepalaBumdes\InputKeuanganController::class, 'hapusPengeluaran'])->name('hapus-pengeluaran');
    });

    // --- MITRA ---
    Route::prefix('mitra')->group(function () {
        Route::get('/menunggu', [MitraController::class, 'cekStatus'])->name('mitra.menunggu');

        Route::middleware(['role:mitra', 'mitra_check'])->group(function () {
            Route::get('/dashboard', [MitraController::class, 'dashboard'])->name('mitra.dashboard');
            Route::get('/jasa/dashboard', [App\Http\Controllers\Mitra\Jasa\DashboardController::class, 'dashboard'])->name('mitra.jasa.dashboard');
            Route::get('/produk/dashboard', [App\Http\Controllers\Mitra\Produk\DashboardController::class, 'dashboard'])->name('mitra.produk.dashboard');

            Route::get('/kelola-usaha', function () {
                if (Auth::user()->mitra->jenis_usaha == 'Jasa') {
                    return view('mitra.jasa.index');
                } else {
                    $produks = \App\Models\Produk::where('user_id', Auth::id())->latest()->get();
                    return view('mitra.produk.index', compact('produks'));
                }
            })->name('mitra.kelola');

            // Pesanan Mitra
            Route::get('/pesanan', [PesananController::class, 'index'])->name('mitra.pesanan.index');
            Route::patch('/pesanan/{id}/status', [PesananController::class, 'updateStatus'])->name('mitra.pesanan.update-status');
            Route::post('/pesanan/{id}/konfirmasi', [PesananController::class, 'konfirmasiLunas'])->name('mitra.pesanan.konfirmasi-lunas');
            Route::get('/pesanan/{id}/cetak', [PesananController::class, 'cetakInvoice'])->name('mitra.pesanan.cetak-invoice');
// SESUDAH
Route::resource('produk', ProductController::class)
    ->names('mitra.produk')
    ->except(['show']);

            Route::resource('jasa', JasaController::class)->names('mitra.jasa');

            // Laporan
            Route::prefix('laporan')->name('mitra.laporan.')->group(function () {
                Route::get('/', [LaporanController::class, 'index'])->name('index');
                Route::get('/pdf', [LaporanController::class, 'pdf'])->name('pdf');
                Route::post('/kirim', [LaporanController::class, 'kirimKeAdmin'])->name('kirim');
            });

            // Pendapatan + Ulasan Mitra
            Route::prefix('pendapatan')->name('mitra.pendapatan.')->group(function () {
                Route::get('/', [PendapatanController::class, 'index'])->name('index');
                Route::get('/laporan', [PendapatanController::class, 'laporan'])->name('laporan');
                Route::get('/laporan/pdf_rekap', [PendapatanController::class, 'laporanPdf'])->name('laporan.pdf_rekap');
                Route::post('/kirim', [BagihasilController::class, 'store'])->name('kirim');

                // Ulasan masuk ke mitra
                Route::get('/ulasan', [UlasanMitraController::class, 'index'])->name('ulasan.index');
                Route::post('/ulasan/{ulasan}/balas', [UlasanMitraController::class, 'balas'])->name('ulasan.balas');
            });
        });
    });

    // --- CUSTOMER (SUDAH DIPERBAIKI PARSEERROR KURUNG) ---
    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            return view('customer.dashboard', [
                'produks' => \App\Models\Produk::all(),
                'jasas'   => \App\Models\Jasa::all(),
            ]);
        })->name('dashboard');

        // Cart
     // Cart
// Cari bagian ini di dalam grup customer
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\Customer\CartController::class, 'index'])->name('index');
    Route::post('/add', [App\Http\Controllers\Customer\CartController::class, 'add'])->name('add');
    Route::post('/add-jasa/{id}', [App\Http\Controllers\Customer\CartController::class, 'addJasa'])->name('add.jasa');
    Route::post('/clear', [App\Http\Controllers\Customer\CartController::class, 'clear'])->name('clear');
});

        // Detail Produk & Jasa
        Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.show');
        Route::get('/jasa/{id}', [JasaController::class, 'show'])->name('jasa.show');

        // Checkout
        Route::match(['get', 'post'], '/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
        Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/checkout/payment/{invoice}', [CheckoutController::class, 'payment'])->name('checkout.payment');

        // Buy Now
        Route::get('/checkout/buynow', [CheckoutController::class, 'buyNowRedirect'])->name('checkout.buynow');
        Route::get('/checkout/buynow/confirm', [CheckoutController::class, 'buyNowConfirm'])->name('checkout.buynow.confirm');

        Route::get('/checkout/finish', function() {
    return redirect()->route('customer.pesanan')
        ->with('success', 'Pembayaran berhasil!');
})->name('checkout.finish');

        // Invoice
        Route::get('/checkout/invoice/{invoice}', [CheckoutController::class, 'invoice'])->name('invoice');

        // Pesanan Customer
        Route::get('/pesanan', [CustomerPesananController::class, 'index'])->name('pesanan');
        Route::get('/pesanan/pending', [CustomerPesananController::class, 'pending'])->name('pesanan.pending');
        Route::get('/pesanan/dikemas', [CustomerPesananController::class, 'dikemas'])->name('pesanan.dikemas');
        Route::get('/pesanan/dikirim', [CustomerPesananController::class, 'dikirim'])->name('pesanan.dikirim');
        Route::get('/pesanan/selesai', [CustomerPesananController::class, 'selesai'])->name('pesanan.selesai');
        Route::post('/pesanan/{invoice}/konfirmasi-diterima', [CustomerPesananController::class, 'konfirmasiDiterima'])
            ->name('pesanan.konfirmasi-diterima');

        // Ulasan Customer
        Route::post('/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
        Route::get('/ulasan', [UlasanController::class, 'index'])->name('ulasan.index');
    });

});

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Done: ' . Artisan::output();
});

Route::get('/clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    return 'Done: ' . now();
});

require base_path('routes/auth.php');

Route::get('/force-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login')->with('success', 'Anda telah berhasil logout.');
});
