@extends('theme.default')

@section('title', 'Edit Profil Saya')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Pengaturan Profil</h1>
        <p class="text-slate-500 text-sm mt-1">Perbarui informasi akun dan kata sandi Anda di sini.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center font-bold">
                <i class="fas fa-check"></i>
            </div>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden max-w-3xl">
        {{-- FORM UTAMA --}}
        <form action="{{ route('profile.update') }}" method="POST" class="p-8">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">Informasi Dasar</h3>
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('name') border-rose-500 @enderror">
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-400 text-sm font-bold">@</span>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('username') border-rose-500 @enderror">
                    </div>
                    @error('username') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Alamat Email --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('email') border-rose-500 @enderror">
                    @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if($user->role === 'customer')
                    <div class="md:col-span-2 mt-4">
                        <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">Informasi Pengiriman</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nomor WhatsApp</label>
                        <input type="number" name="no_wa" value="{{ old('no_wa', $user->pelanggan->no_wa ?? '') }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('no_wa') border-rose-500 @enderror">
                        @error('no_wa') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jenis Kelamin</label>
                        <select name="gender" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition">
                            <option value="L" {{ old('gender', $user->pelanggan->gender ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $user->pelanggan->gender ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Alamat Lengkap (Untuk Pengiriman)</label>
                        <textarea name="alamat_lengkap" rows="3"
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('alamat_lengkap') border-rose-500 @enderror">{{ old('alamat_lengkap', $user->pelanggan->alamat_lengkap ?? '') }}</textarea>
                        @error('alamat_lengkap') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">Ubah Kata Sandi</h3>
                </div>

                @if(request()->has('is_reset'))
                    <input type="hidden" name="token" value="{{ request('token') }}">
                    <input type="hidden" name="is_reset" value="1">
                    <div class="md:col-span-2">
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl text-blue-700 text-xs mb-2">
                            <i class="fas fa-info-circle mr-1"></i> Anda masuk melalui link verifikasi email. Silakan langsung buat <strong>Sandi Baru</strong> Anda.
                        </div>
                    </div>
                @else
                    <div class="md:col-span-2">
                        <p class="text-xs text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100 mb-4">
                            <i class="fas fa-lock mr-1"></i> Untuk keamanan, masukkan <strong>Sandi Saat Ini</strong> jika ingin membuat sandi baru.
                        </p>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase">Sandi Saat Ini (Lama)</label>
                            <button type="submit" form="form-reset-sandi" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition bg-transparent border-none cursor-pointer p-0">
                                Lupa Sandi Lama?
                            </button>
                        </div>
                        <input type="password" name="current_password" placeholder="Masukkan sandi Anda saat ini..."
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('current_password') border-rose-500 @enderror">
                        @error('current_password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 mt-2">Kata Sandi Baru</label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition @error('password') border-rose-500 @enderror">
                    @error('password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 mt-2">Konfirmasi Sandi Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru..."
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition">
                </div>

            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                <button type="reset" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <form id="form-reset-sandi" action="{{ route('profile.send-reset-link') }}" method="POST" class="hidden">
        @csrf
    </form>
@endsection