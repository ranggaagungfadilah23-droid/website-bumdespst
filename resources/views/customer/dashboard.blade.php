@extends('theme.customer')

@section('title', 'Beranda - BUMDes Patimban')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/dashboard.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

@endpush

@section('content')

{{-- TOP SEARCH BAR --}}
{{-- TOP SEARCH BAR --}}


<div class="page-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">

            {{-- Kategori --}}
            <div class="filter-card">
                <div class="filter-title"><i class="fas fa-th-large"></i> Kategori</div>
                <ul class="category-list">
                    <li>
                        <a href="#" class="active" data-cat="all" onclick="filterCategory(event, 'all')">
                            <i class="fas fa-border-all"></i> Semua
                        </a>
                    </li>
                    <li>
                        <a href="#" data-cat="produk" onclick="filterCategory(event, 'produk')">
                            <i class="fas fa-box-open"></i> Produk
                        </a>
                    </li>
                    <li>
                        <a href="#" data-cat="jasa" onclick="filterCategory(event, 'jasa')">
                            <i class="fas fa-tools"></i> Layanan Jasa
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Filter Harga --}}
            <div class="filter-card">
                <div class="filter-title"><i class="fas fa-filter"></i> Filter Harga</div>

                <div class="filter-label">Produk (Rp)</div>
                <div class="price-inputs" style="margin-bottom: 12px;">
                    <input type="number" id="produk-min" placeholder="Min" min="0">
                    <span>–</span>
                    <input type="number" id="produk-max" placeholder="Max" min="0">
                </div>

                <div class="filter-label">Jasa (Rp)</div>
                <div class="price-inputs">
                    <input type="number" id="jasa-min" placeholder="Min" min="0">
                    <span>–</span>
                    <input type="number" id="jasa-max" placeholder="Max" min="0">
                </div>

                <button class="filter-apply-btn" onclick="applyFilters()">
                    <i class="fas fa-check"></i> Terapkan Filter
                </button>
                <button class="filter-reset-btn" onclick="resetFilters()">
                    Reset
                </button>
            </div>

            {{-- Tombol tutup sidebar mobile --}}
            <button class="filter-apply-btn" style="display:none;" id="close-sidebar-btn" onclick="closeSidebar()">
                Tutup
            </button>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="main-content">

        {{-- Banner --}}
        <div class="banner">
            <div>
                <span class="banner-badge">✦ Promo Spesial</span>
                <h2>Dukung Produk<br>Lokal Patimban</h2>
                <p>Gratis ongkir untuk wilayah Patimban. Belanja mudah, cepat, dan aman langsung dari BUMDes.</p>
            </div>
            <i class="fas fa-shopping-bag banner-icon"></i>
        </div>

        {{-- Mobile filter button --}}
        <button class="mobile-filter-btn" id="mobile-filter-btn" onclick="openSidebar()">
            <i class="fas fa-sliders-h"></i> Filter & Kategori
        </button>

        {{-- Sort bar --}}
        <div class="sort-bar" id="sort-bar">
            <span>Urutkan:</span>
            <button class="sort-btn active" data-sort="default" onclick="setSort(this, 'default')">Terbaru</button>
            <button class="sort-btn" data-sort="price-asc" onclick="setSort(this, 'price-asc')">Harga Terendah</button>
            <button class="sort-btn" data-sort="price-desc" onclick="setSort(this, 'price-desc')">Harga Tertinggi</button>
            <span class="results-count" id="results-count"></span>
        </div>

        {{-- ── SECTION JASA ── --}}
        <div id="section-jasa">
            <div class="section-header">
                <h3><i class="fas fa-tools" style="color:#ee4d2d;font-size:14px;"></i>&nbsp; Layanan Jasa</h3>
                <a href="#" class="see-all">Lihat Semua <i class="fas fa-chevron-right" style="font-size:10px;"></i></a>
            </div>

            <div class="jasa-grid" id="jasa-grid">
                @forelse ($jasas as $jasa)
                    <a href="{{ route('customer.jasa.show', $jasa->id) }}"
                       class="jasa-card"
                       data-name="{{ strtolower($jasa->nama_jasa) }}"
                       data-price="{{ $jasa->harga }}"
                       data-type="jasa">
                       <img src="{{ str_starts_with($jasa->gambar ?? '', 'http') ? $jasa->gambar : asset('storage/' . $jasa->gambar) }}" alt="{{ $jasa->nama_jasa }}" class="jasa-img">
                        <div class="jasa-info">
                            <div class="jasa-name">{{ $jasa->nama_jasa }}</div>
                            <div class="jasa-price">Rp {{ number_format($jasa->harga, 0, ',', '.') }}</div>
                            <div class="jasa-meta">
                                <span class="badge-tersedia">Tersedia</span>
                                <span class="star-row"><i class="fas fa-star"></i> 4.9</span>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#ddd;font-size:12px;margin-left:auto;flex-shrink:0;"></i>
                    </a>
                @empty
                    <div class="empty-state" style="grid-column:1/-1;">
                        <i class="fas fa-tools"></i>
                        <p>Belum ada layanan jasa tersedia.</p>
                    </div>
                @endforelse
            </div>

            <div class="no-results" id="no-results-jasa">
                <i class="fas fa-search"></i>
                <p>Tidak ada jasa yang sesuai filter.</p>
                <button onclick="resetFilters()">Reset Filter</button>
            </div>
        </div>

        <div style="height: 16px;"></div>

        {{-- ── SECTION PRODUK ── --}}
        <div id="section-produk">
            <div class="section-header">
                <h3><i class="fas fa-box-open" style="color:#ee4d2d;font-size:14px;"></i>&nbsp; Rekomendasi Produk</h3>
                <a href="#" class="see-all">Lihat Semua <i class="fas fa-chevron-right" style="font-size:10px;"></i></a>
            </div>

            <div class="produk-grid" id="produk-grid" style="margin-top:8px;">
                @forelse ($produks as $produk)
                    <a href="{{ route('customer.produk.show', $produk->id) }}"
                       class="produk-card"
                       data-name="{{ strtolower($produk->nama_produk) }}"
                       data-price="{{ $produk->harga }}"
                       data-stok="{{ $produk->jumlah }}"
                       data-type="produk">
                        <div class="produk-img-wrap">
                          <img src="{{ str_starts_with($produk->gambar ?? '', 'http') ? $produk->gambar : asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" loading="lazy">
                            <span class="produk-badge">BUMDES</span>
                        </div>
                        <div class="produk-info">
                            <div class="produk-name">{{ $produk->nama_produk }}</div>
                            <div class="produk-price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>
                            <div class="produk-meta">
                                <i class="fas fa-star"></i> 4.9 · Stok: {{ $produk->jumlah }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>Belum ada produk tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="no-results" id="no-results-produk">
                <i class="fas fa-search"></i>
                <p>Tidak ada produk yang sesuai filter.</p>
                <button onclick="resetFilters()">Reset Filter</button>
            </div>
        </div>

    </main>
</div>

<script>
    /* ── STATE ── */
    let currentSort = 'default';
    let currentCategory = 'all';
    let activeFilters = { produkMin: 0, produkMax: Infinity, jasaMin: 0, jasaMax: Infinity };
    let searchQuery = '';

    /* ── SEARCH ── */
    function doSearch() {
        searchQuery = document.getElementById('search-input').value.toLowerCase().trim();
        const searchType = document.getElementById('search-type').value;

        /* Update category filter berdasarkan tipe pencarian */
        if (searchType === 'produk') filterCategory(null, 'produk');
        else if (searchType === 'jasa') filterCategory(null, 'jasa');
        else filterCategory(null, 'all');

        applyAll();
    }

    document.getElementById('search-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') doSearch();
    });

    /* ── CATEGORY ── */
    function filterCategory(e, cat) {
        if (e) e.preventDefault();
        currentCategory = cat;

        document.querySelectorAll('.category-list a').forEach(a => a.classList.remove('active'));
        const target = document.querySelector(`.category-list a[data-cat="${cat}"]`);
        if (target) target.classList.add('active');

        const jasaSection = document.getElementById('section-jasa');
        const produkSection = document.getElementById('section-produk');

        jasaSection.style.display = (cat === 'produk') ? 'none' : 'block';
        produkSection.style.display = (cat === 'jasa') ? 'none' : 'block';

        applyAll();
    }

    /* ── SORT ── */
    function setSort(btn, sort) {
        currentSort = sort;
        document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyAll();
    }

    /* ── FILTER ── */
    function applyFilters() {
        activeFilters.produkMin = parseFloat(document.getElementById('produk-min').value) || 0;
        activeFilters.produkMax = parseFloat(document.getElementById('produk-max').value) || Infinity;
        activeFilters.jasaMin   = parseFloat(document.getElementById('jasa-min').value) || 0;
        activeFilters.jasaMax   = parseFloat(document.getElementById('jasa-max').value) || Infinity;
        applyAll();
        /* Tutup sidebar di mobile */
        if (window.innerWidth <= 768) closeSidebar();
    }

    function resetFilters() {
        ['produk-min','produk-max','jasa-min','jasa-max'].forEach(id => document.getElementById(id).value = '');
        activeFilters = { produkMin: 0, produkMax: Infinity, jasaMin: 0, jasaMax: Infinity };
        searchQuery = '';
        document.getElementById('search-input').value = '';
        document.getElementById('search-type').value = 'all';
        currentSort = 'default';
        document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
        document.querySelector('.sort-btn[data-sort="default"]').classList.add('active');
        filterCategory(null, 'all');
    }

    /* ── CORE: APPLY ALL FILTERS + SORT + SEARCH ── */
    function applyAll() {
        applyToGrid('jasa-grid', 'jasa', activeFilters.jasaMin, activeFilters.jasaMax);
        applyToGrid('produk-grid', 'produk', activeFilters.produkMin, activeFilters.produkMax);
        updateCount();
    }

    function applyToGrid(gridId, type, minPrice, maxPrice) {
        const grid = document.getElementById(gridId);
        if (!grid) return;

        const items = Array.from(grid.querySelectorAll(`[data-type="${type}"]`));
        let visible = [];

        items.forEach(item => {
            const price = parseFloat(item.dataset.price) || 0;
            const name  = item.dataset.name || '';
            const matchSearch = !searchQuery || name.includes(searchQuery);
            const matchPrice  = price >= minPrice && price <= maxPrice;

            if (matchSearch && matchPrice) {
                item.style.display = '';
                visible.push(item);
            } else {
                item.style.display = 'none';
            }
        });

        /* Sort visible items */
        if (currentSort !== 'default' && visible.length > 0) {
            visible.sort((a, b) => {
                const pa = parseFloat(a.dataset.price) || 0;
                const pb = parseFloat(b.dataset.price) || 0;
                return currentSort === 'price-asc' ? pa - pb : pb - pa;
            });
            visible.forEach(item => grid.appendChild(item));
        }

        /* Tampilkan no-results */
        const noResults = document.getElementById(`no-results-${type}`);
        if (noResults) noResults.style.display = visible.length === 0 ? 'block' : 'none';
    }

    function updateCount() {
        const visibleProduk = document.querySelectorAll('#produk-grid [data-type="produk"]:not([style*="display: none"])').length;
        const visibleJasa   = document.querySelectorAll('#jasa-grid [data-type="jasa"]:not([style*="display: none"])').length;
        const total = visibleProduk + visibleJasa;
        const el = document.getElementById('results-count');
        if (el) {
            if (searchQuery || activeFilters.produkMax !== Infinity || activeFilters.jasaMax !== Infinity) {
                el.textContent = `${total} hasil ditemukan`;
            } else {
                el.textContent = '';
            }
        }
    }

    /* ── MOBILE SIDEBAR ── */
    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.add('mobile-open');
        document.getElementById('close-sidebar-btn').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.remove('mobile-open');
        document.getElementById('close-sidebar-btn').style.display = 'none';
        document.body.style.overflow = '';
    }
    /* Klik overlay = tutup sidebar */
    document.getElementById('sidebar').addEventListener('click', function(e) {
        if (e.target === this) closeSidebar();
    });
</script>
@endsection
