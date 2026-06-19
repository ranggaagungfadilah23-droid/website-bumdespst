<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Password;
use App\Models\Pelanggan;

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil
     */
    public function edit(Request $request)
    {
    $user = Auth::user()->load('pelanggan');
        return view('profile.edit', compact('user'));
    }

    /**
     * Memproses pembaruan data profil, sandi, dan alamat (khusus pelanggan)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Cek apakah user sedang dalam proses reset sandi via email
        $isReset = $request->has('token') && $request->has('is_reset');

        // 1. Siapkan Aturan Validasi Dasar (Untuk Semua Role)
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Aturan pintar: Sandi lama HANYA wajib jika tidak bawa token reset email
            'current_password' => [$isReset ? 'nullable' : 'required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        // 2. Tambahkan Validasi Khusus Jika User adalah Customer
        if ($user->role === 'customer') {
            $rules['no_wa'] = ['required', 'string', 'max:20'];
            $rules['gender'] = ['required', 'string', Rule::in(['L', 'P'])];
            $rules['alamat_lengkap'] = ['required', 'string'];
        }

        // Jalankan Validasi
        $request->validate($rules, [
            'current_password.required_with' => 'Sandi lama wajib diisi jika ingin mengubah sandi.',
            'current_password.current_password' => 'Sandi lama yang Anda masukkan salah.',
            'password.confirmed' => 'Konfirmasi sandi baru tidak cocok.',
            'password.min' => 'Sandi baru minimal 8 karakter.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'username.unique' => 'Username ini sudah digunakan.'
        ]);

        // 3. Update Data Utama (Tabel users)
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;

        // Update Password JIKA diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // 4. Update Data Alamat & Profil (Tabel pelanggans) - Khusus Customer
        if ($user->role === 'customer') {
            // Gunakan updateOrCreate agar otomatis membuat data baru jika belum ada
            \App\Models\Pelanggan::updateOrCreate(
                ['user_id' => $user->id], // Cari berdasarkan user_id
                [
                    'no_wa' => $request->no_wa,
                    'gender' => $request->gender,
                    'alamat_lengkap' => $request->alamat_lengkap,
                ]
            );
        }

        // 5. Pengalihan Cerdas
        if ($isReset) {
            return redirect()->route('profile.edit')->with('success', 'Kata sandi berhasil diperbarui melalui verifikasi email!');
        }

        return redirect()->back()->with('success', 'Data profil berhasil diperbarui!');
    }

    /**
     * Mengirim link reset password untuk user yang sedang login
     */
    public function sendResetLink(Request $request)
    {
        $user = Auth::user();

        $status = Password::broker()->sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->back()->with('success', 'Link untuk mereset kata sandi telah dikirim ke email Anda! Silakan cek kotak masuk atau folder spam.');
        }

        return redirect()->back()->withErrors(['current_password' => 'Sistem gagal mengirim link reset. Silakan coba lagi nanti (pastikan konfigurasi SMTP email sudah benar di file .env).']);
    }
}
