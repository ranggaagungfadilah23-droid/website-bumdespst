<x-guest-layout :centered="true">

    {{-- Status session --}}
    <x-auth-session-status class="status-success" :status="session('status')" />

    {{-- Error validasi --}}
    @if ($errors->any())
        <div class="status-error">
            <i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>
            @if ($errors->first('login') == 'These credentials do not match our records.')
                Email atau kata sandi yang Anda masukkan salah.
            @else
                {{ $errors->first() }}
            @endif
        </div>
    @endif

    {{-- Logo --}}
    <div class="logo-badge">
        <img src="{{ asset('asset/img/logoBumdes.png') }}"
             onerror="this.src='https://via.placeholder.com/54x54?text=B'"
             alt="Logo BUMDes">
    </div>

    {{-- Judul & Subjudul --}}
    <h2 class="card-title">Masuk ke Akun</h2>
    <p class="card-sub" style="margin-bottom: 20px;">
        Gunakan email atau username Anda untuk masuk.
    </p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email atau Username --}}
        <div class="field-group">
            <label class="field-label" for="login">Email atau Username</label>
            <div class="field-inner">
                <i class="fas fa-user field-icon"></i>
                <input id="login"
                       class="field-input"
                       type="text"
                       name="login"
                       placeholder="email@contoh.com atau username"
                       value="{{ old('login') }}"
                       required autofocus autocomplete="username">
            </div>
        </div>

        {{-- Password --}}
        <div class="field-group">
            <label class="field-label" for="password">Kata Sandi</label>
            <div class="field-inner">
                <i class="fas fa-lock field-icon"></i>
                <input id="password"
                       class="field-input"
                       type="password"
                       name="password"
                       placeholder="Masukkan kata sandi"
                       required autocomplete="current-password">
                <button type="button" class="toggle-eye" id="togglePassword" aria-label="Tampilkan sandi">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        {{-- Lupa sandi --}}
        @if (Route::has('password.request'))
            <div class="forgot-row">
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa kata sandi?</a>
            </div>
        @endif

        {{-- Tombol masuk --}}
        <button type="submit" class="btn-primary">
            <span>Masuk Sekarang</span>
            <i class="fas fa-arrow-right" style="font-size:12px;"></i>
        </button>

        {{-- Divider --}}
        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">atau</span>
            <div class="divider-line"></div>
        </div>

        {{-- Login Google --}}
        <a href="{{ url('/auth/google') }}" class="btn-google">
            <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google">
            <span>Lanjutkan dengan Google</span>
        </a>

        {{-- Daftar --}}
        <p class="card-footer">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar gratis</a>
        </p>

    </form>

    @push('js')
    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const pwInput   = document.getElementById('password');

        if (toggleBtn && pwInput) {
            toggleBtn.addEventListener('click', function () {
                const isHidden = pwInput.type === 'password';
                pwInput.type   = isHidden ? 'text' : 'password';
                const icon     = this.querySelector('i');
                icon.classList.toggle('fa-eye',       !isHidden);
                icon.classList.toggle('fa-eye-slash',  isHidden);
                this.style.color = isHidden ? '#38bdf8' : '';
            });
        }
    </script>
    @endpush

</x-guest-layout>