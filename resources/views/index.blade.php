<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="{{ asset('asset/img/logoBumdes.png') }}" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUMDes Putra Samudra Patimban</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <!-- NAVBAR -->
    <header id="main-navbar">
        <div class="nav-brand">
            <div class="brand-name">Putra Samudra Patimban</div>
            <div class="brand-clock" id="live-clock">00:00:00</div>
            <div class="brand-date" id="live-date">Memuat tanggal...</div>
        </div>

        <nav class="nav-links nav-desktop">
            <a href="#beranda" class="nav-link">Beranda</a>
            <a href="#tentang"  class="nav-link">Tentang</a>
            <a href="#kategori" class="nav-link">Layanan</a>
            <a href="{{ route('login') }}"    class="nav-link">Masuk</a>
            <a href="{{ route('register') }}" class="nav-link cta">Daftar</a>
        </nav>

        <button class="hamburger-btn" id="hamburger-btn" aria-label="Buka menu" aria-expanded="false">
            <span class="hbar"></span>
            <span class="hbar"></span>
            <span class="hbar"></span>
        </button>
    </header>

    <div class="mobile-drawer" id="mobile-drawer" aria-hidden="true">
        <div class="drawer-inner">
            <a href="#beranda" class="drawer-link" data-drawer-close>
                <i class="fas fa-home"></i> Beranda
            </a>
            <a href="#tentang" class="drawer-link" data-drawer-close>
                <i class="fas fa-info-circle"></i> Tentang
            </a>
            <a href="#kategori" class="drawer-link" data-drawer-close>
                <i class="fas fa-th-large"></i> Layanan
            </a>
            <div class="drawer-divider"></div>
            <div class="drawer-actions">
                <a href="{{ route('login') }}"    class="drawer-btn outline">Masuk</a>
                <a href="{{ route('register') }}" class="drawer-btn gold">Daftar</a>
            </div>
        </div>
    </div>
    <div class="drawer-backdrop" id="drawer-backdrop"></div>


    <!-- ===================================================
         HERO
         =================================================== -->
    <section class="hero" id="beranda">
        <img
            class="hero-img"
        src="https://res.cloudinary.com/duxq5a40j/image/upload/v1779850645/berandabg_d8l6rj.jpg"
            alt="Pantai Patimban"
        >
        <div class="hero-overlay"></div>

        <!-- Scroll indicator -->
        <div class="hero-scroll-hint">
            <p>Scroll</p>
            <div class="scroll-line"></div>
        </div>



        <!-- Main copy -->
        <div class="hero-content">
            <div class="hero-tag">
                <span></span>
                Badan Usaha Milik Desa
            </div>
            <h1 class="hero-title">
                Putra<br>
                <em>Samudra</em><br>
                Patimban
            </h1>
            <p class="hero-desc">
                Menggerakkan ekonomi desa melalui ekosistem perdagangan digital yang transparan dan menguntungkan seluruh warga.
            </p>
            <div class="hero-actions">
                <a href="{{ route('register.mitra') }}" class="btn-gold">
                    <i class="fas fa-handshake"></i>
                    Jadi Mitra
                </a>
                <a href="#kategori" class="btn-outline">
                    <i class="fas fa-compass"></i>
                    Jelajahi Produk
                </a>
            </div>
        </div>
    </section>


    <!-- ===================================================
         TENTANG
         =================================================== -->
    <section id="tentang" class="section">
        <div class="about-text reveal">
            <div class="label-tag">
                <i class="fas fa-anchor" style="font-size:0.65rem;"></i>
                Profil BUMDes
            </div>
            <h2 class="section-heading">
                Mendorong Kemandirian<br>Ekonomi Desa Patimban
            </h2>
            <p class="section-sub">
                BUMDes Putra Samudra Patimban menjembatani para pelaku UKM desa dengan pelanggan — menciptakan ekosistem perdagangan yang sehat, modern, dan berdampak nyata bagi warga.
            </p>

            <div class="about-pillars">
                <div class="pillar reveal reveal-delay-1">
                    <div class="pillar-icon" style="background:#fef9ec;">
                        <i class="fas fa-bullseye" style="color:#c9a84c;"></i>
                    </div>
                    <h5>Visi</h5>
                    <p>Pilar ekonomi desa yang modern dan berdaya saing tinggi.</p>
                </div>
                <div class="pillar reveal reveal-delay-2">
                    <div class="pillar-icon" style="background:#eff6ff;">
                        <i class="fas fa-users" style="color:#3b82f6;"></i>
                    </div>
                    <h5>Misi</h5>
                    <p>Pemberdayaan mitra lokal dan digitalisasi layanan desa.</p>
                </div>
                <div class="pillar reveal reveal-delay-3">
                    <div class="pillar-icon" style="background:#ecfdf5;">
                        <i class="fas fa-leaf" style="color:#10b981;"></i>
                    </div>
                    <h5>Nilai</h5>
                    <p>Transparansi, kolaborasi, dan keberlanjutan komunitas.</p>
                </div>
                <div class="pillar reveal reveal-delay-4">
                    <div class="pillar-icon" style="background:#fdf4ff;">
                        <i class="fas fa-star" style="color:#a855f7;"></i>
                    </div>
                    <h5>Komitmen</h5>
                    <p>Memberikan dampak ekonomi nyata bagi seluruh warga desa.</p>
                </div>
            </div>
        </div>

        <div class="about-visual reveal reveal-delay-2">
            <div class="about-deco"></div>
            <div class="about-img-wrap">
                <img  src="https://res.cloudinary.com/duxq5a40j/image/upload/v1779850645/berandabg_d8l6rj.jpg">
            </div>
            <div class="about-badge">
                <strong>10+</strong>
                <span>Tahun Melayani Desa</span>
            </div>
        </div>
    </section>


    <!-- ===================================================
         KATEGORI
         =================================================== -->
    <section id="kategori" class="section">
        <div class="kategori-header reveal">
            <div class="label-tag">
                <i class="fas fa-th-large" style="font-size:0.65rem;"></i>
                Layanan Kami
            </div>
            <h2 class="section-heading">Katalog &amp; Layanan</h2>
            <p class="section-sub">
                Produk fisik dan jasa profesional dari warga desa, tersedia dalam satu platform terpadu.
            </p>
        </div>

        <div class="kategori-grid">
            <!-- Produk -->
            <div class="kat-card blue reveal reveal-delay-1">
                <div class="kat-icon-wrap">
                    <i class="fas fa-box-open" style="color:#3b82f6;"></i>
                </div>
                <h3>Katalog Produk</h3>
                <p>
                    Sembako, kuliner khas Patimban, olahan laut segar, hingga kerajinan tangan kreatif — langsung dari produsen desa.
                </p>
                <a href="{{ route('login') }}" class="kat-link">
                    Eksplor Produk
                    <span class="arrow"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>

            <!-- Jasa -->
            <div class="kat-card green reveal reveal-delay-2">
                <div class="kat-icon-wrap">
                    <i class="fas fa-tools" style="color:#10b981;"></i>
                </div>
                <h3>Layanan Jasa</h3>
                <p>
                    Bengkel, jasa cukur, perbaikan rumah, sewa alat tangkap ikan, dan berbagai keahlian warga siap membantu kebutuhan Anda.
                </p>
                <a href="{{ route('login') }}" class="kat-link">
                    Cari Layanan
                    <span class="arrow"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </section>


    <!-- ===================================================
         PREVIEW PRODUK & JASA
         =================================================== -->
    <section id="produk" class="section">
        <div class="produk-header">
            <div>
                <div class="label-tag">
                    <i class="fas fa-store" style="font-size:0.65rem;"></i>
                    Pilihan Terkini
                </div>
                <h2 class="section-heading">Produk &amp; Jasa Desa</h2>
                <p class="section-sub">Dukung UMKM desa dengan membeli produk asli lokal.</p>
            </div>
        </div>

        <div class="produk-grid">

            {{-- === Loop JASA === --}}
            @foreach($jasas as $jasa)
            <div class="produk-card reveal">
                <div class="produk-img-wrap">
                    <img
                     src="{{ $jasa->gambar ? $jasa->gambar : asset('asset/img/default-product.jpg') }}"
                        alt="{{ $jasa->nama_jasa }}"
                        loading="lazy"
                    >
                    <span class="produk-badge">Jasa</span>
                </div>
                <div class="produk-body">
                    <h4>{{ $jasa->nama_jasa }}</h4>
                    <p class="produk-price">Rp&nbsp;{{ number_format($jasa->harga, 0, ',', '.') }}</p>
                    @auth
                        <a href="/transaksi/jasa/{{ $jasa->id }}" class="btn-buy">
                            <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-buy alt">
                            <i class="fas fa-lock"></i> Login untuk Pesan
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach

            {{-- === Loop PRODUK === --}}
            @foreach($produks as $produk)
            <div class="produk-card reveal">
                <div class="produk-img-wrap">
                    <img
                       src="{{ $produk->gambar ? $produk->gambar : asset('asset/img/default-product.jpg') }}"
                        alt="{{ $produk->nama_produk }}"
                        loading="lazy"
                    >
                    <span class="produk-badge">Produk</span>
                </div>
                <div class="produk-body">
                    <h4>{{ $produk->nama_produk }}</h4>
                    <p class="produk-price">Rp&nbsp;{{ number_format($produk->harga, 0, ',', '.') }}</p>
                    @auth
                        <a href="/transaksi/produk/{{ $produk->id }}" class="btn-buy">
                            <i class="fas fa-shopping-cart"></i> Beli Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-buy alt">
                            <i class="fas fa-lock"></i> Login untuk Beli
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach

        </div>
    </section>


    <!-- ===================================================
         FOOTER
         =================================================== -->
    <footer>
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="brand-name-f">Putra Samudra Patimban</div>
                <p>Badan Usaha Milik Desa — Menggerakkan ekonomi dan kesejahteraan warga Desa Patimban.</p>
            </div>
            <div>
                <p style="font-size:0.72rem;color:#8899b0;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px;">Navigasi</p>
                <div class="footer-links">
                    <a href="#beranda">Beranda</a>
                    <a href="#tentang">Tentang</a>
                    <a href="#kategori">Layanan</a>
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                </div>
            </div>
            <div>
                <p style="font-size:0.72rem;color:#8899b0;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px;">Bergabung</p>
                <div class="footer-links">
                    <a href="{{ route('register.mitra') }}">Daftar sebagai Mitra</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 BUMDes Putra Samudra Patimban. Seluruh hak dilindungi.</p>
            <p class="made-by">Dikembangkan oleh Kelompok Regga Vision Mahasiswa D3 Sistem Informasi Jurusan Teknologi Informasi dan Komputer Politeknik Negeri Subang</p>
        </div>
    </footer>


    <!-- ===================================================
         SCRIPTS
         =================================================== -->
    <script>
    /* ---- Live Clock ---- */
    (function clock() {
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        function tick() {
            const n = new Date();
            const h = String(n.getHours()).padStart(2,'0');
            const m = String(n.getMinutes()).padStart(2,'0');
            const s = String(n.getSeconds()).padStart(2,'0');
            document.getElementById('live-clock').textContent = `${h} : ${m} : ${s}`;
            document.getElementById('live-date').textContent =
                `${days[n.getDay()]}, ${n.getDate()} ${months[n.getMonth()]} ${n.getFullYear()}`;
        }
        setInterval(tick, 1000); tick();
    })();

    /* ---- Hamburger drawer ---- */
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const drawerBackdrop = document.getElementById('drawer-backdrop');

    function openDrawer() {
        mobileDrawer.classList.add('open');
        drawerBackdrop.classList.add('open');
        hamburgerBtn.classList.add('active');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        mobileDrawer.classList.remove('open');
        drawerBackdrop.classList.remove('open');
        hamburgerBtn.classList.remove('active');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }
    hamburgerBtn.addEventListener('click', () => {
        mobileDrawer.classList.contains('open') ? closeDrawer() : openDrawer();
    });
    drawerBackdrop.addEventListener('click', closeDrawer);
    document.querySelectorAll('[data-drawer-close]').forEach(el => {
        el.addEventListener('click', closeDrawer);
    });

    /* ---- Navbar scroll class ---- */
    const navbar = document.getElementById('main-navbar');
    const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 60);
    window.addEventListener('scroll', onScroll, { passive: true });

    /* ---- Smooth anchor scroll ---- */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    /* ---- Scroll Reveal ---- */
    (function reveal() {
        const els = document.querySelectorAll('.reveal');
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        els.forEach(el => io.observe(el));
    })();

    /* ---- Hover tilt effect on cards (subtle) ---- */
    document.querySelectorAll('.kat-card, .produk-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width  - 0.5) * 6;
            const y = ((e.clientY - rect.top)  / rect.height - 0.5) * 6;
            card.style.transform = `translateY(-6px) rotateY(${x}deg) rotateX(${-y}deg)`;
            card.style.transition = 'transform 0.1s ease';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            card.style.transition = 'transform 0.45s cubic-bezier(0.23,1,0.32,1)';
        });
    });
    </script>

</body>
</html>
