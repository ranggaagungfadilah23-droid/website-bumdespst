<x-guest-layout>
<style>
    .reg-title    { font-size: 20px; font-weight: 800; margin-bottom: 4px; text-align: center; color: #fff; }
    .reg-subtitle { font-size: 12px; text-align: center; color: rgba(255,255,255,0.5); margin-bottom: 14px; }

    /* Google Button & Divider */
    .btn-google {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; background: #fff; color: #333;
        border: 2px solid #000; border-radius: 8px; padding: 9px;
        font-size: 13px; font-weight: 700; text-decoration: none;
        transition: 0.2s; margin-bottom: 12px;
    }
    .btn-google:hover { background: #f5f5f5; }

    .div-line { display: flex; align-items: center; margin-bottom: 14px; }
    .div-line::before, .div-line::after { content:''; flex:1; border-bottom: 1px solid rgba(255,255,255,0.15); }
    .div-line span { padding: 0 10px; font-size: 10px; color: rgba(255,255,255,0.4); font-weight: 700; letter-spacing: 1.5px; }

    /* 2-col grid */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 9px 18px; }
    .span2   { grid-column: span 2; }

    /* Mobile */
    @media (max-width: 600px) {
        .two-col { grid-template-columns: 1fr; gap: 8px; }
        .span2   { grid-column: span 1; }
        .reg-title { font-size: 17px; }
        .reg-subtitle { font-size: 11px; margin-bottom: 10px; }
        .ic { padding: 8px 11px !important; font-size: 13px !important; }
        .fg { gap: 3px; }
        .radio-item { padding: 8px !important; }
        textarea.ic { min-height: 58px !important; }
        .btn-google { padding: 8px; margin-bottom: 10px; }
        .div-line { margin-bottom: 10px; }
    }

    /* Field */
    .fg  { display: flex; flex-direction: column; gap: 4px; }
    .lbl { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.8px; }

    /* Input */
    .ic {
        width: 100%; background: #fdf7e3; border: 1.5px solid #1a1a1a;
        border-radius: 8px; padding: 9px 12px; font-size: 13px;
        font-weight: 600; color: #333; outline: none; transition: 0.2s;
        box-sizing: border-box; font-family: inherit;
    }
    .ic:focus { border-color: #8a9a5b; box-shadow: 0 0 0 3px rgba(138,154,91,0.25); }
    textarea.ic { min-height: 68px; resize: none; }

    /* Password hide native eye */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none !important; }

    /* WA prefix */
    .wa-wrap {
        display: flex; align-items: stretch;
        background: #fdf7e3; border: 1.5px solid #1a1a1a; border-radius: 8px; overflow: hidden;
    }
    .wa-prefix {
        padding: 9px 12px; font-size: 13px; font-weight: 800; color: #333;
        border-right: 1.5px solid #1a1a1a; background: #f0e8c8; white-space: nowrap;
        display: flex; align-items: center;
    }
    .wa-input {
        flex: 1; border: none; outline: none; background: #fdf7e3;
        padding: 9px 12px; font-size: 13px; font-weight: 600; color: #333; font-family: inherit;
    }
    .wa-wrap:focus-within { border-color: #8a9a5b; box-shadow: 0 0 0 3px rgba(138,154,91,0.25); }

    /* Username prefix */
    .user-wrap {
        display: flex; align-items: stretch;
        background: #fdf7e3; border: 1.5px solid #1a1a1a; border-radius: 8px; overflow: hidden;
    }
    .user-prefix {
        padding: 9px 10px; font-size: 13px; font-weight: 800; color: #333;
        border-right: 1.5px solid #1a1a1a; background: #f0e8c8;
        display: flex; align-items: center;
    }
    .user-input {
        flex: 1; min-width: 0; border: none; outline: none; background: #fdf7e3;
        padding: 9px 12px; font-size: 13px; font-weight: 600; color: #333; font-family: inherit;
    }
    .user-wrap:focus-within { border-color: #8a9a5b; box-shadow: 0 0 0 3px rgba(138,154,91,0.25); }

    /* Hint text */
    .hint { display: block; font-size: 10px; color: rgba(255,255,255,0.45); margin-top: 1px; }

    /* Password wrapper */
    .pw { position: relative; }
    .pw .ic { padding-right: 36px; }
    .pw-eye { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer; font-size: 13px; }

    /* Gender radio */
    .radio-row { display: flex; gap: 8px; }
    .radio-item {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 7px;
        background: rgba(255,255,255,0.07); border: 1.5px solid rgba(255,255,255,0.2);
        padding: 9px; border-radius: 8px; cursor: pointer; transition: 0.2s;
        color: #fff; font-size: 12px; font-weight: 700;
    }
    .radio-item:hover { background: rgba(255,255,255,0.14); }
    .radio-item input { accent-color: #fbbc05; width: 15px; height: 15px; }

    /* Error */
    .err { color: #ff6b6b; font-size: 11px; font-weight: 700; }

    /* Submit */
    .btn-submit {
        width: 100%; background: #8a9a5b; border: 2px solid #000;
        border-radius: 8px; padding: 11px; font-size: 14px;
        font-weight: 800; color: #000; cursor: pointer; transition: 0.2s; margin-top: 10px;
    }
    .btn-submit:hover { background: #9cb066; transform: translateY(-1px); }

    /* Login link */
    .login-link { text-align: center; margin-top: 10px; font-size: 12px; color: rgba(255,255,255,0.6); }
    .login-link a { color: #fbbc05; font-weight: 700; text-decoration: none; }
    .login-link a:hover { text-decoration: underline; }
</style>

<h1 class="reg-title">Daftar Pelanggan</h1>
<p class="reg-subtitle">Lengkapi data diri Anda untuk mulai berbelanja di BUMDes Patimban.</p>

{{-- Tombol Login Google dengan parameter role=customer --}}
<a href="{{ route('auth.google.redirect', ['role' => 'customer']) }}" class="btn-google">
    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" width="16" alt="G">
    Daftar dengan Google
</a>

<div class="div-line"><span>ATAU DAFTAR MANUAL</span></div>

<form method="POST" action="{{ route('register') }}">
    @csrf
    <input type="hidden" name="role" value="customer">

    <div class="two-col">

        {{-- Nama Lengkap --}}
        <div class="fg span2">
            <label class="lbl">Nama Lengkap</label>
            <input type="text" name="name" class="ic" placeholder="Nama lengkap Anda"
                   value="{{ session('google_name') ?? old('name') }}" required autofocus>
            @error('name') <span class="err">{{ $message }}</span> @enderror
        </div>

        {{-- Username --}}
        <div class="fg span2">
            <label class="lbl">Username</label>
            <div class="user-wrap">
                <span class="user-prefix">@</span>
                <input type="text" name="username" class="user-input" placeholder="username_anda"
                       value="{{ old('username') }}" pattern="^[a-zA-Z0-9_]{3,20}$" required>
            </div>
            <span class="hint">3-20 karakter • huruf, angka, dan underscore (_) saja, tanpa spasi</span>
            @error('username') <span class="err">{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div class="fg span2">
            <label class="lbl">Email</label>
            <input type="email" name="email" class="ic" placeholder="email@contoh.com"
                   value="{{ session('google_email') ?? old('email') }}"
                   {{ session('google_email') ? 'readonly' : 'required' }}>
            @error('email') <span class="err">{{ $message }}</span> @enderror
        </div>

        {{-- No WA --}}
        <div class="fg span2">
            <label class="lbl">Nomor WhatsApp</label>
            <div class="wa-wrap">
                <span class="wa-prefix">+62</span>
                <input type="number" name="no_wa" class="wa-input"
                       placeholder="8xx xxxx xxxx"
                       value="{{ old('no_wa') }}" required>
            </div>
            @error('no_wa') <span class="err">{{ $message }}</span> @enderror
        </div>

        {{-- Gender --}}
        <div class="fg span2">
            <label class="lbl">Jenis Kelamin</label>
            <div class="radio-row">
                <label class="radio-item">
                    <input type="radio" name="gender" value="L"
                           {{ old('gender') == 'L' ? 'checked' : '' }} required>
                    <i class="fas fa-mars"></i> Laki-laki
                </label>
                <label class="radio-item">
                    <input type="radio" name="gender" value="P"
                           {{ old('gender') == 'P' ? 'checked' : '' }}>
                    <i class="fas fa-venus"></i> Perempuan
                </label>
            </div>
            @error('gender') <span class="err">{{ $message }}</span> @enderror
        </div>

        {{-- Alamat --}}
        <div class="fg span2">
            <label class="lbl">Alamat Lengkap</label>
            <textarea name="alamat_lengkap" class="ic"
                      placeholder="Jalan, RT/RW, Dusun, Desa..." required>{{ old('alamat_lengkap') }}</textarea>
            @error('alamat_lengkap') <span class="err">{{ $message }}</span> @enderror
        </div>

        {{-- Password --}}
        <div class="fg">
            <label class="lbl">Password</label>
            <div class="pw">
                <input type="password" name="password" id="pw1" class="ic"
                       placeholder="Min. 8 karakter" required autocomplete="new-password">
                <i class="fas fa-eye pw-eye" onclick="togglePw('pw1',this)"></i>
            </div>
            @error('password') <span class="err">{{ $message }}</span> @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="fg">
            <label class="lbl">Konfirmasi Password</label>
            <div class="pw">
                <input type="password" name="password_confirmation" id="pw2" class="ic"
                       placeholder="Ulangi password" required autocomplete="new-password">
                <i class="fas fa-eye pw-eye" onclick="togglePw('pw2',this)"></i>
            </div>
        </div>

    </div>

    <button type="submit" class="btn-submit">
        <i class="fas fa-user-plus"></i> Daftar Sebagai Pelanggan
    </button>

    <div class="login-link">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</form>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({ icon:'success', title:'Berhasil!', text:"{{ session('success') }}", confirmButtonColor:'#8a9a5b' });
        @endif
        @if($errors->any())
            Swal.fire({ icon:'error', title:'Gagal Daftar', text:'Silakan periksa kembali data yang Anda masukkan.', confirmButtonColor:'#d33' });
        @endif
        @if(session('error'))
            Swal.fire({ icon:'error', title:'Kesalahan', text:"{{ session('error') }}", confirmButtonColor:'#d33' });
        @endif
    });

    function togglePw(id, icon) {
        const el = document.getElementById(id);
        el.type = el.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }
</script>
@endpush
</x-guest-layout>