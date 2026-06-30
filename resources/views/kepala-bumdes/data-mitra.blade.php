@extends('theme.default')
@section('title', 'Data Mitra - BUMDes Patimban')

@push('navbar-search')
{{-- HANYA tampil di laptop/tablet ke atas --}}
<div class="hidden md:flex items-center">
    <form action="{{ route('global.search') }}" method="GET" class="flex items-center w-full md:w-auto">
        <input type="text" name="q" placeholder="Cari..." value="{{ request('q') }}"
               class="border border-slate-200 rounded-l-xl px-3 py-1.5 text-sm outline-none focus:border-emerald-500 w-64">
        <button type="submit" class="bg-emerald-600 text-white px-4 py-1.5 text-sm rounded-r-xl font-bold hover:bg-emerald-700 transition shrink-0">
            CARI
        </button>
    </form>
</div>
@endpush

@push('navbar-separator')
    <div class="hidden md:block w-px h-8 bg-slate-200"></div>
@endpush

@section('content')
    <div class="p-3 sm:p-6">

        {{-- HANYA tampil di mobile, taruh di bawah header sebelum judul --}}
        <div class="md:hidden mb-4">
            <form action="{{ route('global.search') }}" method="GET" class="flex items-center w-full">
                <input type="text" name="q" placeholder="Cari mitra..." value="{{ request('q') }}"
                       class="border border-slate-200 rounded-l-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 w-full">
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 text-sm rounded-r-xl font-bold hover:bg-emerald-700 transition shrink-0">
                    CARI
                </button>
            </form>
        </div>

        <div class="flex justify-between items-center mb-4 sm:mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Data Mitra BUMDes</h1>
                <p class="text-gray-500 text-xs sm:text-sm">Kelola daftar warga yang telah resmi menjadi Mitra.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Tablet ke atas: tabel penuh --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="p-4 font-semibold">Nama Pemilik</th>
                            <th class="p-4 font-semibold">Nama Usaha</th>
                            <th class="p-4 font-semibold">Kategori</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($mitras as $mitra)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="font-bold text-gray-800">{{ $mitra->nama_pemilik }}</div>
                                    <div class="text-gray-500 text-xs">{{ $mitra->user->email ?? '-' }}</div>
                                </td>
                                <td class="p-4 font-medium text-gray-700">{{ $mitra->nama_usaha }}</td>
                                <td class="p-4">
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ $mitra->jenis_usaha }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                                        <p>Belum ada Mitra yang disetujui saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: card list --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse ($mitras as $mitra)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-gray-800 text-sm truncate">{{ $mitra->nama_pemilik }}</div>
                                <div class="text-gray-500 text-xs truncate">{{ $mitra->user->email ?? '-' }}</div>
                            </div>
                            <span class="shrink-0 bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full text-[10px] font-semibold whitespace-nowrap">
                                {{ $mitra->jenis_usaha }}
                            </span>
                        </div>
                        <div class="mt-2 text-xs text-gray-600">
                            <span class="text-gray-400">Usaha:</span> <span class="font-medium text-gray-700">{{ $mitra->nama_usaha }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm">Belum ada Mitra yang disetujui saat ini.</p>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
@endsection