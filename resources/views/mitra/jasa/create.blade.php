{{-- resources/views/mitra/jasa/create.blade.php --}}
@extends('theme.default')
@section('title', 'Buat Layanan Jasa - BUMDes Patimban')

@section('content')
<div class="container-fluid px-6 py-8">
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-2xl font-bold text-slate-800">Tambah Layanan Baru</h3>
            <a href="{{ route('mitra.kelola') }}" class="text-slate-400 hover:text-slate-600 font-medium text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        {{-- Tampilkan Error Validasi --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <p class="text-sm font-bold text-red-600 mb-1">Terdapat kesalahan:</p>
                <ul class="text-sm text-red-500 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('mitra.jasa.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Nama Layanan --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Nama Layanan</label>
                <input
                    type="text"
                    name="nama_jasa"
                    value="{{ old('nama_jasa') }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    placeholder="Contoh: Jasa Cuci Mobil"
                    required
                >
            </div>

            {{-- Harga & Satuan --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Harga (Rp)</label>
                    <input
                        type="number"
                        name="harga"
                        value="{{ old('harga') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="0"
                        min="0"
                        required
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Satuan</label>
                    <select
                        name="satuan"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        required
                    >
                        <option value="Layanan" {{ old('satuan') == 'Layanan' ? 'selected' : '' }}>Per Layanan</option>
                        <option value="Jam"     {{ old('satuan') == 'Jam'     ? 'selected' : '' }}>Per Jam</option>
                        <option value="Hari"    {{ old('satuan') == 'Hari'    ? 'selected' : '' }}>Per Hari</option>
                    </select>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Deskripsi</label>
                <textarea
                    name="deskripsi"
                    rows="4"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    placeholder="Jelaskan detail layanan yang diberikan..."
                    required
                >{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Foto Portofolio --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Foto Portofolio</label>
                <input
                    type="file"
                    name="gambar"
                    accept="image/jpeg,image/png,image/jpg"
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                >
                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG. Maks 2MB. (Opsional)</p>
            </div>

            {{-- Tombol Submit --}}
            <button
                type="submit"
                class="w-full py-3 mt-2 rounded-xl bg-emerald-600 text-white font-bold text-sm shadow-lg hover:bg-emerald-700 active:scale-95 transition-all duration-150"
            >
                <i class="fas fa-save mr-2"></i> Simpan Layanan
            </button>
        </form>

    </div>
</div>

{{-- SweetAlert Sukses --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif
@endsection
