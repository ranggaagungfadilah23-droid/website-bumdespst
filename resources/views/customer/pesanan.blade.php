@extends('theme.customer')

@section('title', 'Pesanan Saya')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/customer/pesanan.css') }}" rel="stylesheet">
    <style>
        /* ===== RATING MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }

        .modal-box {
            background: #fff;
            border-radius: 18px;
            padding: 28px 28px 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            position: relative;
            animation: slideUp .25s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        .modal-close {
            position: absolute;
            top: 14px; right: 16px;
            background: none; border: none;
            font-size: 22px; cursor: pointer;
            color: #888; line-height: 1;
        }
        .modal-close:hover { color: #333; }

        .modal-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 17px; font-weight: 700;
            color: #1a1a2e; margin-bottom: 4px;
        }
        .modal-subtitle {
            font-size: 13px; color: #888;
            margin-bottom: 20px;
        }

        /* Star rating */
        .star-row {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-bottom: 18px;
        }
        .star-btn {
            background: none; border: none;
            font-size: 36px;
            cursor: pointer;
            color: #e0e0e0;
            transition: color .15s, transform .12s;
            line-height: 1;
        }
        .star-btn:hover,
        .star-btn.active { color: #FBBF24; }
        .star-btn:hover { transform: scale(1.18); }

        .star-label {
            text-align: center;
            font-size: 13px;
            color: #f59e0b;
            font-weight: 600;
            min-height: 18px;
            margin-bottom: 14px;
        }

        .modal-textarea {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            resize: none;
            outline: none;
            transition: border .2s;
            box-sizing: border-box;
            color: #333;
        }
        .modal-textarea:focus { border-color: #6366f1; }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }
        .btn-cancel-modal {
            flex: 1;
            padding: 11px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-cancel-modal:hover { background: #f3f4f6; }

        .btn-submit-ulasan {
            flex: 2;
            padding: 11px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .15s;
        }
        .btn-submit-ulasan:hover { opacity: .88; }
        .btn-submit-ulasan:disabled { opacity: .5; cursor: not-allowed; }

        /* ===== CARD ACTIONS ===== */
        .btn-ulasan {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            border-radius: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: opacity .15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-ulasan:hover { opacity: .85; }
        .btn-ulasan svg { width: 14px; height: 14px; }

        .btn-sudah-ulasan {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            border-radius: 8px;
            background: #f8fafc;
            color: #9ca3af;
            font-size: 13px;
            font-weight: 600;
            border: 1.5px solid #e2e8f0;
            cursor: not-allowed;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ===== REVIEW & REPLY BOX ===== */
        .review-display-box {
            margin-top: 14px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
        }
        .customer-review-text {
            font-size: 13px;
            color: #334155;
            margin-top: 6px;
            line-height: 1.5;
            font-style: italic;
        }
        .mitra-reply-box {
            margin-top: 12px;
            padding: 12px 14px;
            background: #f0fdf4;
            border-left: 3px solid #22c55e;
            border-radius: 4px 12px 12px 4px;
        }
        .mitra-reply-title {
            font-size: 11px;
            font-weight: 700;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .mitra-reply-text {
            font-size: 12px;
            color: #1e293b;
            margin-top: 4px;
            line-height: 1.5;
        }

        /* ===== TOAST ===== */
        .toast {
            position: fixed;
            bottom: 28px; left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: #1a1a2e;
            color: #fff;
            padding: 12px 22px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            z-index: 1999;
            transition: transform .3s ease, opacity .3s;
            opacity: 0;
            white-space: nowrap;
        }
        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>
@endpush

@section('content')

<div class="pesanan-wrap" style="margin-top: 20px;">

    <a href="{{ route('customer.dashboard') }}" class="back-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Dashboard
    </a>

    <h1 class="page-title">Pesanan Saya</h1>

    {{-- Tab Nav --}}
    <nav class="tab-nav" role="tablist" aria-label="Filter pesanan">
        @php
            $links = [
                'customer.pesanan'         => 'Semua',
                'customer.pesanan.pending' => 'Belum Bayar',
                'customer.pesanan.dikemas' => 'Dikemas',
                'customer.pesanan.dikirim' => 'Dikirim',
                'customer.pesanan.selesai' => 'Selesai',
            ];
        @endphp
        @foreach($links as $route => $label)
            <a href="{{ route($route) }}"
               class="tab-link {{ request()->routeIs($route) ? 'active' : '' }}"
               role="tab"
               aria-selected="{{ request()->routeIs($route) ? 'true' : 'false' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    {{-- Orders --}}
    <div class="orders-list">
        @forelse($pesanan as $t)
        <div class="order-card">
            {{-- Header --}}
            <div class="card-header">
                <span class="invoice-num">#{{ $t->invoice_number }}</span>
                <div class="badge-row">
                    @if($t->status_pembayaran == 'Lunas')
                        <span class="badge badge-lunas">Lunas</span>
                    @else
                        <span class="badge badge-pending">Belum Bayar</span>
                    @endif
                    @if($t->status_pengiriman == 'Selesai' || $t->status_pengiriman == 'Diterima')
                        <span class="badge" style="background:#d1fae5;color:#065f46;">Selesai</span>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="product-row">
                <div class="product-img">
                    @if($t->produk && $t->produk->foto)
                        <img src="{{ asset('storage/' . $t->produk->foto) }}" alt="{{ $t->produk->nama_produk }}">
                    @elseif($t->jasa && $t->jasa->foto)
                        <img src="{{ asset('storage/' . $t->jasa->foto) }}" alt="{{ $t->jasa->nama_jasa }}">
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @endif
                </div>
                <div class="product-info">
                    <div class="product-name">{{ $t->produk->nama_produk ?? $t->jasa->nama_jasa ?? 'Item' }}</div>
                    <div class="product-meta">{{ $t->jumlah }} item</div>
                    <div class="product-total">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Bagian Render Review dan Balasan Mitra --}}
            @if(($t->status_pengiriman == 'Selesai' || $t->status_pengiriman == 'Diterima') && $t->ulasan)
                <div class="review-display-box text-left">
                    <div class="flex items-center gap-1 text-xs font-bold text-amber-500">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $t->ulasan->bintang ? '★' : '☆' }}</span>
                        @endfor
                        <span class="text-slate-400 font-medium ml-1">({{ $t->ulasan->bintang }} / 5)</span>
                    </div>

                    @if($t->ulasan->pesan)
                        <p class="customer-review-text">"{{ $t->ulasan->pesan }}"</p>
                    @endif

                    {{-- Render Balasan dari Mitra --}}
                    @if($t->ulasan->balasan_mitra)
                        <div class="mitra-reply-box">
                            <div class="mitra-reply-title">
                                <i class="fas fa-store mr-1"></i> Balasan dari Mitra:
                            </div>
                            <p class="mitra-reply-text">{{ $t->ulasan->balasan_mitra }}</p>
                            <span class="text-[9px] text-slate-400 block mt-1">
                                {{ \Carbon\Carbon::parse($t->ulasan->dibalas_at)->format('d M Y, H:i') }}
                            </span>
                        </div>
                    @else
                        <div class="mt-2 text-[11px] text-slate-400 italic">
                            <i class="fas fa-clock mr-1"></i> Menunggu tanggapan atau balasan dari mitra...
                        </div>
                    @endif
                </div>
            @endif

            <hr class="divider">

            {{-- Footer Actions --}}
            <div class="card-footer">
                <span class="order-date">{{ $t->created_at->format('d M Y, H:i') }}</span>
                <div class="action-group">
                    <a href="{{ route('customer.invoice', $t->invoice_number) }}" class="btn btn-ghost">Lihat Detail</a>

                    {{-- Kondisional Tombol / Status Ulasan --}}
                    @if($t->status_pengiriman == 'Selesai' || $t->status_pengiriman == 'Diterima')
                        @if($t->ulasan)
                            <span class="btn-sudah-ulasan">
                                <svg fill="currentColor" viewBox="0 0 20 20" style="width:14px;height:14px;"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Sudah Diulas
                            </span>
                        @else
                            <button
                                class="btn-ulasan"
                                onclick="bukaModalUlasan(
                                    '{{ $t->invoice_number }}',
                                    '{{ addslashes($t->produk->nama_produk ?? $t->jasa->nama_jasa ?? 'Item') }}',
                                    {{ $t->mitra_id ?? $t->produk->mitra_id ?? $t->jasa->mitra_id ?? 'null' }}
                                )"
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Beri Ulasan
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-title">Belum ada pesanan</div>
        </div>
        @endforelse
    </div>
</div>

{{-- ===== MODAL ULASAN ===== --}}
<div class="modal-overlay" id="modalUlasan">
    <div class="modal-box">
        <button class="modal-close" onclick="tutupModal()">&times;</button>
        <div class="modal-title">Beri Ulasan & Penilaian</div>
        <p class="modal-subtitle" id="modalSubtitle">Bagaimana pengalaman kamu?</p>

        {{-- Bintang --}}
        <div class="star-row" id="starRow">
            @for($i = 1; $i <= 5; $i++)
                <button class="star-btn" data-val="{{ $i }}" onclick="setBintang({{ $i }})" type="button">★</button>
            @endfor
        </div>
        <div class="star-label" id="starLabel"></div>

        {{-- Textarea ulasan --}}
        <textarea
            class="modal-textarea"
            id="pesanUlasan"
            rows="4"
            placeholder="Ceritakan pengalamanmu... (opsional)"
        ></textarea>

        <div class="modal-actions">
            <button class="btn-cancel-modal" onclick="tutupModal()">Batal</button>
            <button class="btn-submit-ulasan" id="btnSubmit" onclick="kirimUlasan()" disabled>Kirim Ulasan</button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="toast" id="toast"></div>

@push('scripts')
<script>
    const starLabels = ['', 'Mengecewakan 😞', 'Kurang Memuaskan 😕', 'Cukup Baik 😊', 'Bagus! 😄', 'Luar Biasa! 🤩'];
    let selectedBintang = 0;
    let currentInvoice = null;
    let currentMitraId = null;

    function bukaModalUlasan(invoice, namaProduk, mitraId) {
        currentInvoice = invoice;
        currentMitraId = mitraId;
        selectedBintang = 0;
        document.getElementById('modalSubtitle').textContent = namaProduk;
        document.getElementById('pesanUlasan').value = '';
        document.getElementById('starLabel').textContent = '';
        document.getElementById('btnSubmit').disabled = true;
        document.querySelectorAll('.star-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('modalUlasan').classList.add('active');
    }

    function tutupModal() {
        document.getElementById('modalUlasan').classList.remove('active');
    }

    function setBintang(val) {
        selectedBintang = val;
        document.querySelectorAll('.star-btn').forEach(b => {
            b.classList.toggle('active', parseInt(b.dataset.val) <= val);
        });
        document.getElementById('starLabel').textContent = starLabels[val];
        document.getElementById('btnSubmit').disabled = false;
    }

    // Hover efek bintang
    document.querySelectorAll('.star-btn').forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            const hoverVal = parseInt(btn.dataset.val);
            document.querySelectorAll('.star-btn').forEach(b => {
                b.classList.toggle('active', parseInt(b.dataset.val) <= hoverVal);
            });
        });
        btn.addEventListener('mouseleave', () => {
            document.querySelectorAll('.star-btn').forEach(b => {
                b.classList.toggle('active', parseInt(b.dataset.val) <= selectedBintang);
            });
        });
    });

    async function kirimUlasan() {
        if (!selectedBintang) return;

        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.textContent = 'Mengirim...';

        const payload = {
            invoice_number: currentInvoice,
            mitra_id: currentMitraId,
            bintang: selectedBintang,
            pesan: document.getElementById('pesanUlasan').value.trim(),
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch('{{ route("customer.ulasan.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                tutupModal();
                tampilkanToast('✅ Ulasan berhasil dikirim ke mitra!');
                setTimeout(() => location.reload(), 1500);
            } else {
                tampilkanToast('❌ Gagal mengirim ulasan. Coba lagi.');
                btn.disabled = false;
                btn.textContent = 'Kirim Ulasan';
            }
        } catch (e) {
            tampilkanToast('❌ Terjadi kesalahan. Periksa koneksi kamu.');
            btn.disabled = false;
            btn.textContent = 'Kirim Ulasan';
        }
    }

    function tampilkanToast(pesan) {
        const t = document.getElementById('toast');
        t.textContent = pesan;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    document.getElementById('modalUlasan').addEventListener('click', function(e) {
        if (e.target === this) tutupModal();
    });
</script>
@endpush

@endsection
