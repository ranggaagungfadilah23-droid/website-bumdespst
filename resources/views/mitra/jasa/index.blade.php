@extends('theme.default')
@section('title', 'Kelola Layanan Jasa')

{{-- Pastikan kamu sudah memanggil CDN SweetAlert2 di layout theme.default atau di sini --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid px-6 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Kelola Layanan Jasa</h1>
            <p class="text-slate-500 text-sm mt-1">Daftar layanan jasa yang tampil di halaman publik BUMDes.</p>
        </div>

        <a href="{{ route('mitra.jasa.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold shadow-lg transition hover:scale-105 active:scale-95">
            <i class="fas fa-plus"></i>
            <span>Tambah Layanan</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-200 text-[11px] uppercase tracking-wider font-bold">
                        <th class="p-5">Gambar</th>
                        <th class="p-5">Nama Layanan</th>
                        <th class="p-5">Harga</th>
                        <th class="p-5">Satuan</th>
                        <th class="p-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse (Auth::user()->jasas as $item)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="p-5">
                            <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden border">
                                @if($item->gambar)
                                   <img src="{{ str_starts_with($item->gambar ?? '', 'http') ? $item->gambar : asset('storage/' . $item->gambar) }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-image"></i></div>
                                @endif
                            </div>
                        </td>
                        <td class="p-5 font-bold text-slate-700">{{ $item->nama_jasa }}</td>
                        <td class="p-5 font-semibold text-slate-600">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="p-5 text-slate-500">Per {{ $item->satuan }}</td>
                        <td class="p-5 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('mitra.jasa.edit', $item->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition"><i class="fas fa-edit text-xs"></i></a>

                                <form action="{{ route('mitra.jasa.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('{{ $item->id }}', '{{ $item->nama_jasa }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-20 text-center text-slate-400 italic">Belum ada layanan. Klik tombol tambah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, nama) {
        // Cek apakah Swal sudah terload
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 tidak ditemukan!');
            return;
        }

        Swal.fire({
            title: 'Hapus Layanan?',
            text: "Anda akan menghapus layanan '" + nama + "'. Aksi ini tidak bisa dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection
