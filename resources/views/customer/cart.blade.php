@extends('theme.customer')
@section('content')

<main class="max-w-7xl mx-auto px-6 py-12">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800">Keranjang Saya</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola item yang ingin kamu pesan</p>
        </div>
        <a href="{{ route('customer.dashboard') }}"
           class="flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-700 transition text-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Belanja
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('customer.checkout.confirm') }}" method="POST" id="cartForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Kiri: Daftar Item --}}
            <div class="lg:col-span-8 space-y-4">
                @forelse($carts as $cart)
                    @if(!$cart->produk && !$cart->jasa)
                        @continue
                    @endif

                    @php
                        $nama     = optional($cart->produk)->nama_produk ?? optional($cart->jasa)->nama_jasa ?? '-';
                        $harga    = optional($cart->produk)->harga ?? optional($cart->jasa)->harga ?? 0;
                        $gambar   = optional($cart->produk)->gambar ?? optional($cart->jasa)->gambar ?? '';
                        $tipe     = $cart->produk ? 'Produk' : 'Jasa';
                        $subtotal = $harga * $cart->jumlah;
                    @endphp

                    <div class="group flex items-center gap-5 p-5 bg-white border border-slate-100 rounded-2xl shadow-sm hover:border-blue-300 hover:shadow-md transition-all duration-200">

                        <input type="checkbox"
                               name="cart_ids[]"
                               value="{{ $cart->id }}"
                               data-price="{{ $subtotal }}"
                               class="cart-checkbox w-6 h-6 text-blue-600 rounded-lg border-slate-300 focus:ring-blue-500 cursor-pointer shrink-0 transition">

                        <div class="w-20 h-20 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-50">
                            <img src="{{ $gambar ? (str_starts_with($gambar, 'http') ? $gambar : asset('storage/' . $gambar)) : asset('images/placeholder.png') }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                 onerror="this.src='{{ asset('images/placeholder.png') }}'">
                        </div>

                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-slate-800 truncate">{{ $nama }}</h3>
                            </div>
                            <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ $tipe }}</span>
                            <p class="text-lg font-black text-rose-500 mt-1">
                                Rp {{ number_format($harga, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Kuantitas</div>
                            <div class="text-sm font-black text-slate-700 bg-slate-100 px-3 py-1 rounded-lg inline-block">
                                {{ $cart->jumlah }}
                            </div>
                            <div class="text-xs font-medium text-slate-500 mt-2">
                                Total: Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-basket text-3xl text-slate-300"></i>
                        </div>
                        <p class="text-slate-500 font-semibold text-lg">Wah, keranjangmu masih kosong.</p>
                        <p class="text-slate-400 text-sm mb-6">Yuk, lihat-lihat produk dan jasa menarik di BUMDes!</p>
                        <a href="{{ route('customer.dashboard') }}"
                           class="inline-flex items-center bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                            Mulai Belanja
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Kanan: Ringkasan --}}
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 sticky top-24">
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
                        <i class="fas fa-receipt text-blue-500"></i> Ringkasan
                    </h3>

                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between items-center py-2">
                            <span class="text-slate-500 text-sm">Item dipilih</span>
                            <span id="count-display" class="font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-md text-xs">0 item</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-slate-500 text-sm pb-1">Total Harga</span>
                            <span id="total-harga-display" class="text-2xl font-black text-blue-600">Rp 0</span>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl mb-6">
                        <p id="hint-text" class="text-xs text-center text-slate-500 leading-relaxed font-medium">
                            Centang item yang ingin kamu proses pembayarannya.
                        </p>
                    </div>

                    <button type="button" id="checkoutBtn"
                            onclick="submitCart(this)"
                            disabled
                            class="w-full py-4 rounded-2xl font-extrabold text-lg transition-all duration-300
                                   bg-slate-200 text-slate-400 cursor-not-allowed shadow-inner">
                        Lanjut ke Konfirmasi
                        <i class="fas fa-chevron-right ml-2 text-sm"></i>
                    </button>

                    <p class="mt-4 text-[10px] text-center text-slate-400">
                        *Langkah berikutnya: Pilih metode pembayaran dan alamat.
                    </p>
                </div>
            </div>

        </div>
    </form>
</main>

<script>
    const checkboxes   = document.querySelectorAll('.cart-checkbox');
    const totalDisplay = document.getElementById('total-harga-display');
    const countDisplay = document.getElementById('count-display');
    const checkoutBtn  = document.getElementById('checkoutBtn');
    const hintText     = document.getElementById('hint-text');

    function updateSummary() {
        let total = 0, count = 0;
        document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
            total += parseInt(cb.getAttribute('data-price')) || 0;
            count++;
        });

        totalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
        countDisplay.innerText = count + ' item';

        if (count > 0) {
            checkoutBtn.disabled  = false;
            checkoutBtn.className = 'w-full py-4 rounded-2xl font-extrabold text-lg transition-all duration-300 bg-blue-600 text-white hover:bg-blue-700 active:scale-95 cursor-pointer shadow-lg shadow-blue-200';
            hintText.innerHTML    = `<i class="fas fa-check-circle text-blue-500 mr-1"></i> <strong>${count} item</strong> siap diproses`;
        } else {
            checkoutBtn.disabled  = true;
            checkoutBtn.className = 'w-full py-4 rounded-2xl font-extrabold text-lg transition-all duration-300 bg-slate-200 text-slate-400 cursor-not-allowed';
            hintText.textContent  = 'Centang item yang ingin kamu proses pembayarannya.';
        }
    }

    function submitCart(btn) {
        const checked = document.querySelectorAll('.cart-checkbox:checked');
        if (checked.length === 0) return;

        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Menyiapkan...';
        btn.classList.add('opacity-80');
        btn.disabled  = true;

        document.getElementById('cartForm').submit();
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateSummary));
    window.addEventListener('load', updateSummary);
</script>

@endsection
