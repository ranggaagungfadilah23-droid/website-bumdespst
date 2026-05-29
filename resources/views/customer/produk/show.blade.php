@extends('theme.default')
@section('title', $produk->nama_produk)

@section('content')
<div class="min-h-screen bg-[#f5f5f5]">

    {{-- BREADCRUMB --}}
    <div class="bg-white border-b border-slate-100 px-4 py-3">
        <div class="max-w-6xl mx-auto flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('customer.dashboard') }}" class="hover:text-orange-500 transition">Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="hover:text-orange-500 cursor-pointer transition">Produk</span>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600 font-medium truncate max-w-[200px]">{{ $produk->nama_produk }}</span>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-6">

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
        <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium">
            <i class="fas fa-check-circle text-green-500"></i>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            {{ session('error') }}
        </div>
        @endif

        {{-- MAIN CARD --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

                {{-- GAMBAR --}}
                <div class="relative bg-slate-50 flex items-center justify-center p-6 min-h-[400px]">
                    @if($produk->gambar)
                       <img src="{{ str_starts_with($produk->gambar ?? '', 'http') ? $produk->gambar : asset('storage/' . $produk->gambar) }}"
     alt="{{ $produk->nama_produk }}">
    <div class="w-full max-h-[420px] object-contain rounded-xl">
                             class="w-full max-h-[420px] object-contain rounded-xl">
                    @else
                        <div class="flex flex-col items-center justify-center text-slate-300 gap-3">
                            <i class="fas fa-box-open text-6xl"></i>
                            <span class="text-sm">Tidak ada gambar</span>
                        </div>
                    @endif

                    {{-- Badge Stok --}}
                    <div class="absolute top-4 left-4">
                        @if($produk->jumlah > 0)
                            <span class="bg-orange-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow">
                                Tersedia
                            </span>
                        @else
                            <span class="bg-slate-400 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow">
                                Habis
                            </span>
                        @endif
                    </div>
                </div>

                {{-- DETAIL --}}
                <div class="p-8 flex flex-col justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 leading-snug mb-3">{{ $produk->nama_produk }}</h1>

                        <div class="bg-orange-50 border border-orange-100 rounded-xl px-5 py-4 mb-5">
                            <p class="text-xs text-slate-400 mb-1">Harga produk</p>
                            <p class="text-3xl font-black text-orange-500">
                                Rp {{ number_format($produk->harga, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Info Mitra --}}
                        <div class="flex items-center gap-3 mb-5 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center font-black text-sm">
                                {{ strtoupper(substr($produk->user->name ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Penjual</p>
                                <p class="text-sm font-bold text-slate-700">{{ $produk->user->name ?? 'Mitra BUMDes' }}</p>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-6">
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Deskripsi Produk</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $produk->deskripsi }}</p>
                        </div>
                    </div>

                    {{-- FORM ORDER --}}
                    <div>
                        <div class="mb-5">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah</p>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center border-2 border-slate-200 rounded-xl overflow-hidden">
                                    <button type="button" id="btnMinus" onclick="changeQty(-1)" class="w-11 h-11 flex items-center justify-center hover:bg-orange-50 text-slate-600 font-bold transition text-lg">−</button>
                                    <input type="number" id="jumlahDisplay" value="1" min="1" max="{{ $produk->jumlah }}" class="w-14 text-center font-bold text-slate-800 border-x-2 border-slate-200 h-11 outline-none text-sm" readonly>
                                    <button type="button" id="btnPlus" onclick="changeQty(1)" class="w-11 h-11 flex items-center justify-center hover:bg-orange-50 text-slate-600 font-bold transition text-lg">+</button>
                                </div>
                                <span class="text-xs text-slate-400">Stok: <strong>{{ $produk->jumlah }}</strong></span>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form action="{{ route('customer.cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                <input type="hidden" name="jumlah" id="jumlahKeranjang" value="1">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 border-2 border-orange-500 text-orange-500 bg-orange-50 py-3.5 rounded-xl font-bold text-sm hover:bg-orange-100 transition-all">
                                    <i class="fas fa-shopping-cart"></i> Keranjang
                                </button>
                            </form>

                            <form action="{{ route('customer.cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="buy_now" value="1">
                                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                <input type="hidden" name="jumlah" id="jumlahBeli" value="1">
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-orange-500 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-orange-600 transition-all shadow-lg shadow-orange-200">
                                    <i class="fas fa-bolt"></i> Beli Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function changeQty(delta) {
        const display = document.getElementById('jumlahDisplay');
        const inputKeranjang = document.getElementById('jumlahKeranjang');
        const inputBeli = document.getElementById('jumlahBeli');
        const maxQty = parseInt(display.getAttribute('max'));

        let val = parseInt(display.value) + delta;
        if (val < 1) val = 1;
        if (val > maxQty) val = maxQty;

        display.value = val;
        inputKeranjang.value = val;
        inputBeli.value = val;
    }
</script>
@endsection
