@extends('theme.default')

@section('title', 'Pengajuan Mitra - BUMDes Admin')

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
            <h1 class="admin-page-title">Daftar Pengajuan</h1>
            <p class="admin-page-subtitle">Tinjau dan verifikasi pendaftaran mitra baru.</p>
        </div>
        <span class="admin-page-badge admin-page-badge--indigo">{{ $pengajuans->count() }} Pengajuan</span>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrap admin-table-wrap--cards">
            <table class="admin-table admin-table--responsive">
                <thead>
                    <tr>
                        <th>Pemilik</th>
                        <th>Nama Usaha</th>
                        <th>Kategori</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                        <th>Dokumen</th>
                        <th class="text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuans as $item)
                    <tr>
                        <td data-label="Pemilik">
                            <div class="admin-user-inline">
                                <div class="admin-user-avatar" style="background:linear-gradient(135deg,#6366f1,#3b82f6);">
                                    {{ strtoupper(substr($item->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="admin-user-name">{{ $item->name }}</p>
                                    <p class="admin-user-sub">{{ $item->mitra->no_hp ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Nama Usaha">
                            <span class="admin-user-name">{{ $item->mitra->nama_usaha ?? 'N/A' }}</span>
                        </td>
                        <td data-label="Kategori">
                            @php $jenis = strtolower($item->mitra->jenis_usaha ?? ''); @endphp
                            <span class="admin-badge {{ $jenis === 'jasa' ? 'admin-badge--jasa' : 'admin-badge--produk' }}">
                                <i class="fas {{ $jenis === 'jasa' ? 'fa-concierge-bell' : 'fa-box' }}"></i>
                                {{ $item->mitra->jenis_usaha ?? '-' }}
                            </span>
                        </td>
                        <td data-label="NIK">
                            <code style="font-size:0.75rem;background:#f6f8fa;padding:4px 8px;border-radius:8px;">{{ $item->mitra->nik ?? '-' }}</code>
                        </td>
                        <td data-label="Alamat">
                            <p style="font-size:0.75rem;line-height:1.45;">{{ $item->mitra->alamat_usaha ?? '-' }}</p>
                            @if($item->mitra->dusun)
                            <p class="admin-user-sub"><i class="fas fa-map-marker-alt" style="color:#f87171;margin-right:4px;"></i>Dusun {{ $item->mitra->dusun }}</p>
                            @endif
                        </td>
                        <td data-label="Dokumen">
                            @if($item->mitra && $item->mitra->sku)
                            <button type="button" onclick="openPdfModal('{{ $item->mitra->sku }}')" class="admin-btn admin-btn--primary admin-btn--sm">
                                <i class="fas fa-file-signature"></i> SKU
                            </button>
                            @else
                            <span style="font-size:0.75rem;color:#8b949e;font-style:italic;">Tidak ada</span>
                            @endif
                        </td>
                        <td data-label="Opsi" class="text-center">
                            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:6px;">
                                <button type="button" onclick="openApproveModal({{ $item->id }}, '{{ addslashes($item->name) }}')" class="admin-btn admin-btn--primary admin-btn--sm">
                                    <i class="fas fa-share-square"></i> Proses
                                </button>
                                <button type="button" onclick="openRejectModal({{ $item->id }}, '{{ addslashes($item->name) }}')" class="admin-btn admin-btn--danger admin-btn--icon" title="Tolak">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="admin-table-empty">
                        <td colspan="7">
                            <div class="admin-empty">
                                <i class="fas fa-clipboard-check"></i>
                                <p class="admin-empty-title">Semua Berkas Sudah Difilter</p>
                                <p>Tidak ada pengajuan mitra baru yang perlu diverifikasi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL APPROVE --}}
<div id="approveModal" class="admin-modal">
    <div class="admin-modal-panel">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Konfirmasi Teruskan</h3>
            <button type="button" onclick="closeApproveModal()" class="admin-btn admin-btn--ghost admin-btn--icon" aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="admin-modal-body">
            <p style="font-size:0.875rem;color:#656d76;margin-bottom:4px;">Berkas dari <strong id="approveNameDisplay" style="color:#1f2328;"></strong> akan diteruskan ke Kepala BUMDes.</p>
            <p style="font-size:0.8125rem;color:#8b949e;margin-bottom:16px;">Pastikan dokumen sudah valid dan lengkap.</p>
            <form id="approveForm" method="POST" action="">
                @csrf
                <div class="admin-modal-footer" style="margin:0 -18px -18px;padding:14px 18px;">
                    <button type="button" onclick="closeApproveModal()" class="admin-btn admin-btn--ghost">Batal</button>
                    <button type="submit" class="admin-btn admin-btn--primary">Ya, Teruskan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REJECT --}}
<div id="rejectModal" class="admin-modal">
    <div class="admin-modal-panel">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Tolak Pengajuan</h3>
            <button type="button" onclick="closeRejectModal()" class="admin-btn admin-btn--ghost admin-btn--icon" aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="admin-modal-body">
            <p style="font-size:0.875rem;color:#656d76;margin-bottom:16px;">Tolak pengajuan dari <strong id="rejectNameDisplay" style="color:#1f2328;"></strong>?</p>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <label class="admin-form-label">Pesan Penolakan (Opsional)</label>
                <textarea name="pesan_penolakan" rows="3" class="admin-form-control admin-form-textarea" placeholder="Contoh: Dokumen KTP buram, harap upload ulang..."></textarea>
                <p style="font-size:0.625rem;color:#8b949e;margin-top:6px;">Pesan akan dikirim ke WhatsApp pemohon.</p>
                <div class="admin-modal-footer" style="margin:16px -18px -18px;padding:14px 18px;">
                    <button type="button" onclick="closeRejectModal()" class="admin-btn admin-btn--ghost">Batal</button>
                    <button type="submit" class="admin-btn admin-btn--danger" style="background:#e11d48;color:#fff;border:none;">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PDF --}}
