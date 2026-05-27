<x-guest-layout :centered="true">

    @if ($errors->any())
        <div class="status-error">
            <i class="fas fa-circle-exclamation" style="margin-right:6px;"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Logo --}}
    <div class="logo-badge">
        <img src="https://res.cloudinary.com/duxq5a40j/image/upload/v1779851100/logoBumdes_nsewm6.png" alt="Logo">
    </div>

    <h2 class="card-title">Reset Kata Sandi</h2>
    <p class="card-sub">Masukkan kata sandi baru untuk akun Anda</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div class="field-group">
            <label class="field-label">Alamat Email</label>
            <div class="field-inner">
                <i class="fas fa-envelope field-icon"></i>
                <input class="field-input" type="email" name="email"
                    value="{{ old('email', $request->email) }}"
                    placeholder="nama@email.com" required autofocus>
            </div>
            @error('email')
                <p style="color:#f87171; font-size:12px; margin-top:6px;">
                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div class="field-group">
            <label class="field-label">Kata Sandi Baru</label>
            <div class="field-inner">
                <i class="fas fa-lock field-icon"></i>
                <input id="password" class="field-input" type="password"
                    name="password"
                    placeholder="Minimal 8 karakter" required>
                <button type="button" class="toggle-eye" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p style="color:#f87171; font-size:12px; margin-top:6px;">
                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="field-group">
            <label class="field-label">Konfirmasi Kata Sandi</label>
            <div class="field-inner">
                <i class="fas fa-lock field-icon"></i>
                <input id="password_confirmation" class="field-input" type="password"
                    name="password_confirmation"
                    placeholder="Ulangi kata sandi baru" required>
                <button type="button" class="toggle-eye" id="toggleConfirm">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-key"></i>
            Reset Kata Sandi
        </button>

        <div class="card-footer">
            <a href="{{ route('login') }}">Kembali ke Login</a>
        </div>
    </form>

    @push('js')
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggleConfirm').addEventListener('click', function() {
            const input = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
    @endpush

</x-guest-layout>