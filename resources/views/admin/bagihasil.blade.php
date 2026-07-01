@extends('theme.default')

@section('title', 'Manajemen Bagi Hasil - BUMDes Patimban')

@include('admin.partials.styles')

@section('content')
<div class="admin-page">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Manajemen Bagi Hasil</h1>
            <p class="admin-page-subtitle">Kelola pembagian keuntungan dengan mitra BUMDes.</p>
        </div>
        <button type="button" onclick="openModal('modalBagiHasil')" class="admin-btn admin-btn--primary">
            <i class="fas fa-plus"></i> Input Bagi Hasil
        </button>
    </div>

    {{-- Toast notifikasi (menggantikan alert statis) --}}
    @if(session('success'))
        <div id="toastNotif" class="admin-toast admin-toast--success">
            <div class="admin-toast-icon"><i class="fas fa-check-circle"></i></div>
            <div class="admin-toast-body">
                <p class="admin-toast-title">Berhasil</p>
                <p class="admin-toast-msg">{{ session('success') }}</p>
            </div>
            <button type="button" class="admin-toast-close" onclick="closeToast()">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div id="toastNotif" class="admin-toast admin-toast--error">
            <div class="admin-toast-icon"><i class="fas fa-times-circle"></i></div>
            <div class="admin-toast-body">
                <p class="admin-toast-title">Gagal</p>
                <p class="admin-toast-msg">{{ session('error') }}</p>
            </div>
            <button type="button" class="admin-toast-close" onclick="closeToast()">&times;</button>
        </div>
    @endif

    @php
        // Helper: resolve Mitra dari mitra_id, coba sebagai Mitra->id dulu, fallback ke user_id.
        // Menutupi inkonsistensi data lama pada kolom mitra_id di tabel bagihasils.
        $resolveMitra = function ($mitraId) {
            if (!$mitraId) return null;
            return \App\Models\Mitra::find($mitraId)
                ?? \App\Models\Mitra::where('user_id', $mitraId)->first();
        };
    @endphp

    <div class="admin-card">
        <div class="admin-table-wrap admin-table-wrap--cards">
            <table class="admin-table admin-table--responsive">
                <thead>
                    <tr>
                        <th>Mitra</th>
                        <th>Tanggal</th>
                        <th class="text-right">Total Omzet</th>
                        <th class="text-right">BUMDes</th>
                        <th class="text-right">Mitra</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bagihasils as $bh)
                    @php
                        $mitra = $bh->mitra ?? $resolveMitra($bh->mitra_id);
                    @endphp
                    <tr>
                        <td data-label="Mitra">
                            <p class="admin-user-name">{{ $mitra->nama_usaha ?? '-' }}</p>
                            <p class="admin-user-sub">{{ $mitra->user->name ?? '-' }}</p>
                        </td>
                        <td data-label="Tanggal">
                            {{ $bh->tanggal ? \Carbon\Carbon::parse($bh->tanggal)->format('d M Y') : '-' }}
                        </td>
                        <td data-label="Total Omzet" class="text-right">
                            <span style="font-weight:600;">Rp {{ number_format($bh->total_omzet, 0, ',', '.') }}</span>
                        </td>
                        <td data-label="BUMDes" class="text-right">
                            <span style="font-weight:700;color:#0969da;">Rp {{ number_format($bh->nominal_bumdes, 0, ',', '.') }}</span>
                            <span class="admin-user-sub">({{ $bh->persen_bumdes ?? 10 }}%)</span>
                        </td>
                        <td data-label="Mitra" class="text-right">
                            <span style="font-weight:700;color:#059669;">Rp {{ number_format($bh->nominal_mitra, 0, ',', '.') }}</span>
                            <span class="admin-user-sub">({{ $bh->persen_mitra ?? 90 }}%)</span>
                        </td>
                        <td data-label="Status" class="text-center">
                            @if($bh->status == 'SELESAI')
                                <span class="admin-badge admin-badge--status-ok">Selesai</span>
                            @else
                                <span class="admin-badge admin-badge--status-pending">Pending</span>
                            @endif
                        </td>
                        <td data-label="Aksi" class="text-center">
                            @if($bh->status == 'PENDING')
                                <button type="button"
                                    class="admin-btn admin-btn--success admin-btn--sm"
                                    onclick="openConfirmModal({{ $bh->id }}, '{{ addslashes($mitra->nama_usaha ?? '-') }}', '{{ number_format($bh->nominal_bumdes, 0, ',', '.') }}')">
                                    <i class="fas fa-check"></i> Konfirmasi
                                </button>
                            @else
                                <span style="font-size:0.75rem;color:#8b949e;font-style:italic;">Sudah selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr class="admin-table-empty">
                        <td colspan="7">
                            <div class="admin-empty">
                                <i class="fas fa-hand-holding-usd"></i>
                                <p class="admin-empty-title">Belum Ada Data</p>
                                <p>Belum ada data bagi hasil.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL INPUT --}}