<div id="pdfModal" class="admin-modal">
    <div class="admin-modal-panel admin-modal-panel--lg admin-modal-panel--tall">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Preview Dokumen SKU</h3>
            <div style="display:flex;gap:8px;">
                <a id="pdfDownloadBtn" href="#" target="_blank" class="admin-btn admin-btn--primary admin-btn--sm">
                    <i class="fas fa-download"></i> Download
                </a>
                <button type="button" onclick="closePdfModal()" class="admin-btn admin-btn--ghost admin-btn--icon" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="admin-modal-body" style="flex:1;padding:8px;overflow:hidden;">
            <iframe id="pdfFrame" src="" style="width:100%;height:100%;min-height:50vh;border:1px solid #eaeef2;border-radius:12px;"></iframe>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id)?.classList.add('is-open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id)?.classList.remove('is-open'); document.body.style.overflow = ''; }

function openApproveModal(id, name) {
    document.getElementById('approveNameDisplay').innerText = name;
    document.getElementById('approveForm').action = `/admin/approve/${id}`;
    openModal('approveModal');
}
function closeApproveModal() { closeModal('approveModal'); }

function openRejectModal(id, name) {
    document.getElementById('rejectNameDisplay').innerText = name;
    document.getElementById('rejectForm').action = `/admin/reject/${id}`;
    openModal('rejectModal');
}
function closeRejectModal() {
    closeModal('rejectModal');
    document.querySelector('#rejectForm textarea[name="pesan_penolakan"]').value = '';
}

function openPdfModal(url) {
    document.getElementById('pdfFrame').src = 'https://docs.google.com/viewer?embedded=true&url=' + encodeURIComponent(url);
    document.getElementById('pdfDownloadBtn').href = url;
    openModal('pdfModal');
}
function closePdfModal() {
    document.getElementById('pdfFrame').src = '';
    closeModal('pdfModal');
}

document.querySelectorAll('.admin-modal').forEach(modal => {
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('is-open'); document.body.style.overflow = ''; });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['approveModal','rejectModal','pdfModal'].forEach(closeModal);
    }
});
</script>
@endsection
