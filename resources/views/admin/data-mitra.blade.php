@extends('theme.default')

@section('title', 'Data Mitra - BUMDes Patimban')

@include('admin.partials.styles')

@push('navbar-search')
<div class="hidden md:flex items-center gap-3">
    <p class="text-sm font-bold text-slate-600">Cari:</p>
    <form action="{{ route('global.search') }}" method="GET" class="flex items-center">
        <input type="text" name="q" placeholder="Cari nama mitra..." value="{{ request('q') }}"
               class="border border-slate-200 rounded-l-xl px-3 py-1.5 text-sm outline-none focus:border-blue-500 w-48 lg:w-64">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 text-sm rounded-r-xl font-bold hover:bg-blue-700 transition">CARI</button>
    </form>
</div>
@endpush

@section('content')
<div class="admin-page">

    <div class="admin-mobile-search">
        <form action="{{ route('global.search') }}" method="GET" class="admin-search-form">
            <input type="text" name="q" placeholder="Cari nama mitra..." value="{{ request('q') }}" class="admin-search-input">
            <button type="submit" class="admin-search-btn">CARI</button>
        </form>
    </div>

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Data Mitra BUMDes</h1>
            <p class="admin-page-subtitle">Kelola daftar warga yang telah resmi menjadi Mitra.</p>
        </div>
        <span class="admin-page-badge admin-page-badge--blue">{{ $mitras->count() }} Mitra</span>
    </div>

    @if (session('success'))
        <div class="admin-alert admin-alert--success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="admin-card">
        <div class="admin-table-wrap admin-table-wrap--cards">
            <table class="admin-table admin-table--responsive">
                <thead>
                    <tr>
                        <th>Nama Pemilik</th>
                        <th>Nama Usaha</th>
                        <th>Kategori</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mitras as $mitra)
                    <tr>
                        <td data-label="Nama Pemilik">
                            <p class="admin-user-name">{{ $mitra->nama_pemilik }}</p>
                            <p class="admin-user-sub">{{ $mitra->user->email ?? '-' }}</p>
                        </td>
                        <td data-label="Nama Usaha">
                            <span class="admin-user-name">{{ $mitra->nama_usaha }}</span>
                        </td>
                        <td data-label="Kategori">
                            @php $jenis = strtolower($mitra->jenis_usaha ?? ''); @endphp
                            <span class="admin-badge {{ $jenis === 'jasa' ? 'admin-badge--jasa' : 'admin-badge--produk' }}">
                                {{ $mitra->jenis_usaha }}
                            </span>
                        </td>
                        <td data-label="Aksi" class="text-center">
                     <form action="{{ route('admin.mitra.destroy', $mitra->user_id) }}" method="POST"  onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini? Data tidak bisa dikembalikan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm" title="Hapus Mitra">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr class="admin-table-empty">
                        <td colspan="4">
                            <div class="admin-empty">
                                <i class="fas fa-box-open"></i>
                                <p class="admin-empty-title">Belum Ada Mitra</p>
                                <p>Belum ada mitra yang disetujui saat ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
