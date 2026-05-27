<x-guest-layout :centered="true">
    <div class="auth-card">
        {{-- Logo Badge --}}
        <div class="logo-badge">
            <img src="https://res.cloudinary.com/duxq5a40j/image/upload/v1779851100/logoBumdes_nsewm6.png" alt="Logo">
        </div>

        <h2 class="card-title">Lupa Kata Sandi?</h2>
        <p class="card-sub">
            Masukkan alamat email Anda, kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
        </p>

        <x-auth-session-status class="status-error" style="background: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2);" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="field-group">
                <label class="field-label">Alamat Email</label>
                <div class="field-inner">
                    <i class="fas fa-envelope field-icon"></i>
                    <input id="email" class="field-input" type="email" name="email" :value="old('email')" placeholder="nama@email.com" required autofocus>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-xs" />
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i>
                {{ __('Kirim Tautan Reset') }}
            </button>
            
            <div class="card-footer">
                <a href="{{ route('login') }}">Kembali ke Login</a>
            </div>
        </form>
    </div>
</x-guest-layout>