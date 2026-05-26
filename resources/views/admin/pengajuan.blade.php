@extends('theme.default')

@section('title', 'Pengajuan Mitra - BUMDes Admin')

@push('navbar-search')
<div class="flex items-center gap-3">
    <p class="text-sm font-bold text-slate-600 hidden md:block">Cari:</p>
    <form action="{{ route('global.search') }}" method="GET" class="flex items-center">
        <input type="text" name="q" placeholder="Cari fitur atau nama mitra..." value="{{ request('q') }}"
               class="border border-slate-200 rounded-l-xl px-3 py-1.5 text-sm outline-none focus:border-blue-500 w-64">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 text-sm rounded-r-xl font-bold hover:bg-blue-700 transition">CARI</button>
    </form>
</div>
@endpush

@push('navbar-separator')
    <div class="hidden md:block w-px h-8 bg-slate-200"></div>
@endpush

@section('content')

<style>
    .badge-kategori {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 10px; font-weight: 800; letter-spacing: .06em;
        padding: 3px 10px; border-radius: 99px; text-transform: uppercase;
    }
    .badge-jasa   { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; }
    .badge-produk { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .tbl-th { padding: 14px 16px; font-size: 10px; font-weight: 700; letter-spacing: .08em; color: #94a3b8; text-transform: uppercase; white-space: nowrap; }
    .tbl-td { padding: 14px 16px; vertical-align: middle; }
</style>

<div class="mb-6 flex items-end justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Daftar Pengajuan</h1>
        <p class="text-slate-400 text-sm mt-0.5">Tinjau dan verifikasi pendaftaran mitra baru.</p>
    </div>
    <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wide">
        {{ $pengajuans->count() }} Pengajuan
    </span>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" style="min-width:900px">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="tbl-th">Pemilik</th>
                    <th class="tbl-th">Nama Usaha</th>
                    <th class="tbl-th">Kategori</th>
                    <th class="tbl-th">NIK</th>
                    <th class="tbl-th">Alamat</th>
                    <th class="tbl-th">Dokumen</th>
                    <th class="tbl-th text-center">Opsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($pengajuans as $item)
                <tr class="hover:bg-slate-50/70 transition">

                    {{-- PEMILIK --}}
                    <td class="tbl-td">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-blue-500 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($item->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 leading-tight text-[13px]">{{ $item->name }}</p>
                                <p class="text-slate-400 text-[10px] mt-0.5">{{ $item->mitra->no_hp ?? '-' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- NAMA USAHA --}}
                    <td class="tbl-td">
                        <p class="font-bold text-slate-700 text-[13px] uppercase tracking-tight">
                            {{ $item->mitra->nama_usaha ?? 'N/A' }}
                        </p>
                    </td>

                    {{-- KATEGORI --}}
                    <td class="tbl-td">
                        @php $jenis = strtolower($item->mitra->jenis_usaha ?? ''); @endphp
                        <span class="badge-kategori {{ $jenis === 'jasa' ? 'badge-jasa' : 'badge-produk' }}">
                            <i class="fas {{ $jenis === 'jasa' ? 'fa-concierge-bell' : 'fa-box' }}"></i>
                            {{ $item->mitra->jenis_usaha ?? '-' }}
                        </span>
                    </td>

                    {{-- NIK --}}
                    <td class="tbl-td">
                        <p class="font-mono text-[12px] text-slate-600 tracking-wider bg-slate-100 px-2 py-1 rounded-lg inline-block">
                            {{ $item->mitra->nik ?? '-' }}
                        </p>
                    </td>

                    {{-- ALAMAT --}}
                    <td class="tbl-td">
                        <p class="text-[12px] text-slate-500 leading-relaxed">
                            {{ $item->mitra->alamat_usaha ?? '-' }}
                        </p>
                        @if($item->mitra->dusun)
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            <i class="fas fa-map-marker-alt text-rose-400 mr-1"></i>Dusun {{ $item->mitra->dusun }}
                        </p>
                        @endif
                    </td>

                  {{-- DOKUMEN --}}
<td class="tbl-td">
    <div class="flex gap-1.5 flex-wrap">
        @if($item->mitra && $item->mitra->sku)
        <a href="{{ str_starts_with($item->mitra->sku ?? '', 'http') ? $item->mitra->sku : asset('storage/' . $item->mitra->sku) }}"
           target="_blank"
           class="inline-flex items-center gap-1 bg-sky-500 hover:bg-sky-600 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg transition">
            <i class="fas fa-file-signature text-[9px]"></i> SKU
        </a>
        @else
        <span class="text-slate-300 text-[11px] italic">Tidak ada</span>
        @endif
    </div>
</td>

                    {{-- OPSI --}}
                    <td class="tbl-td text-center">
                        <div class="flex justify-center items-center gap-1.5">
                            <button type="button"
                                    onclick="openApproveModal({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                    class="inline-flex items-center gap-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg transition shadow-sm shadow-indigo-200">
                                <i class="fas fa-share-square text-[9px]"></i> Proses
                            </button>
                            <button type="button"
                                    onclick="openRejectModal({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                    class="inline-flex items-center justify-center bg-rose-50 hover:bg-rose-500 text-rose-500 hover:text-white w-8 h-8 rounded-lg transition border border-rose-100 hover:border-rose-500">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fas fa-clipboard-check text-2xl text-slate-300"></i>
                            </div>
                            <p class="font-bold text-slate-700">Semua Berkas Sudah Difilter</p>
                            <p class="text-slate-400 text-sm mt-1">Tidak ada pengajuan mitra baru yang perlu diverifikasi.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL APPROVE --}}
<div id="approveModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl transform scale-95 transition-transform duration-300" id="approveModalContent">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-extrabold text-slate-800">Konfirmasi Teruskan</h3>
            <button onclick="closeApproveModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-sm text-slate-500 mb-1">Berkas dari <strong id="approveNameDisplay" class="text-slate-800"></strong> akan diteruskan ke Kepala BUMDes.</p>
        <p class="text-sm text-slate-400 mb-5">Pastikan dokumen sudah valid dan lengkap.</p>
        <form id="approveForm" method="POST" action="">
            @csrf
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-indigo-500 hover:bg-indigo-600 shadow-md shadow-indigo-200 transition">Ya, Teruskan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL REJECT --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl transform scale-95 transition-transform duration-300" id="rejectModalContent">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-extrabold text-slate-800">Tolak Pengajuan</h3>
            <button onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-sm text-slate-500 mb-4">Tolak pengajuan dari <strong id="rejectNameDisplay" class="text-slate-800"></strong>?</p>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Pesan Penolakan (Opsional)</label>
                <textarea name="pesan_penolakan" rows="3"
                          class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-rose-400 outline-none transition resize-none"
                          placeholder="Contoh: Dokumen KTP buram, harap upload ulang..."></textarea>
                <p class="text-[10px] text-slate-400 mt-1">Pesan akan dikirim ke WhatsApp pemohon.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 shadow-md shadow-rose-200 transition">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal(id, name) {
        document.getElementById('approveNameDisplay').innerText = name;
        document.getElementById('approveForm').action = `/admin/approve/${id}`;
        const modal = document.getElementById('approveModal');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); document.getElementById('approveModalContent').classList.replace('scale-95','scale-100'); }, 10);
    }
    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        modal.classList.add('opacity-0');
        document.getElementById('approveModalContent').classList.replace('scale-100','scale-95');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
    function openRejectModal(id, name) {
        document.getElementById('rejectNameDisplay').innerText = name;
        document.getElementById('rejectForm').action = `/admin/reject/${id}`;
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); document.getElementById('rejectModalContent').classList.replace('scale-95','scale-100'); }, 10);
    }
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('opacity-0');
        document.getElementById('rejectModalContent').classList.replace('scale-100','scale-95');
        setTimeout(() => { modal.classList.add('hidden'); document.querySelector('textarea[name="pesan_penolakan"]').value = ''; }, 300);
    }
</script>
@endsection
