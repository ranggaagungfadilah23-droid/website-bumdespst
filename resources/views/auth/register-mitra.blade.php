<x-guest-layout>
<style>
    .reg-title {
        font-size: 20px; font-weight: 800; margin-bottom: 14px;
        text-align: center; color: #fff; letter-spacing: -0.3px;
    }

    /* Google */
    .btn-google {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; background: #fff; color: #333;
        border: 2px solid #000; border-radius: 8px; padding: 9px;
        font-size: 13px; font-weight: 700; text-decoration: none;
        transition: 0.2s; margin-bottom: 12px;
    }
    .btn-google:hover { background: #f5f5f5; }

    /* Divider */
    .div-line { display: flex; align-items: center; margin-bottom: 14px; }
    .div-line::before, .div-line::after { content:''; flex:1; border-bottom: 1px solid rgba(255,255,255,0.15); }
    .div-line span { padding: 0 10px; font-size: 10px; color: rgba(255,255,255,0.4); font-weight: 700; letter-spacing: 1.5px; }

    /* 2-col grid */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; }
    .span2   { grid-column: span 2; }

    /* Mobile: 1 kolom, compact */
    @media (max-width: 600px) {
        .two-col { grid-template-columns: 1fr; gap: 8px; }
        .span2   { grid-column: span 1; }
        .reg-title { font-size: 17px; margin-bottom: 10px; }
        .lbl { font-size: 9px; letter-spacing: 0.4px; }
        .ic { padding: 8px 11px; font-size: 13px; }
        textarea.ic { min-height: 58px; }
        .btn-google { padding: 8px; margin-bottom: 10px; }
        .div-line { margin-bottom: 10px; }
        .radio-item { padding: 7px; }
        .upload-area { padding: 9px 11px; }
        .btn-submit { padding: 10px; font-size: 13px; }
        .fg { gap: 3px; }
    }

    /* Field */
    .fg { display: flex; flex-direction: column; gap: 4px; }
    .lbl { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.8px; }

    /* Input */
    .ic {
        width: 100%; background: #fdf7e3; border: 1.5px solid #1a1a1a;
        border-radius: 8px; padding: 9px 12px; font-size: 13px;
        font-weight: 600; color: #333; outline: none; transition: 0.2s;
        box-sizing: border-box; font-family: inherit;
    }
    .ic:focus { border-color: #8a9a5b; box-shadow: 0 0 0 3px rgba(138,154,91,0.25); }
    textarea.ic { min-height: 74px; resize: none; }

    /* Password */
    .pw { position: relative; }
    .pw .ic { padding-right: 36px; }
    .pw-eye { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer; font-size: 13px; }
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear,
    input[type="password"]::-webkit-contacts-auto-fill-button,
    input[type="password"]::-webkit-credentials-auto-fill-button { display: none !important; }
    input[type="text"].ic::-webkit-contacts-auto-fill-button,
    input[type="text"].ic::-webkit-credentials-auto-fill-button { display: none !important; }

    /* Radio */
    .radio-row { display: flex; gap: 8px; }
    .radio-item {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 7px;
        background: rgba(255,255,255,0.07); border: 1.5px solid rgba(255,255,255,0.2);
        padding: 9px; border-radius: 8px; cursor: pointer; transition: 0.2s;
    }
    .radio-item:hover { background: rgba(255,255,255,0.13); }
    .radio-item input { accent-color: #fbbc05; width: 15px; height: 15px; }
    .radio-item span  { color: #fff; font-size: 12px; font-weight: 700; }

    /* Upload */
    .upload-area {
        background: rgba(255,255,255,0.04); border: 1.5px dashed rgba(255,255,255,0.2);
        border-radius: 10px; padding: 12px 14px; display: flex; flex-direction: column; gap: 8px;
    }
    .upload-btn-lbl {
        display: block; width: 100%; background: #fdf7e3; border: 1.5px solid #000;
        border-radius: 7px; padding: 7px; text-align: center;
        font-size: 11px; font-weight: 700; color: #333;
    }
    .file-row { display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .file-row i { font-size: 24px; color: #fbbc05; }
    .file-name { color: #fff; font-size: 12px; font-weight: 600; text-decoration: underline; }

    /* Submit */
    .btn-submit {
        width: 100%; background: #8a9a5b; border: 2px solid #000;
        border-radius: 8px; padding: 11px; font-size: 14px;
        font-weight: 800; color: #000; cursor: pointer; transition: 0.2s;
    }
    .btn-submit:hover { background: #9cb066; transform: translateY(-1px); }

    /* Note & Syarat */
    .note { color: #fbbc05; font-size: 10px; margin-top: 1px; }
    .syarat-row { display: flex; align-items: center; gap: 6px; margin-top: 8px; justify-content: center; }
    .syarat-row label { color: rgba(255,255,255,0.7); font-size: 11px; font-weight: 600; text-decoration: underline; cursor: pointer; }

    .file-row * { cursor: pointer; }
.file-name { cursor: pointer; user-select: none; }
</style>

<h1 class="reg-title">Pendaftaran Mitra BUMDes</h1>

{{-- ✅ TAMBAHAN: Mengirimkan parameter role=mitra ke URL Google Redirect --}}
<a href="{{ route('auth.google.redirect', ['role' => 'mitra']) }}" class="btn-google">
    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" width="16" alt="G">
    Daftar dengan Google
</a>

<div class="div-line"><span>ATAU DAFTAR MANUAL</span></div>

<form method="POST" action="{{ route('mitra.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="two-col">

        {{-- Nama Pemilik --}}
        <div class="fg">
            <label class="lbl">Nama Pemilik</label>
            <input type="text" name="nama_pemilik" class="ic" placeholder="Nama lengkap"
                   value="{{ session('google_name') ?? old('nama_pemilik') }}" required>
        </div>

        <div class="fg">
    <label class="lbl">Username</label>
    <input type="text" name="username" class="ic" placeholder="username_unik"
           value="{{ old('username') }}" pattern="^[a-zA-Z0-9_]{3,20}$" 
           title="3-20 karakter, huruf/angka/underscore" required>
    @error('username') <span class="err">{{ $message }}</span> @enderror
</div>

        {{-- Nama Usaha --}}
        <div class="fg">
            <label class="lbl">Nama Usaha</label>
            <input type="text" name="nama_usaha" class="ic" placeholder="Nama usaha Anda"
                   value="{{ old('nama_usaha') }}" required>
        </div>

        {{-- Email --}}
        <div class="fg">
            <label class="lbl">Email Aktif</label>
            <input type="email" name="email" class="ic" placeholder="email@contoh.com"
                   value="{{ session('google_email') ?? old('email') }}"
                   {{ session('google_email') ? 'readonly' : 'required' }}>
        </div>

        {{-- NIK --}}
        <div class="fg">
            <label class="lbl">NIK (16 Digit)</label>
            <input type="text" name="nik" class="ic" placeholder="3212XXXXXXXXXXXX"
                   value="{{ old('nik') }}" maxlength="16" pattern="[0-9]{16}" required>
        </div>

        {{-- No HP --}}
        <div class="fg">
            <label class="lbl">WhatsApp <span style="color:#fbbc05">(awali 62)</span></label>
            <input type="text" name="no_hp" class="ic" placeholder="628123456789"
                   value="{{ old('no_hp') }}" pattern="^62[0-9]*$" required>
            <span class="note">* Gunakan 62, bukan 0 di depan</span>
        </div>

        {{-- Dusun --}}
        <div class="fg">
            <label class="lbl">Dusun</label>
            <input type="text" name="dusun" class="ic" placeholder="Nama dusun"
                   value="{{ old('dusun') }}" required>
        </div>

        {{-- Alamat --}}
        <div class="fg span2">
            <label class="lbl">Alamat Usaha</label>
            <textarea name="alamat_usaha" class="ic" placeholder="Jl. Contoh No. 1, Desa Patimban..." required>{{ old('alamat_usaha') }}</textarea>
        </div>

        {{-- Password --}}
        <div class="fg">
            <label class="lbl">Password</label>
            <div class="pw">
                <input type="password" name="password" id="password" class="ic" placeholder="Buat password" required>
                <i class="fas fa-eye pw-eye" onclick="togglePw('password', this)"></i>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div class="fg">
            <label class="lbl">Konfirmasi Password</label>
            <div class="pw">
                <input type="password" name="password_confirmation" id="pw2" class="ic" placeholder="Ulangi password" required>
                <i class="fas fa-eye pw-eye" onclick="togglePw('pw2', this)"></i>
            </div>
        </div>

        {{-- Jenis Usaha --}}
        <div class="fg">
            <label class="lbl">Jenis Usaha</label>
            <div class="radio-row">
                <label class="radio-item">
                    <input type="radio" name="jenis_usaha" value="Jasa"
                           {{ old('jenis_usaha') == 'Jasa' ? 'checked' : '' }} required>
                    <span>Jasa</span>
                </label>
                <label class="radio-item">
                    <input type="radio" name="jenis_usaha" value="Produk"
                           {{ old('jenis_usaha') == 'Produk' ? 'checked' : '' }}>
                    <span>Produk</span>
                </label>
            </div>
        </div>

        {{-- Upload Dokumen --}}
        <div class="fg">
            <label class="lbl">Dokumen Pendukung</label>
            <div class="upload-area">
                <span class="upload-btn-lbl">📎 Upload Dokumen</span>
              <label class="file-row" for="sku-upload" style="cursor:pointer; user-select:none;">
                    <i class="fas fa-folder-open"></i>
                   <span class="file-name" id="sku-text" style="cursor:pointer; user-select:none;">Surat Keterangan Usaha</span>
                    <input type="file" name="sku" id="sku-upload" style="display:none"
                           accept=".jpg,.png,.pdf"
                           onchange="updateFileName(this,'sku-text')">
                </label>
            </div>
        </div>

        {{-- Submit + Syarat --}}
        <div class="fg span2" style="margin-top:2px">
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Simpan Data Pendaftaran
            </button>
            <div class="syarat-row">
                <input type="checkbox" id="syarat" name="syarat" required
                       style="accent-color:#fbbc05; width:14px; height:14px;">
                <label for="syarat">Saya menyetujui Syarat dan Ketentuan</label>
            </div>
        </div>

    </div>
</form>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({ icon:'success', title:'Berhasil!', text:"{{ session('success') }}", confirmButtonColor:'#8a9a5b' });
    @endif
    @if($errors->any())
        Swal.fire({
            icon:'error', title:'Oops...',
            html:`<ul style="text-align:left;font-size:13px;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>`,
            confirmButtonColor:'#d33'
        });
    @endif
    @if(session('error'))
        Swal.fire({ icon:'error', title:'Kesalahan', text:"{{ session('error') }}", confirmButtonColor:'#d33' });
    @endif

    function updateFileName(input, id) {
        const el = document.getElementById(id);
        if (input.files?.[0]) {
            let n = input.files[0].name;
            el.innerText = n.length > 24 ? n.substring(0,21)+'...' : n;
            el.style.color = '#fbbc05';
        }
    }

    function togglePw(id, icon) {
        const el = document.getElementById(id);
        el.type = el.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }
</script>
@endpush
</x-guest-layout>
