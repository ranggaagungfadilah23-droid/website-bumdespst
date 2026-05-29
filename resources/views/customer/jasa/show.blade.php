@extends('theme.default')
@section('title', $jasa->nama_jasa)

@section('content')
<div class="min-h-screen bg-[#f5f5f5]">

    {{-- BREADCRUMB --}}
    <div class="bg-white border-b border-slate-100 px-4 py-3">
        <div class="max-w-6xl mx-auto flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('customer.dashboard') }}" class="hover:text-orange-500 transition">Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="hover:text-orange-500 cursor-pointer transition">Layanan Jasa</span>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600 font-medium truncate max-w-[200px]">{{ $jasa->nama_jasa }}</span>
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
                    @if($jasa->gambar)
                        <img src="{{ str_starts_with($jasa->gambar, 'http') ? $jasa->gambar : asset('storage/' . $jasa->gambar) }}"
                             alt="{{ $jasa->nama_jasa }}"
                             class="w-full max-h-[420px] object-contain rounded-xl">
                    @else
                        <div class="flex flex-col items-center justify-center text-slate-300 gap-3">
                            <i class="fas fa-concierge-bell text-6xl"></i>
                            <span class="text-sm">Tidak ada gambar</span>
                        </div>
                    @endif

                    {{-- Badge Satuan --}}
                    <div class="absolute top-4 left-4">
                        <span class="bg-orange-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow">
                            {{ $jasa->satuan }}
                        </span>
                    </div>
                </div>

                {{-- DETAIL --}}
                <div class="p-8 flex flex-col justify-between">
                    <div>
                        {{-- Nama --}}
                        <h1 class="text-2xl font-bold text-slate-800 leading-snug mb-3">
                            {{ $jasa->nama_jasa }}
                        </h1>

                        {{-- Harga --}}
                        <div class="bg-orange-50 border border-orange-100 rounded-xl px-5 py-4 mb-5">
                            <p class="text-xs text-slate-400 mb-1">Harga layanan</p>
                            <p class="text-3xl font-black text-orange-500">
                                Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                                <span class="text-sm font-semibold text-slate-400">/ {{ $jasa->satuan }}</span>
                            </p>
                        </div>

                        {{-- Info Mitra --}}
                        <div class="flex items-center gap-3 mb-5 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center font-black text-sm">
                                {{ strtoupper(substr($jasa->user->name ?? 'M', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Penyedia Layanan</p>
                                <p class="text-sm font-bold text-slate-700">{{ $jasa->user->name ?? 'Mitra BUMDes' }}</p>
                            </div>
                            <div class="ml-auto flex items-center gap-1 text-yellow-400 text-xs font-bold">
                                <i class="fas fa-star"></i> 5.0
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-6">
                            <h3 class="text-sm font-black text-slate-700 uppercase tracking-wider mb-2">
                                Deskripsi Layanan
                            </h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                {{ $jasa->deskripsi }}
                            </p>
                        </div>
                    </div>

                    {{-- FORM ORDER --}}
                    <form action="{{ route('customer.cart.add.jasa', $jasa->id) }}" method="POST">
                        @csrf

                        {{-- Jumlah --}}
                        <div class="mb-5">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah</p>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center border-2 border-slate-200 rounded-xl overflow-hidden">
                                    <button type="button" onclick="changeQty(-1)"
                                            class="w-11 h-11 flex items-center justify-center hover:bg-orange-50 hover:text-orange-500 text-slate-600 font-bold transition text-lg">
                                        −
                                    </button>
                                    <input type="number" name="jumlah" id="jumlahInput" value="1" min="1"
                                           class="w-14 text-center font-bold text-slate-800 border-x-2 border-slate-200 h-11 outline-none text-sm" readonly>
                                    <button type="button" onclick="changeQty(1)"
                                            class="w-11 h-11 flex items-center justify-center hover:bg-orange-50 hover:text-orange-500 text-slate-600 font-bold transition text-lg">
                                        +
                                    </button>
                                </div>
                                <span class="text-xs text-slate-400">
                                    <i class="fas fa-info-circle mr-1 text-orange-400"></i>
                                    Satuan: <strong class="text-slate-600">{{ $jasa->satuan }}</strong>
                                </span>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex gap-3">
                            <button type="submit"
                                    class="flex-1 flex items-center justify-center gap-2 border-2 border-orange-500 text-orange-500 bg-orange-50 py-3.5 rounded-xl font-bold text-sm hover:bg-orange-100 transition-all">
                                <i class="fas fa-shopping-cart"></i>
                                Keranjang
                            </button>
                            <button type="submit" name="action" value="buy"
                                    class="flex-1 flex items-center justify-center gap-2 bg-orange-500 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-orange-600 transition-all shadow-lg shadow-orange-200">
                                <i class="fas fa-bolt"></i>
                                Sewa Sekarang
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        {{-- JAMINAN --}}
        <div class="mt-4 bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-4">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="flex flex-col items-center gap-1.5">
                    <i class="fas fa-shield-alt text-orange-500 text-lg"></i>
                    <p class="text-[11px] font-bold text-slate-600">Terpercaya</p>
                    <p class="text-[10px] text-slate-400">Mitra terverifikasi BUMDes</p>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                    <i class="fas fa-headset text-orange-500 text-lg"></i>
                    <p class="text-[11px] font-bold text-slate-600">Bantuan 24 Jam</p>
                    <p class="text-[10px] text-slate-400">Siap membantu kapanpun</p>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                    <i class="fas fa-medal text-orange-500 text-lg"></i>
                    <p class="text-[11px] font-bold text-slate-600">Kualitas Terjamin</p>
                    <p class="text-[10px] text-slate-400">Layanan terbaik desa</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function changeQty(delta) {
        let input = document.getElementById('jumlahInput');
        let val = parseInt(input.value) + delta;
        if (val >= 1) input.value = val;
    }
</script>
@endsection
