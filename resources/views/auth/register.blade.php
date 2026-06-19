<x-guest-layout>
    <style>
        .auth-title {
            font-family: 'DM Sans', sans-serif;
            font-weight: 800;
            font-size: clamp(1.7rem, 4vw, 2.2rem);
            line-height: 1.15;
            color: #fff;
            letter-spacing: -0.3px;
            margin-bottom: 28px;
            text-align: center;
        }

        .btn-auth-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 18px 24px;
            margin-bottom: 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
        }
        .btn-auth-action i {
            font-size: 1.1rem;
            color: var(--gold, #c9a84c);
        }
        .btn-auth-action:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(201, 168, 76, 0.4);
            transform: translateY(-2px);
        }

        /* CSS khusus yang hanya ada di halaman ini */
        .guide-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            margin-top: 22px;
            margin-bottom: 40px;
            transition: color 0.3s ease;
        }
        .guide-link i {
            font-size: 30px;
            color: var(--gold-lt, #f0d080);
            transition: transform 0.3s ease;
        }
        .guide-link:hover { color: #fff; }
        .guide-link:hover i { transform: translateX(4px); }

        .footer-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            text-align: center;
        }
        .footer-text a {
            color: var(--gold, #c9a84c);
            font-weight: 700;
            text-decoration: none;
        }
        .footer-text a:hover { text-decoration: underline; }
    </style>

    <h1 class="auth-title">Registrasi<br>Sebagai</h1>

    <a href="{{ route('register.mitra') }}" class="btn-auth-action">
        <i class="far fa-handshake"></i> Daftar Mitra
    </a>

    <a href="{{ route('register.pelanggan') }}" class="btn-auth-action">
        <i class="far fa-user-circle"></i> Pelanggan
    </a>

    <a href="{{ route('panduan') }}" class="guide-link">
        Panduan Pendaftaran <i class="fas fa-arrow-circle-right"></i>
    </a>

    <div class="footer-text">
        Sudah punya akun?
        <a href="{{ route('login') }}">Login</a>
    </div>
</x-guest-layout>