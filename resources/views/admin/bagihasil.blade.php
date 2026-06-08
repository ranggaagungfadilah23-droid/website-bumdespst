@extends('theme.default')
@section('title', 'Manajemen Bagi Hasil - BUMDes Patimban')
@section('content')
<div class="p-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Bagi Hasil</h1>
            <p class="text-slate-500 text-sm">Kelola pembagian keuntungan dengan mitra BUMDes.</p>
        </div>
        <button type="button" onclick="document.getElementById('modalBagiHasil').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition">
            <i class="fas fa-plus"></i> Input Bagi Hasil
        </button>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-2xl flex items-center gap-2">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- TABEL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Mitra</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Total Omzet</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">BUMDes</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Mitra</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bagihasils as $bh)
                @php $mitra = \App\Models\Mitra::with('user')->where('user_id', $bh->mitra_id)->first(); @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-700 text-sm">{{ $mitra->nama_usaha ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ $mitra->user->name ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $bh->tanggal ? \Carbon\Carbon::parse($bh->tanggal)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-slate-600">
                        Rp {{ number_format($bh->total_omzet, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-blue-600">Rp {{ number_format($bh->nominal_bumdes, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-blue-400 block">({{ $bh->persen_bumdes ?? 10 }}%)</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($bh->nominal_mitra, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-emerald-400 block">({{ $bh->persen_mitra ?? 90 }}%)</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($bh->status == 'SELESAI')
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-600 text-[10px] font-bold uppercase">Selesai</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-600 text-[10px] font-bold uppercase">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($bh->status == 'PENDING')
                            <form action="{{ route('admin.bagihasil.confirm') }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="id" value="{{ $bh->id }}">
                                <button type="submit"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition"
                                    onclick="return confirm('Konfirmasi bagi hasil ini selesai?')">
                                    <i class="fas fa-check mr-1"></i> Konfirmasi
                                </button>
                            </form>
                        @else
                            <span class="text-slate-300 text-xs italic">Sudah selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                        <i class="fas fa-hand-holding-usd text-4xl mb-3 block text-slate-200"></i>
                        Belum ada data bagi hasil.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL INPUT BAGI HASIL --}}
<div id="modalBagiHasil" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">

        <div class="bg-slate-50 px-6 py-5 flex justify-between items-center border-b border-slate-100">
            <h5 class="font-extrabold text-slate-800 text-lg">Tambah Data Bagi Hasil</h5>
            <button onclick="document.getElementById('modalBagiHasil').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none">&times;</button>
        </div>

        <form action="{{ route('admin.bagihasil.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">

                {{-- Pilih Mitra --}}
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Pilih Mitra</label>
                   <select name="mitra_id" id="select_mitra" class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-slate-700 outline-none focus:ring-2 focus:ring-blue-500" required>
    <option value="">-- Pilih Mitra BUMDes --</option>
    @foreach($all_mitra as $m)
        <option value="{{ $m->user_id }}">{{ $m->nama_usaha }} ({{ $m->user->name }})</option>
    @endforeach
</select>
                </div>

                {{-- Total Omzet --}}
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">Total Omzet (Rp)</label>
                    <input type="number" id="total_omzet" name="total_omzet"
                        class="w-full border border-slate-200 rounded-2xl px-4 py-3 font-bold text-lg outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: 5000000" required>
                </div>

                {{-- Slider Persentase --}}
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 block">
                        Persentase BUMDes: <span id="label_persen" class="text-blue-600 font-black">10%</span>
                        &nbsp;|&nbsp; Mitra: <span id="label_persen_mitra_top" class="text-emerald-600 font-black">90%</span>
                    </label>
                    <input type="range" id="persen_bumdes" name="persen_bumdes"
                        min="1" max="50" value="10"
                        class="w-full accent-blue-600 cursor-pointer">
                    <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                        <span>1%</span><span>50%</span>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-2xl">
                        <span class="text-[10px] font-bold text-blue-600 uppercase">BUMDes (<span id="label_persen_bumdes">10</span>%)</span>
                        <p id="preview_bumdes" class="text-sm font-black text-blue-800 mt-1">Rp 0</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-2xl">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase">Mitra (<span id="label_persen_mitra">90</span>%)</span>
                        <p id="preview_mitra" class="text-sm font-black text-emerald-800 mt-1">Rp 0</p>
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('modalBagiHasil').classList.add('hidden')"
                    class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-lg shadow-blue-500/30">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function hitungBagi() {
        const omzet        = parseFloat(document.getElementById('total_omzet').value) || 0;
        const persenBumdes = parseFloat(document.getElementById('persen_bumdes').value) || 10;
        const persenMitra  = 100 - persenBumdes;

        document.getElementById('label_persen').innerText           = persenBumdes + '%';
        document.getElementById('label_persen_mitra_top').innerText = persenMitra + '%';
        document.getElementById('label_persen_bumdes').innerText    = persenBumdes;
        document.getElementById('label_persen_mitra').innerText     = persenMitra;
        document.getElementById('preview_bumdes').innerText         = 'Rp ' + new Intl.NumberFormat('id-ID').format(omzet * persenBumdes / 100);
        document.getElementById('preview_mitra').innerText          = 'Rp ' + new Intl.NumberFormat('id-ID').format(omzet * persenMitra / 100);
    }

    // ✅ Diperbarui: Hanya menimpa nilai jika input masih kosong atau 0
    document.getElementById('select_mitra').addEventListener('change', function () {
        const mitraId = this.value;
        if (!mitraId) return;

        fetch(`/admin/bagihasil/omzet/${mitraId}`)
            .then(res => res.json())
            .then(data => {
                let inputOmzet = document.getElementById('total_omzet');
                // Jika input masih kosong atau 0, otomatis isi dengan omzet dari database
                if (inputOmzet.value === '' || inputOmzet.value === '0') {
                    inputOmzet.value = data.omzet;
                }
                hitungBagi(); // langsung hitung setelah omzet/nilai dimasukkan
            });
    });

    document.getElementById('total_omzet').addEventListener('input', hitungBagi);
    document.getElementById('persen_bumdes').addEventListener('input', hitungBagi);

    document.getElementById('modalBagiHasil').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>

@endsection
