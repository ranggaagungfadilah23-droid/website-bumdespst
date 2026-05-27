<x-guest-layout>
    <div class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-2xl border border-white/20 w-full max-w-md">
        
        <div class="mb-6">
            <h2 class="text-2xl font-black text-slate-800">Lupa Kata Sandi?</h2>
            <p class="text-slate-500 text-sm mt-2">
                Jangan khawatir. Masukkan email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" value="Alamat Email" class="text-slate-700 font-bold mb-1" />
                <x-text-input id="email" 
                    class="block w-full px-4 py-3 rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 transition" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    placeholder="nama@email.com"
                    required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700 py-3 rounded-xl font-bold shadow-lg shadow-blue-600/30 transition">
                    {{ __('Kirim Tautan Reset') }}
                </x-primary-button>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline font-bold">
                    Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>