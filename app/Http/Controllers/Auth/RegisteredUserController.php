<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi.
     * (Untuk pelanggan, karena mitra punya halaman sendiri)
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses simpan data registrasi KHUSUS PELANGGAN.
     */
  public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input Khusus Pelanggan
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
+           'username' => ['required', 'string', 'min:3', 'max:20', 'alpha_dash', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'no_wa' => ['required', 'string', 'max:20', 'starts_with:62'],
            'gender' => ['required', 'string', \Illuminate\Validation\Rule::in(['L', 'P'])],
            'alamat_lengkap' => ['required', 'string'],
        ], [
            'no_wa.starts_with' => 'Nomor WhatsApp harus diawali dengan angka 62 (contoh: 62812...)'
        ]);

        // 2. Simpan Akun ke Tabel 'users'
        $user = User::create([
            'name' => $request->name,
+           'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
        ]);
        
        // 3. Simpan Profil ke Tabel 'pelanggans'
        \App\Models\Pelanggan::create([
            'user_id' => $user->id,
            'no_wa' => $request->no_wa,
            'gender' => $request->gender,
            'alamat_lengkap' => $request->alamat_lengkap,
        ]);

        // 4. Trigger email verifikasi
        event(new Registered($user));

        // 5. Login otomatis
        Auth::login($user);

        // 6. Redirect
        return redirect()->route('verification.notice');
    }
}