<div id="modalBagiHasil" class="admin-modal">
    <div class="admin-modal-panel">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Tambah Data Bagi Hasil</h3>
            <button type="button" onclick="closeModal('modalBagiHasil')" class="admin-btn admin-btn--ghost admin-btn--icon" aria-label="Tutup">&times;</button>
        </div>
        <form action="{{ route('admin.bagihasil.store') }}" method="POST">
            @csrf
            <div class="admin-modal-body" style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label class="admin-form-label">Pilih Mitra</label>
                    <select name="mitra_id" id="select_mitra" class="admin-form-control" required>
                        <option value="">-- Pilih Mitra BUMDes --</option>
                        @foreach($all_mitra as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_usaha }} ({{ $m->user->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin-form-label">Total Omzet (Rp)</label>
                    <input type="number" id="total_omzet" name="total_omzet" class="admin-form-control" style="font-weight:700;font-size:1.1rem;" placeholder="Contoh: 5000000" required>
                </div>
                <div>
                    <label class="admin-form-label">
                        Persentase BUMDes: <span id="label_persen" style="color:#0969da;font-weight:800;">10%</span>
                        &nbsp;|&nbsp; Mitra: <span id="label_persen_mitra_top" style="color:#059669;font-weight:800;">90%</span>
                    </label>
                    <input type="range" id="persen_bumdes" name="persen_bumdes" min="1" max="50" value="10" style="width:100%;accent-color:#0969da;cursor:pointer;">
                    <div style="display:flex;justify-content:space-between;font-size:0.625rem;color:#8b949e;margin-top:4px;"><span>1%</span><span>50%</span></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="background:#eff6ff;padding:14px;border-radius:12px;">
                        <span class="admin-form-label" style="color:#0969da;">BUMDes (<span id="label_persen_bumdes">10</span>%)</span>
                        <p id="preview_bumdes" style="font-size:0.875rem;font-weight:800;color:#1e40af;margin-top:4px;">Rp 0</p>
                    </div>
                    <div style="background:#ecfdf5;padding:14px;border-radius:12px;">
                        <span class="admin-form-label" style="color:#059669;">Mitra (<span id="label_persen_mitra">90</span>%)</span>
                        <p id="preview_mitra" style="font-size:0.875rem;font-weight:800;color:#047857;margin-top:4px;">Rp 0</p>
                    </div>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" onclick="closeModal('modalBagiHasil')" class="admin-btn admin-btn--ghost">Batal</button>
                <button type="submit" class="admin-btn admin-btn--primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI (pengganti native confirm()) --}}
<div id="modalConfirm" class="admin-modal">
    <div class="admin-modal-panel admin-modal-panel--sm">
        <div class="admin-confirm-icon">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <h3 class="admin-confirm-title">Konfirmasi Bagi Hasil</h3>
        <p class="admin-confirm-text">
            Tandai bagi hasil milik <strong id="confirmNamaMitra">-</strong> sebagai <strong>selesai</strong>?<br>
            Nominal untuk BUMDes: <strong style="color:#0969da;">Rp <span id="confirmNominal">0</span></strong>
        </p>
        <form id="formConfirm" action="{{ route('admin.bagihasil.confirm') }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="id" id="confirmId" value="">
            <div class="admin-confirm-actions">
                <button type="button" onclick="closeModal('modalConfirm')" class="admin-btn admin-btn--ghost">Batal</button>
                <button type="submit" class="admin-btn admin-btn--success">
                    <i class="fas fa-check"></i> Ya, Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal kecil untuk confirm */
.admin-modal-panel--sm {
    max-width: 420px;
    text-align: center;
    padding: 32px 28px 24px;
}
.admin-confirm-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: #ecfdf5;
    color: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.admin-confirm-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #1f2328;
    margin-bottom: 8px;
}
.admin-confirm-text {
    font-size: 0.9rem;
    color: #57606a;
    line-height: 1.6;
    margin-bottom: 24px;
}
.admin-confirm-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}
.admin-confirm-actions .admin-btn {
    flex: 1;
    justify-content: center;
}

/* Toast notifikasi */
.admin-toast {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    border-left: 4px solid;
    animation: toastSlideIn 0.35s ease;
}
.admin-toast--success {
    background: #ecfdf5;
    border-left-color: #059669;
}
.admin-toast--error {
    background: #fef2f2;
    border-left-color: #dc2626;
}
.admin-toast-icon {
    font-size: 1.25rem;
    line-height: 1;
    margin-top: 2px;
}
.admin-toast--success .admin-toast-icon { color: #059669; }
.admin-toast--error .admin-toast-icon { color: #dc2626; }
.admin-toast-body { flex: 1; }
.admin-toast-title {
    font-weight: 700;
    font-size: 0.875rem;
    margin: 0 0 2px;
    color: #1f2328;
}
.admin-toast-msg {
    font-size: 0.8rem;
    color: #57606a;
    margin: 0;
}
.admin-toast-close {
    background: none;
    border: none;
    font-size: 1.1rem;
    line-height: 1;
    cursor: pointer;
    color: #8b949e;
    padding: 0 2px;
}
.admin-toast-close:hover { color: #1f2328; }

@keyframes toastSlideIn {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes toastFadeOut {
    from { opacity: 1; }
    to   { opacity: 0; transform: translateY(-8px); }
}
</style>

<script>
function openModal(id) { document.getElementById(id)?.classList.add('is-open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id)?.classList.remove('is-open'); document.body.style.overflow = ''; }

function openConfirmModal(id, namaMitra, nominal) {
    document.getElementById('confirmId').value = id;
    document.getElementById('confirmNamaMitra').innerText = namaMitra;
    document.getElementById('confirmNominal').innerText = nominal;
    openModal('modalConfirm');
}

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

document.getElementById('select_mitra').addEventListener('change', function () {
    const mitraId = this.value;
    if (!mitraId) return;
    fetch(`/admin/bagihasil/omzet/${mitraId}`)
        .then(res => res.json())
        .then(data => {
            let inputOmzet = document.getElementById('total_omzet');
            if (inputOmzet.value === '' || inputOmzet.value === '0') {
                inputOmzet.value = data.omzet;
            }
            hitungBagi();
        });
});

document.getElementById('total_omzet').addEventListener('input', hitungBagi);
document.getElementById('persen_bumdes').addEventListener('input', hitungBagi);

document.getElementById('modalBagiHasil').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modalBagiHasil');
});
document.getElementById('modalConfirm').addEventListener('click', function(e) {
    if (e.target === this) closeModal('modalConfirm');
});

// Toast auto-hilang
function closeToast() {
    const toast = document.getElementById('toastNotif');
    if (!toast) return;
    toast.style.animation = 'toastFadeOut 0.3s ease forwards';
    setTimeout(() => toast.remove(), 300);
}
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('toastNotif')) {
        setTimeout(closeToast, 4000);
    }
});
</script>
@endsection