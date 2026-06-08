@extends('theme.default')
@section('title', 'Data Mitra - BUMDes Patimban')
@push('navbar-search')
<div class="flex items-center gap-3">
    <p class="text-sm font-bold text-slate-600 hidden md:block">Cari:</p>
    <form action="{{ route('global.search') }}" method="GET" class="flex items-center">
        <input type="text" name="q" placeholder="Cari fitur atau nama mitra..." value="{{ request('q') }}"
               class="border border-slate-200 rounded-l-xl px-3 py-1.5 text-sm outline-none focus:border-blue-500 w-64">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 text-sm rounded-r-xl font-bold hover:bg-blue-700 transition">
            CARI
        </button>
    </form>
</div>
@endpush

@push('navbar-separator')
    <div class="hidden md:block w-px h-8 bg-slate-200"></div>
@endpush

@section('content')
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Mitra BUMDes</h1>
                <p class="text-gray-500 text-sm">Kelola daftar warga yang telah resmi menjadi Mitra.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Nama Pemilik</th>
                        <th class="p-4 font-semibold">Nama Usaha</th>
                        <th class="p-4 font-semibold">Kategori</th>
                        <th class="p-4 font-semibold text-center">Aksi</th>
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
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.mitra.destroy', $mitra->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini? Data tidak bisa dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white px-3 py-2 rounded-lg transition-colors" title="Hapus Mitra">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400">
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
    </div>
@endsection
