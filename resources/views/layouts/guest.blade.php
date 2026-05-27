<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
   <link rel="icon" href="https://res.cloudinary.com/duxq5a40j/image/upload/v1779851100/logoBumdes_nsewm6.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BUMDes Putra Samudra Patimban') }}</title>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            color: #fff;
            overflow-x: hidden;
            background-color: #040d1a;
        }

        body {
           background-image: url("https://res.cloudinary.com/duxq5a40j/image/upload/v1779850645/berandabg_d8l6rj.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            background: linear-gradient(135deg, rgba(2, 8, 22, 0.9) 0%, rgba(4, 18, 48, 0.8) 50%, rgba(2, 8, 22, 0.92) 100%);
            pointer-events: none;
        }

        /* ── Layout ── */
        .page-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 4rem;
            padding: 3rem 8%;
        }

        .page-wrap.center-only {
            display: flex;
            justify-content: center;
            grid-template-columns: none;
        }

        /* ── Brand Section ── */
        .brand-col { animation: slideInLeft 0.8s ease-out both; }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .brand-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #38bdf8;
            margin-bottom: 1.5rem;
        }

        .brand-eyebrow::before { content: ''; width: 30px; height: 1px; background: rgba(56, 189, 248, 0.5); }
        .brand-headline { font-family: 'DM Serif Display', serif; font-size: clamp(2.5rem, 4vw, 4rem); line-height: 1.1; margin-bottom: 1.5rem; }
        .brand-headline em { color: #38bdf8; font-style: italic; }
        .brand-sub { font-size: 16px; color: rgba(255,255,255,0.5); max-width: 450px; line-height: 1.6; }

        /* ── Auth Card ── */
        .auth-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            padding: 40px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .w-wide { max-width: 850px !important; }

        /* ── Auth Components ── */
        .logo-badge {
            width: 85px; height: 85px;
            margin: 0 auto 20px auto;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px; overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .logo-badge img { width: 100%; height: auto; display: block; }

        .card-title { font-family: 'DM Serif Display', serif; font-size: 1.9rem; text-align: center; margin-bottom: 8px; color: #fff; }
        .card-sub { font-size: 14px; text-align: center; color: rgba(255,255,255,0.4); margin-bottom: 30px; }

        /* ── Forms ── */
        .field-group { margin-bottom: 20px; }
        .field-label { display: block; font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.5); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .field-inner { position: relative; width: 100%; }
        .field-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.3); font-size: 14px; }

        .field-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 13px 15px 13px 45px;
            border-radius: 12px;
            color: white;
            font-size: 14px;
            transition: all 0.3s;
            outline: none;
        }
        .field-input:focus { border-color: #38bdf8; background: rgba(56, 189, 248, 0.05); }

        .toggle-eye { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: rgba(255,255,255,0.3); cursor: pointer; }

        /* ── Buttons ── */
        .btn-primary {
            width: 100%; padding: 15px; border-radius: 12px; border: none;
            background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
            color: white; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3); transition: 0.3s;
        }
        .btn-primary:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .btn-auth-action {
            display: flex; align-items: center; justify-content: center; gap: 12px;
            width: 100%; padding: 16px; background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12); border-radius: 16px;
            color: #fff !important; text-decoration: none; font-weight: 600; margin-bottom: 1rem;
        }

        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 12px; background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 12px;
            color: #fff; text-decoration: none; font-size: 14px; transition: 0.3s;
        }
        .btn-google img { width: 18px; }

        /* ── Others ── */
        .divider { display: flex; align-items: center; text-align: center; margin: 20px 0; color: rgba(255,255,255,0.2); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .divider:not(:empty)::before { margin-right: .5em; }
        .divider:not(:empty)::after { margin-left: .5em; }

        .forgot-row { text-align: right; margin: -10px 0 20px 0; }
        .forgot-link { font-size: 12px; color: rgba(255,255,255,0.4); text-decoration: none; }

        .status-error { background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.2); text-align: center; }

        .footer-text, .card-footer { text-align: center; margin-top: 1.5rem; font-size: 14px; color: rgba(255,255,255,0.4); }
        .footer-text a, .card-footer a { color: #38bdf8 !important; text-decoration: none; font-weight: 700; }

        @media (max-width: 992px) {
            .page-wrap { grid-template-columns: 1fr; padding: 2rem; }
            .brand-col { display: none; }
        }
    </style>
</head>
<body>
    <div class="page-wrap {{ $centered ?? false ? 'center-only' : '' }}">
        <div class="brand-col">
            <span class="brand-eyebrow">Sistem Informasi</span>
            <h1 class="brand-headline">Kelola Desa<br><em>Lebih Cerdas,</em><br>Lebih Maju</h1>
            <p class="brand-sub">Platform digital terpadu untuk pengelolaan Badan Usaha Milik Desa yang transparan dan efisien.</p>
        </div>

        <div class="card-col {{ Request::is('register/mitra*') ? 'w-wide' : '' }}">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    </div>
    @stack('js')
</body>
</html>
